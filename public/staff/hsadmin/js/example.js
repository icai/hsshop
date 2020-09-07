$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	
	// 删除列表
    $('body').on('click','.del',function(e){  	   		
        e.stopPropagation();
        var _this = this;
		var id=$(this).data('id');
        showDelProver($(_this),function(){
			$.ajax({
				type:"post",
				url:'/staff/example/caseDel',
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
    
	//批量删除      
    $('body').on('click','.able_bom',function(e){  	
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
			url:'',
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
			}
		});	
    });
	
	//二维码
    $("body").on("click",".examcode",function(e){
        $(".t-pop").remove();
        var id = $(this).data('id');
        e.stopPropagation();//阻止事件冒泡 
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =$(this).offset().top-10+"px";
        div.style.left=$(this).offset().left-191+"px";
        var html ='<div class="t-pop-header">活动二维码<div class="flo-rig">X</div></div><div class="t-pop-content">';
        html +='</div><div class="t-pop-footer"><p class="xiazai">扫一扫立即参与活动</p></div>'
        div.innerHTML=html;      
        $("body").append(div);
    	$.ajax({
    		type:"get",
    		url:"/staff/example/createQrcode",
    		data:{id:id},
    		async:true,
    		success:function(res){
    			var json = $.parseJSON(res);
				$('.t-pop-content').html(json.qrcodeStr);
    		},
    		error:function(){
    			alert('数据访问错误');
    		}
    	});        
    });
    //删除二维码弹窗
    $("body").on("click",".flo-rig",function(e){
    	$(".t-pop").remove();
    	e.stopPropagation();
    })    
    //删除二维码弹窗
    $("body").click(function(e){
        $(".t-pop").remove();
    });
})