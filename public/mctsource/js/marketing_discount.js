$(function(){
	//点击导航栏切换
	$(".content_top ul li").each(function(index, ele){
		$(this).click(function(){
			$(".content_top ul li").removeClass("active");
			$(this).addClass("active");
			$(".content_bottom").removeClass("B_active");
			$("#bottom_"+(index+1)+"").addClass("B_active");
		})
	})
	
	//聚焦搜索框动画；
	$("#search").focus(function(){
		$(this).animate({"width": "200px"});
		$("#FDimg").animate({"right": "180px"})
	});
	$("#search").blur(function(){
		$(this).animate({"width": "150px"});
		$("#FDimg").animate({"right": "130px"})
	})
})