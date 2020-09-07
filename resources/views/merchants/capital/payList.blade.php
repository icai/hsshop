@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/css/payList.css"/>
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
                <a href="javascript:void(0);">续费服务</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/fee/order/list') }}">我的订购</a>
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
    <div class="top-wrap">
        <div class="top">
            <ul>
                <li class="schedule">1.选择所需服务</li>
                <li>-----</li>
                <li class="schedule">2.确认续费订单信息</li>
                <li>-----</li>
                <li class="schedule checked">3.续费服务支付</li>
                <li>-----</li>
                <li class="schedule">4.完成续费</li>
            </ul>
        </div>
    </div>
    <div class="article-wrap">
        <div class="waitPay schedule-item">
            <div class="remit-CS" style="display:none">
                <p><img src="{{ config('app.source_url') }}static/images/remitCS.jpg" alt="CS"/></p>
                <p><a href="tencent://message/?uin=1658349770&Site=&Menu=yes">咨询汇款客服</a></p>
            </div>
            <div class="waitPay-title">
                选择付款方式
            </div>
            <div class="pay-way">
                <p class="fc-666">商家名称：<span class="wid fc-333 inBlock ml-8"></span></p>
                <p class="fc-666">应付金额：<span class="getPrice inBlock ml-8"></span></p>
                <div class="pay-way-choose clearfix ">
                    <p class="pull-left fc-666">
                        支付方式：
                    </p>
                    <div class="pay-way-content pull-left">
                        <p>
                            <label>
                            <input type="radio" name="payWay" id="weixin" value="2" class="choosePayWays"/>
                            <span class="fc-333">微信</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="radio" name="payWay" id="alipay" value="1"  class="choosePayWays"/>
                                <span class="fc-333">支付宝</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="radio" name="payWay" id="remit" value="3" class="choosePayWays"/>
                                <span class="fc-333">汇款支付</span>
                            </label>
                        </p>
                    </div>
                </div>
            </div>

            <div class="pay-content" >
                <div class="alipay-qrcode payment">
                    <!-- <p><img src="{{ config('app.source_url') }}static/images/remitQrCode.jpg" alt=""></p> -->
                    <p>扫一扫 支付宝完成支付</p>
                </div>
                <div class="wechat-qrcode payment">
                    <div class="wechatQRcode"></div>
                    <p>扫一扫 微信完成支付</p>
                </div>
                <div class="remit-form payment">
                    <p class="remit-tips">您需要汇款 <span class="getPrice"></span> 至以下账户，汇款成功后请联系客服，审核通过后为您开通店铺续费，请耐心等待。</p>
                    <div class="remit-content">
                        <p>收款方信息</p>
                        <p>&nbsp;&nbsp;&nbsp;开户名：&nbsp;&nbsp;&nbsp;<span class="receiveCompany"></span></p>
                        <p>&nbsp;&nbsp;&nbsp;开户行：&nbsp;&nbsp;&nbsp;<span class="receiveBank"></span></p>
                        <p>开户账号：&nbsp;&nbsp;&nbsp;<span class="receiveAccount"></span></p>
                        <div class="clearfix">
                            <span class="pull-left">汇款备注：</span>
                            <div class="pull-left">
                                <p>&nbsp;&nbsp;&nbsp;在您进行对公或个人转账时，请务必备注您续费的</p>
                                <p class="red" style="text-indent:16px;font-weight:bold">店铺名称及续费服务</p>
                                <p style="text-indent:16px" class="fc-666 fs-14">例如：续费店铺：<span class="wid"></span>，续费服务：<span class="serviceVersion"></span></p>
                            </div>
                            
                        </div>
                        <p class="remit-submit"><button>提交</button></p>
                        <p class="allowance">
                            <img src="{{ config('app.source_url') }}mctsource/images/payTip.png" alt="tip"/>
                            <span>
                                周末及节假日产生的汇款支付订单，需要等工作人员归来审核，由此给您造成的不便，敬请原谅！
                            </span> </p>
                    </div>
                </div>
            </div>
        </div>

</div>
@endsection
@section('page_js')
<script>
    var host = "{{ config('app.url') }}"
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/payList.js"></script>
@endsection