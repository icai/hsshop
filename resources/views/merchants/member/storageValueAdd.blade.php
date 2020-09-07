@extends('merchants.default._layouts') 
@section('title',$title)
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/storageValueAdd.css">  
@endsection 
@section('slidebar') 
@include('merchants.member.slidebar') 
@endsection 
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 --> 
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{URL('/merchants/member/storageValue')}}">会员储值</a>
                </li>
                <li id="edit_title">
                    新增储值规则
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 面包屑导航 结束 -->
    </div>
    <!-- 三级导航 结束 --> 
</div>
@endsection 
@section('content')
<div class="content"> 
    <div class="nav_module clearfix pr">
        <div class="pull-left">
            <!-- 导航 开始 -->
            <ul class="tab_nav">
                <li class="ng-scope hover">
                    <a href="{{URL('/merchants/member/storageValue')}}" class="ng-binding">储值规则</a>
                </li>
                <li class="ng-scope">
                    <a href="{{URL('/merchants/member/storageRecord')}}" class="ng-binding">储值记录</a>
                </li>
            </ul>
        </div> 
    </div> 
    <div class="wrapper mt30">
        <div class="wrapper-group">
            <div class="wrapper-group-title group-inner">
                <em class="required">*&nbsp;</em>储值规则名称：
            </div>
            <div class="wrapper-group-cont group-inner">
                <input type="text" class="form-control iblock w200" placeholder="最多可输入9个字符" maxlength="9" id="title" value="{{ $ruleData['title'] or '' }}"> 
            </div>
        </div>
        <div class="wrapper-group">
            <div class="wrapper-group-title group-inner">
                <em class="required">*&nbsp;</em>储值金额：
            </div>
            <div class="wrapper-group-cont group-inner">
                <input type="text" class="t-int-number form-control w100" id="money" placeholder="请输入金额" />
                
                
            </div>
        </div> 
        <div class="wrapper-group">
            <div class="wrapper-group-title group-inner">
                充值送：
            </div>
            <div class="wrapper-group-cont group-inner" style="line-height: 33px;"> 
                <input type="text" id="add_score" class="form-control w100 iblock" value="{{ $ruleData['add_score'] or ''}}" placeholder="请输入积分" /> 积分
            </div>
        </div> 
        <div class="wrapper-group">
            <div class="wrapper-group-title group-inner"></div>
            <div class="wrapper-group-cont group-inner">
                <input type="hidden" id="id" value="{{ $ruleData['id'] or '' }}">
                <button class="btn btn-primary js-submit ml10">保存</button>
            </div>
        </div> 
    </div>
</div>
@endsection 
@section('page_js')  
<script type="text/javascript">
    var edit_id =  {{ $ruleData['id'] or '0' }};      //编辑时的id 0为新增 >0 编辑
    var edit_money = {{ $ruleData['money'] or '0' }}; //编辑时的money
</script>
<script src="{{config('app.source_url')}}mctsource/js/storageValueAdd.js"></script>
@endsection