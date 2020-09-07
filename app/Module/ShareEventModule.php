<?php

namespace App\Module;

use App\Events\NewUserEvent;
use App\Jobs\SendTakeAway;
use App\Lib\Redis\NewUserFlagRedis;
use App\Jobs\SendPayedOrderLog;
use App\S\MarketTools\MessagesPushService;
use App\S\ShareEvent\ShareEventRedPacketService;
use App\S\ShareEvent\ShareRewardService;
use Illuminate\Support\Facades\Event;
use OrderCommon;
use DB;
use OrderService;
use OrderDetailService;
use OrderLogService;
use App\S\ShareEvent\ShareEventService;
use App\S\ShareEvent\ShareEventRecordService;
use MemberService;
use App\Services\Shop\MemberAddressService;
use App\Lib\BLogger;
use WeixinService;
use ProductService;
use App\S\Product\ProductSkuService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Weixin\ShopService;

class ShareEventModule
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
        $shareEventRecordService = new ShareEventRecordService();

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

        $shareEventService = new ShareEventService();
        $where = ['wid' => $wid, 'id' => $activityId, 'type' => 0, 'status' => 0];
        $unitAmount = 0.00;
        $lowerPrice = 0.00;
        $shareEventResult = $shareEventService->list($where);
        if (!empty($shareEventResult[0]['data'])) {
            $shareEventData = $shareEventResult[0]['data'][0];
            // add by jonzhang 享立减活动过期判断
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
        $productReturnData = $productModule->getProductByShareEvent($productId, $skuId);
        if ($productReturnData['errCode'] == 0 && !empty($productReturnData['data'])) {
            $product = $productReturnData['data'];
			//update by 吴晓平 2018年08月22日  取消分销商品不能够享立减限制
            /*if ($product['is_distribution']) {
                $returnData['errCode'] = -106;
                $returnData['errMsg'] = '分销商品不能够参加享立减活动';
                return $returnData;
            }*/
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
            $productData['no_logistics'] = $product['no_logistics']??0;//add by zhangyh 20180126 无需物流标识

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
     * 享立减待提交订单信息
     * @param int $wid 店铺ID
     * @param int $mid 用户ID
     * @param array $condition 查询条件数组
     * @return array
     * @author 张国军 2017年12月7日
     * @update 许立   2018年6月28日 修改获取运费功能
     * @update 吴晓平 2018年6月27日 修改返回增加用户余额字段

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

        $member = MemberService::getRowById($mid);
        if (empty($member)) {
            $returnData['errCode'] = -106;
            $returnData['errMsg']  = "用户不存在或未被授权";
            return $returnData;
        }
        //用户余额
        $balance = $member['money']/100; 
        //运费信息
        $freight = 0.00;
        // 许立 2018年6月28日 无地址的时候导入第一个微信地址 会出现无默认地址的情况 此处去掉判断 获取正确运费
        //if(!empty($userAddress['default']))
        //{
            //此处为商品的运费
            $umid = $member['umid']??0;
            $orderModule = new OrderModule();
            //$addressId=$userAddress['default'][0]['id'];
            $freight = $orderModule->getFreightByCartIDArr([], $wid, $mid, $umid, 0, [['product_id' => $productReturnData['data']['product_id'], 'prop_id' => $skuId, 'num' => 1]],false);
            $freight = sprintf('%.2f', $freight);
        //}
        $returnData['data']['freight'] = $freight;
        $returnData['data']['balance'] = $balance; // 增加返回用户余额

        return $returnData;
    }

    /**
     * todo 提交享立减订单
     * @param $wid
     * @param $mid
     * @param array $data
     * @param int $source
     * @param int $orderType
     * @return array
     * @author jonzhang
     * @date 2017-12-07
     * @update 张永辉 2018年7月9日 小程序配置信息写入订单
     * @update 何书哲 2018年8月20日 如果是0元订单，发送付款日志
     * @update 许立   2018年09月17日 下单库存为0不下架
     * @update 梅杰 2018年10月18日 待付款消息提醒
     * @update 何书哲 2018年11月15日 外卖订单0元订单导入第三方
     * @update 何书哲 2018年11月16日 标记外卖订单
     * @update 何书哲 2018年11月22日 外卖店铺添加订单提交约束
     */
    public function submitShareEventOrder($wid = 0, $mid = 0, $data = [], $source = 0, $orderType = 1,$remarkNo='')
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

        //何书哲 2018年11月22日 外卖店铺添加订单提交约束
        $checkRes = (new StoreModule())->checkIfSubmitOrder($wid);
        if ($checkRes['errCode'] != 0) {
            $returnData['errCode'] = -13;
            $returnData['errMsg'] = $checkRes['errMsg'];
            return $returnData;
        }

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
        if ($productData['no_logistics'] == 0 && empty($addressId)) {
            $returnData['errCode'] = -11;
            $returnData['errMsg'] = '地址不能为空';
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
        $productTotalAmount = sprintf('%.2f', $productData['price'] * $productData['num']);
        //商品总价
        $order['products_price'] = $productTotalAmount;
        $order['pay_price'] = $productTotalAmount;
        $order['change_price'] = 0.00;
        $order['source'] = $source;
        $order['form_id'] = 0;
        if ($source == 1) {
            $order['form_id'] = $formId;
        }
        $freight = 0.00;
        //此处为商品的运费
        $member = MemberService::getRowById($mid);
        $umid=$member['umid']??0;
        $orderModule=new OrderModule();
        $freight=$orderModule->getFreightByCartIDArr([], $wid, $mid,$umid, $addressId, [['product_id' =>$productData['product_id'],'prop_id'=>$skuId,'num'=>1]],false);
        $freight=sprintf('%.2f',$freight);
        //最后实际支付金额要加上运费金额
        $order['pay_price'] = $order['pay_price'] + $freight;
        $order['pay_price'] = sprintf('%.2f', $order['pay_price']);
        $order['cash_fee'] = 0.00;
        $order['bonus_point_amount'] = 0.00;

        //运费金额
        $order['freight_price'] = $freight;
        //物流方式 1快递发货
        $order['express_type'] = 1;
    /*
        $userAddress = (new MemberAddressService())->getAddressById($addressId);
        if (empty($userAddress)) {
            $returnData['errCode'] = -10;
            $returnData['errMsg'] = "收货地址为空";
            return $returnData;
        }

        $order['address_id'] = $userAddress['id'];
        $order['address_name'] = $userAddress['title'];
        $order['address_phone'] = $userAddress['phone'];
        //详细地址
        $order['address_detail'] = $userAddress['detail'];

        $order['address_province'] = $userAddress['province']['title'];
        $order['address_city'] = $userAddress['city']['title'];
        $order['address_area'] = $userAddress['area']['title'];
    */
    //add by zhangyh 20180126 无需物流
        if (!empty($addressId)){
            $userAddress = (new MemberAddressService())->getAddressById($addressId);
            if (empty($userAddress)) {
                $returnData['errCode'] = -10;
                $returnData['errMsg'] = "收货地址为空";
                return $returnData;
            }
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
        //end
        //加入享立减活动id
        $order['share_event_id'] = $activityId;

        // 张永辉 2018年7月9日 写入小程序配置id
        $token = app('request')->input('token','0');
        $order['xcx_config_id'] = (new CommonModule())->getXcxConfigIdByToken($token);
        //end

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
            $order['status'] = 1;//已付款
            $order['pay_way'] = 10;//小程序支付
        }
        $order['is_takeaway'] = (new StoreModule())->getWidTakeAway($wid) ? 1 : 0;//何书哲 2018年11月16日 标记外卖订单
        //订单明细
        $orderDetail = [];
        $orderDetail['oid'] = $orderNo;

        $orderDetail['product_id'] = $productData['product_id'];
        $orderDetail['title'] = $productData['product_name'];
        $orderDetail['img'] = $productData['img'];
        $orderDetail['price'] = $productData['price'];
        $orderDetail['oprice'] = $productData['oprice'];
        $orderDetail['num'] = 1;
        $orderDetail['spec'] = $productData['product_spec'];
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
            //何书哲 2018年8月20日 如果是0元订单，发送付款日志
            if ($order['pay_price'] == 0) {
                dispatch((new SendPayedOrderLog($orderReturn))->onQueue('orderPayed'));
            }
            //添加订单详情
            $orderDetail['oid'] = $orderReturn;
            $orderDetailReturn = OrderDetailService::init()->addD($orderDetail, false);
            if (!$orderDetailReturn) {
                throw new \Exception('添加订单明细失败');
            }

            $shareEventRecordReturn = (new ShareEventRecordService())->update(['share_event_id' => $data['activityId'], 'source_id' => $mid, 'current_status' => 0]);
            if ($shareEventRecordReturn['err_code'] == 1) {
                throw new \Exception('享立减更改失败');
            }

            //add by jonzhang 2018-01-17 添加库存
            //更改商品库存
            $stockNum=1;
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
                /*if($productStock==0)
                {
                    $stockData['status']=0;
                }*/
                ProductService::update($productData['product_id'],$stockData);
            }
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
        $source == 1 && (new MessagePushModule($wid,MessagesPushService::TradeUrge,MessagePushModule::SEND_TARGET_WECHAT_XCX))->setDelay(60)->sendMsg($orderReturn,$order['xcx_config_id']);
        $source == 0 && (new MessagePushModule($wid,MessagesPushService::TradeUrge))->setDelay(60)->sendMsg($orderReturn);

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

        //何书哲 2018年11月15日 外卖订单0元订单导入第三方
        $isFree && dispatch(new SendTakeAway($orderReturn));

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
        $shareEventRecordData = (new ShareEventRecordService())->getList(['actor_id' => $where['actorId']]);
        //BLogger::getLogger('info')->info('参与者所有参与数据:'.json_encode($shareEventRecordData));
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
        $returnData['msg'] = '未设置享立减红包';
        $returnData['code'] = -2 ;
        return $returnData;
        $redPacketService = new ShareEventRedPacketService();
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
            $money = (new ShareRewardService())->getReduceSum($wid);
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
        $redPacketService = new ShareEventRedPacketService();
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
        $returnData = (new ShareEventRecordService())->createShareEventRecord($input);
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
        $activityData = (new ShareEventService())->getOne($activityId,$member['wid']);
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
        $is_new = (new ShareEventRecordService())->count(['actor_id'=>$mid,'key'=>$key]);
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
        $shareEventRecordData = (new ShareEventRecordService())->getList(['source_id' => $where['source_id'],'share_event_id'=>$where['share_event_id'],'current_status'=>0]);
        if(!empty($shareEventRecordData))
        {
            $returnData['data'] =1;
        }
        //BLogger::getLogger('info')->info('分享者活动是否有人参与,查询条件:'.json_encode($where).' 返回数据：'.json_encode($returnData));
        return $returnData;
    }

    /***
     * todo 某个活动参与者信息
     * @param array $where
     * @return array
     * @author jonzhang
     * @date 2018-01-13
     * @update 梅杰 2018年8月14号 增加新用户标识
     */
    public function showActorData($where=[],$amount=0,$unitAmount=0,$mid)
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

        $shareEventRecordService=new ShareEventRecordService();
        //参与者总数
        $total=0;
        $members=[];
        //有效的参与者数
        $cnt=0;
        $shareEventRecordData=$shareEventRecordService->getList(['share_event_id'=>$activityId,'source_id'=>$shareId,'current_status'=>0],'','','created_at','desc');
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

        //增加新用户统计
        if ((new NewUserFlagRedis())->get($mid) ){
            $data = [
                'page'          => '/shareevent/product/showproductdetail',
                'type'          => 1,
                'param_id'       => $activityId,
                'register_time' => time(),
            ];
            Event::fire(new NewUserEvent($data));
        }
        return $returnData;
    }

    /**
     * 分享者活动是否有人购买
     * @param array $where
     * @return array
     * @author 何书哲 2018年8月9日
     */
    public function isSharePurchasedByMid($where=[])
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
        $shareEventRecordData = (new ShareEventRecordService())->getList(['source_id' => $where['source_id'],'share_event_id'=>$where['share_event_id'],'current_status'=>1]);
        if(!empty($shareEventRecordData) || !empty($shareEventActorRecordData))
        {
            $returnData['data'] =1;
        }
        return $returnData;
    }

    /**
     * 获取享立减达到保底价的时间
     * @param $id 享立减id
     * @param $wid 店铺id
     * @param $share_at 分享时间
     * @param int $mid 购买用户id
     * @return string
     * @author 何书哲 2018年8月9日
     */
    public function reachLowerPriceTime($id, $wid, $share_at, $mid=0, $is_purchased=0, $source=0) {
        $time = '-';
        $shareEventService = new ShareEventService();
        $shareEventRecordService = new ShareEventRecordService();
        $shareData = $shareEventService->getOne($id, $wid);
        //不存在，直接返回
        if (!$shareData) {
            return $time;
        }
        //获取商品信息
        $productInfo = ProductService::getDetail($shareData['product_id']);
        if (!$productInfo) {
            return $time;
        }
        //商品价格
        $productPrice = $productInfo['price'];
        //减价次数
        $shareTime = 0;
        //未支付
        if ($is_purchased == 0) {
            $reduceNum = (int)ceil(($productPrice*100-$shareData['lower_price'])/$shareData['unit_amount']);
            if ($reduceNum == 0) {
                return $time;
            }
            $shareTime = $shareEventRecordService->getRecordByOrder($id, $mid, $reduceNum);
        } else {
            //已支付
            //需要去查订单表
            $where = ['wid'=>$wid, 'mid'=>$mid, 'share_event_id'=>$id, 'pay_way'=>['<>', 0]];
            if ($source == 1) {
                $where['source'] = 0;
            } elseif ($source == 2) {
                $where['source'] = 1;
            }
            $orderData = OrderService::init()->model->wheres($where)->first();
            if (!$orderData) {
                return $time;
            }
            $payTime = OrderLogService::init()->model->where(['oid'=>$orderData->id, 'wid'=>$wid, 'action'=>2])->orderBy('created_at', 'asc')->select(['created_at'])->first();
            if ($payTime) {
                $shareTime = strtotime($payTime->created_at);
            } else {
                $shareTime = strtotime($orderData->created_at);
            }
        }
        if ($shareTime) {
            $share_at = strtotime($share_at);
            if ($shareTime < $share_at) {
                return $time;
            }
            return $this->shareTimeFormat($shareTime-$share_at);
        }
        return $time;
    }

    /**
     * 时间转化
     * @param $time 时间戳
     * @return string
     * @author 何书哲 2018年8月9日
     */
    public function shareTimeFormat($time){
        if($time < 60){
            return $time.'秒';
        }elseif(3600 > $time && $time>= 60){
            $min = floor($time / 60);
            $sec = $time - (60 * $min);
            return $min.'分'.$sec.'秒';
        }elseif($time >= 3600 && $time < 24*3600){
            $hour = floor($time / 3600);
            $min = floor(($time - (3600 * $hour))/60);
            $sec = $time - (3600 * $hour) - (60 * $min);
            return $hour.'时'.$min.'分';
        }else{
            $day = floor($time/3600/24);
            return $day.'天';
        }
    }


}



