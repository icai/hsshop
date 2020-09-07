$(function(){
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
        var county = "<option value=''>选择地区</option>";
        for(var i = 0;i < city.length;i ++){
            county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
        }
        $('.js-county').html(county);
    });

	var imgs = $("input[name='imgs']").val();//图片数组
	var imgId = imgs.split(",");
	if(imgId == ""){
		imgId = [];
	}
	var imgSrcArr = [];
	//表单验证；
	$('#defaultForm').bootstrapValidator({
        message: '这个值是无效的',
        excluded: [':disabled'],
        trigger: 'blur',
        feedbackIcons: {
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
        	storeName:{
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
                validators: {
                	notEmpty: {
                        message: '电话号码不可空'
                   	},
                    stringLength: {
                        min: 7,
                        max: 8,
                        message: '长度为7-8位数字'
                    },
                    regexp: {
                        regexp: /(^[0-9]{7,8}$)/,
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
            },
            store_img:{
            	validators: {
                    notEmpty: {
                        message: '请选择至少一张图片'
                    }
                }
            }
        }
  });
	
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

    /**
     * @author  邓钊
     * @desc 选择星期几
     * @date 2018-6-26
     * @param
     * @param
     * @return
     *
     */
	$(".times_ul").children('li').on('click',function () {
		var active = $(this).attr('class')
        console.log(active);
		if(active){
            $(this).removeAttr('class')
			$(this).children('input').val('0')
		}else{
            $(this).addClass('active')
            $(this).children('input').val('1')
		}
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
		if($liSelect.length>4){
			tipshow("最多添加四张图片");
		}
	}
	
	//点击确定
	$(".text-center button").click(function(){
		var $li_select = $(".image-list .li_select");
		if(!$(".modal_content_1").hasClass('no')){
			$li_select.each(function(index, ele){
				imgId.push($(this).data("id"));
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
		$("input[name='imgs']").val(imgId);
		reset_styleAnum();
		buttonSure(); 
	})
	//点击删除已选择显示的小图片
	$(document).on("click", ".imgClose", function(){
		$('#addImg').show();
		imgId.splice($(this).parent().index(),1);
		$("input[name='imgs']").val(imgId);
		$(this).parent().remove();
		$("input[name='imgId']").val(imgId);
	})
	// 时间
	$('#startTime').datetimepicker({
        format: 'LT',
        locale: 'ru'//设置时间为24小时
    });
    $('#endTime').datetimepicker({
        format: 'LT',
        locale: 'ru'
    });
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
           imgId.push(response.data.FileInfo.id);
           imgSrcArr.push(response.data.FileInfo.s_path);
           $(".modal_content_2 .text-center button").addClass("ui-btn-primary").attr("disabled", false);
        }
        
    });
    $("#saveBtn").click(function(){
    	if(imgId.length==0){
    		tipshow("请至少添加一张门店图片","warn");
    		return false;
    	}
	    var data = $("#defaultForm").serialize();
        console.log(data);
        $.get("/merchants/currency/editStore",data,function(res){
	    	if(res.status == 1){
	    		tipshow("保存成功");
	    		window.location.href='/merchants/currency/outlets';
	    	}else{
	    		tipshow(res.info,"warn");
	    	}
	    });
	    return false;
    })
})