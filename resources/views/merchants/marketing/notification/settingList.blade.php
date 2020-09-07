@extends('merchants.default._layouts') @section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/notificationList.css" /> 
<style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide{display:none !important;}ng\:form{display:block;}.ng-animate-start{clip:rect(0,auto,auto,0);-ms-zoom:1.0001;}.ng-animate-active{clip:rect(-1px,auto,auto,0);-ms-zoom:1;}
</style>
@endsection @section('slidebar') @include('merchants.marketing.slidebar') @endsection @section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/marketing') }}">营销工具</a>
            </li>
            <li>
                <a href="javascript:;">消息推送</a>
            </li>
            <li>
                <a href="javascript:;">通知消息</a>
            </li>
        </ul>
    </div>
</div>
@endsection @section('content')
<div class="content">
    <!-- 导航模块 开始 -->
    <div class="nav_module clearfix pr">
        <div class="pull-left">
            <!-- 导航 开始 -->
            <!-- update by 韩瑜 2018-10-22 -->
            <ul class="tab_nav">
                <li class="">
                    <a href="{{url('/merchants/marketing/messagesPush')}}">模板消息</a>
                </li>
                <li class="hover">
                    <a href="{{url('/merchants/notification/settingListView/')}}">通知消息</a>
                </li>            
            </ul>
            <!-- end -->
        </div>
        <div class="pull-right common-helps-entry">
            {{--<a class="nav_module_blank" href="/home/index/detail?id=342" target="_blank"><span class="help-icon">?</span>查看【消息提醒】使用教程</a>--}}
        </div>
    </div>
    <!-- 消息提醒 -->
    <div class="widget-app-board">
        <div class="widget-app-board-info">
            <h3>通知消息</h3>
            <p> 通知消息功能可以通过商家后台右下角底部“通知”栏，给商家实时推送已订阅的交易和物流相关的提醒消息（包括订单催付、发货、签收、退款等），让商家及时了解处理相关信息。</p>
        </div>
    </div>
    <div class="app__content" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <div>
            <div>
                <div style="margin-top: 20px;">
                    <div class="ui-block-head">
                        <h3 class="block-title">交易物流信息提醒</h3>
                    </div> 
                    <ul class="clearfix switches" ng-cloak>
                        <li class="switch-item" ng-repeat="(key,vo) in list">
                           <!-- <a ng-href="@{{vo.isSubscribed ?  '/merchants/notification/settingDetailView?notification_type='+ key : 'javascript:void(0)'}}" > -->
                                <h4 class="title" ng-bind="vo.title"></h4>
                                <p class="js-set-wrap disable" ng-if="!vo.isSubscribed">
                                    <span class="js-set-wrap-text">未启用</span>
                                    <span class="free-tip"></span>
                                    <span></span>
                                    <span class="pull-right switch-setting">
                                        <span class="switch-wrap sub-switch switch-small" style="display: block;">
                                            <label class="ui-switcher ui-switcher-off" ng-click="openSwitcher(key,$event)"></label>
                                        </span>
                                    </span> 
                                </p>
                                <p class="js-set-wrap enable" ng-if="vo.isSubscribed">
                                    <span class="js-set-wrap-text">启用</span>
                                    <span class="free-tip" ng-bind="vo.introduce"></span>
                                    <span></span>
                                    <span class="pull-right switch-setting">
                                        <span class="switch-wrap sub-switch switch-small" style="display: block;">
                                            <label class="ui-switcher ui-switcher-on" ng-click="closeSwitcher(key,$event)"></label>
                                        </span>
                                    </span> 
                                </p>
                            </a>
                        </li>  
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@section('page_js')
<!-- 图表插件 -->
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/setting_list.js"></script>

@endsection