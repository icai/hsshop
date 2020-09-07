$(function(){


    //内容导航切换样式
	$(".content_nav ul li:not(.have)").hover(function(){
		$(this).addClass('have')
		var myHover = $(this).find("img").attr("src").replace(/.png/,"_1.png")
		$(this).find("img").attr("src",myHover)
	},function(){
		$(this).removeClass("have")
		var myHover = $(this).find("img").attr("src").replace(/_1.png/,".png")
		$(this).find("img").attr("src",myHover)
	})
		
})