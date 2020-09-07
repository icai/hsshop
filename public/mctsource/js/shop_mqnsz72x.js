//时间筛选-日期
$('#flow_timeone').datetimepicker({		
    format: 'YYYY-MM-DD',
    dayViewHeaderFormat: 'YYYY 年 MM 月',
    useCurrent: false,
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


/**
* @author hxq
* @desc   获取实时概况接口数据，渲染到页面
* @date   2018-06-22
* @param  
* @return
*
*/
function getData(){
    $.ajax({
		url:pageUrl+'/api/v1/general/realTime',
        type:'get',
        data:{
			wid:wid,
		},
        dataType:'json',
        success:function(res){
            if (res.err_code == 0){
                var nowData = res.data.now,
                    yesData = res.data.yes;
                // 实时概况图表
                var payChart = echarts.init(document.getElementById('charts_real_time'));
                var payOption = {
                    tooltip:{			// 提示框
                        trigger: 'axis',
                        backgroundColor:'#fff',
                        borderColor:'#666',
                        borderRadius:4,
                        borderWidth:'2px',
                        textStyle:'#333',
                        padding: [10,10],
                        axisPointer:{				// 提示线
                            type: 'line',
                            lineStyle: {
                                color: '#ccc',
                                width: 2
                            },
                        },
                        formatter: function(data){
                            var str,item,timer;
                            var marker1 = '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:#3197FA;"></span>';
                            var marker2 = '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:#FF7700;"></span>';
                            if (data.length == 1) {
                                item = data[0];
                                timer = item.dataIndex > 9 ? item.dataIndex : ('0'+item.dataIndex);
                                str = '00:00 - ' + timer + ':59<br />';
                                str += marker1 + '今日：-' + '<br />';
                                str += marker2 + '昨日：' + item.value;
                            } else if (data.length == 2){
                                    var item1 = data[0],item2 = data[1];
                                    timer = item1.dataIndex > 9 ? item1.dataIndex : ('0'+item1.dataIndex);
                                    str = '00:00 - ' + timer + ':59<br />';
                                    str += marker1 + '今日：' + item2.value + '<br />';
                                    str += marker2 + '昨日：' + item1.value;
                            }
                            return str;
                        }
                        
                    },
                    grid: {
                        left: '0%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data:['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23'],
                        axisTick:{
                            show:false
                        },
                        axisLine: {
                            show: !0,
                            lineStyle: {
                                color: "#ccc",
                                width: 1
                            }
                        },
                        axisLabel: {
                            textStyle: {
                                color: "#333"
                            }
                        }
                        
                    },
                    yAxis: {
                        type: 'value',
                        splitLine:{
                            show:false,  
                        },
                        axisLine:{
                            show : false, 
                        },
                        axisTick:{
                            show:false
                        }
                    },
                    series: [
                        {
                            name:'昨日',
                            type:'line',
                            symbol: 'circle',
                            symbolSize: 6,
                            itemStyle: {
                                normal: {
                                    color: "#FF7700",
                                    lineStyle: {
                                        color: "#FF7700"
                                    }
                                }
                            },
                            areaStyle:{
                                normal:{
                                    color: '#f5c7a9'
                                }
                            },
                            smooth: true,
                            data:res.data.yes.hourly
                        },
                        {
                            name:'今日',
                            type:'line',
                            symbol: 'circle',
                            symbolSize: 6,
                            itemStyle: {
                                normal: {
                                    color: "#3197FA",
                                    lineStyle: {
                                        color: "#3197FA"
                                    }
                                }
                            },
                            areaStyle:{
                                normal:{
                                    color: '#beddfb'
                                }
                            },
                            smooth: true,
                            data:res.data.now.hourly
                        },
                    ]
                };
                payChart.setOption(payOption);
                $('.J_pay-amount').text(nowData.pay_amount);
                $('.J_yes-total').text(yesData.pay_amount);
				var itemArr = [
					nowData.uv,
					yesData.uv,
					nowData.pv,
                    yesData.pv,
                    nowData.orderPayCnt,
                    yesData.orderPayCnt,
                    nowData.pay_uv,
                    yesData.pay_uv,
				];
				$('.J_pay-data').each(function(i,e){
					$(this).text(itemArr[i])
				})
            }
		}
	})
}

/**
* @author hxq
* @desc   获取流量看板接口数据，渲染到页面
* @date   2018-06-22
* @param  start:开始时间，end:结束时间
* @return
*
*/
function getFlowData(start,end){
    $.ajax({
		url:pageUrl+'/api/v1/general/data',
        type:'get',
        data:{
            wid:wid,
            beginTime:start,
			endTime:end,
		},
        dataType:'json',
        success:function(res){
            if (res.err_code == 0){
               $('.J_pv-per').text(res.data.data_index.avg_pv);
               var data = res.data.data_conversion;
               var itemArr = [
                    res.data.data_index.rate_last_day,
                    res.data.data_index.rate_last_week,
                    data.lastDayProductToVisit,
                    data.lastWeekProductToVisit,
                    data.lastDayVisitToPay,
                    data.lastWeekVisitToPay
                ];
                $('.J_compare').each(function(i){
                    let dire = '';
                    if (itemArr[i] > 0) {
                        dire = '↑ ' + itemArr[i] + '%';
                        $(this).addClass('up-arrow').removeClass('down-arrow');
                    } else if (itemArr[i] == 0) {
                        dire = '-';
                        $(this).removeClass('up-arrow').removeClass('down-arrow');
                    } else {
                        dire = '↓ ' + itemArr[i] + '%';
                        $(this).addClass('down-arrow').removeClass('up-arrow');
                    }
                    $(this).text(dire);
                })
                var loggerDate = $.map(res.data.log,function(v){
                    return v.date.slice(5);
                })
                var loggerPer = $.map(res.data.log, function(val){
                    return val.pv_per;
                });
                // 人均流量质量图表
                var flowQualityChart = echarts.init(document.getElementById('charts-flow'));
                var flowQualityOption = {
                    tooltip:{			// 提示框
                        trigger: 'axis',
                        backgroundColor:'#fff',
                        borderColor:'#666',
                        borderRadius:4,
                        borderWidth:'2px',
                        textStyle:'#333',
                        padding: [10,10],
                        axisPointer:{				// 提示线
                            type: 'line',
                            lineStyle: {
                                color: '#ccc',
                                width: 2
                            },
                        },
                        
                    },
                    grid: {
                        left: '0%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data:loggerDate,
                        axisTick:{
                            show:false
                        },
                        axisLine: {
                            show: !0,
                            lineStyle: {
                                color: "#ccc",
                                width: 1
                            }
                        },
                        axisLabel: {
                            textStyle: {
                                color: "#333"
                            }
                        }
                        
                    },
                    yAxis: {
                        type: 'value',
                        splitLine:{
                            show:false,  
                        },
                        axisLine:{
                            show : false, 
                        },
                        axisTick:{
                            show:false
                        }
                    },
                    series: [
                        {
                            name:'人均浏览量',
                            type:'line',
                            symbol: 'circle',
                            symbolSize: 6,
                            itemStyle: {
                                normal: {
                                    color: "#3197FA",
                                    lineStyle: {
                                        color: "#3197FA"
                                    }
                                }
                            },
                            smooth: true,
                            areaStyle:{
                                normal:{
                                    color: '#beddfb'
                                }
                            },
                            data:loggerPer
                        }
                    ]
                };
                flowQualityChart.setOption(flowQualityOption);
                // 流量转化 --商品访问转化率 
                var oC1 = document.getElementById('circle-chart-c');
                drawCircle(oC1, res.data.data_conversion.productToVisit, '#3197FA');
                var oC2 = document.getElementById('circle-chart-c2');
                drawCircle(oC2, res.data.data_conversion.visitToPay, '#66C0D8');
            }
		}
	})
}
/**
* @author hxq
* @desc   获取商品看板接口数据，渲染到页面
* @date   2018-06-22
* @param  start:开始时间，end:结束时间
* @return
*
*/
function getProductData(start,end){
    $.ajax({
		url:pageUrl+'/api/v1/general/product',
        type:'get',
        data:{
            wid:wid,
            beginTime:start,
			endTime:end,
		},
        dataType:'json',
        success:function(res){
            if (res.err_code == 0){
                var topSales = res.data.top_sales,
                    topView = res.data.top_view;
                if (topSales.length == 0) {
                    $('.J_top-pay').html('<div style="text-align:center;color:#999;margin-top: 15px;">暂无数据</div>');
                } else {
                    var html = '',item,maxCnt = 0,per,salesCnt;
                    for (var i=0; i<topSales.length; i++) {
                        item = topSales[i];
                        salesCnt = +item.salesCnt;
                        maxCnt = salesCnt > maxCnt ? salesCnt : maxCnt;
                    }
                    for (var i=0; i<topSales.length; i++) {
                        item = topSales[i];
                        salesCnt = +item.salesCnt;
                        per = (parseFloat(salesCnt / maxCnt).toFixed(2)) * 100;
                        html += '<div class="goods-tr"><div class="thead-th"><div class="goods-cell"><img src="' + sourceUrl + item.img + '" class="goods-img">';
                        html +='<a href="/shop/preview/'+wid+'/'+item.id+'" class="goods-name">'+item.title+'</a></div></div>';
                        html += '<div class="thead-th"><div class="goods-cell"><span class="progress-bar-num">' + salesCnt + '</span>';
                        html += '<div class="hot-progress-container"><div class="hot-progress" style="width:'+per+'%"></div></div></div></div>';  
                        html += '<div class="thead-th"><div class="goods-cell">' + item.price + '</div></div></div>';
                    }
                    $('.J_top-pay').html(html);
                }
                if (topView.length == 0) {
                    $('.J_top-view').html('<div style="text-align:center;color:#999;margin-top: 15px;">暂无数据</div>');
                } else {
                    var html='',item,maxCnt = 0,per,pv;
                    for (var i=0; i<topView.length; i++) {
                        item = topView[i];
                        pv = +item.pv;
                        maxCnt = pv > maxCnt ? pv : maxCnt;
                    }
                    for (var i=0; i<topView.length; i++) {
                        item = topView[i];
                        pv = +item.pv;
                        per = (parseFloat(pv / maxCnt).toFixed(2)) * 100;
                        html += '<div class="goods-tr"><div class="thead-th"><div class="goods-cell"><img src="' + sourceUrl + item.img + '" class="goods-img">';
                        html +='<a href="/shop/preview/'+wid+'/'+item.id+'" class="goods-name">'+item.title+'</a></div></div>';
                        html += '<div class="thead-th"><div class="goods-cell"><span class="progress-bar-num">' + pv + '</span>';
                        html += '<div class="hot-progress-container"><div class="hot-progress" style="width:'+per+'%"></div></div></div></div>';  
                        html += '<div class="thead-th"><div class="goods-cell">' + item.rate + '%</div></div></div>';
                    }
                    $('.J_top-view').html(html);
                }
            }
		}
	})
}
var coreChart = echarts.init(document.getElementById('core-index'));
var legendData = ['付款金额','访问-付款转化率','客单价','付款订单数','付款人数','访客数','浏览量'];
var coreOption,seriesData;
var yA = [
    {
        type: 'value',
        axisLine:{
            show : false, 
        },
        axisTick:{
            show:false
        },
        splitLine: {
            show: !0,
            lineStyle: {
                color: ["#ccc"],
                type: "dotted"
            }
        }
    },
    {
        type: 'value',
        axisLabel:{
            formatter:'{value}%'
        },
        axisLine:{
            show : false, 
        },
        axisTick:{
            show:false
        },
        splitLine: {
            show: !0,
            lineStyle: {
                color: ["#ccc"],
                type: "dotted"
            }
        }
    }  
];
/**
* @author hxq
* @desc   获取核心指标接口数据，渲染到页面
* @date   2018-06-25
* @param  start:开始时间，end:结束时间
* @return
*
*/
function getCoreData(start,end){
    $.ajax({
		url:pageUrl+'/api/v1/order/index',
        type:'get',
        data:{
            wid:wid,
            beginTime:start,
            endTime:end,
            type: 1
		},
        dataType:'json',
        success:function(res){
            if (res.err_code == 0){
                var logger = res.data.log;
                // 日期
                var loggerDate = $.map(logger,function(v){
                    return v.created_at;
                });
                // 付款金额
                var cloggerPayAmount = $.map(logger,function(v){
                    return v.order_payed_amount;
                })
                // 付款人数
                var cloggerPayUser = $.map(logger,function(v){
                    return v.order_payed_user_count;
                })
                // 付款订单数
                var cloggerPayCount = $.map(logger,function(v){
                    return v.order_payed_count;
                })
                // 访问-付款转换率
                var cloggerVisitedPayedRate = $.map(logger,function(v){
                    return v.viewToPaidRate;
                })
                // 客单价
                var cloggerPerPrice = $.map(logger,function(v){
                    return v.per_price;
                })
                 // 浏览量
                 var cloggerPv = $.map(logger,function(v){
                    return v.viewpv;
                })
                 // 访客数
                 var cloggerUv = $.map(logger,function(v){
                    return v.viewuv;
                })
                var arr1 = [
                    res.data.payedAmount,
                    res.data.visitedPayedRate + '%',
                    res.data.payPerUser,
                    res.data.payedOrderCnt,
                    res.data.payedOrderUserCnt,
                    res.data.visitCount,
                    res.data.viewCount,
                ];
                $('.J_core-data').each(function(i){
                    $(this).text(arr1[i])
                })
                var arr2 = [
                    res.data.lastRate.payAmountRate,
                    res.data.lastRate.viewPayedRate,
                    res.data.lastRate.payPerUserRate,
                    res.data.lastRate.payedOrderCnRate,
                    res.data.lastRate.payedUserCnRate,
                    res.data.lastRate.visitRate,
                    res.data.lastRate.viewRate,
                ];
                $('.J_core-compare').each(function(i){
                    let dire = '';
                    if (arr2[i] > 0) {
                        dire = '↑ ' + arr2[i] + '%';
                        $(this).addClass('up-arrow').removeClass('down-arrow');
                    } else if (arr2[i] == 0) {
                        dire = '-';
                        $(this).removeClass('up-arrow').removeClass('down-arrow');
                    } else {
                        dire = '↓ ' + arr2[i] + '%';
                        $(this).addClass('down-arrow').removeClass('up-arrow');
                    }
                    $(this).text(dire);
                })
                seriesData = [
                    {
                        name:'付款金额',
                        type:'line',
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#3388FF",
                                lineStyle: {
                                    color: "#3388FF"
                                }
                            }
                        },
                        smooth: true,
                        data:cloggerPayAmount
                    },
                    {
                        name:'访问-付款转化率',
                        type:'line',
                        symbol: 'circle',
                        yAxisIndex: 1,
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#2FAE44",
                                lineStyle: {
                                    color: "#2FAE44"
                                }
                            }
                        },
                        smooth: true,
                        data:cloggerVisitedPayedRate
                    },
                    {
                        name:'客单价',
                        type:'line',
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#FF4444",
                                lineStyle: {
                                    color: "#FF4444"
                                }
                            }
                        },
                        smooth: true,
                        data:cloggerPerPrice
                    },
                    {
                        name:'付款订单数',
                        type:'line',
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#FF6600",
                            }
                        },
                        smooth: true,
                        data:cloggerPayCount
                    },
                    {
                        name:'付款人数',
                        type:'line',
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#7053B6",
                            }
                        },
                        smooth: true,
                        data:cloggerPayUser
                    },
                    {
                        name:'访客数',
                        type:'line',
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#FFCE55",
                            }
                        },
                        smooth: true,
                        data:cloggerUv
                    },
                    {
                        name:'浏览量',
                        type:'line',
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                color: "#6ED5E6",
                            }
                        },
                        smooth: true,
                        data:cloggerPv
                    }
                ];
                // 核心指标
                coreOption = {
                    title:{
                        text: '最多显示四项指标',
                        textStyle: {
                            color: '#999',
                            fontSize: 13,
                            fontWeight: 'normal'
                            
                        }
                    },
                    tooltip:{			// 提示框
                        trigger: 'axis',
                        backgroundColor:'#fff',
                        borderColor:'#666',
                        borderRadius:4,
                        borderWidth:'2px',
                        textStyle:'#333',
                        padding: [10,10],
                        axisPointer:{				// 提示线
                            type: 'line',
                            lineStyle: {
                                color: '#ccc',
                                width: 2
                            },
                        },
                        
                    },
                    grid: {
                        left: '0%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    legend: {
                        left: 160,
                        data:['付款金额','访问-付款转化率'],
                        textStyle: {
                            color: '#333'
                        },
                        selectedMode: false
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: loggerDate,
                        axisTick:{
                            show:false
                        },
                        axisLine: {
                            show: !0,
                            lineStyle: {
                                color: "#ccc",
                                width: 1
                            }
                        },
                        axisLabel: {
                            textStyle: {
                                color: "#333"
                            }
                        }
                        
                    },
                    yAxis: [
                        {
                            type: 'value',
                            axisLine:{
                                show : false, 
                            },
                            axisTick:{
                                show:false
                            },
                            splitLine: {
                                show: !0,
                                lineStyle: {
                                    color: ["#ccc"],
                                    type: "dotted"
                                }
                            }
                        },
                        {
                            type: 'value',
                            axisLabel:{
                                formatter:'{value}%'
                            },
                            axisLine:{
                                show : false, 
                            },
                            axisTick:{
                                show:false
                            },
                            splitLine: {
                                show: !0,
                                lineStyle: {
                                    color: ["#ccc"],
                                    type: "dotted"
                                }
                            }
                        }  
                    ],
                    series: [
                        {
                            name:'付款金额',
                            type:'line',
                            symbol: 'circle',
                            symbolSize: 6,
                            itemStyle: {
                                normal: {
                                    color: "#3388FF",
                                    lineStyle: {
                                        color: "#3388FF"
                                    }
                                }
                            },
                            smooth: true,
                            data:cloggerPayAmount
                        },
                        {
                            name:'访问-付款转化率',
                            type:'line',
                            symbol: 'circle',
                            yAxisIndex: 1,
                            symbolSize: 6,
                            itemStyle: {
                                normal: {
                                    color: "#2FAE44",
                                    lineStyle: {
                                        color: "#2FAE44"
                                    }
                                }
                            },
                            smooth: true,
                            data:cloggerVisitedPayedRate
                        }
                    ]
                };
                var len = $('.items-select__item--selected').length;
                var dataIndex,showLegend = [], showSeriesData = [], isShowPer = false;
                for (var i=0;i<len;i++){
                    dataIndex = $($('.items-select__item--selected')[i]).data('index');
                    showLegend.push(legendData[dataIndex]);
                    showSeriesData.push(seriesData[dataIndex]);
                    if (dataIndex == 1) {
                        isShowPer = true;
                    }
                }
                if (isShowPer) {
                    coreOption.yAxis = yA;
                } else {
                    coreOption.yAxis = [yA[0]];
                }
                coreOption.legend.data = showLegend;
                coreOption.series = showSeriesData;
                coreChart.clear();
                coreChart.setOption(coreOption);
            }
		}
	})
}
/**
* @author hxq
* @desc   函数调用，对运营视窗图表的更新
* @date   2018-06-25
* @param  start:开始时间，end:结束时间
* @return
*
*/
function transAjax(start,end){
    getFlowData(start,end);
    getProductData(start,end);
    getCoreData(start,end);
}
$(function(){
    getData();
    //时间筛选的自动填充
	var initDate = new Date(new Date().getTime() - 86400000);
	var initDateVal = initDate.toLocaleString().split(' ');
    $('.laydate-icon').eq(0).val(initDateVal[0].replace(/\//g,'-'));
    //dp.change
	$('#flow_timeone').on('dp.change',function(){
		let endDate = $('#flow_timeone').val();
		transAjax(endDate,endDate);
	});
    $(window).on('load',function(){
		var endDate = null;
		endDate = $('#flow_timeone').val();
		transAjax(endDate ,endDate);
    });
    var selectContent = $('.J_items-select__content');
    var isNext = true,transWidth;
    $('.J_items-select__prev').click(function(){
        if (!isNext) {
            selectContent.css('left', 0);
            isNext = true;
        }
    });
    $('.J_items-select__next').click(function(){
        transWidth = $('.items-select__item')[0].offsetWidth * 3;
        if (isNext) {
            selectContent.css('left','-' + transWidth + 'px');
            isNext = false;
        }
    });
    var calIndex = 0;
    selectContent.on('click', '.J_items-select__item', function(){
        $(this).toggleClass('items-select__item--selected');
        var len = $('.items-select__item--selected').length;
        if (len > 4) {
            if ($('.items-select__item--selected')[calIndex] == this) {
                calIndex++;
                if (calIndex >= 3) {
                    calIndex = 0;
                }
            }
           $($('.items-select__item--selected')[calIndex]).removeClass('items-select__item--selected');
           len = $('.items-select__item--selected').length;
        }
        var dataIndex,showLegend = [], showSeriesData = [], isShowPer = false;
        for (var i=0;i<len;i++){
            dataIndex = $($('.items-select__item--selected')[i]).data('index');
            showLegend.push(legendData[dataIndex]);
            showSeriesData.push(seriesData[dataIndex]);
            if (dataIndex == 1) {
                isShowPer = true;
            }
        }
        if (isShowPer) {
            coreOption.yAxis = yA;
        } else {
            coreOption.yAxis = [yA[0]];
        }
        coreOption.legend.data = showLegend;
        coreOption.series = showSeriesData;
        coreChart.clear();
        coreChart.setOption(coreOption);
    });
})


/**
* @author hxq
* @desc   canvas绘制圆环
* @date   2018-06-22
* @param  el:canvas放置区域的dom节点，per:百分比，strokeColor:圆环进度颜色
* @return
*
*/
function drawCircle(el, per, strokeColor){
    var angl;
    var ctx = el.getContext("2d");
    ctx.clearRect(0, 0, el.width, el.height);
    // 　1、先绘制底部完整的环。
    ctx.beginPath();
    ctx.lineWidth = 8; //10px
    ctx.strokeStyle = '#efefef';
    //arc() 方法创建弧/曲线（用于创建圆或部分圆） 
    ctx.arc(90, 90, 80, 0, 2 * Math.PI);
    ctx.stroke();
    ctx.closePath();
    // 　2、绘制根据百分比变动的环。
    ctx.beginPath();
    ctx.lineCap="round";
    ctx.lineWidth = 12;
    ctx.strokeStyle = strokeColor;
    //设置开始处为0点钟方向(-90 * Math.PI / 180)
    //per为百分比值(0-100)
    if (per == 0) {
        angl = 100;
    } else if (per == 100) {
        angl = 0;
    } else {
        angl = per;
    }
    ctx.arc(90, 90, 80, -90 * Math.PI / 180, (360 - angl * 3.6 - 90) * Math.PI / 180, true);  
    ctx.stroke();
    // 3、绘制中间的文字
    ctx.font = '40px Arial';
    ctx.fillStyle = '#333';
    ctx.textBaseline = 'middle';
    ctx.textAlign = 'center';
    ctx.fillText(per+'%', 90, 90);
}


// 客户看板--微信粉丝
// var customerWxChart = echarts.init(document.getElementById('customer-chart-wx'));
// var customerWxOption = {
//     tooltip:{			// 提示框
//         trigger: 'axis',
//         backgroundColor:'#fff',
//         borderColor:'#666',
//         borderRadius:4,
//         borderWidth:'2px',
//         textStyle:'#333',
//         padding: [10,10],
//         axisPointer:{				// 提示线
//             type: 'line',
//             lineStyle: {
//                 color: '#ccc',
//                 width: 2
//             },
//         },
        
//     },
//     grid: {
//         left: '0%',
//         right: '4%',
//         bottom: '3%',
//         containLabel: true
//     },
//     legend: {
//         left: 0,
//         icon: 'rect',
//         itemWidth: 10,
//         itemHeight: 10,
//         data:['累积粉丝数','净增粉丝数','访问粉丝数'],
//         textStyle: {
//             color: '#333'
//         }
//     },
//     xAxis: {
//         type: 'category',
//         boundaryGap: false,
//         data:['05-21','05-21','05-21','05-21','05-21','05-21','05-21','05-21'],
//         axisTick:{
//             show:false
//         },
//         axisLine: {
//             show: !0,
//             lineStyle: {
//                 color: "#ccc",
//                 width: 1
//             }
//         },
//         axisLabel: {
//             textStyle: {
//                 color: "#333"
//             }
//         }
        
//     },
//     yAxis: {
//         type: 'value',
//         splitLine:{
//             show:false,  
//         },
//         axisLine:{
//             show : false, 
//         },
//         axisTick:{
//             show:false
//         }
//     },
//     series: [
//         {
//             name:'累积粉丝数',
//             type:'line',
//             symbol: 'circle',
//             symbolSize: 6,
//             itemStyle: {
//                 normal: {
//                     color: "#3197FA",
//                     lineStyle: {
//                         color: "#3197FA"
//                     }
//                 }
//             },
//             areaStyle:{
//                 normal:{
//                     color: '#beddfb'
//                 }
//             },
//             smooth: true,
//             data:[20,20,20,20,20,30,30,20]
//         },
//         {
//             name:'净增粉丝数',
//             type:'line',
//             symbol: 'circle',
//             symbolSize: 6,
//             itemStyle: {
//                 normal: {
//                     color: "#FF7700",
//                     lineStyle: {
//                         color: "#FF7700"
//                     }
//                 }
//             },
//             areaStyle:{
//                 normal:{
//                     color: '#f5c7a9'
//                 }
//             },
//             smooth: true,
//             data:[0,10,40,24,43]
//         },
//         {
//             name:'访问粉丝数',
//             type:'line',
//             symbol: 'circle',
//             symbolSize: 6,
//             itemStyle: {
//                 normal: {
//                     color: "#29D5AE",
//                     lineStyle: {
//                         color: "#29D5AE"
//                     }
//                 }
//             },
//             areaStyle:{
//                 normal:{
//                     color: '#9fddeb'
//                 }
//             },
//             smooth: true,
//             data:[0,10,5,5,5,5]
//         },
//     ]
// };
// customerWxChart.setOption(customerWxOption);

// 客户看板--店铺会员
// var customerShopChart = echarts.init(document.getElementById('customer-chart-shop'));
// var customerShopOption = {
//     tooltip:{			// 提示框
//         trigger: 'axis',
//         backgroundColor:'#fff',
//         borderColor:'#666',
//         borderRadius:4,
//         borderWidth:'2px',
//         textStyle:'#333',
//         padding: [10,10],
//         axisPointer:{				// 提示线
//             type: 'line',
//             lineStyle: {
//                 color: '#ccc',
//                 width: 2
//             },
//         },
        
//     },
//     grid: {
//         left: '0%',
//         right: '4%',
//         bottom: '3%',
//         containLabel: true
//     },
//     legend: {
//         left: 0,
//         icon: 'rect',
//         itemWidth: 10,
//         itemHeight: 10,
//         data:['累积会员数','新增会员数','成交会员数'],
//         textStyle: {
//             color: '#333'
//         }
//     },
//     xAxis: {
//         type: 'category',
//         boundaryGap: false,
//         data:['05-21','05-21','05-21','05-21','05-21','05-21','05-21','05-21'],
//         axisTick:{
//             show:false
//         },
//         axisLine: {
//             show: !0,
//             lineStyle: {
//                 color: "#ccc",
//                 width: 1
//             }
//         },
//         axisLabel: {
//             textStyle: {
//                 color: "#333"
//             }
//         }
        
//     },
//     yAxis: {
//         type: 'value',
//         splitLine:{
//             show:false,  
//         },
//         axisLine:{
//             show : false, 
//         },
//         axisTick:{
//             show:false
//         }
//     },
//     series: [
//         {
//             name:'累积会员数',
//             type:'line',
//             symbol: 'circle',
//             symbolSize: 6,
//             itemStyle: {
//                 normal: {
//                     color: "#3197FA",
//                     lineStyle: {
//                         color: "#3197FA"
//                     }
//                 }
//             },
//             areaStyle:{
//                 normal:{
//                     color: '#beddfb'
//                 }
//             },
//             smooth: true,
//             data:[20,20,20,20,20,30,30,20]
//         },
//         {
//             name:'新增会员数',
//             type:'line',
//             symbol: 'circle',
//             symbolSize: 6,
//             itemStyle: {
//                 normal: {
//                     color: "#FF7700",
//                     lineStyle: {
//                         color: "#FF7700"
//                     }
//                 }
//             },
//             areaStyle:{
//                 normal:{
//                     color: '#f5c7a9'
//                 }
//             },
//             smooth: true,
//             data:[0,10,40,24,43]
//         },
//         {
//             name:'成交会员数',
//             type:'line',
//             symbol: 'circle',
//             symbolSize: 6,
//             itemStyle: {
//                 normal: {
//                     color: "#29D5AE",
//                     lineStyle: {
//                         color: "#29D5AE"
//                     }
//                 }
//             },
//             areaStyle:{
//                 normal:{
//                     color: '#9fddeb'
//                 }
//             },
//             smooth: true,
//             data:[0,10,5,5,5,5]
//         },
//     ]
// };
// customerShopChart.setOption(customerShopOption);