$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	});
	
	
	
    // 删除列表
    $('body').on('click','.del',function(e){
        e.stopPropagation();
        var _this = this;
		var id=$(this).data('id');
        showDelProver($(_this),function(){
			$.ajax({
				type:"post",
				url:'/staff/banner/selleradDel',
				data:{
					id:id
				},
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				success: function(res){
					if(res.status===1){
						tipshow('删除成功','info');
						$(_this).parents('.table_body').remove();
					}else{
                        tipshow('删除失败','warn');
                    }
				},
				error:function(){
					alert('数据访问异常')
				}
			});	
        })
    });

    // 启用
    $('body').on('click','.open',function(e){
        var _this = this;
        var id=$(this).data('id');
        var status = $(this).data('status');
        $.ajax({
            type:"post",
            url:'/staff/banner/selleradOpen',
            data:{
                id:id,
                status:status
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                if(res.status===1){
                    tipshow('开启成功','info');
                    window.location.reload();
                }else{
                    tipshow('开启失败','warn');
                }
            },
            error:function(){
                alert('数据访问异常')
            }
        });
    });

    //批量删除      
    $('body').on('click','.del_bom',function(e){  	   		
        e.stopPropagation();
        var category_id = [];
	    $('.ulradio').each(function(key,val){
	        if($(this).is(':checked')){
	            category_id.push($(this).data('id'));
	        }
	    }) 
        var type = 'del';
        var _this = this;;
		$.ajax({
			type:"post",
			url:'/staff/banner/selleradDel',
			data:{
				type:type,
				id:category_id
			},
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			success: function(res){
				if(res.status===1){
					tipshow('删除成功','info');
					window.location.reload(); 
				}else{
                    tipshow('删除失败','warn');
                }
			},
			error:function(){
				alert('数据访问异常')
			}
		});	
    });

})