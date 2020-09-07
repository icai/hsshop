<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/4/11
 * Time: 10:17
 */

namespace App\Http\Controllers\home;


use App\Http\Controllers\Controller;
use App\Module\ProductModule;
use App\S\Foundation\VerifyCodeService;
use App\S\Market\ResearchRecordService;
use App\S\Market\ResearchService;
use App\S\Staff\LiteappHistoryService;
use App\S\Staff\LiteappService;
use App\S\Wechat\WeChatShopConfService;
use App\Services\ReserveService;
use App\Services\Staff\InfoRecommendService;
use App\Services\Staff\InformationService;
use App\S\Staff\InformationTypeService;
use App\Services\WeixinBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Validator;
use WeixinService;
use App\S\Solve\ProblemSolvingService;
use App\Model\Information;
use App\S\Staff\CaseService;
use App\S\Staff\IndustryService;
use App\S\Staff\CaseCommentService;
use Captcha;
use App\S\Staff\BannerService;
use App\S\Staff\LinkService;
use App\S\Staff\AdService;
use App\Services\Lib\JSSDK;
use QrCode;
use App\S\File\FileInfoService;
use App\S\Staff\InformationService as InforService;
use QrCodeService;
use Upyun\Config;
use Upyun\Signature;
use Upyun\Util;

class IndexController extends Controller
{

