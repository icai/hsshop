<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/17
 * Time: 15:17
 */

namespace App\Module;

use App\Jobs\BatchDelivery;
use App\Jobs\Distribution;
use App\Jobs\SendGroupsLog;
use App\Jobs\ImportOrderLogistics;
use App\Jobs\SendTakeAway;
use App\Jobs\SendTplMsg;
use App\Lib\Redis\SeckillSku;
use App\Model\OrderRefund;
use App\Model\Store;
use App\S\Foundation\ExpressService;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Market\CouponLogService;
use App\S\MarketTools\MessagesPushService;
use App\S\NotificationService;
use App\S\Product\RemarkService;
use App\S\ShareEvent\LiEventService;
use App\S\Weixin\DeliveryConfigService;
use Carbon\Carbon;
use DB, Log;
use Illuminate\Support\Facades\Redis;
use WXXCXCache;
use WeixinService;
use MemberAddressService;
use OrderCommon;
use MemberService;
use OrderService;
use OrderDetailService;
use OrderLogService;
use App\Services\Shop\CartService;
use ProductService;
use App\S\Product\ProductSkuService;
use App\S\Product\ProductPropsToValuesService;
use PointApplyRuleService;
use App\Services\Order\LogisticsService;
use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Model\Product;
use App\Model\ProductSku;
use App\S\Groups\GroupsDetailService;
use App\S\Market\SeckillService;
use App\S\Foundation\RegionService;
use App\Services\Order\OrderRefundService;
use App\Services\OrderRefundMessageService;
use App\Services\FreightService;
use App\S\Wechat\WeixinRefundService;
use App\Services\UserService;
use PointRecordService;
use OrderPointRuleService;
use OrderPointExtraRuleService;
use ProductEvaluateService;
use App\Model\Order;
use Illuminate\Http\Request;
use MemberCardRecordService;
use App\Model\Weixin;
use App\S\Lift\ReceptionService;
use App\S\Order\OrderZitiService;
use App\S\Cam\CamListService;
use App\S\Cam\CamActivityService;
use App\Model\Member;
use App\Services\Permission\WeixinRoleService;
use App\S\Weixin\ShopService;

class OrderModule
{

    const TIMEOUT_ORDER = 5;

