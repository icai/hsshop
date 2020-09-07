<!DOCTYPE html>
<html class="ks-webkit533 ks-webkit">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>小程序全国巡回沙龙-宁海</title>
    <meta name="keywords"
    content="微信邀请函,微场景,微场景制作,场景应用,场景应用制作,微信上墙,微信邀请函,电子邀请函,H5页面,微信抽奖,微信请帖,微信发会议邀请,二维码签到,会议签到,请帖,电子邀请函,电子门票">
    <meta name="description"
    content="会搜云致力于做微时代会议活动一站式解决方案，包括会议邀约的微信邀请函、会议前期宣传h5，企业宣传、会议现场签到和会议活动现场互动抽奖等产品，全面支持pc、手机、微信，让你最便捷的邀请你的嘉宾">
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ config('app.source_url') }}applet/css/mobi.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}applet/css/animations.min.css" />
    <meta id="wyqViewport" name="viewport" content="width=320, initial-scale=1, maximum-scale=1, user-scalable=no" servergenerated="true">
    <style>
        *,*:before,*:after{
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        tr{height:25px;}
        html{overflow:auto;width:100%;height:100%;}
        body{overflow:auto;height:100%;}
        .page{width:100%;height:100%;position:absolute;left:0;top:0;  box-shadow: rgb(0, 0, 0) 0px 0px 10px;}
        .textarea{height:90px;}
        /*#share{
            position:absolute;
            left:0;
            top:0;
            width:100%;
            height:100%;
            display: none;
            background-color:rgba(0,0,0,0.8);
            z-index:9999999999;
            background-image:url(/images/share.png);
            background-size:100% 100%;
            background-repeat:no-repeat;
        }*/
        .page{
            -webkit-background-size: 100% 100%;
            -moz-background-size: 100% 100%;
            -o-background-size: 100% 100%;
            background-size: 100% 100%;
        }
        .pageContainer{
            width: 100%;
            height: 100%;
        }
        .swiper-container, .swiper-slide { width: 100%; height: 100%; display: block;position:relative;}
        .swiper-slide{overflow:hidden;}
        #loading { width: 100%; height: 100%; background-color: #fff; color: red; position: absolute; z-index: 10000000000; }
        .loading-text{text-align: center;line-height:2em;color:#000;}
        .loader,.spinner,.rect {  margin: 150px auto 0px; width: 90px; height: 90px; position: relative; text-align: center;  }
        .loader span { display: inline-block; vertical-align: middle; width: 10px; height: 10px; margin: 50px auto; background: #68b6f2; border-radius: 50px; -webkit-animation: loader 0.9s infinite alternate; -moz-animation: loader 0.9s infinite alternate; }
        .loader span:nth-of-type(2) { -webkit-animation-delay: 0.3s; -moz-animation-delay: 0.3s; }
        .loader span:nth-of-type(3) { -webkit-animation-delay: 0.6s; -moz-animation-delay: 0.6s; }
        @-webkit-keyframes loader { 0% { width: 10px; height: 10px; opacity: 0.9; -webkit-transform: translateY(0); } 100% { width: 24px; height: 24px; opacity: 0.1; -webkit-transform: translateY(-21px); } }
        @-moz-keyframes loader { 0% { width: 10px; height: 10px; opacity: 0.9; -moz-transform: translateY(0); } 100% { width: 24px; height: 24px; opacity: 0.1; -moz-transform: translateY(-21px); } }
        .spinner {-webkit-animation: rotate 2.0s infinite linear; animation: rotate 2.0s infinite linear; }
        .dot1, .dot2 { width: 60%; height: 60%; display: inline-block; position: absolute; top: 0; background-color: #68b6f2; border-radius: 100%; -webkit-animation: bounce 2.0s infinite ease-in-out; animation: bounce 2.0s infinite ease-in-out; }
        .dot2 { top: auto; bottom: 0px; -webkit-animation-delay: -1.0s; animation-delay: -1.0s; }
        @-webkit-keyframes rotate { 100% { -webkit-transform: rotate(360deg) }}
        @keyframes rotate { 100% { transform: rotate(360deg); -webkit-transform: rotate(360deg) }}
        @-webkit-keyframes bounce { 0%, 100% { -webkit-transform: scale(0.0) } 50% { -webkit-transform: scale(1.0) } }
        @keyframes bounce { 0%, 100% { transform: scale(0.0); -webkit-transform: scale(0.0); } 50% { transform: scale(1.0); -webkit-transform: scale(1.0); } }
        .rect { width: 60px; height: 60px; background-color: #68b6f2;  -webkit-animation: rotateplane 1.2s infinite ease-in-out; animation: rotateplane 1.2s infinite ease-in-out; }
        @-webkit-keyframes rotateplane { 0% { -webkit-transform: perspective(120px) } 50% { -webkit-transform: perspective(120px) rotateY(180deg) } 100% { -webkit-transform: perspective(120px) rotateY(180deg)  rotateX(180deg) } }
        @keyframes rotateplane { 0% { transform: perspective(120px) rotateX(0deg) rotateY(0deg); -webkit-transform: perspective(120px) rotateX(0deg) rotateY(0deg) } 50% { transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg); -webkit-transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg) } 100% { transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg); -webkit-transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg); } }
        .top{position:absolute;width:100%;height:100%;left:0;background:url(/application/views/mobile/preview/type_4/template_213/source/top.png) no-repeat;background-size: 100% 100%;}
        .layer{
            -webkit-background-size: 100% 100%;
            -moz-background-size: 100% 100%;
            -o-background-size: 100% 100%;
            background-size: 100% 100%;
            width:100%; height: 100%; position: absolute; top: 0; left: 0;
        }
        .dialog{display: none;z-index:150;}
        .dialog .cancel{ position : absolute; width:30px;height:30px;left:2%;top:4%; background-image:url(http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_25/source/cancel.png);background-position:center center;background-repeat:no-repeat;background-color:rgba(0,0,0,.5);background-size: 70% 70%;border-radius: 15px;}
        .form input {
            width: 159px;
            height: 24px;
            margin-bottom: 10px;
        }
        .form {
            font-size: 14px;
            padding-top: 55px;
            margin: 38px;
            margin-top: -14px;
            height: 75%;
            width: 87%;
        }
        .submit{
            margin-top: 10px;
            text-align: center;
            margin-right: 23px;
        }
        .music{position: absolute;
            right: 10px;
            top: 5px;
            z-index: 300;
            width: 38.17%;
        }
        @-webkit-keyframes up {
            0%,30% {opacity: 0;-webkit-transform: translate(0,10px);}
            60% {opacity: 1;-webkit-transform: translate(0,0);}
            100% {opacity: 0;-webkit-transform: translate(0,-8px);}
        }
        @-moz-keyframes up {
            0%,30% {opacity: 0;-moz-transform: translate(0,10px);}
            60% {opacity: 1;-moz-transform: translate(0,0);}
            100% {opacity: 0;-moz-transform: translate(0,-8px);}
        }
        @keyframes up {
            0%,30% {opacity: 0;transform: translate(0,10px);}
            60% {opacity: 1;transform: translate(0,0);}
            100% {opacity: 0;transform: translate(0,-8px);}
        }
        @-webkit-keyframes left {
            0%,30% {opacity: 0;-webkit-transform: translate(10px,0);}
            60% {opacity: 1;-webkit-transform: translate(0,0);}
            100% {opacity: 0;-webkit-transform: translate(-8px,0);}
        }
        @-moz-keyframes left {
            0%,30% {opacity: 0;-moz-transform: translate(10px,0);}
            60% {opacity: 1;-moz-transform: translate(0,0);}
            100% {opacity: 0;-moz-transform: translate(-8px,0);}
        }
        @keyframes left {
            0%,30% {opacity: 0;transform: translate(10px,0);}
            60% {opacity: 1;transform: translate(0,0);}
            100% {opacity: 0;transform: translate(-8px,0);}
        }
        .up{
            animation: up 2s infinite;
            -webkit-animation: up 2s infinite;
            position:absolute;right: 45%;
            top: 95%;
            z-index: 150;
            width: 24px;
            height: 14px;
            background:url(http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_301/arrow.png) no-repeat;
            background-size:100% 100%;
        }
        .left{
            animation: left 2s infinite;
            -webkit-animation: left 2s infinite;
            position:absolute;
            top: 47%;
            right: 7%;
            z-index: 150;
            width: 14px;
            height: 24px;
            background:url(http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_301/left.png) no-repeat;
            background-size:100% 100%;
        }
        .pageContent{
            position: absolute;
            top: 0;
            text-align: left;
            width: 320px;
            height: 486px;
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .comp_button{ padding: -5px; width: 100%; height: 100%; text-align: center; display: table; }
        .comp_button .table_row{ display:table-row; }
        .comp_button .table_cell{ display: table-cell;vertical-align: middle;}
        .element img{ display: block; }
    </style>
    <style>
        #report{position:absolute; right:20px; bottom:20px;background-color:#6b6b6b;color:#fff; width:30px; height:30px; line-height: 30px; border-radius: 15px; display: block; text-align: center; z-index:999; opacity:0.5; box-shadow:0px 0px 10px #ccc;display:none;}
        #report0 div{margin:0;}
        #report0{width:260px;background-color:#fff;position:absolute;top:20px;left:50%;margin-left:-130px;z-index:999;padding-bottom:20px;box-shadow:2px 4px 4px #ccc;}
        #report1{width:100%;height:40%;top:20%;background-color:#fff;z-index:1000;position:relative}
        #report2{width:100%;background-color:#f66;color:#fff;text-align:center;padding:15px 0}
        #report2 h1{font-size:16px;margin-top:10px}
        #report3{margin-top:20px;width:100%;text-align:center}
        #report3 ul li{font-size:15px;line-height:40px}
        #report3 ul li span{padding-right:20px}
        #report3 li.active span{color:#f66;background:url(/application/views/mobile/preview/type_4/template_301/jubao_07.png) no-repeat right;padding-right:20px;background-size:15px}
        #report4{text-align:center;margin-top:10px}
        #report4 a{width:140px;height:30px;line-height:30px;font-size:14px;border-radius:3px;background-color:#f66;color:#fff;text-align:center;display: block; margin: 0 auto;}
        .page-container{
            position: relative;
            width: 640px;
            height: 100%;
            overflow: hidden;
            box-sizing: border-box;
            background-color: #e7e0d3;
        }
        .page {
            border: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #fff;
            background-position: top center;
            background-size: cover;
            box-sizing: border-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            z-index: 0;
            display: none;
            -webkit-transform-origin: center center;
        }
        .page.p-active {
            z-index: 2;
            display: block
        }
        .page.p-current {
            z-index: 1;
            display: block
        }
        .page-content{
            position: absolute;
            top: 0;
            text-align: left;
            width: 320px;
            height: 486px;
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .page-content {
            -webkit-box-flex: 100;
        }
    </style>
</head>
<body cz-shortcut-listen="true" title="小程序全国巡回沙龙-宁海站" icon="http://img.wyaoqing.com/img/upload/20170626/20170626154547_81523.png" link="http://mwfgczpn.weiyixinda.com/419297?" desc="如今，新的千亿级风口即将重现，这一次，属于你的机会来了。">

    <!--<div id="share"> </div>-->
    <div id="loading">
        <div class="loader">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="loading-text">
             powered by huisouyun         </div>
    </div>
    <div class="page-container" style="position: relative;width:100%;height:100%;overflow: hidden;">
    </div>
    <div id='recycle' style="display: none;"></div>
    <div id='report'>举报</div>
    <div id="report0" style='display: none;'>
        <div id="report1">
            <div id="report2">
                <p>
                    <img src="http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_301/jubao_03.png" width="50px;">
                </p>
                <h1>请选择举报原因</h1>
            </div>
            <div id="report3">
                <ul id="reportList">
                    <li value="1" class="active">
                        <span>色情、赌博、毒品</span>
                    </li>
                    <li value="2">
                        <span>谣言、社会负面、诈骗</span>
                    </li>
                    <li value="3">
                        <span>邪教、非法集会、传销</span>
                    </li>
                    <li value="4">
                        <span>医药、整形、虚假广告</span>
                    </li>
                    <li value="5">
                        <span>有奖集赞和关注转发</span>
                    </li>
                    <li value="6">
                        <span>违反国家政策和法律</span>
                    </li>
                    <li value="7">
                        <span>其他原因</span>
                    </li>
                </ul>
            </div>
            <div id="report4">
                <input type='hidden' id='report_type' value='1' />
                <a id="reportSubmit">提交举报</a>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ config('app.source_url') }}applet/js/jquery-2.0.3.min.js"></script>
    <!--<script type="text/javascript" src="/js/idangerous.swiper/idangerous.swiper-2.6.1.min.js"></script>-->
    <!--<script type="text/javascript" src="/js/idangerous.swiper/idangerous.swiper.progress.js"></script>-->
    <!--<link rel="stylesheet" href="/js/idangerous.swiper/idangerous.swiper.css" />-->
    <!--<script type="text/javascript" src="/screen/jquery.transit.min.js"></script>-->
    <!--<script type="text/javascript" src="/js/touch-0.2.13.min.js"></script>-->
    <script type="text/javascript" src="{{ config('app.source_url') }}applet/js/weiyaoqing.mobile.js?v=20161212"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}applet/js/animations.min.js?v=2"></script>
    <!--js动画开始-->
    <!--<script type="text/javascript" src="/js/greensock/TweenMax.min.js"></script>-->
    <!--<script type="text/javascript" src='/js/animations/animation.js?v=2'></script>-->
    <!--js动画结束-->
    <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=26a8b87b0c8138a95b84b6cc405706ac"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}applet/js/sea.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}applet/js/seajs-style.js"></script>
        <script>
        var campaign_id = '419297';
        var referPhone = '{{ $referPhone }}';
        global = {
            img_domain : 'http://img.wyaoqing.com',
            is_show_ad : '1',
            user_grade  : '1',
            ad_link : 'http://www.woyaoqing.com/70128',
            service : 'http://service.wyaoqing.com',
            map:{
                y:'29.3266',
                x:'121.443',
                title:'宁海富泉美悦酒店',
                content:'活动地点'
            },
            page_setting:{
            loop:false, //true, false
            direction:'vertical', //vertical, horizontal
            flipEffect:'cover' //cover, push
        },
        pageOrderMap:{}, //页码与真实第几页的关系映射
        renderPointer:3  //渲染到第几页的指针

        /*,
         pointer:{},
         renderPointer:5*/
     }
     var assetsVersion = '1.1.54';
     seajs.config({
        base:'./',
        map: [
        [ /^(.*\.(?:css|js))(.*)$/i, '$1?v='+ assetsVersion]
        ]
    });
     var clientWidth = document.documentElement.clientWidth;
     function isWeixin() { var a = navigator.userAgent.toLowerCase(); return "micromessenger" == a.match(/MicroMessenger/i) ? !0 : !1 }
     function scalePage() {
        var d, e, f = 1, g = $(window).width(), h = $(window).height();
        g / h >= 320 / 486 ? (f = h / 486, d = (g / f - 320) / 2) : (f = g / 320, e = (h / f - 486) / 2);
        e && $(".pageContent").css({marginTop: e});
        d && $(".pageContent").css({marginLeft: d});
//        $('.page-container').width(Math.round(g/f)).height(Math.round(h/f));
//        $('.swiper-container').width(g+(typeof d == 'undefined' ? 0 : d)*2).height(h+(typeof e == 'undefined' ? 0 : e)*2);
$("#wyqViewport").attr("content", "width=320, initial-scale=" + f + ", maximum-scale=" + f + ", user-scalable=no");
320 != clientWidth && clientWidth == document.documentElement.clientWidth;
if (isWeixin() && (navigator.userAgent.indexOf("Android") > -1 || navigator.userAgent.indexOf("Linux") > -1)) {
    var i = 320 / g, j = 486 / h, k = Math.max(i, j);
    k = k > 1 ? k : 160 * k, k = parseInt(k), $("#wyqViewport").attr("content", "width=320, target-densitydpi=" + k)
}
$(window).resize();
return {
    top : e ? e : 0,
    left : d ? d : 0,
    width : Math.round(g/f),
    height : Math.round(h/f)
}
}

seajs.use('js/index');
</script>
<div class="music">
    <img src="http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_62/source/music_play.png" style="width: 100%">
    <audio src="http://img.wyaoqing.com/music/o_1bjhor0921qjtola8cf184ml0tt.mp3" id="video" autoplay loop preload="auto" style="display:none;" >
        </audio>
    </div>
    <script>
        var isaoto = 0;
        function stop(){
            var myVideo = document.getElementById("video");
            if(!myVideo.paused){
                myVideo.pause();
                $(".music").find("img").attr("src","http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_62/source/music_stopv2.png");
            } else {
                myVideo.play();
                $(".music").find("img").attr("src","http://img.wyaoqing.com/application/views/mobile/preview/type_4/template_62/source/music_play.png");
            }
        }
        function play(){
            var myVideo = document.getElementById("video");
            myVideo.play();
        }
        document.ontouchstart = function(e){
            if(isaoto ==0){
                play();
                isaoto = 1;
            }
        }
        setTimeout(play,1000);
        $(".music").click(stop);
    </script>
    </body>
</html>
