$(function(){
    var id =window.location.href.split("/").pop()
    if(id==1){
        $(".banner").addClass('xlj_banner')
        $(".breadcrumb_nav .active").text("享立减");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/xianglijiand.jpg");
    }else if(id==2){
        $(".banner").addClass('jzan_banner')
        $(".breadcrumb_nav .active").text("集赞");
         $(".intro_item").text("集赞是一种一键快捷分享，让好友帮你点赞减价的营销推广活动");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/jzand.jpg")
    }else if(id==3){
        $(".banner").addClass('pintuan_banner')
        $(".breadcrumb_nav .active").text("多人拼团");
        $(".intro_item").text("多人拼团是一种基于社交邀请好友一起拼团购买的营销推广活动");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/ptuand.jpg")
    }else if(id==4){
        $(".banner").addClass('dazhuanpan_banner')
        $(".breadcrumb_nav .active").text("幸运大转盘");
        $(".intro_item").text("幸运大转盘是一种常见的幸运抽奖营销推广工具");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/dzhuanpand.jpg")
    }else if(id==5){
        $(".banner").addClass('weishequ_banner')
        $(".breadcrumb_nav .active").text("微社区");
        $(".intro_item").text("微社区是基于商家微信公众账号的社交平台，支持图片、视频、文字、表情等方式。借助微社区，商家可以便捷的打造和粉丝的互动平台。在互动中了解粉丝心声，提高粉丝参与度，共同创造内容并发生传播，让交流无限");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/shequd.jpg")
    }else if(id==6){
        $(".banner img").attr("src",imgUrl+"home/image/zajindan.png");
        $(".banner").addClass('zajindan_banner')
        $(".breadcrumb_nav .active").text("砸金蛋");
        $(".intro_item").text("砸金蛋活动规则简单，参与活动粉丝只需在活动界面砸开金蛋即有机会获得奖品，商家可以设置活动界面引导banner及跳转链接，奖品中奖概率也可以分别设置，奖品可设置优惠券和积分。");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/zjd.jpg")
    }else{
        $(".banner").addClass('qiandao_banner')
        $(".breadcrumb_nav .active").text("签到");
        $(".intro_item").text("每日签到能获得更多的积分");
        $(".bottom_slide img").attr("src",imgUrl+"home/image/qiandaod.jpg")
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