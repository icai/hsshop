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

                    stringLength: {
                        min: 4,
                        max: 4,
                        message: '短信验证码格式不对'

                    },
                }
            },
            name: {
                // message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: '个人昵称不能为空'
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
                        max: 20,
                        message: '密码的长度在8~20个字符'
                    },
                    identical: {
                        field: 'psdTwo',
                        message: ' '
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
                    tipshow(data.info);
                    /* 后台验证通过 */
                    if (data.url) {
                        /* 后台返回跳转地址则跳转页面 */
                        window.location.href = data.url;
                    } else {
                        /* 后台没有返回跳转地址 */
                        // to do somethings
                    }
                } else {
                    tipshow(data.info,"warn");
                    /* 后台验证不通过 */
                    $('input[type="submit"]').prop('disabled', false);
                    // to do somethings
                }
            }, 'json');
            $('.btn-primary').prop('disabled',false);
            return false;
        }
    });
    $('.btn-primary').click(function () {
        if($("input[name='hide']").val() !== "1"){
            tipshow($(".register_info .info").text(),"warn");
            return false;
        }
        $('#register').bootstrapValidator('validate');
        return false;
    });
    $(document).keypress(function(event){
        if(event.keyCode == "13")$('.btn-primary').click();
    });
    //设置密码触发确认密码
    $("input[name='password']").blur(function(){
        $("input[name='password_confirmation']").focus().blur();
    });

    // 发送验证码
    //   倒计时
    $(".btn-default").click(function () {
        if($("input[name='hide']").val() !== "1"){
            tipshow($(".register_info .info").text(),"warn");
            return false;
        }
        var r = /^\d{11}$/;　　//正整数
        var phone = $("input[name='mphone']").val();
        if(!r.test(phone)){
            tipshow('请输入正确手机号', "warn");
            return false;
        }
        // var code = $("input[name='captcha']").val();
        countDown($(this));
        $(this).attr("disabled",true)
        $.get("/auth/sendcode", { mphone: phone }, function (res) {
             
            if (res.status != 1) {
                tipshow(res.info, "warn");
                $(this).removeClass('disabled');
            }
           
            
        })

    })
    var t;
    function countDown(that) {
        var time = 60;
        var _this = that;
        t = setInterval(function () {
            time--;
            var html = time + "s后重试";
            $(".btn-default").html(html);
            if (time == 0) {
                clearInterval(t);
                $(".btn-default").html("获取验证码")
                $(".btn-default").attr("disabled",false)
            }
        }, 1000)

    }

});


