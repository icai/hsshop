$(function(){
    var id =window.location.href.split("/").pop()
    if(id==1){
        $(".banner").addClass("xiaoxitixing_banner");
        $(".breadcrumb_nav .active").text("消息提醒");
        // $("intro_item").text("");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/xxtxd.jpg");
    }else if(id==2){
        $(".banner").addClass("xiaoxi_banner");
        $(".breadcrumb_nav .active").text("消息模板");
         $(".intro_item").text("消息模板功能可以通过微信公众平台设置好固定的消息模板，在后台一键发送到公众号下的所有粉丝，高效及时的消息提醒。");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/xxmbd.jpg")
    }else{
        $(".banner").addClass("toupiao_banner");
        $(".breadcrumb_nav .active").text("投票");
        $(".intro_item").text("向客户发起投票活动，收集用户需求，更精准商品定位，打造商品爆款");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/toupiaod.jpg")
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