$(function(){
	$('.j_item')
	if($('.j_item a').length == 0){
		$('.j_item').hide();
		$('.btn').hide();
		$('.no_data').show();
	}
	$('.j_item_dt').each(function(){
		if($(this).hasClass('j_item_dt_2')){
			$(this).children('input').prop('checked',true);
		}
	});
	
	$('.j_item a').click(function(){
		$(this).find('.j_item_dt').toggleClass('j_item_dt_2');
		if($(this).find('.j_item_dt').hasClass('j_item_dt_2')){
			$(this).find('.j_item_dt input').prop('checked',true);
		}else{
			$(this).find('.j_item_dt input').prop('checked',false);
		}
	});
	$('.btn').click(function() {
		var _this = $(this);
		_this.prop("disabled",true);
		var idArr = [];//银行卡id数组
		var data = {
			'ids' : idArr
		}
		$('.j_item .j_item_dt_2').each(function() {
			idArr.push($(this).parents('.bank').data('id'));
		});
		$.get('/shop/distribute/delAccount',data,function(data){
			tool.tip(data.info);
			if(data.status == 1){
				$('.j_item .j_item_dt_2').each(function() {
					$(this).parents('.bank').remove();
				});
				if($('.j_item a').length == 0){
					$('.j_item').hide();
					$('.btn').hide();
					$('.no_data').show();
				}
			}
			_this.prop("disabled",false);
		});
	});
});