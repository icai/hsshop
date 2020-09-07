$(function(){
	$('.opt_btn').click(function(){
		var _val = $(this).siblings('input').val();
		if( _val ){
			layer.msg('激活成功',{
	            skin: 'success_tip',
	            offset: '40px',
	            time:2000
	        });
		}else{
			layer.msg('激活失败',{
	            skin: 'lose_tip',
	            offset: '40px',
	            time:2000
	        });
		}
	});
})