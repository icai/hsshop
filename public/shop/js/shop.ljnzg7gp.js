var isPageHide = false;  //增加页面强制刷新.ios页面返回后.点击事件无效
window.addEventListener('pageshow', function () {  
    if (isPageHide) {  
        window.location.reload();  
    }  
});  
window.addEventListener('pagehide', function () {  
    isPageHide = true;  
});  

var expire_seconds = 3600;
//秒杀 超时未支付时间
if (seckill_expire_seconds) {
    expire_seconds = seckill_expire_seconds;
}
//若无买家留言显示无
if(!remark_judge){
	$('.js-msg-container').val('无')
}
//若无运费显示免运费
if(parseFloat(freight_judge) == 0){
	$('.freight_judge').html('免运费')
}
// 商品详情路径   普通、秒杀、享立减
var href;
if(seckill_id != 0){
    href = '/shop/seckill/detail/'+ wid +'/'+ seckill_id
}else if(share_event_id != 0){
    href="/shop/product/detail/"+ wid +"/"+ pid + "?activityId=" + share_event_id
}else{
    href='/shop/product/detail/'+ wid +'/'+pid
}
// 点击商品信息返回商品详情
$(".name-card-goods").click(function(){
    window.location.href=href;
})
//申请退款
//status:  0待付款；1待发货；2已发货（待收货）；3已完成；4已关闭'
$('body').on('click touchstatr','.applyRefundBtn',function(){
    var refund_status = $(this).data('status');
    var pid = $(this).data('pid');
    var prop_id = $(this).data('prop-id');
    if (refund_status == 0) {
        if(status==1){							//未发货
            location.href = "/shop/order/refundApplyView/"+wid+"/"+oid+"/"+pid+"/0/"+prop_id+"?type=1";
        }else if(status==2 || status == 3){		//已发货
            location.href="/shop/order/refundApplyType/"+wid+"/"+oid+"/"+pid+"/0/"+prop_id;
        }
    } else if (refund_status == 5 || refund_status == 9) {
        if(status==1){							//未发货
            location.href = "/shop/order/refundApplyView/"+wid+"/"+oid+"/"+pid+"/1/"+prop_id+"?type=1";
        }else if(status==2 || status == 3){		//已发货
            location.href="/shop/order/refundApplyType/"+wid+"/"+oid+"/"+pid+"/1/"+prop_id;
        }
    }

})

var time = ($('.js-time').data('seconds') + (+expire_seconds))|| '';
// // 获取当前事件戳
var newTime = new Date();
var newString = Date.parse(newTime)/1000;
var endTime = time - newString;
var newDate = new Date();
if(status != 0){    //0 为待付款状态
    $(".js-order-total-pay").hide();
}else{
    setInterval(function(){
	endTime --;
    // alert(endTime)
	if(endTime<=0 && $('.timeOut').length == 0){
        var html = '<div class="message-status order-state-cancel timeOut">\
                        <div class="status-text">\
                            <h3 style="font-size: 18px;">交易关闭 (交易超时)</h3>\
                        </div>\
                    </div>';
        $('.timeIn').after(html);
	    $('.timeIn').hide();
	    $('.timeIns,.timer').hide();
        $(".js-order-total-pay").hide();
        $('.order_icon').attr('src',imgUrl+'shop/images/order-close.png');
            // window.location.reload();
	    }

        
	    $('.js-time').text(formatSeconds(endTime))
    },1000);
}

