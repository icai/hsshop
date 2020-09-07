@extends('shop.common.marketing')
@section('head_css')
<script type="text/javascript">
    var timestamp=new Date().getTime();
</script>
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/groupDetail.css">
<!-- 当前页面css -->
<style type="text/css">
    html{position:relative;}
    body {
        background-position: center top;
        background-repeat: no-repeat;
        background-size: 100% auto;
        background-size: cover;
        height:100%;
    }
    .tpl-fbb .swiper-container{
        overflow:auto;
    }
    .tpl-fbb .swiper-container {
        position:fixed;
    }
    .showLink{display:none;}
    .custom-nav-4 li img {
        vertical-align: middle;
        max-width: 50px;
        max-height: 50px;
    }
</style>
@endsection
@section('main')
<div class="container" id="container">
    <div class="content no-sidebar">
        <div class="content-body js-page-content">
            <div class="group_name" v-html="group_name"></div>
            <div class="editor" v-html="editor_intro"></div>
            <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>
                <div v-if="list['type']=='goods' || list['type']=='goods_group'">
                    <!-- 一大两小 -->
                    <ul class="js-goods-list sc-goods-list pic clearfix size-2 "  v-if="list['listStyle']== 3 && list.goods.length" v-for="good in list['thGoods']" style="visibility: visible;">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card big-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-show="good[0]">
                            <a :href="good[0].url">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" :src="good[0]['thumbnail']">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class="goods-title v-c" v-html="good[0]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[0]['info']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[0]['price']"></em></p>
                                    <p class="goods-price-taobao " v-if="good[0].oprice != 0" v-html="good[0]['oprice']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                        <li class="js-goods-card goods-card small-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-if="good[1]">
                            <a :href="good[1].url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" data-src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FqEKBL3zUtFZk1meW6aOxeL12Yoh.png?imageView2/2/w/280/h/280/q/75/format/webp" :src="good[1]['thumbnail']+thump_400">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class=" goods-title " v-html="good[1]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[1]['info']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[1]['price']"></em>
                                    </p>
                                    <p class="goods-price-taobao " v-if="good[1]['oprice'] != 0" v-html="good[1]['oprice']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}"  v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                        <li class="js-goods-card goods-card small-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-if="good[2]">
                            <a :href="good[2].url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" data-src="" :src="good[2]['thumbnail']+thump_400">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class=" goods-title " v-html="good[2]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[2]['desc']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[2]['price']"></em></p>
                                    <p class="goods-price-taobao " v-if="good[2]['oprice'] != 0" v-html="good[2]['oprice']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <!-- 一大两小 -->
                    <!-- 商品大图显示 -->
                    <ul class="js-goods-list sc-goods-list pic clearfix size-0 " v-if="list['listStyle']== 1" style="visibility: visible;">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card big-pic" v-for="good in list['goods']" :class="[list.list_style,list.has_sub_title]">
                            <a :href="good.url" class="js-goods link clearfix">
                                <div class="photo-block">
                                    <img class="goods-photo js-goods-lazy" :src="good['thumbnail']">
                                </div>
                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class=" goods-title " v-html="good.name"></p>
                                    <p class="goods-sub-title c-black" :class="list['goodInfo'] ? '' : 'hide' " v-html="good.info"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em></p>
                                    <p class="goods-price-taobao" v-if="good.oprice != 0" v-html="good.oprice"></p>
                                </div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <!-- 商品大图显示 -->
                    <!-- 详细列表模式 -->
                    <ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;"  v-if="list['listStyle']== 4">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card" v-for="good in list['goods']" :class="[list.list_style,list.has_sub_title]">
                            <a :href="good.url" class="js-goods link clearfix"  >
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_300">
                                </div>
                                <div class="info" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class="goods-title" v-html="good.name"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em></p>
                                    <p class="goods-price-taobao" v-if="good.oprice != 0" v-html="good.oprice"></p>
                                    <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell']"></div>
                                    <div class="js-goods-buy buy-response"></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- 详细列表模式 -->

                    <!-- 小图模式 -->
                    <ul class="js-goods-list sc-goods-list pic clearfix size-1 " style="visibility: visible;" v-if="list['listStyle']== 2">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card small-pic card " v-for="good in list['goods']" :class="[list.list_style,list.has_sub_title]">
                            <a :href="good.url" class="js-goods link clearfix" >
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_400">
                                </div>
                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class=" goods-title " v-html="good.name"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good.info"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em>
                                    </p>
                                    <p class="goods-price-taobao" v-if="good.oprice != 0" v-html="good.oprice"></p>
                                </div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell'] && list['cardStyle'] != 4"></div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['cardStyle'] == 4">我要抢购</div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <div style="text-align:center;" v-if="btnFlag" >
                     <button class="custom-tag-list-btn " v-on:click="getGroup(list)"   style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>
                    </div>
                    <!-- 小图模式 -->
                </div>
                <!-- 富文本编辑器 -->
                <div class="custom-richtext js-custom-richtext js-lazy-container" v-if="list['type']=='rich_text'" :style="{background:list.bgcolor}">
                    <div v-html = "list['content']"></div>
                </div>
                <!-- 富文本编辑器 -->

                <!-- 图片广告 -->
                <div class="image_ad" v-if="list['type']=='image_ad' && list['images'].length > 0">
                    <!-- 分开大图模式 -->
                    <ul class="custom-image clearfix js-image-ad-seperated js-view-image-list js-lazy-container" v-if="list['advsListStyle'] ==3">
                        <li class="" v-for = "image in list['images']" v-if="list['advSize']==1">
                            <a :href="image.linkUrl ? image.linkUrl : 'javascript:void(0);'">
                                <h3 class="title" v-html="image.title" v-if="image.title"></h3> 
                                <img class="js-lazy js-view-image-item" :src="image.FileInfo.path" :data-src="image.FileInfo.path" :class="{'J_parseImg':!image.linkUrl && (list['resize_image']==undefined || (list['resize_image']!=undefined&&list['resize_image']==1))}"> 
                            </a>
                        </li>
                        <!-- 分开小图模式 -->
                        <li class="custom-image-small" v-for = "image in list['images']" v-if="list['advSize']==2">
                            <a :href="image.linkUrl ? image.linkUrl : 'javascript:void(0);'">
                                <div>
                                    <h3 class="title" v-html="image.title" v-if="image.title"></h3> 
                                    <img class="js-lazy " :src="image.FileInfo.path" :data-src="image.FileInfo.path" :class="{'J_parseImg':!image.linkUrl && (list['resize_image']==undefined || (list['resize_image']!=undefined&&list['resize_image']==1))}"> 
                                </div>
                            </a>
                        </li>
                        <!-- 分开小图模式 -->
                    </ul>
                    <!-- 分开大图模式 -->
                    <!-- 图片广告折叠模式 -->
                    <div class="swiper-container" v-if="list['advsListStyle'] ==2" :id="list['attr_id']">
                        <div class="swiper-wrapper" style="height:auto;width:100%;">
                            <a class="swiper-slide" style="text-align:center" :href="image.linkUrl ? image.linkUrl : 'javascript:void(0);'" v-for="image in list['images']"> 
                                <img class="js-res-load" style="height:auto;width:100%" :src="image.FileInfo.path" :data-src="image.FileInfo.path" :class="{'J_parseImg':!image.linkUrl && (list['resize_image']==undefined || (list['resize_image']!=undefined&&list['resize_image']==1))}">
                                <h3 class="title" style="position:absolute;bottom:0;width:100%;background-color:rgba(0,0,0,0.4);color:#fff;line-height:30px;text-align:left;" v-html="image.title" v-if="image.title"></h3>
                            </a>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <!-- 图片广告折叠模式 -->
                </div>
                <!-- 图片广告 -->
                <!-- 标题样式 -->
                <div class="custom-title-noline" v-if="list['type']=='title'" v-bind:style="{background:list.bgColor}">
                    <div class="custom-title wx_template" :class="{'text-left':list['showPosition']==1,'text-center':list['showPosition']==2,'text-right':list['showPosition']==3}">
                        <h2 class="title">
                            <span v-html="list.titleName"></span>
                            <span class="custom-title-link" v-if = "list.titleStyle == 1">
                                <span class="c-gray-dark" v-if="list.linkUrl">-</span>
                                <a :href="list.linkUrl ? list.linkUrl : 'javadcript:void(0);' " v-html="list.linkTitle"></a>
                            </span>
                        </h2>
                        <p class="sub_title" v-if="list.titleStyle == 1" v-html="list.subTitle"></p>
                        <p class="sub_title" v-if="list.titleStyle == 2">
                            <span class="sub_title_date" v-html="list.date"></span>
                            <span class="sub_title_author" v-html="list.author"></span>
                            <a class="sub_title_link js-open-follow" :href="list.linkUrl ? list.linkUrl : 'javadcript:void(0);' " v-html="list.wlinkTitle"></a>
                        </p>
                    </div>
                </div>
                <!-- 标题样式 -->

                <!-- 进入店铺 -->
                <div class="custom-store block-item border" v-if="list.type=='store'">
                    <a class="custom-store-link clearfix" :href="list.url">
                        <div class="custom-store-img"></div>
                        <div class="custom-store-name" v-html="list.store_name"></div>
                    </a>
                </div>
                <!-- 进入店铺 -->

                <!-- 优惠券样式 -->
                <ul class="custom-coupon clearfix" v-if="list.type=='coupon' && list.couponList.length > 0" :class="{'coupon-item-multi coupon-item-three':list.couponList.length==3,'coupon-item-multi coupon-item-two':list.couponList.length==2,'coupon-item-one':list.couponList.length==1}">
                    <li :class="{'coupon-style1 coupon-color1':!list.couponStyle,'coupon-style1':list.couponStyle==1,'coupon-style2':list.couponStyle==2,'coupon-style3':list.couponStyle==3,'coupon-style4':list.couponStyle==4,'coupon-color1':list.couponColor==1,'coupon-color2':list.couponColor==2,'coupon-color3':list.couponColor==3,'coupon-color4':list.couponColor==4,'coupon-color5':list.couponColor==5,'coupon-disabled':coupon.cls}" v-for="coupon in list.couponList">
                        <a :href="coupon.type == 0 ? coupon.url : 'javascript:void(0);'">
                            <div class="cap-coupon__disabled-text-wrap" v-if="coupon.cls"><div class="cap-coupon__disabled-text" v-text="coupon.cls=='achieved'?'已领取':coupon.cls=='overdue'?'已过期':coupon.cls=='over'?'已领完':coupon.cls=='invalid'?'已失效':''"></div></div>
                            <i class="coupon-icon coupon-left-icon"></i>
                            <div class="coupon-bg"></div>
                            <i class="coupon-icon coupon-right-icon"></i>
                            <div class="coupon-content">
                                <div class="coupon-content-left">
                                    <div class="custom-coupon-price">
                                        <span>￥</span>
                                        <span class="custom-coupon-amount" v-html="coupon.amount"></span>
                                    </div>
                                    <div class="custom-coupon-desc">
                                        <div class="coupon-name-lg">优惠券</div>
                                        <div v-html="coupon.limit_desc"></div>
                                    </div>
                                </div>
                                <div class="coupon-content-right">
                                    立即领取
                                </div>
                                <i class="cap-coupon__dot-above" v-if="list.couponStyle==1"></i>
                                <i class="cap-coupon__dot-below" v-if="list.couponStyle==1"></i>
                            </div>
                        </a>
                    </li>
                </ul>
                <!-- 优惠券样式 -->

                <!-- 公告样式 -->
                <notice v-if="list.type == 'notice'" :content = "list.content"></notice>
                <!-- 公告样式 -->
                <!-- 商品搜索 -->
                <div class="custom-search" :style="{backgroundColor:list.bgColor}" v-if="list.type == 'search'">
                    <form action="{{ config('app.url') }}/shop/product/search/{{$wid}}" method="GET">
                        <input type="text" class="custom-search-input" name="title" placeholder="搜索商品" value="">
                        <button type="submit" class="custom-search-button">搜索</button>
                    </form>
                </div>
                <!-- 商品搜索 -->
                <!-- 商品列表 -->
                <div v-if="list['type']=='goodslist'">
                    <!-- 一大两小 -->
                    <ul class="js-goods-list sc-goods-list pic clearfix size-2 "  v-if="list['listStyle']== 3 && list.goods.length" v-for="good in list['thGoods']" style="visibility: visible;">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card big-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-show="good[0]">
                            <a :href="good[0].url">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" :src="good[0]['thumbnail']">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class="goods-title v-c" v-html="good[0]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[0]['info']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[0]['price']"></em>
                                    </p>
                                    <p class="goods-price-taobao " v-html="good[0]['oprice']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                        <li class="js-goods-card goods-card small-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-if="good[1]">
                            <a :href="good[1].url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" data-src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FqEKBL3zUtFZk1meW6aOxeL12Yoh.png?imageView2/2/w/280/h/280/q/75/format/webp" :src="good[1]['thumbnail']">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class=" goods-title " v-html="good[1]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[1]['info']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[1]['price']"></em></p>
                                    <p class="goods-price-taobao" v-if="good[1]['oprice'] != 0" v-html="good[1]['oprice']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}"  v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                        <li class="js-goods-card goods-card small-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-if="good[2]">
                            <a :href="good[2].url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" data-src="" :src="good[2]['thumbnail']"></div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class=" goods-title " v-html="good[2]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[2]['desc']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[2]['price']"></em></p>
                                    <p class="goods-price-taobao " v-html="good[2]['oprice']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <!-- 一大两小 -->
                    <!-- 商品大图显示 -->
                    <ul class="js-goods-list sc-goods-list pic clearfix size-0 " v-if="list['listStyle']== 1" style="visibility: visible;">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card big-pic" v-for="good in list['goods']" :class="[list.list_style,list.has_sub_title]">
                            <a :href="good.url" class="js-goods link clearfix">
                                <div class="photo-block">
                                    <img class="goods-photo js-goods-lazy" :src="good['thumbnail']">
                                </div>
                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class=" goods-title " v-html="good.name"></p>
                                    <p class="goods-sub-title c-black" :class="list['goodInfo'] ? '' : 'hide' " v-html="good.info"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em>
                                    </p>
                                    <p class="goods-price-taobao" v-html="good.oprice"></p>
                                </div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <!-- 商品大图显示 -->
                    <!-- 详细列表模式 -->
                    <ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;"  v-if="list['listStyle']== 4">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card" v-for="good in list['goods']" :class="[list.list_style,list.has_sub_title]">
                            <a :href="good.url" class="js-goods link clearfix" >
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail">
                                </div>
                                <div class="info" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class="goods-title" v-html="good.name"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em>
                                    </p>
                                    <p class="goods-price-taobao" v-html="good.oprice"></p>
                                    <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell']"></div>
                                    <div class="js-goods-buy buy-response"></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- 详细列表模式 -->

                    <!-- 小图模式 -->
                    <ul class="js-goods-list sc-goods-list pic clearfix size-1 " style="visibility: visible;" v-if="list['listStyle']== 2">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <li class="js-goods-card goods-card small-pic card " v-for="good in list['goods']" :class="[list.list_style,list.has_sub_title]">
                            <a :href="good.url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail">
                                </div>
                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class=" goods-title " v-html="good.name"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good.info"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em>
                                    </p>
                                    <p class="goods-price-taobao" v-html="good.oprice"></p>
                                </div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell'] && list['cardStyle'] != 4"></div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['cardStyle'] == 4">我要抢购</div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <!-- 小图模式 -->
                </div>
                <!-- 商品列表 -->
                <!-- 商品分组 -->
                <good-group v-if="list.type == 'good_group' && (list.top_nav.length || list.left_nav.length)" :content="list" v-on:transfer="setGoodData"></good-group>
                <!-- 图片导航 -->
                <image-link v-if="list.type == 'image_link'" :content="list.images"></image-link>
                <!-- 图片导航 -->
                <!-- 文本链接 -->
                <ul class="custom-nav clearfix" v-if="list.type == 'textlink'">
                    <li v-for="nav in list.textlink">
                        <a class="clearfix relative arrow-right" :href="nav.linkUrl">
                            <span class="custom-nav-title" v-html="nav.titleName"></span>
                        </a>
                    </li>
                </ul>
                <!-- 文本链接 -->
                <!-- 秒杀活动 -->
                <seckill v-if="list.type == 'marketing_active'&& list.content.length>0" :list = "list"></seckill>
                <!-- 秒杀活动 -->
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

    <!-- 规格弹窗 -->
    <div id="nWxwiu79NT" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; transition: none 0.2s ease; opacity: 1;"
       v-if="goodData" v-cloak></div>
    <div id="p0iHRU4SuT" class="sku-layout sku-box-shadow popup" style="overflow: hidden; position: fixed; z-index: 1000; background: white; bottom: 0px; left: 0px; right: 0px; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;" v-if="goodData" v-cloak>
        <div class="sku-layout-title name-card sku-name-card">
            <div class="thumb">
                <img class="js-goods-thumb goods-thumb" src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fq9Xi4vSuS8D804oC_1CD04sb8uA.png?imageView2/2/w/100/h/100/q/75/format/webp" alt="">
            </div>
            <div class="detail goods-base-info clearfix">
                <p class="title c-black ellipsis" v-html="goodData.title"></p>
                <div class="goods-price clearfix">
                    <div class="current-price pull-left c-black">
                        <span class="price-name pull-left font-size-14 c-orange">¥</span>
                        <i class="js-goods-price price font-size-16 vertical-middle c-orange" v-html="goodData.price"></i>
                    </div>
                </div>
            </div>
            <div class="js-cancel sku-cancel">
                <div class="cancel-img" v-on:click="hideGoodModel"></div>
            </div>
        </div>
        <div class="sku-detail adv-opts hotel-checkin-select" style="border: none; margin: 0; display: none;">
            <div class="sku-detail-inner adv-opts-inner-addons">
                <dl class="sku-group select-sku js-select-checkin-date">
                    <dt>时间：</dt>
                    <dd class="js-checkin-date-value">选择入住时间</dd>
                </dl>
            </div>
        </div>
        <div class="adv-opts layout-content" style="max-height: 544px;">
            <div class="goods-models js-sku-views block block-list border-top-0">
                <dl class="clearfix block-item sku-list-container">
                    <dt class="model-title sku-sel-title">
                        <label>尺寸：</label></dt>
                    <dd>
                        <ul class="model-list sku-sel-list">
                            <li class="tag sku-tag pull-left ellipsis">324</li>
                            <li class="tag sku-tag pull-left ellipsis">234</li></ul>
                    </dd>
                </dl>
                <dl class="clearfix block-item sku-list-container">
                    <dt class="model-title sku-sel-title">
                        <label>规格：</label></dt>
                    <dd>
                        <ul class="model-list sku-sel-list">
                            <li class="tag sku-tag pull-left ellipsis active">234</li></ul>
                    </dd>
                </dl>
                <dl class="clearfix block-item">
                    <dt class="sku-num pull-left">
                        <label>购买数量：</label></dt>
                    <dd class="sku-quantity-contaienr">
                        <dl class="clearfix">
                            <div class="quantity">
                                <button class="minus disabled" type="button" disabled="true"></button>
                                <input type="text" class="txt" pattern="[0-9]*" value="1">
                                <button class="plus" type="button"></button>
                                <div class="response-area response-area-minus"></div>
                                <div class="response-area response-area-plus"></div>
                            </div>
                        </dl>
                    </dd>
                    <dt class="other-info">
                        <div class="stock">剩余23657件</div></dt>
                </dl>
                <div class="block-item block-item-messages" style="display: none;"></div>
            </div>
            <div class="confirm-action content-foot clearfix">
                <div class="big-btn-2-1">
                    <a href="javascript:;" class="js-mutiBtn-confirm cart big-btn orange-btn vice-btn">加入购物车</a>
                    <a href="javascript:;" class="js-mutiBtn-confirm confirm big-btn red-btn main-btn">立即购买</a>
                </div>
            </div>
        </div>
    </div>
 
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript">
    var imgUrl = "{{ imgUrl() }}";
    var wid = "{!!$wid!!}";
    var dataList = {!! json_encode($data) !!};
    var navData = {!! json_encode($footer) !!};
</script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script src="{{ config('app.source_url') }}shop/js/groupDetail.js"></script>
@endsection