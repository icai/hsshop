$(function(){
	//console.log(wechat_qrcode,"-=-=-")
	if(wechat_qrcode){
		$(".codeImgShow").css("display", "inline-block")
	}
	// 二维码上传
	$('#files, #files_1').on('change', function(){
		var reader = new FileReader();
		reader.readAsDataURL(this.files[0]);
		var codeUrl=this.files[0];
		if(this.files[0].size > 3145728){
			tipshow("图片不能超过3M","warn");
			return;
		}
		reader.onload = function(e){
			var image = new Image();
			var formData = new FormData();
			var this_result=this.result;
			image.src = e.target.result;
                image.onload = function () {
					if(image.width/image.height == 1 && image.width>=116){
						formData.append("file", codeUrl)
							$.ajax({
								url: '/merchants/myfile/upfile',
								type: 'POST',
								cache: false,
								data: formData,
								processData: false,
								contentType: false,
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
								success:function(res) {
									res = JSON.parse(res);
									codeUrl = res.data.FileInfo['path'];
									$("#codeUrl").val(codeUrl)
								},
								error:function(){
								}
							})
							if(!wechat_qrcode){
								$("#files").css({"width":"100px", "height":"100px", "top":"-40px"})
							}
							$("#QcodeChange span").css("display", "none");
							$('.codeImgShow').css('display','inline-block');
							$('.codeImgShow').attr('src',this_result);
					}else{
						tipshow("图片尺寸不符合，请重新上传图片","warn");
						return;
					}
				};
		}
	});
	// 店铺图片上传
	$('#files_store').on('change', function(){
		var reader = new FileReader();
		reader.readAsDataURL(this.files[0]);
		if(this.files[0].size > 3145728){
			tipshow("图片不能超过3M","warn");
			return;
		}
		reader.onload = function(e){
			$('#showStoreImg').attr('src',this.result);
		}
		var formData = new FormData();
		var storeImg = this.files[0];
		formData.append("file", storeImg)
		$.ajax({
			url: '/merchants/myfile/upfile',
			type: 'POST',
			cache: false,
			data: formData,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success:function(res) {
				res = JSON.parse(res);
				storeImg = res.data.FileInfo.path;
				$("#storeImg").val(storeImg)
			},
			error:function(){

			}
		})
	});

	$(".submit").click(function(){
		if(!$("#codeUrl").val()) {
			tipshow("请上传二维码","warn");
			return false;
		}
		if(!$("#name").val() || $("#name").val().length>6) {
			tipshow("名称字数不可空且不超过6个字符","warn");
			return false;
		}
//		console.log($("#name").val().length)
		var _data = $("form").serializeObject();
		_data._token = $('meta[name="csrf-token"]').attr('content');
		$.post("/merchants/currency/commonSetting", _data, function(res){
			if(res.status==1){
				tipshow(res.info,"info");
			}
		})
	})
	$('#uploadImg').on('click', function () {
        getCropper(3, 1,function (blob, img_file) {
            var formData = new FormData();
            formData.append("type", img_file.type)
            formData.append("lastModifiedDate", img_file.lastModifiedDate)
            formData.append("size", img_file.size)
            formData.append("file", blob)
            formData.append("name", img_file.name) // update by 倪凯嘉 上传图片时增加图片名称 2019-09-29
            $.ajax({
                url: '/merchants/myfile/upfile',
                type: 'POST',
                cache: false,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(res) {
                    try {
                        var res = JSON.parse(res);
                        if (res.status === 1) {
                            tipshow("图片上传成功！");
                            codeUrl = res.data.FileInfo['path'];
                            $("#QcodeChange span.QcodeChange-span").css("display", "none");
                            $('.codeImgShow').css('display','inline-block');
                            $('.codeImgShow').attr('src','/'+codeUrl);
                            $("#codeUrl").val(codeUrl)
                        } else {
                            tipshow('图片上传失败，请重新上传图片','warm');
                        }
                    } catch (err) {
                        tipshow('图片上传失败，请重新上传图片','warm');
                    }
                },
                error:function(){

                }
            })
        })
    })
})

$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}
