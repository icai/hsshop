$(function(){
	//三级联动选择时输入框的对应显示
	$("#location_p, #location_c, #location_a").mouseup(function(){
		adds();
	});
	
	//回车键搜索地图；
	$(document).keydown(function(e){
		if (e.keyCode==13) {
			searchMap();
		}
	})
	//点击按钮搜索地图；
	$("#addBtn").click(function(){
		searchMap();
	});
	
	Map("mapShow", "杭州", 13);       //默认地点；
	
	function searchMap(){
		var address = $("#addTxt").val();
		Map("mapShow",address, 16);
	}
	
	//地点显示的方法；
	function adds(){
		var province = $("#location_p").val();
		var city = $("#location_c").val();
		var country = $("#location_a").val();
		$("#addTxt").val(province + city + country);
	}
	
	//表单验证；
	$('#defaultForm').bootstrapValidator({
        message: '这个值是无效的',
        feedbackIcons: {
//          valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
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
                    stringLength: {
                        min: 7,
                        max: 11,
                        message: '电话号码长度为7-11位'
                    },
                    regexp: {
                        regexp: /(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}/,
                        message: '电话号码不合法'
                    }
                }
            },
            add:{
                validators: {
                	stringLength: {
                        min: 2,
                        message: '最少两个字符'
                    },
                    notEmpty: {
                        message: '请填写详细地址'
                    }
                }
            }
        }
   });
})

