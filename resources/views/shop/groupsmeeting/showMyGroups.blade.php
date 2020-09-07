<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showMyGroups.css"  media="screen"/>
    <style>
        .tabNav{
            margin-top:0;
            position:fixed;
            top:0;
            font-size:17px;
            height:50px;
            width:calc(100% - 10px);
            z-index:100;
            font-weight:bold;
            border-bottom:1px solid #e5e5e5;
            line-height:50px
        }
        .list_detail_status{
            font:15px/40px "微软雅黑";
            padding:0 10px;
        }
        .orderList .goods_list{
            padding-top:50px
        }
        .orderList .goods_list li .list_detail .goods_info{
            padding:10px;
            background-color:#f5f5f5
        }
        .orderList .goods_list li .list_detail .goods_info .describe{
            margin: 0 30px 0 10px;
            
        }
        .orderList .goods_list li .list_detail .goods_info .describe .goods_title{
            font-weight:normal;
            font:16px/20px "微软雅黑";
            overflow:visible;
            -webkit-line-clamp:10
        }
        .orderList .goods_list li{
            margin-top:10px
        }
        .orderList .goods_list li .list_fun .btnSearch{
            color:#666;
            border:1px solid #999
        }
        .pin_info{
            padding:10px
        }
        .share_info{
            font-size:14px;
            text-align:left
        }
        .list_fun{
            display:flex;
            display: -webkit-flex;
            align-items:center;
            justify-content:space-between;
            padding:0 10px
        }
        .orderList .goods_list li .list_fun .btn{
            line-height:2.5;
            width:120px;
            font-size:16px;
            font-weight:bold;
            margin:8px 1px
        }
        .pin_info .member_pic{
            padding-right:20px
        }
        .pin_info .member_pic .group_member{
            width:30px;
            height:30px;
            border-radius:50%;
            margin:0;
        }
        .pin_info .member_pic .group_member img{
            width:30px;
            height:30px;
            border-radius:50%;
            margin:0;
            margin-left:-5px
        }
        .pin_info{
            display:flex;
            display: -webkit-flex;
            align-items:center;
            justify-content:space-between;
        }
        .pin_info .member_pic .group_info{
            margin:7px 0 0 20px
        }
        .group_leader{
            
            position:relative
        }
        .item_price{
            color:#333;
            text-align:right;
            font-size:16px;
            line-height:30px
        }
        .orderList .goods_list li .list_fun{
            margin-right:0
        }
        .price_count{
            flex-grow:1;
            text-align:right;
            font-size:16px
        }
        .lead{
            position:fixed;
            right:21px;
            bottom:34px;
            width:55px
        }
        .orderList .goods_list li .list_detail .goods_info .imleader{
            width:82px;
            height:auto;
            position:absolute;
            right:20px;
            bottom:0
        }
        /* 支付 */
        .payment{
            flex-grow:1;
            text-align:right
        }
        /* 字体颜色 */
        .grey_999{
            color:#999;
            font-size:16px
        }
        .black_33{
            color:#333;
        }
        /* 浮动 */
        .clearfix:after{content:".";display:block;height:0;clear:both;visibility:hidden}
        .clearfix li{
            float:left
        }
        /* 弹窗 */
        #pop_up{
            z-index:1000;            
            position:fixed;
            top:0;
            left:0;
            height:100%;
            width:100%;display:none
        }
        #pop_up .shade{
            background-color:rgba(0,0,0,.8);
            height:100%;
            width:100%
        }
        
        #pop_up .pop_content{
            position:absolute;
            top:0;bottom:0;left:0;right:0;
            margin:auto;
            z-index:100;
            height:500px;
            width:85%;
            border-radius:5px;
            background:white;
            padding:10px
        }
        #pop_up .attend{
            font-weight:bold;
            padding-top:20px;
        }
        
        #pop_up .title{
            text-align:center;
            font:20px/30px "微软雅黑";
            border-bottom:1px solid #e6e6e6;
            padding-bottom:10px;
            font-weight:bold
        }
        #pop_up .pop_text{
            font:16px/25px "微软雅黑";
            padding-top:10px;
            font-weight:bold;
            text-align:center
            
        }
        #pop_up .qrcode>div{
            width:180px;
            height:160px;
            margin:5px auto;
            text-align:center;
            display:flex;
            flex-direction:column;
        }
        #pop_up .qrcode>div img{
            width:180px;
            /* height:131px; */
            border:none;
            outline:none
        }
        #pop_up .qrcode_tip{
            color:#333;
            font-size:17px;
            display:block;
            font-weight:bold;
            padding:10px 0;
            border-bottom:1px solid #e6e6e6
        }
        #pop_up .code_tip{
            font:18px/40px "微软雅黑";
            font-weight:normal
        }
        #pop_up .code_step{
            font:16px/22px "微软雅黑";
            text-align:left;
            width:230px;
            position:absolute;
            left:0;
            right:0;
            margin:0 auto
        }
        #pop_up .btn_wrap{
            position:absolute;
            bottom:20px;
            left:0;right:0;
            text-align:center
        }
        #pop_up .btn_wrap>.btn{
            width:60%;
            font:18px/40px "微软雅黑";
            color:white;
            font-weight:bold;
            background:#B0282C;
            border-radius:6px;
        }
        #pop_up .close_btn{
            position:absolute;
            right:10px;
            top:10px;
        }
        #pop_up .close_btn img{width:22px}

        #pop_up_click{
            z-index:1000;            
            position:fixed;
            top:0;
            left:0;
            height:100%;
            width:100%;display:none
        }
        #pop_up_click .shade{
            background-color:rgba(0,0,0,.8);
            height:100%;
            width:100%
        }
        
        #pop_up_click .pop_content{
            position:absolute;
            top:0;bottom:0;left:0;right:0;
            margin:auto;
            z-index:100;
            @if(session('wid') == '626' || session('wid') == '661')
            height:480px;
            @elseif(session('wid') == '634')
            height:440px;
            @endif
            width:85%;
            border-radius:5px;
            background:white;
            padding:10px
        }
        #pop_up_click .attend{
            font-weight:bold;
            padding-top:20px;
        }
        
        #pop_up_click .title{
            text-align:center;
            font:20px/30px "微软雅黑";
            border-bottom:1px solid #e6e6e6;
            padding-bottom:10px;
            font-weight:bold
        }
        #pop_up_click .pop_text{
            font:16px/25px "微软雅黑";
            padding-top:10px;
            font-weight:bold;
            text-align:center
            
        }
        #pop_up_click .qrcode>div{
            width:180px;
            height:160px;
            margin:5px auto;
            text-align:center;
            display:flex;
            flex-direction:column;
        }
        #pop_up_click .qrcode>div img{
            width:180px;
            /* height:131px; */
            border:none;
            outline:none
        }
        #pop_up_click .qrcode_tip{
            color:#333;
            font-size:17px;
            display:block;
            font-weight:bold;
            padding:10px 0;
            border-bottom:1px solid #e6e6e6
        }
        #pop_up_click .code_tip{
            font:18px/40px "微软雅黑";
            font-weight:normal
        }
        #pop_up_click .code_step{
            font:16px/22px "微软雅黑";
            text-align:left;
            width:230px;
            position:absolute;
            left:0;
            right:0;
            margin:0 auto
        }
        #pop_up_click .btn_wrap{
            position:absolute;
            bottom:20px;
            left:0;right:0;
            text-align:center
        }
        #pop_up_click .btn_wrap>.btn{
            width:60%;
            font:18px/40px "微软雅黑";
            color:white;
            font-weight:bold;
            background:#B0282C;
            border-radius:6px;
        }
        #pop_up_click .close_btn{
            position:absolute;
            right:10px;
            top:10px;
        }
        #pop_up_click .close_btn img{width:22px}

        .share_mask .share_model{
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }
        .share_mask .share_model img{
            width: 100%;
            position: relative;
        }
        .share_mask .close_share{width: 40px;height: 40px;margin: 0 auto;position: relative;top: -30px;}
        [v-cloak] {
        display: none;
        }
        


    </style>
