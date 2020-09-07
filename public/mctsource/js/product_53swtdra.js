$(function(){
    // 删除确定
    $(document).on('click','.sure_btn',function(){
        hideDelProver();//隐藏删除prover
    })
    // 删除确定
    $(document).on('click','.cancel_btn',function(){
        hideDelProver();//隐藏删除prover
    })
    // $('.delete').popover({
    //     trigger:'click',
    //     content:'<span>你确定要删除吗？</span><button class="btn btn-primary sure_btn">确定</button><button class="btn btn-default cancel_btn">取消</button>',
    //     html:true,
    //     placement:'left',
    //     template:'<div class="popover del_popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    // })
    $('.delete').click(function(e){
    	e.stopPropagation();
		var id= $(this).data('id');
        showDelProver($(this),function(){
			$.ajax({
				type:"POST",
				url:'/merchants/product/deleteProductTemplate',
				data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
				dataType:'json',
				success: function(msg)
				{   
					console.log(msg);
					if(msg.errCode==0)
					{
						tipshow('删除成功！');
						window.location.reload();
					}
				},
				error:function(msg)
				{
					tipshow('网络错误','warn');
				}
			});
		});//显示删除按钮
    })  
	
})