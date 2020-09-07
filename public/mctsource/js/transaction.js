laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
var start = {
    elem: '#startDate',
    format: 'YYYY-MM-DD hh:mm:ss',
    min: '2009-06-16 23:59:59', //设定最小日期为当前日期
    max: '2099-06-16 23:59:59', //最大日期
    event: 'focus',
    istime: true,
    istoday: false,
    choose: function(datas){
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas //将结束日的初始值设定为开始日
    }
};
var end = {
    elem: '#endDate',
    format: 'YYYY-MM-DD hh:mm:ss',
    min: '2009-06-16 23:59:59',
    max: '2099-06-16 23:59:59',
    event: 'focus',
    istime: true,
    istoday: false,
    choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
    }
};
laydate(start);
laydate(end);
$('.date-quick-pick').click(function(){
    var date = $(this).data('days');
    var data = getdate(date);
    $('#startDate').val(data.start_date);
    $('#endDate').val(data.end_date);
})