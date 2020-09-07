//按钮启用
$(document).on('click','.btn1 button',function(){
	if ($(this).hasClass('active')) {
		$(this).removeClass('active');
	} else{
		$(this).addClass('active');
	}
});
//如果存在数据no_result消失
if ($('.data_list tbody tr').length == 0) {
	$('.data_list').hide();
	$('.right_container>.no_result').show();
}else{
	$('.data_list').show();
	$('.right_container>.no_result').hide();
	$('.page span').text('共'+$('.data_list tbody tr').length+'条，每页 20 条');
}
//--------删除模态框点击事件----------
//--------删除模态框点击事件----------
var index;
$(document).on('click','.pop',function(){
    index = $(this).parents('tr').index();
});
$('.del_popover .btn-default').click(function(){
	popoverHidden();
});
$('.del_popover .btn-primary').click(function(){
	console.log(index)
	$('tbody tr').eq(index).remove();
	$('.del_popover').hide();//隐藏删除prover
	if ($('.data_list tbody tr').length == 0) {
		$('.data_list').hide();
		$('.right_container>.no_result').show();
	}
	$('.page span').text('共'+$('.data_list tbody tr').length+'条，每页 20 条');
	popoverHidden();//隐藏popover
});

//-----------修改时间模态框显示时间------------
$(document).on('click','.opts .time',function(){
	//-------datatimepicker实例化------------
	$('.datetimepicker').datetimepicker({
		defaultDate:new Date(),
		locale: 'zh-CN',
		format:'YYYY-MM-DD HH:mm:ss'
	});
	$(this).siblings('.time_cap').show();
});
$(document).on('click','.opts .time_cap .btn-primary',function(){
	$(this).parents('tr').children('.set_time').text($(this).siblings('input').val());
	$(this).parent().hide();
	tipshow('修改时间成功','info');
});
$(document).on('click','.opts .time_cap .btn-default',function(){
	$(this).parent().hide();
});

function copyToClipboard( obj ) {                
	var aux = document.createElement("input");   
	// 获取复制内容                                    
	var content = obj.text() || obj.val();       
	// 设置元素内容                                    
	aux.setAttribute("value", content);          
	// 将元素插入页面进行调用                               
	document.body.appendChild(aux);              
	// 复制内容                                      
	aux.select();                                
	// 将内容复制到剪贴板                                 
	document.execCommand("copy");                
	// 删除创建元素                                    
	document.body.removeChild(aux);              
}
//点击复制复制内容
$('.link_cap .btn-default').on('click',function(){
	copyToClipboard($('.link_cap input'));
	tipshow('复制内容成功','info');
	popoverHidden();//隐藏popover
});
//--------------点击图片显示模态框---------------
$('.his_img').on('click',function(){
	$('.cap').show();
});

//----------------图片模态框点击事件---------------

//删除弹框添加图片
$('.input_img a').on('click',function(){
	$(this).parent().hide();
	$(this).siblings('img').attr('src','');
	$('.add').show();
	$('.upload').val('');
});
//------------添加数据如果为第一条数据则隐藏no_result 显示数据--------
function data_show(){
	if ($('.data_list tbody tr').length == 1) {
		$('.data_list').show();
		$('.right_container>.no_result').hide();
	}
	$('.page span').text('共'+$('.data_list tbody tr').length+'条，每页 20 条');
}
//新建一条数据
function new_data(src){
	var date1 =new Date();
	var add_img = 	'<tr>'
						+'<td class="title">'
							+'<img src="'+src+'"/>'
						+'</td>'
						+'<td class="set_time">'+formatDate(date1,'-')+'</td>'
						+'<td class="send">0</td>'
						+'<td class="delivery">0</td>'
						+'<td class="options">'
							+'<div class="opts">'
								+'<a class="co_38f time" href="javascript:void(0);">修改时间</a>-'
								+'<a class="co_38f pop" data-toggle="del_popover" href="javascript:void(0);">删除</a>'
								+'<div class="time_cap">'
									+'<input class="datetimepicker" type="text" />'
									+'<button class="btn btn-primary">确定</button>'
									+'<button class="btn btn-default">取消</button>'
								+'</div>'
							+'</div>'
							+'<div class="btn1">'
               					+'<button></button>'
               				+'</div>'
						+'</td>'
					+'</tr>';
	$('.data_list tbody').prepend(add_img);
}
//图片库中点击选中图片
$(document).on('click','.img_list li',function(){
	new_data($(this).children('img').attr('src'));
	$('#myModal-adv').modal('hide');
	data_show();
	tipshow('添加内容成功','info');
});
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
		var img = 	'<li>'
	    				+'<img src="'+pictureSrc+'"/>'
	    			+'</li>';
	    $('.content_first .img_list').prepend(img);
		
	}
    new_data(pictureSrc);
	$('.myModal-adv').modal('hide');
	data_show();
	$('.modal-footer .js-confirm').show();
	$('.modal-footer .ui-btn-primary').addClass('no');
	if($('.content_second').css('display') != 'none'){
		closeUploader();
	};
});

//---------------图文模态框点击选取后添加数据----------------
$('#myModal .small .btn-default').on('click',function(){
	var add_news = 	'<tr>'
						+'<td class="title">'
							+'<div class="item">'
								+'<div class="img_text">'
									+'<span class="green">图文</span>'
									+'<a class="co_blue" href="'+$(this).parents('tr').find('.img_text a').attr('href')+'">'+$(this).parents('tr').find('.img_text a').text()+'</a>'
								+'</div>'
								+'<div class="read_all clearfix">'
									+'<span>阅读全文</span>'
									+'<span class="pull-right">></span>'
								+'</div>'
							+'</div>'
						+'</td>'
						+'<td class="set_time">'+$(this).parent().siblings('.news_time').text()+'</td>'
						+'<td class="send">0</td>'
						+'<td class="delivery">0</td>'
						+'<td class="options">'
							+'<div class="opts">'
								+'<a class="co_38f time" href="javascript:void(0);">修改时间</a>-'
								+'<a class="co_38f pop" data-toggle="del_popover" href="javascript:void(0);">删除</a>'
								+'<div class="time_cap">'
									+'<input class="datetimepicker" type="text" />'
									+'<button class="btn btn-primary">确定</button>'
									+'<button class="btn btn-default">取消</button>'
								+'</div>'
							+'</div>'
							+'<div class="btn1">'
               					+'<button></button>'
               				+'</div>'
						+'</td>'
					+'</tr>';
	$('.data_list tbody').prepend(add_news);
	$('#myModal').modal('hide');
	data_show();
	tipshow('添加内容成功','info');
});
