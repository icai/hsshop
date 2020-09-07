<?php

namespace App\Jobs;

use App\Model\Member;
use App\S\Customer\PointRecordService;
use App\S\Market\SignRecordService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use App\S\Order\OrderService;
use App\S\Weixin\ShopService;
use App\S\WXXCX\WXXCXCollectFormIdService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Order\LogisticsService;
use App\Services\Order\OrderRefundService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWeChatXcxTemplatesMsg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $type;
    private $data;
    private $wid;
    private $xcxConfigId;


    /**
     * Create a new job instance.
     * @param $wid
     * @param $xcxConfigId
     * @param $msgType
     * @param $data
     */
    public function __construct($wid, $xcxConfigId, $msgType, $data)
    {
        //
        $this->type = $msgType;
        $this->wid = $wid;
        $this->xcxConfigId = $xcxConfigId;
        $this->data = $data;
        $this->queue = 'messagePush';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }

        switch ($this->type) {
            case MessagesPushService::PaySuccess: //付款成功
                $this->paySuccess();
                break;
            case MessagesPushService::TradeUrge:  //待支付
                $this->waitPay();
                break;
            case MessagesPushService::DeliverySuccess: //发货成功
                $this->deliverySuccess();
                break;
            case MessagesPushService::OrderRefund: //订单退款
                $this->refund();
                break;
            case MessagesPushService::GetMemberCard: //获取会员卡
                $this->grantCard();
                break;
            case MessagesPushService::SignSuccess: //签到成功
                $this->signSuccess();
                break;
            case MessagesPushService::CommissionGrant: //佣金
                $this->commissionGrant();
                break;
            case MessagesPushService::BecomeChild: //成为下级
                $this->becomeChild();
                break;
            case MessagesPushService::BecomePromoter: //成为推广员
                $this->becomePromoter();
                break;
            case MessagesPushService::PointConsume: //积分消费
                $this->pointConsume();
                break;
            case MessagesPushService::MSG_REPLY:
                $this->msgReply();
                break;
            default:
                break;
        }

        return;
    }


    /**
     * 付款成功
     * 订单编号{{keyword1.DATA}}
     * 交易时间{{keyword2.DATA}}
     * 交易金额{{keyword3.DATA}}
     * 实付金额{{keyword4.DATA}}
     * 买家姓名{{keyword5.DATA}}
     * 收货地址{{keyword6.DATA}}
     * 备注{{keyword7.DATA}}
     * @update 梅杰 虚拟商品收货地址为无需物流
     * @update: 跳转链接修改 梅杰[meijie3169@dingtalk.com] at 2019年06月21日 11:38:05
     */
    private function paySuccess()
    {
        if ($orderInfo = (new OrderService())->getRowByWhere(['id' => $this->data])) {
            $member = (new MemberService())->getRowById($orderInfo['mid']);
            $data['touser'] = $member['xcx_openid'];
            if ($orderInfo['pay_way'] == 3) {
                $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($orderInfo['mid']);;
            } else {
                $data['form_id'] = $orderInfo['prepay_id'];
            }
            $data['page'] = 'pages/main/pages/order/orderDetail/orderDetail?flag=1&oid=' . $orderInfo['id'];
            $data['data']['keyword1'] = [
                'value' => $orderInfo['oid'],
            ];
            $data['data']['keyword2'] = [
                'value' => $orderInfo['created_at'],
            ];
            $data['data']['keyword3'] = [
                'value' => $orderInfo['products_price'],
            ];
            $data['data']['keyword4'] = [
                'value' => $orderInfo['pay_price'],
            ];
            $data['data']['keyword5'] = [
                'value' => $member['truename'],
            ];
            $data['data']['keyword6'] = [
                'value' => $orderInfo['type'] == 12 ? '无需物流' : $orderInfo['address_detail'],
            ];
            $data['data']['keyword7'] = [
                'value' => '订单已支付成功',
            ];
            $sendTplService = new WXXCXSendTplService($this->wid, $orderInfo['xcx_config_id']);
            $sendTplService->sendTplNotify($data, $sendTplService::TRADE_NOTIFY, $data['page']);
        }


    }


    /**
     * 待支付提醒
     * @update: 跳转链接修改 梅杰[meijie3169@dingtalk.com] at 2019年06月21日 11:38:05
     */
    private function waitPay()
    {

        if ($order = (new OrderService())->getOrderDetailByOid($this->data)) {
            if ($order['status'] != 0) {
                return;
            }
            $member = (new MemberService())->getRowById($order['mid']);
            $data['touser'] = $member['xcx_openid'];
            $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($order['mid']);
            $data['page'] = 'pages/main/pages/order/orderDetail/orderDetail?flag=1&oid=' . $order['id'];
            $title = '';
            foreach ($order['orderDetail'] as $value) {
                $title .= $value['title'] . " ";
            }
            $data['data']['keyword1'] = [
                'value' => $order['oid'],
            ];
            $data['data']['keyword2'] = [
                'value' => $title,
            ];
            $data['data']['keyword3'] = [
                'value' => $order['pay_price'],
            ];
            $data['data']['keyword4'] = [
                'value' => '您有未付款订单请及时处理',
            ];
            $data['data']['keyword5'] = [
                'value' => '点击进入订单详情页完成支付',
            ];
            $re = (new WXXCXSendTplService($this->wid, $this->xcxConfigId))->sendTplNotify($data, WXXCXSendTplService::WAIT_ORDER_NOTIFY, $data['page']);
        }

    }

    /**
     * 发货成功
     * @update: 跳转链接修改 梅杰[meijie3169@dingtalk.com] at 2019年06月21日 11:38:05
     */
    private function deliverySuccess()
    {
        $shopService = new ShopService();
        $store = $shopService->getRowById($this->wid);

        $orderInfo = (new OrderService())->getRowByWhere(['id' => $this->data['oid']]);

        $logisticsData = (new LogisticsService())->init()->where(['oid' => $this->data['oid'], 'odid' => $this->data['odid']])->getInfo();
        $member = (new MemberService())->getRowById($orderInfo['mid']);
        $data['touser'] = $member['xcx_openid'];

        if ($data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($orderInfo['mid'])) {
            $data['page'] = 'pages/main/pages/order/orderDetail/orderDetail?flag=1&oid=' . $orderInfo['id'];
            $data['data']['keyword1'] = [
                'value' => $orderInfo['oid'],
            ];
            $data['data']['keyword2'] = [
                'value' => $store['shop_name'],
            ];
            $data['data']['keyword3'] = [
                'value' => $logisticsData['created_at'],
            ];
            if ($logisticsData['no_express'] == 1) {
                $data['data']['keyword4'] = [
                    'value' => '无需物流',
                ];
                $data['data']['keyword5'] = [
                    'value' => '无',
                ];
                $data['data']['keyword6'] = [
                    'value' => '无需物流',
                ];
            } else {
                $data['data']['keyword4'] = [
                    'value' => $logisticsData['express_name'],
                ];
                $data['data']['keyword5'] = [
                    'value' => $logisticsData['logistic_no'],
                ];
                $data['data']['keyword6'] = [
                    'value' => $orderInfo['address_detail'],
                ];
            }
            $sendTplService = new WXXCXSendTplService($this->wid, $orderInfo['xcx_config_id']);
            $sendTplService->sendTplNotify($data, $sendTplService::DELIVER_NOTIFY, $data['page']);
        }
    }

    /**
     * 退款成功
     * @update: 跳转链接修改 梅杰[meijie3169@dingtalk.com] at 2019年06月21日 11:38:05
     */
    private function refund()
    {
        if ($refund = (new OrderRefundService())->init()->where(['id' => $this->data])->getInfo()) {
            $member = (new MemberService())->getRowById($refund['mid']);
            $order = (new OrderService())->getRowByWhere(['id' => $refund['oid']]);
            $shopService = new ShopService();
            $store = $shopService->getRowById($this->wid);
            $data['touser'] = $member['xcx_openid'];
            $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($refund['mid']);
//        if ($refund['isGroupAutoRefund'] == 1) {    (new WXXCXCollectFormIdService())->getFormId($member['id'])
//            if ($refund['groups_id']) {
//                $data['page']    = '/pages/grouppurchase/orderDetail/orderDetail?oid='.$refund['order_id'].'&groups_id='.$refund['groups_id'];
//            } else {
//                $data['page']    = '/pages/order/orderDetail/orderDetail?oid='.$refund['order_id'];
//            }
//        } else {
            $data['page'] = '/pages/member/refund/details/details?oid=' . $refund['oid'] . '&pid=' . $refund['pid'];
//        }
            $data['data']['keyword1'] = [
                'value' => $order['oid'],
            ];
            $data['data']['keyword2'] = [
                'value' => $store['shop_name'],
            ];
            $data['data']['keyword3'] = [
                'value' => $refund['amount'],
            ];
            $data['data']['keyword4'] = [
                'value' => date('Y-m-d H:i:s'),
            ];
            $data['data']['keyword5'] = [
                'value' => empty($refund['remark']) ? $refund['remark'] : "退款成功"
            ];

            $service = new WXXCXSendTplService($this->wid, $order['xcx_config_id']);
            $service->sendTplNotify($data, $service::REFUND_NOTIFY, $data['page']);
        }

    }

    /**
     * 发放会员卡
     * @update 何书哲 2019年06月28日 会员卡名称、会员卡数量不存在处理
     */
    private function grantCard()
    {
        $mid = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!empty($member['xcx_openid'])) {
            $data['touser'] = $member['xcx_openid'];
            $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
            if (!$data['form_id']) {
                return false;
            }
            $data['page'] = 'pages/main/pages/member/memberCard/memberCard';
            // update 何书哲 2019年06月28日 会员卡名称、会员卡数量不存在处理
            $data['data']['keyword1'] = [
                'value' => $this->data['card_title'] ?? '',
            ];
            $data['data']['keyword2'] = [
                'value' => $this->data['card_num'] ?? 0,
            ];
            $data['data']['keyword3'] = [
                'value' => date('Y-m-d H:i:s', time()),
            ];
            $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
            $sendTplService->sendTplNotify($data, $sendTplService::GRANT_CARD_NOTIFY, $data['page']);
        }
    }

    private function signSuccess()
    {
        $mid = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!$member || $member && empty($member['xcx_openid'])) {
            return;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return false;
        }
        $re = SignRecordService::getUserSign($this->data);
        $data['page'] = 'pages/activity/pages/activity/sign/sign';
        $data['data']['keyword1'] = [
            'value' => $member['truename'],
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => '您已成功签到' . ($re['errCode'] == 0 ? $re['data']['signDay'] : 0) . '次',
        ];
        $data['data']['keyword4'] = [
            'value' => "签到可获得更多奖励哦。点击查看更多。",
        ];
        $sendTplService = new WXXCXSendTplService($this->wid);
        $sendTplService->sendTplNotify($data, $sendTplService::SIGN_SUCCESS_NOTIFY, $data['page']);
    }

    private function commissionGrant()
    {
        switch ($this->data['commission_type']) {
            case 'commission_order'://下级下单的佣金提醒
                $this->commissionOrder();
                break;
            case 'commission_account'://佣金到账提醒
                $this->commissionAccount();
                break;
            case 'commission_distribute'://佣金发放提醒
                $this->commissionDistribute();
                break;
            default:
                break;
        }
    }

    /**
     * 获得佣金
     * @update 何书哲 2019年06月28日 分销金额不存在处理
     */
    private function commissionOrder()
    {
        $mid = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!$member || $member && empty($member['xcx_openid'])) {
            return;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return false;
        }
        $data['page'] = 'pages/main/pages/member/distribute/distribute/distribute';
        // update 何书哲 2019年06月28日 分销金额不存在处理
        $data['data']['keyword1'] = [
            'value' => sprintf('%.2f', $this->data['money'] ?? 0),
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => '恭喜您获得了一笔新佣金',
        ];
        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        $sendTplService->sendTplNotify($data, $sendTplService::COMMISSION_DISTRIBUTE_NOTIFY, $data['page']);
    }

    /**
     * 佣金到账
     * @update 何书哲 2019年06月28日 分销金额不存在处理
     */
    private function commissionAccount()
    {
        $mid = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!$member || $member && empty($member['xcx_openid'])) {
            return;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return false;
        }
        $data['page'] = 'pages/main/pages/member/distribute/distribute/distribute';
        // update 何书哲 2019年06月28日 分销金额不存在处理
        $data['data']['keyword1'] = [
            'value' => sprintf('%.2f', $this->data['money'] ?? 0),
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => '恭喜您的一笔佣金已到账',
        ];
        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        $sendTplService->sendTplNotify($data, $sendTplService::COMMISSION_DISTRIBUTE_NOTIFY, $data['page']);
    }

    /**
     * 发放佣金
     * @update 何书哲 2019年06月28日 分销金额不存在处理
     */
    private function commissionDistribute()
    {
        $mid = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!$member || $member && empty($member['xcx_openid'])) {
            return;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return false;
        }
        $data['page'] = 'pages/main/pages/member/distribute/distribute/distribute';
        // update 何书哲 2019年06月28日 分销金额不存在处理
        $data['data']['keyword1'] = [
            'value' => sprintf('%.2f', $this->data['money'] ?? 0),
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => '恭喜您的一笔佣金已发放',
        ];
        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        $sendTplService->sendTplNotify($data, $sendTplService::COMMISSION_DISTRIBUTE_NOTIFY, $data['page']);
    }

    private function becomeChild()
    {
        $data = $this->data;
        $memberService = new MemberService();

        $memberParent = $memberService->getRowById($data['pid']);
        if (empty($memberParent) || empty($memberParent['xcx_openid'])) {
            return false;
        }
        $member = $memberService->getRowById($data['mid']);//下级

        $data['touser'] = $memberParent['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($data['pid']);
        if (!$data['form_id']) {
            return false;
        }
        $data['page'] = 'pages/main/pages/member/distribute/distribute/distribute';
        $data['data']['keyword1'] = [
            'value' => '尊敬的推广员，您有新的推广下级',
        ];
        $data['data']['keyword2'] = [
            'value' => $member['nickname'] ?? '',
        ];
        $data['data']['keyword3'] = [
            'value' => '通过',
        ];
        $data['data']['keyword4'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        $sendTplService->sendTplNotify($data, $sendTplService::BECOME_CHILD_NOTIFY, $data['page']);
    }

    private function becomePromoter()
    {
        $mid = $this->data['mid'];
        $memberService = new MemberService();

        $member = $memberService->getRowById($mid);
        if (empty($member) || empty($member['xcx_openid'])) {
            return false;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return false;
        }
        $data['page'] = 'pages/main/pages/member/distribute/distribute/distribute';
        $data['data']['keyword1'] = [
            'value' => '店铺推广员申请通知结果',
        ];
        $data['data']['keyword2'] = [
            'value' => $member['nickname'] ?? '',
        ];
        $data['data']['keyword3'] = [
            'value' => '通过',
        ];
        $data['data']['keyword4'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        $sendTplService->sendTplNotify($data, $sendTplService::BECOME_PROMOTER_NOTIFY, $data['page']);
    }

    private function pointConsume()
    {
        $data = $this->data;
        $memberService = new MemberService();

        $member = $memberService->getRowById($data['mid']);
        if (!$member || $member && empty($member['xcx_openid'])) {
            return false;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($data['mid']);
        if (!$data['form_id']) {
            return false;
        }

        $pointText = (new PointRecordService())->getPointType($data['point_type'], $data['is_add']);

        $data['page'] = 'pages/main/pages/member/point/mypoint/mypoint';
        $data['data']['keyword1'] = [
            'value' => $pointText,
        ];
        $data['data']['keyword2'] = [
            'value' => ($data['is_add'] == 1 ? '增加' : '减少') . strval($data['score']) . '积分',
        ];
        $data['data']['keyword3'] = [
            'value' => strval($member['score']) . '积分',
        ];
        $data['data']['keyword4'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        $sendTplService->sendTplNotify($data, $sendTplService::POINT_CONSUME_NOTIFY, $data['page']);
    }


    /**
     * 留言回复
     *
     * @return bool 是否发送成功
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月23日 11:23:46
     */
    private function msgReply()
    {
        $member = Member::with("merchant:id,shop_name,logo")
            ->where('wid', $this->data['shopId'])
            ->select(["xcx_openid", "wid", "nickname", "headimgurl"])
            ->find($this->data['toUser']);

        if (!$member || empty($member->xcx_openid)) {
            return false;
        }
        $data['touser'] = $member['xcx_openid'];

        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($this->data['toUser']);
        if (!$data['form_id']) {
            return false;
        }

        $data['page'] = "pages/common/kefu/kefu?userId={$this->data['toUser']}&shopId={$this->data['shopId']}&username={$member->nickname}&headurl={$member->headimgurl}&shopName={$member->merchant->shop_name}&shopLogo=" . config("app.url") . $member->merchant->logo;
        $data['data']['keyword1'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword2'] = [
            'value' => "客服",
        ];
        $data['data']['keyword3'] = [
            'value' => "您有新的未读消息，请及时查看",
        ];

        $sendTplService = new WXXCXSendTplService($this->wid, $this->xcxConfigId);
        return $sendTplService->sendTplNotify($data, $sendTplService::MESSAGE_REPLY_NOTIFY, $data['page']);
    }

}
