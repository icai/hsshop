@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_q01f94sv.css" />
<!--特殊按钮css样式文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
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
            <li>
                <a href="{{ URL('/merchants/currency/orderSet') }}">上门自提</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/localCity') }}">同城配送</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/express') }}">快递发货</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/currency/tradingSet') }}">交易设置</a>
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
    <div class="form-group content_top">
        <label for="" class="col-sm-1 control-label top_left">拍下未付款：</label>
        <div class="col-sm-5 top_right">
            <input type="number" name="" id="" class="form-control" value="30" min="20" max="1440"/>分钟内未付款，自动取消该订单；
            <i class="errMsg hide">拍下未付款的时间必须在20-1440分钟之间。</i>
        </div>
    </div>
    <div class="saveDiv">
        <button type="button" class="btn btn-primary">保存</button>
    </div>
    <div class="successPromrt hide">保存成功</div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_q01f94sv.js"></script>
<!--特殊按钮js文件-->
<script src="{{ config('app.source_url') }}mctsource/js/specialBtn.js" type="text/javascript" charset="utf-8"></script>
@endsection