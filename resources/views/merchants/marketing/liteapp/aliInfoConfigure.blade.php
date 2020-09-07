@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">支付宝<span style="color:red;font-size:12px">（目前仅支持企业账户）</span></a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
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
        <ul class="tab_nav">
            <li>
                <a href="/merchants/marketing/alixcx/list">小程序列表</a>
            </li>
            <li class="hover">
                <a href="/merchants/marketing/alixcx/configure">小程序设置</a>
            </li>
        </ul>
        
        
        
    </div>
@endsection
@section('page_js')

@endsection