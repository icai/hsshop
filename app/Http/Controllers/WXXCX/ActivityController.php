<?php
/**
 * Created by PhpStorm.
 * User: Herry
 * Date: 2017/9/26
 * Time: 9:39
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\Model\ActivityAwardAddress;
use App\Module\BonusModule;
use App\Module\CouponModule;
use App\Module\ProductModule;
use App\Module\ResearchModule;
use App\Module\ScratchModule;
use App\Module\SeckillModule;
use App\Module\WheelModule;
use App\S\Market\ActivityAwardAddressService;
use App\S\Market\CouponCardService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Market\EggMemberService;
use App\S\Market\EggPrizeService;
use App\S\Market\ResearchRecordService;
use App\S\Member\MemberService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Scratch\ActivityScratchLogService;
use App\S\Scratch\ActivityScratchService;
use App\S\Scratch\ActivityScratchWinService;
use App\S\Wechat\WeixinCouponLogService;
use App\S\Wheel\ActivityWheelLogService;
use App\S\Wheel\ActivityWheelPrizeService;
use App\S\Wheel\ActivityWheelService;
use App\S\Wheel\ActivityWheelWinService;
use App\Services\MemberCardRecordService;
use App\Services\Shop\MemberAddressService;
use App\Services\Wechat\ApiService;
use Illuminate\Http\Request;
use WeixinService;
use ProductService;
use App\S\PublicShareService;
use App\S\Weixin\ShopService;

class ActivityController extends Controller
{
    private $_activityCouponService;
    private $_couponLogService;

    public function __construct(CouponService $activityCouponService, CouponLogService $couponLogService)
    {
        $this->_activityCouponService = $activityCouponService;
        $this->_couponLogService = $couponLogService;
    }

    /**
     * 优惠券详情
     * @update 许立 2018年09月11日 优化代码
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function couponDetail(Request $request,ShopService $shopService,$id)
    {
        //获取优惠券详情
        $coupon = $this->_activityCouponService->getDetail($id);
        $coupon || xcxerror('优惠券不存在');

        $wid = $request->input('wid');

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);

        $coupon['shop_name'] = $shop['shop_name'] ?? '';
        $coupon['shop_logo'] = $shop['logo'] ?? '';

        /*add  WuXiaoPing 2017.08.14  分享数据*/
        $shareData = [];
        $shareData['share_title'] = $coupon['share_title'] ? $coupon['share_title'] : $coupon['shop_name'].'-'.$coupon['title'];
        $shareData['share_desc']  = $coupon['share_desc'] ? str_replace(PHP_EOL, '', $coupon['share_desc']) : $coupon['description']; //去掉换行符
        $shareData['share_img']   = $coupon['share_img'] ? imgUrl().$coupon['share_img'] : imgUrl().$coupon['shop_logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if(empty($coupon['share_title']) && empty($coupon['share_desc']) && empty($coupon['share_img'])){
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $coupon['shop_name'].'-优惠券';
        }

        xcxsuccess('', ['coupon' => $coupon, 'share' => $shareData]);
    }

    /**
     * 领取优惠券接口
     * @update 许立 2018年09月12日 优化代码
     */
    public function couponReceive(Request $request,ShopService $shopService, $id)
    {
        //参数
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];

        //获取优惠券详情
        $coupon = $this->_activityCouponService->getDetail($id);
        $coupon || xcxerror('优惠券不存在');

        //用户信息
        $member = (new MemberService())->getRowById($mid);
        $member || xcxerror('用户不存在');

        //领取失败返回信息
        //总领取金额
        $list = $this->_couponLogService->getCoupons('valid', $wid, $mid);
        $returnData = [
            'avatar' => $member['headimgurl'],
            'sum'    => sprintf('%.2f', $list['coupon_total_amount'])
        ];

        //判断领取资格
        $receiveFlag = true;
        $now = date('Y-m-d H:i:s');
        if (!empty($coupon['invalid_at'])) {
            $receiveFlag = false;
            $returnData['tip1'] = '该优惠券已经失效';
            $returnData['tip2'] = '该优惠券已经失效';
            $returnData['tip3'] = '不能领取';
        } elseif ($coupon['left'] < 1) {
            $receiveFlag = false;
            $returnData['tip1'] = '您来晚了, 已经被领光了~';
            $returnData['tip2'] = '您来晚了';
            $returnData['tip3'] = '该券已经被领光了~';
        } elseif ($coupon['member_card_id']) {
            //指定会员卡才能领取
            $res = (new MemberCardRecordService())->init('wid', $wid)
                ->where(['mid' => $mid, 'card_id' => $coupon['member_card_id']])
                ->getInfo();
            if ($receiveFlag && empty($res)) {
                $receiveFlag = false;
                $returnData['tip1'] = '您的会员等级不满足领取条件';
                $returnData['tip2'] = '请联系商家';
                $returnData['tip3'] = '提升会员等级后领取';
            }
        } elseif ($coupon['expire_type'] == 0 && $now > $coupon['end_at']) {
            $receiveFlag = false;
            $returnData['tip1'] = '该优惠券已经过期';
            $returnData['tip2'] = '该优惠券已经过期';
            $returnData['tip3'] = '不能领取';
        }

        $show = true;
        $resultCoupon = [];

        //生效时间过期时间新需求 20171127 Herry
        $start = '0000-00-00 00:00:00';
        $end = '0000-00-00 00:00:00';

        if ($receiveFlag) {
            /***优化微信同步卡券功能***/
            $show = false;
            $resultCoupon['err'] = 0;
            // todo 重新获取weixinCoupon
            $resultCoupon['card_id'] = (new CouponCardService())->getRowByCouponId($id)->card_id ?? 0;

            //查询领取记录 是否超过定额
            $couponLogCount = $this->_couponLogService->getCount(['coupon_id' => $coupon['id'], 'mid' => $mid]);
            if ($receiveFlag && $coupon['quota'] && $couponLogCount >= $coupon['quota']) {
                $receiveFlag = false;
                $returnData['tip1'] = '您已经达到领取优惠券定额了';
                $returnData['tip2'] = '已达到领取定额';
                $returnData['tip3'] = '不能领取';
            } else {
                //全部条件符合 执行领取
                //随机领取备注
                $list = $this->_activityCouponService->getStaticList();

                //生效时间过期时间新需求 20171127 Herry
                $start = $coupon['start_at'];
                $end = $coupon['end_at'];
                if ($coupon['expire_type'] == 1) {
                    //领到券当日零点开始N天内有效
                    $start = date('Y-m-d') . ' 00:00:00';
                    $days = $coupon['expire_days'] - 1;
                    $end = date('Y-m-d', strtotime("+" . $days . " days")) . ' 23:59:59';
                } elseif ($coupon['expire_type'] == 2) {
                    //领到券次日零点开始N天内有效
                    $start = date('Y-m-d', strtotime("+1 day")) . ' 00:00:00';
                    $days = $coupon['expire_days'];
                    $end = date('Y-m-d', strtotime("+" . $days . " days")) . ' 23:59:59';
                }

                //成功领取
                $amount = $coupon['is_random'] ? rand($coupon['amount'] * 100, $coupon['amount_random_max'] * 100) / 100 : $coupon['amount'];
                $data = [
                    'wid'          => $wid,
                    'mid'          => $mid,
                    'coupon_id'    => $coupon['id'],
                    'title'        => $coupon['title'],
                    'amount'       => $amount,
                    'limit_amount' => $coupon['limit_amount'],
                    'start_at'     => $start,
                    'end_at'       => $end,
                    'remark'       => $list[1][array_rand($list[1])],
                    'range_type'   => $coupon['range_type'],
                    'range_value'  => $coupon['range_value'],
                    'only_original_price' => $coupon['only_original_price']
                ];
                $couponReceiveID = $this->_couponLogService->createRow($data);
                if ($couponReceiveID) {
                    //优惠券规则表 库存减1
                    $this->_activityCouponService->increment($id, 'left', -1);
                    if ($receiveFlag) {
                        //获取店铺名
                        //$shop = WeixinService::getStageShop($wid);
                        $shop = $shopService->getRowById($wid);
                        $coupon['avatar'] = $member['headimgurl'];
                        $coupon['shop_name'] = $shop['shop_name'];
                        $coupon['mobile'] = $member['mobile'];
                        $coupon['receive_amount'] = $amount;
                        $coupon['sum'] = sprintf('%.2f', $returnData['sum'] + $amount);
                        $returnData = $coupon;
                    }
                } elseif ($receiveFlag) {
                    $receiveFlag = false;
                    $returnData['tip1'] = '领取优惠券失败';
                    $returnData['tip2'] = '领取优惠券失败';
                    $returnData['tip3'] = '领取失败';
                }
            }
        }

        $data = [
            'id' => $id,
            'wid' => $wid,
            'data' => $returnData,
            'result' => $resultCoupon,
            'show' => $show,
            'receiveFlag' => $receiveFlag,
            'start_at' => $start,
            'end_at' => $end,
            'couponReceiveID' => $couponReceiveID ?? 0,
        ];

        xcxsuccess('', $data);
    }

    /**
     * 某优惠券领取列表
     * @update 许立 2018年09月12日 优化代码
     */
    public function couponReceiveList(Request $request, $id)
    {
        list($list) = $this->_couponLogService->listWithPage(['coupon_id' => $id]);
        $list['data'] || xcxsuccess('', $list);

        //获取用户信息
        $list['data'] = (new CouponModule())->handleCouponLogMember($list['data']);

        xcxsuccess('', $list);
    }

    /**
     * 创建微信卡券
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @param int $id 优惠券id
     * @return array
     * @update 许立 2018年09月12日 优化代码
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年09月26日 微信卡券service优化
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function createWeixinCoupon($wid, $mid, $id)
    {
        //获取优惠券详情
        $coupon = $this->_activityCouponService->getDetail($id);
        //获取相关店铺信息
        $info = (new ShopService())->getRowById($wid);
        //优惠券可用条件
        $productTitles = '';
        if($coupon['range_type'] == 1){
            $productData = ProductService::getListById(explode(',', $coupon['range_value']));
            foreach($productData as $p){
                $productTitles .= $p['title'] . ',';
            }
            $productTitles = '仅限购买以下商品时使用：' . substr($productTitles, 0, -1);
        }

        $coupon['only_original_price'] && $productTitles .= ' 仅原价购买商品时可用';

        //是否设置为分享
        $share = $coupon['is_share'] == 1 ? true : false;

        //是否设置满减门槛,使用优惠券减免情况
        if($coupon['is_limited'] == 0){
            if($coupon['is_random'] == 1){
                $default_detail = '下单使用优惠券将随机减免'.$coupon['amount'].'-'.$coupon['amount_random_max'].'元';
            }else{
                $default_detail = '下单使用优惠券将减免'.$coupon['amount'].'元';
            }
        }else{
            if($coupon['is_random'] == 1){
                $default_detail = '下单金额满'.$coupon['limit_amount'].'元将随机减免'.$coupon['amount'].'-'.$coupon['amount_random_max'].'元';
            }else{
                $default_detail = '下单金额满'.$coupon['limit_amount'].'元将减免'.$coupon['amount'];
            }
        }
        //显示在微信卡券上的logo图
        $logo_url = !empty($info['weixin_logo_url']) ? $info['weixin_logo_url'] : 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFNjakmxibMLGWpXrEXB33367o7zHN0CwngnQY7zb7g/0';
        $returnData = [];
        $returnData['card_type'] = 'GENERAL_COUPON';

        $couponCard = (new CouponCardService())->getRowByCouponId($id);
        $couponCard || error('微信卡券不存在');

        $returnData['general_coupon']['base_info'] = [
            "logo_url"      => $logo_url,
            "brand_name"    => $info['shop_name'],
            "code_type"     => "CODE_TYPE_BARCODE",
            "title"         => $couponCard->title,
            "color"         => $couponCard->color,
            "notice"        => "使用时向服务员出示此券",
            "service_phone" => $couponCard->service_phone,
            "description"   => $coupon['description'],
            "date_info"     => [
                "type"            => "DATE_TYPE_FIX_TIME_RANGE",
                "begin_timestamp" => strtotime($coupon['start_at']),
                "end_timestamp"   => strtotime($coupon['end_at'])
            ],
            "sku" => [
                "quantity" => $coupon['total']
            ],
            "custom_url_name"      => "立即使用",
            "custom_url"           => config('app.url').'shop/index/'.$wid,
            "custom_url_sub_title" => '进入店铺',
            "use_limit"            => 100,
            "get_limit"            => 1,
            "use_custom_code"      => false,
            "bind_openid"          => false,
            "can_share"            => $share,
            "can_give_friend"      => true
        ];
        $returnData['general_coupon']['default_detail'] = $default_detail;
        $result['card'] = $returnData;
        //处理生成优惠券的数据
        $result = (new ApiService())->wxCardCreated($wid,$result);
        $err = 0;
        if($result['errcode']){
            $err = 1; //表示创建卡券失败
        }else{
            //同步成功后把生成的卡券id存放到数据库中并更新redis
            $flag = (new CouponCardService())->model->where('id', $couponCard->id)->update(['card_id' => $result['card_id']]);
            if($flag){
                $dataLog['wx_coupon_id'] = $couponCard->id;
                $dataLog['mid']          = $mid;
                (new WeixinCouponLogService())->add($dataLog);
            }else{
                $err = 2; //表示保存数据库失败
            }
        }
        $return['err'] = $err;
        if($err==0){
            $return['card_id'] = $result['card_id'];
        }
        return $return;
    }

    //秒杀详情
    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function seckillDetail(Request $request,$id)
    {
        //参数
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];

        //获取秒杀活动
        $seckill = (new SeckillModule())->getSeckillDetail($id, true);
        unset($seckill['product']);
        //获取商品详情
        $product = (new ProductModule())->getDetail($seckill['seckill']['product_id'], $wid, $mid, true);

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = (new ShopService())->getRowById($wid);

        /*add by wuxiaoping 2017.08.14 分享数据*/
        $shareData = [];
        $shareData['share_title'] = $seckill['seckill']['share_title'] ? $seckill['seckill']['share_title'] : $seckill['seckill']['title'];
        $shareData['share_desc']  = $seckill['seckill']['share_desc'] ? str_replace(PHP_EOL, '', $seckill['seckill']['share_desc']) : $shop['shop_name'].'-'.$seckill['seckill']['title']; //去掉换行符
        $shareData['share_img']   = $seckill['seckill']['share_img'] ? imgUrl().$seckill['seckill']['share_img'] : imgUrl().$shop['logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if(empty($seckill['seckill']['share_title']) && empty($seckill['seckill']['share_desc']) && empty($seckill['seckill']['share_img'])){
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $shop['shop_name'].'-秒杀活动';

        }

        $data = [
            'seckill' => $seckill['seckill'],
            'product' => $product,
            'shareData' => $shareData
        ];

        xcxsuccess('', $data);
    }

    /**
     * 通用的分享设置
     * 2017.08.30 wuxiaoping
     * @param  [int] $wid [所在店铺的id]
     * @return [type]      [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function publicShareSet($wid)
    {
        //$storeInfo=WeixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $storeInfo = (new ShopService())->getRowById($wid);
        $store=['store_name'=>'','logo_url'=>config('app.source_url').'mctsource/images/m1logo.png'];
        if(!empty($storeInfo)) {
            $store['store_name'] = $storeInfo['shop_name'];
            if (!empty($storeInfo['logo'])) {
                $store['logo_url'] = imgUrl() . $storeInfo['logo'];
            }
        }

        /*add WuXiaoPing 2017-08-11*/
        $shareData['share_title'] = $storeInfo['share_title'] ? $storeInfo['share_title'] : $store['store_name'];
        $shareData['share_desc']  = $storeInfo['share_desc'] ? str_replace(PHP_EOL, '', $storeInfo['share_desc']) :''; //去掉换行符
        $shareData['share_img']  = $storeInfo['share_logo'] ? imgUrl() .$storeInfo['share_logo'] : $store['logo_url'];

        return $shareData;
    }

    /*
     * 获取规格列表
     */
    public function seckillSku(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            if (empty($id)) {
                xcxerror('参数不能为空');
            }

            //秒杀详情
            $seckillModule = new SeckillModule();
            $seckill = $seckillModule->getSeckillDetail($id, true);

            //秒杀商品原始sku
            $sku = (new ProductPropsToValuesService())->getSkuList($seckill['seckill']['product_id']);

            //组装最终秒杀库存
            empty($seckill['sku']) && xcxerror('秒杀商品价格库存不存在');
            $seckillSku = $seckillModule->getSeckillSku($sku, $seckill['sku']);

            //秒杀规格前端报错修改 20171204
            xcxsuccess('', ['data' => $seckillSku]);
        }

        xcxerror('只允许get方法');
    }


    /**
     * @author hsz
     * @date 20180515
     * @desc 刮刮卡
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function scratch(Request $request,ShopService $shopService)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        $scratchId = $request->input('scratch_id');
        if (!$scratchId) {
            xcxerror('参数不完整');
        }
        $scratchData = (new ActivityScratchService())->getRowById($scratchId);
        if (!$scratchData){
            xcxerror('活动不存在');
        }
        $logService = new ActivityScratchLogService();
        $scratchData['count'] = $logService->count(['scratch_id'=>$scratchId, 'mid'=>$mid, 'wid'=>$wid]);
        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);
        $scratchData['shop_name'] = $shop['shop_name'];

        $res = (new ScratchModule())->check($scratchData, $mid, $wid);
        if ($res['success']==0){
            xcxerror($res['message'], -100, ['data' => $scratchData]);
        }else{
            xcxsuccess('', ['data' => $scratchData]);
        }
    }

    /**
     * @author hsz
     * @date 20180515
     * @desc 刮刮卡操作
     */
    public function scratchPlay(Request $request)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        $scratchId = $request->input('scratch_id');
        if (!$scratchId) {
            xcxerror('参数不完整');
        }
        $scratchModule = new ScratchModule();
        $scratchService = new ActivityScratchService();
        $scratchData = $scratchService->getRowById($scratchId);
        if (!$scratchData) {
            xcxerror('活动不存在');
        }
        $res = $scratchModule->check($scratchData, $mid, $wid);
        if ($res['success'] == 0){
            xcxerror($res['message']);
        }
        //计算是否中奖
        $result = $scratchModule->compute($scratchData, $wid, $mid);
        if ($result['success'] == 1){
            if ($result['data'] && $result['data']['type'] == 2){
                $result['data']['title'] = $this->_couponLogService->getDetail($result['data']['content'])['title'] ?? '';
            } elseif ($result['data'] && $result['data']['type'] == 1){
                $result['data']['title'] =  $result['data']['content'].'积分';
            } elseif ($result['data'] && $result['data']['type'] == 3)  {//赠品
                $result['data']['title'] = $result['data']['content'];
                $result['data']['type'] = 3;
            } else {
                $result['data']['title'] = '';
                $result['data']['type'] = 0;
            }
            xcxsuccess($result['message'], $result['data']);
        } else {
            xcxerror($result['message']);
        }
    }

    /**
     * @author hsz
     * @desc 刮刮卡--我的赠品
     */
    public function myScratchGift(Request $request)
    {
        $mid = $request->input('mid');
        $where = [
            'mid'   => $mid,
            'type'  => ['<>','1'],
        ];
        $winService = new ActivityScratchWinService();
        list($winData) = $winService->getlistPage($where);
        foreach ($winData['data'] as $key=>&$val){
            if ($val['type'] == 2){
                $data = $this->_couponLogService->getDetail($val['content']);
                if (!$data){
                    unset($winData['data'][$key]);
                }
                $val['coupon'] = $data;
            }
        }
        xcxsuccess('操作成功',$winData);
    }

    /**
     * @author hsz
     * @desc 刮刮卡--删除我的赠品
     */
    public function delScratchGift(Request $request,$id)
    {
        $mid = $request->input('mid');
        $winService = new ActivityScratchWinService();
        $winData = $winService->getRowById($id);
        if (!$winData && $winData['mid'] != $mid){
            xcxerror('赠品不存在');
        }
        $winService->del($id)?xcxsuccess():xcxerror();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180517
     * @desc
     * @param $wid
     * @param $wheelId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 许立 2018年09月12日 优化代码
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function wheel(Request $request,ShopService $shopService,$wheelId)
    {
        $wheelData = (new ActivityWheelService())->getRowById($wheelId);
        if (!$wheelData){
            xcxerror('连接错误');
        }
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        $wheelData['prizeData'] = (new ActivityWheelPrizeService())->getByWheelId($wheelId);
        foreach ($wheelData['prizeData'] as &$val){
            if ($val['type'] == 2){
                $coupon = $this->_activityCouponService->model->select(['id','title'])->find($val['content']);
                $val['coupon'] = $coupon ? $coupon->toArray() : [];
            }
            if ($val['type'] == 4){
                $productdata =  ProductService::getRowById($val['content']);
                $val['title'] = $productdata['title']??'';
            }
        }
        $logService = new ActivityWheelLogService();
        $res = (new WheelModule())->checkTimes($wheelData,$mid);
        if ($res['success']==0){
            $wheelData['is_ok'] = 0;
        }else{
            $wheelData['is_ok'] = 1;
        }
        $wheelData['count'] = $logService->count(['wheel_id'=>$wheelId,'mid'=>$mid]);

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);

        /* add by wuxiaoping 2017.08.28 */
        $shareData['share_title'] = $wheelData['share_title'] ? $wheelData['share_title'] : $wheelData['title'];
        $shareData['share_desc']  = $wheelData['share_desc'] ? str_replace(PHP_EOL, '', $wheelData['share_desc']) : $shop['shop_name'].'-'.$wheelData['title']; //去掉换行符
        $shareData['share_img']   = $wheelData['share_img'] ? imgUrl().$wheelData['share_img'] : imgUrl().$shop['logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if(empty($wheelData['share_title']) && empty($wheelData['share_desc']) && empty($wheelData['share_img'])){
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $shop['shop_name'].'-幸运大转盘抽奖';
        }
        
        $result = [
            'title'         => $wheelData['title'],
            'desc'          => $wheelData['descr'],
            'start_time'   => $wheelData['start_time'],
            'end_time'   => $wheelData['end_time'],
            'reduce_integra'   => $wheelData['reduce_integra'],
            'rule'   => $wheelData['rule'],
            'times'   => $wheelData['times'],
        ];
        $prizeData = [];
        foreach ($wheelData['prizeData'] as &$item ){
            switch ($item['type']){
                case '1':
                    $item['show'] = $item['content'].'积分';
                    break;
                case '2':
                    $item['show'] = $item['coupon']['title']??'';
                    break;
                case '3':
                    $item['show'] = $item['content'];
                    break;
                case '4':
                    $item['show'] = $item['title'];
                    break;

            }
            $result['prizeData'][] = $item;
        }
        xcxsuccess('操作成功',$result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180517
     * @desc 转动大转盘
     * @param $wid
     * @param $wheelId
     */
    public function wheelPlay(Request $request,$wheelId)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $wheelModule = new WheelModule();
        $wheelService = new ActivityWheelService();
        $wheelData = $wheelService->getRowById($wheelId);
        if ($wid != $wheelData['wid']){
            xcxerror('该活动不属于该店铺');
        }
        $res = $wheelModule->check($wheelData,$mid);
        if ($res['success'] == 0){
            xcxerror($res['message']);
        }
        //计算是否中奖
        $result = $wheelModule->compute($wheelData,$wid,$mid);
        if ($result['success'] == 1){
            if ($result['data'] && $result['data']['type'] == 2){
                $result['data']['title'] = $this->_couponLogService->getDetail($result['data']['content'])['title'] ?? '';
            }elseif ($result['data'] && $result['data']['type'] == 4){
                $productdata =  ProductService::getRowById($result['data']['content']);
                $result['data']['title'] = $productdata['title']??'';
            }elseif ($result['data'] && $result['data']['type'] == 3){
                $result['data']['title'] =  $result['data']['content'];
            }elseif ($result['data'] && $result['data']['type'] == 1){
                $result['data']['title'] = $result['data']['content'].'积分';
            }
            xcxsuccess($result['message'],$result['data']);
        }else{
            xcxerror($result['message']);
        }
    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 220180517
     * @desc 我的赠品
     */
    public function myGift(Request $request)
    {
        $mid = $request->input('mid');
        $where = [
            'mid'   => $mid,
            'type'  => ['<>','1'],
        ];
        $winService = new ActivityWheelWinService();
        list($winData) = $winService->getlistPage($where);
        foreach ($winData['data'] as $key=>&$val){
            if ($val['type'] == 2){
                $data = $this->_couponLogService->getDetail($val['content']);
                if (!$data){
                    unset($winData['data'][$key]);
                }
                $val['coupon'] = $data;
            }
        }
        xcxsuccess('操作成功',$winData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170804
     * @desc 删除我的赠品
     */
    public function delGift(Request $request,$id)
    {
        $mid = $request->input('mid');
        $winService = new ActivityWheelWinService();
        $winData = $winService->getRowById($id);
        if (!$winData && $winData['mid'] != $mid){
            xcxerror('赠品不存在');
        }
        $winService->del($id)?xcxsuccess():xcxerror();
    }

    /**
     * 获取调查活动详情
     * @param int $id 活动id
     * @param int $mid 用户id
     * @return json
     * @author 许立 2018年7月5日
     * @update 何书哲 2018年7月23日 去掉sub_rules下的image的域名
     * @update 如果已到限制参与次数，则返回错误
     */
    public function researchDetail($id, $mid=0)
    {
        if (empty($id)) {
            xcxerror('参数不完整');
        }
        //何书哲 2018年7月27日 如果已到限制参与次数，则返回错误
        $research = (new ResearchModule())->getResearch($id);
        if ($mid) {
            // 判断参与次数
            $record_service = new ResearchRecordService();
            // 获取参与次数
            $answer_times = $record_service->getCount($id, $mid);
            if ($answer_times == 1 && $research['times_type'] == 0) {
                xcxerror('该活动只能参与一次');
            }
            if ($research['times_type'] == 2 && $answer_times == 10) {
                xcxerror('该活动最多参与10次');
            }
        }
        //何书哲 2018年7月23日 去掉sub_rules下的image的域名
        if (isset($research['rules']) && $research['rules']) {
            foreach ($research['rules'] as &$rule) {
                if (isset($rule['sub_rules']) && $rule['sub_rules']) {
                    foreach ($rule['sub_rules'] as &$sub_rule) {
                        if (isset($sub_rule['image']) && $sub_rule['image']) {
                            if (strpos($sub_rule['image'], config('app.source_url')) !== false) {
                                $sub_rule['image'] = str_replace(config('app.source_url'), '', $sub_rule['image']);
                            }
                            $sub_rule['image'] =  ltrim(parse_url($sub_rule['image'])['path'], '/');
                        }
                    }
                }
            }
        }
        xcxsuccess('', $research);
    }

    /**
     * 提交调查留言数据
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年7月5日
     * @update 许立 2018年08月23日 返回第几次提交
     */
    public function researchSubmit(Request $request)
    {
        if (!$request->isMethod('post')) {
            xcxerror('请求方式不正确');
        }
        $return = (new ResearchModule())->submitAnswer($request->input());
        if (!$return['err_code']) {
            xcxsuccess('', ['times' => (new ResearchRecordService())->getCount($request->input('id'), $request->input('mid'))]);
        } else {
            xcxerror($return['err_msg']);
        }
    }

    /**
     * 会员中心-我的留言记录
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年6月28日
     */
    public function myResearches(Request $request)
    {
        xcxsuccess('', (new ResearchModule())->memberResearches($request->input('mid')));
    }

    /**
     * 会员中心-我的留言记录-记录详情
     * @param Request $request 参数类
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年6月28日
     */
    public function researchRecord(Request $request, $id)
    {
        // 验证参数
        if (empty($id)) {
            xcxerror('活动ID不能为空');
        }

        $input = $request->input();
        xcxsuccess('', (new ResearchModule())->researchRecords($id, $input['mid'], $input['times']));
    }

    /**
     * 获取红包活动展示
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月18日
     * @update 许立 2018年07月27日 增加店铺参数过滤
     * @update 许立 2018年08月08日 返回活动名
     */
    public function bonusShow(Request $request)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        xcxsuccess('', (new BonusModule())->showWindow($wid, $mid));
    }

    /**
     * 拆红包
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月18日
     * @update 许立 2018年07月27日 增加店铺参数过滤
     */
    public function bonusUnpack(Request $request)
    {
        $return = (new BonusModule())->unpack($request->input('mid'), $request->input('wid'));
        if ($return['err_code']) {
            xcxerror($return['err_msg']);
        } else {
            xcxsuccess();
        }
    }

    /**
     * 获取红包活动展示
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月18日
     * @update 许立 2018年07月27日 增加店铺参数过滤
     */
    public function bonusDetail(Request $request)
    {
        $return = (new BonusModule())->unpackDetail($request->input('mid'), $request->input('wid'));
        if ($return['err_code']) {
            xcxerror($return['err_msg']);
        } else {
            xcxsuccess('', $return['data']);
        }
    }

    /**
     * 拆红包弹窗关闭
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月18日
     * @update 许立 2018年07月27日 增加店铺参数过滤
     */
    public function bonusClose(Request $request)
    {
        $return = (new BonusModule())->closeWindow($request->input('mid'), $request->input('wid'));
        if ($return['err_code']) {
            xcxerror($return['err_msg']);
        } else {
            xcxsuccess();
        }
    }

    /**
     * 营销活动-我的奖品-兑奖方式
     * @param Request $request 参数类
     * @param int $id 奖品记录id
     * @param int $type 活动类型 1:大转盘, 2:砸金蛋 3:刮刮卡
     * @return json
     * @author 许立 2018年08月24日
     * @update 许立 2018年08月29日 用户没有地址情况处理，先设置地址 后地址被删除情况处理
     * @update 许立 2018年09月13日 type不合法 返回空数据
     */
    public function method(Request $request, $id, $type = 1)
    {
        // 根据不同活动类型获取信息
        $activity_id = 0;
        $content = '';
        if ($type == ActivityAwardAddress::ACTIVITY_TYPE_WHEEL) {
            $res = (new ActivityWheelWinService())->getRowById($id);
            $res || xcxerror('中奖纪录不存在');
            $activity_id = $res['wheel_id'];
            $content = $res['content'];
        } elseif ($type == ActivityAwardAddress::ACTIVITY_TYPE_EGG) {
            $res = (new EggMemberService())->getRowById($id);
            $res || xcxerror('中奖纪录不存在');
            $activity_id = $res['egg_id'];
            // 获取兑奖方式说明
            $prizeRow = (new EggPrizeService())->getInfoById($res['prize_id']);
            $content = $prizeRow['method'] ?? '';
        } elseif ($type == ActivityAwardAddress::ACTIVITY_TYPE_SCRATCH) {
            $res = (new ActivityScratchWinService())->getRowById($id);
            $res || xcxerror('中奖纪录不存在');
            $activity_id = $res['scratch_id'];
            $content = $res['content'];
        } else {
            $return = [
                'activity_id' => $activity_id,
                'address' => [],
                'content' => $content
            ];
            xcxsuccess('', $return);
        }

        // 先取设置的地址 没有再取默认地址
        $awardAddress = (new ActivityAwardAddressService())->model
            ->where('activity_id', $activity_id)
            ->where('mid', $request->input('mid'))
            ->where('type', $type)
            ->get()
            ->toArray();
        $awardAddressId = $isConfirm = 0;
        if ($awardAddress) {
            $awardAddressId = $awardAddress[0]['address_id'];
            $isConfirm = $awardAddress[0]['is_confirm'];
        }
        $addressService = new MemberAddressService();
        if ($awardAddress) {
            $address = $addressService->getAddressById($awardAddressId) ?? [];
            if ($address) {
                $address['detail_address'] = $address['detail'] ?? '';
                $address['is_confirm'] = $isConfirm;
            }
        } else {
            $address = $addressService->getUserAddress($res['mid'])['default'][0] ?? [];
            if ($address) {
                $address['title'] = $address['name'];
                $address['is_confirm'] = 0;
            }
        }

        $return = [
            'activity_id' => $activity_id,
            'address' => $address,
            'content' => $content
        ];

        xcxsuccess('', $return);
    }

    /**
     * 营销活动用户奖品收货地址设置
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年08月24日
     */
    public function setAwardAddress(Request $request)
    {
        // 参数判断
        $input = $request->input();
        $mid = $input['mid'];
        if (empty($input['type']) || empty($input['activityId'])) {
            xcxerror('活动参数不完整');
        }

        // 设置
        $update = [];
        !empty($input['isConfirm']) && $update['is_confirm'] = 1;
        !empty($input['addressId']) && $update['address_id'] = $input['addressId'];

        // 查询记录是否存在
        $awardAddressService = new ActivityAwardAddressService();
        $row = $awardAddressService->model
            ->where('activity_id', $input['activityId'])
            ->where('mid', $mid)
            ->where('type', $input['type'])
            ->get()
            ->toArray();

        if ($row) {
            // 存在则更新
            $result = $awardAddressService->model
                ->where('activity_id', $input['activityId'])
                ->where('mid', $mid)
                ->where('type', $input['type'])
                ->update($update);
        } else {
            // 否则新增
            $update['activity_id'] = $input['activityId'];
            $update['mid'] = $mid;
            $update['type'] = $input['type'];
            $result = $awardAddressService->model->insertGetId($update);
        }

        // 返回
        if ($result) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }
}