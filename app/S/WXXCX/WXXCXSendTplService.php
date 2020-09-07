<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/10/9
 * Time: 10:30
 */

namespace App\S\WXXCX;


use App\Lib\WXXCX\ThirdPlatform;
use App\S\Wechat\WeixinNotifyTplService;
use CurlBuilder;

class WXXCXSendTplService
{
    const TRADE_NOTIFY_TPL          = 'AT0004';
    const REFUND_NOTIFY_TPL         = 'AT0036';
    const DELIVER_NOTIFY_TPL        = 'AT0007';
    const GROUP_NOTIFY_TPL          = 'AT0051';
    const WAIT_ORDER_NOTIFY_TPL     = 'AT0525';
    const MARKETING_ORDER_TPL       = 'AT0988'; //预约活动开始
    const REDUCE_PRICE_TPL          = 'AT1345'; //商品降价
    const SIGN_NOTIFY_TPL           = 'AT0264'; //签到提醒
    const ACTIVITY_JOIN_TPL         = 'AT1348';  //活动参与成功
    const ACTIVITY_PROC_TPL         = 'AT0132';        //活动进度
    const PRIZE_GET_TPL             = 'AT1116';        //奖品领取
    const ACTIVITY_EXPIRE_TPL       = 'AT0767';        //活动即将过期
    const SIGN_SUCCESS_NOTIFY_TPL       = 'AT0182';        //签到成功提醒
    const CARD_CERTIFICATIONS_EXPIRE_TPL     = 'AT1199';  //卡券到期
    const PRODUCT_ADVANCE_SALE_TPL = 'AT1004';//商品预售
    const SERVER_EXPIRE_TPL = 'AT0539';//服务过期
    const GRANT_CARD_TPL                  = 'AT2266';        //发放会员卡
    const COMMISSION_DISTRIBUTE_TPL       = 'AT0035';        //佣金发放
    const BECOME_CHILD_TPL                = 'AT0052';        //成为下级
    const BECOME_PROMOTER_TPL             = 'AT0052';        //成为推广者
    const POINT_CONSUME_TPL               = 'AT1213';        //积分消费
    const MESSAGE_TPL = 'AT0891';

    /**
     *  留言回复
     */
    const MESSAGE_REPLY_TPL = 'AT1512';


    const TRADE_NOTIFY      = 1;
    const REFUND_NOTIFY     = 2;
    const DELIVER_NOTIFY    = 3;
    const GROUP_NOTIFY      = 4;
    const WAIT_ORDER_NOTIFY = 5;
    const MARKETING_ORDER_NOTIFY      = 6;
    const REDUCE_PRICE_NOTIFY         = 7;
    const SIGN_NOTIFY_NOTIFY    = 8;
    const ACTIVITY_JOIN_NOTIFY  = 9;
    const ACTIVITY_PROC_NOTIFY  = 10;
    const PRIZE_GET_NOTIFY  = 11;
    const ACTIVITY_EXPIRE_NOTIFY = 12;
    const SIGN_SUCCESS_NOTIFY = 13;
    const CARD_CERTIFICATIONS_EXPIRE_NOTIFY = 14;
    const PRODUCT_ADVANCE_SALE_NOTIFY = 15;
//    const SERVER_EXPIRE_NOTIFY = 16; //暂时未用到
    const GRANT_CARD_NOTIFY = 17;
    const COMMISSION_DISTRIBUTE_NOTIFY = 18;
    const BECOME_CHILD_NOTIFY = 19;
    const BECOME_PROMOTER_NOTIFY = 20;
    const POINT_CONSUME_NOTIFY = 21;
    const MESSAGE_NOTIFY = 22;

    /**
     *  留言回复类型标识
     */
    const MESSAGE_REPLY_NOTIFY = 23;



    private $weixinNotifyTpl;
    private $wxxcxConfigService;
    private $conf;

