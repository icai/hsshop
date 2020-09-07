@extends('shop.common.template')
@section('head_css')
  <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?v=123"> 
  <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/detail.css?v=1.0.1"> 
  <style type="text/css">
      .custom-image-swiper {
        width: 100%;
        position: relative;
    } 
    .swiper-container {
      width:100%;
    }
    .swiper-slide img{width:100%;height:auto;}
    [v-cloak] { display: none!important; }
  </style>
@endsection
@section('main')
<div id="app" v-cloak>
  <div class="page" v-if="show != ''">
    <div class="detail_top">
      <div class="swiper-container" style="min-height: 200px; max-height: 500px;">
          <div class="swiper-wrapper">
                  <div class="swiper-slide" v-for="swImg in list1.img" @click.stop="lookImg(swImg.img)">
                      <img class="" v-bind:src="imgUrl + '' + swImg.img" />
                  </div>
          </div>
          <!-- 如果需要分页器 -->
          <div class="swiper-pagination"></div>
      </div>
      <div class="goods-price_countdown clearfix" v-cloak>
        <div class="countdown-title" v-if="state == 1" v-cloak>距活动结束仅剩</div>
        <div class="countdown-title" v-if="state == 2" v-cloak>距活动开始仅剩</div>
                    <div class="overview-countdown">
                        <div class="js-time-count-down countdown">
                          <span class="js-span-d" >@{{countdown.day}}</span>
                          <i class="js-i-d" style="font-size:12px;">天</i>
                            <span class="js-span-h">@{{countdown.hour}}</span>
                            时
                            <span class="js-span-m">@{{countdown.minute}}</span>
                            分
                            <span class="js-span-s">@{{countdown.second}}</span>
              秒
                        </div>
                    </div>
                </div>
      <!-- <seckill v-if="list.type == 'marketing_active'&& list.content.length>0" :list = "list"></seckill> -->
    </div>
    <div class="detail_content">
      <div class="content_l">
        <p class="title" v-text="list1.title">黑凤梨 20寸全铝镁合金登机箱</p>
        <p class="sub_title" v-text="list1.subtitle">100%铝镁合金，超薄坚固</p>
        <p class="price" v-if="list1.max" v-html="'￥' + list1.min +'~'+ list1.max + '&nbsp;&nbsp<span>市场价：￥' + listPro.price + '</span>'"></p>
        <p class="price" v-else v-html="'￥' + list1.min + '&nbsp;&nbsp<span>市场价：￥' + listPro.price + '</span>'"></p>
        <!--<p class="people" v-text="'已团：' + list1.pnum + '件 ' + list1.groups_num + '人团'"></p>-->
        <p class="people">已团：<strong v-text="list1.pnum" class="light-num"></strong>件<strong class="groups_num light-num" v-text="list1.groups_num"></strong>人团</p>
      </div>
      <div class="content_r">
        <img src="{{ config('app.source_url') }}shop/images/_share.png" @click="share"/>
        <div>分享</div>
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
    </div>
    <div class='selectSku box_bottom_1px box_top_1px' v-on:click="buyTuan" v-if='list1.product.sku_flag == 1'>
      <div>选择规格</div>
      <div class='skuArrow'></div>
    </div>
    <div class="content1 imageStrip" v-if="listLable.length != 0">
      <div class="imgStrip_bot" v-on:click="fwbzTc" v-if="groupInfo.service_status.length != 0">
        <div class="imgStrip_bot">
          <div class="fwbz" v-text="listLable.title">服务保障：</div>
          <div class="imgStrip_botR">
            <div class="imgStrip_bot_c">
              <div class="" v-for="listL in groupInfo.service_status" v-html="'<span></span>' + listL.title"><span>·</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="content1 howTuan" v-if="list2.num != 0" style="padding-left: 10px;">
        <div class="h_title">
          <div class="how_poeple" v-text="list2.num + '人在开团'">89人在开团</div>
          <div class="more" v-on:click="goMorePin" v-if="list2.num > 2">查看更多</div>
          <img v-if="list2.num > 2" src="{{ config('app.source_url') }}shop/images/jinru@2x.png" />
        </div>
        <div class="tuanList" v-for="(howT,hIndex) in list2Data2">
          <img v-bind:src="howT.headimgurl" />
          <div class="tuanList_c">
            <p class="" v-text="howT.nickname">细雨微微</p>
            <p class="tuanList_cTxt">还差<span v-text="howT.num"></span>人，剩余<span v-text=" surplusTime[hIndex]"></span>结束</p>
          </div>
          <div class="goPin" @click="goTuanDetail(howT,hIndex)">去参团</div>
        </div> 
      </div>
      <div class="content1 evaluate" v-if="listE.num != 0 && listE.num != undefined" v-cloak>
        <div class="h_title">
          <div class="how_poeple" v-text="'全部评价（' + listE.num + '）'"></div>
          <div class="more" @click="goEval">查看评价</div>
          <img src="{{ config('app.source_url') }}shop/images/jinru@2x.png" />
        </div>
        <div class="e_list">
          <div class="e_listTit">
            <img v-bind:src="listEData.headimgurl" />
            <p class="e_name" v-text="listEData.nickname"></p>
            <p v-text="listEData.created_at"></p>
          </div>
          <div class="e_txt" v-text="listEData.content"></div>
          <div class="e_img">
            <img @click.stop="lookImg(evalImg)" v-for="evalImg in listEData.img" v-bind:src="imgUrl + '' + evalImg.m_path" />
          </div>
          <div class="e_ruler" v-text="listEData.spes"></div>
        </div>
      </div>
      <div class="content1" v-if="listProContent != ''" data-type="goods">
              <div class="r_title">
          <img src="{{ config('app.source_url') }}shop/images/biaoti1@2x.png" />
        </div>
              <div class="pc_product_setting">
                  <custom-template :lists= "lists" :host="host" :sid="shopId"></custom-template>
              </div>
          </div>

      
    </div>
      <div class="recommend" v-if="list3.length != 0" @if(in_array(session('wid'),config('app.li_wid')))style="display: none" @endif>
        <div class="u-like-title">
                    <div class="u-like-line"></div> 
                    <div class="u-like-icon"></div> 
                    <p class="u-like-tips">为您推荐</p> 
                    <div class="u-like-line"></div>
                </div>
        <div class="r_cont">
          <div class="r_contList" v-for="rList in list3">
            <div class="rc_top" @click="goShopDetail(rList)">
              <img v-bind:src="imgUrl + '' + rList.img" />
              <p v-if="rList.label != ''" v-text="rList.label">膳魔师制造商</p>
            </div>
            <div class="goods-info-wraper">
              <div class="rc_txt" v-text="rList.title">安全性负离子儿童安全牙刷</div>
              <div class="rc_bot">
                <p v-text="rList.min">￥299</p>
                <div class="rc_img">
                  <img v-for="(rcImg,key) in rList.groups.member" v-if="key<2" v-bind:src="rcImg.headimgurl" />
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>

  <!--头部消息轮播-->
  <div class="hint flex_star" v-if="topTipList != null">
    <img :src="topTipList.headimgurl" alt="">
    <span v-text="topTipList.nickname + '，' + topTipList.sec + '秒前拼单了这个商品'"></span>
  </div>
  
  <!--悬浮按钮-->
  <div class="xuanfu">
    <div class="goTop" v-on:click="goTop">
      <img src="{{ config('app.source_url') }}shop/images/diongbu@2x.png" />
    </div>
  </div>
  <!--遮罩-->
  <div class="zhezhao" v-if="bgZhezhao">
    <div class="share_model">
      <img src="{{ config('app.source_url') }}shop/images/share_bg3.png" />
    </div>
    <div class="close_share" v-on:click="bgClick"></div>
  </div>
  <!--服务保障弹窗-->
  <div v-if="fwbz">
    <div class="zhezhao" @click="closeServerModal"></div>
    <div class="fwbzTc tc">
      <div class="f_tcTit">服务保障</div>
      <div class="f_content">
        <div>
          <div class="f_tcContent" v-html="groupInfo.service_txt">
            全场商品支持配送地区内，免费配送到家
          </div>
        </div>				
      </div>
    </div>
    
  </div>
  <div class="preview_picture" v-if="previewShow" @click="previewHide">
    <div class="board"></div>
    <img :src="imgUrl + '' + previewImg" :style="{top: (pageHeight-100-imgHeight)/2+'px'}" ref="img"/>
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
<!--底部按钮-->
<div class="t_footer" id="tFooter" v-if="show != ''">
  <div class="tf_lBut" @click="goIndex">
    <img src="{{ config('app.source_url') }}shop/images/sy@2x.png?t=123" />
  </div>
  @if ( config('app.chat_url') )
  <div class="tf_lBut">
    <a :href="chatUrl">	
      @if($reqFrom == 'aliapp')
      <img src="{{ config('app.source_url') }}shop/images/alikf.png" />
      @else
      <img src="{{ config('app.source_url') }}shop/images/kf@2xx.png?v=3344" />
      @endif	
      <span class="news-num hide"></span>			
    </a>
  </div>
  @endif
  <div class="oPrice_buy" v-on:click="oneBuy">
    <div class="oPrice" v-text="'￥' + listPro.price">￥999</div>
    <div class="oPrice_txt">原价购买</div>
  </div>
  <div :class="state == 2?'nooneOpen oPrice_buy oneOpen':'oPrice_buy oneOpen' " v-if="list1.is_over == 0" v-on:click="buyTuan"  >
    <div class="oPrice" v-text="'￥' + list1.headMin">￥599</div>
    <div class="oPrice_txt">一键开团</div>
  </div>
