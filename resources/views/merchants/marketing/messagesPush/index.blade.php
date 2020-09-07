@extends('merchants.default._layouts') @section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/messagepush.css" /> 
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
                <a href="javascript:;">模板消息</a>
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
            <ul class="tab_nav">
                <li class="hover">
                    <a href="{{url('/merchants/marketing/messagesPush')}}">模板消息</a>
                </li>
                <li class="">
                    <a href="{{url('/merchants/notification/settingListView/')}}">通知消息</a>
                </li>            
            </ul>
        </div>
        <div class="pull-right common-helps-entry">
            {{--<a class="nav_module_blank" href="/home/index/detail?id=342" target="_blank"><span class="help-icon">?</span>查看【消息提醒】使用教程</a>--}}
        </div>
    </div>
    <!-- 消息提醒 -->
    <div class="widget-app-board">
        <div class="widget-app-board-info">
            <h3>模板消息</h3>
            <p> 模板消息功能可以通过微信公众号<span>(请确保微信公众号已申请开通模板消息)</span>，给买家或商家推送交易和物流相关的提醒消息，包括订单催付、发货、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。</p>
        </div>
    </div>
    <div>
        <div data-reactroot="" class="ui-message-warning">
          由于微信将于2020年1月10号下线小程序模版消息。为了应对变化，我们升级了模版消息的能力，并支持了订阅消息。请各位商家务必提前做好准备，否则会导致小程序无法收到消息。<a href="/home/index/helpDetail/884">点击了解详情</a>
        </div>
        <div data-reactroot="" class="ui-message-warning">
            @if(empty($conf))
            您还未绑定微信公众号，请您绑定后，进行消息提醒设置<a href="/merchants/wechat/wxsettled">立即绑定</a>
                @else
                您已绑定微信公众号，请确保微信公众号已申请开通模板消息。<a href="/home/index/detail/628/news">如何开通?</a>
            @endif
        </div>
    </div>
    <div class="app__content">
        <div>
            <div>
                <div style="margin-top: 20px;">
                    <div class="ui-block-head">
                        <h3 class="block-title">交易物流类消息</h3>
                    </div>
                    <div class="setting-list">
                        @foreach($data['tradeLogistic'] as $datum)
                            <a class="setting-item" href="/merchants/marketing/messagesPush/{{ $datum['link'] }}">
                                <h4 class="title">{{ $datum['title'] }} {{ $datum['is_send_seller'] == 1 ? "(发送给商家)" : ''  }}</h4>
                                <ul>
                                    @if(in_array(1,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(1,$datum['config'])) {{ 'checked' }} @endif " aria-hidden="true"></i><span class="child-name">短信</span></li>
                                    @endif

                                    @if(in_array(2,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(2,$datum['config'])) {{ 'checked' }} @endif " aria-hidden="true"></i><span class="child-name">微信粉丝消息</span></li>
                                    @endif

                                    @if(in_array(3,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(3,$datum['config'])) {{ 'checked' }} @endif" aria-hidden="true"></i><span class="child-name">微信模版消息</span></li>
                                    @endif

                                    @if(in_array(4,$datum['message_setting']) && $datum['title'] == '发货成功消息提醒')
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(4,$datum['config'])) {{ 'checked' }} @endif" aria-hidden="true"></i><span class="child-name">小程序模版消息</span></li>
                                    @endif

                                </ul>
                            </a>
                        @endforeach
                        <div class="empty-item"></div>
                        <div class="empty-item"></div>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <div class="ui-block-head">
                        <h3 class="block-title">提醒推送类消息</h3>
                    </div> 
                    <div class="setting-list">
                        @foreach($data['notification'] as $datum)
                            <a class="setting-item" href="/merchants/marketing/messagesPush/{{ $datum['link'] }}">
                                <h4 class="title">{{ $datum['title'] }} {{ $datum['is_send_seller'] == 1 ? "(发送给商家)" : ''  }}</h4>
                                <ul>
                                    @if(in_array(1,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(1,$datum['config'])) {{ 'checked' }} @endif " aria-hidden="true"></i><span class="child-name">短信</span></li>
                                    @endif

                                    @if(in_array(2,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(2,$datum['config'])) {{ 'checked' }} @endif " aria-hidden="true"></i><span class="child-name">微信粉丝消息</span></li>
                                    @endif

                                    @if(in_array(3,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(3,$datum['config'])) {{ 'checked' }} @endif" aria-hidden="true"></i><span class="child-name">微信模版消息</span></li>
                                    @endif

                                    <!-- @if(in_array(4,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(4,$datum['config'])) {{ 'checked' }} @endif" aria-hidden="true"></i><span class="child-name">小程序模版消息</span></li>
                                    @endif -->

                                </ul>
                            </a>
                        @endforeach
                        <div class="empty-item"></div>
                        <div class="empty-item"></div>
                    </div>  
                </div>
                <div style="margin-top: 20px;">
                    <div class="ui-block-head">
                        <h3 class="block-title">营销关怀类消息</h3>
                    </div>
                    <div class="setting-list">
                        @foreach($data['marketingCare'] as $datum)
                            <a class="setting-item" href="/merchants/marketing/messagesPush/{{ $datum['link'] }}">
                                <h4 class="title">{{ $datum['title'] }} {{ $datum['is_send_seller'] == 1 ? "(发送给商家)" : ''  }}</h4>
                                <ul>
                                    @if(in_array(1,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(1,$datum['config'])) {{ 'checked' }} @endif " aria-hidden="true"></i><span class="child-name">短信</span></li>
                                    @endif

                                    @if(in_array(2,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(2,$datum['config'])) {{ 'checked' }} @endif " aria-hidden="true"></i><span class="child-name">微信粉丝消息</span></li>
                                    @endif

                                    @if(in_array(3,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(3,$datum['config'])) {{ 'checked' }} @endif" aria-hidden="true"></i><span class="child-name">微信模版消息</span></li>
                                    @endif

                                    @if(in_array(4,$datum['message_setting']))
                                        <li class="setting-child"><i class="glyphicon glyphicon-ok @if(in_array(4,$datum['config'])) {{ 'checked' }} @endif" aria-hidden="true"></i><span class="child-name">小程序模版消息</span></li>
                                    @endif


                                </ul>
                            </a>
                        @endforeach
<!-- <div class="empty-item"></div> -->
                        <div class="empty-item"></div>                        
                        <!-- <div class="empty-item"></div> -->
                    </div>
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
<!-- <script src="{{ config('app.source_url') }}mctsource/js/setting_list.js"></script> -->

@endsection