</head>

<body>
<div id="app" style="width: 100%;min-height: 100%;">
    <div class="orderList" v-clock v-if="pageShow">
        <!--导航栏-->
        <div class="tabNav flex_between_v">
            <span :class="nav_index==index?'select':''" v-for="(item,index) in nav_bar"  @click="navChange(item.status,index)" v-text="item.title"></span>
        </div>
        <ul class="goods_list" v-if="groupList.length > 0 && tag == 1">
            <li v-for="item in groupList">
                <!--商品详情-->
                <a class="list_detail" :href="'{{config('app.url')}}shop/meeting/groupon/'+item.id+'/'+wid">
                    <p class="list_detail_status">
                        <span>我<span v-if="item.is_head==1">发起的</span><span v-if="item.is_head==0">参与了<span style="color:red">[[item.headImage]]</span>的</span>拼团</span>
                        
                        <span class="co_b1 fr">[[item.statusText]]</span>
                    </p>
                    <div class="goods_info group_leader">
                        <img :src="imgUrl+item.rule.pimg" width="100" />
                        <div class="describe">
                            <p class="goods_title">[[item.rule.ptitle]]</p>
                        </div>
                        <img v-if="item.is_head==1" src="{{ config('app.source_url') }}shop/static/images/iamleader.png" alt="" class="imleader"/>
                        
                        <p class="item_price">
                            <span>￥[[item.rule.min]]</span>
                            <span class="grey_999">x1</span>
                        </p>
                    </div>
                    <div class="pin_info">
                        <ul class="member_pic clearfix">
                            <ul v-for="(details,index) in item.detail" style="float:left">
                                <li class="group_member" v-if="index<=4"><img :src="details.headimgurl" alt=""/></li>
                            </ul>
                            <li class="group_info" ><span class="co_666">[[item.rule.groups_num]]人拼团</span></li>                            
                        </ul>
                        <p class="price_count"><span>实付：￥<span>0.00</span></span></p>
                    </div>
                </a>
                <!--商品列表功能按钮-->
                <div class="list_fun">
                    <p class="share_info" v-if="item.statusText === '待成团'">已有<span>[[item.num]]</span>位好友参与拼团，就差<span>[[item.rule.groups_num-item.num]]</span>个啦！</p>
                    <div class="btn Bred" v-if="item.statusText === '待成团' && item.is_head==1" @click="getShare(item)">邀请好友拼团</div>
                    <div class="btn Bred" v-if="item.statusText === '待成团' && item.is_head==0" @click="getShare(item)">助力好友凑团</div>
                    <p class="share_info" v-if="item.statusText === '拼团失败'">拼团已过期，您可以重新发起拼团</p>
                    <div class="btn Bred" v-if="item.statusText === '拼团失败'"><a :href="'{{config('app.url')}}shop/meeting/detail/'+item.rule_id+'/'+wid" style="color:#fff">重新发起拼团</a></div>
                    <div class="payment" v-if="item.statusText === '已完成'">
                        <a class="btn payment btnSearch"  style="text-align:center" :href="'{{config('app.url')}}shop/meeting/groupon/'+item.id+'/'+wid">查看团详情</a>
                    </div>
                </div>
            </li>
        </ul>



        <ul class="goods_list" v-if="groupList.length > 0 && tag == 2">
            <li v-for="item in groupList">
                <!--商品详情-->
                <a class="list_detail">
                    <p class="list_detail_status">
                        <span>会搜科技股份微商城</span>
                        <span class="co_b1 fr">[[item.statusText]]</span>
                    </p>
                    <div class="goods_info group_leader" v-for="details in item.orderDetail">
                        <img :src="imgUrl+details.img" width="100" />
                        <div class="describe">
                            <p class="goods_title">[[details.title]]</p>
                        </div>
                        
                        <p class="item_price" >
                            <span>￥[[details.price]]</span>
                            <span class="grey_999">x[[details.num]]</span>
                        </p>
                    </div>
                    <div class="pin_info">
                        <p class="price_count" v-if="item.statusText!='待付款' && item.statusText!='已关闭'"><span >实付：￥<span >[[item.pay_price]]</span></span></p>
                    </div>
                </a>
                <!--商品列表功能按钮-->
                <div class="list_fun">
                    <div class="payment">
                        <a class="btn payment Bred" v-if="item.statusText=='待付款'" style="text-align:center" :href="'/shop/pay/index?special=groups&id='+item.id">去支付</a>
                    </div>
                </div>
            </li>
        </ul>

        <div class="noMore" v-if="noMore && groupList.length > 3">没有更多数据</div>
        <div class="noList" v-if="groupList.length == 0">
            <img src="{{ config('app.source_url') }}shop/static/images/order/kong@2x.png">
            <p>您还没有相关团购信息</p>
            <a class="goHome" :href = "'/shop/index/'+wid">去首页看看</a>
        </div>
        
        <div class="share_mask" v-if="shareShow" @click="shareHide">
            <div class="share_model">
            @if(session('wid')== '661')
            <img src="{{ config('app.source_url') }}shop/images/pintuanshare077.png" style="width:100%" @click="shareHide"/>
            @elseif(session('wid') == '626')
            <img src="{{ config('app.source_url') }}shop/images/pintuanshare626.png?t=123" />
            @else
            <img src="{{ config('app.source_url') }}shop/images/pintuanshare066.png" style="width:100%" @click="shareHide"/>
            @endif
            </div>
            <div class="close_share" @click="shareHide"></div>
        </div>
        
        <!-- 数据加载 -->
        <div class="loading" v-if="!noMore && groupList.length > 3">
            <img src="{{ config('app.source_url') }}/shop/static/images/loading.gif">
        </div>
    </div>
    <img src="{{ config('app.source_url') }}shop/static/images/lead01.png" @click.prevent="popUp" alt="" class="lead" />
    <!-- 页面加载 -->
    <div class="pageMask" v-if="!pageShow">
        <img class="pageLoading" src="{{ config('app.source_url') }}/shop/static/images/loading.gif">
    </div>
    
    <!-- 弹窗 -->
    <div id="pop_up" style="display:none">
    <div class="shade" @click="popClose">
        </div>
        <div class="pop_content">
            <div class="title">
            恭喜您拼团成功
            </div>
            <a class="close_btn" @click="popClose"><img src="{{ config('app.source_url') }}shop/static/images/x.png" alt=""/></a>
            <div class="pop_text">
                <p>了解更多详情请关注</p>
                <div class="qrcode">
                    <div>
                        @if(session('wid')== '634')
                        <img src="{{ config('app.source_url') }}/shop/images/hsqrcode1.jpg" alt="" class="qrcodeImage">
                        @elseif(session('wid') == '626' || session('wid') == '661')
                        <img src="{{ config('app.source_url') }}/shop/images/hsqrcode626.jpg" alt="" class="qrcodeImage">
                        @endif
                    </div>
                    <p class="qrcode_tip">长按图片【识别二维码】关注公众号</p>
                    <p class="code_tip">关注公众号方式</p>
                    <ol class="code_step">
                        <li>1.打开微信，点击“添加朋友”</li>
                        <li>2.点击公众号</li>
                        @if(session('wid')== '634')
                        <li>3.搜索“杭州会搜股份”</li>
                        @elseif(session('wid') == '626' || session('wid') == '661')
                        <li>3.搜索“会搜商业智慧”</li>
                        @endif
                        <li>4.点击“关注”，完成</li>
                    </ol>
                </div>
                
            </div>
        </div>
    </div>
    
    <div id="pop_up_click">
        <div class="shade" @click="popClose">
        </div>
        <div class="pop_content">
            <div class="title">
            
            </div>
            <a class="close_btn" @click="popClose"><img src="{{ config('app.source_url') }}shop/static/images/x.png" alt=""/></a>
            <div class="pop_text">
                <p>了解更多详情请关注</p>
                <div class="qrcode">
                    <div>
                        
                        @if(session('wid')== '634')
                        <img src="{{ config('app.source_url') }}/shop/images/hsqrcode1.jpg" alt="" class="qrcodeImage">
                        @elseif(session('wid') == '626' || session('wid') == '661')
                        <img src="{{ config('app.source_url') }}/shop/images/hsqrcode626.jpg" alt="" class="qrcodeImage">
                        @endif
                    </div>
                    <p class="qrcode_tip">长按图片【识别二维码】关注公众号</p>
                    <p class="code_tip">关注公众号方式</p>
                    <ol class="code_step">
                        <li>1.打开微信，点击“添加朋友”</li>
                        <li>2.点击公众号</li>
                        @if(session('wid')== '634')
                        <li>3.搜索“杭州会搜股份”</li>
                        @elseif(session('wid') == '626' || session('wid') == '661')
                        <li>3.搜索“会搜商业智慧”</li>
                        @endif
                        <li>4.点击“关注”，完成</li>
                    </ol>
                </div>
                
            </div>
        </div>
    </div>
    
