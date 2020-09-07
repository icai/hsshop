$(function(){
	laydate({
	 	elem: '#pay_date', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
	 	format: 'YYYY/MM/DD hh:mm:ss',
	 	event: 'focus',
	 	istoday: true,
	});

	/* 发送验证码 */
	$('.validate_mobile').click(function(){
		var _val = $('.mobile_num').val();
		var reg= /^1(3|4|5|7|8)\d{9}$/;
		if( reg.test(_val) ){
	
            tipshow('发送成功！');
		}else{
            tipshow('发送失败！');
		
		}
	});

	// 表单验证
	$('.form').bootstrapValidator({
		message: '不能为空',                    // 设置默认提示语
        trigger:'blur',  						// 设置验证默认触发事件(失焦时验证)                                       
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            num: {
                validators: {
                    notEmpty: {
                        message: '购买数量不能为空'
                    },
                    regexp: {
                        regexp: /^(100$)|^([1-9]\d?)$/,
                        message: '1-100之间的正整数'
                    },
                }
            },
            payDate: {
                validators: {
                    date: {
                        format: 'YYYY/MM/DD hh:mm:ss',
                        message: '注意时间格式'
                    }
                }
            },
            mobile:{
            	validators: {
            		notEmpty: {
                        message: '手机号不能为空'
                    },
                    regexp: {
                        regexp: /^1(3|4|5|7|8)\d{9}$/,
                        message: '手机号不正确'
                    },
                }
            },
            validateCode:{
            	validators:{
            		notEmpty: {
                        message: '验证码不能为空'
                    },
                    regexp: {
                        regexp: /^\d{6}$/,
                        message: '验证码为6个数字'
                    },	
           		}
            }   	
        }
	});
})