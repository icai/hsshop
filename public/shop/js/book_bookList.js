$(function(){
	$('.b_delete').on('click',function(){
        
       var obj=$(this);
       var id = obj.data('id');
        tool.confirm('你确认要删除吗？',function(){

        	$.ajax({
                type:"post",
                url:'/shop/book/user/del',
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success: function(res){
                	
                    if(res.status===1){
                        tool.tip('删除成功','info');
                        setTimeout(function(){
                            obj.parent().parent().remove()
                            window.location.reload()
                        },1000);
                    }else{
                        tool.tip('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            });
        });
        
		
	})

	
	$('.b_revise').on('click',function(){
		var id = $(this).data('id');
		window.location.href='/shop/book/user/save/'+id;
	})
})