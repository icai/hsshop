<?php

namespace App\Jobs;

use App\Module\WechatBakModule;
use App\S\Book\BookService;
use App\S\Groups\GroupsDetailService;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use App\S\Message\MessageTemplateService;
use App\S\WXXCX\WXXCXCollectFormIdService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Order\LogisticsService;
use App\Services\Wechat\CustomService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use OrderDetailService;
use OrderService;
use OrderLogService;
use WeixinService;
use SignRecordService;
use App\Module\AliApp\SendMessageModule;
use App\S\Weixin\ShopService;

class SendTplMsg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 支付方式定义
     */
    const WECHAT_PAY        = 1;
    const ALI_PAY           = 2;

    /**
     * 消息类型定义
     */
    CONST PAY_SUCCESS       = 1;
    CONST NEW_MIC_SUBSCRIBE         = 5;//微预约
    CONST MIC_SUBSCRIBE_SUCCESS     = 6;//微预约成功
    CONST MIC_SUBSCRIBE_REFUSE      = 7;//微预约失败

    CONST ACTIVITY_JOIN = 12;
    CONST ACTIVITY_PROC = 13;
    CONST PRIZE_GET     = 14;
    CONST ACTIVITY_EXPIRE = 15;
    CONST SIGN_SUCCESS = 16;
    CONST PRODUCT_ADVANCE_SALE = 17;
    CONST SERVER_EXPIRE = 18;


    public $tries = 3;
    public $timeout = 60;
    protected $data;
    protected $type;
    protected $service;
    protected $payWay;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = [], $type, $payWay = self::WECHAT_PAY)
    {
        //
        $this->data      = $data;
        $this->type      = $type;
        $this->payWay    = $payWay;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @update 吴晓平 增加支付宝模板发送 2018年07月30日
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        switch ($this->payWay) {

            case self::WECHAT_PAY :
                $this->WeChatMsgTemplate();
                break;

            case self::ALI_PAY :
                $this->aliMsgTemplate();
                break;
            default:
                break;

        }

    }

    protected function WeChatMsgTemplate()
    {
        switch ($this->type){
            case self::ACTIVITY_JOIN :
                $this->activityJoin();
                break;
            case self::ACTIVITY_PROC:
                $this->activityProc();
                break;
            case self::PRIZE_GET:
                $this->prizeGet();
                break;
            case  self::ACTIVITY_EXPIRE:
                $this->serverExpire();
                break;
            case  self::PRODUCT_ADVANCE_SALE:
                $this->ProductAdvanceSale($this->data);
                break;
            case  self::SERVER_EXPIRE:
                $this->advanceServerExpire($this->data);
                break;
            case self::NEW_MIC_SUBSCRIBE :
                $this->NewMirSubscribe($this->data);
                break;
            case self::MIC_SUBSCRIBE_SUCCESS :
                $this->mirSubscribeSuccess($this->data);
                break;
            case self::MIC_SUBSCRIBE_REFUSE :
                $this->mirSubscribeRefuse($this->data);
                break;
            case  self::SIGN_SUCCESS:
                $this->signSuccess();
                break;
            default :
                return ;
                break;
        }

    }

    /**
     * 支付宝支付成功后发送模板
     * @return [type] [description]
     */
    public function aliMsgTemplate()
    {
        switch ($this->type){
            case self::PAY_SUCCESS :
                 $this->aliPaySuccess($this->data);
                break;
            default :
                return ;
                break;
        }
    }






    /**
     * 支付宝支付成功发送模板消息
     * @author 吴晓平 <2018年07月30日>
     * @param  [type] $orderInfo [description]
     * @return [type]            [description]
     */
    private function aliPaySuccess($orderInfo)
    {
        $toUserId     = $orderInfo['data']['member']['ali_user_id'] ?? 0;
        $tradeNo      = $orderInfo['data']['serial_id'];
        $title = '';
        foreach ($orderInfo['data']['orderDetail'] as $value) {
            $title .= $value['title'] ." ";
        }
        $page = "pages/index/index"; //默认直接跳转到首页
        $data = [
            'keyword1' => ['value' => $title],
            'keyword2' => ['value' => $orderInfo['data']['pay_price']],
            'keyword3' => ['value' => $orderInfo['data']['created_at']]
        ];

        (new SendMessageModule())->sendPayMessage($toUserId,$tradeNo,$page,$data);
    }

    public function activityJoin()
    {
        $mid = $this->data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        if (!$member) {
            \Log::info('用户信息获取失败');
            return ;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] =  $this->data['formId'];
        if (!$data['form_id']) {
            return ;
        }
        $data['page'] = 'pages/activity/pages/shareSalezan/shareSalezan?activityId='.$this->data['event_id'].'&list_Or_url=1?shareId='.$mid;
        $data['data']['keyword1'] = [
            'value' => '好友来助力，商品免领',
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => '恭喜您成功发起集赞活动！快来邀请更多好友来点赞～',
        ];
        $sendTplService  = new WXXCXSendTplService($this->data['wid'],$this->data['xcx_config_id']);
        $re = $sendTplService->sendTplNotify($data,$sendTplService::ACTIVITY_JOIN_NOTIFY,$data['page']);
    }


    public function activityProc()
    {
        $mid = $this->data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        if (!$member) {
            \Log::info('用户信息获取失败');
            return ;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return ;
        }
        $data['page'] = 'pages/activity/pages/shareSalezan/shareSalezan?activityId='.$this->data['event_id'].'&list_Or_url=1?shareId='.$mid;
        $data['data']['keyword1'] = [
            'value' => '好友来助力，商品免费领',
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => $this->data['flag'] == 1 ? '哇哦，您的好友超赞！您已经集齐'.$this->data['num'].'位好友的赞啦':'哇，您的好友真给力！已经有'.($this->data['full'] - $this->data['num']).'位好友帮您点赞！'
        ];
        $data['data']['keyword4'] = [
            'value' => $this->data['flag'] == 1 ? '点击进来，立即免费领取（已经领取的请忽略）': '继续努力哦，还差'.($this->data['full'] - $this->data['num']).'位好友点赞啦！'
        ];
        $sendTplService  = new WXXCXSendTplService($member['wid']);
        $sendTplService->sendTplNotify($data,$sendTplService::ACTIVITY_PROC_NOTIFY,$data['page']);
    }


    public function prizeGet()
    {
        $mid = $this->data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        if (!$member) {
            \Log::info('用户信息获取失败');
            return ;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return ;
        }
        $data['page'] = 'pages/activity/pages/shareSalezan/shareSalezan?activityId='.$this->data['event_id'].'&list_Or_url=1?shareId='.$mid;
        $data['data']['keyword1'] = [
            'value' => '免费领取',
        ];
        $data['data']['keyword2'] = [
            'value' => '领取成功',
        ];
        $data['data']['keyword3'] = [
            'value' => '恭喜您领取！点击进入，查看更多信息。',
        ];
        $sendTplService  = new WXXCXSendTplService($this->data['wid']);
        $re = $sendTplService->sendTplNotify($data,$sendTplService::PRIZE_GET_NOTIFY,$data['page']);
        \Log::info($re);
    }

    public function serverExpire()
    {
        $mid = $this->data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        if (!$member) {
            \Log::info('用户信息获取失败');
            return ;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return ;
        }
//        $data['page'] = 'pages/activity/pages/shareSalezan/shareSalezan?activityId='.$this->data['event_id'].'&list_Or_url=1?shareId='.$mid;
        $data['data']['keyword1'] = [
            'value' => '免费领取',
        ];
        $data['data']['keyword2'] = [
            'value' => '领取成功',
        ];
        $data['data']['keyword3'] = [
            'value' => '查看更多信息。',
        ];
        $sendTplService  = new WXXCXSendTplService($this->data['wid']);
        $re = $sendTplService->sendTplNotify($data,$sendTplService::ACTIVITY_EXPIRE_NOTIFY,$data['page']);
        \Log::info($re);
    }


    /**
     * 商品预售通知
     * @param $data
     * @author: 梅杰 2018年8月3日
     */
    private function ProductAdvanceSale($data)
    {
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
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,self::PRODUCT_ADVANCE_SALE);
    }

    /**
     * 活动过期
     * @param $data
     * @author: 梅杰 2018年8月3号
     */
    public function advanceServerExpire($data)
    {
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $data['touser']             = $member['openid'];
//        $data['url']                = config('app.url').'shop/book/detail/'.$book['wid'].'/'.$data['book_id'];
        $data['data']['first']      = ['value' => '您的预约已被确认'];
        $data['data']['keyword1']   = ['value' => '预约项目'];
        $data['data']['keyword2']   = ['value' => '预约时间'];
        $data['data']['remark']     = ['value' => '点击查看详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,self::SERVER_EXPIRE);

    }


    /**
     * {{first.DATA}}
    预约时间：{{keyword1.DATA}}
    预约内容：{{keyword2.DATA}}
    {{remark.DATA}}
     */
    private function NewMirSubscribe($data)
    {
        $mid                        = $data['mid'];
        $member                     = (new MemberService())->getRowById($mid);
        $bookService                = new BookService();
        $book                       = $bookService->getRowById($data['book_id']);
        $data['touser']             = $member['openid'];
        $data['url']                = config('app.url').'shop/book/detail/'.$book['wid'].'/'.$data['book_id'];
        $data['data']['first']      = ['value' => '预约提醒'];
        $data['data']['keyword1']   = ['value' => $data['book_time']];
        $data['data']['keyword2']   = ['value' => $book['title']];
        $data['data']['remark']     = ['value' => '查看预约详情'];
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,self::NEW_MIC_SUBSCRIBE);
    }
    /**
     * {{first.DATA}}
    预约人：{{keyword1.DATA}}
    预约内容：{{keyword2.DATA}}
    联系方式：{{keyword3.DATA}}
    {{remark.DATA}}
     */
    private function mirSubscribeSuccess($data)
    {
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
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,self::MIC_SUBSCRIBE_SUCCESS);
    }
    /**
    {{first.DATA}}
    预约项目：{{keyword1.DATA}}
    预约时间：{{keyword2.DATA}}
    取消原因：{{keyword3.DATA}}
    {{remark.DATA}}
     */
    public function mirSubscribeRefuse($data)
    {
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
        (new WechatBakModule())->sendTplNotify($member['wid'],$data,self::MIC_SUBSCRIBE_REFUSE);
    }

    public function signSuccess()
    {
        $mid = $this->data['mid'];
        $member = (new MemberService())->getRowById($mid);
        if (!$member) {
            \Log::info('用户信息获取失败');
            return ;
        }
        $data['touser'] = $member['xcx_openid'];
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($mid);
        if (!$data['form_id']) {
            return false;
        }
        $re = SignRecordService::getUserSign($this->data);
        $data['page'] = 'pages/activity/pages/activity/sign/sign';
        $data['data']['keyword1'] = [
            'value' =>  $member['truename'],
        ];
        $data['data']['keyword2'] = [
            'value' => date('Y-m-d H:i:s'),
        ];
        $data['data']['keyword3'] = [
            'value' => '您已成功签到'.($re['errCode'] == 0 ? $re['data']['signDay'] : 0 ).'次',
        ];
        $data['data']['keyword4'] = [
            'value' => "签到可获得更多奖励哦。点击查看更多。",
        ];
        $sendTplService  = new WXXCXSendTplService($this->data['wid']);
        $re = $sendTplService->sendTplNotify($data,$sendTplService::SIGN_SUCCESS_NOTIFY,$data['page']);
        \Log::info($re);
    }



}
