$(function(){
	/*新增预约*/
	$('.newOrder').on('click',function(){
		window.location.href='/merchants/wechat/bookSave';
	})
	/*刷新*/
	$('.refresh').on('click',function(){
		window.location.reload()
	})
	/*删除*/
	$('body').on('click','.t_shan',function(e){
		e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/wechat/bookDel',
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow('删除成功','info');
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            }); 
        })
	})

    $('.book-link').click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('input');
        copyToClipboard( obj );
        tipshow('复制成功','info');
    });
})
