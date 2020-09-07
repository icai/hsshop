$(function(){
	//通用享立减设置赋值 huoguanghui
	if( !(shareEvent instanceof Array) ){//已设置享立减通用数据
		//卡片赋值
		$("input[name='card_img']").val(shareEvent.card_img);
	    $('.card_img .js-upload-image').css('background-image','url("'+shareEvent.card_img+'")').addClass('image-display');
	    $(".card_img").append('<span class="remove-img img_del" data-type="3">×</span>');
		$(".card_img .js-upload-image").html('');	  
		//分享赋值
		$('input[name="share_title"]').val(shareEvent.share_title);
		$('input[name="share_img"]').val(shareEvent.share_img);
		// $('.image-wrap-share .image-display').html('');
		// $('.image-wrap-share .image-display').css('background-image','url("'+shareEvent.share_img+'")');
		// $(".image-wrap-share").append('<span class="remove-img img_del" data-type="2">×</span>');

	}
	if(GetQueryString('id')){//存在id可编辑
		$('input[name="title"]').val(shareEvent.title);
		$('.validate').val(shareEvent.product_id);
		//add by jonzhang
		$('.product_name').val(shareEvent.title);
		$('.baodi_price').val(shareEvent.lower_price);
		$('.zhujian_price').val(shareEvent.unit_amount);
		$("#goods_id").val(shareEvent.product_detail.id);
        $(".act_title").html(shareEvent.title);
        $(".active_price").html('￥'+shareEvent.product_detail.price);
        $(".sel-goods").css('border','none').html('<div style="width: 100%;height: 100%;overflow: hidden;"><img class="img-goods" src="/' + shareEvent.product_detail.img + '" /></div>');
        $('.top-img').html('<img src="/' + shareEvent.product_detail.img + '"/>');	
        $('.image-wrap-share .image-display').css('border','none').html('<img class="img-goods" src="'+shareEvent.share_img+'" />')
        $(".image-wrap-share").append('<span class="remove-img img_del" data-type="2">×</span>');
        $(".example_box_img").children('img').removeClass('hide').attr('src',shareEvent.share_img)
        $('input[name="share_title"]').val(shareEvent.share_title);
		$('input[name="share_img"]').val(shareEvent.share_img);
        if(shareEvent.rule_img){
        	$('input[name="rule_img"]').val(shareEvent.rule_img);
            $('.image-wrap-rule .image-display').html('<img class="img-goods" src="'+shareEvent.rule_img+'" />');
        }else{
        	$('input[name="rule_img"]').val("hsshop/image/static/xianglijian_rule.jpg");
            $('.image-wrap-rule .image-display').html('<img class="img-goods" src="'+imgUrl+'hsshop/image/static/xianglijian_rule.jpg" />');
        }
        if(shareEvent.rule_title){
        	$('input[name="rule_title"]').val(shareEvent.rule_title);
        }else{
        	var rule_title = "享立减规则";
        	$('input[name="rule_title"]').val(rule_title);
        }
        if(shareEvent.rule_text){
        	$('textarea[name="rule_text"]').val(shareEvent.rule_text);
        }else{
        	$('textarea[name="rule_text"]').val($('#rule_model .modal-body').text());
        }
		// $(".image-warp-share").append('<span class="remove-img img_del" data-type="2">×</span>');
        // $(".image-warp-rule").append('<span class="remove-img img_del" data-type="4">×</span>');
		// $('.js-btn-save').attr('disabled','disabled').addClass('gay')
		//享立减二期更改
		$('.card_img .image-display').css('background-image','url("'+shareEvent.card_img+'")');
		$("input[name='card_img']").val(shareEvent.card_img);
		$('.image-warp-active .js-upload-image').addClass("image-display");
		$('.image-warp-active .js-upload-image').html('<img class="img-goods" src="'+shareEvent.act_img+'" />').css('border','none');
		$("input[name='act_img']").val(shareEvent.act_img);
		$(".image-warp-active").append('<span class="remove-img img_del" data-type="1">×</span>');
		$(".xlj_example_box_img").children('img').removeClass('hide').attr("src",shareEvent.act_img)
		$(".card_img").append('<span class="remove-img img_del" data-type="3">×</span>');
        // $(".image-warp-rule").append('<span class="remove-img img_del" data-type="4">×</span>');
		$("input[name='btn_title']").val(shareEvent.button_title);
		$(".show_active_title").text(shareEvent.title);
		$(".show_active_ftitle").text(shareEvent.subtitle);
		$("input[name='subtitle']").val(shareEvent.subtitle);
		$("input[name='subtitle']").val(shareEvent.subtitle);
		$("input[name='is_initial'][value="+shareEvent.is_initial+"]").attr("checked",true);
		$(".product_price_info .price .num").text(shareEvent.product_detail.price);
		$(".product_price_info .oprice .num").text(shareEvent.product_detail.oprice);
		if(shareEvent.is_initial == 1){
			$(".initial_value").removeClass("none");
			$("input[name='initial_value']").val(shareEvent.initial_value);
		}
		$("#start_time").val(shareEvent.start_time).attr("disabled",true);
		$("#end_time").val(shareEvent.end_time)
		var show_imgs_arr = shareEvent.show_imgs.split(',');
		if(show_imgs_arr.length>0){
	        	var _html= "";
	        	var productLength = $(".product_sort").length;
	        	for(var i = 0; i<show_imgs_arr.length; i++){
	        		if(productLength + i > 10){//最多十张图
	        			break;
	        		}
	        		_html += '<li class="product_sort sort"><div style="width: 100%;height: 100%;overflow: hidden;background-color: #ffffff;"><img src="'+show_imgs_arr[i]+'" class="js-img-preview"></div>';
	        		_html += '<div class="js-delete-picture close-modal small">×</div></li>';
	        	}
	        	if(i != show_imgs_arr.length || productLength+show_imgs_arr.length == 10){
	        		$(".js-picture-list .add").hide();
	        	}
	        	$(".js-picture-list .add").before(_html);
	        } 
	}
	
    // 选择商品点击事件
    $("body").on("click", ".sel-goods", function(e) {
        if ($(this).find(".icon-add").length > 0) {
            e.preventDefault();
            var href = "/merchants/product/create";
            //is_distribution 享立减参数
			selGoods.open({ success: callback, href: href ,is_distribution:0,postData:{filter_negotiable:1,filter_hexiao:1,filter_cam:1}});
			//var html = '<span style="margin-left:60px;color:#ff4444;">已开启分销的商品，不可参与享立减活动 </span>';
			$('.js_manage').append(html);
        }
    });
    
    //删除商品点击事件
    $("body").on("click", ".sel-goods .remove-img", function(e) {
        e.stopPropagation();
        $(this).parent().attr("href", "javascript:;");
        $(this).parent().removeAttr("target");
        $("#goods_id").val("");
        $("#goods_name").val("");
        $(this).parent().html('<i class="icon-add">+</i>').removeAttr('style');
        delGoods();
    });
	
    function callback(json) {
        var _json = json;
        console.log(json)
        $(".sel-goods").attr("href", json[0].url);
        $(".sel-goods").attr("target", "_blank");
        $(".active_title").html(json[0].title);
        $(".active_price").html('￥'+json[0].price);
        $("#goods_id").val(json[0].id);
        $("#goods_name").val(json[0].title);
        $(".sel-goods").css('border','none').html('<div style="width: 100%;height: 100%;overflow: hidden;"><img class="img-goods" src="/' + json[0].img + '" /></div><span class="remove-img">×</span>');
        $('.top-img').html('<img src="/' + json[0].img + '"/>');

        $("input[name='title']").val(json[0].title);
        $(".show_active_title").text(json[0].title);
        $(".price .num").text(json[0].price);
        $(".oprice .num").text(json[0].oprice);
    }
    
    //删除商品
    function delGoods() {
        $("#div_spec").hide();
        $("#div_spec table").html("");
        $("#goods_id").val('');
        $("#goods_name").val("");
        $(".active_title").html('');
        $(".active_price").html('');
        $('.top-img').html('<div class="top_tle_img">商品主图</div>');
        spec_json = [];
    };
       
    //删除商品图
    $( document ).on("click",".js-delete-picture",function(){
    	$(this).parent(".product_sort").remove();
		// 许立 2018年08月14日 删除图片的同时显示添加图片按钮
		$(".js-picture-list .add").show();
    })
	//提交
	$('.js-btn-save').click(function(){
		var baodi_price = parseFloat($('.baodi_price').val());
		var zhujian_price =parseFloat($('.zhujian_price').val());
		var share_title =$('input[name="share_title"]').val();
		var share_img =$('input[name="share_img"]').val();

		//享立减二期
		var show_imgs = $(".product_sort");//商品图片元素
		var act_img = $("input[name='act_img']").val();//活动图片
		var card_img = $("input[name='card_img']").val();//卡片图片
		var show_imgs_arr = [];//商品图片数组
		for( var i = 0;i < show_imgs.length;i ++ ){
			show_imgs_arr.push(show_imgs.eq(i).find("img").attr("src"));
		}
		var subtitle = $("input[name='subtitle']").val();//活动副标题
		var is_inital = $("input[name='is_initial']:checked").val();//是否开启组建的初始值
		var initial_value = $("input[name='initial_value']").val()?$("input[name='initial_value']").val():0; //开启的初始值
		var btn_title = $("input[name='btn_title']").val(); //按钮的名称
		var start_time = $("#start_time").val();//开始时间
		var end_time = $("#end_time").val();//结束时间
        var rule_img = $('input[name="rule_img"]').val();
        var rule_title = $('input[name="rule_title"]').val();
        var rule_text = $('textarea[name="rule_text"]').val();
		if(!$('.validate').val()){
			tipshow('请选择商品','warm');
			return false;
		};
		if(!$('.z-title').val()){
			tipshow('请填写活动名称','warm');
			return false;
		};
		if(!$('.zhujian_price').val()){
			tipshow('请填写助减金额','warm');
			return false;
		};
		if(zhujian_price < 0){
			tipshow('助减金额不能小于0','warm');
			return false;
		};
		if(!$('.baodi_price').val()){
			tipshow('请填写保底价','warm');
			return false;
		};
		if(!$('.baodi_price').val() < 0){
			tipshow('保底价不能小于0','warm');
			return false;
		};
		if( !btn_title ){
			tipshow('请添加按钮名称','warm');
			return false;
		}
		if( !act_img ){
			tipshow('请添加活动图片','warm');
			return false;
		}
		if( show_imgs_arr.length == 0 ){
			tipshow('请添加商品图片','warm');
			return false;
		}
		if( !subtitle ){
			tipshow('请填写活动副标题','warm');
			return false;
		}
		if( !start_time ){
			tipshow('请填写开始时间','warm');
			return false;
		}
		if( !end_time ){
			tipshow('请填写结束时间','warm');
			return false;
		}
		if( is_inital == 1 && !initial_value){
			tipshow('请填写开启助减的初始值','warm');
			return false;
		}
		if(share_title || share_img){
			if(!(share_title && share_img)){
				tipshow('分享标题和图片不可只添加一项','warm');
				return false;
			}
		}
		if( zhujian_price - ($(".product_price_info .price .num").text() -$('.baodi_price').val()) > 0 ){
			tipshow('助减金额过大','warm');
			return false;
		}
		if( $('.baodi_price').val() - $(".product_price_info .price .num").text() >= 0 ){
			tipshow('保底价格应小于商品价格','warm');
			return false;
		}
        if( !rule_img ){
            tipshow('请选择规则图片','warm');
            return false;
        }
        if( !rule_title ){
            tipshow('请填写规则标题','warm');
            return false;
        }
        if( !rule_text ){
            tipshow('请填写规则内容','warm');
            return false;
        }
		var data = {
			title:$('.z-title').val(),
			product_id:$('.validate').val(),
			lower_price:$('.baodi_price').val(),
			unit_amount:$('.zhujian_price').val(),
			share_title:$('input[name="share_title"]').val(),
			share_img:$('input[name="share_img"]').val(),
			show_imgs:show_imgs_arr,
			subtitle:subtitle,
			act_img:act_img,
			card_img:'',
			is_initial:is_inital,
			initial_value:initial_value,
			btn_title:btn_title,
			start_time:start_time,
			end_time:end_time,
            rule_img:rule_img,
            rule_title:rule_title,
            rule_text:rule_text,
			product_name:$('.product_name').val()
		};
		if(GetQueryString('id')){//存在id可编辑
			data.id=GetQueryString('id')
		}
		$.ajax({
			type:"POST",
			url:"/merchants/shareEvent/create",
			data:data,
			headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
            	if(res.status == 1){
            		tipshow(res.info);
            		setTimeout(function(){
            			window.location.href='/merchants/shareEvent/list'
            		},1000)
            	}else{
            		tipshow(res.info,'warm');
            	}
            },
            error:function(){
            	console.log('数据访问错误')
            }
		});
	});
	/**
	 * 切换助减标签
	 * @author  huoguanghui
	 */
	$("input[name='is_initial']").change(function(){
		if( $(this).val() == 1 ){//开启助减
			$(".initial_value").removeClass("none");
		}else{
			$(".initial_value").addClass("none");
		}
	})
	/**
	 * 活动标题显示
	 * @author  huoguanghui
	 */
	$("input[name='subtitle']").on("input",function(){
		$(".show_active_ftitle").text( $(this).val() );
	})
	$("input[name='title']").on("input",function(){
		$(".show_active_title").text( $(this).val() );
	})

	var start = {
        elem: '#start_time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas) {
            // console.log(datas);
            $('#start_time').val(datas);
            $('#start_time').focus();
            $('#start_time').blur();
            // $('.edit_form').data("bootstrapValidator").validate('start_at');
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var t_min_time = $("#start_time").val() || laydate.now();
    var end = {
        elem: '#end_time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: t_min_time,
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas) {
            // console.log($('#endTime').val())
            $('#end_time').val(datas);
            $('#end_time').focus();
            $('#end_time').blur();
            // $('.edit_form').data("bootstrapValidator").validateField('end_at');
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);

	//上传图片
	var img_add_num = 1 || 2 ; //选择添加图片类型  1：活动图, 2：分享图片  3.分享卡片
	$(".js-upload-image").click(function(){		
		img_add_num = $(this).data('imgadd');
		layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            skin: 'demo-class',
            content: '/merchants/order/clearOrder/1?type='+img_add_num
        }); 
	     /**
	     * 图片选择后的回调函数
	     */
	    selImgCallBack = function(resultSrc){ 
	    	console.log(index)
            console.log(resultSrc);
            if(resultSrc.length>0){
	        	if(img_add_num == 1){
                    var imgNum = parseInt(parseInt(resultSrc[0].imgWidth) / parseInt(resultSrc[0].imgHeight) * 10) / 10
					var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
					var sum = parseInt(750 / 750 * 100) / 100
					if( num < sum - 0.2 || num > sum + 0.2){
                        tipshow('图片比例非1:1，请重新上传','warm');
                        return false;
					}
					if(parseInt(resultSrc[0].imgWidth) < 400){
                        tipshow('图片尺寸小于400px，请重新上传','warm');
                        return false;
					}
	        		$(".image-warp-active .add_active_img").html('');	
	        		$('.image-warp-active .add_active_img').eq(index).html('<img class="img-goods" src="'+resultSrc[0].imgSrc+'" />').addClass('image-display').css("border","none");
				    $("input[name='act_img']").eq(index).val(resultSrc[0].imgSrc);
				    $(".image-warp-active").append('<span class="remove-img img_del" data-type="1">×</span>');
                    $(".xlj_example_box_img").children('img').attr("src",resultSrc[0].imgSrc).removeClass('hide');
                }else if(img_add_num == 2){
                    var imgNum = parseInt(parseInt(resultSrc[0].imgWidth) / parseInt(resultSrc[0].imgHeight) * 10) / 10
					// var num = parseInt(420 / 336 * 10) / 10
					var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
					var sum = parseInt(750 / 750 * 100) / 100
					if( num < sum - 0.2 || num > sum + 0.2){
                        tipshow('图片比例非1:1，请重新上传','warm');
                        return false;
					}
					if(parseInt(resultSrc[0].imgWidth) < 400){
                        tipshow('图片尺寸小于400px，请重新上传','warm');
                        return false;
					}
                    $(".image-wrap-share .image-display").html('');
                    $(".image-wrap-share input[name='share_img']").eq(index).val(resultSrc[0].imgSrc);
                    $(".example_box_img").children('img').attr("src",resultSrc[0].imgSrc).removeClass('hide');
				    $('.image-wrap-share .image-display').eq(index).css('border','none').html('<img class="img-goods" src="'+resultSrc[0].imgSrc+'" />');
				    $(".image-wrap-share").append('<span class="remove-img img_del" data-type="2">×</span>');
	        	}else if(img_add_num == 3){ //分享卡片
		            var resultSrc = resultSrc.join();
                    $(".card_img .js-upload-image").html('');
				    $("input[name='card_img']").val(resultSrc);
				    $('.card_img .js-upload-image').css('background-image','url("'+resultSrc+'")').addClass('image-display');
				    $(".card_img").append('<span class="remove-img img_del" data-type="3">×</span>');
	        	}else if(img_add_num == 4){
                    var imgNum = parseInt(parseInt(resultSrc[0].imgWidth) / parseInt(resultSrc[0].imgHeight) * 10) / 10
					var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
					var sum = parseInt(750 / 125 * 100) / 100
					if( num < sum - 0.2 || num > sum + 0.2){
                        tipshow('图片比例非6:1，请重新上传','warm');
                        return false;
                    }
                    if(parseInt(resultSrc[0].imgWidth) > 400){
                        tipshow('图片尺寸大于400px，请重新上传。','warm');
                        return false;
					}
                    $(".image-wrap-rule .image-display").html('');
                    $(".image-wrap-rule input[name='rule_img']").eq(index).val(resultSrc[0].imgSrc);
                    $('.image-wrap-rule .image-display').eq(index).css('border','none').html('<img class="img-goods" src="'+resultSrc[0].imgSrc+'" />');
                    // $(".image-wrap-rule").append('<span class="remove-img img_del" data-type="4">×</span>');

                }
	        } 
	    }
	});
	
	//清空图片
	$('body').on('click','.image-wrap .remove-img',function(){
		if($(this).data('type') == 1){
			$(".image-warp-active input[name='share_img']").eq(index).val("");
	   		$('.image-warp-active .add_active_img').eq(index).removeAttr('style').removeClass('image-display');
	   		$('.image-warp-active .add_active_img').html('+添加图片');
	   		$(".image-warp-active .img_del").remove();	
	   		$(".xlj_example_box_img").children('img').attr("src",'').addClass('hide');
		}else if($(this).data('type') == 2){
			$(".image-wrap-share input[name='share_img']").eq(index).val("");
	   		$('.image-wrap-share .image-display').eq(index).removeAttr('style');
	   		$('.image-wrap-share .image-display').html('+添加图片');
	   		$(".image-wrap-share .img_del").remove();
            $(".example_box_img").children('img').attr("src",'').addClass('hide');
		}else if($(this).data('type') == 3){
			$(".card_img input[name='card_img']").val("");
	   		$('.card_img .js-upload-image').css('background-image','url("")').removeClass('image-display');
	   		$('.card_img .js-upload-image').html('+添加图片');
	   		$(".card_img .img_del").remove();	
		}else if($(this).data('type') == 4){
            $(".image-wrap-rule input[name='rule_img']").eq(index).val("");
            $('.image-wrap-rule .image-display').eq(index).css('background-image','url("")');
            $('.image-wrap-rule .image-display').html('<i class="icon-add"></i>');
            $(".image-wrap-rule .img_del").remove();
        }
	});
	
	//上传商品图
	$(".js-add-picture").click(function(){		
		img_add_num = $(this).data('imgadd');
		layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/10?type=1'
        }); 
	     /**
	     * 图片选择后的回调函数
	     */
	    selImgCallBack = function(resultSrc){ 
	        if(resultSrc.length>0){
	        	var _html= "";
	        	var productLength = $(".product_sort").length;
	        	var flag = false
	        	for(var i = 0; i<resultSrc.length; i++){
                    var imgNum = parseInt(parseInt(resultSrc[i].imgWidth) / parseInt(resultSrc[i].imgHeight) * 10) / 10
                    console.log(imgNum);
                    if(imgNum != 1){
                        flag = true
	        			continue
					}
					if((resultSrc[i].imgWidth) < 400) {
                        flag = true
                        continue
					}
	        		if(productLength + i > 10){//最多十张图
	        			break;
	        		}
	        		_html += '<li class="product_sort sort"><div style="width: 100%;height: 100%;overflow: hidden;background-color: #ffffff;"><img src="'+resultSrc[i].imgSrc+'" class="js-img-preview"></div>';
	        		_html += '<div class="js-delete-picture close-modal small">×</div></li>';
	        	}
	        	if(flag){
                    tipshow('部分图片尺寸不符合，请重新上传图片','warm');
                }
	        	if(i != resultSrc.length || productLength+resultSrc.length == 10){
	        		$(".js-picture-list .add").hide();
	        	}
	        	$(".js-picture-list .add").before(_html);
	        } 
	    }
	});
	
	var index = 0;
	/**
	 图片调用方法
	 */
	function imgCommona(fileNumLimit,t_index){
		index =t_index;
	    layer.open({
	        type: 2,
	        title:false,
	        closeBtn:false, 
	        move: false, //不允许拖动 
	        area: ['860px', '660px'], //宽高
	        content: '/merchants/order/clearOrder/'+fileNumLimit
	    }); 
	}
	
	//获取url_参数
	function GetQueryString(name){
	    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	    var r = window.location.search.substr(1).match(reg);
	    if(r!=null)return  unescape(r[2]); return null;
	}

    //规则参考弹窗
    $('.rule_info').click(function(){
        $('#rule_model').modal()
    })

    $('.copy').click(function(){
        // ;
        $('textarea[name="rule_text"]').val($('#rule_model .modal-body').text());

    })
})