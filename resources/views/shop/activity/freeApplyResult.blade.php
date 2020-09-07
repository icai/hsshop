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
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <style type="text/css">
        .header_icon{
            text-align:center;
            margin-top:50px;
        } 
        .header_icon img{
            width:100px;
        }
        .content{
            min-height:auto;
        }
        .content .content_title{
            text-align:center;
            font-weight: bold;
            font-size:18px;
            padding:20px;
        }
        .content .content_info{
            text-align:center;
            color: #999;
            margin-top: 20px;
        }
        
        .save_btn {
            background: #09bb07;
            color: #fff;
            line-height: 50px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px;
            margin-top: 55px;
            border-radius: 25px;
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
        .info{
            font-size: 18px;
            color: #333;
            text-align: center;
            margin-top: 40px;
            font-weight: 600;
        }
        .info>div{
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div id="container" :style="{background:bg_color}">
    <div class="header_icon">
        <img src="{{ config('app.source_url') }}shop/images/header_icon.png">
    </div>
    <div class="content">
        <div class="content_title">恭喜您领取成功！</div>
        <div class="content_info">具体结果我们已用短信通知您，请注意查看！</div>
        <div class="info">
            <div>分享推荐两个企业用户，您可免费领取</div>
            <div>价值19800的小程序旗舰版系统一套</div>
        </div>
        <div class="save_btn">
            分享给好友
        </div>
    </div>
</div>
<div class="zhezhao">
    <div class="share_model">
        <img src="{{ config('app.source_url') }}shop/images/freeApplyResult2.png"></div>
    <div class="close_share"></div>
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
    var id = {!!$id!!};
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
        var share_title = '价值39800元的微商城旗舰版系统及运营培训课程限时免费领！';
        var share_desc = "全方位后台操作解答，丰富的营销工具、百余种组合营销模式等你来学习！";
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
    })
</script>
<!-- <script type="text/javascript">
    $(function(){
        var flag = true;
        $('.weui-btn').click(function(){
            if(!flag) return;
            flag = false;
            var data = {};
            data.name = $('input[name="name"]').val();
            data.phone = $('input[name="phone"]').val();
            data.company_name = $('input[name="company_name"]').val();
            data.company_position = $('input[name="company_position"]').val();
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
            $.post('',data,function(res){
                if(res.status == 1){
                    tool.tip('領取成功')
                    setTimeout(function(){
                        window.location.reload();
                    },2000)
                }
                flag = true;
            })
        })
    })
</script> -->
</body>
</html>