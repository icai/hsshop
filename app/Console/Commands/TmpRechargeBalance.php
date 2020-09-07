<?php

namespace App\Console\Commands;

use App\Jobs\SendRechargeBalanceLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TmpRechargeBalance extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TmpRechargeBalance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移余额充值已成功数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $i = 0;
        DB::table('balance_log')->where('status',1)->chunk(10, function ($balances) use (&$i) {
            $arr = array();
            foreach ($balances as $key => $val) {
                $arr[] = $val->id;
                $job = new SendRechargeBalanceLog($val->wid, $val->mid, $val->money/100, $val->type, $val->pay_way, $val->created_at);
                dispatch($job->onQueue('dsBalance'));
            }
            \Log::info('成功发送迁移余额充值数据 '.(count($balances)==10 ? (($i*10+1).'~'.(($i+1)*10)) : (($i*10+1).'~'.($i*10+count($balances)))).' 条');
            \Log::info('对应ds_balance_log的id:'.join(',', $arr));
            $i++;
        });
        \Log::info('迁移余额充值数据发送成功');
    }

}