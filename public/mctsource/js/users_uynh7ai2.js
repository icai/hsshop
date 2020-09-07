'use strict';
$(function(){
	//拉黑
	$(".defriend").click(function(e){ 
		e.stopPropagation();
		var id = $(this).parent().attr("data-id");
		showDelProver($(this), function(){
            $.ajax({
			    url: '/merchants/microforum/users/blocked',
			    type: 'POST', 
			    data: {id:id}, 
			    headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
			    success:function(res) {
			    	if(res.status==1){
			    		tipshow(res.info);
			    		setTimeout(function(){
			    			location.reload();
			    		},500);
			    	}else{
			    		tipshow(res.info,"wran");
			    	}
				},
				error:function(){ 
					console.log("异常");
				}
			});
        }, '确定要拉黑吗?'); 
	});
	//恢复
	$(".recovery").click(function(e){ 
		e.stopPropagation();
		var id = $(this).parent().attr("data-id");
		showDelProver($(this), function(){
            $.ajax({
			    url: '/merchants/microforum/users/unblocked',
			    type: 'POST', 
			    data: {id:id}, 
			    headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
			    success:function(res) {
			    	if(res.status==1){
			    		tipshow(res.info);
			    		setTimeout(function(){
			    			location.reload();
			    		},500);
			    	}else{
			    		tipshow(res.info,"wran");
			    	}
				},
				error:function(){ 
					console.log("异常");
				}
			});
        }, '确定要恢复吗?'); 
	});
})