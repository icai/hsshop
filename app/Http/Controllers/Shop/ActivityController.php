<?php
/**
 * Created by PhpStorm.
 * User: Herry
 * Date: 2017/3/30
 * Time: 11:24
 */

namespace App\Http\Controllers\shop;


use App\Http\Controllers\Controller;
use App\Lib\Redis\Wechat;
use App\Model\ActivityAwardAddress;
use App\Model\Favorite;
use App\Module\BonusModule;
use App\Module\CouponModule;
use App\Module\EggModule;
use App\Module\FavoriteModule;
use App\Module\GroupsRuleModule;
use App\Module\MeetingGroupsRuleModule;
use App\Module\ProductModule;
use App\Module\ResearchModule;
use App\Module\ScratchModule;
use App\Module\SeckillModule;
use App\S\Groups\GroupsDetailService;
use App\S\Market\ActivityAwardAddressService;
use App\S\Market\CouponCardService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Market\EggMemberService;
use App\Module\WheelModule;
use App\S\Market\EggPrizeService;
use App\S\Market\ResearchRecordService;
use App\S\Member\MemberService;
use App\S\Product\RemarkService;
use App\S\Scratch\ActivityScratchWinService;
use App\S\Scratch\ActivityScratchPrizeService;
use App\S\Scratch\ActivityScratchService;
use App\S\ShareEvent\LiSalesmanService;
use App\S\ShareEvent\SalesmanStatisticService;
use App\S\Wechat\WeixinCouponLogService;
use App\S\Wheel\ActivityWheelLogService;
use App\S\Wheel\ActivityWheelPrizeService;
use App\S\Wheel\ActivityWheelService;
use App\S\Wheel\ActivityWheelWinService;
use App\Services\MemberCardRecordService;
use App\Services\Shop\MemberAddressService;
use App\Services\Wechat\ApiService;
use Illuminate\Http\Request;
use ProductService;
use WeixinService;
use Bi;
use App\S\PublicShareService;
use Validator;

use App\S\Foundation\CreateShareImgService;
use QrCodeService;
use App\S\Weixin\ShopService;

class ActivityController extends Controller
{
    protected $_jumpUrl = 'shop/activity/';

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * 优惠券详情
     * @update 许立 2018年09月12日 优化代码
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function couponDetail(ShopService $shopService, $wid, $id)
    {
        $res = (new GroupsRuleModule())->checkShopIsPermission(session('wid'), 'hsshop_weixin_coupon');
        if (!$res) {
            return redirect('shop/member/noPermission');
        }

        //获取优惠券详情
        $coupon = (new CouponService())->getDetail($id);
        $coupon || error('优惠券不存在');

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);
        $coupon['shop_name'] = $shop['shop_name'] ?? '';
        $coupon['shop_logo'] = $shop['logo'] ?? '';
        /*add  WuXiaoPing 2017.08.14  分享数据*/
        $shareData = [];
        $shareData['share_title'] = $coupon['share_title'] ? $coupon['share_title'] : $coupon['shop_name'] . '-' . $coupon['title'];
        $shareData['share_desc'] = $coupon['share_desc'] ? str_replace(PHP_EOL, '', $coupon['share_desc']) : $coupon['description']; //去掉换行符
        $shareData['share_img'] = $coupon['share_img'] ? imgUrl() . $coupon['share_img'] : imgUrl() . $coupon['shop_logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if (empty($coupon['share_title']) && empty($coupon['share_desc']) && empty($coupon['share_img'])) {
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $coupon['shop_name'] . '-优惠券';
        }

