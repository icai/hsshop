$(function(){
    var id =window.location.href.split("/").pop()
    if(id==1){
        // $(".banner img").attr("src",imgUrl+"home/image/xcx-banner.png");
        $(".banner").addClass("xcx-banner_banner");
        $(".breadcrumb_nav .active").text("小程序");
        // $("intro_item").text("");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/xiaochengxud.jpg");
    }else{
        // $(".banner img").attr("src",imgUrl+"home/image/gongzonghao.png");
        $(".banner").addClass("gongzonghao_banner");
        $(".breadcrumb_nav .active").text("公众号");
         $(".intro_item").text("支持商家微信公众号与小程序和微商进行绑定，拓展线上流量渠道");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/gongzhonghaod.jpg")
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