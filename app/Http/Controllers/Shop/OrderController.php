<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 13:43
 */

namespace App\Http\Controllers\shop;
use App\Http\Controllers\Controller;
use App\Jobs\Distribution;
use App\Lib\Redis\SeckillSku;
use App\Model\Cart;
use App\Model\GroupsDetail;
use App\Model\MemberAddress;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderLog;
use App\Module\AliApp\AliClientModule;
use App\Module\AliApp\AlipayTradeAppPayRequest;
use App\Module\BindMobileModule;
use App\Module\DiscountModule;
use App\Module\GroupsRuleModule;
use App\Module\MessagePushModule;
use App\Module\OrderModule;
use App\Module\RefundModule;
use App\Module\SeckillModule;
use App\Module\StoreModule;
use App\S\Groups\GroupsDetailService;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Model\Product;
use App\Model\ProductSku;
use App\S\Foundation\RegionService;
use App\S\Market\CouponLogService;
use App\S\Market\SeckillService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductSkuService;
use App\S\Weixin\DeliveryConfigService;
use App\Services\Order\LogisticsService;
use App\Services\Order\OrderRefundService;
use App\Services\OrderRefundMessageService;
use App\Services\Shop\CartService;
use Carbon\Carbon;
use OrderDetailService,OrderService;
use Psy\Command\ShowCommand;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Validator;
use ProductEvaluateService;
use OrderLogService;
use ProductService;
use MemberCardService,WeixinService;
use App\S\File\FileInfoService;
use App\Jobs\Delay\CancelNonPaymentSeckillOrder;
use App\Jobs\Delay\CancelNonPaymentCommonOrder;
use DB;
use Illuminate\Http\Request;
use Log;
use MemberAddressService;
use MemberCardRecordService;
use OrderCommon;
use OrderPointExtraRuleService as OOrderPointExtraRuleService;
use OrderPointRuleService as OOrderPointRuleService;
use PaymentService;
use PointApplyRuleService as OPointApplyRuleService;
use PointRecordService as OPointRecordService;
use WeixinService as OWeixinServie;
use App\Module\NotificationModule;
use App\S\PublicShareService;
use App\S\Lift\ReceptionService;
use App\S\Order\OrderZitiService;
use App\S\Cam\CamListService;
use App\S\Cam\CamActivityService;
use App\S\Weixin\ShopService;
use App\Module\CommonModule;
use App\Services\Permission\WeixinUserService;

class OrderController extends Controller
{

