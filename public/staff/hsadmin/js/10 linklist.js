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
				url:'/staff/link/linkDel',
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
			url:'/staff/banner/statusSave',
			data:{
				type:type,
				id:category_id
			},
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			success: function(res){
				console.log(res)
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

   // 复制链接跟随效果
    $('body').on('click','.copy',function(e){
        e.stopPropagation();//组织事件冒泡
        var _this = $(this);
        var _url = $(this).data('url');             // 要复制的连接
        var html ='<div class="input-group">';
        html +='<input type="text" class="link_copy form-control" value="'+_url+'" disabled >';
        html +='<a class="copy_btn input-group-addon">复制</a>';
        html +='</div>';
        showDelProver(_this,function(){},html,'false');             // 跟随效果
    });
    // 复制链接
    $('body').on('click','.copy_btn',function(e){
        e.stopPropagation();//组织事件冒泡
        var obj = $(this).siblings('.link_copy');
        console.log(obj)
        copyToClipboard( obj );
        tipshow('复制成功','info');
        $(this).parents('.del_popover').remove();
    });

})

/**
 * [copyToClipboard 复制到粘切板函数]
 * @param  {[type]} obj [ 要复制的对象 ]
 * @return {[type]}     [无]
 */
function copyToClipboard( obj ) {
    var aux = document.createElement("input");                  // 创建元素用于复制
    // 获取复制内容
    var content = obj.text() || obj.val();
    // 设置元素内容
    aux.setAttribute("value", content);
    // 将元素插入页面进行调用
    document.body.appendChild(aux);
    // 复制内容
    aux.select();
    // 将内容复制到剪贴板
    document.execCommand("copy");
    // 删除创建元素
    document.body.removeChild(aux);
}