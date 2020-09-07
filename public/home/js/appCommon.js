$(function(){
   
    //侧边栏导航
	
	$(".sideBar_nav ul li:not(.active)").hover(function(){
		$(this).addClass('active')
		$(this).find(".icon").css("background-position-x","-17px")
	},function(){
		$(this).removeClass("active")
		$(this).find(".icon").css("background-position-x","0")
	})


	 //底部轮播图
	 if($(".sideBar_nav ul li:first").hasClass("active")){
		var imgArr = [];
		var swiper = new Swiper('.bottom_swiper .swiper-container', {
			autoplay: 3000,//可选选项，自动滑动
			loop : true,
			slidesPerView: 3,      //同时显示的slides数量
			spaceBetween: 0,      //slide之间的距离（单位px）
			
	//      loop: true,
			prevButton:'.bottom_swiper .swiper-button-prev',     //上一页
			nextButton:'.bottom_swiper .swiper-button-next',     //下一页
		});
		   $('.bottom_swiper .swiper-container').mouseenter(function(){
			swiper.stopAutoplay();              //自动播放停止
		}).mouseleave(function(){
			swiper.startAutoplay();             //自动播放开始
		});
	 }
	 
})