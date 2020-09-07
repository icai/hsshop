var logo=$('#logo').val();
$('#files').on('change', function(){
	var reader = new FileReader();//获取base64
	reader.readAsDataURL(this.files[0]); 
	reader.onload = function(e){ 
		$('.logo').attr('src',this.result);
	}
	$.ajax({
	    url: '/auth/myfile/upfile',
	    type: 'POST',
	    cache: false,
	    data: new FormData($('#uploadForm')[0]),
	    processData: false,// 告诉jQuery不要去处理发送的数据
	    contentType: false,// 告诉jQuery不要去设置Content-Type请求头
	    headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
	    success:function(res) {
	    	var res = JSON.parse(res);
	    	 console.log(res)
	    	if(res.status == 1){
	    		logo= res.data['path'];
	    	}
		},
		error:function(){

		}
	})
});
$(document).keypress(function(event){
	if(event.keyCode == 13 )$('.btn-primary').click();
})
$('.btn-primary').click(function(){
	var _this = $(this);
 	_this.prop('disabled',true);
	var _value = $('input[name="name"]').val();
	_value = _value.replace(/(^\s*|\s*$)/g,'');
	if($('input[name="name"]').val().length == 0){
		tipshow('请填写昵称后再提交','warn');
		_this.prop('disabled',false);
		return false;
	}
	$.post('/auth/set/update',{'_token': $('meta[name="csrf-token"]').attr('content'),'name': _value,'logo': logo},function(data){
		if(data.status == 1){
			tipshow('确认修改成功','info',3000);
		}
		_this.prop('disabled',false);

	});
});