    /**
     * @param $wid
     * @param $id
     * @return bool
     * @author: 梅杰 20180709  通过小程序配置id获取指定小程序信息
     */
    private function __getConf($wid,$id)
    {
        $xcxConfigData=$this->wxxcxConfigService->getRowByIdWid($wid,$id);
        $result = (new ThirdPlatform())->getAuthorizerAccessToken(['wid' => $wid,'id'=>$id]);
        if ($xcxConfigData['errCode']==0 && !empty($xcxConfigData['data']) && $result['errCode'] == 0) {
            $xcxConfigInfo = $xcxConfigData['data'];
            $conf['app_id'] = $xcxConfigInfo['app_id'];
            $conf['token'] = $result['data'];
            $conf['wid']   = $wid;
            return $conf;
        }
        \Log::info('获取token失败：');
        \Log::info($result);
        return false;
    }

    public function __construct($wid,$id = 0)
    {
        $this->wxxcxConfigService = new WXXCXConfigService();
        $this->weixinNotifyTpl = new WeixinNotifyTplService();
        $this->conf = $this->__getConf($wid,$id);
    }

    /**
     * @param $data
     * @param $type
     * @param bool $page
     * @return mixed
     * @author: 梅杰 2018年08月06日
     * @update：梅杰 2018年08月06日 增加模板类型
     */
    public function sendTplNotify($data, $type ,$page = false)
    {
        $templateIdShort = $status = 0;
        switch ($type) {
            case self::TRADE_NOTIFY ://交易 1
                $templateIdShort = self::TRADE_NOTIFY_TPL;
                break;
            case self::REFUND_NOTIFY ://退款 2
                $templateIdShort = self::REFUND_NOTIFY_TPL;
                break;
            case  self::DELIVER_NOTIFY ://发货 3
                $templateIdShort = self::DELIVER_NOTIFY_TPL;
                break;
            case self::GROUP_NOTIFY: //拼团成功
                $templateIdShort = self::GROUP_NOTIFY_TPL;
                break;
            case self::WAIT_ORDER_NOTIFY: //待支付
                $templateIdShort = self::WAIT_ORDER_NOTIFY_TPL;
                break;
            case self::MARKETING_ORDER_NOTIFY: //预约推广
                $templateIdShort = self::MARKETING_ORDER_TPL;
                break;
            case self::REDUCE_PRICE_NOTIFY: //降价提醒
                $templateIdShort = self::REDUCE_PRICE_TPL;
                break;
            case self::SIGN_NOTIFY_NOTIFY: //签到成功
                $templateIdShort = self::SIGN_NOTIFY_TPL;
                break;
            case self::ACTIVITY_JOIN_NOTIFY: //活动参与成功
                $templateIdShort = self::ACTIVITY_JOIN_TPL;
                break;
            case self::ACTIVITY_PROC_NOTIFY: //进度
                $templateIdShort = self::ACTIVITY_PROC_TPL;
                break;
            case self::PRIZE_GET_NOTIFY: //获取奖品
                $templateIdShort = self::PRIZE_GET_TPL;
                break;
            case self::ACTIVITY_EXPIRE_NOTIFY: //活动即将过期
                $templateIdShort = self::ACTIVITY_EXPIRE_TPL;
                break;
            case self::SIGN_SUCCESS_NOTIFY: //签到成功
                $templateIdShort = self::SIGN_SUCCESS_NOTIFY_TPL;
                break;
            case self::CARD_CERTIFICATIONS_EXPIRE_NOTIFY: //卡券到期
                $templateIdShort = self::CARD_CERTIFICATIONS_EXPIRE_TPL;
                break;
            case self::PRODUCT_ADVANCE_SALE_NOTIFY: //商品预售
                $templateIdShort = self::PRODUCT_ADVANCE_SALE_TPL;
                break;
//            case self::SERVER_EXPIRE_NOTIFY: //商品预售
//                $templateIdShort = self::SERVER_EXPIRE_TPL;
//                break;
            case self:: GRANT_CARD_NOTIFY: //发放会员卡
                $templateIdShort = self::GRANT_CARD_TPL;
                break;
            case self::COMMISSION_DISTRIBUTE_NOTIFY://佣金发放
                $templateIdShort = self::COMMISSION_DISTRIBUTE_TPL;
                break;
            case self::BECOME_CHILD_NOTIFY:
                $templateIdShort = self::BECOME_CHILD_TPL;
                break;
            case self::BECOME_PROMOTER_NOTIFY:
                $templateIdShort = self::BECOME_PROMOTER_TPL;
                break;
            case self::POINT_CONSUME_NOTIFY:
                $templateIdShort = self::POINT_CONSUME_TPL;
                break;
            case self::MESSAGE_NOTIFY:
                $templateIdShort = self::MESSAGE_TPL;
                break;
            case self::MESSAGE_REPLY_NOTIFY:
                $templateIdShort = self::MESSAGE_REPLY_TPL;
                break;
            default:
                break;
        }
        $status = $type;
        if (empty($templateIdShort) || empty($status)){
            return false;
        }
        $conf = $this->conf;
        if (!$conf) {
            \Log::info('token获取失败');
            return ;
        }
        $tplInfo = $this->weixinNotifyTpl->getRowByAppId($conf['app_id'], $status,2);
        //判断模板是否已经替换，替换则更新
        if ($tplInfo && $tplInfo['tpl_id']){
            $tplIdInDb = $tplInfo['tpl_id'];
            //更新
            if($tplInfo['tpl_short_id'] != $templateIdShort){
                $re = $this->saveTpl($templateIdShort, $tplInfo['id'], $status);
                $tplIdInDb = $re['template_id'];
            }
        }else{
            $re = $this->saveTpl($templateIdShort,'', $status);
            $tplIdInDb = $re['template_id'];
        }
        $data['template_id'] = $tplIdInDb;
        //小程序消息全部跳转到首页
        $data['page'] = $page != false ? $page :'pages/index/index';
        if ($conf['wid'] == 626 || $conf['wid'] == 664) {
            $data['page'] = 'pages/apply/apply';
        }
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $conf['token'];
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($data)->post();
        $result = json_decode($result, true);
        \Log::info('小程序模板消息发送结果------');
        \Log::info(json_encode($result));
        return $result;
    }

