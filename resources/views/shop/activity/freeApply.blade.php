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
        .form1{
            padding:20px 0;
            background:#fff;
        }
        .form1 .form_title {
            text-align:center;
            font-weight: bold;
            font-size:18px;
        }
        .form1 .form_lebel {
            font-size: 16px;
            padding: 10px;
        }
        .form1 .form_lebel span {
            color:red;
            margin-left:5px;
        }
        .form1 .form-control {
            padding:0 10px;
        }
        .form1 .form-control input {
            display: block;
            width: 100%;
            height: 34px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            padding-left:10px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            outline: none;
            box-sizing: border-box;
        }
        .weui-btn {
            position: relative;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding-left: 14px;
            padding-right: 14px;
            box-sizing: border-box;
            font-size: 18px;
            text-align: center;
            text-decoration: none;
            color: #fff;
            line-height: 2.55555556;
            border-radius: 5px;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            overflow: hidden;
            width:100%;
            margin-top:20px;
        }
        .weui-btn_primary {
            background-color: #f44747;
        }
        .weui-btn_primary:not(.weui-btn_disabled):visited {
            color: #fff;
        }
        .action{
            padding:0 20px;
        }
        .form2{
            background:#fbcd54;
        }
        .form2 .banner{
            position:relative;
        }
        .form2 .banner .rule{
            position: absolute;
            right: 10px;
            top: 10px;
            color: #fff;
            display: inline-block;
            padding: 5px 10px;
            background: rgba(0,0,0,0.6);
            border-radius: 5px;
            font-size: 15px;
        }
        .form2 .banner img{
            width:100%;
            vertical-align: bottom;
            display: block;
        }
        .form2 .control-group{
            background:#fff;
            margin: 10px;
        }
        .form2 .banner_footer img{
            width:100%;
            vertical-align: bottom;
            display:block;
        }
        /*label, input, button, select, textarea {
            font-size: 14px;
            font-weight: normal;
            line-height: 20px;
        }*/
        .form2 label {
            display: block;
        }
        .form2 .control-label {
            float: left;
            width: 60px;
            line-height: 50px;
            text-align: right;
            font-size: 18px;
            color: #333;
            height: 50px;
        }
        .form2 .controls {
            margin-left: 70px;
            height: 50px;
            box-sizing: border-box;
        }
        .form2 .control-group input{
            outline: none;
            border: none;
            padding: 10px 0;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }
        .form2 .save_btn{
            background: #f85936;
            color: #fff;
            line-height: 50px;
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            margin: 10px;
            /* background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #f55431), color-stop(1,#ba2909)); */
            border-radius: 25px;
            letter-spacing: 2px;
        }
        /*.form2 .save_btn:after{
            content: " ";
            width: 200%;
            height: 200%;
            position: absolute;
            top: 0;
            left: 0;
            border: 1px solid rgba(0,0,0,.2);
            -webkit-transform: scale(.5);
            transform: scale(.5);
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            box-sizing: border-box;
            border-radius: 10px;
        }*/
        .red{
            color:red;
        }
        .btn_info{
            font-size:14px;
            text-align:center;
            padding-bottom:10px;
        }
        .list_lh {
            height: 200px;
            overflow: hidden;
            margin:0 10px;
        }

        .list_lh li {
            padding: 10px;
            font-size:12px;
            display: flex;
            display: -webkit-flex;
            display: -moz-flex;
            display: -o-flex;
            color:#fff;
        }
        .list_lh li div{
            -webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;
            text-align:center;
        }
        .list_lh li:nth-of-type(odd){ 
            background:#322911;
        } 
        .list_lh li:nth-of-type(even){
            background:#463a18;
        }
        .list_2h{
            margin:0 10px;
        }
        .list_2h li {
            padding: 10px;
            font-size:12px;
            display: flex;
            display: -webkit-flex;
            display: -moz-flex;
            display: -o-flex;
            background: #bf9c40;
        }
        .list_2h li div{
            -webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;
            text-align:center;
        }
        /*规则弹窗*/
        .weui-mask {
            background: rgba(0,0,0,.6);
        }
        .weui-mask, .weui-mask_transparent {
            position: fixed;
            z-index: 100000000;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
        }
        .weui-mask {
            opacity: 0;
            -webkit-transition-duration: .3s;
            transition-duration: .3s;
            visibility: hidden;
        }
        .weui-mask.weui-mask--visible {
            opacity: 1;
            visibility: visible;
        }
        .weui-dialog {
            position: fixed;
            z-index: 100000001;
            width: 80%;
            max-width: 300px;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%,-50%);
            transform: translate(-50%,-50%);
            background-color: #fff;
            text-align: center;
            border-radius: 3px;
            overflow: hidden;
        }
        .weui-dialog, .weui-toast {
            -webkit-transition-duration: .2s;
            transition-duration: .2s;
            opacity: 0;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            visibility: hidden;
            margin: 0;
            top: 45%;
            z-index: 100000001;
        }
        .weui-dialog.weui-dialog--visible, .weui-toast.weui-dialog--visible, .weui-dialog.weui-toast--visible, .weui-toast.weui-toast--visible {
            opacity: 1;
            visibility: visible;
        }
        .weui-dialog__hd {
            padding: 17px 10px 12px;
            position:relative;
        }
        .weui-dialog__hd strong{
            font-weight: normal; 
            color: #333;
        }
        .weui-dialog__bd {
            padding: 0 1.6em .8em;
            min-height: 40px;
            font-size: 15px;
            line-height: 1.3;
            word-wrap: break-word;
            word-break: break-all;
            color: #999;
            text-align: justify;
            height: 200px;
            overflow: auto;
        }
        .weui-dialog__ft {
            position: relative;
            line-height: 48px;
            font-size: 18px;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
        }
        .weui-dialog__btn {
            display: block;
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            flex: 1;
            color: #F55836;
            text-decoration: none;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            position: relative;
        }
        .weui-dialog__ft:after {
            content: " ";
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            height: 1px;
            border-top: 1px solid #d5d5d6;
            color: #d5d5d6;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            -webkit-transform: scaleY(.5);
            transform: scaleY(.5);
        }
        .line1{
            content: ' ';
            display: inline-block;
            width: 32%;
            position: absolute;
            left: 10px;
            top: 25px;
            height: 1px;
            color: #d5d5d6;
            border-top: 1px solid #d5d5d6;
            transform-origin: 0 0;
            -webkit-transform: scaleY(.5);
            transform: scaleY(.5);
        }
        .line2{
            content: ' ';
            display: inline-block;
            width: 32%;
            position: absolute;
            right: 10px;
            top: 25px;
            height: 1px;
            color: #d5d5d6;
            border-top: 1px solid #d5d5d6;
            transform-origin: 0 0;
            -webkit-transform: scaleY(.5);
            transform: scaleY(.5);
        }
        .is_already{
            font-size: 15px;
            color: #333;
            text-align: center;
        }
        .is_already>div{
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container" id="container" :style="{background:bg_color}">
    <!-- <div class="form2">
        <div class="control-group">
          <label class="control-label" for="inputEmail">Email</label>
          <div class="controls">
            <input type="text" id="inputEmail" placeholder="Email">
          </div>
        </div>
    </div> -->
    <div class="form2">
        <div class="banner">
            <img src="{{ config('app.source_url') }}/shop/images/form_banner11.png?t=123">
            <span class="rule">规则</span>
        </div>
        <div class="control-group">
            <label class="control-label" for="name"><em class="red">*</em>姓名</label>
            <div class="controls">
                <input type="text" id="name" name="name" placeholder="请输入您的姓名">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="phone"><em class="red">*</em>手机</label>
            <div class="controls">
                <input type="text" id="phone" name="phone" placeholder="请输入您的手机号">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="company_name"><em class="red">*</em>公司</label>
            <div class="controls">
                <input type="text" id="company_name" name="company_name" placeholder="请输入您的企业全称">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="company_position"><em class="red">*</em>职务</label>
            <div class="controls">
                <input type="text" id="company_position" name="company_position" placeholder="请输入您的职务">
            </div>
        </div>
        {{--<div class="control-group">--}}
            {{--<label class="control-label" for="wechat_name">微信</label>--}}
            {{--<div class="controls">--}}
                {{--<input type="text" id="wechat_name" name="wechat_name" placeholder="请输入您的微信">--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="btn_info red">仅限企业董事长、法人、总经理、CEO领取</div>
        <div class="save_btn">
            保存提交
        </div>
        <div class="list_2h">
            <ul>
                <li>
                    <div class="username">
                        姓名
                    </div>
                    <div class="user_phone">电话</div>
                    <div class="user_phone">领取时间</div>
                </li>
            </ul>
        </div>
        <div class="list_lh">
            <ul>
                
            </ul>
        </div>
        <div class="banner_footer">
            <img src="{{ config('app.source_url') }}/shop/images/form_banner_footer1.png">
        </div>
    </div>
    <div class="content no-sidebar">
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
                <!-- author 华亢 update 2018/06/28 -->
                <!-- 联系方式组件 -->
                <cmobile :list="list" :reqFrom="reqFrom" v-if="list.type == 'mobile'"></cmobile>
                <!-- 联系方组件 -->
                <!-- 享立减 -->
                <share-rebate :list="list" v-if="list.type == 'share_goods'"></share-rebate>
                <!-- 享立减 -->
                <seckill-list :list="list" v-if="list.type == 'seckill_list'"></seckill-list>
            </div>
        </div>
    </div>
    <div class="js-navmenu js-footer-auto-ele shop-nav nav-menu nav-menu-1 has-menu-3" style="height:55px">
        <div class="nav-items-wrap" style="margin-left:0px">
            <div class="nav-item" style="width: 50%;border-left: none;height:55px">
                <a href="/shop/activity/freeApply/{{session('wid')}}/{{$id}}/9?{{ time() }}" class="mainmenu js-mainmenu" style="height:55px">
                    <span class="mainmenu-txt" style="font-size:17px;font-weight: bold;line-height:55px">首页</span>
                </a>
                <div class="submenu js-submenu" style="display: none;">
                    <span class="arrow before-arrow"></span>
                    <span class="arrow after-arrow"></span>
                    <ul></ul>
                </div>
            </div>
            <div class="nav-item" style="width: 50%;height:55px;">
                <a href="/shop/activity/freeApplyInviteList/{{session('wid')}}/{{$id}}/9?{{ time() }}" class="mainmenu js-mainmenu" style="height:55px">
                    <span class="mainmenu-txt" style="font-size:17px;font-weight: bold;line-height:55px">我的分享</span>
                </a>
                <div class="submenu js-submenu" style="display: none;">
                    <span class="arrow before-arrow"></span>
                    <span class="arrow after-arrow"></span>
                    <ul></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- weui-mask--visible -->
<div class="weui-mask"></div>
 <!-- weui-dialog--visible -->
<div class="weui-dialog">
    <div class="weui-dialog__hd">
        <span class="line1"></span>
        <strong class="weui-dialog__title">活动规则</strong>
        <span class="line2"></span>
    </div>
    <div class="weui-dialog__bd">
        1.Q：一个企业是否支持领取多次？
        A:一个企业只允许领取一次，必须填写企业全称。并且是法人或CEO、总经理、董事长只能领取一次微商城旗舰版系统和小程序旗舰版系统。
        <br /><br />
        2.Q:如何免费获得微商城旗舰版系统？
         A:您需要准确填写姓名、手机号、企业名称、职位、微信号提交成功后，平台审核后将会自动发送给您账号相关信息。
         <br /><br />

        3.Q:如何领取微商城旗舰版系统？
        A：当您领取成功，电脑登录网址：huisou.cn ，用短信发您的账号密码登录，创建店铺，然后联系在线QQ客服帮您开通微商城权限。
        <br /><br />

        4.Q:如何免费领取小程序旗舰版系统？
        A:您需要满足平台邀请人数的条件，邀请企业好友填写的账号相关信息真实有效才可领取，平台审核后将会发送您相关的领取通知，联系在线官方QQ客服或辅导员，帮您开通小程序权限！
        <br /><br />


        5.Q：收到领取小程序旗舰版系统短信通知，如何开通操作权限？
        A：请电脑上登录huisou.cn，联系在线官方QQ客服，帮您开通小程序权限。
        <br /><br />
        6.Q:如何进行微商城旗舰版系统线上操作培训？
        A:我们会定期进行线上操作培训，教大家如何搭建微商城旗舰版系统和小程序旗舰版系统，请大家在“我的分享里”加辅导员微信，获取培训方式”。
        <br /><br />
        7.Q:  QQ客服在线上班时间? 
        A：周一至周日，8:30~12:00  13:30~18:00
        <br /><br />
        最终解释权归杭州会搜科技股份有限公司所有
    </div>
    <div class="weui-dialog__ft">
        <a href="javascript:;" class="weui-dialog__btn primary">我知道了</a>
    </div>
</div>
<!-- 底部 结束
<!-- 当前页面js -->
<script type="text/javascript">
    var APP_HOST = "{{ config('app.url') }}"
    var APP_IMG_URL = "{{ imgUrl() }}"
    var APP_SOURCE_URL = "{{ config('app.source_url') }}"
</script>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
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
    var mid = "{{session('mid')}}";
    var reqFrom = "{{ $reqFrom }}";
</script>
@if($reqFrom == 'aliapp')
<script type="text/javascript" src="https://appx/web-view.min.js"></script>
@endif
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/scroll.js"></script>
<script src="{{ config('app.source_url') }}shop/js/meeting_index.js"></script>
<script type="text/javascript">
    $(function(){
        // 分享设置
        var url = location.href.split('#').toString();
        // 许立 2018年07月13日 分享文案修改
        var share_title = '价值39800元的微商城旗舰版系统限时免费领！';
        var share_desc = "拼团、秒杀、享立减、签到、集赞、会员卡等功能应有尽有！";
        var share_url = host + '/shop/activity/freeApply/' + wid + '/'+ id + "/9?_pid_={{session('mid')}}";
        var share_img = _host + 'shop/images/freeApplyResult_share.png';

        $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
            if(data.errCode == 0){
                wx.config({
                    debug: false, 
                    appId: data.data.appId, 
                    timestamp: data.data.timestamp, 
                    nonceStr: data.data.nonceStr, 
                    signature: data.data.signature,
                    jsApiList: [
                        'checkJsApi',
                        'onMenuShareTimeline',
                        'onMenuShareAppMessage',
                        'onMenuShareQQ',
                        'chooseWXPay'
                    ] 
                });
            }
        })
        wx.ready(function () {
            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: share_title, 
                desc: share_desc,
                link: share_url, 
                imgUrl: share_img,
                success: function () {
                },
                cancel: function () {
                }
            });

            //分享给朋友
            wx.onMenuShareAppMessage({
                title: share_title, 
                desc: share_desc, 
                link: share_url, 
                imgUrl: share_img, 
                type: '', 
                dataUrl: '', 
                success: function () {
                },
                cancel: function () {
                    
                }
            });

            //分享到QQ
            wx.onMenuShareQQ({
                title: share_title, 
                desc: share_desc, 
                link: share_url, 
                imgUrl: share_img, 
                success: function () {
                   
                },
                cancel: function () {
                   
                }
            });

            //分享到腾讯微博
            wx.onMenuShareWeibo({
                title: share_title, 
                desc: share_desc, 
                link: share_url, 
                imgUrl: share_img, 
                success: function () {
                   
                },
                cancel: function () {
                    
                }
            });
            wx.error(function(res){
                
            });
        });
        var flag = true;
        $('.save_btn').click(function(){
            if(!flag) return;
            flag = false;
            var data = {};
            data.name = $('input[name="name"]').val();
            data.phone = $('input[name="phone"]').val();
            data.company_name = $('input[name="company_name"]').val();
            data.company_position = $('input[name="company_position"]').val();
            data.wechat_name = $('input[name="wechat_name"]').val();
            data._token = $('meta[name="csrf-token"]').attr('content');
            if(data.name == ''){
                tool.tip('请输入姓名');
                flag = true;
                return;
            }
            if(data.phone == ''){
                tool.tip('请输入手机号码');
                flag = true;
                return;
            }
            if(!/^[1][3,4,5,6,7,8][0-9]{9}$/.test(data.phone)){
                tool.tip('手机号码格式不正确');
                flag = true;
                return;
            }
            if(data.company_name == ''){
                tool.tip('请输入公司名称');
                flag = true;
                return;
            }
            if(data.company_position == ''){
                tool.tip('请输入职务');
                flag = true;
                return;
            }
            $.post('',data,function(res){
                if(res.status == 1){
                    tool.tip('领取成功')
                    setTimeout(function(){
                        window.location.href="/shop/activity/freeApplyResult/" + wid + '/' + id + '/9';
                    },2000)
                }else{
                    tool.tip(res.info);
                }
                flag = true;
            })
        })
        $.get('/shop/activity/freeApplyUserList/'+wid+'/9',function(data){
            if(data.status == 1){
                if(data.data.length){
                    var html = '';
                    for(var i=0;i<data.data.length;i++){
                        html += '<li>';
                        html += '<div class="username">' + data.data[i]['name'] + '</div>';
                        html += '<div class="user_phone">' + data.data[i]['phone'] + '</div>';
                        html += '<div class="user_phone">' + data.data[i]['created_at'] + '</div>';
                        html += '</li>';
                    }
                    $('.list_lh ul').append(html);
                    $("div.list_lh").myScroll({
                        speed:40, //数值越大，速度越慢
                        rowHeight:68 //li的高度
                    });
                }
            }
        })
        //规则点击
        $('.banner .rule').click(function(){
            $('.weui-mask').addClass('weui-mask--visible');
            $('.weui-dialog').addClass('weui-dialog--visible');
        })
        $('.weui-dialog__btn').click(function(){
            $('.weui-mask').removeClass('weui-mask--visible');
            $('.weui-dialog').removeClass('weui-dialog--visible');         
        })
        if (/Android [4-9]/.test(navigator.appVersion)) {
            window.addEventListener("resize", function () {
                if (document.activeElement.tagName == "INPUT" || document.activeElement.tagName == "TEXTAREA") {
                    window.setTimeout(function () {
                        document.activeElement.scrollIntoViewIfNeeded();
                    }, 0);
                }
            })
        }
    })
</script>
</body>
</html>