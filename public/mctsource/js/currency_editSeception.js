$(function(){
	if (typeof(storeTimes) == 'string') {
		storeTimes = JSON.parse(storeTimes);
	}
	// if (typeof(zitiTimes) == 'string') {
	// 	zitiTimes = JSON.parse(zitiTimes);
	// }
	
	var county = "<option value=''>选择地区</option>";
	/*省市区三级联动*/
    $('.js-province').change(function(){
		$('.js-province option:selected').attr("selected","selected").siblings().removeAttr('selected');
        var dataId = $('.js-province option:selected').val();
        var province = json[dataId];
        var city = "<option value=''>选择城市</option>";
        for(var i = 0;i < province.length;i ++){
            city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
		}
		// console.log(this,dataId);
        $('.js-city').html(city);
        $('.js-county').html(county);
    });
    $('.js-city').change(function(){
		$('.js-city option:selected').attr("selected","selected").siblings().removeAttr('selected');
        var dataId = $('.js-city option:selected').val();
        var city = json[dataId]; 
        //console.log(json[dataId])
        var county = "<option value=''>选择地区</option>";
        for(var i = 0;i < city.length;i ++){
            county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
        }
        $('.js-county').html(county);
    });

	// var imgs = $("input[name='images']").val();//图片数组
	
	var imgSrcArr = [];
	$('#first_number').blur(function(){
		var reg = /^0\d{2,3}$/;
		if ($('#first_number').val()!='' && !reg.test($('#first_number').val())) {
			tipshow('区号不合法','warn');
		}
	});
	//表单验证；
	$('#defaultForm').bootstrapValidator({
		live: 'enabled',
        message: '这个值是无效的',
        excluded: [':disabled'],
        // trigger: 'blur',
        feedbackIcons: {
//          valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
        	title:{
        		validators: {
                  notEmpty: {
                        message: '自提点名称不可为空'
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
                        message: '长度为7-11位数字'
                    },
                    regexp: {
                        regexp: /(^[0-9]{7,11}$)/,
                        message: '电话号码不合法'
                    }
                }
            },
            address:{
                validators: {
                	stringLength: {
						min: 5,
						max: 120,
                        message: '最少5个字符,最长120字'
                    },
                    notEmpty: {
                        message: '详细地址不能为空！'
                    }
                }
            }
        }
  	});


	// 电话
	var $firstNumber = $('#first_number'),$lastNumber=$('#last_number'),tel='';
	$('#first_number,#last_number').change(function(){
		if ($firstNumber.val() == ''){
			tel = $lastNumber.val();
		} else {
			tel = $firstNumber.val()+'-'+$lastNumber.val();
		}
		$('#telphone').val(tel);
	});

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

	//--------------------- 接待时间段时间处理开始 ----------------------
	//点击接待时间段确定按钮的事件
	$("#beSure").click(function(){
		//选择开始、结束时间的判断；
		var beginH = $("select[name='beginHour']").val();
		var beginM = $("select[name='beginMinut']").val();
		var endH = $("select[name='endHour']").val();
		var endM = $("select[name='endMinut']").val();
		if (beginH < endH) {
			pass1(beginH, beginM, endH, endM);
		}else if(beginH == endH && beginM < endM){
			pass1(beginH, beginM, endH, endM);
		}else{
			tipshow('关门时间要大于开门时间','warn');
		}
		//手动重新验证
		Revalidate("store_time");
	})
	
	//接待时间段选择验证通过事件
	function pass1(bh,bm,eh,em){
		var weekName = "";
		if ($(".week1").hasClass("weekSelect") == true) {
			var arr={};
			arr.startTime= bh+':'+bm;
			arr.endTime= eh+':'+em;
			arr.days= [];
			$(".weekDiv1 .weekSelect").each(function(index, ele){
				weekName += $(this).text()+" ";
				arr.days.push($(this).data('index'));
			})
			if ($(".week1").hasClass("weekSelect") == true) {
				var time =  "<p>" +
								"<t>"+bh+" 时 "+bm+" 分  ~ "+eh+" 时 "+em+" 分 . </t>"+
								"<n style='display:inline-block'>"+weekName+"</n>"+
								"<a href='##' class='del1'>  | 删除</a>"+
							"</p>";
				$(".timeDivShow1").prepend(time);
			}
			$(".selectTimeDiv1").addClass("hide");
			$(".addTimeDiv1").removeClass("hide");
			
			$(".week1").each(function(index, ele){
				if ($(this).hasClass("weekSelect")) {
					$(this).removeClass("weekSelect").children(".weekBoard").removeClass('hide');
				}
			})
			storeTimes.unshift(arr);
			$("#store_time").val(storeTimes);
		}else{
			tipshow('请选择至少一天接待时间','warn');
		}
	}

	//点击删除，删除设置的时间段；
	$(document).on("click", ".del1", function(){
		var resetWeekArr = $(this).siblings("n").html().split(" "); 
		var weekArr = $(".week1").text().split(" ");
		//拿到删除的时间段中的星期几；
		for(var i=0; i<resetWeekArr.length; i++){
			if (resetWeekArr[i]=='') {continue};
			var n = $.inArray(resetWeekArr[i], weekArr);
			if (n == -1) {n = null}
			$(".week1 .weekBoard").eq(n).addClass('hide');
		}
		var d_index = $('.timeDivShow1 p').index($(this).parent());
		$(this).parent().remove();
		// var d_time = $(this).parent().data('index');
		// var d_index = $.inArray(d_time, storeTimes);
		storeTimes.splice(d_index, 1);
		$("#store_time").val(storeTimes);
		//手动重新验证
		Revalidate("store_time");
	});

	//接待时间部分的“取消”点击事件
	$(".cancle1").click(function(){
		$(".selectTimeDiv1").addClass("hide");
		$(".addTimeDiv1").removeClass("hide");
	});
	//点击“新增时间段”事件；
	$(".addTimeDiv1").click(function(){
		$(".selectTimeDiv1").removeClass("hide");
		$(".addTimeDiv1").addClass("hide");
	})
	//--------------------- 接待时间段时间处理结束 ----------------------

	//--------------------- 自提时间段时间处理开始 ----------------------
	//点击自提时间段确定按钮的事件；
	// $(".J_beSure").click(function(){
	// 	//选择开始、结束时间的判断；
	// 	var beginH = $("select[name='beginHour2']").val();
	// 	var beginM = $("select[name='beginMinut2']").val();
	// 	var endH = $("select[name='endHour2']").val();
	// 	var endM = $("select[name='endMinut2']").val();
	// 	if (beginH < endH) {
	// 		pass2(beginH, beginM, endH, endM);
	// 	}else if(beginH == endH && beginM < endM){
	// 		pass2(beginH, beginM, endH, endM);
	// 	}else{
	// 		tipshow('关门时间要大于开门时间','warn');
	// 	}
		
	// 	//手动重新验证
	// 	Revalidate("zitiTimes");
	// })
	// //自提时间段选择验证通过事件
	// function pass2(bh,bm,eh,em){
	// 	var weekName = "";
	// 	if ($(".week2").hasClass("weekSelect") == true) {
	// 		var arr={};
	// 		arr.startTime= bh+':'+bm;
	// 		arr.endTime= eh+':'+em;
	// 		arr.days= [];
	// 		$(".weekDiv2 .weekSelect").each(function(index, ele){
	// 			weekName += $(this).text()+" ";
	// 			arr.days.push($(this).data('index'));
	// 		})
	// 		if ($(".week2").hasClass("weekSelect") == true) {
	// 			var time =   "<p>"+
	// 							"<t>"+bh+" 时 "+bm+" 分  ~ "+eh+" 时 "+em+" 分 . </t>"+
	// 							"<n style='display:inline-block'>"+weekName+"</n>"+
	// 							"<a href='##' class='del2'>  | 删除</a>"+
	// 						"</p>";
	// 			$(".timeDivShow2").prepend(time);
	// 		}
	// 		$(".selectTimeDiv2").addClass("hide");
	// 		$(".addTimeDiv2").removeClass("hide");
			
	// 		$(".week2").each(function(index, ele){
	// 			if ($(this).hasClass("weekSelect")) {
	// 				$(this).removeClass("weekSelect").children(".weekBoard").removeClass('hide');
	// 			}
	// 		})

	// 		zitiTimes.unshift(arr);
	// 		$("#zitiTimes").val(zitiTimes);

	// 	}else{
	// 		tipshow('请选择至少一天接待时间','warn');
	// 	}
	// }
	// //点击删除，删除设置的时间段；
	// $(document).on("click", ".del2", function(){
	// 	var resetWeekArr = $(this).siblings("n").html().split(" "); 
	// 	var weekArr = $(".week2").text().split(" "); 
	// 	//拿到删除的时间段中的星期几；
	// 	for(var i=0; i<resetWeekArr.length; i++){
	// 		if (resetWeekArr[i]=='') {continue};
	// 		var n = $.inArray(resetWeekArr[i], weekArr);
	// 		if (n == -1) {n = null}
	// 		$(".week2 .weekBoard").eq(n).addClass('hide');
	// 	}
	// 	var d_index = $('.timeDivShow2 p').index($(this).parent());
	// 	$(this).parent().remove();
		
	
	// 	zitiTimes.splice(d_index, 1);
	// 	$("#zitiTimes").val(zitiTimes);
	// 	//手动重新验证
	// 	Revalidate("zitiTimes");
	// });
	// //接待时间部分的“取消”点击事件
	// $(".cancle2").click(function(){
	// 	$(".selectTimeDiv2").addClass("hide");
	// 	$(".addTimeDiv2").removeClass("hide");
	// });
	// //点击“新增时间段”事件；
	// $(".addTimeDiv2").click(function(){
	// 	$(".selectTimeDiv2").removeClass("hide");
	// 	$(".addTimeDiv2").addClass("hide");
	// })
	//--------------------- 自提时间段时间处理结束 ----------------------

	//手动重新验证
	function Revalidate(ele){
		$('#defaultForm')
			.data('bootstrapValidator')
			.updateStatus(ele, 'NOT_VALIDATED', null)
			.validateField(ele);
	}
	
	
	//------------图片上传弹出层的切换----------------
	function hideAndShow(hideEle, showEle){
		$(hideEle).addClass("no");
		$(showEle).removeClass("no");
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
				$(".Img_add, .attachment-list-region").addClass("no");
				$(".attachment_"+(index+1)+"").removeClass("no")
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
	$(document).on("click",".image-item",function(){
		if ($(this).hasClass("li_select")) {
			$(this).css("border-color", "#ccc").removeClass("li_select").children(".attachment-selected").addClass("no");
			n--;
		}else{
			$(this).css("border-color", "#0077DD").addClass("li_select").children(".attachment-selected").removeClass("no");
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
		$(".image-list li").removeClass("li_select").children(".attachment-selected").addClass("no");
		$(".image-item").css("borderColor", "#ccc")
	}
	
	//判断确定按钮是否可用以及样式
	function buttonSure(){
		var $liSelect = $(".image-list .li_select");
		if ($liSelect.length>0 && $liSelect.length<=4) {
			$(".modal_content_1 .text-center button").addClass("ui-btn-primary").attr("disabled", false);
		}else{
			$(".modal_content_1 .text-center button").removeClass("ui-btn-primary").attr("disabled", true);
		}
		if($liSelect.length>3){
			tipshow("最多添加三张图片");
		}
	}
	
	//点击确定
	$(".text-center button").click(function(){
		var $li_select = $(".image-list .li_select");
		if(!$(".modal_content_1").hasClass('no')){
			$li_select.each(function(index, ele){
				imgId.push($(this).children('.image-box').attr('src'));
				var imgSrc = $(this).children("img").attr("src");
				if ($("#selImg").children("span").length < 4) {
					var _html = "<span class='showLittleImg'>"+
									"<img src="+imgSrc+" class='addSeleImg'/>"+
							 		"<i class='imgClose' style='color:white;'>×</i>"+
								 "</span>";
					$("#addImg").before(_html);
					var _length = $('#selImg').find('.showLittleImg').length;
					_length>=4 ? $('#addImg').hide() : $('#addImg').show();
				}else{
					return false;
				}
			});
		}else{
			for(var i = 0;i < imgSrcArr.length;i ++){
				if ($("#selImg").children("span").length < 4) {
					var _html = "<span class='showLittleImg'>"+
									"<img src=/"+imgSrcArr[i]+" class='addSeleImg'/>"+
							 		"<i class='imgClose' style='color:white;'>×</i>"+
								 "</span>";
					$(_html).prependTo($("#selImg"));
					var _length = $('#selImg').find('.showLittleImg').length;
					_length>=4 ? $('#addImg').hide() : $('#addImg').show();
				}else{
					return false;
				}
			}
		}
		$('.modal').modal('hide')
		$("input[name='images']").val(imgId);
		reset_styleAnum();
		buttonSure(); 
	})
	//点击删除已选择显示的小图片
	$(document).on("click", ".imgClose", function(){
		$('#addImg').show();
		imgId.splice($(this).parent().index(),1);
		$("input[name='images']").val(imgId);
		$(this).parent().remove();
		$("input[name='imgId']").val(imgId);
	})

	var classifyId;//图片分组id
    //----------------图片模态框点击事件---------------
	$("#addImg").click(function(){
	    $.get('/merchants/myfile/getClassify',function(data){
	        $('.category-list').empty();
	        classifyId = data.data[0].id;//默认分组
	        var _group = '';
	        for( var i = 0;i < data.data.length;i++ ){
	            if (i == 0){
	                _group += '<li class="js-category-item active" data-id="'+data.data[i].id+'">'+data.data[i].name+'\
	                            <span>'+data.data[i].number+'</span>\
	                        </li>';
	            }else{
	                _group += '<li class="js-category-item" data-id="'+data.data[i].id+'">'+data.data[i].name+'\
	                            <span>'+data.data[i].number+'</span>\
	                        </li>';
	            }
	        }
	        if(i == data.data.length){
	            $('.category-list').append(_group);
	        }
	    });
	    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token},function(data){//默认第一组
	        getPicture(data);
	        $('.picturePage').extendPagination({
	            totalCount: data.data[0].total,
	            showCount: data.data[0].last_page,
	            limit: data.data[0].per_page
	        });
	    });
	    $('#myModal-adv').modal('show');
	});
	// 数据请求成功后执行方法
	function getPicture(data){
	    $('.attachment-list-region .image-list').empty();//先清空所有的元素
	    var _img_item= '';
	    var _imgType;
	    for ( var i = 0;i < data.data[0].data.length;i++ ){
	        _imgType = data.data[0].data[i].FileInfo.type.slice(data.data[0].data[i].FileInfo.type.lastIndexOf('/')+1)
	        _img_item +='<li class="image-item" data-id="'+data.data[0].data[i].FileInfo.id+'">\
	            <img class="image-box" src="/'+data.data[0].data[i].FileInfo.path+'" />\
	            <div class="image-meta"></div>\
	            <div class="image-title">'+data.data[0].data[i].FileInfo.name+'.'+_imgType+'</div>\
	            <div class="attachment-selected no">\
	                <i class="icon-ok icon-white"></i>\
	            </div>\
	        </li>';
	    }
	    if(i == data.data[0].data.length){
	        $('.attachment-list-region .image-list').append(_img_item);
	    }
	}
	$('.modal .attachment-pagination').on('click','.picturePage .pagination li a', function(event) {
	    var page = $(this).text()//下标切换页码数
	    if(!parseInt(page)&& $(this).parent().index() == 0){
	        page =  $('.picturePage .pagination .active').text();
	    }else if(!parseInt(page)&& $(this).parent().index() != 0){
	        page =  parseInt($('.picturePage .pagination .active').text());
	    }else if($(this).parents('li').hasClass('disabled')){
	        return false;
	    }
	    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token,page:page},function(data){
	        getPicture(data);
	    });
	});
	$(document).on('click','.js-category-item',function(){
	    $('.js-category-item').removeClass('active');
	    $(this).addClass('active');
	    classifyId = $(this).data('id');
	    $("input[name='classifyId']").val(classifyId);
	    if($(this).children('span').text() == 0){
	        $('.attachment_1').hide();
	        $('.Img_add').removeClass('no');
	        return false;
	    }
	    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,'_token':_token},function(data){//默认第一组
	        getPicture(data);
	        $('.picturePage').extendPagination({
	            totalCount: data.data[0].total,
	            showCount: data.data[0].last_page,
	            limit: data.data[0].per_page
	        });
	        $('.attachment_1').show();
	        $('.Img_add').addClass('no');
	    });
	});
	
	// 上传成功
    uploader.on('uploadSuccess', function (file,response) {
        console.log(response);
        if(response.status==1){
           imgId.push(response.data.FileInfo.s_path);
           imgSrcArr.push(response.data.FileInfo.s_path);
           $(".modal_content_2 .text-center button").addClass("ui-btn-primary").attr("disabled", false);
        }
        
	});
	
	// 买家选择自提时间段
	// var $isSet = $('.J_set-time');
	// $('#is_set_time_fl').change(function(){
	// 	if ($('#is_set_time_fl')[0].checked) {
	// 		$('#is_set_time').val(1);
	// 		$isSet.removeClass('hide');
	// 	} else {
	// 		$('#is_set_time').val(0);
	// 		$isSet.addClass('hide');
	// 	}
	// });
	// 同时作为线下门店接待
	$('#checkBox').change(function(){
		if ($('#checkBox')[0].checked){
			$('#store_reception').val(1);
		} else {
			$('#store_reception').val(0);
		}
	});
	
    $("#saveBtn").click(function(){
		if ($('.js-province option:selected').val() == '' || $('.js-city option:selected').val() == '' || $('.js-county option:selected').val() == '') {
			tipshow("请选择自提点地址","warn");
			return false;
		};
		if(storeTimes.length == 0) {
    		tipshow("请选择接待时间","warn");
			return false;
		}
		// if($('#is_set_time').val()==1 && zitiTimes.length == 0) {
    	// 	tipshow("请选择自提时间","warn");
		// 	return false;
		// }
    	if(imgId.length==0) {
    		tipshow("请至少添加一张自提点图片","warn");
    		return false;
		}
		var bootstrapValidator = $('#defaultForm').data('bootstrapValidator');
        //手动触发验证
		bootstrapValidator.validate();
        if(bootstrapValidator.isValid()){
			//表单提交的方法、比如ajax提交
			var data = {};
			data.longitude = $('.search_map').attr('data-lng');
			data.latitude = $('.search_map').attr('data-lat');
			data._token = $('meta[name="csrf-token"]').attr('content');
			data.title = $('input[name="title"]').val();
			data.provinceId = $('.js-province option:selected').val();
			data.cityId = $('.js-city option:selected').val();
			data.areaId = $('.js-county option:selected').val();
			data.address = $('input[name="address"]').val();
			data.telphone = $('input[name="telphone"]').val();
			data.is_set_time = 0;
			data.images = imgId;
			data.comment = $('#comment').val();
			data.store_reception = $('input[name="store_reception"]').val();
			data.receptionTimes = storeTimes;
			data.zitiTimes = [];
			data.id = $('input[name="id"]').val();
			// console.log(data);
			$.post("/merchants/currency/saveZiti",data,function(res){
				if(res.status == 1){
					tipshow("保存成功");
					window.location.href='/merchants/currency/receptionList';
				}else{
					tipshow(res.info,"warn");
				}
			});
        } else {
			tipshow("请按要求完成带*的必填项","warn");
		}
		
    })
})