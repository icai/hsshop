$(function(){
   
    


	 //轮播图
	 
		var imgArr = [];
		var swiper = new Swiper('.pic_swiper .swiper-container', {
			autoplay: 3000,//可选选项，自动滑动
			loop : true,
			slidesPerView: 1,      //同时显示的slides数量
			spaceBetween: 0,      //slide之间的距离（单位px）
			
	//      loop: true,
			prevButton:'.pic_swiper .swiper-button-prev',     //上一页
			nextButton:'.pic_swiper .swiper-button-next',     //下一页
		});
		   $('.pic_swiper .swiper-container').mouseenter(function(){
			swiper.stopAutoplay();              //自动播放停止
		}).mouseleave(function(){
			swiper.startAutoplay();             //自动播放开始
		});
	 
	 
})