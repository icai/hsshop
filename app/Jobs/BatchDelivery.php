<?php

namespace App\Jobs;

use App\Model\Logistics;
use App\Module\MessagePushModule;
use App\Module\WechatBakModule;
use App\S\Foundation\ExpressService;
use App\S\Log\BatchDeliveryLogService;
use App\S\MarketTools\MessagesPushService;
use App\Services\Order\LogisticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use OrderDetailService;
use OrderService;
use OrderLogService;

class BatchDelivery implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data = [];
    protected $wid;
    protected $flag;

    //每个任务失败后最大尝试次数
    public $tries = 3;

    //每个任务最大运行时间(s)
    public $timeout = 60;


    /**
     * Create a new job instance.
     * @param $data 处理数据
     * @param $wid 店铺Id
     * @return void
     */
    public function __construct($data,$wid,$NotificationFlag)
    {
        //
        $data[2] = (string)$data[2];
        $this->flag = $NotificationFlag;
        $this->data = $data ;
        $this->wid = $wid;
        $this->queue = 'BatchDelivery';
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

        $status = 0;
        $errMsg = '';
        try {
            $data = $this->data;
            $orderData = OrderService::init()->with(['orderDetail'])->where(['wid' => $this->wid,'status'=>1, 'oid' => $data[0]])->getList(false);
            if (!$orderData[0]['data']) {
               throw new \Exception('未发货订单不存在');
            }
            $orderData = $orderData[0]['data'][0];
            $odid = [];
            foreach ($orderData['orderDetail'] as $item) {
                $odid[] = $item['id'];
            }
            $noExpress = $data[1] == '无需物流' ? 1 : 0;
            if ($noExpress) {
                $express = [];
            } else {
                $title = mb_substr($data[1],0,2);
                $express = (new ExpressService())->model->whereRaw("title REGEXP '^".$title."'")->select(['id', 'word'])->get()->toArray();
                $express = $express[0];
                if (!$express) {
                    $errMsg = '不支持该物流公司';
                }
            }
            DB::beginTransaction();
            //修改订单详情中的每个订单相关商品的发货状态设置为已发货
            foreach ($odid as $id) {
                OrderDetailService::init()->where(['id' => $id])->update(['is_delivery' => 1,'delivery_time' => time()], false);
            }
            list($data) = OrderDetailService::init()->where(['oid' => $orderData['id'], 'is_delivery' => 0])->getList();
            if (!$data['data']) {
                $where['id'] = $orderData['id'];
                $where['wid'] = $this->wid;
                if (!OrderService::init('wid', $this->wid)->where($where)->update(['status' => 2], false)) {
                    throw new \Exception('order 修改状态失败');
                }
                $logistics = [
                    'logistic_no' => $noExpress ? '' : (string)$this->data[2],
                    'express_id' => $express ? $express["id"] : 999,
                    'oid' => $orderData['id'],
                    'odid' => implode(',', $odid),
                    'express_name' => $this->data[1],
                    'word' => $express ? $express["word"] : '其他',
                    'no_express' => $noExpress
                ];
                if (DB::table('logistics')->insertGetId($logistics) === false) {
                    throw new \Exception('物流信息 增加失败');
                }
                $orderLog = [
                    'oid' => $orderData['id'],
                    'wid' => $this->wid,
                    'mid' => $orderData['mid'],
                    'action' => 3,
                    'remark' => '商家发货',
                ];
                if (DB::table('order_logs')->insertGetId($orderLog)  === false) {
                    throw new \Exception('订单日志 增加失败');
                }
                OrderService::upOrderLog($orderData['id'], $this->wid);
                DB::commit();
                $status = 1;
                $this->__sendTplNotify($orderData,$odid);
            }
            \Log::info('处理成功');

        }catch (\Exception $exception) {
            DB::rollBack();
            $errMsg = $exception->getMessage();
            \Log::info('批量发货处理失败：'.$exception->getMessage());
        }

        $this->log($status,$errMsg);
    }

    //发货消息提醒
    protected function __sendTplNotify($orderData,$odid)
    {
        //获取用户openid
        $orderInfo = OrderService::getOrderInfo($orderData['id']);
        $orderData = $orderInfo['data'];
        $orderData['odid'] = implode(',', $odid);
        (new MessagePushModule($orderData['wid'], MessagesPushService::DeliverySuccess))->sendMsg($orderData);
    }


    /**
     * 插入批量发货日志
     * @param $status
     * @author: 梅杰 2018年8月30日
     */
    public function log($status, $errMsg)
    {
        $data = [
            'oid'           => $this->data[0],
            'express_no'    => $this->data[2],
            'express_name'  => $this->data[1],
            'status'        => $status,
            'created_at'    => date("Y-m-d H:i:s",time()),
            'wid'           => $this->wid,
            'err_msg'       => $errMsg
        ];
        (new BatchDeliveryLogService())->model->insertGetId($data);
    }
}
