<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;

use App\Jobs\SendTplMsg;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use App\S\NotificationService;
use App\S\Foundation\ExpressService;
use App\S\Order\OrderLogisticsService;
use App\Services\Order\LogisticsService;
use App\Services\Order\OrderRefundService;
use OrderDetailService;
use OrderLogService;
use OrderService;

/**
 * 快递打单控制器类
 * create 何书哲 2018年6月28日
 */
class KuaidiController extends Controller {

    /**
     * 快递打单异步回调
     * @create 何书哲 2018年6月28日 快递打单异步回调
     * @update 何书哲 2018年7月10号 如果订单只有单件商品，则自动发货
     */
    public function kuaidiNotify()
    {
        $streamData = file_get_contents('php://input');
        \Log::info('【快速打单异步回调】 返回结果：');
        \Log::info($streamData);
        $streamData = json_decode($streamData, true);
        if ($streamData && isset($streamData['type']) && $streamData['type'] == 'SEND') {//导入异步回调
            if (isset($streamData['data']['status']) && $streamData['data']['status'] == 200) {
                //更新订单is_import状态为1
                OrderService::init()->where(['id'=>$streamData['data']['orderNum']])->update(['is_import'=>1], false);
            }
        } elseif ($streamData && isset($streamData['type']) && $streamData['type'] == 'FILLEXPNUM') {//打单异步回调
            $orderLogisticsService = new OrderLogisticsService();
            $expressService = new ExpressService();
            $logisticsService = new LogisticsService();
            foreach ($streamData['data'] as $val) {
                //获取快递公司id，不存在则为其他
                $expressData = $expressService->model->where('word_code', $val['kuaidicom'])->first()->toArray();
                //拼接数组
                $data = [
                    'oid'          => $val['orderNum'],
                    'kuaidi_num'   => $val['kuaidinum'],
                    'express_id'   => $expressData ? $expressData['id'] : 999
                ];
                //添加到订单打单表
                $orderLogisticsService->add($data);
                //update 何书哲 2018年7月10号 如果订单只有单件商品，则自动发货
                list($orderDetail) = OrderDetailService::init()->where(['oid'=>$val['orderNum']])->getList(false);
                if ($orderDetail && isset($orderDetail['data']) && count($orderDetail['data']) == 1 && $orderDetail['data'][0]['num'] == 1) {
                    $logisticsData = $logisticsService->init()->model->where('oid', $val['orderNum'])->where('odid',implode(',', [$orderDetail['data'][0]['id']]))->first();
                    if ($logisticsData) {
                        continue;
                    }
                    $logistics = [
                        'logistic_no'       => $data['kuaidi_num'],
                        'express_id'        => $data['express_id'],
                        'oid'               => $val['orderNum'],
                        'odid'              => implode(',', [$orderDetail['data'][0]['id']]),
                        'express_name'      => $expressData['title'],
                        'word'              => $expressData['word_code'],
                    ];
                    $logistics_id = $logisticsService->init()->add($logistics,false);
                    if ($logistics_id){
                        OrderDetailService::init()->where(['id'=>$orderDetail['data'][0]['id']])->update(['id'=>$orderDetail['data'][0]['id'], 'is_delivery'=>1, 'delivery_time'=>time()],false);
                    }
                    //订单中每个商品的退款状态 Herry
                    $ids = [];
                    $query = OrderService::init()->model->find($val['orderNum']);
                    if (!$query){
                        continue;
                    }
                    $data['order'] = $query->load('orderDetail')->toArray();
                    $wid = $data['order']['wid'];
                    $refundService = new OrderRefundService();
                    foreach ($data['order']['orderDetail'] as $k => $detail) {
                        $refund = $refundService->init('oid', $val['orderNum'])->where(['oid' => $val['orderNum'], 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
                        if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                            $ids[] = $detail['id'];
                        }
                    }
                    $condition = ['oid'=>$val['orderNum'], 'is_delivery'=>0];
                    if ($ids){
                        $condition['id'] = ['not in',$ids];
                    }
                    $orderData = OrderService::init()->getInfo($val['orderNum']);
                    list($data) =  OrderDetailService::init()->where($condition)->getList();
                    if (!$data['data']){
                        $where['id']  = $val['orderNum'];
                        $where['wid'] = $wid;
                        OrderService::init('wid', $wid)->where($where)->update([ 'status' => 2 ],false);
                        $orderLog = [
                            'oid'       => $val['orderNum'],
                            'wid'       => $wid,
                            'mid'       => $orderData['mid'],
                            'action'    => 3,
                            'remark'    => '商家发货',
                        ];
                        OrderLogService::init()->add($orderLog,false);
                    }
                    OrderService::upOrderLog($val['orderNum'], $wid);
                    //修改对应的打单记录（order_logistics数据表）的使用状态
                    if ($orderLogistics = $orderLogisticsService->getRowByWhere(['oid'=>$val['orderNum'], 'kuaidi_num'=>$val['kuaidinum']])) {
                        $orderLogisticsService->update($orderLogistics['id'], ['is_used'=>1]);
                    }
                    //发送发货通知
                    if($orderData) {
                        //发送微信发货提醒消息
                        $orderData['odid'] = $logistics['odid'];
                        (new MessagePushModule($wid, MessagesPushService::DeliverySuccess))->sendMsg($orderData);
                    }
                }
            }
        } elseif ($streamData && isset($streamData['type']) && $streamData['type'] == 'EXPRESSINFO') {//物流推送异步回调
            if (isset($streamData['data']) && isset($streamData['data']['expressinfo']) && isset($streamData['data']['expressinfo']['lastResult']) && $streamData['data']['expressinfo']['lastResult']['status'] == 200) {
                //如果已存在修改对应包裹物流信息
                $orderLogisticsService = new LogisticsService();
                $where = ['oid' => $streamData['orderNum'], 'logistic_no' => $streamData['data']['expressinfo']['lastResult']['nu']];
                $logistics = $orderLogisticsService->init()->where($where)->getInfo();
                if ($logistics) {
                    $update = [
                        'id' => $logistics['id'],
                        'logistic_log' => json_encode($streamData['data']['expressinfo']['lastResult'])
                    ];
                    $orderLogisticsService->init()->where($where)->update($update,false);
                }
            }
        }
    }
}