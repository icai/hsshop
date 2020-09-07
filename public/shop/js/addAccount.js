$(function(){
	$('.j_item_dt').each(function(){
		if($(this).hasClass('j_item_dt_2')){
			$(this).children('input').prop('checked',true);
		}
	});
	var data = {};
	$('.j_item a').click(function(){
		$('.j_item_dt').removeClass('j_item_dt_2');
		$(this).find('.j_item_dt').addClass('j_item_dt_2');
		$(this).find('.j_item_dt input').prop('checked',true);
		$('.add').show();
		$('.bank_list').hide();
		$('.add .item1 .left').text($(this).find('.bank_name').text());
		data.logo = $(this).find('.j_item_logo img').attr('src');
		data.bank_name = $(this).find('.bank_name').text();
	});
	$('.order-related .item1').click(function(){
		$('.add').hide();
		$('.bank_list').show();
	});
	$('.btn').click(function(){
		data.account = $('input[name="account"]').val();
		data.name = $('input[name="name"]').val();
		data['_token'] = $('meta[name="csrf-token"]').attr('content');
		data.type = 1;
		if(!data.logo||!data.bank_name||!data.account||!data.name){
			tool.tip('请完善所有信息');
			return false;
		}
		var _this = $(this);
		_this.prop("disabled",true);
		$.post('/shop/distribute/addAccount',data,function(data){
			tool.tip(data.info);
			if(data.status == 1){
				location.href = '/shop/distribute/selectAccount';
			}
			_this.prop("disabled",false);
		});
	});
});