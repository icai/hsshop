
//主体左侧列表高度控制
$('.left_nav').height($('.content').height());

var myChart = echarts.init(document.getElementById('echarts'));
    option = {
	    
	    tooltip: {
	        trigger: 'axis'
	    },
	    legend: {
	        data:['浏览PV','浏览UV','到店PV','到店UV']
	    },
	    grid: {
	        left: '3%',
	        right: '4%',
	        bottom: '3%',
	        containLabel: true
	    },
	    toolbox: {
	        feature: {
	            saveAsImage: {}
	        }
	    },
	    xAxis: {
	        type: 'category',
	        boundaryGap: false,
	        data: ['2016-11-16','2016-11-17','2016-11-18','2016-11-19','2016-11-20','2011-11-21','2011-11-21']
	    },
	    yAxis: {
	        type: 'value'
	    },
	    series: [
	        {
	            name:'浏览PV',
	            type:'line',
	            stack: '总量',
	            data:[120, 132, 101, 134, 90, 230, 210]
	        },
	        {
	            name:'浏览UV',
	            type:'line',
	            stack: '总量',
	            data:[220, 182, 191, 234, 290, 330, 310]
	        },
	        {
	            name:'到店PV',
	            type:'line',
	            stack: '总量',
	            data:[150, 232, 201, 154, 190, 330, 410]
	        },
	        {
	            name:'到店UV',
	            type:'line',
	            stack: '总量',
	            data:[320, 332, 301, 334, 390, 330, 320]
	        },
	        
	    ]
	};
    myChart.setOption(option);