</div>
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/clipboard.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    //支付成功后返回的弹窗
    if(window.location.href.match(/(tag=2)/g)){
        $('#pop_up').show()
    }
    $('.shade,.close_btn').on('click',function(){
        $('#pop_up').hide()
    });
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
                $('.other_opt').text('关注公众号方式');
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
        })

    })
    </script>
    <script type="text/javascript">
        var _host = "{{ config('app.source_url') }}";;//静态资源
        var host ="{{ config('app.url') }}";;//网址域名
        var imgUrl = "{{ imgUrl() }}";//动态图片地址
        var wid = "{{session('wid')}}";
        var shareTitle = '{{ $shareData["share_title"] }}';
        var shareDesc = '{{ $shareData["share_desc"] }}';
        var shareImg = '{{ $shareData["share_img"] }}';
        var pid = '{{ session("mid") }}';
    </script>
    <!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/js/showMeetingMyGroups.js" ></script>
<script src="{{ config('app.source_url') }}shop/js/meeting_until.js"></script>
<script type="text/javascript">
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
wxShare();
function wxShare(){
    if(wid == 661){
        var share_title ='39800微商城我已免费领到，你也赶紧领一个';
        var share_desc ='一起来拼团，拼团满50人，全部免单！';
    }else if(wid == 626){
        var share_title ='移动互联网实战总裁班课程我已免费领到，你也赶紧领！';
        var share_desc ='超值拼团限时回馈，5人成团，人人0元领取《移动互联网实战总裁班》1天！';
    }else{
        var share_title =' 19800小程序我已免费领到，你也赶紧领一个';
        var share_desc ='一起来拼团，拼团满50人全部免单！';
    }
    if(vm.share){
        var share_img =vm.share.share_img?host+vm.share.share_img:"https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=4186641830,3509273267&fm=173&s=689200D71221B14942BF9AA70300C00B&w=600&h=400&img.JPEG";
        var share_url=vm.share.share_url; 
    }else{
        var share_url = location.href.split('#').toString();
        var share_title = shareTitle;
        var share_desc = shareDesc;
        var share_img = shareImg;
        if(window.location.search){
            share_url += '&_pid_='+ pid;
        }else{
            share_url += '?_pid_='+ pid;
        }
    } 
    wx.ready(function () {
        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: share_title, // 分享标题
            desc: share_desc, // 分享描述
            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
            imgUrl: share_img, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                $('#pop_up_click .pop_content .title').text('恭喜您分享成功！');
		        $('#pop_up_click').show();
                $('.share_mask').hide();
                vm.shareShow = false;
            
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

        //分享给朋友
        wx.onMenuShareAppMessage({
            title: share_title, // 分享标题
            desc: share_desc, // 分享描述
            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
            imgUrl:share_img, // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                $('#pop_up_click .pop_content .title').text('恭喜您分享成功！');
		        $('#pop_up_click').show();
                $('.share_mask').hide();
                vm.shareShow = false;                    
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

        //分享到QQ
        wx.onMenuShareQQ({
            title: share_title, // 分享标题
            desc: share_desc, // 分享描述
            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
            imgUrl: share_img, // 分享图标
            success: function () {
               // 用户确认分享后执行的回调函数
                $('#pop_up_click .pop_content .title').text('恭喜您分享成功！');
		        $('#pop_up_click').show();
                $('.share_mask').hide();
                vm.shareShow = false;                    
            },
            cancel: function () {
               // 用户取消分享后执行的回调函数
            }
        });

        //分享到腾讯微博
        wx.onMenuShareWeibo({
            title: share_title, // 分享标题
            desc: share_desc, // 分享描述
            link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
            imgUrl: share_img, // 分享图标
            success: function () {
               // 用户确认分享后执行的回调函数
               $('#pop_up_click .pop_content .title').text('恭喜您分享成功！');
		        $('#pop_up_click').show();
                $('.share_mask').hide();
                vm.shareShow = false;                    
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.error(function(res){
            
        });
    });
}
</script>
</body>