    /**
     * 获取模板库某个模板标题下关键词库
     */
    public function getTplKeyLibrary($id)
    {
        $conf = $this->conf;
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=' . $conf['token'];
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData(['id'=>$id])->post();
        $result = json_decode($result, true);
        if ($result['errcode'] == 0) {
            return $result['keyword_list'];
        }
        \Log::info('获取小程序模板消息失败');
        \Log::info($result);
        return false;
    }

    /**
     * 组合模板并添加至帐号下的个人模板库
     * ['id' => 'AT0004', 'value' => array(9, 1, 3, 33, 17, 56, 34), 'name' => '交易提醒'],
     * ['id' => 'AT0007', 'value' => array(7, 6, 3, 2, 23, 53), 'name' => '订单发货提醒'],
     * ['id' => 'AT0036', 'value' => array(33, 35, 3, 5, 30), 'name' => '退款通知']
     */
    public function saveTpl($templateIdShort, $id = 0, $tplStatus = 0)
    {
        $conf = $this->conf;
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=' . $conf['token'];
        $postData['id'] = $templateIdShort;
        $status = 0;
        switch ($templateIdShort) {
            case self::TRADE_NOTIFY_TPL:
                $postData['keyword_id_list'] = [9, 1, 3, 33, 17, 56, 34];
                $status = self::TRADE_NOTIFY;
                break;
            case self::DELIVER_NOTIFY_TPL:
                $postData['keyword_id_list'] = [7, 6, 3, 2, 23, 53];
                $status = self::DELIVER_NOTIFY;
                break;
            case self::REFUND_NOTIFY_TPL:
                $postData['keyword_id_list'] = [33, 35, 3, 5, 30];
                $status = self::REFUND_NOTIFY;
                break;
            case self::GROUP_NOTIFY_TPL:
                $postData['keyword_id_list'] = [6, 7, 16, 17];
                $status = self::GROUP_NOTIFY_TPL;
                break;
            case self::WAIT_ORDER_NOTIFY_TPL:
                $postData['keyword_id_list'] = [1,2,3,4,5];
                $status = self::WAIT_ORDER_NOTIFY;
                break;
            case self::MARKETING_ORDER_TPL:
                $postData['keyword_id_list'] = [4,5,6];
                $status = self::MARKETING_ORDER_NOTIFY;
                break;
            case self::REDUCE_PRICE_TPL:
                $postData['keyword_id_list'] = [2,5,4,7];
                $status = self::REDUCE_PRICE_NOTIFY;
                break;
            case self::SIGN_NOTIFY_TPL:
                $postData['keyword_id_list'] = [4,6];
                $status = self::SIGN_NOTIFY_NOTIFY;
                break;
            case self::ACTIVITY_JOIN_TPL:
                $postData['keyword_id_list'] = [1,4,3];
                $status = self::ACTIVITY_JOIN_NOTIFY;
                break;
            case self::ACTIVITY_PROC_TPL:
                $postData['keyword_id_list'] = [6,19,4,12];
                $status = self::ACTIVITY_PROC_NOTIFY;
                break;
            case self::PRIZE_GET_TPL:
                $postData['keyword_id_list'] = [2,1,5];
                $status = self::PRIZE_GET_NOTIFY;
                break;
            case self::ACTIVITY_EXPIRE_TPL:
                $postData['keyword_id_list'] = [1,2,3];
                $status = self::ACTIVITY_EXPIRE_NOTIFY;
                break;
            case self::SIGN_SUCCESS_NOTIFY_TPL:
                $postData['keyword_id_list'] = [1,2,30,6];
                $status = self::SIGN_SUCCESS_NOTIFY;
                break;
            case self::CARD_CERTIFICATIONS_EXPIRE_TPL:
                $postData['keyword_id_list'] = [5,2,3,4];
                $status = self::CARD_CERTIFICATIONS_EXPIRE_NOTIFY;
                break;
            case self::PRODUCT_ADVANCE_SALE_TPL:
                $postData['keyword_id_list'] = [1,2,3,4];
                $status = self::PRODUCT_ADVANCE_SALE_NOTIFY;
                break;
//            case self::SERVER_EXPIRE_TPL:
//                $postData['keyword_id_list'] = [1,2,3,7];
//                $status = self::SERVER_EXPIRE_NOTIFY;
                break;
            case self::GRANT_CARD_TPL:
                $postData['keyword_id_list'] = [1,2,3];
                $status = self::GRANT_CARD_NOTIFY;
                break;
            case self::COMMISSION_DISTRIBUTE_TPL:
                $postData['keyword_id_list'] = [21,3,15];
                $status = self::COMMISSION_DISTRIBUTE_NOTIFY;
                break;
            case self::BECOME_CHILD_TPL:
                $postData['keyword_id_list'] = [29,100,40,89];
                $status = $tplStatus;
                break;
            case self::POINT_CONSUME_TPL:
                $postData['keyword_id_list'] = [27,16,7,22];
                $status = self::POINT_CONSUME_NOTIFY;
                break;
            case self::MESSAGE_TPL:
                $postData['keyword_id_list'] = [2,3,6];
                $status = self::MESSAGE_NOTIFY;
                break;
            case self::MESSAGE_REPLY_TPL:
                $postData['keyword_id_list'] = [1, 2, 4];
                $status = self::MESSAGE_REPLY_NOTIFY;
                break;
            default:
                break;
        }
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        $result = json_decode($result, true);
        if($result['errcode'] == 0){
            //加入数据库
            $data['appid']  = $conf['app_id'];
            $data['status'] = $status;
            $data['type']   = 2;
            $data['tpl_id'] = $result['template_id'];
            $data['tpl_short_id'] = $templateIdShort;

            if($id == 0 && $this->weixinNotifyTpl->add($data)){
                return $result;
            }
            if($id != 0 && $this->weixinNotifyTpl->updateRowById($id,$data)){
                return $result;
            }

        }
        return false;
    }


    public function getTemplateIdList()
    {
        $conf = $this->conf;
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=' . $conf['token'];
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData(['offset' => 0, 'count' => 10])->post();
        $result = json_decode($result, true);
        if ($result['errcode'] == 0) {
            return $result['list'];
        }
        \Log::info('获取小程序模板消息失败');
        \Log::info($result);
        return false;
    }



}