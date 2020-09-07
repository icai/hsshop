@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/delivery_config.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/delivery/deliveryConfig') }}">外卖订单设置</a>
            </li>
            <li>
                <a href="{{ URL('merchants/delivery/printerList') }}">打印机</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
    <div class="switch">
        外卖订单开启
        <div class="switch-wrap sub-switch switch-small">
            <label class="ui-switcher ui-switcher-off" data-is-on="0"></label>
        </div>
    </div>
    <br />
    <br />
    <form id="uploadForm" class="form-horizontal none" enctype="multipart/form-data">
        <div class="delivery-time">
            <label><span>*</span>外卖时段：</label>
            <input type="text" class="week" readonly onclick="checkWeek()"/>
                <div class="week-tip none" >
                    <div class="week-list"></div>
                    <div class="week-confirm">确定</div>
                </div>
            <div class="time-slot">
                <div class="time-item">
                    <div class="start-time select-item"></div>
                    <span>至</span>
                    <div class="end-time select-item"></div>
                    <span class="delete">删除</span>
                </div>
            </div>
            <div class="add-more">最多可添加三个时间段</div>
        </div>
        <div class="cancel-time">
            <label><span>*</span>待付款订单取消时间设置：</label>
            <div>拍下未付款订单<input class="unpay_min" type="text" onkeyup="this.value=this.value.replace(/\D/g,'')">分钟内未付款，自动取消订单</div>
        </div>
        <div class="confirm-time">
            <label><span>*</span>发货后自动确认收货时间设置：</label>
            <div>货后<input class="delivery_hour" type="text" onkeyup="this.value=this.value.replace(/\D/g,'')">小时，自动确认收货</div>
        </div>
	</form>
</div>
<div class="btn-grounp">
    <button class="cancal-btn">取消</button>
    <button class="confirm-btn">保存</button>
</div>
<div class="time-select">
    <span class="time-option">11111</span>
</div>
@endsection
@section('page_js')
<!--layer文件引入-->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script type="text/javascript">
    // var imgUrl = "{{ config('app.source_url') }}" + 'mctsource/';
    var work_days = {!! $configData['work_days'] !!} ;
    var delivery_times = {!! $configData['delivery_times'] !!}
    var is_on = "{{ $configData['is_on']  or '0' }}";
    var unpay_min = "{{ $configData['unpay_min']  or '0' }}";
    var delivery_hour = "{{ $configData['delivery_hour']  or '0' }}";
    var is_set = "{{$is_set}}"
    console.log(work_days,delivery_times,is_on,1111)
</script>
<script src="{{ config('app.source_url') }}mctsource/js/delivery_config.js"></script>
@endsection

