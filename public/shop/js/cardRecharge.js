'use strict'
$(function(){
	//点击充值
	$("body").on("click",".balance-wrap",function(){
		var money = $(this).attr("data-money"); 
		$.ajax({
            type: "get",
            url: '/shop/member/addBalance/'+wid+'/'+money,
            data: {},
            dataType: 'json',
            success: function (msg) { 
                if (msg.errCode == 0) { 
                    var order_id = msg.data; 
                    window.location.href= '/shop/pay/index?id='+order_id+'&payment=1';
                }//购物车中商品存在异常
                else if (msg.errCode == -5) {
                    tool.tip(msg.errMsg);
                }else if(msg.errCode == -7){
                    tool.tip('下单失败'); 
                }else{
                    tool.tip(msg.errMsg);
                }
            },
            error: function (msg) {
                tool.tip('生成订单失败！');
            }
        });
	});
});