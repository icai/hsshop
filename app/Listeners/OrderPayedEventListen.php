<?php

namespace App\Listeners;

use App\Events\OrderPayedEvent;
use App\Jobs\SendByteDanceTemplate;
use App\Jobs\SendPayedOrderLog;
use App\Jobs\SendSMS;
use App\Jobs\SendTplMsg;
use App\Module\ByteDance\SendTemplateModule;
use App\Module\MessagePushModule;
use App\Module\NotificationModule;
use App\Module\WechatBakModule;
use App\S\MarketTools\MessagesPushService;
use App\S\NotificationService;
use App\S\Order\OrderService;
use App\Services\Permission\WeixinUserService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Log;

class OrderPayedEventListen
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
     * @param  OrderPayedEvent  $event
     * @return void
     * @update 何书哲 2018年10月22日 修改发送新订单提醒条件
     * @update 张永辉 2019年10月10日16:38:27 发送字节跳动小程序模板消息
     */
    public function handle(OrderPayedEvent $event)
    {
        $orderInfo = (new OrderService())->getRowByWhere(['id' => $event->oid]);
        //发送消息模板
        try {

            //发送买家付款成功消息提醒
            $orderInfo['source'] == 0 && (new MessagePushModule($orderInfo['wid'], MessagesPushService::PaySuccess))->sendMsg($event->oid);

            $orderInfo['source'] == 1 && (new MessagePushModule($orderInfo['wid'],MessagesPushService::PaySuccess,MessagePushModule::SEND_TARGET_WECHAT_XCX))
                    ->sendMsg($event->oid);

            if ($orderInfo['source'] == 4) {
                dispatch((new SendByteDanceTemplate(SendTemplateModule::PAY_ORDER, ['oid' => $event->oid])));
            }
            // 发送新订单提醒
            $weixinUserService=  new WeixinUserService();
            $userData = $weixinUserService->init()->where(['wid'=>$orderInfo['wid'],'open_id' => ['<>',null]])->getList(false);
            foreach ($userData[0]['data'] as $v) {
                (new MessagePushModule($orderInfo['wid'], MessagesPushService::NewOrder))->sendMsg(['oid'=>$event->oid, 'openid'=>$v['open_id']]);
            }

            //判断店铺是否开了新订单消息提醒
            if((new NotificationService())->checkIfSubscribed(2,$orderInfo['wid'],1)){
                //发送微信模板消息给店铺（必须先开启消息提醒）
                (new NotificationModule())->publishNewPaidOrderNotification($event->oid);
                //短信通知add MayJay
                dispatch((new SendSMS($orderInfo['wid'],$event->oid))->onQueue('SendSMS'));
            }
            //发送付款日志
            $job = new SendPayedOrderLog($event->oid);
            dispatch($job->onQueue('orderPayed'));
        } catch (Exception $e) {
            Log::info('微信消息模板发送失败'.$e->getMessage());
        }
    }
}
