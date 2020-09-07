<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/3/20
 * Time: 14:20
 */

namespace App\Http\Controllers\SellerApp;

use App\Jobs\SendTplMsg;
use App\Http\Controllers\Controller;
use App\Model\WeixinAddress;
use App\Module\MessagePushModule;
use App\Module\WeChatRefundModule;
use App\S\Foundation\RegionService;
use App\S\Groups\GroupsDetailService;
use App\S\MarketTools\MessagesPushService;
use App\Services\Order\LogisticsService;
use App\Services\Order\OrderDetailService;
use App\Services\Order\OrderRefundService;
use App\Services\Order\OrderService;
use App\Services\OrderRefundMessageService;
use App\Module\OrderModule;
use App\Services\Shop\MemberAddressService;
use Illuminate\Http\Request;
use App\Services\Order\OrderLogService;
use App\S\Foundation\ExpressService;
use App\S\Groups\GroupsService;
use App\Module\RefundModule;
use Illuminate\Support\Facades\Redis;
use Validator;

class OrderController extends Controller
{

    /**
     * @auth hsz
     * @date 2018/3/20 8:57
     * @desc 获取订单类型、订单状态
     */
    public function getStaticList()
    {
        /* 订单类型 */
        $list['order_type'] = [
            ['type' => 0, 'text' => '全部'],
            ['type' => 1, 'text' => '普通订单'],
            ['type' => 3, 'text' => '多人拼团订单'],
            ['type' => 4, 'text' => '积分兑换订单'],
            ['type' => 5, 'text' => '积分抵现订单'],
            ['type' => 6, 'text' => '分销订单'],
            ['type' => 7, 'text' => '秒杀订单'],
            ['type' => 8, 'text' => '小程序订单'],
            ['type' => 10, 'text' => '享立减订单']
        ];
        /* 订单状态 */
        $list['order_status'] = [
            ['status' => -2, 'text' => '全部'],
            ['status' => 0, 'text' => '待付款'],
            ['status' => -1, 'text' => '待成团'],
            ['status' => 1, 'text' => '待发货'],
            ['status' => 5, 'text' => '退款中'],
            ['status' => 2, 'text' => '已发货'],
            ['status' => 3, 'text' => '已完成'],
            ['status' => 4, 'text' => '已关闭']
        ];
        appsuccess('获取订单类型|状态成功', $list);
    }

    /**
     * @auth hsz
     * @date 2018/3/20 13:57
     * @desc 订单列表
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function orderList(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $wid = $tokenData['wid'];
        $order_list = (new OrderModule())->getOrderList($wid, $input);
        appsuccess('订单列表获取成功', $order_list);
    }

    /**
     * @auth hsz
     * @date 2018/3/20 17:40
     * @desc 获取订单信息
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getOrderInfo(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $orderData = (new OrderService())->getOrderPriceInfo($input['oid'], $wid);
        if (!$orderData || $orderData['wid'] != $wid || $orderData['type'] != 1) {//只有普通订单可以改价
            apperror('订单不存在或操作非法');
        }
        $orderData['now_price'] = $orderData['pay_price'] - $orderData['freight_price'];
        unset($orderData['coupon_price'], $orderData['wid'], $orderData['type']);
        appsuccess('订单信息获取成功', $orderData);
    }

    /**
     * @auth hsz
     * @date 2018/3/20 17:40
     * @desc 获取订单备注
     */
    public function getSellerRemark(Request $request)
    {
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $seller_remark = (new OrderService())->init()->model->where(['id' => $input['oid']])->value('seller_remark');
        appsuccess('订单卖家备注获取成功', ['seller_remark' => $seller_remark]);
    }

