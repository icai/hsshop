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
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
</head>
<style type="text/css">
    .form2{
            background:#fbcd54;
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
            font-size: 18px;
            font-weight: bold;
            margin: 10px;
            border-radius: 25px;
        }
        .form2 .save_btn img{
            width: 25px;
            position: relative;
            top: 5px;
            left: -7px;
        }
        .red{
            color:red;
        }
        .btn_info{
            font-size:14px;
            text-align:center;
            padding-bottom:10px;
        }
        .list{
            position:relative;
            border-bottom:1px solid #cab377;
            padding: 5px;
        }
        .list .img{
            height: 50px;
            width: 50px;
            border-radius: 100%;
            position: absolute;
            left: 10px;
            top: 6px;
        }
        .list .img img{
            width: 100%;
            overflow: hidden;
            border-radius: 50%;
        }
        .list .right{
            padding-left: 60px;
            position:relative;
            padding-top:10px;
            padding-bottom:10px;
        }
        .list .right .username{
            padding: 0 0 5px 0;
            font-size:15px;
        }
        .list .right .phone{
            font-size:14px;
        }
        .list .right .status{
            position: absolute;
            right: 10px;
            top: 20px;
            font-size:15px;
        }
        .line_title{
            text-align: center;
        }
        .line_title .line1{
            width: 30%;
            display: inline-block;
            height: 1px;
            background: #ccc;
            margin-left: 10px;
        }
        .line_title span{
            position: relative;
            top: 3px;
        }
        .line_title .line2{
            width: 30%;
            display: inline-block;
            height: 1px;
            background: #ccc;
            margin-right: 10px;
        }
        .zhezhao {
            background: rgba(0,0,0,0.7) !important;
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 10000000;
            display: none;
        }
        .zhezhao .share_model {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }
        .zhezhao .share_model img {
            width: 80%;
            position: relative;
        }
        .zhezhao .close_share {
            width: 40px;
            height: 40px;
            position: relative;
            top: 18.5%;
            left: 5%;
            margin: 0;
            position: absolute;
        }
        .qrcode{
            text-align:center;
            padding-top:50px;
            padding-bottom:90px;
        }
        .qrcode img{
            width:60%;
        }
</style>
<body>
    <div class="container" id="container">
        <div class="form2">
            <div class="banner">
                <!--许立 2018年07月11日 判断邀请人数-->
                @if(count($list) == 0)
                    <img src="{{ config('app.source_url') }}/shop/images/invite_left_22.png">
                @elseif(count($list) == 1)
                    <img src="{{ config('app.source_url') }}/shop/images/invite_left_1.png">
                @else
                    <img src="{{ config('app.source_url') }}/shop/images/invite_done.png">
                @endif
            </div>
            <div>
                <!--许立 2018年07月11日 展示邀请列表-->
                @foreach($list as $v)
                <div class="list">
                    <div class="img">
                        <img src="{{$v['headimgurl']}}">
                    </div>
                    <div class="right">
                        <div class="username">{{$v['name']}}</div>
                        <div class="phone">{{$v['phone']}}</div>
                        <span class="status">已领取</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="save_btn">
                <img src="{{ config('app.source_url') }}/shop/images/wechat@2x.png">分享给好友
            </div>
            <div class="banner_footer">
                <img src="{{ config('app.source_url') }}/shop/images/form_banner_footer1.png">
            </div>
        </div>
        <div class="line_title">
            <div class="line1"></div>
            <span>辅导员二维码</span>
            <div class="line2"></div>
        </div>
        <div class="qrcode">
            @foreach($image_path_list as $image)
            <img src="{{$image}}">
            @endforeach
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
    <div class="zhezhao">
        <div class="share_model">
            <img src="{{ config('app.source_url') }}shop/images/freeApplyResult2.png"></div>
        <div class="close_share"></div>
    </div>
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript">
        var _host = "{{ config('app.source_url') }}";
        var imgUrl ="{{ imgUrl() }}";
        var host = "{{ config('app.url') }}";
        var videoUrl = "{{ videoUrl() }}";
        var wid = {!!$wid!!};
        var id = {!! $id !!};
        var mid = "{{session('mid')}}";
    </script>
    <script type="text/javascript">
        $('.save_btn').click(function(){
            $('.zhezhao').show();
        })
        $('.zhezhao').click(function(){
            $(this).hide();
        })
        $(function(){
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
            })
        })
    </script>
</body>
</html>