    public $marketing = [
        'appRecommen' => [
            ['xcx-bg' => 'mobile/images/shareEvent-bg.png', 'name' => '享立减', 'url' => '/home/index/appRecommen/1', 'desc' => '用户点击分享链接可减钱的新玩法', 'content' => '享立减是一种一键快捷分享，让好友帮你减价的常用营销推广活动', 'xcxImgs' => 'mobile/images/shareEvent-detail.jpg'],
            ['xcx-bg' => 'mobile/images/xcx-bg.png', 'name' => '小程序', 'url' => '/home/index/manageChannel/1', 'desc' => '一键生成微信小程序', 'content' => '全新的用户体验 效果堪比原生APP', 'xcxImgs' => 'mobile/images/xcx-detail.jpg'],
            ['xcx-bg' => 'mobile/images/collect-bg.png', 'name' => '集赞', 'url' => '/home/index/salesTools/2', 'desc' => '用户点击分享链接可减钱的新玩法', 'content' => '集赞是一种一键快捷分享，让好友帮你点赞减价的营销推广活动', 'xcxImgs' => 'mobile/images/collect-detail.jpg'],
            ['xcx-bg' => 'mobile/images/point-bg.png', 'name' => '积分', 'url' => '/home/index/memberTicket/2', 'desc' => '向客户赠送店铺积分可减金额', 'content' => '积分管理是帮助您增加用户用于激励和回馈用户在平台的消费行为和活动行为，提升用户对平台的黏度和重复下单率。', 'xcxImgs' => 'mobile/images/point-detail.jpg'],
            ['xcx-bg' => 'mobile/images/group-bg.png', 'name' => '多人拼团', 'url' => '/home/index/salesTools/3', 'desc' => '引导客户邀请朋友一起拼团购买', 'content' => '多人拼团是一种基于社交邀请好友一起拼团购买的营销推广活动', 'xcxIms' => 'mobile/images/group-detail.jpg'],
        ],
        'manageChannel' => [
            ['xcx-bg' => 'mobile/images/xcx-bg.png', 'name' => '小程序', 'url' => '/home/index/manageChannel/1', 'desc' => '一键生成微信小程序', 'content' => '全新的用户体验 效果堪比原生APP', 'xcxImgs' => 'mobile/images/xcx-detail.jpg'],
            ['xcx-bg' => 'mobile/images/wx-bg.png', 'name' => '公众号', 'url' => '/home/index/manageChannel/2', 'desc' => '链接公众号,玩转微信生态圈', 'content' => '支持商家微信公众号与小程序和微商进行绑定，拓展线上流量渠道', 'xcxImgs' => 'mobile/images/wx-detail.jpg'],
        ],
        'salesDiscount' => [
            ['xcx-bg' => 'mobile/images/coupon-bg.png', 'name' => '优惠券', 'url' => '/home/index/salesDiscount/1', 'desc' => '向客户发放店铺优惠券', 'content' => '优惠券是商家吸引和回馈客户一种很有效的营销推广工具，设置便捷，操作简单，效果显著', 'xcxImgs' => 'mobile/images/coupon-detail.jpg'],
            ['xcx-bg' => 'mobile/images/seckill-bg.png', 'name' => '秒杀', 'url' => '/home/index/salesDiscount/2', 'desc' => '快速抢购引导客户更多消费', 'content' => '秒杀是商家较常使用的一种快速汇集流量、促销购买的营销推广活动', 'xcxImgs' => 'mobile/images/seckill-detail.jpg'],
        ],
        'salesTools' => [
            ['xcx-bg' => 'mobile/images/shareEvent-bg.png', 'name' => '享立减', 'url' => '/home/index/appRecommen/1', 'desc' => '用户点击分享链接可减钱的新玩法', 'content' => '享立减是一种一键快捷分享，让好友帮你减价的常用营销推广活动', 'xcxImgs' => 'mobile/images/shareEvent-detail.jpg'],
            ['xcx-bg' => 'mobile/images/collect-bg.png', 'name' => '集赞', 'url' => '/home/index/salesTools/2', 'desc' => '用户点击分享链接可减钱的新玩法', 'content' => '集赞是一种一键快捷分享，让好友帮你点赞减价的营销推广活动', 'xcxImgs' => 'mobile/images/collect-detail.jpg'],
            ['xcx-bg' => 'mobile/images/group-bg.png', 'name' => '多人拼团', 'url' => '/home/index/salesTools/3', 'desc' => '引导客户邀请朋友一起拼团购买', 'content' => '多人拼团是一种基于社交邀请好友一起拼团购买的营销推广活动', 'xcxImgs' => 'mobile/images/group-detail.jpg'],
            ['xcx-bg' => 'mobile/images/plate-bg.png', 'name' => '幸运大转盘', 'url' => '/home/index/salesTools/4', 'desc' => '常见的转盘式抽奖玩法', 'content' => '幸运大转盘是一种常见的幸运抽奖营销推广工具', 'xcxImgs' => 'mobile/images/plate-detail.jpg'],
            ['xcx-bg' => 'mobile/images/community-bg.png', 'name' => '微社区', 'url' => '/home/index/salesTools/5', 'desc' => '打造人气移动社区，增加客户流量', 'content' => '微社区是基于商家微信公众账号的社交平台，支持图片、视频、文字、表情等方式。借助微社区，商家可以便捷的打造和粉丝的互动平台。在互动中了解粉丝心声，提高粉丝参与度，共同创造内容并发生传播，让交流无限', 'xcxImgs' => 'mobile/images/community-detail.jpg'],
            ['xcx-bg' => 'mobile/images/egg-bg.png', 'name' => '砸金蛋', 'url' => '/home/index/salesTools/6', 'desc' => '好蛋砸出来，礼品不间断', 'content' => '砸金蛋活动规则简单，参与活动粉丝只需在活动界面砸开金蛋即有机会获得奖品，商家可以设置活动界面引导banner及跳转链接，奖品中奖概率也可以分别设置，奖品可设置优惠券和积分。', 'xcxImgs' => 'mobile/images/egg_detail.jpg'],
            ['xcx-bg' => 'mobile/images/sign-bg.png', 'name' => '签到', 'url' => '/home/index/salesTools/7', 'desc' => '每日签到领取积分或奖励', 'content' => '每日签到能获得更多的积分', 'xcxImgs' => 'mobile/images/sign-detail.jpg'],
        ],
        'memberTicket' => [
            ['xcx-bg' => 'mobile/images/card-bg.png', 'name' => '会员卡', 'url' => '/home/index/memberTicket/detail/1', 'desc' => '设置并给客户发放会员卡', 'content' => '通过在微信内植入会员卡，基于全国6亿微信用户，帮助企业建立集品牌推广、会员管理、营销活动、统计报表于一体的微信会员管理平台。清晰记录企业用户的消费行为并进行数据分析；还可根据用户特征进行精细分类，从而实现各种模式的精准营销。', 'xcxImgs' => 'mobile/images/card-detail.jpg'],
            ['xcx-bg' => 'mobile/images/point-bg.png', 'name' => '积分', 'url' => '/home/index/memberTicket/2', 'desc' => '向客户赠送店铺积分可减金额', 'content' => '积分管理是帮助您增加用户用于激励和回馈用户在平台的消费行为和活动行为，提升用户对平台的黏度和重复下单率。', 'xcxImgs' => 'mobile/images/point-detail.jpg'],
            ['xcx-bg' => 'mobile/images/recharge-bg.png', 'name' => '充值', 'url' => '/home/index/memberTicket/detail/3', 'desc' => '开通会员充值功能', 'content' => '会员储值，是可帮助商家提升客户忠诚度、增加会员粘性，商家可根据需要创建储值规则，会员储值后可在消费时使用余额进行支付。', 'xcxImgs' => 'mobile/images/recharge-detail.jpg'],
        ],
        'extension' => [
            ['xcx-bg' => 'mobile/images/news_remind-bg.png', 'name' => '消息提醒', 'url' => '/home/index/extension/1', 'desc' => '向客户发布微信消息提醒', 'content' => '消息提醒功能可以通过微信公众号(请确保微信公众号已申请开通模板消息)，给买家或商家推送交易和物流相关的提醒消息，包括订单催付、发货、签收、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。', 'xcxImgs' => 'mobile/images/news_remind-detail.jpg'],
            ['xcx-bg' => 'mobile/images/news_temp-bg.png', 'name' => '消息模板', 'url' => '/home/index/extension/2', 'desc' => '设置消息提醒模板', 'content' => '消息模板功能可以通过微信公众平台设置好固定的消息模板，在后台一键发送到公众号下的所有粉丝，高效及时的消息提醒。', 'xcxImgs' => 'mobile/images/news_temp-detail.jpg'],
            ['xcx-bg' => 'mobile/images/vote-bg.png', 'name' => '投票', 'url' => '/home/index/extension/3', 'desc' => '向客户发起投票活动', 'content' => '向客户发起投票活动，收集用户需求，更精准商品定位，打造商品爆款', 'xcxImgs' => 'mobile/images/vote-detail.jpg'],
        ],

    ];

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc首页
     */
    public function index(Request $request, BannerService $bannerService, InformationTypeService $informationTypeService, CaseService $caseService, InfoRecommendService $infoRecommendService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.index' : 'home.index.index';
        /**首页显示最新的三条资讯内容**/
        $data = $informationTypeService->getNewsList('', 0, 2, 'list', 3);

        $newestList = [];
        if (isset($data['newsList']['data'])) {
            $newestList = $data['newsList']['data'];
        }

        //获取案例列表 根据sort字段从大到小排
        $typeData = ['card' => '会搜云新零售系统', 'app' => 'APP定制', 'xcx' => '微信小程序', 'shop' => '微信商城'];//定义案例分类数组分别是：app开发，小程序开发，微商城开发
        $caseTypeList = [];
        foreach ($typeData as $key => $value) {
            $whereData['type'] = $value;
            list($caseList, $page) = $caseService->getAllList($whereData, 'sort', 6);
            if ($caseList['data']) {
                $caseTypeList[$key] = $caseList['data'];
            }
        }
        /***首页固定推存三个二级分类并显示其下的两条最新资讯***/
        $types = ['industry' => '行业资讯', 'online' => '学习答疑'];
        $typeNewsList['newest'] = array_slice($newestList, 0, 2);
        foreach ($types as $key => $type) {
            $typeNewsList[$key] = $informationTypeService->getListFromSecTypeName($type);
        }
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        /*获取banner图*/
        list($bannerList) = $bannerService->getAllList(['position' => '会搜云首页']);
        return view($view_html, array(
            'title' => '会搜云-会搜云新零售系统|人工智能名片制作|电子名片在线制作|微信获客神器|微信商城分销系统',
            'slidebar' => 'home',
            //'informationList' => $informationList,
            //'bannerList'      => $bannerList,
            'newestList' => $newestList,
            'caseTypeList' => $caseTypeList,
            'typenewsList' => $typeNewsList,
            'publicData' => $publicData
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170411
     * @desc 预约订购
     * @param Request $request
     * @update 许立 2018年10月09日 官网小程序微商城报名数据保存到指定营销活动中
     * @update 许立 20190426 type参数加上整数类型验证 防止js脚本注入
     */
    public function reserve(Request $request, ReserveService $reserveService, VerifyCodeService $verifyCodeService)
    {
        $input = $request->input();
        $input['type'] = isset($input['type']) ? (int)$input['type'] : 1;
        $input['type'] = $input['type'] ?: 1;
        $paramKeys = array_keys($input);
        //判断链接来源
        $link_source = '站内链接';
        if (in_array('source_type_888_html', $paramKeys)) {
            $link_source = '百度';
        } else if (in_array('source_type_666_html', $paramKeys)) {
            $link_source = '360';
        }
        Cookie::queue('source_user', $link_source, 120);  //把参数保存到cookie,设置2小时过期

        if ($request->isMethod('post')) {
            $rules = array(
                'phone' => 'required|regex:mobile',
                'name' => 'required',
            );
            $messages = array(
                'phone.required' => '请输入手机号码',
                'phone.regex' => '手机号码格式不正确',
                'name.required' => '请填写你的称呼',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $reserveData = [
                'name' => $input['name'],
                'phone' => $input['phone'],
                'industry' => $input['industry'],
                'type' => $input['type'],
            ];

            //判断访问链接来源
            if ($request->cookie('source_user')) {
                $param = $request->cookie('source_user');
                $reserveData['link_source'] = $param;
            } else {
                $reserveData['link_source'] = '站内链接';
            }

            //客户端访问来源（pc、移动端）
            $reserveData['source'] = source();
            if (config('app.env') == 'prod') {
                try {
                    $smsData = [$input['name'], $input['phone'], $input['industry']];
                    $datas = [date('Y-m-d H:i:s')];
                    //获取后台设置中的默认的客服
                    $tel = \CusSerManageService::getPhone();
                    $verifyCodeService->sendCode($tel, $smsData, 6);
                } catch (Exception $e) {
                }
            }

            $insert = $reserveService->init()->add($reserveData, false);

            // 插入官网在线报名活动表
            $data = [];
            if ($input['type'] == 3) {
                // 小程序报名
                $data = [
                    'id' => config('app.RESEARCH_ID_XCX'),
                    'mid' => config('app.RESEARCH_MID'),
                    'data' => [
                        config('app.RESEARCH_XCX_RULE_ID_1') => ['type' => 'text', 'val' => $input['name']],
                        config('app.RESEARCH_XCX_RULE_ID_2') => ['type' => 'text', 'val' => $input['phone']],
                        config('app.RESEARCH_XCX_RULE_ID_3') => ['type' => 'text', 'val' => $input['industry']],
                    ]
                ];
            } elseif ($input['type'] == 5) {
                // 微商城
                $data = [
                    'id' => config('app.RESEARCH_ID_SHOP'),
                    'mid' => config('app.RESEARCH_MID'),
                    'data' => [
                        config('app.RESEARCH_SHOP_RULE_ID_1') => ['type' => 'text', 'val' => $input['name']],
                        config('app.RESEARCH_SHOP_RULE_ID_2') => ['type' => 'text', 'val' => $input['phone']],
                        config('app.RESEARCH_SHOP_RULE_ID_3') => ['type' => 'text', 'val' => $input['industry']],
                    ]
                ];
            }
            // 检查活动是否存在
            $researchRecordService = new ResearchRecordService();
            $research = (new ResearchService())->getDetail($data['id'] ?? 0);
            $research && $researchRecordService->add($data, $researchRecordService->getCount($data['id'] ?? 0, config('app.RESEARCH_MID')) + 1);

            if ($insert) {
                return mysuccess('预约成功');
            } else {
                return myerror('预约失败');
            }

        }
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.serviceSec' : 'home.index.reserve';

        return view($view_html, array(
            'title' => '预约订购',
            'type' => $input['type'],
            'publicData' => $publicData,
            'slidebar' => 'service'
        ));

    }

    /**
     * [我要服务/分销系统]
     * @return [type] [description]
     */
    public function serviceFir()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.mobile.serviceFir', [
            'title' => '我要服务',
            'slidebar' => 'home',
            'publicData' => $publicData
        ]);
    }

    /**
     * [我要服务/APP定制]
     * @return [type] [description]
     */
    public function serviceSec()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.mobile.serviceSec', [
            'title' => '我要服务',
            'slidebar' => 'home',
            'publicData' => $publicData
        ]);
    }

    /**
     * [我要服务/微信小程序]
     * @return [type] [description]
     */
    public function serviceThi(Request $request)
    {
        $input = $request->input();
        $paramKeys = array_keys($input);
        //判断链接来源
        $link_source = '站内链接';
        if (in_array('source_type_888_html', $paramKeys)) {
            $link_source = '百度';
        } else if (in_array('source_type_666_html', $paramKeys)) {
            $link_source = '360';
        }
        Cookie::queue('source_user', $link_source, 120);  //把参数保存到cookie,设置2小时过期

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.mobile.serviceThi', [
            'title' => '我要服务',
            'slidebar' => 'home',
            'publicData' => $publicData
        ]);
    }

    /**
     * [我要服务/微营销总裁班]
     * @return [type] [description]
     */
    public function serviceFou()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.mobile.serviceFou', [
            'title' => '我要服务',
            'slidebar' => 'home',
            'publicData' => $publicData
        ]);
    }

    /**
     * [我要服务/微信商城]
     * @return [type] [description]
     */
    public function serviceFif()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.mobile.serviceFif', [
            'title' => '我要服务',
            'slidebar' => 'home',
            'publicData' => $publicData
        ]);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc微商城系统
     */
    public function microshop(Request $request, CaseService $caseService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.ecShop' : 'home.index.microshop';
        $where['type'] = '微信商城';
        list($caseList, $page) = $caseService->getAllList($where, 'sort', 8); //显示案例每页显示8条
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '微信商城哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技',
            'publicData' => $publicData,
            'slidebar' => 'service',
            'caseList' => $caseList
        ));
    }

    public function applet(Request $request, CaseService $caseService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.smallCode' : 'home.index.applet';
        $where['type'] = '微信小程序';
        list($caseList, $page) = $caseService->getAllList($where, 'sort', 8); //显示案例每页显示8条
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        //dd($caseList);
        return view($view_html, array(
            'title' => '微信小程序哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技',
            'publicData' => $publicData,
            'slidebar' => 'service',
            'caseList' => $caseList
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc 分销
     */
    public function distribution(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.distribution' : 'home.index.distribution';

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '分销系统哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技',
            'publicData' => $publicData,
            'slidebar' => 'service'
        ));
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170412
     * @desc 资讯信息
     * @param Request $request
     */
    public function information(Request $request, InformationTypeService $informationTypeService, InformationService $informationService, InfoRecommendService $infoRecommendService, $title = 0, $id = 0, $type = '')
    {

        $_REQUEST[$title] = $id;
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.cloudInstitute' : 'home.index.information';
        if ($is_mobile) {
            /*updateby wuxiaoping 2017.08.16*/
            $cateId = $id;
            $data = $informationTypeService->model->where(['parent_id' => 0])->get()->toArray();

            /*运营需求 移动端把帮助中心与资讯内容分开（把帮助内容独立出来）2017.08.16 wuxiaoping*/
            foreach ($data as $key => $item) {
                if ($item['name'] == '帮助中心') {
                    array_splice($data, $key, 1);
                }
            }

            if (empty($cateId)) {
                $cateId = $data[0]['id'];
            }
            $_REQUEST['oneCategory'] = $cateId;
            list($informationData) = $informationService->get();

            foreach ($informationData['data'] as &$val) {
                $val['content'] = $infoRecommendService->intercept(strip_tags($val['content']), 100);
            }
            return view($view_html, [
                'title' => '会搜云·云商学院',
                'slidebar' => 'home',
                'data' => $data,
                'informationData' => $informationData,
                'cateId' => $cateId,

            ]);
        }
        $data = $informationTypeService->model->get()->toArray();
        foreach ($data as $key => $item) {
            if ($item['name'] == '帮助中心') {
                array_splice($data, $key, 1);
            }
        }
        $typeData = [];
        $tag = '';

        foreach ($data as $val) {
            if (count(explode(',', $val['type_path'])) < 3) {
                $typeData[$val['parent_id']][] = $val;
                if ($request->input('secCategory') && $request->input('secCategory') == $val['id']) {
                    $tag = $val['parent_id'];
                }
            }

        }
        $cateId = $id;
        if (empty($cateId)) {
            $cateId = $data[0]['id'];
        }
        list($sceCateList) = $informationTypeService->get($cateId);
        if ($sceCateList['data']) {
            $_REQUEST['secCategory'] = $sceCateList['data'][0]['id'];
        } else {
            $_REQUEST['oneCategory'] = $cateId;
        }
        $informationData = $informationService->get(['status' => 1]);

        foreach ($informationData[0]['data'] as &$val) {
            $val['content'] = $infoRecommendService->intercept(strip_tags($val['content']), 100);
        }

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '会搜云资讯']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        $groups = [];
        if ($informationData[0]['data']) {
            /*左边分类是否选中*/
            $dataInfo = $informationTypeService->getRowById($informationData[0]['data'][0]['info_type']);
            $groups = explode(',', $dataInfo['type_path']);
        }


        return view($view_html, array(
            'title' => '会搜云学院',
            'slidebar' => 'home',
            'type' => $typeData,
            'information' => $informationData,
            'tag' => $tag,
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'groups' => $groups,
            'cateId' => $cateId,

        ));
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170412
     * @desc 资讯详情页面
     * @param Request $request
     * @param InformationService $informationService
     */
    public function detail(Request $request, InformationService $informationService, $id = 0, $type = 'news')
    {
        if ($type == 'help') {
            return redirect('/home/index/helpDetail/' . $id);

        } else if ($type == 'news') {
            return redirect('/home/index/newsDetail/' . $id . '/news');
        } else {
            error('该页面不存在');
        }

        if (empty($id)) {
            return myerror('资讯不存在或已被删除');
        }
        $_REQUEST['id'] = $id;
        list($inforData) = $informationService->get();
        if (!$inforData['data']) {
            return myerror('资讯不存在或已被删除');
        }
        /*addby wuxiaoping 2017.08.18 根据需求添加上一篇，下一篇*/
        $nextArr = $preArr = [];
        $informationModel = new Information();
        $data = $informationModel->context($inforData['data'][0]['id'], $inforData['data'][0]['info_type']);
        foreach ($data as $val) {
            if ($val['id'] > $inforData['data'][0]['id']) {
                $nextArr['id'] = $val['id'];
                $nextArr['title'] = $val['title'];
            } else {
                $preArr['id'] = $val['id'];
                $preArr['title'] = $val['title'];
            }
        }

        /*相关新闻*/
        $keywords = $inforData['data'][0]['keywords'];
        $releveNews = $informationModel->getNewsFromKeywords($keywords);

        /*左边的分类列表*/
        $informationTypeService = new InformationTypeService();
        $catAll = $informationTypeService->model->orderBy('sort', 'desc')->get()->toArray();
        foreach ($catAll as $key => $item) {
            if ($item['name'] == '帮助中心') {
                array_splice($catAll, $key, 1);
            }
        }
        $typeData = [];
        $tag = '';
        foreach ($catAll as $val) {
            if (count(explode(',', $val['type_path'])) < 3) {
                $typeData[$val['parent_id']][] = $val;
                if ($request->input('secCategory') && $request->input('secCategory') == $val['id']) {
                    $tag = $val['parent_id'];
                }
            }

        }
        /*左边分类是否选中*/
        $dataInfo = $informationTypeService->getRowById($inforData['data'][0]['info_type']);
        $groups = explode(',', $dataInfo['type_path']);
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.pageEnd' : 'home.index.detail';

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        /*广告*/
        $adService = new AdService();
        list($adList) = $adService->getAllList('sort');
        $adResult = [];
        foreach ($adList['data'] as $ad) {
            if ($ad['type'] == 0) {
                $adResult['common'][] = $ad;
            } else {  //精选广告只选一条（只显示一条信息）
                $adResult['very'] = $ad;
            }
        }

        /**帮助中心--二级分类**/
        $helpData = $helpChildData = [];
        if ($type == 'help') {
            $obj = $informationTypeService->model->wheres(['parent_id' => 0, 'name' => '帮助中心'])->first();
            if ($obj) {
                $helpData = $obj->toArray();
            }

            if ($helpData) {
                $helpChildData = $informationTypeService->model->wheres(['parent_id' => $helpData['id']])->orderBy('sort', 'desc')->get()->toArray();
                if ($helpChildData) {
                    foreach ($helpChildData as &$v) {
                        $v['erji'] = $informationTypeService->model->where('type_path', 'like', '%' . $v['type_path'] . ',%')->orderBy('sort', 'desc')->get()->toArray();
                    }
                }
            }
        }
        $inforData['data'][0]['content'] = ProductModule::addProductContentHost($inforData['data'][0]['content'] ?? '', '1');
        return view($view_html, array(
            'title' => '会搜云学院',
            'detail' => $inforData['data'][0],
            'type' => $type,
            'nextArr' => $nextArr,
            'preArr' => $preArr,
            'releveNews' => $releveNews,
            'typeData' => $typeData,
            'tag' => $tag,
            'groups' => $groups,
            'publicData' => $publicData,
            'adResult' => $adResult,
            'helpChildData' => $helpChildData
        ));
    }

    /**
     * 帮助中心详情
     * @author 吴晓平 <2018.07.10>
     * @param  Request $request [description]
     * @param  InformationTypeService $informationTypeService [description]
     * @param  InforService $inforService [description]
     * @param  integer $id [相关帮助资讯id]
     * @return [type]                                         [description]
     */
    public function helpDetail(Request $request, InformationTypeService $informationTypeService, InforService $inforService, $id = 0)
    {
        //移动端帮助详情收集用户对内容是否认同
        if ($request->isMethod('post')) {
            $input = $request->input();
            $id = $input['id'] ?? 0;
            $type = $input['type'] ?? 0;
            if (empty($id)) {
                error('内容不存在或已被删除');
            }
            $inforData = $inforService->getRowById($id);
            if (empty($inforData)) {
                error('内容不存在或已被删除');
            }
            $saveData = [];
            if ($type == 1) {
                $trueNums = $inforData['trueNums'] + 1;
                $saveData['trueNums'] = $trueNums;
            } else {
                $falseNums = $inforData['falseNums'] + 1;
                $saveData['falseNums'] = $falseNums;
            }
            $inforService->update($id, $saveData);

        }
        if (empty($id)) {
            return myerror('内容不存在或已被删除');
        }
        $inforData = $inforService->getRowById($id);
        if (empty($inforData)) {
            error('内容不存在或已被删除');
        }
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.helpDetail' : 'home.index.helpDetail';

        /*左边的分类列表*/
        $typeData = $informationTypeService->getNewsList('', 0, 1, 'detail');

        /*左边分类是否选中*/
        $upperTypData = $informationTypeService->getRowById($inforData['info_type']);
        if (empty($upperTypData)) {
            error('所属分类不存在或已被删除');
        }

        /**拓展阅读**/
        $whereData['info_type'] = [$inforData['info_type']];
        $whereData['remove'] = $inforData['id'];
        $otherData = $inforService->getAllWithPage($whereData);
        $otherHelps = $otherData[0]['data'];

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        $inforData['content'] = ProductModule::addProductContentHost($inforData['content'] ?? '', '1');
        return view($view_html, array(
            'title' => '会搜云学院',
            'slidebar' => 'helps',
            'publicData' => $publicData,
            'typeData' => $typeData,
            'inforData' => $inforData,
            'upperTypData' => $upperTypData,
            'otherHelps' => $otherHelps
        ));
    }

    /**
     * 案例详情
     * @param  Request $request [description]
     * @param  CaseService $caseService [description]
     * @param  CaseCommentService $caseCommentService [description]
     * @return [type]                                 [description]
     * @update 何书哲 2019年06月28日 案例参数处理，防止下面cookie里有不合法的字符
     */
    public function caseDetails(Request $request, CaseService $caseService, CaseCommentService $caseCommentService)
    {
        // update 何书哲 2019年06月28日 案例参数处理，防止下面cookie里有不合法的字符
        $id = intval($request->input('id') ?? '');
        if (empty($id)) {
            error('数据异常');
        }

        /*提交评论*/
        if ($request->isMethod('post')) {
            /* 验证验证码 */
            if (!Captcha::check($request->input('captcha'))) {
                error('验证码错误');
            }
            $saveData = [];
            $saveData['case_id'] = $request->input('caseId');
            $saveData['nickname'] = $request->input('nickname');
            $saveData['content'] = $request->input('content');
            if ($caseCommentService->add($saveData)) {
                success('', '', ['created' => date('Y-m-d H:s:i', time())]);
            }
            error();
        }

        /*详情数据*/
        $data = $caseService->getRowById($id);
        if (empty($data)) {
            error('该案例不存在或已被删除');
        }
        $showImgArr = [];
        if ($data['show_img']) {
            $showImgArr = explode(';', $data['show_img']);
        }
        //dd($showImgArr);
        $industryArr = [];
        if ($data['industry_ids']) {
            $industryArr = explode(',', $data['industry_ids']);
        }
        $industryService = new IndustryService();
        $industryList = $industryService->getAllList(false, $industryArr);
        $industryStr = '';
        foreach ($industryList as $items) {
            $industryStr .= $items['name'] . '/';
        }

        /*相关资讯新闻*/
        $information = new Information();
        $news = $information->getNewsFromKeywords($data['type']);

        /*相关评论*/
        list($commentData) = $caseCommentService->getAllList($id);
        $commentData['count'] = count($commentData['data']);

        /*设置访问量*/
        //获用访问用户ip
        $ip = getIP();
        $stevIP = str_replace('.', '_', $ip);
        if (!$request->cookie('user:' . $ip . 'news:' . $id) && !$request->cookie('user:' . $stevIP . 'news:' . $id)) {
            $caseService->statistics($ip, $id, 1);
            $num = (int)$data['browse_num'] + 1;
            $caseService->update($id, ['browse_num' => $num]);
        }

        /*广告*/
        $adService = new AdService();
        list($adList) = $adService->getAllList('sort');
        $adResult = [];
        foreach ($adList['data'] as $ad) {
            if ($ad['type'] == 0) {
                $adResult['common'][] = $ad;
            } else {  //精选广告只选一条（只显示一条信息）
                $adResult['very'] = $ad;
            }
        }
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.caseDetails' : 'home.index.caseDetails';

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, array(
            'title' => '案例详情',
            'data' => $data,
            'showImgArr' => $showImgArr,
            'commentData' => $commentData,
            'news' => $news,
            'industryStr' => substr($industryStr, 0, -1),
            'publicData' => $publicData,
            'adResult' => $adResult,
            'slidebar' => 'shop'
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc
     */
    public function customization(Request $request, CaseService $caseService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.appCustomize' : 'home.index.customization';
        $where['type'] = 'APP定制';
        list($caseList, $page) = $caseService->getAllList($where, 'sort', 8); //显示案例每页显示8条
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => 'App定制哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技',
            'publicData' => $publicData,
            'slidebar' => 'service',
            'caseList' => $caseList
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc
     */
    public function microMarketing(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.ceoClass' : 'home.index.microMarketing';

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '微营销总裁班哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技',
            'publicData' => $publicData,
            'slidebar' => 'service'
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170418
     * @desc 会搜简介
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function about(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.about' : 'home.index.about';

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '关于我们']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '会搜简介',
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'slidebar' => 'about'
        ));
    }

    /**
     * 发展历程
     * @author 吴晓平 <2018年07月05日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function growth(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.growth' : 'home.index.growth';

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '关于我们']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '发展历程',
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'slidebar' => 'about'
        ));
    }

    /**
     * 企业文化
     * @author 吴晓平 <2018年07月05日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function culture(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.culture' : 'home.index.culture';

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '关于我们']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '企业文化',
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'slidebar' => 'about'
        ));
    }

    /**
     * 招贤纳士
     * @author 吴晓平 <2018年07月05日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function recruit(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.recruit' : 'home.index.recruit';

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '关于我们']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '招贤纳士',
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'slidebar' => 'about'
        ));
    }

    /**
     * 联系我们
     * @author 吴晓平 <2018年07月05日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function contactUs(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.contactUs' : 'home.index.contactUs';

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '关于我们']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, array(
            'title' => '联系我们',
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'slidebar' => 'about'
        ));
    }


    /**
     * app定制
     * @return [type] [description]
     */
    public function appCustomize()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.mobile.appCustomize', [
            'title' => 'App定制哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技',
            'slidebar' => 'home',
            'publicData' => $publicData
        ]);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170418
     * @desc 网站地图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function siteMap()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view('home.index.siteMap', array(
            'title' => '网站地图',
            'publicData' => $publicData
        ));
    }

    /*************************************************************************************/
    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170418
     * @desc 网站地图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function help(Request $request, InformationTypeService $informationTypeService, InformationService $informationService, InfoRecommendService $infoRecommendService, $category = '', $id = 0)
    {

        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.help' : 'home.index.help';
        $tag = $request->input('tag') ?? 0;
        $data = $informationData = $typeData = [];
        $obj = $informationTypeService->model->where(['parent_id' => 0, 'name' => '帮助中心'])->first();
        if ($obj) {
            $data = $obj->toArray();
            $typeData = $informationTypeService->model->where(['parent_id' => $data['id']])->get()->toArray();
            //进页面默认分类数据
            if (empty($request->input['secCategory'])) {
                $result = $informationTypeService->model->where('type_path', 'like', '%,' . $typeData[0]['id'] . ',%')->get()->toArray();
                if ($result) {
                    $ids = [];
                    foreach ($result as $val) {
                        $ids[] = $val['id'];
                    }
                    $where['info_type'] = ['in', $ids];
                } else {
                    $where['info_type'] = $typeData[0]['id'];
                }
                $whereData = array_merge([$data['id']], $where);
                list($informationData) = $informationService->get($whereData);
            } else {
                list($informationData) = $informationService->get([$data['id']]);
            }

            foreach ($informationData['data'] as &$val) {
                $val['content'] = $infoRecommendService->intercept(strip_tags($val['content']), 100);
            }
        }

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '帮助中心']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, array(
            'title' => '帮助中心',
            'typeData' => $typeData,
            'informationData' => $informationData,
            'publicData' => $publicData,
            'bannerList' => $bannerList
        ));
    }

    /**
     * 搜索小程序
     * @return [type] [description]
     */
    public function searchXCX(Request $request)
    {
        //区分移动端和pc端路由
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.searchXCX' : 'home.index.searchXCX';

        //获取参数
        $input = $request->input();

        $paramKeys = array_keys($input);
        //判断链接来源
        $link_source = '站内链接';
        if (in_array('source_type_888_html', $paramKeys)) {
            $link_source = '百度';
        } else if (in_array('source_type_666_html', $paramKeys)) {
            $link_source = '360';
        }
        Cookie::queue('source_user', $link_source, 120);  //把参数保存到cookie,设置2小时过期

        if ($request->isMethod('post')) {
            $rules = array(
                'title' => 'required',
                'phone' => 'required|regex:mobile',
                'name' => 'required',
            );
            $messages = array(
                'title.required' => '请输入小程序名称',
                'phone.required' => '请输入手机号码',
                'phone.regex' => '手机号码格式不正确',
                'name.required' => '请填写你的称呼',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return myerror($validator->errors()->first());
            }

            $reserveData = [
                'liteapp_title' => trim($input['title']),
                'name' => $input['name'],
                'phone' => $input['phone'],
                'action' => 1,
                'type' => 3
            ];

            //判断客户查询的小程序是否已经在系统中
            $flag = (new LiteappService())->checkExistence(trim($input['title'])) ? 1 : 0;
            $reserveData['is_register'] = $flag;

            //判断访问链接来源
            if ($request->cookie('source_user')) {
                $param = $request->cookie('source_user');
                $reserveData['link_source'] = $param;
            } else {
                $reserveData['link_source'] = '站内链接';
            }

            //客户端访问来源（pc、移动端）
            $reserveData['source'] = source();

            //插入
            (new ReserveService())->init()->add($reserveData, false);

            return mysuccess('', '', ['flag' => $flag]);
        }

        //底部查询历史案例列表
        $data = (new LiteappHistoryService())->listWithPage();
        $data = $data[0]['data'];

        //拆分成3条数据一组
        $list = [];
        $len = intval(ceil(count($data) / 3));
        for ($i = 0; $i < $len; $i++) {
            $temp = array_slice($data, $i * 3, 3);
            if (count($temp) == 3) {
                $list[] = $temp;
            }
        }

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, [
            'title' => '搜索小程序',
            'slidebar' => 'home',
            'hideFooter' => 1,
            'history' => $list,
            'publicData' => $publicData
        ]);
    }

    //提交问题帮助
    public function putQuestion(Request $request, ProblemSolvingService $problemSolvingService)
    {
        $input = $request->input();
        if (empty($input)) {
            error('数据异常');
        }

        $saveData = [];
        $saveData['news_id'] = $input['id'];
        $saveData['is_solve'] = $input['is_solve'];
        $saveData['reason'] = join(';', $input['reason']);
        $rs = $problemSolvingService->add($saveData);

        if ($rs) {
            success('提交成功');
        }

        error();
    }


    /**
     * 获取底部公共的信息
     * @return [type] [description]
     */
    public function getPublicInfo()
    {
        /**案例展示--行业类型**/
        $industryService = new IndustryService();
        $industryList = $industryService->getAllList(false);

        /**会搜去资讯--新闻资讯分类-获取一级分类**/
        $informationTypeService = new InformationTypeService();
        $catAll = $informationTypeService->model->wheres(['parent_id' => 0])->get()->toArray();
        foreach ($catAll as $key => $item) {
            if ($item['name'] == '帮助中心') { //会搜去资讯把帮助中心的一级分类移除
                array_splice($catAll, $key, 1);
            }
        }

        /**帮助中心--二级分类**/
        $helpData = $helpChildData = [];
        $obj = $informationTypeService->model->wheres(['parent_id' => 0, 'name' => '帮助中心'])->first();
        if ($obj) {
            $helpData = $obj->toArray();
        }

        /**帮助中心,资讯二级分类显示七条**/
        $types = ['help' => 1, 'news' => 2];
        foreach ($types as $key => $value) {
            $helpTypes = $informationTypeService->getNewsList('', 0, $value, 'detail', 7);
            if (isset($helpTypes['nav']) && $helpTypes['nav']) {
                $informationTypes[$key] = array_slice($helpTypes['nav'], 0, 7);
            }
        }

        /**友情链接**/
        $linkService = new LinkService();
        list($linkList) = $linkService->getAllList();

        $returnData = ['industryList' => $industryList, 'catAll' => $catAll, 'helpChildData' => $helpChildData, 'linkList' => $linkList['data'], 'helpsType' => $informationTypes['help'] ?? [], 'newsType' => $informationTypes['news'] ?? []];

        return $returnData;
    }

    /*
    * @desc 产品服务
    */
    public function productServiec(Request $request)
    {

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.productServiec', array(
            'title' => '产品服务',
            'publicData' => $publicData,
            'slidebar' => 'service',
        ));
    }

    /**
     * todo 获取微信公众号的密钥
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-22
     */
    public function getWeixinSecretKey(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = session('wid');
        $weChatConfService = new WeChatShopConfService();
        $conf = $weChatConfService->getConfigByWid($wid);
        if (empty($conf['app_secret']) && empty($conf['app_id'])) {
            $appId = config('app.public_auth_appid');
            $secret = config('app.public_auth_secret');
        } else {
            $appId = $conf['app_id'];
            $secret = $conf['app_secret'];
        }
        $url = $request->input('url');
        try {
            $jssdk = new JSSDK($appId, $secret, $wid);
            $signPackage = $jssdk->GetSignPackage($url);
            if (!empty($signPackage)) {
                $returnData['data'] = $signPackage;
            } else {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '没有获取到微信api数据';
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $ex->getMessage();
        }
        return $returnData;
    }


    /**
     * 生成二维码
     * @author fuguowei
     * @date 20171117
     */
    public function createQrcode(Request $request)
    {
        $id = $request->input('id'); //数据库中保存的会员卡id
        $url = config('app.url') . 'home/index/caseDetails?id=' . $id;
        $result['show_qrcode_url'] = $url;
        $qrcodeStr = QrCode::size(150)->generate(URL($url));
        $result['qrcodeStr'] = $qrcodeStr;
        echo json_encode($result);
        exit;
    }

    /**
     * 案例展示
     * @author fuguowei
     * @date 20171117
     * @update 许立 2019年01月10日 取整防注入
     * @update 吴晓平 2019年05月22日   获取行业分类 (单独区分会搜云新零售系统)
     * @update 何书哲 2019年05月27日 添加type参数判断
     */
    public function shop(Request $request, CaseService $caseService, IndustryService $industryService, $type = 1)
    {
        // 案例展示增加了会搜云新零售系统案例展示
        // update by 吴晓平 2019年5月17日
        $typeData = ['1' => '会搜云新零售系统', '2' => 'APP定制', '3' => '微信小程序', '4' => '微信商城'];
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.industryCase' : 'home.index.shop';
        $industry = intval($request->input('industry') ?? 0);
        $industryRow = [];
        if (!isset($typeData[$type])) {
            error('参数错误');
        }
        $where['type'] = $typeData[$type];
        if ($industry) {
            $where['industry'] = $industry;
            $industryRow = $industryService->getRowById($industry);
        }
        //获取行业分类 (单独区分会搜云新零售系统)
        if ($type == 1) {
            $industryList = $industryService->getAllList(false, [], 1);
            if (!$is_mobile) {
                $industryList = collect($industryList)->chunk(11)->toArray();
            }
        } else {
            $industryList = $industryService->getAllList(false, [], 0);
            if (empty($industryList)) {
                $industryList = $industryService->getAllList(false, []);
            }
            if (!$is_mobile) {
                $industryList = [$industryList];
            }
        }

        //获取案例
        list($caseList, $page) = $caseService->getAllList($where, 'sort', 12); //显示案例每页显示12条

        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '案例展示']);
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, array(
            'title' => '行业案例',
            'industryList' => $industryList,
            'caseList' => $caseList,
            'page' => $page,
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'typeData' => $typeData,
            'type' => $type,
            'industry' => $industry,
            'industryRow' => $industryRow,
            'slidebar' => 'shop'
        ));
    }

    /**
     * 案例展示接口
     * @author fuguowei
     * @date 20171117
     */
    public function shopApi(Request $request, CaseService $caseService)
    {
        $input = $request->input();
        if (empty($input['industry'])) {
            error('参数不能为空');
        }
        //获取案例列表 根据sort字段从大到小排
        list($caseList, $page) = $caseService->getAllList($input, 'sort');

        $return = array(
            'caseList' => $caseList,
            'page' => $page,
        );

        success('', '', $return);
    }


    /**
     * 帮助中心文章列表页
     * @param  Request $request [description]
     * @param  InformationTypeService $informationTypeService [description]
     * @param  InformationService $informationService [description]
     * @param  InfoRecommendService $infoRecommendService [description]
     * @return [type]
     * @author 吴晓平 2018年07月03日 pc端帮助中心页面数据重构                                     [description]
     */
    public function helps(Request $request, InformationTypeService $informationTypeService, InformationService $informationService, InfoRecommendService $infoRecommendService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.help' : 'home.index.help';
        /****帮助中心数据重构 2018年07月03日 吴晓平****/
        $keywords = $request->input('keywords') ?? '';
        $type_info = $request->input('info_type') ?? 0;
        $nav = [];
        //移动端访问数据
        $mobileHelpTypes = [];
        if ($is_mobile) {
            $mobileHelpTypes = $this->getMobileHelpTypes($request, $informationTypeService);
        } else { //PC端数据
            $data = $informationTypeService->getNewsList($keywords, $type_info, 1, 'detail');
            if (isset($data['nav']) && $data['nav']) {
                $nav = array_slice($data['nav'], 0, 6);
                foreach ($nav as $key => &$value) {
                    $value['newList'] = $informationTypeService->getListFromSecById($value['id']);
                }
            }
            /*底部的公共数据*/
            $publicData = $this->getPublicInfo();
            $informationData['content'] = ProductModule::addProductContentHost($informationData['content'] ?? '');//add by zhangyh
        }
        return view($view_html, array(
            'title' => '帮助中心-帮助首页',
            'slidebar' => 'helps',
            'nav' => $nav,
            'publicData' => $publicData ?? [],
            'mobileHelpTypes' => $mobileHelpTypes
        ));
    }

    /**
     * 移动端数据与pc端数据结构不一样，所以单独获取移动端数据
     * @author 吴晓平 <2018.07.12>
     * @param  [object] $request                [request对象]
     * @param  [object] $informationTypeService [informationTypeService 对象]
     * @return [type]                         [description]
     */
    public function getMobileHelpTypes($request, $informationTypeService)
    {
        $keywords = $request->input('keywords') ?? '';
        $type_info = $request->input('info_type') ?? 0;
        $data = $informationTypeService->getNewsList($keywords, $type_info, 1, 'detail', 0);
        return $data;
    }

    /**
     * 帮助中心--问题词汇
     * @param  Request $request [description]
     * @param  InformationTypeService $informationTypeService [description]
     * @param  InformationService $informationService [description]
     * @param  InfoRecommendService $infoRecommendService [description]
     * @return [type]                                         [description]
     */
    public function helpList(Request $request, InformationTypeService $informationTypeService, InformationService $informationService, InfoRecommendService $infoRecommendService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.helpList' : 'home.index.helpList';
        /****帮助中心数据重构 2018年07月03日 吴晓平****/
        $keywords = $request->input('keywords') ?? '';
        $type_info = $request->input('info_type') ?? 0;
        $pid = $request->input('Pid') ?? 0;
        $sort = $request->input('sort') ?? 0;
        $data = $informationTypeService->getNewsList($keywords, $type_info, 1, 'list', 0, $pid);
        //右侧标题显示
        $typeTitle = '';
        if ($type_info) {
            $typeInfo = $informationTypeService->getRowById($type_info);
            $typeTitle = $typeInfo['name'] ?? '';
        }

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        $informationData['content'] = ProductModule::addProductContentHost($informationData['content'] ?? '');//add by zhangyh
        //dd($data['newsList']);
        return view($view_html, array(
            'title' => '帮助中心-问题词汇',
            'slidebar' => 'helps',
            'type' => $data['nav'],
            'publicData' => $publicData,
            'information' => $data['newsList'] ?? [],
            'newArr' => $newArr ?? [],
            'page' => $data['pageHtml'] ?? '',
            'type_info' => $type_info,
            'pid' => $pid,
            'typeTitle' => $typeTitle
        ));
    }

    /**
     * 帮助中心--自助服务
     * @return [type] [description]
     */
    public function selfServe()
    {
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.selfSere', array(
            'title' => '帮助中心-自助服务',
            'slidebar' => 'home',
            'publicData' => $publicData,
            'slidebar' => 'helps'
        ));
    }

    /**
     * 会搜云资讯数据重构
     * @param  Request $request [description]
     * @param  InformationTypeService $informationTypeService [description]
     * @param  InformationService $informationService [description]
     * @param  InfoRecommendService $infoRecommendService [description]
     * @return [type]                                         [description]
     * @author 吴晓平 <2018年07月04日>
     */
    public function newList(Request $request, InformationTypeService $informationTypeService, InformationService $informationService, InfoRecommendService $infoRecommendService)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.news' : 'home.index.news';
        $isApi = $request->input('api') ?? '';

        $keywords = $request->input('keywords') ?? '';
        $info_type = $request->input('info_type') ?? 0;
        $pid = $request->input('Pid') ?? 0;
        $data = $informationTypeService->getNewsList($keywords, $info_type, 2, 'list', 6, $pid);
        /***处理资讯图片，资讯内容显示***/
        $source = [];
        if (isset($data['newsList']) && $data['newsList']) {
            foreach ($data['newsList']['data'] as &$val) {
                if ($val['attachment']) {
                    $fileInfoService = new FileInfoService();
                    $tmp = explode(',', $val['attachment']);
                    $source = $fileInfoService->getRowById($tmp[0]);
                    $val['source'] = $source;
                }
                $val['content'] = $infoRecommendService->intercept(strip_tags($val['content']), 100);
            }
        }
        if ($isApi) {
            success('', '', $data);
        }
        /**banner图列表**/
        $bannerService = new BannerService();
        list($bannerList) = $bannerService->getAllList(['position' => '会搜云资讯']);

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, array(
            'title' => '会搜云学院',
            'slidebar' => 'news',
            'type' => $data['nav'],
            'information' => $data['newsList'] ?? [],
            'publicData' => $publicData,
            'bannerList' => $bannerList,
            'page' => $data['pageHtml']
        ));
    }

    /**
     * 资讯详情
     * @author 吴晓平 <2018年07月10>
     * @param  Request $request [description]
     * @param  InformationTypeService $informationTypeService [description]
     * @param  InforService $inforService [description]
     * @param  integer $id [资讯id]
     * @param  string $type [标识类型 news为资讯，help为帮助中心]
     * @return [type]                                         [description]
     */
    public function newsDetail(Request $request, InformationTypeService $informationTypeService, InforService $inforService, $id = 0, $type = 'news')
    {
        if (empty($id)) {
            return myerror('资讯不存在或已被删除');
        }
        $inforData = $inforService->getRowById($id);
        if (empty($inforData)) {
            error('资讯不存在或已被删除');
        }

        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.newsDetail' : 'home.index.newsDetail';

        /*上一篇，下一篇*/
        $nextArr = $preArr = [];
        $informationModel = new Information();
        $data = $informationModel->context($inforData['id'], $inforData['info_type']);

        /*相关新闻*/
        $releveNews = $inforService->getRelevantNews($inforData['info_type'], $inforData['id'], $inforData['keywords']);

        $newsType = $type == 'news' ? 2 : 1;
        /*左边的分类列表*/
        $typeData = $informationTypeService->getNewsList('', 0, $newsType, 'detail');
        /**面包屑导航**/
        $dataInfo = $informationTypeService->getRowById($inforData['info_type']);
        $typePath = '';
        $groups = [];
        if ($dataInfo && !empty($typeData['nav'])) {
            $groups = explode(',', $dataInfo['type_path']);
            foreach ($typeData['nav'] as $key => $value) {
                if (in_array($value['id'], $groups)) {
                    foreach ($value['child'] as $k => $v) {
                        $typePath = $value['name'] . '->' . $v['name'];
                    }
                }
            }
        }

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        /*广告*/
        $adService = new AdService();
        list($adList) = $adService->getAllList('sort');
        $adResult = [];
        foreach ($adList['data'] as $ad) {
            if ($ad['type'] == 0) {
                $adResult['common'][] = $ad;
            } else {  //精选广告只选一条（只显示一条信息）
                $adResult['very'] = $ad;
            }
        }
        $inforData['content'] = ProductModule::addProductContentHost($inforData['content'] ?? '', '1');
        return view($view_html, array(
            'title' => '会搜云学院',
            'detail' => $inforData,
            'type' => $type,
            'nextArr' => $data['next'],
            'preArr' => $data['pre'],
            'releveNews' => $releveNews,
            'publicData' => $publicData,
            'adResult' => $adResult,
            'typeData' => $typeData,
            'typePath' => $typePath,
            'groups' => $groups,
            'slidebar' => 'news'
        ));
    }

    /**********************************营销应用 add by 吴晓平 2018年07月02日**********************************************/
    /**
     * 应用推荐页面
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function appRecommen(Request $request, $id = 0)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.appRecommen.index' : 'home.index.appRecommen.index';
        $data = $this->marketing['appRecommen'];
        $referData = $this->aboutRefer($id, $data); //对应相关推荐数据

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.appRecommen.index', [
            'title' => '应用推荐',
            'data' => $data,
            'referData' => $referData,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 经营渠道
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function manageChannel()
    {
        $data = $this->marketing['manageChannel'];
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.manageChannel.index', [
            'title' => '经营渠道',
            'data' => $data,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 经营渠道详情
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function manageChannelDetail(Request $request, $id = 0)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.manageChannel.detail' : 'home.index.manageChannel.detail';
        $ids = [1, 2];
        if (empty($id)) {
            error('请先选择要查看的经营渠道');
        }
        if (!in_array($id, $ids)) {
            error('该经营渠道不存在或已被删除');
        }
        $data = $this->marketing['manageChannel'];

        $referData = $this->aboutRefer($id, $data); //对应相关推荐数据
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, [
            'title' => '经营渠道',
            'data' => $data,
            'referData' => $referData,
            'id' => $id,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 促销折扣
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function salesDiscount()
    {
        $data = $this->marketing['salesDiscount'];
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.salesDiscount.index', [
            'title' => '促销折扣',
            'data' => $data,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 促销折扣
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function salesDiscountDetail(Request $request, $id = 0)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.salesDiscount.detail' : 'home.index.salesDiscount.detail';
        $ids = [1, 2];
        if (empty($id)) {
            error('请先选择要查看的促销折扣');
        }
        if (!in_array($id, $ids)) {
            error('该促销折扣不存在或已被删除');
        }
        $data = $this->marketing['salesDiscount'];
        $referData = $this->aboutRefer($id, $data); //对应相关推荐数据
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, [
            'title' => '促销折扣',
            'data' => $data,
            'referData' => $referData,
            'id' => $id,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 促销工具
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function salesTools()
    {
        $data = $this->marketing['salesTools'];
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.salesTools.index', [
            'title' => '促销工具',
            'data' => $data,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 促销工具详情页
     * @author 吴晓平 <2018.07.02>
     * @return [type] [description]
     */
    public function salesToolsDetail(Request $request, $id = 0)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.salesTools.detail' : 'home.index.salesTools.detail';
        $ids = [1, 2, 3, 4, 5, 6, 7];
        if (empty($id)) {
            error('请先选择要查看的促销工具');
        }
        if (!in_array($id, $ids)) {
            error('该促销工具不存在或已被删除');
        }
        $data = $this->marketing['salesTools'];
        $referData = $this->aboutRefer($id, $data); //对应相关推荐数据
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, [
            'title' => '促销工具',
            'data' => $data,
            'referData' => $referData,
            'id' => $id,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 会员卡券
     * @author 吴晓平 <2018年07月02日>
     * @return [type] [description]
     */
    public function memberTicket()
    {
        $data = $this->marketing['memberTicket'];
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.memberTicket.index', [
            'title' => '会员卡券',
            'data' => $data,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 应用推荐详情页
     * @author 吴晓平 <2018年07月02日>
     * @return [type] [description]
     */
    public function memberTicketDetail(Request $request, $id = 0)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.memberTicket.detail' : 'home.index.memberTicket.detail';
        $ids = [1, 2, 3];
        if (empty($id)) {
            error('请先选择要查看的会员卡券');
        }
        if (!in_array($id, $ids)) {
            error('该会员卡券不存在或已被删除');
        }
        $data = $this->marketing['memberTicket'];
        $referData = $this->aboutRefer($id, $data); //对应相关推荐数据
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, [
            'title' => '会员卡券',
            'data' => $data,
            'referData' => $referData,
            'id' => $id,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 推广工具
     * @author 吴晓平 <2018年07月02日>
     * @return [type] [description]
     */
    public function extension()
    {
        $data = $this->marketing['extension']; //推广工具数据
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view('home.index.extension.index', [
            'title' => '推广工具',
            'data' => $data,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 推广工具详情页
     * @author 吴晓平 <2018年07月02日>
     * @return [type] [description]
     */
    public function extensionDetail(Request $request, $id = 0)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.extension.detail' : 'home.index.extension.detail';
        $ids = [1, 2, 3];
        if (empty($id)) {
            error('请先选择要查看的推广工具');
        }
        if (!in_array($id, $ids)) {
            error('该推广工具不存在或已被删除');
        }
        $data = $this->marketing['extension']; //推广工具数据
        $referData = $this->aboutRefer($id, $data); //对应相关推荐数据
        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, [
            'title' => '推广工具',
            'data' => $data,
            'referData' => $referData,
            'id' => $id,
            'publicData' => $publicData,
            'slidebar' => 'marketing'
        ]);
    }

    /**
     * 相关详情推荐
     * @author 吴晓平 <2018年07月02日>
     * @param  integer $id [查看当前应用的id]
     * @param  array $data [该分类下的所有应用]
     * @return [type]        [description]
     */
    public function aboutRefer($id = 0, $data = [])
    {
        $countNum = count($data);
        //相关推荐
        $referData = [];
        if ($id) {
            foreach ($data as $key => $value) {
                if ($countNum >= $id) {
                    if (($key + 1) != $id) {
                        $referData[] = $value;
                    }
                }
            }
        }
        return $referData;
    }

    /**
     * 下载app页面
     * @author 吴晓平 <2018.07.13>
     */
    public function AppDownLoad(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.AppDownLoad' : 'home.index.AppDownLoad';

        /**生成下载详情页的二维码，并返回生成url**/
        $logo_water = '/public/home/image/logo_water.png';
        $url = config('app.url') . 'home/index/downLoadDetail';
        $qrcodePath = QrCodeService::create($url, $logo_water, 300, 'app', 20);
        $pathData = explode('public', $qrcodePath);
        $qrcodeUrl = count($pathData) >= 2 ? $pathData[1] : '';

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();
        return view($view_html, [
            'title' => 'app定制下载',
            'publicData' => $publicData,
            'qrcodeUrl' => $qrcodeUrl,
            'slidebar' => 'service'
        ]);
    }

    /**
     * 下载详情页
     * @author 吴晓平 <2018.07.16>
     * @return [type] [description]
     * @update 何书哲 2019年06月25日 只能在移动端下载
     */
    public function downLoadDetail(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');

        // update 何书哲 2019年06月25日 只能在移动端下载
        if (!$is_mobile) {
            error('请在移动端下载');
        }

        $view_html = $is_mobile ? 'home.mobile.downLoadDetail' : 'home.index.downLoadDetail';
        return view($view_html, [
            'title' => '下载会搜云商家版APP',
        ]);
    }

    public function honor(Request $request)
    {
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.honor' : 'home.index.honor';

        /*底部的公共数据*/
        $publicData = $this->getPublicInfo();

        return view($view_html, [
            'title' => '资质荣誉',
            'publicData' => $publicData,
            'slidebar' => 'about'
        ]);
    }

    /**
     * 晒晒活动提供的上传图片cdn接口
     * @return [type] [description]
     */
    public function uploadCdnIamge()
    {
        $fileName = date('His') . rand(0, 99999) . rand(0, 99999);
        $path = 'upfile/image/' . date('Y/m/d') . '/' . $fileName . '{.suffix}';

        $bucket = 'shai-cdn';
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $config->setFormApiKey('Mv83tlocuzkmfKKUFbz2s04FzTw=');
        $data['save-key'] = $path;
        $data['expiration'] = time() + 120;
        $data['bucket'] = $bucket;
        $data['notify-url'] = 'https://www.huisou.cn/merchants/myfile/notify/';
        $data['content-length-range'] = '0,10240000';
        $policy = Util::base64Json($data);
        $method = 'POST';
        $uri = '/' . $bucket;
        $signature = Signature::getBodySignature($config, $method, $uri, null, $policy);
        $data = [
            'policy' => $policy,
            'authorization' => $signature,
            'url' => 'https://v0.api.upyun.com/' . $bucket,
        ];
        success($data);
    }

}
    







