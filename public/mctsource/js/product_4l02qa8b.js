function changeDivStyle(){
        //      var o_status = $("#o_status").val();    //获取隐藏框值
    var o_status = 2;
    if(o_status==0){
        $('#create').css('background', '#428bca');
        $('#createText').css('color', '#428bca');
        $('.order_progress li').css('background','#bbb')
        $('.order_progress li').each(function(key,val){
            if(key<1){
                $(this).css('background', '#428bca');   
            }
        })
    }else if(o_status==1){
        $('#check').css('background', '#428bca');
        $('#checkText').css('color', '#428bca');
        $('.order_progress li').each(function(key,val){
            if(key<3){
                $(this).css('background', '#428bca');   
            }
        })

    }else if(o_status==3){
        $('#delivery').css('background', '#428bca');
        $('#deliveryText').css('color', '#428bca');
        $('.order_progress li').each(function(key,val){
            if(key<6){
                $(this).css('background', '#428bca');   
            }
        })
    }else if(o_status==2){
       
        $('#produce').css('background', '#428bca');
        $('#produceText').css('color', '#428bca');
         $('.order_progress li').each(function(key,val){
            if(key<5){
                $(this).css('background', '#428bca');   
            }
        })
    }
}
window.onload = function(){
    setTimeout("changeDivStyle();", 100);
    $('.widget-goods-klass-item').click(function(){
        $('.widget-goods-klass-item').removeClass('current');
        $(this).addClass('current');
    })
}