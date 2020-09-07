<?php

namespace App\Console\Commands;

use App\Jobs\DataStatisticsDistributeWithdrawal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TmpDistributeWithdrawal extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TmpDistributeWithdrawal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移分销提现已打款数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $i = 0;
        DB::table('cash_log')->where('status',2)->chunk(10, function ($cashes) use (&$i) {
            $arr = array();
            foreach ($cashes as $key => $val) {
                $arr[] = $val->id;
                $job = new DataStatisticsDistributeWithdrawal($val->wid, $val->mid, $val->money, $val->type, $val->source, strtotime($val->updated_at));
                dispatch($job->onQueue('dsDraw'));
            }
            \Log::info('成功发送迁移分销提现数据 '.(count($cashes)==10 ? (($i*10+1).'~'.(($i+1)*10)) : (($i*10+1).'~'.($i*10+count($cashes)))).' 条');
            \Log::info('对应ds_cash_log的id:'.join(',', $arr));
            $i++;
        });
        \Log::info('迁移分销提现数据发送成功');
    }

}