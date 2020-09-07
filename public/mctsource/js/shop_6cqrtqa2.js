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
    // $('.delete').popover({
    //     trigger:'click',
    //     content:'<span>你确定要删除吗？</span><button class="btn btn-primary sure_btn">确定</button><button class="btn btn-default cancel_btn">取消</button>',
    //     html:true,
    //     placement:'left',
    //     template:'<div class="popover del_popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    // })
    $('.delete').click(function(e){
        e.stopPropagation();
		var id = $(this).data('id');
        showDelProver($(this),function(){
				$.ajax({
				type:"post",
				url:'/merchants/store/deleteModule',
				data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
				dataType:'json',
				success: function(msg){
					console.log(msg);
					if(msg.errCode==0){
                        tipshow('删除成功！');
                        setTimeout(function(){
                            window.location.reload();
                        },2000)
					}else{
                        tipshow(msg.errMsg);
                    }
				},
				error:function(msg){
					console.log(msg);
				}
			});
		});//显示删除按钮
    }) 
    // 改名
	var tid = 0;
    $('.change_name').click(function(e){
        e.stopPropagation();//阻止事件冒泡
	    tid = $(this).data('id');
        $('#change_name_input').val($(this).parent().parent().children('.text-left').html());
        $('#hsgf149058723771').show();
        $('#hsgf149058723771').css('top',$(this).offset().top-$('#hsgf149058723771').height()-16);
        $('#hsgf149058723771').css('left',$(this).offset().left-$('#hsgf149058723771').width()-210);
    })
    $('.sure_change_name').click(function(){ 
        $('#hsgf149058723771').hide();
		$.ajax({
			type:"post",
			url:'/merchants/store/updateModule/1',
			data:{id:tid,name:$('#change_name_input').val(),_token:$('meta[name="csrf-token"]').attr('content')},
			dataType:'json',
			success: function(msg){
				if(msg.errCode==0){
                    tipshow('改名成功!');
                    setTimeout(function(){
                        window.location.reload();
                    },2000)
				}
			},
			error:function(msg){
				console.log(msg);
			}
		});
    })
    $('.cancel_change_name').click(function(){
        $('#hsgf149058723771').hide();
    })
})