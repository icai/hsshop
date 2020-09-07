// 存在数据就消除提示
if ($('tbody tr').length > 0) {
	$('.no_result').hide();
	$('.widget_list').show();
}
var ctl_cap = true;
var index;
$('.btn-success').click(function(){
	ctl_cap = true;
})
$('.modal-footer .btn-primary').on('click',function(){
	var d = new Date();
	//调用封装时间函数
	s=formatDate(d,'-');
	//输出时间
	var add_list ='<tr>'
			        +' <td class="ctt">'+replace_em($('#ctl_emotion').val())+'</td>'
			        + '<td></td>'
			         +'<td>'+s+'</td>'
			         +'<td class="text_right">'
			         	+'<div class="operate">'
	   						+'<a class="operate_edit co_38f" href="javascript:void(0);" data-toggle="modal1" data-target="#myModal1"">编辑</a>'
	   						+'<span>-</span>'
	   						+'<a class="operate_delete co_38f" href="javascript:void(0);">删除</a>'
			         	+'</div>'
			         	+'<div class="num">'
			         		+'序号：<span class="co_38f">0</span>'
			         	+'</div>'
			         +'</td>'
			      +'</tr>';
	if (ctl_cap){
		$('tbody').prepend(add_list);
	}else{
		ctl_cap = false;
		$('.ctt').eq(index).text($('#ctl_emotion').val());
	}
	$('.modal').modal('hide');
	if ($('tbody tr').length == 1) {
		$('.widget_list').show();
		$('.no_result').hide();
	}
	var page_num = '共 '+$('.table tbody tr').length+'条，每页 20 条';
	$('.page_footer span').text(page_num);
	$('#ctl_emotion').val('');
	tipshow('新建快捷短语成功','info');
	count();
});
//编辑
$(document).on('click','.operate_edit',function(){
	index = $(this).index('.operate_edit');
	ctl_cap = false;
	$('.modal').modal('show');
	$('#ctl_emotion').val($(this).parents('tr').children('.ctt').text()).focus();
});
//--------删除模态框点击事件----------
var index; 
$('.pop').click(function(){
    index = $(this).parents('tr').index();
});
$('.del_popover .btn-default').click(function(){
	popoverHidden();//隐藏popover
});
$('.del_popover .btn-primary').click(function(){
	$('tbody tr').eq(index).remove();
	if ($('.table tbody tr').length == 0) {
		$('.widget_list').hide();
		$('.no_result').show();
	}
	var page_num = '共 '+$('.table tbody tr').length+'条，每页 20 条';
	$('.page_footer span').text(page_num);
	popoverHidden();//隐藏popover
});
//字数控制
function count(){
	var tails_content = '大约还可以输入'+(600-$('#ctl_emotion').val().length)+'个字';
	$('.cap_info').text(tails_content);
}
$('#ctl_emotion').on('input',function(){
	count();
});
//qq表情包调用
emotion('.emotion','ctl_emotion');
$(document).on('click','.qqFace img',function(){
	$('#ctl_emotion').focus();
});
//链接
$('.link_span').on('click',function(){
	$('.link').show().focus();
});
$('.link .btn-primary').on('click',function(){
	var str = /^http:\/\//;
	var value = $(this).siblings('input').val();
	if (str.test(value)) {
		value = $('#ctl_emotion').val()+value;
		$('#ctl_emotion').val(value);
	} else{
		value = 'http://'+value;
		value = $('#ctl_emotion').val()+value;
		$('#ctl_emotion').val(value);
		
	}
	$('.link').hide();
	$('#ctl_emotion').focus();
});