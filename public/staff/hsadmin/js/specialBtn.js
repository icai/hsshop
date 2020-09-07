$(function(){
    // 按钮样式
    $('.switch_items').click(function(event){
        $(this).find('label').addClass('loadding');
        var _this = $(this);
        setTimeout(function(){
            _this.find('label').removeClass('loadding');
        },80);
        event.stopPropagation();    //  阻止事件冒泡
    });
    

})