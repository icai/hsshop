var wid = $('input[name="wid"]').val();
var id = $('input[name="id"]').val();
$.ajax({
    type:"POST",
    url:"/shop/activity/coupon/receive/"+wid+"/"+id,
    async:true,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success:function(res){
        $(".pageLoading").hide();
        if(res.status == 1){
            var couponId = res.data.couponReceiveID;//领取优惠券id
            $(".bg-pic-content").css("background-image","url("+res.data.data.avatar+")");
            $(".shop_name").text(res.data.data.shop_name);
            $(".receive_amount").text(res.data.data.receive_amount);
            $(".mobile").text(res.data.data.mobile);
            $(".start_at").text(res.data.start_at);
            $(".end_at").text(res.data.end_at);
            $(".btn-main-action").attr("href","/shop/index/"+wid);
            $(".sum").text(parseFloat(res.data.data.sum).toFixed(2));
            $("#addCard").data("id",res.data.result.card_id);
            $("#addCard").data("err",res.data.result.err);
            //用户头像
            $('.block-link .thumb img').attr("src",res.data.data.avatar);

            if(res.data.data.is_limited == 1){
                $(".is_limited").text(res.data.data.limit_amount);
                $(".hid_z").eq(0).removeClass("hide");
            }else{
                $(".hid_z").eq(1).removeClass("hide");
            }
//			微信卡包
            if(res.data.show == false){
                $(".opt").hide();
            }else{
                $(".opt").show();
            }
//			是否领取成功
            if (res.data.receiveFlag == false) {
                $(".msg").html(res.data.data.tip1);
                $(".err-tip2").html(res.data.data.tip2);
                $(".err-tip3").html(res.data.data.tip3);
                $(".coupon-value").hide();
                $(".hid_z").hide();
                $(".coupon-msg").hide();
                $(".coupon-validity").hide();
            }
//			立即使用显示
            if (res.data.receiveFlag == true) {
                $(".flag-for").removeClass("hide");
            }else{
                $(".flag-in").removeClass("hide");
            }
//			立即使用链接
            switch(parseFloat(res.data.data.link_type)){
                case 1://商品详情
                    var linkId = res.data.data.link_id;
                    $(".flag-for a").attr("href","/shop/product/detail/"+wid+"/"+linkId);
                    break;
                case 2://商品列表
                    $(".flag-for a").attr("href","/shop/member/couponProducts/"+wid+"/"+couponId);
                    break;
                case 3://微页面
                    $(".flag-for a").attr("href","/shop/microPage/index/"+wid+"/"+res.data.data.link_id.split(',')[0]);
                    break;
                default://默认首页
                    $(".flag-for a").attr("href","/shop/index/"+wid);
            }
        }
    },
    error:function(){
        alert("数据访问错误")
    }
});

// 下拉加载更多
var page = 2;
var loading = false;  //状态标记
var wid = $('input[name="wid"]').val();
var id = $('input[name="id"]').val();
var html = '';
$.get('/shop/activity/couponReceiveList/'+ wid + '/' + id,{page:1},function(data){
    if(data.data.data.length>0){
        for(var i = 0;i<data.data.data.length;i++){
            html += '<div class="block-item name-card name-card-3col name-card-promocard name-card-other">';
            html += '<figure class="thumb"><img src="'+data.data.data[i]['avatar']+'"></figure>';
            html += '<div class="detail"><h3><strong>'+data.data.data[i]['nickname']+'</strong>';
            html += '<i>'+data.data.data[i]['created_at_new']+'</i></h3><p class="ellipsis">'+data.data.data[i]['remark']+'</p></div></div>';
        }
    }
    $('.promocard-others').append(html);
},'json')
window.onscroll = function(){
    if(scrollTop() + windowHeight() >= (documentHeight() - 100)){
        if(loading)return;
        loading = true;
        $.get('/shop/activity/couponReceiveList/'+ wid + '/' + id,{page:page},function(data){
            if(data.data.data.length>0){
                for(var i = 0;i<data.data.data.length;i++){
                    html += '<div class="block-item name-card name-card-3col name-card-promocard name-card-other">';
                    html += '<figure class="thumb"><img src="'+data.data.data[i]['avatar']+'"></figure>';
                    html += '<div class="detail"><h3><strong>'+data.data.data[i]['nickname']+'</strong>';
                    html += '<i>'+data.data.data[i]['created_at_new']+'</i></h3><p class="ellipsis">'+data.data.data[i]['remark']+'</p></div></div>';
                }
                $('.promocard-others').append(html);
                page++;
                loading = false;
            }
        },'json')
    }
}
function trim(str,is_global){
    var result;
    result = str.replace(/(^\s+)|(\s+$)/g,"");
    return result;
}
//获取页面顶部被卷起来的高度
function scrollTop(){
    return Math.max(
        document.body.scrollTop,
        document.documentElement.scrollTop);
}
//获取页面文档的总高度
function documentHeight(){
    return Math.max(document.body.scrollHeight,document.documentElement.scrollHeight);
}
function windowHeight(){
    return (document.compatMode == "CSS1Compat")?
        document.documentElement.clientHeight:
        document.body.clientHeight;
}



$(function(){
    var url = location.href.split('#').toString();
    $.get("/shop/weixin/getWeixinSecretKey",{"url": url},function(data){
        if(data.errCode == 0){
            wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: data.data.appId, // 必填，公众号的唯一标识
                timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                signature: data.data.signature,// 必填，签名，见附录1
                jsApiList: [
                    'addCard'
                ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });
        }
    });

    wx.ready(function(){
        //添加卡券
        $('#addCard').click(function(){
            var card_id = $(this).data('id');
            var url = '/shop/activety/couponAuth/'+card_id;
            var _this = $(this);
            _this.attr("disabled",true);
            $.get(url,function(data){
                var cardExt = JSON.stringify(data.data.cardExt);
                wx.addCard({
                    cardList: [
                        {
                            cardId:  card_id,
                            cardExt: cardExt
                        }
                    ],
                    success: function (res) {
                        _this.remove();
                    }
                });

            });


        });
    });

});