// 存在数据就消除提示
if ($('tbody tr').length > 0) {
	$('.no_result').hide();
	$('.data_list').show();
}
// 控制显示页码数量
$('.page_footer span').text('共 '+$('tbody tr').length+'条，每页 20 条');
//编辑
$('.operate_edit ').click(function(){
	//交互时再写   数据成功后跳转页面
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
		$('.data_list').hide();
		$('.no_result').show();
	}
	var page_num = '共 '+$('.table tbody tr').length+'条，每页 20 条';
	$('.page_footer span').text(page_num);
	popoverHidden();//隐藏popover
});
