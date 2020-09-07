<!--退款添加留言页-->
<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>移动互联网实战总裁班现场</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <style type="text/css">
    body{
        background:white
    }
        .container{
            background:#fff;
        }
        .title{
            text-align: center;
            color: #000;
            font-size: 20px;
            padding: 30px;
            font-weight: bold;
        }
        .item .input_item{
            text-align:left;
            display:flex;
            display: -webkit-flex;
            display: -ms-flexbox; 
            display: -moz-box;
            display: -webkit-box;
            border-bottom:1px solid #e6e6e6;
            margin :0 20px
        }
        .item .input_item p{
            font:16px/58px '微软雅黑';
            color:#999
        }
        .item .input_item input{
            border:none;
            padding:20px;
            outline:none;
            text-align:left;
        }
        .item .input_item:nth-of-type(2) input{
            padding-top:18px
        }
        .item .input_item:nth-of-type(1) input{
            padding-top:19px
        }
        input::-webkit-input-placeholder, textarea::-webkit-input-placeholder { 
            color: #999;
            text-align:center;
        } 
        input:-moz-placeholder, textarea:-moz-placeholder { 
            color: #999; 
            text-align:center;
        } 
        input::-moz-placeholder, textarea::-moz-placeholder { 
            color: #999;
            text-align:center;
        } 
        input:-ms-input-placeholder, textarea:-ms-input-placeholder { 
            color: #999;
            text-align:center;
        }
        .upload p:first-child{
            font-size:16px;
        }
        .upload p{
            text-align:left;
            color:#333;
            padding:30px 10px 20px 20px
        }
        .upload p span{
            color:red;
            font-size:12px;

        }
        .uplaod_image{
            /* height:120px; */
            width:70%;
            margin:0 auto;
            position:relative;
        }
        .uplaod_image input{
            position:absolute;
            top:0;
            right:0;
            left:0;
            bottom:0;
            opacity: 0;
            width:100%;
        }
        .uplaod_image .upload_img{
            display:block;
            margin:auto;
            width:100%;
            /* height:200px; */
        }
        .uplaod_image .upload_loading{
            background:rgba(0,0,0,.7);
            width:100px;
            padding:20px;
            padding-bottom:10px;
            border-radius:10px;
            text-align:center;
            position:absolute;
            top:55px;
            left:0;right:0;margin:0 auto;
            display:none;
        }
        .uplaod_image .upload_loading img{
            width:40px
        }
        .uplaod_image .upload_loading p{
            padding:0;
            width:100%;
            text-align:center;
            padding-top:20px;
            color:white
        }
        .action_btn{
            text-align:center;
            padding-top:20px;
        }
        .btn-green{
            width:80%;
            line-height:30px;
            font-size:16px;
            margin:0 auto;
        }
        textarea:disabled, input:not([type]):disabled, input[type="color" i]:disabled, input[type="date" i]:disabled, input[type="datetime" i]:disabled, input[type="datetime-local" i]:disabled, input[type="email" i]:disabled, input[type="month" i]:disabled, input[type="password" i]:disabled, input[type="number" i]:disabled, input[type="search" i]:disabled, input[type="tel" i]:disabled, input[type="text" i]:disabled, input[type="time" i]:disabled, input[type="url" i]:disabled, input[type="week" i]:disabled {
            background-color: #fff;
        }
        .upload .upload_tip{
            text-align:center;
            line-height:24px;
            color:#999;
            padding:0
        }
        .reload{
            margin-top:10px;
            padding:10px 20px
        }
    </style>
