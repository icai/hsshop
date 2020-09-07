var _token =document.getElementsByTagName('meta')[4].content;//tokenֵ

//模态框居中控制
$('.modal').on('shown.bs.modal', function (e) { 
  	// 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
  	$(this).css('display', 'block'); 
  	var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
  	if(modalHeight < 0){
  		modalHeight = 0;
  	}
  	$(this).find('.modal-dialog').css({ 
    	'margin-top': modalHeight 
 	}); 
});
$('.remove').change(function(event) {
	$('#myModal .modal-footer .btn:eq(0)').toggleClass('co_bbb btn-success');
});
$(document).on('click','#myModal .modal-footer .btn-success',function(){
	$.get('/merchants/wechat/relieveAuth',function(data){
		if(data.status==1){
			window.location.href = data.url;
		}else{
			tipshow(data.info,'warn');
			$('#myModal').modal('hide');
		}
	});
});
$(document).on('click','.btn-deltete',function(){
	$.get('/merchants/wechat/relieveAuth',function(data){
		if(data.status==1){
			window.location.href = data.url;
		}else{
			tipshow(data.info,'warn');
			$('#myModal').modal('hide');
		}
	});
});