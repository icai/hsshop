<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/1/24
 * Time: 9:19
 */

namespace App\Module;
use App\Http\Controllers\shop\NewsController;
use App\Jobs\SendRegisterSMS;
use App\Jobs\SendTplMsg;
use App\Lib\Redis\RedisClient;
use App\S\MarketTools\MessagesPushService;
use App\S\ShareEvent\ActivityRegisterService;
use App\S\ShareEvent\AdminSellerkpiService;
use App\S\ShareEvent\LiDetailService;
use App\S\ShareEvent\LiEventService;
use App\S\ShareEvent\LiRegisterService;
use App\S\ShareEvent\LiSalesmanService;
use App\S\ShareEvent\LiShareLogService;
use App\S\ShareEvent\MeetingNexusService;
use App\Services\UserService;
use App\Services\Wechat\CustomService;
use OrderCommon;
use DB;
use OrderService;
use OrderDetailService;
use OrderLogService;
use MemberService;
use App\Services\Shop\MemberAddressService;
use App\Lib\BLogger;
use WeixinService;
use ProductService;
use App\S\Product\ProductSkuService;
use App\S\Product\ProductPropsToValuesService;

use App\S\ShareEvent\LiEventRedPacketService as LiShareEventRedPacketService;
use App\S\ShareEvent\LiRewardService as LiShareRewardService;
use App\S\ShareEvent\LiEventService as LiShareEventService;
use App\S\ShareEvent\LiEventRecordService as LiShareEventRecordService;
use App\S\ShareEvent\LiFriendService;
use App\S\Weixin\ShopService;


class LiShareEventModule
{
    /***
     * todo 获取商品信息供享立减使用
     * @param int $productId
     * @param int $skuId
     * @param int $num
     * @return array
     * @author jonzhang
     * @date 2017-12-07
     */
    private function getProductMessage($wid, $activityId, $mid, $skuId = 0, $num = 1)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        //商品享立减金额
        $amount = 0.00;
        $shareEventRecordService = new LiShareEventRecordService();

        $where = [
            'share_event_id'    => $activityId,
            'source_id'         => $mid,
            'current_status'    => 0 ,
            'red_packet_id'     => 0
        ];
        $count = $shareEventRecordService->count($where);
        //如果有红包累加上红包金额 add by jonzhang 2018-01-15
        $redPacket=0.00;
        unset($where['red_packet_id']);
        $shareEventRedPacketData=$shareEventRecordService->statRedPacket($where);
        //BLogger::getLogger('info')->info('RedPacketData:'.json_encode($shareEventRedPacketData));
        if($shareEventRedPacketData['errCode']==0)
        {
            $redPacket=$shareEventRedPacketData['data'];
        }

        $shareEventService = new LiShareEventService();
        $where = ['wid' => $wid, 'id' => $activityId, 'type' => 0, 'status' => 0];
        $unitAmount = 0.00;
        $lowerPrice = 0.00;
        $shareEventResult = $shareEventService->list($where);
        if (!empty($shareEventResult[0]['data']))
        {
            $shareEventData = $shareEventResult[0]['data'][0];

            // add by jonzhang 享立减活动过期判断 2018-01-29
            if($shareEventData['start_time']>time())
            {
                $returnData['errCode'] = -108;
                $returnData['errMsg'] = '该享立减活动还没开始';
                return $returnData;
            }
            if($shareEventData['end_time']<time())
            {
                $returnData['errCode'] = -107;
                $returnData['errMsg'] = '该享立减活动已过期';
                return $returnData;
            }

            //此处分转化为元
            $unitAmount = sprintf('%.2f', $shareEventData['unit_amount'] / 100);
            $lowerPrice = sprintf('%.2f', $shareEventData['lower_price'] / 100);
            $productId = $shareEventData['product_id'];
        } else {
            $returnData['errCode'] = -103;
            $returnData['errMsg'] = '享立减活动不存在';
            return $returnData;
        }

