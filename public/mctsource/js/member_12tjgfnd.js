$(function(){
	//如果没有数据；
	if($(".main_content .data_content").length == 0){
		var _html = '<div class="no_date">没有更多数据了</div>'
		$(".main_content").append(_html);
		//页码隐藏
		$("#show, .pagination").hide();
	}

})