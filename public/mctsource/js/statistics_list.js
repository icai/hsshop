"use strict";  //严格模式
//文档加载完成

$(function(){ 
    var year = '2017';
    var month = '10';
    setYear(2010,2080);
    setMonth();
    getDataInfo(year,month);
});

//设置年份
function setYear(stratTime,endTime){
    var _html = "";
    var year = (new Date()).getFullYear();
    for(var i=stratTime;i<=endTime;i++){
        if(i==year)
            _html+='<option value="'+i+'" selected="selected">'+i+'</option>';
        else
            _html+='<option value="'+i+'">'+i+'</option>'; 
    }
    $("#year").html(_html);
}

//设置月份
function setMonth(){
    var month = (new Date()).getMonth()+1;
    $("#month").val(month);
}

//月份选择触发
$("#month").change(function(){
    var year = $("#year").val();
    var month = $("#month").val();
    getDataInfo(year,month);
});

$("#year").change(function(){
    var year = $("#year").val();
    var month = $("#month").val();
    getDataInfo(year,month);
});

function getDataInfo(year,month){
    hstool.load();
    $.ajax({
        url:'/merchants/microforum/statistics/list',
        type:'get',
        data:{year:year,month:month},
        dataType:"json",
        success:function(res){
            console.log(res);
            if(res.status==1){
                var _date = res.data.statisticsCounts.date; 
                var _active = res.data.statisticsCounts.active;
                var _delete = res.data.statisticsCounts.delete;
                var _release = res.data.statisticsCounts.release; 
                drawGraphs(_date,_release,_delete,_active);
            }else{
                drawGraphs([],[],[],[]);
            }
            hstool.closeLoad();
        },
        error:function(){
            hstool.closeLoad(); 
        }
    })
}

// 基于准备好的dom，初始化echarts图表 
var myChart = null;  
function drawGraphs(_date,_release,_delete,_active){
    if(myChart) {
        myChart.dispose();
    }
    myChart = echarts.init(document.getElementById('echarts'));
    var option = { 
        tooltip : {
            trigger: 'axis'
        },
        legend: {
            x:"center",
            y:"bottom",
            data:['发帖','删贴',"活跃度"]
        },
        // grid: {
        //     left: '3%',
        //     right: '4%',
        //     bottom: '3%',
        //     containLabel: true
        // }, 
        toolbox: {
            show : true
        },
        calculable : true,
        xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : _date,
            axisLine:{
                show:true,
                lineStyle:{
                    color: '#999',
                }
            }
        }
        ],
        yAxis : [
        {
            type : 'value',
            // min:0,
            // max:300,
            axisLine:{
                show:true,
                lineStyle:{
                    color: '#999',
                }
            }
        }
        ],
        series : [
            {
                name:'发帖',
                type:'line',
                // stack: '总量',
                data:_release
            },
            {
                name:'删贴',
                type:'line',
                // stack: '总量',
                data:_delete
            },
            {
                name:'活跃度',
                type:'line',
                // stack: '总量',
                data:_active
            }
        ]
    };
    // 为echarts对象加载数据 
    myChart.setOption(option,true);
}
