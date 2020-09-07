$(function(){
	var isValid = $('#isValid').val();
	$(".pageLoading").hide();//加载消失

	
	//优惠券立即使用链接
	switch(parseFloat(link_type)){
		case 1://商品详情
			$(".receive_url").attr("href","/shop/product/detail/"+wid+"/"+link_id);
			break;
		case 2://商品列表
			$(".receive_url").attr("href","/shop/member/couponProducts/"+wid+"/"+id);
			break;
		case 3:// 微页面
			var ids = link_id.split(',');
			$(".receive_url").attr("href","/shop/microPage/index/"+wid+"/"+ids[0]);
			break;
		default://默认首页
			$(".receive_url").attr("href","/shop/index/"+wid);
	}
	//无效的优惠券   为0
	if(isValid == 0){
		$(".promote-card").css("background", "#CDCED0");
		$(".promote-card .js-share, .promote-for-shop, .addWXcard").css("display", "none");
		$(".promote-card").append("<img src='/shop/images/invalid1.png' style='position:absolute; width:100px; height: 70px; top:6px; right:5px;'>")
		$(".get-promote-card .receive_url").text(invalid_text).css({"background":"#CDCED0"}).attr("href", "##")
	}
	//点击分享出现蒙板
	$(".js-share").click(function(){
		$("#js-share-guide").hasClass("hide")?$("#js-share-guide").removeClass("hide"):"";
	})
	//点击分享的蒙板使之消失
	$("#js-share-guide").click(function(){
		$("#js-share-guide").addClass("hide")
	})
	
	//添加值微信卡包
	$(".addWXcard").click(function(){
		//如果添加过之后不能再添加
		if ($(".get-promote-card").has(".wxCode").length==0) {
			var _html = '<div class="wxCode" style="text-align: center; margin-top: 10px;">'+
							'<img src="/hsadmin/images/qr.png" width="180" height="180"/>'+
							'<p style="color: #333; font-size: 13px;">长按图片，识别二维码添加至卡包</p>'+
						'</div>';
			$(".get-promote-card .center").after(_html)
		}
	})
	
	//点击全部商品跳转值产品页
	$(".promote-goods .block-item").click(function(){
        var id = $(this).data('coupon-id');
        var wid = $(this).data('wid');
		var rangeType = $(this).data('range-type');
        if (rangeType == 1) {
            window.location.href = "/shop/member/couponProducts/" + wid + "/" + id;
        }
	})
	
	//使用说明-更多
	if($(".js-desc-detail").text() != null){
		var textLength = $(".js-desc-detail").text();
		if(textLength>=56){
			$(".promote-desc .more-desc").css("display", "block")
			.click(function(){
				$(".js-desc-detail").css({"overflow": "visible", "text-overflow": "inherit", "-webkit-line-clamp": "1000"})
				$(".promote-desc .more-desc").css("display", "none")
			})
		}
	}

	

})