</div>
<!--遮罩-->
<div class="zhezhao" v-if="bgZhezhao">
  <div class="share_model">
    <img src="{{ config('app.source_url') }}shop/images/share_bg3.png" />
  </div>
  <div class="close_share" v-on:click="bgClick"></div>
</div>
<div class="pinNow-zhezhao" v-if="mpinDan"  @click="closeMorePin">
  <div class="pinNow" v-if="mpinDan">
    <div class="pin_tit">
      <span></span>
      <p>正在拼单</p>
      <span></span>
    </div>
    <div class="lately">(最近5位)</div>
    <div class="pinList" v-for="(tcPin,tcHList) in list2Data" v-if="tcHList<5" @click="goTuanDetail(tcPin,tcHList)">
      <img v-bind:src="tcPin.headimgurl" />
      <div class="pin_content">
        <div class="pin_content_top"><b v-text="tcPin.nickname">丁香</b><span v-text="'（还差' + tcPin.num + '人）'">（还差2人）</span></div>
        <div class="surpTime" v-text="'剩余' + surplusTime[tcHList]">剩余 10:04:50</div>
      </div>
      <div class="goTuan">去参团</div>
    </div>
  </div>
</div>
@include('shop.common.footer')

@endsection
@section('page_js')
  <script>
    var rule_id="{!!$rule_id!!}";
    var _host = "{{ config('app.source_url') }}";
    var videoUrl = "{{ videoUrl() }}";
      var host ="{{ config('app.url') }}";
      var imgUrl = "{{ imgUrl() }}";
      var isBind = {{$__isBind__}};
      // var isBind = 1;
      var shopId = "{{session('wid')}}"; // 店铺id
      var userId = "{{session('mid')}}"; // 用户id
      var sign = "{{md5(session('wid').session('mid').'huisou')}}"; // 签名
  </script>
  <script src="{{ config('app.source_url') }}shop/js/until.js?v=1.00"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
  <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
  <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
  <script src="{{ config('app.source_url') }}shop/static/js/socket.io.js"></script>
  <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
  <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
  <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
  <!--懒加载插件-->
  <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
  <script>
    // 微信分享
      $(function(){
            $('.attention').click(function(){
                $('.follow_us').show();
            });
            $(".code img").click(function(e){
                e.stopPropagation()
            })
            $('.follow_us').click(function(){
                $('.follow_us').hide();
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
              wxShare();
          })
          
          function wxShare(){
              if(typeof app.list1.share_title !="undefined"){   
                  var share_title =app.list1.share_title ? app.list1.share_title : app.list1.title;
                  var share_desc =app.list1.share_desc ? app.list1.share_desc : app.list1.subtitle;
                  var share_img =app.list1.share_img ?imgUrl + app.list1.share_img : imgUrl + app.list1.img2;
                  var share_url=host+'shop/grouppurchase/detail/'+rule_id+'/{{session('wid')}}?_pid_={{session('mid')}}'
          @if($reqFrom == 'aliapp')
          my.postMessage({share_title:share_title,share_desc:share_desc,share_url:share_url,imgUrl:share_img});
          @endif
          @if($reqFrom == 'wechat')
          wx.ready(function () {
                      //分享到朋友圈
                      wx.onMenuShareTimeline({
                          title: share_title, // 分享标题
                          desc: share_desc, // 分享描述
                          link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                          imgUrl: share_img, // 分享图标
                          success: function () {
                              // 用户确认分享后执行的回调函数

                          },
                          cancel: function () {
                              // 用户取消分享后执行的回调函数
                          }
                      });
  
                      //分享给朋友
                      wx.onMenuShareAppMessage({
                          title: share_title, // 分享标题
                          desc: share_desc, // 分享描述
                          link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                          imgUrl: share_img, // 分享图标
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
                          title: share_title, // 分享标题
                          desc: share_desc, // 分享描述
                          link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                          imgUrl: share_img, // 分享图标
                          success: function () {
                             // 用户确认分享后执行的回调函数
                          },
                          cancel: function () {
                             // 用户取消分享后执行的回调函数
                          }
                      });
  
                      //分享到腾讯微博
                      wx.onMenuShareWeibo({
                          title: share_title, // 分享标题
                          desc: share_desc, // 分享描述
                          link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                          imgUrl: share_img, // 分享图标
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
          @endif
              }else{
                  setTimeout(function(){
                      wxShare();
                  },50)
              }
          }
      });
  </script>
  <!-- 当前页面js -->
  <script src="{{ config('app.source_url') }}shop/js/detail.js?v="+ New Date()></script>
@endsection

