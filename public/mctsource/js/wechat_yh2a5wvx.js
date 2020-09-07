$('.right_content .box').on('click',function(e){
	$('.new_cap').show();
	e.stopPropagation();
});

$('body').on('click',function(){
	$('.new_cap').hide();
});
if ($('tbody tr').length > 0) {
	$('.no_result').hide();
}
//--------删除模态框点击事件----------
var index; 
var url= '';
$(document).on('click','.delete',function(){
    index = $(this).parents('tr').index();
    url = $(this).data('url');
});
$('.popover .sure_btn').click(function(){//确定删除
	$.get(url,function(data){
		console.log(data)
		if(data.status == 1){
			tipshow(data.info,'info');
			$('.table tbody tr:eq('+index+')').remove();
		}else{
			tipshow(data.info,'warn');
		}
	});
	popoverHidden();//隐藏pop
});
$('.popover .cancel_btn').click(function(){//取消删除
	popoverHidden();//隐藏pop
});
// //数据条数
// $('.page span').text('共 '+$('.data_list tbody tr').length+'条，每页 20 条');
