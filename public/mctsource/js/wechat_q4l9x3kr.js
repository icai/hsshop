
//qq表情包
emotion('.emotion','emotion_text');
$(document).on('click','.qqFace img',function(){
	$('#emotion_text').focus();
	fontCount();//显示文本域字数统计
});
//判断文本域中字数统计
function fontCount(){
	$('.word_count').text('大约还可输入'+(600-$('#emotion_text').val().length)+'字');
}
$('#emotion_text').on('input',function(){
	fontCount();
	$('.send_info').hide();
});
//-------创建发送消息的标题点击事件----------
//点击标题显示自己的文本图文
$('.set_message_title li').on('click',function(){
	$('.set_message_title li').removeClass('opacity');
	$(this).addClass('opacity');
	$('.set_message_content').children().hide();
	$(''+$(this).data('class')+'').show();
	if ($(this).index() == 0) {
		$('.set_message_footer').show();
		$('#emotion_text').focus();
	} else{
		$('.set_message_footer').hide();
	}
});
//点击图片显示模态框
$('.set_message_title .img,.editor_img .select').on('click',function(){
	$('.cap').show();
});
//点击删除  删除创建的图片
$('.editor_img .message_img a').on('click',function(){
	$(this).parent().hide();
	$('.editor_img .select').show();
	$(this).siblings('img').attr('src','');
});
//--------------选择弹框若是没内容显示info----------------
$(document).on('click','.js_showModel',function(){
//	判断当前模态框有几个tbody大于一就隐藏info
	if($(''+$(this).attr('data-target')+' tbody').length > 1){
		$(''+$(this).attr('data-target')+' .tabel_info').hide();
	}
});

//----------------图片模态框点击事件---------------
//点击确认后获取图片
$('.myModal-adv .ui-btn-primary').on('click',function(){
	if($('.content_second').css('display') != 'none'){
		var add_img =  '<li class="image-item">'
                        	+'<img class="image-box" src="'+pictureSrc+'"/>'
                            +'<div class="image-meta">1920*1200</div>'
                            +'<div class="image-title">01.png</div>'
                            +'<div class="attachment-selected no">'
                                +'<i class="icon-ok icon-white"></i>'
                           + '</div>'
                        +'</li>';
		$('.attachment-list-region:eq('+$('.category-list .active').index()+') .image-list').prepend(add_img);
		
	}
	$('.message_img img').attr('src',pictureSrc);
	$('.send_info').hide();
	$('.editor_img .select').hide();
	$('.editor_img .message_img').show();
	$('.myModal-adv').modal('hide');
	//重置上传样式
	$('.attachment-selected').addClass('no');
	$('.modal-footer .js-confirm').show();
	$('.modal-footer .ui-btn-primary').addClass('no');
	if($('.content_second').css('display') != 'none'){
		closeUploader();
	};
});
//---------按钮组点击事件-------------
//立即群发
$('.btn_group .promptly').on('click',function(){
	if($('.opacity').index() == 0){
		if ($('#emotion_text').val() == '') {
			$('.send_info').show();
			return false;
		}
	}else if($('.opacity').index() == 1){
		if ($('.editor_img img').attr('src') == '') {
			$('.send_info').show();
			return false;
		}
	}
});
//手机预览
$('.btn_group .preview').on('click',function(){
	if($('.opacity').index() == 0){
		if ($('#emotion_text').val() == '') {
			$('.send_info').show();
			return false;
		}
	}else if($('.opacity').index() == 1){
		if ($('.editor_img img').attr('src') == '') {
			$('.send_info').show();
			return false;
		}
	}
})
// 点击预览按钮隐藏模态框
$('.previewModal .btn-success').click(function(){
	$('.previewModal').modal('hide');
});
//-------datatimepicker实例化------------
$('#datetimepicker').datetimepicker({
	defaultDate:new Date(),
	locale: 'zh-CN',
	format:'YYYY-MM-DD HH:mm:ss'
});
//点击定时群发触发模态框
$('.btn_group .timer').on('click',function(){
	if($('.opacity').index() == 0){
		if ($('#emotion_text').val() == '') {
			$('.send_info').show();
			return false;
		}
	}else if($('.opacity').index() == 1){
		if ($('.editor_img img').attr('src') == '') {
			$('.send_info').show();
			return false;
		}
	}
	$('.timer_cap').show();
});
$('.timer_cap .btn-default').on('click',function(){
	$(this).parent().hide();
});
$('.timer_cap .btn-primary').on('click',function(){
	$(this).parent().hide();
});
//-----------图文模态框点击选取添加消息-----------
$('.modal .modal-body .btn-default').on('click',function(){
	$('.editor_news .img_text a').attr('href',$(this).parents('tr').find('a:first').attr('href'));
	$('.editor_news .read_all a').attr('href',$(this).parents('tr').find('a:first').attr('href'));
	$('.editor_news .img_text a').text($(this).parents('tr').find('a:first').text());
	$('.modal').modal('hide');
});

