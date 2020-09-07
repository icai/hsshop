$(function(){
    $('.solution-item').mouseenter(function(e){
        e.stopPropagation();
        $(this).find('.solution-item-example').animate(
            {opacity:1,bottom:130},100
        );
    })
    //点击选择模板
    var template_id = 0;
    $('.solution-item').click(function(){
        $('.solution-item').removeClass('active');
        $(this).addClass('active');
        template_id = $(this).data('id');
    })
    $('.solution-item').mouseleave(function(e){
        e.stopPropagation(); 
        $(this).find('.solution-item-example').animate(
            {opacity:0,bottom:0},100
        );
    })
    $('.js-save').click(function(){
        $.post('',{template_id:template_id,_token:$('input[name="_token"]').val()},function(data){
            if ( data.status == 1 ) {
                tipshow(data.info);
                /* 后台验证通过 */
                if ( data.url ) {
                    /* 后台返回跳转地址则跳转页面 */
                    window.location.href = data.url;
                } else {
                    /* 后台没有返回跳转地址 */
                    // to do somethings
                }
            } else {
                tipshow(data.info, 'warn');
                /* 后台验证不通过 */
                $('input[type="submit"]').prop('disabled', false);
                // to do somethings
            }
        })
    })
})