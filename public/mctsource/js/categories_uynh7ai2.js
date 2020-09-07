"use strict";
$(function(){
	$(".js-submit").click(function(){
		$(this).attr("disabled","disabled");
		var data ={},
			id = $("#id").val(),
			title = $("#title").val(),
			sort = $("#sort").val();
		if(title==""){
			$(this).removeAttr("disabled"); 
			tipshow("请填写分类名称!","warn");
			return;
		}   
		if(title==""){
			$(this).removeAttr("disabled"); 
			tipshow("请填写排序!","warn");
			return;
		}  
		var url =""; 
		if(id){
			url = "/merchants/microforum/categories/edited";
			data.id = id;
		}else{
			url = '/merchants/microforum/categories/added';
		}
		data.title = title;
		data.sort = sort;
	  	$.ajax({
		    url: url,
		    type: 'POST',
		    cache: false,
		    data: data, 
		    headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
		    success:function(res) {
		    	if(res.status==1){
		    		tipshow(res.info);
		    		setTimeout(function(){
		    			location.href="/merchants/microforum/categories/list";
		    		},500);
		    	}else{
		    		tipshow(res.info,"wran");
		    	}
			},
			error:function(){
				console.log("异常");
			}
		});
		$(this).removeAttr("disabled");
	}); 
});