    public function __construct(MemberService $memberService) {
        $this->memberService = $memberService;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170406
     * @desc  订单列表页
     * @param Request $request
     * @param $wid
     */
    public function index(Request $request,$wid)
    {
        $status = $request->input('status');
        $where = [
            'mid'	=> session('mid'),
            'wid'  => $wid
        ];
        if (isset($status) && $status != ''){
            if ($status == -1){
                $oids = (new GroupsDetailService())->getOrder(session('mid'));
                $where['id'] = ['in',$oids];
            }elseif($status == 1){
                $where['status'] = $status;
                $where['groups_status'] = ['<>',1];
            }else{
                $where['status'] = $status;
            }
		}
        //添加通用分享
        $shareData = (new PublicShareService())->publicShareSet($wid);
        $page = $request->input('page')?$request->input('page'):1;
        $pagesize = config('database.perPage');
        $offset = ($page-1)*$pagesize;
        if ($request->input('status') == 3) {
            $where['status'] = $status;
            $where['ievaluate'] = 0;
            $orderData = OrderService::init('wid',$wid)->model->wheres($where)->orderBy('id','desc')->skip($offset)->take($pagesize)->get() ->load('orderDetail')->load('weixin')->load('orderLog')->toArray();
        }else {
            $orderData = OrderService::init('wid',$wid)->model->wheres($where)->orderBy('id','desc')->skip($offset)->take($pagesize)->get() ->load('orderDetail')->load('weixin')->load('orderLog')->toArray();
        }

        $gids = [];
        foreach ($orderData as &$val){
            $val['count'] = count($val['orderDetail']);
            $val['evaluate'] = 1;
            $gids[] = $val['groups_id'];

            foreach ($val['orderDetail'] as $v)
            {
                if ($v['is_evaluate'] == 0){
                    $val['evaluate'] = 0;
                    break;
                }
            }
        }
        //处理团购数据
        $ruleData =  [];
        if ($gids){
            $ruleData = (new GroupsService())->getListById($gids);
            /*获取拼团规则数据 add by wuxiaoping 2017.11.16*/
            foreach ($ruleData as $key => &$value) {
                $value['groupsRuleData'] = (new GroupsRuleService())->getRowById($value['rule_id']);
            }
            $ruleData = (new GroupsRuleModule())->dealKey($ruleData);

        }

        foreach ($orderData as &$item){
            //是否需要物流 add MayJay
            $item['no_express'] = 0;
            $re = ( new LogisticsService())->init()->where(['oid'=>$item['id']])->getInfo();
            if($re){
                $item['no_express'] = $re['no_express'];
            }
            //end

            $orderGroupsData[$item['groups_id']][] = $item;
            $item['rule_id'] = $ruleData[$item['groups_id']]['rule_id']??0;
            /*获取拼团规则数据 add by wuxiaoping 2017.11.16*/
            $item['is_open_draw'] = $ruleData[$item['groups_id']]['groupsRuleData']['is_open_draw']??0;
            if($item['is_open_draw'] == 1){   //开启抽奖时，如果未满足成团人数与普通拼团流程一致（未成团）
                if ($ruleData[$item['groups_id']]['status'] == 3) {
                    $item['is_open_draw'] = 0;
                }
            }
        }
        if ($request->isMethod('post')){
            success('','',$orderData);
        }else{
            return view('shop.order.index2', [
                'title'     => '订单列表',
                'orderData' => $orderData,
                'status'    => $status,
                'shareData' => $shareData,
                'takeAwayConfig' => (new StoreModule())->getWidTakeAway($wid) ? 1 : 0
            ]);
        }

    }

    /**
     * todo 等待支付订单信息
     * @param Request $request
     * @param CartService $cartService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-05
     * 何书哲 2018年7月30日 支付宝小程序通过mid获取收货地址
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月16日 百度小程序来源处理
     * @update 何书哲 2018年11月21日 添加是否外卖店铺
     */
    public  function waitPayOrder(Request $request,CartService $cartService,RegionService $regionService,CouponLogService $couponLogService,ShopService $shopService)
    {
        //定义返回数据数组
        $returnData = array('errCode' => 0, 'errMsg' => '','data'=>[]);
        $wid=session('wid');
        $mid=session('mid');
        if(empty($wid)||empty($mid))
        {
            error('登录超时');
        }
        //接收购物车数据id //上线默认值要删除
        $id=$request->input('cart_id');
        if(empty($id))
        {
            error('请选择购物车中的商品');
        }
        try {
            /*ios手机会把card_id参数进行转义处理，所以链接地址添加了转码 wuxiaoping 2018.01.01*/
            $id = urldecode($id);
            $id = json_decode($id, true);
            if (empty($id)) {
                error('数据存在异常');
            }
        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            error($message);
        }
        $memberPointData = $this->memberService->getRowById($mid);

        //$store = WeixinService::getStageShop($wid);
        $store = $shopService->getRowById($wid);
        $shopName='';
        if(!empty($store))
        {
            $shopName=$store['shop_name'];
        }
        $shopUrl=config('app.url').'shop/index/'.$wid;

        //cwh 2017.12.5===============
        
        $address_id = $request->input('address_id', 0);

        //用户收货地址信息
        //何书哲 2018年7月30日 支付宝小程序通过mid获取收货地址
        if (!is_null(session('reqFrom')) && in_array(session('reqFrom'), ['aliapp', 'baiduapp'])) {
            $userAddressList=MemberAddressService::init()->where(['mid'=>$mid])->getList(false);
        } else {
            $userAddressList=MemberAddressService::init()->where(['umid'=>session('umid')])->getList(false);
        }
        //存放用户收货地址
        $userAddressInfo=['default'=>[],'all'=>[]];
        foreach($userAddressList[0]['data'] as $item)
        {
            $data=[];
            $data['detail_address']=$item['province']['title'];
            $data['detail_address'].=$item['city']['title'];
            $data['detail_address'].=$item['area']['title'];
            $data['detail_address'].=$item['address'];
            $data['address']=$item['address'];
            $data['phone']=$item['phone'];
            $data['name']=$item['title'];
            $data['id']=$item['id'];
            $data['province_id']=$item['province_id'];
            $data['city_id']=$item['city_id'];
            $data['area_id']=$item['area_id'];
            $data['code']=$item['zip_code'];
            $data['type']=$item['type'];
            //默认收货地址
            if($address_id == 0 && $item['type']==1)
            {
                $userAddressInfo['default'][]=$data;
            }
            if ($address_id > 0 && $data['id'] == $address_id) {
                $userAddressInfo['default'][]=$data;
            }
            //所有的收货地址
            $userAddressInfo['all'][]=$data;
        }
        //获取区域信息
        $regionList=[];
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }

        //查询购物车中的商品
        $conditionData=[];
        $conditionData['mid']=$mid;
        $conditionData['wid']=$wid;
        $conditionData['cart_id']=$id;
        $conditionData['address_id']=$address_id; //cwh 2017.12.5=========
        $result=OrderService::processOrder($conditionData, session('umid'));
        //判断购物车中商品状态
        if($result['errCode']!==0)
        {
            error($result['errMsg']);
        }
        else if($result['errCode']==0&&!empty($result['data']['error']))
        {
            error($result['data']['error'][0]['err_msg'][0]['errMsg'], '', ['err_code'=>$result['data']['error'][0]['err_msg'][0]['errCode'], 'data'=>$result['data']['error'][0]['err_msg'][0]['data']]);
        }
        //商品信息
        $userCart=$result['data'];
        //商品基本信息数组
        $productInfoList=[];
        //商品是否设置了批发价 Herry
        $is_wholesale = 0;

        foreach ($userCart['correct'] as $item) {
            //如果商品打折
            if($userCart['is_discount'])
            {
                //如果商品打折扣 那么商品的当前金额应该为折扣的商品金额
                $item['product_amount']=$item['after_discount_product_amount'];
            }
            //商品信息
            $productInfo=[];
            $productInfo['product_id']=$item['product_id'];
            $productInfo['product_amount']=$item['product_amount'];
            $productInfoList[]=$productInfo;
            $discount[] = [
                'id'        => $item['product_id'],
                'price'     => $item['product_amount'],
                'num'       => $item['num'],
            ];
            $item['wholesale_flag'] && $is_wholesale = 1;
        }
        $discountDetail = (new DiscountModule())->getDiscountByPids($discount,$wid);
        $productDiscount = [];
        foreach ($discountDetail['discountDetail'] as $val){
            foreach ($productInfoList as $key=>$item){
                if (in_array($item['product_id'],$val['discountPids'])){
                    $productInfoList[$key]['product_amount'] = bcsub($productInfoList[$key]['product_amount'],$val['discount']*($item['product_amount']/$val['amount']),2) ;
                    $productDiscount[$item['product_id']] = bcmul($val['discount'],($item['product_amount']/$val['amount']),2);
                }
            }
        }
        //新需求20171103 Herry 待提交订单页面返回所有优惠券
        if ($is_wholesale) {
            $coupon = [
                'valid' => [], //可用
                'invalid' => [], //不可用
                'default' => [], //最优,
                'all' => [] //全部
            ];
        } else {
            $coupon = (new OrderModule())->getDefaultCouponByMid($wid,$mid,$productInfoList, $userCart['is_discount']);
        }

        //优惠劵优惠金额
        $couponAmount=0.00;
        if(!empty($coupon['default']))
        {
            $couponAmount=$coupon['default']['discount_amount'];
        }
        //运费
        $freight=$userCart['freight'];
        //商品总金额
        $productTotalAmount=0.00;
        if($userCart['is_discount'])
        {
            $productTotalAmount=$userCart['after_discount_amount'];
        }elseif (isset($userCart['groupFlag'])){
            $productTotalAmount=$userCart['after_discount_amount'];
        } else{
            $productTotalAmount=$userCart['amount'];
        }

        //add by jonzhang 2017-11-23
        //可使用积分的金额
        $usePointAmount=0.00;

        //购物车中的商品信息
        foreach($userCart['correct'] as $productItem)
        {
            $cartProduct=[];
            /*add wuxiaoping 2017.09.11 购物车商品数组中加入商品id*/
            $cartProduct['product_id'] = $productItem['product_id'];
            $cartProduct['cart_id']=$productItem['cart_id'];
            $cartProduct['product_name']=$productItem['product_name'];
            $cartProduct['num']=$productItem['num'];
            //如果商品打折扣，那商品的价格为折扣价
            if($userCart['is_discount']) {
                $cartProduct['price'] = $productItem['after_discount_price'];
            }
            else {
                $cartProduct['price'] = $productItem['price'];
            }
            //商品是否使用积分 add by jonzhang
            if($productItem['is_point'])
            {
                $usePointAmount=$usePointAmount+$cartProduct['price']*$productItem['num'];
            }
            $cartProduct['img_path']=$productItem['img_path'];
            $cartProduct['attr']=$productItem['attr'];
            //add by 张国军 添加卡密id
            $cartProduct['cam_id']=$productItem['cam_id'];
            $cartProducts[]=$cartProduct;
        }

        //没有使用优惠券的商品金额
        $noCouponAmount=$usePointAmount;
        //使用优惠券的最后需要支付金额
        $lastAmount=$productTotalAmount-$couponAmount;
        //对订单金额小于0的特殊处理
        if($lastAmount<0)
        {
            $couponAmount=$productTotalAmount;
            $lastAmount=0.00;
        }
        //可使用积分金额
        $usePointAmount=$usePointAmount-$couponAmount-$discountDetail['discount'];
        if($usePointAmount<0)
        {
            $usePointAmount=0.00;
        }
        //使用优惠券可使用积分
        $usablePoint=0;
        //使用优惠券积分抵现金额
        $bonusPoints=0.00;
        //不使用优惠券可使用积分
        $NoCouponBonusPoints=0;
        //不使用优惠券积分抵现金额
        $NoCouponUsablePoint=0.00;

        //是否显示积分div
        $isShowPointDiv=0;
        //是否可使用积分 0表示不可用
        $isUsePoint = 0;
        $storePointData = OWeixinServie::selectPointStatus(['id' => $wid]);

        //批发价商品订单不显示积分模块 Herry
        if (!$is_wholesale && $storePointData['errCode'] == 0 && !empty($storePointData['data'])) {
            $isUsePoint = $storePointData['data'][0]['is_point'];
            $isShowPointDiv=$isUsePoint;
        }
        $isON = 0;
        $rate = 0;
        if($isShowPointDiv)
        {
            //获取积分兑换金额的规则
            $pointApplyRuleData = OPointApplyRuleService::getRow($wid);
            if ($pointApplyRuleData['errCode'] == 0 && !empty($pointApplyRuleData['data'])) {
                $isON = $pointApplyRuleData['data']['is_on'];
                $isShowPointDiv = $isON;
                $rate = $pointApplyRuleData['data']['rate'];
            }
        }
        //展现积分抵现逻辑begin
        //订单支付金额大于0时，才会使用积分
        if($usePointAmount>0&&$isShowPointDiv)
        {
            //店铺开启使用积分
            if ($isUsePoint)
            {
                $myPoint=0;
                //查询该用户拥有的积分
                if(!empty($memberPointData)) {
                    $myPoint = $memberPointData['score'];
                }

                if ($myPoint > 0)
                {
                    //开启积分使用
                    if ($isON)
                    {
                        $percent = $pointApplyRuleData['data']['percent'];
                        //可使用积分
                        $percent=$percent/100>1?1:$percent/100;
                        $rate = $pointApplyRuleData['data']['rate'];
                        //积分兑换成可使用的钱
                        $usableAmount=$myPoint/$rate;
                        //有优惠券可最大抵现金额
                        $maxAmount=$usePointAmount*$percent;
                        //没有优惠券可最大抵现金额
                        $noCouponMaxAmount=$noCouponAmount*$percent;
                        //积分兑换的钱小于等于可最大抵现金额
                        if($usableAmount<=$maxAmount)
                        {
                            //可抵现金额
                            $bonusPoints=$usableAmount;
                            //可使用积分
                            $usablePoint=$myPoint;
                        }//积分兑换的钱大于可最大抵现金额
                        else if($usableAmount>$maxAmount)
                        {
                            //可使用积分
                            $usablePoint=intval($maxAmount*$rate);
                            //可抵现金额
                            $bonusPoints=$usablePoint/$rate;

                        }
                        if($usablePoint<1)
                        {
                            $bonusPoints=0.00;
                            $usablePoint=0;
                        }
                        //不使用会员卡计算出可用积分和抵现金额
                        //积分兑换的钱小于等于可最大抵现金额
                        if($usableAmount<=$noCouponMaxAmount)
                        {
                            //可抵现金额
                            $NoCouponBonusPoints=$usableAmount;
                            //可使用积分
                            $NoCouponUsablePoint=$myPoint;
                        }//积分兑换的钱大于可最大抵现金额
                        else if($usableAmount>$noCouponMaxAmount)
                        {
                            //可使用积分
                            $NoCouponUsablePoint=$noCouponMaxAmount*$rate;
                            //可抵现金额
                            $NoCouponBonusPoints=intval($noCouponMaxAmount)/$rate;
                        }
                        if($NoCouponUsablePoint<1)
                        {
                            $NoCouponUsablePoint=0;
                            $NoCouponBonusPoints=0.00;
                        }
                    }
                }
                
            }
        }
        //展现积分抵现逻辑end

        //格式化货币
        $NoCouponBonusPoints=sprintf('%.2f',$NoCouponBonusPoints);
        $bonusPoints=sprintf('%.2f',$bonusPoints);

        //计算满减活动优惠金额 2018年8月27日
        if ($lastAmount>= $discountDetail['discount']){
            $lastAmount = bcsub($lastAmount,$discountDetail['discount'],2);
        }else{
            $lastAmount = 0.00;
        }//end

        //运费不参与积分抵现
        $lastAmount=$lastAmount+$freight;
        //对金额格式化处理
        $lastAmount=sprintf('%.2f',$lastAmount);
        //优惠券信息
        //是否是团购
        $is_groups = 0;

        //是否是秒杀
        $is_seckill = 0;

        if (count($id) == 1){
            $cartData = $cartService->init()->getInfo($id[0]);
            if ($cartData && $cartData['groups_id'] != 0){
                $is_groups = 1;
                $result = $this->groupsOrder($cartData);
                //重新设置优惠选择项
                $couponAmount = $result['head_discount'];
                $coupon = ['all'=>[],'default'=>[],'valid'=>[],'invalid'=>[]];
                $lastAmount = $result['lastAmount'];
                $productTotalAmount = $result['productTotalAmount'];
                $usablePoint = 0;
                $bonusPoints = 0;
                $NoCouponUsablePoint = 0;
                $NoCouponBonusPoints=0;
                $freight = $result['freight'];
                $cartProducts[0]['price'] = $result['productPrice'];
            }

            //判断秒杀
            if ($cartData && $cartData['seckill_id'] != 0){
                $is_seckill = 1;
                $cartData['address_id'] = $address_id;
                $result = (new SeckillModule)->getWaitPayOrder($cartData, $wid, $mid, session('umid'), $address_id);
                //重新设置优惠选择项
                $couponAmount = $result['couponAmount'];
                $coupon = ['all'=>[],'default'=>[],'valid'=>[],'invalid'=>[]];
                $lastAmount = $result['lastAmount'];
                $productTotalAmount = $result['productTotalAmount'];
                $usablePoint = 0;
                $bonusPoints = 0;
                $NoCouponUsablePoint = 0;
                $NoCouponBonusPoints=0;
                $freight = $result['freight'];
                $cartProducts[0]['price'] = $result['seckillPrice'];
                $isShowPointDiv=0;
                $noCouponAmount=0.00;
            }
        }
        /**add by wuxiaoping 2018.05.22 添加自提模块功能**/
        $isDeliveryShow = false;  // 是否显示配送方式选择
        $distributionData = ['status' => 0,'hint' => '','data' => []];   // 购物车中商品是否可以一起下单返回的数据
        if ($store['is_ziti_on'] == 1) {
            $receptionService = new ReceptionService();
            $distributionData = $receptionService->isZitiProduct($cartProducts);
            /**
             * 当购物车中的多种商品一块下单，其中有只能发物流，只能自提或者物流自提都可以
             * 因为在进入到提交订单页面默认发物流，所以对比下全部商品是否可以全部发物流，然后再进行
             * 重新计算商品总额，合计支付金额
             * add by 吴晓平 2018年07月20日
             */
            if ($distributionData['status'] == 2) {
                $logicAmount = $zitiAmount = 0;
                if (count($distributionData['data']['Logistics']) <> count($distributionData['data']['all'])) {
                    //计算选择物流时的优惠金额
                    foreach ($distributionData['data']['Logistics'] as $key => $logis) {
                         $logicAmount += $logis['price'] * $logis['num'];
                    }
                    //计算选择自提时的优惠金额
                    foreach ($distributionData['data']['ziti'] as $key => $ziti) {
                        $zitiAmount += $ziti['price'] * $ziti['num'];
                    }
                    if($couponAmount < $zitiAmount) {
                    	$zitiAmount = $couponAmount;
                    }
                    //重新计算商品总价（默认发送物流）
                    $productTotalAmount = $logicAmount;
                    //重新计算优惠金额
                    if ($couponAmount > $productTotalAmount) {
                        $couponAmount = $productTotalAmount;
                    }

                    //重新计算积分金额
                    $overAmount = $productTotalAmount-$couponAmount;
                    if ($bonusPoints > $overAmount) {
                        $bonusPoints = $overAmount;
                    }
                    $usablePoint = $bonusPoints * $rate;
                    $goodAmount = $overAmount;
                    if ($goodAmount < 0) {
                        $goodAmount = 0;
                    }
                    //计算满减活动优惠金额 2018年8月27日
                    if ($goodAmount>= $discountDetail['discount']){
                        $goodAmount = bcsub($goodAmount,$discountDetail['discount'],2);
                    }else{
                        $goodAmount = 0.00;
                    }//end
                    $lastAmount = $goodAmount + $freight;
                    $lastAmount = sprintf('%.2f',$lastAmount);
                }else { 
                    if (count($distributionData['data']['ziti']) <> count($distributionData['data']['all'])) {
                        //计算选择自提时的优惠金额
                        foreach ($distributionData['data']['ziti'] as $key => $ziti) {
                            $zitiAmount += $ziti['price'] * $ziti['num'];
                        }
                        if ($couponAmount < $zitiAmount) {
                            $zitiAmount = $couponAmount;
                        }
                        /*//重新计算积分金额 (积分暂时不考虑)
                        $overAmount = $zitiAmount-$couponAmount;
                        if ($bonusPoints > $overAmount) {
                            $bonusPoints = $overAmount;
                        }
                        //重新计算可使用积分
                        $usablePoint = $bonusPoints * $rate;*/
                    }else {
                        $zitiAmount = $couponAmount;
                    }
                }
            }
            if ($distributionData['status'] >= 0) {
                $cartProducts = empty($distributionData['data']['Logistics']) ? $distributionData['data']['ziti'] : $distributionData['data']['Logistics'];
            }
            // status==1时，表示所有商品都可以选择物流或自提
            // status==2时，表示部分商品可以选择自提或所有商品选择物流
            if ($distributionData['status'] == 1 || $distributionData['status'] == 2) {
                $isDeliveryShow = true;
            }
        }//end
        $noCouponAmount = ($noCouponAmount-$discountDetail['discount'])>0?($noCouponAmount-$discountDetail['discount']):0;
        return view('shop.order.waitPayOrder',[
            'title'                  => '待付款的订单',
            'userAddress'            => $userAddressInfo,
            'userCart'               => $cartProducts,
            'region_data'            => json_encode($regionList),
            'coupon'                 => $coupon,
            'shop_name'              => $shopName,
            'shop_url'               => $shopUrl,
            'coupon_amount'          => $couponAmount, //优惠金额
            'freight'                => $freight,//运费
            'last_amount'            => $lastAmount,//最总价格
            'product_total_amount'   => $productTotalAmount, //商品总额
            'point'                  => intval($usablePoint),  //可使用积分
            'bonus_points'           => $bonusPoints, //积分兑换金额
            'no_coupon_point'        => $NoCouponUsablePoint, //没有使用使用优惠券和积分
            'no_coupon_bonus_points' => $NoCouponBonusPoints,
            'is_groups'              => $is_groups,
            'is_seckill'             => $is_seckill,
            'memberData'             => $memberPointData,
            'use_point_amount'=>$noCouponAmount, //可使用积分商品金额
            'is_show_point_div'=>$isShowPointDiv, //是否显示积分div
            'wid'                    => $wid,
            'is_wholesale'           => $is_wholesale, //是否是批发价购买
            'distributionData'       => json_encode($distributionData,JSON_UNESCAPED_UNICODE),
            'status'                 => $distributionData['status'],
            'isDeliveryShow'         => $isDeliveryShow,
            'rate'                   => $rate,
            'zitiCoupon'             => $zitiAmount ?? 0,
            'discount'               => $discountDetail['discount']??0,
            'takeAwayConfig'         => (new StoreModule())->getWidTakeAway($wid) ? 1 : 0,
            'seckill_id'             => $cartData['seckill_id'] ?? 0,
        ]);
    }

