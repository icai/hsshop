@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/css/invoiceList.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="javascript:void(0);">已开发票</a>
            </li>
            <li >
                <a href="{{ URL('/merchants/capital/fee/printInvoice') }}">开具发票</a>
            </li>
        </ul>
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
    <div class="table">
        <ul class="table-head clearfix">
            <li>申请时间</li>
            <li>发票金额</li>
            <li>发票类型</li>
            <li>发票性质</li>
            <li>状态</li>
            <li>快递单号</li>
            <li>发票下载</li>
            <li>操作</li>
        </ul>
        <div class="table-body">
           
        </div>
    </div>
    
    <div id="checkPop" style="display:none">
        <div class="invoiceDetail">
            <p class="title">发票信息</p>
            <ul>
                <li>发票类型:<span>纸质增值税专用发票(企业)</span></li>
                <li>发票金额:<span>5900元</span></li>
                <li>发票抬头:<span>杭州会搜股份有限公司</span></li>
                <li class="red">开户行地址:<span>杭州市招商银行江干九盛路支行</span></li>
                <li class="red">开户行:<span>0000 0000 0000 000</span></li>
                <li class="red">公司地址:<span>浙江省杭州市九堡东方电子商务园</span></li>
                <li class="red">公司电话:<span>0571-87796692</span></li>
            </ul>
        </div>
        <div class="consignee">
            <p class="title">收件人信息</p>
            <ul><li>收货人:<span>张贝贝</span></li>
                <li>联系电话:<span>13789807689</span></li>
                <li>地址: <span>浙江省杭州市江干区九堡街道东方电子商务园</span></li>
            </ul></div>
        <div class="printLogo"><img src="{{ config('app.source_url') }}static/images/havePrinted.png" /></div>
    </div>
</div>
@endsection
@section('page_js')
<script>
    var host ="{{ config('app.url') }}";
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/invoiceList.js"></script>
@endsection