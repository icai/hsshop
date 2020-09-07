$('#date_start_time,#date_end_time').datetimepicker({		
    format: 'YYYY-MM-DD',
    dayViewHeaderFormat: 'YYYY 年 MM 月',
    useCurrent: false,
    // showClear:true,
    // showClose:true,
    // showTodayButton:true,
    locale:'zh-cn',
    maxDate: new Date(new Date().getTime() - 86400000),
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

//ajax封装
function ajaxDaily(start,end){
	$.ajax({
		url:`${userApp}/api/v1/merchantsAnalysisPerPv`,
        type:'get',
		async:true,
		data:{
            beginTime:start,
            endTime:end,
            wid:wid
        },
		dataType:'json',
		success:function(res){
			let dailyDate = res.data.map(v=>v.date); //筛选出的时间
			let dailyUv = res.data.map(v=>parseInt(v.uv));//访客数
			let dailyPv = res.data.map(v=>parseInt(v.pv));//浏览量
			let dailyPuv = res.data.map(v=>parseInt(v.puv));//商品访客量
			let dailyPpv = res.data.map(v=>parseInt(v.ppv));//商品浏览量
			console.log(dailyPpv);
			var myChart = echarts.init(document.getElementById('data_chart')); 
			var option = {
				polar:{
					axisLine:{show:true},
					center:['50%', '50%'],
					boundaryGap	:['20px', '50px'],
					name:{
							show: true,
							formatter: null,
							textStyle: {
								color:'#f00'
							}
						} 
				},
				tooltip:{			// 提示框
					trigger: 'axis',
					backgroundColor:'#fff',
					borderColor:'#515151',
					borderRadius:4,
					borderWidth:'1px',
					textStyle:'#000',
					axisPointer:{				// 提示线
						type: 'line',
						lineStyle: {
							color: '#c5daea',
							width: 2,
							type: 'dotted'
						},
					}     
				},
				grid:{							// 边框
					borderWidth:0,
				},
				legend: {
					data:['浏览量','访客数','商品浏览量','商品访客数']
				},
				toolbox: {			// 工具箱
					show : false,
					feature : {
						mark : {show: true},
						dataView : {show: true, readOnly: false},
						magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
						restore : {show: true},
						saveAsImage : {show: true}
					}
				},
				calculable : true,
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data : dailyDate,   
						splitLine:{
							show:false,
						},
						axisLine:{
							show:false,
						},
						axisTick:{
								show:false
							},
						}
				],
				yAxis : [
						{	
							type : 'value',
							splitLine:{
								show:true,
								lineStyle:{
									color: ['#ccc'],
									width: 2,
									type: 'dotted'
								}   
							},
							axisLine:{
								show : false, 
							},
							axisTick:{
								show:false
							}
						}
					], 	  
				series : [
					{
						name:'浏览量',
						type:'line',
						stack: '总量',
						data:dailyPv, 
					},
					{
						name:'访客数',
						type:'line',
						stack: '总量',
						data:dailyUv
					},
					{
						name:'商品浏览量',
						type:'line',
						stack: '总量',
						data:dailyPpv
					},
					{
						name:'商品访客数',
						type:'line',
						stack: '总量',
						data:dailyPuv
					}
				]
			};         
			myChart.setOption(option);
			//详细数据表单填充
			let listData = res.data;
			let list = '';
			for(let i=0,l=res.data.length;i<l;i++){
				list+=`<ul class="tatilMsg"><li>${listData[i].date}</li><li>${listData[i].pv}</li><li>${listData[i].uv}</li><li>${listData[i].ppv}</li><li>${listData[i].puv}</li></ul>`
			}

			list+='</ul>'
			$('.daily_table_content').html(list)
		}
	})
}

//拘束器
$("#date_start_time").on("dp.change", function (e) {
    $('#date_end_time').data("DateTimePicker").minDate(e.date); 
});
$("#date_end_time").on("dp.change", function (e) {
    $('#date_start_time').data("DateTimePicker").maxDate(e.date);
});

//前几天
$('.fastSelect_time').click(function(){
	var beforeDay = $(this).val().replace(/[^0-9]/ig,'').replace(/[^0-9]/ig,'');// 得到前n天
    var fullDay=getdate(beforeDay);
    $('#date_end_time input').val( fullDay.end_date.substring(0,10));
    $('#date_start_time input').val( fullDay.start_date.substring(0,10));
});

//时间筛选按钮事件
$('#filter').on('click',function(){
    var startDate = $("#date_start_time").find("input").val();
    var endDate = $("#date_end_time").find("input").val();
    ajaxDaily(startDate,endDate)
})

//页面加载
$(window).on('load',function(){
	var initDate = new Date(new Date().getTime() - 86400000);
    var initDateVal = initDate.toLocaleString().split(' ');
    $('#date_start_time input,#date_end_time input').val(initDateVal[0].split('/').map((v,i)=>v>=10?v:'0'+v).join('-'));
	ajaxDaily($('#date_start_time input').val(),$('#date_end_time input').val());
})