///shop/pay/index?id={{ $orderDetail['id'] }}&payment=1 
//去支付
$('.commit-bill-btn').click(function () {
    // update by 黄新琴  2018-7-30 9:29 判断当前环境，如果是支付宝小程序打开，调用支付宝支付或者余额支付
    if (reqFrom == 'aliapp') {
        var str ='<nav class="sel-pay-wrap">';
        if (parseFloat(balance) >= parseFloat(pay_price)) {
          str +=
            '<a href="javascript:;" id="alipayYuerzf">储值余额支付（剩余￥' +
            balance +
            '）</a>'
          str += '<a href="javascript:;" id="alipay">支付宝支付</a>'
        } else {
          str += '<a href="javascript:;" id="alipay">支付宝支付</a>'
          str +=
            '<a href="javascript:;" id="alipayYuerzf" class="disabled">储值余额支付（余额不足）</a>'
        }
        str +='</nav>';
    } 
    // add by 韩瑜 2018-10-17 百度支付
      else if(reqFrom == 'baiduapp'){
        if (parseFloat(balance) >= parseFloat(pay_price)) {
          str +=
            '<a href="javascript:;" id="yuerzf">储值余额支付（剩余￥' +
            balance +
            '）</a>'
          str += '<a href="javascript:;" id="baidupay">百度收银台支付</a>'
        } else {
          str += '<a href="javascript:;" id="baidupay">百度收银台支付</a>'
          str +=
            '<a href="javascript:;" id="yuerzf" class="disabled">储值余额支付（余额不足）</a>'
        }
      }
    else {
        var str ='<nav class="sel-pay-wrap order_pay"><div class=\'order_pay_title pay_bottom\'>选择支付方式</div>';
        if (parseFloat(balance) >= parseFloat(pay_price)) {
            str += '<div class="order_balance_pay pay_bottom" id="yuerzf">'+
                '<div data-id="1">'+
                '<div class="balance_img"></div>'+
                '<a href="javascript:;">储值余额支付（剩余￥' + balance + '）</a>'+
                '</div>'+
                '<div class="order_pay_way" data-id="2">'+
                '<div class="ap-weixuan hide"></div>'+
                '<div class="dui"></div>'+
                '</div>'+
                '</div>'+
                '<div class="order_balance_pay pay_bottom" id="weixinzf">'+
                '<div data-id="2">'+
                '<div class="balance_img weixin_img"></div>'+
                '<a href="javascript:;">微信支付</a>'+
                '</div>'+
                '<div class="order_pay_way" data-id="1">'+
                '<div class="ap-weixuan"></div>'+
                '<div class="dui hide"></div>'+
                '</div>'+
                '</div>'
        } else {
            str += '<div class="order_balance_pay pay_bottom" id="weixinzf">'+
                '<div data-id="2">'+
                '<div class="balance_img weixin_img"></div>'+
                '<a href="javascript:;">微信支付</a>'+
                '</div>'+
                '<div class="order_pay_way" data-id="2">'+
                '<div class="ap-weixuan hide"></div>'+
                '<div class="dui"></div>'+
                '</div>'+
                '</div>'+
                '<div class="order_balance_pay pay_bottom" id="yuerzf">'+
                '<div data-id="1">'+
                '<div class="balance_img"></div>'+
                '<a href="javascript:;">储值余额支付（剩余￥0元）</a>'+
                '</div>'+
                '<div class="order_pay_way" data-id="1">'+
                '<div class="ap-weixuan"></div>'+
                '<div class="dui hide"></div>'+
                '</div>'+
                '</div>'
        }
        str +='<div class="confirm_btn"><p>确认</p></div></nav>';
      }


    $("body").append("<div class='sel-mask'></div>");
    $("body").append(str);
    //余额支付
    $(".confirm_btn").on("click",function(e){
        e.stopPropagation();
        if(payFlag){
            payFlag = false
            var box = $(".order_pay_way")
            for(var i = 0; i < box.length; i++){
                var id = $(box[i]).attr('data-id')
                if(id == 2){
                    var pay_id = $(box[i]).siblings('div').attr('data-id')
                    if(pay_id == 1){
                        if(parseFloat(balance) < parseFloat(pay_price)){
                            tool.tip('余额不足');
                            return;
                        }
                        publicBay(3)
                    }else if(pay_id == 2){
                        publicBay(1)
                    }
                }
            }
        }
    });
    //支付按钮点击监听
    $("#weixinzf").on("click",function(e){
        e.stopPropagation();
        var id = $(this).children('.order_pay_way').attr('data-id')
        if(id == 2){
            return false
        }
        $(this).children('.order_pay_way').attr('data-id','2').children('.dui').removeClass("hide").siblings('.ap-weixuan').addClass("hide")
        $(this).siblings('div').children('.order_pay_way').attr('data-id','1').children('.dui').addClass("hide").siblings('.ap-weixuan').removeClass("hide")
    });
    $("#yuerzf").on("click",function(e){
        e.stopPropagation();
        var id = $(this).children('.order_pay_way').attr('data-id')
        if(id == 2){
            return false
        }
        $(this).children('.order_pay_way').attr('data-id','2').children('.dui').removeClass("hide").siblings('.ap-weixuan').addClass("hide")
        $(this).siblings('div').children('.order_pay_way').attr('data-id','1').children('.dui').addClass("hide").siblings('.ap-weixuan').removeClass("hide")
    });
});
//移除支付弹窗
$("body").on("click",".sel-mask",function(event){
    $(this).remove();
    $(".sel-pay-wrap").remove();
});

 //add by 黄新琴  2018-7-30  09:30   支付宝支付按钮点击监听
 $('body').on('click', '#alipay', function(e) {
    e.stopPropagation()
    publicBay(4)
})

  //add by 韩瑜  2018-10-17    百度收银台支付跳转
 $('body').on('click', '#baidupay', function(e) {
    e.stopPropagation()
    swan.webView.navigateTo({url: '/pages/baidupay/baidupay?id='+order_id});
})


