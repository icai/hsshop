$(function(){
	$(".guding img").hover(function(){
		$(this).toggleClass("intro")
	});
	
	$(".weixin,.weixin-erwei").hover(function(){
		$(".weixin-erwei").css("display","block")
	},function(){
		$(".weixin-erwei").css("display","none")
	})

	$(".lianxi,.xianshi-dianhua").hover(function(){
		$(".xianshi-dianhua").css("display","block")
	},function(){
		$(".xianshi-dianhua").css("display","none")
	})

	$(".slider_tel,.xianshi-tel").hover(function(){
		$(".xianshi-tel").css("display","block")
	},function(){
		$(".xianshi-tel").css("display","none")
	})

	$(".zaixian").hover(function(){
		$(".qq").css("display","block")
	},function(){
		$(".qq").css("display","none")
	})
	
	$(".zhankai").click(function(){
		$(document).scrollTop(0);
	})
	
	$(window).scroll(function(){
		var top = $(document).scrollTop();
		var Wintop = $(window).height();
		if(top > Wintop){
			$(".zhankai").css("display","block")
		}else{
			$(".zhankai").css("display","none")
		}
	})
})	
