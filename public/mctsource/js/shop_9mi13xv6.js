//时间筛选-日期
$('#flow_timeone,#flow_timethr_1,#flow_timethr_2').datetimepicker({		
    format: 'YYYY-MM-DD',
    dayViewHeaderFormat: 'YYYY 年 MM 月',
    useCurrent: false,
    // showClear:true,
    // showClose:true,
    // showTodayButton:true,
    locale:'zh-cn',
	focusOnShow: true,
	maxDate: new Date(new Date().getTime() - 86400000),
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

//时间筛选-月份
$('#flow_timetwo').datetimepicker({		
    format: 'YYYY-MM',
    dayViewHeaderFormat: 'YYYY 年 MM 月',
    useCurrent: false,
    // showClear:true,
	// showClose:true,
	maxDate: new Date(),
    // showTodayButton:true,
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


function transAjax(start,end,type){
	$.ajax({
		url:pageUrl+'/api/v1/order/index',
        type:'get',
        data:{
			beginTime:start,
			endTime:end,
			wid:wid,
			type:type
		},
        async:true,
        dataType:'json',
        success:function(res){
			$('.order_export').attr({'href':pageUrl+'/api/v1/order/export?beginTime='+start+'&endTime='+end+'&wid='+wid+'&type='+type});
			var logger = res.data.log?res.data.log:res.data.detail;
			// var loggerDate = logger.map(v=>v.created_at?v.created_at:v.date);//日期
			var loggerDate = $.map(logger,function(v){ //日期
				return v.created_at?v.created_at:v.date
			});
			// var loggerPayAmount = logger.map(v=>v.order_payed_amount);//付款金额
			var loggerPayAmount = $.map(logger,function(v){
				return v.order_payed_amount
			})
			// var loggerPayUser = logger.map(v=>v.order_payed_user_count);//付款人数
			var loggerPayUser = $.map(logger,function(v){
				return v.order_payed_user_count
			})
			// var loggerPayGoods = logger.map(v=>v.order_payed_goods_count);//付款件数
			var loggerPayGoods = $.map(logger,function(v){
				return v.order_payed_goods_count
			})
			// var loggerVisitedOrderRate = logger.map(v=>v.order_payed_goods_count);//访问下单转换率
			var loggerVisitedOrderRate = $.map(logger,function(v){
				return v.order_payed_goods_count
			})
			// var loggerVisitedPayedRate = logger.map(v=>v.order_payed_goods_count);//访问付款转换率
			var loggerVisitedPayedRate = $.map(logger,function(v){
				return v.order_payed_goods_count
			})
			// var loggerOrderPayedRate = logger.map(v=>v.order_payed_goods_count);//下单付款转换率
			var loggerOrderPayedRate = $.map(logger,function(v){
				return v.order_payed_goods_count
			})

			//表格填充
			var resData = res.data;
			$('.items_visited_order').text(resData.visitedOrderRate.toFixed(2)+'%');
			$('.items_visited_payed').text(resData.visitedPayedRate.toFixed(2)+'%');
			$('.items_order_payed').text(resData.orderPayedRate.toFixed(2)+'%');
			
			var itemCountArr = [
				resData.visitCount,
				resData.orderUserCnt,
				resData.orderCnt,
				resData.orderAmount,
				resData.payedOrderUserCnt,
				resData.payedOrderCnt,
				resData.payedAmount,
				resData.payedGoodsCnt,
				resData.payPerUser
			];
			$('.items_arr').each(function(i,e){
				$(this).text(itemCountArr[i])
			})
			
			if(type != 3){
				var resPer = resData.lastRate;
				var itemPerArr = [
					resPer.visitRate,
					resPer.orderUserCnRate,
					resPer.orderCnRate,
					resPer.orderAmountRate,
					resPer.payedUserCnRate,
					resPer.payedOrderCnRate,
					resPer.payAmountRate,
					resPer.goodsRate,
					resPer.payPerUserRate
				];
				$('.items_per_arr').each(function(i,e){
					let dire = '';
					if(itemPerArr[i]>0){
						dire = '↑';
						$(this).css({color:'red'})
					}else if(itemPerArr[i]==0){
						dire = '';
						$(this).css({color:'grey'})
					}else{
						dire = '↓';
						$(this).css({color:'green'})
					}
					$(this).text(dire+''+itemPerArr[i].toFixed(2)+'%')
				})
			}
			//图表显示
			var tradeChart = echarts.init(document.getElementById('trade_chart')); 
			var tradeOption = {
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
				legend: {
					data:['付款金额','付款人数','付款件数']//,'访问-下单转换率','访问-付款转化率','下单-付款转化率'
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
						data : loggerDate, 
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
									width: 1,
									type: 'solid'
								}   
							},
							axisLine:{
								show : true, 
							},
							axisTick:{
								show:false
							}
						},
						{
							type: 'value',
							min:0,
							max:100,
							axisLabel:{
								formatter:'{value}%'
							}
						}
					], 	  
				series : [
					{
						name:'付款金额',
						type:'line',
						stack: '总量',
						data:loggerPayAmount,
						smooth: true
					},
					{
						name:'付款人数',
						type:'line',
						stack: '总量',
						data:loggerPayUser,
						smooth: true
					},
					{
						name:'付款件数',
						type:'line',
						stack: '总量',
						data:loggerPayGoods,
						smooth: true
					},
					// {
					// 	name:'访问-下单转换率',
					// 	type:'line',
					// 	stack: '总量',
					// 	yAxisIndex:1,
					// 	data:loggerVisitedOrderRate
					// },
					// {
					// 	name:'访问-付款转化率',
					// 	type:'line',
					// 	stack: '总量',
					// 	yAxisIndex:1,
					// 	data:loggerVisitedPayedRate
					// },
					// {
					// 	name:'下单-付款转化率',
					// 	type:'line',
					// 	stack: '总量',
					// 	yAxisIndex:1,
					// 	data:loggerOrderPayedRate
					// }
				]
			};         
			tradeChart.setOption(tradeOption);
		}
	})

}

$(function(){
	//时间筛选的自动填充
	var initDate = new Date(new Date().getTime() - 86400000);
	var initDateVal = initDate.toLocaleString().split(' ');
	// $('.laydate-icon').eq(0).val(initDateVal[0].split('/').map((v,i)=>v>=10?v:'0'+v).join('-'));
	$('.laydate-icon').eq(0).val(initDateVal[0].replace(/\//g,'-'));
	$('.laydate-icon').eq(1).val(initDateVal[0].replace(/\//g,'-').replace(/(\-\d{1,2})$/g,''));
	$('.laydate-icon').eq(2).val(initDateVal[0].replace(/\//g,'-'));
	$('.laydate-icon').eq(3).val(initDateVal[0].replace(/\//g,'-'));

	//自定义时间筛选拘束器
	$("#flow_timethr_1").on("dp.change", function (e) {
		$('#flow_timethr_2').data("DateTimePicker").minDate(e.date); 
	});
	$("#flow_timethr_2").on("dp.change", function (e) {
		$('#flow_timethr_1').data("DateTimePicker").maxDate(e.date);
	});	

	//selected choice
	$('select.time_select').change(function(){
		var num = $(this).val();
		$('.zent-input').eq(num).removeClass('hidden').siblings('.zent-input').addClass('hidden')

		var startDate = null;
		var endDate = null;
		switch (num){
			case '0':
			$('.items_form').text('较前一天');
			$('.itmes_num').removeClass('custom-selected');
			endDate = $('#flow_timeone').val();
			transAjax(endDate,endDate,1);
			break;
			case '1':
			$('.items_form').text('较前一月');
			$('.itmes_num').removeClass('custom-selected');
			endDate = $('#flow_timetwo').val();	
			transAjax(endDate ,endDate,2);
			break;
			case '2':
			$('.items_gray').text('');
			$('.itmes_num').addClass('custom-selected');
			endDate = $('#flow_timethr_2').val();
			startDate = $('#flow_timethr_1').val();
			transAjax(startDate,endDate,3)
			break;
		}
	})

	//dp.change
	$('#flow_timeone').on('dp.change',function(){
		let endDate = $('#flow_timeone').val();
		transAjax(endDate,endDate,1);
	});
	$('#flow_timetwo').on('dp.change',function(){
		let endDate = $('#flow_timetwo').val();
		transAjax(endDate,endDate,2);
	});
	$('#flow_timethr_2,#flow_timethr_1').on('dp.change',function(){
		let endDate = $('#flow_timethr_2').val();
		let startDate = $('#flow_timethr_1').val();
		transAjax(startDate,endDate,3);
	});

	$(window).on('load',function(){
		var endDate = null;
		endDate = $('#flow_timeone').val();
		transAjax(endDate ,endDate,1);
	})
})