</head>
    <body>
        <div class="container ">
           <div class="title">请填写您的基本信息</div>
           <div class="item">
               <div class="input_item">
                    <p>您的姓名</p>
                   <input type="text" name="name" @if($register) value="{{$register['name']}}" @endif placeholder="">
               </div>
               <div class="input_item">
                    <p>公司名称</p>
                   <input type="text" name="company" @if($register) value="{{$register['company']}}" @endif placeholder="">
               </div>
               <div class="upload">
                    <p>上传会场实时照片<span>（请上传横幅照片）</span></p>
                    
                    <div class="uplaod_image">
                       <img class="upload_img" src="@if(isset($register['img']) && $register['img'])https://upx.cdn.huisou.cn/{{$register['img']}}" @else {{ config('app.source_url') }}/shop/images/3t3yupload@2x.png  @endif"">
                       <input class="file" type="file" value="">
                       <input type="hidden" name="headimgUrl" value="">
                       <input type="hidden" name="qrcode" value="">
                       <input type="hidden" name="img" value="@if($register){{$register['img']}}" @endif"">
                       <div class="upload_loading">
                            <img src="{{ config('app.source_url') }}/shop/images/3t3yloading.gif">
                            <p>生成海报中...</p>
                       </div>
                   </div>
                   
                   <p class="upload_tip">点击相框可重新上传照片</p>
                  
               </div>
               <div class="action_btn">
                   <p class="btn btn-green">确认提交</p>
               </div>
           </div>
        </div>
        <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
        <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
        <script type="text/javascript">
           var wid = '{{session("wid")}}';
           var register = "";
           @if($register)
               register ={!! json_encode($register) !!} ;
            @endif
           var imgUrl = "{{ imgUrl() }}";
           var branch = "{{$branch}}";
           var share_link= "";
           var upload_flag = false;
           var reloadTimer = null;
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
            if(window.location.search){
                url += '&_pid_='+ '{{ session("mid") }}' + urladd;
            }else{
                url += '?_pid_='+ '{{ session("mid") }}' + urladd;
            }
            wx.ready(function () {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: '移动互联网实战总裁班现场', // 分享标题
                    desc: '会搜股份', // 分享描述
                    link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share_n.jpg", // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: '移动互联网实战总裁班现场', // 分享标题
                    desc: '会搜股份', // 分享描述
                    link: url, // 分享链接
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share_n.jpg", // 分享图标
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
                    title: '移动互联网实战总裁班现场', // 分享标题
                    desc: '会搜股份', // 分享描述
                    link: url, // 分享链接
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share_n.jpg", // 分享图标
                    success: function () {
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                       // 用户取消分享后执行的回调函数
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: '移动互联网实战总裁班现场', // 分享标题
                    desc: '会搜股份', // 分享描述
                    link: url, // 分享链接
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share_n.jpg", // 分享图标
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
            $('.file').change(function(e){
                var formData = new FormData();
                formData.append('file', $(this)[0].files[0]);
                var filename = 'hsshop/'+ branch + '/' + '{{session("mid")}}' + '/' +  new Date().getTime() + parseInt(10000*Math.random()) + '.' + $(this)[0].files[0]['type'].split("/")[1];
                $('.upload_loading').show();
                //执行20s后页面刷新
                reloadTimer=setTimeout(function(){
                    $('.upload_loading').show().html('<p>网络异常</p><button class="reload btn">刷新</button>');
                    $('.reload').click(function(){
                        location.reload()
                    });
                }, 20000);
                $.get('/shop/meeting/getVideoSign',{save_key:filename},function(data){
                    if(typeof data == 'string'){
                        data = JSON.parse(data);
                    }
                    formData.append('policy', data.policy);
                    formData.append('authorization', data.authorization);
                    $('input[name="headimgUrl"]').val(data.headimgUrl);
                    $('input[name="qrcode"]').val(data.qrcode);
                    var http = new XMLHttpRequest();
                    http.onreadystatechange = function(){
                        if(http.readyState == 4){
                            if(http.status >= 200 && http.status <300 || http.status == 304){
                                d = JSON.parse(http.response);
                                $('.uplaod_image .upload_img').attr('src','https://upx.cdn.huisou.cn/' +""+ d.url);
                                $('input[name="img"]').val(d.url);
                                var image = $('.upload_img').eq(0);
                                getImageNatural(image,function(changeImage){
                                    if(changeImage.w>changeImage.h){
                                        $('.upload_img').eq(0).css({width:'100%',height:'auto'})
                                    }else{
                                        $('.upload_img').eq(0).css({width:'auto',height:'200px'})                                        
                                    }
                                })
                            }
                            clearTimeout(reloadTimer);
                            $('.upload_loading').hide();
                            upload_flag = true;
                        }
                    }
                    http.open('post', 'https://v0.api.upyun.com/huisoucn');
                    http.send(formData);
                })
            })
            
            var flag = false;
            $('.btn-green').click(function(){
                var postData = {};
                    postData.name = $('input[name="name"]').val();
                    postData.company = $('input[name="company"]').val();
                    postData.img = $('input[name="img"]').val();
                    postData._token = $('meta[name="csrf-token"]').attr('content');
                    postData.headimgUrl = $('input[name="headimgUrl"]').val();
                    postData.qrcode = $('input[name="qrcode"]').val();
                    if(register){
                        postData.id = register.id;
                    }
                if(postData.name == ""){
                    tool.tip('姓名不能为空！');
                    return;
                }
                if(postData.company == ""){
                    tool.tip('公司不能为空！');
                    return;
                }
                if(postData.img == ""){
                    tool.tip('会场照片不能为空！');
                    return;
                }
                if(flag) return;
                flag = true;
                if(upload_flag){
                    $('.upload_loading').show().children('p').text('传输数据中...');
                    $.post('/shop/meeting/defaulRegister/'+ wid,postData,function(data){
                    if(data.status == 1){
                            $('.upload_loading').hide();
                            tool.tip('提交成功');
                            setTimeout(function(){
                                flag = false;
                                window.location.href="/shop/meeting/posterPage/" + wid;
                            },2000)
                        }else{
                            tool.tip(data.info);
                            flag = false;
                        }
                    })
                }   
            })

            //请求生成二维码 add by wuxiaoping
            $.get('/shop/meeting/defaultUpload',{},function(data){
                if (data.status == 1) {
                }else{
                    tool.tip(data.info);
                }
            });
            
            //获取图片的原始大小
            function getImageNatural(img,cb){
                if (img.naturalWidth) { // 现代浏览器
                    nWidth = img.naturalWidth
                    nHeight = img.naturalHeight
                    cb({w:nWidth,h:nHeight})
                } else { // IE6/7/8
                    var image = new Image();
                    image.src = img.attr('src');
                    if(image.complete){
                        cb({w:image.width,h:image.height})
                    }else{
                        image.onload = function(){
                            var w = image.width;
                            var h = image.height;
                            cb({w:w,h:h})
                        }                           
                    }
                }
            }
        })
        </script>
    </body>
</html>
