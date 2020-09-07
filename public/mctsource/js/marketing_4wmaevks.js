$(function(){
	//(审核信息)开始、结束时时间
	$('#start_day,#end_day').datetimepicker({
        format: 'YYYY-MM-DD',
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

    $("#start_day").on("dp.change", function (e) {
        $('#end_day').data("DateTimePicker").minDate(e.date);
    });
    $("#end_day").on("dp.change", function (e) {
        $('#start_day').data("DateTimePicker").maxDate(e.date);
    });
})