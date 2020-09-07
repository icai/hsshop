<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/10/11
 * Time: 11:16
 */

namespace App\S\MarketTools;

use App\S\S;

class MessagesPushService extends S
{

    #todo --------定义消息所属分类（1：交易物流类 2：营销推广类，3：提醒推送类,）--------
    const TradeLogistic     = 1;
    const MarketingCare     = 2;
    const Notification      = 3;




    #todo --------定义消息类型（与$messageType中的 message_type 一致）--------
    const CustomMessage      = 1;  //客服消息
    const EnrollOnline       = 2;  //在线报名

    const TradeUrge          = 3;  //订单催付
    const PaySuccess         = 4;  //付款成功
    const DeliverySuccess    = 5;  //发货成功
    const NewOrder           = 6;  //新订单
    const OrderRefund        = 7;  //订单退款

    const ActivityGroup      = 8;  //拼团
    const GetMemberCard      = 9;  //会员卡
    const ActivityBook       = 10; //预约
    const ProductAdvanceSale = 11; //商品预售
    const ServerExpire       = 12; //活动过期
    const SignSuccess        = 13; //签到成功
    const CommissionGrant    = 14; //佣金发放
    const BecomeChild        = 15; //成为下级
    const BecomePromoter     = 16; //成为推广员
    const PointConsume       = 17; //积分消费

    /**
     *  留言回复
     */
    const MSG_REPLY = 18;





    //title：            消息类型名称
    //type：             消息所属类型 1：商品订单类 2：营销推广类，3：提醒推送类,
    //message_type       消息类型/提醒
    //message_setting：  每种消息提醒拥有的消息发送方式 1:短信 2：微信粉丝 3：公众号模板消息 4：小程序模板消息
    //is_send_seller：   是否是发送给商家
    static public $messageType = [

        ['title' => '订单催付消息提醒','link' => 'tradeUrge','type' => self::TradeLogistic,'message_type' => self::TradeUrge,'is_send_seller' => 0,'message_setting' => [3,4]],
        ['title' => '付款成功消息提醒','link' => 'paySuccess','type' => self::TradeLogistic,'message_type' => self::PaySuccess,'is_send_seller' => 0,'message_setting' => [3,4]],
        ['title' => '发货成功消息提醒','link' => 'deliverySuccess','type' => self::TradeLogistic,'message_type' => self::DeliverySuccess,'is_send_seller' => 0,'message_setting' => [3,4]],
        ['title' => '订单退款消息提醒','link' => 'orderRefund ','type' => self::TradeLogistic,'message_type' => self::OrderRefund,'is_send_seller' => 0,'message_setting' => [3,4]],

        ['title' => '拼团消息提醒','link' => 'group','type' => self::MarketingCare,'message_type' => self::ActivityGroup,'is_send_seller' => 0,'message_setting' => [2]],
        ['title' => '获得会员卡消息提醒','link' => 'getMemberCard','type' => self::MarketingCare,'message_type' => self::GetMemberCard,'is_send_seller' => 0,'message_setting' => [3,4]],
//        ['title' => '预约消息提醒','link' => 'activityBook','type' => self::MarketingCare,'message_type' => self::ActivityBook,'is_send_seller' => 0,'message_setting' => [3]],


        ['title' => '新订单消息提醒','link' => 'newOrder','type' => self::Notification,'message_type' => self::NewOrder,'is_send_seller' => 1,'message_setting' => [3]],
        ['title' => '客服新消息提醒','link' => 'custom','type' => self::Notification,'message_type' => self::CustomMessage,'is_send_seller' => 1,'message_setting' => [3]],
        ['title' => '在线报名消息提醒','link' => 'enroll','type' => self::Notification,'message_type' => self::EnrollOnline,'is_send_seller' => 1,'message_setting' => [3]],
        ['title' => '客服新消息提醒', 'link' => 'customReply', 'type' => self::Notification, 'message_type' => self::MSG_REPLY, 'is_send_seller' => 0, 'message_setting' => [2, 4]],
    ];


    /**
     * 配置处理
     * @param $dbData
     * @return array
     * @author: 梅杰 2018年10月11日
     */
    public function getSetting($dbData)
    {
        $return = self::$messageType;
        foreach ($return as &$v) {
            $temp = [];

            array_filter($dbData,function ($db) use ($v,&$temp){
                $db['message_type'] == $v['message_type'] && $temp = explode(',',$db['send_way']);
            });
            $v['config'] = $temp;

        }

        $tradeLogistic = array_filter($return,function ($item) {
            return $item['type'] == 1;
        });

        $marketing = array_filter($return,function ($item) {
            return $item['type'] == 2;
        });

        $notification = array_filter($return,function ($item) {
           return $item['type'] == 3;
        });

        $re = [
            'tradeLogistic'     => array_column($tradeLogistic,null),
            'notification'      => array_column($notification,null),
            'marketingCare'     => array_column($marketing,null),
        ];

        return $re;

    }


    /**
     * 获取所有的消息提醒内容
     * @return array
     * @author: 梅杰
     */
    static public function getAllMessageType()
    {
        return array_column(self::$messageType,'message_type');
    }

    /**
     * @param $wid
     * @param $messageType
     * @return mixed
     * @author: 梅杰 2018年10月11日
     */
    public function handDbData($wid,$messageType)
    {
        $re = array_filter(self::$messageType,function ($item) use($messageType) {
            return $item['message_type'] == $messageType;
        });
        $re = array_column($re,null);
        $data = $re[0];
        $data['config'] = [];
        if ($db = $this->getRowByType($wid ,$messageType)) {
            $data['config'] = explode(',',$db->send_way);
        }
        return $data;
    }



    public function __construct($modelName = 'MessagesPush')
    {
        parent::__construct($modelName);
    }



    public function getRowByWhere($where = [])
    {
        return $this->model->where($where)->get()->toArray();
    }


    /**
     * 存在则更新 不存在则插入
     * @param $data
     * @return bool
     * @author: 梅杰 2018年10月12日
     */
    public function save($data)
    {
        //1、验证发送方式是否合法
        if ($data['send_way'] && !$this->validate($data['message_type'],explode(',',$data['send_way']))) {
            return false;
        }

        if ($model = $this->model->where(['wid'=>$data['wid'],'message_type'=>$data['message_type']])->first()) {

            return $model->where(['wid'=>$data['wid'],'message_type'=>$data['message_type']])->update(['send_way'=>$data['send_way']]);
        }

        return $this->model->insertGetId($data);
    }

    public function getRowByType($wid,$messageType)
    {
        return $this->model->where(['wid' => $wid,'message_type' => $messageType])->first();
    }

    /**
     * 判断发送方式是否合法
     * @param $messageType 消息类型
     * @param array $self 发送方式集合
     * @return bool
     * @author: 梅杰
     */
    public function validate($type,$self = [])
    {
        $re = array_filter(self::$messageType,function ($item) use($type) {

            return $item['message_type'] == $type;

        });


        $re = array_column($re,null);


        if ($re && $setting = $re[0]) {

            return  !$self || $self == array_column(array_intersect($setting['message_setting'],$self),null);
        }

        return false;
    }


    /**
     * 获取指定消息类型获取用户设置的发送方式权限集
     * @param $wid 店铺id
     * @param $messageType 消息类型
     * @return array
     * @author: 梅杰 2018年10月12日
     */
    public function getSendWayByMessageType($wid,$messageType)
    {
       if ($setting = $this->getRowByType($wid,$messageType)) {

           return explode(',',$setting['send_way']);

       }

       return [];
    }

}