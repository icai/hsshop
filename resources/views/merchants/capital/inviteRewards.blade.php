@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_b1f0nn99.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">邀请奖励</div>
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
    <!-- 邀请码头部 开始 -->
    <div class="invite_header mgb30 display_box">
        <span class="invite_name">我的邀请码:</span>  
        <div class="box_flex1 mgl15">
            <!-- 邀请码 -->
            <p class="invite_num orange_f60 f18">15305769389025</p>
            <p class="gray_999 f18">暂无</p>
            <!-- 获取邀请码 -->
            <a  class="blue_38f f14" href="javascript:void(0);" data-toggle="modal" data-target="#invite_get">获取邀请码</a>
            <!-- 设置邀请码 -->
            <div class="gray_999 f14">
                为方便推广，您也可以设置个性邀请码。
                <a  class="blue_38f f16" href="javascript:void(0);" data-toggle="modal" data-target="#invite_set">设置</a>
            </div>
        </div>
    </div>
    <!-- 邀请码头部 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">邀请码奖励</p>
        <div class="common_link"></div>   
    </div>
    <!-- 区域标题 结束 --> 
    <!-- 邀请码奖励 开始 -->
    <div class="invite_flow">
        <p class="mgb15">邀请码的奖励流程：</p>
        <ul class="flow_wrap">
            <li class="flow_gone">
                <div class="flow_icon">1</div>
                <p>获取您的店铺邀请码</p>
            </li>
            <li>
                <div class="flow_icon">2</div>
                <p>分享邀请码给其他商家</p>
            </li>
            <li>
                <div class="flow_icon">3</div>
                <p>其他商家使用您邀请码订购可多领取100会搜云币</p>
            </li>
            <li>
                <div class="flow_icon">4</div>
                <p>领取您店铺的会搜云币奖励</p>
            </li>
        </ul>
    </div>
    <!-- 邀请码奖励 结束 -->
    <!-- 分隔线 开始 -->
    <hr />
    <!--  分隔线 结束 -->
    <!-- 导航 开始 -->
    <ul class="nav nav-tabs mgb30" role="tablist">
        <li role="presentation" class="active">
            <a href="javascript:void(0);">奖励记录</a>
        </li>
    </ul>
    <!-- 导航 结束 -->
    <!-- 列表 开始 -->
    <table class="table table-bordered table-hover mgb15">
        <tr class="active">
            <td>服务名称</td>
            <td>服务类型</td>
            <td>到期时间</td>
            <td>服务状态</td>
            <td>操作</td>
        </tr>
    </table>
    <!-- 列表 结束 -->
    <!--列表为空 开始-->
    <div class="no_result mgb15">还没相关数据</div>
    <!-- 列表为空 结束 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">常见问题</p>
        <div class="common_link"></div>   
    </div>
    <!-- 区域标题 结束 -->  
    <!-- 常见问题 开始 -->
    <div class="question_wrap">
        <h5>1.邀请码如何获取</h5>
        <div class="f12 mgb30">商家成功注册或登录会搜云微商城后，可立即获取店铺初始邀请码。</div>
        <h5>2.什么是个性邀请码</h5>
        <div class="f12 mgb30">初始邀请码默认为注册手机号码，为方便推广，您也可以设置个性邀请码，但仅可设置一次，设置成功后初始邀请码依旧有效。您可以长按复制任一邀请码并分享给其他商家，其他商家填写邀请码并订购后，您可领取店铺邀请奖励。</div>
        <h5>3.邀请码奖励规则</h5>
        <div class="f12 mgb30">其他商家使用邀请码并首次订购［会搜云微商城 半年期］，您可领取100会搜云币奖励（非首次订购可领取80会搜云币奖励）；其他商家使用邀请码并首次订购［会搜云微商城 1年期］，您可领取200会搜云币奖励（非首次订购可领取150会搜云币奖励）；其他商家使用邀请码并首次订购［会搜云微商城 2年期］，您可领取 400 会搜云币奖励（非首次订购可领取300会搜云币奖励）；其他商家使用邀请码并订购会搜云VIP会员，不享受奖励。</div>
        <h5>4.邀请码是否可以自己使用</h5>
        <div class="f12 mgb30">不可以。邀请码仅作为推广奖励，用于其他商家订购服务使用，本人使用邀请码订购没有奖励。</div>
        <h5>5.邀请码奖励发放时间</h5>
        <div class="f12 mgb30">好友订购服务后48小时后，会搜云将发放对应邀请码奖励。</div>
    </div>
    <!-- 常见问题 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_b1f0nn99.js"></script>
@endsection