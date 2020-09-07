@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_e0ybm4m6.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{ URL('/merchants/capital/serviceOrdering') }}">服务订购</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/bulkPurchase') }}">批量采购</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/capital/cdkeyExchange') }}">激活码兑换</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
    <!-- 头部 开始 -->
    <div class="purchase_header">
        <p class="f20 mgb15 items_ellipsis"><strong>感谢你，亲爱的客户！</strong></p>
        <p class="gray_999 f16 mgb15 items_ellipisis2">使用激活码购买会搜云产品</p>
    </div>
    <!-- 头部 结束 -->
    <!-- 激活码 开始 -->
    <div class="active_code">
        <input type="text" name="activeCode" value="" placeholder="请输入激活码" />
        <button class="opt_btn">使用激活码</button>
    </div>
    <!-- 激活码 结束 -->
    <!-- 问题块 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">常见问题</p>
        <div class="common_link"></div>   
    </div>
    <!-- 区域标题 结束 -->  
    <!-- 常见问题 开始 -->
    <div class="question_wrap">
        <h5>1.什么是激活码？</h5>
        <div class="f12 mgb30">活码是指会搜云针对批量采购用户发放的10位以上的串码。</div>
        <h5>2.如何获得激活码？</h5>
        <div class="f12 mgb30">你可以通过批量采购的方式，向会搜云一次性购买5个以上（包含5个）激活码。</div>
        <h5>3.如何使用激活码？</h5>
        <div class="f12 mgb30">你获得激活码以后，可在激活码兑换页面进行兑换。且每个激活码仅可兑换一次，兑换成功后，可获得会搜云微商城1年期（即365日）的使用期限。</div>
        <h5>4.激活码有效期是多久？</h5>
        <div class="f12 mgb30">激活码的兑换有效期为365日，自会搜云发放激活码之时起开始计算。</div>
        <h5>5.激活码每次可使用多少个？</h5>
        <div class="f12 mgb30">目前每家店铺，最多可购买5年微商城服务期，和1年VIP会员服务期。因此您最多可在同一家店铺使用5个激活码兑换为5年微商城服务期。</div>
        <h5>6.如何开发票？</h5>
        <div class="f12 mgb30">我们只向批量采购激活码的采购用户，一次性开具相应金额的“软件服务费”发票。不再单独向使用激活码的店铺，提供发票申请。</div>
        <h5>7.遇到问题我可以联系谁？</h5>
        <div class="f12 mgb30">如果您在激活的过程中遇到问题，可致电商家服务电话：0571-87796692</div>
    </div>
    <!-- 常见问题 结束 -->
</div>
@endsection
@section('page_js')
<!-- 弹框插件 -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_e0ybm4m6.js"></script>
@endsection