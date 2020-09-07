// 基于准备好的dom，初始化echarts图表
var myChart = echarts.init(document.getElementById('echarts')); 
option = {
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['下单笔数','付款订单']
    },
    toolbox: {
        show : true,
    },
    calculable : true,
    xAxis : [
    {
        type : 'category',
        boundaryGap : false,
        data : ['周一','周二','周三','周四','周五','周六','周日'],
        axisLine:{
            show:true,
            lineStyle:{
                color: '#ddd',
            }
        }
    }
    ],
    yAxis : [
    {
        type : 'value',
        min:0,
        max:300,
        axisLine:{
            show:true,
            lineStyle:{
                color: '#ddd',
            }
        }
    }
    ],
    series : [
    {
        name:'下单笔数',
        type:'line',
        stack: '总量',
        data:[120, 132, 101, 134, 90, 230, 210]
    },
    {
        name:'付款订单',
        type:'line',
        stack: '总量',
        data:[220, 182, 191, 234, 290, 330, 310]
    }
    ]
};
// 为echarts对象加载数据 
myChart.setOption(option);