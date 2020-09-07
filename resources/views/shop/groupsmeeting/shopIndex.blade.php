<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=yes" />
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <script type="text/javascript">
        var timestamp=new Date().getTime();
    </script>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/store_index.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/product_detail.css" />
    <script src="{{ config('app.source_url') }}shop/js/html5media.js"></script>
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
        /* 弹窗 */
        .pop_up{
            position:fixed;
            top:0;
            left:0;
            height:100%;
            width:100%;
            z-index: 11111111;
        }
        .pop_up .shade{
            background-color:rgba(0,0,0,.8);
            height:100%;
            width:100%
        }
        .code_step{
            text-align:left;
            margin:0 auto;
            font:16px/20px '微软雅黑'
        }
        .pop_up .pop_content{
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 100;
            height: 400px;
            width: 80%;
            border-radius: 5px;
            background: white;
            padding: 20px;
        }
        .pop_up .attend{
            font-weight:bold;
            padding-top:20px;
        }
        .code_tip{
            font:18px/40px '微软雅黑';
            font-weight:normal
        }
        .pop_up .title{
            text-align:center;
            font:20px/30px "微软雅黑";
            border-bottom:1px solid #e6e6e6;
            padding-bottom:10px;
            font-weight:bold
        }
        .pop_up .pop_text{
            font:14px/25px "微软雅黑";
            font-weight:bold
        }
        .pop_up .qrcode>div{
            text-align: center;
            display: flex;
            flex-direction: column;
            
        }
        .pop_up .qrcode>.qwrap{
            padding-bottom:5px;
            border-bottom:1px solid #e6e6e6
        }
        .pop_up .qrcode>div img{
            width: 200px;
            height: 200px;
            border: none;
            outline: none;
            margin: 0 auto;
        }
        .pop_up .qrcode_tip{
            color: #4d4d4d;
            font-size: 18px;
            display: block;
            font-weight: bold;
        }
        .pop_up .btn_wrap{
            position:absolute;
            bottom:10px;
            left:0;right:0;
            text-align:center
        }
        .pop_up .btn_wrap>.btn{
            width:170px;
            font:16px/40px "微软雅黑";
            color:white;
            background:#B0282C;
            border-radius:10px;
        }
        .pop_up .close_btn{
            position:absolute;
            right:10px;
            top:3px;
        }
        .pop_up .info p{
            text-align: center;
            font-size: 20px;
        }
        .pop_up .close_btn img {
            width: 22px;
            position: relative;
            top: 5px;
        }
        .pop_up .tip_bg{
            padding: 0;
            border-radius: 14px;
            height:300px;
        }
        .pop_up .tip_bg .tip_action_title{
            text-align: center;
            padding-top: 20px;
        }
        .pop_up .tip_bg .tip_action_title a {
            display: inline-block;
            padding: 15px 50px;
            background: #B1292D;
            border-radius: 22px;
            color: #fff;
            margin: 0 auto;
        }
        .pop_up .tip_bg .xx_close{
            width: 31px;
            position: absolute;
            top: 20px;
            right: 30px;
        }
        .pop_up .tip_row{
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 100;
            height:50%;
            border-radius: 5px;
            padding: 20px;
        }
        .pop_up .tip_row .tip_bg1{
            width: 88%;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }
        .pop_up .tip_row_a{
            position: absolute;
            width: 43%;
            height: 70px;
            left: 28%;
            bottom: 21%;
        }
        .pop_up .tip_bg .tip_title{
            text-align: center;
            font-size: 16px;
            color: #333;
            padding: 10px;
        }
        .pop_up .tip_bg .tip_title span{
            color:#F33A40;
        }
    </style>

