$(function(){
    var preHandler=function(e){e.preventDefault();}
    $(".icon-nav").on( 'click', function(e){
        e.stopPropagation();//阻止事件冒泡
        //打开右侧导航栏
        $(".nav").removeClass("none");
        document.addEventListener('touchmove', preHandler,false);
    });
    $(".nav-box").on( 'click',function(e){
        e.stopPropagation();
    });
    $("body, .icon-nav-close").on( 'click',function(){
        $(".nav").addClass("none");
        document.removeEventListener('touchmove', preHandler, false);
    });
    $('.nav-list').on('click', '.J_more-list', function(){
        if ($(this).children('.nav-item-icon').hasClass('nav-item-icon-active')) {
            $(this).children('.nav-item-icon').removeClass('nav-item-icon-active');
            $(this).siblings('ul').children().find('.nav-item-icon').removeClass('nav-item-icon-active');
            $(this).siblings('ul').addClass('none').children().children('ul').addClass('none');
        } else {
            $(this).children('.nav-item-icon').addClass('nav-item-icon-active');
            $(this).siblings('ul').removeClass('none');
        }
    });
});
 
 