        if (empty($productId)) {
            $returnData['errCode'] = -104;
            $returnData['errMsg'] = '商品不存在';
            return $returnData;
        }
        //红包金额和分享金额
        $amount = $unitAmount * $count+$redPacket;
        $productModule = new ProductModule();
        $productReturnData = $productModule->getProductByShareEvent($productId, $skuId,false);
        if ($productReturnData['errCode'] == 0 && !empty($productReturnData['data'])) {
            $product = $productReturnData['data'];
            if ($product['is_distribution']) {
                $returnData['errCode'] = -106;
                $returnData['errMsg'] = '分销商品不能够参加享立减活动';
                return $returnData;
            }
            $productData['img'] = $product['img'] ?? '';
            $productData['price'] = $product['price'] ?? 0.00;
            if($lowerPrice>$productData['price'])
            {
                $returnData['errCode'] = -105;
                $returnData['errMsg'] = '享立减保底价不能够高于商品价格';
                return $returnData;
            }

            if ($lowerPrice+$amount>$productData['price'])
            {
                //享立减金额
                $amount =$productData['price']-$lowerPrice;
            }
            $amount=sprintf('%.2f', $amount);
            $productData['bprice'] = $productData['price'];
            $productData['price'] = $productData['price'] - $amount;
            $productData['oprice'] = $product['oprice'] ?? 0.00;
            $productData['product_spec'] = $product['product_spec'] ?? '';
            $productData['product_id'] = $product['id'] ?? 0;
            $productData['product_name'] = $product['title'] ?? '';
            $productData['sku_flag'] = $product['sku_flag'] ?? 0;
            $productData['lower_price'] = $lowerPrice;
            if ($product['stock'] < $num) {
                $returnData['errCode'] = -102;
                $returnData['errMsg'] = "该商品库存不足";
                return $returnData;
            }
            $productData['num'] = $num;
            $productData['less_amount'] = $amount;
            $productData['unit_amount'] = $unitAmount;
            $returnData['data'] = $productData;
            return $returnData;
        } else {
            if ($productReturnData['errCode'] == 0) {
                $returnData['errCode'] = -101;
                $returnData['errMsg'] = "该商品不存在";
                return $returnData;
            } else {
                return $productReturnData;
            }
        }
    }

    /**
     * todo 待提交享立减订单信息
     * @author jonzhang
     * @date 2017-12-07
     */
    public function processWaitSubmitShareEventOrder($wid = 0, $mid = 0, $condition = [])
    {
        //定义返回数据数组
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($condition['activityId'])) {
            $returnData['errCode'] = -103;
            $returnData['errMsg'] = "享立减活动id为空";
            return $returnData;
        }
        $activityId = $condition['activityId'];
        $skuId = $condition['skuId'] ?? 0;
        $num = $condition['num'] ?? 1;

        //商品信息
        $productReturnData = $this->getProductMessage($wid, $activityId, $mid, $skuId, $num);
        if ($productReturnData['errCode'] == 0 && !empty($productReturnData['data'])) {
            $returnData['data']['product_data'] = $productReturnData['data'];
        } else {
            return $productReturnData;
        }

        $userAddress = (new MemberAddressService())->getUserAddress($mid);
        $returnData['data']['address'] = $userAddress;
        //运费信息
        $freight = 0.00;
