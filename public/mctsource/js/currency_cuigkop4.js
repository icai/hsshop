$(function(){
	//选中与不选中《担保交易服务协议》，缴纳开通按钮的样式及可用改变；
	$("#agreement").click(function(){
		if ($("#agreement").prop("checked") == true) {
			$(".content_bottom button").removeClass("btn-default");
			$(".content_bottom button").addClass("btn-success");
			$(".content_bottom button").removeAttr("disabled")
		}else{
			$(".content_bottom button").removeClass("btn-success");
			$(".content_bottom button").addClass("btn-default");
			$(".content_bottom button").attr("disabled", "disabled")
		}
	});
	
	
	//点击缴纳开通按钮触发的事件
	$(".content_bottom button").on("click", function(){
//		alert(222)
	})
})