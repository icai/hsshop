$(function(){
	$('.delete').click(function(){
		$(this).siblings('input').val('');
	});
	$('.btn').click(function(){
		if(!$('.withdrawal_money input').val()){
			tool.tip('请输入提现金额');
			return false;
		}
		console.log()
		if(Number($('.withdrawal_money input').val()) > Number($('.cash').val())){
			console.log($('tip'))
			$('.tip').text('输入金额超过零钱余额').css({'color':'#F96268'})
			return false;
		}
		var data = {
			_token: $('meta[name="csrf-token"]').attr('content'),
			bank_id: bank_id,
			money: $('.withdrawal_money input').val()
		}
		$.post('/shop/distribute/withdrawal',data,function(data){
			tool.tip(data.info);
			if(data.status==1){
				setTimeout(function(){
					location.href = '/shop/distribute/wealth';
				},1000);
			}
		})
	});
	$('#money').on('focus',function(){
		if(!$('.withdrawal_money input').val()){
			$(this).siblings('.delete').show()
		}else{
			$(this).siblings('.delete').hide()			
		}
	});
	$('#money').on('blur',function(){
		if(!$('.withdrawal_money input').val()){
			$(this).siblings('.delete').hide()
		}else{
			$(this).siblings('.delete').show()
		}
	});
});