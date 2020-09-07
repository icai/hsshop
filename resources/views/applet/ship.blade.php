<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <title>亲情大考验</title>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}applet/kinship/static/css/swiper.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}applet/kinship/static/css/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}applet/kinship/public/css/index.css"/>
</head>
<body>
    <!--第一个页面-->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide pages page1">
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/DBT.png" class="img_1 ani" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.2s'>
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/KSTZ@3x.png" class="ani img_2" swiper-animate-effect='fadeInDown' swiper-animate-duration='.5s' swiper-animate-delay='0.2s'>
            </div>
            <div class="swiper-slide pages page2">
                <ul class="ani topic topic_1" swiper-animate-effect='bounceInLeft' swiper-animate-duration='1s' swiper-animate-delay='0.3s'>
                	<li><label for="t_1_1"><input id="t_1_1" type="radio" name="t_1"/><span>知道，很清楚</span></label></li>
                	<li><label for="t_1_2"><input id="t_1_2" type="radio" name="t_1"/><span>曾经记得，现在忘了</span></label></li>
                	<li><label for="t_1_3"><input id="t_1_3" type="radio" name="t_1"/><span>没有注意过</span></label></li>
                </ul>
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/JXTZ@3x.png" class="ani img_2" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.5s'>
            </div>
            <div class="swiper-slide pages page3">
                <ul class="ani topic topic_2" swiper-animate-effect='bounceInLeft' swiper-animate-duration='1s' swiper-animate-delay='0.3s'>
                	<li><label for="t_2_1"><input id="t_2_1" type="radio" name="t_2"/><span>3天以内</span></label></li>
                	<li><label for="t_2_2"><input id="t_2_2" type="radio" name="t_2"/><span>3~7天以内</span></label></li>
                	<li><label for="t_2_3"><input id="t_2_3" type="radio" name="t_2"/><span>一周前</span></label></li>
                	<li><label for="t_2_4"><input id="t_2_4" type="radio" name="t_2"/><span>一个月前</span></label></li>
                </ul>
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/JXTZ@3x.png" class="ani img_2" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.7s'>
            </div>
            <div class="swiper-slide pages page4">
                <ul class="ani topic topic_3" swiper-animate-effect='bounceInLeft' swiper-animate-duration='1s' swiper-animate-delay='0.3s'>
                	<li><label for="t_3_1"><input id="t_3_1" type="radio" name="t_3"/><span>手机微信、QQ</span></label></li>
                	<li><label for="t_3_2"><input id="t_3_2" type="radio" name="t_3"/><span>手机通话</span></label></li>
                	<li><label for="t_3_3"><input id="t_3_3" type="radio" name="t_3"/><span>当面交谈</span></label></li>
                	<li><label for="t_3_4"><input id="t_3_4" type="radio" name="t_3"/><span>其他</span></label></li>
                </ul>
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/JXTZ@3x.png" class="ani img_2" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.7s'>
            </div>
            <div class="swiper-slide pages page5">
                <ul class="ani topic topic_4" swiper-animate-effect='bounceInLeft' swiper-animate-duration='1s' swiper-animate-delay='0.3s'>
                	<li><label for="t_4_1"><input id="t_4_1" type="radio" name="t_4"/><span>挺好的，一直很关注</span></label></li>
                	<li><label for="t_4_2"><input id="t_4_2" type="radio" name="t_4"/><span>好像还不错吧</span></label></li>
                	<li><label for="t_4_3"><input id="t_4_3" type="radio" name="t_4"/><span>最近出了点状况</span></label></li>
                	<li><label for="t_4_4"><input id="t_4_4" type="radio" name="t_4"/><span>额。。不是很清楚</span></label></li>
                </ul>
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/JXTZ@3x.png" class="ani img_2" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.7s'>
            </div>
            <div class="swiper-slide pages page6">
                <ul class="ani topic topic_5" swiper-animate-effect='bounceInLeft' swiper-animate-duration='1s' swiper-animate-delay='0.3s'>
                	<li><label for="t_5_1"><input id="t_5_1" type="radio" name="t_5"/><span>刚刚回过啊</span></label></li>
                	<li><label for="t_5_2"><input id="t_5_2" type="radio" name="t_5"/><span>3天左右吧，还行</span></label></li>
                	<li><label for="t_5_3"><input id="t_5_3" type="radio" name="t_5"/><span>一周多了</span></label></li>
                	<li><label for="t_5_4"><input id="t_5_4" type="radio" name="t_5"/><span>一个多月了，离家远，有点忙</span></label></li>
                	<li><label for="t_5_5"><input id="t_5_5" type="radio" name="t_5"/><span>大概半年以上了吧</span></label></li>
                </ul>
                <img src="{{ config('app.source_url') }}applet/kinship/static/img/jxda@5x.png" class="ani img_2" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.7s'>
            </div>
            <div class="swiper-slide pages page7">
                <!--<div class="ani topic topic_6" swiper-animate-effect='bounceInLeft' swiper-animate-duration='.5s' swiper-animate-delay='0.3s'>
                	<p>古人云；树欲静而风不止，子欲养而亲不待</p>
                	<p>亲情无价</p>
                	<p>每个人有自己的理解</p>
                	<p>我们希望通过几个简单的问题</p>
                	<p>唤醒你对爸爸妈妈的关注。</p>
                </div>
                <div class="ani topic topic_7" swiper-animate-effect='bounceInLeft' swiper-animate-duration='.5s' swiper-animate-delay='0.3s'>
                	<p>无论你是   Cindy，David，</p>
                	<p>还是 小花，大狗</p>
                	<p>国庆中秋团圆日</p>
                	<p>忙一点，远一点，记得回家看看</p>
                </div>
                <img src="static/img/an@3x.png" class="ani share" swiper-animate-effect='bounceIn' swiper-animate-duration='.5s' swiper-animate-delay='0.7s'>-->
            	<div class="btn"></div>
            </div>
        </div>
    </div>
    <!--音乐-->
    <img class="music music-animate" src="{{ config('app.source_url') }}applet/kinship/static/img/music.png" />
    <audio id="bgmusic" src="{{ config('app.source_url') }}applet/kinship/static/audio/Ireallyloveyou.mp3" loop autoplay preload></audio>
	<!--蒙板-->
	<div class="board hide">
		<img src="{{ config('app.source_url') }}applet/kinship/static/img/图层3@3x.png"/>
		<img src="{{ config('app.source_url') }}applet/kinship/static/img/4@3x.png"/>
	</div>
	<!--提示-->
	<div class="hint hide">您还没有选择！</div>
