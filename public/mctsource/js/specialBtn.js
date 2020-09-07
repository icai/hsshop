$(function(){
    // 按钮样式
    $('.switch_items').click(function(event){
    	//alert($("input").prop("checked"));   //触发的事件；
        $(this).find('label').addClass('loadding');
        var _this = $(this);
        setTimeout(function(){
            _this.find('label').removeClass('loadding');
        },80);
        event.stopPropagation();    //  阻止事件冒泡
    });
    

})