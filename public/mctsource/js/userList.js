$(function(){
	/*预约详情*/
	$(".details").on('click',function(){
		var enroll_id = $(this).parents('.t_content_con').data('id');
		var vote_id = $(this).parents('.t_content_con').data('voteid');
		window.location.href='/merchants/wechat/bookDetail?enroll_id=' + enroll_id + '&vote_id=' + vote_id
	})

	
	 // 预约日期
    $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
        showClose: true,
        showTodayButton: true,
        locale: 'zh-cn',
        focusOnShow: false,
        useCurrent: false,
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
        allowInputToggle: true,
    });
    if(start_at){
        $("#datetimepicker1").val(start_at);
    }
    
    //序号全选全不选 
	$("#cb_all").click(function(){
	    var that = this;
	    $("input[name='cb_order']").each(function(index,obj){
	        obj.checked = that.checked;
	    });
	});
    
})
