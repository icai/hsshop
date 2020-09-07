<?php

/**
 * 发送外卖订单
 */

namespace App\Jobs;

use App\Module\StoreModule;
use App\Module\TakeAwayModule;
use App\S\Order\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendTakeAway implements ShouldQueue {

    use InteractsWithQueue, Queueable, SerializesModels;

    private $oid;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($oid)
    {
        $this->oid = $oid;
        $this->queue = 'takeAway';
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $orderService = new OrderService();
        $storeModule = new StoreModule();
        $orderData = $orderService->getOrderDetailByOid($this->oid);

        //按订单的创建时间进行判断
        $checkRes = $storeModule->checkIfSendTakeAway($orderData['wid'], $orderData);
        if ($checkRes['errCode'] == 1) {
            return;
        } elseif ($checkRes['errCode'] == 2) {
            \Log::info($checkRes['errMsg']);
            return;
        } elseif ($checkRes['errCode'] == 0) {
            $datas = [];
            //导入到第三方
            try {

                if ($orderData['groups_id'] > 0 && $orderData['groups_status'] == 2) {
                    //获取拼团的所有订单
                    $orderIds = $orderService->model->wheres(['groups_id'=>$orderData['groups_id'], 'pay_way'=>['<>', 0]])->pluck('id');
                    $orderIds = json_decode(json_encode($orderIds), true);
                    if (empty($orderIds)) {
                        return;
                    }
                    foreach ($orderIds as $orderId) {
                        $datas[] = $orderService->getOrderDetailByOid($orderId);
                    }
                } else {
                    $datas[] = $orderData;
                }
                (new TakeAwayModule($orderData['wid']))->addOrder($datas);

            } catch (\Exception $e) {

                \Log::info($e->getMessage());

            }
        }

    }


}