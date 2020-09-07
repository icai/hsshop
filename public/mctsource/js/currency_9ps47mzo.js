$(function(){
	//店铺名称修改；
	$("#nameChange").click(function(){
		
		$('#shop_name').removeClass('disabled').prop('disabled',false).focus();
		$('.save').removeAttr('disabled');
		flag = true;
	});


	//点击解绑的事件；
	$("#phoneRemove").click(function(){
		layer.open({
			area:['400px', '200px'],
			title: ["解绑联系人手机", 'font-size:18px;'],
			skin: 'demo-class',
			type:1,
			closeBtn: 1,
			anim: 2,
			content:'<div id="removeMsgLayer_content">'+
			'<span id="userPhoneNum">'+
			'联系人手机号：<p id="UPNum">12121221212</p><br />'+
			'</span>'+
			'<span id="checkNum">'+
			'<i style="display: inline-block; color: red;">*</i>短信校验码：'+
			'<input type="text" id="phoneCheckNum" class="form-control"/>'+
			'<button type="button" class="btn btn-default" style="margin-top:-3px;">获取</button>'+
			'</span>'+
			'</div>',
			btn:['绑定', '取消'],
			yes:function(){
				alert("绑定绑定")
			}
		})
	});
	//点击获取验证码
	$(document).on("click", "#checkNum button", function(){
		//
	});


	//--------------------------------------------------
	// logo 上传
	var imgUrl;
	options ={
		thumbBox: '.thumbBox',
		spinner: '.spinner',
		imgSrc: imgUrl + 'images/default.png',
		img :  '',
		flag: false
	}
	$('#files').on('change', function(){
		var reader = new FileReader();
		reader.readAsDataURL(this.files[0]);
		if(this.files[0].size > 3145728){
			tipshow("图片不能超过3M","warn");
			return;
		}
		reader.onload = function(e){
			var image = new Image();
			var this_result=this.result;
			image.src = e.target.result;
			image.onload=function(){
				if (image.width/image.height == 1 && image.width>=116) {
					$.ajax({
						url: '/merchants/myfile/upfile',
						type: 'POST',
						cache: false,
						data: new FormData($('#uploadForm')[0]),
						processData: false,
						contentType: false,
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success:function(res) {
							res = JSON.parse(res);
							logo = res.data.FileInfo['path'];
						},
						error:function(){
			
						}
					})
					$('#logoImgDiv img').attr('src',this_result);
				} else {
					tipshow("图片尺寸不符合，请重新上传图片","warn");
							return;
				}
			}
		}
	});
});
var logo = $('.logo').val();
var logo1 ='';
var id = $('#id').val();
var flag = true;
$('.btn-primary').click(function(){
	if(logo == false){
		var logo1 = $('.logo_items img').attr('src')
		var logo1 = logo1.split('/')
		var logo1 = logo1.slice(3).join('/')
	}	
	(logo.length == 0)?logo=logo1 : logo=logo;
//	$(this).addClass('disabled');
	var _this = $(this);
	if(flag){
//		flag = false;
		$(this).attr('disabled','disabled');
		$.post('',{id:id,logo:logo,_token:$('meta[name="csrf-token"]').attr('content'),shop_name: $('#shop_name').val()},function(data){
			if(data.status == 1){
				tipshow(data.info,'info');
				window.location.reload();
			}else{
				flag = true;
				tipshow(data.info,'warn');
			}
			_this.removeAttr('disabled');
		})
	}

});
/**
 * [setPreview 设置预览效果函数]
 */
function setPreview(){
	options.img = cropper.getDataURL();
	var _html ='';
	_html += '<div class="round_img preview_img">';
	_html += '<img src="'+options.img+'" width="64" height="64" />';
	_html += '<p class="size_info">尺寸：64px*64px</p>';
	_html += '<img src="'+options.img+'" width="128" height="128" />';
	_html += '<p class="size_info">尺寸：128px*128px</p>';
	_html += '</div>';
	_html += '<div class="preview_img">';
	_html += '<img src="'+options.img+'" width="64" height="64" />';
	_html += '<p class="size_info">尺寸：64px*64px</p>';
	_html += '<img src="'+options.img+'" width="128" height="128" />';
	_html += '<p class="size_info">尺寸：128px*128px</p>';
	_html += '</div>';
	$('.cropped').empty().append( _html );					// 清空并存入预览图
}

//添加地址；
var county = "<option value=''>选择地区</option>";
/*省市区三级联动*/
$('.js-province').change(function(){
	var dataId = $('.js-province option:selected').val();
	var province = json[dataId];
	var city = "<option value=''>选择城市</option>";
	for(var i = 0;i < province.length;i ++){
		city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
	}
	$('.js-city').html(city);
	$('.js-county').html(county);
});
$('.js-city').change(function(){
	var dataId = $('.js-city option:selected').val();
	var city = json[dataId];

	for(var i = 0;i < city.length;i ++){
		county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
	}
	$('.js-county').html(county);
});

