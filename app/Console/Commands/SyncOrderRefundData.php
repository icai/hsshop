<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2019/8/19
 * Time: 11:09
 * Desc：同步店铺每天的退款记录
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOrderRefundData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:orderRefundData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update order refund data to dc.order';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $select = [
            "wid",
            DB::raw("FROM_UNIXTIME(created_at, '%Y-%m-%d') AS time"),
            DB::raw("COUNT(DISTINCT oid) AS order_refund_count"),
        ];
        $fields = "wid,order_amount,order_count,order_user_count,order_payed_amount,order_payed_count,order_payed_user_count,order_payed_goods_count,order_pay,created_at,refund_order_count,flag";
        $connect = DB::connection("mysql_dc_log");
        DB::connection("mysql_dc_order_log")->table("order_refund")->select($select)->groupBy(['wid', 'time'])
            ->where('created_at', '<', strtotime(date('Y-m-d')))
            ->orderBy('time', 'desc')->chunk(100, function ($data) use ($connect, $fields) {

                $sql = "INSERT INTO dc_order ($fields) VALUES";
                $a = [];
                foreach ($data as &$log) {
                    $logTime = strtotime($log->time) + 30;

                    $a[] = "($log->wid,0,0,0,0,0,0,0,0,$logTime,$log->order_refund_count,1)";
                }
                $sql .= implode(",", $a) . " ON DUPLICATE KEY UPDATE refund_order_count = VALUES(refund_order_count)";
                $re = $connect->insert($sql);

            });


        // 删除多余的数据
        $re = $connect->table('order')->where('flag', 1)->delete();
    }
}
