"use strict";
$(function(){ 
	//点击保存事件
	$(".js-submit").click(function(){
		$(this).attr("disabled","disabled");
		var data ={},
			imgid = $("#imgid").val(),
			title = $("#title").val(),
			introduction = $("#introduction").val();

		if(title==""){
			$(this).removeAttr("disabled"); 
			tipshow("请填写社区名称!","warn");
			return;
		}
		if(imgid==""){
			$(this).removeAttr("disabled"); 
			tipshow("请上传社区头像!","warn");
			return;
		}
		data.imgid = imgid;
		data.title = title;
		data.introduction = introduction;
	  	$.ajax({
		    url: '/merchants/microforum/settings/listed',
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
		    			location.reload();
		    		},1000)
		    	}else{
		    		tipshow(res.info,"wran");
		    	}
			},
			error:function(){
				console.log("上传失败");
			}
		});
		$(this).removeAttr("disabled");
	}); 

	//选择上传图片
	$("#head_file").change(function() {
		var that = this;
		console.log(this.files[0],'files')
		if (this.files[0].size > 1024000) {
			tipshow("图片容量超过3M，请重新上传", "warn");
			return;
		}
		var myimg = URL.createObjectURL(this.files[0])
		var img = new Image()
		img.src = myimg
		img.onload = function() {
			console.log(img.width/img.height,'img')
			if (img.width / img.height > 1.2 || img.width / img.height < 0.8){
				tipshow("图片比例非1:1，请重新上传图片", "warn");
			}else{
				var reader = new FileReader();
				reader.readAsDataURL(that.files[0]);
				reader.onload = function (e) {
					console.log(this, 'this')
					var result = this.result;
					var html = '<img src="' + result + '" id="head_img" />';
					$("#head_img_box").html(html);
				}
				var formdata = new FormData();
				formdata.append("file", that.files[0]);
				formdata.append("halt", 1);
				$("#imgid").val('');
				$.ajax({
					url: '/merchants/myfile/upfile',
					type: 'POST',
					cache: false,
					data: formdata,
					processData: false,
					contentType: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (res) {
						res = JSON.parse(res);
						if (res.success == 1) {
							$("#imgid").val(res.data.id);
						}
					},
					error: function () {
						console.log("上传失败");
					}
				}) 
			}
		}
		console.log(myimg,'myimg')
	});
});