$('.J_download').hover(function(){
    $('.er-code-box').toggleClass('active');
});
var mySwiper = new Swiper('.swiper-container', {
    observer: true,//修改swiper自己或子元素时，自动初始化swiper
    observeParents: true,//修改swiper的父元素时，自动初始化swiper
    autoplay: 2000, //可选选项，自动滑动
    speed:1000,      //滑动速度
    loop : true,    //环路
});