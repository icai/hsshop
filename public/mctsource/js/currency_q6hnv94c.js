$(function(){
	//判断列表内是否有数据
	$(".addContentShow ul").length<=1 ?$(".addContentShow .title, .page_num").hide():$(".addContentShow .title, .page_num").show();

	//蒙板设置
    $(".board").css({width: $(window).width()+"px",height: $(window).height()+"px",})
    
    //点击按钮显示弹出层；
    $("#addNewAdress").click(function(){
    	$(".board, .layer").removeClass("hide");
    	//关闭弹出层；；
    	$(".layer_top_right").click(function(){
    		$(".board, .layer").addClass("hide");
    	})
    })
    
	
	//点击搜索地址 定位地图；
	$("#addBtn").click(function(){
		if ($("#addTxt").val() == "") {
			$("#addErr").show();
		}else{
			$("#addErr").hide();
			var address = $("#addTxt").val();
			Map("mapShow",address, 16);
		}
	});
	Map("mapShow", "杭州", 12);       //默认地点；
	
	$("#addTxt").focus(function(){
		//手动重新验证
		Revalidate("addTxt");
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
	
	//点击时间段确定按钮的事件；
	var storeTimes = [];
	$("#beSure").click(function(){
		//选择开始、结束时间的判断；
		var beginH = $("select[name='beginHour']").val();
		var beginM = $("select[name='beginMinut']").val();
		var endH = $("select[name='endHour']").val();
		var endM = $("select[name='endMinut']").val();
		if (beginH < endH) {
			pass(beginH, beginM, endH, endM);
		}else if(beginH == endH && beginM < endM){
			pass(beginH, beginM, endH, endM);
		}else{
			tipshow('关门时间要大于开门时间','warn');
		}
		storeTimes = [];
		$(".timeDivShow p").each(function(index,ele){
			var TM = $(this).children("t").text();
				TM += $(this).children("n").text();
			storeTimes.push(TM);
		})
		$("#store_time").val(storeTimes);
		//手动重新验证
		Revalidate("store_time");
	})
	
	//时间段选择验证通过事件
	function pass(bh,bm,eh,em){
		var weekName = "";
		if ($(".week").hasClass("weekSelect") == true) {
//			pass(beginH, beginM, endH, endM);
			$(".weekDiv .weekSelect").each(function(index, ele){
				weekName += $(this).text()+" ";
			})
			if ($(".week").hasClass("weekSelect") == true) {
				var time = "<p>"+
								"<t>"+bh+" 时 "+bm+" 分  ~ "+eh+" 时 "+em+" 分 . </t>"+
								"<n style='display:inline-block'>"+weekName+"</n>"+
								"<a href='##' class='del'>  | 删除</a>"+
							"</p>";
				$(".timeDivShow").prepend(time);
			}
			$(".selectTimeDiv").addClass("hide");
			$(".addTimeDiv").removeClass("hide");
			
			$(".week").each(function(index, ele){
				if ($(this).hasClass("weekSelect")) {
					$(this).removeClass("weekSelect").children(".weekBoard").show();
				}
			})
		}else{
			tipshow('请选择至少一天接待时间','warn');
		}
	}
	//点击删除，删除设置的时间段；
	$(document).on("click", ".del", function(){
		var resetWeekArr = $(this).siblings("n").html().split(" "); 
		var weekArr = $(".week").text().split(" "); 
		//拿到删除的时间段中的星期几；
		for(var i=0; i<resetWeekArr.length; i++){
			if (resetWeekArr[i]=='') {continue};
			var n = $.inArray(resetWeekArr[i], weekArr);
			if (n == -1) {n = null}
			$(".week .weekBoard").eq(n).hide();
		}
		//alert($.inArray(resetWeekArr, weekArr));
		$(this).parent().remove();
		
		var d_time = $(this).siblings("t").text()+$(this).siblings("n").text();
		var d_index = $.inArray(d_time, storeTimes);
		storeTimes.splice(d_index, 1);
		$("#store_time").val(storeTimes);
		//手动重新验证
		Revalidate("store_time");
	});
	
	//接待时间部分的“取消”点击事件
	$(".cancle").click(function(){
		$(".selectTimeDiv").addClass("hide");
		$(".addTimeDiv").removeClass("hide");
	});
	//点击“新增时间段”事件；
	$(".addTimeDiv").click(function(){
		$(".selectTimeDiv").removeClass("hide");
		$(".addTimeDiv").addClass("hide");
	})
	
	//------------图片上传弹出层的切换----------------
	function hideAndShow(hideEle, showEle){
		$(hideEle).addClass("hide");
		$(showEle).removeClass("hide");
	}
	$("a[href='#uploadImg']").click(function(){
		hideAndShow(".modal-content",".modal_content_2");
		reset_styleAnum();
		buttonSure();
	})
	$("a[href='#layer']").click(function(){
		hideAndShow(".modal-content",".modal_content_1");
		reset_styleAnum();
		buttonSure();
	})
	$("a[href='#uploadImgLayer']").click(function(){
		hideAndShow(".modal-content",".modal_content_3");
		reset_styleAnum();
		buttonSure();
	})
	
	//-----------我的图片左侧列表栏切换-----------------
	var $list = $(".modal-body .category-list li");
	$list.each(function(index,ele){
		$(this).click(function(){
			$list.removeClass("active");
			$(this).addClass("active");
			if ($(this).children("span").text()==0) {
				hideAndShow(".attachment-list-region",".Img_add");
			}else{
				$(".Img_add").addClass("hide");
				$(".attachment_"+(index+1)+"").removeClass("hide")
			}
		})
	})
	
	
	//--------------图标库弹出层------------------------
	//风格、颜色、类型选择；
	function clearStyle(eleSect){
		for (var i=0; i<$(eleSect).length; i++) {
			$(eleSect+":eq("+i+")").removeClass("selected");
		}
	}
	$("#style a").click(function(){
		clearStyle("#style a");
		$(this).addClass("selected");
	})
	$("#color a").click(function(){
		clearStyle("#color a");
		$(this).addClass("selected");
	})
	$("#type a").click(function(){
		clearStyle("#type a");
		$(this).addClass("selected");
	})
	
	//选择图片显示边框
	var n = 0;
	$(".image-item").click(function(){
		if ($(this).hasClass("li_select")) {
			$(this).css("border-color", "#ccc").removeClass("li_select").children(".attachment-selected").addClass("hide");
			n--;
		}else{
			$(this).css("border-color", "#0077DD").addClass("li_select").children(".attachment-selected").removeClass("hide");
			n++;
		}
		$(".js-selected-count").text(n);
		buttonSure();
	})
	
	//分页切换显示；
	$('.pagination').jqPaginator({
	    totalPages: 10,
	    visiblePages: 5,
	    currentPage: 1,
	    onPageChange: function (num, type) {
	        $('#show').html('当前第' + num + '页');
	        $("#iconImgSelect").css({"top": -(num-1)*parseInt($("#iconLibraryContent #iconImgShow").css("height"))+"px"});
	    }
	});
	
	//关闭模态框是清除上传的图片
	$('#myModal-adv').on('hidden.bs.modal', function (e) {
	  	closeUploader();       //
	})
	
	//切换、关闭开启弹框时选择的图片样式重置，计数重置；
	function reset_styleAnum(){
		n=0;
		$(".js-selected-count").text(0);
		$(".image-list li").removeClass("li_select").children(".attachment-selected").addClass("hide");
		$(".image-item").css("borderColor", "#ccc")
	}
	
	//判断确定按钮是否可用以及样式
	function buttonSure(){
		var $liSelect = $(".image-list .li_select");
		if ($liSelect.length>0 && $liSelect.length<=4) {
			$(".text-center button").addClass("ui-btn-primary").attr("disabled", false);
		}else{
			$(".text-center button").removeClass("ui-btn-primary").attr("disabled", true);
		}
	}
	
	//点击确定添加图片
	var storeImgs = [];
	$(".text-center button").click(function(){
		var $li_select = $(".image-list .li_select");
		if ($li_select.length>0 && $li_select.length<=4) {
			$li_select.each(function(index, ele){
				var imgSrc = $(this).children("img").attr("src");
				if ($("#selImg").children("span").length < 4) {
					var _html = "<span class='showLittleImg'>"+
									"<img src="+imgSrc+" class='addSeleImg'/>"+
							 		"<i class='imgClose' style='color:white;'>×</i>"+
								 "</span>";
					$(_html).prependTo($("#selImg"));
					var _length = $('#selImg').find('.showLittleImg').length;
					_length>=4 ? $('#addImg').hide() : $('#addImg').show();
				}else{
					return false;
				}
			});
			$('.modal').modal('hide');
		}
		storeImgs = [];
		$("#selImg span").each(function(index,ele){
			storeImgs.push($(this).children("img").attr("src"));
		})
		$("#store_img").val(storeImgs);
		reset_styleAnum();
		buttonSure();
		//手动重新验证
		Revalidate("store_img");
	})
	//点击删除已选择显示的小图片
	$(document).on("click", ".imgClose", function(){
		$('#addImg').show();
		$(this).parent().remove();
		var del_src = $(this).siblings("img").attr("src");
		var del_index = $.inArray(del_src, storeImgs);
		storeImgs.splice(del_index, 1);
		$("#store_img").val(storeImgs);
		//手动重新验证
		Revalidate("store_img");
	})

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
        	adressName:{
        		validators: {
                	notEmpty: {
                        message: '店铺名称不可为空'
                  },
                  stringLength: {
                        min: 1,
                        max: 20,
                        message: '店铺名称最长支持20个字符'
                    },
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
                    stringLength: {
                        min: 8,
                        max: 11,
                        message: '电话号码长度为8-11位'
                    },
                    regexp: {
                        regexp: /(^[1-9][2-4][0-9]{6}$)|(^1[1-9]{10}$)/,
                        message: '电话号码不合法'
                    }
                }
            },
            addTxt:{
                validators: {
                	stringLength: {
                        min: 5,
                        max: 120,
                        message: '最少5个字符,最多120个字符'
                    },
                    notEmpty: {
                        message: '请填写详细地址'
                    }
                }
            },
            store_img:{
            	validators: {
                    notEmpty: {
                        message: '请选择至少一张图片'
                    }
                }
            },
            store_time:{
            	validators: {
                    notEmpty: {
                        message: '接待时间不可为空'
                    }
                }
            }
        }
  	});
  	
  	//手动重新验证
	function Revalidate(ele){
		$('#defaultForm')
			.data('bootstrapValidator')
	        .updateStatus(ele, 'NOT_VALIDATED', null)
	        .validateField(ele);
	}
		
})