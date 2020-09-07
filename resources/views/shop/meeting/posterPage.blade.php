<!--退款添加留言页-->
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
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <style type="text/css">
        html,body{
            height:100%;
            background:#fff;
        }
        .container{
            text-align: center;
            padding-top: 20px;
        }
        .container .header{
            min-height:400px;
        }
        img{
            width: 80%;
            margin: 0 auto;
            /*min-height: 400px;*/
        }
        p{
            padding:20px;
            font-size:18px;
            color:#333333;
            font-weight: bold;
        }
    </style>
</head>
    <body>
        <div class="container ">
            <div class="header">
                <img src="{{ $img }}">    
            </div>
            <p>长按图片保存至相册</p>
            <!-- 二恶烷翁 -->
        </div>
        <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
        <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
        <script type="text/javascript">
           var wid = '{{session("wid")}}';
           var share_link= "";
           $(function(){
            var url = location.href.split('#').toString();
            var urladd = "";
            
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
            wx.ready(function () {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
                    desc: '会搜股份新年钜献', // 分享描述
                    link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
                    desc: '会搜股份新年钜献', // 分享描述
                    link: url, // 分享链接
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
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
                    title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
                    desc: '会搜股份新年钜献', // 分享描述
                    link: url, // 分享链接
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
                    success: function () {
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                       // 用户取消分享后执行的回调函数
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
                    desc: '会搜股份新年钜献', // 分享描述
                    link: url, // 分享链接
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
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
        })
        document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
            // 通过下面这个API隐藏右上角按钮
            WeixinJSBridge.call('hideOptionMenu');
        });
        </script>
    </body>
</html>