    /**
     * @auth hsz
     * @date 2018/3/21 8:40
     * @desc 修改价格
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function changePrice(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
            'now_price' => 'required',
            'now_freight_price' => 'required'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
            'now_price.required' => '现价不能为空',
            'now_freight_price.required' => '现运费不能为空'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $orderData = (new OrderService())->getOrderPriceInfo($input['oid'], $wid);
        if (!$orderData or $orderData['wid'] != $wid) {
            apperror('订单不存在或操作非法');
        }
        if ($input['now_freight_price'] < 0) {
            $input['now_freight_price'] = 0;
        }
        if ($input['now_price'] < 0 || ($input['now_price'] + $input['now_freight_price'] < $orderData['coupon_price'])) {
            apperror('减价过多，请重新设置');
        }
        //差价
        $changePrice = $input['now_price'] - $orderData['products_price'];
        $pay_price = $input['now_price'] + $input['now_freight_price'] - $orderData['coupon_price'];
        $derate_freight = ($input['now_freight_price'] < $orderData['freight_price']) ? ($orderData['freight_price'] - $input['now_freight_price']) : 0;
        $where = ['id' => $input['oid'], 'wid' => $wid];
        $orderUpdate = array(
            'pay_price' => sprintf('%.2f', $pay_price),
            'change_price' => sprintf('%.2f', $changePrice),
            'freight_price' => sprintf('%.2f', $input['now_freight_price']),
            'derate_freight' => sprintf('%.2f', $derate_freight)
        );
        (new OrderService())->init('wid', $wid)->where($where)->update($orderUpdate, false);
        //新增一条改价日志
        $orderLog = [
            'oid' => $input['oid'],
            'wid' => $wid,
            'mid' => $tokenData['userInfo']['id'],
            'action' => 15,
            'remark' => '[订单改价] 改价: ' . sprintf('%.2f', $changePrice) . ', 邮费: ' . sprintf('%.2f', $input['now_freight_price'])
        ];
        (new OrderLogService)->init('wid', $wid)->add($orderLog, false);
        (new OrderService())->upOrderLog($input['oid'], $wid);
        appsuccess('改价成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/21 10:41
     * @desc 关闭获取订单信息
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getCloseOrder(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        //获取商品
        $orderDetail = (new OrderModule())->getCloseOrderInfo($input['oid']);
        if (!$orderDetail || $orderDetail['wid'] != $tokenData['wid']) {
            apperror('订单不存在或非法操作');
        }
        //关闭理由
        $closeReason = array('买家不想要了', '没有货了', '不想卖了');
        $orderDetail['close_reason'] = $closeReason;
        appsuccess('关闭订单信息获取成功', $orderDetail);
    }

    /**
     * @auth hsz
     * @date 2018/3/21 14:41
     * @desc 关闭订单
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function closeOrder(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
            'close_reason' => 'required|between:1,60'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
            'close_reason.required' => '请选择关闭原因',
            'close_reason.between' => '关闭原因最多填写60个字符'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        //仅待付款订单可以关闭
        $order = (new OrderService())->init('wid', $tokenData['wid'])->getInfo($input['oid']);
        if (!$order) {
            apperror('订单不存在');
        }
        if ($order['status'] != 0) {
            apperror('仅有待付款订单才可以关闭');
        }
        //修改订单状态为已关闭
        $where = ['id' => $input['oid']];
        $orderUpdate = ['status' => 4];
        (new OrderService())->init('wid', $tokenData['wid'])->where($where)->update($orderUpdate, false);
        //新增日志
        $orderLog = [
            'oid' => $input['oid'],
            'wid' => $tokenData['wid'],
            'mid' => $tokenData['userInfo']['id'],
            'action' => 12, //商家关闭订单
            'remark' => $input['close_reason']
        ];
        (new OrderLogService())->init('wid', $tokenData['wid'])->add($orderLog, false);
        (new OrderService())->upOrderLog($input['oid'], $tokenData['wid']);
        appsuccess('订单已成功关闭');
    }

    /**
     * @auth hsz
     * @date 2018/3/21 16:40
     * @desc 订单设置卖家备注
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function setSellerRemark(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $where['id'] = $input['oid'];
        $where['wid'] = $tokenData['wid'];
        (new OrderService())->init('wid', $tokenData['wid'])->where($where)->update(['seller_remark' => $input['seller_remark']], false);
        appsuccess('备注保存成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/21 17:40
     * @desc 删除设置卖家备注
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function delSellerRemark(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $where['id'] = $input['oid'];
        $where['wid'] = $tokenData['wid'];
        (new OrderService())->init('wid', $tokenData['wid'])->where($where)->update(['seller_remark' => ''], false);
        appsuccess('备注删除成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/22 8:40
     * @desc 获取已发货包裹信息
     */
    public function getDeliveryPackageInfo(Request $request, OrderModule $orderModule)
    {
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $list = $orderModule->getDeliveryPackage($input['oid']);
        appsuccess('发货包裹获取成功', ['package_list' => $list]);
    }

