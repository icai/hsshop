$(function(){
	// 全选
	$('.check_all').click(function(){
		if( $(this).prop('checked') ){		// 全选
			$('.check_single').prop('checked',true);
		}else{								// 反选
			$('.check_single').prop('checked',false);
		}
	});

	// 单选
	$('.check_single').click(function(){
		if( $(this).prop('checked') ){
			$(this).prop('checked' ,true);
		}else{
			$(this).prop('checked' ,false);
		}
		checkAll();
	});

	// 设置弹框
	$('.set_batch').click(function(){
		if( checkAll() ){
			$('#setModule').modal('show');
		}else{
	
	  		tipshow('未选中商品！');
		}
	});
	

})

/**
 * [checkAll 全选函数]
 * @return {[type]} [boolean]
 */
function checkAll(){
	var count = 0,
		length = $('.check_single').length,
		flag = false;
	$('.check_single').each(function(){
		if( $(this).prop('checked') ){
			count ++;
			flag = true;
		}
	});

	if( count == length ){
		$('.check_all').prop('checked' ,true);
	}else{
		$('.check_all').prop('checked' ,false);
	}
	return flag;
}