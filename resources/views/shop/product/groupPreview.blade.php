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
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css"> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_admin_with_components_99562062d4cc8282402cd99c65db38a1.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/groupDetail.css">
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
        #container{padding-bottom:50px;}
        .custom-nav-4 li img {
            vertical-align: middle;
            max-width: 50px;
            max-height: 50px;
        }
    </style>
</head>
<body>
<div class="container" id="container">
    <div class="content">
        <div class="content-body js-page-content">
        	<div class="group_name" v-html="group_name"></div>
            <div class="editor" v-html="editor_intro"></div>
            <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>
                <!-- 官网模板2 -->
                <div class="ti_list"  v-if="list['type']=='imageTextModel'">
                    <div class="swiper-container" v-if="list['slideLists'].length">
                        <div class="swiper-wrapper" style="height:auto;width:100%;">
                            <a class="swiper-slide" style="text-align:center" v-for="item in list['slideLists']"> 
                                <img class="js-res-load" style="height:auto;width:100%" :src="item.cover">
                                <h3 class="title" style="position:absolute;bottom:0;width:100%;background-color:rgba(0,0,0,0.4);color:#fff;line-height:30px;text-align:left;padding-left:10px" v-html="item.title"></h3>
                            </a>
                        </div>
                        <div class="swiper-pagination guanwang-swiper-pagination"></div>
                    </div>
                    <div class="js-tabber-tags tabber tabber-bottom red clearfix tabber-n4  ">
                        <div class="custom-tags-more js-show-all-tags"></div>
                        <div id="J_tabber_scroll_wrap" class="custom-tags-scorll clearfix">
                            <div id="J_tabber_scroll_con" class="custom-tags-scorll-con">
                                <a href="javascript:;" v-for="(kind,innerIndex) in list['lists']"  :class="kind.isActive ? 'current':''" :style="{'width':list.width + '%'}" @click="getTextList(kind,list)">@{{kind.title}}</a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <ul>
                            <li v-for="item in textList">
                                <div class="image">
                                    <img :src="item.cover">
                                </div>
                                <div class="ti_content">
                                    <p class="image_title">@{{item.title}}</p>
                                    <p class="image_desc">@{{item.digest}}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- 官网模板2 -->
                <!-- 官网模板 -->
                <guan-wang v-if="list['type']=='bingbing'" :content="list"></guan-wang>
                <!-- 官网模板 -->
                <!-- 美妆小店头部 -->
                <div class="tpl-shop" v-if="list['type']=='header'">
                    <div class="tpl-shop-header" :style="list.bg_image ? {backgroundImage:'url('+list.bg_image+')'} : {backgroundColor:list.bg_color}">
                        <div class="tpl-shop-title"></div>
                        <div class="tpl-shop-avatar">
                            <img :src="list.logo" alt="头像">
                        </div>
                    </div>
                    <div class="tpl-shop-content">
                        <ul class="clearfix">
                            <li class="js-order">
                                <a href="/shop/order/index/{!!$wid!!}">
                                    <span class="count user"></span>
                                    <span class="text">我的订单</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-if="list['type']=='goods'|| list['type']=='goods_group'">
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
                                    <p class="goods-price-taobao " v-html="good[0]['price']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                        <li class="js-goods-card goods-card small-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-if="good[1]">
                            <a :href="good[1].url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" data-src="{{ config('app.source_url') }}shop/images/FqEKBL3zUtFZk1meW6aOxeL12Yoh.png?imageView2/2/w/280/h/280/q/75/format/webp" :src="good[1]['thumbnail']">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class=" goods-title " v-html="good[1]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[1]['info']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[1]['price']"></em></p>
                                    <p class="goods-price-taobao " v-if="good[1]['oprice'] != 0" v-html="good[1]['oprice']"></p>
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
                                    <p class="goods-price-taobao ">100</p>
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
                                    <p class="goods-price-taobao" v-html="good.price"></p>
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
                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail">
                                </div>
                                <div class="info" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class="goods-title" v-html="good.name"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em></p>
                                    <p class="goods-price-taobao" v-html="good.price"></p>
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
                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail">
                                </div>
                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">
                                    <p class=" goods-title " v-html="good.name"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good.info"></p>
                                    <p class="goods-price">
                                        <em v-html="good.price"></em>
                                    </p>
                                    <p class="goods-price-taobao" v-html="good.price">100</p>
                                </div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['showSell'] && list['cardStyle'] != 4"></div>
                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list['cardStyle'] == 4">我要抢购</div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                    </ul>
                    <!-- 小图模式 -->
                </div>
                <!-- 富文本编辑器 -->
                <div class="custom-richtext js-custom-richtext js-lazy-container" v-if="list['type']=='rich_text'">
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
                            <span class="custom-title-link" v-if = "list.titleStyle == 1 && list.linkTitle">
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
                    <form action="" method="GET">
                        <input type="text" class="custom-search-input" name="q" placeholder="搜索商品" value="">
                        <button type="submit" class="custom-search-button">搜索</button>
                    </form>
                </div>
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
                                        <em v-html="good[0]['price']"></em></p>
                                    <p class="goods-price-taobao " v-html="good[0]['price']"></p>
                                </div>
                                <div class="goods-buy info-no-title" v-bind:class="{'btn1':list['btnStyle']=='1','btn2':list['btnStyle']=='2','btn3':list['btnStyle']=='3','btn4':list['btnStyle']=='4'}" v-show="list['showSell']"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </a>
                        </li>
                        <li class="js-goods-card goods-card small-pic" v-bind:class="{'card':list['cardStyle']=='1','normal':list['cardStyle']=='3'}" v-if="good[1]">
                            <a :href="good[1].url" class="js-goods link clearfix">
                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                    <img class="goods-photo js-goods-lazy" data-src="{{ config('app.source_url') }}shop/images/FqEKBL3zUtFZk1meW6aOxeL12Yoh.png?imageView2/2/w/280/h/280/q/75/format/webp" :src="good[1]['thumbnail']">
                                </div>
                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">
                                    <p class=" goods-title " v-html="good[1]['name']"></p>
                                    <p class="goods-sub-title c-black hide" v-html="good[1]['info']"></p>
                                    <p class="goods-price">
                                        <em v-html="good[1]['price']"></em></p>
                                    <p class="goods-price-taobao " v-if="good[1]['oprice'] != 0" v-html="good[1]['oprice']"></p>
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
                                    <p class="goods-price-taobao ">100</p>
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
                                    <p class="goods-price-taobao" v-html="good.price"></p>
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
                                    <p class="goods-price-taobao" v-html="good.price"></p>
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
                                    <p class="goods-price-taobao" v-html="good.price">100</p>
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
                <good-group v-if="list.type == 'good_group' && (list.top_nav.length || list.left_nav.length)" :content="list"></good-group>
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
        <!-- 左侧二维码 -->
        <div class="content-sidebar">
            <div class="sidebar-section qrcode-info">
                <div class="section-detail">
                    <p class="text-center shop-detail">
                        <strong>手机扫码访问</strong></p>
                    <p class="text-center weixin-title">微信“扫一扫”分享到朋友圈</p>
                    <p class="text-center qr-code">
                        {!! QrCode::size(150)->generate(URL("/shop/group/detail/$wid/$id")) !!}
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

<!-- 底部 结束
<!-- 当前页面js -->
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/static/js/swipe.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var wid = {!!$wid!!};
    var id = {!! $id !!}
    var dataList = {!! json_encode($data) !!};
</script>
<script src="{{ config('app.source_url') }}shop/js/groupDetail.js"></script>
</body>
</html>
