@extends('shop.common.marketing')
@section('head_css')

<!-- 当前页面css -->
<script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/store_index.css">
 <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/sign.css" /> 
@endsection
<style>
   
</style>
@section('main')
<body class=" ">
    <div class="sign" v-cloak style="width: 100%;overflow: hidden;">
        <div class="signModule" >
            <div class="apps-game">
                <div class="apps-checkin">
                    <div class="apps-checkin-nav">
                        <a href="{{ config('app.url') }}/shop/member/index/{{session('wid')}}">
                            <div class="apps-checkin-avatar">
                            <img :src="signModule.userData.headimgurl">
                            </div>
                        </a>
                        <div class="apps-checkin-nav-opt">
                            <a class="btn btn-opt" href="{{ config('app.url') }}shop/point/signActivityRule">活动规则</a>
                        </div>
                        <div class="apps-checkin-userinfo">
                            <p class="apps-checkin-userinfo-row">[[signModule.userData.nickname]]</p>
                            <p class="apps-checkin-userinfo-row apps-checkin-userinfo-points">积分：<span class="js-points">[[signModule.userData.score]]</span></p>
                        </div>
                    </div>
                    <div class="apps-checkin-content">
                        <div class="apps-checkin-center-content" style="width: 375px; visibility: visible;">
                            <div class="apps-checkin-center-block">
                                <div class="apps-checkin-center-info">
                                    <h4 class="apps-checkin-center-info-title">已连续签到</h4>
                                    <p class="apps-checkin-center-info-row">
                                        <span class="apps-checkin-center-info-days">[[signModule.signData.signDay]]</span>
                                        <span class="apps-checkin-center-info-small">天</span>
                                    </p>
                                </div>
                            </div>

                            <div class="apps-checkin-runway">
                                <div class="apps-checkin-progress apps-checkin-progress-fromzero"></div>
                                <div class="apps-checkin-progress-filled-wrap" :style="{width: signModule.progressWidth+'px'}">
                                    <div class="apps-checkin-progress-filled"></div>
                                </div>
                                <div class="apps-checkin-prize-wrap" v-if="signModule.signReward">
                                    再签到<span class="js-prize-need">[[signRewardResidueDays]]</span>天，有惊喜！
                                </div>
                                <ul class="apps-checkin-days-wrap">
                                    <li class="apps-checkin-day" :class="{'apps-checkin-day-at' : signModule.progressIndex == index}" v-for="(item,index) in 7">[[item - 1 + signModule.difference]]</li>
                                </ul>
                                <div class="apps-checkin-man" :style="{left: signModule.progressWidth<0? 10: (signModule.progressWidth + 8)+'px'}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="apps-checkin-footer">
                        <button class="btn1 btn-checkin js-checkin" @click="sign">[[signModule.signText]]</button>
                    </div>
                </div>
            </div>
            <div class="bg-color" v-if="signModule.showModal">
                <div class="sign-success">
                    <div class="title">签到成功 ！</div>
                    <div class="contetn">本次签到，您获得了以下奖励:</div>
                    <div class="contetn"><span class="jifen-num">[[getScore]]</span>积分奖励</div>
                    <div class="know" @click="hideModule"><span>知道了</span></div>
                </div>
            </div>
        </div>
        <!-- 其他模块 -->
        <div class="content no-sidebar">
            <div class="content-body js-page-content">
                <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>

                    <goods v-if="list['type']=='goods'" :list="list"></goods>
                    <!-- 富文本编辑器 -->
                    <rich-text v-if="list['type']=='rich_text'" :list="list"></rich-text>
                    <!-- 图片广告 -->
                    <image-ad v-if="list['type']=='image_ad' && list['images'].length > 0" :list="list"></image-ad>
                    <!-- 标题样式 -->
                    <title-style v-if="list['type']=='title'" :list="list"></title-style>
                    <!-- 进入店铺 -->
                    <store-in v-if="list['type']=='store'" :list="list"></store-in>
                    <!-- 公告样式 -->
                    <notice v-if="list.type == 'notice'" :content = "list.content"></notice>
                    <!-- 商品列表 -->
                    <goods-list v-if="list['type']=='goodslist'" :list="list"></goods-list>
                    <!-- 商品搜索 -->
                    <search :list='list' :host="host" :wid='wid' v-if="list.type == 'search'"></search>
                    <!-- 商品分组 -->
                    <good-group v-if="list.type == 'good_group' && (list.top_nav.length || list.left_nav.length)" :content="list" v-on:transfer="setGoodData"></good-group>
                </div>
            </div>
        </div>
    </div>

</body>
    
@include('shop.common.footer')
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script type="text/javascript" src="{{config('app.source_url')}}shop/js/until.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var wid = {{$wid}};
    var isBind = {{$__isBind__}};
    var host = "{{ config('app.url') }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/sign.js"></script>
@endsection