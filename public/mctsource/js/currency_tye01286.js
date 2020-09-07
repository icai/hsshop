$(function(){
	$(".tab_nav li").click(function(){
		$(".tab_nav li").each(function(index, ele){
			$(this).removeClass("hover");
		})
		$(this).addClass("hover");
	})
	
	$(".imgShow").hide();
	$(".showed").show();
	for (var i=0; i<$(".tab_nav li").length; i++) {
		$(".tab_nav li:eq("+i+")").click(function(){
			var index = $('.tab_nav li').index($(this));
			for (var j=0; j<$(".imgShow").length; j++) {
				$(".imgShow:eq("+j+")").hide();
			}
			$(".imgShow:eq("+index+")").show();
		})
	}
	//没有数据时，显示没有相关数据；
	for (var i=0; i<$(".agreementMsg").length; i++) {
		if ($(".agreementMsg").eq(i).html()=="") {
			$(".agreementMsg").eq(i).html("没有协议")
									.css({textAlign: "center",padding: "39px 0", border:"1px solid #e5e5e5"});
		}
	}
	//有数据 点击查看详情
	$(document).on("click", ".agreementMsg .detail", function(){
		alert("查看详情 无UI图（可使用模态框）")
	})
});