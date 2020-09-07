<?php
/**
 * 退款
 * Created by PhpStorm.
 * User: Herry
 * Date: 2017/10/16
 * Time: 10:53
 */

namespace App\Module;


use App\S\Groups\GroupsService;
use App\S\Member\MemberService;
use App\S\Wechat\WeixinRefundService;
use App\Services\Order\OrderRefundService;
use App\Services\OrderRefundMessageService;
use App\Services\UserService;
use DB;
use OrderDetailService;
use OrderLogService;
use OrderService;
use ProductService;
use WeixinService;
use App\S\Weixin\ShopService;

class RefundModule
{
    /**
     * 申请退款
     * @modify author 张国军 2018年08月07日 虚拟订单不能够申请退款
     * @update 许立 2018年10月8日 新需求：确认收货7天后不能申请退款
     * @update 何书哲 2019年10月08日 退款记录因数据表是唯一索引，为避免异常报错，添加判断
     */
    public function refundApply($request, $oid, $pid, $wid, $mid, $prop_id = 0)
    {
        // 返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        // 订单详情
        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }
        if ($orderData['mid'] != $mid) {
            $resultArr['errMsg'] = '不可操作别人的订单';
            return $resultArr;
        }

        // add by 张国军 处理卡密订单逻辑
        if (isset($orderData['type']) && $orderData['type'] == 12) {
            $return['err_msg'] = '虚拟订单[卡密订单]不能够退款';
            return $return;
        }

        // 查看是否是待成团订单
        if ($orderData['groups_id'] != 0) {
            $groups = (new GroupsService())->getRowById($orderData['groups_id']);
            if ($groups['status'] != 2) {
                $resultArr['errCode'] = 2;
                $resultArr['errMsg'] = '未成团订单不可申请退款';
                $resultArr['data'] = ['groupID' => $orderData['groups_id']];
                return $resultArr;
            }
        }

        if ($orderData['pay_price'] <= 0) {
            $resultArr['errMsg'] = '0元支付订单不能退款';
            return $resultArr;
        }

        // 判断订单状态是否可申请退款 查询退款表记录
        $orderRefundService = new OrderRefundService();

