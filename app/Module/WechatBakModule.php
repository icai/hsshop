<?php
// 陈文豪
// 临时代码，后面整个微信需要重构  bak bak bak
// 文档地址
// https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277

namespace App\Module;

use Log;
use App\Services\Wechat\ApiService;
use App\S\Wechat\WeixinNotifyTplService;

class WechatBakModule
{
    CONST PAY_SUCCESS_TPL           = 'TM00398'; //发货成功模板
    CONST NEW_ORDER_TPL             = 'OPENTM406438518';//新订单模板
    CONST DELIVERY_TPL              = 'OPENTM410404351';//发货模板
    CONST REFUND_TPL                = 'OPENTM406075758';//退款成功模板
    CONST NEW_MIC_SUBSCRIBE_TPL     = 'OPENTM400339500';//微预约
    CONST MIC_SUBSCRIBE_SUCCESS_TPL = 'OPENTM204528055';//微预约成功
    CONST MIC_SUBSCRIBE_REFUSE_TPL  = 'OPENTM207847150';//微预约失败
    CONST COMMON_NOTIFY_TPL         = 'OPENTM204650588';//通用通知
    CONST COURSE_NOTIFY_TPL         = 'OPENTM405456204';//课程通知
    CONST PRODUCT_ADVANCE_SALE_TPL  = 'TM411229712';//预约商品开售
    CONST SERVER_EXPIRE_TPL              = 'OPENTM202473452';//服务过期
    CONST GRANT_CARD_TPL                 = 'OPENTM400566501';//会员卡通知
    CONST CUSTOM_MESSAGE_UNREAD_TPL      = 'OPENTM405841082';//客服消息未读通知
    CONST ENROLL_ONLINE_SUCCESS_TPL      = 'OPENTM410885702';//在线报名成功通知
    CONST WAIT_PAY_TPL              = 'OPENTM405885989'; //待支付提醒
    CONST COMMISSION_GRANT_TPL      = 'OPENTM201812627'; //佣金提醒
    CONST BECOME_CHILD_TPL          = 'OPENTM415281152'; //成为下级
    CONST BECOME_RPOMOTER_TPL       = 'OPENTM415281152'; //成为推广员
    CONST POINT_CONSUME_TPL         = 'OPENTM207681011'; //积分消费提醒

    /**
     *  留言回复
     */
    const MESSAGE_REPLY_TPL = 'OPENTM413387398';


    CONST PAY_SUCCESS       = 1;
    CONST DELIVERY_SUCCESS  = 2;
    CONST ORDER_SUCCESS     = 3;
    CONST REFUND_SUCCESS    = 4;
    CONST NEW_MIC_SUBSCRIBE         = 5;//微预约
    CONST MIC_SUBSCRIBE_SUCCESS     = 6;//微预约成功
    CONST MIC_SUBSCRIBE_REFUSE      = 7;//微预约失败
    CONST COMMON_NOTIFY = 8;  //后台通用通知模板
    CONST COURSE_NOTIFY = 9;  //课程预告模板
    CONST PRODUCT_ADVANCE_NOTIFY = 10;//预约商品开售模板
    CONST SERVER_EXPIRE_NOTIFY = 11; //服务过期
    CONST WAIT_PAY_TPL_NOTIFY  = 12; //待支付

    CONST GRANT_CARD = 17;  //会员卡消息通知
    CONST CUSTOM_MESSAGE_UNREAD = 20;  //客服消息未读通知
    CONST ENROLL_ONLINE_SUCCESS = 21;  //在线报名成功通知
    CONST COMMISSION_GRANT_NOTIFY = 22; //佣金发放（下单提醒、佣金到账、佣金发放）
    CONST BECOME_CHILD_NOTIFY = 23; //成为下级
    CONST BECOME_PROMOTER_NOTIFY = 24; //成为推广员
    CONST POINT_CONSUME_NOTIFY = 25; //积分消费提醒

    /**
     *  留言回复
     */
    const MESSAGE_REPLY_NOTIFY = 26;


    public function __construct(){
        $this->apiService      = new ApiService();
        $this->weixinNotifyTpl = new WeixinNotifyTplService();
    }

