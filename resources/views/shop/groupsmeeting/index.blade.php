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
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/store_index.css">
    <style type="text/css">
        .qqkefu img{width:50px;height:50px;}
        .footer{
            margin-top:20px;
        }
        html {
		    width: 100%;
		    height:auto;
		    overflow-x: hidden;
		}
		body {
		    text-align: left;
		    width: 100%;
		    background: #e9dfc7;
		    overflow-y:scroll;
		}
    </style>
</head>
<body>
<!-- 顶部导航 开始 -->

<!-- 顶部导航 结束 -->
<div class="container" id="container" :style="{background:bg_color}">
    <div class="content no-sidebar">
        <div class="content-body js-page-content">
            <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>
                <!-- 官网模板2 -->
                <guan-text v-if="list['type']=='imageTextModel'" :list="list"></guan-text>
                <!-- 官网模板 -->
                <guan-wang v-if="list['type']=='bingbing'" :content="list"></guan-wang>

                <goods v-if="list['type']=='goods'" :list="list"></goods>
                <!-- 美妆小店头部 -->
                <guan-header v-if="list['type']=='header'" :list="list"></guan-header>
                <!-- 富文本编辑器 -->
                <rich-text v-if="list['type']=='rich_text'" :list="list"></rich-text>
                <!-- 图片广告 -->
                <image-ad v-if="list['type']=='image_ad' && list['images'].length > 0" :list="list"></image-ad>
                <!-- 标题样式 -->
                <title-style v-if="list['type']=='title'" :list="list"></title-style>
                <!-- 进入店铺 -->
                <store-in v-if="list['type']=='store'" :list="list"></store-in>
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
                <!--update by 韩瑜 2018-9-19-->
                <search :list='list' :host="host" :wid='wid' v-if="list.type == 'search'"></search>
                <!--end-->
                <!-- 商品搜索 -->
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
                <!--分类模板页-->
                <group-page :list="list" v-if="list.type == 'group_page'"></group-page>
                <!--商品分组模板页-->
                <group-template :content="list" v-if="list.type == 'group_template' && (list.top_nav.length || list.left_nav.length)"></group-template>
            </div>
        </div>
    </div>
    @include('shop.common.meetingBottom')
    <!-- 客服弹窗 -->
    <div class="weui-mask weui-actions_mask weui-mask--visible" v-if="kefuShow" v-cloak></div>
    <div class="weui-actionsheet  weui-actionsheet_toggle" v-if="kefuShow" v-cloak>
        <div class="weui-actionsheet__title">选择操作</div>
        <div class="weui-actionsheet__menu">
            <div class="weui-actionsheet__cell color-primary">
                <a :href="url">联系客服QQ</a>
            </div>
            <div class="weui-actionsheet__cell color-warning">
                <a :href="telphone">联系客服电话</a>
            </div>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell weui-actionsheet_cancel color-primary" @click="hideKeFu">取消</div>
        </div>
    </div>
    <!-- 客服弹窗 -->
   
</div>
@include('shop.common.footerMeeting')

<!-- 底部 结束
<!-- 当前页面js -->
<script>
    var CDN_IMG_URL = "{{config('app.cdn_img_url')}}";
    var mid = '{{ session("mid") }}';
</script>
<script type="text/javascript">
    var APP_HOST = "{{ config('app.url') }}"
    var APP_IMG_URL = "{{ imgUrl() }}"
    var APP_SOURCE_URL = "{{ config('app.source_url') }}"
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl ="{{ imgUrl() }}";
    var host = "{{ config('app.url') }}";
    var videoUrl = "{{ videoUrl() }}";
    var wid = {!!$wid!!};
    var id = {!! $id !!};
    var mid = {{session('mid')}};
</script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-lazyload.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script src="{{ config('app.source_url') }}shop/js/micropage_index.js"></script>
<script type="text/javascript">
    $('.attention').click(function(){
        $('.follow_us').show();
    });
    $(".code img").click(function(e){
        e.stopPropagation()
    })
    $('.follow_us').click(function(){
        $('.follow_us').hide();
    });
    $(function(){
        $.get("/shop/getApiName",function(data){
            if(data.status == 1){
                $('.code img').attr('src',data.data.url);
                $('.other_opt').text('若无法识别二维码');
                var html = " <p>1.打开微信，点击“公众号”</p>" +
                    "<p>2.搜索公众号："+ data.data.name +"</p>" +
                    "<p>3.点击“关注”，完成</p>";
                $('.opt').html(html);
                $('.set').removeClass('hide');
                $('.noset').addClass('hide');
            }else {
                $('.set').addClass('hide');
                $('.noset').removeClass('hide');
            }
        });

        var url = location.href.split('#').toString();
        $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
            if(data.errCode == 0){
                wx.config({
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: data.data.appId, // 必填，公众号的唯一标识
                    timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                    nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                    signature: data.data.signature,// 必填，签名，见附录1
                    jsApiList: [
                        'checkJsApi',
                        'onMenuShareTimeline',
                        'onMenuShareAppMessage',
                        'onMenuShareQQ',
                        'chooseWXPay'
                    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                });
            }
        })
        if(window.location.search){
            url += '&_pid_='+ '{{ session("mid") }}';
        }else{
            url += '?_pid_='+ '{{ session("mid") }}';
        }
        wx.ready(function () {
            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    //alert('分享成功');
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享给朋友
            wx.onMenuShareAppMessage({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享到QQ
            wx.onMenuShareQQ({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                success: function () {
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                   // 用户取消分享后执行的回调函数
                }
            });

            //分享到腾讯微博
            wx.onMenuShareWeibo({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                success: function () {
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.error(function(res){
                // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
            });
        });
    });

</script>