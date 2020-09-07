$(function () {
    /*
	* @auther 邓钊
	* @desc 帮助中心搜索功能
	* @date 2018-7-9
	* */
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

    // 邓钊 2018年08月15日 回车搜索
    $("body").on('keydown',function (e) {
        if(e.keyCode == 13){
            $(".right_btn").click()
        }
    })
})