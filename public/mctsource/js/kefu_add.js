$(function(){		
	//全局定义总页数和当前页数
	var totalPage = 0, nowPage = 1;
	//进入页面获取数据
//	getMemberInfo('', '', '' ,1)
	//点击筛选, 点击首页
	$("#search, .firstPage").click(function(){
		nowPage = 1;
		pageSeach(nowPage);
	});
	$(document).keydown(function (e) {
	    if (e.keyCode==13) {
	    	nowPage = 1;
			pageSeach(nowPage);
	    }
	});
	//点击尾页
	$(".lastPage").click(function(){
		nowPage = totalPage;
		pageSeach(nowPage);
	})
	//点击上一页
	$(".prevPage").click(function(){
		if(nowPage > 1) {
			nowPage--;
			pageSeach(nowPage);
		}
	})
	//点击下一页
	$(".nextPage").click(function(){
		if(nowPage < totalPage) {
			nowPage++;
			pageSeach(nowPage);
		}
	});
	
	//分页数据
	function pageSeach(page){
   		ajax();
	}
	
	//客服列表
	var ajax = $.ajax({
		type:"get",
		url:"/merchants/currency/KfList",
		data:{type:1,page:nowPage},
		async:true,
		success:function(res){
			//页数信息
			var pageInfo =res.data[0];
			//每次加载之前先清空
			$("#pageInfo span").html("");
			if(res.status == 1){
				if(res.data[0].data.length == 0){
					var tr = '<tr><td colspan="6">暂无数据</td></tr>'
					$(".add_kefu").append(tr);
				}else{
					for(var i = 0;i<res.data[0].data.length;i++){
						var tr = '<tr class="remover_del">';
						tr += '<td>'+res.data[0].data[i].name+'</td>';
						tr += '<td>'+res.data[0].data[i].qq+'</td>';
						tr += '<td>'+res.data[0].data[i].updated_at+'</td>';
						tr += '<td class="location-action" style="min-width: 120px;">';
						tr += '<a class="a-shanchu" href="javascript:;" data-id ="'+res.data[0].data[i].id+'">删除</a>'
						tr += '</td></tr>';
						$(".add_kefu").append(tr);
					};
					$("#pageInfo span").prepend('总条数：'+pageInfo.total+' &nbsp;&nbsp; 当前页码'+pageInfo.current_page+'/'+pageInfo.last_page);
					//赋值总页数和当前页
					totalPage = pageInfo.last_page;
				}
			}
		},
		error:function(){
			alert('数据访问错误')
		}
	});
	// 删除列表
    $('body').on('click','.a-shanchu',function(e){
        e.stopPropagation();
        var _this = this;
		var id=$(this).data('id');
        showDelProver($(_this),function(){
			$.ajax({
				type:"GET",
				url:"/merchants/currency/kefuDel/"+id,
				dataType:'json',
				success: function(res){
					if(res.status == 1){						
						tipshow(res.info);
						setTimeout(function(){
							location.reload() 
						},1000);
					}else{
						tipshow(res.info,'warn');
					}
				},
				error:function(){
					alert("数据访问错误");
				}
			});	
        })
   });		
	
	//添加
	$("body").on('click','.qq_up',function(){
		$.ajax({
			type:"POST",
			url:"/merchants/currency/kefu",
			data:{
				name:$("input[name='name']").val(),
				qq:$("input[name='qq']").val()
			},
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			async:true,
			success:function(res){
				if(res.status == 1){
					tipshow(res.info);
					setTimeout(function(){
						location.reload() 
					},1000);
				}else{
					tipshow(res.info,'warn');
				}
			},
			error:function(){
				alert("数据访问错误")
			}
		});
	})				
})