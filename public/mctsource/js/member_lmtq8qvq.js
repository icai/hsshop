$(function(){
	$('input[name="card_title"]').bind('blur',function(){
		$('.card_title').html($(this).val());
	})
	// 会员卡选择背景颜色
	$('#bg_color').click(function(){
		console.log($('input[name="choose_color"]').val());
		$('.card_image').css('background',$('input[name="choose_color"]').val());
	})
	// 改变背景颜色
	$('input[name="choose_color"]').bind('change',function(){
		if($('#bg_color').prop("checked")){
			$('.card_image').css('background-image','');
			$('.card_image').css('background',$('input[name="choose_color"]').val());
		}
	})
	// 选择图片点击
	$('#bg_image').click(function(){
		$('.card_image').css('background','');
		$('.card_image').css('background','url('+$('.bg_image img').attr('src')+')');
	})
	// 表单验证
	// 表单验证
    $('#cardForm').bootstrapValidator({
        trigger:'blur', 
        submitHandler: function(validator, form, submitButton) {
        },
        fields: {
            card_title: {
                validators: {
                    notEmpty: {
                        message: '会员卡名称不能为空！'
                    },
                    stringLength: {
                       min: 6,
                       max: 18,
                       message: '会员开名称长度必须在6到18位之间'
                    }
                }
            },
            card_level: {
                validators: {
                    notEmpty: {
                        message: '等级不能为空！'
                    }
                }
            }
        }
    });
})	
function readImageFile(event){
    var reader = new FileReader(); 
    reader.readAsDataURL(event.target.files[0]); 
    reader.onload = function(e){ 
        $('.bg_image img').attr('src',this.result);
        if($('#bg_image').prop("checked")){
        	$('.card_image').css('background-image','');
        	$('.card_image').css('background-image','url('+this.result+')');
        }
    } 
}