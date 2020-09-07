@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_tye01286.css" />
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
            <li>
                <a href="{{ URL('/merchants/currency/service') }}">微商城协议</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/currency/serviceShop') }}">服务商协议</a>
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
<div class="prompt">
	这里是您与服务商之间的服务协议，协议生效后，您即可享受他们的服务。如需购买新的服务，请联系服务商。<a href="##">寻找专业服务 ></a>
</div>
<div class="content">
    <div class="nav_module clearfix">
    	<div class="pull-left">
            <ul class=" tab_nav">
            	<li class="hover"><a href="##">全部协议</a></li>
            	<li><a href="##">等待生成</a></li>
            	<li><a href="##">已生成</a></li>
            	<li><a href="##">已作废</a></li>
            </ul>
        </div>
    </div>
    <!--全部协议-->
    <div class="imgShow allAgreement showed">
    	<ul class="title flex_star">
    		<li>服务商名称</li>
    		<li>创建时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="agreementMsg">
    		<li>杭州会搜云科技有限公司</li>
    		<li>2017-01-20 10:11</li>
    		<li>已生效</li>
    		<li>
    			<a href="##" class="download" download="name">下载</a>-
    			<a href="##" class="detail">查看详情</a>
    		</li>
    	</ul>
    	<ul class="agreementMsg">
    		<li>杭州会搜云科技有限公司</li>
    		<li>2017-01-20 10:11</li>
    		<li>等待生效</li>
    		<li>
    			<a href="##" class="download" download="name">下载</a>-
    			<a href="##" class="detail">查看详情</a>
    		</li>
    	</ul>
    	<ul class="agreementMsg">
    		<li>杭州会搜云科技有限公司</li>
    		<li>2017-01-20 10:11</li>
    		<li>已作废</li>
    		<li>
    			<a href="##" class="download" download="name">下载</a>-
    			<a href="##" class="detail">查看详情</a>
    		</li>
    	</ul>
    	<span style="float: right;">共<an>3</an>条，每页20条</span>
    </div>
    <!--等待生成-->
    <div class="imgShow waitAgreement">
    	<p>等待生效的协议包括等待您同意并付款的在线协议，以及等待会搜云审核的纸质协议</p>
    	<ul class="title flex_star">
    		<li>服务商名称</li>
    		<li>创建时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="agreementMsg">
    		<li>杭州会搜云科技有限公司</li>
    		<li>2017-01-20 10:11</li>
    		<li>等待生成</li>
    		<li>
    			<a href="##" class="download" download="name">下载</a>-
    			<a href="##" class="detail">查看详情</a>
    		</li>
    	</ul>
    	<span style="float: right;">共<an>1</an>条，每页20条</span>
    </div>
    <!--已生成-->
    <div class="imgShow alreadyAgreement">
    	<p>已生效的协议包括您已经同意并付款的在线协议，以及会搜云审核通过的纸质协议</p>
    	<ul class="title flex_star">
    		<li>服务商名称</li>
    		<li>创建时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="agreementMsg">
    		<li>杭州会搜云科技有限公司</li>
    		<li>2017-01-20 10:11</li>
    		<li>已生效</li>
    		<li>
    			<a href="##" class="download" download="name">下载</a>-
    			<a href="##" class="detail">查看详情</a>
    		</li>
    	</ul>
    	<span style="float: right;">共<an>1</an>条，每页20条</span>
    </div>
    <!--已作废-->
    <div class="imgShow invalidAgreement">
    	<p>已作废的协议包括您拒绝付款的在线协议，以及会搜云审核未通过的纸质协议</p>
    	<ul class="title flex_star">
    		<li>服务商名称</li>
    		<li>创建时间</li>
    		<li>状态</li>
    		<li>操作</li>
    	</ul>
    	<ul class="agreementMsg">
    		<li>杭州会搜云科技有限公司</li>
    		<li>2017-01-20 10:11</li>
    		<li>已作废</li>
    		<li>
    			<a href="##" class="download" download="name">下载</a>-
    			<a href="##" class="detail">查看详情</a>
    		</li>
    	</ul>
    	<span style="float: right;">共<an>1</an>条，每页20条</span>
    </div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_tye01286.js"></script>
@endsection
