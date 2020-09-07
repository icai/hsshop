$(function(){
	var data = {
		type      : 1,			//样式
		manifesto : '这一生我只牵你的手，因为有你就足够！',		//爱情宣言
	}
	
	
	//选择样式
	$(".style_img img").click(function(){
		$(".style_img img").removeClass("active");
		$(this).addClass("active");
		data.type = $(this)["0"].dataset.type;
		judge_show(data.type)
	})
	
	//选择宣言
	$(".radios p").click(function(){
		//切换图片
		$(".radios p").children("img").attr("src", "public/images/coupon_use_normal@2x.png")
		$(this).children("img").attr("src", "public/images/coupon_use_select@2x.png")
		//拿到对应的下标
		let index = $(this)["0"].dataset.index;
		
		if (index!=4) {
			data.manifesto = $(this).children("span")["0"].innerHTML;
		}else{
			data.manifesto = $("#manifestoSelf").val()
		}
	})
	
	//自定义宣言赋值
	$("#manifestoSelf").change(function(){
		data.manifesto = $("#manifestoSelf").val();
	})
	
	//结婚登记日期
	$("#my-input").calendar({
		value: ["2017-10-01"]
	});
	
	//婚礼日期
	$("#datetime-picker").datetimePicker({
		min: "2017-10-01",
		onClose(){
	  		delPickerPadding()
	  	}
	});
	
	//选择酒店
	$("#picker").picker({
	  	title: "请选择酒店",
	  	cols: [
	    	{
	      		textAlign: 'center',
	      		values: ['香格里拉大酒店', '希尔顿大酒店', '皇冠假日酒店', 'JW万豪酒店', '四季酒店']
	    	}
	  	],
	  	onClose(){
	  		delPickerPadding()
	  	}
	});
	
	//提交
	$(".submit button").click(function(){
		var oMyForm = {};
		oMyForm.type      = data.type;
		oMyForm.man       = $("#man").val();
		oMyForm.woman     = $("#woman").val();
		oMyForm.jiehun    = $("#my-input").val();
		oMyForm.manifesto = data.manifesto;
		oMyForm.hunli     = $("#datetime-picker").val();
		oMyForm.hotel     = $("#picker").val();
		
		$.ajax({
			type:"POST",
			url:"/applet/invitation/index",
			data: oMyForm,
			dataType: 'json',
			async:true,
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			success:function(res){
				if(res.status==1){
					window.location.href =  res.url + '?imgSrc=' + res.data.fileName;
				} 	
			},
			error:function(){
				alert("数据访问错误")
			}

		});
	})
	
	//根据样式判断显示内容
	function judge_show(type){
		$('.type_1, .type_2, .type_3').hide()
		if (type==1) {
			$('.type_1').show()
		} else if(type==2){
			$('.type_2').show()
		} else{
			$('.type_3').show()
		}
	}
	//处理content在picker时自动加上的padding
	function delPickerPadding(){
		$('.content').css("padding-bottom", 0)
	}
})