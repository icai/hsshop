$(function(){
	$('.no_longer').click(function(){//点击取消或不再提示
		var tag = $(this).data('tag');
		$.ajax({
			type:"GET",
			data:{tag:tag},
			url:"/shop/distribute/cancelDistribute",
			headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
            	if(res.status == 1){
            		tool.tip(res.info);
            		setTimeout(function(){
            			location.href='/shop/member/index/'+wid;            			
            		},500)
            	}else{
            		tool.tip(res.info);
            	}
            },
            error:function(){
            	alert('数据访问错误')
            }
		});
	});
	
	$('.button_sure').click(function(){
		$.ajax({
			type:"GET",
			data:'',
			url:"/shop/distribute/beDistribute",
			headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
            	if(res.status == 1){
            		tool.tip(res.info);
            		setTimeout(function(){
            			location.href='/shop/member/index/'+wid;            			
            		},500)
            	}else{
            		tool.tip(res.info);
            	}
            },
            error:function(){
            	alert('数据访问错误')
            }
		});
	})
})
