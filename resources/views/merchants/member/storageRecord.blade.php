@extends('merchants.default._layouts') 
@section('title',$title) 
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/storageRecord.css"> @endsection 
@section('slidebar') 
@include('merchants.member.slidebar') 
@endsection @section('middle_header')
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
                <li class="ng-scope">
                    <a href="{{URL('/merchants/member/storageValue')}}" class="ng-binding">储值规则</a>
                </li>
                <li class="ng-scope hover">
                    <a href="javascript:;" class="ng-binding">储值记录</a>
                </li>
            </ul>
        </div> 
    </div> 
    <div class="summary-wrap">
    	<!--<p class="summary-title">汇总数据更新时间：2016-11-25 21:57:38</p> -->
    	<ul class="summary-list">
    		<li>
    			<h4>￥{{ $allRecharge/100 }}</h4>
    			<p>累计储值金额</p>
    		</li>
    		<li>
    			<h4>{{ $costNum }}</h4>
    			<p>累计储值人</p><!--/次-->
    		</li>
    		<li>
    			<h4>￥{{ ($allRecharge - $allCost)/100 }}</h4>
    			<p>剩余储值金额</p>
    		</li>
    	</ul>
    </div>

    <div class="member_list clearfix">
        <ul class="list_item">
            <li>时间</li>
            <li>会员姓名</li>
            <li>手机号</li>
            <li>储值金额</li> 
            <li>操作备注</li> 
           
        </ul>
        @foreach($list as $v)
        <ul class="list_item" data-mid="{{ $v['mid'] }}">
            <li>{{ $v['id']}}|{{ date('Y-m-d H:i:s',$v['created_at'])}}</li>
            <li>
            	<a class="balance_detail" href="javascript:;">{{ $members[$v['mid']]['nickname'] or ''}}</a>
            </li>
            <li>{{ $members[$v['mid']]['mobile'] or ''}}</li>
            <li>
                @if ($v['type'] == 1) + {{ $v['money']/100 }} @endif

                @if ($v['type'] == 2) - {{ $v['money']/100 }} @endif
            </li>
            <li>{{ $v['pay_desc'] }}</li> 
           
        </ul>
        @endforeach
        <!-- 分页 -->
        <div class="text-right">
        {{ $pageHtml }}
        </div>
    </div>
</div>
@endsection @section('page_js')
<!-- 搜索插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<script src="{{config('app.source_url')}}mctsource/js/storageRecord.js"></script>
@endsection