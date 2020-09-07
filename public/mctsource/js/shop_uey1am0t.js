$('#start_time,#end_time').datetimepicker({		
    format: 'YYYY-MM-DD',
    dayViewHeaderFormat: 'YYYY 年 MM 月',
    useCurrent: false,
    // showTodayButton:true,
    // showClose:true,
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


//浏览器加载
$(window).on('load',function(){
    var initDate = new Date(new Date().getTime()-86400000);
    var initDateVal = initDate.toLocaleString().split(' ')[0].replace(/\//g,'-');
    $('#start_time input,#end_time input').val(initDateVal.split('-').map((v,i)=>v>=10?v:'0'+v).join('-'));
    ajaxFunc($('#start_time input').val(),$('#end_time input').val());
    AjaxPagination($('#start_time input').val(),$('#end_time input').val());
})

//拘束器
$("#start_time").on("dp.change", function (e) {
    $('#end_time').data("DateTimePicker").minDate(e.date); 
});
$("#end_time").on("dp.change", function (e) {
    $('#start_time').data("DateTimePicker").maxDate(e.date);
});

//前几天
$('.fastSelect_time').click(function(){
	var beforeDay = $(this).val().replace(/[^0-9]/ig,'').replace(/[^0-9]/ig,'');// 得到前n天
    var fullDay=getdate(beforeDay);
    $('#end_time input').val( fullDay.end_date.substring(0,10));
    $('#start_time input').val( fullDay.start_date.substring(0,10));
});

//时间筛选按钮事件
$('#filter').on('click',function(){
    var startDate = $("#start_time").find("input").val();
    var endDate = $("#end_time").find("input").val();
    ajaxFunc(startDate,endDate);
    AjaxPagination(startDate,endDate);
})

// ajax的封装
function ajaxFunc(start,end,pages){
    $.ajax({
        url:userApp+'/api/v1/merchantsAnalysisPageData',
        type:'get',
        data:{
            beginTime:start,
            endTime:end,
            wid:wid,
            page:pages
        },
        async:true,
        dataType:'json',
        success:function(res){
            //表格显示
            $('.table_data_appear').eq(0).children('dd').html(res.data.overview.uv);
            $('.table_data_appear').eq(1).children('dd').html(res.data.overview.pv);
            $('.table_data_appear').eq(2).children('dd').html(res.data.overview.visitProductUv);
            $('.table_data_appear').eq(3).children('dd').html(res.data.overview.visitProductPv);

            //饼状图
            let page_data = res.data.page_type.map((v)=>{return v.name});
            let page_table = res.data.page_type.map((v)=>{return {value:v.value,name:v.name}})
            var pieData = echarts.init(document.getElementById('pie_data'));
            option = {
                noDataLoadingOption:
                {
                    text: '暂无数据',
                    effect: 'bubble',
                    effectOption:
                    {
                        effect:
                        {
                            n: 0
                        }
                    }
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    data:page_data
                },
                series: [
                    {
                        name:'访问来源',
                        type:'pie',
                        radius: ['30%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: true,
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data:page_table
                    }
                ]
            };
            pieData.setOption(option);
        },
        error:function(){
            
        }
    })
}


 function AjaxPagination(start,end,pages){
    $.ajax({
        url:userApp+'/api/v1/pageList',
        type:'get',
        data:{
            beginTime:start,
            endTime:end,
            wid:wid,
            page:pages
        },
        async:true,
        dataType:'json',
        success:function(res){
            //单页面流量数据
            let list = '';
            let listData = res.data.data;
            for(let i=0,l=listData.length;i<l;i++){
                list+='<ul class="tatilMsg"><li><a href="'+listData[i].url+'">'+listData[i].name+'</a></li><li>'+listData[i].viewpv+'</li><li>'+listData[i].viewuv+'</li></ul>';
            }
            $('#table_manage').html(list);

            //分页
            let page = res.data.pages;
            let pageHtml = '';

            for(var i=0,l=page;i<l;i++){
                pageHtml+='<li page='+(i+1)+' class="pages_item"><a class="btn" href="javascript:;">'+(i+1)+'</a></li>';
            }
            // pageHtml+='<li class="right_page"><a class="btn" href="javascript:;" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
            $(".pagination").html(pageHtml);

            //分页点击事件
            $('li.pages_item').on('click',function(){
                $(this).addClass('active').siblings('li').removeClass('active');
                AjaxPagination(start,end,$(this).attr('page'));
            })
        }
    })
}