        return view('shop.activity.couponDetail', array(
            'title' => '优惠券详情',
            'data' => $coupon,
            'wid' => $wid,
            'id' => $id,
            'shareData' => $shareData
        ));
    }

    /**
     * 领取优惠券页面
     * @update 许立 2018年09月12日 优化代码
     */
    public function couponReceive($wid, $id)
    {
        $id || error('优惠券id为空');

        return view('shop.activity.couponReceive', array(
            'title' => '领取优惠券详情',
            'wid' => $wid,
            'id' => $id,
            'shareData' => (new PublicShareService())->publicShareSet($wid) // 领取优惠券页分享数据
        ));
    }

    /**
     * 领取优惠券接口
     * @update 许立 2018年09月12日 优化代码
     * @update 许立 2018年09月26日 微信卡券service优化
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function couponReceiveApi(ShopService $shopService, $wid, $id)
    {
        $mid = session('mid');

        //获取优惠券详情
        $couponService = new CouponService();
        $coupon = $couponService->getDetail($id);
        $coupon || error('优惠券不存在');

        //用户信息
        $member = $this->memberService->getRowById($mid);
        $member || error('用户不存在');

        //领取失败返回信息
        //总领取金额
        $list = (new CouponLogService())->getCoupons('valid', $wid, $mid);
        $returnData = [
            'avatar' => $member['headimgurl'],
            'sum' => sprintf('%.2f', $list['coupon_total_amount'])
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
        } elseif ($coupon['expire_type'] == 0 && $now >= $coupon['end_at']) {
            $receiveFlag = false;
            $returnData['tip1'] = '该优惠券已经过期';
            $returnData['tip2'] = '该优惠券已经过期';
            $returnData['tip3'] = '不能领取';
        }

        $show = true;
        $resultCoupon = [];

        //领取后的实际生效时间
        $start = '0000-00-00 00:00:00';
        $end = '0000-00-00 00:00:00';

        if ($receiveFlag) {
            // 微信卡券
            $couponCard = (new CouponCardService())->getRowByCouponId($id);
            $couponCardId = $couponCard->id ?? 0;
            $couponCardCardId = $couponCard->card_id ?? 0;

            //后台有同步微信卡券，添加一条同步领取日志
            $weixinCouponLogService = new WeixinCouponLogService();
            if ($coupon['is_sync_weixin'] == 1) {
                $rs = $weixinCouponLogService->getInfoByWhere(['mid' => session('mid'), 'wx_coupon_id' => $couponCardId]);
                if ($rs) {
                    $weixinCouponLogService->add(['mid' => session('mid'), 'wx_coupon_id' => $couponCardId]);
                }
            }

            /***优化微信同步卡券功能***/
            if ($coupon['is_sync_weixin'] == 1 && (!isset($couponCardCardId) || empty($couponCardCardId))) {
                $resultCoupon = $this->createWeixinCoupon($id); //如果返回1表示创建卡券失败，2表示保存数据库失败
            } else {
                $show = false;
                $resultCoupon['err'] = 0;
                $resultCoupon['card_id'] = $couponCardCardId;
            }

            //查询领取记录 是否超过定额
            $couponLogService = new CouponLogService();
            $couponLogCount = $couponLogService->getCount(['coupon_id' => $coupon['id'], 'mid' => $mid]);
            if ($receiveFlag && $coupon['quota'] && $couponLogCount >= $coupon['quota']) {
                $receiveFlag = false;
                $returnData['tip1'] = '您已经达到领取优惠券定额了';
                $returnData['tip2'] = '已达到领取定额';
                $returnData['tip3'] = '不能领取';
            } else {
                //全部条件符合 执行领取
                //随机领取备注
                $list = $couponService->getStaticList();

                //成功领取
                $amount = $coupon['is_random'] ? rand($coupon['amount'] * 100, $coupon['amount_random_max'] * 100) / 100 : $coupon['amount'];

                //生效时间过期时间新需求 20171106 Herry
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

                $data = [
                    'wid' => $wid,
                    'mid' => $mid,
                    'coupon_id' => $coupon['id'],
                    'title' => $coupon['title'],
                    'amount' => $amount,
                    'limit_amount' => $coupon['limit_amount'],
                    'start_at' => $start,
                    'end_at' => $end,
                    'remark' => $list[1][array_rand($list[1])],
                    'range_type' => $coupon['range_type'],
                    'range_value' => $coupon['range_value'],
                    'only_original_price' => $coupon['only_original_price']
                ];
                $couponReceiveID = $couponLogService->createRow($data);
                if ($couponReceiveID) {
                    //优惠券规则表 库存减1
                    $couponService->increment($id, 'left', -1);
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
            'couponReceiveID' => $couponReceiveID ?? 0,
            'start_at' => $start,
            'end_at' => $end
        ];

        return mysuccess('', '', $data);
    }

    /**
     * 卡券H5投放，生成卡券扩展
     * @param  [string] $card_id [微信同步生成卡券的id]
     * @return [array]          [返回签名的数组数据]
     * @update 许立 2018年09月12日 优化代码
     */
    public function couponAuth($card_id)
    {
        //获取api_ticket
        $accessToken = (new ApiService())->getAccessToken(session('wid'));
        $wechatRedis = new Wechat('api_ticket');
        $api_ticket = '';
        if ($wechatRedis->get()) {
            $api_ticket = $wechatRedis->get();
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $accessToken . '&type=wx_card';
            $result = jsonCurl($url);
            if ($result['errcode']) {
                error('获取api_ticket失败');
            }
            $api_ticket = $result['ticket'];
            $wechatRedis->set($api_ticket);
        }
        //生成16位随机字符串
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $nonce_str = "";
        for ($i = 0; $i < 16; $i++) {
            $nonce_str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        //卡券的签名
        $signature = $this->getCardSign(time(), $api_ticket, $nonce_str, $card_id);

        //返回前端的签名数据
        $returnData['cardExt'] = ['timestamp' => time(), 'nonce_str' => $nonce_str, 'signature' => $signature];
        $returnData['card_id'] = $card_id;

        success('', '', $returnData);
    }

    //获取cardExt扩展签名
    public function getCardSign($timestamp, $cticket, $nonce_str, $card_id)
    {
        $card = [
            $timestamp,
            $cticket,
            $card_id,
            $nonce_str
        ];
        sort($card, SORT_STRING);
        $return = '';
        foreach ($card as $k => $v) {
            $return .= $v;
        }
        return sha1($return);
    }

    /**
     * 创建微信卡券
     * @update 许立 2018年09月12日 优化代码
     * @update 许立 2018年09月26日 微信卡券service优化
     */
    public function createWeixinCoupon($id)
    {
        $wid = session('wid');
        $shopService = new ShopService();
        //获取优惠券详情
        $coupon = (new CouponService())->getDetail($id);
        $coupon || error('优惠券不存在');

        //获取相关店铺信息
        $info = $shopService->getRowById($wid);
        //优惠券可用条件
        $productTitles = '';
        if ($coupon['range_type'] == 1) {
            $ids = explode(',', $coupon['range_value']);
            $productData = ProductService::getListById(explode(',', $coupon['range_value']));
            foreach ($productData as $p) {
                $productTitles .= $p['title'] . ',';
            }
            $productTitles = '仅限购买以下商品时使用：' . substr($productTitles, 0, -1);
        }

        $coupon['only_original_price'] && $productTitles .= ' 仅原价购买商品时可用';

        //是否设置为分享
        $share = $coupon['is_share'] == 1 ? true : false;

        //是否设置满减门槛,使用优惠券减免情况
        if ($coupon['is_limited'] == 0) {
            if ($coupon['is_random'] == 1) {
                $default_detail = '下单使用优惠券将随机减免' . $coupon['amount'] . '-' . $coupon['amount_random_max'] . '元';
            } else {
                $default_detail = '下单使用优惠券将减免' . $coupon['amount'] . '元';
            }
        } else {
            if ($coupon['is_random'] == 1) {
                $default_detail = '下单金额满' . $coupon['limit_amount'] . '元将随机减免' . $coupon['amount'] . '-' . $coupon['amount_random_max'] . '元';
            } else {
                $default_detail = '下单金额满' . $coupon['limit_amount'] . '元将减免' . $coupon['amount'];
            }
        }
        //显示在微信卡券上的logo图
        $logo_url = !empty($info['weixin_logo_url']) ? $info['weixin_logo_url'] : 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFNjakmxibMLGWpXrEXB33367o7zHN0CwngnQY7zb7g/0';
        $returnData = [];
        $returnData['card_type'] = 'GENERAL_COUPON';

        $couponCard = (new CouponCardService())->getRowByCouponId($id);
        $couponCard || error('微信卡券不存在');

        $returnData['general_coupon']['base_info'] = [
            "logo_url" => $logo_url,
            "brand_name" => $info['shop_name'],
            "code_type" => "CODE_TYPE_BARCODE",
            "title" => $couponCard->title,
            "color" => $couponCard->color,
            "notice" => "使用时向服务员出示此券",
            "service_phone" => $couponCard->service_phone,
            "description" => $coupon['description'],
            "date_info" => [
                "type" => "DATE_TYPE_FIX_TIME_RANGE",
                "begin_timestamp" => strtotime($coupon['start_at']),
                "end_timestamp" => strtotime($coupon['end_at'])
            ],
            "sku" => [
                "quantity" => $coupon['total']
            ],
            "custom_url_name" => "立即使用",
            "custom_url" => config('app.url') . 'shop/index/' . $wid,
            "custom_url_sub_title" => '进入店铺',
            "use_limit" => 100,
            "get_limit" => 1,
            "use_custom_code" => false,
            "bind_openid" => false,
            "can_share" => $share,
            "can_give_friend" => true
        ];
        $returnData['general_coupon']['default_detail'] = $default_detail;
        $result['card'] = $returnData;
        //处理生成优惠券的数据
        $result = (new ApiService())->wxCardCreated($wid, $result);
        $err = 0;
        if ($result['errcode']) {
            $err = 1; //表示创建卡券失败
        } else {
            //同步成功后把生成的卡券id存放到数据库中并更新redis
            $flag = (new CouponCardService())->model->where('id', $couponCard->id)->update(['card_id' => $result['card_id']]);
            if ($flag) {
                $dataLog['wx_coupon_id'] = $couponCard->id;
                $dataLog['mid'] = session('mid');
                (new WeixinCouponLogService())->add($dataLog);
            } else {
                $err = 2; //表示保存数据库失败
            }
        }
        $return['err'] = $err;
        if ($err == 0) {
            $return['card_id'] = $result['card_id'];
        }
        return $return;
    }


    /**
     * 生成投放二维码url
     * @update 许立 2018年09月12日 优化代码
     */
    public function getQrCodeUrl($wid, $card_id)
    {
        $result['action_name'] = 'QR_CARD';
        $result['expire_seconds'] = 1800;
        $result['action_info']['card']['card_id'] = $card_id;
        $result['action_info']['card']['is_unique_code'] = false;
        $result['action_info']['card']['outer_str'] = '12b';
        $result = (new ApiService())->qrcodeCreated($wid, $result);

        return $result['errcode'] ? '' : $result['show_qrcode_url'];
    }

    /**
     * 某优惠券领取列表
     * @update 许立 2018年09月12日 优化代码
     */
    public function couponReceiveList($wid, $id)
    {
        list($list) = (new CouponLogService())->listWithPage(['coupon_id' => $id]);
        $list['data'] || success('', $list);

        //获取用户信息
        $list['data'] = (new CouponModule())->handleCouponLogMember($list['data']);

        success('', '', $list);
    }

    //秒杀预览
    public function seckillPreview($id)
    {
        //获取秒杀活动
        $seckill = (new SeckillModule())->getSeckillDetail($id);
        unset($seckill['product']);

        //获取商品详情
        $product = (new ProductModule())->getDetail($seckill['seckill']['product_id'], $seckill['seckill']['wid'], session('mid'));

        return view('shop.activity.seckillPreview', array(
            'title' => '秒杀预览',
            'seckill' => $seckill['seckill'],
            'product' => $product
        ));
    }

    /**
     * 秒杀详情页
     * @param int $wid 店铺id
     * @param int $id 秒杀活动id
     * @return view
     * @author 许立 2018年09月04日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     **/
    public function seckillDetail(ShopService $shopService, $wid, $id)
    {
        $res = (new GroupsRuleModule())->checkShopIsPermission(session('wid'), 'hsshop_weixin_marketing_active');
        if (!$res) {
            return redirect('shop/member/noPermission');
        }
        //获取秒杀活动
        $seckill = (new SeckillModule())->getSeckillDetail($id);
        unset($seckill['product']);
        //获取商品详情
        $product = (new ProductModule())->getDetail($seckill['seckill']['product_id'], $seckill['seckill']['wid'], session('mid'));

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);

        /*add by wuxiaoping 2017.08.14 分享数据*/
        $shareData = [];
        $shareData['share_title'] = $seckill['seckill']['share_title'] ? $seckill['seckill']['share_title'] : $seckill['seckill']['title'];
        $shareData['share_desc'] = $seckill['seckill']['share_desc'] ? str_replace(PHP_EOL, '', $seckill['seckill']['share_desc']) : $shop['shop_name'] . '-' . $seckill['seckill']['title']; //去掉换行符
        $shareData['share_img'] = $seckill['seckill']['share_img'] ? imgUrl() . $seckill['seckill']['share_img'] : imgUrl() . $shop['logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if (empty($seckill['seckill']['share_title']) && empty($seckill['seckill']['share_desc']) && empty($seckill['seckill']['share_img'])) {
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $shop['shop_name'] . '-秒杀活动';

        }

        return view('shop.activity.seckillDetail', array(
            'title' => '秒杀详情',
            'seckill' => $seckill['seckill'],
            'product' => $product,
            'shareData' => $shareData,
            'shop' => $shop,
            'member' => $this->memberService->getRowById(session('mid')),
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170801
     * @desc 大转盘页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function wheel(ShopService $shopService, $wid, $wheelId)
    {
        $wheelData = (new ActivityWheelService())->getRowById($wheelId);
        if (!$wheelData) {
            error('连接错误');
        }
        $wheelData['prizeData'] = (new ActivityWheelPrizeService())->getByWheelId($wheelId);
        foreach ($wheelData['prizeData'] as &$val) {
            if ($val['type'] == 2) {
                $coupon = (new CouponService())->model->select(['id', 'title'])->find($val['content']);
                $val['coupon'] = $coupon ? $coupon->toArray() : [];
            }
            if ($val['type'] == 4) {
                $productdata = ProductService::getRowById($val['content']);
                $val['title'] = $productdata['title'] ?? '';
            }
        }
        $logService = new ActivityWheelLogService();
        $res = (new WheelModule())->checkTimes($wheelData, session('mid'));
        if ($res['success'] == 0) {
            $wheelData['is_ok'] = 0;
        } else {
            $wheelData['is_ok'] = 1;
        }
        $wheelData['count'] = $logService->count(['wheel_id' => $wheelId, 'mid' => session('mid')]);

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);

        /* add by wuxiaoping 2017.08.28 */
        $shareData['share_title'] = $wheelData['share_title'] ? $wheelData['share_title'] : $wheelData['title'];
        $shareData['share_desc'] = $wheelData['share_desc'] ? str_replace(PHP_EOL, '', $wheelData['share_desc']) : $shop['shop_name'] . '-' . $wheelData['title']; //去掉换行符
        $shareData['share_img'] = $wheelData['share_img'] ? imgUrl() . $wheelData['share_img'] : imgUrl() . $shop['logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if (empty($wheelData['share_title']) && empty($wheelData['share_desc']) && empty($wheelData['share_img'])) {
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $shop['shop_name'] . '-幸运大转盘抽奖';
        }

        return view('shop.activity.wheel', array(
            'title' => $wheelData['title'],
            'data' => $wheelData,
            'shareData' => $shareData
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170802
     * @desc 大转盘操作
     * @param $wid
     * @param $wheelId
     */
    public function wheelPlay($wid, $wheelId)
    {
        $wheelModule = new WheelModule();
        $wheelService = new ActivityWheelService();
        $wheelData = $wheelService->getRowById($wheelId);
        $res = $wheelModule->check($wheelData, session('mid'));
        if ($res['success'] == 0) {
            error($res['message']);
        }
        //计算是否中奖
        $result = $wheelModule->compute($wheelData, session('wid'), session('mid'));
        if ($result['success'] == 1) {
            if ($result['data'] && $result['data']['type'] == 2) {
                $result['data']['title'] = (new CouponLogService())->getDetail($result['data']['content'])['title'] ?? '';
            } elseif ($result['data'] && $result['data']['type'] == 4) {
                $productdata = ProductService::getRowById($result['data']['content']);
                $result['data']['title'] = $productdata['title'] ?? '';
            } elseif ($result['data'] && $result['data']['type'] == 3) {
                $result['data']['title'] = $result['data']['content'];
            } elseif ($result['data'] && $result['data']['type'] == 1) {
                $result['data']['title'] = $result['data']['content'] . '积分';
            }
            success($result['message'], '', $result['data']);
        } else {
            error($result['message']);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170804
     * @desc 我的赠品
     */
    public function myGift(Request $request, $wid)
    {
        $mid = session('mid');
        $where = [
            'mid' => $mid,
            'type' => ['<>', '1'],
        ];
        $winService = new ActivityWheelWinService();
        list($winData) = $winService->getlistPage($where);
        foreach ($winData['data'] as $key => &$val) {
            if ($val['type'] == 2) {
                $data = (new CouponLogService())->getDetail($val['content']);
                if (!$data) {
                    unset($winData['data'][$key]);
                }
                $val['coupon'] = $data;
            }
        }
        if ($request->input(['page']) && $request->input('page') >= 1) {
            success('操作成功', '', $winData);
        }

        return view('shop.activity.myGift', array(
            'title' => '我的奖品',
            'winData' => $winData,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170804
     * @desc 删除我的赠品
     * @update 何书哲 2019年06月21日 修改条件判断，由&&改为||
     */
    public function delGift($wid, $id)
    {
        $mid = session('mid');
        $winService = new ActivityWheelWinService();
        $winData = $winService->getRowById($id);
        // update 何书哲 2019年06月21日 修改条件判断，由&&改为||
        if (!$winData || $winData['mid'] != $mid) {
            error('赠品不存在');
        }
        $winService->del($id) ? success() : error();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170804
     * @desc 我的赠品
     * @param int $id 奖品记录id
     * @param int $type 活动类型 1:大转盘, 2:砸金蛋 3:刮刮卡 许立 2018年08月20日
     * @update 许立 2018年08月16日 返回默认地址
     * @update 许立 2018年08月17日 优先显示该活动确认过的地址
     * @update 许立 2018年08月20日 增加砸金蛋活动类型
     * @update 许立 2018年08月29日 用户没有地址情况处理，先设置地址 后地址被删除情况处理
     */
    public function method($id, $type = 1)
    {
        // 根据不同活动类型获取信息
        $activity_id = 0;
        $content = '';
        if ($type == ActivityAwardAddress::ACTIVITY_TYPE_WHEEL) {
            $res = (new ActivityWheelWinService())->getRowById($id);
            if (!$res) {
                error('id不存在或错误');
            }
            $activity_id = $res['wheel_id'];
            $content = $res['content'];
        } elseif ($type == ActivityAwardAddress::ACTIVITY_TYPE_EGG) {
            $res = (new EggMemberService())->getRowById($id);
            if (!$res) {
                error('id不存在或错误');
            }
            $activity_id = $res['egg_id'];
            // 获取兑奖方式说明
            $prizeRow = (new EggPrizeService())->getInfoById($res['prize_id']);
            $content = $prizeRow['method'] ?? '';
        }

        // 先取设置的地址 没有再取默认地址
        $awardAddress = (new ActivityAwardAddressService())->model
            ->where('activity_id', $activity_id)
            ->where('mid', session('mid'))
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

        return view('shop.activity.method', array(
            'title' => '兑奖方式',
            'data' => ['activity_id' => $activity_id, 'content' => $content],
            'address' => $address
        ));
    }

    /**
     * 砸金蛋详情
     * @param $wid
     * @param $id
     * @author: 梅杰 2018年8月16日
     */
    public function eggDetail($wid, $id)
    {
        $eggModule = new EggModule();
        $data = $eggModule->getEggDetailById($id);
        $detail = $eggModule->getEggDetailByEggId($id);
        if (!$detail)
            error();
        array_pop($detail['prize_info']);
        if (!empty($detail['share_json'])) {
            $detail['share_json'] = json_decode($detail['share_json'], 1);
        } else {
            $detail['share_json'] = [];
            $detail['share_json']['share_img'] = '';
            $detail['share_json']['title'] = '';
            $detail['share_json']['share_desc'] = '';
        }
        if (!$data) {
            error();
        }
        success('', '', $data + $detail);
    }

    /**
     * 砸金蛋
     * @param $wid 店铺id
     * @param $id 活动id
     * @author: 梅杰 2018年8月16日
     */
    public function eggRun($wid, $id)
    {
        $eggModule = new EggModule();
        if ($data = $eggModule->getPrize($id, $wid)) {
            success('', '', $data);
        }
        error();
    }


    //砸金蛋首页
    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function eggIndex(ShopService $shopService, $wid = 0, $id = 0)
    {
        if ($id == 0) {
            error('参数错误');
        }
        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        $eggModule = new EggModule();
        $detail = $eggModule->getEggDetailByEggId($id);
        $shareData = [];
        if (empty($detail['share_json'])) {
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $shop['shop_name'] . '-砸金蛋活动';
        } else {
            $shareData = json_decode($detail['share_json'], true);
            $shareData['share_title'] = $shareData['title'];
            $shareData['share_img'] = imgUrl() . $shareData['share_img'];
        }
        return view('shop.activity.eggIndex', array(
            'title' => '砸蛋活动',
            'eggId' => $id,
            'wid' => session('wid'),
            'shareData' => $shareData
        ));
    }

    //获取砸金蛋中奖名单
    public function getPrizeList($wid, $eggId = 0)
    {
        if (!$eggId) {
            error('参数缺失');
        }
        $eggMemberService = new EggMemberService();
        $where = [
            'wid' => session('wid'),
            'egg_id' => $eggId,
            'is_prize' => 1
        ];
        list($list, $pageHtml) = $eggMemberService->listWithPage($where, '', '', 10);
        if (empty($list['data'])) {
            success('', '', []);
        }
        $memberService = new MemberService();
        foreach ($list['data'] as $k => $v) {
            $mData = $memberService->getRowById($v['mid']);
            $list['data'][$k]['name'] = $mData['nickname'];
        }
        $eggModule = new EggModule();
        $list = $eggModule->getLogDetail($list);
        success('', '', $list['data']);
    }

    /**
     * 砸金蛋-我的奖品列表页
     * @param Request $request 参数类
     * @param int $wid 店铺id
     * @return json|view
     * @author 梅杰 2018年08月20日
     */
    public function eggPrizeList(Request $request, $wid)
    {
        $mid = session('mid');
        list($winData) = (new EggModule())->getMemberAllPrizeInfo($mid);
        if ($request->input(['page']) && $request->input('page') >= 1) {
            success('操作成功', '', $winData);
        }
        return view('shop.activity.eggPrizeList', array(
            'title' => '我的奖品',
            'winData' => $winData,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }


    /**
     * 我的奖品删除
     * @param Request $request
     * @param EggMemberService $service
     * @author: 梅杰 2018年8月21日
     */
    public function eggPrizeDel(Request $request, EggMemberService $service)
    {
        if ($request->method('post')) {
            $id = $request->input('id', 0);
            if ($service->del($id)) {
                success();
            }
            error();
        }
        error('请求method error');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180228
     * @desc 注册销售员
     */
    public function registerSalesMan(Request $request)
    {
        $liSalesmanService = new LiSalesmanService();
        $mid = session('mid');
        $wid = session('wid');
        $input = $request->input();
        if ($request->isMethod('post')) {
            $rules = array(
                'mobile' => 'required|regex:mobile',
                'name' => 'required'
            );
            $messages = array(
                'mobile.required' => '请输入手机号码',
                'mobile.regex' => '手机号码格式不正确',
                'name.required' => '请输入密码',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return myerror($validator->errors()->first());
            }

            $sourceMobile = [13862414586, 18657193758, 15868124602, 18326618736, 15958106762, 18358124405, 15669767032, 13970770834, 18668138621, 13003618895, 18668090589, 18867531987, 17558441597, 17681551520, 13928867409, 18166094074, 13625296517, 18657107323, 13093788976, 18557537887, 18667032619, 13616513872, 17316907352, 13479977660, 13576764314, 15658823700, 15606518920, 18072115323, 15669988036, 17679351636, 13340047615, 18657103883, 18657193756, 13735860792, 15558178767, 15858237827, 13136136219, 15158071356, 18679789637, 15170645808, 18605885169, 15557168830, 13346177032, 18814876993, 18758759770, 19979459929, 18668103689, 13136113670, 18868821597, 15068761088, 18658116805, 13486144923, 13606638941, 18657185709, 18758220403, 15757177826, 15700061384, 13282809503, 18867124731, 15270250573, 15270669087, 13776027545, 18668489696, 13067717757, 13516720730, 13858084648, 15267449427, 18606510825, 18870970810, 18870970630, 15669027951, 15658158989, 13867130790, 15669096287, 13616744285, 17681800805, 18667919629, 15170747330, 15558198501, 15068763885, 13208039513, 18701823462, 18758168146, 18557519431, 15068928746, 13257078727, 18707076180, 15657195953, 18668438006, 18949948213, 17681806986, 13588154137, 15638293746, 15558116250, 18694555618, 18626897436, 13675866125, 15068184516, 13588046634, 15869124974, 18969034728, 13018998797, 18225550463, 18069794795, 13806632748, 18621945930, 15682013169];
            if (!in_array($input['mobile'], $sourceMobile)) {
                error('该手机号码，禁止注册');
            }
            $res = $liSalesmanService->getList(['mobile' => $input['mobile']]);
            if ($res) {
                error('您已注册过信息，请不要重复注册');
            }
            $data = [
                'umid' => session('umid'),
                'wx_mid' => $mid,
                'name' => $input['name'],
                'mobile' => $input['mobile']
            ];
            $liSalesmanService->add($data) ? success() : error();
        }
        $registerData = $liSalesmanService->getList(['wx_mid' => $mid]);
        $registerData = current($registerData);
        $where = $this->getWhere($input);
        $where = array_merge($where, ['topid' => $mid]);
        $data = (new SalesmanStatisticService())->getlistPage($where);
        if (!empty($input['page']) && $input['page'] >= 1 || !empty($input['flag'])) {
            success('操作成功', '', $data);
        }
        if (!empty($registerData) && (empty($registerData['qrcode']) || !empty($input['tag']))) {
            $url = $this->createCode();
            if ($url) {
                $liSalesmanService->update($registerData['id'], ['qrcode' => $url]);
                $registerData['qrcode'] = $url;
            }
        }
        $jumpUrl = config('app.url') . $this->_jumpUrl . $wid . '?_pid_=' . $mid;
        return view('shop.activity.registerSalesMan', array(
            'title' => '会搜业务绑定',
            'registerData' => $registerData,
            'data' => $data,
            'jumpUrl' => $jumpUrl,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function getGroupsInfo(Request $request, SalesmanStatisticService $salesmanStatisticService)
    {
        $id = $request->input('id');
        $data = $salesmanStatisticService->getRowById($id);
        if (!$data) {
            error();
        }
        $groupsDetailService = new GroupsDetailService();
        $res = $groupsDetailService->getListByWhere(['member_id' => $data['mid']]);
        if (!$res) {
            error('该用户未参加拼团活动');
        }
        $remarkNo = [];
        foreach ($res as $val) {
            $remarkNo[] = $val['remark_no'];
        }
        $data = (new MeetingGroupsRuleModule())->getRemark(0, [], $data['mid']);

        return view('shop.activity.getGroupsInfo', array(
            'title' => '会搜业务绑定',
            'data' => $data,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180516
     * @desc  获取搜索条件
     */
    public function getWhere($input)
    {

        $where = [];
        if (!empty($input['nickname'])) {
            $where['nickname'] = $input['nickname'];
        }
        if (!empty($input['is_open_groups'])) {
            $where['is_open_groups'] = $input['is_open_groups'];
        }
        if (!empty($input['mobile'])) {
            $remarkService = new RemarkService();
            $res = $remarkService->getList(['content' => $input['mobile'], 'type' => '7']);
            if (!$res) {
                $where['mid'] = '';
            } else {
                $remakNOs = array_column($res, 'remark_no');
                $groupsDetailData = (new GroupsDetailService())->getListByWhere(['remark_no' => ['in', $remakNOs]]);
                if (!$groupsDetailData) {
                    $where['mid'] = '';
                } else {
                    $mids = array_column($groupsDetailData, 'member_id');
                    $where['mid'] = ['in', $mids];
                }
            }
        }
        if (!empty($input['name'])) {
            $remarkService = new RemarkService();
            $res = $remarkService->getList(['content' => $input['name'], 'type' => '0']);
            if (!$res) {
                $where['mid'] = '';
            } else {
                $remakNOs = array_column($res, 'remark_no');
                $groupsDetailData = (new GroupsDetailService())->getListByWhere(['remark_no' => ['in', $remakNOs]]);
                if (!$groupsDetailData) {
                    $where['mid'] = '';
                } else {
                    $mids = array_column($groupsDetailData, 'member_id');
                    $where['mid'] = ['in', $mids];
                }
            }
        }
        return $where;
    }

    public function createCode()
    {
        $wid = session('wid');
        $mid = session('mid');
        $jumpUrl = config('app.url') . $this->_jumpUrl . $wid . '?_pid_=' . $mid;

        //生成头像
        $memberService = new MemberService();
        $memberInfo = $memberService->getRowById($mid);

        $headimgurl = 'https://www.huisou.cn/static/images/member_default.png';
        if (isset($memberInfo['headimgurl']) && !empty($memberInfo['headimgurl'])) {
            $headimgurl = $memberInfo['headimgurl'];
        }

        $createShareImgService = new CreateShareImgService();
        $result = $createShareImgService->weixinHeadImg($wid, 'salesman' . $mid . rand(1, 9999), $headimgurl);//生成头像地址
        $code = QrCodeService::create($jumpUrl, '/public/' . $result, 300, 'salesman' . $mid . rand(1, 23233));
        return str_replace(base_path('public'), '', $code);;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180514
     * @desc 活动跳转地址
     */
    public function activity()
    {
        $wid = session('wid');
        return redirect('shop/meeting/detail/2143/' . $wid);
    }

    /**
     * 获取红包活动展示
     * @param int $wid 店铺id
     * @return json
     * @author 许立 2018年08月02日
     * @update 许立 2018年08月08日 返回活动名
     */
    public function bonusShow($wid)
    {
        success('', '', (new BonusModule())->showWindow($wid, session('mid')));
    }

    /**
     * 拆红包
     * @param int $wid 店铺id
     * @return json
     * @author 许立 2018年08月02日
     */
    public function bonusUnpack($wid)
    {
        $return = (new BonusModule())->unpack(session('mid'), $wid);
        if ($return['err_code']) {
            error($return['err_msg']);
        } else {
            success();
        }
    }

    /**
     * 拆红包弹窗关闭
     * @param int $wid 店铺id
     * @return json
     * @author 许立 2018年08月02日
     */
    public function bonusClose($wid)
    {
        $return = (new BonusModule())->closeWindow(session('mid'), $wid);
        if ($return['err_code']) {
            error($return['err_msg']);
        } else {
            success();
        }
    }

    /**
     * 获取红包活动展示
     * @param int $wid 店铺id
     * @return json
     * @author 许立 2018年08月02日
     */
    public function bonusDetail($wid)
    {
        $return = (new BonusModule())->unpackDetail(session('mid'), $wid);
        if ($return['err_code']) {
            error($return['err_msg']);
        }

        return view('shop.activity.bonusDetail', array(
            'title' => '拆红包详情',
            'data' => $return['data'],
            'wid' => $wid
        ));
    }

    /**
     * 营销活动用户奖品收货地址设置
     * @param Request $request 参数类
     * @param int $wid 店铺id
     * @return json
     * @author 许立 2018年08月17日
     */
    public function setAwardAddress(Request $request, $wid)
    {
        // 参数判断
        $mid = session('mid');
        $input = $request->input();
        if (empty($input['type']) || empty($input['activityId'])) {
            error('活动参数不完整');
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
                ->where('mid', session('mid'))
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
            success();
        } else {
            error();
        }
    }

    /**
     * 获取调查活动详情
     * @param int $wid 店铺id
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年08月02日
     * @update 许立 2018年08月23日 每次都判断参与次数
     * @update 许立 2018年10月16日 报名活动页面显示活动标题
     * @update 何书哲 2019年07月26日 添加活动不存在判断
     */
    public function researchDetail($wid, $id)
    {
        if (empty($id)) {
            error('参数不完整');
        }
        // 何书哲 2018年7月27日 如果已到限制参与次数，则返回错误
        $research = (new ResearchModule())->getResearch($id);
        // update 何书哲 2019年07月26日 添加活动不存在判断
        if (empty($research)) {
            error('该活动不存在');
        }

        // 华亢 2018年8月3日 留言板页面增加shareDara数据
        $shareData = (new PublicShareService())->publicShareSet($wid);
        // 判断参与次数
        $answer_times = (new ResearchRecordService())->getCount($id, session('mid'));
        if ($answer_times == 1 && $research['times_type'] == 0) {
            error('该活动只能参与一次');
        }
        if ($research['times_type'] == 2 && $answer_times == 10) {
            error('该活动最多参与10次');
        }

        return view('shop.activity.researchDetail', array(
            'title' => $research['title'] ?? '',
            'data' => $research,
            'wid' => $wid,
            'shareData' => $shareData,
        ));
    }

    /**
     * 提交调查留言数据
     * @param int $wid 店铺id
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年08月02日
     * @update 许立 2018年08月23日 返回第几次提交
     * @update 何书哲 2018年10月12日 发送模板消息
     */
    public function researchSubmit($wid, Request $request)
    {
        if (!$request->isMethod('post')) {
            error('请求方式不正确');
        }
        $researchModule = new ResearchModule();
        $input = $request->input();
        $input['mid'] = session('mid');
        $return = $researchModule->submitAnswer($input);
        if (!$return['err_code']) {
            //何书哲 2018年10月12日 发送模板消息
            $researchModule->sendResearchWechatMsg($return['data']);
            success('', '', ['times' => (new ResearchRecordService())->getCount($input['id'], $input['mid'])]);
        } else {
            error($return['err_msg']);
        }
    }

    /**
     * 刮刮卡页面
     * @param $wid 店铺id
     * @param $scratchId 刮刮卡活动id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 何书哲 2018年8月24日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function scratch(ShopService $shopService, $wid, $scratchId)
    {
        $scratchData = (new ActivityScratchService())->getRowById($scratchId);
        if (!$scratchData) {
            error('连接错误');
        }
        $scratchData['prizeData'] = (new ActivityScratchPrizeService())->getByScratchId($scratchId);
        foreach ($scratchData['prizeData'] as &$val) {
            if ($val['type'] == 2) {
                $coupon = (new CouponService())->model->select(['id', 'title'])->find($val['content']);
                $val['coupon'] = $coupon ? $coupon->toArray() : [];
            }
            if ($val['type'] == 4) {
                $productdata = ProductService::getRowById($val['content']);
                $val['title'] = $productdata['title'] ?? '';
            }
        }
        $res = (new ScratchModule())->check($scratchData, session('mid'), $wid);
        if ($res['success'] == 0) {
            $scratchData['is_ok'] = 0;
        } else {
            $scratchData['is_ok'] = 1;
        }

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);
        /* add by wuxiaoping 2017.08.28 */
        $shareData['share_title'] = isset($scratchData['share_title']) ? $scratchData['share_title'] : $scratchData['title'];
        $shareData['share_desc'] = isset($scratchData['share_desc']) ? str_replace(PHP_EOL, '', $scratchData['share_desc']) : $shop['shop_name'] . '-' . $scratchData['title']; //去掉换行符
        $shareData['share_img'] = isset($scratchData['share_img']) ? imgUrl() . $scratchData['share_img'] : imgUrl() . $shop['logo'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if (empty($scratchData['share_title']) && empty($scratchData['share_desc']) && empty($scratchData['share_img'])) {
            $shareData = (new PublicShareService())->publicShareSet($wid);
            $shareData['share_desc'] = $shareData['share_desc'] ? $shareData['share_desc'] : $shop['shop_name'] . '-刮刮乐抽奖';
        }

        return view('shop.activity.wheel', array(
            'title' => $scratchData['title'],
            'data' => $scratchData,
            'shareData' => $shareData
        ));
    }

    /**
     * 刮刮乐操作
     * @param $wid 店铺id
     * @param $scratchId 刮刮乐活动id
     * @author 何书哲 2018年8月24日
     */
    public function scratchPlay($wid, $scratchId)
    {
        if (!$scratchId) {
            error('参数不完整');
        }
        $scratchModule = new ScratchModule();
        $scratchService = new ActivityScratchService();
        $scratchData = $scratchService->getRowById($scratchId);
        if (!$scratchData) {
            error('活动不存在');
        }
        $res = $scratchModule->check($scratchData, session('mid'), $wid);
        if ($res['success'] == 0) {
            error($res['message']);
        }
        //计算是否中奖
        $result = $scratchModule->compute($scratchData, $wid, session('mid'));
        if ($result['success'] == 1) {
            if ($result['data'] && $result['data']['type'] == 2) {
                $result['data']['title'] = (new CouponLogService())->getDetail($result['data']['content'])['title'] ?? '';
            } elseif ($result['data'] && $result['data']['type'] == 1) {
                $result['data']['title'] = $result['data']['content'] . '积分';
            } elseif ($result['data'] && $result['data']['type'] == 3) {
                $result['data']['title'] = $result['data']['content'];
            } elseif ($result['data'] && $result['data']['type'] == 4) {
                $productdata = ProductService::getRowById($result['data']['content']);
                $result['data']['title'] = $productdata['title'] ?? '';
            }
            success($result['message'], '', $result['data']);
        } else {
            error($result['message']);
        }
    }

    /**
     * 刮刮乐-我的奖品
     * @param Request $request 请求参数
     * @param $wid 店铺id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 何书哲 2018年8月24日
     */
    public function myScratchGift(Request $request, $wid)
    {
        $mid = session('mid');
        $where = [
            'mid' => $mid,
            'type' => ['<>', '1'],
        ];
        $winService = new ActivityScratchWinService();
        list($winData) = $winService->getlistPage($where);
        foreach ($winData['data'] as $key => &$val) {
            if ($val['type'] == 2) {
                $data = (new CouponLogService())->getDetail($val['content']);
                if (!$data) {
                    unset($winData['data'][$key]);
                }
                $val['coupon'] = $data;
            }
        }
        if ($request->input(['page']) && $request->input('page') >= 1) {
            success('操作成功', '', $winData);
        }

        return view('shop.activity.myScratchGift', array(
            'title' => '我的奖品',
            'winData' => $winData,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }

    /**
     * 刮刮乐-删除我的赠品
     * @param $wid 店铺id
     * @param $id 刮刮乐活动id
     * @author 何书哲 2018年8月24日
     */
    public function delScratchGift($wid, $id)
    {
        $mid = session('mid');
        $winService = new ActivityScratchWinService();
        $winData = $winService->getRowById($id);
        if (!$winData && $winData['mid'] != $mid) {
            error('赠品不存在');
        }
        $winService->del($id) ? success() : error();
    }


}