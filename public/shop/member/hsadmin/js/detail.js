$(function () {
	//包邮 打折 优惠券部分的点击下拉显示
    $(".membership-region").on("click", function () {
        if ($(".membership ~ .block-sub-desc").css("display") == "block") {
            $(".membership ~ .block-sub-desc").hide();
            $(".membership-region ul").removeClass("down-arrow-right");
        } else {
            $(".membership ~ .block-sub-desc").show();
            $(".membership-region ul").addClass("down-arrow-right");
        }
    })
	//使用须知点击下拉
    $("a.js-show-sub-info").on("click", function () {
        if ($("p.arrow-right ~ .block-sub-desc").css("display") == "block") {
            $("p.arrow-right ~ .block-sub-desc").hide(); 
            $("p.arrow-right").removeClass("down-arrow-right");
        } else {
            $("p.arrow-right ~ .block-sub-desc").show(); 
            $("p.arrow-right").addClass("down-arrow-right");
        }
    })
    
    //点击领取会员卡按钮
    $(".btn-1-1 .js-obtain-card-btn").click(function(){
        var cardId = $(this).attr('data-id');
    	window.location.href="/shop/member/getCardAction/42/"+cardId
    })
    
    //点击二维码标识和出示会员凭证
    $(".js-show-code").click(function(){
    	tool.qrCode()
    });
    
    //点击客服电话
    $(".custPhone").click(function(){
    	tool.customPhone()
    })
})