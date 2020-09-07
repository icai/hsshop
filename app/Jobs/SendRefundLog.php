<?php

namespace App\Jobs;

use App\S\Foundation\Bi;
use App\S\Order\OrderService;
use App\Services\Order\OrderRefundService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Lib\BLogger;

class SendRefundLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $connection;

    private $refund_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($refund_id)
    {
        //
        $this->refund_id = $refund_id;
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

        $url = config('app.log_center_url').'/hsy_order_refund';
        $orderRefundService = new OrderRefundService();
        $refundData = $orderRefundService->init()->getInfo($this->refund_id);
        $orderInfo = (new OrderService())->getOrderDetailByOid($refundData['oid'],['pay_way']);
        //组装postData
        $postData = [
            'oid'           => $refundData['oid'],
            'refund_id'     => $this->refund_id,
            'wid'           => $refundData['wid'],
            'amount'        => $refundData['amount'],
            'mid'           => $refundData['mid'],
            'pay_way'       => $orderInfo['pay_way'],
            'created_at'    => time(),
        ];
        $re = (new Bi())->request_post($url,json_encode($postData, JSON_UNESCAPED_UNICODE));
        if ( $re != 200 ) {
            //将日志输出存放到其他文件中去
            \Log::error($this->refund_id.'退款日志发送失败,返回状态码：'.$re);
            BLogger::getDCLogger('error','hsy_order_refund')->error($this->refund_id);
        }else {
            \Log::info($this->refund_id.'退款日志发送成功');
        }
    }
}
