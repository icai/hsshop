@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/css/serviceList.css"/>
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="javascript:void(0);">续费服务</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/fee/order/list') }}">我的订购</a>
            </li>
        </ul>
        <!-- 二级导航三级标题 结束 -->
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
    <div class="top-wrap">
        <div class="top">
            <ul>
                <li class="schedule checked">1.选择所需服务</li>
                <li>-----</li>
                <li class="schedule">2.确认续费订单信息</li>
                <li>-----</li>
                <li class="schedule">3.续费服务支付</li>
                <li>-----</li>
                <li class="schedule">4.完成续费</li>
            </ul>
        </div>
    </div>
    <div class="article-wrap">
        <div class="service-comtent clearfix schedule-item">
            <div class="service-item first-item pull-left">
                <div class="item-top">
                    <p class="i-title"></p>
                    <div><span class="coin red">￥</span><span class="red num"></span>&nbsp;/&nbsp;1&nbsp;年</div>
                    <i class="item-type"></i>
                </div>
                <div class="item-content">
                    <dl>
                        <dt>基础服务</dt>
                        <dd class="i-basis">
                        </dd>
                        <dt>营销推广</dt>
                        <dd class="i-market">
                        </dd>
                        <dt>数据分析</dt>
                        <dd class="i-data">
                        </dd>
                    </dl>
                </div>
                <div class="item-footer">
                    <p><button class="rightNowPay" data-type="3">立即订购</button></p>
                    <p>享受微商城所有功能</p>
                </div>
            </div>
            
            <div class="service-item second-item pull-left">
            <div class="item-top">
                    <p class="i-title"></p>
                    <div><span class="coin red">￥</span><span class="red num"></span>&nbsp;/&nbsp;1&nbsp;年</div>
                    <i class="item-type"></i>
                </div>
                <div class="item-content">
                    <dl>
                        <dt>基础服务</dt>
                        <dd class="i-basis">
                        </dd>
                        <dt>营销推广</dt>
                        <dd class="i-market">
                        </dd>
                        <dt>数据分析</dt>
                        <dd class="i-data">
                        </dd>
                    </dl>
                </div>
                <div class="item-footer">
                    <p><button class="rightNowPay" data-type="2">立即订购</button></p>
                    <p>享受微商城所有功能</p>
                </div>
            </div>

            <div class="service-item third-item pull-left">
            <div class="item-top">
                    <p class="i-title"></p>
                    <div><span class="coin red">￥</span><span class="red num"></span>&nbsp;/&nbsp;1&nbsp;年</div>
                    <i class="item-type"></i>
                </div>
                <div class="item-content">
                    <dl>
                        <dt>基础服务</dt>
                        <dd class="i-basis">
                        </dd>
                        <dt>营销推广</dt>
                        <dd class="i-market">
                        </dd>
                        <dt>数据分析</dt>
                        <dd class="i-data">
                        </dd>
                    </dl>
                </div>
                <div class="item-footer">
                    <p><button class="rightNowPay" data-type="1">立即订购</button></p>
                    <p>享受微商城所有功能</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script>
    var host = "{{ config('app.url') }}"
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/serviceList.js"></script>
@endsection