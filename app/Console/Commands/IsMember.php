<?php

namespace App\Console\Commands;

use App\Model\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class IsMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IsMember';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量更新是否是会员';

    /**
     * Create a new command instance.
     *
     * @return void
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $isMember = [];
        DB::table('member_card_record as l')
            ->select(["l.mid", "c.id as a", "l.status", "l.created_at", "c.id", "limit_days", "limit_type", "limit_start", "limit_end", "state"])
            ->leftJoin('member_card as c', 'l.card_id', '=', 'c.id')
            ->whereNull('c.deleted_at')
            ->whereNotNull('c.id')
            ->whereNull('l.deleted_at')->chunk(100, function ($cards) use (&$isMember) {
                $has = $is = [];
                foreach ($cards as &$val) {

                    // 会员卡到期时间是指定天数
                    if ($val->limit_type == 1) {
                        $day = (time() - strtotime($val->created_at)) / 86400;
                        if ($val->limit_days < $day) {
                            if (!in_array($val->mid, $has) && !in_array($val->mid, $isMember)) {
                                $has[] = $val->mid;
                            }
                            continue;
                        }
                    }

                    // 会员卡到期时间是指定时间
                    if ($val->limit_type == 2) {
                        if (strtotime($val->limit_end) < time()) {
                            if (!in_array($val->mid, $has) && !in_array($val->mid, $isMember)) {
                                $has[] = $val->mid;
                            }
                            continue;
                        }
                    }

                    // 如果会员卡退卡或者为启用
                    if ($val->state != 1 || $val->status == 0) {
                        if (!in_array($val->mid, $has) && !in_array($val->mid, $isMember)) {
                            $has[] = $val->mid;
                        }
                        continue;
                    }

                    if (!in_array($val->mid, $is)) {
                        $is[] = $val->mid;
                    }


                    if (!in_array($val->mid, $isMember)) {
                        $isMember[] = $val->mid;
                    }


                }

                Member::whereIn('id', $has)->update(['is_member' => 2]);
                Member::whereIn('id', $is)->update(['is_member' => 1]);
            });

    }

}
