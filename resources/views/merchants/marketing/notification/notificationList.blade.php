@extends('merchants.default._layouts') 
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/notificationList.css" /> 
<style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide{display:none !important;}ng\:form{display:block;}.ng-animate-start{clip:rect(0,auto,auto,0);-ms-zoom:1.0001;}.ng-animate-active{clip:rect(-1px,auto,auto,0);-ms-zoom:1;}
</style>
@endsection 
@section('slidebar') 
@include('merchants.marketing.slidebar') 
@endsection 
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="javascript:;">营销中心</a>
            </li>
            <li>
                <a href="javascript:;">消息提醒</a>
            </li>
        </ul>
    </div>
</div>
@endsection @section('content')
<div class="content">
    <div  ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix pr">
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li ng-repeat="(key,vo) in navList" ng-class="{hover:vo.isActive}">
                        <a ng-click="tabNav(key)" href="javascript:;">@{{vo.title}} @{{vo.notificationCount>0?'('+vo.notificationCount+')':''}}  </a>
                    </li> 
                </ul>
            </div> 
        </div>
        <div class="notice-list">
            <div class="notice-list-item" ng-if="list.length>0" ng-repeat="vo in list">
                <div class="notice-list-item-inner">
                    <div class="notice-close" ng-click="delMsg(vo.id)">×</div>
                    <div class="notice-from"> 
                        @{{vo.from_content}} @{{vo.is_read==0?'[未读]':'[已读]'}}
                    </div>
                    <div class="notice-content">
                        <div>
                            <span>@{{vo.notification_content}}</span>
                            <a href="@{{vo.redirect_url}}" target="_blank">
                            查看详情»
                            </a>
                        </div>
                        <div class="mt10" ng-if="vo.notification_type == 1">
                            <img src="@{{vo.order_img}}" width="80" height="80" />
                        </div> 
                    </div>
                    <div class="notice-datetime" ng-bind="vo.created_at"></div>
                </div>
            </div> 
            <div class="notice-empty" ng-if="list.length==0">暂时没有消息哦~</div>
        </div>
        <!-- 分页 -->
        <div class="notice-footer" ng-show="isPage">
            <ul class="pagination"></ul>
        </div>
    </div>  
</div>
@endsection @section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/notificationList.js"></script>

@endsection