    /**
     * @auth hsz
     * @date 2018/3/22 13:40
     * @desc 发货获取订单信息
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getDeliveryInfo(Request $request, OrderModule $orderModule)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        //发货获取订单信息
        $wid = $tokenData['wid'];
        $deliveyData = $orderModule->getDeliveryOrderInfo($input['oid'], $wid);
        if ($deliveyData == 1) {
            apperror('订单不存在');
        } elseif ($deliveyData == 2) {
            apperror('仅有待发货订单可以发货');
        }
        appsuccess('发货订单获取成功', $deliveyData);
    }

    /**
     * @auth hsz
     * @date 2018/3/22 16:40
     * @desc 发货
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function deliveryOrder(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
            'odid' => 'required'
        );
        if ($input['no_express'] == 0) { //0=>物流发货 1=>无需物流
            $rules['logistic_no'] = 'required';
            $rules['express_id'] = 'required';
        }
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
            'odid.required' => '订单详情id不能为空',
            'logistic_no.required' => '物流单号不能为空',
            'express_id.required' => '快递公司id不能为空'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $input['odid'] = json_decode($input['odid'], true);
        if (empty($input['odid']) || !is_array($input['odid'])) {
            apperror('请选择发货商品');
        }
        //判断是否是退货完成的，是则不可以发货，否则可以发货
        if (!(new OrderService())->checkCanDelivery($input['oid'], $wid, $input['odid'])) {
            apperror('存在无法发货的商品');
        }
        //物流
        if ($input['no_express'] == 0) { //0=>物流发货 1=>无需物流
            if ($input['express_id'] <= 0) {
                apperror('物流公司id错误');
            }
            $express = (new ExpressService())->getRowById($input['express_id']);
            if (!$express) {
                apperror('物流公司不存在');
            }
            $logistics = [
                'logistic_no' => $input['logistic_no'],
                'express_id' => $input['express_id'],
                'oid' => $input['oid'],
                'odid' => implode(',', $input['odid']),
                'express_name' => $express['title'],
                'word' => $express['word']
            ];
        } else {
            $logistics = [
                'oid' => $input['oid'],
                'odid' => implode(',', $input['odid']),
                'no_express' => 1
            ];
        }
        $logistics_id = (new LogisticsService())->init()->add($logistics, false);
        if ($logistics_id) {
            foreach ($input['odid'] as $val) {
                (new OrderDetailService())->init()->where(['id' => $val])->update(['is_delivery' => 1, 'delivery_time' => time()], false);
            }
        }
        //订单添加卖家备注
        $seller_remark = isset($input['seller_remark']) ? $input['seller_remark'] : '';
        (new OrderService())->init('wid', $tokenData['wid'])->where(['id' => $input['oid'], 'wid' => $tokenData['wid']])->update(['seller_remark' => $seller_remark], false);

        //发货后续操作
        (new OrderModule())->afterDeliveryOrder($input['oid'], $tokenData['wid'], $logistics['odid']);
        appsuccess('发货成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/23 9:00
     * @desc 使成团
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function makeCompleteGroups(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $orderWhere = [
            'id' => $input['oid'],
            'wid' => $wid
        ];
        $orderData = (new OrderService())->init('wid', $wid)->model->where($orderWhere)->select(['groups_id', 'groups_status'])->first()->toArray();
        if (!$orderData) {
            apperror('订单不存在');
        }
        if ($orderData['groups_id'] == 0) {
            apperror('订单是非团购订单');
        }
        if ($orderData['groups_status'] == 2) {
            apperror('该订单已成团');
        }
        $orderUpdate = [
            'status' => 1,
            'groups_status' => 2
        ];
        (new OrderService())->init()->where(['id' => $input['oid']])->update($orderUpdate, false);
        $groupsService = new GroupsService();
        $res = $groupsService->getRowById($orderData['groups_id']);
        if ($res) {
            $groupUpdate = [
                'status' => 2,
                'complete_time' => date("Y-m-d H:i:s", time()),
            ];
            $groupsService->update($orderData['groups_id'], $groupUpdate);
            //成团提醒
            //获取所有参团人信息

            $groupsDetail = (new GroupsDetailService())->getListByWhere(['groups_id' => $orderData['groups_id']]);

            $mids = array_column($groupsDetail, 'member_id');

            foreach ($mids as $mid) {
                (new MessagePushModule($orderData['wid'], MessagesPushService::ActivityGroup))->sendMsg(['oid' => $orderData['id'], 'mid' => $mid, 'group_type' => 'group_success']);
            }
        }
        appsuccess('订单使成团成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/23 14:23
     * @desc 订单详情
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getOrderDetail(Request $request, OrderModule $orderModule)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $res = $orderModule->getOrderDetail($input['oid'], $wid);
        $res['ischange'] = Redis::get(OrderModule::changeSendOrderAddrKey($input['oid'])) ? 0 : 1;
        if (!$res) {
            apperror('订单不存在');
        }
        appsuccess('订单详情获取成功', $res);
    }

    /**
     * @auth hsz
     * @date 2018/3/26 9:45
     * @desc 获取退款信息
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getRefundOrder(Request $request, OrderModule $orderModule)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
            'pid' => 'required|integer',
            'prop_id' => 'required|integer'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
            'pid.required' => '商品id不能为空',
            'pid.integer' => '商品id必须是整数',
            'prop_id.required' => '商品SKU ID不能为空',
            'prop_id.integer' => '订单SKU ID必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $res = $orderModule->getRefundOrder($input['oid'], $input['pid'], $input['prop_id'], $wid);
        appsuccess('退款信息获取成功', $res);
    }

    /**
     * @auth hsz
     * @date 2018/3/26 11:40
     * @desc 获取协商列表
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getConsultList(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'refund_id' => 'required|integer',
        );
        $messages = array(
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $res = (new OrderModule())->getConsultList($input['refund_id'], $wid);
        $refund_query = (new OrderRefundService())->init()->model->where(['id' => $input['refund_id']])->select(['remark', 'phone', 'created_at'])->first();
        if ($refund_query) {
            $refund = $refund_query->toArray();
            $refund_first = [
                'is_seller' => 0,
                'status' => '',
                'content' => $refund['remark'],
                'express_name' => '',
                'express_no' => '',
                'phone' => $refund['phone'],
                'created_at' => $refund['created_at'],
                'status_string' => '发起了退款申请，等待商家处理'
            ];
            $res['list'][] = $refund_first;
        }
        appsuccess('查看完整协商记录成功', $res);
    }

    /**
     * @auth hsz
     * @date 2018/3/27 17:40
     * @desc 退款添加留言
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function refundAddMessage(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
            'refund_id' => 'required|integer',
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $refundQuery = (new OrderRefundService())->init('oid', $input['oid'])->model->select(['mid'])->where(['wid' => $wid, 'oid' => $input['oid'], 'id' => $input['refund_id']])->first();
        if (!$refundQuery) {//退款才能留言
            apperror('退款订单不存在');
        }
        $refundData = $refundQuery->toArray();
        if (isset($input['imgs'])) {
            $input['imgs'] = json_decode($input['imgs'], true);
            if (count($input['imgs']) > 3) {
                apperror('图片最多三张');
            }
        }
        //添加一条协商留言
        $data = [
            'mid' => $refundData['mid'],
            'wid' => $wid,
            'refund_id' => $input['refund_id'],
            'is_seller' => intval(1),
            'content' => $input['content'] ?? '',
            'imgs' => $input['imgs'] ? implode(',', $input['imgs']) : ''
        ];
        $res = (new OrderRefundMessageService())->init()->add($data, false);
        if ($res) {
            appsuccess('留言添加成功');
        } else {
            apperror('留言添加失败');
        }
    }

    /**
     * @auth hsz
     * @date 2018/3/27 17:40
     * @desc 拒绝买家申请
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function refundDisagree(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'oid' => 'required|integer',
            'refund_id' => 'required|integer',
            'remark' => 'required|between:1,200'
        );
        $messages = array(
            'oid.required' => '订单id不能为空',
            'oid.integer' => '订单id必须是整数',
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数',
            'remark.required' => '拒绝理由不能为空',
            'remark.between' => '拒绝理由不能超过200个字符'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        (new OrderModule())->refundDisagree($input['oid'], $input['refund_id'], $wid, $input['remark']);
        appsuccess('拒绝申请成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/28 10:12
     * @desc 同意退货（选择退货地址）
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function refundAddress(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'refund_id' => 'required|integer',
        );
        $messages = array(
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $res = (new OrderModule())->getRefundAddress($input['refund_id'], $wid);
        appsuccess('退货地址获取成功', ['address_list' => $res]);
    }

    /**
     * @auth hsz
     * @date 2018/3/29 9:12
     * @desc 发送退货地址
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function setRefundAddress(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'refund_id' => 'required|integer',
            'address_id' => 'required|integer'
        );
        $messages = array(
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数',
            'address_id.required' => '地址id不能为空',
            'address_id.integer' => '地址id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $refundQuery = (new OrderRefundService())->init()->model->where(['id' => $input['refund_id']])->select(['oid', 'type', 'mid'])->first();
        if (!$refundQuery) {
            apperror('退款不存在');
        }
        $refundData = $refundQuery->toArray();
        if ($refundData['type'] != 1) {
            apperror('仅支持退货退款类型');
        }
        $orderService = new OrderService();
        if (!$orderService->isAddressExist($input['address_id'])) {
            apperror('地址不存在');
        }
        //设置订单address_id
        $orderService->init('wid', $wid)->where(['id' => $refundData['oid']])->update(['address_id' => $input['address_id']], false);
        //后置操作
        (new OrderModule())->setRefundAddress($refundData['oid'], $input['refund_id'], $wid, $refundData['mid']);
        appsuccess('发送退货地址成功');
    }

    /**
     * @auth hsz
     * @date 2018/3/29 11:30
     * @desc 同意退款
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function refundAgree(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'refund_id' => 'required|integer'
        );
        $messages = array(
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $refund = (new OrderRefundService())->init()->getInfo($input['refund_id']);
        if (!$refund) {
            apperror('退款不存在');
        }
        if (!(($refund['type'] == 0 && $refund['status'] == 1) || ($refund['type'] == 1 && $refund['status'] == 7))) {
            apperror('不在可同意退款状态');
        }
        $order = (new OrderService())->init('wid', $wid)->getInfo($refund['oid']);
        if (!$order) {
            apperror('订单不存在');
        }
        $res = (new RefundModule())->sellerAgree($refund['oid'], $wid, $input['refund_id']);
        if ($res['errCode'] == 0) {
            appsuccess('同意退款成功');
        } else {
            apperror($res['errMsg']);
        }
    }

    /**
     * @auth hsz
     * @date 2018/3/22 8:40
     * @desc 获取已退货包裹信息
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function getRefundPackageInfo(Request $request, OrderModule $orderModule)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $input = $request->input('parameter');
        $rules = array(
            'refund_id' => 'required|integer',
        );
        $messages = array(
            'refund_id.required' => '退款id不能为空',
            'refund_id.integer' => '退款id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $list = $orderModule->getRefundPackage($input['refund_id'], $wid);
        appsuccess('退货包裹获取成功', ['package_list' => $list]);
    }


    /**
     * @desc 修改物流地址
     * @param Request $request
     * @param LogisticsService $logisticsService
     * @param ExpressService $expressService
     * @param $oid
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\CommonException
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 08 月 17 日
     */
    public function modifyLogistics(Request $request, LogisticsService $logisticsService, ExpressService $expressService)
    {
        $input = $request->input('parameter');
        $rule = Array(
            'no_express' => 'required',
            'id' => 'required',
            'logistic_no' => 'required_if:no_express,0',
            'express_id' => 'required_if:no_express,0',
        );


        $message = Array(
            'no_express.required' => '是否需要物流',
            'id.required' => '物流单不能为空',
            'logistic_no.required_if' => '请输入物流单号',
            'express_id.required_if' => '请选择物流公司',
        );

        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }

