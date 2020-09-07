@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refundVerfyView.css">
@endsection
@section('main')
    <div id="main" v-cloak>
       <div class="content" v-if="detail != null">
            <div class="refundMsg">
                <p>退款金额</p>
                <h3>￥@{{detail.amount}}</h3>
                <p v-if="detail.status == 8">已退至：@{{detail.remark}}</p>
                <div class="flex_between_v">
                    <span>用户账号：</span>
                    <span class="vertical_m">
                        <img :src="detail.headimgurl" width="20">@{{detail.nickname}}</span>
                </div>
                <div class="flex_between_v">
                    <span>支付方式：</span>
                    <span v-if="detail.pay_way == 0">未支付</span>
                    <span v-if="detail.pay_way == 1">微信支付</span>
                    <span v-if="detail.pay_way == 2">支付宝支付</span>
                    <span v-if="detail.pay_way == 3">储值余额支付</span>
                    <span v-if="detail.pay_way == 4">货到付款/到店付款</span>
                    <span v-if="detail.pay_way == 5">找人代付</span>
                    <span v-if="detail.pay_way == 6">领取赠品</span>
                    <span v-if="detail.pay_way == 7">优惠兑换</span>
                    <span v-if="detail.pay_way == 8">银行卡支付</span>
                    <span v-if="detail.pay_way == 9">会员卡支付</span>
                    <span v-if="detail.pay_way == 10">小程序支付</span>
                </div>
            </div>
            <div class="refundStep">
                <div class="vux-timeline">
                    <ul>
                        <li class="vux-timeline-item" v-if="detail.status == 3 || detail.status == 4 || detail.status == 8">
                            <div class="vux-timeline-item-color vux-timeline-item-head-first">
                                <i class="vux-timeline-item-checked weui-icon weui_icon_success_no_circle weui-icon-success-no-circle"></i>
                            </div>
                            <div class="vux-timeline-item-tail" style="display: block;"></div>
                            <div class="vux-timeline-item-content">
                                <div class="step flex_between_v">
                                    <h4 class="recent">商家同意退款</h4>
                                    <p class="recent">@{{detail.agree_at}}</p></div>
                                <span>系统将会在1-2天内提交微信处理</span></div>
                        </li>
                        <li class="vux-timeline-item" v-if="(detail.status == 4 || detail.status == 8) && detail.pay_way != 3">
                            <div class="vux-timeline-item-color vux-timeline-item-head-first">
                                <i class="vux-timeline-item-checked weui-icon weui_icon_success_no_circle weui-icon-success-no-circle"></i>
                            </div>
                            <div class="vux-timeline-item-tail" style="display: block;"></div>
                            <div class="vux-timeline-item-content">
                                <div class="step flex_between_v">
                                    <h4>已退款，微信支付处理中</h4>
                                    <p>@{{detail.verify_at}}</p></div>
                                <span>您的退款已提交给微信处理，通常情况下款项会按照您的付款方式原路退回，退款会在1-3个工作日内到账</span>
                            </div>
                        </li>
                        <li class="vux-timeline-item" v-if="detail.status == 8 ">
                            <div class="vux-timeline-item-color vux-timeline-item-head-first">
                                <i class="vux-timeline-item-checked weui-icon weui_icon_success_no_circle weui-icon-success-no-circle"></i>
                            </div>
                            <div class="vux-timeline-item-tail" style="display: none;"></div>
                            <div class="vux-timeline-item-content">
                                <div class="step flex_between_v">
                                    <h4>到账成功</h4>
                                    <p>@{{detail.success_at}}</p></div>
                                <span>款项已按照您的付款方式原路退回</span></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var wid = "{{$wid}}"
        var refundID = "{{$refundID}}"
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/refundVerfyView.js"></script>
@endsection
