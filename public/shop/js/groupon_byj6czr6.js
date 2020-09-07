$(function(){
	//邀请好友参团
	$(".js-open-share").click(function(){ 
		$("#js-share-guide").removeClass("hide");
	});

	//更多活动商品
	$(".js-sel-goods").click(function(){
		location.href="/shop/grouppurchase/index";
	});

	//我知道啦
	$("#js-share-guide .tag-big").click(function(){
		$(this).parents("#js-share-guide").addClass("hide");
	});

	//邀请码
	$(".js-groupon-share").click(function(){
		$("#yvuLlGnRDS").removeClass("hide");
		$("#OlYqBLIeVs").removeClass("hide");
	});

	//点击遮罩
	$("#OlYqBLIeVs").click(function(){
		$("#yvuLlGnRDS").addClass("hide");
		$(this).addClass("hide");
	});

    setShareImgHeight();
    //新增功能 图片太小2017/8/9 
    function setShareImgHeight(){
        var h = $(window).height();
        $(".groupon-share-popup").css({"height":(h*.9)+"px","width":"90%","margin":"auto"});
    }

    getrtime(end_time); // 倒计时 
    
    //倒计时函数
    function getrtime(time){
        var EndTime= new Date(time);
        var t = EndTime.getTime() - ntime;
        if(t>=0){
            var h=Math.floor(t/1000/60/60);
            var m=Math.floor(t/1000/60%60);
            var s=Math.floor(t/1000%60);
            $(".js-time-count .time-wrap").eq(0).html(h);
            $(".js-time-count .time-wrap").eq(1).html(m);
            $(".js-time-count .time-wrap").eq(2).html(s);
            ntime = ntime +1000;
            setTimeout(function(){
                getrtime(time);
            },1000);
        }
    } 

	//我要参团
	$(".js-join-group").click(function(){ 
		var product = rule.product;
        var showPrice = rule.max?rule.min+'~'+rule.max:rule.min;
        tool.spec.open({
            "type":2,
            "callback":buyCallBack,
            "url": "/shop/grouppurchase/getSkus",  //获取规格接口 
            "data":{
                "_token": $("meta[name='csrf-token']").attr("content"),
                rule_id:rule.id
            }, 
            "initSpec": {    // 默认商品数据
                "title": product.title,
                "img": product.img,
                "stock": product.stock,
                "price": showPrice,
            },
            "unActive":1,   //非拼团活动
            "isEdit":true,  //点x  不保存数据
            "buyCar": false,  //按钮  为单按钮  加入购物车  可选
            "noteList":product.noteList,
            "pid":product.id
        }); 
	});
	function buyCallBack(data){   
	    if(data.status==1){ 
	    tool.spec.close();  
        var data = {
            "id": pid,
            "num": data.data.num,
            "propid": data.data.spec_id,
            "content": "",//留言  to do something
            "groups_id": groups_id,
            "tag": 1
        }
        $.ajax({
            url:'/shop/cart/add/'+wid,// 跳转到 action
            data:data,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    window.location.href='/shop/order/waitPayOrder?cart_id=['+response.data.id+']'; 
                }else{
                    tool.tip(response.info);
                }
            },
            error:function() { 
                tool.tip("异常！");
            }
        });
    }

}
});
