$(function(){
	function approve(getEle, reg){
		var val = $(getEle).val();
		var goal = $(getEle).parent().parents(".form-group");
		if (reg) {
			if (!reg.test(val)) {
				goal.addClass("has-error");
				return false;
			}else{
				goal.removeClass("has-error");
			}
		}else{
			if (val == "") {
				goal.addClass("has-error");
				return false;
			}else{
				goal.removeClass("has-error");
			}
		}
	}
	
	//-------------------
	$(".GstoreSelect .selector").each(function(index, ele){
		$(this).click(function(){
			for (var i=0; i<$(".GstoreSelect .selector").length; i++) {
				$(".GstoreSelect .selector").eq(i).css({"border":"1px solid #ccc"})
			}
			$(this).css({"border": "2px solid #ff6602"});
		})
	})
	
	//-------------------
	var _GstoreMainNameReg = /^[\u4e00-\u9fa5]{2,4}$/;               	//姓名正则；
	//点击提交按钮的认证；
	$(".content_bottom a").click(function(){
		//公司名称；
		approve("#GstoreName");
		//法人姓名；
		approve("#GstoremainName", _GstoreMainNameReg);
		//短信验证码；
		approve("#checkPhon3");
	});
	
	//输入框失焦验证；
	$("#GstoreName").blur(function(){approve("#GstoreName");});
	$("#GstoremainName").blur(function(){approve("#GstoremainName", _GstoreMainNameReg)});
	
})
