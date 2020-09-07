$(function(){
//	第一个轮播
	var mySwiper = new Swiper ('.zc-swiper .swiper-container', {
	    direction: 'horizontal',
//	    autoplay: 1000, //可选选项，自动滑动
		speed:1000,      //滑动速度
		loop : true,    //环路
//      effect : 'coverflow',	//切换效果
		slidesPerView: 3,      //同时显示的slides数量
//		centeredSlides: true,
//		coverflow: {
//          rotate: 30, 		//slide做3d旋转时Y轴的旋转角度
//          stretch: 10,		//每个slide之间的拉伸值，越大slide靠得越紧
//          depth: 60,			//slide的位置深度。值越大z轴距离越远，看起来越小。
//          modifier: 2,		//depth和rotate和stretch的倍率
//          slideShadows : true	//开启slide阴影
//     }
	})  	
	//	第二个轮播
	var mySwiper = new Swiper ('.fif-swiper .swiper-container', {
	    direction: 'horizontal',
	    autoplay: 1000, //可选选项，自动滑动
		speed:1000,      //滑动速度
		loop : true,    //环路
	    
	    // 如果需要分页器
	    pagination: '.fif-swiper .swiper-pagination',
    })   
})
