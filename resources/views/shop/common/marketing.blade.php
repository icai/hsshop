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
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    @yield('head_css')
</head>
<body>
    <!-- 顶部导航 开始 -->
    <nav>
        <div class="header">
            <div class="js-mp-info share-mp-info ">
                <a href="/shop/index/<?php echo e($__weixin['id']); ?>" class="page-mp-info">
                    @if ( !empty($__weixin['logo']) )
                    <img width="24" height="24" src="{{ imgUrl($__weixin['logo']) }}" class="mp-image">
                    @else
                    <img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" width="40" height="40" class="mp-image">
                    @endif
                    <i class="mp-nickname">{{$__weixin['shop_name']}}</i>
                </a> 
                <div class="links">
                    <a href="javascript:void(0);" class="attention zx_attention top_attention hide"></a>
                    @if($reqFrom != 'aliapp' && $reqFrom != 'baiduapp')
                    <a href="/shop/member/index/{{session('wid')}}" class="mp-homepage">会员主页</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    <!-- 顶部导航 结束 -->
    <!-- 主体内容 开始 -->
    @yield('main')
    <!-- 主体内容 结束 -->
    <script type="text/javascript">
        var APP_HOST = "{{ config('app.url') }}";
        var APP_IMG_URL = "{{ imgUrl() }}";
        var APP_SOURCE_URL = "{{ config('app.source_url') }}";
        var CHAT_URL = "{{config('app.chat_url')}}";
        var CDN_IMG_URL = "{{config('app.cdn_img_url')}}";
    </script>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
    @if($reqFrom == 'aliapp')
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    @if($reqFrom == 'aliapp')
    <script type="text/javascript">
        // my.navigateTo({url: '/pages/index1/index1'});
        $.ajaxSettings = $.extend($.ajaxSettings, {
            beforeSend: beforeSend,
            complete:complete,
        });
         // alert(444)
         function complete(xhr, status){
            // window.location.href="http://www.baidu.com"
            if(xhr.responseText.code && xhr.responseText.code == 40004){
                window.location.href = "/aliapp/authorization/login?fromUrl=" + encodeURIComponent(window.location.href)
            }
         }
         function getQueryString(name) { 
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
            var r = window.location.search.substr(1).match(reg); 
            if (r != null) return unescape(r[2]); 
            return null; 
         }
         var aliToken = getQueryString('aliToken');
         if(aliToken){
            window.localStorage.setItem('aliToken',aliToken);
         }else{
            aliToken = window.localStorage.getItem('aliToken');
         }
         function beforeSend(xhr, settings) {
            // console.log(xhr)
            xhr.setRequestHeader("aliToken", aliToken);
            // var context = settings.context
            // console.log(44224)
            // if (settings.beforeSend.call(context, xhr, settings) === false ||
            //     triggerGlobal(settings, context, 'ajaxBeforeSend', [xhr, settings]) === false)
            //   return false

            // triggerGlobal(settings, context, 'ajaxSend', [xhr, settings])
          }
          var xcx_share_title = '{{ $shareData["share_title"] }}';
          var xcx_share_desc = '{{ $shareData["share_desc"] }}';
          var url = location.href.split('#').toString();
          var xcx_img_url =  '{{ $shareData["share_img"] }}'; // 分享图标
          if(window.location.search){
            url += '&_pid_='+ '{{ session("mid") }}';
          }else{
            url += '?_pid_='+ '{{ session("mid") }}';
          }
          var xcx_share_url = url;
          my.postMessage({share_title:xcx_share_title,share_desc:xcx_share_desc,share_url:xcx_share_url,imageUrl:xcx_img_url});
    </script>
    @endif
    @if($reqFrom == 'baiduapp')
    <script type="text/javascript" src="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan.js"></script>
    @endif
    @if($reqFrom == 'baiduapp')
    <script type="text/javascript">
        $.ajaxSettings = $.extend($.ajaxSettings, {
            beforeSend: beforeSend,
            complete:complete,
        });
//       alert(444)
         function complete(xhr, status){
            // window.location.href="http://www.baidu.com"
            if(xhr.responseText.code && xhr.responseText.code == 40004){
                window.location.href = "/baiduapp/login?fromUrl=" + encodeURIComponent(window.location.href)
            }
         }
         function getQueryString(name) { 
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
            var r = window.location.search.substr(1).match(reg); 
            if (r != null) return unescape(r[2]); 
            return null; 
         }
         var baiduToken = getQueryString('baiduToken');
         if(baiduToken){
            window.localStorage.setItem('baiduToken',baiduToken);
         }else{
            baiduToken = window.localStorage.getItem('baiduToken');
         }
         function beforeSend(xhr, settings) {
            xhr.setRequestHeader("baiduToken", baiduToken);
          }
    </script>
    @endif
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        //关注我们
        $('.attention').click(function(){
            $('.follow_us').show();
        });
        $(".code img").click(function(e){
            e.stopPropagation()
        })
        $('.follow_us').click(function(){
            $('.follow_us').hide();
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
            @if($reqFrom == 'wechat')
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
                        // 用户确认分享后执行的回调函数(分享成功后送积分)
                        $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
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
                        // 用户确认分享后执行的回调函数(分享成功后送积分)
                        $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
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
                        // 用户确认分享后执行的回调函数(分享成功后送积分)
                        $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
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
                        // 用户确认分享后执行的回调函数(分享成功后送积分)
                        $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
                        });
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
            @endif


        });

    </script>
    @yield('page_js')
</body>
</html>