    /**
     * 待提交订单信息
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @param json $cartId 购物车id json串
     * @param int $isXCX 是否是小程序 0:不是, 1:是
     * @param int $umid 统一账户id
     * @param int $address_id 地址id
     * @return array
     * @author 张国军 2017年8月18日
     * @update 许立   2018年6月26日 小程序秒杀获取运费需要传递地址参数
     * @update by 吴晓平 2018年06月28日 不是自提商品时返回前端判断状态说明
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年11月21日 返回店铺是否是外卖店铺
     */
    public function processWaitSubmitOrder($wid, $mid, $cartId, $isXCX = 0, $umid = 0, $address_id)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];

        if (empty($cartId)) {
            $returnData['code'] = -101;
            $returnData['hint'] = '请选择购物车中的商品';
            return $returnData;
        }
        if (empty($wid) || empty($mid)) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        try {
            $id = json_decode($cartId, true);
            if (empty($id)) {
                $returnData['code'] = -103;
                $returnData['hint'] = '数据转化出现问题';
                return $returnData;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $returnData['code'] = -104;
            $returnData['hint'] = $message;
            return $returnData;
        }

        //店铺信息
        //$store = WeixinService::getStageShop($wid);
        $shopService = new ShopService();
        $store = $shopService->getRowById($wid);
        $shopName = '';
        if (!empty($store)) {
            $shopName = $store['shop_name'];
        }
        //小程序主页URL
        if ($isXCX) {
            $shopUrl = config('app.url') . 'pages/index/index';
        }//微商城店铺主页
        else {
            $shopUrl = config('app.url') . 'shop/index/' . $wid;
        }
        //用户收货地址信息
        $userAddress = MemberAddressService::getUserAddress($mid);

        //查询购物车中的商品
        $conditionData['mid'] = $mid;
        $conditionData['wid'] = $wid;
        $conditionData['cart_id'] = $id;
        $conditionData['address_id'] = $address_id;

        //效验购物车中的商品
        $result = OrderService::processOrder($conditionData, $umid);
        //判断购物车中商品状态
        if ($result['errCode'] !== 0) {
            $returnData['code'] = $result['errCode'];
            $returnData['hint'] = $result['errMsg'];
            return $returnData;
        } else if ($result['errCode'] == 0 && !empty($result['data']['error'])) {
            $returnData['code'] = $result['data']['error'][0]['err_msg'][0]['errCode'];
            $returnData['hint'] = $result['data']['error'][0]['err_msg'][0]['errMsg'];
            //返回具体错误数据 Herry
            $returnData['list'] = ['data' => $result['data']['error'][0]['err_msg'][0]['data']];
            return $returnData;
        }
        //商品信息
        $userCart = $result['data'];

        $productInfoList = [];

        //商品是否设置了批发价 Herry
        $is_wholesale = 0;

        foreach ($userCart['correct'] as $item) {
            $productsId[] = $item['product_id'];
            //如果商品打折
            if ($userCart['is_discount']) {
                //如果商品打折扣 那么商品的当前金额应该为折扣的商品金额
                $item['product_amount'] = $item['after_discount_product_amount'];
            }
            //商品信息
            $productInfo = [];
            $productInfo['product_id'] = $item['product_id'];
            $productInfo['product_amount'] = $item['product_amount'];
            $productInfoList[] = $productInfo;
            $discount[] = [
                'id' => $item['product_id'],
                'price' => $item['product_amount'],
                'num' => $item['num'],
            ];

            $item['wholesale_flag'] && $is_wholesale = 1;
        }


        $discountDetail = (new DiscountModule())->getDiscountByPids($discount, $wid);
        $productDiscount = [];
        foreach ($discountDetail['discountDetail'] as $val) {
            foreach ($productInfoList as $key => $item) {
                if (in_array($item['product_id'], $val['discountPids'])) {
                    $productInfoList[$key]['product_amount'] = bcsub($productInfoList[$key]['product_amount'], $val['discount'] * ($item['product_amount'] / $val['amount']), 2);
                    $productDiscount[$item['product_id']] = bcmul($val['discount'], ($item['product_amount'] / $val['amount']), 2);
                }
            }
        }

        //优惠券信息
        if ($is_wholesale) {
            $coupon = [
                'valid' => [], //可用
                'invalid' => [], //不可用
                'default' => [], //最优,
                'all' => [] //全部
            ];
        } else {
            $coupon = $this->getDefaultCouponByMid($wid, $mid, $productInfoList, $userCart['is_discount']);
        }
        //优惠劵优惠金额
        $couponAmount = 0.00;
        if (!empty($coupon['default'])) {
            $couponAmount = $coupon['default']['discount_amount'];
        }
        //商品总金额
        //$productTotalAmount=0.00;
        if ($userCart['is_discount']) {
            $productTotalAmount = $userCart['after_discount_amount'];
        } elseif (isset($userCart['groupFlag'])) {
            $productTotalAmount = $userCart['after_discount_amount'];
        } else {
            $productTotalAmount = $userCart['amount'];
        }

        //add by jonzhang 2017-11-23
        $usePointAmount = 0.00;
        $cartProducts = [];
        //购物车中的商品信息
        foreach ($userCart['correct'] as $productItem) {
            $cartProduct = [];
            $cartProduct['cart_id'] = $productItem['cart_id'];
            $cartProduct['product_name'] = $productItem['product_name'];
            $cartProduct['num'] = $productItem['num'];
            //如果商品打折扣，那商品的价格为折扣价
            if ($userCart['is_discount']) {
                $cartProduct['price'] = $productItem['after_discount_price'];
            } else {
                $cartProduct['price'] = $productItem['price'];
            }
            //add by jonzhang 2017-11-23
            if ($productItem['is_point']) {
                $usePointAmount = $usePointAmount + $cartProduct['price'] * $cartProduct['num'];
            }
            $cartProduct['img_path'] = $productItem['img_path'];
            $cartProduct['attr'] = $productItem['attr'];
            $cartProduct['product_id'] = $productItem['product_id'];
            //add by 张国军 2018-08-07
            $cartProduct['cam_id'] = $productItem['cam_id'];
            $cartProducts[] = $cartProduct;
        }

        //使用优惠券的最后需要支付金额
        $lastAmount = $productTotalAmount - $couponAmount;
        $noCouponLastAmount = $usePointAmount;
        //对订单金额小于0的特殊处理
        if ($lastAmount < 0) {
            //$couponAmount=$productTotalAmount;
            $lastAmount = 0.00;
        }
        //没有使用优惠券订单金额小于0特殊处理
        if ($noCouponLastAmount < 0) {
            $noCouponLastAmount = 0.00;
        }
        //可使用积分金额
        $usePointAmount = $usePointAmount - $couponAmount - $discountDetail['discount'];
        if ($usePointAmount < 0) {
            $usePointAmount = 0.00;
        }

        //展现积分抵现逻辑begin
        //订单支付金额大于0时，才会使用积分
        $point = 0;
        $bonusPoints = 0.0;

        $noCouponPoint = 0;
        $noCouponBonusPoints = 0.0;

        //是否显示积分div
        $isShowPointDiv = 0;
        //是否可使用积分 0表示不可用
        $isUsePoint = 0;
        //积分兑换货币
        //店铺是否开启积分
        //$storePointData = WeixinService::selectPointStatus(['id' => $wid]);
        $storePointData = $shopService->getRowById($wid);
        if (!$is_wholesale && !empty($storePointData)) {
            $isUsePoint = $storePointData['is_point'];
            $isShowPointDiv = $isUsePoint;
        }
        if ($isShowPointDiv) {
            //积分消费规则是否开启
            $pointApplyRuleData = PointApplyRuleService::getRow($wid);
            if ($pointApplyRuleData['errCode'] == 0 && !empty($pointApplyRuleData['data'])) {
                $isShowPointDiv = $pointApplyRuleData['data']['is_on'] ?? 0;
            }
        }
        if ($usePointAmount > 0 && $isShowPointDiv) {
            //店铺开启使用积分
            if ($isUsePoint) {
                $myPoint = 0;
                //查询该用户拥有的积分
                $memberPointData = MemberService::getRowById($mid);
                if (!empty($memberPointData)) {
                    $myPoint = $memberPointData['score'];
                }
                //积分大于0
                if ($myPoint > 0) {
                    $exchangeData = $this->getAmountByPoint($wid, $myPoint, $usePointAmount);
                    if ($exchangeData['errCode'] == 0 && !empty($exchangeData['data'])) {
                        //用户可用的积分，金额
                        $point = $exchangeData['data']['point'];
                        $bonusPoints = $exchangeData['data']['amount'];
                    }
                    $noCouponExchangeData = $this->getAmountByPoint($wid, $myPoint, $noCouponLastAmount);
                    if ($noCouponExchangeData['errCode'] == 0 && !empty($noCouponExchangeData['data'])) {
                        //用户可用的积分，金额
                        $noCouponPoint = $noCouponExchangeData['data']['point'];
                        $noCouponBonusPoints = $noCouponExchangeData['data']['amount'];
                    }
                }
            }
        }

        $freight = $userCart['freight'];
        //计算满减活动优惠金额 2018年8月27日
        if ($lastAmount >= $discountDetail['discount']) {
            $lastAmount = bcsub($lastAmount, $discountDetail['discount'], 2);
        } else {
            $lastAmount = 0.00;
        }//end

        //最后总金额
        $lastAmount = $lastAmount + $freight;
        //展现积分抵现逻辑end
        $lastAmount = sprintf('%.2f', $lastAmount);
        //是否需要绑定手机号码
        (new BindMobileModule())->xcxIsBind($mid, $wid) ? $isBind = 1 : $isBind = 0;

        //是否是秒杀
        $is_seckill = 0;

        if (count($id) == 1) {
            $cartData = (new CartService())->init()->getInfo($id[0]);
            //判断秒杀
            if ($cartData && $cartData['seckill_id'] != 0) {
                $is_seckill = 1;
                // 许立 2018年6月26日 小程序秒杀获取运费需要传递地址参数
                $result = (new SeckillModule)->getWaitPayOrder($cartData, $wid, $mid, $umid, $address_id);
                //重新设置优惠选择项
                //$couponAmount = $result['couponAmount'];
                //优惠券默认返回格式
                $coupon = ['all' => [], 'default' => [], 'valid' => [], 'invalid' => []];
                $lastAmount = $result['lastAmount'];
                $productTotalAmount = $result['productTotalAmount'];
                $point = 0;
                $bonusPoints = 0;
                $noCouponPoint = 0;
                $noCouponBonusPoints = 0;
                $freight = $result['freight'];
                $cartProducts[0]['price'] = $result['seckillPrice'];
                $isShowPointDiv = 0;
                $noCouponLastAmount = 0.00;
            }
        }

        $isDeliveryShow = false;  // 是否显示配送方式选择
        $distributionData = [];   // 购物车中商品是否可以一起下单返回的数据
        if ($store['is_ziti_on'] == 1) {
            $receptionService = new ReceptionService();
            $distributionData = $receptionService->isZitiProduct($cartProducts);
            if ($distributionData['status'] == 2) {
                $logicAmount = $zitiAmount = 0;
                foreach ($distributionData['data']['Logistics'] as $key => $logis) {
                    $logicAmount += $logis['price'] * $logis['num'];
                }
                $logicCoupon = $zitiCoupon = $couponAmount;
                foreach ($distributionData['data']['ziti'] as $key => $ziti) {
                    $zitiAmount += $ziti['price'] * $ziti['num'];
                }
                if ($couponAmount > $logicAmount) {
                    $logicCoupon = $logicAmount;
                }
                if ($couponAmount > $zitiAmount) {
                    $zitiCoupon = $zitiAmount;
                }
                /*if (count($distributionData['data']['Logistics']) <> count($distributionData['data']['all'])) {
                    $amount = 0;
                    foreach ($distributionData['data']['Logistics'] as $key => $logis) {
                        $amount += $logis['price'] * $logis['num'];
                    }
                    //重新计算商品总价
                    $productTotalAmount = $amount;
                    //重新计算商品合计总价（商品总价 + 运费 - 优惠金额 - 可使用的商品积分金额）
                    $goodAmount = $productTotalAmount-$couponAmount;
                    //dd($noCouponAmount);
                    if ($goodAmount < 0) {
                        $goodAmount = 0;
                    }
                    $lastAmount = $goodAmount + $freight;
                }*/
            }
            // status==1时，表示所有商品都可以选择物流或自提
            // status==2时，表示部分商品可以选择自提或所有商品选择物流
            if ($distributionData['status'] == 1 || $distributionData['status'] == 2) {
                $isDeliveryShow = true;
            }
        } else { //不是自提商品时返回前端判断状态说明 update by 吴晓平 2018年06月28日
            $distributionData = [
                'data' => [],
                'hint' => '',
                'status' => 0
            ];
        }//end
        $noCouponLastAmount = ($noCouponLastAmount - $discountDetail['discount']) > 0 ? ($noCouponLastAmount - $discountDetail['discount']) : 0;
        $returnData['list'] = [
            'shop_name' => $shopName,//店铺名称
            'shop_url' => $shopUrl,//店铺URL
            'userAddress' => $userAddress,//用户地址
            'userCart' => $cartProducts,//购物车商品信息
            'product_total_amount' => sprintf('%.2f', $productTotalAmount),//商品总金额
            'point' => intval($point),//可使用积分
            'bonus_points' => sprintf('%.2f', $bonusPoints),//积分兑换金额
            'last_amount' => sprintf('%.2f', $lastAmount),//总金额
            'freight' => $freight, //运费
            'coupon' => $coupon,//优惠券信息
            'isBind' => $isBind,
            'no_coupon_point' => intval($noCouponPoint),//没有使用优惠券可使用积分
            'no_coupon_bonus_points' => sprintf('%.2f', $noCouponBonusPoints),//没有使用优惠券积分兑换金额
            'is_seckill' => $is_seckill,
            'use_point_amount' => $noCouponLastAmount, //可使用积分的商品金额
            'is_show_point_div' => $isShowPointDiv, //是否显示积分div
            'isDeliveryShow' => $isDeliveryShow, //是否显示选择配送方式导航条
            'distributionData' => $distributionData, //配送方式数据
            'is_wholesale' => $is_wholesale, //是否设置批发价 Herry,
            /**增加返回自提各自的优惠方便前端处理数据 吴晓平**/
            'logicCoupon' => $logicCoupon ?? 0,
            'zitiCoupon' => $zitiCoupon ?? 0,
            'discount' => $discountDetail['discount'] ?? 0,
            'takeAwayConfig' => (new StoreModule())->getWidTakeAway($wid) ? 1 : 0,//返回外卖订单配置
        ];
        return $returnData;
    }

    /**
     * todo 通过积分获取积分兑换的金额[此方法在其他地方被引用]
     * @param $wid
     * @param $point
     * @param $amount
     * @return array
     * @author jonzhang
     * @date 2017-08-18
     */
    public function getAmountByPoint($wid, $point, $amount)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $errMsg = '';
        if (empty($wid)) {
            $errMsg .= 'wid为空';
        }
        if (empty($point)) {
            $errMsg .= 'point为空';
        }
        if (empty($amount)) {
            $errMsg .= 'amount为空';
        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        $bonusPoints = 0.0;
        $usablePoint = 0;
        //获取积分兑换金额的规则
        $pointApplyRuleData = PointApplyRuleService::getRow($wid);
        if ($pointApplyRuleData['errCode'] == 0 && !empty($pointApplyRuleData['data'])) {
            $isON = $pointApplyRuleData['data']['is_on'];
            //开启积分使用
            if ($isON) {
                //何书哲 2018年12月24日 rate为0时，直接返回
                $returnData['data'] = ['point' => intval($usablePoint), 'amount' => sprintf('%.2f', $bonusPoints)];
                $rate = $pointApplyRuleData['data']['rate'];
                if (empty($rate)) {
                    return $returnData;
                }

                $percent = $pointApplyRuleData['data']['percent'];
                //可使用积分
                $percent = $percent / 100 > 1 ? 1 : $percent / 100;
                //积分兑换成可使用的钱
                $usableAmount = $point / $rate;
                $maxAmount = $amount * $percent;
                //积分兑换的钱小于等于可最大抵现金额
                if ($usableAmount <= $maxAmount) {
                    //可抵现金额
                    $bonusPoints = $usableAmount;
                    //可使用积分
                    $usablePoint = $point;
                }//积分兑换的钱大于可最大抵现金额
                else if ($usableAmount > $maxAmount) {
                    //可使用积分
                    $usablePoint = $maxAmount * $rate;
                    //可抵现金额
                    $bonusPoints = intval($usablePoint) / $rate;
                }
                if ($usablePoint < 1) {
                    $bonusPoints = 0.00;
                    $usablePoint = 0;
                }
            } else {
                $returnData['errCode'] = -3;
                $returnData['errMsg'] = '店铺未开启积分功能';
                return $returnData;
            }
            $returnData['data'] = ['point' => intval($usablePoint), 'amount' => sprintf('%.2f', $bonusPoints)];
            return $returnData;
        } else {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '没有配置积分规则';
            return $returnData;
        }
    }

    /**
     * todo 提交订单
     * @param $token
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-18
     * @author 吴晓平 2018年06月26日 修改自提下单运费设置为0
     * @update 张永辉 2018年7月9日 小程序配置信息写入订单
     * @update 何书哲 2018年7月4日 如果是0元&&自提订单,则导入快递管家
     * @update 张永辉 2018年8月20日  满减活动优惠
     * @update 许立   2018年09月17日 下单库存为0不下架
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 梅杰 2018年10月18日 待付款订单提醒
     * @update 何书哲 2018年11月15日 外卖店铺0元订单发送到第三方
     * @update 何书哲 2018年11月16日 添加外卖订单标记
     * @update 何书哲 2018年11月22日 外卖店铺添加订单提交约束
     */
    public function submitOrder($wid, $mid, $data = [], $source = 0, $orderType = 1, $umid = 0, $request = null)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        if (empty($data)) {
            $returnData['code'] = -101;
            $returnData['hint'] = '参数为null';
            return $returnData;
        }
        if (empty($wid) || empty($mid)) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        //购物车id
        $cartId = $data['cartId'];
        //快递方式编号
        $expressNo = $data['expressNo'];
        //优惠券id
        $couponId = intval($data['couponId']) ?? 0;
        //备注
        $remark = $data['remark'] ?? '';
        //是否发送短信
        $isSend = $data['isSend'] ?? 0;
        //使用积分
        $point = $data['point'] ?? 0;
        $point = intval($point);
        $errMsg = '';
        //dd($couponId);
        if (empty($cartId)) {
            $errMsg .= 'cart_id为null';
        }
        if (empty($expressNo)) {
            $errMsg .= 'express_no为null';
        }
        if (strlen($errMsg) > 0) {
            $returnData['code'] = -103;
            $returnData['hint'] = $errMsg;
            return $returnData;
        }

        try {
            $cartId = json_decode($cartId, true);
            if (empty($cartId)) {
                $returnData['code'] = -104;
                $returnData['hint'] = '传递数据不符合要求';
                return $returnData;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $returnData['code'] = -105;
            $returnData['hint'] = $message;
            return $returnData;
        }

        if (count($cartId) == 1) {
            $cartService = new CartService();
            $cartData = $cartService->init()->model->select('id', 'groups_id', 'seckill_id', 'num', 'prop_id')->find($cartId[0]);

            if (empty($cartData)) {
                $returnData['code'] = -104;
                $returnData['hint'] = '购物车不存在';
                return $returnData;
            }

            //add by Herry 创建秒杀订单
            if ($cartData->seckill_id != 0) {
                //秒杀活动是否未开始或结束或失效
                if (!(new SeckillService())->checkValidity($cartData->seckill_id)) {
                    $returnData['code'] = -9;
                    $returnData['hint'] = '活动不在进行中或已失效';
                    return $returnData;
                }

                //用户秒杀限额检查
                $seckillModule = new SeckillModule();
                $limitNum = $seckillModule->isLimited($cartData->seckill_id, $mid, $cartData->num);
                if ($limitNum) {
                    $returnData['code'] = -9;
                    $returnData['hint'] = '该商品每人限购' . $limitNum . '件';
                    return $returnData;
                }

                //秒杀库存检查
                /*if ($seckillModule->canSeckill($cartData->seckill_id, $cartData->prop_id, $cartData->num) === false) {
                    $returnData['code'] = -9;
                    $returnData['hint'] = '秒杀库存不足';
                    return $returnData;
                }

                //redis 库存资格递减$num
                $flagAfterDecr = (new SeckillSku())->decrStockQualification($cartData->seckill_id, $cartData->prop_id, $cartData->num);
                if ($flagAfterDecr < 0) {
                    $returnData['code'] = -9;
                    $returnData['hint'] = '秒杀库存不足, 别人抢先一步 ';
                    return $returnData;
                }*/

                $orderData = $seckillModule->createSeckillOrder($cartId, $request, $wid, $mid, $umid, 1);
                $returnData['list'] = ['orderNo' => $orderData['id'], 'isFree' => 0];
                //dispatch(new CancelNonPaymentSeckillOrder($orderData['id'], $cartData->seckill_id));
                return $returnData;
            }
        }

        //何书哲 2018年11月22日 外卖店铺添加订单提交约束
        $checkRes = (new StoreModule())->checkIfSubmitOrder($wid);
        if ($checkRes['errCode'] != 0) {
            $returnData['errCode'] = -103;
            $returnData['hint'] = $checkRes['errMsg'];
            return $returnData;
        }


        //查询用户默认的收货地址信息
        $address_id = $request->input('address_id', 0);
        $userAddress = OrderService::getDeliveryAddress($umid, $address_id);

        //add by zhangyh 20180116
        if ($data['is_hexiao'] == 0) {
            if (!$userAddress) {
                $returnData['code'] = -19;
                $returnData['hint'] = '请选择正确的收货地址';
                return $returnData;
            }
        }
        //收货地址详细信息
        $address = $userAddress['address'] ?? '';
        $phone = $userAddress['phone'] ?? '';
        $name = $userAddress['name'] ?? '';
        $areaId = $userAddress['address_id'] ?? '';

        //检查用户购买的商品是否存在异常
        $productWhere['mid'] = $mid;
        $productWhere['wid'] = $wid;
        $productWhere['cart_id'] = $cartId;
        $productWhere['address_id'] = $address_id;
        $productResult = OrderService::processOrder($productWhere, $umid);
        //查询条件不符合要求
        if ($productResult['errCode'] != 0) {
            $returnData['code'] = -107;
            $returnData['hint'] = $productResult['errMsg'];
            return $returnData;
        } //购物车中有异常商品
        else if ($productResult['errCode'] == 0 && !empty($productResult['data']['error'])) {
            $returnData['code'] = -108;
            $returnData['hint'] = '待付款订单中有异常商品';
            $returnData['list'] = $productResult['data']['error'];
            return $returnData;
        }//购物车商品添加到订单表中
        else if ($productResult['errCode'] == 0 && empty($productResult['data']['error'])) {
            //生成订单号
            $orderID = OrderCommon::createOrderNumber();
            //有效商品
            $productInfo = $productResult['data']['correct'];

            //订单商品金额
            $productAmount = $productResult['data']['amount'];
            //折扣前的总金额
            $beforeDiscountAmount = $productResult['data']['amount'];
            //折扣后的总金额
            $afterDiscountAmount = $productResult['data']['after_discount_amount'];
            //是否折扣
            $isDiscount = $productResult['data']['is_discount'];
            //减免的运费信息
            $derateFreight = $productResult['data']['derate_freight'];
            if ($isDiscount) {
                $productAmount = $afterDiscountAmount;
            }
            //订单运费
            $freight = $productResult['data']['freight'];
            //自提订单运费为0
            if ($data['is_hexiao'] == 1) {
                $freight = 0;
            }
            //优惠券金额
            $couponSum = 0.00;
            $couponLogService = new CouponLogService();
            if (!empty($couponId)) {
                $couponLogData = $couponLogService->getDetail($couponId);
                if ($couponLogData) {
                    if ($couponLogData['status'] != 0) {
                        $returnData['code'] = -110;
                        $returnData['hint'] = '优惠劵已使用';
                        return $returnData;
                    }
                    $couponSum = $couponLogData['amount'];
                } else {
                    $returnData['code'] = -111;
                    $returnData['hint'] = '优惠劵不存在';
                    return $returnData;
                }
            }
            // add by jonzhang 2017-11-23 商品可使用积分
            //begin
            $usePointAmount = 0.00;
            $isCamiProduct = 0;
            //购物车中的商品信息
            foreach ($productInfo as $productItem) {
                //如果商品打折扣，那商品的价格为折扣价
                if ($isDiscount) {
                    $productItem['price'] = $productItem['after_discount_price'];
                }
                //商品是否使用积分 add by jonzhang
                if ($productItem['is_point']) {
                    $usePointAmount = $usePointAmount + $productItem['price'] * $productItem['num'];
                }
                //卡密商品
                if (isset($productItem['cam_id']) && $productItem['cam_id'] > 0) {
                    $isCamiProduct = 1;
                }
                //满减活动优惠计算
                if ($productItem['after_discount_product_amount'] > 0) {
                    $disPrice = $productItem['after_discount_product_amount'];
                } else {
                    $disPrice = $productItem['product_amount'];
                }
                $discount[] = [
                    'id' => $productItem['product_id'],
                    'price' => $disPrice,
                    'num' => $productItem['num'],
                ];
            }

            $discountDetail = (new DiscountModule())->getDiscountByPids($discount, $wid);
            $usePointAmount = $usePointAmount - $couponSum;
            if ($usePointAmount < 0) {
                $usePointAmount = 0.00;
            }
            //end

            //订单信息
            $createTime = date("Y-m-d H:i:s");
            $order = [];
            $order['oid'] = $orderID;
            $order['trade_id'] = $orderID;
            $order['wid'] = $wid;
            $order['mid'] = $mid;

            $order['is_hexiao'] = $data['is_hexiao'];
            if ($order['is_hexiao'] && $order['is_hexiao'] == 1) { //表示自提订单
                $hexiaoCode = rand(5000, 9999) . rand(1000, 4999) . rand(100, 999); //生成七位提货码
                $order['hexiao_code'] = $hexiaoCode;
            }

            //商品总价
            $order['products_price'] = sprintf('%.2f', $productAmount);
            $order['change_price'] = 0.00;
            $order['source'] = $source;
            if ($source == 1) {
                $order['form_id'] = $data['formId'];
            }
            //订单实际支付金额
            if ($productAmount > $discountDetail['discount']) {
                $productAmount = $productAmount - $discountDetail['discount'];  //优惠金额
            } else {
                $productAmount = 0;
            }


//            //订单实际支付金额
//            $order['pay_price']=$productAmount;

            //优惠券使用
            if ($productAmount < $couponSum) {
                //优惠券金额比本来需要支付金额更大
                $orderCouponPrice = $productAmount;
                $order['coupon_price'] = sprintf('%.2f', $orderCouponPrice);
                $order['pay_price'] = 0.00;
            } else {
                $order['coupon_price'] = $couponSum;
                $payOrderPrice = $productAmount - $couponSum;
                $order['pay_price'] = sprintf('%.2f', $payOrderPrice);
            }

            //积分逻辑 begin
            //可抵现金额
            $bonusPointAmount = 0.00;
            //当前用户拥有的积分
            $myPoint = 0;
            //订单支付金额大于0时才使用积分
            if ($order['pay_price'] > 0 && $usePointAmount > 0 && $point > 0) {
                //是否可使用积分 0表示不可用
                $isUsePoint = 0;
                //$storePointData = WeixinService::selectPointStatus(['id' => $wid]);
                $shopService = new ShopService();
                $storePointData = $shopService->getRowById($wid);
                if (!empty($storePointData)) {
                    $isUsePoint = $storePointData['is_point'];
                }
                if (!$isUsePoint) {
                    $returnData['code'] = -109;
                    $returnData['hint'] = '该店铺没有开启使用积分功能';
                    return $returnData;
                }
                //查询用户拥有的积分
                $memberPointData = MemberService::getRowById($mid);
                //用户积分
                if (!empty($memberPointData)) {
                    $myPoint = $memberPointData['score'];
                }
                if ($myPoint > 0) {
                    //用户使用的积分大于用户拥有的积分
                    if ($point > $myPoint) {
                        $returnData['code'] = -110;
                        $returnData['hint'] = '对不起，你没有那么多积分可使用';
                        return $returnData;
                    }
                    $exchangeData = $this->getAmountByPoint($wid, $point, $usePointAmount);
                    if ($exchangeData['errCode'] == 0 && !empty($exchangeData['data'])) {
                        //此处还可以加一个判断 比较用户传递过来的积分与方法传递过来的积分进行比较
                        $exchangeAmount = $exchangeData['data']['amount'];
                        $bonusPointAmount = $exchangeAmount;
                        //最后的实际支付金额
                        $order['pay_price'] = $order['pay_price'] - $exchangeAmount;
                    } else {
                        $returnData['code'] = $exchangeData['errCode'];
                        $returnData['hint'] = $exchangeData['errMsg'];
                        return $returnData;
                    }

                }//该用户没有积分
                else {
                    $returnData['code'] = -111;
                    $returnData['hint'] = '该用户没有可使用的积分';
                    return $returnData;
                }

            }
            //积分逻辑 end

            //最后实际支付金额要加上运费金额
            $order['pay_price'] = $order['pay_price'] + $freight;
            $order['pay_price'] = sprintf('%.2f', $order['pay_price']);
            $order['cash_fee'] = 0.00;
            $order['bonus_point_amount'] = sprintf('%.2f', $bonusPointAmount);

            //dd($order['pay_price']);
            //运费金额
            $order['freight_price'] = $freight;
            //物流方式 1快递发货
            $order['express_type'] = $expressNo;
            $order['address_id'] = $areaId;
            $order['address_name'] = $name;
            $order['address_phone'] = $phone;
            //详细地址
            $order['address_detail'] = $address;

            //Herry 保存收货地址城市名 打印快递单使用
            $order['address_province'] = $userAddress['province'] ?? '';
            $order['address_city'] = $userAddress['city'] ?? '';
            $order['address_area'] = $userAddress['area'] ?? '';


            // 张永辉 2018年7月9日 写入小程序配置id
            $token = $request->input('token', '0');
            $order['xcx_config_id'] = (new CommonModule())->getXcxConfigIdByToken($token);
            //end
            //普通订单
            $order['type'] = $orderType;
            //积分抵现订单
            if ($order['bonus_point_amount'] > 0) {
                $order['type'] = 5;
            }
            //支付方式 0未支付
            $order['pay_way'] = 0;
            //买家备注
            $order['buy_remark'] = $remark;
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
            $order['derate_freight'] = sprintf('%.2f', $derateFreight);
            //使用的积分
            $order['use_point'] = $point;
            //优惠券id
            $order['coupon_id'] = $couponId;
            $order['card_discount'] = 0;
            if ($isDiscount) {
                //订单折扣金额
                $discountAmount = $beforeDiscountAmount - $afterDiscountAmount;
                $order['discount_amount'] = sprintf('%.2f', $discountAmount);
                $order['card_discount'] = 1;
            }
            //是否分销订单
            $order['distribute_type'] = 0;
            //满减活动优惠金额
            $order['discount'] = $discountDetail['discount'];
            $order['discount_ids'] = implode(',', array_column($discountDetail['discountDetail'], 'id'));
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
                $order['pay_way'] = 1;//微信支付[此处为微信支付有点不恰当]
                /*拉黑客户0元订单的处理 add by wuxiaoping 2018.05.17*/
                $memberInfo = MemberService::getRowById($mid);
                if (isset($memberInfo['is_pull_black']) && $memberInfo['is_pull_black']) {
                    $order['status'] = 0;//未付款
                    $order['pay_way'] = 0;
                }//end
            }

            //add by 张国军 2018年08月07日 卡密商品没有收获地址。
            if ($isCamiProduct) {
                $order['freight_price'] = 0;
                $order['express_type'] = 1;
                $order['address_detail'] = '';
                $order['address_province'] = '';
                $order['address_city'] = '';
                $order['address_area'] = '';
                $order['type'] = 12;
            }
            $order['is_takeaway'] = (new StoreModule())->getWidTakeAway($wid) ? 1 : 0;//何书哲 2018年11月16日 添加外卖订单标记

            //订单明细
            $orderDetails = [];
            foreach ($productInfo as $item) {
                $orderDetail = [];
                //计算单个商品满减活动
                $orderDetail['discount'] = 0;
                $orderDetail['discount_detail'] = '';
                foreach ($discountDetail['discountDetail'] as $val) {
                    if (in_array($item['product_id'], $val['discountPids'])) {
                        $orderDetail['discount'] = bcmul($val['discount'], $item['product_amount'] / $val['amount'], 2);
                        $orderDetail['discount_detail'] = $val['title'];
                        break;
                    }
                }


                $orderDetail['oid'] = $orderID;
                $orderDetail['product_id'] = $item['product_id'];
                $orderDetail['title'] = $item['product_name'];
                $orderDetail['img'] = $item['img_url'];
                $orderDetail['price'] = $item['price'];
                //折扣的商品单价
                $orderDetail['after_discount_price'] = bcsub($item['after_discount_price'], $orderDetail['discount'], 2);
                if ($orderDetail['after_discount_price'] < 0) {
                    $orderDetail['after_discount_price'] = 0;
                }

                $orderDetail['oprice'] = $item['old_price'];
                $orderDetail['num'] = $item['num'];
                $orderDetail['spec'] = $item['attr'];
                $orderDetail['is_attr'] = $item['is_attr'];
                //商品有规格product_prop_id为product_prop表中的id 否则为o
                if ($item['is_attr'] == 1) {
                    $orderDetail['product_prop_id'] = $item['product_prop_id'];
                } else {
                    $orderDetail['product_prop_id'] = 0;
                }
                //缓存redis使用
                $orderDetail['is_evaluate'] = 0;
                $orderDetail['created_at'] = $createTime;
                $orderDetails[] = $orderDetail;
            }
            \Log::info('订单满减优惠详情订单号：:' . $order['oid']);
            \Log::info($discountDetail);
            //事务
            DB::beginTransaction();
            try {
                //添加订单信息
                $orderReturn = OrderService::init()->addD($order, false);
                if (!$orderReturn) {
                    throw new \Exception('创建订单失败');
                }

                /*如果是自提订单，创建订单自提信息 wuxiaoping 2018.06.05*/
                if ($order['is_hexiao'] && $order['is_hexiao'] == 1) {
                    $zitiData['wid'] = $wid;
                    $zitiData['mid'] = $mid;
                    $zitiData['oid'] = $orderReturn;
                    $zitiData['ziti_id'] = $data['ziti']['ziti_id'];
                    $zitiData['ziti_contact'] = $data['ziti']['ziti_contact'];
                    $zitiData['ziti_phone'] = $data['ziti']['ziti_phone'];
                    $zitiData['ziti_datetime'] = $data['ziti']['ziti_datetime'];
                    (new OrderZitiService())->add($zitiData);
                }
                //添加订单详情
                foreach ($orderDetails as $k => $item) {
                    $item['oid'] = $orderReturn;

                    //redis的oid字段一直保存错误 Herry 20180119
                    $orderDetails[$k] = $item;

                    //清除创建订单明细不需要的字段
                    unset($item['is_attr']);
                    $orderDetailReturn = OrderDetailService::init()->addD($item, false);

                    if (!$orderDetailReturn) {
                        throw new \Exception('添加订单明细失败');
                    }
                }
                //更改商品库存
                foreach ($orderDetails as $item) {
                    $num = $item['num'];
                    if ($item['is_attr'] == 1) {
                        //更改有规格商品 数据库库存
                        $propList = (new ProductPropsToValuesService())->getSkuList($item['product_id']);
                        if (!empty($propList['stocks'])) {
                            $productSkuService = new ProductSkuService();
                            //更改商品表中的总库存和规格信息
                            foreach ($propList['stocks'] as $propItem) {
                                if ($propItem['id'] == $item['product_prop_id']) {
                                    $propStock = $propItem['stock_num'] - $num;
                                    if ($propStock < 0) {
                                        throw new \Exception('库存不足');
                                    }
                                    $productSkuService->update($propItem['id'], ['stock' => $propStock, 'sold_num' => $propItem['sold_num'] + $num]);
                                    break;
                                }
                            }
                        }
                    }
                    //此处查询出来的信息为商品的总库存，如何商品无规格，则减去为无规格商品的库存，如果商品有规格，则从总库中减去购买数量。
                    $productInfo = ProductService::getDetail($item['product_id']);
                    if (!empty($productInfo)) {
                        //库存
                        $productStock = $productInfo['stock'] - $num;
                        $stockData = ['stock' => $productStock, 'sold_num' => $productInfo['sold_num'] + $num];
                        if ($productStock < 0) {
                            throw new \Exception('库存不足');
                        }
                        //库存为0，商品更改为下架
                        /*if($productStock==0)
                        {
                            $stockData['status']=0;
                        }*/

                        ProductService::update($item['product_id'], $stockData);
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();//事务回滚
                //echo $e->getCode();
                $message = $e->getMessage();
                $returnData['code'] = -1000;
                //$returnData['errMsg']='下单失败';
                $returnData['hint'] = $message;
                //Log::error('下单失败,原因为：'.$message);
                return $returnData;
            }
            //更改优惠券的状态
            $couponId && $couponLogService->update($couponId, ['status' => 1, 'oid' => $orderReturn]);

            //订单流水
            $orderLog = [];
            $orderLog['oid'] = $orderReturn;
            $orderLog['wid'] = $wid;
            $orderLog['mid'] = $mid;
            $orderLog['action'] = 1;
            $orderLog['created_at'] = $createTime;
            //添加订单流水
            OrderLogService::init()->addD($orderLog, false);
            $order['id'] = $orderReturn;
            $order['orderDetail'] = $orderDetails;
            //redis订单日志字段 存二维数组
            $order['orderLog'] = [$orderLog];
            //dd($order);
            //订单信息插入到缓存
            OrderService::init('mid', $mid)->addR($order, false);
            OrderService::init('wid', $wid)->addR($order, false);
            //清除购物车
            $cartService = new CartService();
            foreach ($cartId as $id) {
                $cartService->init('mid', $mid)->where(['id' => $id])->delete($id, false);
            }

            //积分订单去掉使用的积分
            if ($point > 0 && $myPoint > 0) {
                //point_type为4表示积分抵现
                $pointRecordData = ['wid' => $wid, 'mid' => $mid, 'point_type' => 4, 'is_add' => 0, 'score' => $point];
                //消费积分记录
                PointRecordService::insertData($pointRecordData);
                //更改用户积分
                MemberService::incrementScore($mid, '-' . $point);
            }
            //梅杰 2018年10月18日 订单待付款
            $source == 1 && (new MessagePushModule($wid, MessagesPushService::TradeUrge, MessagePushModule::SEND_TARGET_WECHAT_XCX))->setDelay(60)->sendMsg($orderReturn, $order['xcx_config_id']);
            $source == 0 && (new MessagePushModule($wid, MessagesPushService::TradeUrge))->setDelay(60)->sendMsg($orderReturn);

            //何书哲 2018年7月4日 如果是小程序&&0元&&自提订单,则导入快递管家
            if ($source == 1 && isset($order['pay_price']) && $order['pay_price'] == 0 && isset($order['is_hexiao']) && $order['is_hexiao'] != 1) {
                dispatch((new ImportOrderLogistics($wid, $orderReturn))->onQueue('ImportOrderLogistics'));
            }

            //何书哲 2018年11月15日 外卖店铺0元订单发送到第三方
            $isFree && dispatch(new SendTakeAway($orderReturn));

            $returnData['list'] = ['orderNo' => $orderReturn, 'isFree' => $isFree];
            return $returnData;
        }
        //其他
        $returnData['code'] = -1001;
        $returnData['hint'] = '没有找到对应的逻辑判断';
        return $returnData;
    }

    /**
     * todo 获取订单流水
     * @param $orderNo
     * @return array
     * @date 2017-09-05
     */
    public function getOrderTrackInfo($orderNo)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $logisticsService = new LogisticsService();
        $res = $logisticsService->getLogistics($orderNo);
        if ($res['success'] == 1) {
            $returnData['list'] = $res['data'];
        } else {
            $returnData['code'] = -103;
            $returnData['hint'] = $res['message'];
        }
        return $returnData;
    }

    /***
     * @param Request $request
     * @return array
     * @date 2017-09-05
     */
    public function delay($wid, $mid, $orderNo)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '操作成功', 'list' => []];
        $orderData = OrderService::init()->model->find($orderNo)->load('orderLog')->toArray();
        if ($orderData['mid'] != $mid) {
            $returnData['code'] = -103;
            $returnData['hint'] = '操作非法';
            return $returnData;
        }
        //判断该订单是否可以取消退款
        if ($orderData['status'] != 2) {
            $returnData['code'] = -104;
            $returnData['hint'] = '该订单暂时无法确认收货';
            return $returnData;
        }
        //判断是否可以延期
        foreach ($orderData['orderLog'] as $val) {
            if ($val['action'] == 13) {
                $returnData['code'] = -105;
                $returnData['hint'] = '亲！你已申请过延期了！';
                return $returnData;
            } elseif ($val['action'] == 3) {
                $day = (int)((time() - strtotime($val['created_at'])) / 86400);
                $autoReceiveDays = config('app.auto_confirm_receive_days');
                if (($autoReceiveDays - 3) > $day) {
                    $returnData['code'] = -106;
                    $returnData['hint'] = '亲！发货后' . ($autoReceiveDays - 3) . '天后才能延期收货哦';
                    return $returnData;
                }
            }
        }
        $orderLog = [
            'oid' => $orderNo,
            'wid' => $wid,
            'mid' => $mid,
            'action' => 13,
            'remark' => '买家申请延期',
        ];
        $result = OrderLogService::init('wid', $wid)->add($orderLog, false);
        OrderService::upOrderLog($orderNo, $wid);
        if (!$result) {
            $returnData['code'] = -107;
            $returnData['hint'] = '延迟收货失败';
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 获取订单评论列表
     * @param $orderNo
     * @date 2017-09-05
     */
    public function getCommentList($oid)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $oid = intval($oid);
        if (empty($oid)) {
            $returnData['code'] = -101;
            $returnData['hint'] = '订单id错误';
            return $returnData;
        }
        $orderData = OrderService::init()->model->find($oid)->load('orderDetail');

        if (empty($orderData)) {
            $returnData['code'] = -101;
            $returnData['hint'] = '订单不存在';
            return $returnData;
        } else {
            $orderData = $orderData->toArray();
        }

        $refundService = new OrderRefundService();
        foreach ($orderData['orderDetail'] as $k => &$val) {
            if ($val['is_evaluate'] == 1) {
                $val['evaluate'] = ProductEvaluateService::init()->model->where(['odid' => $val['id']])->first();
                $val['evaluate'] = $val['evaluate'] ? $val['evaluate']->toArray() : [];
            }

            //订单中每个商品的退款状态 Herry
            $refund = $refundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $val['product_id'], 'prop_id' => $val['product_prop_id']])->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                //在多商品订单中，已完成退款的商品不能评价 Herry
                unset($orderData['orderDetail'][$k]);
            }
        }
        $returnData['list'] = $orderData;
        return $returnData;
    }

    /**
     * todo 添加订单评论
     * @param $data
     * @return array
     * @date 2017-09-05
     */
    public function insertOrderComment($wid, $mid, $data)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $odid = $data['odid'];
        $orderDetailData = OrderDetailService::init()->model->find($odid)->toArray();
        //判断该订单是否可以评价
        if ($orderDetailData) {
            if ($orderDetailData['is_evaluate']) {
                $returnData['code'] = -104;
                $returnData['hint'] = '操作非法';
                return $returnData;
            }
        } else {
            $returnData['code'] = -105;
            $returnData['hint'] = '订单不存在';
            return $returnData;
        }

        $evaluate = [
            'wid' => $wid,
            'mid' => $mid,
            'oid' => $orderDetailData['oid'],
            'odid' => $orderDetailData['id'],
            'pid' => $orderDetailData['product_id'],
            'content' => $data['content'],
            'img' => (isset($data['img']) && !empty($data['img'])) ? implode(',', $data['img']) : '',
            'status' => $data['status'],
            'depict' => $data['depict'],
            'service' => $data['service'],
            'speed' => $data['speed'],
            'is_hiden' => (isset($data['is_hiden']) && !empty($data['is_hiden'])) ? $data['is_hiden'] : 0,
        ];

        $id = ProductEvaluateService::init('wid', $wid)->add($evaluate, false);
        if ($id) {
            $orderLog = [
                'oid' => $orderDetailData['oid'],
                'wid' => $wid,
                'mid' => $mid,
                'action' => 5,
                'remark' => '订单评价',
            ];
            OrderLogService::init('wid', $wid)->add($orderLog, false);
            OrderService::upOrderLog($orderDetailData['oid'], $wid);
            OrderDetailService::init()->where(['id' => $data['odid']])->update(['is_evaluate' => 1], false);
            //add fuguowei 定单表中添加数据,用来确认订单是否评价
            /*$re  =OrderDetailService::init()->model->where(['id'=>$data['odid']])->first();
            if($re)
            {
                $re = $re->toArray();
                $res =OrderDetailService::init()->model->select(['id','is_evaluate'])->wheres(['oid'=>$re['oid']])->get();
                if($res)
                {
                    $res = $res->toArray();
                    $result = count($res);
                    $countt = 0;
                    foreach($res as $v)
                    {
                        if($v['is_evaluate'] != 0){
                            $countt++;
                        }
                    }
                    if($countt == $result){
                        OrderService::init()->model->where(['id'=>$re['oid']])->update(['ievaluate'=>1],false);
                    }
                }
            }*/

            //检查订单评价状态 Herry 20180124
            OrderService::checkEvaluate($orderDetailData['oid'], $wid);

            //end
            $evaluate['id'] = $id;
            $returnData['list'] = $evaluate;
            return $returnData;
        }
        $returnData['code'] = -106;
        $returnData['hint'] = '订单评论失败';
        return $returnData;
    }


    /**
     * todo 统计用户在某个店铺下的订单数据
     * @param $wid
     * @param $mid
     * @return array
     * @date 2017-09-05
     * @updated 梅杰 20180704 增加默认logo
     * @update 何书哲 2018年11月21日 增加是否外卖店铺
     */
    public function statOrderData($wid, $mid)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $waitPay = 0;
        $waitSend = 0;
        $waitReceive = 0;
        $finish = 0;
        $stayGroup = 0;
        $where = ['wid' => $wid, 'mid' => $mid];
        if (config('app.del_wid') == $wid) {
            $where['admin_del'] = 0;
        }
        $orderStatusInfo = OrderService::getOrderData($where);
        foreach ($orderStatusInfo as $item) {
            if ($item['status'] == 0) {
                $waitPay = $item['number'];
            } else if ($item['status'] == 1) {
                $waitSend = $item['number'];
            } else if ($item['status'] == 2) {
                $waitReceive = $item['number'];
            } else if ($item['status'] == 3) {
                $finish = OrderService::finishStatus($wid, $mid);
            }
        }
        $stayGroup = OrderService::getStayGroupOrder($mid);
        $waitSend = $waitSend - $stayGroup;
        /*add wuxiaoping 2018.01.19 多返回店铺信息（公众号二维码）*/
        $weixin = new Weixin;
        $weixinInfo = $weixin->getStageShop($wid);
        $weixinInfo['logo'] = $weixinInfo['logo'] ? $weixinInfo['logo'] : '/home/image/huisouyun_120.png';
        $memberInfo = (new WeixinRoleService())->getShopPermission();
        $member = Member::find($mid)->toArray();
        //对财富眼和二维码的操作
        if ($weixinInfo['is_distribute'] == 0) {
            $eyeCode = 0;
        } else {
            if ($weixinInfo['distribute_grade'] == 0 || ($weixinInfo['distribute_grade'] == 1 && $member['is_distribute'] == 1)) {
                $eyeCode = 1;
            } else {
                $eyeCode = 0;
            }
        }
        $returnData['list'] = [
            'waitPay' => $waitPay,
            'waitSend' => $waitSend,
            'waitReceive' => $waitReceive,
            'finish' => $finish,
            'stayGroup' => $stayGroup,
            'weixinInfo' => $weixinInfo,
            'eyeCode' => $eyeCode,
            'takeAwayConfig' => (new StoreModule())->getWidTakeAway($wid) ? 1 : 0
        ];
        return $returnData;
    }


    /**
     * todo 确认收货
     * @param Request $request
     * @return array
     * @date 2017-09-05
     */
    public function receive($wid, $mid, $orderNo)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $orderData = OrderService::init('wid', $wid)->where(['id' => $orderNo])->getInfo($orderNo);
        if ($orderData['mid'] != $mid) {
            $returnData['code'] = -103;
            $returnData['hint'] = '操作非法';
            return $returnData;
        }
        //判断该订单是否可以取消退款
        if ($orderData['status'] != 2) {
            $returnData['code'] = -104;
            $returnData['hint'] = '该订单暂时无法确认收货';
            return $returnData;
        }
        $res = OrderService::init('wid', $wid)->where(['id' => $orderNo])->update(['id' => $orderNo, 'status' => 3], false);
        if ($res) {
            //确认收货分销佣金到账
//            $res = OrderService::getMoney($orderData['id']);
//            if ($res['success'] == 0){
//                $returnData['code']=-105;
//                $returnData['hint']=$res['message'];
//                return $returnData;
//            }
            //确认收货后赠送积分 add by jonzhang 2017-05-31
            //订单金额大于0,进行赠送积分
            $point = 0;
            if ($orderData['pay_price'] > 0) {
                //当店铺开启订单赠送积分
                $orderPointData = OrderPointRuleService::getRowByCondition(['wid' => $wid, 'is_on' => 1]);
                if ($orderPointData['errCode'] == 0 && !empty($orderPointData['data'])) {
                    //积分规则id
                    $id = $orderPointData['data']['id'];
                    //订单对应的积分
                    $orderPoint = intval($orderData['pay_price'] * $orderPointData['data']['basic_rule'] / 100);

                    $whereData = [];
                    $whereData['p_id'] = $id;
                    $whereData['used_money'] = ['<=', $orderData['pay_price']];
                    //查询订单积分对应的额外规则
                    $orderExtraRuleData = OrderPointExtraRuleService::getListByConditionWithPage($whereData, 'used_money', 'desc');

                    $orderExtraPoint = 0;
                    if ($orderExtraRuleData['errCode'] == 0 && !empty($orderExtraRuleData['data'])) {
                        //查询该订单对应的金额 额外积分
                        $orderExtraPoint = $orderExtraRuleData['data'][0]['reward_point'];
                    }
                    // 该订单总共积分
                    $point = intval($orderPoint + $orderExtraPoint);
                    if ($point > 0) {
                        $pointRecordData = ['wid' => $wid, 'mid' => $mid, 'point_type' => 1, 'is_add' => 1, 'score' => $point];
                        //消费积分记录
                        PointRecordService::insertData($pointRecordData);
                        //查询改用户当前积分
                        MemberService::incrementScore($mid, $point);
                    }
                }
            }

            //确认收货 如果有退款 关闭退款 Herry 20171101
            (new RefundModule())->closeAfterReceive($orderNo);

            $orderLogData = [
                'oid' => $orderData['id'],
                'wid' => $orderData['wid'],
                'mid' => $mid,
                'action' => 4,
                'remark' => '买家确认收货',
            ];
            OrderLogService::init()->add($orderLogData, false);
            OrderService::upOrderLog($orderData['id'], $orderData['wid']);
            if ($point > 0)
                $info = '收货成功，获得' . $point . '积分';
            else
                $info = '收货成功';
            $returnData['hint'] = $info;
            return $returnData;
        }
    }

    /**
     * todo 取消订单
     * @param $wid
     * @param $mid
     * @param $orderNo
     * @return array
     * @date 2017-09-05
     * @modify author 张国军 2018年08月07日 已经付过款的虚拟订单，不能够取消订单
     */
    public function cancelOrder($wid, $mid, $orderNo)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $orderData = OrderService::init()->model->find($orderNo)->load('orderLog')->toArray();
        if ($orderData['mid'] != $mid) {
            $returnData['code'] = -103;
            $returnData['hint'] = '非法操作';
            return $returnData;
        }

        //add by 张国军 2018年08月06日 付过款的虚拟订单不能够取消
        if (isset($orderData['type']) && isset($orderData['status']) && $orderData['type'] == 12 && ($orderData['status'] == 1 || $orderData['status'] == 2 || $orderData['status'] == 3)) {
            $returnData['code'] = -105;
            $returnData['hint'] = '已付过款的虚拟订单不能够取消';
            return $returnData;
        }


        //判断该订单是否可以取消退款
        if ($orderData['status'] != 0) {
            $returnData['code'] = -104;
            $returnData['hint'] = '该订单买家已付款，暂时无法取消';
            return $returnData;
        }

        //退还积分
        if ($orderData['use_point'] > 0) {
            $point = $orderData['use_point'];
            $pointRecordData = [
                'wid' => $wid,
                'mid' => $mid,
                'point_type' => 6,
                'is_add' => 1,
                'score' => $point
            ];

            PointRecordService::insertData($pointRecordData);
            MemberService::incrementScore($mid, $point);
        }

        //退还优惠券
        $orderData['coupon_id'] && (new CouponLogService())->update($orderData['coupon_id'], ['status' => 0, 'oid' => 0]);

        //修改订单状态
        OrderService::init('wid', $wid)->where(['id' => $orderNo])->update(['status' => 4], false);

        list($orderDetails) = OrderDetailService::init()->where(['oid' => $orderNo])->getList(false);
        foreach ($orderDetails['data'] as $item) {
            $num = $item['num'];
            if (!empty($item['product_prop_id'])) {
                //更改有规格商品 数据库库存
                ProductSku::where('id', $item['product_prop_id'])->increment('stock', $num);
                (new SkuRedis())->incr($item['product_prop_id'], 'stock', $num);
            }

            //更新商品
            Product::where('id', $item['product_id'])->increment('stock', $num);
            (new ProductRedis())->incr($item['product_id'], 'stock', $num);
        }

        $orderLog = [
            'oid' => $orderNo,
            'wid' => $wid,
            'mid' => $mid,
            'action' => 6,
            'remark' => '买家取消订单',
        ];
        OrderLogService::init('wid', $wid)->add($orderLog, false);
        OrderService::upOrderLog($orderNo, $wid);

        //如果是秒杀订单 需要返还秒杀库存 Herry
        if ($orderData['seckill_id']) {
            (new SeckillModule())->returnSeckillStock($orderData['id'], $orderData['seckill_id']);
        }

        return $returnData;
    }

    /**
     * todo 订单详情
     * @param $wid
     * @param $mid
     * @param $orderNo
     * @return array
     * @date 2017-09-05
     * @modify author  张国军 虚拟订单显示卡密信息 2018年08月07日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月8日 新需求：确认收货7天后不能申请退款
     * @update 何书哲 2018年11月16日 返回店铺外卖配置及订单发货时间
     */
    public function showOrderDetail($wid, $mid, $orderNo)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $orderDetail = OrderService::orderDetail($wid, $orderNo);
        if (empty($orderDetail)) {
            $returnData['code'] = -104;
            $returnData['hint'] = '订单异常';
            return $returnData;
        }
        //add meiJay
        if ($orderDetail['zan_id'] != 0) {
            $re = (new LiEventService())->getOne($orderDetail['zan_id'], $wid);
            $orderDetail['zan_price'] = ($re['lower_price'] / 100);
        }
        /*自提订单增加返回用户自提信息 add by wuxiaoping 2018.06.11*/
        if ($orderDetail['is_hexiao'] && $orderDetail['is_hexiao'] == 1) {
            $where['oid'] = $orderDetail['id'];
            $where['wid'] = $wid;
            $where['mid'] = $mid;
            $zitiData = (new OrderZitiService())->getDataByCondition($where);
            $temp = [$zitiData['orderZiti']['province_id'], $zitiData['orderZiti']['city_id'], $zitiData['orderZiti']['area_id']];
            $regionService = new RegionService();
            $region = $regionService->getListById($temp);
            $tmpAddr = [];
            foreach ($region as $val) {
                $tmpAddr[$val['id']] = $val['title'];
            }
            $zitiData['orderZiti']['province_title'] = $tmpAddr[$zitiData['orderZiti']['province_id']];
            $zitiData['orderZiti']['city_title'] = $tmpAddr[$zitiData['orderZiti']['city_id']];
            $zitiData['orderZiti']['area_title'] = $tmpAddr[$zitiData['orderZiti']['area_id']];
            $orderDetail['ziti'] = $zitiData;
        }

        if ($orderDetail['groups_id'] != 0) {
            /*获取是否开启抽奖团 add by wuxiaoping 2017.11.16*/
            $groupData = (new GroupsService())->getRowById($orderDetail['groups_id']);
            if ($groupData) {
                $groupsRuleData = (new GroupsRuleService())->getRowById($groupData['rule_id']);
                $orderDetail['is_open_draw'] = $groupsRuleData['is_open_draw'] ?? 0;
                if ($orderDetail['is_open_draw'] == 1) {
                    if ($groupData['status'] == 3) {
                        $orderDetail['is_open_draw'] = 0;
                    }
                }
            }
            $orderDetail['groupsProductPrice'] = $orderDetail['products_price'];
        }

        //计算商品总价
        $productPrice = 0;
        $evaluate = 1;
        foreach ($orderDetail['orderDetail'] as $value) {
            $productPrice += $value['price'] * $value['num'];
            if ($value['is_evaluate'] == 0) {
                $evaluate = 0;
            }
        }
        $orderDetail['productPrice'] = $productPrice;
        //获取店铺名称、logo
        //$storeData = WeixinService::getStageShop($wid);
        $shopService = new ShopService();
        $storeData = $shopService->getRowById($wid);

        $orderDetail['shop_name'] = $storeData['shop_name'] ?? '';
        $orderDetail['shop_logo'] = $storeData['logo'] ?? '';
        $orderDetail['order_create_time'] = strtotime($orderDetail['created_at']);  //订单生成时间，主要用于倒计时，传递时间戳格式

        //秒杀订单 获取超时未支付时间
        $orderDetail['seckill_expire_seconds'] = 3600;
        if ($orderDetail['type'] == 7) {
            $seckill = (new SeckillService())->getDetail($orderDetail['seckill_id']);
            /*if (empty($seckill)) {
                error('秒杀活动不存在');
            }*/
            //秒杀活动如果被删除 则默认一小时取消订单 Herry
            $orderDetail['seckill_expire_seconds'] = !empty($seckill) ? $seckill['cancel_minutes'] * 60 : 3600;
        }

        //退款信息
        $refundService = new OrderRefundService();
        foreach ($orderDetail['orderDetail'] as &$detail) {
            $refund = $refundService->init('oid', $orderDetail['id'])->where(['oid' => $orderDetail['id'], 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
            $detail['refund_status'] = 0;
            if ($refund) {
                $detail['refund_status'] = $refund['status'];
            }
        }
        $orderDetail['evaluate'] = $evaluate;
        $orderDetail['deliver'] = $this->getDeliverTime($orderDetail['id']);
        $orderDetail['nowtime'] = '';
        if ($orderDetail['deliver']) {
            $orderDetail['nowtime'] = (strtotime($orderDetail['deliver']) + 15 * 86400) - time();
        }

        //虚拟订单 卡密信息 add by 张国军 2018年08月07日
        $orderDetail['carmName'] = [];
        $orderDetail['carmAttr'] = [];
        $orderDetail['userInstruction'] = "";
        if (isset($orderDetail['type']) && $orderDetail['type'] == 12 && !empty($orderDetail['id'])) {
            $camListService = new CamListService();
            //查询卡密信息
            $camListData = $camListService->getAllList(['oid' => $orderDetail['id']], 'id', false);
            if (!empty($camListData) && count($camListData) > 0) {
                $cnt = 0;
                foreach ($camListData as $item) {
                    $orderDetail['carmName'][] = $item['name'] ?? "";
                    $orderDetail['carmAttr'][] = $item['attr'] ?? "";
                    $id = $item['id'] ?? 0;
                    $carmId = $item['cam_id'] ?? 0;
                    if (!empty($carmId) && !$cnt) {
                        $camActivityData = (new CamActivityService())->getRowById($carmId);
                        if (!empty($camActivityData['remark'])) {
                            $orderDetail['userInstruction'] = $camActivityData['remark'];
                        }
                    }
                    $cnt++;
                    //更改卡密的使用时间
                    //if (!empty($id)) {
                    //    $camListService->update($id, ['use_time' => date('Y-m-d H:i:s', time())]);
                    //}
                }
            }
        }

        $returnData['list']['configData'] = (new StoreModule())->getDeliveryConfig(['wid' => $wid, 'is_on' => 1]);
        $returnData['list']['orderDetail'] = $orderDetail;
        $returnData['list']['coupon'] = (new CouponLogService())->getRowByOid($orderNo);
        //确认收货7天后 不能申请或修改退款 Herry
        $returnData['list']['orderDetail']['canRefund'] = 1;

        $logs = OrderLogService::init()->model->where(['oid' => $orderNo, 'action' => 4])->get()->toArray();
        if ($logs) {
            $receiveTime = $logs[0]['created_at'];
            if (strtotime($receiveTime) + 7 * 24 * 3600 < time()) {
                $returnData['list']['orderDetail']['canRefund'] = 0;
            }
        }

        //新增返回余额
        $memberInfo = (new \App\S\Member\MemberService())->getRowById($mid);
        $returnData['balance'] = $memberInfo ? $memberInfo['money'] : 0;

        return $returnData;
    }

    /**
     * 我的订单列表
     * @param $wid int 店铺ID
     * @param $mid int 用户ID
     * @param status int 订单状态
     * @return json
     * @update 陈文豪 2018年6月29日 增加返回用户余额
     * @update 何书哲 2018年11月21日 返回店铺是否是外卖店铺
     */
    public function showAllOrders($wid, $mid, $status, $page = 1)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $where = [
            'mid' => $mid,
            'wid' => $wid
        ];
        if (config('app.del_wid') == $wid) {
            $where['admin_del'] = 0;
        }
        if (isset($status)) {
            if ($status == -1) {
                $oids = (new GroupsDetailService())->getOrder($mid);
                $where['id'] = ['in', $oids];
            } elseif ($status == 1) {
                $where['status'] = $status;
                $where['groups_status'] = ['<>', 1];
            } else {
                $where['status'] = $status;
            }

            //退款成功 只显示在全部订单里
            //全部商品退款完成会关闭订单（status=4）；订单中部分退款完成refund_status也会等于8，但是订单状态不变 20180124
            /*$where['refund_status'] = [
                'not in',
                [4, 8]
            ];*/
        }
        $pagesize = config('database.perPage');
        $offset = ($page - 1) * $pagesize;
        //xiugai  fuguowei 20171219
        if ($status && $status == 3) {
            $where['status'] = $status;
            $where['ievaluate'] = 0;
            $orderData = OrderService::init('wid', $wid)->model->wheres($where)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->load('orderDetail')->load('weixin')->load('orderLog')->toArray();

        } else {
            $orderData = OrderService::init('wid', $wid)->model->wheres($where)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->load('orderDetail')->load('weixin')->load('orderLog')->toArray();
        }
        //end
        foreach ($orderData as &$val) {
            //add MayJay
            $val['no_express'] = 0;
            $re = (new LogisticsService())->init()->where(['oid' => $val['id']])->getInfo();
            if ($re) {
                $val['no_express'] = $re['no_express'];
            }
            //end
            $val['count'] = count($val['orderDetail']);
            $val['evaluate'] = 1;
            foreach ($val['orderDetail'] as $v) {
                if ($v['is_evaluate'] == 0) {
                    $val['evaluate'] = 0;
                    break;
                }
            }

            //团购订单添加分享信息
            if ($val['groups_id'] != 0) {
                /*获取拼团规则数据 add by wuxiaoping 2017.11.16*/
                $ruleData = (new GroupsService())->getRowById($val['groups_id']);
                if ($ruleData) {
                    $groupsRuleData = (new GroupsRuleService())->getRowById($ruleData['rule_id']);
                    $val['is_open_draw'] = $groupsRuleData['is_open_draw'] ?? 0;
                    if ($val['is_open_draw'] == 1) {   //开启抽奖时，如果未满足成团人数与普通拼团流程一致（未成团）
                        if ($ruleData['status'] == 3) {
                            $val['is_open_draw'] = 0;
                        }
                    }
                }
                $val['shareData'] = (new GroupsRuleModule())->getShareData($val['groups_id']);
            }
        }
        $returnData['list'] = $orderData;

        //新增返回余额
        $memberInfo = (new \App\S\Member\MemberService())->getRowById($mid);
        $returnData['balance'] = $memberInfo ? $memberInfo['money'] : 0;
        //返回是否是外卖店铺
        $returnData['takeAwayConfig'] = (new StoreModule())->getWidTakeAway($wid) ? 1 : 0;
        return $returnData;
    }


    public function cancelTimeoutNonPaymentCommonOrder($commonOrderId)
    {
        $orderInfo = Order::find($commonOrderId);
        if (is_null($orderInfo) || $orderInfo->status != 0) {
            return;
        }
        $now = time();
        if ($now - strtotime($orderInfo->created_at) >= static::TIMEOUT_ORDER * 60) {
            $wid = $orderInfo->wid;
            $mid = $orderInfo->mid;
            $use_point = $orderInfo->use_point;
            $coupon_id = $orderInfo->coupon_id;
            DB::beginTransaction();
            try {
                //退还积分
                if ($use_point > 0) {
                    $point = $use_point;
                    $pointRecordData = [
                        'wid' => $wid,
                        'mid' => $mid,
                        'point_type' => 6,
                        'is_add' => 1,
                        'score' => $point
                    ];

                    PointRecordService::insertData($pointRecordData);
                    (new memberService())->incrementScore($mid, $point);
                }

                //退还优惠券
                $coupon_id && (new CouponLogService())->update($coupon_id, ['status' => 0, 'oid' => 0]);

                OrderService::init('wid', $orderInfo->wid)
                    ->where(['id' => $orderInfo->id])
                    ->update(['status' => 4], false);

                list($orderDetails) = OrderDetailService::init()->where(['oid' => $orderInfo->id])->getList(false);
                foreach ($orderDetails['data'] as $item) {
                    $num = $item['num'];
                    if (!empty($item['product_prop_id'])) {
                        $id = $item['product_prop_id'];
                        //更改有规格商品 数据库库存
                        //更新规格
                        ProductSku::where('id', $id)->increment('stock', $num);
                        (new SkuRedis())->incr($id, 'stock', $num);
                    }

                    //更新商品
                    Product::where('id', $item['product_id'])->increment('stock', $num);
                    (new ProductRedis())->incr($item['product_id'], 'stock', $num);
                }

                //走完订单一生
                $orderLog = [
                    'oid' => $orderInfo->id,
                    'wid' => $orderInfo->wid,
                    'mid' => $orderInfo->mid,
                    'action' => 14,
                    'remark' => '系统自动关闭订单',
                ];
                //操作：1买家创建订单；2买家付款；3商家发货；4买家确认收货；5买家评价；6买家取消订单；7买家申请退款；8商家同意退款；9商家拒绝退款；10买家取消退款；11系统自动确认收货；12商家关闭交易；13 延期收货 14 系统关闭订单
                OrderLogService::init('wid', $orderInfo->wid)->add($orderLog, false);
                OrderService::upOrderLog($orderInfo->id, $orderInfo->wid);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('取消超时未付款普通订单时发生错误，订单ID：' . $commonOrderId . '；错误信息：' . $e->getMessage());
            }
        }
    }

    /**
     * todo 获取用户拥有的优惠券
     * @param $wid
     * @param $mid
     * @param array $productsId
     * @param int $productAmount
     * @return array
     * @author jonzhang
     * @date 2017-09-15
     */
    public function getCouponByMid($wid, $mid, $productsList = [], $isDiscount = 0)
    {
        $productsId = [];
        //订单折扣后的总额 判断优惠券使用
        $cartAmountTotal = 0.00;
        foreach ($productsList as $item) {
            $productsId[] = $item['product_id'];
            $cartAmountTotal += $item['product_amount'];
        }
        //获取优惠券列表
        //传递商品ID 即查出该商品对应的有限制的优惠券[该商品可以使用的有限制的优惠券] 不传商品id则查出该用户在某个店铺下拥有的所有优惠券
        //用户在该店铺下拥有的所有优惠券[无限制优惠券和有品类限制优惠券]
        $couponList = (new CouponLogService())->getCoupons('valid', $wid, $mid, [], false);
        $validCoupons = [];
        $invalidCoupons = [];
        $now = time();
        if (!empty($couponList['data'])) {
            foreach ($couponList['data'] as $item) {
                //商品的通用优惠券
                if ($item['range_type'] == 0) {
                    $commonData = [];
                    //优惠券id
                    $commonData['coupon_id'] = $item['id'];
                    //优惠券名称
                    $commonData['name'] = $item['title'];
                    //优惠券折扣金额
                    $commonData['discount_amount'] = $item['amount'];
                    //优惠券商品金额 [limit_amount为0表示不属于满减]
                    $commonData['product_amount'] = $item['limit_amount'];
                    //是否通用 1通用
                    $commonData['is_common'] = 1;
                    //优惠券描述
                    $commonData['desc'] = $item['limit_amount'] > 0 ? ('满' . $item['limit_amount'] . '元使用') : '无使用门槛';
                    $commonData['only_original_price'] = $item['only_original_price'];
                    $commonData['start_at'] = $item['start_at'];
                    $commonData['end_at'] = $item['end_at'];

                    //下单页的优惠券列表 未开始的 放入不可用列表
                    $commonData['isStart'] = 1;

                    //优惠券具体不可用原因 1未生效 2仅原价购买可使用 3未满足满减 4没有一个商品属于指定商品 Herry 20180320
                    $commonData['invalid_type'] = 1;
                    $commonData['range_type'] = $item['range_type'];

                    if (strtotime($item['start_at']) > $now) {
                        $commonData['isStart'] = 0;
                        $commonData['invalid_type'] = 1;
                        $invalidCoupons[] = $commonData;
                    } else {
                        //判断是否可用
                        if ($commonData['only_original_price'] && $isDiscount) {
                            //仅原价商品才能使用该优惠券
                            $commonData['invalid_type'] = 2;
                            $invalidCoupons[] = $commonData;
                        } else {
                            if ($item['limit_amount'] <= $cartAmountTotal) {
                                $validCoupons[] = $commonData;
                            } else {
                                $commonData['invalid_type'] = 3;
                                $invalidCoupons[] = $commonData;
                            }
                        }
                    }
                } //针对商品的优惠券
                else if ($item['range_type'] == 1) {
                    $typeData = [];
                    //优惠券的id
                    $typeData['coupon_id'] = $item['id'];
                    //优惠券名称
                    $typeData['name'] = $item['title'];
                    //优惠券的优惠金额
                    $typeData['discount_amount'] = $item['amount'];
                    //优惠券的使用标准 满减
                    $typeData['product_amount'] = $item['limit_amount'];
                    //可以使用优惠券的商品id
                    $typeData['product_id'] = $item['range_value'];
                    //是否通用 0不通用
                    $typeData['is_common'] = 0;
                    //优惠券描述
                    $typeData['desc'] = $item['limit_amount'] > 0 ? ('满' . $item['limit_amount'] . '元使用') : '无使用门槛';
                    //Herry @todo 是否仅原价购买使用
                    $typeData['only_original_price'] = $item['only_original_price'];
                    $typeData['start_at'] = $item['start_at'];
                    $typeData['end_at'] = $item['end_at'];

                    //下单页的优惠券列表 未开始的 放入不可用列表
                    $typeData['isStart'] = 1;

                    //优惠券具体不可用原因 1未生效 2仅原价购买可使用 3未满足满减 4没有一个商品属于指定商品 Herry 20180320
                    $typeData['invalid_type'] = 1;
                    $typeData['range_type'] = $item['range_type'];

                    if (strtotime($item['start_at']) > $now) {
                        $typeData['isStart'] = 0;
                        $typeData['invalid_type'] = 1;
                        $invalidCoupons[] = $typeData;
                    } else {
                        //判断是否可用
                        if ($typeData['only_original_price'] && $isDiscount) {
                            //仅原价商品才能使用该优惠券
                            $typeData['invalid_type'] = 2;
                            $invalidCoupons[] = $typeData;
                        } else {
                            //判断订单里所有商品是否都在指定优惠范围 且价格是否满足满减
                            $couponProductArr = explode(',', $item['range_value']);
                            //满减新规则 订单中只要有一个商品满足条件 或者 某几个商品加起来满足条件 这张优惠券则可用 @鹏飞 Herry
                            //订单中部分商品满足门槛则可使用优惠券 退款时金额还是按以前的按比例分摊优惠券
                            //订单中满足优惠券条件的商品总金额
                            $productAmount = 0;
                            //是否满足满减
                            $isValid = false;

                            //订单中是否有商品属于指定商品
                            $is_product_in_range = false;

                            //循环订单中商品
                            foreach ($productsList as $product) {
                                if (in_array($product['product_id'], $couponProductArr)) {
                                    //当前商品在优惠券指定商品范围
                                    $is_product_in_range = true;
                                    if ($product['product_amount'] >= $item['limit_amount']) {
                                        $isValid = true;
                                        break;
                                    } else {
                                        $productAmount += $product['product_amount'];
                                        if ($productAmount >= $item['limit_amount']) {
                                            //多个商品金额相加满足条件
                                            $isValid = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            if (!$is_product_in_range) {
                                $typeData['invalid_type'] = 4;
                            } elseif (!$isValid) {
                                $typeData['invalid_type'] = 3;
                            }

                            $isValid ? ($validCoupons[] = $typeData) : ($invalidCoupons[] = $typeData);
                        }
                    }
                }
            }
        }

        return [
            'valid' => $validCoupons,
            'invalid' => $invalidCoupons
        ];
    }

    /***
     * todo 用户默认可以使用的优惠券
     * @param $wid
     * @param $mid
     * @param array $productsId
     * @param int $productAmount
     * @return array|mixed
     * @author jonzhang
     * @date 2017-09-15
     */
    public function getDefaultCouponByMid($wid, $mid, $productsList = [], $isDiscount = 0)
    {
        //定义数组，存放用户拥有的所有优惠券和最优优惠券
        $couponList = ['all' => [], 'default' => []];
        if (empty($wid) || empty($mid) || empty($productsList)) {
            return $couponList;
        }
        //用户拥有的所有优惠券
        $coupons = $this->getCouponByMid($wid, $mid, $productsList, $isDiscount);
        $couponList['all'] = array_merge($coupons['valid'], $coupons['invalid']);

        $value = 0.00;
        $coupon = [];
        //获取用户可是使用的最大金额优惠券
        foreach ($coupons['valid'] as $item) {
            if ($item['discount_amount'] > $value) {
                $value = $item['discount_amount'];
                $coupon = $item;
            }
        }
        //当拥有金额相同的两种类型的优惠券时，选择有限制的优惠券
        foreach ($coupons['valid'] as $item) {
            if (!empty($coupon) && $coupon['discount_amount'] == $item['discount_amount'] && $item['is_common'] == 0) {
                $coupon = $item;
                break;
            }
        }
        $couponList['default'] = $coupon;

        //先按优惠金额倒叙 再按结束时间排序 Herry 20171107
        $amount = [];
        $end = [];
        foreach ($coupons['valid'] as $k => $v) {
            $amount[$k] = $v['discount_amount'];
            $end[$k] = $v['end_at'];
        }
        if ($coupons['valid']) {
            array_multisort($amount, SORT_DESC, $end, SORT_ASC, $coupons['valid']);
        }

        $amount = [];
        $end = [];
        foreach ($coupons['invalid'] as $k => $v) {
            $amount[$k] = $v['discount_amount'];
            $end[$k] = $v['end_at'];
        }
        if ($coupons['invalid']) {
            array_multisort($amount, SORT_DESC, $end, SORT_ASC, $coupons['invalid']);
        }

        return [
            'valid' => $coupons['valid'], //可用
            'invalid' => $coupons['invalid'], //不可用
            'default' => $couponList['default'], //最优,
            'all' => $couponList['all'] //全部
        ];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171013
     * @desc 创建订单
     * @param $pid
     * @param $num
     * @param $skuId
     * @param $addr
     * @param int $groups_id
     * @update 张永辉 2018年7月9日 小程序配置信息写入订单
     * @update 梅杰 2018年10月19日 待付款订单消息提醒
     * @update 何书哲 2018年11月16日 添加外卖订单标记
     */
    public function createOrder($mData, $addr, $groups_id = 0, $wid = '', $source = 0, $isHexiao = 0, $heXiaoData = [])
    {
        $request = app('request');
        $num = $request->input('num');
        $remarkNo = $request->input('remark_no', '');
        $token = $request->input('token', '0');
        $xcx_config_id = (new CommonModule())->getXcxConfigIdByToken($token);
        if ($num <= 0 || floor($num) != $num) {
            $result['errCode'] = -5;
            $result['errMsg'] = '数量必须为正整数!';
            return $result;
        }
        $pid = $request->input('pid');
        $sku_id = $request->input('sku_id') ?? 0;
        $formId = $request->input('formId') ?? 0;
        $orderData['oid'] = OrderCommon::createOrderNumber();
        $orderData['trade_id'] = $orderData['oid'];
        $orderData['wid'] = empty($wid) ? $mData['wid'] : $wid;
        $orderData['mid'] = $mData['id'];
        $orderData['umid'] = $mData['umid'];
        $orderData['groups_id'] = $groups_id;
        $orderData['source'] = $source;
        $orderData['form_id'] = $formId;
        $orderData['xcx_config_id'] = $xcx_config_id ? $xcx_config_id : 0;
        $res = (new GroupsRuleModule())->getSettlementInfo($pid, $sku_id);
        if ($res['errCode'] != 0) {
            return $res;
        }

        //何书哲 2018年11月22日 外卖店铺添加订单提交约束
        $checkRes = (new StoreModule())->checkIfSubmitOrder($orderData['wid']);
        if ($checkRes['errCode'] != 0) {
            $result['errCode'] = -3;
            $result['errMsg'] = $checkRes['errMsg'];
            return $result;
        }

        //判断是否无需物流
        $productData = ProductService::getRowById($pid);
        // update by wuxiaoping 2018.06.11
        if ($isHexiao == 0) {
            if ($productData && $productData['no_logistics'] == 0 && $addr['id'] == 0) {
                $result['errCode'] = -3;
                $result['errMsg'] = '请填写收货地址哦!';
                return $result;
            }
        }

        if ($groups_id != 0) {
            $groupDetailData = (new GroupsDetailService())->getListByWhere(['groups_id' => $groups_id, 'member_id' => $mData['id']]);
            if ($groupDetailData) {
                $result['errCode'] = -3;
                $result['errMsg'] = '您已参加过该团!';
                return $result;
            }
            //判断限购
//            $ruleNum = (new GroupsService())->getLimitNumByGroupsId($groups_id);
            $groupsData = (new GroupsService())->getRowById($groups_id);
            $ruleNum = (new GroupsRuleModule())->limtNum($mData['id'], $groupsData['rule_id']);
            if ($ruleNum > 0 && $ruleNum < $num) {
                $result['errCode'] = -4;
                $result['errMsg'] = '购买数量超过限购数量!';
                return $result;
            }
        }

        $res = $res['data'];
        // update by wuxiaoping 2018.06.11
        if ($isHexiao == 0) {
            $orderData['address_id'] = $addr['id'];
            $orderData['address_name'] = $addr['title'];
            $orderData['address_phone'] = $addr['phone'];
            $orderData['address_detail'] = $addr['detail'];
            $orderData['address_province'] = $addr['province']['title'];
            $orderData['address_city'] = $addr['city']['title'];
            $orderData['address_area'] = $addr['area']['title'];
        }

        $orderData['type'] = 3;
        $orderData['buy_remark'] = $request->input('remark') ?? '';

        // update by wuxiaoping 2018.06.11
        if ($isHexiao == 0) {
            $result = $this->getGroupsOrder($groups_id, $num, $orderData['wid'], $orderData['mid'], $orderData['umid'], $addr['id']);
        } else { //如果是自提订单则不需要运费
            $result = $this->getGroupsOrder($groups_id, $num, $orderData['wid'], $orderData['mid'], $orderData['umid'], 0);
        }
        $orderData['pay_price'] = $result['lastAmount'];
        $orderData['products_price'] = $result['productTotalAmount'];
        $orderData['freight_price'] = $result['freight'];

        $orderData['discount_amount'] = $result['head_discount'];
        $orderData['head_discount'] = $result['head_discount'];
        $orderData['groups_id'] = $groups_id;
        $orderData['use_point'] = 0;
        $orderData['groups_status'] = 1;
        $orderData['is_hexiao'] = $isHexiao;
        if ($isHexiao == 1) {
            $hexiaoCode = rand(5000, 9999) . rand(1000, 4999) . rand(100, 999); //生成七位提货码
            $order['hexiao_code'] = $hexiaoCode;
        }
        $orderData['is_takeaway'] = (new StoreModule())->getWidTakeAway($orderData['wid']) ? 1 : 0;//何书哲 2018年11月16日 添加外卖订单标记

        DB::beginTransaction();
        //创建订单
        $id = Order::insertGetId($orderData);
        $orderData = Order::find($id)->toArray();
        //减库存
        $reduce = $this->reduceStock($pid, $sku_id, $num);
        if (!$reduce) {
            $result['errCode'] = -2;
            $result['errMsg'] = '商品库存不足';
            return $result;
        }
        /*如果是自提订单，创建订单自提信息 wuxiaoping 2018.06.11*/
        if ($isHexiao == 1) {
            $zitiData['wid'] = $orderData['wid'];
            $zitiData['mid'] = $orderData['mid'];
            $zitiData['oid'] = $id;
            $zitiData['ziti_id'] = $heXiaoData['zitiId'];
            $zitiData['ziti_contact'] = $heXiaoData['zitiContact'];
            $zitiData['ziti_phone'] = $heXiaoData['zitiPhone'];
            $zitiData['ziti_datetime'] = $heXiaoData['zitiDatetime'];
            (new OrderZitiService())->add($zitiData);
        }
        //创建订单详情
        $data = [
            'pid' => $pid,
            'title' => $res['title'],
            'img' => $res['skuData']['img'],
            'oprice' => $res['oprice'],
            'price' => $res['price'],
            'num' => $num,
            'sku_id' => $sku_id,
            'skuData' => $res['skuData'] ?? [],
            'remark_no' => $remarkNo,
        ];
        $orderDetailData = $this->createOrderDetail($orderData, $data);
        //添加订单日志
        $orderLogData = $this->addOrderLog($orderData['id'], $mData['wid'], $mData['id']);
        //存redis
        $orderData['orderDetail'][] = $orderDetailData;
        $orderData['orderLog'][] = $orderLogData;
        OrderService::init()->addR($orderData, false);
        DB::commit();
        //梅杰 2018年10月18日 订单待付款
        $source == 0 && (new MessagePushModule($orderData['wid'], MessagesPushService::TradeUrge))->setDelay(60)->sendMsg($id, $xcx_config_id);
        $source == 1 && (new MessagePushModule($orderData['wid'], MessagesPushService::TradeUrge, MessagePushModule::SEND_TARGET_WECHAT_XCX))->setDelay(60)->sendMsg($id, $xcx_config_id);
        //计算分销
        dispatch((new Distribution($orderData, '2'))->onQueue('Distribution'));
        $result['errCode'] = 0;
        $result['data'] = $orderData;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 创建订单日志
     */
    public function addOrderLog($id, $wid, $mid)
    {
        $orderLogData = [
            'oid' => $id,
            'wid' => $wid,
            'mid' => $mid,
            'action' => 1,
            'remark' => '创建订单',
        ];

        $id = OrderLogService::init()->model->insertGetId($orderLogData);
        $orderLogData['id'] = $id;
        OrderLogService::init()->addR($orderLogData, false);
        return $orderLogData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 创建订单详情
     * @param $order
     * @param $cartData
     * @return mixed
     */
    public function createOrderDetail($order, $data)
    {
        $orderDetailData['oid'] = $order['id'];
        $orderDetailData['product_id'] = $data['pid'];
        $orderDetailData['title'] = $data['title'];
        $orderDetailData['img'] = $data['img'];
        $orderDetailData['oprice'] = $data['oprice'];
        $orderDetailData['price'] = $data['price'];
        $orderDetailData['num'] = $data['num'];
        $orderDetailData['after_discount_price'] = $order['products_price'] - $order['head_discount'];
        $orderDetailData['remark_no'] = $data['remark_no'];
        if ($data['sku_id']) {
//            $orderDetailData['spec'] = $cartData['prop1'].':'.$cartData['prop_value1'].','.$cartData['prop2'].":".$cartData['prop_value2'].','.$cartData['prop3'].":".$cartData['prop_value3'];
            $orderDetailData['spec'] = '';
            if ($data['skuData']['k1']) {
                $orderDetailData['spec'] = $data['skuData']['k1'] . ':' . $data['skuData']['v1'];
            }
            if ($data['skuData']['k2']) {
                $orderDetailData['spec'] = $orderDetailData['spec'] . ',' . $data['skuData']['k2'] . ":" . $data['skuData']['v2'];
            }
            if ($data['skuData']['k3']) {
                $orderDetailData['spec'] = $orderDetailData['spec'] . ',' . $data['skuData']['k3'] . ":" . $data['skuData']['v3'];
            }
            $orderDetailData['product_prop_id'] = $data['sku_id'];
        }
        $id = OrderDetailService::init()->model->insertGetId($orderDetailData);
        if ($id) {
            $orderDetailData = OrderDetailService::init()->find($id)->toArray();
            OrderDetailService::init()->addR($orderDetailData, false);
        }
        return $orderDetailData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 生成订单减少库存
     * @param $pid
     * @param $skuid
     */
    public function reduceStock($pid, $skuid, $num)
    {
        $res = ProductService::decrement($pid, 'stock', $num);
        if (!$res) {
            return false;
        }
        ProductService::increment($pid, 'sold_num', $num);
        if ($skuid != 0) {
            $productSkuService = new ProductSkuService();
            $res = $productSkuService->decrement($skuid, 'stock', $num);
            $productSkuService->increment($skuid, 'sold_num', $num);
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc  获取团购订单的相关优惠及价格
     * @param $groups_id
     * @return array
     */
    public function getGroupsOrder($groups_id, $num, $wid, $mid, $umid, $addressID)
    {

        $groupsService = new GroupsService();
        $groupsRuleService = new GroupsRuleService();
        $groupsSkuService = new GroupsSkuService();
        $groupsData = $groupsService->getRowById($groups_id);
        $groupsRuleData = $groupsRuleService->getRowById($groupsData['rule_id']);
        $sku_id = app('request')->input('sku_id') ?? 0;

        $groupsSkuData = $groupsSkuService->getlistByWhere(['rule_id' => $groupsData['rule_id'], 'sku_id' => $sku_id]);
        $head_discount = 0;
        if ($groupsRuleData['head_discount'] == 1) {
            //团长优惠
            $groupsDetailData = (new GroupsDetailService())->count(['groups_id' => $groups_id]);
            if ($groupsDetailData == 0) {
                $head_discount = ($groupsSkuData[0]['price'] - $groupsSkuData[0]['head_price']) * $num;
            }

        }
        if ($groupsSkuData) {
            //商品价格
            $productPrice = $groupsSkuData[0]['price'];
            $productTotalAmount = $groupsSkuData[0]['price'] * $num;
            $lastAmount = $productTotalAmount - $head_discount;
        }

        //拼团代付款页面返回运费字段 Herry 20171208
        $groupBuyInfo = [
            [
                'product_id' => $groupsRuleData['pid'],
                'prop_id' => $sku_id,
                'num' => $num
            ]
        ];
        //运费
        $freight = (new OrderModule())->getFreightByCartIDArr([], $wid, $mid, $umid, $addressID, $groupBuyInfo);
        $freight = sprintf('%.2f', $freight);

        $lastAmount = $lastAmount + $freight;
        $lastAmount = sprintf('%.2f', $lastAmount);
        return [
            'head_discount' => $head_discount,
            'productTotalAmount' => $productTotalAmount,
            'lastAmount' => $lastAmount,
            'freight' => $freight,
            'productPrice' => $productPrice,
        ];

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171023
     * @desc 获取发货时间
     * @param $oid
     */
    public function getDeliverTime($oid)
    {
        $logData = OrderLogService::init()->model->where('oid', $oid)->where('action', 3)->get()->toArray();
        if ($logData) {
            return $logData[0]['created_at'];
        } else {
            return '';
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 团购订单未成团退款
     * @param $oid
     * @update 梅杰 20180716 指定小程序拼团订单退款
     */
    public function groupOrderRefund($oid, $pid)
    {
        $orderData = OrderService::init()->getInfo($oid);
        $wechatRefundModule = new WeChatRefundModule();
        switch ($orderData['pay_way']) {
            case '1':
            case '10':
                //pay_way 1公众号微信支付 10小程序微信支付 Herry 20171110
                $param = [
                    'source' => $orderData['source'],   //0公众号 1小程序
                    'serial_id' => $orderData['serial_id'],//order.serial_id
                    'id' => $pid,
                    'pay_price' => $orderData['pay_price'],//订单总支付金额
                    'amount' => $orderData['pay_price'],//退款金额
                    'pay_way' => $orderData['pay_way'], // 支付方式
                    'xcxConfigId' => $orderData['xcx_config_id']
                ];
                return $wechatRefundModule->weChatRefund($orderData['wid'], $param, true);
                break;
            case '2':

                break;
            case '3':
                $param = [
                    'pay_price' => $orderData['pay_price'],//订单总支付金额
                    'mid' => $orderData['mid'],
                    'oid' => $oid,
                    'refund_status' => $orderData['refund_status'],
                    'amount' => $orderData['pay_price'], //退款金额
                    'id' => $pid,
                ];
                return $wechatRefundModule->balanceRefund($orderData['wid'], $param);
                break;
            case '9':
                break;
            default:
                /*return [
                    'code' => 'false',
                    'code_des' =>'该支付方式暂时不支持退款'
                ];*/
                break;
        }
    }

    /**
     * 根据购物车获取运费
     * @param array $cartIDArr 购物车ID数组 除拼团外使用
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @param int $umid 统一账户id
     * @param $addressID int 收货地址ID 不传则取默认地址
     * @param $groupBuyInfo array 拼团待提交订单信息 仅拼团使用
     * $groupBuyInfo = [
     *     [
     *         'product_id' => 1, 商品ID
     *         'prop_id' => 0, sku ID 无规格则为0
     *         'num' => 1 购买数
     *     ],
     * ]
     * @param bool $isUseCard 是否使用会员卡
     * @return float 运费金额
     * @author 许立 2017年11月23日
     * @update 许立 2018年6月26日   修改获取用户地址
     */
    public function getFreightByCartIDArr($cartIDArr, $wid, $mid, $umid, $addressID = 0, $groupBuyInfo = [], $isUseCard = true)
    {
        //Herry 20180327 09:18
        if ($isUseCard) {
            //检查是否有包邮会员卡
            $userCard = MemberCardRecordService::useCard($mid, $wid);
            if ($userCard['errCode'] == 0 && $userCard['data']['isOwn'] == 1) {
                if ($userCard['data']['info']['isDelivery']) {
                    //包邮
                    return 0.00;
                }
            }
        }

        //参数$cartIDArr和$groupBuyInfo只传一个
        if ($cartIDArr) {
            //获取购物车信息
            $cartService = new CartService();
            list($carts) = $cartService->init('mid', $mid)->where(['id' => ['in', $cartIDArr]])->getList(false);
            if (empty($carts['data'])) {
                return 0.00;
            }
            $carts = $carts['data'];
        } elseif ($groupBuyInfo) {
            $carts = $groupBuyInfo;
        } else {
            return 0.00;
        }

        //购物车中所有商品
        $productIDArr = [];
        foreach ($carts as $cart) {
            $productIDArr[] = $cart['product_id'];
        }

        //商品列表信息
        list($products) = ProductService::listWithoutPage(['id' => ['in', $productIDArr]]);
        if (empty($products['data'])) {
            return 0.00;
        }

        //遍历商品 归类运费类型 计算运费
        //统一运费
        $commonFreight = 0.00;
        $commonFreightArr = [];
        //$commonFreightArr = [5, 10, 8];
        //统一运费的每个运费是否都相同
        $areCommonFreightEqual = true;

        //运费模板
        $templateFreight = 0.00;
        //某运费模板商品总购买数量数组 按件数计算运费时使用
        $templateFreightNumArr = [];
        //购物车中商品信息数组
        $templateFreightProductInfoArr = [];
        foreach ($carts as $cart) {
            $product = ProductService::getDetail($cart['product_id']);
            if (empty($product)) {
                continue;
            }
            if ($product['freight_type'] == 1) {
                //统一运费
                if ($commonFreightArr && $areCommonFreightEqual && !in_array($product['freight_price'], $commonFreightArr)) {
                    $areCommonFreightEqual = false;
                }
                $commonFreightArr[] = $product['freight_price'];
            } elseif ($product['freight_type'] == 2) {
                //运费模板
                $product['cart_sku_id'] = $cart['prop_id'] ?: 0;
                $product['cart_num'] = $cart['num'] ?: 1;
                $templateFreightProductInfoArr[$product['freight_id']][] = $product;
                if (empty($templateFreightNumArr[$product['freight_id']])) {
                    $templateFreightNumArr[$product['freight_id']] = $cart['num'];
                } else {
                    $templateFreightNumArr[$product['freight_id']] += $cart['num'];
                }
            }
        }

        //统一运费 如果每个商品的运费都相同 则相加；否则 取最大运费 @鹏飞 20180118
        if ($commonFreightArr) {
            if ($areCommonFreightEqual) {
                $commonFreight = array_sum($commonFreightArr);
            } else {
                $commonFreight = max($commonFreightArr);
            }
        }

        //获取当前收货地址
        if (!empty($addressID)) {
            $where = ['id' => $addressID];
        } elseif (empty($umid)) {
            $where = ['mid' => $mid, 'type' => 1];
        } else {
            $where = ['umid' => $umid, 'type' => 1];
        }
        list($address) = MemberAddressService::init()->where($where)->getList(false);
        if (empty($address['data'])) {
            // 许立 2018年6月26日 微信导入地址 只导入一个地址的时候地址表的这条地址type=0(即不是默认地址) 判断非默认地址是否存在
            if (empty($umid)) {
                $where = ['mid' => $mid];
            } else {
                $where = ['umid' => $umid];
            }
            list($address) = MemberAddressService::init()->where($where)->getList(false);
            if (empty($address['data'])) {
                // 一条地址都没有
                return 0.00;
            }
        }

        //当前收货地区信息
        $provinceID = $address['data'][0]['province_id'] ?? 0;
        $cityID = $address['data'][0]['city_id'] ?? 0;
        $areaID = $address['data'][0]['area_id'] ?? 0;

        //当前地址最低级ID 用来判断是否在模板规则中
        //$currentAddressID = $areaID ? $areaID : ($cityID ? $cityID : ($provinceID ? $provinceID : 0));

        //运费模板运费计算
        $freightService = new FreightService();
        $skuService = new ProductSkuService();
        foreach ($templateFreightProductInfoArr as $freightID => $products) {
            //运费模板
            $freightTpl = $freightService->init('wid', $wid)->getInfo($freightID);

            //todo 运费模板不存在 运费为0
            if (empty($freightTpl)) {
                continue;
            }

            //模板具体规则
            $rule = json_decode($freightTpl['delivery_rule'], true);

            if (!is_array($rule)) {
                continue;
            }

            //获取默认规则
            $defaultRule = [];
            foreach ($rule as $v) {
                if (count($v['regions']) == 1 && $v['regions'][0] == 0) {
                    //默认配置规则
                    $defaultRule = $v;
                    break;
                }
            }

            //获取当前收货地址所属规则
            $bestRule = [];
            foreach ($rule as $v) {
                //地区数组
                /*$isThisRule = false;
                //自定义省市区规则
                foreach ($v['regions'] as $id) {
                    if ($id) {
                        if (in_array($id, [$provinceID, $cityID, $areaID])) {
                            //如果该规则直接匹配到当前收货地址
                            $isThisRule = true;
                            break;
                        }
                    }
                }
                if ($isThisRule) {
                    //当前地址属于该规则
                    $bestRule = $v;
                    break;
                }*/

                //当前地址是否属于该规则 20171129
                /*if (in_array($currentAddressID, $v['regions'])) {
                    $bestRule = $v;
                    break;
                }*/

                //规则 前端存储最低级 所有区选中则只存它们的市 所有的市选中则只存它们的省
                if (in_array($areaID, $v['regions']) || in_array($cityID, $v['regions']) || in_array($provinceID, $v['regions'])) {
                    $bestRule = $v;
                    break;
                }
            }

            //最终规则 如果属于指定规则 则按照指定规则计算 否则按默认规则计算
            $finalRule = $bestRule ? $bestRule : $defaultRule;

            //运费计量类型
            $numOrWeight = 0;
            if ($freightTpl['billing_type'] == 0) {
                //按件
                $numOrWeight = $templateFreightNumArr[$freightID];
            } else {
                //按重量 计算某模板中所有购买商品的总重量
                foreach ($products as $product) {
                    if (empty($product['cart_sku_id'])) {
                        //无规格 直接累加商品重量
                        $numOrWeight += $product['weight'] * $product['cart_num'];
                    } else {
                        //有规格的重量 购买规格的重量 * 购买数量
                        $sku = $skuService->getRowById($product['cart_sku_id']);
                        if ($sku) {
                            $numOrWeight += $sku['weight'] * $product['cart_num'];
                        }
                    }
                }
            }

            //首件(重)续件(重) 如果没填 设置默认值为1 费用默认为0
            //first_fee必须为正数 additional_fee可为负数(多买减邮费的情况)
            $finalRule['first_amount'] = $finalRule['first_amount'] ? abs($finalRule['first_amount']) : 1;
            $finalRule['additional_amount'] = $finalRule['additional_amount'] ? abs($finalRule['additional_amount']) : 1;
            $finalRule['first_fee'] = empty($finalRule['first_fee']) ? 0.00 : abs(sprintf('%.2f', $finalRule['first_fee']));
            $finalRule['additional_fee'] = empty($finalRule['additional_fee']) ? 0.00 : sprintf('%.2f', $finalRule['additional_fee']);

            //最终根据首件续件计算运费
            if ($numOrWeight <= $finalRule['first_amount']) {
                //首件首重内
                $templateFreight += $finalRule['first_fee'];
            } else {
                //超出首件首重
                $calculateFreight = $finalRule['first_fee'] + (ceil(($numOrWeight - $finalRule['first_amount']) / $finalRule['additional_amount']) * $finalRule['additional_fee']);
                $calculateFreight = $calculateFreight > 0 ? $calculateFreight : 0.00;
                $templateFreight += $calculateFreight;
            }
        }

        //最终总运费=统一运费+运费模板计算运费
        $totalFreight = $commonFreight + $templateFreight;
        $totalFreight = sprintf('%.2f', $totalFreight);

        return $totalFreight;
    }


    /**
     * @author hsz
     * @param $wid 店铺id
     * @param array $data 查询条件
     * @return array
     * @desc 获取订单列表
     */
    public function getOrderList($wid, $data = [])
    {
        $where['wid'] = $wid;
        /*
         * 订单类型筛选 order_type
         * ['0'=> '全部', '1'=>'普通订单',  '3'=>'多人拼团订单', '4'=>'积分兑换订单' ,'5'=>'积分抵现订单', '6'=>'分销订单','7'=>'秒杀订单','8'=>'小程序订单','10'=>'享立减订单']
         */
        if (isset($data['order_type'])) {
            if ($data['order_type'] == 0) {//全部

            } elseif ($data['order_type'] == 6) { //分销订单
                $where['distribute_type'] = 1;
            } elseif ($data['order_type'] == 1) {
                $where['type'] = $data['order_type'];//普通订单
                $where['distribute_type'] = 0;
            } else {
                $where['type'] = $data['order_type'];
            }
        }
        /*
         * 模糊查询 订单号、团编号、收货人姓名、收货人手机号 key_search
         */
        //\DB::connection()->enableQueryLog();
        $page = isset($data['page']) ? $data['page'] : 1;
        $size = isset($data['size']) ? $data['size'] : 6;
        $orderBy = isset($data['orderby']) ? $data['orderby'] : 'created_at';
        $order = isset($data['order']) ? $data['order'] : 'DESC';
        if (isset($data['key_search']) && !empty($data['key_search'])) {
            $query = OrderService::init()->model
                ->where($where)
                ->where(function ($query) use ($data) {
                    if (isset($data['status'])) {
                        /*
                        * 订单状态筛选 status
                        * [ '0'=>'待付款','-1'=>'待成团', '1'=>'待发货', '5'=>'退款中', '2'=>'已发货', '3'=>'已完成', '4'=>'已关闭'];
                         */
                        if ($data['status'] == -2) {//全部订单

                        } elseif ($data['status'] == 5) {//退款中订单
                            //除去refund_status为0,2,5,8,9的订单
                            $query->whereNotIn('refund_status', [0, 2, 5, 8, 9]);
                        } elseif ($data['status'] == -1) {//团购订单
                            $query->where('groups_id', '<>', 0)->where('groups_status', 1)->where('status', 1);
                        } elseif ($data['status'] == 1) {//待发货订单(去掉退款成功的订单)
                            $query->whereIn('groups_status', [0, 2])->where('is_hexiao', '<>', 1)->where('status', 1)->where('refund_status', '<>', 8);
                        } elseif ($data['status'] == 3) {//已完成订单
                            $query->where('status', 3)->orWhere('refund_status', 8);
                        } elseif ($data['status'] == 4) {//已关闭订单(去掉退款成功的订单)
                            $query->where('status', 4)->where('refund_status', '<>', 8);
                        } else {
                            $query->where('status', $data['status']);
                        }
                    }
                })
                ->where(function ($query) use ($data) {
                    $query->where('oid', 'like', '%' . $data['key_search'] . '%')
                        ->orWhere('address_name', 'like', '%' . $data['key_search'] . '%')
                        ->orWhere('address_phone', 'like', '%' . $data['key_search'] . '%')
                        ->orWhere(function ($query) use ($data) {
                            $group_ids = (new GroupsService())->getInfoByIdentifier($data['key_search']);
                            if (!empty($group_ids)) {
                                $query->whereIn('groups_id', array_unique(array_column($group_ids, 'id')));
                            }
                        });
                })
                ->select(['id', 'oid', 'mid', 'wid', 'created_at', 'pay_price', 'freight_price', 'seller_remark', 'status', 'groups_id', 'groups_status', 'is_hexiao', 'refund_status']);
            //$queries = \DB::getQueryLog();
            //show_debug($queries);
        } else {
            $query = OrderService::init()->model
                ->where($where)
                ->where(function ($query) use ($data) {
                    if (isset($data['status'])) {
                        /*
                        * 订单状态筛选 status
                        * [ '0'=>'待付款','-1'=>'待成团', '1'=>'待发货', '5'=>'退款中', '2'=>'已发货', '3'=>'已完成', '4'=>'已关闭'];
                         */
                        if ($data['status'] == -2) {//全部订单

                        } elseif ($data['status'] == 5) {//退款中订单
                            //除去refund_status为0,2,5,8,9的订单
                            $query->whereNotIn('refund_status', [0, 2, 5, 8, 9]);
                        } elseif ($data['status'] == -1) {//团购订单
                            $query->where('groups_id', '<>', 0)->where('groups_status', 1)->where('status', 1);
                        } elseif ($data['status'] == 1) {//待发货订单（去掉退款成功的订单）
                            $query->whereIn('groups_status', [0, 2])->where('is_hexiao', '<>', 1)->where('status', 1)->where('refund_status', '<>', 8);
                        } elseif ($data['status'] == 3) {//已完成订单
                            $query->where('status', 3)->orWhere('refund_status', 8);
                        } elseif ($data['status'] == 4) {//已关闭订单(去掉退款成功的订单)
                            $query->where('status', 4)->where('refund_status', '<>', 8);
                        } else {
                            $query->where('status', $data['status']);
                        }
                    }
                })->select(['id', 'oid', 'mid', 'wid', 'created_at', 'pay_price', 'freight_price', 'seller_remark', 'status', 'groups_id', 'groups_status', 'is_hexiao', 'refund_status']);
        }
        $count_num = $query->count();
        $orderList = $query->orderBy($orderBy, $order)->skip(($page - 1) * $size)->take($size)->get()->toArray();
        foreach ($orderList as $key => &$value) {
            //获取买家昵称
            $member_info = (new \App\S\Member\MemberService())->getRowById($value['mid']);
            $value['nickname'] = ($member_info && isset($member_info['nickname'])) ? $member_info['nickname'] : '';
            //获取订单详情 商品名称、商品图片、购买数量、规格字符串
            $value['order_detail'] = OrderDetailService::init()->model->where('oid', $value['id'])->get(['id as detail_id', 'product_id', 'product_prop_id', 'title', 'img', 'num', 'spec'])->toArray();
            if (!$value['order_detail']) {
                unset($orderList[$key]);
            } else {
                //总件数
                $value['total_num'] = array_sum(array_column($value['order_detail'], 'num'));
                //商品退款笔数 订单退款表status不是2,5,8,9的都算退款
                $value['refund_num'] = (new OrderRefundService())->init()->model->where(['oid' => $value['id'], 'wid' => $wid])->whereNotIn('status', [2, 5, 8, 9])->count();
                // 如果是已发货已完成并且需要物流 才在列表中显示查看物流
                $value['is_wuliu'] = ((new LogisticsService())->init()->model->where(['oid' => $value['id'], 'no_express' => 0])->first()) ? 1 : 0;
                if ($value['status'] == 1 && $value['groups_id'] != 0 && $value['groups_status'] == 1) {
                    // 返回待成团订单状态-1
                    $value['status'] = -1;
                } elseif ($value['refund_status'] == 8) {
                    // 退款成功显示为已完成
                    $value['status'] = 3;
                }
                //返回状态内容及id
                $value['text'] = OrderService::getStatusString($value['status']);
            }
        }
        return [
            'is_last' => ($size >= $count_num || ($page * $size >= $count_num)) ? 1 : 0,
            'order_list' => $orderList
        ];
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @return bool
     * @desc 关闭订单获取信息
     */
    public function getCloseOrderInfo($oid)
    {
        $order_detail = OrderService::init()->model->where(['id' => $oid])->select(['id', 'oid', 'mid', 'wid', 'created_at', 'pay_price', 'freight_price', 'status', 'groups_id', 'groups_status'])->get()->toArray();
        if (!$order_detail[0] || $order_detail[0]['status'] != 0) {
            return false;
        }
        //获取订单详情
        //获取买家昵称
        $order_detail[0]['nickname'] = (new \App\S\Member\MemberService())->getRowById($order_detail[0]['mid'])['nickname'];
        //获取订单详情 商品名称、商品图片、购买数量、规格字符串
        $order_detail[0]['order_detail'] = OrderDetailService::init()->model->where('oid', $order_detail[0]['id'])->get(['id as detail_id', 'product_id', 'product_prop_id', 'title', 'img', 'num', 'spec'])->toArray();
        //总件数
        $order_detail[0]['total_num'] = array_sum(array_column($order_detail[0]['order_detail'], 'num'));
        //返回待成团订单状态-1
        if ($order_detail[0]['status'] == 1 && $order_detail[0]['groups_id'] != 0 && $order_detail[0]['groups_status'] == 1) {
            $order_detail[0]['status'] = -1;
        }
        //返回状态内容及id
        $order_detail[0]['text'] = OrderService::getStatusString($order_detail[0]['status']);
        unset($order_detail[0]['mid']);
        return $order_detail[0];
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @return array
     * @desc 获取已发货包裹信息
     */
    public function getDeliveryPackage($oid)
    {
        $deliveryOrderInfo = (new LogisticsService())->getLogistics($oid);
        $delivery_list = [];
        if ($deliveryOrderInfo['success']) {
            foreach ($deliveryOrderInfo['data'] as $value) {
                $tmp = [];
                $tmp['no_express'] = (!isset($value['no_express']) || isset($value['no_express']) && $value['no_express'] == 0) ? 0 : 1;
                $tmp['logistic_no'] = $value['nu']; //运单号
                $tmp['express_name'] = $value['com']; //快递名称
                $tmp['package_num'] = $value['sum']; //件数
                $tmp['detail_list'] = $value['img']; //发货件数详情
                $tmp['id'] = $value['id'];
                $tmp['ischange'] = Redis::get(self::modifyLogisticsKey($value['id'])) ? 0 : 1;
                $tmp['logistics_status'] = (!isset($value['no_express']) || isset($value['no_express']) && $value['no_express'] == 0) && isset($value['state']) ? $this->_dealLogisticsStatus($value['state']) : '';
                $tmp['logistics_info'] = (!isset($value['no_express']) || isset($value['no_express']) && $value['no_express'] == 0) && isset($value['data']) ? $value['data'] : [];
                $delivery_list[] = $tmp;
            }
        }
        return $delivery_list;
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @return array
     * @desc 获取退款包裹信息
     */
    public function getRefundPackage($refund_id, $wid)
    {
        $where = ['wid' => $wid, 'refund_id' => $refund_id, 'status' => 6];
        $messageQuery = (new OrderRefundMessageService())->init()->model->where($where)->select(['express_name', 'express_no'])->first();
        if (!$messageQuery) {
            return [];
        }
        $messageData = $messageQuery->toArray();
        //查询物流word
//        $messageData['express_no'] = '11111111';
//        $messageData['express_name'] = 'yuantong';
        $expressQuery = (new ExpressService())->model->where('word', 'like', '%' . $messageData['express_name'] . '%')->orWhere('title', 'like', '%' . $messageData['express_name'] . '%')->select(['word'])->first();
        if (!$expressQuery) {
            return [];
        }
        $expressData = $expressQuery->toArray();
        $refundQuery = (new OrderRefundService())->init()->model->where(['id' => $refund_id])->select(['oid', 'pid', 'prop_id'])->first();
        if (!$refundQuery) {
            return [];
        }
        $refundData = $refundQuery->toArray();
        $detailQuery = OrderDetailService::init('wid', $wid)->model->where(['oid' => $refundData['oid'], 'product_id' => $refundData['pid'], 'product_prop_id' => $refundData['prop_id']])->first();
        if (!$detailQuery) {
            return [];
        }
        $detailData = $detailQuery->toArray();
        if (isset($messageData['express_no']) && $messageData['express_no']) {
            //去掉运单号中除数字之外的字串 例如运单号:234325436她,查询报错
            preg_match_all('/\d+/', $messageData['express_no'], $arr);
            $messageData['express_no'] = join('', $arr[0]);
            $url = 'http://www.kuaidi100.com/query?&type=' . $expressData['word'] . '&postid=' . $messageData['express_no'];
            //$url = 'http://www.kuaidi100.com/query?&type=zhongtong&postid=11111111';
            $express = jsonCurl($url);
            if ($express['status'] == 200) {
                $res['logistic_no'] = $express['nu']; //运单号
                $res['express_name'] = $express['com']; //快递名称
                $res['package_num'] = $detailData['num']; //件数
                $res['detail_list'] = [
                    'id' => $detailData['id'],
                    'img' => $detailData['img'],
                    'num' => $detailData['num']
                ];
                $res['logistics_status'] = $this->_dealLogisticsStatus($express['state']);
                $res['logistics_info'] = $express['data'];
                return $res;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    private function _dealLogisticsStatus($state)
    {
        $str = '';
        switch ($state) {
            case 0:
                $str = '在途';
                break;
            case 1:
                $str = '揽件';
                break;
            case 2:
                $str = '疑难';
                break;
            case 3:
                $str = '签收';
                break;
            case 4:
                $str = '退签';
                break;
            case 5:
                $str = '派件';
                break;
            case 6:
                $str = '退回';
                break;
        }
        return $str;
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @param $wid 店铺id
     * @return null
     * @desc 发货订单获取信息
     */
    public function getDeliveryOrderInfo($oid, $wid)
    {
        $orderWhere = ['id' => $oid, 'wid' => $wid];
        $orderData = OrderService::init('wid', $wid)->model->where($orderWhere)->get(['id', 'oid', 'created_at', 'address_name', 'address_phone', 'address_detail', 'status', 'groups_id', 'groups_status', 'seller_remark'])->toArray();
        if (!$orderData) {
            return 1;
        }
        if ($orderData[0]['status'] != 1) {
            return 2;
        }
        //获取没有发货的及其退款信息
        $logisticsWhere = ['oid' => $oid];
        $logisticsData = (new LogisticsService())->init()->model->where($logisticsWhere)->get(['odid'])->toArray();
        $ids = [];
        if ($logisticsData) {
            foreach ($logisticsData as $key => $val) {
                $ids = array_merge($ids, explode(',', $val['odid']));
            }
        }
        $orderDetail = OrderDetailService::init()->model->where(['oid' => $oid, 'is_delivery' => 0])->whereNotIn('id', $ids)->get(['id as detail_id', 'product_id', 'product_prop_id', 'title', 'img', 'price', 'num', 'spec'])->toArray();
        $orderData[0]['total_num'] = array_sum(array_column($orderDetail, 'num'));
        foreach ($orderDetail as &$detail) {
            $refundData = (new OrderRefundService())->init('oid', $oid)->where(['oid' => $oid, 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
            if ($refundData && isset($refundData['status']) && ($refundData['status'] == 4 || $refundData['status'] == 8)) {//已退款完成
                $detail['can_delivery'] = 0;
            } else {
                $detail['can_delivery'] = 1;
            }
        }
        $orderData[0]['detail_list'] = $orderDetail;
        //获取已发货的包裹数
        $orderData[0]['parcel_num'] = count($logisticsData);
        //获取付款时间
        $payWhere = ['oid' => $oid, 'wid' => $wid, 'action' => 2];
        $payData = OrderLogService::init()->model->where($payWhere)->order('created_at desc')->get(['created_at'])->toArray();
        $orderData[0]['pay_at'] = $payData ? current($payData)['created_at'] : '';
        //获取快递列表
        $orderData[0]['express_list'] = array_values((new ExpressService())->getListWithoutPage());
        //返回待成团订单状态-1
        if ($orderData[0]['status'] == 1 && $orderData[0]['groups_id'] != 0 && $orderData[0]['groups_status'] == 1) {
            $orderData[0]['status'] = -1;
        }
        //返回状态内容及id
        $orderData[0]['text'] = OrderService::getStatusString($orderData[0]['status']);
        return $orderData[0];
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @param $wid 店铺id
     * @desc 发货订单后续操作
     * @update 何书哲 2020年03月07日 修改订单未发货商品数量问题
     */
    public function afterDeliveryOrder($oid, $wid, $odid)
    {
        $odids = explode(',', $odid);
        // 获取订单所有退款详情
        $orderDetail = OrderDetailService::init()->model->where('oid', $oid)->get(['id', 'product_id', 'product_prop_id'])->toArray();
        foreach ($orderDetail as $key => $val) {
            $refund = (new OrderRefundService())->init('oid', $oid)->where(['oid' => $oid, 'pid' => $val['product_id'], 'prop_id' => $val['product_prop_id']])->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                $odids[] = $val['id'];
            }
        }
        $where = ['oid' => $oid, 'is_delivery' => 0];
        $count = OrderDetailService::init()->model->where($where)->whereNotIn('id', $odids)->count();
        $orderData = OrderService::init()->getInfo($oid);
        if (!$count) {
            // 更改订单状态为已发货
            OrderService::init('wid', $wid)->where(['id' => $oid])->update(['status' => 2], false);
            // 添加商家发货日志
            $orderLog = [
                'oid' => $oid,
                'wid' => $wid,
                'mid' => $orderData['mid'],
                'action' => 3,
                'remark' => '商家发货',
            ];
            OrderLogService::init('wid', $wid)->add($orderLog, false);
        }
        OrderService::init()->upOrderLog($oid, $wid);
        // 发送发货通知
        if ($orderData) {
            // 发送微信发货提醒消息
            $orderData['odid'] = $odid;
            (new MessagePushModule($wid, MessagesPushService::DeliverySuccess))->sendMsg($orderData);
        }
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @param $wid 店铺id
     * @return null
     * @desc 获取订单详情
     */
    public function getOrderDetail($oid, $wid)
    {
        $res = $actionList = [];
        $orderWhere = [
            'id' => $oid,
            'wid' => $wid
        ];
        $query = OrderService::init()->model->where($orderWhere)
            ->select(['id', 'oid', 'wid', 'mid', 'type', 'status', 'groups_status', 'groups_id', 'created_at', 'buy_remark', 'seller_remark',
                'address_name', 'address_phone', 'address_detail', 'pay_price', 'freight_price', 'coupon_price', 'is_hexiao', 'refund_status'])
            ->first();
        if (!$query) {
            return false;
        }
        $orderData = $query->toArray();
        $res['id'] = $orderData['id'];
        //订单状态
        $res['status'] = $orderData['status'];
        $res['seller_remark'] = $orderData['seller_remark'];
        $detail_pic = config('sellerapp.detail_pic');
        $actionList = [];
        if ((new LogisticsService())->init()->model->where(['oid' => $oid, 'no_express' => 0])->first()) {
            $actionList[] = ['action' => 'wuliu', 'text' => '查看物流', 'img' => imgUrl() . $detail_pic['wuliu']];
        }
        if ($orderData['status'] == 0) {
            $res['status_string'] = '订单状态：买家待付款';
            $actionList[] = ['action' => 'gaijai', 'text' => '改价', 'img' => imgUrl() . $detail_pic['gaijia']];
            $actionList[] = ['action' => 'guanbi', 'text' => '关闭', 'img' => imgUrl() . $detail_pic['guanbi']];
            $actionList[] = ['action' => 'beizhu', 'text' => '备注', 'img' => imgUrl() . $detail_pic['beizhu']];
        } elseif ($orderData['status'] == 2) {
            $res['status_string'] = '订单状态：商家已发货';
            $actionList[] = ['action' => 'beizhu', 'text' => '备注', 'img' => imgUrl() . $detail_pic['beizhu']];
        } elseif ($orderData['status'] == 3 || $orderData['refund_status'] == 8) {
            $res['status_string'] = '订单状态：交易已完成';
            $actionList[] = ['action' => 'beizhu', 'text' => '备注', 'img' => imgUrl() . $detail_pic['beizhu']];
        } elseif ($orderData['status'] == 4) {
            $res['status_string'] = '订单状态：交易关闭';
            $actionList[] = ['action' => 'beizhu', 'text' => '备注', 'img' => imgUrl() . $detail_pic['beizhu']];
        } elseif ($orderData['status'] == 1 && $orderData['groups_id'] != 0 && $orderData['groups_status'] == 1) {
            $res['status'] = $orderData['status'] = -1;
            $res['status_string'] = '订单状态：买家已付款，等待成团';
            $actionList[] = ['action' => 'shichengtuan', 'text' => '使成团', 'img' => imgUrl() . $detail_pic['shichengtuan']];
            $actionList[] = ['action' => 'beizhu', 'text' => '备注', 'img' => imgUrl() . $detail_pic['beizhu']];
        } elseif ($orderData['status'] == 1) {
            $res['status_string'] = '订单状态：等待商家发货';
            $actionList[] = ['action' => 'fahuo', 'text' => '发货', 'img' => imgUrl() . $detail_pic['fahuo']];
            $actionList[] = ['action' => 'beizhu', 'text' => '备注', 'img' => imgUrl() . $detail_pic['beizhu']];
        }
        //支付时间
        $payWhere = ['oid' => $oid, 'wid' => $wid, 'action' => 2];
        $payTime = OrderLogService::init()->model->where($payWhere)->orderBy('created_at', 'desc')->select(['created_at'])->first();
        $orderData['pay_at'] = is_null($payTime) ? '' : $payTime->toArray()['created_at'];
        $orderData['is_pay'] = is_null($payTime) ? 0 : 1;
        // 物流信息
        if ($orderData['status'] == 2 || $orderData['status'] == 3) { //已发货或者已完成状态
            $logisticsData = (new LogisticsService())->init()->where(['oid' => $oid])->getInfo();
            if ($logisticsData) { //存在物流信息
                if ($logisticsData['no_express'] == 0) {
                    $res['no_express'] = 0;
                    $res['logistics'] = $logisticsData['express_name'] . ' 运单编号：' . $logisticsData['logistic_no'];
                    //获取物流最新信息
                    $logistics = (new LogisticsService())->getLogistics($oid);
                    if ($logistics && $logistics['success'] == 1 && !empty($logistics['data']) && !empty($logistics['data'][0]['data'])) {
                        $res['logistics_context'] = $logistics['data'][0]['data'][0]['context'];
                    } else {
                        $res['logistics_context'] = '';
                    }
                } else {
                    $res['no_express'] = 1;
                    $res['logistics'] = '';
                    $res['logistics_context'] = '';
                }
            } else {
                $res['no_express'] = 1;
                $res['logistics'] = '';
                $res['logistics_context'] = '';
            }
        }
        //团编号
        if ($orderData['groups_id'] != 0) {
            $groupsData = (new GroupsService())->getRowById($orderData['groups_id']);
            $orderData['identifier'] = $groupsData && isset($groupsData['identifier']) ? $groupsData['identifier'] : '';
        }
        //获取店铺名称、logo
        //$storeData = WeixinService::getStageShop($wid);
        $shopService = new ShopService();
        $storeData = $shopService->getRowById($wid);
        //获取订单详情及其退款信息
        $orderDetail = OrderDetailService::init()->model->where('oid', $oid)->get(['product_id', 'product_prop_id', 'title', 'img', 'price', 'num', 'spec'])->toArray();
        $is_refund = 0;
        foreach ($orderDetail as &$detail) {
            $detail['price'] = '￥' . strval($detail['price']);
            $detail['num'] = 'x' . strval($detail['num']);
            $refundData = (new OrderRefundService())->init('oid', $oid)->where(['oid' => $oid, 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
            if ($refundData && $refundData['status'] == 1) {
                $is_refund = 1;
            }
            $detail['status'] = $refundData ? $refundData['status'] : 0;
            //返回商品退款状态
            if (in_array($detail['status'], [1, 3, 4, 6, 7, 10])) {
                $detail['refund_status'] = '退款中';
            } elseif ($detail['status'] == 8) {
                $detail['refund_status'] = '退款成功';
            } elseif ($detail['status'] == 2) {
                $detail['refund_status'] = '已拒绝';
            } elseif ($detail['status'] == 5) {
                $detail['refund_status'] = '已取消';
            } elseif ($detail['status'] == 9) {
                $detail['refund_status'] = '已关闭';
            } else {
                $detail['refund_status'] = '';
            }
        }
        $res['is_refund'] = $is_refund;
        $res['address_name'] = $orderData['address_name'];
        $res['address_phone'] = $orderData['address_phone'];
        $res['address_detail'] = $orderData['address_detail'];
        $memberInfo = (new \App\S\Member\MemberService())->getRowById($orderData['mid']);
        $res['nickname'] = $memberInfo && isset($memberInfo['nickname']) ? $memberInfo['nickname'] : '';
        $res['detail_list'] = $orderDetail;
        $res['shop_name'] = $storeData['shop_name'] ?? '';
        $res['buy_remark'] = $orderData['buy_remark'];
        $res['pay_str'] = in_array($orderData['status'], [0, 4]) ? '需付款：￥' . $orderData['pay_price'] : '已付款：￥' . $orderData['pay_price'];
        $res['order_str'] = '￥' . $orderData['pay_price'] . '(包含运费￥' . $orderData['freight_price'] . ')';
        $res['recv_str'] = in_array($orderData['status'], [0, 4]) ? '应收：￥' . $orderData['pay_price'] : '实收：￥' . $orderData['pay_price'];
        $res['freight_price'] = '￥' . $orderData['freight_price'];
        //退款笔数
        $res['refund_num'] = (new OrderRefundService())->init()->model->where(['oid' => $oid, 'wid' => $wid])->whereNotIn('status', [2, 5, 8, 9])->count();
        $res['action_list'] = $actionList;
        $res['order_info']['oid'] = $orderData['oid'];
        if ($orderData['status'] == -1) {
            $res['order_info']['identifier'] = $orderData['identifier'];
        }
        $res['order_info']['created_at'] = $orderData['created_at'];
        if ($orderData['status'] != 0) {
            $res['order_info']['pay_at'] = $orderData['pay_at'];
        }

        return $res;
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @param $wid 店铺id
     * @param $pid 店铺id
     * @param $prop_id 店铺id
     * @return null
     * @desc 获取退款信息
     */
    public function getRefundOrder($oid, $pid, $prop_id = 0, $wid)
    {
        //获取订单买家电话
        $orderQuery = OrderService::init('wid', $wid)->model->where(['id' => $oid, 'wid' => $wid])->select(['id', 'address_phone', 'address_id'])->first();
        if (!$orderQuery) {
            apperror('订单不存在');
        }
        $orderData = $orderQuery->toArray();
        //获取退款信息
        $refundQuery = (new OrderRefundService())->init('oid', $oid)->model->where(['oid' => $oid, 'wid' => $wid, 'pid' => $pid, 'prop_id' => $prop_id])
            ->select(['id as refund_id', 'amount', 'type', 'reason', 'updated_at', 'status', 'created_at'])->first();
        if (!$refundQuery) {
            apperror('退款不存在');
        }
        $refundData = $refundQuery->toArray();

        //申请退款或者修改申请退款后(所以用updated_at) 商家7天不处理 自动同意退款
        $refundData['end_timestamp'] = strtotime($refundData['updated_at']) + 7 * 24 * 3600;
        $refundData['now_timestamp'] = time();
        //退款原因
        $refundData['reason'] = (new OrderRefundService())->getReasonString($refundData['reason']);
        //返回状态内容及id
        //$orderData['text'] = OrderService::getStatusString($orderData['status']);
        $refund_address = [];
        //获取退货地址
        $regionData = OrderService::init()->getDefaultAddress($orderData['address_id']);
        if ($refundData['type'] == 1 && in_array($refundData['status'], [3, 4, 7, 8, 10])) { //获取退货地址
            $regionData = OrderService::init()->getDefaultAddress($orderData['address_id']);
            if ($regionData) {
                $temp = [$regionData[0]['province_id'], $regionData[0]['city_id'], $regionData[0]['area_id']];
                $region = (new RegionService())->getListById($temp);
                $regionData[0]['address'] = $region[$regionData[0]['province_id']]['title'] . $region[$regionData[0]['city_id']]['title'] . $region[$regionData[0]['area_id']]['title'] . $regionData[0]['address'];
                unset($regionData[0]['province_id'], $regionData[0]['city_id'], $regionData[0]['area_id']);
                $refund_address = $regionData[0];
            } else {
                $refund_address['address'] = '';
            }
        }
        $status_list1 = [
            ['id' => 1, 'title' => '同意退货，等待买家发货'],
            ['id' => 2, 'title' => '买家已发货'],
            ['id' => 3, 'title' => '商家已收到退货确认退款'],
            ['id' => 4, 'title' => '已退款，银行受理中'],
            ['id' => 5, 'title' => '退款至买家账户']
        ];
        $status_list2 = [
            ['id' => 1, 'title' => '商家同意退款'],
            ['id' => 2, 'title' => '已退款，银行受理中'],
            ['id' => 3, 'title' => '退款至买家账户']
        ];
        //返回title
        $message_list = OrderService::init()->getRefundMessageDate($refundData['refund_id']);
        foreach ($message_list as $key => $val) {
            $message_list[$val['status']][] = $val;
            unset($message_list[$key]);
        }
        if ($refundData['status'] == 1) {
            $refundData['title'] = '等待商家处理退款申请';
        } elseif ($refundData['status'] == 2) {
            $refundData['title'] = '商家拒绝退款';
        } elseif ($refundData['status'] == 5) {
            $refundData['title'] = '买家取消退款';
        } elseif ($refundData['status'] == 9) {
            $refundData['title'] = '退款申请关闭';
        }
        if ($refundData['type'] == 1) {
            if ($refundData['status'] == 6) {
                $refundData['title'] = $status_list1[0]['title'];
                $refundData['title_id'] = 1;
                $status_list1[0]['date'] = !empty($message_list[5]) ? $message_list[5][0]['created_at'] : '';
            } elseif ($refundData['status'] == 7) {
                $refundData['title'] = $status_list1[1]['title'];
                $refundData['title_id'] = 2;
                $status_list1[0]['date'] = !empty($message_list[5]) ? $message_list[5][0]['created_at'] : '';
                $status_list1[1]['date'] = !empty($message_list[6]) ? $message_list[6][0]['created_at'] : '预计2~7个工作日';
            } elseif ($refundData['status'] == 3 || $refundData['status'] == 10) {
                $refundData['title'] = $status_list1[2]['title'];
                $refundData['title_id'] = 3;
                $status_list1[0]['date'] = !empty($message_list[5]) ? $message_list[5][0]['created_at'] : '';
                $status_list1[1]['date'] = !empty($message_list[6]) ? $message_list[6][0]['created_at'] : '';
                $status_list1[2]['date'] = !empty($message_list[2]) ? $message_list[2][0]['created_at'] : '';
            } elseif ($refundData['status'] == 4) {
                $refundData['title'] = $status_list1[3]['title'];
                $refundData['title_id'] = 4;
                $status_list1[0]['date'] = !empty($message_list[5]) ? $message_list[5][0]['created_at'] : '';
                $status_list1[1]['date'] = !empty($message_list[6]) ? $message_list[6][0]['created_at'] : '';
                $status_list1[2]['date'] = !empty($message_list[2]) ? $message_list[2][0]['created_at'] : '';
                $status_list1[3]['date'] = '预计2~7个工作日';
            } elseif ($refundData['status'] == 8) {
                $refundData['title'] = $status_list1[4]['title'];
                $refundData['title_id'] = 5;
                $status_list1[0]['date'] = !empty($message_list[5]) ? $message_list[5][0]['created_at'] : '';
                $status_list1[1]['date'] = !empty($message_list[6]) ? $message_list[6][0]['created_at'] : '';
                $status_list1[2]['date'] = !empty($message_list[2]) ? $message_list[2][0]['created_at'] : '';
                $status_list1[3]['date'] = '';
                $status_list1[4]['date'] = !empty($message_list[7]) ? $message_list[7][0]['created_at'] : '';;
            }
            $refundData['status_list'] = $status_list1;
        } else {
            if ($refundData['status'] == 4) {
                $refundData['title'] = $status_list2[1]['title'];
                $refundData['title_id'] = 2;
                $status_list2[0]['date'] = !empty($message_list[2]) ? $message_list[2][0]['created_at'] : '';
                $status_list2[1]['date'] = '预计2~7个工作日';
            } elseif ($refundData['status'] == 8) {
                $refundData['title'] = $status_list2[2]['title'];
                $refundData['title_id'] = 3;
                $status_list2[0]['date'] = !empty($message_list[2]) ? $message_list[2][0]['created_at'] : '';
                $status_list2[1]['date'] = '';
                $status_list2[2]['date'] = !empty($message_list[7]) ? $message_list[7][0]['created_at'] : '';
            } elseif (!in_array($refundData['status'], [1, 2, 5, 9])) {
                $refundData['title'] = $status_list2[0]['title'];
                $refundData['title_id'] = 1;
                $status_list2[0]['date'] = !empty($message_list[2]) ? $message_list[2][0]['created_at'] : '';
            }
            $refundData['status_list'] = $status_list2;
        }
        unset($refundData['updated_at'], $orderData['groups_id'], $orderData['groups_status'], $orderData['address_id']);
        $orderData = array_merge($orderData, $refundData, $refund_address);
        return $orderData;
    }

    /**
     * @author hsz
     * @param $refund_id 退款id
     * @param $wid 店铺id
     * @return null
     * @desc 获取协商记录
     */
    public function getConsultList($refund_id, $wid)
    {
        //获取退款信息
        $refundQuery = (new OrderRefundService())->init()->model->where(['id' => $refund_id, 'wid' => $wid])->select(['id as refund_id', 'oid', 'pid', 'prop_id', 'mid', 'amount', 'return_freight', 'type', 'imgs'])->first();
        if (!$refundQuery) {
            apperror('退款不存在');
        }
        $refundData = $refundQuery->toArray();
        $refundData['imgs'] = explode(',', $refundData['imgs']);
        //获取订单编号、创建时间
        $orderQruey = OrderService::init('wid', $wid)->model->where(['id' => $refundData['oid'], 'wid' => $wid])->select(['id', 'oid', 'mid', 'created_at', 'address_id'])->first();
        if (!$orderQruey) {
            apperror('订单不存在');
        }
        $orderData = $orderQruey->toArray();
        //获取订单详情
        $orderDetailQuery = OrderDetailService::init('oid', $refundData['oid'])->model->where(['oid' => $refundData['oid'], 'product_id' => $refundData['pid'], 'product_prop_id' => $refundData['prop_id']])->select(['product_id', 'title', 'img', 'price', 'num', 'spec', 'product_prop_id', 'is_delivery'])->first();
        if (!$orderDetailQuery) {
            apperror('商品不存在');
        }
        $orderDetailData = $orderDetailQuery->toArray();
        //获取退款协商留言表
        $orderRefundMessage = (new OrderRefundMessageService())->init('wid', $wid)->model->where(['refund_id' => $refund_id])->select(['is_seller', 'status', 'content', 'express_name', 'express_no', 'phone', 'created_at'])->orderBy('created_at', 'desc')->get()->toArray();
        foreach ($orderRefundMessage as $key => &$val) {
            if (empty($val['content'])) {
                unset($orderRefundMessage[$key]);
                continue;
            }
            if ($val['status'] == 4) {//买家修改退款申请
                //修改申请 获取修改后的退款申请
                $val['refund'] = $refundData;
            } elseif ($val['status'] == 5) {//卖家同意退货
                //获取退货地址
                $regionData = OrderService::init()->getDefaultAddress($orderData['address_id']);
                if ($regionData) {
                    $temp = [$regionData[0]['province_id'], $regionData[0]['city_id'], $regionData[0]['area_id']];
                    $region = (new RegionService())->getListById($temp);
                    $regionData[0]['address'] = $region[$regionData[0]['province_id']]['title'] . $region[$regionData[0]['city_id']]['title'] . $region[$regionData[0]['area_id']]['title'] . $regionData[0]['address'];
                    unset($regionData[0]['province_id'], $regionData[0]['city_id'], $regionData[0]['area_id']);
                    $val['refund_address'] = $regionData[0];
                } else {
                    $val['refund_address'] = (new WeixinRefundService())->getDefaultAddress($wid, (new UserService())->getInfoByWid($wid));
                }
            }
            $val['status_string'] = (new OrderRefundMessageService())->getStatusString($val['status']);
            //content如果是json
            if (preg_match('/{.+}/', $val['content'])) {
                $content = json_decode($val['content'], true);
                $val['content'] = $content['content'];
            }
        }
        $orderData = array_merge($orderData, $orderDetailData);
        $orderData['list'] = array_merge([], $orderRefundMessage);
        return $orderData;
    }

    /**
     * @author hsz
     * @param $oid 订单id
     * @param $wid 店铺id
     * @param $refund_id 退款id
     * @param $remark 备注说明
     * @return null
     * @desc 拒绝申请
     */
    public function refundDisagree($oid, $refund_id, $wid, $remark)
    {
        $orderQuery = OrderService::init('wid', $wid)->model->where(['id' => $oid, 'wid' => $wid])->first();
        if (!$orderQuery) {
            apperror('订单不存在');
        }
        $orderRefundService = new OrderRefundService();
        $orderRefundQuery = $orderRefundService->init('oid', $oid)->model->where(['id' => $refund_id, 'oid' => $oid])->select(['mid', 'status'])->first();
        if (!$orderRefundQuery) {
            apperror('退款不存在');
        }
        $orderRefundData = $orderRefundQuery->toArray();
        if ($orderRefundData['status'] != 1) {
            apperror('不在可拒绝退款范围状态');
        }
        //更新退款状态
        $orderRefundService->init('oid', $oid)->where(['id' => $refund_id])->update(['status' => 2, 'remark' => $remark], false);
        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $flag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 2) {
                $flag = false;
                break;
            }
        }
        if ($flag) {
            OrderService::init('wid', $wid)->where(['id' => $oid])->update(['refund_status' => 2], false);
        }
        //添加一条协商留言
        $data = [
            'mid' => $orderRefundData['mid'],
            'wid' => $wid,
            'refund_id' => $refund_id,
            'is_seller' => 1,
            'status' => 1,
            'content' => $remark
        ];
        (new OrderRefundMessageService())->addMessage($data);
        //添加订单日志表记录
        $log = [
            'oid' => $oid,
            'wid' => $wid,
            'mid' => $orderRefundData['mid'],
            'action' => 9,
            'remark' => '商家拒绝退款'
        ];
        OrderLogService::init()->add($log, false);
        OrderService::upOrderLog($oid, $wid);
    }

    /**
     * @author hsz
     * @param $wid 店铺id
     * @param $refund_id 退款id
     * @return null
     * @desc 同意退货（获取退货地址）
     */
    public function getRefundAddress($refund_id, $wid)
    {
        $orderRefundService = new OrderRefundService();
        $orderRefundQuery = $orderRefundService->init()->model->where(['id' => $refund_id])->select(['mid', 'status', 'oid', 'type'])->first();
        if (!$orderRefundQuery) {
            apperror('退款id不存在');
        }
        $orderRefundData = $orderRefundQuery->toArray();
        if ($orderRefundData['status'] != 1) {
            apperror('不在申请退款中状态');
        }
        if ($orderRefundData['type'] == 0) {
            apperror('仅支持退货退款类型');
        }
        $orderQuery = OrderService::init('wid', $wid)->model->where(['id' => $orderRefundData['oid'], 'wid' => $wid])->first();
        if (!$orderQuery) {
            apperror('订单不存在');
        }
        //获取退货地址
        $list = OrderService::init('wid', $wid)->getRefundAddress($wid);
        if (!empty($list)) {
            $flag = 0;
            foreach ($list as $key => &$val) {
                $temp = [$val['province_id'], $val['city_id'], $val['area_id']];
                $region = (new RegionService())->getListById($temp);
                $val['address'] = $region[$val['province_id']]['title'] . $region[$val['city_id']]['title'] . $region[$val['area_id']]['title'] . $val['address'];
                if ($flag && $val['is_default'] == 1) {
                    $val['is_default'] = 0;
                }
                if ($val['is_default'] == 1) {
                    $flag = 1;
                }
                unset($val['province_id'], $val['city_id'], $val['area_id']);
            }
        }
        return $list;
    }

    /**
     * @author hsz
     * @param $wid 店铺id
     * @param $refund_id 退款id
     * @param $oid 订单id
     * @param $mid 买家id
     * @return null
     * @desc 发送退货地址
     */
    public function setRefundAddress($oid, $refund_id, $wid, $mid)
    {
        $orderRefundService = new OrderRefundService();
        //更改退货状态
        $orderRefundService->init()->where(['id' => $refund_id])->update(['status' => 6], false);
        //一个订单中的所有退款商品都同意退款 订单状态才改为同意退款 否则整个订单状态不变
        //当且仅当一个订单中的所有商品都退款完成 才设置订单表的退款状态refund_status=4或8
        list($refunds) = $orderRefundService->init('oid', $oid)->where(['1' => 1])->getList(false);
        $flag = true;
        foreach ($refunds['data'] as $v) {
            if ($v['status'] != 6) {
                $flag = false;
                break;
            }
        }
        if ($flag) {
            OrderService::init('wid', $wid)->where(['id' => $oid])->update(['refund_status' => 6], false);
        }
        //添加一条协商留言
        $data = [
            'mid' => $mid,
            'wid' => $wid,
            'refund_id' => $refund_id,
            'is_seller' => 1,
            'status' => 5,
            'content' => ''
        ];
        (new OrderRefundMessageService())->addMessage($data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180327
     * @desc 获取留言订单详情
     */
    public function getOrderReMarkByGids($gids, $type = '')
    {
        if (!$gids) {
            return [];
        }
        if ($type == 1) {
            $sql = 'SELECT o.id,o.oid,o.status,o.groups_id,od.remark_no,o.groups_status,o.refund_status,o.created_at FROM ds_order_detail as od LEFT JOIN ds_order as o ON od.oid=o.id LEFT JOIN ds_groups_detail as gd ON o.id=gd.oid  WHERE o.groups_id in(' . implode(',', $gids) . ')';
        } else {
            $sql = 'SELECT o.id,o.oid,o.status,o.groups_id,od.remark_no,o.groups_status,o.refund_status,o.created_at FROM ds_order_detail as od LEFT JOIN ds_order as o ON od.oid=o.id LEFT JOIN ds_groups_detail as gd ON o.id=gd.oid  WHERE o.groups_id in(' . implode(',', $gids) . ') limit 0,100';
        }
        $request = app('request');
        if ($request->input('oid')) {
            $sql .= ' and o.oid=' . $request->input('oid');
        }
        if ($request->input('groups_status')) {
            $sql .= 'and groups_status=' . $request->input('groups_status');
        }
        $res = DB::select($sql);
        $orderData = json_decode(json_encode($res), true);
        if (!$orderData) {
            return [];
        }
        $ids = array_column($orderData, 'id');
        $remardNOs = array_column($orderData, 'remark_no');
        $res = (new GroupsDetailService())->model->whereIn('oid', $ids)->get()->toArray();
        $groupsDetail = [];
        foreach ($res as $val) {
            $groupsDetail[$val['oid']] = $val;
        }
        $res = (new RemarkService())->model->whereIn('remark_no', $remardNOs)->get()->toArray();
        $remark = [];
        foreach ($res as $val) {
            $remark[$val['remark_no']][] = $val;
        }
        foreach ($orderData as &$item) {
            $item['groupsDetail'] = $groupsDetail[$item['id']] ?? [];
            $item['remark'] = $remark[$item['remark_no']] ?? [];
        }
        if ($type == 1) {
            $exportData = [];
            $data['title'] = [
                'id' => '订单序号',
                'oid' => '订单号',
                'groups_id' => '团ID',
                'status' => '状态(0待付款；1待发货；2已发货（待收货）；3已完成；4已关闭; 7待抽奖)',
                'remark_no' => '团编号',
                'groups_status' => '团状态(0：非团购，1:待成团，2：已成团，3：未成团)',
                'member_id' => '用户id',
                'is_head' => '是否是团长',
                'created_at' => '订单时间',
                'refund_status' => '退款状态(0非退款状态；1申请退款中；2申请退款被拒；3退款中（商家同意退款）；4退款完成（微信支付处理中）；5买家取消退款；6商家同意退货；7买家填写退货信息完成；8退款到账（微信支付审核通过，原路退回）；9退款申请关闭（退货申请超时未发货，商家拒绝未继续修改等操作），10商家未及时处理自动同意退款)'
            ];
            foreach ($orderData as $key => $val) {
                $tmp = [
                    'id' => $val['id'],
                    'oid' => strval($val['oid']),
                    'groups_id' => $val['groups_id'],
                    'refund_status' => $val['refund_status'],
                    'status' => $val['status'],
                    'remark_no' => $val['remark_no'],
                    'member_id' => $val['groupsDetail']['member_id'] ?? '',
                    'is_head' => $val['groupsDetail']['is_head'] ?? '',
                    'created_at' => $val['created_at'],
                ];
                switch ($val['groups_status']) {
                    case  '0':
                        $tmp['groups_status'] = '非团购';
                        break;
                    case  '1':
                        $tmp['groups_status'] = '待成团';
                        break;
                    case  '2':
                        $tmp['groups_status'] = '已成团';
                        break;
                    case  '3':
                        $tmp['groups_status'] = '未成团';
                        break;
                }
//                show_debug($val);
                foreach ($val['remark'] as $k => $v) {
                    $tmp[$v['title']] = $v['content'];
                    $data['title'][$v['title']] = $v['title'];
                }
                $exportData[] = $tmp;
            }
            $data['data'] = $exportData;
            (new ExportModule())->derive($data, '团购导出');
        }
        return $orderData;
    }

    /**
     * 统计店铺订单数据 脚本执行
     * @update 梅杰 增加退款订单数统计 at 2019年08月19日 16:00:52
     */
    public function orderStatistics()
    {
        //查询所有店铺 统计到昨天为止的数据
        DB::table('weixin')->select('id', 'uid')
            ->whereNull('deleted_at')
            ->chunk(100, function ($shops) {
                foreach ($shops as $shop) {
                    try {
                        //最终数组
                        $array = [];

                        //统计的截止时间
                        $yesterday = date('Y-m-d', strtotime('-1 days')) . ' 23:59:59';
                        $where = [
                            'wid' => $shop->id,
                            'created_at' => ['<=', $yesterday]
                        ];

                        //查询最大时间 只统计最大时间之后的订单数据
                        $next_day = '2010-01-01 00:00:00';
                        $connect = DB::connection('mysql_dc_log');
                        $res = $connect->select("select max(created_at) as max_time from dc_order where wid = " . $shop->id);
                        if (!empty($res[0]->max_time)) {
                            $next_day = date('Y-m-d H:i:s', strtotime("+1 day", $res[0]->max_time));
                            $where['created_at'] = ['between', [$next_day, $yesterday]];
                        }

                        //查询的字段 根据日期
                        $date_string = "DATE_FORMAT(created_at,'%Y-%m-%d') AS date_string";
                        $sum_string = "SUM(pay_price) AS order_amount";
                        $count_string = "COUNT(1) AS order_count";
                        $member_string = "COUNT(DISTINCT mid) AS member_count";

                        //所有订单数据
                        $all_status_order = OrderService::init('wid', $shop->id)
                            ->model
                            ->select(DB::raw($date_string . "," . $sum_string . "," . $count_string . "," . $member_string))
                            ->wheres($where)
                            ->groupBy('date_string')
                            ->get()
                            ->toArray();

                        //所有支付订单数据
                        $where['status'] = ['in', [1, 2, 3, 7]];
                        $paid_order = OrderService::init('wid', $shop->id)
                            ->model
                            ->select(DB::raw($date_string . "," . $sum_string . "," . $count_string . "," . $member_string))
                            ->wheres($where)
                            ->groupBy('date_string')
                            ->get()
                            ->toArray();

                        //付款订单商品总件数
                        $select = DB::table('order as o')
                            ->select(DB::raw("DATE_FORMAT(ds_o.created_at,'%Y-%m-%d') AS date_string,SUM(ds_d.num) AS product_count"))
                            ->leftJoin('order_detail as d', 'd.oid', '=', 'o.id')
                            ->where('wid', $shop->id)
                            ->whereIn('status', [1, 2, 3, 7]);

                        if (!empty($res[0]->max_time)) {
                            $select = $select->where('o.created_at', '>=', $next_day);
                        }

                        $product_count = $select->where('o.created_at', '<=', $yesterday)
                            ->groupBy('date_string')
                            ->get()
                            ->toArray();

                        //支出 (目前只统计退款完成)
                        $where_refund = [
                            'wid' => $shop->id,
                            'created_at' => ['<=', $yesterday],
                            'status' => ['in', [4, 8]]
                        ];

                        if (!empty($res[0]->max_time)) {
                            $where_refund['created_at'] = ['between', [$next_day, $yesterday]];
                        }

                        $refund_amount = (new OrderRefundService())->init()
                            ->model
                            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') AS date_string,SUM(amount) AS refund_amount,COUNT(*) as refund_count"))
                            ->wheres($where_refund)
                            ->groupBy('date_string')
                            ->get()
                            ->toArray();

                        //组装数据 更新到dc库
                        foreach ($all_status_order as $v) {
                            $v['order_amount_paid'] = 0.00;
                            $v['order_count_paid'] = 0;
                            $v['member_count_paid'] = 0;
                            $v['refund_amount'] = 0.00;
                            $v['product_count'] = 0;
                            $v['refund_count'] = 0;
                            $array[$v['date_string']] = $v;
                        }

                        foreach ($paid_order as $v) {
                            $array[$v['date_string']]['order_amount_paid'] = $v['order_amount'];
                            $array[$v['date_string']]['order_count_paid'] = $v['order_count'];
                            $array[$v['date_string']]['member_count_paid'] = $v['member_count'];
                        }

                        foreach ($product_count as $v) {
                            $array[$v->date_string]['product_count'] = $v->product_count;
                        }

                        foreach ($refund_amount as $k => $v) {
                            $refund_amount[$v['date_string']] = $v;
                            unset($refund_amount[$k]);
                        }

                        foreach ($array as $k => $v) {
                            if (!empty($refund_amount[$v['date_string']])) {
                                $array[$k]['refund_amount'] = $refund_amount[$v['date_string']]['refund_amount'];
                                $array[$k]['refund_count'] = $refund_amount[$v['date_string']]['refund_count'];
                            }
                        }

                        foreach ($refund_amount as $k => $v) {
                            if (empty($array[$k])) {
                                $tmp = [
                                    'order_amount' => 0.00,
                                    'order_count' => 0,
                                    'member_count' => 0,
                                    'order_amount_paid' => 0.00,
                                    'order_count_paid' => 0,
                                    'member_count_paid' => 0,
                                    'product_count' => 0,
                                    'refund_amount' => $v['refund_amount'],
                                    'date_string' => $v['date_string'],
                                    'refund_count' => $v['refund_count']
                                ];
                                $array[$k] = $tmp;
                            }
                        }

                        //更新到dc库
                        //拼接insert语句 1000条数据一个sql
                        if ($array) {
                            $slice_length = 1000;
                            $max = intval(ceil(count($array) / $slice_length));
                            $fields = "wid,order_amount,order_count,order_user_count,order_payed_amount,order_payed_count,order_payed_user_count,order_payed_goods_count,order_pay,created_at,refund_order_count";
                            for ($i = 0; $i < $max; $i++) {
                                $sql_array = array_slice($array, $i * $slice_length, $slice_length);
                                $sql = '';
                                foreach ($sql_array as $v) {
                                    $sql && $sql .= ',';
                                    $sql .= "(" . $shop->id . "," . $v['order_amount'] . "," . $v['order_count'] . "," . $v['member_count'] . "
                                ," . $v['order_amount_paid'] . "," . $v['order_count_paid'] . "," . $v['member_count_paid'] . "," . $v['product_count'] . "
                                ," . $v['refund_amount'] . "," . strtotime($v['date_string']) . "," . $v['refund_count'] . ")";
                                }
                                $res = $connect->update("INSERT INTO dc_order (" . $fields . ") VALUES " . $sql);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                        continue;
                    }
                }
            });

        Log::info('统计店铺订单数据脚本执行完成');
    }

    /**
     * 统计拼团数据 脚本执行
     * @param null
     * @return void
     * @create 何书哲 2018年7月9日
     * @update 梅杰 20180716 拼团统计类型优化
     */
    public function orderGroupsStatistics()
    {
        DB::table('groups_detail')
            ->orderBy('created_at', 'asc')
            ->chunk(500, function ($groupsDetail) {
                try {
                    $sqlStart = "INSERT INTO dc_groups(`wid`,`oid`,`rule_id`,`group_id`,`mid`,`discount_money`,`type`,`created_at`) VALUES";
                    $sql = $sql1 = "";
                    $sql1Start = "INSERT INTO dc_groups_complete(`group_id`,`complete_time`) VALUES";

                    foreach ($groupsDetail as $groupDetail) {
                        //获取拼团数据
                        $groupsData = (new GroupsService())->getRowById($groupDetail->groups_id);
                        if ($groupsData) {
                            //获取是否是团长
                            $type = $groupDetail->is_head ? 1 : 2;
                            $discountMoney = $this->computeDiscount($groupDetail->oid);
                            $sql .= "('" . $groupsData['wid'] . "'," . "'" . $groupDetail->oid . "'," . "'" . $groupsData['rule_id'] . "'," . "'" . $groupsData['id'] . "'," . "'" . $groupDetail->member_id . "'," . "'" . $discountMoney . "'," . "'" . $type . "'," . "'" . strtotime($groupsData['created_at']) . "'),";
                            //如果已成团，发送成团数据
                            if ($groupsData['status'] == 2) {
                                $sql1 .= "('" . $groupDetail->groups_id . "'," . "'" . strtotime($groupsData['complete_time']) . "'),";
                            }
                        } else {
                            \Log::Info($groupDetail->groups_id);
                        }
                    }
                    if ($sql) {
                        $sql = substr($sql, 0, -1);
                        $sql .= "ON DUPLICATE KEY UPDATE wid = VALUES(wid), oid = VALUES(oid), rule_id = VALUES(rule_id), group_id = VALUES(group_id), mid = VALUES(mid),  discount_money = VALUES(discount_money), type = VALUES(type), created_at = VALUES(created_at)";
                        $sqlEnd = $sqlStart . $sql;
                        DB::connection('mysql_dc_order_log')->insert($sqlEnd);
                    }

                    if ($sql1) {
                        $sql1 = substr($sql1, 0, -1);
                        $sql1 .= "ON DUPLICATE KEY UPDATE group_id = VALUES(group_id), complete_time = VALUES(complete_time)";
                        $sql1End = $sql1Start . $sql1;
                        DB::connection('mysql_dc_order_log')->insert($sql1End);
                    }

                } catch (\Exception $e) {
                    \Log::info('第' . __LINE__ . '行： ' . $e->getMessage());
                    \Log::info($sqlEnd);
                    \Log::info($sql1End);
                }
            });
    }


    /**
     * 获取拼团优惠金额
     * @param $oid
     * @return int
     * @author: 梅杰 20180717
     */
    public function computeDiscount($oid)
    {
        $orderData = OrderService::init()->model->select(['pay_price'])->find($oid);
        $detailData = OrderDetailService::init()->model->where(['oid' => $oid])->get(['price', 'num'])->toArray();
        $sum = 0;
        foreach ($detailData as $val) {
            $sum = $sum + ($val['price'] * $val['num']);
        }
        $discount = $sum - $orderData->pay_price ?? 0;
        return $discount;
    }

    /**
     * 批量发货
     * @param $wid
     * @param $orderData
     * @author: 梅杰 2018年8月29日
     * @return array
     */
    public function BatchDelivery($wid, $orderData)
    {
        //导入的数据订单号
        $oids = array_values(array_unique(array_column($orderData, 0)));
        $orderList = OrderService::init()->with(['orderDetail'])->where(['wid' => $wid, 'status' => 1, 'oid' => ['in', $oids]])->getList(false);
        $usefulOrderData = $orderList[0]['data'];
        //正确的订单号结果集
        $usefulOrderIds = array_column($usefulOrderData, 'oid');
        //获取错误的订单号
        $errOrderIds = array_values(array_diff($oids, $usefulOrderIds));
        $NotificationFlag = (new NotificationService())->checkIfSubscribed(5, $wid);
        $insertLog = [];
        foreach ($orderData as $k => $v) {
            if (in_array($v[0], $errOrderIds)) {
                $insertLog[] = [
                    'oid' => $v[0],
                    'express_no' => $v[2],
                    'express_name' => $v[1],
                    'status' => 0,
                    'created_at' => date("Y-m-d H:i:s", time()),
                    'wid' => $wid,
                    'err_msg' => '未发货订单信息不存在'
                ];
                unset($orderData[$k]);
                continue;
            }
            $job = (new BatchDelivery($v, $wid, $NotificationFlag));
            dispatch($job);
        }
        //批量插入日志
        if ($insertLog && \Illuminate\Support\Facades\DB::table('batch_delivery_log')->insert($insertLog) === false) {
            \Log::info('日志插入失败');
        }
        return [
            'success' => array_slice($usefulOrderIds, 0, 10),
            'error' => array_slice($errOrderIds, 0, 10)
        ];
    }


    /**
     * @desc 获取修改订单redis key值
     * @param $oid 订单id
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 08 月 18 日
     */
    public static function changeSendOrderAddrKey($oid)
    {
        return 'order:change:orderaddress:oid:' . $oid;
    }

    /**
     * @desc 获取修改包裹信息的key
     * @param $id 物流包裹id
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 08 月 18 日
     */
    public static function modifyLogisticsKey($id)
    {
        return 'order:logistics:id:' . $id;
    }


}
