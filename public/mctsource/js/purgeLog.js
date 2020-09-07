$(function(){
	
	
	//清空筛选
	$("#clearJudge").click(function(){
		$("#nickName, #mobile,#orderSource").val("");
	})
	var sort='created_at-desc';
	// 排序
	$('.J_sort').click(function(){
		var type = $(this).data('type'),
			sortType = +$(this).data('sort');
		switch (sortType){
			case 1:
				sort = type + '-desc';
				break;
			case 2:
				sort = type + '-asc';
				break;
		}
		$('#sort').val(sort);
		$('#forms').submit();
	});
   
})
