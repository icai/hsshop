@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_42swredk.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/currency/service') }}">微商城协议</a>
            </li>
            <li>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
    </div>   
    <!-- 三级导航 结束 -->

    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
	<div class="nav_module clearfix">
    	<div class="pull-left">
            <ul class=" tab_nav">
            	<li class="active hover"><a href="##">全部协议</a></li>
            	<li><a href="##">等待生成</a></li>
            	<li><a href="##">已生成</a></li>
            	<li><a href="##">已作废</a></li>
            </ul>
       </div>
  	</div>
    <!--全部协议-->
    <div class="imgShow allAgreement showed">
    	<ul class="imgShow_title flex_star">
    		<li>协议名称</li>
    		<li>服务支付时间</li>
    		<li>合同金额</li>
    		<li>协议服务时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>等待生效</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>已生成</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>已作废</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    </div>
    <!--等待生成-->
    <div class="imgShow waitAgreement">
    	<ul class="imgShow_title flex_star">
    		<li>协议名称</li>
    		<li>服务支付时间</li>
    		<li>合同金额</li>
    		<li>协议服务时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>等待生效</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>等待生效</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    </div>
    <!--已生成-->
    <div class="imgShow alreadyAgreement">
    	<ul class="imgShow_title flex_star">
    		<li>协议名称</li>
    		<li>服务支付时间</li>
    		<li>合同金额</li>
    		<li>协议服务时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>已生成</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>已生成</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    </div>
    <!--已作废-->
    <div class="imgShow invalidAgreement">
    	<ul class="imgShow_title flex_star">
    		<li>协议名称</li>
    		<li>服务支付时间</li>
    		<li>合同金额</li>
    		<li>协议服务时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>已作废</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    	<ul class="imgShow_content flex_star">
    		<li>会搜云微商城代理销售服务和结算协议（一年版）</li>
    		<li>2017-1-20 09：45</li>
    		<li>4800</li>
    		<li>1年</li>
    		<li>已作废</li>
    		<li class="seeDetail">查看详情</li>
    	</ul>
    </div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_42swredk.js"></script>
@endsection
