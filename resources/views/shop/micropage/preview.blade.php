<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css"> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_admin_with_components_99562062d4cc8282402cd99c65db38a1.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/store_index.css">
    <style type="text/css">
        .responsive-320 .content {
            width: 540px;
        }
        .content-sidebar{
            display: block;
            margin-left: 550px;
        }
    </style>
</head>
<body>
<div class="container" id="container" :style="{background:bg_color}">
    <div class="content">
        <div class="content-body js-page-content">
            <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>
                <!-- 官网模板2 -->
                <guan-text v-if="list['type']=='imageTextModel'" :list="list"></guan-text>
                <!-- 官网模板2 -->
                <!-- 官网模板 -->
                <guan-wang v-if="list['type']=='bingbing'" :content="list"></guan-wang>
                <!-- 官网模板 -->
                <!-- 美妆小店头部 -->
                <guan-header v-if="list['type']=='header'" :list="list"></guan-header>
                <goods v-if="list['type']=='goods'" :list="list"></goods>
                <!-- 富文本编辑器 -->
                <rich-text v-if="list['type']=='rich_text'" :list="list"></rich-text>
                <!-- 富文本编辑器 -->

                <!-- 图片广告 -->
                <image-ad v-if="list['type']=='image_ad' && list['images'].length > 0" :list="list"></image-ad>
                <!-- 图片广告 -->
                <!-- 标题样式 -->
                <title-style v-if="list['type']=='title'" :list="list"></title-style>
                <!-- 标题样式 -->

                <!-- 进入店铺 -->
                <store-in v-if="list['type']=='store'" :list="list"></store-in>
                <!-- 进入店铺 -->

                <!-- 优惠券样式 -->
                <coupon v-if="list.type=='coupon' && list.couponList.length > 0" :list="list"></coupon>
                <!-- 优惠券样式 -->
                <!-- 会员卡样式 -->
                <card v-if="list.type=='card' && list.cardList.length > 0" :list="list" :host='_host'></card>
                <!-- 会员卡样式 -->
                <!-- 公告样式 -->
                <notice v-if="list.type == 'notice'" :content = "list.content" :bg-color="list.colorBg" :bg-txt="list.txtBg"></notice>
                <!-- 公告样式 -->
                <!-- 商品搜索 -->
                <search :list='list' :host="host" :wid='wid' v-if="list.type == 'search'"></search>
                <!-- 商品列表 -->
                <goods-list v-if="list['type']=='goodslist'" :list="list"></goods-list>
                <!-- 商品列表 -->
                <!-- 商品分组 -->
                <good-group v-if="list.type == 'good_group' && (list.top_nav.length || list.left_nav.length)" :content="list"></good-group>
                <!-- 图片导航 -->
                <image-link v-if="list.type == 'image_link'" :content="list.images"></image-link>
                <!-- 图片导航 -->
                <!-- 文本链接 -->
                <text-link v-if="list.type == 'textlink'" :list='list'></text-link>
                <!-- 文本链接 -->
                <!-- 秒杀活动 -->
                <seckill v-if="list.type == 'marketing_active'&& list.content.length>0" :list = "list"></seckill>
                <!-- 秒杀活动 -->
                <!-- 拼团标题 -->
                <spell-title :content="list.pages" v-if="list.type == 'spell_title'"></spell-title>
                <!-- 拼团标题 -->
                <!-- 拼团列表 -->
                <spell-goods :content="list" v-if="list.type == 'spell_goods'"></spell-goods>
                <!-- 拼团列表 -->
                <!-- 视频组件 -->
                <cvideo :list="list" v-if="list.type == 'video'"></cvideo>
                <!-- 视频组建 -->
                <!-- 魔方组件 -->
                <cube :list="list" :wid="wid" :host="host" v-if="list.type == 'cube'"></cube>
                <!-- 魔方组件 -->

                
                <!-- 预览页面新增 华亢 -->
                <!-- 联系方式组件 -->
                <cmobile :list="list" v-if="list.type == 'mobile'"></cmobile>
                <!-- 联系方组件 -->
                <!-- 享立减 -->
                <share-rebate :list="list" v-if="list.type == 'share_goods'"></share-rebate>
                <!-- 享立减 -->
                <!-- 留言板 -->
                <info-board :list="list" v-if="list.type == 'researchVote'"></info-board>
                <info-board :list="list" v-if="list.type == 'researchAppoint'"></info-board>
                <info-board :list="list" v-if="list.type == 'researchSign'"></info-board>
                <!-- 留言板 -->
                <seckill-list :list="list" v-if="list.type == 'seckill_list'"></seckill-list>
                <!--add by 韩瑜 2018-9-18-->
                <!--商品分组模板页-->
                <group-page :list="list" v-if="list.type == 'group_page'"></group-page>
                <!--end-->
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
        <!-- 左侧二维码 -->
        <div class="content-sidebar">
            <div class="sidebar-section qrcode-info">
                <div class="section-detail">
                    <p class="text-center shop-detail">
                        <strong>手机扫码访问</strong></p>
                    <p class="text-center weixin-title">微信“扫一扫”分享到朋友圈</p>
                    <p class="text-center qr-code">
                        {!! QrCode::size(150)->generate(URL("/shop/microPage/index/$wid/$id")) !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- 左侧二维码 -->
    </div>

</div>
<!-- 底部 开始 -->
<footer class="showLink">
    <div>
        <div class="footer">
            <div class="copyright">
                <div class="ft-links">
                    <a href="{{ config('app.url') }}/shop/index/{{session('wid')}}" >店铺主页</a>
                    <a href="{{ config('app.url') }}/shop/member/index/{{session('wid')}}" >会员中心</a>
                    <a class="attention" href="javascript:void(0);" >关注我们</a>
                    <a href="#" >店铺信息</a>
                </div>
                <div class="ft-copyright ">
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="follow_us">
    <div class="delete">x</div>
    <div class="set" style="display: none;">
        <div class="code">
            <img src="https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=3410440878,2131962516&fm=117&gp=0.jpg">
        </div>
        <p class="suc_info">长按图片【识别二维码】关注公众号</p>
        <p class="other_opt">无法识别二维码</p>
        <div class="opt">
            <p>1.打开微信，点击‘添加朋友’</p>
            <p>2.搜索微信号：lllll</p>
            <p>3.点击‘关注’，完成</p>
        </div>
    </div>
    <div class="noset">
        <div class="code" style="background:url('{{ config('app.url') }}/shop/images/no_code.png') center center no-repeat; background-size: 200% 160%;margin-bottom: 0;padding-top: 20px;">
        </div>
        <p class="info">商家二维码失效</p>
        <p class="info">公众号暂时无法关注~</p>
    </div>
</div>
<!-- 底部 结束 -->
<!-- 当前页面js -->
<script type="text/javascript">
    var APP_HOST = "{{ config('app.url') }}"
    var APP_IMG_URL = "{{ imgUrl() }}"
    var APP_SOURCE_URL = "{{ config('app.source_url') }}"
    var CDN_IMG_URL = "{{config('app.cdn_img_url')}}";
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var host = "{{ config('app.url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var videoUrl = "{{ videoUrl() }}";
    var wid = {!!$wid!!};
    var id = {!! $id !!};
    var mid = '{{ session("mid") }}';
</script>
<script src="{{ config('app.source_url') }}shop/js/preview.js"></script>
</body>
</html>
