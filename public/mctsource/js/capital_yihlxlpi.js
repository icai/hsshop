$(function() {
    // 提交
    $('.js-submit').click(function() {
        var val = parseFloat($('.js_money').val()).toFixed(2);
        var reg = /^\d+(\.\d{2})?$/;
        if (reg.test(val)) {
            $('.js-pay li').eq(0).click();
            $('#payModal').modal('show');
            $('#proveModal').modal('hide');
            $('.recharge_money').text(val);
        } else {
            tipshow('充值金额必须是数字且不能为空', 'wran');
        }
    });
    // 上传凭证
    $('.js_upload').click(function() {
        $('#proveModal').modal('show');
        $('#payModal').modal('hide');
    });
    // 支付导航
    $('.js-pay li').click(function() {
        $(this).addClass('active').siblings('li').removeClass('active');
        var _cls = $(this).data('class');
        $('.' + _cls).removeClass('hidden').siblings('.recharge_wrap').addClass('hidden');
    });
    //开始、结束时时间
    $('#start_time').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        dayViewHeaderFormat: 'YYYY 年 MM 月',
        useCurrent: false,
        showClear: true,
        showClose: true,
        showTodayButton: true,
        locale: 'zh-cn',
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
        allowInputToggle: true,
    });
})