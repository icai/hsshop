$(function(){
	var code1 = '';
	var code2 = '';
	//点击下一步
	$(".phone-next").click(function(ev){
		code1 = $("input[name='code']").val();
		if($("input[name='code']").val()){
			$(".get_codefir").addClass('hidden');
			$(".get_codesec").removeClass('hidden');
			$(this).addClass("hidden");
			$(".phone-up").show();
			$(".remo_col").removeClass("col-f8");
			$("input[name='phone']").removeAttr("disabled").val(""); //input可用	
			$("input[name='code']").val("");			
		}else{
			tool.tip('请填写验证码','warn');
		}
	})
		
	//第一步获取验证码
	$(".get_codefir").on('click',function(){
		if(!(/^1[345789]\d{9}$/.test(mobile))){ 
	        tool.tip("手机号码有误，请重填");  
	        return false; 
	    } 
		$(".phone_codefir").attr('disabled',true);
		$.ajax({
			type:"GET",
			url:"/shop/bindmobile/sendCode",
			data:{
				phone:mobile
			},
			async:true,
			success:function(res){
				if(res.status == 1){
					$(".phone_codefir").addClass("col-ccc").val("60s");
					tool.tip(res.info,'warn');
					var n = 59;
					function succs(){ 
						$(".phone_codefir").val(n+"s"); // 显示倒计时 
						if(n == 0){
							clearInterval(interval)
							$(".phone_codefir").removeAttr('disabled').removeClass("col-ccc").val("获取验证码");
						}
						n--; 
					};
					var interval = setInterval(succs,1000);							
				}else{
					$(".phone_codefir").removeAttr('disabled')
					tool.tip(res.info);
				}
			},
			error:function(){
				$(".phone_codefir").removeAttr('disabled');
				alert('数据访问错误')
			}
		})	
	});
	
	//第二步获取验证码
	$(".get_codesec").on('click',function(){
		if(!(/^1[345789]\d{9}$/.test(mobile))){ 
	        tool.tip("手机号码有误，请重填");  
	        return false; 
	    } 
		$(".phone_codesec").attr('disabled',true);
		$.ajax({
			type:"GET",
			url:"/shop/bindmobile/sendCode",
			data:{
				phone:$(".ver_phone").val()
			},
			async:true,
			success:function(res){
				if(res.status == 1){
					$(".phone_codesec").addClass("col-ccc").val("60s");
					tool.tip(res.info,'warn');
					var n = 59;
					function succs(){ 
						$(".phone_codesec").val(n+"s"); // 显示倒计时 
						if(n == 0){
							clearInterval(interval)
							$(".phone_codesec").removeAttr('disabled').removeClass("col-ccc").val("获取验证码");
						}
						n--; 
					};
					var interval = setInterval(succs,1000);							
				}else{
					$(".phone_codesec").removeAttr('disabled')
					tool.tip(res.info);
				}
			},
			error:function(){
				$(".phone_codesec").removeAttr('disabled');
				alert('数据访问错误')
			}
		})	
	});
	
	//确定修改
	$("body").on('click','.phone-up',function(){
		var num = $(".ver_phone").val();
		if(!(/^1[345789]\d{9}$/.test(num))){ 
	        tool.tip("手机号码有误，请重填");  
	        return false; 
	    } 
		$.ajax({
			type:"POST",
			url:"/shop/bindmobile/changeMobile",
			data:{
				mobile:$(".ver_phone").val(),
				code1:code1,
				code2:$(".ver_code").val()
			},
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			async:true,
			success:function(res){
				if(res.status == 1){
					if(url == ''){
						window.location.href = '/shop/member/index/' + wid
					}else{
					window.location.href=url;
					}
				}else{
					tool.tip(res.info,'warn')
				}
			},
			error:function(){
				alert('数据访问错误')
			}
		});  			    			
	})
	
})