$(function(){
	$('#search').keypress(function(event){  
        var keycode = (event.keyCode ? event.keyCode : event.which);  
        if(keycode == '13'){  
            alert('a');    
        }  
    });
    // 删除确定
    $(document).on('click','.sure_btn',function(){
        hideDelProver();//隐藏删除prover
    })
    // 删除确定
    $(document).on('click','.cancel_btn',function(){
        hideDelProver();//隐藏删除prover
    })
   
    $('.delete').click(function(){
        showDelProver($(this));//显示删除按钮
    })
})