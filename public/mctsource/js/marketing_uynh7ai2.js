$('.xcx-icon').hover(function(){
    $(this).siblings('.xcx-icon-tips').toggle();
});

/**
 * @auther 邓钊
 * @desc 判断是否绑定小程序来执行是否跳转到刮刮卡列表
 * @date 2018-7-30
 * */
$("#getScratch").on('click',function () {
    if(isBindWechat == 0){
        alert('未绑定小程序不可使用此功能')
    }else{
        window.location.href='/merchants/marketing/scratchList'
    }
})
// 晒图有奖弹出小程序二维码
$(".shai_img").hover(function(){
    $(".shai_code").show()
},function(){
    $(".shai_code").hide()
})