        if (Redis::get(OrderModule::modifyLogisticsKey($input['id']))) {
            apperror('物流仅支持修改一次');
        }

        if ($input['no_express'] == 1) {
            $tmp = [
                'id' => $input['id'],
                'logistic_no' => '',
                'express_id' => 0,
                'express_name' => '',
                'word' => '',
                'logistic_log' => '',
                'no_express' => 1
            ];
        } else {
            $expressTemp = $expressService->getRowById($input['express_id']);
            if (!$expressTemp) {
                apperror('物流公司不存在');
            }
            $tmp = [
                'id' => $input['id'],
                'logistic_no' => $input['logistic_no'],
                'express_id' => $input['express_id'],
                'express_name' => $expressTemp['title'],
                'word' => $expressTemp['word'],
                'no_express' => 0,
                'logistic_log' => '',
            ];
        }
        $logisticsService->init()->where(['id' => $input['id']])->update($tmp, false);
        Redis::set(OrderModule::modifyLogisticsKey($input['id']), 1);
        appsuccess();
    }


    /**
     * @desc 修改收货地址
     * @param Request $request
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 08 月 17 日
     */
    public function changeSendOrderAddr(Request $request, OrderService $orderService)
    {
        $input = $request->input('parameter');
        $oid = $input['oid'] ?? 0;
        if (empty($oid)) {
            apperror('订单不存在或已被删除');
        }
        $orderData = $orderService->init()->getInfo($oid, false);
        if (empty($orderData)) {
            apperror('该订单不存在或已删除');
        }
        if (Redis::get(OrderModule::changeSendOrderAddrKey($oid))) {
            apperror('订单收货地址仅支持修改一次');
        }
        $addrData = [
            'address_province' => $orderData['address_province'],
            'address_city' => $orderData['address_city'],
            'address_area' => $orderData['address_area'],
            'address_detail' => $orderData['address_detail'],
            'address_name' => $orderData['address_name'],
            'address_phone' => $orderData['address_phone']
        ];
        $rules = [
            'province_id' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'address' => 'required',
            'address_name' => 'required',
            'address_phone' => 'required'
        ];
        $message = [
            'province_id.required' => '请选择省份或直辖市',
            'city_id.required' => '请选择该省份对应的市',
            'area_id.required' => '请选择该市对应的区或镇',
            'address.required' => '请输入详细地址',
            'address_name.required' => '请输入收货人姓名',
            'address_phone.required' => '请输入联系人电话'
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        try {
            $dbResult = \DB::transaction(function () use ($input, $orderData, $addrData, $orderService) {
                $saveData['mid'] = $orderData['mid'];
                $saveData['title'] = $input['address_name'];
                $saveData['province_id'] = $input['province_id'];
                $saveData['city_id'] = $input['city_id'];
                $saveData['area_id'] = $input['area_id'];
                $saveData['address'] = $input['address'];
                $saveData['phone'] = $input['address_phone'];
                $addressId = (new MemberAddressService())->init()->add($saveData, false);
                if ($addressId) {
                    $temp = [$input['province_id'], $input['city_id'], $input['area_id']];
                    $regions = (new RegionService())->getListByIdWithoutDel($temp);
                    $saveOrderData['address_id'] = $addressId;
                    $saveOrderData['address_name'] = $input['address_name'];
                    $saveOrderData['address_phone'] = $input['address_phone'];
                    $saveOrderData['address_province'] = $regions[$input['province_id']]['title'];
                    $saveOrderData['address_city'] = $regions[$input['city_id']]['title'];
                    $saveOrderData['address_area'] = $regions[$input['area_id']]['title'];
                    $saveOrderData['address_detail'] = $saveOrderData['address_province'] . $saveOrderData['address_city'] . $saveOrderData['address_area'] . $input['address'];
                    if ($orderService->init('wid', session('wid'))->where(['id' => $orderData['id']])->update($saveOrderData, false)) {
                        $saveOrderLogData['oid'] = $orderData['id'];
                        $saveOrderLogData['wid'] = $orderData['wid'];
                        $saveOrderLogData['mid'] = $orderData['mid'];
                        $saveOrderLogData['action'] = 17;
                        $saveOrderLogData['remark'] = '[修改发货地址] ' . $addrData['address_detail'] . ' 改为 ' . $saveOrderData['address_detail'];
                        if ((new OrderLogService())->init()->add($saveOrderLogData, false)) {
                            Redis::set(OrderModule::changeSendOrderAddrKey($orderData['id']), 1);
                            return true;
                        }
                        return false;
                    }
                    return false;
                }
                return false;
            });
        } catch (\Exception $e) {
            apperror($e->getMessage());
        }
        if ($dbResult) {
            appsuccess('发货地址修改成功');
        }
        apperror('发货地址修改失败');

    }


    /**
     * @desc获取物流公司信息
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 08 月 18 日
     */
    public function getExpress(ExpressService $expressService)
    {
       $data =  array_values($expressService->getListWithoutPage());
       appsuccess('操作成功', $data);
    }


}
