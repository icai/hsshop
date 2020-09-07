@extends('shop.common.marketing')
@section('head_css')
    <script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/member_index.css">
<style type="text/css">
      .card-coupon {
            flex-wrap: wrap;
            justify-content: center;
        }
        .member-card {
            width: 80% !important;
            margin-left: 0 !important;
            margin-top: 10px !important;
        }
        .member-card:nth-child(1){
            margin-top: 0 !important;
        }
    .custom-nav-4 li img {
        vertical-align: middle;
        max-width: 50px;
        max-height: 50px;
    }
    .logo{
        display: -webkit-flex;
    }
    .logo a{
        float:right;
        color:#fff;
    }
</style>
@endsection
@section('main') 
<div class="container " id="container" style="min-height:500px">
    <div class="header"></div>
    <div class="content no-sidebar">
        <div class="content-body">
            <div v-for="(list, index) in lists" v-if="lists.length" v-if="lists.length" v-cloak>
                <!-- 等级/积分 -->
                <div v-if="list['type']=='member'">
                    <div class="custom-level">
                        <div class="logo">
                            <div class='member_logo'>
                                <img src="{{$member_logo}}">
                                {{--<p>{{$member_name}} @if($reqFrom == 'aliapp')<a href="/aliapp/authorization/login?type=1&fromUrl={{urlencode(request()->fullUrl())}}">服务授权>></a>@endif</p>--}}
                                <p>
                                    <span>{{$member_name}}</span>
                                    <span>@if($reqFrom == 'aliapp')<a style="float: none" href="/aliapp/authorization/login?type=1&fromUrl={{urlencode(request()->fullUrl())}}">服务授权>></a>@endif</span>
                                </p>
                            </div>
                            @if($eyeCode==1 && $reqFrom != 'aliapp')
                            <a href="/shop/member/distribution?wid={{ $wid }}" class="qrcode_img"></a>
                            @endif
                        </div>
                        <img v-if="!list.thumbnail" class="custom-level-img js-lazy " src="{{ config('app.source_url') }}shop/images/member_bg.png">
                        <img v-else class="custom-level-img js-lazy " :src="list.thumbnail">
                    </div>
                    <!-- 绑定手机号 -->
                    <div class="bind_action" v-if="isBind">
                        登录手机号，同步全渠道订单和优惠券<span @click="bindMobile">登录</span>
                    </div>
                    <!-- 绑定手机号 -->
                    <div class="order-related">
                        <div class="allOrder box_bottom_1px">
                            <a href="/shop/order/index/{{$wid}}">
                                <span class="myOrder">我的订单</span>
                                <span class="all_order">全部订单</span>
                            </a>
                        </div>
                        <ul class="uc-order list-horizon clearfix">
                            <li>
                                <a class="link clearfix relative link-topay" href="/shop/order/index/{{$wid}}?status=0" >
                                    @if($wait_pay>0)
                                    <span class="title-num">{{$wait_pay}}</span>
                                    @endif
                                    <p class="title-info c-black font-size-12">待付款</p>
                                </a>
                            </li>
                            @if($reqFrom != 'aliapp')
                            <li>
                                <a class="link clearfix relative link-totuan" href="/shop/order/index/{{$wid}}?status=-1" >
                                    @if($groupsNum>0)
                                        <span class="title-num">{{$groupsNum}}</span>
                                    @endif
                                    <p class="title-info c-black font-size-12">待成团</p>
                                </a>
                            </li>
                            @endif
                            <li v-if="takeAwayConfig == 0">
                                <a class="link clearfix relative link-tosend" href="/shop/order/index/{{$wid}}?status=1" >
                                    @if($wait_send>0)
                                    <span class="title-num">{{$wait_send}}</span>
                                    @endif
                                    <p class="title-info c-black font-size-12">待发货</p>
                                </a>
                            </li>
                            <li>
                                <a class="link clearfix relative link-send" href="/shop/order/index/{{$wid}}?status=2" >
                                    @if($wait_receive>0)
                                    <span class="title-num">{{$wait_receive}}</span>
                                    @endif
                                    <p class="title-info c-black font-size-12">待收货</p>
                                </a>
                            </li>
                            @if($reqFrom != 'aliapp')
                            <li>
                                <a class="link clearfix relative link-sign" href="/shop/order/index/{{$wid}}?status=3" >
                                    @if($finish>0)
                                    <span class="title-num">{{$finish}}</span>
                                    @endif
                                    <p class="title-info c-black font-size-12">待评价</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                        <div class="block block-list list-vertical">
                            <a class="pageList" :href="item.shop" v-for="(item,index) in homeModule">
                                <div :class='index == homeModule.length - 1 ? "" : "box_bottom_1px"'>
                                    <img class="icon_img" :src="_host + item.icon" alt="">
                                    <p class="title-info" v-html="item.name"></p>
                                    <span class="new-sign" v-if="item.id == 5 && Newcard">NEW</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="shop-nav" v-if="footer != {} && footer.menu" v-cloak>
            <div class="js-navmenu js-footer-auto-ele shop-nav nav-menu nav-menu-1 has-menu-3" v-if="footer.menusType == 1">
                <div class="nav-special-item">
                    <a href="/shop/index/{{$wid}}" class="home">主页</a>
                </div>
                <div class="nav-items-wrap">
                    <div class="nav-item" v-for="(menu,index) in footer.menu" :style="{width:menu.width}">
                        <a class="mainmenu js-mainmenu" :href="menu.submenus.length > 0 ? 'javascript:void(0);': menu.linkUrl" v-on:click="showSub(menu,index)">
                            <span class="mainmenu-txt">
                            <i class="arrow-weixin" v-if="menu.submenus.length"></i>@{{menu.title}}</span>
                        </a>
                        <!-- 子菜单 -->
                        <div class="submenu js-submenu" style="display:none" v-show = "menu.submenusShow && menu.submenus.length">
                            <span class="arrow before-arrow"></span>
                            <span class="arrow after-arrow"></span>
                            <ul>
                                <li v-for = "submenu in menu.submenus">
                                    <a :href="submenu['linkUrl']">@{{submenu['title']}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="js-navmenu js-footer-auto-ele shop-nav nav-menu nav-menu-2 has-menu-3" v-bind:style="{backgroundColor: footer.bgColor}" v-if="footer.menusType == 2">
                <ul class="clearfix">
                    <li v-for="menu in footer.menu" :style="{width:menu.width}">
                        <a :href="menu.linkUrl" style="
                        background-size: 64px 50px
                        " v-bind:style="menu.styleObject">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @if($is_distribute_show)
        <a v-if='distribute' class="be_distribute" href="/shop/distribute/beDistributor">
            <img src="{{ config('app.source_url') }}shop/static/images/fxk@2x.png"/>
        </a>
    @endif
</div>
<!-- 当前页面js -->
@include('shop.common.footer')
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/javascript">
    var imgUrl = "{{ imgUrl() }}";
    var _host = "{{ config('app.source_url') }}" ;
    var is_open = {{$member['is_open_weath']}};//1开启财富眼 0 关闭
    var id = {!!$wid!!};
    var is_overdue = "{{ $is_overdue or '0' }}";  //店铺是否过期标识
    var takeAwayConfig = "{{$takeAwayConfig}}";  //外卖店铺
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script type="text/javascript">
    var isBind = {{$__isBind__}};
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/member_index.js"></script>
@endsection