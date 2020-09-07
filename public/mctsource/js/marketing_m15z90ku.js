$(function(){	
	//抽奖资格选择商品
	var pidarr = [];
	var pids = '';	
	$('.js-find-goods').click(function(){
		var url = '/merchants/product/create';
		var that = $(this);				
		selGoods.open({success:function(data){	
			pids = data[0].id;
			var html = '<li class="sort-find disflx">';
			html += '<div class="porela z-inlin">';
			html += '<img alt="商品图" width="50" height="50" src="'+_host + data[0].img+'">';			
			html += '<a class="close-modal js-delete-goods-find small ng-hide" data-id="" title="删除">×</a>';			
			html += '</div>';
			html += '<span class="malf15">'+data[0].title+'</span>';
			html += '</li>';
			$('.flonone').prepend(html);
			pidarr.push(pids);
			$('.find-con').val(pidarr.join(","));
		},href:url,postData:{filter_negotiable:1}});
	})     
	// 点击删除选择图片
	$('body').on('click','.js-delete-goods-find',function(){
		$(this).parents(".sort-find").remove();
		var ind = $(this).parents(".sort-find").index();		
		pidarr.splice(ind-1,1);
		$('.find-con').val(pidarr.join(","));
	})
	// 大转盘选择商品
	$('.js-add-goods').click(function(){
		var url = '/merchants/product/create';
		var that = $(this);
		var index = $(this).parents('.prize-content').index();
		selGoods.open({success:function(data){
			if(index == 1){
				var t_index =$('.type1').find("input[type='radio']:checked").val();
				$('.prize-content-set1 .points-st'+t_index).find('.sort').show();
				$('.prize-content-set1 .points-st'+t_index).find('.fir-con').val(data[0]['id']);
				
				$(".fir-img").val(_host + data[0].img);
			}else if(index == 2){
				var t_indexa = $('.type2').find("input[type='radio']:checked").val();
				// alert(t_indexa);
				$('.prize-content-set2 .points-st'+t_indexa).find('.sort').show();
				$('.prize-content-set2 .points-st'+t_indexa).find('.sec-con').val(data[0]['id']);
				// that.next('.sec-con').val();
				$(".sec-img").val(_host + data[0].img);
			}else if(index == 3){
				var t_indexb = $('.type3').find("input[type='radio']:checked").val();
				// $('.prize-content-set3 .points-st'+t_indexb).find('.tri-num').val();
				$('.prize-content-set3 .points-st'+ t_indexb).find('.sort').show();
				$('.prize-content-set3 .points-st'+ t_indexb).find('.tri-con').val(data[0]['id']);
				$(".tri-img").val(_host + data[0].img);
			}
			that.parents('.prize-content').find('.sort img').attr('src',_host + data[0].img);
			that.parents('.prize-content').find('.sort').removeClass('hide');
		},href:url,postData:{filter_negotiable:1}})
	})
	// 点击删除选择图片
	$('.js-delete-goods').click(function(){
		// $('.sort img').attr('src','');
		// $('.sort').addClass('hide');
		$(this).parent('.sort').find('img').attr('src','');
		$(this).parent('.sort').hide();
	})
	$(".js-add-picture").click(function(){
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
	     /**
	     * 图片选择后的回调函数
	     */
	    selImgCallBack = function(resultSrc){
			if(resultSrc.length>0){
				//2018.10.16 大转盘分享页图片尺寸限制
				var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
                var sum = parseInt(750 / 750 * 100) / 100
				if( num < sum - 0.2 || num > sum + 0.2){
                    tipshow("图片比例非1:1，请重新上传","warn");
                    return false
                }
                if(parseInt(resultSrc[0].imgWidth) < 400){
                    tipshow("图片尺寸小于400px，请重新上传","warn");
                    return false
                }
				$("input[name='share_img']").val(resultSrc[0].imgSrc);
				$(".share_img").attr("src",_host+resultSrc[0].imgSrc).parent().removeClass('hide');
				$(".js-add-picture").html("修改图片").removeClass("add-goods");
	        } 
	    }
    }); 
    /*删除图片*/
    $(".delete").click(function(){
        $("input[name='share_img']").val("");
		$(".share_img").attr("src","").parent().addClass('hide');  
		$(".js-add-picture").html("+添加图片").addClass("add-goods");
    });
	//分享
	$("input[name='share_title']").val(data.share_title);
	$("input[name='share_img']").val(data.share_img);
	$(".share_img").attr('src',_host+data.share_img);
	if(data.share_img){
		$(".share_img_box").removeClass("hide");
	}
	$("textarea[name='share_desc']").val(data.share_desc);
	//会员列表
	var car_lgth = cardData.length;
	for(var i = 0;i<car_lgth;i++){
		var option = '<option value="'+cardData[i].id+'">'+cardData[i].title+'</option>';
		$('.card-data').append(option);
	}
	if($.isEmptyObject(data)==false){
		var data_lgth = data.card_id.split(',').length;
		for(var i = 0;i<data_lgth;i++){
			var option = $('.card-data option[value="'+data.card_id.split(',')[i]+'"]').attr("selected",true);
		}		
	};
	if($.isEmptyObject(data)==false){
		var data_lgth = data.card_id.split(',').length;
		for(var i = 0;i<data_lgth;i++){
			var option = $('.card-data option[value="'+data.card_id.split(',')[i]+'"]').attr("selected",true);
		}		
	};
	//优惠券
	var cou_lgth = couponList.length;
	for(var i = 0;i<cou_lgth;i++){
		var option = '<option value="'+couponList[i].id+'">'+couponList[i].title+'</option>';
		$('.cou-sel').append(option);		
	}
	//中奖概率限制
	$('.z-rate').keyup(function(){
		var val = $(this).val()
		val = $(this).val().replace(/\D/g,'');
		if($('.z-rate').val()>100){
			$(this).val(100);
		}
	})
	//奖品数量限制
	$('.fir-num').keyup(function(){
		var val = $(this).val();
		val = $(this).val().replace(/\D/g,'');
		if($(this).val()>10000){
			$(this).val(10000);
		}
	})
	$('.sec-num').keyup(function(){
		var val = $(this).val();
		val = $(this).val().replace(/\D/g,'');
		if($(this).val()>10000){
			$(this).val(10000);
		}
	})
	$('.tri-num').keyup(function(){
		var val = $(this).val();
		val = $(this).val().replace(/\D/g,'');
		if($(this).val()>10000){
			$(this).val(10000);
		}
	})
	//重置select样式
	$('.radio-sel').chosen({
            allow_single_deselect : true,
    });
	$('.chosen-container-multi').css('width',200);
	$('.search-field input').css('width','100%');
//	编辑有数据显示
	var issend = 1;//参与送积分
	var sendall = 0;//参与用户
	var condit = 0;
	if($.isEmptyObject(data)==false){
		id = data.id;
		id_1 = data.prize[0].id;
		id_2 = data.prize[1].id;
		id_3 = data.prize[2].id;
		$('.z-title').val(data.title);//活动名称
		$('.start_time').val(data.start_time);//开始时间
		$('.statim').html(data.start_time);//开始时间
		$('.end_time').val(data.end_time);//结束时间
		$('.endtim').html(data.end_time);//结束时间
		$('.z-descr,.miaoshu').val(data.descr);//活动描述	
		$('.miaoshu').html(data.descr);//活动描述
		$('.z-reduce').val(data.reduce_integra);//消耗积分					
		$('.z-isall').val(data.send_integra);//送积分数量				
		$('.z-rate').val(data.rate);//中奖概率		
		for(var i = 0;i<data.product.length;i++){//抽奖资格产品
			var html = '<li class="sort-find disflx">';			
			html += '<div class="porela z-inlin">';
			html += '<img alt="商品图" width="50" height="50" src="'+_host + data.product[i].img+'">';			
			html += '<a class="close-modal js-delete-goods-find small ng-hide" data-id="" title="删除">×</a>';			
			html += '</div>';
			html += '<span class="malf15">'+data.product[i].title+'</span>';
			html += '</li>';
			$('.flonone').prepend(html);
			pidarr.push(data.product[i].id);
		}
		$('.find-con').val(pidarr.join(","));//抽奖资格产品
		sendall = data.is_send_all;
		issend = data.condit;
		condit = data.condit;		
		if(data.is_send_all==0){
			$(".z-sendall input").attr("checked","checked");//参与送积分
		}
        $(".condit input[type='radio'][value='"+data.condit+"']").attr("checked",true);//参与用户
        if(data.condit==1){
			$('.radio-p').show();			
		}else{
			$('.radio-p').hide();
		}	
		$(".z-rule input[type='radio'][value='"+data.rule+"']").attr("checked",true);//参与次数
		$(".z-rule input[type='radio'][value='"+data.rule+"']+.z-flolef+.lim-times").val(data.times)//参与次数
		// update 华亢 2018年7月30日 单选框选择bug 
		$('.type1').find("input[type='radio'][value='"+data.prize[0].type+"']").prop("checked",true)//奖项类型
		var type1 = data.prize[0].type;
		$('.type2').find("input[type='radio'][value='"+data.prize[1].type+"']").prop("checked",true)//奖项类型
		var type2 = data.prize[1].type;
		$('.type3').find("input[type='radio'][value='"+data.prize[2].type+"']").prop("checked",true)//奖项类型
		var type3 = data.prize[2].type;
		$('.prize-content-set1 .prize-group').hide();
		$('.prize-content-set1 .points-st'+type1).show();
		$('.prize-content-set1 .points-st'+type1).find('.fir-con').val(data.prize[0].content);
		$('.prize-content-set1 .points-st'+type1).find('.fir-num').val(data.prize[0].num);
		$('.prize-content-set2 .prize-group').hide();
		$('.prize-content-set2 .points-st'+type2).show();
		$('.prize-content-set2 .points-st'+type2).find('.sec-con').val(data.prize[1].content);
		$('.prize-content-set2 .points-st'+type2).find('.sec-num').val(data.prize[1].num);
		$('.prize-content-set3 .prize-group').hide();
		$('.prize-content-set3 .points-st'+type3).show();
		$('.prize-content-set3 .points-st'+type3).find('.tri-con').val(data.prize[2].content);
		$('.prize-content-set3 .points-st'+type3).find('.tri-num').val(data.prize[2].num);
		//奖项内容
		//一等奖
		var lgth1 = $('.type1').find("input[type='radio']:checked").index();
		//切换时候 不重复赋值 且该值显示错误
		// $('.prize-content-set1 .prize-group').eq(lgth1).find('.fir-con').val(data.prize[0].content);
		// $('.prize-content-set1 .prize-group').eq(lgth1).find('.fir-num').val(data.prize[0].num);
		$('.prize-content-set1 .prize-group').eq(3).find('.sort').removeClass('hide');
		$('.prize-content-set1 .prize-group').eq(3).find('.sort img').attr('src',_host + data.prize[0].img);
		$(".shuoming1").val(data.prize[0].method);
		$(".fir-img").val(data.prize[0].img);

		$('.z-imga').css('background-image','url("'+data.prize[0].img+'")');
	//		二等奖
		var lgth2 = $('.type2').find("input[type='radio']:checked").index();
		//切换时候 不重复赋值 且该值显示错误
		// $('.prize-content-set2 .prize-group').eq(lgth2).find('.sec-con').val(data.prize[1].content);
		// $('.prize-content-set2 .prize-group').eq(lgth2).find('.sec-num').val(data.prize[1].num );
		$('.prize-content-set2 .prize-group').eq(3).find('.sort').removeClass('hide');
		$('.prize-content-set2 .prize-group').eq(3).find('.sort img').attr('src',_host + data.prize[1].img);
		$(".shuoming2").val(data.prize[1].method);
		$(".sec-img").val(data.prize[1].img);
		$('.z-imgb').css('background-image','url("'+data.prize[1].img+'")');
	//		三等奖
		var lgth3 = $('.type3').find("input[type='radio']:checked").index();
		//切换时候 不重复赋值 且该值显示错误
		// $('.prize-content-set3 .prize-group').eq(lgth3).find('.tri-con').val(data.prize[2].content);
		// $('.prize-content-set3 .prize-group').eq(lgth3).find('.tri-num').val(data.prize[2].num);
		$('.prize-content-set3 .prize-group').eq(3).find('.sort').removeClass('hide');
		$('.prize-content-set3 .prize-group').eq(3).find('.sort img').attr('src',_host + data.prize[2].img);
		$(".shuoming3").val(data.prize[2].method);
		$(".tri-img").val(data.prize[2].img); 
		$('.z-imgc').css('background-image','url("'+data.prize[2].img+'")');
	}
	
//	设置日期
	var start = {
		elem: '#start_time',
		format: 'YYYY-MM-DD hh:mm:ss',
		min: laydate.now(), //设定最小日期为当前日期
		max: '2099-06-16 23:59:59', //最大日期
		istime: true,
		istoday: false,
		choose: function(datas) {
			end.min = datas; //开始日选好后，重置结束日的最小日期
			end.start = datas //将结束日的初始值设定为开始日
			$('.statim').text(datas);
		}
	};
	var end = {
		elem: '#end_time',
		format: 'YYYY-MM-DD hh:mm:ss',
		min: laydate.now(),
		max: '2099-06-16 23:59:59',
		istime: true,
		istoday: false,
		choose: function(datas) {
			start.max = datas; //结束日选好后，重置开始日的最大日期
			$('.endtim').html(datas);;
		}
	};
	laydate(start);
	laydate(end);
	condit = condit;//参与用户	
	$(".condit label").click(function(){
		condit = $(".condit input[type='radio']:checked").val();
		if(condit==1){
			$('.radio-p').show();
		}else{
			$('.radio-p').hide();
		}
	});	
	var is_send_all = issend;//是否所有参与者送积分
	$(".z-sendall").click(function(){
		if($('.z-sendall input').is(':checked')){
			is_send_all = 0;
		}else{
			is_send_all = 1;
		}
	});	
	var rule = 2;//参与次数
	var times = 1;//参与次数
	$(".z-rule").click(function(){
		rule = $(".z-rule input[type='radio']:checked").val();
	});	
	
	var index = 0;
	//奖项选择一二三
	$('.prize-tab-item').click(function(){
		index = $(this).index();
		$('.prize-tab-item').removeClass('selected');
		$(this).addClass('selected');
		$('.prize-content').addClass('hide');
		$('.prize-content').eq(index).removeClass('hide');
	})
	
	//奖品选取 积分优惠赠品
	var leng = 0;
	$('.prize-content .prize-spoil label').click(function(){
		leng = $(this).index();
		$('.prize-content').eq(index).find('.prize-group').hide();
		$('.prize-content').eq(index).find('.prize-group').eq(leng).show();
	});
	
	//下一步	验证
	//活动名称
	$("input[name='title']").blur(function(){ 
		if($("input[name='title']").val()==""){
			$('.rem1').remove();
			$("input[name='title']").css('border-color','red').after('<span class="nul-red rem1">活动名称未填写</span>');
		}else{
			$('.rem1').remove();
			$("input[name='title']").css('border-color','#ccc');
		};
	});
	//开始时间
	$(".start_time").blur(function(){ 
		$('.rem2').remove();
		$(".start_time").css('border-color','#ccc');		
	});
	//结束时间
	$(".end_time").blur(function(){ 
		$('.rem3').remove();
		$(".end_time").css('border-color','#ccc');		
	});
	//活动说明	
	$(".z-string").blur(function(){
		if($('.z-string').val()==""){
			$('.rem4').remove();
			$('.z-string').html($(".z-string").val());
			$(".z-string").css('border-color','red').after('<span class="nul-red rem4">活动说明未填写</span>');	
		}else{
			$('.z-string').html($(".z-string").val());	
			$(".z-string").css('border-color','#ccc');
			$('.rem4').remove();
		}		
	});
	$(".z-string").keyup(function(){
		$('.miaoshu').html($(".z-string").val());
	});
	//消耗积分
	$("input[name='lose_integral']").blur(function(){ 
		if($("input[name='lose_integral']").val()==""){
			$('.rem5').remove();
			$("input[name='lose_integral']").css('border-color','red').after('<p class="nul-red rem5">消耗积分未填写</p>');
		}else{
			$('.rem5').remove();
			$("input[name='lose_integral']").css('border-color','#ccc');
		};
	});
	//参与送积分	
	$("input[name='send_integral']").blur(function(){ 
		if($("input[name='send_integral']").val()==""){
			$('.rem6').remove();
			$("input[name='send_integral']").css('border-color','red').after('<p class="nul-red rem6">参与送积分未填写</p>');
		}else{
			$('.rem6').remove();
			$("input[name='send_integral']").css('border-color','#ccc');
		};
	});
	//中奖概率
	$("input[name='probability']").blur(function(){ 
		if($("input[name='probability']").val()==""){
			$('.rem7').remove();
			$("input[name='probability']").css('border-color','red');
			$('.z-leve').after('<p class="nul-red rem7">中奖概率未填写</p>');
		}else{
			$('.rem7').remove();
			$("input[name='probability']").css('border-color','#ccc');
		};
	});
	//奖品详情
	var t_index =$('.type1').find("input[type='radio']:checked").val();
	$('.type1').find("input[type='radio']").change(function(){
		t_index = $(this).val();		
	})
	$('.fir-con').blur(function(){
		if($('.prize-content-set1 .points-st'+t_index).find('.fir-con').val()==""){
			$('.rem8').remove();
			$('.prize-content-set1 .points-st'+t_index).find('.fir-con').css('border-color','red').after('<p class="nul-red rem8">奖品详情未填写</p>');
		}else{
			$('.rem8').remove();
			$('.fir-con').css('border-color','#ccc');
		};
	});
	//奖品详情
	var t_indexa =$('.type2').find("input[type='radio']:checked").val();
	$('.type2').find("input[type='radio']").change(function(){
		t_indexa = $(this).val();		
	})
	$('.sec-con').blur(function(){ 	
		if($('.prize-content-set2 .points-st'+t_indexa).find('.sec-con').val()==""){
			$('.rem9').remove();
			$('.prize-content-set2 .points-st'+t_indexa).find('.sec-con').css('border-color','red').after('<p class="nul-red rem9">奖品详情未填写</p>');
		}else{
			$('.rem9').remove();
			$('.sec-con').css('border-color','#ccc');
		};
	});
	//奖品详情
	var t_indexb =$('.type3').find("input[type='radio']:checked").val();
	$('.type3').find("input[type='radio']").change(function(){
		t_indexb = $(this).val();
	})
	$('.tri-con').blur(function(){ 	
		if($('.prize-content-set3 .points-st'+t_indexb).find('.tri-con').val()==""){
			$('.rem10').remove();
			$('.prize-content-set3 .points-st'+t_indexb).find('.tri-con').css('border-color','red').after('<p class="nul-red rem10">奖品详情未填写</p>');
		}else{
			$('.rem10').remove();
			$('.tri-con').css('border-color','#ccc');
		};
	});

	//奖品数量
	$('.fir-num').blur(function(){ 
		if($('.prize-content-set1 .points-st'+t_index).find('.fir-num').val()==""){
			$('.rem12').remove();
			$('.prize-content-set1 .points-st'+t_index).find('.fir-num').css('border-color','red').next('span').after('<p class="nul-red rem12">奖品数量未填写</p>');
		}else{
			$('.rem12').remove();
			$('.fir-num').css('border-color','#ccc');
		};
	});
	//奖品数量
	$('.sec-num').blur(function(){ 	
		if($('.prize-content-set2 .points-st'+t_indexa).find('.sec-num').val()==""){
			$('.rem13').remove();
			$('.prize-content-set2 .points-st'+t_indexa).find('.sec-num').css('border-color','red').next('span').after('<p class="nul-red rem13">奖品数量未填写</p>');
		}else{
			$('.rem13').remove();
			$('.sec-num').css('border-color','#ccc');
		};
	});
	//奖品数量
	$('.tri-num').blur(function(){ 	
		if($('.prize-content-set3 .points-st'+t_indexb).find('.tri-num').val()==""){
			$('.rem13').remove();
			$('.prize-content-set3 .points-st'+t_indexb).find('.tri-num').css('border-color','red').next('span').after('<p class="nul-red rem13">奖品数量未填写</p>');
		}else{
			$('.rem13').remove();
			$('.tri-num').css('border-color','#ccc');
		};
	});
	
	var n = 0;
    $('.next').click(function(){

    	/*验证分享内容*/
		var share_title = $("input[name='share_title']").val();
        var share_desc = $("textarea[name='share_desc']").val();
        var share_img = $("input[name='share_img']").val();
        if(!((share_title && share_desc && share_img) || (!share_title && !share_desc && !share_img))){//都有内容或者都没内容通过
            if(!share_img && share_title && share_desc){
                tipshow("请填写分享图片","warn");
                return false;
            }
            if(!share_title && share_img && share_desc){
                tipshow("请填写分享标题","warn");
                return false;
            }
            if(!share_desc && share_title && share_img){
                tipshow("请填写分享内容","warn");
                return false;
            }
            if(share_img){
                tipshow("请填写分享标题及内容","warn");
                return false;
            }
            if(share_title){
                tipshow("请填写分享内容及图片","warn");
                return false;
            }
            if(share_desc){
                tipshow("请填写分享标题及图片","warn");
                return false;
            }
        }
//  	验证
		if($("input[name='title']").val()==""){
			$('.rem1').remove();
			$("input[name='title']").css('border-color','red').after('<span class="nul-red rem1">活动名称未填写</span>');
			return false;
		};
		if($(".start_time").val()==""){
			$('.rem2').remove();
			$(".start_time").css('border-color','red').after('<span class="nul-red rem2">开始时间未填写</span>');
			return false;
		}else{
			$('.rem2').remove();
			$(".start_time").css('border-color','#ccc');
			$('.statim').text($(".start_time").val());//开始时间	
		}
		if($(".end_time").val()==""){
			$('.rem3').remove();
			$(".end_time").css('border-color','red').after('<span class="nul-red rem3">结束时间未填写</span>');
			return false;
		}else{
			$('.rem3').remove();
			$(".end_time").css('border-color','#ccc');
			$('.endtim').text($(".end_time").val());//结束时间
		}


		if($(".z-string").val()==""){
			$('.rem4').remove();
			$(".z-string").css('border-color','red').after('<span class="nul-red rem4">活动说明未填写</span>');
			return false;
		};
		if(n==1){
			if($("input[name='lose_integral']").val()==""){
				$('.rem5').remove();
				$("input[name='lose_integral']").css('border-color','red').after('<span class="nul-red rem5">消耗积分未填写</span>');
				return false;
			};
			if($("input[name='send_integral']").val()==""){
				$('.rem6').remove();
				$("input[name='send_integral']").css('border-color','red').after('<span class="nul-red rem6">参与送积分未填写</span>');
				return false;
			};			
		}
		if(n==2){
			if($("input[name='probability']").val()==""){
				$('.rem7').remove();
				$("input[name='probability']").css('border-color','red').after('<span class="nul-red rem7">中奖概率未填写</span>');
				return false;
			};
			if($('.prize-content-set1 .points-st'+t_index).find('.fir-con').val()==""){
				tipshow("请填写一等奖，二等奖，三等奖的赠送积分","warn");
				$('.rem8').remove();
				$('.prize-content-set1 .points-st'+t_index).find('.fir-con').css('border-color','red').after('<p class="nul-red rem8">奖品详情未填写</p>');
				return false;
			}
			if($('.prize-content-set2 .points-st'+t_indexa).find('.sec-con').val()==""){
				tipshow("请填写一等奖，二等奖，三等奖的赠送积分","warn");
				$('.rem9').remove();
				$('.prize-content-set2 .points-st'+t_indexa).find('.sec-con').css('border-color','red').after('<p class="nul-red rem9">奖品详情未填写</p>');
				return false;
			};
			if($('.prize-content-set3 .points-st'+t_indexb).find('.tri-con').val()==""){
				tipshow("请填写一等奖，二等奖，三等奖的赠送积分","warn");
				$('.rem10').remove();
				$('.prize-content-set3 .points-st'+t_indexb).find('.tri-con').css('border-color','red').after('<p class="nul-red rem10">奖品详情未填写</p>');
				return false;
			};
			if($('.prize-content-set1 .points-st'+t_index).find('.fir-num').val()==""){
				$('.rem12').remove();
				$('.prize-content-set1 .points-st'+t_index).find('.fir-num').css('border-color','red').next('span').after('<p class="nul-red rem12">奖品数量未填写</p>');
				return false;
			};
			if($('.prize-content-set2 .points-st'+t_indexa).find('.sec-num').val()==""){
				$('.rem13').remove();
				$('.prize-content-set2 .points-st'+t_indexa).find('.sec-num').css('border-color','red').next('span').after('<p class="nul-red rem13">奖品数量未填写</p>');
				return false;
			};
			if($('.prize-content-set3 .points-st'+t_indexb).find('.tri-num').val()==""){
				$('.rem14').remove();
				$('.prize-content-set3 .points-st'+t_indexb).find('.tri-num').css('border-color','red').next('span').after('<p class="nul-red rem14">奖品数量未填写</p>');
				return false;
			};
//			if($("input[name='image_url']").eq(index).val()==""){
//				alert("未选择上传图片");
//				return false;
//			}			
		}
		
		$('.statim').html($(".start_time").val());//开始时间
		$('.endtim').html($(".end_time").val());//结束时间
		$('.miaoshu').html($(".z-string").val());//活动描述
   		n ++;
        $(".steps").addClass("hide").siblings(".step_"+(n+1)).removeClass("hide");
        $(".app-actions .prev").removeClass("hide");
        $(".step li:eq("+n+")").addClass("active");        
       	if (n==3) {
       		n = 0;
       		$(".prev, .next").addClass("hide").siblings(".reset, .sure").removeClass("hide");//显示确认修改按钮
       		$('.next').addClass('pri-sure');//设置第三步点击保存提交按钮
       	}else{
       		$('.next').removeClass('pri-sure');
       	}
    });
    
    //修改
   	$(".reset").click(function(){
   		n =0;
   		$(".steps").addClass("hide").siblings(".step_"+(n+1)+"").removeClass("hide");
        $(".prev, .reset, .sure").addClass("hide").siblings(".next").removeClass("hide");
        $(".step li").removeClass("active");
        $(".step li:eq("+n+")").addClass("active");
   	})
   	
   	//上一步
	$(".prev").click(function(){
		n --;
        $(".steps").addClass("hide").siblings(".step_"+(n+1)+"").removeClass("hide");
        $(".app-actions .prev").addClass("hide");
        $(".step li:eq("+(n+1)+")").removeClass("active");
        if (n==1) {
        	$(".app-actions .prev").removeClass("hide");
        }
	})	
	
	//上传图片
	$(".js-upload-image").click(function(){		 
		imgCommona(1,index);
		layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['860px', '660px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
	     /**
	     * 图片选择后的回调函数
	     */
	    selImgCallBack = function(resultSrc){   
	        if(resultSrc.length>0){
	        	if(resultSrc[0]['imgSrc']){
	            	var resultSrc = resultSrc[0]['imgSrc'];
	        	}else{
	        		var resultSrc = resultSrc[0]
	        	}	
			    $("input[name='image_url']").eq(index).val(resultSrc)
			    $('.image-display').eq(index).css('background-image','url("'+resultSrc+'")')  
	        } 
	    }
	})
	
	//清空图片
	$('.js-clear-prize-image').click(function(){
		$("input[name='image_url']").eq(index).val("");
   		$('.image-display').eq(index).css('background-image','url("")');
	});
	
	//第三步点击下一步进行保存提交
	$('body').on('click','.pri-sure',function(){
		
		var title = $('.z-title').val();//活动名称
		var start_time = $('.start_time').val();//开始时间
		var end_time = $('.end_time').val();//结束时间
		var descr = $('.z-descr').val();//活动描述				
		var reduce_integra = $('.z-reduce').val();//消耗积分					
		var send_integra = $('.z-isall').val();//送积分数量	
		rule = $(".z-rule input[type='radio']:checked").val();
		times = $(".z-rule input[type='radio']:checked+.z-flolef+.lim-times").val();			
		var rate = $('.z-rate').val();//中奖概率	
		var card_id = '';//目标用户
		if(condit==1){
			card_id = $('.card-data').val().join(',');//目标用户
		}
		var prize =[{},{},{}];//奖项内容
//		一等奖
		prize[0].id=id_1;
		prize[0].grade=1;
		var t_index =$('.type1').find("input[type='radio']:checked").val();
		prize[0].type = t_index;
		prize[0].content = $('.prize-content-set1 .points-st'+t_index).find('.fir-con').val();
		prize[0].num = $('.prize-content-set1 .points-st'+t_index).find('.fir-num').val();
		prize[0].method = $(".shuoming1").val();
		prize[0].img = $(".fir-img").val().replace(_host,'');		
//		二等奖
		prize[1].id=id_2;
		prize[1].grade=2;
		var t_indexb = $('.type2').find("input[type='radio']:checked").val();
		prize[1].type= t_indexb;
		prize[1].content = $('.prize-content-set2 .points-st'+t_indexb).find('.sec-con').val();
		prize[1].num = $('.prize-content-set2 .points-st'+t_indexb).find('.sec-num').val();
		prize[1].method = $(".shuoming2").val();
		prize[1].img = $(".sec-img").val().replace(_host,'');
//		三等奖
		prize[2].id=id_3;
		prize[2].grade=3;
		var t_indexc = $('.type3').find("input[type='radio']:checked").val();
		prize[2].type= t_indexc;
		prize[2].content = $('.prize-content-set3 .points-st'+t_indexc).find('.tri-con').val();
		prize[2].num = $('.prize-content-set3 .points-st'+t_indexc).find('.tri-num').val();
		prize[2].method = $(".shuoming3").val();
		prize[2].img = $(".tri-img").val().replace(_host,''); 

//		发送数据
		var data = {
			id:id,
			title:title,
			start_time:start_time,
			end_time:end_time,
			descr:descr,
			condit:condit,
			reduce_integra:reduce_integra,
			is_send_all:is_send_all,
			send_integra:send_integra,
			rule:rule,
			rate:rate,
			prize:prize,
			card_id:card_id,
			times:times,
			share_title:$("input[name='share_title']").val(),
			share_desc:$("textarea[name='share_desc']").val(),
			share_img:$("input[name='share_img']").val(),
			pids:$('.find-con').val()
		}	
		$.ajax({
			url:"/merchants/marketing/saveWheel",
			type:"POST",
			data:data,		
			dataType:'json',
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			success:function(res){
				if(res.status==0){
					tipshow(res.info);
					$('.success-title').html("活动未创建成功，请重新填写");
					$('.info_tip').css('background','red');
					return false;
				}				
				if(res.status==1){
					$('.success-title').html("你已成功创建该活动！");
					$('.info_tip').css('background','rgb(69,177,130)');
					$('.statim').text(start_time);
					$('.endtim').text(end_time);
					$('.miaoshu').html(descr);				
					//访问显示二维码
					var url = wheel_url+"shop/activity/wheel/"+wid+"/"+res.data;
					$('.cop-int').val(url);
					$.ajax({
						type:"get",
						url:" /merchants/marketing/getCode?url="+url,
						async:true,
						success:function(data){
							$('.code-aj').html(data);
						},
						error:function(){
							alert("数据访问错误")
						}
					});
				};			
			},
			error:function(res){
				alert("数据访问错误");
			}
		});
	});
	
	//复制链接
    $('body').on('click','.cop-btn',function(e){
        e.stopPropagation();//组织事件冒泡
        var obj = $(this).siblings('.cop-int');  //获取同胞元素
        copyToClipboard( obj );
        tipshow('复制成功','info');
    });
})
var index = 0;
/**
 奖品图片调用方法
 */
function imgCommona(fileNumLimit,t_index){
	index =t_index;
    layer.open({
        type: 2,
        title:false,
        closeBtn:false, 
        // skin:"layer-tskin", //自定义layer皮肤 
        move: false, //不允许拖动 
        area: ['860px', '660px'], //宽高
        content: '/merchants/order/clearOrder/'+fileNumLimit
    }); 
}