</body>
</html>
<script src="{{ config('app.source_url') }}applet/kinship/static/js/jquery-1.11.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}applet/kinship/static/js/swiper.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}applet/kinship/static/js/swiper.animate.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}applet/kinship/public/js/index.js" type="text/javascript" charset="utf-8"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script type="text/javascript">
    $(function(){
        var url = location.href.split('#').toString();
        $.get("/applet/weixin/getWeixinSecretKey",{"url": url},function(data){
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

        wx.ready(function () {  

            //分享到朋友圈
            wx.onMenuShareTimeline({  
                title: '史上最残酷的亲情考验，你敢来吗？', // 分享标题  
                desc: '史上最残酷的亲情考验，你敢来吗？', // 分享描述
                link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线  
                imgUrl: "{{ config('app.source_url') }}applet/kinship/static/img/pic300-2.jpg", // 分享图标  
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
                title: '史上最残酷的亲情考验，你敢来吗？', // 分享标题  
                desc: '史上最残酷的亲情考验，你敢来吗？', // 分享描述  
                link: url, // 分享链接  
                imgUrl: "{{ config('app.source_url') }}applet/kinship/static/img/pic300-2.jpg", // 分享图标  
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
                title: '史上最残酷的亲情考验，你敢来吗？', // 分享标题
                desc: '史上最残酷的亲情考验，你敢来吗？', // 分享描述
                link: url, // 分享链接
                imgUrl: "{{ config('app.source_url') }}applet/kinship/static/img/pic300-2.jpg", // 分享图标
                success: function () { 
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () { 
                   // 用户取消分享后执行的回调函数
                }
            });

            //分享到腾讯微博
            wx.onMenuShareWeibo({
                title: '史上最残酷的亲情考验，你敢来吗？', // 分享标题
                desc: '史上最残酷的亲情考验，你敢来吗？', // 分享描述
                link: url, // 分享链接
                imgUrl: "{{ config('app.source_url') }}applet/kinship/static/img/pic300-2.jpg", // 分享图标
                success: function () { 
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () { 
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.error(function(res){  
                // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。  
                //alert("errorMSG:"+res);  
            });  
        });
    });
    
</script>

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?fb1293a38043285a17e8c985a616254f";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>