    /**
     * @param $wid
     * @param $data
     * @param int $type
     * @return array|bool
     * @author: 梅杰 发送公众号模板消息
     * @update 梅杰 增加模板类型
     */
    public function sendTplNotify($wid, $data, $type = self::PAY_SUCCESS)
    {
        switch ($type) {
            case self::PAY_SUCCESS ://支付成功 1
                $templateIdShort = self::PAY_SUCCESS_TPL;
                break;
            case self::DELIVERY_SUCCESS ://发货成功 2
                $templateIdShort = self::DELIVERY_TPL;
                break;
            case  self::ORDER_SUCCESS ://新订单 3
                $templateIdShort = self::NEW_ORDER_TPL;
                break;
            case self::REFUND_SUCCESS : //退款成功
                $templateIdShort = self::REFUND_TPL;
                break;
            case self::NEW_MIC_SUBSCRIBE :
                $templateIdShort = self::NEW_MIC_SUBSCRIBE_TPL;
                break;
            case self::MIC_SUBSCRIBE_SUCCESS :
                $templateIdShort = self::MIC_SUBSCRIBE_SUCCESS_TPL;
                break;
            case self::MIC_SUBSCRIBE_REFUSE :
                $templateIdShort = self::MIC_SUBSCRIBE_REFUSE_TPL;
                break;
            case self::COMMON_NOTIFY :
                $templateIdShort = self::COMMON_NOTIFY_TPL;
                break;
            case self::COURSE_NOTIFY :
                $templateIdShort = self::COURSE_NOTIFY_TPL;
                break;
            case self::PRODUCT_ADVANCE_NOTIFY :
                $templateIdShort = self::PRODUCT_ADVANCE_SALE_TPL;
                break;
            case self::SERVER_EXPIRE_NOTIFY :
                $templateIdShort = self::SERVER_EXPIRE_TPL;
                break;
            case self::GRANT_CARD :
                $templateIdShort = self::GRANT_CARD_TPL;
                break;
            case self::CUSTOM_MESSAGE_UNREAD:
                $templateIdShort = self::CUSTOM_MESSAGE_UNREAD_TPL;
                break;
            case self::ENROLL_ONLINE_SUCCESS:
                $templateIdShort = self::ENROLL_ONLINE_SUCCESS_TPL;
                break;
            case self::WAIT_PAY_TPL_NOTIFY:
                $templateIdShort = self::WAIT_PAY_TPL;
                break;
            case self::COMMISSION_GRANT_NOTIFY:
                $templateIdShort = self::COMMISSION_GRANT_TPL;
                break;
            case self::BECOME_CHILD_NOTIFY:
                $templateIdShort = self::BECOME_CHILD_TPL;
                break;
            case self::BECOME_PROMOTER_NOTIFY:
                $templateIdShort = self::BECOME_RPOMOTER_TPL;
                break;
            case self::POINT_CONSUME_NOTIFY:
                $templateIdShort = self::POINT_CONSUME_TPL;
                break;
            case self::MESSAGE_REPLY_NOTIFY:
                $templateIdShort = self::MESSAGE_REPLY_NOTIFY;
                break;
            default:
                return true;
                break;
        }
        $status = $type;
        if (empty($templateIdShort) || empty($status))
            return false;
        //1、获取微信的模板列表，顺道检测是否开通了模板消息
        $tplListFromWechat = $this->getTemplateIdList($wid);
        $appid = $this->apiService->getAppid($wid);
        if (empty($appid))
            return false;

        $tplIdInDb = 0;
        $tplInfo = $this->weixinNotifyTpl->getRowByAppId($appid, $status);
        if (isset($tplInfo['tpl_id'])) {
            $tplIdInDb = $tplInfo['tpl_id'];
        }
        //2、取数据库列表是否存在,微信列表不存在，修改数据库，发送
        if (!empty($tplIdInDb)) {
            if (!in_array($tplIdInDb, $tplListFromWechat)) {
                $tplIdInDb = $templateId = $this->getTemplateId($wid, $templateIdShort);
                if (!$templateId) {
                    return false;
                }
                $this->weixinNotifyTpl->updateRowById($tplInfo['id'], ['tpl_id' => $templateId]);
            }
        }
        //3、数据列表不存在，插入数据列表
        if ( $tplIdInDb === 0 ) {
            $templateId = $this->getTemplateId( $wid, $templateIdShort);
            if (!$templateId) return false;

            $insert['appid']  = $appid;
            $insert['status'] = $status;
            $tplIdInDb = $insert['tpl_id'] = $templateId;
            $this->weixinNotifyTpl->add($insert);
        }
        $data['template_id'] = $tplIdInDb;
        $return = $this->apiService->sendTplNotify($wid, $data);
        \Log::info(json_encode($return));
        return $return;

    }

    private function getTemplateIdList($wid)
    {
        $result = $this->apiService->getTplListFromWechat($wid,[]);
        if (!isset($result['template_list']))
            return [];

        if(count($result['template_list']) < 1)
            return [];

        $return = [];
        foreach ($result['template_list'] as $value) {
            $return[] = $value['template_id'];
        }
        return $return;
    }

    private function getTemplateId($wid, $templateIdShort)
    {
        $result = $this->apiService->getTplId(
            $wid,
            ["template_id_short" => $templateIdShort]
        );
        if ($result['errcode'] !== 0) 
            return false;
        
        if (!isset($result['template_id']) || empty($result['template_id']))
            return false;

        return $result['template_id'];
    }
}