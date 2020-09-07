<!--退款添加留言页-->
@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refund.css">
@endsection
@section('main')
   <!-- 顶部分类 -->
   <div id="main" v-cloak>
        <div class="vux-tab tabNav">
            <div v-for="nav in nav" :class="nav.isActive ? 'vux-tab-selected' : ''" class="vux-tab-item" @click="chooseTab(nav)" v-html="nav.title">
            </div>
            <div class="vux-tab-ink-bar vux-tab-ink-bar-transition-backward">
            </div>
        </div>
        <!-- 中间内容 -->
        <ul class="content" v-if="refundList.length">
            <li v-for="list in refundList">
                <a :href="'/shop/order/refundDetailView/'+ list.wid + '/' + list.oid + '/' + list.pid + '/' + list.prop_id">
                    <div class="list_top flex_between_v">
                        <span v-html="list.shop_name"></span>
                        <span class="status" style='color: #F72F37'>@{{list.status | listStatus}}</span>
                    </div>
                    <div class="list_detail">
                        <div class="goods_info flex_between_v">
                            <img :src="imgUrl + list.product_img" width="100">
                            <div class="describe" v-html="list.product_title"></div>
                        </div>
                        <div class="goods_price">实付:￥@{{list.pay_price}} &nbsp;&nbsp;退款金额:
                            <span style='color: #F72F37'>￥@{{list.amount}}</span>
                        </div>
                    </div>
                    <div class="list_fun" v-if="list.status == 4 || list.status == 8">
                        <div class="btn Bred">钱款去向</div>
                    </div>
                </a>
            </li>
        </ul>
        <div style="text-align:center;padding:20px;font-size:16px" v-if="!refundList.length">暂无数据</div>
   </div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var wid = "{{$wid}}"
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/refund.js"></script>
@endsection