//        if(!empty($userAddress['default']))
//        {
//            //此处为商品的运费
//            $member = MemberService::getRowById($mid);
//            $umid = $member['umid']??0;
//            $orderModule = new OrderModule();
//            $addressId=$userAddress['default'][0]['id'];
//            $freight = $orderModule->getFreightByCartIDArr([], $wid, $mid, $umid, $addressId, [['product_id' => $productReturnData['data']['product_id'], 'prop_id' => $skuId, 'num' => 1]]);
//            $freight = sprintf('%.2f', $freight);
//        }
        $returnData['data']['freight'] = $freight;

        return $returnData;
    }

    /**
     * @upate 张永辉 2018年7月9日 小程序配置信息id写入订单
     * @update 梅杰 2018年10月18日 待支付消息推送
     */
    public function submitShareEventOrder($wid=0,$mid=0,$data=[],$source=0,$orderType=1,$remarkNo='')
    {
        //定义返回数据数组
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $errMsg = "";
        if (empty($wid)) {
            $errMsg .= "店铺id不能为空";
        }
        if (empty($mid)) {
            $errMsg .= "用户id不能为空";
        }
        if (empty($data['activityId'])) {
            $errMsg .= "享立减活动id不能为空";
        }
//        if (empty($data['addressId'])) {
//            $errMsg .= "收货地址id不能为空";
//        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -11;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        $activityId = $data['activityId'];
        $skuId = $data['skuId'] ?? 0;
        $num = $data['num'];
        $addressId = $data['addressId'];
        $formId = $data['formId'] ?? 0;

        //商品信息
        $productData = [];
        //商品信息
        $productReturnData = $this->getProductMessage($wid, $activityId, $mid, $skuId, $num);
        if ($productReturnData['errCode'] == 0 && !empty($productReturnData['data'])) {
            $productData = $productReturnData['data'];
        } else {
            return $productReturnData;
        }

        if (empty($productData)) {
            $returnData['errCode'] = -12;
            $returnData['errMsg'] = '商品信息为空';
            return $returnData;
        }

        //生成订单号
        $orderNo = OrderCommon::createOrderNumber();
        //订单信息
        $createTime = date("Y-m-d H:i:s");
        $order = [];
        $order['oid'] = $orderNo;
        $order['trade_id'] = $orderNo;
        $order['wid'] = $wid;
        $order['mid'] = $mid;
//        $productTotalAmount = sprintf('%.2f', $productData['price'] * $productData['num']);
        //商品总价

        // 张永辉 2018年7月9日 写入小程序配置id
        $token = app('request')->input('token','0');
        $order['xcx_config_id'] = (new CommonModule())->getXcxConfigIdByToken($token);
        //end

        //add may jay 获取活动信息
        $activity = (new LiEventService())->getOne($data['activityId'],$wid);
        $pay_price = sprintf('%.2f',  $activity['lower_price'] * $data['num'] / 100 );
        $order['products_price'] = 0.00;
        $order['pay_price'] = $pay_price;
        $order['change_price'] = 0.00;
        $order['source'] = $source;
        $order['form_id'] = 0;
        if ($source == 1) {
            $order['form_id'] = $formId;
        }
        $freight = 0.00;
        //此处为商品的运费
//        $member = MemberService::getRowById($mid);
//        $umid=$member['umid']??0;
//        $orderModule=new OrderModule();
//        $freight=$orderModule->getFreightByCartIDArr([], $wid, $mid,$umid, $addressId, [['product_id' =>$productData['product_id'],'prop_id'=>$skuId,'num'=>1]]);
//        $freight=sprintf('%.2f',$freight);
        //最后实际支付金额要加上运费金额
        $order['pay_price'] = $order['pay_price'] + $freight;
        $order['pay_price'] = sprintf('%.2f', $order['pay_price']);
        $order['cash_fee'] = 0.00;
        $order['bonus_point_amount'] = 0.00;

        //运费金额
        $order['freight_price'] = 0;
        //物流方式 1快递发货
        $order['express_type'] = 1;

        if (!empty($addressId)){
            $userAddress = (new MemberAddressService())->getAddressById($addressId);
            $order['address_id'] = $userAddress['id'];
            $order['address_name'] = $userAddress['title'];
            $order['address_phone'] = $userAddress['phone'];
            //详细地址
            $order['address_detail'] = $userAddress['detail'];

            $order['address_province'] = $userAddress['province']['title'];
            $order['address_city'] = $userAddress['city']['title'];
            $order['address_area'] = $userAddress['area']['title'];
        }else{
            $order['address_id'] = 0;
            $order['address_name'] = '';
            $order['address_phone'] = '';
            //详细地址
            $order['address_detail'] = '';

            $order['address_province'] = '';
            $order['address_city'] = '';
            $order['address_area'] = '';
        }




        //加入享立减活动id
        $order['zan_id'] = $activityId;

        //普通订单
        $order['type'] = $orderType;
        //支付方式 0未支付
        $order['pay_way'] = 0;
        //买家备注
        $order['buy_remark'] = '';
        //商家备注
        $order['seller_remark'] = '';
        //由于使用到redis缓存 下面字段必须传递值
        //订单状态 0待付款
        $order['status'] = 0;
        //维权状态
        $order['refund_status'] = 0;
        //星级
        $order['star_level'] = 0;
        //创建时间
        $order['created_at'] = $createTime;
        //折扣金额
        $order['discount_amount'] = 0.00;
        //减免运费金额
        $order['derate_freight'] = 0.00;
        //使用的积分
        $order['use_point'] = 0;
        //优惠券id
        $order['coupon_id'] = 0;
        $order['coupon_price'] = 0.00;
        $order['card_discount'] = 0;
        //是否分销订单
        $order['distribute_type'] = 0;
        //团购信息
        $order['groups_id'] = 0;
        $order['groups_status'] = 0;
        $order['head_discount'] = 0;
        //是否为0元订单
        $isFree = 0;
        //0元订单[订单状态和支付状态都进行更改]
        if ($order['pay_price'] == 0) {
            $isFree = 1;//0元订单
            $order['pay_way'] = 10;//小程序支付
            $order['status'] = 1;//已付款
        }
        //定制版享立减特有
        $order['is_customize']=1;
        //订单明细
        $orderDetail = [];
        $orderDetail['oid'] = $orderNo;
        $orderDetail['product_id']=$productData['product_id'];
        $orderDetail['title']=$productData['product_name'];
        $orderDetail['img']=$productData['img'];
        $orderDetail['price']=$productData['price'];
        $orderDetail['oprice']=$productData['oprice'];
        $orderDetail['num']=$data['num'];
        $orderDetail['spec']=$productData['product_spec'];
        $orderDetail['remark_no'] = $remarkNo; //4 by zhangyh 20180119
        //享立减减少单价
        $orderDetail['less_price'] = $productData['less_amount'];
        //缓存redis使用
        $orderDetail['created_at'] = $createTime;
        $orderDetail['updated_at'] = $createTime;
        $orderDetail['is_evaluate'] = 0;
        $orderDetail['product_prop_id'] = $skuId;
        $orderDetail['is_delivery'] = 0;
        $orderDetail['after_discount_price'] = 0.00;

        //事务
        DB::beginTransaction();
        try {
            //添加订单信息
            $orderReturn = OrderService::init()->addD($order, false);
            if (!$orderReturn) {
                throw new \Exception('创建订单失败');
            }
            //添加订单详情
            $orderDetail['oid'] = $orderReturn;
            $orderDetailReturn = OrderDetailService::init()->addD($orderDetail, false);
            if (!$orderDetailReturn) {
                throw new \Exception('添加订单明细失败');
            }

            $shareEventRecordReturn = (new LiShareEventRecordService())->update(['share_event_id' => $data['activityId'], 'source_id' => $mid, 'current_status' => 0]);
            if ($shareEventRecordReturn['err_code'] == 1) {
                throw new \Exception('享立减更改失败');
            }

            //add by jonzhang 2018-01-17 添加库存
            //更改商品库存
            $stockNum=$data['num'];
            if($skuId)
            {
                //更改有规格商品 数据库库存
                $propList = (new ProductPropsToValuesService())->getSkuList($productData['product_id']);
                if(!empty($propList['stocks']))
                {
                    $productSkuService=new ProductSkuService();
                    //更改商品表中的总库存和规格信息
                    foreach($propList['stocks'] as $propItem)
                    {
                        if($propItem['id']==$skuId)
                        {
                            $propStock = $propItem['stock_num']-$stockNum;
                            if($propStock<0)
                            {
                                throw new \Exception('库存不足');
                            }
                            $productSkuService->update($propItem['id'], ['stock' => $propStock, 'sold_num' => $propItem['sold_num'] + $stockNum]);
                            break;
                        }
                    }
                }
            }
            //此处查询出来的信息为商品的总库存，如何商品无规格，则减去为无规格商品的库存，如果商品有规格，则从总库中减去购买数量。
            $productInfo = ProductService::getDetail($productData['product_id']);
            if(!empty($productInfo))
            {
                //库存
                $productStock=$productInfo['stock']-$stockNum;
                $stockData=['stock' =>$productStock, 'sold_num' => $productInfo['sold_num'] + $stockNum];
                if($productStock<0)
                {
                    throw new \Exception('库存不足');
                }
                //库存为0，商品更改为下架
                if($productStock==0)
                {
                    $stockData['status']=0;
                }
                ProductService::update($productData['product_id'],$stockData);
            }
            //add by jonzhang 2018-02-07
            $this->successfulGet($mid,$activityId);
            DB::commit();
        } catch (\Exception $e) {
            //事务回滚
            DB::rollback();
            $message = $e->getMessage();
            $returnData['errCode'] = -1000;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        //梅杰 2018年10月18日 订单待付款
        (new MessagePushModule($wid,MessagesPushService::TradeUrge,MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($orderReturn,$order['xcx_config_id']);

        //订单流水
        $orderLog = [];
        $orderLog['oid'] = $orderReturn;
        $orderLog['wid'] = $wid;
        $orderLog['mid'] = $mid;
        $orderLog['action'] = 1;
        $orderLog['created_at'] = $createTime;
        $orderLog['updated_at'] = $createTime;
        $orderLog['remark'] = '';
        //添加订单流水
        OrderLogService::init()->addD($orderLog, false);
        $order['id'] = $orderReturn;
        $order['orderDetail'] = [$orderDetail];
        //redis订单日志字段 存二维数组
        $order['orderLog'] = [$orderLog];
        //订单信息插入到缓存
        OrderService::init('mid', $mid)->addR($order, false);
        OrderService::init('wid', $wid)->addR($order, false);
        $returnData['data'] = ['orderNo' => $orderReturn, 'isFree' => $isFree];
        return $returnData;
    }

    /***
     * todo 判断参与者是否可参加活动
     * @param array $where
     * @return array
     * @author jonzhang
     * @date 2017-12-14
     */
    public function isShareEvent($where = [])
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => 1,'redPacket' => []];
        if (empty($where)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = "查询条件为空";
            return $returnData;
        }
        //BLogger::getLogger('info')->info('参与者参数:'.json_encode($where));
        $strMsg = "";
        if (!isset($where['actorId'])) {
            $strMsg .= "参与者不存在";
        }
        if (!isset($where['key'])) {
            $strMsg .= "key不存在";
        }
        if (!isset($where['shareEventId'])) {
            $strMsg .= "享立减活动id不存在";
        }
        if (!isset($where['shareId'])) {
            $strMsg .= "分享者不存在";
        }
        if (strlen($strMsg) > 0) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = $strMsg;
            return $returnData;
        }
        $shareEventId = $where['shareEventId'];
        $shareId = $where['shareId'];
        $key = $where['key'];
        //'current_status'=>0表示享立减提交订单后 该用户就变成新用户
        $shareEventRecordData = (new LiShareEventRecordService())->getList(['actor_id' => $where['actorId']]);
        //参与者没有参与过任何享立减活动
        if (empty($shareEventRecordData)) {
            $returnData['data'] = 1;
            return $returnData;
        }//参与者参加过活动
        else {
            foreach ($shareEventRecordData as $item) {
                //没有翻新过
                if ($item['key'] == $key) {
                    //$returnData['data']=0;
                    $returnData['errCode'] = -3;
                    $returnData['errMsg'] = "享立减活动参与者参加过";
                    break;
                } else {
                    //同一个活动
                    if ($item['share_event_id'] == $shareEventId) {
                        //用户没有提交过订单
                        if ($item['current_status'] == 0) {
                            //同一个分享者
                            if ($item['source_id'] == $shareId) {
                                //$returnData['data']=0;
                                $returnData['errCode'] = -4;
                                $returnData['errMsg'] = "享立减活动翻新者不可参加";
                                break;
                            }
                        }
                    }
                }
            }
            return $returnData;
        }


    }


    /**
     * 每天一个红包
     * Author: MeiJay
     * @param $mid
     * @return array
     */
    public function getRedPacket($mid,$wid)
    {
        $returnData = ['code' => 0, 'msg' => 'success', 'data' => []];
        $redPacketService = new LiShareEventRedPacketService();
        $beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $where = [
            'mid'        => $mid,
            'created_at' => [
                '>=', date('Y-m-d H:i:s',$beginToday)
            ]
        ];
        $data = $redPacketService->getList($where);
        if ($data) {

            $data[0]['flag'] = 1;

            $redPacketService->update($data[0]['id'],$data[0]);

            $data = !$data[0]['status'] && $data[0] ? $data[0] : [];

            if(!$data) {
                $data['flag'] = 1;
                $returnData['code'] = -1;
                $returnData['msg'] = '今日无可用红包';
            }

        } else {
            $money = (new LiShareRewardService())->getReduceSum($wid);
            if ($money) {
                $data = [
                    'mid' => $mid,
                    'money' => $money,
                    'flag'  => 0,
                ];
                $data['id']  = $redPacketService->create($data);
            } else {
                $returnData['msg'] = '未设置享立减红包';
                $returnData['code'] = -2 ;
            }
        }
        //判断该用户是新用户还是老用户
//        $shop = WeixinService::getStore($wid);
//        $key = $shop['data']['share_event_key'];
//        $data['is_new'] = 0;
//        $is_new = (new ShareEventRecordService())->count(['actor_id'=>$mid,'key'=>$key]);
//        if(!$is_new) {
//            $data['is_new'] = 1;
//        }
        $returnData['data'] = $data;
        return $returnData;
    }


    /**
     * Author: MeiJay
     * @param $mid
     * @param $packetId
     * @param $activityId
     * @return array
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function useRedPacket($mid,$wid,$packetId,$activityId)
    {
        //使用红包  修改红包状态 ，插入红包助减信息
        $returnData = ['code' => 0, 'msg' => 'success', 'data' => []];
        $redPacketService = new LiShareEventRedPacketService();
        $beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $where = [
            'id' => $packetId,
            'status' => 0,
            'mid'    => $mid ,
            'created_at' => [
                '>=', date('Y-m-d H:i:s',$beginToday)
            ]
        ];
        $redInfo = $redPacketService->getList($where);
        if (!$redInfo) {
            $returnData['code']     = -1;
            $returnData['msg']      = '无可用红包';
            return $returnData;
        }
        $redInfo = $redInfo[0];
        $redInfo['status'] = 1;
        $redInfo['share_event_id'] = $activityId;
        if ($redPacketService->update($redInfo['id'],$redInfo) == false) {
            $returnData['code']     = -2;
            $returnData['msg']      = '使用失败';
            return $returnData;
        }
        //$shop = WeixinService::getStore($wid);
        $shopService = new ShopService();
        $shop = $shopService->getRowById($wid);
        $input = [
            'key'               => $shop['share_event_key'],
            'wid'               => $wid,
            'share_event_id'    => $activityId,
            'avatar_url'        => '',
            'nick_name'         => '',
            'red_packet_id'     =>$packetId ,
            'source_id'         => $mid,
            'red_packet_money'  => $redInfo['money']
        ];
        $returnData = (new LiShareEventRecordService())->createShareEventRecord($input);
        return $returnData;
    }


    /**
     * 获取活动进度
     * Author: MeiJay
     * @param $sourceId
     * @param $activityId
     * @param $mid
     * @return array
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function activityProcess($mid,$sourceId,$activityId)
    {

        $returnData = ['errCode'=> 0 ,'msg'=> 'success', 'data'=> [] ] ;

        //列表页进入
        if ($sourceId == 0 ) {
            $returnData['msg'] = '列表页进入';
            return $returnData;
        }
        $data = [
            'head_img_url' => '',
        ];
        //获取用户信息（分享者头像）
        $member = MemberService::getRowById($sourceId);
        $data['head_img_url'] = $member['headimgurl'] ?? '';
        $data['truename'] = $member['truename'] ?? '';
        $activityData = (new LiShareEventService())->getOne($activityId,$member['wid']);
        if (!$activityData) {
            $returnData['errCode'] = -1;
            $returnData['msg'] = '活动不存在';
            return $returnData;
        }

        $data['lower_price'] = sprintf('%.2f',$activityData['lower_price']/100);;
        //判断该用户是新用户还是老用户
        //$shop = WeixinService::getStore($member['wid']);
        $shopService = new ShopService();
        $shop = $shopService->getRowById($member['wid']);
        $key = $shop['share_event_key'];
        $data['is_new'] = 0;
        $is_new = (new LiShareEventRecordService())->count(['actor_id'=>$mid,'key'=>$key]);
        if(!$is_new) {
            $data['is_new'] = 1;
        }
        $returnData['data'] = $data;
        return $returnData;
    }

    /**
     * todo 分享者活动是否有人参与
     * @param array $where
     * @return array
     * @author jonzhang
     * @date 2018-01-13
     */
    public function isShareByMid($where=[])
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' =>0];
        if(empty($where))
        {
            $returnData['errCode']=-100;
            $returnData['errMsg']='查询条件为空';
            return $returnData;
        }
        $strMsg="";
        if(empty($where['source_id']))
        {
            $strMsg.="分享者id为空 ";
        }
        if(empty($where['share_event_id']))
        {
            $strMsg.="活动id为空";
        }
        if(strlen($strMsg)>0)
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']=$strMsg;
            return $returnData;
        }
        $shareEventRecordData = (new LiShareEventRecordService())->getList(['source_id' => $where['source_id'],'share_event_id'=>$where['share_event_id'],'current_status'=>0]);
        if(!empty($shareEventRecordData))
        {
            $returnData['data'] =1;
        }
        return $returnData;
    }

    /***
     * todo 某个活动参与者信息
     * @param array $where
     * @return array
     * @author jonzhang
     * @date 2018-01-13
     */
    public function showActorData($where=[],$amount=0,$unitAmount=0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' =>
            ['amount'=>0, 'total'=>0,'memberCount'=>0,'members'=>[]]
        ];
        if(empty($where))
        {
            $returnData['errCode']=-100;
            $returnData['errMsg']='查询条件为空';
            return $returnData;
        }
        $strMsg="";
        if(empty($where['shareId']))
        {
            $strMsg.="分享者id为空 ";
        }
        if(empty($where['activityId']))
        {
            $strMsg.="活动id为空";
        }
        if(strlen($strMsg)>0)
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']=$strMsg;
            return $returnData;
        }

        if(empty($amount))
        {
            return $returnData;
        }

        $activityId=$where['activityId'];
        $shareId=$where['shareId'];

        $shareEventRecordService=new LiShareEventRecordService();
        //参与者总数
        $total=0;
        $members=[];
        //有效的参与者数
        $cnt=0;
        $shareEventRecordData=$shareEventRecordService->getList(['share_event_id'=>$activityId,'source_id'=>$shareId],'','','created_at','desc');
        if(!empty($shareEventRecordData))
        {
            //定义有效助减金额
            $targetAmount=0;
            foreach($shareEventRecordData as $item)
            {
                $total++;
                if($targetAmount<$amount)
                {
                    $cnt++;
                }
                if($item['red_packet_id']>0)
                {
                    $targetAmount=$targetAmount+$item['red_packet_money'];
                }
                else if($item['red_packet_id']==0)
                {
                    $targetAmount=$targetAmount+$unitAmount;
                }
                if($targetAmount>=$amount)
                {
                    $targetAmount=$amount;
                }
                $member['actor_id']=$item['actor_id'];
                $member['nick_name']=$item['nick_name'];
                $member['avatar_url']=$item['avatar_url'];
                $member['created_at']=date('Y/m/d H:i:s',$item['created_at']);
                $member['is_red_packet'] =  $item['red_packet_id'] == 0 ? 0 : 1;
                $member['red_packet_money'] = $item['red_packet_money'];
                //参与者头像信息
                $members[]=$member;
            }
            //助减的金额
            $returnData['data']['amount'] = sprintf("%.2f",$targetAmount);
        }
        $returnData['data']['members']=$members;
        $returnData['data']['total']=$total;
        $returnData['data']['memberCount']=$cnt;
        return $returnData;
    }

    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function insertLiShareEventRecord($wid,$shareId,$mid,$activityId)
    {
        $returnData = ['code'=> 0 ,'msg' => 'success','data'=> [] ];

        if ($mid == $shareId ) {
            $returnData['msg'] = '分享者提交';
            return $returnData;
        }

        //$shop = WeixinService::getStore($wid);
        $shopService = new ShopService();
        $shop = $shopService->getRowById($wid);
        $key  = $shop['share_event_key']??'';
        $statusData = $this->isShareEvent(['shareEventId'=>$activityId,'actorId'=>$mid,'key'=>$key,'shareId'=>$shareId]);
        //参与者可以参与活动
        if($statusData['errCode'] == 0 && $statusData['data'] == 1) {
            $member = MemberService::getRowById($mid);
            $input = [
                'key' => $key,
                'actor_id' => $mid,
                'wid' => $wid,
                'source_id' => $shareId,
                'share_event_id' => $activityId,
                'avatar_url' => $member['headimgurl']??'',
                'nick_name' => $member['nickname']??'',
            ];
            $shareEventRecordService = new LiShareEventRecordService();
            $shareEventRecord = $shareEventRecordService->createShareEventRecord($input);
            if ($shareEventRecord['err_code'] != 0) {
                $returnData['code'] = -108;
                $returnData['msg'] = $shareEventRecord['msg'];
                return $returnData;
            } else {
                $shareEventService = new LiShareEventService();
                $shareEventService->incrementReduceTotal($activityId);
                $shareEventData = $shareEventService->getOne($activityId,$wid);
                (new LiDetailService())->updateEventLiDetail($shareId,$activityId,$shareEventData['like_count']);
                $returnData['code'] = 0;
                //返回分享者信息
                $member = MemberService::getRowById($shareId);
                $returnData['data'] = [
                    'nickName' => $member['nickname'],
                    'head_img' => $member['headimgurl'],
                ];
                $returnData['msg'] = 'success';
                return $returnData;
            }
        }

        return $returnData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180206
     * @desc 成功获取小程序
     */
    public function successfulGet($mid,$activityId)
    {
        $memberData = MemberService::getRowById($mid);
        $openid = $wid = '';
        $redisClient = (new RedisClient())->getRedisClient();
        $openid = $redisClient->GET($this->getKey($memberData['unionid']));
        $wid = $memberData['wid'];
        if ($memberData['openid']){
            $openid = $memberData['openid'];
            $wid = $memberData['wid'];
        }elseif($memberData['unionid']){
            $where = [
                'unionid'       => $memberData['unionid'],
                'source'        => ['<>',6]
            ];
            $mData = MemberService::getList($where);
            if ($mData){
                $mData = current($mData);
                $openid = $mData['openid'];
                $wid = $mData['wid'];
            }
        }

        if ($openid){
            $this->sendMessage($openid,$wid);
        }
        (new LiDetailService())->updateFullLiDetail($mid,$activityId);
        //领取成功是推送消息 add MayJay
        $job = new SendTplMsg(['mid'=>$mid,'wid'=>$wid,'event_id'=>$activityId],14);
        dispatch($job->onQueue('SendTplMsg'));
        $this->addLog($memberData);

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180206
     * @desc 发送消息
     * @param $openid
     */
    public function sendMessage($openid,$wid,$type=1)
    {

        $data = [
            'touser'        => $openid,
            'msgtype'       => 'text',
            'text'          => [
                'content'       => $this->getSendMessage($type),
            ],
        ];

        (new CustomService($wid))->sendMsg($data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180206
     * @desc
     */
    public function getSendMessage($type=1)
    {
        if ($type == 1){
            $str = <<<EOF
欢迎关注会搜科技股份！首先，恭喜您已成功领取到价值12800元的小程序资格。为了确保您快速领取成功，您可以自行提前操作以下步骤：\n
<a href="https://www.huisou.cn/shop/freeXCX/apply/626">☞ 小程序免费领</a>\n
<a href="https://v.qq.com/x/page/l0564nugj7u.html">☞ 如何注册小程序</a>\n
<a href="https://v.qq.com/x/page/b0564jpmuu2.html">☞ 如何申请邮箱</a>\n
<a href="https://v.qq.com/x/page/k056418bwas.html">☞ 如何搭建小程序</a>\n
<a href="https://www.huisou.cn/shop/kf/index?wid=626">☞ 在线客服</a>\n
EOF;
        }else{
            $str = <<<EOF
恭喜您！已成功报名价值12800元的3天3夜移动互联网实战总裁班课程。届时请准时参加！\n
<a href="https://www.huisou.cn/shop/freeXCX/apply/626">☞ 小程序免费领</a>\n
<a href="https://v.qq.com/x/page/l0564nugj7u.html">☞ 如何注册小程序</a>\n
<a href="https://v.qq.com/x/page/k056418bwas.html">☞ 如何搭建小程序</a>\n
<a href="https://www.huisou.cn/shop/kf/index?wid=626">☞ 在线客服</a>\n
EOF;
        }

        return $str;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180206
     * @desc 写日志
     */
    public function addLog($memberData)
    {
        $data = [
            'wid'       => $memberData['wid'],
            'mid'       => $memberData['id'],
            'openid'    => $memberData['openid'],
            'unionid'   => $memberData['unionid'],
        ];
        (new LiShareLogService())->add($data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180206
     * @desc 是否领取过小程序
     */
    public function isGetMinApp($wid,$unionid)
    {
        $where = [
            'unionid'       => $unionid,
        ];

        $res = MemberService::getList($where);
        if (!$res){
            return false;
        }else{
            $mids = array_column($res,'id');
            $data = (new LiRegisterService())->model->whereIn('mid',$mids)->get()->toArray();
            return !!$data;
        }

    }

    public function getMyFriend($mid, $activityId)
    {
        $return = [];
        $friendService = new LiFriendService();
        $mids = $friendService->getMyFriendList($mid, $activityId);
        if (empty($mids)) {
            return [];
        }
        $detailService = new LiDetailService();

        $return['detail'] = $detailService->getMyFriendList($mids, $activityId);

        $mids[] = $mid;
        $members = MemberService::getListById($mids);
        if (!empty($members)) {
            foreach ($members as $key => $value) {
                $return['member'][$value['id']] = $value;
            }
        }
        $return['mine'] = $detailService->getRowByMidAndEventId($mid,$activityId);
        return $return;
    }

    /**
     * @param $mid 用户id
     * @param $event_id 活动id
     * @param $wid 店铺id
     * @param $formId
     * @param $xcxConfigId 小程序配置id
     * @return array
     * @author: 梅杰 20180709
     */
    public function shareCallBack($mid,$event_id,$wid,$formId,$xcxConfigId)
    {
        $return = [
            'msg'       => 'success',
            'err_code'  => 0,
            'data'      => []
        ];
        $service = new LiDetailService();
        $data = $service->getRowByMidAndEventId($mid,$event_id);
        if ($data['is_share'] != 0) {
            return $service->updateShareLiDetail($mid,$event_id);
        }

        $job = new SendTplMsg(['mid'=>$mid,'wid'=>$wid,'event_id'=>$event_id,'formId'=>$formId,'xcx_config_id'=>$xcxConfigId],12);
        dispatch($job->onQueue('SendTplMsg'));
        return $service->updateShareLiDetail($mid,$event_id);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180208
     * @desc 获取可以
     * @param $unionid
     */
    public function getKey($unionid)
    {
        return 'weixin:unionid:'.$unionid;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180224
     * @desc 获取集赞名次
     * @param $eventId
     */
    public function getLikeNum($eventId)
    {
        $num = (new LiDetailService())->getCount(['event_id'=>$eventId,'is_full'=>1]);
        return $num+1;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180227
     * @desc 处理用户注册信息
     */
    public function dealRegister($ids)
    {
        $result = ['errCode'=> 0 ,'errMsg'=> ''];
        $liRegisterService = (new LiRegisterService());
        $data = $liRegisterService->getListById($ids);
        if (!$data){
            $result['errCode'] = 1;
            $result['errMsg'] = '注册用户不存在';
            return $result;
        }
        $parameter = [];
        foreach ($data as $val){
            $temp = [
                'mphone'        => $val['phone'],
                'name'          => $val['company_name'],
            ];
            $parameter[] = $temp;
        }
        $res = (new UserService())->addUser($parameter);
        if ($res['errCode'] == 0){
            if ($liRegisterService->batchUpdate($ids,['is_register'=>1])){
                return $result;
            }else{
                $result['errCode'] = 2;
                $result['errMsg'] = '更新失败';
                return $result;
            }
        }else{
            return $res;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180307
     * @desc 根据手机号码注册用户
     */
    public function registerByMobile($mobiles)
    {
        $parameter = [];
        foreach ($mobiles as $val){
            $temp = [
                'mphone'        => $val,
            ];
            $parameter[] = $temp;
        }
        $res = (new UserService())->addUser($parameter);
        if ($res['errCode'] == 0){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 分享链接参数
     */
    public function getShareMid($umid, $mid)
    {
        $li_register_service = new LiRegisterService();
        if ($li_register_service->isApplied($umid) || $this->isSalesman($mid)) {
            return $mid;
        }

        return 0;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180227
     * @desc 发送短信
     * @param $ids
     * @return array
     */
    public function sendSMS($ids)
    {
        $result = ['errCode'=> 0 ,'errMsg'=> ''];
        $liRegisterService = new LiRegisterService();
        $data = $liRegisterService->getListById($ids);
        if (!$data){
            $result['errCode'] = 1;
            $result['errMsg'] = '注册用户不存在';
            return $result;
        }
        $sourceData = [];
        $ids = [];
        foreach ($data as $val){
            if ($val['is_sms'] == 0){
                $ids[] = $val['id'];
                $sourceData = $data;
            }
        }
        $liRegisterService->batchUpdate($ids,['is_sms'=>1]);
        foreach ($sourceData as $val){
            $temp = [
                'id'        => $val['id'],
                'phone'     => $val['phone'],
            ];
            dispatch((new SendRegisterSMS($temp))->onQueue('SendRegisterSMS'));
        }

        return $result;

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 2018031
     * @desc 绑定上下架关系
     * @param $mid
     * @param $mobile
     */
    public function addsSellerkpi($phone,$pid)
    {
        $result = ['errCode'=>0,'errMsg'=> ''];
        if (empty(intval($pid))){
            $data = [
                'mid'           => $phone,
                'prev_mid'      => 0,
                'top_mid'       => 0,
                'grade'         => 1,
                'manage_mid'    => 0
            ];
        }else{
            $result = $this->_getMobile($pid);
            if ($result['errCode'] != 0){
                return $result;
            }
            $data = $result['data'];
            $data['mid'] = $phone;
            $data['addtime'] = time();
        }

        $res = (new AdminSellerkpiService())->add($data);
        if ($res){
            return $result;
        }else{
            $result['errCode'] = -1;
            $result['errMsg'] = '保存失败';
            return $result;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180301
     * @desc 获取用户手机号吗
     * @param $pid
     */
    private  function _getMobile($pid)
    {
        $result = ['errCode'=>0,'errMsg'=> ''];
        $memberData = MemberService::getRowById($pid);
        if (!$memberData){
            $result = ['errCode'=>-11,'errMsg'=> '上级不存在'];
            \Log::info(__FILE__.'第：'.__LINE__.'行，错误：上级不存在,pid=='.$pid);
        }
        $umid = $memberData['umid'];
        $liSalesmanService = new LiSalesmanService();
        $saleManData = $liSalesmanService->getList(['umid'=>$umid]);
        if ($umid && $saleManData){
            $saleManData = current($saleManData);
            $data = [
                'prev_mid'      => $saleManData['mobile'],
                'top_mid'       => $saleManData['mobile'],
                'grade'         => 1,
                'manage_mid'    => $saleManData['mobile']
            ];
            $result['data'] = $data;
            return $result;
        }else{
            $sellerkpiService = new AdminSellerkpiService();
            $register = (new LiRegisterService())->model->where('mid',$pid)->get()->toArray();
            if (!$register){
                $result = ['errCode'=>-12,'errMsg'=> '上级不存在'];
                \Log::info(__FILE__.'第：'.__LINE__.'行，错误：上级没有注册,pid=='.$pid);
                return $result;
            }
            $register = current($register);
            $mobile = $register['phone'];
            $kpi = $sellerkpiService->getList(['mid'=>$mobile]);
            if (!$kpi){
                $result = ['errCode'=>-13,'errMsg'=> '上级没有注册'];
                \Log::info(__FILE__.'第：'.__LINE__.'行，错误：上级没有注册,pid=='.$pid);
                return $result;
            }

            $kpi = current($kpi);
            $result['data'] =  [
                'prev_mid'      => $mobile,
                'top_mid'       => $kpi['top_mid'],
                'grade'         => $kpi['grade']+1,
                'manage_mid'    => $kpi['manage_mid']
            ];
            return $result;
        }

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180301
     * @desc 判断用户是否是销售
     * @param $mid
     */
    public function isSalesman($mid)
    {
        $memberData =  MemberService::getRowById($mid);
        if (!$memberData || !$memberData['umid']){
            return false;
        }
       return !!(new LiSalesmanService())->getList(['umid'=>$memberData['umid']]);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180305
     * @desc 领取成功发送关注信息
     * @param $mid
     */
    public function registerSuccess($mid)
    {
        $memberData = MemberService::getRowById($mid);
        if ($memberData){
            $this->sendMessage($memberData['openid'],$memberData['wid']);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180307
     * @desc 课程领取成功
     * @param $mid
     */
    public function courseGetSuccess($mid)
    {
        $memberData = MemberService::getRowById($mid);
        if (!$memberData){
            return false;
        }
        $this->sendMessage($memberData['openid'],$memberData['wid'],2);
        $nexusService = new MeetingNexusService();
        $result = $nexusService->getRowByOpenId($memberData['openid']);
        if (!$result){
            return false;
        }
//        return (new ActivityRegisterService())->increment(['mid'=>$result['mid']],'num',1);
    }



    public function isGetCourse($unionid)
    {
        $where = [
            'unionid'       => $unionid,
        ];

        $res = MemberService::getList($where);
        if (!$res){
            return false;
        }else{
            $mids = array_column($res,'id');
            $data = (new LiRegisterService())->model->whereIn('mid',$mids)->where('type',3)->get()->toArray();
            return !!$data;
        }
    }


}