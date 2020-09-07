@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refundMessagesView.css">
@endsection
@section('main')
    <div id="main" v-if="detail != null" v-cloak>

        <div class="userRefundMsg" v-for="list in detail.messages">
            <div class="userInfo flex_start_v">
                <img :src="list.avatar" v-if="list.avatar">
                <div>
                    <h4 v-if="list.status == 0 && list.is_seller == 0">用户添加描述和凭证</h4>
                    <h4 v-if="list.status == 2">商家通过售后单</h4>
                    <h4 v-if="list.status == 1">商家驳回售后单</h4>
                    <h4 v-if="list.status == 4">用户修改退款申请</h4>
                    <h4 v-if="list.status == 3">用户撤销售后单</h4>
                    <h4 v-if="list.status == 10">用户处理逾期，退款失败</h4>
                    <h4 v-if="list.status == 9">用户发货逾期，退款失败</h4>
                    <h4 v-if="list.status == 6">用户确认退货</h4>
                    <h4 v-if="list.status == 5">商家同意退款退货</h4>
                    <h4 v-if="list.status == 0 && list.is_seller == 1">商家添加描述和凭证</h4>
                    <h4 v-if="list.status == 8">商家处理逾期，自动通过售后单</h4>
                    <h4 v-if="list.status == 7">退款完成</h4>
                    <p class="refund-timer">@{{list.created_at}}</p>
                </div>
            </div>
            <div class="problemMsg" v-if="list.status == 0 && list.is_seller == 0">
                <p>问题描述：@{{list.content}}</p>
                <img v-if="list.imgs.length" v-for="img in list.imgs" :src="img">
            </div>
            <div class="problemMsg" v-if="list.status == 1">
                <p>驳回理由：@{{list.content}}</p>
            </div>
            <div class="problemMsg" v-if="list.status == 4">
                <p>退款金额：@{{list.amount}}</p>
                <p>退款原因：@{{list.reason}}</p>
                <p>联系方式：@{{list.phone}}</p>
                <p>退款留言：@{{list.edit_remark}}</p>
            </div>
            <div class="problemMsg" v-if="list.status == 5">
                <p>退货地址：@{{list.refund_address.address}}</p>
                <p>收件人：@{{list.refund_address.name}}@{{list.refund_address.phone}}</p>
            </div>
            <div class="problemMsg" v-if="list.status == 6">
                <p>快递公司：@{{list.express_name}}</p>
                <p>快递单号：@{{list.express_no}}</p>
                <p>发货留言：@{{list.content}}</p>
            </div>
            <div class="problemMsg" v-if="list.status == 0 && list.is_seller == 1">
                <p>问题描述：@{{list.content}}</p>
                <img v-if="list.imgs.length" v-for="img in list.imgs" :src="img">
            </div>
        </div>
        <div class="userRefundMsg">
            <div class="userInfo flex_start_v">
                <img :src="detail.refund.memberAvatar">
                <div>
                    <h4>用户申请退款</h4>
                    <p class="refund-timer">@{{detail.refund.created_at}}</p>
                </div>
            </div>
            <div class="problemMsg">
                <p>退款金额：￥@{{detail.refund.amount}}</p>
                <p>退款方式：@{{detail.refund.type}}</p>
                <p>退款原因：@{{detail.refund.reason}}</p>
                <p>联系方式：@{{detail.refund.phone}}</p>
                <p>问题描述：@{{detail.refund.remark}}</p>
                <img v-if="detail.refund.remark.imgs.length" v-for="img in detail.refund.remark.imgs" :src="img">
            </div>
        </div>
    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var wid = "{{$wid}}"
        var pid = "{{$pid}}"
        var oid = "{{$oid}}"
        var prop_id = "{{$propID}}"
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/refundMessagesView.js"></script>
@endsection
