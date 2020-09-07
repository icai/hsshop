$(function(){
	//点击选择商品；
	$(".add_goods").click(function(){
		layer.open({
			title:"已上架商品|<a href='##'>新建商品</a>-<a href='##'>草稿管理</a>",
			area: ['650px', '520px'],
			skin: 'demo-class',
  			type: 1,
		  	anim: 2,
		  	shade: [0.8, '#4d4d4d'],
		  	content: '<div class="layer_content">'+
        			'<ul class="layer_content_top">'+
        				'<li>标题 <a href="##"> 刷新</a></li>'+
        				'<li>创建时间</li>'+
        				'<li>'+
        					'<div class="col-lg-12">'+
							    '<div class="input-group">'+
							      	'<input type="text" class="form-control">'+
							      	'<span class="input-group-btn">'+
							        	'<button class="btn btn-default" type="button">搜</button>'+
							      	'</span>'+
							    '</div>'+
							'</div>'+
        				'</li>'+
        			'</ul>'+
        			'<ul class="layer_content_main">'+
        				'<li>'+
        					'<img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fq9Xi4vSuS8D804oC_1CD04sb8uA.png?imageView2/2/w/100/h/100/q/75/format/webp"/>'+
        					'<a href="##">电子卡券（购买时无需填写收货地址，测试商品，不发货，不退款）</a>'+
        				'</li>'+
        				'<li>2016-11-11 09:00:01</li>'+
        				'<li><button type="button" class="btn btn-default select">选取</button></li>'+
        			'</ul>'+
        		'</div>'+
        		'<div class="layer_bottom">'+
        			'<p>共<span>1</span>条，每页<span>8</span>条</p>'+
        		'</div>',
		})
	})
	
	//点击选取按钮，显示图片和物品标题
	$(document).on("click", ".layer_content .select", function(){
		$(".goods_img").html("<img src='"+$(this).parents(".layer_content_main").find("img").prop("src")+"'/>");
		$(".goods_name, .add_title").html($(this).parents(".layer_content_main").find("a").text());
		$(".add_goods").html("<img src='"+$(this).parents(".layer_content_main").find("img").prop("src")+"'style='width:100%; height:100%'/>")
		$(".demo-class, .layui-layer-shade").hide();
		$("#goodChoose").val($(".goods_name").text());
		Revalidate("goodChoose");
	})
	$(".add_goods_del").each(function(index, ele){
		$(this).click(function(){
			$(this).parents("add_goods").html("");
			
		})
	})
	
	//团购价输入数字转换成货币；
	moneyNum(".group_price");
	
	//点击单选按钮的事件；
	$("input[type='radio']").click(function(){
		if ($("#cashBack").prop("checked") == false) {
			$("#cashback").hide();
			$(".cashBack_condition").hide();
		}else{
			$("#cashback").show();
			$(".cashBack_condition").show();
		}
	});
	
	
	//日期、时间选择
	$('#datetimepicker, #datetimepicker,#datetimepicker1').datetimepicker({
		format: 'YYYY-MM-DD HH:mm:ss',
		dayViewHeaderFormat: 'YYYY 年 MM 月',
        useCurrent: false,        //必须设置false的；
        showClear:true,
        showClose:true,
        showTodayButton:true,
        locale:'zh-cn',
        focusOnShow: true,
	}); 
	//开始时间小于结束时间；
	$("#datetimepicker").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker').data("DateTimePicker").maxDate(e.date);
    });
	
	//新增奖励条件
	$(".add_reward_conditions").click(function(){
		if ($(".Reward_conditions_show span").length == 0) {
			$(".Reward_conditions_show").append('<span>每售出达<input type="text" name="goodsNum" class="form-control goodsNum"/>件，每件返现'+
									'<input class="form-control has-error add_group_price" name="group_price_1">元<br/><a class="del_group_price" href="##">删除</a>'+
								    '<i class="err_msg hide">请填写正确的返现条件。</i></span>')
		}else{
		$(".Reward_conditions_show").append('<span><b>或&nbsp;&nbsp;</b>每售出达<input type="text" name="goodsNum" class="form-control goodsNum"/>件，每件返现'+
									'<input class="form-control add_group_price" name="group_price_1">元<br/><a class="del_group_price" href="##">删除</a>'+
								    '<i class="err_msg hide">请填写正确的返现条件。</i></span>')
		}
		//件数失焦转整
		$(document).on("blur",".goodsNum", function(){
			$(this).val(parseInt($(this).val()))
			if($(this).val()=="NaN"){
				$(this).val("")
			}
		})
		//返现失焦保留两位；
		moneyNum(".add_group_price");
		//点击删除事件；
		$(document).on("click", ".del_group_price", function(){
			$(this).parents("span").remove();
		})
	})
	
	//团购价失焦回显
	$(".group_price").blur(function(){
		$(".goods_price lft span").text($(".group_price").val());
	});
	
	//表单验证；
	$('#defaultForm').bootstrapValidator({
        message: '这个值是无效的',
        excluded: [':disabled'],
        feedbackIcons: {
//          valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
        	goodChoose:{
        		validators: {
                	notEmpty: {
                        message: '请选择一个要进行团购的商品。'
                 	},
                }
        	},
            begTimg: {
            	trigger: "blur",
                validators: {
                	notEmpty: {
                        message: '请输入开始时间'
                   	},
                }
            },
            closeTime:{
            	trigger: "blur",
                validators: {
                	notEmpty: {
                        message: '请输入结束时间'
                   	},
                }
            },
            group_price:{
                validators: {
                    notEmpty: {
                        message: '请输入团购价'
                    }
                }
            }
        }
  	});
  	//取消重置内容；
  	$('.content_bottom .btn-default').click(function() {
  		$(".goods_img").html("");
  		$(".goods_price span").text("0.00");
		$(".goods_name, .add_title").html("团购商品标题");
		$(".add_goods").html("<span>+</span>");
        $('#defaultForm').data('bootstrapValidator').resetForm(true);
    });
  	
  	//手动重新验证
	function Revalidate(ele){
		$('#defaultForm')
			.data('bootstrapValidator')
	        .updateStatus(ele, 'NOT_VALIDATED', null)
	        .validateField(ele);
	}
	
	//金额数字转换封装；
	function moneyNum(ele){
		$(document).on("blur", ele, function(){
			var _val1 = $(this).val();
			var num = parseFloat(_val1);
			var a = Math.floor(num * 100) / 100;
			var reg = /\./;
			if(!reg.test(a)){  
	            a += ".00"; 
			}  
			if (a == "NaN.00") {
				a ="";
			}
			$(this).val(a);
		})
	}
	
})
