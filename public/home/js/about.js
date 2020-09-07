$(function(){

	//底部轮播图
	var imgArr = [];
	var swiper = new Swiper('.bottom_swiper .swiper-container', {
		autoplay: 3000,//可选选项，自动滑动
		loop : true,
        slidesPerView: 3,      //同时显示的slides数量
        spaceBetween: 29,      //slide之间的距离（单位px）
//      loop: true,
        prevButton:'.bottom_swiper .swiper-button-prev',     //上一页
		nextButton:'.bottom_swiper .swiper-button-next',     //下一页
    });
   	$('.bottom_swiper .swiper-container').mouseenter(function(){
		swiper.stopAutoplay();              //自动播放停止
	}).mouseleave(function(){
		swiper.startAutoplay();             //自动播放开始
	});
   	
   	
   	//内容导航栏的吸顶设置；
//  var ie6 = document.all;
//  var fixDiv = $('.content_nav'), height;  //内容导航栏的高度
//  var Pheight = $(".nav").outerHeight();   //导航栏的整个高度
//  fixDiv.attr('top', fixDiv.offset().top); //存储原来的距离顶部的距离
//  $(window).scroll(function () {
//      height = Math.max(document.body.scrollTop || document.documentElement.scrollTop);
//      var xheight = parseInt(fixDiv.attr('top'))-parseInt(Pheight)
//      if (height > xheight) {
//          if (ie6) {                      //IE6不支持fixed属性，所以只能靠设置position为absolute和top实现此效果
//              fixDiv.css({ position: 'absolute', top: height });
//          }else if (fixDiv.css('position') != 'fixed'){
//          	fixDiv.css({ 'position': 'fixed', "top": Pheight,  "z-index":"10"});
//          } 
//      } else if (fixDiv.css('position') != 'static'){
//      	fixDiv.css({ 'position': 'static' });
//      } 
//  });
	
	
	
})

