@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_kljmoljn.css" />
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
                <a href="{{ URL('/merchants/currency/admin') }}">所有管理员</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/currency/partner') }}">我的拍档</a>
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
    <ul class="hint">
    	<li>由于服务市场业务调整，下面的列表只展现通过邀请码与您绑定的拍档。<a href="##">了解更多</a></li>
    	<li>如需更多店铺服务，请联系会搜云认可的服务商。<a href="##">寻找更多服务商</a></li>
    </ul>
    <div class="dataDiv">
    	<ul class="dataDiv_title">
    		<li>服务商名称</li>
    		<li>联系方式</li>
    		<li>服务商地址</li>
    		<li>服务商资质</li>
    		<li>绑定日期</li>
    		<li>操作</li>
    	</ul>
    	@foreach($partner as $p)
            @php
                $createdAt = date('Y-m-d',$p['created_at']);
            @endphp
            <ul class="dataDiv_content">
                <li>{{$p['service_title'] or ''}}</li>
                <li>{{$p['telephone'] or ''}}</li>
                <li>{{$p['service_addr'] or ''}}</li>
                <li>{{$p['service_aptitude'] or ''}}</li>
                <li>{{ $createdAt  or ''}}</li>
                <li><a href="##" data="{{ $p['id'] or 0 }}" class="del">删除</a></li>
            </ul>
        @endforeach
        {{ $pageHtml }}

    	<span style="float: right;">共<an>0</an>条，每页20条</span>
    </div>
</div>
@endsection
@section('page_js')
<script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_kljmoljn.js"></script>
@endsection