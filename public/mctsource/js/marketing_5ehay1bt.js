$(function(){
	// 导航、
	$('.tab_nav li').click(function(){
		$(this).addClass('hover').siblings('li').removeClass('hover');		// 添加选中样式并去掉兄弟节点的选中样式
		var _cls = $(this).data('tab');											// 找到对应的demo的类名
		var _obj = $(this).parents('.bulletin_nav').siblings('.tab_body').find('.'+_cls); 		// 找到对应的demo对象
		_obj.removeClass('no');													// 显示
		_obj.siblings('.tab_items').addClass('no');								// 兄弟节点隐藏
	});

	// 信息提示
	$(".note_tip").popover({
		html :true,									// html标签是否起作用
		container:'.note_tip',						// 依靠的元素
		placement:'left',							// 箭头方向
		trigger:'hover',							// 事件
		// 主体内容
		content:'由于消息模版功能仅对认证服务号开放，因此将通过［有赞］公众号向你的粉丝代发送模版消息。<a class="blue_38f" href="javascript:void(0);" target="_blank">查看消息模版教程&gt;&gt;</a>',
		// 模板
		template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content f12"></div></div>',
	});
})