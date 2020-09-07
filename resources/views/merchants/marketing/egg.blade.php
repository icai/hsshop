@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marking-hitEgg.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="{{URL('/merchants/marketing')}}">营销中心</a>
            </li>
            <li>
                <a href="#">砸金蛋</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->

    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!--顶部导航内容-->
    <div class="content_top">
        <ul>
            <li>活动列表</li>
        </ul>
        <!-- <a href="##" class="tutorial"><span id="icon">?</span>  查看【砸金蛋】使用教程</a> -->
    </div>
    <!--数据显示页面部分-->
    <div class="show_data">
        <!--新建幸运砸蛋按钮部分-->
        <div class="btn_search flex_center rtv">
            <button type="button" class="btn btn-success add_new_btn">新建砸金蛋</button>
        </div>
        <!--数据显示部分-->
        <div class="condent_data">
            <ul class="data_title flex_center" style="background: #F8F8F8;">
                <li style="margin-left: 8px">活动名称</li>
	            <li>参与限制</li>
	            <li>有效期</li>
	            <li>参与人/数</li>
	            <li>中奖/未中奖</li>
	            <li>操作</li>
            </ul>
            @forelse ( $list['data'] as $value )
            <ul class="data flex_center">
                <li style="margin-left: 8px;">{{$value['title']}}</li>
                <li>每人{{ $value['limit_json']['join_limit']['type'] == 2 ? '全程': '每天' }} {{  $value['limit_json']['join_limit']['amount'] }} 次<!--需要渲染--></li>
                <li>{{$value['start_at']}}<br />至<br />{{$value['end_at']}} </li>
                <li>{{ $value['logCount']['memberCount'] }}/<!--需要渲染--><a class="blue_97f" href="/merchants/marketing/egg/member/list/{{$value['id']}}">{{ $value['logCount']['all'] }}<!--需要渲染--></a></li>
                <li><a class="blue_97f" href="/merchants/marketing/egg/member/list/{{$value['id']}}?status=1">{{ $value['logCount']['prize'] }}<!--需要渲染--></a>/<a class="blue_97f" href="/merchants/marketing/egg/member/list/{{$value['id']}}?status=2">{{$value['logCount']['all'] -  $value['logCount']['prize'] }}<!--需要渲染--></a></li>
                <li  data-id="{{$value['id']}}">
                    <a class="management" href="/merchants/marketing/egg/edit/{{$value['id']}}">编辑 -</a>
                    <a class="del" data-id="{{$value['id']}}" href="javascript:void(0);">删除 -</a>
                    <!--update by 韩瑜 2018-8-16-->
                    <a class="link_btn" href="javascript:void(0);" data-id = "{{ $value['id'] }}" data-url="{{ config('app.url') }}shop/activity/egg/index/{{ session('wid')}}/{{$value['id']}}">推广</a>
                	<!--end-->
                </li>
            </ul>
            @empty
                <div class="noData">还没有相关数据</div>
            @endforelse
            <div class="" style="text-align: right;">
            	{{ $pageHtml }}            	
            </div>
        </div>
    </div>
    <!-- 推广弹窗 -->
    <!--add by 韩瑜 2018-8-16-->
    <div class="widget-promotion widget-promotion1" style="display: none;">
        <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
            <li class="wsc_code active">微商城</li>
			<!--<li class="xcx_code">小程序</li>-->
        </ul>
        <div class="widget-promotion-content js-tabs-content">
        	<!--微商城-->
            <div class="js-tab-content-wsc" style="display: block;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="js-qrcode-content">
                            <div class="widget-promotion-content">
	                            <label>商品页链接</label>
	                            <div class="input-append">
	                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="{{config('app.url')}}shop/index/{{session('wid')}}" />
	                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">商品页二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
                                        </div>
                                    </div>
                               	</div>
	                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--小程序-->
            <!--<div class="js-tab-content-xcx" style="display: none;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="js-qrcode-content">
                            <div class="widget-promotion-content">
	                            <label>小程序链接</label>
	                            <div class="input-append">
	                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="pages/index/index" />
	                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">小程序二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img_xcx"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
                                        </div>
                                    </div>
                               </div>
	                        </div>           	
                        </div> 
                    </div>
                </div>
            </div>-->
        </div>
    </div>
    <!--end-->
</div>
@endsection
@section('page_js')
<!--主要内容js文件-->
<script type="text/javascript">
    var host = "{{config('app.url')}}"
    var wid = "{{ session('wid') }}"
</script>
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/marking-hitEgg.js" type="text/javascript" charset="utf-8"></script>
@endsection