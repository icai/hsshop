
// 实现点击nav切换信息状态
$('.tab_nav .status').click(function(){
	$('.tab_nav .status').removeClass('hover');
	$(this).addClass('hover');
});
// 点击快速回复
var _index;
$('.click_reply').click(function(){
	_index =$(this).parents('tr').index();
	if(!$(this).hasClass('co_0099')){//存在这个类名弹框
		return false;
	}
});
// 快速回复点击确定发送消息
$('#quick_modal .btn-success').click(function(){
	var message = $(this).parents('.modal').find('.quick_modal_textarea').val()
	$('.table tbody tr').eq(_index).find('.reply_text').text(message);
	$('.modal').modal('hide');
	// 发送消息成功后不允许发送消息
	$('.table tbody tr').eq(_index).find('.click_reply').removeClass('co_0099');
});
// 快速回复字数显示
$('.quick_modal_textarea').on('input',function(){
	$('.js_quick_ctl').text(500 - $(this).val().length);
	
});
// 点击实现加星
$('.add_star').click(function(){
	tipshow('加星成功','info');
});
// 点击实现备注
$('.remark').click(function(){
	tipshow('备注成功','info');
});