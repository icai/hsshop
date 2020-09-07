
$(function(){
    // // 表单验证
    $('#signForm').bootstrapValidator({
        trigger:'blur', 
        submitHandler: function(validator, form, submitButton) {
        },
        fields: {
            rule_name: {
                validators: {
                    notEmpty: {
                        message: '标签名称不能为空！'
                    },
                    stringLength: {
                        min: 6,
                        max: 18,
                        message: '标签名称长度必须在6到30之间'
                    },
                }
            },
            sell_money: {
                validators: {
                    numeric:{
                        message:'必须为数字！'
                    }
                }
            },
            buy_money: {
                validators: {
                    numeric:{
                        message:'必须为数字！'
                    }
                }
            },
            credit_number: {
                validators: {
                    numeric:{
                        message:'必须为数字！'
                    }
                }
            }
        },
        onSuccess:function(){
            url = $('form').attr("action");
            $.ajax({
                type: "POST",
                url: url,
                data: $('#signForm').serialize(),
                dataType: "json",
                success: function (data) {
                    if(data.status=="0"){
                        tipshow(data.info);
                    }else{
                        // layer.msg(data.info,
                        //     {
                        //         icon: 1,
                        //         time: 5000 //5秒关闭（如果不配置，默认是3秒）
                        //     }
                        // );
                        tipshow(data.info,'warn');
                        window.location.href = url;
                    }
                }
            });
        }

    });
    // $('.btn-primary').click(function() {
    //     alert('a')
    //     $('#signForm').bootstrapValidator('validate');
    //     return false;
    // });
})
