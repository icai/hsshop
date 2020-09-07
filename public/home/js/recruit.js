$(function(){

	//$(".recruit_item:even").css("margin-left","0")
	$(".recruit_type:first").css("display","block")
    $(".recruit_nav li").hover(function(){
		$(".recruit_nav li").removeClass("active")
		$(this).addClass("active")
		$(".recruit_type").css("display","none")
		var idx = $(this).index()
		$(".recruit_type").eq(idx).css("display","block")
	},function(){

	})
	
	
	
//	招聘	
	// $(".zhao-li").click(function(){
	// 	var inde = $(this).index();
	// 	$(".zhaopin-nav").css("display","none");
	// 	$(".zhaopin-nav").eq(inde).css("display","block")
	// })
	
})