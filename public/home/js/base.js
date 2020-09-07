$(function(){
	var $sliderBar = $('.J_sliderBar');
	$('.nav-menu-list').on('mouseenter', '.chil-li', function(){
		$sliderBar.css({
			left: $(this).offset().left + 21,
			width: $(this).width(),
			opacity: 1
		});
		if ($(this).hasClass('J_service')) {
			$('.second_list').show();
		}
	}).on('mouseleave', '.chil-li', function(){
		$sliderBar.css({
			opacity: 0
		});
		if ($(this).hasClass('J_service')) {
			$('.second_list').hide();
		}
	});
	$('.qq-link').click(function(){
		$('body').append('<iframe class="iframe" style="display:none;" src="tencent://message/?uin=1658349770&Site=&menu=yes"></iframe>');
	})
	

	
//	懒加载
	$("img.lazy").lazyload({
    	threshold : 200,
    	effect : "fadeIn"
	});
	
});
 /**
html 提示信息;
bgcolor:提示背景颜色;值为 info，warn
time:提示显示时间默认2秒;
**/
function tipshow(html,bgcolor,time){
    $(".info_tip").remove(); 
    var bgcolor = bgcolor || 'info';
    var a = arguments[2] ? arguments[2] : 2000; 
    var tipHtml = '<div class="info_tip">'+ html +'</div>';
    $('body').append(tipHtml);
    if(bgcolor == "info"){
        $('.info_tip').css('background-color','#45b182')
    }else if(bgcolor == "warn"){
        $('.info_tip').css('background-color','#ff1313')
    }
    var w = $(".info_tip").width()/2;
    if(w>450)
        $(".info_tip").css({"margin-left":-w+"px"});
    $('.info_tip').show(100);
    setTimeout(function(){
        $('.info_tip').remove();
    },a); 
}