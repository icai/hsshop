$(function(){
	$(".saveDiv button").click(function(){
		var inpVal = $(".top_right input").val();
		if (inpVal<20 || inpVal>1440) {
			$(".errMsg").removeClass("hide").parents(".form-group").addClass("has-error");
		}else{
			$(".errMsg").addClass("hide").parents(".form-group").removeClass("has-error");
			tipshow('保存成功','info');
		}
	})
})