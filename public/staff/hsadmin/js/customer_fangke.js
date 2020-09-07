$(function(){
	
    //修改手机号
    $("body").on('click','.change_phone',function(){
    	var id = $(this).data('id');
    	var original_name = $(this).parents('.table_body').find('.original_name').html();
    	var original_mobile = $(this).parents('.table_body').find('.original_mobile').html();
    	var original_nickname = $(this).parents('.table_body').find('.original_nickname').html();
    	$('.span_name').text(original_name);
    	$('.span_nickname').text(original_nickname);
    	$('.span_phone').text(original_mobile);
		layer.open({
            type: 1,
            area: ['400px', 'auto'],
            title: '请确认需要修改号码的用户信息',
            shadeClose: true, //点击遮罩关闭
            content: $('.change_tip').html(),
            btn: ['确认', '取消'],
            success:function(index,layero){
		    },
            yes:function(idnex,layero){
            	var data = {
		    		id:id,
		    		mobile:$(layero).find(".set_zhost").val()
		    	}
		    	$.ajax({
					url:"/staff/BusinessManage/changeMobile",
					type:"GET",
					data:data,		
					dataType:'json',
					headers: {
			            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			        },
					success:function(res){
						if(res.status == 1){
							tipshow(res.info,"info");
							window.location.reload();
						}else{
							tipshow(res.info,"warn")
						}
					},
					error:function(res){
						alert("数据访问错误");
					}
				}),
		    	layer.closeAll();
           }
        });
    })	
})
