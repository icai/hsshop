$(function(){
	$('#defaultForm').bootstrapValidator({
        message: '这个值是无效的',
        feedbackIcons: {
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
            peopleName: {
                validators: {
                	stringLength: {
                        min: 2,
                        max: 10,
                        message: '姓名为2-10个字符'
                    },
                    notEmpty: {
                        message: '请填写收件人姓名'
                    }
                }
            },
            first_number: {
//              message: '用户名是无效的',
                validators: {
                    stringLength: {
                        min: 3,
                        max: 4,
                        message: '区号为3-4位'
                    },
                    regexp: {
                        regexp: /^0\d{2,3}$/,
                        message: '区号不合法'
                    }
                }
            },
            last_number:{
//              message: '用户名是无效的',
                validators: {
                	notEmpty: {
                        message: '电话号码不可空'
                   	},
                    regexp: {
                        regexp: /(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[358]\d{9})$)/,
                        message: '电话号码不合法'
                    }
                }
            },
            add:{
                validators: {
                    notEmpty: {
                        message: '请填写详细地址'
                    }
                }
            },
            afterSaleNum:{
                validators: {
                	notEmpty: {
                        message: '电话号码不可空'
                   },
                    regexp: {
                        regexp: /^((0\d{2,3}-\d{7,8})|(1[3584]\d{9}))$/,
                        message: '手机号码不合法'
                    }
                }
            }
        }
   });
   
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
})