<?php
/**
 * 营销模块
 *
 * @package default
 * @author  大王叫我来巡山
 */

namespace App\Http\Controllers\Merchants;

use App\Model\ActivityAwardAddress;
use App\Model\Favorite;
use App\Model\Product;
use App\Module\CommonModule;
use App\Module\CouponModule;
use App\Module\DiscountModule;
use App\Module\EggModule;
use App\Module\ExportModule;
use App\Module\FavoriteModule;
use App\Module\MeetingGroupsRuleModule;
use App\Module\OrderModule;
use App\Module\ResearchModule;
use App\Module\WheelModule;
use App\S\AliApp\AliappConfigService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\Module\GroupsRuleModule;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Module\SeckillModule;
use App\Module\ProductModule;
use App\S\Market\ActivityAwardAddressService;
use App\S\Market\CouponCardService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Market\DiscountService;
use App\S\Market\EggMemberService;
use App\S\Market\ResearchRecordService;
use App\S\Market\ResearchService;
use App\S\Market\ResearchTemplateService;
use App\S\Market\ScoreService;
use App\S\Market\SeckillService;
use App\S\Market\SmokedEggsService;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Scratch\ActivityScratchLogService;
use App\S\Scratch\ActivityScratchPrizeService;
use App\S\Scratch\ActivityScratchService;
use App\S\Store\MicroPageService;
use App\S\Store\TemplateMarketService;
use App\S\Wheel\ActivityWheelLogService;
use App\S\Wheel\ActivityWheelPrizeService;
use App\S\Wheel\ActivityWheelService;
use App\S\WXXCX\WXXCXMicroPageService;
use App\Services\DistributeTemplateService;
use App\S\WXXCX\WXXCXCustomFooterBarService;
use App\Services\Marketing\ActivityService;
use App\Services\MsgpushDayStatService;
use App\Services\MsgpushTotalStatService;
use App\S\Product\ProductPropService;
use App\Services\Shop\MemberAddressService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use ProductService;
use SignService as MSignService;
use MallModule;
use App\S\Groups\GroupsRuleService;
use WeixinService;
use Validator;
use QrCode;
use QrCodeService;
use App\S\WXXCX\WXXCXConfigService;
use App\S\Vote\ActivityVoteService;
use App\S\Vote\EnrollInfoService;
use App\S\Vote\VoteLogService;
use App\S\WXXCX\WXXCXFooterBarService;
use App\Module\XCXModule;
use App\S\WXXCX\WXXCXSyncFooterBarService;
use OrderService;
use App\S\Order\OrderZitiService;
use App\S\Foundation\RegionService;
use App\Services\Order\OrderRefundService;
use Excel;
use App\S\Weixin\ShopService;

class MarketingController extends Controller
{
    /*
     * @return void
     */
    public function __construct(MsgpushTotalStatService $msgpushTotalStatService, MsgpushDayStatService $msgpushDayStatService, MemberService $memberService)
    {
        $this->msgpushTotalStatService = $msgpushTotalStatService;
        $this->msgpushDayStatService = $msgpushDayStatService;
        $this->memberService = $memberService;
        $this->leftNav = 'marketing';
        $this->minapp = 'minapp';
    }

    /**
     * 营销中心
     * @return [type] [description]Z
     * @update 何书哲 2018年7月30日 判断店铺是否绑定了微信小程序，绑定返回1，未绑定返回0
     */
    public function index()
    {

        /*****在营销活动首页标识是否显示独立享立减二期****/
        $wid = session('wid');
        $actLiWids = config('app.li_wid');
        $showLi = 0;
        $wxxcxConfigService = new WXXCXConfigService();
        if ($actLiWids) {
            $actLiWidArr = [];
            foreach ($actLiWids as $key => $value) {
                $actLiWidArr = explode(',', $value);
            }
            if (in_array($wid, $actLiWidArr)) {
                $showLi = 1;
            }
        }
        //何书哲 2018年7月30日 判断店铺是否绑定了微信小程序，绑定返回1，未绑定返回0
        $wxxcxConfigData = $wxxcxConfigService->model->where(['wid' => $wid, 'current_status' => 0])->first();
        /*******/
        return view('merchants.marketing.index', array(
            'title' => '营销中心',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'showLi' => $showLi,
            'isBindWechat' => $wxxcxConfigData ? 1 : 0
        ));
    }

    /**
     * 营销中心 - 微信公众号
     * @return [type] [description]
     */
    public function wechatMp()
    {
        return view('merchants.marketing.wechatMp', array(
            'title' => '营销中心 - 微信公众号',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'bodyClass' => ' class=oneNav_right'
        ));
    }

