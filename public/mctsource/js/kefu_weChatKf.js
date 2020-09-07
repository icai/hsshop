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
	
	//微信错误代码
	function weChart(error){
		switch(error){
			case 65400:
				tipshow('暂未开通客服功能，请先到公众平台开通客服功能','warm','5000')			
			break;
			case 65401:
				tipshow('无效客服帐号','warm')			
			break;
			case 65403:
				tipshow('客服昵称不合法','warm')			
			break;			
			case 65404:
				tipshow('客服帐号不合法','warm')			
			break;
			case 65405:
				tipshow('帐号数目已达到上限，不能继续添加','warm')			
			break;			
			case 65406:
				tipshow('已经存在的客服帐号','warm')			
			break;
			case 65407:
				tipshow('邀请对象已经是该公众号客服','warm')			
			break;
			case 65408:
				tipshow('本公众号已经有一个邀请给该微信','warm')			
			break;
			case 65409:
				tipshow('无效的微信号','warm')			
			break;
			case 65410:
				tipshow('邀请对象绑定公众号客服数达到上限','warm')			
			break;
			case 65411:
				tipshow('该帐号已经有一个等待确认的邀请，不能重复邀请','warm')			
			break;
			case 65412:
				tipshow('该帐号已经绑定微信号，不能进行邀请','warm')			
			break;
			case 40005:
				tipshow('不支持的媒体类型','warm')			
			break;
			case 40009:
				tipshow('媒体文件长度不合法','warm')			
			break;
			default:
				tipshow('异常(请确保该店铺已绑定了微信服务号且已开通过了客服功能)','warm','5000')	
		}
	}


    var tr = '<tr><td colspan="6" class="z_none">正在加载数据</td></tr>'
    $(".add_kefu").append(tr);
    var ajax = $.ajax({
		type:"get",
		url:"/merchants/WeChatCustom/list",
		async:true,
		success:function(res){
			//页数信息
			var pageInfo =res.data[0];
			//每次加载之前先清空
			$("#pageInfo span").html("");
			if(res.status == 1){
                $(".add_kefu").html('');
				if(res.data.length == 0){
					var tr = '<tr><td colspan="6" class="z_none">暂无数据</td></tr>'
					$(".add_kefu").append(tr);					
				}else{
					for(var i = 0;i<res.data.length;i++){
						var tr = '<tr class="remover_del set"><td>'+res.data[i].kf_nick+'</td>';
						tr += '<td><img class="wid15" src="'+res.data[i].kf_headimgurl+'" /></td>';
						if (res.data[i].kf_wx == undefined){
							tr += '<td>未绑定</td>';
						}else{
							tr += '<td>'+res.data[i].kf_wx+'</td>';						
						}
						if(res.data[i].invite_status){
							switch (res.data[i].invite_status){
								case 'waiting':
									tr += '<td>等待确认</td>';
									break;
								case 'rejected':
									tr += '<td>被拒绝</td>';
								break;
								case 'expired':
									tr += '<td>过期</td>';
								break;
							}
						}else if(res.data[i].kf_wx && res.data[i].invite_status == undefined){
							tr += '<td>已绑定</td>';
						}else{
							tr += '<td>未绑定</td>';
						}
						tr += '<td class="location-action" style="min-width: 120px;">';
						if ((res.data[i].kf_wx == undefined && res.data[i].invite_status == undefined) || res.data[i].invite_status == 'rejected' || res.data[i].invite_status == 'expired'){
							tr += '<a class="invite_id" href="javascript:;" data-id="'+res.data[i].kf_account+'" data-toggle="modal" data-target="#exampleModal_w">邀请</a>';
						}
						tr += '<a class="a-shanchu" href="javascript:;" data-id="'+res.data[i].kf_account+'">删除</a>';		
						// tr += '<a class="update_id" href="javascript:;" data-id="'+res.data[i].kf_account+'" data-toggle="modal" data-target="#exampleModal_x">修改</a>';
						tr += '<a class="image_id" href="javascript:;" data-id="'+res.data[i].kf_account+'" data-toggle="modal" data-target="#exampleModal_img">头像设置</a>';	
						$('.add_kefu').append(tr)
					}	
//					$("#pageInfo span").prepend('总条数：'+pageInfo.total+' &nbsp;&nbsp; 当前页码'+pageInfo.current_page+'/'+pageInfo.last_page);
					//赋值总页数和当前页
					totalPage = pageInfo.last_page;
				}
			}else{
				$(".add_kefu").html('');	
				var tr = '<tr><td colspan="6" class="z_none">暂无数据</td></tr>'
				$(".add_kefu").append(tr);	
			}
		},
		error:function(){
			// alert('数据访问错误')
		}
	});
	
	
	// 删除列表
    $('body').on('click','.a-shanchu',function(e){
        e.stopPropagation();
        var _this = this;
		var id=$(this).data('id');
        showDelProver($(_this),function(){
			$.ajax({
				type:"POST",
				url:"/merchants/WeChatCustom/delete",
				data:{kf_account:id},
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				success: function(res){
					if(res.status == 1){						
						tipshow(res.info);
						setTimeout(function(){
							location.reload() 
						},1000);
					}else{
						weChart(res.data.errcode)
					}
				},
				error:function(){
					// alert("数据访问错误");
				}
			});	
        })
   });		
	
	//添加
	$("body").on('click','.qq_up',function(){
		$.ajax({
			type:"POST",
			url:"/merchants/WeChatCustom/add",
			data:$('#wx_form').serialize(),
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
					if(res.info){
						tipshow(res.info,'warm');
					}else{
						weChart(res.data.errcode);
					}
				}
			},
			error:function(){
				// alert("数据访问错误")
			}
		});
	})		
	//绑定微信
	var that_id = '';
	$("body").on('click','.invite_id',function(){	
		that_id = $(this).data('id');
	})
	$("body").on('click','.kfwx_up',function(){	
		$.ajax({
			type:"POST",
			url:"/merchants/WeChatCustom/invite",
			data:{
				invite_wx:$('input[name="invite_wx"]').val(),
				kf_account:that_id
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
					if(res.info){
						tipshow(res.info,'warm');
					}else{
						weChart(res.data.errcode);
					}					
				}
			},
			error:function(){
				// alert("数据访问错误")
			}
		});
	});
	//昵称修改
	var update_id ='';
	$("body").on('click','.update_id',function(){	
		update_id = $(this).data('id');
	})
	$("body").on('click','.xiugai_up',function(){	
		$.ajax({
			type:"POST",
			url:"/merchants/WeChatCustom/update",
			data:{
				kf_nick:$('input[name="kf_nick_up"]').val(),
				kf_account:update_id
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
					weChart(res.data.errcode)
				}
			},
			error:function(){
				// alert("数据访问错误")
			}
		});
	});
	
	//上传图片
	var logo=$('#logo').val();
	$('#files').on('change', function(){
		var reader = new FileReader();//获取base64
		reader.readAsDataURL(this.files[0]); 
		reader.onload = function(e){ 
			$('.logo').attr('src',this.result);
		}
		$.ajax({
		    url: '/auth/myfile/upfile',
		    type: 'POST',
		    cache: false,
		    data: new FormData($('#uploadForm')[0]),
		    processData: false,// 告诉jQuery不要去处理发送的数据
		    contentType: false,// 告诉jQuery不要去设置Content-Type请求头
		    headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
		    success:function(res) {
		    	var res = JSON.parse(res);
		    	 console.log(res)
		    	if(res.status == 1){
		    		logo= res.data['path'];
		    		$('.img_src').removeClass('hidden');
		    	}
			},
			error:function(){
	
			}
		})
	});
	var image_id ='';
	$("body").on('click','.image_id',function(){	
		image_id = $(this).data('id');
	})
	$("body").on('click','.img_up',function(){
        if(!logo){
            tipshow('请选择图片','warm');
            return false;
        }
		$.ajax({
			type:"POST",
			url:"/merchants/WeChatCustom/uploadHeadImg",
			data:{
				fileName:logo,
				kf_account:image_id
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
					weChart(res.data.errcode)
				}
			},
			error:function(){
				// alert("数据访问错误")
			}
		});
	});
})