        // 201710新需求 不限制申请退款次数
        // update 何书哲 2019年10月08日 退款记录因数据表是唯一索引，为避免异常报错，添加判断
        // update 张永辉 2019年10月31日20:37:26 查询条件修改
        $refund = $orderRefundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $pid, 'prop_id' => $prop_id])->getInfo();
        if ($refund) {
            $return['err_msg'] = '只能申请一次退款';
            return $return;
        }

        // 201710新需求 订单确认收货7天内 都可以申请退款
        // todo 被合并冲突解决错误文件暂时找回 可能有遗漏 20171121 16:52 Herry
        $logs = OrderLogService::init()->model->where(['oid' => $oid])->whereIn('action', [4, 11])->get()->toArray();
        if ($logs) {
            $receiveTime = $logs[0]['created_at'];
            if (strtotime($receiveTime) + 7 * 24 * 3600 < time()) {
                $resultArr['errMsg'] = '确认收货7天后无法申请退款';
                return $resultArr;
            }
        }

        if ($orderData['status'] == 0) {
            $resultArr['errMsg'] = '该订单暂时无法申请退款';
            return $resultArr;
        }

        // 商品详情
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            $resultArr['errMsg'] = '商品不存在';
            return $resultArr;
        }

        // 商品最多可退款金额
        $refundAmountMax = $this->_getMaxRefundAmount($orderData, $pid, $prop_id);
        if ($request->isMethod('post')) {
            // 参数
            $input = $request->input();

            $now = date('Y-m-d H:i:s');
            if ($input['data']['amount'] > $orderData['pay_price'] || $input['data']['amount'] <= 0) {
                $resultArr['errMsg'] = '退款金额不正确';
                return $resultArr;
            }

            // 图片最多三张
            $imgs = $input['data']['imgs'] ?? '';
            if ($imgs && count($imgs) > 3) {
                $resultArr['errMsg'] = '图片最多三张';
                return $resultArr;
            }

            $data = [
                'mid' => $mid,
                'wid' => $wid,
                'oid' => $oid,
                'pid' => $pid,
                'prop_id' => $prop_id,
                'amount' => $input['data']['amount'],
                'type' => intval($input['data']['type']),
                'order_status' => intval($input['data']['order_status']),
                'reason' => intval($input['data']['reason']),
                'phone' => $input['data']['phone'],
                'remark' => $input['data']['remark'],
                'imgs' => $imgs ? implode(',', $imgs) : '',
                'created_at' => $now,
                'updated_at' => $now
            ];

            // 插入订单退款表
            $resRefund = $orderRefundService->init('oid', $oid)->add($data, false);
            if ($resRefund) {
                // 更新订单表 产品需求：订单中只要有一个商品退款中 则属于退款订单，订单内全部商品退款关闭 则订单才算退款关闭
                // 订单status不变 refund_status改为退款状态
                // 当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
                // 只要有申请退款 则refund_status=1 后续状态不更新 这样维权订单列表可以根据这个字段获取
                $resOrder = OrderService::init('wid', $wid)
                    ->where(['id' => $oid])
                    ->update(['refund_status' => 1], false);

                // 添加订单日志表记录
                $log = [
                    'oid' => $oid,
                    'wid' => $wid,
                    'mid' => $mid,
                    'action' => 7,
                    'remark' => '买家申请退款(小程序平台)'
                ];
                OrderLogService::init()->add($log, false);
                OrderService::upOrderLog($oid, $wid);

                $resultArr['errCode'] = 0;
                $resultArr['errMsg'] = '退款申请成功';

                // 退款消息提醒
                (new NotificationModule())->publishRefundRequestNotification($oid);

                return $resultArr;
            } else {
                $resultArr['errMsg'] = '新增退款记录出错';
                return $resultArr;
            }
        } else {
            $orderData['refundAmountMax'] = $refundAmountMax['refundAmountMax'];
            // 非0 则显示含x元运费
            $orderData['refund_freight'] = $refundAmountMax['isLastRefundProduct'] ? $orderData['freight_price'] : 0;

            $resultArr['errCode'] = 0;
            $resultArr['errMsg'] = '获取申请退款页信息成功';
            $resultArr['data'] = ['order' => $orderData, 'product' => $product];

            // 退款消息提醒
            // (new NotificationModule())->publishRefundRequestNotification($oid);

            return $resultArr;
        }
    }

    /**
     * 修改申请退款
     */
    public function applyEdit($request, $oid, $pid, $prop_id = 0)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $orderRefundService = new OrderRefundService();
        //退款申请详情
        $refund = $orderRefundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $pid, 'prop_id' => $prop_id])->getInfo();
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }

        //有修改则获取最新修改的申请
        $refund = $this->getLatestEditApply($refund);

        //订单详情
        $orderData = OrderService::init('wid', $refund['wid'])->getInfo($oid);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        if ($request->isMethod('post')) {
            $input = $request->input();
            //图片最多三张
            $imgs = $input['data']['imgs'] ?? '';
            if ($imgs && count($imgs) > 3) {
                $resultArr['errMsg'] = '图片最多三张';
                return $resultArr;
            }

            $data = [
                'id' => $refund['id'],
                /*'amount' => $input['data']['amount'],
                'type' => intval($input['data']['type']),
                'order_status' => intval($input['data']['order_status']),
                'reason' => intval($input['data']['reason']),
                'phone' => $input['data']['phone'],
                'remark' => $input['data']['remark'],
                'imgs' => $imgs ? implode(',', $imgs) : '',*/
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ];

            //插入订单退款表
            $resRefund = $orderRefundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $pid, 'prop_id' => $prop_id])->update($data, false);
            if ($resRefund) {
                //同时更新订单退款状态
                $resOrder = OrderService::init('wid', $refund['wid'])
                    ->where(['id' => $oid])
                    ->update(['refund_status' => 1], false);

                //拼接修改后的申请内容 修改后的申请需要保存到退款日志 协商列表里需要具体展示
                $content = [
                    'type' => intval($input['data']['type']),
                    'order_status' => intval($input['data']['order_status']),
                    'content' => $input['data']['remark']
                ];

                //添加一条协商留言
                $data = [
                    'mid' => $refund['mid'],
                    'wid' => $refund['wid'],
                    'refund_id' => $refund['id'],
                    'status' => 4,
                    'amount' => $input['data']['amount'],
                    'reason' => intval($input['data']['reason']),
                    'phone' => $input['data']['phone'],
                    'imgs' => $imgs ? implode(',', $imgs) : '',
                    'content' => json_encode($content)
                ];
                $res = (new OrderRefundMessageService())->addMessage($data);

                //add MayJay
                if (in_array($refund['status'], [2, 5])) {
                    //退款消息提醒
                    (new NotificationModule())->publishRefundRequestNotification($oid);
                }

                $resultArr['errCode'] = 0;
                $resultArr['errMsg'] = '修改退款申请成功';

            } else {
                $resultArr['errMsg'] = '修改退款申请失败';
            }
            return $resultArr;
        } else {
            //商品详情
            $product = ProductService::getDetail($pid);
            if (empty($product)) {
                $resultArr['errMsg'] = '商品不存在';
                return $resultArr;
            }

            //商品最多可退款金额
            $refundAmountMax = $this->_getMaxRefundAmount($orderData, $pid, $prop_id);
            $orderData['refundAmountMax'] = $refundAmountMax['refundAmountMax'];
            //非0 则显示含x元运费
            $orderData['refund_freight'] = $refundAmountMax['isLastRefundProduct'] ? $orderData['freight_price'] : 0;

            $data = ['refund' => $refund, 'product' => $product, 'order' => $orderData];

            $resultArr['errCode'] = 0;
            $resultArr['errMsg'] = '获取申请退款页信息成功';
            $resultArr['data'] = $data;
            return $resultArr;
        }
    }

    /**
     * 退款订单列表
     * @param $status int 0全部 1待用户处理
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function list($wid, $mid, $status = 0)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $where = [
            'mid' => $mid,
            'wid' => $wid
        ];

        //状态过滤
        if ($status == 1) {
            //待用户处理
            $where['status'] = ['in', [2, 6]];
        }

        //退款列表
        $refundService = new OrderRefundService();
        list($list) = $refundService->init()->where($where)->getList();


        $oidArr = [];
        if ($list['total']) {
            foreach ($list['data'] as $v) {
                $oidArr[] = $v['oid'];
                $widArr[] = $v['wid'];
            }
            $oidArr = array_unique($oidArr);
        }

        //获取订单实付款
        list($orders) = OrderService::init('wid', $wid)->with(['orderDetail'])->where(['mid' => $mid, 'wid' => $wid])->getList(true, $oidArr);

        //获取店铺名
        //list($shops) = WeixinService::init()->where(['id' => $wid])->getList();
        $shopService = new ShopService();
        $shops = $shopService->getRowById($wid);
        //处理数据
        foreach ($list['data'] as $k => $v) {
            $v['pay_price'] = 0.00;
            foreach ($orders['data'] as $order) {
                if ($v['oid'] == $order['id']) {
                    $v['pay_price'] = $order['pay_price'];

                    //订单明细
                    $v['product_title'] = '';
                    $v['product_img'] = '';
                    foreach ($order['orderDetail'] as $detail) {
                        if ($v['pid'] == $detail['product_id']) {
                            $v['product_title'] = $detail['title'];
                            $v['product_img'] = $detail['img'];
                        }
                    }
                }
            }

            //店铺名 目前单店铺
            $v['shop_name'] = $shops['shop_name'] ?? '';

            $list['data'][$k] = $v;
        }

        $resultArr['errCode'] = 0;
        $resultArr['data'] = $list;
        return $resultArr;
    }

    /**
     * 退款详情页
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function detail($oid, $pid, $wid, $mid, $prop_id = 0)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        /*if ($orderData['mid'] != $mid) {
            $resultArr['errMsg'] = '不可操作别人的订单';
            return $resultArr;
        }*/

        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $pid, 'prop_id' => $prop_id])->getInfo();
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }

        //有修改则获取最新修改的申请
        $refund = $this->getLatestEditApply($refund);

        //获取申请退款7天后时间
        $refundEndTimestamp = date('Y/m/d H:i:s', strtotime($refund['created_at']) + 7 * 24 * 3600);

        //服务器当前时间 倒计时使用
        $now = date('Y/m/d H:i:s');

        $refund['refundEndTimestamp'] = $refundEndTimestamp;
        $refund['now_at'] = $now;

        //店铺名
        //$shop = WeixinService::init()->where(['id' => $wid])->getInfo($wid);
        $shopService = new ShopService();
        $shop = $shopService->getRowById($wid);
        $refund['shop_name'] = $shop['shop_name'] ?? '';

        //商品名
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            $resultArr['errMsg'] = '商品不存在';
            return $resultArr;
        }
        $refund['product_title'] = $product['title'];

        //申请退款或者修改申请退款后(所以用updated_at) 商家7天不处理 自动同意退款
        $refund['end_timestamp'] = strtotime($refund['updated_at']) + 7 * 24 * 3600;
        $refund['now_timestamp'] = time();

        if ($refund['status'] == 6) {
            //获取退货地址
            $refund['refund_address'] = (new WeixinRefundService())->getDefaultAddress($wid, (new UserService())->getInfoByWid($wid));
        }
        /*elseif ($refund['status'] == 4 || $refund['status'] == 8) {
            //判断是否是退了运费
            if ($refund['return_freight'] > 0) {
                $refund['amount'] += $orderData['freight_price'];
                $refund['amount'] =sprintf('%.2f', $refund['amount']);
            }
        }*/

        $expressMessage = [
            'express_name' => '',
            'express_no' => '',
            'content' => ''
        ];

        //发货状态 获取快递信息
        list($message) = (new OrderRefundMessageService())->init()->where(['refund_id' => $refund['id'], 'status' => 6])->getList(false);

        if ($message['data']) {
            $expressMessage = $message['data'][0] ?? [];
        }

        $data = [
            'order' => $orderData,
            'refund' => $refund,
            'message' => $expressMessage
        ];

        $resultArr['errCode'] = 0;
        $resultArr['data'] = $data;
        return $resultArr;
    }

    /**
     * 买家撤销退款
     */
    public function buyerCancel($oid, $refundID, $wid, $mid)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        //退款详情
        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $oid)->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }

        //订单详情
        $order = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($order)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }
        if ($order['mid'] != $mid) {
            $resultArr['errMsg'] = '不可操作别人的订单';
            return $resultArr;
        }

        //执行取消操作
        $this->_cancelRefund($wid, $oid, $refundID);

        //添加一条协商留言
        $data = [
            'mid' => $mid,
            'wid' => $wid,
            'refund_id' => $refundID,
            'status' => 3
        ];
        $res = (new OrderRefundMessageService())->addMessage($data);

        //添加订单日志表记录
        $log = [
            'oid' => $oid,
            'wid' => $wid,
            'mid' => $mid,
            'action' => 10,
            'remark' => '买家撤销退款'
        ];
        OrderLogService::init()->add($log, false);
        OrderService::upOrderLog($oid, $wid);

        $resultArr['errCode'] = 0;
        //返回订单groups_id字段 前端用来区分跳转到新的还是老的订单详情
        $resultArr['data'] = ['groupID' => $order['groups_id']];
        return $resultArr;
    }

    private function _cancelRefund($wid, $oid, $refundID)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 5], false);

        //一个订单中的所有退款商品都取消退款 订单状态才改为取消退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 5) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 5], false);
        }
    }

    /**
     * 协商列表
     */
    public function messages($oid, $pid, $wid, $mid, $prop_id = 0)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        //订单
        $order = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($order)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        if ($order['mid'] != $mid) {
            $resultArr['errMsg'] = '不可操作别人的订单';
            return $resultArr;
        }

        //退款详情
        $refund = (new OrderRefundService())->init('oid', $oid)->where(['oid' => $oid, 'pid' => $pid, 'prop_id' => $prop_id])->getInfo();
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }

        //商品是否在订单中
        $product = [];
        $productInOrder = false;
        foreach ($order['orderDetail'] as $v) {
            if ($v['product_id'] == $pid) {
                $product = $v;
                $productInOrder = true;
                break;
            }
        }
        if (!$productInOrder) {
            $resultArr['errMsg'] = '商品不在订单中';
            return $resultArr;
        }

        //获取买家头像
        $member = (new MemberService())->getRowById($mid);
        $refund['memberAvatar'] = $member['headimgurl'] ?? '';

        $data = [
            'order' => $order,
            'refund' => $refund,
            'product' => $product,
            'messages' => $this->getMessages($wid, $refund['id'])
        ];

        $resultArr['errCode'] = 0;
        $resultArr['data'] = $data;
        return $resultArr;
    }

    /**
     * 获取协商列表
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getMessages($wid, $refundID)
    {
        list($list) = (new OrderRefundMessageService())->init('wid', $wid)->where(['refund_id' => $refundID])->order('id desc')->getList();

        //获取买家商家头像
        $mid = $list['data'][0]['mid'] ?? 0;

        //获取买家头像
        $member = (new MemberService())->getRowById($mid);
        $memberAvatar = $member['headimgurl'] ?? '';

        //获取商家logo
        //$shop = WeixinService::init()->where(['id' => $wid])->getInfo($wid);
        $shopService = new ShopService();
        $shop = $shopService->getRowById($wid);
        $shopAvatar = !empty($shop['logo']) ? imgUrl($shop['logo']) : '';

        foreach ($list['data'] as $k => $v) {
            if ($v['status'] == 4) {
                $content = json_decode($v['content'], true);
                $v['edit_remark'] = !empty($content['content']) ? $content['content'] : '';
            } elseif ($v['status'] == 5) {
                //获取退货地址
                $v['refund_address'] = (new WeixinRefundService())->getDefaultAddress($wid, (new UserService())->getInfoByWid($wid));
            }
            $v['avatar'] = $v['is_seller'] ? $shopAvatar : $memberAvatar;
            $list['data'][$k] = $v;
        }

        return $list['data'];
    }

    /**
     * 协商列表
     */
    public function addMessage($request, $oid, $wid, $refundID, $isSeller = 0)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        //退款中 才能留言
        $refund = (new OrderRefundService())->init('oid', $oid)->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }

        //图片最多三张
        $imgs = $request->input('imgs');
        if ($imgs && count($imgs) > 3) {
            $resultArr['errMsg'] = '图片最多三张';
            return $resultArr;
        }

        //添加一条协商留言
        $data = [
            'mid' => $refund['mid'],
            'wid' => $wid,
            'refund_id' => $refundID,
            'is_seller' => intval($isSeller),
            'content' => $request->input('content') ?? '',
            'imgs' => $imgs ? implode(',', $imgs) : ''
        ];
        if ((new OrderRefundMessageService())->addMessage($data)) {
            $resultArr['errCode'] = 0;
            $resultArr['data'] = ['oid' => $oid, 'pid' => $refund['pid'], 'refundID' => $refundID];
            return $resultArr;
        } else {
            $resultArr['errMsg'] = '添加留言失败';
            return $resultArr;
        }
    }

    /**
     * 商家同意退货
     */
    public function agreeReturn($request, $oid, $wid, $refundID)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        $refund = (new OrderRefundService())->init('oid', $oid)->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }
        if ($refund['status'] != 1) {
            $resultArr['errMsg'] = '不在退款申请中状态';
            return $resultArr;
        }

        $remark = $request->input('remark') ?? '';
        $this->_agreeReturn($wid, $oid, $refundID);

        //添加一条协商留言
        $data = [
            'mid' => $refund['mid'],
            'wid' => $wid,
            'refund_id' => $refundID,
            'is_seller' => 1,
            'status' => 5,
            'content' => $remark
        ];
        (new OrderRefundMessageService())->addMessage($data);

        $resultArr['errCode'] = 0;
        return $resultArr;
    }

    private function _agreeReturn($wid, $oid, $refundID)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 6], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 6) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 6], false);
        }
    }

    /**
     * 商家同意退款
     * @param int $oid 订单id
     * @param int $wid 店铺id
     * @param int $refundID 退款id
     * @return array
     * @author 许立 2018年04月02日
     * @update 许立 2018年08月01日 增加支付宝退款
     */
    public function sellerAgree($oid, $wid, $refundID)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $oid)->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }
        if ($refund['status'] != 1 && $refund['status'] != 7) {
            $resultArr['errMsg'] = '不在可同意退款状态';
            return $resultArr;
        }

        //todo 调用微信退款接口 是否需要考虑用crontab做
        $wxRefundModule = new WeChatRefundModule($wid, $oid, $refund['pid'], $refund['prop_id']);
        $wxResult = $wxRefundModule->refund();
        if ($wxResult['code'] == 'SUCCESS') {
            if ($wxResult['code_des'] == '订单已全额退款') {
                //商家在微信商家后台直接退款 没有走本系统流程退款 设置退款成功 Herry 20180409 @todo 微信后台退款部分金额怎么处理？
                $this->success($oid, $refundID, '支付用户零钱');
            } else {
                //非余额支付订单的退款 后续操作
                if ($orderData['pay_way'] != 3 && $orderData['pay_way'] != 2) {
                    $this->_agreeRefund($wid, $oid, $refundID);

                    //添加一条协商留言
                    $data = [
                        'mid' => $refund['mid'],
                        'wid' => $wid,
                        'refund_id' => $refundID,
                        'is_seller' => 1,
                        'status' => 2,
                        'content' => $refund['amount'],
                        'amount' => $refund['amount']
                    ];
                    (new OrderRefundMessageService())->addMessage($data);

                    //添加订单日志表记录
                    $log = [
                        'oid' => $oid,
                        'wid' => $wid,
                        'mid' => $orderData['mid'],
                        'action' => 8,
                        'remark' => '商家同意退款'
                    ];
                    OrderLogService::init()->add($log, false);
                    OrderService::upOrderLog($oid, $wid);

                    //退款状态设置为退款微信审核中
                    $this->_wxRefund($wid, $oid, $refundID);

                    //添加一条协商留言
                    $data = [
                        'mid' => $refund['mid'],
                        'wid' => $wid,
                        'refund_id' => $refundID,
                        'is_seller' => 1,
                        'status' => 7,
                        'content' => '退款成功'
                    ];
                    (new OrderRefundMessageService())->addMessage($data);
                }
            }
        } else {
            $resultArr['errMsg'] = $wxResult['code_des'];
            return $resultArr;
        }

        $resultArr['errCode'] = 0;
        return $resultArr;
    }

    public function _agreeRefund($wid, $oid, $refundID)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 3, 'agree_at' => date('Y-m-d H:i:s')], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 3) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 3], false);
        }
    }

    /**
     * 商家拒绝退款
     */
    public function sellerDisagree($request, $oid, $wid, $refundID)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        $refund = (new OrderRefundService())->init('oid', $oid)->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }
        if ($refund['status'] != 1 && $refund['status'] != 7) {
            $resultArr['errMsg'] = '不在可拒绝退款状态';
            return $resultArr;
        }

        $seller_remark = $request->input('remark') ?? '';
        $this->_disagreeRefund($wid, $oid, $refundID, $seller_remark);

        //添加一条协商留言
        $data = [
            'mid' => $refund['mid'],
            'wid' => $wid,
            'refund_id' => $refundID,
            'is_seller' => 1,
            'status' => 1,
            'content' => $seller_remark
        ];
        (new OrderRefundMessageService())->addMessage($data);

        //添加订单日志表记录
        $log = [
            'oid' => $oid,
            'wid' => $wid,
            'mid' => $orderData['mid'],
            'action' => 9,
            'remark' => '商家拒绝退款'
        ];
        OrderLogService::init()->add($log, false);
        OrderService::upOrderLog($oid, $wid);

        $resultArr['errCode'] = 0;
        return $resultArr;
    }

    private function _disagreeRefund($wid, $oid, $refundID, $seller_remark)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 2, 'seller_remark' => $seller_remark], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 2) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 2], false);
        }
    }

    /**
     * 买家退款发货
     */
    public function refundReturn($request, $refundID)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $refund = (new OrderRefundService())->init()->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }
        if ($refund['status'] != 6) {
            $resultArr['errMsg'] = '不在可退款退货状态';
            return $resultArr;
        }

        $orderData = OrderService::init('wid', $refund['wid'])->getInfo($refund['oid']);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        $input = $request->input('data');
        $imgs = $input['imgs'];
        if ($imgs && count($imgs) > 3) {
            $resultArr['errMsg'] = '图片最多三张';
            return $resultArr;
        }

        $this->_refundReturn($refund['wid'], $refund['oid'], $refundID);

        //添加一条协商留言
        $data = [
            'mid' => $refund['mid'],
            'wid' => $refund['wid'],
            'refund_id' => $refundID,
            'status' => 6,
            'content' => $input['remark'],
            'imgs' => $imgs ? implode(',', $imgs) : '',
            'express_name' => $input['express_name'] ?? '',
            'express_no' => $input['express_no'] ?? ''
        ];

        (new OrderRefundMessageService())->addMessage($data);

        $resultArr['errCode'] = 0;
        $resultArr['errMsg'] = '退款发货成功';
        $resultArr['data'] = ['oid' => $refund['oid'], 'pid' => $refund['pid'], 'prop_id' => $refund['prop_id']];
        return $resultArr;
    }

    private function _refundReturn($wid, $oid, $refundID)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 7], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 7) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 7], false);
        }
    }

    private function _closeRefund($wid, $oid, $refundID)
    {
        //退款完成的退款不能关闭 Herry 20180105
        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $oid)->getInfo($refundID);
        if ($refund['status'] == 4 || $refund['status'] == 8) {
            return false;
        }

        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 9], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变 其余逻辑同理
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 9) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 9], false);
        }
    }

    private function _autoAgreeRefund($wid, $oid, $refundID)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 10, 'agree_at' => date('Y-m-d H:i:s')], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 10) {
                $allFlag = false;
                break;
            }
        }
        if ($allFlag) {
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update(['refund_status' => 10], false);
        }
    }

    private function _wxRefund($wid, $oid, $refundID)
    {
        $orderRefundService = new OrderRefundService();

        //一个订单中所有商品都退款完成 才改变订单状态（如3个商品 2个申请退款并成功 不关闭订单） Herry 20180104
        //todo 为什么取不到orderDetail 数据库 redis都有数据 其他方法中 getInfo能取到orderdetail
        //$orderData = OrderService::init('wid', $wid)->getInfo($oid);
        $orderData = OrderService::init('wid', $wid)->model->find($oid)->load('orderDetail')->toArray();

        $refundWhere = ['status' => 4, 'verify_at' => date('Y-m-d H:i:s')];
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update($refundWhere, false);

        //判断是否是退了运费
        $hasRefundCount = $orderRefundService->init('oid', $oid)->model->where(['oid' => $oid])->whereIn('status', [4, 8])->count();
        $detailCount = count($orderData['orderDetail']);
        if ($hasRefundCount == $detailCount) {
            $latestSuccessRefund = $orderRefundService->init('oid', $oid)->model->where(['oid' => $oid])->whereIn('status', [4, 8])->orderBy('success_at', 'desc')->take(1)->get()->toArray();
            if ($latestSuccessRefund && $latestSuccessRefund[0]['id'] == $refundID) {
                $orderRefundService->init('oid', $oid)
                    ->where(['id' => $refundID])
                    ->update(['return_freight' => $orderData['freight_price']], false);
            }
        }

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            //4或8都属于退款完成
            if ($v['status'] != 4 && $v['status'] != 8) {
                $allFlag = false;
                break;
            }
        }

        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105
        if ($allFlag) {
            $orderWhere = ['refund_status' => 4];
            if (count($orderData['orderDetail']) == count($refunds['data'])) {
                $orderWhere = ['refund_status' => 4, 'status' => 4];
            }
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update($orderWhere, false);
        }

        //todo 检查订单的发货状态
        OrderService::checkDeliver($oid, $wid);
        //检查订单评价状态
        OrderService::checkEvaluate($oid, $wid);
    }

    /**
     * 退款到账 退款最后一步完成
     * @param $wxReturnData array 微信退款成功回调解密后数据 重要字段如下
     * out_trade_no order表主键(需要截取)
     * out_refund_no order_refund表主键
     * refund_recv_accout 退款入账账户 如：招商银行信用卡0403
     */
    public function success($orderID, $refundID, $account)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        if (empty($orderID)) {
            $resultArr['errMsg'] = '订单ID不存在';
            return $resultArr;
        }

        //订单
        $orderData = OrderService::init()->model->find($orderID);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        //如果退款到账 则返回 因为微信回调成功后还会回调 避免多次发送模板或多次报错
        if ($orderData->refund_status == 8) {
            $resultArr['errCode'] = 1000;
            $resultArr['errMsg'] = '退款已经成功';
            return $resultArr;
        }

        //未成团订单退款单独处理 未成团 且 脚本后台没有模拟申请退款流程
        $logs = OrderLogService::init()->model->where(['oid' => $orderID, 'action' => 7])->get()->toArray();
        if ($orderData->groups_status == 3 && empty($logs)) {
            return $this->groupOrderRefund($orderData);
        }
        //退款
        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $orderID)->getInfo($refundID);
        if (empty($refund)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }
        /*if ($refund['status'] != 4) {
            $resultArr['errMsg'] = '不在微信退款审核中状态';
            return $resultArr;
        }*/

        $this->_success($orderData->wid, $orderID, $refundID, $account);

        //添加一条协商留言
        /*$data = [
            'mid' => $refund['mid'],
            'wid' => $orderData->wid,
            'refund_id' => $refund['id'],
            'is_seller' => 1,
            'status' => 7,
            'content' => $account //原路退回的银行卡号等信息 或者余额退款等
        ];
        (new OrderRefundMessageService())->addMessage($data);*/

        $resultArr['errCode'] = 0;
        return $resultArr;
    }


    private function _success($wid, $oid, $refundID, $receiveAccount)
    {
        $orderRefundService = new OrderRefundService();
        // @update 张永辉 2020年2月20日10:23:48 标记退款成功时写入同意时间，确认时间信息
        $now = date('Y-m-d H:i:s');
        $orderRefundService->init('oid', $oid)
            ->where(['id' => $refundID])
            ->update(['status' => 8, 'agree_at' => $now, 'verify_at' => $now, 'success_at' => $now, 'remark' => $receiveAccount], false);

        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $allFlag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 8) {
                $allFlag = false;
                break;
            }
        }

        //一个订单中所有商品都退款完成 才改变订单状态（如3个商品 2个申请退款并成功 不关闭订单） Herry 20180104
        //todo 为什么取不到orderDetail 数据库 redis都有数据 其他方法中 getInfo能取到orderdetail
        //$orderData = OrderService::init('wid', $wid)->getInfo($oid);
        $orderData = OrderService::init('wid', $wid)->model->find($oid)->load('orderDetail')->toArray();
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8 Herry 鹏飞 20180105

        if ($allFlag) {
            $orderWhere = ['refund_status' => 8];
            if (count($orderData['orderDetail']) == count($refunds['data'])) {
                $orderWhere = ['refund_status' => 8, 'status' => 4];
            }
            OrderService::init('wid', $wid)
                ->where(['id' => $oid])
                ->update($orderWhere, false);
        }

        //todo 检查订单的发货状态
        OrderService::checkDeliver($oid, $wid);
        //检查订单评价状态
        OrderService::checkEvaluate($oid, $wid);
    }

    /**
     * 逾期未处理退款脚本
     * @author 许立 2017年10月20日
     * @update 许立 2018年08月01日 增加支付宝退款
     */
    public function autoExpireRefund()
    {
        //取出未处理超过7天的退款
        DB::table('order_refund')->select('id', 'status', 'wid', 'mid', 'oid', 'pid', 'updated_at', 'prop_id')
            ->whereIn('status', [1, 2, 6, 7])
            ->where('created_at', '>', '2017-11-06 19:51:00')
            ->where('updated_at', '<', date('Y-m-d H:i:s', (time() - 7 * 24 * 3600)))
            ->chunk(100, function ($refunds) {
                $refundMessageService = new OrderRefundMessageService();
                foreach ($refunds as $refund) {
                    try {
                        //协商留言类型
                        $status = 0;
                        $isSeller = 0;
                        $content = '';

                        //订单详情
                        $order = OrderService::init('wid', $refund->wid)->getInfo($refund->oid);

                        switch ($refund->status) {
                            case 1:
                            case 7:
                                //申请中 a普通退款 b退货退款 商家超时未处理 自动同意
                                $status = 8;
                                $isSeller = 1;
                                $content = '商家超时未处理 自动同意退款';
                                //todo 调用微信退款接口 是否需要考虑用crontab做
                                $wxRefundModule = new WeChatRefundModule($refund->wid, $refund->oid, $refund->pid, $refund->prop_id);
                                $wxResult = $wxRefundModule->refund();
                                //\Log::info('[退款id:'.$refund->id.']申请中微信回调: '.json_encode($wxResult));
                                if (isset($wxResult['code']) && $wxResult['code'] == 'SUCCESS') {
                                    if ($wxResult['code_des'] == '订单已全额退款') {
                                        //商家在微信商家后台直接退款 没有走本系统流程退款 设置退款成功 Herry 20180409 @todo 微信后台退款部分金额怎么处理？
                                        $this->success($refund->oid, $refund->id, '支付用户零钱');
                                    } else {
                                        if ($order['pay_way'] != 3 && $order['pay_way'] != 2) {
                                            //非余额支付订单的退款 才改变状态 Herry 20171226
                                            //添加一条协商留言
                                            $data = [
                                                'mid' => $refund->mid,
                                                'wid' => $refund->wid,
                                                'refund_id' => $refund->id,
                                                'is_seller' => $isSeller,
                                                'status' => $status,
                                                'content' => $content
                                            ];
                                            $refundMessageService->addMessage($data);

                                            //审核成功才同意退款 不然要用事务
                                            $this->_autoAgreeRefund($refund->wid, $refund->oid, $refund->id);

                                            //退款状态设置为退款微信审核中
                                            $this->_wxRefund($refund->wid, $refund->oid, $refund->id);

                                            //添加一条协商留言
                                            $data = [
                                                'mid' => $refund->mid,
                                                'wid' => $refund->wid,
                                                'refund_id' => $refund->id,
                                                'is_seller' => 1,
                                                'status' => 7,
                                                'content' => '退款成功, 微信审核中'
                                            ];
                                            (new OrderRefundMessageService())->addMessage($data);
                                        }
                                    }
                                } else {
                                    //审核失败 更新退款申请时间 继续7天倒计时 继续等待商家处理
                                    $this->_updateTime($refund->id, $refund->oid, $refund->pid, $refund->prop_id);
                                }
                                break;
                            case 2:
                                //申请退款被拒绝 买家处理逾期 则自动关闭退款申请
                                $status = 10;
                                $content = '申请退款被拒绝, 买家处理逾期, 自动关闭申请';

                                //添加一条协商留言
                                $data = [
                                    'mid' => $refund->mid,
                                    'wid' => $refund->wid,
                                    'refund_id' => $refund->id,
                                    'is_seller' => $isSeller,
                                    'status' => $status,
                                    'content' => $content
                                ];
                                $refundMessageService->addMessage($data);

                                $this->_closeRefund($refund->wid, $refund->oid, $refund->id);
                                break;
                            case 3:
                                //商家同意退款 微信审核失败 继续审核
                                //todo 调用微信退款接口 是否需要考虑用crontab做
                                $wxRefundModule = new WeChatRefundModule($refund->wid, $refund->oid, $refund->pid, $refund->prop_id);
                                $wxResult = $wxRefundModule->refund();
                                //\Log::info('[退款id:'.$refund->id.']申请同意微信回调: '.json_encode($wxResult));
                                if (isset($wxResult['code']) && $wxResult['code'] == 'SUCCESS') {
                                    if ($wxResult['code_des'] == '订单已全额退款') {
                                        //商家在微信商家后台直接退款 没有走本系统流程退款 设置退款成功 Herry 20180409 @todo 微信后台退款部分金额怎么处理？
                                        $this->success($refund->oid, $refund->id, '支付用户零钱');
                                    } else {
                                        if ($order['pay_way'] != 3 && $order['pay_way'] != 2) {
                                            //非余额支付订单的退款 才改变状态 Herry 20171226
                                            //添加一条协商留言
                                            $data = [
                                                'mid' => $refund->mid,
                                                'wid' => $refund->wid,
                                                'refund_id' => $refund->id,
                                                'is_seller' => $isSeller,
                                                'status' => $status,
                                                'content' => $content
                                            ];
                                            $refundMessageService->addMessage($data);

                                            //退款状态设置为退款微信审核中
                                            $this->_wxRefund($refund->wid, $refund->oid, $refund->id);

                                            //添加一条协商留言
                                            $data = [
                                                'mid' => $refund->mid,
                                                'wid' => $refund->wid,
                                                'refund_id' => $refund->id,
                                                'is_seller' => 1,
                                                'status' => 7,
                                                'content' => '退款成功, 微信审核中'
                                            ];
                                            (new OrderRefundMessageService())->addMessage($data);
                                        }
                                    }
                                } else {
                                    //审核失败 更新退款申请时间 继续7天倒计时 继续等待商家处理
                                    $this->_updateTime($refund->id, $refund->oid, $refund->pid, $refund->prop_id);
                                }
                                break;
                            case 6:
                                //商家同意退款 买家发货逾期 则自动关闭退款申请
                                $status = 9;
                                $content = '商家同意退款. 买家发货逾期, 则自动关闭退款申请';
                                //添加一条协商留言
                                $data = [
                                    'mid' => $refund->mid,
                                    'wid' => $refund->wid,
                                    'refund_id' => $refund->id,
                                    'is_seller' => $isSeller,
                                    'status' => $status,
                                    'content' => $content
                                ];
                                $refundMessageService->addMessage($data);
                                $this->_closeRefund($refund->wid, $refund->oid, $refund->id);
                                break;
                            default:
                                break;
                        }
                    } catch (\Exception $e) {
                        \Log::info($e->getMessage());
                        continue;
                    }
                }
            });
    }

    /**
     * 退款微信审核 钱款去向
     */
    public function refundVerify($refundID)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        $fields = ['r.return_freight', 'm.nickname', 'm.headimgurl', 'o.pay_way', 'r.amount', 'r.remark', 'r.status', 'r.agree_at', 'r.verify_at', 'r.success_at', 'r.id', 'r.oid', 'r.wid'];
        $res = DB::table('order_refund as r')
            ->leftJoin('order as o', 'r.oid', '=', 'o.id')
            ->leftJoin('member as m', 'r.mid', '=', 'm.id')
            ->where('r.id', $refundID)
            ->get($fields)
            ->first();

        if (empty($res)) {
            $resultArr['errMsg'] = '退款不存在';
            return $resultArr;
        }

        //订单详情
        $orderData = OrderService::init('wid', $res->wid)->getInfo($res->oid);
        if (empty($orderData)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        //判断是否是退了运费
        /*if ($res->return_freight > 0) {
            $res->amount += $orderData['freight_price'];
            $res->amount =sprintf('%.2f', $res->amount);
        }*/

        //获取最新退款金额 有可能退款申请修改过 Herry
        $refund = (new OrderRefundService())->init('oid', $res->oid)->getInfo($refundID);
        $refund_latest = $this->getLatestEditApply($refund);
        $res->amount = $refund_latest['amount'];

        $resultArr['data'] = json_decode(json_encode($res), true);
        $resultArr['errCode'] = 0;
        return $resultArr;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 未成团订单退款完成回调处理
     * @desc 20171026
     * @param $orderData
     */
    public function groupOrderRefund($orderData)
    {
        $upData = [
            'id' => $orderData->id,
            'status' => 4, //todo 被合并冲突解决错误文件暂时找回 可能有遗漏 20171121 16:52 Herry
            'refund_status' => 8,
        ];
        OrderService::init()->where(['id' => $orderData->id])->update($upData, false);
        $resultArr = [
            'errCode' => 0,
            'errMsg' => '',
            'data' => []
        ];
        return $resultArr;
    }

    /**
     * 获取商品最多可退款金额
     * @param $orderData
     * @param $pid
     * @return array
     * @update 许立 2018年09月17日 有会员折扣价 商品的退款金额比例按折扣价来计算
     */
    private function _getMaxRefundAmount($orderData, $pid, $prop_id)
    {
        $refundAmountMax = 0.00;
        //当前商品在订单中的购买详情
        $orderProduct = [];
        $currentProductPrice = 0.00;
        $otherProductsPrice = 0.00;
        foreach ($orderData['orderDetail'] as $v) {
            $productAmount = ($v['after_discount_price'] > 0 ? $v['after_discount_price'] : $v['price']) * $v['num'];
            if ($v['product_id'] == $pid && $v['product_prop_id'] == $prop_id) {
                $orderProduct = $v;
                $currentProductPrice = $productAmount;
            } else {
                $otherProductsPrice += $productAmount;
            }
        }

        if (empty($currentProductPrice)) {
            $resultArr['errMsg'] = '商品价格不合法';
            return $resultArr;
        }

        if (empty($orderProduct)) {
            $resultArr['errMsg'] = '商品购买信息不存在';
            return $resultArr;
        }

        //一个订单里的最后一个商品申请退款时，运费会被计算在可退款金额内，此时可以将运费退掉 @鹏飞 20180103
        //2申请退款被拒,5买家取消退款,9退款申请关闭（退货申请超时未发货，商家拒绝未继续修改等操作） 一订单多商品 这几种情况算没申请过退款
        /*$refundCount = (new OrderRefundService())->init()->model->where('oid', $orderData['id'])->whereNotIn('status', [2, 5, 9])->count();
        $orderProductCount = count($orderData['orderDetail']);*/

        //错误的订单号 add meiJie
        $errIds = [2720, 2719, 2715, 2710, 2705, 2703, 2700, 2699, 2697, 2696, 2659, 2658, 2614, 2561, 2560,
            2559, 2558, 2485, 2482, 2479, 2476, 2465, 2464, 2434];
        if (in_array($orderData['id'], $errIds)) {
            $orderData['pay_price'] = 19.89;
        }

        //todo 最大金额最终需求：只在微信打款计算金额判断 是否订单内n-1个商品都退款成功 如果是 则最后一个退款金额加上运费
        //订单可退款总金额 a先除去运费
        $orderRefundAmount = $orderData['pay_price'] - $orderData['freight_price'];

        //订单可退款总金额 b按比例计算当前商品占比
        $refundAmountMax = ($orderRefundAmount * $currentProductPrice) / ($currentProductPrice + $otherProductsPrice);

        //判断是否是退了运费
        $orderRefundService = new OrderRefundService();
        $hasRefundCount = $orderRefundService->init('oid', $orderData['id'])->model->where(['oid' => $orderData['id']])->whereIn('status', [4, 8])->count();
        $detailCount = count($orderData['orderDetail']);
        $isLastRefundProduct = 0;
        if ($hasRefundCount == $detailCount - 1) {
            foreach ($orderData['orderDetail'] as $detail) {
                $where = [
                    'oid' => $detail['oid'],
                    'product_id' => $pid,
                    'product_prop_id' => $prop_id
                ];
                $orderDetailData = OrderDetailService::init()->model->wheres($where)->get()->toArray();
                $orderDetailData = $orderDetailData ? $orderDetailData[0] : [];
                if ($orderDetailData && $orderDetailData['is_delivery'] == 0 && $orderDetailData['oid'] == $orderData['id'] && $orderDetailData['product_id'] == $pid && $orderDetailData['product_prop_id'] == $prop_id) {
                    $isLastRefundProduct = 1;
                    $refundAmountMax += $orderData['freight_price'];
                    break;
                }
            }
        }

        //格式化金额 @todo number_format会出现逗号 判断大小 会有问题
        //$refundAmountMax = number_format($refundAmountMax, 2);
        $refundAmountMax = sprintf('%.2f', $refundAmountMax);

        return [
            'refundAmountMax' => $refundAmountMax,
            'isLastRefundProduct' => $isLastRefundProduct
        ];
    }

    /**
     * 确认收货后关闭退款
     * @param $oid
     * @return array
     */
    public function closeAfterReceive($oid)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        //订单
        $order = OrderService::init()->model->find($oid);
        if (empty($order)) {
            $resultArr['errMsg'] = '订单不存在';
            return $resultArr;
        }

        //查询该订单的所有退款
        $refundService = new OrderRefundService();
        $refunds = $refundService->init()->model->where('oid', $oid)->get()->toArray();
        if ($refunds) {
            foreach ($refunds as $refund) {
                $this->_closeRefund($refund['wid'], $refund['oid'], $refund['id']);
            }
        }

        $resultArr['errCode'] = 0;
        return $resultArr;
    }

    /**
     * 未成团订单关闭退款
     */
    public function closeGroupOrderApplyRefund($order, $pid, $payPrice, $propID = 0)
    {
        //退款是否已经存在
        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $order['id'])->where(['oid' => $order['id'], 'pid' => $pid, 'prop_id' => $propID])->getInfo();
        if (empty($refund)) {
            $now = date('Y-m-d H:i:s');
            $data = [
                'mid' => $order['mid'],
                'wid' => $order['wid'],
                'oid' => $order['id'],
                'pid' => $pid,
                'prop_id' => $propID,
                'amount' => $payPrice,
                'phone' => $order['address_phone'] ?? 0,
                'remark' => '未成团订单已付款用户系统代退款',
                'created_at' => $now,
                'updated_at' => $now
            ];

            //插入订单退款表
            $resRefund = $orderRefundService->init('oid', $order['id'])->add($data, false);
            if ($resRefund) {
                //更新订单表 产品需求：订单中只要有一个商品退款中 则属于退款订单，订单内全部商品退款关闭 则订单才算退款关闭
                //订单status不变 refund_status改为退款状态
                OrderService::init('wid', $order['wid'])
                    ->where(['id' => $order['id']])
                    ->update(['refund_status' => 1], false);

                //添加订单日志表记录
                $log = [
                    'oid' => $order['id'],
                    'mid' => $order['mid'],
                    'wid' => $order['wid'],
                    'action' => 7,
                    'remark' => '未成团订单系统代申请退款'
                ];
                OrderLogService::init()->add($log, false);
                OrderService::upOrderLog($order['id'], $order['wid']);
            }
        } else {
            //修改更新时间
            $this->_updateTime($refund['id'], $order['id'], $pid, $propID);
        }
    }

    /**
     * 更新退款更新时间
     */
    private function _updateTime($refundID, $oid, $pid, $prop_id = 0)
    {
        $where = ['oid' => $oid, 'pid' => $pid, 'prop_id' => $prop_id];
        $refundService = new OrderRefundService();
        $num = $refundService->init('oid', $oid)->model->wheres($where)->count();
        if ($num == 1) {
            //正常退款记录 最多只有一条
            $refundService->init('oid', $oid)->where($where)->update(['id' => $refundID, 'updated_at' => date('Y-m-d H:i:s')], false);
        }
    }

    /**
     * 获取最新一条修改的申请
     */
    public function getLatestEditApply($refund)
    {
        $row = (new OrderRefundMessageService())->init()
            ->model
            ->wheres(['refund_id' => $refund['id'], 'status' => 4])
            ->orderBy('created_at', 'desc')
            ->first();
        $row = $row ? $row->toArray() : [];

        if ($row) {
            //更新退款申请为修改之后的申请
            $content = json_decode($row['content'], true);
            $refund['amount'] = $row['amount'];
            $refund['type'] = $content['type'];
            $refund['order_status'] = $content['order_status'];
            $refund['reason'] = $row['reason'];
            $refund['phone'] = $row['phone'];
            $refund['remark'] = $content['content'];
            $refund['imgs'] = $row['imgs'];
            $refund['created_at'] = $row['created_at'];
        }

        return $refund;
    }

    /**
     * 标记退款完成
     * @param array $input 退款相关参数
     * @return array
     * @author 许立 2018年7月2日
     * @modify author 张国军 2018年08月06日 退款过滤卡密订单
     */
    public function manuallySuccess($input)
    {
        // 返回格式
        $return = [
            'err_code' => 1,
            'err_msg' => ''
        ];

        // 订单详情
        $order = OrderService::init()->getInfo($input['oid']);
        if (empty($order)) {
            $return['err_msg'] = '订单不存在';
            return $return;
        }

        //add by 张国军 处理卡密订单逻辑
        if (isset($order['type']) && $order['type'] == 12) {
            $return['err_msg'] = '虚拟订单[卡密订单]不能够退款';
            return $return;
        }

        // 首先判断买家是否发起过退款申请
        $refund_service = new OrderRefundService();
        $refund = $refund_service->init('oid', $input['oid'])->where(['oid' => $input['oid'], 'pid' => $input['pid'], 'prop_id' => $input['prop_id']])->getInfo();
        if (empty($refund)) {
            // 退款不存在 系统模拟一条退款申请
            $now = date('Y-m-d H:i:s');
            $data = [
                'mid' => $order['mid'],
                'wid' => $order['wid'],
                'oid' => $order['id'],
                'pid' => $input['pid'],
                'prop_id' => $input['prop_id'],
                'amount' => $this->_getMaxRefundAmount($order, $input['pid'], $input['prop_id'])['refundAmountMax'],
                'phone' => $order['address_phone'] ?? 0,
                'remark' => '商家手动标记退款完成系统代申请退款',
                'created_at' => $now,
                'updated_at' => $now
            ];
            // 插入订单退款表
            $refund_id = $refund_service->init('oid', $order['id'])->add($data, false);
            if ($refund_id) {
                // 更新订单表 产品需求：订单中只要有一个商品退款中 则属于退款订单，订单内全部商品退款关闭 则订单才算退款关闭
                // 订单status不变 refund_status改为退款状态
                OrderService::init('wid', $order['wid'])
                    ->where(['id' => $order['id']])
                    ->update(['refund_status' => 1], false);

                // 添加订单日志表记录
                $log = [
                    'oid' => $order['id'],
                    'mid' => $order['mid'],
                    'wid' => $order['wid'],
                    'action' => 7,
                    'remark' => '商家手动标记退款完成系统代申请退款'
                ];
                OrderLogService::init()->add($log, false);
                OrderService::upOrderLog($order['id'], $order['wid']);
            }
        } else {
            $refund_id = $refund['id'];
        }

        // 手动退款完成
        $res = $this->success($order['id'], $refund_id, '线下打款账号');

        //添加一条协商留言
        $data = [
            'mid' => $order['mid'],
            'wid' => $order['wid'],
            'refund_id' => $refund_id,
            'is_seller' => 1,
            'status' => 7,
            'content' => '标记退款成功'
        ];
        (new OrderRefundMessageService())->addMessage($data);

        if ($res['errCode']) {
            $return['err_msg'] = $res['errMsg'];
        } else {
            $return = [
                'err_code' => 0,
                'err_msg' => '成功标记退款'
            ];
        }

        return $return;
    }

}

