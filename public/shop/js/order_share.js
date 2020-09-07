$(function(){
    $('#js-share-guide').click(function(){
        $(this).hide();
        $('.order-share-back').css('visibility','visible');
    })
    var width = window.screen.width;
    var height = window.screen.height;
    $('.order-item').css('width',width-40);
    $('.order').css('width',width*$('.order-item').length - ($('.order-item').length-1)*20);
})