    /**
     * 获取自提提货地址列表(前端调用接口)
     * @author wuxiaoping 2018.05.24
     * @return [type] [description]
     */
    public function getZitiList(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '登录超时';
            return $returnData;
        }
        // 经纬度
        $lat = $request->input('latitude') ?? 0;
        $lng = $request->input('longitude') ?? 0;
        $keyword = $request->input('keyword') ?? [];
        $cityId = $request->input('city_id') ?? 0;
        $where = $from = [];
        if ($lat && $lng) {
            $from = [$lng,$lat];
        }
        if ($keyword) {
            $where['title'] = $keyword;
        }
        if ($cityId) {
            $where['city_id'] = $cityId;
        }
        $receptionService = new ReceptionService();
        $returnData = $receptionService->dealZitiList($wid,$where,$from);

        return $returnData;
    }

    /**
     * 获取相应的自提点日期，时间
     * @author wuxiaoping 2018.05.24
     * @return [type] [description]
     */
    public function getZitiDates(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '请先选择提货地址';
            return $returnData;
        }
        $receptionService = new ReceptionService();
        $data = $receptionService->getRowById($id);
        $zitiTimes = json_decode($data['ziti_times'],true);
        if (empty($zitiTimes)) {
            $returnData['errCode'] = 1;
            $returnData['data']    = '请尽快到店自提';
            return $returnData;
        }

        $date = $request->input('date') ?? '';
        $result = $receptionService->getZitiDates($zitiTimes,$date);
        $returnData['data'] = $result;
        return $returnData;
    }

    /**
     * 地图自提导航
     * @return [type] [description]
     */
    public function detailMap()
    {
        return view('shop.order.detailMap',[
            'title' => '自提导航',
        ]);
    }

	/**
     * 点击自提地址跳转的查看线路页面
     * @return [type] [description]
     */
    public function location(Request $request)
    {
        $olng = $request->input('olng') ?? 0;
        $olat = $request->input('olat') ?? 0;
        return view('shop.order.location',[
            'title' => '查看线路页',
            'olng'  => $olng,
            'olat'  => $olat

        ]);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 团购创建订单
     * @param $groups_id
     */
    public function  groupsOrder($cartData)
    {
        $groupsService = new GroupsService();
        $groupsRuleService = new GroupsRuleService();
        $groupsSkuService = new GroupsSkuService();
        $groupsData = $groupsService->getRowById($cartData['groups_id']);
        $groupsRuleData = $groupsRuleService->getRowById($groupsData['rule_id']);
        $groupsSkuData = $groupsSkuService->getlistByWhere(['rule_id'=>$groupsData['rule_id'],'sku_id'=>$cartData['prop_id']]);
        $head_discount = 0;
        if ($groupsRuleData['head_discount'] == 1){
            //团长优惠
            $groupsDetailData = (new GroupsDetailService())->count(['groups_id'=>$cartData['groups_id']]);
            if ($groupsDetailData == 0){
                $head_discount = ($groupsSkuData[0]['price']-$groupsSkuData[0]['head_price'])*$cartData['num'];
            }

        }
        if ($groupsSkuData){
            //商品价格
            $productPrice = $groupsSkuData[0]['price'];
            $productTotalAmount = $groupsSkuData[0]['price']*$cartData['num'];
            $lastAmount = $productTotalAmount-$head_discount;
        }
        $freight = (new OrderModule())->getFreightByCartIDArr([$cartData['id']], session('wid'), session('mid'), session('umid'));
        $lastAmount = $lastAmount+$freight;
        return [
            'head_discount'        => $head_discount,
            'productTotalAmount'  => $productTotalAmount,
            'lastAmount'           => $lastAmount,
            'freight'               => $freight,
            'productPrice'          => $productPrice,
        ];
    }



    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703311944
     * @desc 订单评价
     * @param Request $request
     * @param $wid
     */
    public function comment(Request $request,$wid)
    {
        $input = $request->input();
        //添加评论
        if ($request->isMethod('post')){
            $rule = Array(
                'odid'    		=> 'required',
                'content'    	=> 'required',
                'status'    	=> 'required',
                'depict'    	=> 'required',
                'service'    	=> 'required',
                'speed'    		=> 'required',
            );
            $message = Array(
                'odid.required' 		=> '订单详情ID不能为空',
                'content.required' 	=> '评论内容不能为空',
                'status.required' 	=> '总体评价不能为空',
                'depict.required' 	=> '商品描述不能为空',
                'service.required' 	=> '服务不能为空',
                'speed.required' 		=> '发货速度不能为空',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $orderDetailData = OrderDetailService::init()->model->find($input['odid'])->toArray();
            //判断该订单是否可以评价
            if ($orderDetailData){
                if ($orderDetailData['is_evaluate']){
                    error('操作非法');
                }
            }else{
                error('订单不存在');
            }

            $evaluate = [
                'wid'		=> session('wid'),
                'mid'		=> session('mid'),
                'oid'		=> $orderDetailData['oid'],
                'odid'		=> $orderDetailData['id'],
                'pid'		=> $orderDetailData['product_id'],
                'content'	=> $input['content'],
                'img'		=> (isset($input['img']) && !empty($input['img']))?implode(',',$input['img']):'',
                'status'	=> $input['status'],
                'depict'	=> $input['depict'],
                'service'	=> $input['service'],
                'speed'		=> $input['speed'],
                'is_hiden'	=> (isset($input['is_hiden']) && !empty($input['is_hiden']))?$input['is_hiden']:0,
            ];

            $id = ProductEvaluateService::init('wid',session('wid'))->add($evaluate,false);
            if ($id){
                $orderLog = [
                    'oid'		=> $orderDetailData['oid'],
                    'wid'		=> session('wid'),
                    'mid'		=> session('mid'),
                    'action'	=> 5,
                    'remark'	=> '订单评价',
                ];
                OrderLogService::init('wid',session('wid'))->add($orderLog,false);
                OrderService::upOrderLog($orderDetailData['oid'], session('wid'));
                OrderDetailService::init()->where(['id'=>$input['odid']])->update(['is_evaluate'=>1,'id'=>$input['odid']],false);

                //检查订单评价状态 Herry 20180124
                OrderService::checkEvaluate($orderDetailData['oid'], $wid);

                $evaluate['id'] = $id;
                success('','',$evaluate);
            }
        }
        //页面展示
        $rule = Array(
            'odid'    		=> 'required',
        );
        $message = Array(
            'odid.required' 		=> '订单详情ID不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $orderDetailData = OrderDetailService::init()->model->find($input['odid'])->toArray();
        if (!$orderDetailData){
            error('订单数据错误');
        }
        return view('shop.order.comment', [
            'title'			=> '评价',
            'orderDetail'	=> $orderDetailData,
            'shareData'     => (new PublicShareService())->publicShareSet($wid)
        ]);

    }

	/**
	 * @auth zhangyh
	 * @Email zhangyh_private@foxmail.com
	 * @date 201703061144
	 * @desc 订单评价列表页
	 * @param $wid
	 * @param $oid
	 */
	public function commentList($wid,$oid)
	{
        $orderData = OrderService::init('wid',$wid)->model->find($oid)->load('orderDetail')->toArray();
        $refundService = new OrderRefundService();
        foreach ($orderData['orderDetail'] as $k => &$val)
        {
            if ($val['is_evaluate'] == 1){
                $val['evaluate']= ProductEvaluateService::init('wid',$wid)->model->where(['odid'=>$val['id']])->first()->toArray();
            }

            //订单中每个商品的退款状态 Herry
            $refund = $refundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $val['product_id'], 'prop_id' => $val['product_prop_id']])->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                //在多商品订单中，已完成退款的商品不能评价 Herry
                unset($orderData['orderDetail'][$k]);
            }
        }
		return view('shop.order.commentList', [
			'title'			=> '评价列表',
			'order'			=> $orderData,
            'shareData'     => (new PublicShareService())->publicShareSet($wid)
		]);
	}

    /**
     * 退款订单列表
     * @param Request $request
     * @param int $status 退款状态 0全部 1待用户处理
     * @return array
     */
    public function refundList($wid, $status = 0)
    {
        //获取列表
        $resultArr = (new RefundModule())->list($wid, session('mid'), $status);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('获取退款列表成功', '', $resultArr['data']);
        } else {
            error($resultArr['errMsg']);
        }
    }
    /**
     * 退款订单页面
     * @param Request $request
     * @param int $status 退款状态 0全部 1待用户处理
     * @return array
     */
    public function refund($wid)
    {

        return view('shop.order.refund', [
            'title'         => '退款售后',
            'wid' => $wid
        ]);
    }


     public function refundDetailView($wid, $oid, $pid, $propID = 0)
    {
        return view('shop.order.refundDetailView', [
                'title'              => '退款详情',
                'wid'                => $wid,
                'oid'                => $oid,
                'pid'                => $pid,
                'propID'             => $propID
            ]);
    }

    public function refundReturnView($wid, $refundID)
    {
        return view('shop.order.refundReturnView', [
                'title'              => '退款详情',
                'wid'                => $wid,
                'refundID'           => $refundID
            ]);
    }


    public function refundDetail($wid, $oid, $pid, $propID = 0)
    {
        $resultArr = (new RefundModule())->detail($oid, $pid, $wid, session('mid'), $propID);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('', '', $resultArr['data']);
        } else {
            error($resultArr['errMsg']);
        }
    }

    /**
     * 买家退款发货
     */
    public function refundReturn(Request $request, $wid, $refundID)
    {
        $resultArr = (new RefundModule())->refundReturn($request, $refundID);
        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('买家退款发货成功', '', $resultArr['data']);
        } else {
            error($resultArr['errMsg']);
        }
    }


    /**
     * 订单申请退款 类型页面
     */
    public function refundApplyType(Request $request, $wid, $oid, $pid, $isEdit, $propID = 0)
    {
        return view('shop.order.refundApplyType',
            [
                'title'       => '退款类型',
                'wid'         => $wid,
                'oid'         => $oid,
                'pid'         => $pid,
                'isEdit'      => $isEdit,
                'propID'      => $propID
            ]
        );
    }


    /**
     * 订单申请退款 类型页面
     */
    public function refundApplyView(Request $request, $wid, $oid, $pid, $isEdit, $propID = 0)
    {
        return view('shop.order.refundApplyView',
            [
                'title'       => '退款申请',
                'wid'         => $wid,
                'oid'         => $oid,
                'pid'         => $pid,
                'isEdit'      => $isEdit,
                'propID'      => $propID
            ]
        );
    }


	/**
	 * 订单申请退款 Herry
	 */
	public function refundApply(Request $request, $wid, $oid, $pid, $propID = 0)
	{
        $resultArr = (new RefundModule())->refundApply($request, $oid, $pid, $wid, session('mid'), $propID);
        if ($resultArr['errCode'] == 1) {
            //普通报错
            error($resultArr['errMsg']);
        } elseif ($resultArr['errCode'] == 2) {
            //跳转指定url
            error($resultArr['errMsg'], '', $resultArr['data']['groupID']);
        }

        if ($request->isMethod('post')) {
            success($resultArr['errMsg']);
        } else {
            success($resultArr['errMsg'], '', $resultArr['data']);
        }
	}

    /**
     * 修改申请退款
     * @udpate 张永辉 2019年10月31日20:34:47 处理客户订单
     */
    public function refundApplyEdit(Request $request, $wid, $oid, $pid, $propID = 0)
    {
        if ($oid == '88175') {
            error('该订单已维权结束');

        }
        $resultArr = (new RefundModule())->applyEdit($request, $oid, $pid, $propID);

        if ($resultArr['errCode'] == 1) {
            //普通报错
            error($resultArr['errMsg']);
        }

        if ($request->isMethod('post')) {
            success($resultArr['errMsg']);
        } else {
            $data = [
                'order' => $resultArr['data']['order'],
                'product' => $resultArr['data']['product'],
                'refund' => $resultArr['data']['refund']
            ];
            success($resultArr['errMsg'], '', $data);
        }
    }


	/**
	 * 买家取消退款
	 */
	public function refundCancel($wid, $oid, $refundID)
	{
        $resultArr = (new RefundModule())->buyerCancel($oid, $refundID, $wid, session('mid'));
        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('撤销退款成功', '', $resultArr['data']);
        } else {
            error($resultArr['errMsg']);
        }
	}
    
    /**
     * 退款添加留言
     */
    public function refundAddMessage(Request $request, $wid, $refundID, $oid)
    {
        $resultArr = (new RefundModule())->addMessage($request, $oid, $wid, $refundID);
        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('添加留言成功', '', $resultArr['data']);
        } else {
            error($resultArr['errMsg']);
        }
    }
    /**
     * 退款添加留言
     */
    public function refundAddMessageView(Request $request, $wid, $refundID, $oid)
    {
        $refund = (new OrderRefundService())->init('oid', $oid)->getInfo($refundID);

        return view('shop.order.refundAddMessageView',
            [
                'title'       => '退款添加留言',
                'wid'         => $wid,
                'refundID'    => $refundID,
                'oid'         => $oid,
                'propID'      => $refund['prop_id']
            ]
        );
    }
    /**
     * 退款协商列表
     */
    public function refundMessages($wid,$oid,$pid,$propID = 0)
    {
        $resultArr = (new RefundModule())->messages($oid, $pid, $wid, session('mid'), $propID);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('获取协商详情成功', '', ['data' => $resultArr['data']]);
        } else {
            error($resultArr['errMsg']);
        }
    }

    /**
     * 退款协商列表
     */
    public function refundMessagesView($wid,$oid,$pid,$propID = 0)
    {
        return view('shop.order.refundMessagesView',
            [
                'title'       => '退款协商列表',
                'wid'         => $wid,
                'oid'         => $oid,
                'pid'         => $pid,
                'propID'      => $propID
            ]
        );
    }


    /**
     * 退款微信审核 钱款去向
     */
    public function refundVerify($wid, $refundID)
    {
        //获取列表
        $resultArr = (new RefundModule())->refundVerify($refundID);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            success('获取退款列表成功', '', $resultArr['data']);
        } else {
            error($resultArr['errMsg']);
        }
    }

    /**
     *钱款去向页面
     */
    public function refundVerifyView($wid, $refundID)
    {
        return view('shop.order.refundVerifyView', [
            'title'              => '钱款去向',
            'wid'                => $wid,
            'refundID'           => $refundID
        ]);
    }
	/**
	 * @auth zhangyh
	 * @Email zhangyh_private@foxmail.com
	 * @date 20170407
	 * @desc 确认收货
	 * @param $wid
	 * @param $oid
	 */
	public function received($wid,$oid)
	{
		$orderData = OrderService::init('wid',$wid)->where(['id'=>$oid])->getInfo($oid);
        $mid=session('mid');
		if ($orderData['mid'] !=$mid ){
			error('操作非法');
		}
		//判断该订单是否可以取消退款
		if ($orderData['status'] != 2)
		{
			error('该订单暂时无法确认收货');
		}
		$res = OrderService::init('wid',$wid)->where(['id'=>$oid])->update(['id'=>$oid,'status'=>3],false);
		if ($res){
		    //确认收货后赠送积分 add by jonzhang 2017-05-31
            //订单金额大于0,进行赠送积分
            $point=0;
            if($orderData['pay_price']>0) {
                //当店铺开启订单赠送积分
                $orderPointData = OOrderPointRuleService::getRowByCondition(['wid' => $wid, 'is_on' => 1]);
                if ($orderPointData['errCode'] == 0 && !empty($orderPointData['data'])) {
                    //积分规则id
                    $id=$orderPointData['data']['id'];
                    //订单对应的积分
                    $orderPoint=intval($orderData['pay_price']*$orderPointData['data']['basic_rule']/100);

                    $whereData=[];
                    $whereData['p_id']=$id;
                    $whereData['used_money']=['<=',$orderData['pay_price']];
                    //查询订单积分对应的额外规则
                    $orderExtraRuleData=OOrderPointExtraRuleService::getListByConditionWithPage($whereData,'used_money','desc');

                    $orderExtraPoint=0;
                    if($orderExtraRuleData['errCode']==0&&!empty($orderExtraRuleData['data']))
                    {
                        //查询该订单对应的金额 额外积分
                        $orderExtraPoint=$orderExtraRuleData['data'][0]['reward_point'];
                    }
                    // 该订单总共积分
                    $point=intval($orderPoint+$orderExtraPoint);
                    if($point>0)
                    {
                        $pointRecordData=['wid'=>$wid,'mid'=>$mid,'point_type'=>1,'is_add'=>1,'score'=>$point];
                        //消费积分记录
                        OPointRecordService::insertData($pointRecordData);
                        //查询改用户当前积分
                        $this->memberService->incrementScore($mid, $point);
                    }
                }
            }

            //确认收货 如果有退款 关闭退款 Herry 20171101
            (new RefundModule())->closeAfterReceive($oid);

			$orderLogData = [
				'oid'		=> $orderData['id'],
				'wid'		=> $orderData['wid'],
				'mid'		=> session('mid'),
				'action'	=> 4,
				'remark'	=> '买家确认收货',
			];
			OrderLogService::init()->add($orderLogData,false);
            OrderService::upOrderLog($orderData['id'], $orderData['wid']);
			if($point>0)
			    $info='收货成功，获得'.$point.'积分';
			else
			    $info='收货成功';
			return mysuccess($info);
		}
	}

    /**
     * @author <吴晓平>
     * @param  int $wid 店铺id 
     * @param  int $oid 订单id
     * @return array 订单详情数组 （购买商品信息，购买日志）
     * @modify author  张国军 虚拟订单显示卡密信息 2018年08月07日
     * @update 何书哲 2018年7月31日 添加支付宝支付
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年11月16日 返回店铺外卖配置及订单发货时间
     */
    public function detail($oid, Request $request, ShopService $shopService, DeliveryConfigService $deliveryConfigService)
    {
    	$mid = session('mid');
    	$wid = session('wid');

        
    	//获取标识
    	$tab = $request->input('tab') ?? '';
        $orderDetail = OrderService::orderDetail($wid,$oid);
        if ($orderDetail['mid'] != $mid){
            return redirect('/shop/index/'.session('wid'));
            exit();
        }
        if(empty($orderDetail)){
            error('订单异常');
        }

        if ($orderDetail['groups_id'] !=0){
            $orderDetail['groupsProductPrice'] = $orderDetail['products_price'];
        }
        //计算商品总价
        $productPrice = 0;
        foreach($orderDetail['orderDetail'] as $value){
            $productPrice += $value['price']*$value['num'];
        }
        $orderDetail['productPrice'] = $productPrice;
        //获取店铺名称、logo
        //$stroeData = WeixinService::getStageShop($wid);
        $stroeData = $shopService->getRowById($wid);

        $orderDetail['shop_name'] = $stroeData['shop_name'] ?? '';
        $orderDetail['shop_logo'] = $stroeData['logo'] ?? '';
        $orderDetail['order_create_time'] = strtotime($orderDetail['created_at']);  //订单生成时间，主要用于倒计时，传递时间戳格式

        //秒杀订单 获取超时未支付时间
        $orderDetail['seckill_expire_seconds'] = 3600;
        if ($orderDetail['type'] == 7) {
            $seckill = (new SeckillService())->getDetail($orderDetail['seckill_id']);
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
        $memberData = $this->memberService->getRowById($mid);
        $orderDetail['pay_way_name'] = '微信安全支付';
        if ($orderDetail['pay_way'] == 3) {
            $orderDetail['pay_way_name'] = '余额支付';
        } elseif ($orderDetail['pay_way'] == 2) {//何书哲 2018年7月31日 添加支付宝支付
            $orderDetail['pay_way_name'] = '支付宝支付';
        }

        //是否需要物流 add MayJay
        $orderDetail['no_express'] = 0;
        $re = ( new LogisticsService())->init()->where(['oid'=>$oid])->getInfo();
        if($re){
            $orderDetail['no_express'] = $re['no_express'];
        }
        //end
        //自提订单增加返回用户自提信息 add by wuxiaoping 2018.06.11
        if ($orderDetail['is_hexiao'] == 1) {
            $where['oid'] = $orderDetail['id'];
            $where['wid'] = $wid;
            $where['mid'] = $mid;
            $zitiData = (new OrderZitiService())->getDataByCondition($where);
            if ($zitiData) {
                $temp = [$zitiData['orderZiti']['province_id'],$zitiData['orderZiti']['city_id'],$zitiData['orderZiti']['area_id']];
                $regionService = new RegionService();
                $region = $regionService->getListByIdWithoutDel($temp);
                $tmpAddr = [];
                foreach ($region as $val){
                    $tmpAddr[$val['id']] = $val['title'];
                }
                $zitiData['orderZiti']['province_title'] = $tmpAddr[$zitiData['orderZiti']['province_id']];
                $zitiData['orderZiti']['city_title']     = $tmpAddr[$zitiData['orderZiti']['city_id']];
                $zitiData['orderZiti']['area_title']     = $tmpAddr[$zitiData['orderZiti']['area_id']];
                $orderDetail['ziti'] = $zitiData;
            } 
        }

        //虚拟订单 卡密信息
        $orderDetail['carmName']=[];
        $orderDetail['carmAttr']=[];
        $orderDetail['userInstruction']="";
        if(isset($orderDetail['type'])&&$orderDetail['type']==12&&!empty($orderDetail['id']))
        {
            $camListService=new CamListService();
            //查询卡密信息
            $camListData=$camListService->getAllList(['oid'=>$orderDetail['id']],'id',false);
            if(!empty($camListData)&&count($camListData)>0)
            {
                $cnt=0;
                foreach($camListData as $item)
                {
                    $name=json_decode($item['name'],true)??[];
                    $attr=json_decode($item['attr'],true)??[];
                    $orderDetail['carmName'][] = $name;
                    $orderDetail['carmAttr'][] = $attr;
                    $id = $item['id']??0;
                    $carmId = $item['cam_id']??0;
                    if (!empty($carmId)&&!$cnt)
                    {
                        $camActivityData = (new CamActivityService())->getRowById($carmId);
                        if (!empty($camActivityData['remark']))
                        {
                            $orderDetail['userInstruction'] = $camActivityData['remark'];
                        }
                    }
                    $cnt++;
                    //更改卡密的使用时间
                    //if (!empty($id)) {
                     //   $camListService->update($id, ['use_time' => date('Y-m-d H:i:s', time())]);
                    //}
                }
            }
        }

        return view('shop.order.detail',
            [
                'memberData'  => $memberData,
                'title'       => '订单详情',
                'orderDetail' => $orderDetail,
                'tab'         => $tab,
                'wid'         => $wid,
                'coupon'      => (new CouponLogService())->getRowByOid($oid),
                'shareData'   => (new PublicShareService())->publicShareSet($wid),
                'configData'  => (new StoreModule())->getDeliveryConfig(['wid'=>$wid, 'is_on'=>1]),
            ]
        );
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @udpate 何书哲 2018年7月31日 添加支付宝支付
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月8日 新需求：确认收货7天后不能申请退款
     */
     public function groupsOrderDetail($oid, Request $request,ShopService $shopService)
    {
        $mid = session('mid');
        $wid = session('wid');

        
        //获取标识
        $tab = $request->input('tab') ?? '';
        $orderDetail = OrderService::orderDetail($wid,$oid);
        if(empty($orderDetail)){
            error('订单异常');
        }

        if ($orderDetail['groups_id'] !=0){
            /*获取是否开启抽奖团 add by wuxiaoping 2017.11.16*/
            $groupData = (new GroupsService())->getRowById($orderDetail['groups_id']);
            $groupsRuleData = (new GroupsRuleService())->getRowById($groupData['rule_id']);
            $orderDetail['is_open_draw'] = $groupsRuleData['is_open_draw'];
            if ($orderDetail['is_open_draw'] == 1) {
                if ($groupData['status'] == 3) {
                    $orderDetail['is_open_draw'] = 0;
                } 
            }
            $orderDetail['groupsProductPrice'] = $orderDetail['products_price'];
        }
        //计算商品总价
        $productPrice = 0;
        //add by jonzhang
        $evaluate = 1;
        foreach($orderDetail['orderDetail'] as $value){
            $productPrice += $value['price']*$value['num'];
            if ($value['is_evaluate'] == 0){
                $evaluate = 0;
            }
        }

        $orderDetail['evaluate'] = $evaluate;
        $orderDetail['productPrice'] = $productPrice;
        //获取店铺名称、logo
        //$stroeData = WeixinService::getStageShop($wid);
        $stroeData = $shopService->getRowById($wid);

        $orderDetail['shop_name'] = $stroeData['shop_name'] ?? '';
        $orderDetail['shop_logo'] = $stroeData['logo'] ?? '';
        $orderDetail['order_create_time'] = strtotime($orderDetail['created_at']);  //订单生成时间，主要用于倒计时，传递时间戳格式

        //秒杀订单 获取超时未支付时间
        $orderDetail['seckill_expire_seconds'] = 3600;
        if ($orderDetail['type'] == 7) {
            $seckill = (new SeckillService())->getDetail($orderDetail['seckill_id']);
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
        $memberData = $this->memberService->getRowById($mid);
        $orderDetail['pay_way_name'] = '微信安全支付';
        if ($orderDetail['pay_way'] == 3) {
            $orderDetail['pay_way_name'] = '余额支付';
        } elseif ($orderDetail['pay_way'] == 2){ //何书哲 2018年7月31日 添加支付宝支付
            $orderDetail['pay_way_name'] = '支付宝支付';
        }

        $orderDetail['deliver'] = (new OrderModule())->getDeliverTime($orderDetail['id']);
        $orderDetail['nowtime'] = '';
        if ($orderDetail['deliver']){
            $orderDetail['nowtime'] =  (strtotime($orderDetail['deliver'])+15*86400)-time();
        }

        //确认收货7天后 不能申请或修改退款 Herry
        $orderDetail['canRefund'] = 1;
        $logs = OrderLogService::init()->model->where(['oid' => $oid, 'action' => 4])->get()->toArray();
        if ($logs) {
            $receiveTime = $logs[0]['created_at'];
            if (strtotime($receiveTime) + 7 * 24 * 3600 < time()) {
                $orderDetail['canRefund'] = 0;
            }
        }
        //是否需要物流 add MayJay
        $orderDetail['no_express'] = 0;
        $re = ( new LogisticsService())->init()->where(['oid'=>$oid])->getInfo();
        if($re){
            $orderDetail['no_express'] = $re['no_express'];
        }
        //end

        $result =   [
                'memberData'  => $memberData,
                'orderDetail' => $orderDetail,
                'tab'         => $tab,
                'wid'         => $wid,
                'coupon'      => (new CouponLogService())->getRowByOid($oid),
                'shareData'   => (new PublicShareService())->publicShareSet($wid)
            ];
        return view('shop.order.groupsOrderDetail',[
            'title'       => '订单详情',
            'data'        => $result,
        ]);
    }


    //订单助手
    public function assistant()
    {
        return view('shop.order.assistant',[
                'title' => '订单助手'
            ]);
    }

    /**
     * [orderPay 订单支付]
     * @author 吴晓平 
     * @param  [int]  $oid     [订单id]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function orderPay($oid,Request $request)
    {
        $type  = $request->input('type');  //支付方式

        //调用相关的支付
        PaymentService::pay($oid,$type);
    }



     /* todo 创建订单
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-11
     * @update 张永辉 2018年7月30日 标注支付宝来源
     * @update 许立   2018年09月17日 下单库存为0不下架
     * @update 许立   2018年09月25日 优惠券使用状态判断修复
     * @update 梅杰   2018年10月19日 待付款订单提醒
     * @update 张永辉 2018年10月10日 创建订单完成计算分销
     * @update 许立 2018年10月16日 百度小程序来源处理
     * @update 何书哲 2018年11月16日 标记外卖订单
     * @update 何书哲 2018年11月22日 外卖店铺添加订单提交约束
     */
    public function submitOrder(Request $request,CartService $cartService,CouponLogService $couponLogService)
    {
        //定义返回数据数组
        $returnData = array('errCode' => 0, 'errMsg' => '', 'data' => []);
        //获取mid和wid
        $mid = session('mid');
        $wid = session('wid');
        if (empty($mid) || empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '登录超时';
            return $returnData;
        }
        //接收参数
        //$cartId=$request->input('cart_id')??'[29,30]';
        $cartId = $request->input('cart_id');
        $expressNo = $request->input('express_no')??1;
        $couponId = $request->input('coupon_id')??0;
        $remark = $request->input('remark')??'';
        $isSend = $request->input('is_send')??0;
        $address_id = $request->input('address_id',0);

        $errMsg = '';
        /****提交订单时增加配送方式字段 默认为物流(1-物流 2-自提) add by wuxiaoping 2018.05.22****/
        $isHexiao = $request->input('isHexiao') ?? 0;
        $zitiContact = $request->input('zitiContact') ?? '';
        $zitiPhone = $request->input('zitiPhone') ?? '';
        $zitiId = $request->input('zitiId') ?? 0;
        $zitiDatetime = $request->input('zitiDatetime') ?? '';
        if ($isHexiao == 1) {
            if(empty($zitiContact)) {
                $errMsg .= '提货人不能为空';
            }
            if (empty($zitiPhone)) {
                $errMsg .= '提货人手机号不能为空';
            }
            if (!$zitiId) {
                $errMsg .= '提货地址不能为空';
            }
            if (empty($zitiDatetime)) {
                $errMsg .= '提货时间不能为空';
            }
        }
        //使用积分
        $point=$request->input('point')??0;
        $point=intval($point);
        $address_id = $request->input('address_id',0);
        if (empty($cartId)) {
            $errMsg .= 'cart_id为null';
        }
        if (empty($expressNo)) {
            $errMsg .= 'express_no为null';
        }
        if (strlen($errMsg)>0) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = $errMsg;

            return $returnData;
        }
        try {
            $cartId = json_decode($cartId, true);
            if (empty($cartId)) {
                $returnData['errCode'] = -8;
                $returnData['errMsg'] = '传递数据不符合要求';
                return $returnData;
            }
        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            $returnData['errCode'] = -9;
            $returnData['errMsg']=$message;
            return $returnData;
        }
        //add by zhangyh 如果是团购订单另行处理
        if (count($cartId) == 1){
            $cartData = Cart::select('id','groups_id', 'seckill_id', 'num', 'prop_id')->find($cartId[0]);
            if (!$cartData){
                $returnData['errCode'] = -9;
                $returnData['errMsg']='购物车不存在';
                return $returnData;
            }
            if ($cartData->groups_id != 0){
                $orderData=  $this->createGroupsOrder($cartId,$request);
                $returnData['data'] = $orderData['id'];
                return $returnData;
            }
            //add by Herry 创建秒杀订单
            if ($cartData->seckill_id != 0){
                //秒杀活动是否未开始或结束或失效
                if (!(new SeckillService())->checkValidity($cartData->seckill_id)) {
                    $returnData['errCode'] = -9;
                    $returnData['errMsg'] = '活动不在进行中或已失效';
                    return $returnData;
                }

                //用户秒杀限额检查
                $seckillModule = new SeckillModule();
                $limitNum = $seckillModule->isLimited($cartData->seckill_id, $mid, $cartData->num);
                if ($limitNum) {
                    $returnData['errCode'] = -9;
                    $returnData['errMsg'] = '该商品每人限购' . $limitNum . '件';
                    return $returnData;
                }
                if ($request->input('reqFrom') == 'aliapp') {
                    $source = 2;
                } elseif ($request->input('reqFrom') == 'baiduapp') {
                    $source = 3;
                } else {
                    $source = 0;
                }
                $orderData = $seckillModule->createSeckillOrder($cartId, $request, $wid, $mid,session('umid'),$source);
                $returnData['data'] = $orderData['id'];
				//dispatch(new CancelNonPaymentSeckillOrder($orderData['id'], $cartData->seckill_id));
                return $returnData;
            }
        }
        //end add

        //何书哲 2018年11月22日 外卖店铺添加订单提交约束
        $checkRes = (new StoreModule())->checkIfSubmitOrder($wid);
        if ($checkRes['errCode'] != 0) {
            $returnData['errCode'] = -12;
            $returnData['errMsg'] = $checkRes['errMsg'];
            return $returnData;
        }

        //查询用户默认的收货地址信息
        $userAddress = OrderService::getDeliveryAddress(session('umid'), $address_id);
        if(empty($userAddress) && empty($isHexiao))
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='请选择收货地址';
            return $returnData;
        }
        //收货地址详细信息
        $address = $userAddress['address']??'';
        $phone=$userAddress['phone']??'';
        $name=$userAddress['name']??'';
        $areaId=$userAddress['areaId']??'';

        //检查用户购买的商品是否存在异常
        $productWhere = [];
        $productWhere['mid'] = $mid;
        $productWhere['wid'] = $wid;
        $productWhere['cart_id'] = $cartId;
        $productWhere['address_id'] = $address_id;
        $productResult = OrderService::processOrder($productWhere, session('umid'));
        //查询条件不符合要求
        if ($productResult['errCode'] != 0) {
            $returnData['errCode']=-4;
            $returnData['errMsg']=$productResult['errMsg'];
            return $returnData;
        } //购物车中有异常商品
        else if ($productResult['errCode'] == 0 && !empty($productResult['data']['error'])) {
            $returnData['errCode']=-5;
            $returnData['errMsg']='待付款订单中有异常商品';
            $returnData['data']=$productResult['data']['error'];
            return $returnData;
        }//购物车商品添加到订单表中
        else if($productResult['errCode'] == 0 &&empty($productResult['data']['error'])) {
            //生成订单号
            $orderID=OrderCommon::createOrderNumber();
            //有效商品
            $productInfo = $productResult['data']['correct'];
            //订单商品金额
            $productAmount = $productResult['data']['amount'];
            //折扣前的总金额
            $beforeDiscountAmount = $productResult['data']['amount'];
            //折扣后的总金额
            $afterDiscountAmount=$productResult['data']['after_discount_amount'];
            //是否折扣
            $isDiscount=$productResult['data']['is_discount'];
            //减免的运费信息
            $derateFreight=$productResult['data']['derate_freight'];
            if($isDiscount)
            {
                $productAmount=$afterDiscountAmount;
            }
            //订单运费
            $freight = $productResult['data']['freight'];
            //add by wuxiaoping 2018.06.14 自提订单运费为0
            if ($isHexiao == 1) {
                $freight = 0;
            }
            //优惠券金额
            $couponSum = 0.00;
            if (!empty($couponId)) {
                $coupon = $couponLogService->getDetail($couponId);
                if (!$coupon) {
                    $returnData['errCode'] = -6;
                    $returnData['errMsg'] = '优惠券不存在';
                    return $returnData;
                } elseif ($coupon['status']) {
                    $returnData['errCode'] = -6;
                    $returnData['errMsg'] = '优惠券已使用过';
                    return $returnData;
                }
                $couponSum = $coupon['amount'];
            }
            $usePointAmount=0.00;
            //购物车中的商品信息
            foreach($productInfo as $productItem)
            {
                //如果商品打折扣，那商品的价格为折扣价
                if($isDiscount)
                {
                    $productItem['price'] = $productItem['after_discount_price'];
                }
                //商品是否使用积分 add by jonzhang
                if($productItem['is_point'])
                {
                    $usePointAmount=$usePointAmount+$productItem['price']*$productItem['num'];
                }
                //满减活动优惠计算
                if ($productItem['after_discount_product_amount'] > 0) {
                    $disPrice = $productItem['after_discount_product_amount'];
                } else {
                    $disPrice = $productItem['product_amount'];
                }
                $discount[] = [
                    'id'    => $productItem['product_id'],
                    'price' => $disPrice,
                    'num'   => $productItem['num'],
                ];
            }
            $discountDetail = (new DiscountModule())->getDiscountByPids($discount,$wid);
            $usePointAmount=$usePointAmount-$couponSum-$discountDetail['discount'];
            if($usePointAmount<0)
            {
                $usePointAmount=0.00;
            }
            //订单信息
            $createTime=date("Y-m-d H:i:s");
            $order=[];
            $order['oid']=$orderID;
            $order['trade_id']=$orderID;
            $order['wid']=$wid;
            $order['mid']=$mid;
            if ($request->input('reqFrom') == 'aliapp') {
                $order['source'] = 2;
            } elseif ($request->input('reqFrom') == 'baiduapp') {
                $order['source'] = 3;
            } else {
                $order['source'] = 0;
            }
            //商品总价
			$order['products_price'] = $productAmount;
			$order['change_price'] = 0.00;
            $order['cash_fee'] = 0.00;
            $order['is_hexiao'] = $isHexiao; //add by wuxiaoping 2018.05.22配送方式
            if ($isHexiao && $isHexiao == 1) {
                $order['express_type'] = 2; //配送方式为上门自提
                $hexiaoCode = rand(5000,9999).rand(1000,4999).rand(100,999); //生成七位提货码
                $order['hexiao_code'] = $hexiaoCode;
            }
            //订单实际支付金额
            $productAmount = $productAmount-$discountDetail['discount'];  //满减优惠金额
            if ($productAmount<$couponSum) {
                //优惠券金额比本来需要支付金额更大
                $orderCouponPrice=$productAmount;
                $order['coupon_price'] =sprintf('%.2f',$orderCouponPrice);
                $order['pay_price'] = 0.00;
            } else {
                $order['coupon_price'] = $couponSum;
                $payOrderPrice=$productAmount-$couponSum;
                $order['pay_price'] =sprintf('%.2f',$payOrderPrice);
            }
            //积分逻辑 begin
            //可抵现金额
            $bonusPointAmount=0.00;
            //当前用户拥有的积分
            $myPoint=0;
            //订单支付金额大于0时才使用积分
            if($order['pay_price']>0&&$usePointAmount>0&&$point>0)
            {
                //是否可使用积分 0表示不可用
                $isUsePoint = 0;
                $storePointData = OWeixinServie::selectPointStatus(['id' => $wid]);
                if ($storePointData['errCode'] == 0 && !empty($storePointData['data'])) {
                    $isUsePoint = $storePointData['data'][0]['is_point'];
                }
                if(!$isUsePoint)
                {
                    $returnData['errCode']=-8;
                    $returnData['errMsg']='该店铺没有开启使用积分功能';
                    return  $returnData;
                }
                //查询用户拥有的积分
                $memberPointData=$this->memberService->getRowById($mid);
                //用户积分
                if(!empty($memberPointData)) {
                    $myPoint = $memberPointData['score'];
                }
                if ($myPoint > 0)
                {
                    //查询当前店铺的积分使用规则
                    $pointApplyRuleData = OPointApplyRuleService::getRow($wid);
                    if ($pointApplyRuleData['errCode'] == 0 && !empty($pointApplyRuleData['data'])) {
                        $isON = $pointApplyRuleData['data']['is_on'];
                        //该店铺积分使用规则没有开启
                        if (!$isON)
                        {
                            $returnData['errCode']=-9;
                            $returnData['errMsg']='该店铺积分使用规则没有开启';
                            return  $returnData;
                        }
                        $percent = $pointApplyRuleData['data']['percent'];
                        $percent=$percent/100>1?1:$percent/100;
                        //用户使用的积分大于用户拥有的积分
                        if($point>$myPoint)
                        {
                            $returnData['errCode']=-10;
                            $returnData['errMsg']='对不起，你没有那么多积分可使用';
                            return  $returnData;
                        }
                        $rate = $pointApplyRuleData['data']['rate'];
                        //用户通过积分兑换人民币
                        $usablePointAmount=$point/$rate;
                        //最大可抵现金额
                        $maxAmount=$usePointAmount*$percent;
                        //如果订单金额大于0，且积分抵现金额大于订单金额
                        //此情况正常状态下不存在
                        if($usablePointAmount>$maxAmount)
                        {
                            $returnData['errCode']=-11;
                            $returnData['errMsg']='对不起，不能够使用那么多的积分';
                            return  $returnData;
                        }
                        $bonusPointAmount=sprintf('%.2f',$usablePointAmount);
                        //最后的实际支付金额
                        $order['pay_price']=$order['pay_price']-$bonusPointAmount;
                    }
                }//该用户没有积分
                else
                {
                    $returnData['errCode']=-10;
                    $returnData['errMsg']='该用户没有可使用的积分';
                    return  $returnData;
                }
                
            }
            //积分逻辑 end

            //最后实际支付金额要加上运费金额
            $order['pay_price']=$order['pay_price']+$freight;
            $order['pay_price']=sprintf('%.2f',$order['pay_price']);
            //积分抵扣金额
            $order['bonus_point_amount']=$bonusPointAmount;
            //运费金额
            $order['freight_price']=$freight;
            //物流方式 1快递发货
            $order['express_type']= $isHexiao == 1 ? 2 : $expressNo;
            $order['address_id']=$areaId;
            $order['address_name']=$name;
            $order['address_phone']=$phone;
            //详细地址
            $order['address_detail']=$address;

            //Herry 保存收货地址城市名 打印快递单使用 
            //update by 吴晓平 2018年08月31日
            if (!empty($userAddress)) {
                $order['address_province'] = $userAddress['province'];
                $order['address_city'] = $userAddress['city'];
                $order['address_area'] = $userAddress['area'];
            }
            //普通订单
            $order['type']=1;

            //积分抵现订单
            if($order['bonus_point_amount']>0)
            {
                $order['type']=5;
            }
            //支付方式 0未支付
            $order['pay_way']=0;
            //买家备注
            $order['buy_remark']=$remark;
            //商家备注
            $order['seller_remark']='';
            //由于使用到redis缓存 下面字段必须传递值
            //订单状态 0待付款
            $order['status']=0;
            //维权状态
            $order['refund_status']=0;
            //星级
            $order['star_level']=0;
            //是否是代销订单
            $order['is_deposit']=0;
            //统一账户id
            $order['umid']=session('umid')??0;
            //创建时间
            $order['created_at']=$createTime;
            //折扣金额
            $order['discount_amount']=0.00;
            //减免运费金额
            $order['derate_freight']=sprintf('%.2f',$derateFreight);
            //使用的积分
            $order['use_point']=$point;
            //优惠券id
            $order['coupon_id']=$couponId;
            $order['card_discount'] = 0;
            if($isDiscount)
            {
                //订单折扣金额
                $discountAmount=$beforeDiscountAmount-$afterDiscountAmount;
                $order['discount_amount']=sprintf('%.2f',$discountAmount);
                $order['card_discount'] = 1;
            }
            //是否分销订单
            $order['distribute_type']=0;
            //团购信息
            $order['groups_id'] = 0;
            $order['groups_status'] = 0;
            $order['head_discount'] = 0;

            //秒杀ID字段默认值 Herry
            $order['seckill_id'] = 0;
            //满减活动优惠金额
            $order['discount'] = $discountDetail['discount'];
            $order['discount_ids'] = implode(',',array_column($discountDetail['discountDetail'],'id'));
            $order['is_takeaway'] = (new StoreModule())->getWidTakeAway($wid) ? 1 : 0;//何书哲 2018年11月16日 标记外卖订单


            /*add wuxiaoping 2017.09.12 检查商品有否有核销商品*/
            $productSave = $this->checkProduct($productInfo);
            //设置5位核销码
            $code = OrderService::createCode(5); 
            //订单明细
            $orderDetails=[];
            $isCamiProduct=0;
            foreach($productInfo as $item) {
                $orderDetail = [];
                $orderDetail['discount'] = 0;
                $orderDetail['discount_detail'] = '';
                foreach ($discountDetail['discountDetail'] as $val){
                    if (in_array($item['product_id'],$val['discountPids'])){
                        $orderDetail['discount'] = bcmul($val['discount'],$item['product_amount']/$val['amount'],2);
                        $orderDetail['discount_detail'] = $val['title'];
                        break;
                    }
                }

                $orderDetail['oid'] = $orderID;
                $orderDetail['product_id']=$item['product_id'];
                $orderDetail['title']=$item['product_name'];
                $orderDetail['img']=$item['img_url'];
                $orderDetail['price']=$item['price'];
                //折扣的商品单价
                $orderDetail['after_discount_price']= bcsub($item['after_discount_price'],$orderDetail['discount'],2) ;
                if ($orderDetail['after_discount_price']<0){
                    $orderDetail['after_discount_price'] = 0;
                }
                $orderDetail['oprice']=$item['old_price'];
                $orderDetail['num']=$item['num'];
                $orderDetail['spec']=$item['attr'];
                $orderDetail['is_attr']=$item['is_attr'];
                //商品有规格product_prop_id为product_prop表中的id 否则为o
                if($item['is_attr']==1) {
                    $orderDetail['product_prop_id'] = $item['product_prop_id'];
                }
                else{
                    $orderDetail['product_prop_id']=0;
                }
                //缓存redis使用
                $orderDetail['is_evaluate']=0;
                $orderDetail['created_at']=$createTime;
                $orderDetail['remark_no'] = '';
                if(isset($item['cam_id'])&&$item['cam_id']>0)
                {
                    $isCamiProduct=1;
                }
                $orderDetails[]=$orderDetail;

            }

            //add by 张国军 2018年08月07日 卡密商品没有收获地址。
            if($isCamiProduct)
            {
                $order['freight_price']=0;
                $order['express_type']=1;
                $order['address_detail']='';
                $order['address_province'] = '';
                $order['address_city'] = '';
                $order['address_area'] = '';
                $order['type']=12;
            }
            \Log::info('订单满减优惠详情订单号：:'.$order['oid']);
            \Log::info($discountDetail);
            //事务
            DB::beginTransaction();
            try {
                //添加订单信息
                $orderReturn=OrderService::init()->addD($order, false);

                if(!$orderReturn)
                {
                    throw new \Exception('创建订单失败');
                }

                /*如果是自提订单，创建订单自提信息 wuxiaoping 2018.06.05*/
                if ($order['is_hexiao'] && $order['is_hexiao'] == 1) {
                    $zitiData['wid']           = $wid;
                    $zitiData['mid']           = $mid;
                    $zitiData['oid']           = $orderReturn;
                    $zitiData['ziti_id']       = $zitiId;
                    $zitiData['ziti_contact']  = $zitiContact;
                    $zitiData['ziti_phone']    = $zitiPhone;
                    $zitiData['ziti_datetime'] = $zitiDatetime;
                    (new OrderZitiService())->add($zitiData);
                }

                //添加订单详情0
                foreach($orderDetails as $k => $item)
                {
                    $item['oid']=$orderReturn;

                    //redis的oid字段一直保存错误 Herry 20180119
                    $orderDetails[$k] = $item;

                    //清除创建订单明细不需要的字段
                    unset($item['is_attr']);
                    $orderDetailReturn=OrderDetailService::init()->addD($item, false);
                    if(!$orderDetailReturn)
                    {
                        throw new \Exception('添加订单明细失败');
                    }
                }
                //更改商品库存
                foreach($orderDetails as $item)
                {
                    $num=$item['num'];
                    if($item['is_attr']==1)
                    {
                        //更改有规格商品 数据库库存
                        $propList = (new ProductPropsToValuesService())->getSkuList($item['product_id']);
                        if(!empty($propList['stocks']))
                        {
                            $productSkuService=new ProductSkuService();
                            //更改商品表中的总库存和规格信息
                            foreach($propList['stocks'] as $propItem)
                            {
                                if($propItem['id']==$item['product_prop_id'])
                                {
                                    $propStock = $propItem['stock_num']-$num;
                                    if($propStock<0)
                                    {
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
                    if(!empty($productInfo))
                    {
                        //库存
                        $productStock=$productInfo['stock']-$num;
                        $data=['stock' =>$productStock, 'sold_num' => $productInfo['sold_num'] + $num];
                        if($productStock<0)
                        {
                            throw new \Exception('库存不足');
                        }
                        //库存为0，商品更改为下架
                        /*if($productStock==0)
                        {
                            $data['status']=0;
                        }*/

                        ProductService::update($item['product_id'],$data);
                    }
                }
                DB::commit();
            }
            catch (\Exception $e){
                DB::rollback();//事务回滚
                //echo $e->getCode();
                $message=$e->getMessage();
                $returnData['errCode']=-7;
                //$returnData['errMsg']='下单失败';
                $returnData['errMsg']=$message;
                return $returnData;
            }

            //更改优惠券的状态
            $couponId && $couponLogService->update($couponId, ['status' => 1, 'oid' => $orderReturn]);

            //订单流水
            $orderLog=[];
            $orderLog['oid']=$orderReturn;
            $orderLog['wid']=$wid;
            $orderLog['mid']=$mid;
            $orderLog['action']=1;
            $orderLog['created_at']=$createTime;
            //添加订单流水
            OrderLogService::init()->addD($orderLog, false);
            $order['id']=$orderReturn;
            $order['orderDetail']=$orderDetails;
            //redis订单日志字段 存二维数组
            $order['orderLog']=[$orderLog];
            //订单信息插入到缓存
            OrderService::init('mid',$mid)->addR($order,false);
            OrderService::init('wid',$wid)->addR($order,false);
            //计算分销
            dispatch((new Distribution($order,'2'))->onQueue('Distribution'));
            //清除购物车
            foreach($cartId as $id) {
                $cartService->init('mid',$mid)->where(['id'=>$id])->delete($id,false);
            }

            //积分订单去掉使用的积分
            if($point>0&&$myPoint>0)
            {
                //point_type为4表示积分抵现
                $pointRecordData=['wid'=>$wid,'mid'=>$mid,'point_type'=>4,'is_add'=>0,'score'=>$point];
                //消费积分记录
                OPointRecordService::insertData($pointRecordData);
                //更改用户积分
                $this->memberService->incrementScore($mid,'-'.$point);
            }

            (new MessagePushModule($wid,MessagesPushService::TradeUrge))->setDelay(60)->sendMsg($orderReturn);
            $returnData['data']=$orderReturn;
            return $returnData;
        }
        //其他
        $returnData['errCode']=-100;
        $returnData['errMsg']='没有找到对应的逻辑判断';
        return  $returnData;
    }

    /**
     * [检查订单商品是否为核销商品，且是否在核销有效时间内]
     * @param  [array] $products [购物车商品信息]
     * @return [type]           [description]
     */
    public function checkProduct($products)
    {

        if(empty($products)){
            error('操作非法');
        }

        $returnData = [];
        foreach($products as $key=>$product){
            //商品设置为核销商品，且不超过设置的核销时间
            $startTime = strtotime($product['hexiao_start'].'00:00:00');
            $endTime   = strtotime($product['hexiao_end'].'23:59:59');
            if($product['is_hexiao'] == 1 && ( $startTime <= time() && time() <= $endTime ) ){ //核销商品
                $returnData['is_hexiao'][] = $product;
            }else{
                $returnData['normal'][] = $product;
            }
        }

        return $returnData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170710
     * @desc 创建团购订单
     * @param $cardId
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @update 何书哲 2018年11月16日 标记外卖订单
     * @update 何书哲 2018年11月22日 外卖店铺提交订单添加约束
     */
    public function createGroupsOrder($cardId,$request)
    {
        $orderData['oid'] = OrderCommon::createOrderNumber();
        $orderData['trade_id'] = $orderData['oid'];
        $orderData['wid'] = session('wid');
        $orderData['mid'] = session('mid');
        $orderData['umid'] = session('umid');

        //购物车信息
        $cartData = Cart::find($cardId[0]);
        if ($cartData){
            $cartData = $cartData->toArray();
        }else{
           error();
        }

        //查看购物车的商品详情
        $productData = ProductService::getDetail($cartData['product_id']);

        //判断该团购是否可以下单
        $groupsData = (new GroupsService())->getRowById($cartData['groups_id']);
        if ($groupsData['status'] != 1 && $groupsData['status'] != 0){
            $returnData['errCode']=-3;
            $returnData['errMsg']='亲！该团已完成，赶紧再去开一个团吧！';
            //坑
            exit(json_encode($returnData));
        }

        //计算商品价格,团长优惠金额，最终价格等
        $result = $this->groupsOrder($cartData);
        $orderData['pay_price'] = $result['lastAmount'];
        $orderData['products_price'] = $result['productTotalAmount'];
        $orderData['freight_price'] = $result['freight'];
        $address = OrderService::getDeliveryAddress(session('umid'));
        if (!$address){
            $returnData['errCode']=-3;
            $returnData['errMsg']='请选择收货地址';
            //坑
            exit(json_encode($returnData));
        }

        //何书哲 2018年11月22日 外卖店铺提交订单添加约束
        $checkRes = (new StoreModule())->checkIfSubmitOrder(session('wid'));
        if ($checkRes['errCode'] != 0) {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = $checkRes['errMsg'];
            exit(json_encode($returnData));
        }

        $orderData['address_id'] = $address['areaId'];
        $orderData['address_name'] = $address['name'];
        $orderData['address_phone'] = $address['phone'];
        $orderData['address_detail'] = $address['address'];
        $orderData['type'] = 3;
        $orderData['buy_remark'] = $request->input('remark')??'';
        $orderData['discount_amount'] = $result['head_discount'];
        $orderData['head_discount'] = $result['head_discount'];
        $orderData['groups_id'] = $cartData['groups_id'];
        $orderData['use_point'] = 0;
        $orderData['groups_status'] = 1;
        $orderData['is_takeaway'] = (new StoreModule())->getWidTakeAway(session('wid')) ? 1 : 0;//何书哲 2018年11月16日 标记外卖订单

        //当团购商品为核销商品，并且在商品核销的有效时间内生成核销订单
        //update by wuxiaoping 2018.06.11
        if ($request->input('isHexiao') == 1) {
            $orderData['is_hexiao'] = 1;
            $hexiaoCode = rand(5000,9999).rand(1000,4999).rand(100,999); //生成七位提货码
            $orderData['hexiao_code'] = $hexiaoCode;
        }

        DB::beginTransaction();
        $id = Order::insertGetId($orderData);
        $orderData = Order::find($id)->toArray();
        //减库存
        $this->reduceStock($cartData['product_id'],$cartData['prop_id'],$cartData['num']);
        //创建订单详情
        $orderDetailData = OrderService::createOrderDetail($orderData,$cartData);
        //添加订单日志
        $orderLogData = OrderService::addOrderLog($orderData['id'], session('wid'), session('mid'));
        //存redis
        $orderData['orderDetail'][] = $orderDetailData;
        $orderData['orderLog'] = $orderLogData;
        OrderService::init()->addR($orderData,false);
        //删除g购物车
        (new CartService())->init()->delete($cartData['id'],false);
        DB::commit();
       return $orderData;

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170726
     * @desc 生成订单减少库存
     * @param $pid
     * @param $skuid
     */
    public function reduceStock($pid,$skuid,$num)
    {
        $productData = ProductService::getDetail($pid);
        $up['stock'] = $productData['stock']-$num;
        $up['sold_num'] = $productData['sold_num']+$num;
        ProductService::update($pid,$up);
        if ($skuid != 0){
            $productSkuService = new ProductSkuService();
            $productSkuData = $productSkuService->getRowById($skuid);
            $skuData['stock'] = $productSkuData['stock']-$num;
            $productSkuService->update($skuid,$skuData);
        }

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170407
     * @desc 延长收货时间
     * @param $wid
     * @param $oid
     */
    public function receiveDelay($wid,$oid)
    {
        $orderData = OrderService::init('wid',$wid)->model->find($oid)->load('orderLog')->toArray();
        if ($orderData['mid'] != session('mid')){
            error('操作非法');
        }
        //判断该订单是否可以取消退款
        if ($orderData['status'] != 2){
            error('该订单暂时无法确认收货');
        }
        //判断是否可以延期
        foreach ($orderData['orderLog'] as $val)
        {
            if ($val['action'] == 13){
                error('亲！你已申请过延期了！');
            }elseif ($val['action'] == 3){
                $day = (int)((time()-strtotime($val['created_at']))/86400);
                $autoReceiveDays = config('app.auto_confirm_receive_days');
                if (($autoReceiveDays-3)>$day){
                    error('亲！发货后'.($autoReceiveDays-3).'天后才能延期收货哦');
                }
            }
        }
        $orderLog = [
            'oid'       => $oid,
            'wid'       => $wid,
            'mid'       => session('mid'),
            'action'    => 13,
            'remark'    => '买家申请延期',
        ];
        OrderLogService::init('wid',session('wid'))->add($orderLog);
        OrderService::upOrderLog($oid, $wid);

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @desc 上传文件
     * @param Request $request
     * @param FileInfoService $fileInfoService
     */
    public function upfile(Request $request,FileInfoService $fileInfoService)
    {
        if ($request->hasFile('file')){
            $result = $fileInfoService->upFile($request->file('file'));
            if ($result['success'] == 1){
                $content = array( 'status' => 1, 'info' => '上传成功', 'url' => '', 'data' => $result['data'] );
                echo  json_encode($content);
                exit();
            }else{
                error('文件上传失败');
            }
        }else{
            error('请上传文件');
        }
    }


/**
 * 
 * @desc 物流
 * @return [type] [description]
 */
    public function expresslist($wid,$id)
    {

      return view('shop.order.expresslist',

            [
                'title'       => '物流信息',
                'id'          => $id,
            ]
        );
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170504
     * @desc 获取物流信息
     * @param LogisticsService $logisticsService
     * @param $id
     */
    public function getLogistics(LogisticsService $logisticsService,$wid,$id)
    {
        $res = $logisticsService->getLogistics($id);
        if ($res['success'] == 1){
            success('','',$res['data']);
        }else{
            error($res['message']);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20150515
     * @desc 分享订单
     * @param Request $request
     * @param $id
     */
    public function share(Request $request,$wid,$id)
    {
        list($orderDetail) = OrderDetailService::init()->where(['oid'=>$id])->getList(false);

        return view('shop.order.share', [
            'title'			=> '订单分享',
            'orderDetail'  => $orderDetail['data'],
            'shareData'   => (new PublicShareService())->publicShareSet($wid)
        ]);
    }

    /**
     * 根据购物车获取运费
     */
    public function getFreight(Request $request)
    {
        if (!$request->input('tag')){
            //会员卡免运费
            $freeFreight = false;
            $userCard = MemberCardRecordService::useCard(session('mid'),session('wid'));
            if($userCard['errCode']==0&&$userCard['data']['isOwn']==1)
            {
                $freeFreight = $userCard['data']['info']['isDelivery'] ? true : false;
            }
            if ($freeFreight) {
                return success('', '', 0.00);
            }

        }
        //非免运费
        $freight = 0.00;
        $cartIDArr = $request->input('cartIDArr');
        $address_id = $request->input('address_id', 0);
        $cartIDArr = json_decode($cartIDArr, true);
        if (!empty($cartIDArr) && is_array($cartIDArr)) {
            //获取运费
            $freight = (new OrderModule())->getFreightByCartIDArr($cartIDArr, session('wid'), session('mid'), session('umid'), $address_id);
        }

        return success('', '', $freight);
    }

    /*
     *  取消订单  陈文豪
     * @modify author 张国军 2018年08月07日 虚拟订单付过款不能够取消
     */
    public function cancelOrder($wid, $oid)
    {
        $orderData = OrderService::init('wid',$wid)->model->find($oid)->load('orderLog')->toArray();
        if ($orderData['mid'] != session('mid')){
            return myerror('操作非法');
        }
        //判断该订单是否可以取消退款
        if ($orderData['status'] != 0){
            return myerror('该订单买家已付款，暂时无法取消');
        }

        //add by 张国军 2018年08月06日 付过款的虚拟订单不能够取消
        if(isset($orderData['type'])&&isset($orderData['status'])&&$orderData['type']==12&&($orderData['status']==1||$orderData['status']==2||$orderData['status']==3))
        {
            return myerror('已付过款的虚拟订单不能够取消');
        }


        //退还积分
        if ($orderData['use_point'] > 0) {
            $point = $orderData['use_point'];
            $pointRecordData = [
                'wid'           =>  $wid,
                'mid'           =>  session('mid'),
                'point_type'    =>  6,
                'is_add'        =>  1,
                'score'         =>  $point
            ];

            OPointRecordService::insertData($pointRecordData);
            $this->memberService->incrementScore(session('mid'), $point);            
        }

        //退还优惠券
        $orderData['coupon_id'] && (new CouponLogService())->update($orderData['coupon_id'], ['status' => 0, 'oid' => 0]);

        //修改订单状态
        OrderService::init('wid',$wid)->where(['id'=>$oid])->update(['id'=>$oid,'status'=>4],false);

        list($orderDetails) = OrderDetailService::init()->where(['oid'=>$oid])->getList(false);
        foreach($orderDetails['data'] as $item)
        {
            $num=$item['num'];
            if(!empty($item['product_prop_id'])) {
                //更改有规格商品 数据库库存
                ProductSku::where('id', $item['product_prop_id'])->increment('stock', $num);
                (new SkuRedis())->incr($item['product_prop_id'], 'stock', $num);
            }

            //更新商品
            Product::where('id', $item['product_id'])->increment('stock', $num);
            (new ProductRedis())->incr($item['product_id'], 'stock', $num);
        }

        $orderLog = [
            'oid'       => $oid,
            'wid'       => $wid,
            'mid'       => session('mid'),
            'action'    => 6,
            'remark'    => '买家取消订单',
        ];
        OrderLogService::init('wid',session('wid'))->add($orderLog, false);
        OrderService::upOrderLog($oid, $wid);

        //如果是秒杀订单 需要返还秒杀库存 Herry
        if (!empty($orderData['seckill_id'])) {
            (new SeckillModule())->returnSeckillStock($orderData['id'], $orderData['seckill_id']);
        }

        return mysuccess('操作成功');
    }


    /**
     * 自提订单凭证页面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function zitiVoucher(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $type = $request->input('type') ?? 'success';
        $result = $this->getZitiOrderInfo($oid);
        $orderData = $result['data'];
        if ($result['errCode'] && empty($orderData)) {
            error($result['errMsg']);
        }
        // 生成核销二维码
        $commonModule = new CommonModule(); 
        $shopUrl = config('app.url') . 'shop/order/hexiaoConfirm?wid='.$orderData['wid'].'&oid='.$orderData['id'];
        $url = $commonModule->qrCode($orderData['wid'], $shopUrl);
        $qrcodeData['qrcode'] = $url;
        $qrcodeData['url'] = $shopUrl;

        return view('shop.order.zitiVoucher',[
            'title'      => '自提订单凭证',
            'orderData'  => $orderData,
            'qrcodeData' => $qrcodeData,
            'type'       => $type,
        ]);
    }

    /**
     * 长连接接口
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function scanLongConnet(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $result = $this->getZitiOrderInfo($oid);
        $orderData = $result['data'];
        if ($result['errCode'] && empty($orderData)) {
            error($result['errMsg']);
        }
        $time_count = 0;
        $returnData = ['status' => 0,'msg' => ''];
        set_time_limit(0);//无限请求超时时间
        while (true) {
            usleep(500000); // 0.5秒   
            $time_count++; 
            if ($orderData['status'] == 1) {
                $returnData = ['status' => 1,'msg' => '订单扫码核销正常'];
                return $returnData;
            }else if ($orderData['status'] == 2) {
                $returnData = ['status' => 200,'msg' => '订单已核销成功'];
                return $returnData;
            }
            if ($time_count >= 30) {
                $returnData['msg'] = '长时间未操作';
                echo json_encode($returnData);
                break;
            }
        }
    }

    /**
     * 商家扫码核销确认页面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function hexiaoConfirm(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $wid = session('wid');
        $mid = session('mid');
        $memberInfo = $this->memberService->getRowById($mid);
        if (empty($memberInfo)) {
            error('该用户不存在');
        }
        if (!(new WeixinUserService())->isBindWeixin($wid,$mid)) {
            error('未绑定店铺核销员,不能进行扫码核销操作');
        }
        $result = $this->getZitiOrderInfo($oid);
        if ($result['errCode']) {
            error('errMsg');
        }
        $orderData = $result['data'];
        if ($orderData['status'] == 2) {
            error('订单已核销');
        }
        return view('shop.order.hexiaoConfirm',[
            'title'     => '到店自提订单核销',
            'orderData' => $orderData,
            'flag'      => 0
        ]);
    }

    /**
     * 用户展示核销码核销成功跳转页面
     * @return [type] [description]
     */
    public function hexiaoRedirect(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $wid = session('wid');
        $result = $this->getZitiOrderInfo($oid);
        if ($result['errCode'] == -1) {
            return redirect()->back()->withInput()->withErrors('参数错误，数据异常');
        }else if($result['errCode'] == -2) {
            return redirect()->back()->withInput()->withErrors('该订单不存在，或已删除');
        }
        $orderData = $result['data'];
        $flag = 1;
        return view('shop.order.hexiaoConfirm',[
            'title'     => '到店自提订单核销',
            'orderData' => $orderData,
            'flag'      => $flag
        ]);
    }

    /**
     * 确认核销
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function hexiaoSure(Request $request)
    {
        $returnData = ['errCode' => 0,'errMsg' => '','data' => []];
        $oid = $request->input('oid') ?? 0;
        $mid = session('mid');
        $wid = session('wid');
        $result = $this->getZitiOrderInfo($oid);
        if ($result['errCode']) {
            $returnData['errCode'] = $result['errCode'];
            $returnData['errMsg']  = $result['errMsg'];
            return $returnData;
        }
        if (!OrderService::init('wid',$wid)->where(['id'=>$oid])->update(['status' => 2],false)) {
            $returnData['errCode'] = -5;
            $returnData['errMsg']  = '订单核销失败';
            return $returnData;
        }
        return $returnData;
    }

    /**
     * 获取自提订单相关信息
     * @author 吴晓平 <2018年09月21日>
     * @param  integer $oid [订单表id]
     * @return [type]       [description]
     */
    public function getZitiOrderInfo($oid = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($oid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '参数错误，数据异常';
            return $returnData;
        }
        $orderData = Order::with('orderDetail')->where(['id' => $oid,'is_hexiao' => 1])->first();
        if (!$orderData) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '该订单不存在，或已删除';
            return $returnData;
        }
        $orderData = $orderData->toArray();
        $where['oid'] = $oid;
        $zitiData = (new OrderZitiService())->getDataByCondition($where);
        if ($zitiData) {
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
            $orderData['ziti'] = $zitiData;
        }
        $returnData['data'] = $orderData;
        return $returnData;
    }

}