//余额支付 
$("body").on("click","#alipayYuerzf",function(e){
    e.stopPropagation();
    if(!$(this).hasClass("disabled")){
        tool.confirm("<p style='text-align:center;font-size:16px;'>确定支付？</p>",function(){
            publicBay(3);
        });
    }
});
var payFlag = true
/**
 * @param  支付方式 1.微信支付 3.余额支付
 * @return null
 */
function publicBay(payment){
     // add by 黄新琴 payment为4唤起支付宝支付
     if (payment == 4) {
        my.navigateTo({url:'/pages/shop/alipay/alipay?id='+order_id});
    } else {
        window.location.href = '/shop/pay/index?id=' + order_id + '&payment=' + payment;
    }
} 

//复制核销码
var copy_hexiao_code = document.getElementById('copy_hexiao_code');
if(copy_hexiao_code){
    var clipboard = new Clipboard(copy_hexiao_code);
    clipboard.on('success', function(e) {
        tool.tip("复制成功");
    });

    clipboard.on('error', function(e) {
        tool.tip("复制失败，请手动复制。"); 
    });
}
  
var copy_length = $('.copy').length;
var copy_attr_length = $('.copy_attr').length;
// alert(copy_length);
for(var i = 0;i<copy_length;i++){
    var clipboard =  new Clipboard('.copy_' + i);
    clipboard.on('success', function(e) {    
        tool.tip("复制成功");    
        e.clearSelection();    
    });   
    clipboard.on('error', function(e) {
        tool.tip("复制失败，请手动复制。"); 
    });
}
for(var i = 0;i<copy_attr_length;i++){
    var clipboard =  new Clipboard('.copy_attr_' + i);
    clipboard.on('success', function(e) {    
        tool.tip("复制成功");    
        e.clearSelection();    
    });   
    clipboard.on('error', function(e) {
        tool.tip("复制失败，请手动复制。"); 
    });
}
// 卡密加载更多 add by 魏冬冬2018-8-9
$('.arrow_container span').click(function(){
    if($(this).data('status') == 0){
        $(this).attr('data-status',1);
        $('.card-list-more').removeClass('hide');
        $(this).html('上拉收起');
        $(this).next('img').attr('src',APP_SOURCE_URL + 'shop/images/arrow_top.png');
    }else{
        $(this).attr('data-status',0);
        $('.card-list-more').addClass('hide');
        $(this).html('查看更多');
        $(this).next('img').attr('src',APP_SOURCE_URL + 'shop/images/arrow_bottom.png');
    }
})
//end
// $("body").on("click",".copy_hexiao_code",function(){ 
//     var hexiao_code = $(this).attr("data-code");

// });

function formatSeconds(value) {
    var theTime = parseInt(value);// 秒
    var theTime1 = 0;// 分
    var theTime2 = 0;// 小时
    if(theTime > 60) {
        theTime1 = parseInt(theTime/60);
        theTime = parseInt(theTime%60);
            if(theTime1 > 60) {
            theTime2 = parseInt(theTime1/60);
            theTime1 = parseInt(theTime1%60);
            }
    }
        var result = ""+parseInt(theTime)+"秒";
        if(theTime1 > 0) {
        result = ""+parseInt(theTime1)+"分"+result;
        }
        if(theTime2 > 0) {
        result = ""+parseInt(theTime2)+"小时"+result;
        }
    return result;
}

