$(document).ready(function () {
    $('#form').bootstrapValidator({
        trigger: 'blur keyup',
        message: 'This value is not valid',
        feedbackIcons: {
            // valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            old_password: {
                // message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '旧密码不能为空'
                    },
                    stringLength: {
                        min: 8,
                        max: 20,
                        message: '请输入正确的旧密码'
                    },
                }
            },
            password: {
                // message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    },
                    stringLength: {
                        min: 8,
                        max: 18,
                        message: '密码的长度在8~18个字符'
                    },
                    regexp: {
                        regexp: /^[0-9a-zA-Z]+$/,
                        message: '只支持8-18位英文数字密码'
                    },
                }
            },
            password_confirmation: {
                // message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    },
                    identical: {
                        field: 'password',
                        message: '两次密码不一致'
                    },
                }
            },
        },
        onSuccess: function () {
            $('.btn-primary').prop('disabled',true);
            $.post($('form').attr('action'), $('form').serialize(), function (data) {
                if (data.status == 1) {
                    var num = 5;
                    /* 后台验证通过 */
                    if (data.url) {
                        var Timer = setInterval(function(){
                            tipshow('密码修改成功，'+num+'秒后自动跳转到店铺管理页','info',5000);
                            num --;
                            if(num < 0){
                                /* 后台返回跳转地址则跳转页面 */
                                window.location.href = data.url;
                            }
                        },1000);
                    } else {
                        /* 后台没有返回跳转地址 */
                        // to do somethings
                    }
                } else {
                    tipshow(data.info,'warn');
                    /* 后台验证不通过 */
                    $('input[type="submit"]').prop('disabled', false);
                    // to do somethings
                }
                $('.btn-primary').prop('disabled',false);
            }, 'json');
            return false;
        }
    });
    $("input[name='password']").blur(function(){
        $("input[name='password_confirmation']").focus().blur();
    })
    $('.btn-primary').click(function () {
        $('#form').bootstrapValidator('validate');
    });
    $(document).keypress(function(event){
        if(event.keyCode == "13")$('.btn-primary').click();
    });

    // 返回
    $(".btn-default").click(function () {
        window.history.go(-1);
        return false;
    })
});


