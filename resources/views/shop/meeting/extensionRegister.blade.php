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
        .header h3{
            color: #FE0800;
            font-size: 25px;
            padding: 40px;
            line-height: 39px;
            font-weight: bold;
            text-align: center;
        }
        .title_info{
            padding: 20px;
            border: 1px solid #CCCCCC;
            width: 80%;
            margin: 0 auto;
            margin-bottom: 40px;
            text-align:center;
        }
        .apply_input{
            color:#fff;
        }
        .apply_input .info_title{
            width: 80%;
            background: #C7292C;
            text-align: center;
            padding: 6px;
            color: #fff;
            border-radius: 14px;
            margin: 0 auto;
            font-weight: bold;
        }
        .apply_input p{
            color: #fff;
            font-size: 12px;
            text-align: center;
            padding: 8px;
        }
        .apply_input .input_item{
            text-align:center;
            margin: 20px 0;
        }
        .apply_input .input_item label{
            color: #333;
            width: 80px;
            display: inline-block;
            text-align: right;
            font-size:20px;
        }
        .apply_input .input_item input{
            border: 1px solid #ddd;
            width: 60%;
            display: inline-block;
            padding: 10px;
            border-radius:3px;
            outline: none;
        }
        input::-webkit-input-placeholder{
            color:#fff;
        }
        input::-moz-placeholder{   /* Mozilla Firefox 19+ */
            color:#fff;
        }
        input:-moz-placeholder{    /* Mozilla Firefox 4 to 18 */
            color:#fff;
        }
        input:-ms-input-placeholder{  /* Internet Explorer 10-11 */ 
            color:#fff;
        }
        .apply_btn{
            padding:10px;
        }
        .apply_btn p {
            width:80%;
            background:#09BA07;
            border-radius: 8px;
            padding:15px;
            color:#fff;
            margin:0px auto;
            text-align:center;
            font-size:18px;
        }
        .img img{
            width:100%;
        }
        
        .popout-confirm .confirm-content {
            line-height: 65px !important;
        }   
    </style>
</head>
    <body>
        <div class="container " style="min-height: 650px;">
           <div class="header">
               <h3>免费领取小程序实战运营总裁班</h3>
           </div>
           <p class="title_info">请填写以下信息，免费领取总裁班课程</p>
           <div class="apply_input">
               <div class="input_item">
                   <label>姓名：</label>
                   <input type="text" name="name">
               </div>
               <div class="input_item">
                   <label>手机号：</label>
                   <input type="number" name="phone">
               </div>
               <div class="input_item">
                   <label>公司：</label>
                   <input type="text" name="company_name">
               </div>
               <div class="input_item">
                    <label>职务：</label>
                   <input type="text" name="company_position">
               </div>
               <div class="apply_btn">
                   <p>提交</p>
               </div>
           </div>
           <div class="img">
               <img src="{{ config('app.source_url') }}shop/images/1_04_01.jpg">
               <img src="{{ config('app.source_url') }}shop/images/1_04_02.jpg">
               <img src="{{ config('app.source_url') }}shop/images/1_04_03.jpg">
               <img src="{{ config('app.source_url') }}shop/images/1_04_04.jpg">
               <img src="{{ config('app.source_url') }}shop/images/1_04_05.jpg">
               <img src="{{ config('app.source_url') }}shop/images/1_04_06.jpg">
               <img src="{{ config('app.source_url') }}shop/images/1_04_07.jpg">
           </div>
        </div>
        <form name="form1" id="form1" enctype="multipart/form-data" method="post" style="opacity: 0;">
            <input type="file" class="add-picture" id="upload_img" name="file" style="width: 1px;height: 1px;">
        </form>
        <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
        <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
        <script type="text/javascript">
           var share_link= "{{$share_link}}";
           $(function(){
            var url = location.href.split('#').toString();
            var urladd = "";
            if(share_link){
                urladd = '&shareMid='+'{{ $share_link }}';
            }
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
                    title: '小程序免费啦，大家都在领，名额不多了', // 分享标题
                    desc: '会搜股份新年钜献', // 分享描述
                    link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: "{{ config('app.source_url') }}shop/images/apply_share.jpg", // 分享图标
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
                    //alert("errorMSG:"+res);
                });
            });
             $('.apply_btn p').click(function(){
                tool.hitEgg({
                    type:2,
                    sureBtn:function(){
                        var data = {};
                            data.name = $('input[name="name"]').val();
                            data.phone = $('input[name="phone"]').val();
                            data.company_name = $('input[name="company_name"]').val();
                            data.company_position = $('input[name="company_position"]').val();
                            data._token = $('meta[name="csrf-token"]').attr('content');
                        if(data.name == ""){
                            tool.tip('姓名不能为空！');
                            return;
                        }
                        if(data.phone == ""){
                            tool.tip('手机号不能为空！');
                            return;
                        }
                        if(!/^[1][3,4,5,7,8,9][0-9]{9}$/.test(data.phone)){
                            tool.tip('请输入正确的手机号！');
                            return;   
                        }
                        if(data.company_name == ""){
                            tool.tip('公司不能为空！');
                            return;
                        }
                        if(data.company_position == ""){
                            tool.tip('职务不能为空！');
                            return;
                        }
                        $.post('/shop/meeting/extensionRegister/{{session("wid")}}',data,function(res){
                            if(res.status == 1){
                                tool.tip('提交成功！')
                                window.location.href="/shop/meeting/registerSuccess/{{session('wid')}}";
                            }
                        })           
                    },
                    cancelBtn:function(){},
                    sureTitle:'确定',
                    content:'确认信息无误？'
                })
                return;
             })
           })
        </script>
    </body>
</html>
