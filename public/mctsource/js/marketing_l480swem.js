$(function(){
	//(审核信息)开始、结束时时间
	$('#start_time,#end_time').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        dayViewHeaderFormat: 'YYYY 年 MM 月',
        useCurrent: false,
        showClear:true,
        showClose:true,
        showTodayButton:true,
        locale:'zh-cn',
        focusOnShow: true,
        tooltips: {
            today: '今天',
            clear: '清除',
            close: '关闭',
            selectMonth: '选择月',
            prevMonth: '上个月',
            nextMonth: '下一月',
            selectTime: '选择时间',
            selectYear: '选择年',
            prevYear: '上一年',
            nextYear: '下一年',
            selectDecade: '十年一组',
            prevDecade: '前十年',
            nextDecade: '后十年',
            prevCentury: '前一世纪',
            nextCentury: '后一世纪',
        },
        allowInputToggle:true,
    });

    $("#start_time").on("dp.change", function (e) {
        $('#end_time').data("DateTimePicker").minDate(e.date);
    });
    $("#end_time").on("dp.change", function (e) {
        $('#start_time').data("DateTimePicker").maxDate(e.date);
    });

	/* 手动选择时间 */
	// 第几天n天
	$('.data_btn').click(function(){
		$(this).addClass('active').siblings('.data_btn').removeClass('active');
		var dayBefore = $(this).data('day');		// 前n天
		var endTime    = new Date();
		var endYear    = endTime.getFullYear(); 		// 年
		var endMonth   = endTime.getMonth()+1;			// 月
		var endDate    = endTime.getDate();				// 日
		var endHours   = endTime.getHours();			// 时
		var endMinutes = endTime.getMinutes();			// 分
		var endSeconds = endTime.getSeconds(); 		    //秒
		var end = endYear +'-';  			
			end += ( endMonth   < 10 ? ('0'+endMonth)   : endMonth   ) + '-';  			
			end += ( endDate    < 10 ? ('0'+endDate)    : endDate    ) + ' ';	    		
			end += ( endHours   < 10 ? ('0'+endHours)   : endHours   ) + ':';					// 时
			end += ( endMinutes < 10 ? ('0'+endMinutes) : endMinutes ) + ':';					// 分
			end += ( endSeconds < 10 ? ('0'+endSeconds) : endSeconds );			 			// 秒
		var startTime    = new Date( endYear , endMonth-1 , endDate-dayBefore);
		var startYear    = startTime.getFullYear(); 		// 年
		var startMonth   = startTime.getMonth()+1;			// 月
		var startDate    = startTime.getDate();				// 日
		var startHours   = startTime.getHours();			// 时
		var startMinutes = startTime.getMinutes();			// 分
		var startSeconds = startTime.getSeconds(); 		    //秒
		var start = startYear +'-';  			
			start += ( startMonth   < 10 ? ('0'+startMonth)   : startMonth   ) + '-';  			
			start += ( startDate    < 10 ? ('0'+startDate)    : startDate    ) + ' ';	    		
			start += ( startHours   < 10 ? ('0'+startHours)   : startHours   ) + ':';					// 时
			start += ( startMinutes < 10 ? ('0'+startMinutes) : startMinutes ) + ':';					// 分
			start += ( startSeconds < 10 ? ('0'+startSeconds) : startSeconds );			 			// 秒	
		$('#start_date').val( start );	// 前n天
		$('#end_data').val( end );		// 今天
	});

})