</head>
<body>
<div class="container" id="container" :style="{background:bg_color}">
    <div class='topNav' v-cloak v-if='topNav_flag' :style="{background:topNav_color.background_font_color}" style="font-size: 0">
        <ul ref='topNav_ul' id='ul_box'>
            <li ref='list_nav' v-for='(item,index) in topNav'>
                <span :class='{"active_a":topNav_index == index}' @click='getUrl(item,index)'>@{{item.title}}</span>
            </li>
        </ul>
    </div>
    <div class="content no-sidebar">
        <div class="content-body js-page-content">
            <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>
                <!-- 官网模板2 -->
                <guan-text v-if="list['type']=='imageTextModel'" :list="list"></guan-text>
                <!-- 官网模板 -->
                <guan-wang v-if="list['type']=='bingbing'" :content="list"></guan-wang>
                <!-- 美妆小店头部 -->
                <guan-header v-if="list['type']=='header'" :list="list"></guan-header>
                <goods v-if="list['type']=='goods'" :list="list"></goods>
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
                <good-group v-if="list.type == 'good_group' && (list.top_nav.length || list.left_nav.length)" :content="list" v-on:transfer="setGoodData"></good-group>
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
    <!--积分弹窗-->
	<div class="jifen_tc">
		<div><img src="{{ config('app.source_url') }}shop/images/jifentc.png" width="53px" height="55px" /></div>
		<p>积分+<span>5</span></p>
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
            <!-- <div class="bottom-padding"></div> -->
            <div class="confirm-action content-foot clearfix">
                <div class="big-btn-2-1">
                    <a href="javascript:;" class="js-mutiBtn-confirm cart big-btn orange-btn vice-btn">加入购物车</a>
                    <a href="javascript:;" class="js-mutiBtn-confirm confirm big-btn red-btn main-btn">立即购买</a>
                </div>
            </div>
        </div>
    </div>
    <!-- 弹窗 -->
    <div class="pop_up" v-if="show_tip">
        <div class="shade" style="background-color: rgba(0,0,0,0.6);">
        </div>
        <div class="tip_bg">
            <div class="tip_row">
                <img class="tip_bg1" src="{{ config('app.source_url') }}shop/images/tip_bg1.png">
                <img @click="hideModel" class="xx_close" src="{{ config('app.source_url') }}shop/images/xx2.png">
                <a class="tip_row_a" href="/shop/meeting/groups/showMyGroups/{{session('wid')}}"></a>   
            </div>
        </div>
    </div>
    <!--add by 韩瑜 2018-8-2 拆红包弹框-->
	<div class='bouns_tip' v-if="bonusShow" v-cloak>
	  <div class='bouns_box' @click="getBouns">
	    <div class='bouns_text'>
	      <div class='bouns_text_tip'>恭喜您获得神秘红包一个！</div>
	      <div class='bouns_text_msg'>
	      	<div v-text="activity_title"></div>
	      </div>
	    </div>
	    <div class='bouns_close' @click.stop='closeBouns'></div>
	  </div>
	</div>
	<!--红包右下角图标-->
	<div class='bonusShow_tip' @click="showBonus" v-if="bonusShow_tip" v-cloak></div>
	<!--end-->
    
   
</div>
@include('shop.common.footerMeeting')
<!-- 当前页面js -->
<script>
	var APP_HOST = "{{ config('app.url') }}";
    var APP_IMG_URL = "{{ imgUrl() }}";
    var APP_SOURCE_URL = "{{ config('app.source_url') }}";
    var CHAT_URL = "{{config('app.chat_url')}}";
    var CDN_IMG_URL = "{{config('app.cdn_img_url')}}";
    var mid = '{{ session("mid") }}';
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var host ="{{ config('app.url') }}";
    var id = "{!!$wid!!}";
    var wid = "{!!$wid!!}";
    var imgUrl = "{{ imgUrl() }}";
    var videoUrl = "{{ videoUrl() }}";
    var isBind = {{$__isBind__}};
    var isAutoFrame = {{$isAutoFrame}};
</script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-lazyload.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script src="{{ config('app.source_url') }}shop/js/store_ptuan_index.js?234"></script>
<script type="text/javascript"> 
    var timestamp2=new Date().getTime();
    //console.log(timestamp2-timestamp);
    //微信分享
	$(function(){  	
		var $jifen_tc = $('.jifen_tc');	
		function jifentcShow(data){
    		$jifen_tc.find('p').find('span').html(data);
			$jifen_tc.show();
    	}	
    	function jifenAjax(){
    		$.ajax({
				type:"get",
				data:{},
				url:"/shop/point/addShareRecord/"+id,
				dataType:"json",
				headers:{
					'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
				},
				success:function(data){
					if(data.errCode == 3 || data.errCode == 1 || data.errCode == 2){
						return false;
					}else{
						jifentcShow(data.data);
						setTimeout(function(){
							$jifen_tc.hide();
						},3000)
					}
				},
				eerror:function(data){
					tool.tip(data.errMsg);
				}
			});
    	}
	})
</script>

<!-- 主体内容 结束 -->
@if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
@endif
@if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
@endif
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
    // 点击图片查看大图 add by 黄新琴 2018/9/3
    $('body').on('click','.J_parseImg',function(){
        var nowImgurl = $(this).data('src');
        wx.previewImage({
            "urls":[nowImgurl],
            "current":nowImgurl
        });
    });
    /*
    * @auther 黄新琴
    * @desc 富文本图片点击放大
    * @date 2018-10-18
    * */
    $('body').on('click','.js-custom-richtext',function(){
        var imgs = [];
        var imgObj = $(this).find('img');
        for(var i=0; i<imgObj.length; i++){
            imgs.push(imgObj.eq(i).attr('src'));
            imgObj.eq(i).click(function(){
                var nowImgurl = $(this).attr('src');
                wx.previewImage({
                    "urls":imgs,
                    "current":nowImgurl
                });
            });
        }
    });
    // 微信分享
    $(function(){

        $.get("/shop/isSubscribe",function(data){
            if(data.status == 1){
                if(data.data.subscribe == 0)
                {
                    $('.top_attention').text('关注我们');
                    $('.top_attention').removeClass('hide');
                }
            }
        });

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
                    $.get("/shop/point/addShareRecord/{{ $wid }}",function(data){
                        console.log(data);
                    });
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
                    $.get("/shop/point/addShareRecord/{{ $wid }}",function(data){
                        console.log(data);
                    });
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
                    $.get("/shop/point/addShareRecord/{{ $wid }}",function(data){
                        console.log(data);
                    });
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
                    $.get("/shop/point/addShareRecord/{{ $wid }}",function(data){
                        console.log(data);
                    });
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
</body>
</html>






