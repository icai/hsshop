$(document).ready(function () {
    $('#register').bootstrapValidator({
        trigger: 'blur',
        message: 'This value is not valid',
        feedbackIcons: {
            // valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            mphone: {
                // message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '手机号码不能为空'
                    },
                    regexp: {
                        regexp: /^\d{11}$/,
                        message: '手机号码格式不对'
                    },
                }
            },
            code: {
                // message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '短信验证码不能为空'
                    },
                    regexp: {
                        regexp: /^\d{4}$/,
                        message: '短信验证码格式不对'
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
            }
        },
        onSuccess: function () {
            $('.change').prop('disabled',true);
            $.post($('form').attr('action'), $('form').serialize(), function (data) {
                if (data.status == 1) {
                    var num = 5;
                    /* 后台验证通过 */
                    if (data.url) {
                        var Timer = setInterval(function(){
                            tipshow('密码修改成功，'+ num +'s后跳转到登录页面','info',5000);
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
                $('.change').prop('disabled',false);
            }, 'json');
            return false;
        }
    });
    $('.change').click(function () {
        $('#register').bootstrapValidator('validate');
    });
    $(document).keypress(function(event){
        if(event.keyCode == "13")$('.change').click();
    });

    // 发送验证码
    //   倒计时
    $(".send").click(function () {
        var _this = $(this);
        var phone = $("input[name='mphone']").val();
        var reg = /^\d{11}$/;
        if(!reg.test(phone)){
            tipshow('请先输入正确手机号码', "warn");
            return false;   
        }
        $.get("/auth/sendcode", { mphone: phone,type: 4}, function (res) {
            var num = 60;
                _this.removeClass('btn-primary');
                _this.addClass('btn-default disabled');
                 _this.text(num+'s后重试');
            var Timer = setInterval(function(){
                num --;
                _this.text(num+'s后重试');
                if(num < 0 ){
                    _this.addClass('btn-primary');
                    _this.removeClass('btn-default disabled');
                    _this.text('重新发送');
                    clearInterval(Timer);
                }
            },1000);
            if (res.status != 1) {
                tipshow(res.info[0], "warn");
                _this.addClass('btn-primary');
                _this.removeClass('btn-default disabled');
                _this.text('重新发送');
                clearInterval(Timer);
            }
            
        })
        return false;
    })

});


