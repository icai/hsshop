@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_e9shuibk.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">会搜云币</div>
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
    <!-- 头部 开始 -->
    <div class="coin_header display_box mgb30">
        <!-- 主体 开始 -->
        <div class="box_flex1 display_box">
            <!-- 会搜云币 -->
            <div class="display_box f18">
                我的会搜云币
                <div class="mgl15 box_flex1 red items_ellipsis f14">0</div>
            </div>
            <!-- 说明 -->
            <div class="coin_info box_flex1">
                会搜云币领取规则调整，直接发放到会搜云币账户。 <a class="blue_38f" href="javascript:void(0);">查看详情</a>
            </div>
        </div>
        <!-- 主体 结束 -->
        <!-- 按钮 开始 -->
        <a class="bg_red_ff4343" href="javascript:void(0);" target="_blank">赚取更多</a>
        <!-- 按钮 结束 -->
    </div>
    <!-- 头部 结束 -->
    <!-- 分割线 开始 -->
    <hr />
    <!-- 分割线 结束 -->
    <!-- 导航 开始 -->
    <ul class="nav nav-tabs mgb30" role="tablist">
        <li role="presentation" class="active">
            <a href="javascript:void(0);">所有服务</a>
        </li>
        <li role="presentation">
            <a href="javascript:void(0);">即将到期</a>
        </li>
        <li role="presentation">
            <a href="javascript:void(0);">已过期</a>
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
    <div class="no_result mgb15">暂无数据</div>
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
        <h5>1.会搜云币的有效期</h5>
        <div class="f12 mgb30">会搜云币的有效期1年，自领取当月的1日开始计算有效期。若未在有效期内领取的，则逾期自动作废（如若使用会搜云币后发生退款的，则该部分会搜云币不予退还，但会搜云将重新发放相应数额的会搜云币）。</div>
        <h5>2.会搜云币如何获取</h5>
        <div class="f12 mgb30">商家在订购会搜云微商城、会员、应用、邀请商家订购等活动都可以获取会搜云币。<a class="blue_38f" href="javascript:void(0);" target="_blank">详见会搜云币使用规则。</a></div>
        <h5>3.会搜云币的扣除</h5>
        <div class="f12 mgb30">订购会搜云各类业务发生退订（退款），需要扣除该业务通过订购、邀请、奖励所获赠的会搜云币，如账户会搜云币不足，则从业务退款金额中扣除相应的现金。<a class="blue_38f" href="javascript:void(0);">详见会搜云币使用规则。</a></div>
    </div>
    <!-- 常见问题 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_e9shuibk.js"></script>
@endsection