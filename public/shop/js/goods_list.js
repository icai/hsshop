$(function(){
    //点击商品跳转到详情页
    $(".flex_star").click(function(){
        window.location.href = $(this).data('href');
    })
});