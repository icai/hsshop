<?php

namespace App\Console\Commands;

use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Model\Product;
use App\Model\ProductSku;
use App\S\Market\CouponLogService;
use App\S\Member\MemberService;
use DB;
use PointRecordService as OPointRecordService;
use Illuminate\Console\Command;
use OrderDetailService;
use OrderLogService;
use OrderService;

class AutoExpireOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoExpireOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '过期订单自动关闭';

    /**
     * Execute the console command.
     *
     * todo  考虑事务的问题
     * @return mixed
     * @update 何书哲 2018年11月19日 添加外卖订单（连接delivery_config，按设置未支付时间来走）
     * @update 何书哲 2018年11月22日 排除秒杀订单（因为秒杀有专门待支付自动取消设置）
     */
    public function handle()
    {
        DB::table('order as o')
        ->leftJoin('delivery_config as dc', 'o.wid', '=', 'dc.wid')
        ->select('o.id','o.wid','o.mid','o.use_point','o.coupon_id','o.is_takeaway','dc.unpay_min')
        ->where('o.status', 0)
        ->where('o.seckill_id', 0)
        ->whereRaw('UNIX_TIMESTAMP(ds_o.created_at) < IF(ds_o.is_takeaway = 1, ?-ds_dc.unpay_min*60, ?)', [time(), time()-3600])
        ->chunk(100, function($orders) {
            foreach ($orders as $order) {
                $wid = $order->wid;
                $mid = $order->mid;
                $use_point = $order->use_point;
                $coupon_id = $order->coupon_id;

                //退还积分
                if ($use_point > 0) {
                    $point = $use_point;
                    $pointRecordData = [
                        'wid'           =>  $wid,
                        'mid'           =>  $mid,
                        'point_type'    =>  6,
                        'is_add'        =>  1,
                        'score'         =>  $point
                    ];

                    OPointRecordService::insertData($pointRecordData);
                    (new memberService())->incrementScore($mid, $point);
                }

                //退还优惠券
                $coupon_id && (new CouponLogService())->update($coupon_id, ['status' => 0, 'oid' => 0]);

                OrderService::init('wid', $order->wid)
                ->where(['id' => $order->id])
                ->update([ 'status' => 4 ],false);

                list($orderDetails) = OrderDetailService::init()->where(['oid'=>$order->id])->getList(false);
                foreach($orderDetails['data'] as $item)
                {
                    $num=$item['num'];
                    if(!empty($item['product_prop_id'])) {
                        $id=$item['product_prop_id'];
                        //更改有规格商品 数据库库存
                        //更新规格
                        ProductSku::where('id', $id)->increment('stock', $num);
                        (new SkuRedis())->incr($id, 'stock', $num);
                    }

                    //更新商品
                    Product::where('id', $item['product_id'])->increment('stock', $num);
                    (new ProductRedis())->incr($item['product_id'], 'stock', $num);
                }

                //走完订单一生
                $orderLog = [
                    'oid'       => $order->id,
                    'wid'       => $order->wid,
                    'mid'       => $order->mid,
                    'action'    => 14,
                    'remark'    => '系统自动关闭订单',
                ];
                //操作：1买家创建订单；2买家付款；3商家发货；4买家确认收货；5买家评价；6买家取消订单；7买家申请退款；8商家同意退款；9商家拒绝退款；10买家取消退款；11系统自动确认收货；12商家关闭交易；13 延期收货 14 系统关闭订单
                OrderLogService::init('wid',$order->wid)->add($orderLog, false);
                OrderService::upOrderLog($order->id, $order->wid);

                echo $order->id."@@@";
            }
        });
    }
}
