$(function(){
    var id =window.location.href.split("/").pop()
    if(id==1){
        $(".banner").addClass("huiyuanka_banner");
        $(".breadcrumb_nav .active").text("会员卡");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/huiyuankad.jpg");
    }else if(id==2){
        $(".banner").addClass("jifen_banner");
        $(".breadcrumb_nav .active").text("积分");
         $(".intro_item").text("积分管理是帮助您增加用户用于激励和回馈用户在平台的消费行为和活动行为，提升用户对平台的黏度和重复下单率。");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/jifend.jpg")
    }else{
        $(".banner").addClass("chongzhi_banner");
        $(".breadcrumb_nav .active").text("充值");
        $(".intro_item").text("会员储值，是可帮助商家提升客户忠诚度、增加会员粘性，商家可根据需要创建储值规则，会员储值后可在消费时使用余额进行支付。");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/chongzhid.jpg")
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