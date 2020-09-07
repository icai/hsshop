
//下载二维码控制
$(".download").hover(function(){
	$(".main_right .more").css('display','block');
},function(){
	$(".main_right .more").css('display','none');
});
$(document).ready(function() {
    $('#login').bootstrapValidator({
        trigger:'blur',
        message: 'This value is not valid',
        feedbackIcons: {
            // valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            mphone: {
                message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '手机号码不能为空'
                    },
                    regexp: {
                        regexp: /^1\d{10}$/,
                        message: '手机号码格式不对'
                    },
                }
            },
            password: {
                message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    },
                    regexp: {
                        regexp: /^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]{6,18}$/,
                        message: '密码格式不对'
                    },
                }
            },
            captcha: {
                message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '验证码不能为空'
                    },
                }
            },
        },
        onSuccess: function() {
            $('.btn-primary').prop("disabled",true);
            $.post($('form').attr('action'), $('form').serialize(), function( data ) {
                
                if ( data.status == 1 ) {
                    /* 后台验证通过 */
                    if ( data.url ) {
                        /* 后台返回跳转地址则跳转页面 */
                        window.location.href = data.url;
                    } else {
                        /* 后台没有返回跳转地址 */
                        // to do somethings
                    }
                } else {
                    tipshow(data.info,'warn',2000);
                    reImg();
                    /* 后台验证不通过 */
                    $('input[type="submit"]').prop('disabled', false);
                    // to do somethings
                }
                $('.btn-primary').prop("disabled",false);
                return false;
            }, 'json');
            return false;
        }
    });
});
$('.btn-primary').click(function(){
	$('#login').bootstrapValidator('validate');
});
$(document).keypress(function(event){
    if(event.keyCode == "13")$('.btn-primary').click();
});