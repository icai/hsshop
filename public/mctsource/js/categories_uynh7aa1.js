"use strict";
$(function(){
	//删除
	$(".del").click(function(e){ 
		e.stopPropagation();
		var id = $(this).parents("ul").attr("data-id");
		showDelProver($(this), function(){
            $.ajax({
			    url: '/merchants/microforum/categories/deleted',
			    type: 'POST',
			    cache: false,
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
        }, '确定要删除吗?');
		
	});
	//全选或全不选
	$("#check_all").click(function(){ 
		var checked = this.checked;
		$(".cb_select").each(function(){
			this.checked = checked;
		}); 
	});
	//批量删除
	$(".js_batch_del").click(function(e){
		e.stopPropagation(); 
		var arr =[];
		$(".cb_select").each(function(){
			if(this.checked){
				arr.push(this.value);
			}
		}); 
		if(arr.length>0){
			showDelProver($(this), function(){
	            $.ajax({
				    url: '/merchants/microforum/categories/deleted',
				    type: 'POST',
				    cache: false,
				    data: {id:arr}, 
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
        	}, '确定要删除吗?',true,"right");
		}else{
			tipshow("请选择分类","wran");
		}
	});

});