//确认收货
$('body').on('click','.received',function () {
    var obj = $(this);
    var wid = $("#wid").val();
    var oid = obj.data('kdtid');
    if(refund_status != 0 && refund_status != 4 && refund_status != 8){
        tool.notice(1,'确认收货','您正在申请退款中,确认收货将会关闭退款','确认收货',function(){
            $.ajax({
                url:'/shop/order/received/'+wid+'/'+oid,// 跳转到 action
                data:{},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tool.tip(response.info);
                        setTimeout(function(){
                            window.location.href="/shop/order/index/"+wid
                        },1000);
                        // var src = '/shop/order/commentList/'+wid+'/'+oid;
                        // obj.removeClass('received');
                        // obj.html('评价');
                        // obj.attr('href',src);
                        // obj.siblings().remove();
                        return false;
                    }else{
                        tool.tip(response.info);
                        return false;
                    }
                },
                error : function() {
                    // view("异常！");
                    tool.tip("异常！");
                }
            });
        });
    }else{
        tool.notice(1,'确认收货','确认收货后，订单交易完成，钱款将立即到达商家账户。','确认收货',function(){
            $.ajax({
                url:'/shop/order/received/'+wid+'/'+oid,// 跳转到 action
                data:{},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tool.tip(response.info);
                        setTimeout(function(){
                            window.location.href="/shop/order/index/"+wid
                        },1000);
                        // var src = '/shop/order/commentList/'+wid+'/'+oid;
                        // obj.removeClass('received');
                        // obj.html('评价');
                        // obj.attr('href',src);
                        // obj.siblings().remove();
                        return false;
                    }else{
                        tool.tip(response.info);
                        return false;
                    }
                },
                error : function() {
                    // view("异常！");
                    tool.tip("异常！");
                }
            });
        });
    }
    
});

 $('body').on('click','.receiveDelay',function () {
    //查看是否超过三天
    var obj = $(this);
    var wid = $("#wid").val();
    var oid = obj.data('kdtid');
    tool.notice(1,'延长收货时间','每笔订单只能延长一次收货时间，如需多次延长请联系商家','确定延长',success);
    // if($('.three_day').val() == 0){
    //    tool.notice(0,'延长收货时间','距离结束时间前三天才能申请哦。','我知道了')
    //     return false;
    // }else{
    //     tool.notice(1,'延长收货时间','每笔订单只能延长一次收货时间，如需多次延长请联系商家','确定延长',success)
    // }
    function success(){
        $.ajax({
            url:'/shop/order/receiveDelay/'+wid+'/'+oid,// 跳转到 action
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tool.tip(response.info);
                    return false;
                }else{
                    tool.tip(response.info);
                    return false;
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });
    }

});

$('.J_go-destination').click(function(){
    var olat = $(this).data('lat');
    var olng = $(this).data('lng');
    window.location = '/shop/order/location?olat='+olat+'&olng='+olng;
});

//复制订单编号
var clipboard = new Clipboard('.js-oid-copy');
clipboard.on('success', function(e) {
    tool.tip("复制成功");
});

clipboard.on('error', function(e) {
    tool.tip("复制失败，请手动复制。"); 
});


// 外卖订单确认收货倒计时
var nowDate = new Date().getTime();
var endDate = new Date(created_at.replace(/-/g, '/')).getTime() + parseInt(deliveryHour)*60*60*1000;
if(nowDate<endDate){
    var count = endDate - nowDate;
    countDown(count)
    var setTime = setInterval(() => {
        count = count -1000;
        countDown(count)
    }, 1000);
    if(count<0){
        clearInterval(setTime)
        $(".show_count").hide()
    }
}
function countDown(count){
    var hour = parseInt(count/1000/60/60)<10?'0'+parseInt(count/1000/60/60):parseInt(count/1000/60/60);
    var minute = parseInt(count/1000/60%60)<10?'0'+parseInt(count/1000/60%60):parseInt(count/1000/60%60);
    var second = parseInt(count/1000%60)<10? '0'+parseInt(count/1000%60):parseInt(count/1000%60);
    $(".count-hour").text(hour + '小时')
    $(".count-minute").text(minute + '分')
    $(".count-second").text(second + '秒')
}
// 外卖订单未付款倒计时
var endPay = new Date(created_at.replace(/-/g, '/')).getTime() + parseInt(unpayMinite)*60*1000;
if(nowDate<endPay){
    var payTime = endPay - nowDate;
    countPay(payTime)
    var setTime1 = setInterval(() => {
        payTime = payTime -1000;
        countPay(payTime)
    }, 1000);
    if(payTime<0){
        clearInterval(setTime1)
        $(".js-unpay-time").hide()
    }
}
function countPay(count){
    var hour = parseInt(count/1000/60/60)<10?'0'+parseInt(count/1000/60/60):parseInt(count/1000/60/60);
    var minute = parseInt(count/1000/60%60)<10?'0'+parseInt(count/1000/60%60):parseInt(count/1000/60%60);
    var second = parseInt(count/1000%60)<10? '0'+parseInt(count/1000%60):parseInt(count/1000%60);
    $(".js-unpay-time").text(hour + '小时'+minute + '分'+second + '秒')
}