@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/applyList.css" /> 
   
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')

<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav"> 
            <li>  
                <a href="{{ URL('/merchants/distribute') }}">一键配置</a>
            </li>  
            <li> 
                <a href="{{ URL('/merchants/distribute/template') }}">分销模板</a>
            </li>  
			<li class="hover"> 
                <a href="{{ URL('merchants/distribute/applyList') }}">申请页面</a>
            </li>  
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content">
	<a href="{{ URL('merchants/distribute/addApplyPage') }}" class="createApply">新建申请页面</a>
	<!--客户列表-->
	<div class="list-wraper">
        <ul class="list_item list_header">
			<li><label><input type="checkbox" class="check-btn js-checkall" >标题</label></li>
			<li>创建时间</li>
			<li>申请次数</li>
			<li>操作</li>
		</ul>
		<div id="t_wrap">
            @forelse($data[0]['data'] as $val)
            <ul class="list_item">
                <li><label><input type="checkbox" class="check-btn js-checkbtn" value="{{$val['id']}}">{{$val['title']}}</label></li>
                <li>{{$val['created_at']}}</li>
                <li>{{$val['num']}}</li>
                <li>
                    <a href="/merchants/distribute/addApplyPage?id={{$val['id']}}">编辑-</a>
                    <a href="javascript:;" data-id="{{$val['id']}}" class="js-ads" data-url="{{config('app.url')}}shop/distribute/apply/{{session('wid')}}/{{$val['id']}}" >推广-</a>
                    <a href="javascript:;" data-id="{{$val['id']}}" class="js-del">删除</a>
                </li>
            </ul>
            @endforeach
		</div>
    </div>
    <div style="text-align: right;overflow: visible;" class="clearfloat">
        <div class="choose-del js-delAll">批量删除</div>
        {{$data[1]}}
    </div>
</div>
<!-- 推广弹窗 -->
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
                            <label>分销客申请链接</label>
                            <div class="input-append">
                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="" />
                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
                            </div>
                        </div>
                        <div class="widget-promotion-content">
                            <label class="label-b">分销客申请二维码</label>
                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                <div class="qrcode">
                                    <div class="qr_img"></div>
                                    <!-- <div class="clearfix qrcode-links">
                                        <a class="down_qrcode down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
                                    </div> -->
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
                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="" />
                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
                            </div>
                        </div>
                        <div class="widget-promotion-content">
                            <label class="label-b">小程序二维码</label>
                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                <div class="qrcode">
                                    <div class="qr_img_xcx"></div>
                                    <!-- <div class="clearfix qrcode-links">
                                        <a class="down_qrcode down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
                                    </div> -->
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
@endsection

@section('page_js')
	<script src="{{ config('app.source_url') }}mctsource/js/applyList.js"></script>
	
@endsection