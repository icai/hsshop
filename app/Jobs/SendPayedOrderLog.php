<?php

namespace App\Jobs;

use App\Lib\BLogger;
use App\S\Foundation\Bi;
use App\S\Order\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;


class SendPayedOrderLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $connection ;

    protected $oid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($oid)
    {
        //
        $this->oid = $oid;
        $this->connection = 'dc';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $url = config('app.log_center_url').'/hsy_order_payed';
        //获取订单信息
        //何书哲 2018年8月6日 获取字段添加拼团id，秒杀id，享立减id
        $columns = ['id','wid','mid','created_at','pay_price','pay_way','type','source','cash_fee','groups_id','seckill_id','share_event_id','discount','discount_ids'];
        $orderInfo = (new OrderService())->getOrderDetailByOid($this->oid,$columns);
        foreach ( $orderInfo['orderDetail'] as &$value) {
            $product[] = [
                'product_id' => $value['product_id'],
                'num'        => $value['num'],
                'price'      => $value['price']
            ];
        }
        //何书哲 2018年8月6日 获取活动id
        $activity_id = 0;
        switch ($orderInfo['type']) {
            case 3://拼团订单
                $activity_id = $orderInfo['groups_id'];
                break;
            case 7://秒杀订单
                $activity_id = $orderInfo['seckill_id'];
                break;
            case 10://享立减订单
                $activity_id = $orderInfo['share_event_id'];
                break;
        }
        //组装postData
        $postData = [
            'oid'       => $orderInfo['id'],
            'wid'       => $orderInfo['wid'],
            'mid'       => $orderInfo['mid'],
            'pay_way'   => $orderInfo['pay_way'],
            'type'      => $orderInfo['type'],
            'source'    => $orderInfo['source'],
            'created_at'        => $orderInfo['created_at'],
            'product_detail'    => $product,
            'pay_price'         => $orderInfo['pay_price'],
            'activity_id'       => $activity_id, //何书哲 2018年8月6日 添加对应活动id
            'discount'          => $orderInfo['discount'],
            'discount_ids'          => $orderInfo['discount_ids'],
        ];
        $re = (new Bi())->request_post($url,json_encode($postData, JSON_UNESCAPED_UNICODE));
        if ( $re !=200 ) {
            //将日志输出存放到其他文件中去
            \Log::error($this->oid.'付款日志发送失败,返回状态码：'.$re);
            BLogger::getDCLogger('error','hsy_order_payed')->error($this->oid);
        }else {
            \Log::info($this->oid.'付款日志发送成功');
        }
    }

}
