@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_39ygjl7x.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="crumb_nav clearfix">
            <li>
                <a href="{{URL('/merchants/marketing')}}">营销中心</a>
            </li>
            <li>
                <a href="javascript:void(0);">大转盘</a>
            </li>
        </ul>
        <!-- 二级导航三级标题 结束 -->
    </div>
</div>
@endsection
@section('content')
<div class="content">
	<ul class="screen_nav nav nav-tabs mgb15" role="tablist">
        <li role="presentation" class="active nav_li">活动列表 </li>
        <!-- <a class="a-rig" href="{{ config('app.url') }}home/index/detail/626/help" target="_blank"><span class="z-cir">?</span>查看如何玩转【幸运大转盘】</a>           -->
    </ul>
	<div class="model_itmes mgb20">
		<a href="/merchants/marketing/addWheel" class="btn btn-success">新建大转盘</a>
        <!-- 搜索 开始 -->
        <div class="search_wrap">
            <form action="" method="get" name="searchForm">
                <label class="search_items">
                    <input class="search_input" type="text" name="title" value="" placeholder="搜索">   
                </label>
            </form>
        </div>
    </div>	
    <!--无数据-->
    <!--<div class="no_result">还没有相关数据</div>-->
	<!-- 列表 开始 -->
    <div class="table table-hover condent_data">
        <!-- 标题 -->
        <ul class="active ul_color data_title flex_center">
            <li>活动名称</li>
            <li>参与限制</li>
            <li>有效期</li>
            <li>参与人/数</li>
            <li>中奖/未中奖</li>
            <li>操作</li>
        </ul>
        <!-- 列表 -->
        @forelse($data[0]['data'] as $val)
            <ul class="data flex_center">
                <li>{{$val['title']}}</li>
                <li>@if($val['rule'] == 1)一天{{$val['times']}}次@elseif($val['rule'] == 2)一人{{$val['times']}}次@endif</li>
                <li>{{$val['start_time']}}至<br>{{$val['end_time']}}</li>
                <li>{{$val['memberCount']}} / @if($val['num']>0)<a class="blue_97f" href="/merchants/marketing/wheelCount/{{$val['id']}}">{{$val['num']}}</a> @else {{$val['num']}}@endif</li>
                <li>
                    @if($val['win_num']>0)<a class="blue_97f" href="/merchants/marketing/wheelCount/{{$val['id']}}?status=2">{{$val['win_num']}}</a>@else{{$val['win_num']}}@endif /
                        @if($val['num'] - $val['win_num']>0)<a class="blue_97f" href="/merchants/marketing/wheelCount/{{$val['id']}}?status=1">{{$val['num'] - $val['win_num']}}</a>@else 0 @endif
                </li>
                <li class="opt_wrap blue_97f">
                    <a href="{{ URL('/merchants/marketing/addWheel?id='.$val['id']) }}">
                        <span class="blue_97f">编辑 -</span>
                    </a>
                    <a class="pagecat-del" data-id={{$val['id']}}>
                        <span class="blue_97f">删除 - </span>
                    </a>
                    <a class="link_btn customTip_items" data-id={{$val['id']}} data-url="{{ config('app.url') }}shop/activity/wheel/{{session('wid')}}/{{$val['id']}}">
                        <span class="blue_97f">推广</span>
                    </a>
                </li>
            </ul>
         @endforeach
    </div>
    <div style="text-align: right;">{{$data[1]}}</div>
	<!-- 推广弹窗 -->
    <!--updata by 韩瑜 2018-8-16-->
    <div class="widget-promotion widget-promotion1" style="display: none;">
        <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
            <li class="wsc_code active">微商城</li>
            <li class="xcx_code">小程序</li>
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
	                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="" />
	                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">商品页二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
                                        </div>
                                    </div>
                               	</div>
	                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--小程序-->
            <div class="js-tab-content-xcx" style="display: none;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="js-qrcode-content">
                            <div class="widget-promotion-content">
	                            <label>小程序链接</label>
	                            <div class="input-append">
	                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="pages/activity/pages/activity/wheel/wheel" />
	                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">小程序二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img_xcx"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
                                        </div>
                                    </div>
                               </div>
	                        </div>           	
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end-->
    
</div>
@endsection
@section('page_js')
    <!-- 当前页面js --> 
    <script type="text/javascript">
    	var host ="{{ config('app.url') }}";
    	var _host = "{{ config('app.source_url') }}";
    	var wid = {{session('wid')}};	
    </script>   
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_39ygjl7x.js"></script>
@endsection