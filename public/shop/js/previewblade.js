$(function(){
    //懒加载
    $('.lazyload').picLazyLoad({
        threshold: 200,
        effect : "fadeIn"
    });
	$('.messageHints').on('click',function(){
		tool.tip('预览暂不支持此功能，<br>实际效果请在手机上进行')
	})
    $('.shop_top').on('click',function(){
        tool.tip('预览暂不支持此功能，<br>实际效果请在手机上进行')
    })
    $('.buyNow').on('click',function(){
    	$("body").css("overflow","hidden");
    	$('.mask').removeClass('hideNoSee')
    	$('.content_sizeBox').removeClass('hideNoSee')
    })
    $('.delete').on('click',function(){
    	$("body").css("overflow","initial");
    	$('.mask').addClass('hideNoSee')
    	$('.content_sizeBox').addClass('hideNoSee')
    })
    $('.sizeBox_bottom').on('click',function(){
        tool.tip('预览暂不支持此功能，<br>实际效果请在手机上进行')
    })
})
