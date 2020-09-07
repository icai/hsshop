<?php

/**
 * 发送微信公众号模板消息队列
 */

namespace App\Jobs;

use App\Model\Member;
use App\Module\WechatBakModule;
use App\S\Book\BookService;
use App\S\Customer\PointRecordService;
use App\S\Groups\GroupsDetailService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use App\S\Message\MessageTemplateService;
use App\S\Order\OrderService;
use App\S\Weixin\ShopService;
use App\Services\Order\LogisticsService;
use App\Services\Order\OrderRefundService;
use App\Services\Wechat\CustomService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendWeChatTemplatesMsg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    private $wid;
    private $data;
    private $type;


    public $tries = 3;
    public $timeout = 60;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid,$type,$data)
    {
        //
        $this->wid = $wid;
        $this->data = $data;
        $this->type = $type;
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
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        switch ($this->type) {
            case MessagesPushService::CustomMessage:
                $this->customMessageUnread();
                break;
            case MessagesPushService::EnrollOnline:
                $this->enrollOnlineSuccess();
                break;
            case MessagesPushService::PaySuccess:
                $this->paySuccess();
                break;
            case MessagesPushService::TradeUrge:
                $this->waitPay();
                break;
            case MessagesPushService::DeliverySuccess:
                $this->orderDeliverySuccess();
                break;
            case MessagesPushService::OrderRefund:
                $this->orderRefund();
                break;
            case MessagesPushService::NewOrder:
                $this->newOrder();
                break;
            case MessagesPushService::ActivityGroup:
                $this->activityGroup();
                break;
            case MessagesPushService::GetMemberCard:
                $this->grantCard();
                break;
            case MessagesPushService::ActivityBook:
                $this->activityBook();
                break;
            case MessagesPushService::ProductAdvanceSale:
                $this->ProductAdvanceSale();
                break;
            case MessagesPushService::ServerExpire:
                // $this->advanceServerExpire();
                break;
            case MessagesPushService::CommissionGrant:
                $this->commissionGrant();
                break;
            case MessagesPushService::BecomeChild:
                $this->becomeChild();
                break;
            case MessagesPushService::BecomePromoter:
                $this->becomePromoter();
                break;
            case MessagesPushService::PointConsume:
                $this->pointConsume();
                break;
            case MessagesPushService::MSG_REPLY:
                $this->msgReply();
                break;
            default:
                break;

        }

        return ;


    }



    /**
     * 客服消息未读
     * @param $data
     * @author: 梅杰 2018年10月11号
     */
    public function customMessageUnread()
    {
        $data['touser']             = $this->data['open_id'];
        $data['data']['first']      = ['value' => "你好,“".$this->data['shop_name']."”管理员，你店铺有新的客服消息咨询尚未处理"];
        $data['data']['keyword1']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['keyword2']   = ['value' => '请您看到消息后登录商家后台及时回复处理'];
        $data['data']['remark']     = ['value' => '感谢您对会搜云的支持'];
        $re = (new WechatBakModule())->sendTplNotify($this->wid,$data,WechatBakModule::CUSTOM_MESSAGE_UNREAD);
    }



    /**
     * 在线报名成功（目前给商家发送模板消息）
     * @param $data
     * @author 何书哲 2018年10月12日
     *
     * {{first.DATA}} '您好，<xx>店铺管理员，您店铺有新的报名信息尚未处理
     * 申请结果：{{keyword1.DATA}} 报名成功
     * 通过时间：{{keyword2.DATA}} 年-月-日 时:分
     * {{remark.DATA}} 您看到消息后请商家后台及时处理，感谢您对会搜云的支持！
     */
    private function enrollOnlineSuccess()
    {
        $data['touser']        = $this->data['open_id'];
        $data['data']['first'] = ['value' => '您好，<'.$this->data['shop_name'].'>店铺管理员，您店铺有新的报名信息尚未处理'];
        $data['data']['keyword1']   = ['value' => '报名成功'];
        $data['data']['keyword2']   = ['value' => date('Y-m-d H:i')];
        $data['data']['remark']     = ['value' => '您看到消息后请登录商家后台及时处理，感谢您对会搜云的支持'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::ENROLL_ONLINE_SUCCESS);
    }



    /**
     * 付款成功
     *
     */
    private function paySuccess()
    {
        $orderInfo = (new OrderService())->getOrderDetailByOid( $this->data);
        $member = (new MemberService())->getRowById($orderInfo['mid']);

        $data['touser'] = $member['openid'];
        $data['data']['first']   = [
            'value' => '亲，我们已收到你的货款，会尽快为你打包，请耐心等待 :）',
        ];
        if($orderInfo['groups_id'] == 0){
            $data['url']    = config('app.url').'/shop/order/detail/'.$orderInfo['id'].'/'.$this->wid;
        }else{
            $data['url']    = config('app.url').'/shop/order/groupsOrderDetail/'.$orderInfo['id'].'/'.$this->wid;
        }
        $title = '';
        foreach ($orderInfo['orderDetail'] as $value) {
            $title .= $value['title'] ." ";
        }
        $data['data']['orderProductPrice'] = [
            'value' => $orderInfo['pay_price']
        ];
        $data['data']['orderName'] = [
            'value' => $orderInfo['oid']
        ];
        $data['data']['orderAddress'] = [
            'value' => $orderInfo['type'] == 12 ? '虚拟商品无需收货地址' : $orderInfo['address_detail']
        ];
        $data['data']['orderProductName']   = [
            'value' => $title,
            'color' => '#000000'
        ];
        $data['data']['remark'] = [
            'value' => "查看订单详情",
            'color' => '#000000'
        ];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::PAY_SUCCESS);
    }



    /**
     * 订单发货成功
     * @author: 梅杰 2018年8月13日 兼容
     */
    private function orderDeliverySuccess()
    {
        //获取用户openid
        if ( !($orderData = (new OrderService())->getRowByWhere(['id' => $this->data['oid']])) ) {
            return ;
        }
        $member  = (new MemberService())->getRowById($orderData['mid']);
        $data['touser'] = $member['openid'];
        $data['data']['first']   = [
            'value' => '亲，你的宝贝已在路上啦，正全速向你飞奔～',
        ];
        //获取物流信息
        $logisticsData = (new LogisticsService())->init()->where(['oid'=>$orderData['id'],'odid' => $this->data['odid'] ])->getInfo();
        $data['data']['keyword1']   = [
            'value' => $orderData['oid'],
        ];
        if($logisticsData['no_express'] == 1){
            if($orderData['groups_id'] == 0){
                $data['url']    = config('app.url').'/shop/order/detail/'.$orderData['id'].'/'.$orderData['wid'];
            }else{
                $data['url']    = config('app.url').'/shop/order/groupsOrderDetail/'.$orderData['id'].'/'.$orderData['wid'];
            }
            $expressName = '无需物流';
            if ($orderData['type'] == 12) {
                $expressName = '虚拟商品，无需发货';
            }
            if ($orderData['is_hexiao']) {
                $expressName = '用户到店自提，无需发货';
            }

            $data['data']['keyword2']   = [
                'value' => $expressName ,
            ];
            $data['data']['keyword3']   = [
                'value' => '无',
            ];
            $data['data']['remark'] = [
                'value' => "查看订单详情",
                'color' => '#000000'
            ];
        }else{
            $data['url']    = config('app.url').'/shop/order/expresslist/'.$orderData['wid'].'/'.$orderData['id'];
            $data['data']['keyword2']   = [
                'value' => $logisticsData['express_name'],
            ];
            $data['data']['keyword3']   = [
                'value' => $logisticsData['logistic_no'],
            ];
            $data['data']['remark'] = [
                'value' => "查看物流信息",
                'color' => '#000000'
            ];
        }
        $data['data']['keyword4']   = [
            'value' => $orderData['pay_price'],
        ];
        $data['data']['keyword5']   = [
            'value' => $orderData['address_detail'].','.$orderData['address_name'].','.$orderData['address_phone'],
        ];
        $wechatBakModule = new WechatBakModule();
        $wechatBakModule->sendTplNotify($this->wid, $data,$wechatBakModule::DELIVERY_SUCCESS);

    }



    /**
    {{first.DATA}}
    订单名称：{{keyword1.DATA}}
    下单时间：{{keyword2.DATA}}
    订单金额：{{keyword3.DATA}}
    订单信息：{{keyword4.DATA}}
    {{remark.DATA}}
     */
    private function newOrder()
    {
        $orderInfo = (new OrderService())->getOrderDetailByOid( $this->data['oid']);
        $member = (new MemberService())->getRowById($orderInfo['mid']);
        $data['touser'] = $this->data['openid'];
        $store = (new ShopService())->getRowById($this->wid);
        $data['data']['first']   = [
            'value' =>  '店铺“'.$store['shop_name'].'”,管理员，您有新订单了~',
        ];
//        $data['url']    = config('app.url').'/merchants/order/orderDetail/'.$id;
        $title = '';
        foreach ($orderInfo['orderDetail'] as $value) {
            $title .= $value['title'] ." ";
        }
        $data['data']['keyword1'] = [
            'value' => $title
        ];
        $data['data']['keyword2'] = [
            'value' => $orderInfo['created_at']
        ];
        $data['data']['keyword3'] = [
            'value' => $orderInfo['pay_price'],
            'color' => '#000000'
        ];
        $data['data']['keyword4'] = [
            'value' => $title.','.$member['truename'].','.$orderInfo['address_detail'],
            'color' => '#000000'
        ];
        $data['data']['remark'] = [
            'value' => "请登录商家后台及时处理",
        ];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::ORDER_SUCCESS);
    }

    private function activityGroup()
    {
        switch ($this->data['group_type'])
        {
            case 'new_group':
                $this->newGroupMsg();
                break;
            case 'group_success':
                $this->groupSuccessMsg();
                break;
            case 'group_dead_time':
                $this->groupDeadTimeMsg();
                break;
            case 'group_close':
                $this->groupCloseMsg();
                break;
            default:
                break;
        }
    }

    private function activityBook()
    {
        switch ($this->data['book_type'])
        {
            case 'new_mic_subscribe':
                $this->NewMirSubscribe();
                break;
            case 'mic_subscribe_success':
                $this->mirSubscribeSuccess();
                break;
            case 'mic_subscribe_refuse':
                $this->mirSubscribeRefuse();
                break;
            default:
                break;
        }
    }

    private function newGroupMsg()
    {
        $orderInfo = (new OrderService())->getOrderDetailByOid( $this->data['oid']);
        if (!$orderInfo) {
            return;
        }
        $member = (new MemberService())->getRowById($orderInfo['mid']);
        $data['touser']             = $member['openid'];
        $data['msgtype']            = 'text';
        $url = config('app.url')."shop/grouppurchase/groupon/{$orderInfo['groups_id']}/{$orderInfo['wid']}";
        $data['text']               = [
            'content'               => "拼团通知:您购买的拼团商品".'"'.$orderInfo['orderDetail'][0]['title']. '"，实付金额：'.$orderInfo['cash_fee']. "元。已支付成功！快把参团链接分享给好友或朋友圈，邀请好友参团吧。<a href='".$url."'>查看团订单</a>"
        ];
        $re = (new CustomService($this->wid))->sendMsg($data);
    }

    private function groupSuccessMsg()
    {
        $orderInfo = (new OrderService())->getOrderDetailByOid( $this->data['oid']);
        if (!$orderInfo) {
            return;
        }
        $member = (new MemberService())->getRowById($this->data['mid']);
        $data['touser']             = $member['openid'];
        $data['msgtype']            = 'text';
        $url = config('app.url')."shop/grouppurchase/groupon/{$orderInfo['groups_id']}/{$orderInfo['wid']}";
        $data['text']               = [
            'content'               => "拼团通知:您购买的拼团商品".'"'.$orderInfo['orderDetail'][0]['title'].'"'."已成团，商家将尽快发货。<a href='".$url."'>查看团订单</a>"
        ];
        $re = (new CustomService($this->wid))->sendMsg($data);

    }

    private function groupDeadTimeMsg()
    {
        $orderInfo = (new OrderService())->getOrderDetailByOid( $this->data['oid']);
        if (!$orderInfo || $orderInfo && $orderInfo['groups_status'] == 2) {
            return;
        }
        $member = (new MemberService())->getRowById($orderInfo['mid']);
        $groupsDetailService = new GroupsDetailService();
        $count = $groupsDetailService->model->where('groups_id',$orderInfo['groups_id'])->count();
        $left  = $this->data['num'] - $count;
        $data['touser']             = $member['openid'];
        $data['msgtype']            = 'text';
        $url = config('app.url')."shop/grouppurchase/groupon/{$orderInfo['groups_id']}/{$orderInfo['wid']}";
        $data['text']               = [
            'content'               => "拼团通知:团长大人您发起的".'"'.$orderInfo['orderDetail'][0]['title'].'"'."拼团离成团还差".$left."人呢。快把参团链接分享给好友或朋友圈，邀请好友参团吧。<a href='".$url."'>查看团订单</a>"
        ];
        $re = (new CustomService($this->wid))->sendMsg($data);
    }

    private function groupCloseMsg()
    {
        $orderInfo = (new OrderService())->getOrderDetailByOid( $this->data['oid']);
        if (!$orderInfo || $orderInfo && $orderInfo['groups_status'] == 2) {
            return;
        }
        $member = (new MemberService())->getRowById($orderInfo['mid']);
        $data['touser']             = $member['openid'];
        $data['msgtype']            = 'text';
        $url = config('app.url')."shop/grouppurchase/groupon/{$orderInfo['groups_id']}/{$orderInfo['wid']}";
        $data['text']               = [
            'content'               => "拼团通知:您购买的".'"'.$orderInfo['orderDetail'][0]['title'].'"'."拼团商品已经关闭，买家将自动退款，请注意查收。<a href='".$url."'>查看团订单</a>"
        ];
        $re = (new CustomService($this->wid))->sendMsg($data);
    }

    private function grantCard()
    {
        $mid    = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!empty($member['openid']) && !empty($this->data['card_title'])){
            $store = (new ShopService())->getRowById($this->wid);
            $data['touser']                    = $member['openid'];
            $data['url']                       = config('app.url').'shop/member/mycards/'.$this->wid;
            $data['data']['first']['value']    = '您的'.$store['shop_name'].'店铺的会员卡已成功领取';
            $data['data']['keyword1']['value'] = $this->data['card_title'];
            $data['data']['keyword2']['value'] = $this->data['card_num'];
            $data['data']['remark']['value']   = '您已领取'.$store['shop_name'].'店铺的'.$this->data['card_title'].'会员卡，在本店消费可享受更多优惠哦～～';
            $wechatBakModule = new WechatBakModule();
            $wechatBakModule->sendTplNotify($this->wid, $data, $wechatBakModule::GRANT_CARD);
        }
    }


    /**
     * {{first.DATA}}
    预约时间：{{keyword1.DATA}}
    预约内容：{{keyword2.DATA}}
    {{remark.DATA}}
     */
    private function NewMirSubscribe()
    {
        $data =  $this->data;
        $mid = $data['mid'];
        $member = (new MemberService())->getRowById($mid);
        $bookService = new BookService();
        $book = $bookService->getRowById($data['book_id']);
        $data['touser'] = $member['openid'];
        $data['url'] = config('app.url') . 'shop/book/detail/' . $book['wid'] . '/' . $data['book_id'];
        $data['data']['first'] = ['value' => '预约提醒'];
        $data['data']['keyword1'] = ['value' => $data['book_time']];
        $data['data']['keyword2'] = ['value' => $book['title']];
        $data['data']['remark'] = ['value' => '查看预约详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'], $data, WechatBakModule::NEW_MIC_SUBSCRIBE);
    }


    /**
     * {{first.DATA}}
    预约人：{{keyword1.DATA}}
    预约内容：{{keyword2.DATA}}
    联系方式：{{keyword3.DATA}}
    {{remark.DATA}}
     */
    private function mirSubscribeSuccess()
    {
        $data =  $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $bookService                = new MessageTemplateService();
        $book                       = $bookService->getRowById($data['book_id']);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/book/detail/'.$book['wid'].'/'.$data['book_id'];
        $data['data']['first']      = ['value' => '您的预约已被确认'];
        $data['data']['keyword1']   = ['value' => $member['truename']];
        $data['data']['keyword2']   = ['value' => $book['title']];
        $data['data']['keyword3']   = ['value' => $book['phone']];
        $data['data']['remark']     = ['value' => '查看预约详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,WechatBakModule::MIC_SUBSCRIBE_SUCCESS);
    }

    /**
    {{first.DATA}}
    预约项目：{{keyword1.DATA}}
    预约时间：{{keyword2.DATA}}
    取消原因：{{keyword3.DATA}}
    {{remark.DATA}}
     */
    public function mirSubscribeRefuse()
    {
        $data = $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $bookService                = new BookService();
        $book                       = $bookService->getRowById($data['book_id']);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/book/detail/'.$book['wid'].'/'.$data['book_id'];
        $data['data']['first']      = ['value' => '您的预约已被取消'];
        $data['data']['keyword1']   = ['value' => $book['title']];
        $data['data']['keyword2']   = ['value' => $data['book_time']];
        $data['data']['keyword3']   = ['value' => $data['content'] ];
        $data['data']['remark']     = ['value' => '查看预约详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,WechatBakModule::MIC_SUBSCRIBE_REFUSE);
    }


    /**
     * 商品预售通知
     * @param $data
     * @author: 梅杰 2018年8月3日
     */
    private function ProductAdvanceSale()
    {
        $data = $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $bookService                = new BookService();
        $book                       = $bookService->getRowById($data['book_id']);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/book/detail/'.$book['wid'].'/'.$data['book_id'];
        $data['data']['first']      = ['value' => '预约商品开售提醒'];
        $data['data']['keyword1']   = ['value' => '开售内容'];
        $data['data']['keyword2']   = ['value' => '开售时间'];
        $data['data']['remark']     = ['value' => '查看详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,WechatBakModule::PRODUCT_ADVANCE_NOTIFY);
    }

    /**
     * 活动过期
     * @param $data
     * @author: 梅杰 2018年8月3号
     */
    public function advanceServerExpire()
    {
        $data = $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $data['touser']             = $member['openid'];
//        $data['url']                = config('app.url').'shop/book/detail/'.$book['wid'].'/'.$data['book_id'];
        $data['data']['first']      = ['value' => '您的预约已被确认'];
        $data['data']['keyword1']   = ['value' => '预约项目'];
        $data['data']['keyword2']   = ['value' => '预约时间'];
        $data['data']['remark']     = ['value' => '点击查看详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,WechatBakModule::SERVER_EXPIRE_NOTIFY);

    }

    private function orderRefund()
    {
        if ($refund = (new OrderRefundService())->init()->where(['id' => $this->data])->getInfo()) {
            $member = (new MemberService())->getRowById($refund['mid']);
            $data['touser'] = $member['openid'];
            $order = (new OrderService())->getRowByWhere(['id'=>$refund['oid']]);
            $data['url']                = config('app.url').'shop/order/refundDetailView/'.$this->wid.'/'.$refund['oid'].'/'.$refund['pid'].'/'.$refund['prop_id'];
            $data['data']['first']      = ['value' => '商家已经同意您的退款申请'];
            $data['data']['keyword1']   = [
                'value' => $order['oid'],
            ];
            $data['data']['keyword2']   = [
                'value' => $refund['amount'],
            ];
            $data['data']['remark']     = [
                'value' => empty($refund['remark']) ? $refund['remark'] : "退款成功",
            ];
            $service = new WechatBakModule();
            $service->sendTplNotify($this->wid,$data,$service::REFUND_SUCCESS);
        }

    }

    //待付款
    private function waitPay()
    {
        $order = (new OrderService())->getOrderDetailByOid($this->data);
        if ($order['status'] != 0) {
            return ;
        }
        $member =  (new MemberService())->getRowById($order['mid']);
        $data['touser'] = $member['openid'];
        $data['url']                =  $data['url']    = config('app.url').'/shop/order/detail/'.$order['id'].'/'.$this->wid;
        $data['data']['first']      = ['value' => '亲，您的订单还未付款哦，再不付款宝贝就被别人买走啦～'];
        $data['data']['keyword1']   = ['value' => $order['oid']];
        $data['data']['keyword2']   = ['value' => $order['pay_price']];
        $data['data']['keyword3']   = ['value' => $order['created_at']];
        $data['data']['remark']     = ['value' => '点击查看详情'];

        $service = new WechatBakModule();
        $service->sendTplNotify($this->wid,$data,$service::WAIT_PAY_TPL_NOTIFY);

    }

    //佣金
    public function commissionGrant() {
        switch ($this->data['commission_type'])
        {
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

    private function commissionOrder() {
        $data =  $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/distribute/wealth?wid='.$this->wid;
        $data['data']['first']      = ['value' => '恭喜您获得了一笔新佣金'];
        $data['data']['keyword1']   = ['value' => sprintf('%.2f', $data['money'])];
        $data['data']['keyword2']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['remark']     = ['value' => '请点击会员中心-我的财富查看详情'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::COMMISSION_GRANT_NOTIFY);
    }

    private function commissionAccount() {
        $data =  $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/distribute/wealth?wid='.$this->wid;
        $data['data']['first']      = ['value' => '恭喜您的一笔佣金已到账'];
        $data['data']['keyword1']   = ['value' => sprintf('%.2f', $data['money'])];
        $data['data']['keyword2']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['remark']     = ['value' => '请点击会员中心-我的财富查看详情'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::COMMISSION_GRANT_NOTIFY);
    }

    private function commissionDistribute() {
        $data =  $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/distribute/wealth?wid='.$this->wid;
        $data['data']['first']      = ['value' => '恭喜您的一笔佣金已发放'];
        $data['data']['keyword1']   = ['value' => sprintf('%.2f', $data['money'])];
        $data['data']['keyword2']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['remark']     = ['value' => '请点击会员中心-我的财富查看详情'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::COMMISSION_GRANT_NOTIFY);
    }

    private function becomeChild() {
        $data =  $this->data;
        $memberService = new MemberService();
        $memberParent               = $memberService->getRowById($data['pid']);//上级
        $member                     = $memberService->getRowById($data['mid']);//下级
        if (empty($memberParent) || empty($memberParent['openid'])) {
            return false;
        }
        $data['touser']             = $memberParent['openid'];
        $data['url']                = config('app.url').'shop/distribute/wealth?wid='.$this->wid;
        $data['data']['first']      = ['value' => '尊敬的推广员，您有新的推广下级'];
        $data['data']['keyword1']   = ['value' => $member['nickname']??''];
        $data['data']['keyword2']   = ['value' => '通过'];
        $data['data']['keyword3']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['remark']     = ['value' => '请前往店铺我的-会员中心-我的财富查看详情！'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::BECOME_CHILD_NOTIFY);
    }

    private function becomePromoter() {
        $data =  $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        if (empty($member) || empty($member['openid'])) {
            return false;
        }
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/distribute/wealth?wid='.$this->wid;
        $data['data']['first']      = ['value' => '店铺推广员申请通知结果'];
        $data['data']['keyword1']   = ['value' => $member['nickname']??'']; //推广员微信昵称
        $data['data']['keyword2']   = ['value' => '通过'];
        $data['data']['keyword3']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['remark']     = ['value' => '请前往店铺我的-会员中心-我的财富查看详情！'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::BECOME_PROMOTER_NOTIFY);
    }

    /**
    {{first.DATA}}
    服务内容：{{keyword1.DATA}}
    积分变化：{{keyword2.DATA}}
    商户名称：{{keyword3.DATA}}
    日期时间：{{keyword4.DATA}}
    {{remark.DATA}}
     */
    private function pointConsume() {
        $data =  $this->data;
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $shop                       = (new ShopService())->getRowById($this->wid);
        if (empty($member) || empty($shop)) {
            return false;
        }
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/point/mypoint?wid='.$this->wid;
        $data['data']['first']      = ['value' => ($data['is_add'] == 1 ? '恭喜您获得新积分' : '您有新的积分消费')];
        $data['data']['keyword1']   = ['value' => (new PointRecordService())->getPointType($data['point_type'], $data['is_add'])];
        $data['data']['keyword2']   = ['value' => ($data['is_add'] == 1 ? '增加' : '减少').strval($data['score']).'积分'];
        $data['data']['keyword3']   = ['value' => $shop['shop_name']];
        $data['data']['keyword4']   = ['value' => date('Y-m-d H:i:s')];
        $data['data']['remark']     = ['value' => '请前往店铺我的-会员中心-我的积分查看详情！'];
        (new WechatBakModule())->sendTplNotify($this->wid, $data,WechatBakModule::POINT_CONSUME_NOTIFY);

    }


    /**
     * 客服留言回复
     *
     * @return bool 是否发送成功
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月24日 11:31:59
     */
    private function msgReply()
    {
        $member = Member::with("merchant:id,shop_name,logo")
            ->where('wid', $this->data['shopId'])
            ->select(["openid", "wid", "nickname", "mobile", "headimgurl"])
            ->find($this->data['toUser']);

        if (!$member || empty($member->openid)) {
            return false;
        }

        $data['touser'] = $member->openid;
        $url = config('app.chat_url') . "/#/kefu?userId={$this->data['toUser']}&shopId={$this->data['shopId']}&username={$member->nickname}&headurl={$member->headimgurl}&shopName={$member->merchant->shop_name}&shopLogo=" . config("app.url") . $member->merchant->logo;
        $data['msgtype'] = 'text';
        $data['text'] = [
            'content' => "客服通知：您有新的客服消息，请及时查看。<a href='" . $url . "'>（查看客服消息）</a>"
        ];
        $re = (new CustomService($this->wid))->sendMsg($data);
    }

}
