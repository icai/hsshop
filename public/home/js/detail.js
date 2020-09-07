$(function(){
    $(".right_btn").on('click',function () {
        var val = $(".right_inp").val()
        if(!val){
            return false
        }
        window.location.href = APP_URL + 'home/index/helpList?keywords=' + val
        var html = "<span>"+val+"</span>"
        var spans = $(".help_top_right_tip").children('span').length
        if(spans >= 4){
            $(".help_top_right_tip").children('span').eq(3).remove()
        }
        $(html).insertBefore($(".help_top_right_tip").children('span')[0]);
    })

    $(".list_left_div").on('click',function () {
        var Cls = $(this).children('span').attr('class')
        if(!Cls){
            $(this).children('span').addClass('span_active')
            $(this).siblings('ul').show()
        }else{
            $(this).children('span').removeClass('span_active')
            $(this).siblings('ul').hide()
        }
    })
    var boxWidth = $(".list_right_box_div").width();
    var imgs = $(".list_right_box_div").find('img')
    for(var i = 0; i < imgs.length; i++){
        var imgWidth = $(imgs[i]).width()
        if(imgWidth > boxWidth){
            $(imgs[i]).css('width',"100%")
        }
    }

    // 邓钊 2018年08月15日 回车搜索
    $("body").on('keydown',function (e) {
        if(e.keyCode == 13){
            $(".right_btn").click()
        }
    })
})
