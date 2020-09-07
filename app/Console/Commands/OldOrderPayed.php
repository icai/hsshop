<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class OldOrderPayed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OldOrderPayed';

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
        $where = [
//            'order.status' => ['in',[1,2,3,7]],
            'order.pay_way' => ['<>',0]
        ];
        $select = [
            'order.id',
            'order.mid',
            'order.wid',
            'order.source',
            'order.type',
            'pay_way',
            'pay_price',
            'order_detail.product_id',
            'order_detail.num',
            'order_detail.price',
            'order_detail.created_at',
            'order.groups_id',
            'order.seckill_id',
            'order.share_event_id'
        ];
        $sql = "INSERT INTO dc_order_payed (`oid`, `mid`, `product_id`, `num`, `type`, `price`, `pay_way`, `source`,`created_at`,`wid`,`activity_id`) VALUES ";
        $connect = DB::connection('mysql_dc_order_log');
        $re = DB::table('order')->wheres($where)->select($select)->leftJoin('order_detail','order.id','=','order_detail.oid')
            ->chunk(500,function ($item) use($connect,$sql) {
                foreach ($item as $value) {
                    //何书哲 2018年8月6日 添加对应活动id
                    $activity_id = 0;
                    if ($value->type == 3) {//拼团
                        $activity_id = $value->groups_id;
                    } elseif ($value->type == 7) {//秒杀
                        $activity_id = $value->seckill_id;
                    } elseif ($value->type == 10) {//享立减
                        $activity_id = $value->share_event_id;
                    }
                    $value->created_at = strtotime($value->created_at);
                    $sql .= "($value->id,$value->mid,$value->product_id,$value->num,$value->type,$value->pay_price,$value->pay_way,$value->source,$value->created_at,$value->wid,$activity_id),";
                }
                $sql = substr($sql, 0,-1);

                $sql .= "ON DUPLICATE KEY UPDATE  oid = VALUES(oid),  mid = VALUES(mid),  product_id = VALUES(product_id),type = VALUES(type),price = VALUES(price),pay_way = VALUES(pay_way),source = VALUES(source),created_at = VALUES(created_at),wid = VALUES(wid),activity_id = VALUES(activity_id)";
                $connect->insert($sql);
            });
    }
}
