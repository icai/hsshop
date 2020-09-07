<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Services\ReserveService;
use App\Services\Staff\InfoRecommendService;
use Illuminate\Support\Facades\Cookie;
use App\Services\Staff\InformationService;
use App\Services\Staff\InformationTypeService;
use App\Services\Lib\JSSDK;
use App\Lib\Redis\SourceRedis;
use Illuminate\Http\Request;
use Validator;


class MController extends Controller
{

    /**
     * 移动电商， 会搜云享
     * @return [type] [description]
     */
    public function index()
    {
        return view('home.mobile.index', [
            'title' => '会搜云-会搜云新零售系统|人工智能名片制作|电子名片在线制作|微信获客神器|微信商城分销系统',
            'slidebar' => 'home',
        ]);
    }

    /**
     * [会搜云学院]
     * @return [type] [description]
     */
    public function cloudInstitute(Request $request, InformationTypeService $informationTypeService, InformationService $informationService, InfoRecommendService $infoRecommendService)
    {
        $cateId = $request->input('oneCategory');
        $data = $informationTypeService->model->where(['parent_id' => 0])->get()->toArray();
        if (empty($cateId)) {
            $cateId = $data[0]['id'];
        }

        list($informationData) = $informationService->get([$cateId]);

        foreach ($informationData['data'] as &$val) {
            $val['content'] = $infoRecommendService->intercept(strip_tags($val['content']), 100);
        }
        return view('home.mobile.cloudInstitute', [
            'title' => '会搜云·云商学院',
            'slidebar' => 'home',
            'data' => $data,
            'informationData' => $informationData,
            'cateId' => $cateId,
        ]);
    }

    /**
     * [会搜云·行业案例]
     * @return [type] [description]
     */
    public function industryCase()
    {
        return view('home.mobile.industryCase', [
            'title' => '会搜云·行业案例',
            'slidebar' => 'home',
        ]);
    }

    /**
     * [我要服务/分销系统]
     * @return [type] [description]
     */
    public function serviceFir()
    {
        return view('home.mobile.serviceFir', [
            'title' => '我要服务',
            'slidebar' => 'home',
        ]);
    }

    /**
     * [我要服务/APP定制]
     * @return [type] [description]
     */
    public function serviceSec()
    {
        return view('home.mobile.serviceSec', [
            'title' => '我要服务',
            'slidebar' => 'home',
        ]);
    }

    /**
     * [我要服务/微信小程序]
     * @return [type] [description]
     */
    public function serviceThi()
    {
        return view('home.mobile.serviceThi', [
            'title' => '我要服务',
            'slidebar' => 'home',
        ]);
    }

    /**
     * [我要服务/微营销总裁班]
     * @return [type] [description]
     */
    public function serviceFou()
    {
        return view('home.mobile.serviceFou', [
            'title' => '我要服务',
            'slidebar' => 'home',
        ]);
    }

    /**
     * [我要服务/微信商城]
     * @return [type] [description]
     */
    public function serviceFif()
    {
        return view('home.mobile.serviceFif', [
            'title' => '我要服务',
            'slidebar' => 'home',
        ]);
    }


    /**
     * 联系我们
     * @return [type] [description]
     */
    public function contactUs()
    {
        return view('home.mobile.contactUs', array(
            'title' => '联系我们',
            'slidebar' => 'home',
        ));
    }

    /**
     * 企业文化
     * @return [type] [description]
     */
    public function corporateCulture()
    {
        return view('home.mobile.corporateCulture', array(
            'title' => '企业文化',
            'slidebar' => 'home',
        ));
    }

    /**
     * 招商代理
     * @return [type] [description]
     */
    public function business()
    {
        return view('home.mobile.business', array(
            'title' => '招商代理',
            'slidebar' => 'home',
        ));
    }

    /**
     * 微营销总裁班
     * @return [type] [description]
     */
    public function ceoClass()
    {
        return view('home.mobile.ceoClass', [
            'title' => '微营销总裁班',
            'slidebar' => 'home',
        ]);
    }

    /**
     * 发展历程
     * @return [type] [description]
     */
    public function develop()
    {
        return view('home.mobile.develop', [
            'title' => '发展历程',
            'slidebar' => 'home'
        ]);
    }

    /**
     * 关于我们
     * @return [type] [description]
     */
    public function about()
    {
        return view('home.mobile.about', [
            'title' => '关于会搜',
            'slidebar' => 'home',
        ]);
    }

    /**
     * 预约咨询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function reserve(Request $request, ReserveService $reserveService)
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

        // 定义验证规则
        $rules = [
            'name' => 'required',
            'phone' => 'required|regex:mobile'
        ];
        // 定义错误消息
        $messages = [
            'name.required' => '请填写你的姓名',
            'phone.required' => '请填写手机号码',
            'phone.regex' => '手机号码格式不正确'

        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $reserveData = [];
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
        $rs = $reserveService->init()->add($reserveData, false);

        if ($rs) {
            success();
        } else {
            error();
        }
    }

    /**
     * 招贤纳士
     * @return [type] [description]
     */
    public function recruit()
    {
        return view('home.mobile.recruit', [
            'title' => '招贤纳士',
            'slidebar' => 'home'
        ]);
    }

    /**
     * 会搜云·分销3.0
     * @return [type] [description]
     */
    public function distribution()
    {
        return view('home.mobile.distribution', [
            'title' => '会搜云·分销系统',
            'slidebar' => 'home'
        ]);
    }


    /**
     * 会搜云·小程序
     * @return [type] [description]
     */
    public function smallCode()
    {
        return view('home.mobile.smallCode', [
            'title' => '会搜云·微信小程序',
            'slidebar' => 'home'
        ]);
    }


    /**
     * 会搜云·APP定制
     * @return [type] [description]
     */
    public function appCustomize()
    {
        return view('home.mobile.appCustomize', [
            'title' => '会搜云·APP定制',
            'slidebar' => 'home'
        ]);
    }


    /**
     * 会搜云·微商城
     * @return [type] [description]
     */
    public function ecShop()
    {
        return view('home.mobile.ecShop', [
            'title' => '会搜云·微信商城',
            'slidebar' => 'home'
        ]);
    }

    /**
     * [会搜云·微营销总裁班]
     */
    public function MicroCEOClass()
    {
        return view('home.mobile.MicroCEOClass', array(
            'title' => '会搜云·微营销总裁班',
            'slidebar' => 'home'
        ));
    }
}







