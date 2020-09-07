@extends('shop.common.marketing')
@section('head_css')
<script type="text/javascript" xmlns:v-bind="http://www.w3.org/1999/xhtml">
    var timestamp=new Date().getTime();
</script>
<script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/store_index.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/product_detail.css" />
<script src="{{ config('app.source_url') }}shop/js/html5media.js"></script>
<style type="text/css">
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
@endsection
@section('main')
<div class="container" id="container" :style="{background:bg_color}">
    <div class='topNav' v-cloak v-if='topNav_flag' :style="{background:topNav_color.background_font_color}" style='font-size: 0'>
        <ul ref='topNav_ul' id='ul_box'>
            <li v-for='(item,index) in topNav'>
                <span @click="getUrl(item,index)" :style="topNav_index == index?checked_font_color:{color:topNav_color.font_color}">@{{item.title}}</span>
                <!-- :class='{"active_a":topNav_index == index}' -->
                <!-- :style="topNav_index == index?{topNav_color.checked_font_color}:{color:topNav_color.font_color}" -->
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
                <cmobile :list="list" :reqfrom="reqFrom" v-if="list.type == 'mobile'"></cmobile>
                <!-- 联系方式组件 -->
                <!-- 享立减 -->
                <share-rebate :list="list" v-if="list.type == 'share_goods'"></share-rebate>
                <!-- 享立减 -->
                <!-- 留言板 -->
                <info-board :list="list" v-if="list.type == 'researchVote'"></info-board>
                <info-board :list="list" v-if="list.type == 'researchAppoint'"></info-board>
                <info-board :list="list" v-if="list.type == 'researchSign'"></info-board>
                <!-- 留言板 -->
                <seckill-list :list="list" v-if="list.type == 'seckill_list'"></seckill-list>
                <!-- 分类模板页 -->
                <group-page :list="list" v-if="list.type == 'group_page'"></group-page>
                <!-- 商品分组模板页 -->
                <group-template :content="list" v-if="list.type == 'group_template' && (list.top_nav.length || list.left_nav.length)"></group-template>
            </div>
        </div>
        @if(session('wid') != 3714)
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
        @endif
    </div>
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
            <div class="confirm-action content-foot clearfix">
                <div class="big-btn-2-1">
                    <a href="javascript:;" class="js-mutiBtn-confirm cart big-btn orange-btn vice-btn">加入购物车</a>
                    <a href="javascript:;" class="js-mutiBtn-confirm confirm big-btn red-btn main-btn">立即购买</a>
                </div>
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
<!-- add by 魏冬冬特殊店铺定制 -->
@if(session('wid') == 3714)
<a href="/shop/seckill/detail/3714/4176" style="background:#0A83F9;color:#fff;height:50px;text-align:center;position:fixed;bottom:0px;display:block;line-height:50px;width:100%;z-index:1000" >立即订购</a>
@endif
<!-- end -->
@include('shop.common.footer')
@endsection
@section('page_js')
<!-- 当前页面js -->
<!-- <script src="https://unpkg.com/ajax-hook/dist/ajaxhook.min.js"></script> -->

<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<!-- <script type="text/javascript">
function getQueryString(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
    var r = window.location.search.substr(1).match(reg); 
    if (r != null) return unescape(r[2]); 
    return null; 
} 
Vue.http.interceptors.push((request, next)  =>{
  //登录成功后将后台返回的TOKEN在本地存下来,每次请求从sessionStorage中拿到存储的TOKEN值
  // let TOKEN=sessionStorage.getItem('STORAGE_TOKEN');
  // console.log(5545)
  var aliToken = getQueryString('aliToken');
  if(aliToken){
    //如果请求时TOKEN存在,就为每次请求的headers中设置好TOKEN,后台根据headers中的TOKEN判断是否放行
    request.headers.set('aliToken',aliToken);
  }
  next((response) => {
    // console.log(response)
    return response;
  });
});
</script> -->
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var host ="{{ config('app.url') }}";
    var id = "{!!$wid!!}";
    var wid = "{!!$wid!!}";
    var imgUrl = "{{ imgUrl() }}";
    var videoUrl = "{{ videoUrl() }}";
    var isBind = {{$__isBind__}};
    var mid = '{{ session("mid") }}';
    var reqFrom = "{{ $reqFrom }}";
    console.log(host)
    console.log(id);
</script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
@if($reqFrom == 'aliapp')
<script type="text/javascript" src="https://appx/web-view.min.js"></script>
@endif
<script src="{{ config('app.source_url') }}shop/static/js/vue-lazyload.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script src="{{ config('app.source_url') }}shop/js/store_index.js?234"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script type="text/javascript"> 
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
    var timestamp2=new Date().getTime();
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
@endsection