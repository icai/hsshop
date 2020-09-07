$(function(){
	//小程序信息
	$.ajax({
		type:"get",
		url:"/merchants/xcx/config/query",
		async:true,
		success:function(res){	
			console.log(res)
			if(res.code == 40000){
				$(".title_res").html(res.list.title);
				$(".app_id").val(res.list.app_id);
			}			
		},
		error:function(){
			console.log("数据访问错误")
		}
	});
	
	//关闭弹窗
	$("body").on('click','.btn-close,.close',function(){
		$(".modal").hide();
	})
	
	//解除绑定显示
	$(".mt_lab").click(function(){
		if($(".mt_che").prop("checked")){
			$(".btn-jiec").removeClass("form_remov").addClass("btn_up").attr("disabled",false);
		}else{
			$(".btn-jiec").addClass("form_remov").removeClass("btn_up").attr("disabled",true);			
		}
	});
	
	//解除绑定按钮点击	
	$("body").on('click','.btn_up',function(){
		$.ajax({
			type:"get",
			url:"/merchants/xcx/cancelAuthorizer",
			data:"",
			async:true,
			success:function(res){
				console.log(res);				
				if(res.errCode == 0){
					tipshow("解除绑定成功")
					window.location.href="/merchants/marketing/liteapp";
				}else{
					tipshow(res.errMsg,"warn")
				}
			},
			error:function(){
				alert("数据访问错误");				
			}
		});
	})
	
	
	//提交审核按钮
	$(".lab_che").click(function(){
		console.log($("#form").serialize())
		if($(".z_check").prop("checked")){
			$(".z_mon").removeClass("form_close").addClass("form_up").attr("disabled",false);
		}else{
			$(".z_mon").addClass("form_close").removeClass("form_up").attr("disabled",true);			
		}
	});
	
	$("body").on('click','.form_up',function(){
		$.ajax({
			type:"post",
			url:"/merchants/xcx/config/processData",
			async:true,
			data:$("#form").serialize(),
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			success:function(res){
				console.log(res)
				if(res.code == 40000){
					tipshow(res.hint);
					window.location.href="/merchants/marketing/liteappInfo"	;				
				}else{
					tipshow(res.hint,'warn')
				}
			},
			error:function(){
				alert("数据访问错误")
			}
		});
	})
})
