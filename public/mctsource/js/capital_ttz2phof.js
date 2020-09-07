$(function(){
	// 账号切换
	$('.js_selectBank input').change(function(){
		var _cls = $(this).data('class');
		$('.'+_cls).removeClass('no').siblings('.message_notice_warning').addClass('no');
	});
})