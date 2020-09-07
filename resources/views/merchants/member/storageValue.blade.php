@extends('merchants.default._layouts') @section('title',$title) @section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/storageValue.css"> @endsection @section('slidebar') @include('merchants.member.slidebar') @endsection @section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{URL('/merchants/member/membercard')}}">会员卡管理</a>
            </li>
            <li class="hover">
                <a href="javascript:;">会员储值</a>
            </li>
            <li class="">
                <a href="{{URL('/merchants/member/membercard/obtain')}}">领取记录</a>
            </li>
            <li class="">
                <a href="{{URL('/merchants/member/membercard/refund')}}">退卡记录</a>
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
@endsection @section('content')
<div class="content">
    <div class="explain-wrap">
        <h4>会员储值</h4>
        <p>会员储值，是可帮助商家提升客户忠诚度、增加会员粘性，商家可根据需要创建储值规则，会员储值后可在消费时使用余额进行支付。</p>
    </div>
    <div class="nav_module clearfix pr">
        <div class="pull-left">
            <!-- 导航 开始 -->
            <ul class="tab_nav">
                <li class="ng-scope hover">
                    <a href="javascript:;" class="ng-binding">储值规则</a>
                </li>
                <li class="ng-scope">
                    <a href="{{URL('/merchants/member/storageRecord')}}" class="ng-binding">储值记录</a>
                </li>
            </ul>
        </div> 
    </div> 
    <a href="{{ URL('/merchants/member/storageValueAdd') }}" class="btn btn-primary add-rule">新建储值规则</a>
    <div class="member_list clearfix">
        <ul class="list_item">
            <li>储值规则名称</li>
            <li>储值设置</li>
            <!--<li>储值人/次</li>-->
            <li>营销活动</li> 
            <li>操作</li>
        </ul>
        @forelse($ruleList['data'] as $v)
        <ul class="list_item" data-mid="18294">
            <li>{{ $v['title'] }}</li>
            <li>{{ $v['money']/100 }}</li>
            <!-- <li>0/0</li> -->
            <li>充值送{{ $v['add_score'] }}积分</li>  
            <li data-id="{{ $v['id'] }}">
                <a href="/merchants/member/storageValueAdd?id={{$v['id']}}" class="edit">编辑</a>&nbsp;|&nbsp;
                <!-- <a href="javascript:void(0);" class="link">储值链接</a>&nbsp;|&nbsp; -->
                <a href="javascript:void(0);" class="del">删除</a>
            </li>
        </ul>
        @endforeach
        
    </div>
</div>
@endsection @section('page_js')
<script src="{{config('app.source_url')}}mctsource/js/storageValue.js"></script>
@endsection