var type = _type || 0;
var is_default = _default || 0;
var is_send_default = _send_default || 0;
//$(".tuihuo-moren").attr("disabled",true);
function tuihuo(){
	if($(".tuihuo").is(':checked')){
		$(".tuihuo-moren").attr("disabled",false);
		$(".shoupiao,.shoupiao-moren").attr("disabled",true);
		type = 0;
		return type;
	}else{
		$(".tuihuo-moren").attr("disabled",true);
		$(".shoupiao").attr("disabled",false);
		is_default = 0;
		return is_default;
	}
}
//默认退货地址
function tuihuo_moren(){
	if($(".tuihuo-moren").is(':checked')){		//点击退货的时候
		if($(".fahuo-moren").is(':checked')){			//两个全选的情况
			type = 3;
			is_default = 1;
			is_send_default = 1;
		}else{											//只有默认退货的时候
			type = 0;
			is_default = 1;
			is_send_default = 0;
		}
		//type = 0;
		//is_default = 1;
		//return is_default;
	}else{
		if($(".fahuo-moren").is(':checked')){			//只有默认发货的情况
			type = 2;
			is_default = 0;
			is_send_default = 1;
		}else{											//两者都不选的情况
			type = 0;
			is_default = 0;
			is_send_default = 0;
		}
		
	}
}
//默认收货地址
function fahuo_moren(){
	if($(".fahuo-moren").is(':checked')){			//点击发货的时候
		if($(".tuihuo-moren").is(':checked')){			//两者都选的情况
			type = 3;
			is_default = 1;
			is_send_default = 1;
		}else{											//只选默认发货的情况
			type = 2;
			is_default = 0;
			is_send_default = 1;
		}
		
	}else{
		if($(".tuihuo-moren").is(':checked')){			//只选默认退货的情况
			type = 0;
			is_default = 1;
			is_send_default = 0;
		}else{											//两者都没的情况
			type = 0;
			is_default = 0;
			is_send_default = 0;
		}
		
	}
}

function shoupiao(){
	if($(".shoupiao").is(':checked')){
		$(".shoupiao-moren").attr("disabled",false);
		$(".tuihuo,.tuihuo-moren").attr("disabled",true);
		type = 1;
		return type;
	}else{
		$(".shoupiao-moren").attr("disabled",true);
		$(".tuihuo").attr("disabled",false);
		is_default = 0;
		return is_default;
	}
}
function shoupiao_moren(){
	if($(".shoupiao-moren").is(':checked')){
		is_default = 1;
		return is_default;
	}else{
		is_default = 0;
		return is_default;
	}
}
if($(".shoupiao").is(':checked')){
	type = 1;
};
if($(".shoupiao-moren").is(':checked')){
	type = 1;
	is_default = 1;
}
if($(".tuihuo").is(':checked')){
	type = 0;
};
if($(".tuihuo-moren").is(':checked')){
	if ($(".fahuo-moren").is(':checked')) {
		type = 3;
	}else{
		type = 0;
	}

	is_default = 1;
}else{

}





$(".location-add-form .zent-form__form-actions .ui-btn-primary").click(function(){
	var name = $("input[name='contact_name']").val();
	var mobile = $(".zent-phone").val();
	var province_id = $(".address-province option:selected").val();
	var city_id = $(".address-city option:selected").val();
	var area_id = $(".address-county option:selected").val();
	var address = $("input[name='address']").val()
	var zip_code = $("input[name='zip_code']").val()
	var id = $("#address_id").val();
	var data = {
		"name":name,
		"mobile":mobile,
		"province_id":province_id,
		"city_id":city_id,
		"area_id":area_id,
		"address":address,
		"zip_code":zip_code,
		"is_default":is_default,
		"is_send_default":is_send_default,
		"type":type,
		"id":id,
		"_token":$('meta[name="csrf-token"]').attr('content')
	};
	if(province_id && city_id && area_id){
		if(type != 4){
			if($("input[name='contact_name']").val()){
				if ($("input[name='address']").val()) {
					if ($(".zent-phone").val()) {
						$.ajax({
							"type":"POST",
							"url":"/merchants/currency/editAddress",
							"data":data,
							"dataType":'json',
							"success":function(res){
								if (res.status == 1) {
									tipshow(res.info)
									window.location.href = res.url
								}else{
									console.log(res);
								}
							},
							"error":function(res){
								alert("数据访问错误");
							}
						});
					} else{
						tipshow('电话号码不能为空','info');
					}
				} else{
					tipshow('输入详细地址','info');
				}
			}else{
				tipshow('输入联系人','info');
			};
		}else{
			tipshow('选择地址类型','info');
		}
	}else{
		tipshow('请选择地址','info');
	}
});

//删除地址
$(".a-shanchu").click(function(){
	var id = $(this).data('id');
	$.ajax({
		"type":"GET",
		"url":"/merchants/currency/delAddress/"+id,
		"data":'',
		"success":function(res){
			tipshow("删除成功");
			window.location.href = res.url
			//{{redirect('merchants/currency/location')}}
		},
		"error":function(res){
			alert("数据访问错误");
		}
	});
})
