<?php

namespace App\Listeners;

use App\Events\OrderRefundEvent;
use App\Jobs\SendRefundLog;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use App\S\Order\OrderService;
use App\Services\Order\OrderRefundService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderRefundEventListen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderRefundEvent  $event
     * @return void
     */
    public function handle(OrderRefundEvent $event)
    {
        //
        $job = new SendRefundLog($event->refund_id);
        dispatch($job->onQueue('orderRefund'));


        $refund = (new OrderRefundService())->init()->where(['id' => $event->refund_id])->getInfo();

        $order = (new OrderService())->getRowByWhere(['id'=>$refund['oid']]);

        $order['source'] == 0 && (new MessagePushModule($order['wid'],MessagesPushService::OrderRefund))->sendMsg($event->refund_id);

        $order['source'] == 1 && (new MessagePushModule($order['wid'],MessagesPushService::OrderRefund,MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($event->refund_id);


    }
}
