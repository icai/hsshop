<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class OldRefundLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OldRefund';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        //
        $where = [
            'order_refund.status' => ['in',[4,7]]
        ];
        $select = [
            'pay_way',
            'order_refund.mid',
            'order_refund.wid',
            'order_refund.amount',
            'order_refund.id',
            'order_refund.oid',
            'order_refund.type',
            'order_refund.updated_at'
        ];
        $sql = "INSERT INTO dc_order_refund (`oid`, `mid`, `amount`, `type`,  `pay_way`, `refund_id`,`created_at`,`wid`) VALUES ";
        $connect = DB::connection('mysql_dc_order_log');

        $re = DB::table('order')->wheres($where)->select($select)->leftJoin('order_refund','order.id','=','order_refund.oid')
            ->chunk(500,function ($item) use($connect,$sql) {
                foreach ($item as $value) {
                    $value->updated_at = strtotime($value->updated_at);
                    $sql .= "($value->oid,$value->mid,$value->amount,$value->type,$value->pay_way,$value->id,$value->updated_at,$value->wid),";
                }
                $sql = substr($sql, 0,-1);
                $sql .= " ON DUPLICATE KEY UPDATE  oid = VALUES(oid),  mid = VALUES(mid),  amount = VALUES(amount),type = VALUES(type),pay_way = VALUES(pay_way),refund_id = VALUES(refund_id),created_at = VALUES(created_at),wid = VALUES(wid)";
                $connect->insert($sql);
            });

    }
}