    /**
     * 营销中心 - 微博 - 微博帐号
     * @return [type] [description]
     */
    public function weibo()
    {
        return view('merchants.marketing.weibo', array(
            'title' => '营销中心 - 微博 - 微博帐号',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
     * 营销中心 - 消息推送
     * @return [type] [description]
     */
    public function messagepush(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        #1. 查询总的消息推送统计信息
        $msgpush_total_stat = $this->msgpushTotalStatService->getMsgpushTotalStat($params, $wid);
        return view('merchants.marketing.messagepush', array(
            'title' => '营销中心 - 消息推送',
            'leftNav' => $this->leftNav,
            'msgpush_total_stat' => $msgpush_total_stat['list'],
            'slidebar' => 'index'
        ));
    }

    /*
     * @todo: 推送统计
     */
    public function pushStatistics(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        #1. 查询列表统计信息
        $msgpush_day_stat = $this->msgpushDayStatService->getMsgpushDayStats($params, $wid);
        return view('merchants.marketing.pushStatistics', array(
            'title' => '营销中心 - 推送统计',
            'leftNav' => $this->leftNav,
            'msgpush_day_stat' => $msgpush_day_stat['list'] && isset($msgpush_day_stat['list']['data']) ? $msgpush_day_stat['list']['data'] : $msgpush_day_stat['list'],
            'pageLinks' => $msgpush_day_stat['pageLinks'],
            'slidebar' => 'index'
        ));
    }

    /*
     * @todo: 短信充值
     */
    public function msgRecharge()
    {
        return view('merchants.marketing.msgRecharge', array(
            'title' => '营销中心 - 短信充值',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 订单催付
    */
    public function expediting()
    {
        return view('merchants.marketing.expediting', array(
            'title' => '营销中心 - 订单催付',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 付款成功通知
    */
    public function paySuccess()
    {
        return view('merchants.marketing.paysuccess', array(
            'title' => '营销中心 - 付款成功通知',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 发货提醒
    */
    public function sendNotice()
    {
        return view('merchants.marketing.sendnotice', array(
            'title' => '营销中心 - 发货提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 签收提醒
    */
    public function signNotice()
    {
        return view('merchants.marketing.signnotice', array(
            'title' => '营销中心 - 签收提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 商家同意退款
    */
    public function agreeRefund()
    {
        return view('merchants.marketing.agreerefund', array(
            'title' => '营销中心 - 商家同意退款',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 商家拒绝退款
    */
    public function disagreeRefund()
    {
        return view('merchants.marketing.disagreerefund', array(
            'title' => '营销中心 - 商家拒绝退款',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 接单提醒
    */
    public function takeOrder()
    {
        return view('merchants.marketing.takeorder', array(
            'title' => '营销中心 - 接单提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 拒绝接单提醒
    */
    public function distakeOrder()
    {
        return view('merchants.marketing.distakeorder', array(
            'title' => '营销中心 - 拒绝接单提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
    * @todo: 核销提醒
    */
    public function verifyNotice()
    {
        return view('merchants.marketing.verifynotice', array(
            'title' => '营销中心 - 核销提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
   * @todo: 获得会员卡提醒
   */
    public function getVipcard()
    {
        return view('merchants.marketing.getvipcard', array(
            'title' => '营销中心 - 获得会员卡提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
   * @todo: 会员卡升级提醒
   */
    public function vipUpgrade()
    {
        return view('merchants.marketing.vipupgrade', array(
            'title' => '营销中心 - 会员卡升级提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
   * @todo: 销售员关系通知
   */
    public function salemanRelation()
    {
        return view('merchants.marketing.salemanrelation', array(
            'title' => '营销中心 - 销售员关系通知',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
   * todo: 销售员订单通知
   */
    public function salemanOrder()
    {
        return view('merchants.marketing.salemanorder', array(
            'title' => '营销中心 - 销售员订单通知',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
   * todo: 会员储值成功提醒
   */
    public function vipRecharge()
    {
        return view('merchants.marketing.viprecharge', array(
            'title' => '营销中心 - 会员储值成功提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /*
   * todo: 储值余额变动提醒
   */
    public function banlanceChange()
    {
        return view('merchants.marketing.banlancechange', array(
            'title' => '营销中心 - 储值余额变动提醒',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /**
     * 模版市场
     * @return [type] [description]
     */
    public function template()
    {
        return view('merchants.marketing.template', array(
            'title' => '模版市场',
            'leftNav' => $this->leftNav,
            'slidebar' => 'template'
        ));
    }

    /**
     * 营销-优惠券-列表
     * @param CouponService $couponService
     * @param string $status 列表状态 future:未开始,on:进行中,end:已结束
     * @return view
     * @author Herry
     * @since 2018/6/20
     */
    public function coupons(CouponService $couponService, $status = '')
    {
        //列表状态
        $where = ['wid' => session('wid')];
        $now = date('Y-m-d H:i:s');
        if ($status == 'future') {
            //未开始
            $where['start_at'] = ['>', $now];
        } else if ($status == 'on') {
            //进行中
            $where['_closure'] = function ($query) {
                $query->where(function ($query) {
                    $now = date('Y-m-d H:i:s');
                    $query->where('expire_type', 0)->where('start_at', '<=', $now)->where('end_at', '>', $now);
                })->orWhere(function ($query) {
                    //领取后生效的 属于进行中
                    $query->where('expire_type', '>', 0);
                });
            };
        } else if ($status == 'end') {
            //已结束
            $where['end_at'] = ['<=', $now];
            $where['expire_type'] = 0;
        }

        //查询未删除列表
        $where['status'] = 0;

        //列表和分页数据
        list($list, $pageHtml) = $couponService->listWithPage($where);

        $couponLogService = new CouponLogService();
        foreach ($list['data'] as $k => $v) {
            //获取优惠券领取使用数据
            list($receiveData) = $couponLogService->model
                ->select(DB::raw('count(1) as receiveNum, count(DISTINCT mid) as memberNum'))
                ->where(['coupon_id' => $v['id']])
                ->get()
                ->toArray();
            list($useData) = $couponLogService->model
                ->select(DB::raw('count(1) as useNum'))
                ->where(['coupon_id' => $v['id']])
                ->where('status', '>', 0)
                ->get()
                ->toArray();
            $list['data'][$k]['receiveNum'] = $receiveData['receiveNum'];
            $list['data'][$k]['memberNum'] = $receiveData['memberNum'];
            $list['data'][$k]['useNum'] = $useData['useNum'];
            //优惠券领取链接
            $list['data'][$k]['receiveUrl'] = url('/shop/activity/couponDetail/' . session('wid') . '/' . $v['id']);
        }

        return view('merchants.marketing.couponNew', array(
            'title' => '优惠券',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'list' => $list['data'] ?? [],
            'pageHtml' => $pageHtml,
            'tabList' => $couponService->getStaticList()[0]
        ));
    }

    /**
     * 营销-优惠券-列表
     * @param CouponService $couponService 优惠券活动类
     * @param MemberCardService $memberCardService 会员卡类
     * @param int $id 优惠券id
     * @return view | json
     * @author 许立 2018年06月20日
     * @update 许立 2018年08月09日 设置成满减 满金额必须大于0
     * @update 许立 2019年01月08日 跳转链接增加跳转到微页面
     * @update 梅杰 2019年09月24日 10:11:10 去除删除的优惠券
     */
    public function couponSet(CouponService $couponService, MemberCardService $memberCardService, $id = 0)
    {
        $coupon = [];
        $couponCardService = new CouponCardService();
        if (!empty($id)) {
            //编辑 先获取详情
            $webTitle = '编辑优惠券';

            // 优惠券
            $coupon = $couponService->getDetail($id);
            $coupon || error('优惠券不存在');

            // 优惠券卡券
            $couponCard = $couponCardService->getRowByCouponId($id);
            $couponCard = $couponCard ? $couponCard->toArray() : [];

            //如果是指定商品适用 获取商品列表
            if ($coupon['range_type'] == 1 && $coupon['range_value']) {
                //指定商品列出所有设置商品 Herry 20180104
                $products = ProductService::getListById(explode(',', $coupon['range_value']));
            }

            //指定跳转名称
            $coupon['link_title'] = '';
            if ($coupon['link_type'] == 1 && $coupon['link_id']) {
                $product = ProductService::getDetail($coupon['link_id']);
                $product || error('商品不存在');
                $coupon['link_title'] = $product['title'];
            }

            // 微页面标题
            if ($coupon['link_type'] == 3 && $coupon['link_id']) {
                $ids = explode(',', $coupon['link_id']);
                $shopPageId = $ids[0] ?? 0;
                $xcxPageId = $ids[1] ?? 0;
                // 微商城微页标题
                $shopPage = (new MicroPageService())->getRowById($shopPageId);
                $coupon['shopPageTitle'] = $shopPage['data']['page_title'] ?? '';
                // 小程序微页标题
                $xcxPage = (new WXXCXMicroPageService())->getRowById($xcxPageId);
                $coupon['xcxPageTitle'] = $xcxPage['data']['title'] ?? '';
            }
        }

        //提交
        if ($couponService->request->isMethod('post')) {
            //原始表单参数
            $rawInput = $couponService->request->input();
            $weixinCoupon = [
                'color' => $rawInput['weixin_color'],
                'title' => $rawInput['weixin_title'],
                'subtitle' => $rawInput['weixin_subtitle'],
                'service_phone' => $rawInput['weixin_service_phone'],
                'id' => 0
            ];

            // 处理表单参数
            $input = $couponService->verify($rawInput, $coupon);

            if ($input['is_random']) {
                $input['amount_random_max'] = empty($input['amount_random_max']) ? 0 : $input['amount_random_max'];
            }
            $input['is_sync_weixin'] == 1 && $couponCardService->verify($input);
            if (!empty($input['id'])) {
                //编辑
                $flag = $couponService->update($input['id'], $input, true);
                if ($input['is_sync_weixin'] == 1) {
                    //更新附属表微信卡券表
                    if ($coupon['is_sync_weixin'] == 1) {
                        $weixinCoupon['id'] = $couponCard['id'] ?? 0;
                        //之前已有设置卡券
                        $couponCardService->model->where('coupon_id', $input['id'])->update($weixinCoupon);
                    } else {
                        //新增卡券
                        $weixinCoupon['coupon_id'] = $input['id'];
                        $couponCardService->model->insertGetId($weixinCoupon);
                    }
                }
            } else {
                //新增
                $input['wid'] = session('wid');
                $flag = $couponService->create($input);
                if ($input['is_sync_weixin'] == 1) {
                    //更新附属表微信卡券表
                    $weixinCoupon['coupon_id'] = $flag;
                    $couponCardService->model->insertGetId($weixinCoupon);
                }
            }
            $flag || error('添加优惠券失败');
            success('添加优惠券成功');
        }

        $cards = $memberCardService->model
            ->where(['wid' => session('wid'), 'state' => 1])
            ->get(["id", "title", "limit_type", "limit_end"])
            ->toArray();
        foreach ($cards as $k => $card) {
            if ($card['limit_type'] == 2 && strtotime($card['limit_end']) < time()) {
                unset($cards[$k]);
            }
        }

        return view('merchants.marketing.coupon_add', array(
            'title' => $webTitle ?? '新增优惠券',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'memberCards' => $cards,
            'coupon' => $coupon,
            'products' => $products ?? [],
            'weixinCoupon' => $couponCard ?? [],
            'tabList' => $couponService->getStaticList()[0],
        ));
    }

    /**
     * 营销-优惠券-删除
     * @param CouponService $couponService
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function couponDelete(CouponService $couponService)
    {
        $id = $couponService->request->input('id');
        empty($id) && error('优惠券id为空');

        //删除优惠券 修改状态值 不影响已领取优惠券获取优惠券详情信息
        $result = $couponService->update($id, ['status' => 1]);
        $result ? success() : error();
    }

    /**
     * 营销-优惠券-使失效
     * @param CouponService $couponService
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function couponInvalid(CouponService $couponService)
    {
        $id = $couponService->request->input('id');
        empty($id) && error('优惠券id为空');

        $result = $couponService->update($id, ['invalid_at' => date('Y-m-d H:i:s')]);
        $result ? success() : error();
    }

    /**
     * 营销-优惠券-领取记录
     * @param int $id 优惠券ID
     * @param string $status 状态 received:领取,used:已使用
     * @return view
     * @author Herry
     * @since 2018/6/20
     */
    public function couponReceiveList($id, $status = 'received')
    {
        //获取优惠券
        $couponService = new CouponService();
        $coupon = $couponService->getDetail($id);
        $coupon || error('优惠券不存在');

        //获取列表
        $where = ['coupon_id' => $id];
        $status == 'used' && $where['status'] = ['>', 0];
        list($list, $pageHtml) = (new CouponLogService())->listWithPage($where);

        //获取用户信息
        $list['data'] = (new CouponModule())->handleCouponLogMember($list['data']);

        return view('merchants.marketing.couponReceive', array(
            'title' => '优惠券',
            'leftNav' => $this->leftNav,
            'slidebar' => 'coupon',
            'list' => $list['data'],
            'pageHtml' => $pageHtml,
            'tabList' => $couponService->getStaticList()[0],
            'couponTitle' => $coupon['title'],
            'status' => $status
        ));
    }

    /**
     * 营销-优惠券-获取领取页面的二维码
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function getCouponQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = config('app.url') . 'shop/activity/couponDetail/' . $wid . '/' . $id;

        success('', '', QrCode::size(160)->generate(url($url)));
    }

    /**
     * 营销-优惠券-下载领取页面的二维码
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function downloadCouponQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = config('app.url') . 'shop/activity/couponDetail/' . $wid . '/' . $id;
        $code = QrCodeService::create($url, '', 200);

        return response()->download($code, time() . '.png');
    }

    /**
     * 生成小程序优惠券活动二维码
     * @return string
     * @author 许立 2018年08月07日
     * @update 许立 2018年09月03日 二维码链接修改
     */
    public function couponXcxQrCode($id)
    {
        if (empty($id)) {
            error('参数不合法');
        }
        // 跳转到店铺主页
        success('', '', (new CommonModule())->qrCode(session('wid'), 'pages/activity/pages/activity/couponDetail/couponDetail?id=' . $id, 1));
    }

    /**
     * 下载小程序优惠券活动二维码
     * @return file
     * @author 许立 2018年08月07日
     */
    public function downloadCouponXcxQrCode($id)
    {
        if (empty($id)) {
            error('参数不合法');
        }
        // 跳转到店铺主页
        (new CommonModule())->qrCodeDownload(session('wid'), 'pages/activity/pages/activity/couponDetail/couponDetail?id=' . $id, 1);
    }

    /**
     * 优惠码
     */
    public function achieveGive()
    {
        return view('merchants.marketing.achieveGive', array(
            'title' => '满减送',
            'leftNav' => $this->leftNav,
            'slidebar' => 'achieveGive'
        ));
    }

    /**
     * 优惠码添加
     */
    public function achieveGiveAdd()
    {
        return view('merchants.marketing.achieveGive_add', array(
            'title' => '添加满减送',
            'leftNav' => $this->leftNav,
            'slidebar' => 'achieveGive'
        ));
    }

    /**
     * 多人拼团
     */
    public function togetherGroup()
    {
        return view('merchants.marketing.togetherGroup', array(
            'title' => '多人拼团',
            'leftNav' => $this->leftNav,
            'slidebar' => 'togetherGroup'
        ));
    }

    /**
     * 拼团列表
     * @update 许立 2018年09月06日 返回收藏量
     */
    public function togetherGroupList(Request $request, GroupsRuleModule $groupsRuleModule, FavoriteModule $favoriteModule)
    {

        $where = ['wid' => session('wid')];
        if ($request->input('title')) {
            $where['title'] = ['like', '%' . $request->input('title') . '%'];
        }
        $data = $groupsRuleModule->getRule($where);

        // 收藏量
        $data[0]['data'] = $favoriteModule->handleListFavoriteCount($data[0]['data'], Favorite::FAVORITE_TYPE_GROUP);

        return view('merchants.marketing.togetherGroupList', array(
            'title' => '多人拼团',
            'leftNav' => $this->leftNav,
            'slidebar' => 'togetherGroupList',
            'data' => $data,
        ));
    }

    /**
     * 拼团新增页
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function togetherGroupAdd(Request $request, GroupsRuleService $groupsRuleService, GroupsSkuService $groupsSkuService, ShopService $shopService)
    {
        $rule = [];
        $tag = 0;
        //店铺是否开启了分销
        //$weixinData = WeixinService::init()->getInfo(session('wid'));
        $weixinData = $shopService->getRowById(session('wid'));
        $distributionInfo['is_distribute'] = $weixinData['is_distribute'];
        if ($weixinData['is_distribute'] == 1) {
            $distributionInfo['info'] = (new DistributeTemplateService())->getDefaultTemplate(session('wid'));
        }

        if ($id = $request->input('id')) {
            $tag = 1;
            $rule = $groupsRuleService->getRowById($id);
            $product = Product::select(['id', 'img', 'sku_flag', 'stock', 'price'])->find($rule['pid']);
            if ($product) {
                $rule['product'] = $product->toArray();
                $rule['product']['url'] = '/shop/product/detail/' . session('wid') . '/' . $rule['pid'];
            } else {
                return myerror();
            }
//            $rule['skus'] = $productPropService->getProps($rule['pid']);
            $rule['skus'] = (new ProductPropsToValuesService())->getSkuList($rule['pid']);
            $rule['skus'] = $rule['skus']['stocks'];
            $res = $groupsSkuService->getlistByRuleId($id);
            if (!$res) {
                return myerror('脏数据错误');
            }
            $temp = [];
            foreach ($res as $item) {
                $temp[$item['sku_id']] = $item;
            }
            foreach ($rule['skus'] as &$val) {
//                $val['groups'] = $temp[$val['id']];
                $val['g_head_price'] = $temp[$val['id']]['head_price'] ?? '';
                $val['g_price'] = $temp[$val['id']]['price'] ?? '';
                $val['id'] = $temp[$val['id']]['id'] ?? '';
            }
            if ($product['sku_flag'] == 0) { //如果没有多规格
                $temp = array_pop($temp);
                $temp['stock_num'] = $product['stock'];
                $temp['g_price'] = $temp['price'];
                $temp['price'] = $product['price'];
                $rule['skus'][] = $temp;
            }
            //判断团购状态

            if ($rule['status'] == -1) {
                $rule['state'] = -1; //失效
            } elseif (strtotime($rule['start_time']) >= time()) {
                $rule['state'] = 2; //未开始
            } elseif (strtotime($rule['start_time']) <= time() && strtotime($rule['end_time']) >= time()) {
                $rule['state'] = 1; //正在进行中
            } elseif (strtotime($rule['end_time']) <= time()) {
                $rule['state'] = 3; //已过期
            }
            $rule['templateData'] = [];
            if ($rule['distribute_template_id'] != 0) {
                $distributionInfo['info'] = $rule['templateData'] = (new DistributeTemplateService())->getRowById($rule['distribute_template_id']);
            }

            //服务保障  add fuguowei
            $rule['sevice'] = explode(',', $rule['service_status']);
        }

        return view('merchants.marketing.togetherGroupAdd', array(
            'title' => '多人拼团',
            'leftNav' => $this->leftNav,
            'slidebar' => 'togetherGroupAdd',
            'rule' => json_encode($rule),
            'tag' => $tag,
            'distribute' => $distributionInfo,
        ));
    }

    /**
     *团购
     */
    public function groupBuy()
    {
        return view('merchants.marketing.groupBuy', array(
            'title' => '团购',
            'leftNav' => $this->leftNav,
            'slidebar' => 'groupBuy'
        ));
    }

    /**
     *团购添加
     */
    public function groupBuyAdd()
    {
        return view('merchants.marketing.groupBuy_add', array(
            'title' => '团购',
            'leftNav' => $this->leftNav,
            'slidebar' => 'groupBuy'
        ));
    }

    /**
     *团购内容页
     */
    public function groupBuyContent()
    {
        return view('merchants.marketing.groupBuy_content', array(
            'title' => '团购内容页',
            'leftNav' => $this->leftNav,
            'slidebar' => 'groupBuy'
        ));
    }

    /**
     * 限时折扣
     */
    public function discount()
    {
        return view('merchants.marketing.discount', array(
            'title' => '限时折扣',
            'leftNav' => $this->leftNav,
            'slidebar' => 'discount'
        ));
    }

    /**
     * 限时折扣添加
     */
    public function discountAdd()
    {
        return view('merchants.marketing.discountAdd', array(
            'title' => '新建限时折扣',
            'leftNav' => $this->leftNav,
            'slidebar' => 'discount'
        ));
    }

    /**
     *赠品
     */
    public function gift()
    {
        return view('merchants.marketing.gift', array(
            'title' => '赠品',
            'leftNav' => $this->leftNav,
            'slidebar' => 'gift'
        ));
    }

    /**
     *赠品添加
     */
    public function giftAdd()
    {
        return view('merchants.marketing.gift_add', array(
            'title' => '新建赠品',
            'leftNav' => $this->leftNav,
            'slidebar' => 'gift'
        ));
    }

    /**
     * 降价拍
     */
    public function cutsBuy()
    {
        return view('merchants.marketing.cutsBuy', array(
            'title' => '降价拍',
            'leftNav' => $this->leftNav,
            'slidebar' => 'cutsBuy'
        ));
    }

    /**
     * 降价拍添加
     */
    public function cutsBuyAdd()
    {
        return view('merchants.marketing.cutsBuy_add', array(
            'title' => '新建降价拍',
            'leftNav' => $this->leftNav,
            'slidebar' => 'cutsBuy'
        ));
    }

    /**
     *签到
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function sign(ShopService $shopService)
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时');
        }
        $signTemplate = [];
        $signData = MSignService::getRow($wid);
        if ($signData['errCode'] == 0 && !empty($signData['data'])) {
            $signTemplate = $signData['data'];
            if (!empty($signTemplate['template_data'])) {
                $signTemplate['template_data'] = MallModule::processTemplateData($wid, $signTemplate['template_data'], 1);
            }
        }
        $store = [];
        /*$storeInfo = WeixinService::getStoreInfo($wid);
        if (!empty($storeInfo['data'])) {
            $store = $storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store = $storeInfo;
        }
        return view('merchants.marketing.sign', array(
            'title' => '签到',
            'leftNav' => $this->leftNav,
            'slidebar' => 'gift',
            'wid' => $wid,
            'store' => json_encode($store),
            'sign_template' => json_encode($signTemplate)
        ));
    }

    /**
     *订单返现
     */
    public function orderCash()
    {
        return view('merchants.marketing.orderCash', array(
            'title' => '新建降价拍',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderCash'
        ));
    }

    /**
     *新建订单返现
     */
    public function orderCashAdd()
    {
        return view('merchants.marketing.orderCash_add', array(
            'title' => '新建降价拍',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderCash'
        ));
    }

    /**
     *支付有礼
     */
    public function payGift()
    {
        return view('merchants.marketing.payGift', array(
            'title' => '支付有礼',
            'leftNav' => $this->leftNav,
            'slidebar' => 'payGift'
        ));
    }

    /*
     * 微商场收款
     */
    public function shopReceivables(Request $request, $status = 0)
    {
        $shopStatus = ['index', 'notes', 'discount', 'label'];
        //var_dump($shopStatus);exit;
        //$shopStatus = isset($shopStatus[$status])?$shopStatus[$status]:'index';
        // var_dump($shopStatus);exit;
        return view('merchants.marketing.shopReceivables', array(
            'title' => '支付有礼',
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopReceivables',
            'status' => $shopStatus
        ));
    }

    /*
     * 微商场收款添加
     */
    public function shopReceivablesAdd()
    {
        return view('merchants.marketing.shopReceivables_add', array(
            'title' => '支付有礼',
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopReceivables'
        ));
    }


    public function egg()
    {
        $activityService = (new ActivityService)->init('wid', session('wid'));
        list($list, $pageHtml) = $activityService->getList();

        return view('merchants.marketing.egg', array(
            'title' => '幸运砸蛋',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'list' => $list,
            'pageHtml' => $pageHtml,
        ));
    }

    public function eggAdd()
    {
        return view('merchants.marketing.egg_add', array(
            'title' => '幸运砸蛋',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170731
     * @desc 大转盘
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 许立 2018年08月16日 获取参与人数
     */
    public function wheelList(Request $request)
    {
        $activityWheelService = new ActivityWheelService();
        //获取活动正在进行中的大转盘
        if ($request->isMethod('post')) {
            $now = date("Y-m-d H:i:s", time());
            $where = [
                'wid' => session('wid'),
                'end_time' => ['>', $now],
            ];
            if ($request->input('keyword') && !empty($request->input('keyword'))) {
                $where['title'] = ['like', '%' . $request->input('keyword') . '%'];
            }
            $data = $activityWheelService->getlistPage($where);
            success('操作成功', '', $data);
        }
        $data = $activityWheelService->getlistPage(['wid' => session('wid')]);

        // 获取参与人数
        $memberCountArr = (new WheelModule())->getMemberCount(array_column($data[0]['data'], 'id'));
        $memberCountArr = array_column($memberCountArr, null, 'wheel_id');
        foreach ($data[0]['data'] as $k => $v) {
            $data[0]['data'][$k]['memberCount'] = $memberCountArr[$v['id']]['memberCount'] ?? 0;
        }

        return view('merchants.marketing.wheel.wheelList', array(
            'title' => '大转盘',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'data' => $data,
        ));

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170731
     * @desc 添加大转盘规则
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 许立 2018年09月14日 只显示自己店铺的优惠券
     */
    public function addWheel(Request $request)
    {
        $data = [];
        if ($request->input('id')) {
            $activityWheelService = new ActivityWheelService();
            $data = $activityWheelService->getRowById($request->input(['id']));
            if ($data) {
                $activityWheelPrizeService = new ActivityWheelPrizeService();
                $data['prize'] = $activityWheelPrizeService->getByWheelId($data['id']);
            }
            //获取关联商品
            $data['product'] = [];
            if ($data['pids']) {
                $where = [
                    'id' => ['in', explode(',', trim($data['pids'], ','))],
                ];
                $data['product'] = ProductService::getModel()->wheres($where)->get(['id', 'title', 'img'])->toArray();
            }
        }
        //获取会员卡列表
        $where = [
            'wid' => session('wid'),
            'state' => 1,
        ];
        $cardData = (new MemberCardService())->getListByWhere($where);
        /****优惠券过期、被删除、或库存为0时，系统不再送券 ***/
        $where = ['wid' => session('wid')];
        $where['_closure'] = function ($query) {
            $query->where(function ($query) {
                $now = date('Y-m-d H:i:s');
                $query->where('expire_type', 0)->where('start_at', '<=', $now)->where('end_at', '>', $now)->where('left', '>', 0)->whereNull('invalid_at');
            })->orWhere(function ($query) {
                //领取后生效的 属于进行中
                $query->where('expire_type', '>', 0)->where('left', '>', 0)->whereNull('invalid_at');
            });
        };

        return view('merchants.marketing.wheel.addWheel', array(
            'title' => '大转盘',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'data' => $data,
            'cardData' => $cardData,
            'conponList' => (new CouponService())->listWithoutPage($where)[0]['data'],
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170731
     * @desc
     * @param Request $request
     */
    public function saveWheel(Request $request)
    {
        $this->checkData($request);
        $input = $request->input();
        if (isset($input['pids'])) {
            $input['pids'] = ',' . $input['pids'] . ',';
        }
        $wheelData = [
            'wid' => session('wid'),
            'title' => $input['title'],
            'start_time' => $input['start_time'],
            'end_time' => $input['end_time'],
            'descr' => $input['descr'],
            'condit' => $input['condit'],
            'card_id' => $input['card_id'] ?? '',
            'label_id' => $input['label_id'] ?? '',
            'reduce_integra' => $input['reduce_integra'],
            'is_send_all' => $input['is_send_all'],
            'send_integra' => $input['send_integra'],
            'rule' => $input['rule'],
            'times' => $input['times'] ?? 1,
            'rate' => $input['rate'],
            //add by wuxiaoping 2017.08.28
            'share_title' => $input['share_title'],
            'share_img' => $input['share_img'],
            'share_desc' => $input['share_desc'],
            'pids' => $input['pids'] ?? '',
        ];

        //编辑
        if ($request->input('id')) {
            $id = $input['id'];
            DB::beginTransaction();
            (new ActivityWheelService())->update($input['id'], $wheelData);
            $prizeService = new ActivityWheelPrizeService();
            foreach ($input['prize'] as $value) {
                if (!isset($value['id']) || empty($value['id'])) {
                    error('奖品id不能为空');
                }
                if (in_array($value['type'], ['1', '2'])) {
                    $value['img'] = '';
                }
                $prizeData = [
                    'wheel_id' => $input['id'],
                    'grade' => $value['grade'],
                    'method' => $value['method'] ?? '',
                    'type' => $value['type'],
                    'content' => $value['content'],
                    'num' => $value['num'],
                    'img' => $value['img'] ?? '',
                ];
                $prizeService->update($value['id'], $prizeData);
            }
            DB::commit();
            success('操作成功', '', $id);
        } else {
            DB::beginTransaction();
            $id = (new ActivityWheelService())->add($wheelData);
            $prizeService = new ActivityWheelPrizeService();
            foreach ($input['prize'] as $value) {
                $prizeData = [
                    'wheel_id' => $id,
                    'grade' => $value['grade'],
                    'method' => $value['method'] ?? '',
                    'type' => $value['type'],
                    'content' => $value['content'],
                    'num' => $value['num'],
                    'img' => $value['img'] ?? '',
                ];
                $prizeService->add($prizeData);
            }
            DB::commit();
            success('操作成功', '', $id);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170801
     * @desc 验证数据是否合法
     */
    public function checkData($request)
    {
        $input = $request->input();
        $rule = Array(
            'title' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'descr' => 'required',
            'condit' => 'required|in:0,1',
            'reduce_integra' => 'required',
            'is_send_all' => 'required',
            'send_integra' => 'required',
            'rule' => 'required',
            'rate' => 'required',
            'prize' => 'required',
        );
        $message = Array(
            'title.required' => '活动名称不能为空',
            'start_time.required' => '活动开始时间不能为空',
            'end_time.required' => '活动结束时间不能为空',
            'descr.required' => '活动描述不能为空',
            'condit.required' => '目标人群不能为空',
            'reduce_integra.required' => '消费积分不能为空',
            'is_send_all.required' => '所有人送积分',
            'send_integra.required' => '送积分数量',
            'rule.required' => '活动品次不能为空',
            'rate.required' => '中奖概率不能为空',
            'prize.required' => '奖品不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        if (strtotime($input['start_time']) >= strtotime($input['end_time'])) {
            error('开始时间不能大于结束时间');
        }
        if ($input['rate'] < 0 || $input['rate'] > 100) {
            error('中奖概率为0%-100%之间');
        }

        $rule1 = Array(
            'grade' => 'required',
            'type' => 'required',
            'content' => 'required',
            'num' => 'required',

        );
        $message1 = Array(
            'grade.required' => '几等奖不能为空',
            'type.required' => '精品类型不能为空',
            'content.required' => '奖品不能为空',
            'num.required' => '奖品数量不能为空',
        );
        foreach ($input['prize'] as $val) {
            $validator = Validator::make($val, $rule1, $message1);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            if ($val['type'] == 1 || $val['type'] == 2) {
                if (!is_numeric($val['content'])) {
                    error('积分和优惠券奖品内容必须为整数');
                }
            }
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170801
     * @desc  获取二维码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCode(Request $request)
    {
        $url = $request->input('url') ?? '';
        return view('merchants.marketing.wheel.getCode', array(
            'data' => $url,
        ));
    }

    /**
     * 获取微商城大转盘活动二维码
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年08月16日
     */
    public function wheelQrCode($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        success('', '', (new CommonModule())->qrCode(session('wid'), config('app.url') . 'shop/activity/wheel/' . session('wid') . '/' . $id));
    }

    /**
     * 下载微商城大转盘活动二维码
     * @param int $id 活动id
     * @return file
     * @author 许立 2018年08月16日
     */
    public function wheelQrCodeDownload($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        return (new CommonModule())->qrCodeDownload(session('wid'), config('app.url') . 'shop/activity/wheel/' . session('wid') . '/' . $id);
    }

    /**
     * 生成小程序大转盘活动二维码
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年08月16日
     */
    public function wheelQrCodeXcx($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        success('', '', (new CommonModule())->qrCode(session('wid'), 'pages/activity/pages/activity/wheel/wheel?id=' . $id, 1));
    }

    /**
     * 下载小程序大转盘活动二维码
     * @param int $id 活动id
     * @return file
     * @author 许立 2018年08月16日
     */
    public function wheelQrCodeXcxDownload($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        (new CommonModule())->qrCodeDownload(session('wid'), 'pages/activity/pages/activity/wheel/wheel?id=' . $id, 1);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 删除大转盘
     * @desc
     */
    public function delWheel($id)
    {
        $wheelService = new ActivityWheelService();
        $res = $wheelService->getRowById($id);
        if (!$res || $res['wid'] != session('wid')) {
            error('该活动不存在');
        }
        $wheelService->del($id);
        $prizeService = new ActivityWheelPrizeService();
        $prizeData = $prizeService->getByWheelId($id);
        foreach ($prizeData as $val) {
            $prizeService->del($val['id']);
        }
        success();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170801
     * @desc 获取列表
     * @param $wheel_id
     * @update 何书哲 2019年06月28日 导出报错处理
     */
    public function wheelCount($wheel_id)
    {
        $data = (new ActivityWheelLogService())->getByWheelId($wheel_id);
        // update 何书哲 2019年06月28日 导出报错处理
        if (empty(app('request')->input('is_export'))) {
            return view('merchants.marketing.wheel.wheelCount', array(
                'title' => '列表统计',
                'leftNav' => $this->leftNav,
                'slidebar' => 'index',
                'data' => $data,
            ));
        }
    }

    /**
     * 营销-秒杀-列表
     * @param string $status 状态 future:未开始,on:进行中,end:已结束
     * @param FavoriteModule $favoriteModule 收藏module
     * @return view
     * @author 许立 2017年07月19日
     * @update 许立 2018年09月06日 增加收藏量
     */
    public function seckills(FavoriteModule $favoriteModule, $status = '')
    {
        //列表条件
        $where = ['wid' => session('wid')];
        $now = date('Y-m-d H:i:s');
        if ($status == 'future') {
            //未开始
            $where['start_at'] = ['>', $now];
        } else if ($status == 'on') {
            //进行中
            $where['start_at'] = ['<=', $now];
            $where['end_at'] = ['>', $now];
        } else if ($status == 'end') {
            //已结束
            $where['end_at'] = ['<=', $now];
        }

        //列表数据
        $seckillService = new SeckillService();
        list($list, $pageHtml) = $seckillService->listWithPage($where);

        //活动状态
        foreach ($list['data'] as &$v) {
            if ($v['invalidate_at'] > '0000-00-00 00:00:00') {
                $v['status'] = '已失效';
            } elseif ($v['start_at'] > $now) {
                $v['status'] = '未开始';
            } elseif ($v['end_at'] <= $now) {
                $v['status'] = '已结束';
            } else {
                $v['status'] = '进行中';
            }

            //推广链接
            $v['url'] = url('/shop/seckill/detail/' . session('wid') . '/' . $v['id']);
        }

        // 收藏量
        $list['data'] = $favoriteModule->handleListFavoriteCount($list['data'], Favorite::FAVORITE_TYPE_SECKILL);

        return view('merchants.marketing.seckills', array(
            'title' => '秒杀',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'list' => $list['data'],
            'pageHtml' => $pageHtml,
            'tabList' => $seckillService->getStaticList()
        ));
    }

    /**
     * 营销-秒杀-新建编辑
     * @param Request $request
     * @param int $id 秒杀ID
     * @return view
     * @author Herry
     * @since 2018/6/20
     */
    public function seckillSet(Request $request, $id = 0)
    {
        $detail = [];
        if ($id) {
            $detail = (new SeckillModule())->getSeckillDetail($id);
        }
        if ($request->isMethod('post')) {
            //获取参数
            $input = $request->input();

            //验证数据
            if (empty($input['product_id'])) {
                error('请选择商品');
            }
            if (empty($input['title'])) {
                error('请填写标题');
            }
            if (empty($input['start_at'])) {
                error('请选择活动开始时间');
            }
            if (empty($input['end_at'])) {
                error('请选择活动结束时间');
            }

            //秒杀活动数据
            $data = [
                'wid' => session('wid'),
                'type' => $input['type'] ?? 0,
                'product_id' => $input['product_id'],
                'title' => $input['title'],
                'start_at' => $input['start_at'],
                'end_at' => $input['end_at'],
                'tag' => $input['tag'],
                'limit_num' => $input['limit_num'],
                'cancel_minutes' => $input['cancel_minutes'],
                'share_title' => $input['share_title'],
                'share_desc' => $input['share_desc'],
                'share_img' => $input['share_img'],
                'skuData' => $input['skuData']
            ];
            if ($id) {
                $data['id'] = $id;
                if ((new SeckillModule())->update($data)) {
                    success('编辑秒杀活动成功');
                } else {
                    error('编辑秒杀活动失败');
                }
            } else {
                if ((new SeckillModule())->add($data)) {
                    success('新建秒杀活动成功');
                } else {
                    error('新建秒杀活动失败');
                }
            }

        }
        return view('merchants.marketing.seckillSet', array(
            'title' => '秒杀',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'detail' => $detail,
        ));
    }

    /**
     * 营销-秒杀-获取商品列表
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年06月20日
     * @update 许立 2018年07月13日 删除 正在参加拼团秒杀商品不能再参加秒杀活动 的限制
     * @update 吴晓平 2018年07月26日  过滤掉纯自提的商品选择
     * @update 许立 2018年08月09日  过滤掉卡密的商品
     */
    public function seckillProducts(Request $request)
    {
        if ($request->isMethod('get')) {
            //获取参数
            $params = $request->input();
            $wid = session('wid');
            $where = [
                'wid' => $wid,
                'status' => 1,
                'cam_id' => 0
            ];

            //add by 吴晓平 过滤掉纯自提的商品选择
            $where['is_hexiao'] = 0;
            //检索条件
            if (!empty($params['keyword']) && !empty($params['keyword_type'])) {
                $field = '';
                if ($params['keyword_type'] == 'product_title') {
                    $field = 'title';
                } elseif ($params['keyword_type'] == 'product_no') {
                    $field = 'goods_no';
                }
                if ($field) {
                    $where[$field] = ['like', '%' . $params['keyword'] . '%'];
                }
            }
            if (!empty($params['group_id'])) {
                $params['group_id'] = addslashes(strip_tags($params['group_id']));
                $where['_string'] = ' FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
            }

            //秒杀不返回面议商品 20171229
            if (!empty($params['filter_negotiable'])) {
                $where['is_price_negotiable'] = 0;
            }

            //过滤掉秒杀没有失效没有结束的商品
            //$seckillingProductIDArr = (new SeckillService())->getSeckillingProductIDArr($wid);

            // 过滤掉 拼团未失效且未结束的商品
            // 删除限制
            //$groupingProductIDArr = (new GroupsRuleService())->getGroupingProductIDArr();
            //$where['id'] = ['not in', array_unique(array_merge($seckillingProductIDArr, $groupingProductIDArr))];

            //获取列表
            $list = ProductService::listWithPage($where, '', '', 10);

            //分页转化
            if (isset($list[1]) && ($list[1] instanceof HtmlString)) {
                $list[1] = $list[1]->toHtml();
            }

            success('', '', $list);
        }
    }

    /**
     * 营销-秒杀-获取详情
     * @param int $id 秒杀ID
     * @return view
     * @author Herry
     * @since 2018/6/20
     */
    public function seckillDetail($id)
    {
        return view('merchants.marketing.seckillDetail', array(
            'title' => '秒杀',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'detail' => (new SeckillModule())->getSeckillDetail($id),
            'tabList' => (new SeckillService())->getStaticList()
        ));
    }

    /**
     * 营销-秒杀-删除
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function seckillDelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $idArr = $request->input('id');
            if (empty($idArr) || !is_array($idArr)) {
                error('参数不正确');
            }

            //执行删除
            foreach ($idArr as $id) {
                (new SeckillModule())->deleteSeckill($id);
            }

            success('删除成功');
        } else {
            error('路由错误');
        }
    }

    /**
     * 营销-秒杀-使失效
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function seckillInvalidate(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            if (empty($id)) {
                error('参数不完整');
            }

            //执行删除
            (new SeckillModule())->invalidateSeckill($id);
            success('使失效成功');
        } else {
            error('路由错误');
        }
    }

    /**
     * 营销-秒杀-获取领取页面的二维码
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function getSeckillQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = config('app.url') . 'shop/seckill/detail/' . $wid . '/' . $id;

        success('', '', QrCode::size(160)->generate(url($url)));
    }

    /**
     * 营销-秒杀-下载领取页面的二维码
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function downloadSeckillQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = config('app.url') . 'shop/seckill/detail/' . $wid . '/' . $id;
        $code = QrCodeService::create($url, '', 200);

        return response()->download($code, time() . '.png');
    }

    /**
     *
     * 砸金蛋首页
     * author: meijie
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 梅杰 2048年8月21日 添加中奖数信息
     */
    public function smokedEggIndex(Request $request)
    {
        //获取分页数据
        $SmokedEggsService = new SmokedEggsService();
        if ($request->isMethod('post')) {
            $condition = $request->input(['keyword']);
            $pageSize = $request->input(['size']);
            $time = date('Y-m-d H:i:s');
            $where = [
                'end_at' => ['>', "'" . $time . "'"],
                'status' => 0
            ];
            if (!empty($condition)) {
                $where['title'] = ['like', '%' . $condition . '%'];
            }
            $data = $SmokedEggsService->listWithPage($where, '', '', $pageSize ?? 15);
            success('', '', $data ?? []);
        }
        list($list, $pageHtml) = $SmokedEggsService->listWithPage();
        $eggMemberService = (new EggMemberService());
        foreach ($list['data'] as &$v) {
            $v['limit_json'] = json_decode($v['limit_json'], 1);
            //获取中奖人数以及参与人数
            $v['logCount'] = $eggMemberService->logCount($v['id']);

        }
        return view('merchants.marketing.egg', array(
            'title' => '幸运砸蛋',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'list' => $list,
            'pageHtml' => $pageHtml,
        ));
    }


    /**
     * 砸金蛋活动添加
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰
     * @update: 梅杰 2018年8月13日 成功后增加跳转地址
     */
    public function smokedEggAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $eggModule = new EggModule();
            //验证数据
            $eggModule->validateData($input);
            if ($eggModule->createActivity($input))
                success('操作成功', '/merchants/marketing/egg/index');
            error();
        }
        return view('merchants.marketing.egg_add', array(
            'title' => '添加幸运砸蛋',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }


    /**修改砸金蛋
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update: 梅杰 2018年8月13日 成功后增加跳转地址
     */
    public function smokedEggEdit(Request $request, $id = 0)
    {
        if (!$id) {
            error('缺少参数');
        }
        $eggModule = new EggModule();
        if ($request->isMethod('post')) {
            $input = $request->input();
            //验证数据
            $re = $eggModule->editEgg($id, $input);;
            if ($re) {
                success('操作成功', '/merchants/marketing/egg/index');
            }
            error();
        }
        $detail = $eggModule->getEggDetailByEggId($id);
        if (!$detail)
            error();
        $limitData = json_decode($detail['limit_json'], 1);
        if (isset($detail['share_json'])) {
            $shareData = json_decode($detail['share_json'], 1);
        }
        return view('merchants.marketing.egg_add', array(
            'title' => '幸运砸蛋活动编辑',
            'detail' => $detail,
            'limitData' => $limitData,
            'shareData' => $shareData ?? '',
            'leftNav' => $this->leftNav,
            'noPrize' => $detail['noPrize'][0] ?? '',
            'slidebar' => 'index'
        ));
    }

    /**
     * 删除活动
     * author: meijie
     * @param Request $request
     * @param int $id
     */
    public function smokedEggDel(Request $request, $id = 0)
    {
        if ($id == 0)
            error('参数缺失');
        $smokedEggsService = new SmokedEggsService();
        $re = $smokedEggsService->delActivity($id, session('wid'));
        if ($re) {
            success();
        }
        error();
    }

    /**
     * 手动终止活动
     * author: meijie
     * @param int $id
     */
    public function smokedEggStop($id = 0)
    {
        $smokedEggsService = new SmokedEggsService();
        if ($smokedEggsService->stopActivity($id, session('wid'))) {
            success();
        }
        error();
    }

    /**
     * 获取该店铺中的砸金蛋奖项（优惠券+积分）
     * author: meijie
     */
    public function getAllPrize()
    {
        $eggModule = new EggModule();
        $data = $eggModule->getAllPrize();
        if ($data) {
            success('', '', $data);
        }
        error();
    }

    /**
     * 创建积分库
     * author: meijie
     * @param Request $request
     * @update 何书哲 2019年05月20日 单次积分数须大于0
     */
    public function addScore(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();

            (!is_numeric($data['amount_score']) || !is_numeric($data['per_score'])) && error('单次积分数或总积分数为非数字类型');
            $data['per_score'] <= 0 && error('单次积分数须大于0');

            //新建积分奖库
            if ($data['amount_score'] > 500000)
                error('积分上限500000');
            if ($data['per_score'] > $data['amount_score'])
                error('单次积分数不能超过总积分数');
            $input['per_score'] = $data['per_score'];
            $input['amount_score'] = $data['amount_score'];
            $input['wid'] = session('wid');
            $input['total'] = $input['left'] = (int)floor($data['amount_score'] / $data['per_score']);
            $service = new ScoreService();
            $re = $service->create($input);
            if ($re) {
                success('', '', $re);
            }
        }
        error();
    }

    /**
     * 获取积分奖库中列表信息
     * author: meijie
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getScore()
    {
        //获取分页数据
        $ScoreService = new ScoreService();
        $data = $ScoreService->listWithPage();
        //获取剩余积分
        foreach ($data[0]['data'] as $k => $v) {
            $times = $v['total'] - $v['left'];//已使用次数
            $data[0]['data'][$k]['left_flag'] = ($times > 0 ? 1 : 0);
            $data[0]['data'][$k]['left_score'] = $v['amount_score'] - $times * $v['per_score'];
        }
        success('', '', $data);
    }

    /**
     *
     * @param Request $request
     * @param $eggId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年8月17日
     */
    public function getEggMemberList(Request $request, $eggId)
    {
        if (!$eggId) {
            error('参数缺失');
        }
        $isExport = $request->input('isExport', 0);
        $status = $request->input(['status']);
        $eggMemberService = new EggMemberService();
        $where = [
            'wid' => session('wid'),
            'egg_id' => $eggId
        ];
        switch ($status) {
            case 1:
                $where['is_prize'] = 1;
                break;
            case 2:
                $where['is_prize'] = 0;
                break;
        }
        $memberService = new MemberService();
        //用于昵称条件的搜索
        /**********start by fuguowei 20180120******************/
        if ($request->input(['name'])) {
            $name['nickname'] = trim($request->input(['name']));
            $search = $memberService->getListByConditionPage($name);
            $where['mid'] = 0;
            if ($search) {
                foreach ($search as $k => $v) {
                    $memberId[] = $v['id'];
                }
                $where['mid'] = ['in', $memberId];
            }
        }
        /*******************end**********************/
        if ($isExport) {
            $list = $eggMemberService->getPrizeInfo($where);
            $data = [
                ['粉丝', '参与时间', '奖品', '收货地址']
            ];
            foreach ($list as &$v) {
                $mData = $memberService->getRowById($v['mid']);
                if (!$mData) {
                    unset($v);
                    continue;
                }
                $address = '';
                if ($v['prize']['type'] == 3) {
                    $row = (new ActivityAwardAddressService())->model
                        ->where('activity_id', $eggId)
                        ->where('mid', $v['mid'])
                        ->where('is_confirm', 1)
                        ->where('type', ActivityAwardAddress::ACTIVITY_TYPE_EGG)
                        ->get()
                        ->toArray();
                    if (!empty($row[0]['is_confirm'])) {
                        $address = (new MemberAddressService())->getAddressById($row[0]['address_id']);
                    }
                }

                $data[] = [
                    filterEmoji($mData['nickname']), $v['created_at'], $v['prize']['name'], ($v['prize']['type'] == 3 && $address) ? $address['title'] . ',' . $address['phone']
                        . ',' . $address['detail'] : ''
                ];
            }
            //导出
            Excel::create(iconv('UTF-8', 'GBK', '砸金蛋中奖'), function ($excel) use ($data) {
                $excel->sheet('score', function ($sheet) use ($data) {
                    $sheet->rows($data);
                });
            })->export('xls');

        } else {
            list($list, $pageHtml) = $eggMemberService->listWithPage($where, '', '', 15);
            foreach ($list['data'] as $k => $v) {
                $mData = $memberService->getRowById($v['mid']);
                if (!$mData) {
                    unset($list['data'][$k]);
                    continue;
                }
                $list['data'][$k]['name'] = $mData['nickname'];
                $list['data'][$k]['headimgurl'] = $mData['headimgurl'];
                $row = (new ActivityAwardAddressService())->model
                    ->where('activity_id', $eggId)
                    ->where('mid', $v['mid'])
                    ->where('is_confirm', 1)
                    ->where('type', ActivityAwardAddress::ACTIVITY_TYPE_EGG)
                    ->get()
                    ->toArray();
                if (!empty($row[0]['is_confirm'])) {
                    $list['data'][$k]['address'] = (new MemberAddressService())->getAddressById($row[0]['address_id']);
                }
            }
            if ($status != 2) {
                $eggModule = new EggModule();
                $list = $eggModule->getLogDetail($list);
            }
            $statusArray = $eggMemberService->getStaticList();
            return view('merchants.marketing.getEggMember', array(
                'title' => '幸运砸蛋',
                'leftNav' => $this->leftNav,
                'slidebar' => 'index',
                'status' => $statusArray,
                'list' => $list,
                'pageHtml' => $pageHtml,
                'eggId' => $eggId,
            ));
        }
    }

    /**
     * 获取未过期切可用的优惠券
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年11月01日 08:58:30
     */
    public function getCouponList()
    {
        // 获取正在进行且未被领取完的优惠券
        $where['wid'] = session('wid');
        $where['invalid_at'] = null;
        $where['left'] = ['>', 0];
        $where['_closure'] = function ($query) {
            $query->where(function ($query) {
                $now = date('Y-m-d H:i:s');
                $query->where('expire_type', 0)
                    ->where('start_at', '<=', $now)
                    ->where('end_at', '>', $now);
            })->orWhere(function ($query) {
                // 领取后生效的 属于进行中
                $query->where('expire_type', '>', 0);
            });
        };

        success('', '', (new CouponService())->listWithPage($where, '', '', 4));
    }

    /**
     * 小程序设置首页
     */
    public function liteapp()
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时');
        }
        $wxxcxConfigService = new WXXCXConfigService();
        $xcxConfigData = $wxxcxConfigService->getRow($wid);
        if ($xcxConfigData['errCode'] == 0 && !empty($xcxConfigData['data'])) {
            return redirect('/merchants/marketing/liteappInfo');
        }
        return view('merchants.marketing.liteapp.index', array(
            'title' => '小程序设置首页',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /**
     * 店铺小程序列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 20180705
     * @update 张永辉 2018年7月13日 限制绑定小程序的数量
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function xcxList(WXXCXConfigService $WXXCXConfigService, ShopService $shopService)
    {
        list($list, $pageHtml) = $WXXCXConfigService->listWithPage(['wid' => session('wid'), 'current_status' => 0]);
        $num = $WXXCXConfigService->count(['wid' => session('wid'), 'current_status' => 0]);
        //$res = WeixinService::getStore(session('wid'));
        $res = $shopService->getRowById(session('wid'));
        if ($res['xcx_num'] > $num) {
            $flag = 1;
        } else {
            $flag = 0;
        }
        return view('merchants.marketing.liteapp.infoList', array(
            'title' => '小程序配置信息',
            'leftNav' => $this->minapp,
            'slidebar' => 'infoList',
            'list' => $list['data'],
            'html' => $pageHtml,
            'flag' => $flag,
        ));
    }


    /**
     * 店铺支付宝小程序列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @param null
     * @return view
     */
    public function aliXcxList(Request $request, AliappConfigService $aliappConfigService)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            if (empty($input['id']) || empty($input['ali_rsa_pub_key'])) {
                error('公钥或id不能为空');
            }
            $res = $aliappConfigService->update($input['id'], ['ali_rsa_pub_key' => $input['ali_rsa_pub_key']]);
            if ($res) {
                success();
            } else {
                error();
            }
        }

        $where = ['wid' => session('wid')];
        $data = $aliappConfigService->getlistPage($where);

        return view('merchants.marketing.liteapp.aliInfoList', array(
            'title' => '小程序配置信息',
            'leftNav' => $this->leftNav,
            'slidebar' => 'alilist',
            'data' => $data,
        ));
    }

    /**
     * 店铺支付宝小程序配置
     * @param null
     * @return view
     */
    public function aliXcxConfigure()
    {
        return view('merchants.marketing.liteapp.aliInfoConfigure', array(
            'title' => '小程序配置信息',
            'leftNav' => $this->leftNav,
            'slidebar' => 'aliconfigure'
        ));
    }

    /**
     * Author: 梅杰 20180706
     * @param Request $request
     * @param WXXCXConfigService $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 梅杰 20180720 增加判断商户证书是否上传标识
     * @update 梅杰 20180725 修改小程序商户证书获取路径
     */
    public function info(Request $request, WXXCXConfigService $service)
    {
        $id = $request->input(['id'], 0);
        $wid = session('wid');

        $miniCertPath = $id == 0 ? "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_cert.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_$id/apiclient_cert.pem";
        $miniKeyPath = $id == 0 ? "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_key.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_$id/apiclient_key.pem";

        $mini_flag = Storage::exists($miniCertPath) && Storage::exists($miniKeyPath) ? 1 : 0;
        return view('merchants.marketing.liteapp.infoNew', array(
            'title' => '小程序配置信息',
            'leftNav' => $this->minapp,
            'slidebar' => 'index',
            'id' => $id,
            'mini_flag' => $mini_flag
        ));
    }

    /**
     * 小程序数据统计
     */
    public function liteStatistics()
    {
        return view('merchants.marketing.liteapp.liteStatistics', array(
            'title' => '小程序数据统计',
            'leftNav' => $this->minapp,
            'slidebar' => 'liteStatistics'
        ));
    }

    /**
     * 小程序微页面列表
     */
    public function litePage()
    {
        //开发阶段status为1
        $status = 0;
        //$status=1;
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时');
        }
        $wxxcxConfigService = new WXXCXConfigService($wid);
        $configData = $wxxcxConfigService->getRow($wid);
        //获取到appid和secret配置信息
        if ($configData['errCode'] == 0 && !empty($configData['data'])) {
            if (!empty($configData['data']['template_id']) && $configData['data']['request_domain'] != '' && $configData['data']['page_list'] != ''
                && $configData['data']['category_list'] != '') {
                $status = 1;
            }
        }
        return view('merchants.marketing.liteapp.page', array(
            'title' => '小程序微页面列表',
            'leftNav' => $this->minapp,
            'slidebar' => 'litePage',
            'status' => $status
        ));
    }

    /**
     * 小程序微页面添加
     */
    public function liteAddPage(Request $request)
    {
        $wid = session('wid');
        //add by jonzhang 2018-01-24 forGao
        $isShow = 0;
        $liWid = config('app.li_wid') ?? 0;
        if (in_array($wid, $liWid)) {
            $isShow = 1;
        }
        //店铺信息
        $store = [];
        $storeInfo = WeixinService::getStoreInfo($wid);
        if (!empty($storeInfo['data'])) {
            $store = $storeInfo['data'];
        }
        return view('merchants.marketing.liteapp.addPage', array(
            'title' => '小程序新建微页面',
            'leftNav' => $this->minapp,
            'slidebar' => 'index',
            'wid' => $wid,
            'isShow' => $isShow,
            'store' => json_encode($store),
        ));
    }

    /**
     * 小程序设置
     */
    public function liteappConfig()
    {
        return view('merchants.marketing.liteapp.config', array(
            'title' => '小程序设置',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index'
        ));
    }

    /**
     * 小程序配置信息
     */
    public function liteappInfo()
    {
        //如果不存在则跳转至默认页面 存在则跳转到配置信息页面
        list($list, $pageHtml) = (new WXXCXConfigService())->listWithPage(['wid' => session('wid'), 'current_status' => 0]);
        if (!$list['data']) {
            return view('merchants.marketing.liteapp.info', array(
                'title' => '小程序配置信息',
                'leftNav' => $this->minapp,
                'slidebar' => 'index'
            ));
        }
        return redirect('/merchants/marketing/Info?id=' . $list['data'][0]['id']);
    }

    /**
     * 小程序配置修改
     */
    public function liteappEdit()
    {
        return view('merchants.marketing.liteapp.edit', array(
            'title' => '小程序配置修改',
            'leftNav' => $this->minapp,
            'slidebar' => 'index'
        ));
    }

    /**
     * 小程序底部导航设置
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function xcxShopNav(ShopService $shopService)
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时', '/auth/login');
        }
        $store = [];
        /*$storeInfo = WeixinService::getStoreInfo($wid);
        if (!empty($storeInfo['data'])) {
            $store = $storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store = $storeInfo;
        }
        return view('merchants.marketing.liteapp.xcxShopNav', array(
            'title' => '店铺导航',
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopNav',
            'bodyClass' => ' ng-controller=myCtrl',
            'store' => json_encode($store),
            'wid' => $wid
        ));
    }

    /******************************投票活动************************************/

    public function vote(Request $request)
    {
        $input = $request->input() ?? [];
        $wid = session('wid');
        $activityVoteService = new ActivityVoteService();
        list($voteData, $pageHtml) = $activityVoteService->getAllList($wid, $input);
        //定义活动状态
        foreach ($voteData['data'] as &$val) {
            if (time() < $val['start_time']) {
                $val['status'] = '未开始';
            } else if ($val['end_time'] < time()) {
                $val['status'] = '已结束';
            } else {
                $val['status'] = '进行中';
            }
        }
        return view('merchants.marketing.vote.index', [
            'title' => '投票活动列表',
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopNav',
            'voteData' => $voteData,
            'pageHtml' => $pageHtml
        ]);
    }

    /**
     * 新建投票活动
     * @return [type] [description]
     */
    public function voteSave(Request $request)
    {
        $activityVoteService = new ActivityVoteService();
        $voteData = [];
        $wid = session('wid');
        $id = $request->input('id') ?? '';
        if ($request->isMethod('post')) {
            $input = $request->input();
            $input['wid'] = $wid;
            if (!isset($input['keyword']) || empty($input['keyword'])) {
                $input['keyword'] = '号码';
            } else {
                $input['keyword'] = trim($input['keyword']);
            }
            $rule = Array(
                'act_title' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'act_img' => 'required',
                'vote_rule' => 'required',
                'prize_set' => 'required',
                //'text_prompt'  => 'required',
                //'sign_info'    => 'required',
                'canvass_info' => 'required',
                'act_rule' => 'required',
            );
            $message = Array(
                'act_title.required' => '活动名称不能为空',
                'start_time.required' => '活动开始时间不能为空',
                'end_time.required' => '活动结束时间不能为空',
                'act_img.required' => '活动图片不能为空',
                'vote_rule.required' => '投票规则不能为空',
                'prize_set.required' => '奖项设置不能为空',
                //'text_prompt.required'  => '设置描述文字不能为空',
                //'sign_info.required'    => '我要报名信息不能为空',
                'canvass_info.required' => '拉票秘籍不能为空',
                'act_rule.required' => '活动规则不能为空',
            );

            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $msg = '';
            $input['act_title'] = trim($input['act_title']);
            $input['vote_rule'] = trim($input['vote_rule']);
            $input['start_time'] = strtotime($input['start_time']);
            $input['end_time'] = strtotime($input['end_time']);
            $input['prize_set'] = $input['prize_set'];
            $input['canvass_info'] = $input['canvass_info'];
            $input['act_rule'] = $input['act_rule'];
            if (isset($input['id']) && $input['id']) {
                $rs = $activityVoteService->update($input['id'], $input);
                $msg = '活动更新成功';
            } else {
                $rs = $activityVoteService->add($input);
                $msg = '活动发布成功';
            }

            if ($rs) {
                success($msg);
            }
            error();
        }

        //编辑
        if ($id) {
            $voteData = $activityVoteService->getRowById($id);
        }

        return view('merchants.marketing.vote.voteAdd', [

            'title' => '投票发布',
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopNav',
            'voteData' => $voteData
        ]);
    }

    /**
     * 删除投票活动
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function voteDel(Request $request)
    {
        $id = $request->input('id') ?? '';
        if (empty($id)) {
            error('请先选择要删除的活动');
        }

        if ((new ActivityVoteService())->del($id)) {
            success();
        }
        error();
    }

    //生成、下载二维码
    public function createQrcode(Request $request)
    {
        $id = $request->input('id') ?? '';
        $wid = session('wid');
        if (empty($id)) {
            error('请先指定要生成活动的二维码');
        }
        /*下载二维码*/
        if (isset($input['download']) && $input['download']) {
            $data = (new ActivityVoteService())->getRowById($id);
            return response()->download($filename, $data['act_title'] . '.png');  //下载
        }
        $webUrl = config('app.url') . 'shop/vote/index/' . $wid . '/' . $id;
        $filename = QrCodeService::create($webUrl, '', 238, 'vote/' . $wid . '/' . $id); //生成二维码

        //返回二维码图片的地址
        $imgUrl = imgUrl('/hsshop/image/qrcodes/vote/' . $wid . '/' . $id . '/qrcode.png');
        success('', '', $imgUrl);
    }

    /**
     * 获取参加投票活动的用户信息
     * @return [type] [description]
     */
    public function getEnrollUsersList(EnrollInfoService $enrollInfoService, Request $request)
    {
        $wid = session('wid');
        $input = $request->input();
        $voteId = $input['vote_id'] ?? 0;
        if (!$voteId) {
            error('请先选择要查看对应活动的结果');
        }
        $condition['vote_id'] = $voteId;
        list($voteUserList, $pageHtml) = $enrollInfoService->getAllList($wid, $input);
        //获取活动的报名信息
        if ($voteUserList) {
            foreach ($voteUserList['data'] as $key => &$value) {
                $enrollInfo = json_decode($value['enroll_info'], true);
                $value['book_name'] = $enrollInfo['name'];
                $value['book_mobile'] = $enrollInfo['phone'];

                $members = (new MemberService())->getRowById($value['mid']);
                $value['wechat_id'] = $members['wechat_id'];
                $value['nickname'] = $members['nickname'];
            }
        }
        return view('merchants.marketing.vote.userList', [
            'title' => '查看参加投票活动用户列表',
            'voteUserList' => $voteUserList['data'],
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopNav',
            'voteId' => $voteId,
            'pageHtml' => $pageHtml
        ]);
    }

    /**
     * 删除参加投票活动用户
     * @param  [int]            $id                [参加投票用户id]
     * @param  EnrollInfoService $enrollInfoService [description]
     * @return [type]                               [description]
     */
    public function enrollUserDel(Request $request, EnrollInfoService $enrollInfoService)
    {
        $id = $request->input('id') ?? 0;
        if (!$id) {
            error('请先选择要查看对应参加的活动用户');
        }
        $enrollInfo = $enrollInfoService->getRowById($id);
        if (empty($enrollInfo)) {
            error('该用户不存在或已被删除');
        }
        if ($enrollInfoService->del($id)) {
            success('删除成功');
        }
        error();
    }

    /**
     * 获取投票人列表
     * @return [type] [description]
     */
    public function getVoteUserList(Request $request, VoteLogService $voteLogService)
    {
        $wid = session('wid');
        $input = $request->input();
        $enrollId = $input['enroll_id'] ?? 0;
        $voteId = $input['vote_id'] ?? 0;
        if (!$enrollId) {
            error('请先选择要查看对应参加的活动用户');
        }

        if (!$voteId) {
            error('请先选择要查看对应的投票活动');
        }
        list($memberDatas, $pageHtml) = $voteLogService->getListByWhereData($wid, ['enroll_id' => $enrollId, 'vote_id' => $voteId], 'mid', $input);
        return view('merchants.marketing.vote.voteUserList', [
            'title' => '投票人列表',
            'leftNav' => $this->leftNav,
            'slidebar' => 'shopNav',
            'memberDatas' => $memberDatas['data'] ?? [],
            'input' => $input
        ]);
    }

    /*******小程序底部导航设置*********/
    public function footerBar()
    {
        return view('merchants.marketing.liteapp.footerBar', [
            'title' => '小程序底部导航设置',
            'leftNav' => $this->minapp,
            'slidebar' => 'footerBar'
        ]);
    }

    /**
     * 设置新页面（处理更新标题或图标）
     * @author 吴晓平 <2018年07月19日>
     * @return [type] [description]
     */
    public function footerSyncBar()
    {
        return view('merchants.marketing.liteapp.footerSyncBar', [
            'title' => '小程序底部导航设置',
            'leftNav' => $this->minapp,
            'slidebar' => 'footerBar'
        ]);
    }

    /**
     * 设置新页面（处理更新标题或图标）数据
     * @author 吴晓平 <2018年07月19日>
     * @return [type] [description]
     */
    public function getSyncSimpleBarDataList()
    {
        $wid = session('wid');
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        $strData = $xcxSyncFooterBarService->getSyncBarData($wid);
        $returnData['tabBar']['list'] = [];
        if (empty($strData) || empty(json_decode($strData, true))) {
            list($list, $pageHtml) = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
            if ($list['data']) {
                foreach ($list['data'] as $key => $value) {
                    $returnData['tabBar']['list'][$key]['id'] = $value['id'];
                    $returnData['tabBar']['list'][$key]['text'] = $value['name'];
                    $returnData['tabBar']['list'][$key]['pagePath'] = $value['page_path'];
                    $returnData['tabBar']['list'][$key]['iconPath'] = $value['icon_path'];
                    $returnData['tabBar']['list'][$key]['selectedIconPath'] = $value['selected_path'];
                    $returnData['tabBar']['list'][$key]['urlTitle'] = $value['url_title'];
                    $returnData['tabBar']['list'][$key]['pageId'] = $value['page_id'];
                    $returnData['tabBar']['list'][$key]['isSyncWeixin'] = $value['is_sync_weixin'];
                    $returnData['tabBar']['list'][$key]['isCanReviseUrl'] = $value['is_can_revise_url'] == 0 ? false : true;
                }
            }
        } else {
            $syncData = json_decode($strData, true);
            foreach ($syncData as $key => $value) {
                $returnData['tabBar']['list'][$key]['id'] = $value['id'];
                $returnData['tabBar']['list'][$key]['text'] = $value['text'];
                $returnData['tabBar']['list'][$key]['pagePath'] = $value['pagePath'];
                $returnData['tabBar']['list'][$key]['iconPath'] = $value['iconPath'];
                $returnData['tabBar']['list'][$key]['selectedIconPath'] = $value['selectedIconPath'];
                $returnData['tabBar']['list'][$key]['urlTitle'] = $value['urlTitle'];
                $returnData['tabBar']['list'][$key]['pageId'] = $value['pageId'];
                $returnData['tabBar']['list'][$key]['isSyncWeixin'] = $value['isSyncWeixin'];
                $returnData['tabBar']['list'][$key]['isCanReviseUrl'] = $value['isCanReviseUrl'] == 0 ? false : true;
            }

        }
        success('', '', $returnData);
    }

    /**
     * 新刷底部导航栏数据
     * @author 吴晓平 <2019.03.26>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function refreshFooterBar(Request $request)
    {
        $wid = $request->input('wid', 0);
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        if ($xcxSyncFooterBarService->delSyncBarData($wid)) {
            success();
        }
        error();
    }

    /**
     * 是否开启自动审核
     * @param Request $request
     * @update: 梅杰 20180705 指定小程序开启自动审核
     */
    public function isAuthAuditing(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id', 0);
        if (!$wid || !$id) {
            error('登录超时');
        }
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowByIdWid($wid, $id);
        $row['errCode'] != 0 && error('小程序不存在');
        $input = $request->input() ?? [];
        $isAuthSubmit = $input['isAuthSubmit'] ?? 0;
        if ($row['data']) {
            $rs = $xcxConfigService->updateData($row['data']['id'], ['is_auth_submit' => $isAuthSubmit]);
            if ($rs) {
                success();
            }
        }
        error();
    }

    /**
     * 小程序底部导航数据返回
     * @return [type] [description]
     */
    public function getBarDataList()
    {
        $wid = session('wid');
        if (!$wid) {
            error('登录超时');
        }
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRow($wid);
        $row['errCode'] != 0 && error('小程序不存在');

        $xcxFooterBarService = new WXXCXFooterBarService();
        list($list, $pageHtml) = $xcxFooterBarService->getAllList($wid, [], 'order');
        $returnData = [];
        //$returnData['isBinding'] = !empty($row['data']) ? 1 : 0;
        $returnData['isBinding'] = 0;
        //add by jonzhang 2018-03-07 设置域名，上传代码，页面，类目，才可以提交审核
        if (!empty($row['data']['app_secret']) && !empty($row['data']['request_domain']) && !empty($row['data']['template_id']) && !empty($row['data']['category_list']) && !empty($row['data']['page_list'])) {
            $returnData['isBinding'] = 1;
        }
        $returnData['xcx_status'] = $row['data']['status'] ?? 0;
        $returnData['is_auth_submit'] = 1;
        $returnData['tabBar']['selectedColor'] = '#b1292d';
        $returnData['tabBar']['backgroundColr'] = '#fff';
        $returnData['tabBar']['borderStyle'] = 'black';
        //根据小程序底部栏的格式返回数据
        if ($list['data']) {
            foreach ($list['data'] as $key => $value) {
                $returnData['tabBar']['list'][$key]['id'] = $value['id'];
                $returnData['tabBar']['list'][$key]['text'] = $value['name'];
                $returnData['tabBar']['list'][$key]['pagePath'] = $value['page_path'];
                $returnData['tabBar']['list'][$key]['iconPath'] = $value['icon_path'];
                $returnData['tabBar']['list'][$key]['selectedIconPath'] = $value['selected_path'];
                $returnData['tabBar']['list'][$key]['urlTitle'] = $value['url_title'];
                $returnData['tabBar']['list'][$key]['pageId'] = $value['page_id'];
                $returnData['tabBar']['list'][$key]['isSyncWeixin'] = $value['is_sync_weixin'];
                $returnData['tabBar']['list'][$key]['isCanReviseUrl'] = $value['is_can_revise_url'] == 0 ? false : true;
                $returnData['is_auth_submit'] = $row['data']['is_auth_submit'] ?? 0;
            }
        } else {
            $returnData['tabBar']['list'] = [];
        }
        success('', '', $returnData);
    }

    /**
     * 小程序底部导航数据返回
     * @return [type] [description]
     */
    public function getSyncBarDataList()
    {
        $wid = session('wid');
        if (!$wid) {
            error('登录超时');
        }
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRow($wid);
        $row['errCode'] != 0 && error('小程序不存在');
        $row['data']['status'] == 2 && error('小程序在审核中');

        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        list($list, $pageHtml) = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
        $returnData = [];
        //根据小程序底部栏的格式返回数据
        if ($list['data']) {
            foreach ($list['data'] as $key => $value) {
                $returnData['tabBar']['list'][$key]['id'] = $value['id'];
                $returnData['tabBar']['list'][$key]['text'] = $value['name'];
                $returnData['tabBar']['list'][$key]['pagePath'] = $value['page_path'];
                $returnData['tabBar']['list'][$key]['iconPath'] = $value['icon_path'];
                $returnData['tabBar']['list'][$key]['selectedIconPath'] = $value['selected_path'];
                $returnData['tabBar']['list'][$key]['urlTitle'] = $value['url_title'];
                $returnData['tabBar']['list'][$key]['pageId'] = $value['page_id'];
                $returnData['tabBar']['list'][$key]['isSyncWeixin'] = $value['is_sync_weixin'];
                $returnData['tabBar']['list'][$key]['isCanReviseUrl'] = $value['is_can_revise_url'] == 0 ? false : true;
                $returnData['is_auth_submit'] = $row['data']['is_auth_submit'] ?? 0;
            }
        } else {
            $returnData['tabBar']['list'] = [];
        }
        success('', '', $returnData);
    }

    /**
     * 处理添加，编辑，删除
     * @update 梅杰 20180716 获取小程序配置id错误
     */
    public function SaveBar(Request $request)
    {
        $wid = session('wid');
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRow($wid);
        $row['errCode'] != 0 && error('小程序不存在');
        $input = $request->input();
        $isSyncWeixin = $input['isSyncWeixin'] ?? false;
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        $xcxFooterBarService = new WXXCXFooterBarService();
        list($list, $pageHtml) = $xcxFooterBarService->getAllList($wid);
        $ids = $selectId = [];  //保存全部的底部
        if ($list['data']) {
            foreach ($list['data'] as $key => $value) {
                $ids[] = $value['id'];
            }
        }
        $rule = [
            'text' => 'required',
            'pagePath' => 'required',
            'iconPath' => 'required',
            'selectedIconPath' => 'required',
        ];
        $messages = [
            'text.required' => '名称不能为空',
            'pagePath.required' => '链接不能为空',
            'iconPath.required' => '图片不能为空',
            'selectedIconPath.required' => '图片不能为空',
        ];
        if (empty($input['barList'])) {
            error('操作异常');
        }

        //添加标识，主要用于操作更新底部导航栏标题，图标可立即生效
        $type = $input['type'] ?? 0;
        if ($type) {
            $rs = $xcxSyncFooterBarService->saveSyncBarByRedis($wid, $input['barList']);
            if ($rs) {
                success();
            }
            error();
        }
        foreach ($input['barList'] as $key => $value) {
            $validator = Validator::make($value, $rule, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $selectId[] = $value['id'];
            $data['name'] = $value['text'];
            $data['page_path'] = $value['pagePath'];
            $data['icon_path'] = $value['iconPath'] ?? '';
            $data['selected_path'] = $value['selectedIconPath'] ?? '';
            $data['url_title'] = $value['urlTitle'] ?? '';
            $data['is_can_revise_url'] = (int)$value['isCanReviseUrl'];
            $data['page_id'] = $value['pageId'] ?? 0;
            $data['is_sync_weixin'] = (int)$isSyncWeixin;
            $data['order'] = $key;

            //处理添加操作
            if ($value['id'] == 0) {
                $data['wid'] = session('wid');
                try {
                    $rs = $xcxFooterBarService->add($data);
                } catch (Exception $e) {
                    error($input['text'] . '底部栏添加失败');
                }
                //处理编辑操作
            } else {
                try {
                    $rs = $xcxFooterBarService->update($value['id'], $data);
                } catch (Exception $e) {
                    error($input['text'] . '底部栏修改失败');
                }
            }
        }
        //处理删除操作
        $diffId = array_diff($ids, $selectId);
        if ($diffId) {
            foreach ($diffId as $id) {
                try {
                    $rs = $xcxFooterBarService->del($id);
                } catch (Exception $e) {
                    error($e->getMessage());
                }
            }
        }
        if ($rs) {
            if ($isSyncWeixin == true) {
                //当保存并更新到小程序时，先把存在的数据删除，并添加新数据
                $syncData = $xcxSyncFooterBarService->getAllList($wid);
                if (isset($syncData[0]['data']) && $syncData[0]['data']) {
                    foreach ($syncData[0]['data'] as $key => $value) {
                        $xcxSyncFooterBarService->del($value['id']);
                    }
                }

                foreach ($input['barList'] as $ikey => $item) {
                    $data['name'] = $item['text'];
                    $data['page_path'] = $item['pagePath'];
                    $data['icon_path'] = $item['iconPath'] ?? '';
                    $data['selected_path'] = $item['selectedIconPath'] ?? '';
                    $data['url_title'] = $item['urlTitle'] ?? '';
                    $data['is_can_revise_url'] = (int)$item['isCanReviseUrl'];
                    $data['page_id'] = $item['pageId'] ?? 0;
                    $data['is_sync_weixin'] = (int)$isSyncWeixin;
                    $data['order'] = $ikey;
                    $data['wid'] = $wid;
                    $xcxSyncFooterBarService->add($data);
                }

                if ($row['data']) {
                    if ($row['data']['status'] == 2) {
                        error('小程序已提交审核中，请稍后再提交更新');
                    }
                }
//                $userInfo = session('userInfo');
//                $rs = (new WXXCXConfigService())->getListByCondition(['wid' => $wid,'current_status'=>0]);
//                if ($rs['errCode'] == 0 && $rs['data']) {
//                    foreach ($rs['data'] as $v) {
//                        $this->authSubmitSave($input['barList'],$v['id']);
//                        $job = new batchSaveBar($v['id'],$userInfo,$input['barList']);
//                        $this->dispatch($job->onQueue('saveBar'));
//                    }
//                }
                $this->authSubmitSave($input['barList'], $row['data']['id']);
            }
            success();
        }

        error();
    }

    /**
     * 手动提交审核处理
     * @param  Request $request [description]
     * @return [type]           [description]
     * @update 梅杰 20180712 根据小程序Id提交版本
     */
    public function nomalSubmitSave(Request $request)
    {
        $isSyncWeixin = $request->input('isSyncWeixin') ?? false;
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        $wid = session('wid');
        list($list, $pageHtml) = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
        if (empty($list['data'])) {
            error('暂无数据更新审核');
        }
        $xcxConfigId = $request->input('xcxConfigId', 0);
        if ($isSyncWeixin) {
            foreach ($list['data'] as $key => &$value) {
                $value['iconPath'] = $value['icon_path'];
                $value['selectedIconPath'] = $value['selected_path'];
                $value['text'] = $value['name'];
                $value['pagePath'] = $value['page_path'];
            }
            $this->authSubmitSave($list['data'], $xcxConfigId);
        }

    }

    /**
     * 上传代码并提交审核
     * @author wuxiaoping 2017.12.15
     * @return [type]     [description]
     * @update 何书哲 2018年7月12日 截取category_list前5个（微信提交审核限制1-5个，这里取最大值5）
     * @update 梅杰 20180712 根据小程序Id提交版本
     */
    public function authSubmitSave($input = [], $xcxConfigId)
    {
        $wid = session('wid');

        //add by jonzhang 2018-05-22
        $userInfo = session('userInfo');
        $operatorId = $userInfo['id'] ?? 0;
        $operator = $userInfo['name'] ?? '';
        if (!$wid) {
            error('登录超时');
        }

        $data = [];
        if ($input) {
            foreach ($input as $key => $value) {
                $iconPathArr = explode('mctsource/', $value['iconPath']);
                $selectedPathArr = explode('mctsource/', $value['selectedIconPath']);
                $data[$key]['text'] = $value['text'];
                $data[$key]['pagePath'] = $value['pagePath'];
                $data[$key]['iconPath'] = !empty($iconPathArr) ? $iconPathArr[1] : '';
                $data[$key]['selectedIconPath'] = !empty($selectedPathArr) ? $selectedPathArr[1] : '';
            }
        }
        $barList['selectedColor'] = '#b1292d';
        $barList['list'] = $data;
        $barList['backgroundColor'] = '#fff';
        $barList['borderStyle'] = 'black';
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($xcxConfigId);
        $row['errCode'] != 0 && error('小程序不存在');
        $pagesList = json_decode($row['data']['page_list'], true);
        if ($row['data']) {
            $templateId = $row['data']['template_id'] ?? 0;
            $xcxModule = new XCXModule();
            $categoryList = json_decode($row['data']['category_list'], true);
            if (!$categoryList) {
                $categoryList = $xcxModule->getCategory($row, true, $operatorId, $operator);
                //error('提交审核项的一个列表不能为空（至少填写1项，至多填写5项）');
            }
            //何书哲 2018年7月12日 截取category_list前5个（微信提交审核限制1-5个，这里取最大值5）
            if ($categoryList) {
                $categoryList = array_slice($categoryList, 0, 5);
            }
            $itemList = [];
            foreach ($categoryList as $key => $value) {
                $value['title'] = '首页';
                $value['tag'] = '';
                $value['address'] = !empty($pagesList) ? $pagesList[0] : 'pages/index/index';
                $itemList[$key] = $value;
            }

            //add by jonzhang 模板id为最新模板id
            //begin
            $userVersion = $row['data']['version'];
            $userDesc = $row['data']['version_desc'];
            $xcxOnlineData = $xcxModule->getXCXOnLine();
            if ($xcxOnlineData['errCode'] == 0 && !empty($xcxOnlineData['data'])) {
                $templateId = $xcxOnlineData['data']['template_id'] ?? 0;
                $userVersion = $xcxOnlineData['data']['user_version'] ?? '';
                $userDesc = $xcxOnlineData['data']['user_desc'] ?? '';
            }

            if (empty($templateId)) {
                error('模板Id不能够为0');
            }
            //end

            $xcxModule->commit($xcxConfigId, $templateId, $userVersion, $userDesc, $barList, true, $itemList, $operatorId, $operator);
        }

    }

    /**
     * todo add by jonzhang
     * @param Request $request
     */
    public function topnav(Request $request)
    {
        return view('merchants.marketing.liteapp.topNav', array(
            'title' => '店铺导航',
            'leftNav' => $this->minapp,
            'slidebar' => 'topnav',
            'bodyClass' => ' ng-controller=myCtrl'
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180327
     * @desc 获取留言列表
     * @param Request $request
     */
    public function getRemark(Request $request)
    {
        $ruleId = $request->input('rule_id');
        $res = (new GroupsService())->getListByRuleId($ruleId);
        $input = $request->input();
        $gids = array_column($res, 'id');
        $type = $request->input('type', '');
        if ($type == 1) {
            $order = (new OrderModule())->getOrderReMarkByGids($gids, $type);
            exit();
        } else {
            $result = (new MeetingGroupsRuleModule())->getRemark($ruleId, $input);
        }
        return view('merchants.marketing.getRemark', array(
            'title' => '注册信息列表',
            'leftNav' => $this->leftNav,
            'slidebar' => 'togetherGroupList',
            'result' => $result,
        ));
    }


    /**
     * @author hsz
     * @date 20180515
     * @desc 刮刮卡列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function scratchList(Request $request)
    {
        $activityScratchService = new ActivityScratchService();
        $activityScratchLogService = new ActivityScratchLogService();
        $activityScratchPrizeService = new ActivityScratchPrizeService();
        $where['wid'] = session('wid');
        //搜索
        if ($title = $request->input('title')) {
            $where['title'] = ['like', '%' . $title . '%'];
        }
        $data = $activityScratchService->getlistPage($where);

        foreach ($data[0]['data'] as &$val) {
            //获取参与人数、参与次数、领到人数、未领到人数
            $res = $activityScratchLogService->getPrizeInfo($val['id'], $val['wid']);
            $val['participate_user_num'] = $res['participate_user_num'];
            $val['participate_total_num'] = $res['participate_total_num'];
            $val['receive_user_num'] = $res['receive_user_num'];
            $val['unreceive_user_num'] = $res['unreceive_user_num'];
            //获取配置的奖项
//            $prizeArr = $activityScratchPrizeService->getByScratchId($val['id']);
//            $val['prize'] = (new ScratchModule())->dealPrizeInfo($prizeArr);
        }
        return view('merchants.marketing.scratch.scratchList', array(
            'title' => '刮刮卡',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'data' => $data
        ));
    }

    /**
     * @author hsz
     * @date 20180515
     * @desc 添加刮刮卡规则
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年09月14日 只显示自己店铺的优惠券
     * @update 许立 2018年09月14日 只显示自己店铺的优惠券
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function addScratch(Request $request, ShopService $shopService)
    {
        $data = [];
        //编辑刮刮卡
        if ($request->input('id')) {
            $activityScratchService = new ActivityScratchService();
            $data = $activityScratchService->getRowById($request->input(['id']));
            if ($data) {
                $activityScratchPrizeService = new ActivityScratchPrizeService();
                $data['prize'] = $activityScratchPrizeService->getByScratchId($data['id']);
            }
            //获取关联商品
            $data['product'] = [];
            if ($data['pids']) {
                $where = [
                    'id' => ['in', explode(',', trim($data['pids'], ','))],
                ];
                $data['product'] = ProductService::getModel()->wheres($where)->get(['id', 'title', 'img'])->toArray();
            }
        }
        //获取会员卡列表
        $where = [
            'wid' => session('wid'),
            'state' => 1,
        ];
        $cardData = (new MemberCardService())->getListByWhere($where);
        /****优惠券过期、被删除、或库存为0时，系统不再送券 ***/
        $where = ['wid' => session('wid')];
        $where['_closure'] = function ($query) {
            $query->where(function ($query) {
                $now = date('Y-m-d H:i:s');
                $query->where('expire_type', 0)->where('start_at', '<=', $now)->where('end_at', '>', $now)->where('left', '>', 0)->whereNull('invalid_at');
            })->orWhere(function ($query) {
                //领取后生效的 属于进行中
                $query->where('expire_type', '>', 0)->where('left', '>', 0)->whereNull('invalid_at');
            });
        };
        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop(session('wid'));
        $shop = $shopService->getRowById(session('wid'));
        return view('merchants.marketing.scratch.addScratch', array(
            'title' => '刮刮卡',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'data' => $data,
            'cardData' => $cardData,
            'couponList' => (new CouponService())->listWithoutPage($where)[0]['data'],
            'shopData' => $shop
        ));
    }

    /**
     * @author hsz
     * @date 20180515
     * @desc 保存刮刮卡
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function saveScratch(Request $request)
    {
        $this->checkScratchData($request);
        $input = $request->input();
        $scratchData = [
            'wid' => session('wid'),
            'title' => $input['title'],
            'start_time' => $input['start_time'],
            'end_time' => $input['end_time'],
            'descr' => $input['descr'],
            'condit' => $input['condit'],
            'card_id' => $input['card_id'] ?? '',
            'label_id' => $input['label_id'] ?? '',
            'reduce_integra' => $input['reduce_integra'],
            'is_send_all' => $input['is_send_all'],
            'send_integra' => $input['send_integra'],
            'rule' => $input['rule'],
            'rate' => $input['rate'],
            'unwin_descr' => $input['unwin_descr'],
            'pids' => $input['pids'] ?? '',
        ];
        //编辑
        if ($request->input('id')) {
            $id = $input['id'];
            DB::beginTransaction();
            (new ActivityScratchService())->update($id, $scratchData);
            $prizeService = new ActivityScratchPrizeService();
            foreach ($input['prize'] as $value) {
                if (!isset($value['id']) || empty($value['id'])) {
                    error('奖品id不能为空');
                }
                $prizeData = [
                    'scratch_id' => $input['id'],
                    'grade' => $value['grade'],
                    'type' => $value['type'],
                    'content' => $value['content'],
                    'num' => $value['num'],
                    'method' => $value['method'] ?? '',
                    'img' => $value['img'] ?? '',
                ];
                $prizeService->update($value['id'], $prizeData);
            }
            DB::commit();
            success('操作成功', '', $id);
        } else {
            DB::beginTransaction();
            $id = (new ActivityScratchService())->add($scratchData);
            $prizeService = new ActivityScratchPrizeService();
            foreach ($input['prize'] as $value) {
                $prizeData = [
                    'scratch_id' => $id,
                    'grade' => $value['grade'],
                    'type' => $value['type'],
                    'content' => $value['content'],
                    'num' => $value['num'],
                    'method' => $value['method'] ?? '',
                    'img' => $value['img'] ?? '',
                ];
                $prizeService->add($prizeData);
            }
            DB::commit();
            success('操作成功', '', $id);
        }
    }

    /**
     * 获取微商城刮刮卡活动二维码
     * @param int $id 活动id
     * @return json
     * @author 何书哲 2018年08月24日
     */
    public function scratchQrCode($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        success('', '', (new CommonModule())->qrCode(session('wid'), config('app.url') . 'shop/activity/scratch/' . session('wid') . '/' . $id));
    }

    /**
     * 下载微商城刮刮卡活动二维码
     * @param int $id 活动id
     * @return file
     * @author 何书哲 2018年08月24日
     */
    public function scratchQrCodeDownload($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        return (new CommonModule())->qrCodeDownload(session('wid'), config('app.url') . 'shop/activity/scratch/' . session('wid') . '/' . $id);
    }

    /**
     * 生成小程序刮刮卡活动二维码
     * @param int $id 活动id
     * @return json
     * @author 何书哲 2018年08月24日
     */
    public function scratchQrCodeXcx($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        success('', '', (new CommonModule())->qrCode(session('wid'), 'pages/scratchCard/scratchCard?scratchId=' . $id, 1));
    }

    /**
     * 下载小程序刮刮卡活动二维码
     * @param int $id 活动id
     * @return file
     * @author 何书哲 2018年08月24日
     */
    public function scratchQrCodeXcxDownload($id)
    {
        if (empty($id)) {
            error('活动id不能为空');
        }
        (new CommonModule())->qrCodeDownload(session('wid'), 'pages/scratchCard/scratchCard?scratchId=' . $id, 1);
    }


    /**
     * @author hsz
     * @desc 验证数据是否合法
     */
    public function checkScratchData($request)
    {
        $input = $request->input();
        $rule = Array(
            'title' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'descr' => 'required',
            'condit' => 'required|in:0,1',
            'reduce_integra' => 'required',
            'is_send_all' => 'required',
            'send_integra' => 'required',
            'rule' => 'required',
            'rate' => 'required',
            'unwin_descr' => 'required',
            'prize' => 'required',
        );
        $message = Array(
            'title.required' => '活动名称不能为空',
            'start_time.required' => '活动开始时间不能为空',
            'end_time.required' => '活动结束时间不能为空',
            'descr.required' => '活动描述不能为空',
            'condit.required' => '目标人群不能为空',
            'reduce_integra.required' => '消费积分不能为空',
            'is_send_all.required' => '所有人送积分',
            'send_integra.required' => '送积分数量',
            'rule.required' => '活动品次不能为空',
            'rate.required' => '中奖概率不能为空',
            'unwin_descr.required' => '未中奖说明不能为空',
            'prize.required' => '奖品不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        if (strtotime($input['start_time']) >= strtotime($input['end_time'])) {
            error('开始时间不能大于结束时间');
        }
        if ($input['rate'] < 0 || $input['rate'] > 100) {
            error('中奖概率为0%-100%之间');
        }
        $rule1 = Array(
            'grade' => 'required',
            'type' => 'required',
            'content' => 'required',
            'num' => 'required',
        );
        $message1 = Array(
            'grade.required' => '几等奖不能为空',
            'type.required' => '奖品类型不能为空',
            'content.required' => '奖品不能为空',
            'num.required' => '奖品数量不能为空',
        );
        foreach ($input['prize'] as $val) {
            $validator = Validator::make($val, $rule1, $message1);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            if ($val['type'] == 1 || $val['type'] == 2) {
                if (!is_numeric($val['content'])) {
                    error('积分和优惠券奖品内容必须为整数');
                }
            }
        }
    }

    /**
     * @author hsz
     * @date 删除刮刮卡
     * @desc
     */
    public function delScratch(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            error('活动id不能为空');
        }
        $scratchService = new ActivityScratchService();
        $res = $scratchService->getRowById($id);
        if (!$res || $res['wid'] != session('wid')) {
            error('该活动不存在');
        }
        $scratchService->del($id);
        $prizeService = new ActivityScratchPrizeService();
        $prizeData = $prizeService->getByScratchId($id);
        foreach ($prizeData as $val) {
            $prizeService->del($val['id']);
        }
        success();
    }

    /**
     * 刮刮卡中奖统计
     * @param $scratch_id 刮刮卡活动id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 何书哲 2018年8月24日
     * @update 何书哲 2019年06月28日 导出报错处理
     */
    public function scratchCount($scratch_id)
    {
        $data = (new ActivityScratchLogService())->getByScratchId($scratch_id);
        // update 何书哲 2019年06月28日 导出报错处理
        if (empty(app('request')->input('is_export'))) {
            return view('merchants.marketing.scratch.scratchCount', array(
                'title' => '列表统计',
                'leftNav' => $this->leftNav,
                'slidebar' => 'index',
                'data' => $data,
            ));
        }
    }

    /**
     * 营销中心--订单核销页面
     * @author wuxiaoping <2018.06.11>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function zitiOrderHexiao(Request $request)
    {
        $code = $request->input('code') ?? '';
        $detail = [];
        if ($code) {
            $detail = OrderService::init('wid', session('wid'))->model->where('hexiao_code', $code)->get()->load('orderDetail')->load('orderLog')->toArray();
            $detail = current($detail);
            if ($detail) {
                $oid = $detail['id'];
                //退款信息
                $refundService = new OrderRefundService();
                foreach ($detail['orderDetail'] as &$value) {
                    $refund = $refundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $value['product_id'], 'prop_id' => $value['product_prop_id']])->getInfo();
                    $value['status_string'] = '';
                    if ($refund) {
                        $value['status_string'] = $refundService->getStatusString($refund['status']);
                    }
                }
                $where['oid'] = $detail['id'];
                $where['wid'] = session('wid');
                $where['mid'] = $detail['mid'];
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
                    $detail['ziti'] = $zitiData;
                }
            }
        }
        //dd($detail);
        return view('merchants.marketing.orderHexiao', [
            'title' => '订单核销页面',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'detail' => $detail
        ]);
    }

    /**
     * 获取自定义底部导航
     * @author hsz 2018/6/14
     * @param Request $request
     */
    public function getCustomFooterBarList(Request $request)
    {
        if (!($wid = session('wid'))) {
            error('没有访问权限');
        }
        $where['wid'] = $wid;
        $list = (new WXXCXCustomFooterBarService())->getAllList($where, '', false);
        foreach ($list as $key => $val) {
            $list[$key]['text'] = $val['name'];
            $list[$key]['iconPath'] = $val['icon_path'];
            $list[$key]['selectedIconPath'] = $val['selected_path'];
            unset($list[$key]['name'], $list[$key]['icon_path'], $list[$key]['selected_path']);
        }
        success('', '', ['list' => $list]);
    }

    /**
     * 添加自定义底部导航
     * @author hsz 2018/6/14
     * @param Request $request
     */
    public function addCustomFooterBar(Request $request)
    {
        if (!($wid = session('wid'))) {
            error('没有访问权限');
        }
        $input = $request->input();
        $rule = [
            'text' => 'required',
            'iconPath' => 'required',
            'selectedIconPath' => 'required',
        ];
        $messages = [
            'text.required' => '名称不能为空',
            'iconPath.required' => '图片不能为空',
            'selectedIconPath.required' => '图片不能为空',
        ];
        $validator = Validator::make($input, $rule, $messages);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        //添加自定义底部导航记录
        $data = [
            'wid' => $wid,
            'name' => $input['text'],
            'icon_path' => parse_url($input['iconPath'])['path'],
            'selected_path' => parse_url($input['selectedIconPath'])['path']
        ];
        if ((new WXXCXCustomFooterBarService())->add($data)) {
            success('操作成功');
        }
        error('操作失败');
    }

    /**
     * 删除自定义底部导航
     * @author hsz 2018/6/14
     * @param Request $request
     */
    public function delCustomFooterBar(Request $request)
    {
        if (!($wid = session('wid'))) {
            error('没有访问权限');
        }
        if (empty($id = $request->input('id'))) {
            error('参数不完整');
        }
        $customService = new WXXCXCustomFooterBarService();
        $data = $customService->getRowById($id);
        if (empty($data) || (isset($data['wid']) && $data['wid'] != $wid)) {
            error('没有权限删除别人的底部导航');
        }
        if ($customService->del($id)) {
            success('删除成功');
        }
        error('删除失败');
    }

    /**
     * 微页面刮刮卡列表
     * @param Request $request 请求参数
     * @return json
     * @create 何书哲 2018年6月28日 微页面刮刮卡列表
     */
    public function getUsefulScratch(Request $request)
    {
        $activityScratchService = new ActivityScratchService();
        //获取活动正在进行中的刮刮卡
        $now = date("Y-m-d H:i:s", time());
        $where = [
            'wid' => session('wid'),
            'wid' => session('wid'),
            'end_time' => ['>', $now],
        ];
        if ($request->input('keyword') && !empty($request->input('keyword'))) {
            $where['title'] = ['like', '%' . $request->input('keyword') . '%'];
        }
        $data = $activityScratchService->getlistPage($where, '', '', 6);
        success('操作成功', '', $data);
    }

    /**
     * 营销活动-调查列表
     * @param Request $request 参数类
     * @param int $type 活动类型 0:在线报名, 1:在线预约, 2:在线投票 许立 2018年08月07日
     * @param string $status 活动状态 future:未开始,on:进行中,end:已结束
     * @return view
     * @author 许立 2018年6月27日
     * @update 许立 2018年6月27日 新需求活动增加类型type 0:留言板, 1:预约, 2:投票
     * @update 许立 2018年08月08日 增加活动类型过滤,返回参与人数
     */
    public function researches(Request $request, $type, $status = '')
    {
        // 列表条件
        $where = ['wid' => session('wid')];
        // 活动类型
        $where['type'] = $type;
        $now = date('Y-m-d H:i:s');
        if ($status == 'future') {
            // 未开始
            $where['start_at'] = ['>', $now];
        } else if ($status == 'on') {
            // 进行中
            $where['start_at'] = ['<=', $now];
            $where['end_at'] = ['>', $now];
        } else if ($status == 'end') {
            // 已结束
            $where['end_at'] = ['<=', $now];
        }

        if (!empty($request->input('title'))) {
            $where['title'] = ['like', '%' . $request->input('title') . '%'];
        }

        // 列表数据
        $research_service = new ResearchService();
        list($list, $pageHtml) = $research_service->listWithPage($where);

        // 活动状态
        $recordService = new ResearchRecordService();
        foreach ($list['data'] as &$v) {
            if ($v['invalidate_at'] > '0000-00-00 00:00:00') {
                $v['status'] = '已失效';
            } elseif ($v['start_at'] > $now) {
                $v['status'] = '未开始';
            } elseif ($v['end_at'] <= $now) {
                $v['status'] = '已结束';
            } else {
                $v['status'] = '进行中';
            }

            // 许立 2018年6月27日 新需求活动增加类型
            $v['type_string'] = '普通留言';
            if ($v['type'] == 1) {
                $v['type_string'] = '预约留言';
            } elseif ($v['type'] == 2) {
                $v['type_string'] = '投票留言';
            }

            // 参与人数
            $v['partakeCount'] = $recordService->model
                ->select(DB::raw('count(DISTINCT mid) as partakeCount'))
                ->where('research_id', $v['id'])
                ->get()
                ->toArray()[0]['partakeCount'];
        }

        //判断当前店铺是否授权了小程序
        $lite_app_is_authorized = 0;
        $lite_app_config = (new WXXCXConfigService())->getRow(session('wid'));
        if (empty($lite_app_config['errCode']) && !empty($lite_app_config['data'])) {
            $lite_app_is_authorized = 1;
        }

        return view('merchants.marketing.research.index', array(
            'title' => '调查列表',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'list' => $list['data'],
            'pageHtml' => $pageHtml,
            'tabList' => $research_service->getStaticList(),
            'is_xcx' => $lite_app_is_authorized,
            'type' => $type
        ));
    }

    /**
     * 营销活动-调查新建
     * @param Request $request 参数类
     * @return json|view
     * @author 许立 2018年6月27日
     * @update 许立 2018年6月27日 新需求活动增加类型type 0:留言板, 1:预约, 2:投票
     * @update 许立 2018年7月9日  活动标题不能超过30字
     * @update 何书哲 2018年7月17日 获取调查模板内容(餐饮预约、课程报名、美业报名)及保存添加模板id字段
     * @update 许立 2018年08月03日  新建成功返回活动id
     * @update 许立 2018年08月07日 新建活动页面返回活动类型, 提交增加新字段
     * @update 许立 2018年08月16日 新建活动页面规则没有配置提示
     */
    public function researchAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            // 获取参数
            $input = $request->input();
            if (empty($input['title'])) {
                error('活动标题不能为空');
            } elseif (mb_strlen($input['title'], 'utf-8') > 30) {
                error('活动标题不能超过30字');
            }

            // 规则没有配置提示
            if (empty($input['rules'])) {
                error('请至少配置一个规则');
            }

            // 活动数据
            $data = [
                'wid' => session('wid'),
                'type' => $input['type'], // 许立 2018年6月27日 增加活动类型
                'title' => $input['title'],
                'start_at' => $input['start_at'],
                'end_at' => $input['end_at'],
                'times_type' => $input['times_type'],
                'rules' => $input['rules'],
                'template_id' => $input['template_id'] ?? 0, //何书哲 2018年7月17日 添加模板字段
                'background_color' => $input['background_color'] ?? '',
                'submit_button_title' => $input['submit_button_title'] ?? '',
                'submit_button_color' => $input['submit_button_color'] ?? '',
            ];

            $result = (new ResearchModule())->addResearch($data);
            if ($result) {
                success('新建调查活动成功', '', $result);
            } else {
                error('新建调查活动失败');
            }

        }
        //何书哲 2018年7月17日 获取调查模板列表(餐饮预约、课程报名、美业报名)
        $template_id = $request->input('template_id');
        $templateData = (new ResearchTemplateService())->getResearchTemplateById($template_id);
        $templateData['template_data'] = str_replace('"true"', 1, $templateData['template_data']);
        $templateData['template_data'] = str_replace('"false"', 0, $templateData['template_data']);
        return view('merchants.marketing.research.add', array(
            'title' => '添加调查活动',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'template' => json_encode($templateData),
            'type' => $request->input('type') ?? 0
        ));
    }

    /**
     * 营销活动-调查编辑
     * @param Request $request 参数类
     * @param int $id 活动id
     * @return json|view
     * @author 许立 2018年7月5日
     * @update 许立 2018年08月07日 提交增加新字段
     */
    public function researchEdit(Request $request, $id)
    {
        // 获取参数
        $input = $request->input();
        if (empty($id)) {
            error('活动ID不能为空');
        }
        $research_module = new ResearchModule();
        if ($request->isMethod('post')) {
            $data = [
                'id' => $id,
                'wid' => session('wid'),
                'title' => $input['title'],
                'start_at' => $input['start_at'],
                'end_at' => $input['end_at'],
                'times_type' => $input['times_type'],
                'rules' => $input['rules'],
                'background_color' => $input['background_color'] ?? '',
                'submit_button_title' => $input['submit_button_title'] ?? '',
                'submit_button_color' => $input['submit_button_color'] ?? '',
            ];
            if ($research_module->editResearch($data)) {
                success('编辑调查活动成功');
            } else {
                error('编辑调查活动出错');
            }
        } else {
            // 获取调查活动详情

            return view('merchants.marketing.research.edit', array(
                'title' => '编辑调查活动',
                'leftNav' => $this->leftNav,
                'slidebar' => 'index',
                'data' => $research_module->getResearch($id)
            ));
        }
    }

    /**
     * 营销活动-调查删除
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年7月5日
     */
    public function researchDelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $ids = $request->input('ids');
            if (empty($ids) || !is_array($ids)) {
                error('参数不正确');
            }

            // 执行删除
            $research_module = new ResearchModule();
            foreach ($ids as $id) {
                $res = $research_module->deleteResearch($id);
                !$res && error('删除出错');
            }

            success('删除成功');
        } else {
            error('路由错误');
        }
    }

    /**
     * 营销活动-调查使失效
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年7月5日
     */
    public function researchInvalidate(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            if (empty($id)) {
                error('参数不完整');
            }

            // 执行使失效
            if ((new ResearchService())->invalidateResearch($id)) {
                success('使失效成功');
            } else {
                error('使失效失败');
            }
        } else {
            error('路由错误');
        }
    }

    /**
     * 营销活动-调查活动-参与人列表
     * @param Request $request 参数类
     * @param int $id 活动id
     * @return view
     * @author 许立 2018年6月27日
     * @update 许立 2018年6月27日 返回活动类型 & 增加用户名搜索功能
     */
    public function researchMembers(Request $request, $id)
    {
        if (empty($id)) {
            error('活动ID不能为空');
        }
        // 许立 2018年6月27日 获取活动
        $research = (new ResearchService())->getDetail($id);
        if (empty($research)) {
            error('活动不存在');
        }
        $list = (new ResearchModule())->getMembers($id, $request->input('name'));

        return view('merchants.marketing.research.members', array(
            'title' => '调查参与列表',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'list' => $list['data'],
            'pageHtml' => $list['pageHtml'],
            'id' => $id,
            'type' => $research['type'] // 许立 2018年6月27日 返回活动类型
        ));
    }

    /**
     * 营销活动-调查活动
     * @param $id int 活动id
     * @param int $mid 参与人id
     * @param int $times 第几次参与
     * @return json
     * @author 许立 2018年7月5日
     */
    public function researchRecords($id, $mid, $times)
    {
        if (empty($id) || empty($mid) || empty($times)) {
            error('参数不完整');
        }
        success('', '', ['list' => (new ResearchModule())->researchRecords($id, $mid, $times)]);
    }

    /**
     * 营销活动-调查活动-选项类型活动结果
     * @param $id int 活动ID
     * @return json
     * @author 许立 2018年6月27日
     */
    public function researchResult($id)
    {
        // 判断活动合法性
        if (empty($id)) {
            error('活动ID不能为空');
        }

        // 获取投票预约等活动的结果
        success('', '', (new ResearchModule())->researchRecords($id));
    }

    /**
     * 营销活动-调查活动-导出参与人或投票类型活动的投票结果excel
     * @param Request $request 参数类
     * @param int $id 活动id
     * @return excel
     * @author 许立 2018年7月3日
     */
    public function researchExport(Request $request, $id)
    {
        // 判断参数
        if (empty($id)) {
            error('活动ID不能为空');
        }
        $input = $request->input();
        if (empty($input['type'])) {
            error('导出类型不能为空');
        }

        // 获取活动
        $research = (new ResearchService())->getDetail($id);
        if (empty($research)) {
            error('活动不存在');
        }

        // 获取数据
        $final_data = [];
        $title = '';
        $width_list = [];
        $research_module = new ResearchModule();
        if ($input['type'] == 'member') {
            $list = $research_module->researchRecords($id, 0, 0, $input['name']);
            // 转化成数组 处理提交内容
            $final_data = $research_module->dealWithExportData($list['records']);
            $title = $research['title'] . '-参与记录';
            $width_list = [50, 100, 100, 50, 50];
        } elseif ($input['type'] == 'result') {
            $list = $research_module->researchRecords($id);
            $final_data = $research_module->dealWithExportData($list['vote_result'], 'vote_result');
            $title = $research['title'] . '-投票结果';
            $width_list = [50, 20, 50, 20, 20];
        }

        // 导出文件
        (new ExportModule())->derive($final_data, $title, 'xlsx', $width_list);
    }

    /**
     * 通过主键获取调查活动详情
     * @param Request $request 请求参数
     * @param $id 调查活动主键id
     * @return view
     * @author 何书哲 2018年7月17日
     */
    public function getResearchById(Request $request, $id)
    {
        if (empty($id)) {
            error('活动ID不能为空');
        }
        $researchData = (new ResearchModule())->getResearch($id);
        return view('merchants.marketing.research.edit', array(
            'title' => '预览调查活动',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'data' => $researchData
        ));
    }

    /**
     * 获取调查模板列表(餐饮预约、课程报名、美业报名)
     * @param Request $request 请求参数
     * @return json
     * @author 何书哲 2018年7月17日
     * @update 许立 2018年08月07日 返回活动类型
     */
    public function getResearchTemplateList(Request $request)
    {
        //何书哲 2018年7月17日 获取调查模板列表(餐饮预约、课程报名、美业报名)
        $templateList = (new ResearchTemplateService())->listWithoutPage([], 'type', 'asc');
        foreach ($templateList as &$template) {
            $template['activity_type'] = 99;
            if ($template['template_data']) {
                $data = json_decode($template['template_data'], true);
                $template['activity_type'] = $data['type'];
            }

            $template['url'] = '/merchants/marketing/researchAdd?template_id=' . $template['id'];
        }
        success('', '', $templateList);
    }


    /**
     * 营销活动-调查预览效果
     * @param int $id 活动id
     * @return view
     * @author 许立 2018年08月03日
     */
    public function researchPreview($id)
    {
        // 获取参数
        if (empty($id)) {
            error('活动ID不能为空');
        }
        // 获取调查活动详情
        return view('merchants.marketing.research.preview', array(
            'title' => '调查预览',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'data' => (new ResearchModule())->getResearch($id)
        ));
    }

    /**
     * 生成小程序在线报名活动二维码
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年08月09日
     */
    public function researchXcxQrCode($id)
    {
        // 跳转到店铺主页
        success('', '', (new CommonModule())->qrCode(session('wid'), 'pages/main/pages/research/research?id=' . $id, 1));
    }

    /**
     * 下载小程序在线报名活动二维码
     * @param int $id 活动id
     * @return file
     * @author 许立 2018年08月09日
     */
    public function researchXcxQrCodeDownload($id)
    {
        // 跳转到店铺主页
        (new CommonModule())->qrCodeDownload(session('wid'), 'pages/main/pages/research/research?id=' . $id, 1);
    }

    /**
     * 生成微商城在线报名活动二维码
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年08月23日
     */
    public function researchQrCode($id)
    {
        // 跳转到店铺主页
        success('', '', (new CommonModule())->qrCode(session('wid'), config('app.url') . 'shop/activity/researchDetail/' . session('wid') . '/' . $id));
    }

    /**
     * 下载微商城在线报名活动二维码
     * @param int $id 活动id
     * @return file
     * @author 许立 2018年08月23日
     */
    public function researchQrCodeDownload($id)
    {
        // 跳转到店铺主页
        return (new CommonModule())->qrCodeDownload(session('wid'), 'shop/activity/researchDetail/' . session('wid') . '/' . $id);
    }

    /**
     *  根据商品分组id获取商品信息
     * @author 张永辉  2018年8月6日，根据商品获取
     */
    public function getProductByGroupId(Request $request)
    {
        $gids = $request->input('ids');
        if (!$gids) {
            error('id不能为空');
        }
        $res = ProductService::getProductByGroupId($gids, session('wid'));
        success('操作成功', '', $res);
    }

    /**
     * 获取商品分组
     * @author 张永辉 2018年8月6日
     */
    public function getProductGroups()
    {
        $wid = session('wid');
        $groups = (new ProductGroupService())->listWithPage(['wid' => $wid]);
        success('操作成功', '', $groups);
    }

    /**
     * 添加保存满减活动
     * @param Request $request
     * @author 张永辉
     */
    public function edit(Request $request, DiscountService $discountService, DiscountModule $discountModule)
    {
        $input = $request->input();
        // show_debug($input);
        if ($request->isMethod('post')) {
            $rule = Array(
                'title' => 'required',
                'start_time' => 'required',
                'content' => 'required',
                'type' => 'required',
                'use_type' => 'required',

            );
            $message = Array(
                'title.required' => '活动名称不能为空',
                'start_time.required' => '活动开始时间不能为空',
                'content.required' => '活动内容不能为空',
                'type.required' => '活动类型不能为空',
                'use_type.required' => '使用类型不能为空',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            if ($input['use_type'] == '2' && empty($input['use_content'])) {
                error('请选择指定商品');
            }
            $res = $discountModule->save($input, session('wid'));
            if ($res['errCode'] == '0') {
                success();
            } else {
                error($res['errMsg'], '', $res['data'] ?? []);
            }
        }
        //添加编辑显示
        $discount = [];
        if (!empty($input['id'])) {
            $discount = $discountService->getRowById($input['id']);
            if (!$discount) {
                error('满减活动不存在');
            }
            $discount['use_content'] = explode(',', $discount['use_content']);
            $product = ProductService::getListById(array_slice($discount['use_content'], 0, 3));
            $discount['product'] = array_map(function ($val) {
                return [
                    'id' => $val['id'],
                    'title' => $val['title'],
                    'price' => $val['price'],
                    'img' => $val['img'],
                ];
            }, $product);

            foreach ($discount['use_content'] as &$val) {
                $val = intval($val);
            }

            // $discount['use_content'] = implode(',',$discount['use_content']);
            if ($discount['end_time'] == '2038-01-01 00:00:00') {
                $discount['end_time'] = '';
            }
        }

        return view('merchants.marketing.edit', array(
            'title' => '编辑满减活动',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'discount' => $discount,
        ));

    }


    /**
     * 获取更多
     * @param $id
     * @author 张永辉 2018年8月7日
     */
    public function getMore(Request $request, DiscountService $discountService)
    {
        $ids = $request->input('ids');
        if (!$ids) {
            error('id不能为空');
        }
        $where = [
            'id' => ['in', $ids],
        ];
        $res = ProductService::listWithPage($where);
        success('操作成功', '', $res);
    }


    /**
     * 满减活动列表
     * @author 张永辉 2018年8月7日
     */
    public function discountList(Request $request, DiscountService $discountService)
    {
        $where = [
            'wid' => session('wid'),
        ];
        $status = $request->input('status', '');

        if (!empty($status)) {
            $nowTime = date('Y-m-d H:i:s', time());
            switch ($status) {
                case '1' :
                    $where['start_time'] = ['>', $nowTime];
                    break;
                case '2' :
                    $where['start_time'] = ['<', $nowTime];
                    $where['end_time'] = ['>', $nowTime];
                    break;
                case 3 :
                    $where['end_time'] = ['<', $nowTime];
                    break;
            }
        }

        $res = $discountService->getlistPage($where);
        $nowTime = time();
        foreach ($res[0]['data'] as &$val) {
            if (strtotime($val['start_time']) >= $nowTime) {
                $val['status'] = 1;
            } elseif (strtotime($val['start_time']) < $nowTime && strtotime($val['end_time']) > $nowTime) {
                $val['status'] = 2;
            } elseif (strtotime($val['end_time']) < $nowTime) {
                $val['status'] = 3;
            }
            if ($val['end_time'] == '2038-01-01 00:00:00') {
                $val['end_time'] = '长久';
            }
        }

        return view('merchants.marketing.discountList', array(
            'title' => '满减活动列表',
            'slidebar' => 'index',
            'leftNav' => $this->leftNav,
            'data' => $res,
        ));

    }


    /**
     * 获取统计接口
     * @param $id
     * @author 张永辉 2018年8月16日
     */
    public function getDiscountInfo($id)
    {
        $dcUrl = config('app.dc_url');
        $route = '/api/v1/discount/discountOrderInfo?discount_id=' . $id;
        $res = jsonCurl($dcUrl . $route);
        if ($res['err_code'] == 0) {
            success('操作成功', '', $res['data']);
        } else {
            error();
        }
    }

    /**
     * 使失效
     * @param $id
     * @param DiscountService $discountService
     * @author 张永辉 2018年8月16日
     */
    public function invalidate($id, DiscountService $discountService)
    {
        $res = $discountService->getRowById($id);
        if (!$res || $res['wid'] != session('wid')) {
            error('该活动存在问题');
        }
        $nowtime = date('Y-m-d H:i:s', time());
        $res = $discountService->update($id, ['end_time' => $nowtime]);
        if ($res) {
            success();
        } else {
            error();
        }
    }


    /**
     *  删除满减活动
     * @param $id
     * @param DiscountService $discountService
     * @author 张永辉
     */
    public function delDiscount($id, DiscountService $discountService)
    {
        $res = $discountService->getRowById($id);
        if (!$res || $res['wid'] != session('wid')) {
            error('该活动存在问题');
        }
        $nowtime = date('Y-m-d H:i:s', time());
        $res = $discountService->del($id);
        if ($res) {
            success();
        } else {
            error();
        }
    }

}