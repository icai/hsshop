@extends('merchants.default._layouts')
@section('head_css')
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/share_list.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/discount_list.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
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
                    <a href="javascript:void(0)">满减</a>
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
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix pr">
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li @if(!request('status'))class="hover"@endif>
                        <a href="/merchants/marketing/discountList">所有活动</a>
                    </li>
                    <li @if(request('status') == 1)class="hover"@endif>
                        <a href="/merchants/marketing/discountList?status=1">未开始</a>
                    </li>
                    <li @if(request('status') == 2)class="hover"@endif>
                        <a href="/merchants/marketing/discountList?status=2">进行中</a>
                    </li>
                    <li @if(request('status') == 3)class="hover"@endif>
                        <a href="/merchants/marketing/discountList?status=3">已结束</a>
                    </li>
                </ul>  
            </div>
        </div> 
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <div class="mb-20 clearfix pr">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a class="btn btn-success new_btn" href="{{ URL('/merchants/marketing/edit') }}">新建满减</a>
                </div>
            </div>
        </div>
        <!-- search 结束 -->
        <!-- 列表 开始 -->
        <div class="main_content">
            <ul class="main_content_title">
                <li>创建时间</li>
                <li>活动名称</li>
                <li>有效期</li>
                <li>活动状态</li>
                <li>操作</li>
            </ul>
            @forelse($data[0]['data'] as $val)
            <ul class="data_content">
                <li>{{$val['created_at']}}</li>
                <li class="shop_name" title="{{$val['title']}}">{{$val['title']}}</li>
                <li>{{$val['start_time']}} 至 {{$val['end_time']}}</li>
                <li>@if($val['status'] == 1)未开始@elseif($val['status'] == 2)进行中@else 已结束 @endif</li>
                <li class="pr">                    
                    <a class="J_see_create" href="/merchants/marketing/edit?id={{$val['id']}}">编辑</a>
                    -<a class="J_watch_data" href="javascript:void(0)" data-id="{{$val['id']}}" data-title="{{$val['title']}}">查看数据</a>
                    -<a class="J_delete" href="javascript:void(0);" data-id="{{$val['id']}}">删除</a>
                    @if($val['status'] != '3')
                        -<a class="J_invalid" href="javascript:void(0);" data-id="{{$val['id']}}">结束活动<span style="color:#ff6600">[?]</span></a>
                    @endif
                </li>
            </ul>
            @endforeach
           
            @if(!$data[0]['data'])
            <div class="empty">暂无数据</div> 
            @endif
        </div> 
        <!-- 列表 结束 -->
        <!-- 分页 -->
        <div class="text-right">
          {{$data[1]}}
        </div>
        
        <div class="watch-model">
            <div class="watch-wraper">
                <p class="watch-title">
                    查看数据
                    <span class="close-wraper">×</span>
                </p>
                <p class="activity-title">活动名称：<span class="J_title"></span></p>
                <div class="activity-data">
                    <div class="data-item">
                        <p class="item-title">新用户</p>
                        <p class="item-num">0</p>
                    </div>
                    <div class="data-item">
                        <p class="item-title">老用户</p>
                        <p class="item-num">0</p>
                    </div>
                    <div class="data-item">
                        <p class="item-title">成单量</p>
                        <p class="item-num">0</p>
                    </div>
                    <div class="data-item">
                        <p class="item-title">成单金额（元）</p>
                        <p class="item-num">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/discount_list.js"></script> 
@endsection