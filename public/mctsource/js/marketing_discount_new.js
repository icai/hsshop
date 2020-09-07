$(function(){
	//点击导航栏切换
	$(".content_top ul li").each(function(index, ele){
		$(this).click(function(){
			$(".content_top ul li").removeClass("active");
			$(this).addClass("active");
			$(".content_bottom").removeClass("B_active");
			$("#bottom_"+(index+1)+"").addClass("B_active");
		})
	})
	
    $('#datetimepicker1, #datetimepicker2').datetimepicker({
    	format: 'YYYY-MM-DD HH:mm:ss',               
	    dayViewHeaderFormat: 'YYYY 年 MM 月DD日',
	    useCurrent: true, 
    	showClear:true,                               
	    showClose:true,                               
	    showTodayButton:true,
	    locale:'zh-cn',
	    allowInputToggle:true, 
	    focusOnShow: true,
        useCurrent: false 				//必须要设置的
    });
	//datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });
    
    $("#datetimepicker3, #datetimepicker4, #datetimepicker5, #datetimepicker6, #datetimepicker7, #datetimepicker8").datetimepicker({
    	format: 'HH:mm',   
    	dayViewHeaderFormat: 'HH时mm分',
    	allowInputToggle:true, 
    });
    $("#datetimepicker3").on("dp.change", function (e) {
        $('#datetimepicker4').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker4").on("dp.change", function (e) {
        $('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
    });
    $("#datetimepicker5").on("dp.change", function (e) {
        $('#datetimepicker6').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker6").on("dp.change", function (e) {
        $('#datetimepicker5').data("DateTimePicker").maxDate(e.date);
    });
    $("#datetimepicker7").on("dp.change", function (e) {
        $('#datetimepicker8').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker8").on("dp.change", function (e) {
        $('#datetimepicker7').data("DateTimePicker").maxDate(e.date);
    });
    
	
	//选择按周期重复
	$("#cycleR").click(function(){
		$("#cycleR").prop("checked") == true ? $(".cycleRepet").removeClass("hide"):$(".cycleRepet").addClass("hide");
	});
    
    //只有某个选中时其子元素可用
    $(".month").find("input[type='text'], select").prop("disabled", true);
    $(".week").find("input[type='text']").prop("disabled", true);
    $(".week").find(".weekBoard").css("display", "block");
    $(".cycleRepet .Gp input[type='radio']").each(function(index, ele){
    	$(this).click(function(){
	    	$(".cycleRepet .Gp").find("input[type='text'], select").prop("disabled", true);
	    	$(".cycleRepet .Gp").find(".weekBoard").css("display", "block");
    		if ($(this).prop("checked")) {
    			$(this).parents(".Gp").find("input[type='text'], select").prop("disabled", false);
    			$(this).parents(".Gp").find(".weekBoard").css("display", "none");
    		}
    	})
    })
	
	//点击星期div的样式显示：
	$(".week").each(function(index, ele){
		$(this).click(function(){
			$(this).hasClass("weekSelect") ? $(this).removeClass("weekSelect") : $(this).addClass("weekSelect");
		})
	})
	//星期蒙板点击事件：
	$(".weekBoard").click(function(event){
		event.stopPropagation();    //  阻止事件冒泡
	})
	
	//查看示例；
	$(".hoverShowImg").each(function(index, ele){
		$(this).mouseenter(function(){
			$(".hoverImg").addClass("hide");
			$(this).children("img").removeClass("hide")
		});
		$(this).mouseout(function(){
			$(".hoverImg").addClass("hide");
		})
	})
	
	
	//选择活动商品
	$(".selectGoods_top ul li").each(function(index, ele){
		$(this).click(function(){
			$(".selectGoods_top ul li").removeClass("li_active");
			$(this).addClass("li_active");
			$(".change_show").addClass("hide");
			$(".change_show_"+(index+1)+"").removeClass("hide");
		})
	})
	
	//参加折扣商品计数
	var discountNum = $(".selectGoods_top ul li n").text();
	//参加折扣按钮
	var index = 0;
	$(document).on("click", ".ulMsgContent li .btn-primary", function(){
		index ++;
		discountNum ++;
		$(this).removeClass("btn-primary").addClass("btn-warning").text("取消折扣");
		var pr = $(this).parents(".ulMsgContent").find("pr").text();
		var html = '<ul class="flex_star addDiv" data="'+$(this).parents(".ulMsgContent").attr('data')+'">'+
	  					'<li>'+
	  						'<input type="checkbox" name="" id="" class="choose" value="" />'+
	  						'<div class="goods_img"><img src="'+$(this).parents(".ulMsgContent").find("img").prop("src")+'"/></div>'+
	  						'<div class="titleGp">'+
	  							'<a href="##" title="'+$(this).parents(".ulMsgContent").find("a").text()+'">'+$(this).parents(".ulMsgContent").find("a").text()+'</a>'+
		  						'<p class="price">￥<pr>'+pr+'</pr></p>'+
		  						'<p class="hint">库存'+$(this).parents(".ulMsgContent").find(".inventory").text()+'</p>'+
	  						'</div>'+
	  					'</li>'+
	  					'<li>'+
	  						'<div class="form-group">'+
							    '<div class="input-group">'+
							      	'<div class="input-group-addon">打折</div>'+
							      	'<input class="form-control discount" type="text" value="10.0">'+
							      	'<div class="input-group-addon">折</div>'+
							   '</div>'+
							'</div>'+
	  					'</li>'+
	  					'<li>'+
	  						'<div class="form-group">'+
							    '<div class="input-group">'+
							      	'<div class="input-group-addon">减价</div>'+
							      	'<input class="form-control reduce" type="text" value="0.00">'+
							      	'<div class="input-group-addon">元</div>'+
							    '</div>'+
							'</div>'+
	  					'</li>'+
	  					'<li>'+
	  						'<div class="form-group">'+
							    '<div class="input-group">'+
							      	'<div class="input-group-addon">打折后</div>'+
							      	'<input class="form-control result" name="after_discount[]" type="text" value="'+pr+'" disabled>'+
							      	'<div class="input-group-addon">元</div>'+
							    '</div>'+
							'</div>'+
	  					'</li>'+
	  					'<li><button type="button" class="btn btn-primary">取消</button></li>'+
	  				'</ul>';
	  	$(".bulk_content").append(html);
	  	$(".selectGoods_top ul li n, .bulk_bottom_right p tn").text(discountNum);
	})
	//点击第一部页面中的 取消折扣 按钮；
	$(document).on("click", ".ulMsgContent li .btn-warning", function(){
		discountNum --;
		$(this).removeClass("btn-warning").addClass("btn-primary").text("参加折扣");
		var data_1 = $(this).parents(".ulMsgContent").attr("data");
		$(".bulk_content ul[data='"+data_1+"']").remove();
		$(".selectGoods_top ul li n, .bulk_bottom_right p tn").text(discountNum);
	});
	//点击第二部页面中的 取消 按钮；
	$(document).on("click", ".addDiv li .btn-primary", function(){
		discountNum --;
		var data_1 = $(this).parents(".addDiv").attr("data");
		$(".ulMsgContent[data='"+data_1+"'] li .btn-warning").removeClass("btn-warning").addClass("btn-primary").text("参加折扣");
		$(this).parents(".addDiv").remove();
		$(".selectGoods_top ul li n, .bulk_bottom_right p tn").text(discountNum);
	});
	
	//全选复选框
	$("#allChose").click(function(){
		$("#allChose").prop("checked")?$(".msgContent").find(".choose").prop("checked", true):$(".msgContent").find(".choose").prop("checked", false);
	})
	$("#allCancle").click(function(){
		$("#allCancle").prop("checked")?$(".bulk_content").find(".choose").prop("checked", true):$(".bulk_content").find(".choose").prop("checked", false);
	})
	
	//批量参加
	$("#allJoin").click(function(){
		if($(".msgContent ul input:checked").length >= 1){
//			$(".msgContent ul input:checked").parents(".ulMsgContent").find("button").removeClass("btn-primary").addClass("btn-warning").text("取消折扣");
			for (var j=0; j<$(".msgContent ul input:checked").length; j++) {
				for (var i = 0; i<$(".bulk_content ul").length; i ++) {
					if($(".msgContent ul input:checked").eq(j).parents('ul').attr('data') == $(".bulk_content ul").eq(i).attr('data')){
						return false;
					}
				}
				if(i == $(".bulk_content ul").length){
					$(".msgContent ul input:checked").each(function(ind, ele){
						discountNum ++;
						$(this).parents(".ulMsgContent").find("button").removeClass("btn-primary").addClass("btn-warning").text("取消折扣");
						var pr = $(this).parents(".ulMsgContent").find("pr").text();
						var html = '<ul class="flex_star addDiv" data="'+$(this).parents(".ulMsgContent").attr('data')+'">'+
		  					'<li>'+
		  						'<input type="checkbox" name="" id="" class="choose" value="" />'+
		  						'<div class="goods_img"><img src="'+$(this).parents(".ulMsgContent").find("img").prop("src")+'"/></div>'+
		  						'<div class="titleGp">'+
		  							'<a href="##" title="'+$(this).parents(".ulMsgContent").find("a").text()+'">'+$(this).parents(".ulMsgContent").find("a").text()+'</a>'+
			  						'<p class="price">￥<pr>'+pr+'</pr></p>'+
			  						'<p class="hint">库存'+$(this).parents(".ulMsgContent").find(".inventory").text()+'</p>'+
		  						'</div>'+
		  					'</li>'+
		  					'<li>'+
		  						'<div class="form-group">'+
								    '<div class="input-group">'+
								      	'<div class="input-group-addon">打折</div>'+
								      	'<input class="form-control discount" type="text" value="10.0">'+
								      	'<div class="input-group-addon">折</div>'+
								   '</div>'+
								'</div>'+
		  					'</li>'+
		  					'<li>'+
		  						'<div class="form-group">'+
								    '<div class="input-group">'+
								      	'<div class="input-group-addon">减价</div>'+
								      	'<input class="form-control reduce" type="text" value="0.00">'+
								      	'<div class="input-group-addon">元</div>'+
								    '</div>'+
								'</div>'+
		  					'</li>'+
		  					'<li>'+
		  						'<div class="form-group">'+
								    '<div class="input-group">'+
								      	'<div class="input-group-addon">打折后</div>'+
								      	'<input class="form-control result" name="after_discount[]" type="text" value="'+pr+'" disabled>'+
								      	'<div class="input-group-addon">元</div>'+
								    '</div>'+
								'</div>'+
		  					'</li>'+
		  					'<li><button type="button" class="btn btn-primary">取消</button></li>'+
		  				'</ul>';
						$(".bulk_content").append(html);
				  		$(".selectGoods_top ul li n, .bulk_bottom_right p tn").text(discountNum);
					})
				}
			}
		}
	})
	
	//批量取消
	var removeArr = [];
	$("#allRemove").click(function(){
		var removeNum = $(".bulk_content ul input:checked").length;
		for (var i=0; i<$(".bulk_content ul input:checked").length; i++) {
			removeArr.push($(".bulk_content ul input:checked").eq(i).parents("ul").attr("data"));
		}
		
		for (var j=0; j<removeArr.length; j++) {
			for (var h=0; h<$(".msgContent ul").length; h++) {
				if ($(".msgContent ul").eq(h).attr("data") == removeArr[j]) {
					$(".msgContent ul:eq("+h+") li .btn-warning").removeClass("btn-warning").addClass("btn-primary").text("参加折扣");
				}
			}
		}
		$(".bulk_content ul input:checked").parents("ul").remove();
		discountNum = discountNum - removeNum;
		$(".selectGoods_top ul li n, .bulk_bottom_right p tn").text(discountNum);
	})
	
	//第二部中的打折  减价显示判断
	$(document).on("click", function(){
		if ($(".bulk_content ul").length>0) {
			$(".change_show_2 .bulk_title").removeClass("hide");
			$(".change_show_2 #emptyHint").addClass("hide");
		}else{
			$(".change_show_2 .bulk_title").addClass("hide");
			$(".change_show_2 #emptyHint").removeClass("hide");
		}
	})
	
	//第二部中的批量打折，批量减价的功能
	function Action(ActEle, hideEle, groupEle, sureBtn, parentEle, showInp, inp, ulEle, releEle, cancleBtn){
		$(ActEle).click(function(){
			$(hideEle).prop("disabled",true);
			$(this).addClass("hide");
			$(groupEle).removeClass("hide");
			
			$(sureBtn).click(function(){
				$(hideEle).prop("disabled",false);
				$(this).parent().addClass("hide");
				$(this).parents(parentEle).children("button").removeClass("hide");
				$(showInp).val(parseInt($(inp).val()*100)/100);
				if (ActEle==".bulk_D") {
					for (var i=0; i<$(ulEle).length; i++) {
						var originalP = $(ulEle).eq(i).find("pr").text();
						var price = originalP*$(inp).val()/10;
						price = parseInt(price*100)/100;
						$(".result").eq(i).val(price);
						$(releEle).eq(i).val(parseInt((originalP-price)*100)/100)
					}
				}else{
					for (var i=0; i<$(ulEle).length; i++) {
						var originalP = $(ulEle).eq(i).find("pr").text();
						var price = originalP-$(inp).val();
						$(".result").eq(i).val(parseInt(price*100)/100);
						$(releEle).eq(i).val(parseInt(price*10/originalP*100)/100)
					}
				}
				//减价金额判断；
				judgeP(".result", ".content_bottom .btn-primary");
			})
			$(cancleBtn).click(function(){
				$(hideEle).prop("disabled",false);
				$(this).parent().addClass("hide");
				$(this).parents(parentEle).children("button").removeClass("hide");
			})
		})
	}
	
	//减价之后金额小于0的样式判断；
	function judgeP(judgeEle, btnEle){
		$(judgeEle).each(function(index, ele){
			$(this).val()<=0?$(this).parents(".form-group").addClass("has-error"):$(this).parents(".form-group").removeClass("has-error");
			if($(this).val()<=0){
				$(btnEle).attr("disabled", "disabled");
				return false;
			} else{
				$(btnEle).removeAttr("disabled");
			}
		})
	}
	Action(".bulk_D", ".bulk_S", ".bulk_D_inpGroup", ".bulk_D_sureBtn", ".bulk_discount", ".discount", "#bulk_D_inp", ".bulk_content ul", ".reduce", ".bulk_D_cancleBtn");
	Action(".bulk_S", ".bulk_D", ".bulk_S_inpGroup", ".bulk_S_sureBtn", ".bulk_sale", ".reduce", "#bulk_S_inp", ".bulk_content ul", ".discount", ".bulk_S_cancleBtn");
	
	
	//单个输入的时候同时改变金额和折扣
	$(document).on("blur", ".discount", function(){
		var originalP = $(this).parents("ul").find("pr").text();
		var discount = Math.round($(this).val()*100)/100;
		var reduce = Math.round(originalP * (1-discount/10)*100)/100;
		var result = Math.round((originalP - reduce)*100)/100;
		$(this).val(discount);
		$(this).parents("ul").find(".reduce").val(reduce);
		$(this).parents("ul").find(".result").val(result);
	})
	$(document).on("blur", ".reduce", function(){
		var originalP = $(this).parents("ul").find("pr").text();
		var reduce = Math.round($(this).val()*100)/100;
		var discount = Math.round(((1 - reduce/originalP)*10)*100)/100;
		var result = Math.round((originalP - reduce)*100)/100;
		$(this).val(reduce);
		$(this).parents("ul").find(".discount").val(discount);
		$(this).parents("ul").find(".result").val(result);
		//单个改变 减价金额 触发的判断
		judgeP(".result", ".content_bottom .btn-primary");
	})
	
	//explain显示
//	$(".explain_2").popover({
//		container:".explain_1",
//		trigger: "hover",
//		placement: "bottom",
//		content: "可设置该活动需要用户授权登录某些平台才能参加抽奖（目前仅支持微信登录，且至少需要选择一个平台授权登录），兑奖联系方式则是在用户中奖之后需要填写的信息（目前支持填写手机号码，也可以不填）；可限制每个用户参与抽奖活动的次数和中奖次数，设置的次数单位可选择次/每天、次/活动全程。"
//	});
	//popover弹框；
	$("[data-toggle='popover']").popover();
	
	//表单验证
	$('#defaultForm').bootstrapValidator({
        message: '填写的值不合法',
        excluded: [':disabled'],
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
//          invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            activeName: {
                validators: {
                    notEmpty: {
                        message: '活动名称必须在 1-20 个字内'
                    }
                }
            },
            Btime: {
            	trigger:'blur',
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            Ctime: {
            	trigger:'blur',
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            'after_discount[]': {
                validators: {
                    greaterThan: {
			            value: 0.01,
			            //notInclusive: '请输入大于 %s 的值'
			        },
                }
            }
        }
  });
  	$('#defaultForm').bootstrapValidator('addField', 'after_discount[]');
  	
   //手动重新验证
	function Revalidate(ele){
		$('#defaultForm')
			.data('bootstrapValidator')
	        .updateStatus(ele, 'NOT_VALIDATED', null)
	        .validateField(ele);
	}
	


    $("button[type='reset']").click(function() {
        $('#defaultForm').data('bootstrapValidator').resetForm(true);
    });
	
})