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
        [v-cloak] { display: none }
        .t_footer{
            width:320px;
        }
        .xuanfu {
            position: absolute;
            right: 5px;
        }
        #app{
            width: 320px;
            margin: 0 auto;
            height: 100%;
            position: relative;
        }
        .content_right{width: 150px;height: 225px;background: #fff;padding: 5px 10px;text-align: center;position: fixed;top: 80px;right:calc(50% - 365px) ;}
        .right_top{display: flex;align-items: center;justify-content: center;height: 40px;border-bottom: 1px dashed #ccc;}
        .right_top span{font-size: 16px;color: #94d154;}
        .content_right p{line-height: 40px;font-size: 14px;}
        .right_img{display: inline-block;width: 136px;height: 136px;}
    </style>
</head>
<body>

<div id="app" v-cloak>
    <div class="page" v-if="show != ''">
        <div class="detail_top">
            <div class="swiper-container" style="min-height: 200px; max-height: 350px;">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" v-for="swImg in list1.img" @click.stop="lookImg(swImg.img)">
                        <img class="" v-bind:src="imgUrl + '' + swImg.img" />
                    </div>
                </div>
                <!-- 如果需要分页器 -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="detail_content">
            <div class="content_l">
                <p class="title" v-text="list1.title">黑凤梨 20寸全铝镁合金登机箱</p>
                <p class="sub_title" v-text="list1.subtitle">100%铝镁合金，超薄坚固</p>
                <p class="price" v-html="'￥' + list1.min + '&nbsp;&nbsp<span>品牌价：￥' + listPro.price + '</span>'"></p>
                <p class="people">已团：<strong v-text="list1.pnum"></strong>件<strong class="groups_num" v-text="list1.groups_num"></strong>人团</p>
            </div>
            <div class="content_r">
                <img src="{{ config('app.source_url') }}shop/images/fenxiang@2x.png"/>
                <div>分享</div>
            </div>
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
            <div class="content1 howTuan" v-if="list2.num != 0">
                <div class="h_title">
                    <div class="how_poeple" v-text="list2.num + '人在开团'">89人在开团</div>
                    <div class="more" v-on:click="goMorePin" v-if="list2.num > 2">查看更多</div>
                    <img v-if="list2.num > 2" src="{{ config('app.source_url') }}shop/images/jinru@2x.png" />
                </div>
                <div class="tuanList" v-for="(howT,hIndex) in list2Data2">
                    <img v-bind:src="howT.headimgurl" />
                    <div class="tuanList_c">
                        <p class="" v-text="howT.nickname" style="font-size:16px">细雨微微</p>
                        <p class="tuanList_cTxt"><span v-text="'还差' + howT.num + '人，'">还差两人，</span><span v-text="'剩余' + surplusTime[hIndex]">剩余23:34:32</span></p>
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
                    <div class="e_txt" v-text="listEData.content">这个鞋子质量出乎意料的好啊，额分光光度法感觉哈哈我I如何，发给方法</div>
                    <div class="e_img">
                        <img @click.stop="lookImg(evalImg)" v-for="evalImg in listEData.img" v-bind:src="imgUrl + '' + evalImg.m_path" />
                    </div>
                    <div class="e_ruler" v-text="listEData.spes">亮黑色；20英寸</div>
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

            <div class="content1 recommend" v-if="list3.length != 0" @if(in_array(session('wid'),config('app.li_wid')))style="display: none" @endif>
                <div class="r_title">
                    <img src="{{ config('app.source_url') }}shop/images/tuijian@2x.png" />
                </div>
                <div class="r_cont">
                    <div class="r_contList" v-for="rList in list3">
                        <div class="rc_top">
                            <img v-bind:src="imgUrl + '' + rList.img" />
                            <p v-if="rList.label != ''" v-text="rList.label">膳魔师制造商</p>
                        </div>
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
</div>
<div class="content_right">
    <div class="right_top">
        <img src="/shop/images/previewblade_erweima.png" alt="">
        <span>手机扫码购买</span></div>
    <p>微信“扫一扫”关注购买</p>
    <p class="text-center qr-code">
        {!! QrCode::size(150)->generate(URL("/shop/grouppurchase/detail/".$rule_id."/".session('wid'))); !!}
    </p>
</div>
<!--底部按钮-->
<div class="t_footer" id="tFooter" v-if="show != ''">
    <div class="tf_lBut">
        <img src="{{ config('app.source_url') }}shop/images/sy@2x.png?t=123" />
    </div>
    @if ( config('app.chat_url') )
    <div class="tf_lBut">
        <a href="javascript:void(0);">
            <!--许立 2018年09月29日 预览页不需要判断来源-->
            <img src="{{ config('app.source_url') }}shop/images/kf@2xx.png?t=123" />
        </a>
    </div>
    @endif
    <div class="oPrice_buy">
        <div class="oPrice" v-text="'￥' + listPro.price">￥999</div>
        <div class="oPrice_txt">原价购买</div>
    </div>
    <div class="oPrice_buy oneOpen" v-if="list1.is_over == 0">
        <div class="oPrice" v-text="'￥' + list1.headMin">￥599</div>
        <div class="oPrice_txt">一键开团</div>
    </div>
</div>
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
<!-- 主体内容 结束 -->
    <script type="text/javascript">
        var APP_HOST = "{{ config('app.url') }}";
        var APP_IMG_URL = "{{ imgUrl() }}";
        var APP_SOURCE_URL = "{{ config('app.source_url') }}";
        var CHAT_URL = "{{config('app.chat_url')}}";
        var ruleData = {!! json_encode($ruleData) !!}
    </script>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <!-- <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script> -->
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script>
        var rule_id="{!!$rule_id!!}";
        var _host = "{{ config('app.source_url') }}";
        var host ="{{ config('app.url') }}";
        var imgUrl = "{{ imgUrl() }}";
       
        var isBind = 0;
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js?v=1.00"></script>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <!--  <script src="./public/static/js/jquery-weui.min.js"></script> -->
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}shop/js/groupon_preview.js"></script>
</body>
</html>

