<?php

namespace App\Module;

use App\Jobs\SendTplMsg;
use App\Jobs\SendWeChatTemplatesMsg;
use App\Jobs\SendWeChatXcxTemplatesMsg;
use App\S\MarketTools\MessagesPushService;
use App\Services\Wechat\CustomService;

class MessagePushModule
{

    /**
     * 发送方式
     */
    CONST SMS_SEND_WAY          = 1; //短信模板通知
    CONST WECHAT_FANS_SEND_WAY  = 2; //微信粉丝通知
    CONST WECHAT_TPL_SEND_WAY   = 3; //公众号模板通知
    CONST XCX_TPL_SEND_WAY      = 4; //小程序模板通知

    static $send_way = [
        self::SMS_SEND_WAY,
        self::WECHAT_FANS_SEND_WAY,
        self::WECHAT_TPL_SEND_WAY,
        self::XCX_TPL_SEND_WAY
    ];


    /**
     * @var
     */
    CONST SEND_TARGET_WECHAT     = 1;
    CONST SEND_TARGET_WECHAT_XCX = 2;


    protected $wid;  //店铺id
    protected $type; //通知类型 1.客服 2.在线报名 4.订单付款成功 5.发货成功 6.新订单 8.拼团 9.会员卡 10.预约
    protected $data; //通知数据

    protected $sendTarget; //发送目标 0：公众号 1：小程序


    private $delay;
    /**
     * MessagePushModule constructor.
     * @param $wid
     * @param $type
     * @param $delay
     * @param $sendTarget
     */
    public function __construct($wid, $type ,$sendTarget = self::SEND_TARGET_WECHAT)
    {
        $this->wid    = $wid;
        $this->type   = $type;
        $this->sendTarget = $sendTarget;
    }

    /**
     * 获取商家发送方式集合
     * @return array
     * @author 何书哲 2018年10月12日
     */
    public function getSendWay() {

        return (new MessagesPushService())->getSendWayByMessageType($this->wid, $this->type);

    }

    /**
     * 发送通知
     * @author 何书哲 2018年10月12日
     */
    public function sendMsg($data,$xcxConfigId = 0) {
        //todo 佣金发放、成为下级、成为推广员特殊处理
        if ($this->type == MessagesPushService::CommissionGrant
            || $this->type == MessagesPushService::BecomeChild
            || $this->type == MessagesPushService::BecomePromoter
            || $this->type == MessagesPushService::PointConsume) {
            $this->sendTarget == self::SEND_TARGET_WECHAT && dispatch((new SendWeChatTemplatesMsg($this->wid,$this->type,$data))->delay($this->delay));
            $this->sendTarget == self::SEND_TARGET_WECHAT_XCX && dispatch((new SendWeChatXcxTemplatesMsg($this->wid,$xcxConfigId,$this->type,$data))->delay($this->delay));
        }

        $sendWays = $this->getSendWay();


        //发送短信
        if (in_array(self::SMS_SEND_WAY,$sendWays)) {


        }

        //通过微信客服Api发送消息
        if (in_array(self::WECHAT_FANS_SEND_WAY,$sendWays) && $this->sendTarget == self::SEND_TARGET_WECHAT) {

            dispatch((new SendWeChatTemplatesMsg($this->wid,$this->type,$data)));
        }

        //发送公众号模板消息
        if (in_array(self::WECHAT_TPL_SEND_WAY,$sendWays) && $this->sendTarget == self::SEND_TARGET_WECHAT) {

            dispatch((new SendWeChatTemplatesMsg($this->wid,$this->type,$data))->delay($this->delay));
        }

        //发送小程序服务通知
        if (in_array(self::XCX_TPL_SEND_WAY,$sendWays) && $this->sendTarget == self::SEND_TARGET_WECHAT_XCX) {

            dispatch((new SendWeChatXcxTemplatesMsg($this->wid,$xcxConfigId,$this->type,$data))->delay($this->delay));

        }

        return true;

    }


    public function setDelay($delay = 0)
    {
        $this->delay = $delay;
        return $this;
    }




}