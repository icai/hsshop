<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?v=123">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/share_preview.css?v=123">
    <style type="text/css">
      .container{
        width:320px;
        margin:0 auto;
      }
    </style>
</head>
<body> 
<div class="container" id="app">
    <div class="detail_top">
      <div class="swiper-container" style="min-height: 200px; max-height: 350px;">
          <div class="swiper-wrapper">
              <div class="swiper-slide" v-for="(item,key) in activityImg" :key="key">
                  <img class="" :src="imgUrl + item" />
              </div>
          </div>
          <!-- 如果需要分页器 -->
          <div class="swiper-pagination"></div>
      </div>
    </div>
    <!-- 商品名称 -->
    <div class='goods_cont' v-if="product != null">
      <div class='goods_box'>
        <div class='goods_lef'>
          <div class='goods_name price_weight' v-text = "product.title"></div>
          <div class='goods_subtitle' v-text="product.subtitle"></div>
          <div class='goods_price'>
            <div class='price_weight goods_ret' v-text = "'￥' + product.price"></div>
            <div class='goods_original'>
              <text>原价:</text>
              <text class='goods_decoration' v-text= "'￥' + product.oprice"></text>
            </div>
          </div>
        </div>
        <form report-submit="true" bindsubmit="getFormId">
          <div class='goods_rgt'>
            <img class='goods_share' src='https://upx.cdn.huisou.cn/wscphp/xcx/images//fx@2x.png'></image>
            <text class='mtop5'>分享</text>
          </div>
        </form>
      </div>
      <div class='goods_bom'>
        <div class='goods_bom_tex'>享立减</div>
        <div class='goods_bom_wec'>分享给朋友，1个好友点击减<span v-text="pageData.unitAmount"></span>元</div>
      </div>
    </div>
    <!-- 喊好友减钱 -->
    <div class="share_box_top">
      <form>
        <div class='sharing'>
          <img src='https://upx.cdn.huisou.cn/wscphp/xcx/images/wx@2x.png' />
          <span>喊好友来减钱</span>
        </div>
      </form>
      <div class='refresh_vi' bindtap='min_refresh'>
        <img class='refresh_img' src='https://upx.cdn.huisou.cn/wscphp/xcx/images/refresh@3x.png' />
        <span class='refresh_tex'>我的进度</span>
      </div>
    </div>

    <!-- 享立减活动 -->
    <!-- x享立减活动 -->
    <div class='active_share'>
      <div class='active_tex'>
        <div class='active_til'>享立减活动</div>
        <div class='active_rul' bindtap='rul_show' wx:if="ruleImg && ruleTitle && ruleContent">规则详情
          <img class='partake_jinru' src='https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/jinru@2x.png' />
        </div>
      </div>
      <div>
        <div class='active_flex'>
          <img src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/aictive_rure.jpg" wx:if="">
        </div>
      </div>
    </div>

    <!-- 商品详情 -->
    <div class='goods-detail-wrap'>
      <div class='goods-detail-title'>
        <img src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/biaoti@2x.png" class='goods-detail-icon'>
      </div>
      <div class='goods-detail-content'>
        <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
      </div>
    </div>

    <!-- 推荐商品开始 -->
    <div class='recommend-wrap'>
      <div class='recommend-title' wx:if="@{{more_list.length>0}}">
        <img src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/tuijian@2x.png" class='recommend-icon'>
      </div>
      <!--团购样式4  -->
      <div class='gp-list-wrap list-4'>
        <div class='gp-list-box'>
          <a class='gp-list-item' v-for="list in recommend" src='javascript:void(0);'>
            <div class='gp-list-img-wrap'>
              <img class='gp-list-img' :src='imgUrl + list.activityImg'>
            </div>
            <div class='gp-list-goods-name' v-if="list.title" v-text="list.title"></div>
            <div class='gp-list-other'>
              <div class='gp-list-price' v-text="'￥' + list.lowerPrice"></div>
            </div>
          </a>
        </div>
      </div>
    </div>
    <!-- 底部 -->
    <div class='boom_share'>
      <a class='boom_sy' bindtap='index_to'>
        <img src='https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/footer/sy@2x.png'>
      </a>
      <a class='boom_kf'>
        @if(!empty($reqFrom) && ($reqFrom == 'aliapp'))
        <img src="{{ config('app.source_url') }}shop/images/alikf.png" />
        @else
        <img src='https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/footer/kf@2xx.png'>
        @endif
      </a>
      <div class='boom_buy' bindtap='groupPurchaseBuy' v-if="product != null">
        <div class='boom_price' v-text="'￥' + product.price"></div>
        <div>立即购买</div>
      </div>
    </div>
  </div>
<!-- 弹框文字结束 -->
<!-- 主体内容 结束 -->
    <script type="text/javascript">
        var APP_HOST = "{{ config('app.url') }}";
        var APP_IMG_URL = "{{ imgUrl() }}";
        var APP_SOURCE_URL = "{{ config('app.source_url') }}";
        var CHAT_URL = "{{config('app.chat_url')}}";
        var wid = "{{session('wid')}}";
    </script>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script>
        
        var _host = "{{ config('app.source_url') }}";
        var host ="{{ config('app.url') }}";
        var imgUrl = "{{ imgUrl() }}";
       
        var isBind = 0;
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js?v=1.00"></script>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}shop/js/share_preview.js"></script>
</body>
</html>