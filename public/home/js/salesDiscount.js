$(function(){
    var id =window.location.href.split("/").pop()
    if(id==1){
        $(".banner").addClass("youhuijuan_banner");
        $(".breadcrumb_nav .active").text("优惠券");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/youhuijuand.jpg");
    }else{
        $(".banner").addClass("seckill_banner");
        $(".breadcrumb_nav .active").text("秒杀");
         $(".intro_item").text("秒杀是商家较常使用的一种快速汇集流量、促销购买的营销推广活动");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/seckilld.jpg")
    }
    


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