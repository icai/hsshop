$(function(){
	// 搜索
    $(".times_select,.grouping_select").chosen({
        width:'120px',
        no_results_text: "没有找到",
        allow_single_de: true
    });

    // 自然天
    laydate({
    	elem: '#layer_date1',		// 自然天		
		format: 'YYYY-MM-DD', 		// 分隔符可以任意定义，该例子表示只显示年月
		max: laydate.now(),
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 自然月
	laydate({
    	elem: '#layer_mouth1',		// 自然天		
		format: 'YYYY-MM', 			// 分隔符可以任意定义，该例子表示只显示年月
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 开始时间 
	laydate({
    	elem: '#start_time1',		// 自定义开始时间		
		format: 'YYYY-MM-DD', 		// 分隔符可以任意定义，该例子表示只显示年月
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 结束时间 
	laydate({
    	elem: '#end_time1',			// 自定义 结束时间		
		format: 'YYYY-MM-DD', 		// 分隔符可以任意定义，该例子表示只显示年月
		max: laydate.now(),			//+1代表明天，+2代表后天，以此类推
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 自然天
    laydate({
    	elem: '#layer_date2',		// 自然天		
		format: 'YYYY-MM-DD', 		// 分隔符可以任意定义，该例子表示只显示年月
		max: laydate.now(),
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 自然月
	laydate({
    	elem: '#layer_mouth2',		// 自然天		
		format: 'YYYY-MM', 			// 分隔符可以任意定义，该例子表示只显示年月
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 开始时间 
	laydate({
    	elem: '#start_time2',		// 自定义开始时间		
		format: 'YYYY-MM-DD', 		// 分隔符可以任意定义，该例子表示只显示年月
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	// 结束时间 
	laydate({
    	elem: '#end_time2',			// 自定义 结束时间		
		format: 'YYYY-MM-DD', 		// 分隔符可以任意定义，该例子表示只显示年月
		max: laydate.now(),			//+1代表明天，+2代表后天，以此类推
		choose: function(datas){ 	//选择日期完毕的回调	
		}
	});

	$('.times_select').change(function(){
		$(this).parent().siblings().hide();
		var _this = $(this).parent('.times_items');
		var _val = $(this).val();
		if( _val< 3 ){
			notimeControl( _val , _this );
		}else{
			timeControl( _val , _this );
		}
		
	});
	
	// 刷新
	$('body').on('click','.refres_btn',function(){
		var _this = $(this).parent().siblings('.times_items');
		notimeControl( '0' , _this);
	});

})

/**
 * [notimeControl 没有时间控件函数]
 * @param  {[type]} val [ 选中的参数]
 * @return {[type]}     [无]
 */
function notimeControl( val , obj ){
	var nowTime    = new Date();
	var nowYear    = nowTime.getFullYear(); 		// 年
	var nowMonth   = nowTime.getMonth()+1;			// 月
	var nowDate    = nowTime.getDate();				// 日
	var nowHours   = nowTime.getHours();			// 时
	var nowMinutes = nowTime.getMinutes();			// 分
	var nowSeconds = nowTime.getSeconds(); 		    //秒
	switch( val ){
		case '0': 									// 实时时间 									
			var html = nowYear +'-';  			
				html+= ( nowMonth   < 10 ? ('0'+nowMonth)   : nowMonth   ) + '-';  			
				html+= ( nowDate    < 10 ? ('0'+nowDate)    : nowDate    ) + ' ';	    		
				html+= ( nowHours   < 10 ? ('0'+nowHours)   : nowHours   ) + ':';					// 时
				html+= ( nowMinutes < 10 ? ('0'+nowMinutes) : nowMinutes ) + ':';					// 分
				html+= ( nowSeconds < 10 ? ('0'+nowSeconds) : nowSeconds );			 				// 秒
				html+='<a class="refres_btn blue_38f" href="javascript:void(0);">刷新</a>';			// 刷新
			obj.siblings('.notime_control').show().html('').append( html );break;
			obj.siblings('.notime_control').show().html('').append( html );break;
		case '1':
			var starTime 	= new Date( nowYear,nowMonth-1,nowDate-7);								 // 前七天
			var starYear    = starTime.getFullYear(); 			// 年
			var starMonth   = starTime.getMonth()+1;			// 月
			var starDate    = starTime.getDate();				// 日
			var starHours   = starTime.getHours();				// 时
			var starMinutes = starTime.getMinutes();			// 分
			var starSeconds = starTime.getSeconds(); 		    // 秒
			var html = starYear +'-';  			
				html+= ( starMonth   < 10 ? ('0'+starMonth)   : starMonth   ) + '-';					// 月  			
				html+= ( starDate    < 10 ? ('0'+starDate)    : starDate    ) + ' 至 ';					// 日	    		
				html+= nowYear +'-';																	// 年  			
				html+= ( nowMonth   < 10 ? ('0'+nowMonth)   : nowMonth   ) + '-'; 						// 月						 			
				html+= ( nowDate    < 10 ? ('0'+nowDate)    : nowDate    ) + ' ';						// 日	    		
			obj.siblings('.notime_control').show().html('').append( html );break;
		case '2':
			var starTime 	= new Date( nowYear,nowMonth-1,nowDate-30);								 	// 前30天
			var starYear    = starTime.getFullYear(); 			// 年
			var starMonth   = starTime.getMonth()+1;			// 月
			var starDate    = starTime.getDate();				// 日
			var starHours   = starTime.getHours();				// 时
			var starMinutes = starTime.getMinutes();			// 分
			var starSeconds = starTime.getSeconds(); 		    // 秒
			var html = starYear +'-';  			
				html+= ( starMonth   < 10 ? ('0'+starMonth)   : starMonth   ) + '-';					// 月  			
				html+= ( starDate    < 10 ? ('0'+starDate)    : starDate    ) + ' 至 ';					// 日	    		
				html+= nowYear +'-';																	// 年  			
				html+= ( nowMonth   < 10 ? ('0'+nowMonth)   : nowMonth   ) + '-'; 						// 月						 			
				html+= ( nowDate    < 10 ? ('0'+nowDate)    : nowDate    ) + ' ';						// 日	    		
			obj.siblings('.notime_control').show().html('').append( html );break;
	}
}

function timeControl( val , obj ){
	obj.siblings('.category_search').css('display','-webkit-box');
	var timeControl = obj.siblings('.time_control');
	timeControl.css('display','-webkit-box');
	timeControl.find('.time_wrap').hide();
	switch( val ){
		case '3' : 																	// 自然天
			timeControl.find('.layer_date').show();

			break;
		case '4' : 																	// 自然天	
			timeControl.find('.time_control .time_wrap').hide();
			$('.layer_mouth').show();break;
		case '5' : 																	// 自定义
			$('.layer_idefine').css('display','-webkit-box').find('.time_wrap').show();break;
	}
}
