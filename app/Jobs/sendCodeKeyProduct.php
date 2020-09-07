<?php

namespace App\Jobs;

use App\Lib\Redis\CamListRedis;
use App\Module\MessagePushModule;
use App\S\Cam\CamListService;
use App\S\MarketTools\MessagesPushService;
use App\S\NotificationService;
use App\Services\Order\LogisticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use OrderService;
use OrderDetailService;
use OrderLogService;

class sendCodeKeyProduct implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    protected $oid;

    protected $mid;
    protected $wid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid, $mid, $oid)
    {
        //
        $this->mid = $mid;
        $this->oid = $oid;
        $this->wid = $wid;
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

        //获取订单详情数据
        $data = OrderDetailService::init()->where(['oid' => $this->oid, 'is_delivery' => 0])->with(['product'])->getList(false);
        if (!$data['0']['data']) {
            \Log::info('未发货订单不存在' . $this->oid);
            \Log::info($data);
            return;

        }
        $detail = $data[0]['data'][0];
        $detailId = $detail['id'];
        //获取可用的卡密
        $camService = new CamListService();
        $cdKey = $camService->getCdKey($detail['product']['cam_id'], $detail['num']);
        if ($cdKey) {
            $orderData = OrderService::getOrderInfo($this->oid);
            $orderData = $orderData['data'];
            //事务处理，1，将详情表
            DB::transaction(function () use ($cdKey, $detailId, $camService,$orderData) {
                try {
                    #todo 1、修改数据库CamList
                    $camService->model->whereIn('id', $cdKey)->update(['mid' => $this->mid, 'is_send' => 1, 'oid' => $this->oid, 'send_time' => date('Y-m-d H:i:s')]);
                    //缓存更新
                    (new CamListRedis())->sendCdKey($cdKey, ['mid' => $this->mid, 'oid' => $this->oid,'getMember' => json_encode($orderData['member'],JSON_UNESCAPED_UNICODE)]);

                    #todo 2、订单表改为发货状态
                    OrderService::init('wid', $this->wid)->where(['id' => $this->oid])->update(['status' => 3], false);
                    #todo 3、订单详情表改为发货状态
                    OrderDetailService::init()->where(['id' => $detailId])->update(['id' => $detailId, 'is_delivery' => 1, 'delivery_time' => time()], false);

                    #todo 4、 订单物流信息信息
                    $logistics = [
                        'oid' => $this->oid,
                        'odid' => $detailId,
                        'no_express' => 1,
                    ];
                    (new LogisticsService())->init()->add($logistics, false);

                    #todo 4、增加订单发货日志
                    $orderLog = [
                        'oid' => $this->oid,
                        'wid' => $this->wid,
                        'mid' => $this->mid,
                        'action' => 3,
                        'remark' => '商家发货',
                    ];
                    OrderLogService::init()->add($orderLog, false);

                } catch (\Exception $exception) {
                    \Log::info($this->oid . '卡密发送失败:' . $detailId);
                }
                \Log::info($this->oid . '卡密发送完成' . $detailId);

            });

            //发送发货通知
            $orderData ['odid'] = $detailId;
            (new MessagePushModule($this->wid, MessagesPushService::DeliverySuccess))->sendMsg($orderData);
        } else {
            \Log::info($this->oid . '卡密发送失败:无库存' . $detailId);
        }

    }

}
