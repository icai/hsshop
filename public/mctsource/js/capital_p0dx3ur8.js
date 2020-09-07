$(function(){
     // 帮助弹框
    var _nodeHtml = '<div class="mgb30">';
        _nodeHtml += '<p class="mgb10">结算方式：</p>';
        _nodeHtml += '<ul class="f12">';
        _nodeHtml += '<li>［担保交易］结算时间＝发货后T+7天自然日或顾客确认收货时间</li>';
        _nodeHtml += '<li>［快速结算］结算时间＝发货时间（适用已缴纳保证金商户＝》<a class="blue_38f" href="保证金.html">查看</a>）</li>';
        _nodeHtml += '</ul>';
        _nodeHtml += '</div>';
        _nodeHtml += '<div class="mgb30">';
        _nodeHtml += '<ul class="f12">';
        _nodeHtml += '<li>ps：分销订单仅以担保交易方式结算</li>';
        _nodeHtml += '</ul>';
        _nodeHtml += '</div>'
    $(".note_tip").popover({
        html :true,                                 // 是否转义为html标签
        container:'body',                           // 依靠的元素
        placement:'bottom',                          // 箭头方向
        trigger:'click',                            // 事件
        content:_nodeHtml,                          // 主体内容
    });
    // 银行信息
    
    $('.reflect_wrap').click(function(){
        var flag = $(this).data('flag');
        if(flag){
            $(this).find('.note_tips').popover('show');
            $(this).data('flag',false);
        }else{
            $(this).find('.note_tips').popover('hide');
            $(this).data('flag',true);
        }
       
    });
	//开始、结束时时间
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
        $('.fastSelect_time').removeClass('hover');
    });
    $("#end_time").on("dp.change", function (e) {
        $('#start_time').data("DateTimePicker").maxDate(e.date);
        $('.fastSelect_time').removeClass('hover');
    });
    // 前n天
	$('.fastSelect_time').click(function(){
		$(this).addClass('hover').siblings('.fastSelect_time').removeClass('hover');	// 添加对应的样式
		var beforeDay = parseInt( $(this).data('day') );// 得到前n天
		var day =fun_time( beforeDay);
		$('#start_time input').val( day[0].substr(0,10) +' 00:00:00');	// 前七天
		$('#end_time input').val( day[1].substr(0,10)+' 23:59:59' );		// 今天
	});

})