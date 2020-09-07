@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/goods_62d5db3e3f0f2435e941566b8d882e5d.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/showEventDetail.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/shareeventPop.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?t=123">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <style type="text/css">
        .container{background-color: #f5f5f5;}
        .js-footer{margin-bottom:51px;}
        .c-bg-gray{
            background:#c9c9c9 !important;
        }
        .c-bg-gray::after{border-top:none;}
        .hexiao{position:absolute;right:10px;width:60px !important;top: 20px;}
        /*客服弹窗*/
        .weui-mask {
            background: rgba(0,0,0,.6);
        }
        .weui-mask, .weui-mask_transparent {
            position: fixed;
            z-index: 1000;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
        }
        .weui-mask {
            z-index: 1000;
        }
        .weui-mask.weui-mask--visible {
            opacity: 0.5;
            visibility: visible;
        }
        .weui-actionsheet {
            position: fixed;
            left: 0;
            bottom: 0;
            -webkit-transform: translateY(100%);
            transform: translateY(100%);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            z-index: 5000;
            width: 100%;
            background-color: #e6e6e6;
            -webkit-transition: -webkit-transform .3s;
            transition: -webkit-transform .3s;
            transition: transform .3s;
            transition: transform .3s,-webkit-transform .3s;
        }
        .weui-actionsheet_toggle {
            -webkit-transform: translate(0);
            transform: translate(0);
        }
        .weui-actionsheet {
            z-index: 200000;
        }
        .weui-actionsheet .weui-actionsheet__title {
            padding: 8px 0;
            text-align: center;
            font-size: 16px;
            background-color: #fff;
            position: relative;
            height: 34px;
            color: #4c4c4c;
            line-height: 34px;
        }
        .weui-actionsheet .weui-actionsheet__title:after {
            content: " ";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1px;
            border-top: 1px solid #d9d9d9;
            color: #d9d9d9;
            -webkit-transform-origin: 0 100%;
            transform-origin: 0 100%;
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }
        .weui-actionsheet__menu {
            background-color: #fff;
        }
        .weui-actionsheet__cell {
            position: relative;
            padding: 10px 0;
            text-align: center;
            font-size: 18px;
            height: 30px;
            line-height: 30px;
        }
        .weui-actionsheet__cell a{
            color: #1a1a1a;
        }
        .color-primary {
            color: #04BE02;
        }
        .color-warning {
            color: #f60;
        }
        .weui-actionsheet__cell:before {
            content: " ";
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            height: 1px;
            border-top: 1px solid #d9d9d9;
            color: #d9d9d9;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            -webkit-transform: scaleY(.5);
            transform: scaleY(.5);
        }
        .color-danger, .color-error {
            color: #f6383a;
        }
        .weui-actionsheet__action {
            margin-top: 6px;
            background-color: #fff;
        }

        /* 此页面弹窗修改 */
        .sku-box-shadow{overflow:visible}
        [v-cloak]{display:none!important;}
        
        .swiper-pagination-bullet {
            opacity: .4;
        }
        .swiper-pagination-bullet-active {
            opacity: 1;
            background: #000;
        }
    </style>
@endsection
@section('main')
    <div class="container wap-goods internal-purchase" id="container" style="min-height: 617px;">
        <!-- 弹幕 -->
        <div class="hint flex_star" v-if="topTipList != null" v-cloak>
            <img :src="topTipList.avatar_url?topTipList.avatar_url:(host + 'static/images/customer_service.jpg')" alt=""/>
            <span>[[topTipList.nick_name]],[[topTipList.sec]]秒前参与了享立减活动</span>
        </div>
        <!-- 弹幕 -->
        <input id="wid" type="hidden" value="{{$shop['id']}}">
        <input id="pid" type="hidden" value="{{$product['id']}}" >
        <!-- 广告业添加开始 -->
        <div class="pc_ad_setting" v-if= "productAdPosition == 1">
            <custom-template :lists= "productAd" :host="host" :sid="shopId"></custom-template>
        </div>
        <!-- 广告页添加结束 -->
        <div class="content no-sidebar">
            <div class="content-body">
                <!--轮播图-->
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @forelse($product['shareEventInfoImg'] as $value)
                            <div class="swiper-slide" style="text-align:center"> 
                                <img class="" src="{{ imgUrl($value) }}">
                                @if($product['is_hexiao'] == 1 && (strtotime($product['hexiao_start'].'00:00:00') <= time() && time() <= strtotime($product['hexiao_end'].'23:59:59')))
                                <img src="{{ config('app.source_url') }}shop/images/hexiao.png" class="hexiao">
                                @endif
                            </div>
                            @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <!-- 倒计时 -->
                <!--author 韩瑜  date 2018-6-26-->
                <div class='timeout' v-cloak>
                    <p class="countdown" v-cloak>[[timeOver==0?'距开始':timeOver==1?'距结束':'已结束']]<span v-text="days"></span>天<span v-text="hours"></span>时<span v-text="minutes"></span>分<span v-text="seconds"></span>秒</p>
                </div>	
                <div class="goods-header">
                    <div class='goods_rgt'>
                        <img class='goods_share' src='{{ config('app.source_url') }}shop/images/fx@2x.png' v-on:click="dialogShow"></image>
                        <span class='mtop5'>分享</span>
                        <!--add by 韩瑜 2018-9-4 收藏按钮-->
                        <span class="collect" @click="collect" v-if="!isFavorite" v-cloak>
                            <img src="{{ config('app.source_url') }}shop/images/nofavorite.png"/>
                            <p class="collect-word">收藏</p>
                        </span>
                        <span class="collect" @click="collectcancel" v-if="isFavorite" v-cloak>
                            <img src="{{ config('app.source_url') }}shop/images/isfavorite.png"/>
                            <p class="collectcancel-word">已收藏</p>
                        </span>
                        <!--end-->
                    </div>
                    <h2 class="title">{{$product['title']}}</h2>
                    <h3 class="subtitle" v-text="product_subtitle"></h3>
                    <div class="goods-price ">
                        @if(empty($product['is_price_negotiable']))
                            <div class="current-price abatement">
                                <span>￥</span>
                                <span class="huiyuanjia">{{$product['price']}}
                            </div>
                            @if( $product['oprice'] > 0 )
                                <div class="original-price">原价:￥{{$product['oprice']}}</div>
                            @endif
                        @else
                        @endif
                    </div>
                    
                    <div class="stock-detail">
                        <div class='goods_bom'>
                            <div class='goods_bom_tex'>享立减</div>
                            <div class='goods_bom_wec'>分享给朋友，1个好友点击减<span v-text="unitAmount"></span>元</div>
                        </div>
                        
                    </div>
                </div>
                @if($product['sku_flag'] == 1)
                    <div class='selectSku box_bottom_1px'>
                        <div>选择规格</div>
                        <div class='skuArrow'></div>
                    </div>
                @endif
                <!-- 喊好友减钱 -->
                <!--author 韩瑜  date 2018-6-25-->
                <div class="share_box_top" >
                    <div class="partake_con" v-if="count != 0"><!--有人帮1，无人帮0-->
                        <!--点赞者看到-->			    		
                        <div class="partak_top" v-if="isShare == 0">共
                            <span v-text="total"></span>人帮助
                            <div class='sharer_img_head'>
                                <img :src="headURL" /><!--分享者头像-->
                            </div>
                            <span v-text="sharer" class="write_hidden"></span>助减
                            <span v-text="alllowerPrice"></span>元
                        </div>
                        <!--分享者看到-->
                        <div class='partak_top' v-if="isShare == 1">已减
                            <span class='partak_reduce' v-text="alllowerPrice"></span>元
                        </div>
                        
                        <!--参与人员-->
                        <div class="partake_box">
                            <!--小于6人-->
                            <div class='partake_six' v-if="total < 6">
                                <div class='partake_for' v-for='item in member'>
                                    <div class="partake_peo marght10"><!--循环体-->
                                        <div class="partake_price"><span v-text="'-'+unitAmount"></span></div>
                                        <div class='z_ovhidden partake_ovre' v-on:click='shareMemberClick'>
                                            <img :src="item.avatar_url" /><!--点赞者头像-->
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <!--大于6人-->
                            <div class='partake_fifteen' v-if="total > 6" v-on:click='shareMemberClick'>
                                <div class='partake_for_big' v-for='item in member'>
                                    <div class='partake_peo' ><!--循环体-->
                                        <div class='partake_price'><span v-text="'-'+unitAmount"></span></div>
                                        <div class='z_ovhidden partake_ovre'>
                                            <img :src="item.avatar_url" /><!--点赞者头像-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 不足6人显示空位 -->
                            <div v-if="total < 6">
                                <div class='partake_peo partake_nopeo'>?</div>
                            </div>
                            <!-- 参与人数 -->
                            <div class='partake_15peo' v-if="total > 6" bindtap='peo_show'>
                                <div>等<span v-text="total"></span>人</div>
                            </div>
                        </div>
                    </div>
                  <form>
                    <div class='sharing' v-on:click="dialogShow">
                      <img src='{{ config('app.source_url') }}shop/images/wx@2x.png' />
                      <span>喊好友来减钱</span>
                    </div>
                  </form>
                  <div class='refresh_vi' v-on:click='min_refresh'>
                    <img class='refresh_img' src='{{ config('app.source_url') }}shop/images/refresh@3x.png' />
                    <span class='refresh_tex'>我的进度</span>
                  </div>
                </div>
            
                <!-- 享立减活动 -->
                <!--author 韩瑜  date 2018-6-26-->
                <div class='active_share'>
                  <div class='active_tex'>
                    <div class='active_til'>享立减活动</div>
                    <div class='active_rul' v-on:click='rul_show'>规则详情
                      <img class='partake_jinru'  src='{{ config('app.source_url') }}shop/images/rule-arrow.png' />
                    </div>
                  </div>
                  <div class="share_rule_img">
                      <img :src="ruleImg" v-if="ruleImg">
                  </div>
                </div>
                <div class="js-detail-container">
                    <!-- 商品详情 -->
                    <div class='goods-detail-wrap'>
                      <div class='goods-detail-title'>
                        <img src="{{ config('app.source_url') }}shop/images/biaoti2@2x.png" class='goods-detail-icon'>
                      </div>
                        <div class="js-part js-goods-detail goods-tabber-c" data-type="goods">
                            <!-- 商品的富文本  自定义组件的添加开始 -->
                            <div class="pc_product_setting">
                                <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
                            </div>
                            <!-- 商品的富文本  自定义组件的添加结束 -->
                            <!-- 广告业添加开始 -->
                            <div class="pc_ad_setting" v-if= "productAdPosition == 2">
                                <custom-template :lists= "productAd" :host="host" :sid="shopId"></custom-template>
                            </div>
                            <!-- 广告页添加结束 -->
                        </div>
                      <div class='goods-detail-content'>
                        <!-- <div class="goods-detail-content-image" v-html="productDetailImage">				        	
                      </div> -->
                      </div>
                    </div>
                </div>
            </div>
            <div class="js-bottom-opts js-footer-auto-ele bottom-fix">
                    <div class="responsive-wrapper boom_share">
                        @if(empty($product['is_price_negotiable']))
                            <!--author 韩瑜  date 2018-6-28-->
                            <div class="mini-btn-2-1" style="width: 120px;">
                                <div class=" btn_bottom btn_bottom_left" style="width: 60px;" v-on:click="index_to">
                                    <img src="{{ config('app.source_url') }}shop/images/sy@2x.png" />
                                </div>
                @if($reqFrom == 'aliapp')
                    <a style="display:inline-block" href="{{config('app.chat_url')}}/zfb/kefu?productName={{urlencode($product['title'])}}&productImg={{ imgUrl($product['img']) }}&productPrice={{$product['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$product['id'].'&type=8'.'&activityId='.$activityId)}}&activityId={{ $activityId or 0 }}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}">
                    @else
                        <a href="{{config('app.chat_url')}}/#/kefu?productName={{urlencode($product['title'])}}&productImg={{ imgUrl($product['img']) }}&productPrice={{$product['showPrice']}}&userId={{session('mid')}}&shopId={{session('wid')}}&productLink={{urlencode('host='.config('app.url').'&wid='.session('wid').'&id='.$product['id'].'&type=8'.'&activityId='.$activityId)}}&activityId={{ $activityId or 0 }}&username={{$member['nickname']}}&headurl={{$member['headimgurl']}}&shopName={{$shop['shop_name']}}&shopLogo={{imgUrl()}}{{$shop['logo']}}&sign={{md5(session('wid').session('mid').'huisou')}}">
                @endif
                                

                                       <div class=" btn_bottom" style="width: 60px;">
                                       @if($reqFrom == 'aliapp')
                                       <img src="{{ config('app.source_url') }}shop/images/alikf.png" />
                                        @else
                                        <img src="{{ config('app.source_url') }}shop/images/kf@2xx.png" />
                                        @endif
                                        <span class="news-num hide"></span>
                                    </div>
                                </a>
                            </div>
                                <a href="javascript:;" class="js-buy-it boom_buy">
                                    <p class="boom_price" v-if="sharer.length && isShare == 0">
                                        <span v-text="'￥'+price"></span>
                                    </p>
                                    <p class="boom_price" v-if="( (!sharer.length) && isShare == 0 ) || isShare == 1">
                                        <span v-text="'￥'+now_price"></span>
                                    </p>
                                    <p>立即购买</p>
                               </a>
                        @else

                           @endif
                    </div>
            </div>
            @if(!empty($more))
            <div class="js-recommend">
                <div class="u-like-title">
                    <div class="u-like-line"></div> 
                    <div class="u-like-icon"></div> 
                    <p class="u-like-tips">为您推荐</p> 
                    <div class="u-like-line"></div>
                </div>
                <div class="js-recommend-goods-list">
                    <ul class="js-goods-list sc-goods-list pic clearfix size-1 " data-size="1" data-showtype="card" style="visibility: visible;">
                        <!-- 商品区域 -->
                        <!-- 展现类型判断 -->
                        <!--author 韩瑜  date 2018-6-25-->
                        <li class="js-goods-card goods-card small-pic card " v-for="item in recommend_product">
                            <a style='border: none' :href="host+'/shop/product/detail/'+wid+'/'+item.product_id+'?activityId='+item.id" class="js-goods link clearfix">
                                <div class="photo-block">
                                    <img class="goods-photo js-goods-lazy test-lazyload" :src="host+item.activityImg">
                                </div>
                                <div class="info clearfix info-title info-price btn0">
                                    <p class=" goods-title " v-text="item.name"></p>
                                    <p class="goods-price">
                                        <em v-text="'￥'+item.price"></em>
                                    </p>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <p class="center" style="margin: 10px 0 20px;">
                    <a href="/shop/index/{{$shop['id']}}" class="center btn btn-white btn-xsmall font-size-14 " style="padding:8px 26px;">进店逛逛&gt;</a>
                </p>
            </div>
            @endif
        </div>
        <!--author 韩瑜  date 2018-6-26-->
        <!--分享弹窗-->
        <div v-show='shareDialogShow' class='shareDialog' v-on:click='shareCancle' >
            <div class='shareDialogBoard' ></div>
            <div class='shareDialogDiv' >
                <img src="{{ config('app.source_url') }}shop/images/share2@2x.png" class="share_img"/>
            </div>
        </div>
        <!-- 规则弹窗 -->
        <div class='rul_tip' v-if="rulshow" v-cloak>
            <div class='rul_tip_co' v-on:click='rul_show_no'></div>
            <div class='rul_con'>
              <div class='rul_hr'>
                <div class='rul_img'></div>
                <div class='rul_tli' v-text="ruleTitle"></div>
                <div class='rul_img'></div>
              </div>
              <div class='rul_ment' v-html="ruleContent"></div>
              <div class='rul_know' v-on:click='rul_show_no'>我知道了</div>
            </div>
        </div>
        <!--author 韩瑜  date 2018-6-28-->
        <!--所有参与者弹框-->
        <div class='share_Member' v-if="shareMember" v-cloak>
            <div class='share_Member_co' v-on:click='shareMemberCancle'></div>
            <div class="share_Member_content">
                <div class="share_Member_content_title">
                    <div class="button" @click="shareMemberCancle">&times;</div>
                    <h3>参与享立减好友</h3>
                    <h4>共<span v-text="total"></span>个好友参与</h4>
                </div>
                <div class="share_Member_content_list">
                    <div class="share_Member_wrap" v-for='item in member'><!--循环体-->
                        <div class='share_Member_wrap_head'>
                            <img :src="item.avatar_url" /><!--点赞者头像-->
                        </div>
                        <div class="share_Member_wrap_name" v-text="item.nick_name"></div>
                        <div class="share_Member_wrap_time"><span v-text="item.created_at"></span>&nbsp;&nbsp;助减</div>
                    </div>
                </div>
            </div>
        </div>
         <!-- 进入页面的弹窗 -->
        <div id="pop_info" v-if="popFlag" v-cloak>
            <div class="info_content" @click="back_index">
                <div class="pop_info_shade" v-if="popShadow" @click="allClose"></div>
                <div class="content_end Pcontent" v-if="theActEnd">
                    <p>活动已结束，去首页逛逛~~</p>
                    <div class="content_bottom">
                        <button>去首页逛逛</button>
                    </div>
                </div>
                <div class="content_end Pcontent" v-if="theNotStart">
                    <p>活动尚未开始，去首页逛逛~~</p>
                    <div class="content_bottom">
                        <button>去首页逛逛</button>
                    </div>
                </div>
                <div class="content_share_partner Pcontent" v-if="isShare == 0 && !isComplete && isExpire==0">
                    <div class="content_top">
                        <p class="image_user">
                            <img :src="headURL" alt=""/>
                        </p>
                        <p class="text_content">您已经帮“[[sharer]]”</p>
                        <p class="text_content">助减了<span v-text="unitAmount"></span>元钱</p>
                    </div>
                    <div class="content_bottom">
                        <button @click="allClose">我知道了</button>
                    </div>
                </div>
                <div class="content_share_owner Pcontent" v-if="isComplete">
                    <div class="content_top">
                        <p class="image_user"><img :src="headURL" alt=""/></p>
                        <p class="text_content" v-if="isShare == 1">您的朋友已帮您完成享立减商品</p>
                        <p class="text_content" v-if="isShare == 0">您的朋友已完成享立减商品</p>
                        <p class="text_tip" v-if="isShare == 1">您可以立即领取哦</p>
                        <p class="text_tip" v-if="isShare == 0">您也可以分享购买哦</p>
                    </div>
                    <div class="content_bottom">
                        <button @click="allClose"> 我知道了</button>
                    </div>
                </div> 
            </div>
        </div>
        <!--add by 韩瑜 2018-9-6 收藏提示-->
        <div class='collecttip iscollecttip'>
            <div >收藏成功</div>
        </div>
        <div class='collecttip nocollecttip'>
            <div>取消成功</div>
        </div>
        <!--end-->
    </div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <!-- 加入购物车弹窗 -->
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/socket.io.js"></script>
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        var mid = '{{ session("mid") }}';
        var shareId = '{{ $shareId or 0 }}';
        var activityId = '{{ $activityId or 0 }}';
        var host = "{{config('app.url')}}";
        var videoUrl = "{{ videoUrl() }}";
        var shop_id = "{{$shop['id']}}";
        var product = {!! json_encode($product) !!};
        var micro_page_notice= {!! $micro_page_notice !!};//公共广告
        var productModel = {!! json_encode($template) !!};   //商品页模板
        var source = '{{ imgUrl() }}';
        var wid = $("#wid").val();
        var _host = "{{ config('app.source_url') }}";
        var cartNum = {{$cartNum}};
        var imgUrl = "{{ imgUrl() }}";
        var sku = {!! $sku !!};//规格字段
        var isBind = {{$__isBind__}};
        if(cartNum == 0){$(".goods-num").hide()}
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/showEventDetail.js"></script>
    <script type="text/javascript">
        //是批发价的时候
        var big_btn_array = $('.big-btn')
        if(big_btn_array.length == 1){
            big_btn_array.css({'width':'100%'})
        }

        //商品简介
        var proIntro = product["summary"]?product["summary"]:'移动电商，会搜云享-{{ $title or '' }}';
        // 懒加载
        $('.test-lazyload').picLazyLoad({
            threshold: 200,
            effect : "fadeIn"
        });
          $(function(){
            // 初始化消息数量socket
            tool.initSocket({
                shopId:"{{session('wid')}}",
                userId:"{{session('mid')}}",
                joinWay:'',
                sign:"{{md5(session('wid').session('mid').'huisou')}}",
                msgCallBack:function(res) {
                    if (res > 0 && res <= 99) {
                        $('.news-num').html(res).removeClass('hide');
                    } else if (res > 99) {
                        $('.news-num').html('99+').addClass('big-num').removeClass('hide');
                    } else {
                        $('.news-num').html('').removeClass('big-num').addClass('hide');
                    }
                }
            })	  		  	
            var $jifen_tc = $('.jifen_tc');	
            function jifentcShow(data){
                $jifen_tc.find('p').find('span').html(data);
                $jifen_tc.show();
            }	
            function jifenAjax(){
                $.ajax({
                    type:"get",
                    data:{},
                    url:"/shop/point/addShareRecord/"+wid,
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
                    error:function(data){
                        tool.tip(data.errMsg);
                    }
                });
            }
       })    
        // 微信分享
        $(function() {
            var url = location.href.split('#').toString();
            $.get("/home/weixin/getWeixinSecretKey", { "url": url }, function(data) {
                if (data.errCode == 0) {
                    wx.config({
                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                        appId: data.data.appId, // 必填，公众号的唯一标识
                        timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                        nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                        signature: data.data.signature, // 必填，签名，见附录1
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
            wxShare();
            function wxShare() {
                if (vm1.$data.shareDetail.product) {
                    var share_title = vm1.share_title || vm1.shareDetail.product.title;
                    var share_desc = vm1.shareDetail.share_desc || vm1.shareDetail.product.subtitle;
                    var share_img = vm1.shareDetail.share_img ? vm1.imgUrl + vm1.shareDetail.share_img : vm1.imgUrl + vm1.shareDetail.product.img; 
                    var share_url = host+'shop/product/detail/'+vm1.wid+'/'+vm1.shareDetail.product.productId+'?activityId='+vm1.activityId+'&shareId='+vm1.mid+'&_share_event_id_='+vm1.activityId+'&_pid_={{session('mid')}}';//何书哲 2018年8月8日 公众号分享添加享立减标志
                    @if($reqFrom == 'aliapp')
                        my.postMessage({share_title:share_title,share_desc:share_desc,share_url:share_url,imageUrl:share_img});
                    @endif
                    @if($reqFrom == 'wechat')
                    wx.ready(function() {
                        //分享到朋友圈 update by 黄新琴 2018/8/13
                        wx.onMenuShareTimeline({
                            title: share_title, // 分享标题
                            desc: share_desc, // 分享描述
                            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                            imgUrl: share_img, // 分享图标
                            success: function() {
                                // 用户确认分享后执行的回调函数
                                // tool.tip("分享成功");
                                $.ajax({
                                    url: '/shop/shareevent/shareRecord',
                                    data:{
                                        share_event_id: activityId
                                    }
                                });
                            },
                            cancel: function() {
                                // 用户取消分享后执行的回调函数
                            }
                        });

                        //分享给朋友 update by 黄新琴 2018/8/13
                        wx.onMenuShareAppMessage({
                            title: share_title, // 分享标题
                            desc: share_desc, // 分享描述
                            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                            imgUrl: share_img, // 分享图标
                            type: '', // 分享类型,music、video或link，不填默认为link
                            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                            success: function() {
                                // 用户确认分享后执行的回调函数
                                // tool.tip("分享成功");
                                $.ajax({
                                    url: '/shop/shareevent/shareRecord',
                                    data:{
                                        share_event_id: activityId
                                    }
                                });
                            },
                            cancel: function() {
                                // 用户取消分享后执行的回调函数
                            }
                        });

                        //分享到QQ update by 黄新琴 2018/8/13
                        wx.onMenuShareQQ({
                            title: share_title, // 分享标题
                            desc: share_desc, // 分享描述
                            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                            imgUrl: share_img, // 分享图标
                            success: function() {
                                // 用户确认分享后执行的回调函数
                                $.ajax({
                                    url: '/shop/shareevent/shareRecord',
                                    data:{
                                        share_event_id: activityId
                                    }
                                });
                            },
                            cancel: function() {
                                // 用户取消分享后执行的回调函数
                            }
                        });

                        //分享到腾讯微博 update by 黄新琴 2018/8/13
                        wx.onMenuShareWeibo({
                            title: share_title, // 分享标题
                            desc: share_desc, // 分享描述
                            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                            imgUrl: share_img, // 分享图标
                            success: function() {
                                // 用户确认分享后执行的回调函数
                                $.ajax({
                                    url: '/shop/shareevent/shareRecord',
                                    data:{
                                        share_event_id: activityId
                                    }
                                });
                            },
                            cancel: function() {
                                // 用户取消分享后执行的回调函数
                            }
                        });
                        wx.error(function(res) {
                            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                            //alert("errorMSG:"+res);
                        });
                    });
                    @endif
                } else {
                    setTimeout(function() {
                        wxShare();
                    }, 50)
                }
            }
        });

        $('.changeColor').on('touchstart',function(){
            $(this).css('background','#f8f8f8')
        })
        $('.changeColor').on('touchend',function(){
            $(this).css('background','#fff')
        })
    </script>
@endsection
