$(function(){
	$('.btn').click(function(){
		var _this = $(this);
		_this.prop("disabled",true);
		var data = {
			'type': 2,
			'account': $('input[name="account"]').val(),
			'bank_name': '支付宝',
			'name': $('input[name="name"]').val(),
			'_token': $('meta[name="csrf-token"]').attr('content'),
			'logo': $('input[name="logo"]').val()
		};
		$.post('/shop/distribute/addAccount',data,function(data){
			tool.tip(data.info);
			if(data.status == 1){
				location.href = '/shop/distribute/selectAccount';
			}
			_this.prop("disabled",false);
		});
	});
});