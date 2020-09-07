$(function(){
	$("[data-toggle='tooltip']").tooltip();
	$('#flow_timeone').val(getNowFormatDate());
	//流量统计图标方法
	var myChart = ''; 
	//访问来源图标方法
	var myChart_sec = '';
	var option_sec = '';
	//流量统计坐标
	var flow_xAxis = [];//flow横轴
	var flow_pv = [];//浏览量
	var flow_uv = [];//访客数
	var flow_new_uv = [];//新访客数
	var flow_visit_depth = [];//平均访问深度
	var flow_stay_time_uv = [];//人均停留时长
	var flow_series_new = [];//流量统计来源
	var flow_data = ['浏览量','访客数'];//页面加载显示两项流量统计来源
	var flow_series = [];//控制器显示浏览量访客数数据
	var visit_distribution_series = [];//访问来源
	//获取当前时间
	function getNowFormatDate() {
	    var date = new Date();
	    var seperator1 = "-";
	    var seperator2 = ":";
	    var month = date.getMonth() + 1;
	    var strDate = date.getDate()-1;
	    if (month >= 1 && month <= 9) {
	        month = "0" + month;
	    }
	    if (strDate >= 0 && strDate <= 9) {
	        strDate = "0" + strDate;
	    }
	    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
//	            + " " + date.getHours() + seperator2 + date.getMinutes()
//	            + seperator2 + date.getSeconds();
	    return currentdate;
	}
	//删除数组指定元素
	Array.prototype.indexOf = function(val) {//获取索引
		for (var i = 0; i < this.length; i++) {
			if (this[i] == val) return i;
		}
		return -1;
	};
	Array.prototype.remove = function(val) {
		var index = this.indexOf(val);
		if (index > -1) {
			this.splice(index, 1);
		}
	};
	
	//获取数据昨日概况
	$.ajax({
		type:"get",
		url:"/merchants/xcx/stat/overview",
		success:function(res){
			if(res.errCode == 0){
				//昨日概况
				//付款金额
				$('.pay_amount').html(res.data.pay_amount.value);
				if(parseFloat(res.data.pay_amount.growth)>0){
					$('.pay_amount_growth_col').addClass('z_colred');
				}else if(parseFloat(res.data.pay_amount.growth)<0){
					$('.pay_amount_growth_col').addClass('z_col4ab');
				}
				$('.pay_amount_growth').text(res.data.pay_amount.growth);
				
				//浏览量
				$('.pv').html(res.data.pv.value);
				if(parseFloat(res.data.pv.growth)>0){
					$('.pv_growth_col').addClass('z_colred');
				}else if(parseFloat(res.data.pv.growth)<0){
					$('.pv_growth_col').addClass('z_col4ab');
				}
				$('.pv_growth').text(res.data.pv.growth);
				
				//访客数
				$('.uv').html(res.data.uv.value);
				if(parseFloat(res.data.uv.growth)>0){
					$('.uv_growth_col').addClass('z_colred');
				}else if(parseFloat(res.data.uv.growth)<0){
					$('.uv_growth_col').addClass('z_col4ab');
				}
				$('.uv_growth').text(res.data.uv.growth);
				
				//付款订单数
				$('.pay_order_count').html(res.data.pay_order_count.value);
				if(parseFloat(res.data.pay_order_count.growth)>0){
					$('.pay_order_count_growth_col').addClass('z_colred');
				}else if(parseFloat(res.data.pay_order_count.growth)<0){
					$('.pay_order_count_growth_col').addClass('z_col4ab');
				}
				$('.pay_order_count_growth').text(res.data.pay_order_count.growth);
				
				//付款客户数
				$('.pay_customer_count').html(res.data.pay_customer_count.value);
				if(parseFloat(res.data.pay_customer_count.growth)>0){
					$('.pay_customer_count_growth_col').addClass('z_colred');
				}else if(parseFloat(res.data.pay_customer_count.growth)<0){
					$('.pay_customer_count_growth_col').addClass('z_col4ab');
				}
				$('.pay_customer_count_growth').text(res.data.pay_customer_count.growth);			
			}else{
				tipshow(res.errMsg,'warm')
			}
		},
		error:function(){
			console.log('数据访问错误')
		}
	});
	//获取数据昨日流量统计
	$.ajax({
		type:"POST",
		url:"/merchants/xcx/stat/flow",
		data:{
			type:1,
			beginDate:getNowFormatDate(),
			endDate:getNowFormatDate()
		},
		headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
		success:function(res){
			if(res.errCode == 0){
				var flow = res;
				flow_ajax(flow);
				fangwen_ajax(flow);
			}else{
				tipshow(res.errMsg,'warm')
			}
		},
		error:function(){
			console.log('数据访问错误')
		}
	});
	//浏览量
	var flow_ajax = function (flow){
		flow_xAxis = [];//flow横轴
		flow_pv = [];//浏览量
		flow_uv = [];//访客数
		flow_new_uv = [];//新访客数
		flow_visit_depth = [];//平均访问深度
		flow_stay_time_uv = [];//人均停留时长
		//浏览量
		$('.flow_pv').html(flow.data.total.pv.value);
		if(parseFloat(flow.data.total.pv.growth)>0){
			$('.flow_pv_growth_col').addClass('z_colred');
		}else if(parseFloat(flow.data.total.pv.growth)<0){
			$('.flow_pv_growth_col').addClass('z_col4ab');
		}
		$('.flow_pv_growth').text(flow.data.total.pv.growth);
		
		//访客数
		$('.flow_uv').html(flow.data.total.uv.value);
		if(parseFloat(flow.data.total.uv.growth)>0){
			$('.flow_uv_growth_col').addClass('z_colred');
		}else if(parseFloat(flow.data.total.uv.growth)<0){
			$('.flow_uv_growth_col').addClass('z_col4ab');
		}
		$('.flow_uv_growth').text(flow.data.total.uv.growth);
		
		//新访客数
		$('.flow_newuv').html(flow.data.total.new_uv.value);
		if(parseFloat(flow.data.total.new_uv.growth)>0){
			$('.flow_newuv_growth_col').addClass('z_colred');
		}else if(parseFloat(flow.data.total.new_uv.growth)<0){
			$('.flow_newuv_growth_col').addClass('z_col4ab');
		}
		$('.flow_newuv_growth').text(flow.data.total.new_uv.growth);
		
		//平均访问深度
		$('.flow_visit_depth').html(flow.data.total.visit_depth.value);
		if(parseFloat(flow.data.total.visit_depth.growth)>0){
			$('.flow_visit_depth_growth_col').addClass('z_colred');
		}else if(parseFloat(flow.data.total.visit_depth.growth)<0){
			$('.flow_visit_depth_growth_col').addClass('z_col4ab');
		}
		$('.flow_visit_depth_growth').text(flow.data.total.visit_depth.growth);
		
		//平均停留时长
		$('.flow_stay_time_uv').html(flow.data.total.stay_time_uv.value+'s');
		if(parseFloat(flow.data.total.stay_time_uv.growth)>0){
			$('.flow_stay_time_uv_growth_col').addClass('z_colred');
		}else if(parseFloat(flow.data.total.stay_time_uv.growth)<0){
			$('.flow_stay_time_uv_growth_col').addClass('z_col4ab');
		}
		$('.flow_stay_time_uv_growth').text(flow.data.total.stay_time_uv.growth);
		
		//流量统计横轴
		for(var i = 0;i<flow.data.date_data.length;i++){
			var arr = flow.data.date_data[i].date;
			flow_xAxis.push(arr) 
		}
		//浏览量
		for(var i = 0;i<flow.data.date_data.length;i++){
			var arr = flow.data.date_data[i].pv;
			flow_pv.push(arr) 
		}
		//访客数
		for(var i = 0;i<flow.data.date_data.length;i++){
			var arr = flow.data.date_data[i].uv;
			flow_uv.push(arr) 
		}
		//新访客数
		for(var i = 0;i<flow.data.date_data.length;i++){
			var arr = flow.data.date_data[i].new_uv;
			flow_new_uv.push(arr) 
		}
		//平均访问深度
		for(var i = 0;i<flow.data.date_data.length;i++){
			var arr = flow.data.date_data[i].visit_depth;
			flow_visit_depth.push(arr) 
		}
		//人均停留时长
		for(var i = 0;i<flow.data.date_data.length;i++){
			var arr = flow.data.date_data[i].stay_time_uv;
			flow_stay_time_uv.push(arr) 
		}	
		flow_yser(flow_pv,flow_uv,flow_new_uv,flow_visit_depth,flow_stay_time_uv);
		flow_myChart();
	}
	//访问来源
	var fangwen_ajax = function(flow){
		if(flow.data.visit_distribution.length>0){
			for(var i = 0;i<flow.data.visit_distribution.length;i++){
				var arr = flow.data.visit_distribution[i].value*10;
				visit_distribution_series.push(arr) 
			}
			fangwen_cahrt(visit_distribution_series)
		}else{
			$('.integration__fallback-placeholder').removeClass('hidden');
			$('#main_sec').html('')
		}		
	}
		
	//流量统计选择
	$('.flow_select').change(function(){
		var option = $(".flow_select option:selected").val();
			$('.flow_input_time input').addClass('hidden');
			$('.flow_input_time input').eq(option).removeClass('hidden')
	})
	//流量统计时间
    //日期选择
	var flow_timeone = {
		elem: '#flow_timeone',
		type: 'date',
		max: getNowFormatDate(),  
		istime: true,
		istoday: true,
		theme:'#38f',
		done: function(datas) {
			$.ajax({
				type:"POST",
				url:"/merchants/xcx/stat/flow",
				data:{
					type:1,
					beginDate:datas,
					endDate:datas
				},
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				success:function(res){
					if(res.errCode == 0){
						var flow = res;
						flow_ajax(flow);
						fangwen_ajax(flow);
					}else{
						tipshow(res.errMsg,'warm')
					}
				},
				error:function(){
					console.log('数据访问错误')
				}
			});
		}
	};
	//范围选择
	var flow_timetwo = {
		elem: '#flow_timetwo',
		max: getNowFormatDate(), 
		range:true,
		istime: true,
		istoday: true,
		theme:'#38f',
		done: function(datas) {
			$.ajax({
				type:"POST",
				url:"/merchants/xcx/stat/flow/",
				data:{
					type:2,
					beginDate:datas.split(' - ')[0],
					endDate:datas.split(' - ')[1]
				},
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				success:function(res){
					if(res.errCode == 0){
						var flow = res;
						flow_ajax(flow);
						fangwen_ajax(flow);
					}else{
						tipshow('请选择正确的一周日期','warm','3000')
					}
				},
				error:function(){
					console.log('数据访问错误')
				}
			});
		}
	};
	//月份选择
	var flow_timethr = {
		elem: '#flow_timethr',
		type: 'month',
		max: getNowFormatDate(), 
		istime: true,
		istoday: true,
		theme:'#38f',
		done: function(datas) {
			if(datas.split('-')[1] ==1 ||datas.split('-')[1] ==3 || datas.split('-')[1]==5 || datas.split('-')[1]==7 || datas.split('-')[1]==8 || datas.split('-')[1]==10 || datas.split('-')[1]==12){
				var end_date = datas + '-31';
			}
			if(datas.split('-')[1] ==4 || datas.split('-')[1]==6 || datas.split('-')[1]==9 || datas.split('-')[1]==11){
				var end_date = datas + '-30';
			}
			if(datas.split('-')[1] == 2){
				if(datas.split('-')[0]%4 == 0 && datas.split('-')[0]%100 != 0 || datas.split('-')[0]%400 == 0){
					var end_date = datas + '-29';
				}else{
					var end_date = datas + '-28';
				}
			}		
			$.ajax({
				type:"POST",
				url:"/merchants/xcx/stat/flow/",
				data:{
					type:3,
					beginDate:datas+'-01',
					endDate:end_date
				},
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				success:function(res){
					if(res.errCode == 0){
						var flow = res;
						flow_ajax(flow);
						fangwen_ajax(flow);
					}else{
						tipshow(res.errMsg,'warm')
					}
				},
				error:function(){
					console.log('数据访问错误')
				}
			});
		}
	};
	laydate.render(flow_timeone);
	laydate.render(flow_timetwo);
	laydate.render(flow_timethr);
	
	function flow_yser(flow_pv,flow_uv,flow_new_uv,flow_visit_depth,flow_stay_time_uv){
		//流量统计ajax获取到的数据
//		flow_data = ['浏览量','访客数'];
		flow_series_new = [
	        {
	            name:'浏览量',
	            type:'line',
	            stack: '总量',
	            data:flow_pv,
	            itemStyle:{
	        		normal:{
		                color: "#5d9cec" //图标颜色
		            }
		        }
	        },
	        {
	            name:'访客数',
	            type:'line',
	            stack: '总量',
	            data:flow_uv,
	            itemStyle:{
	        		normal:{
		                color: "#62c87f" //图标颜色
		            }
		        }
	        },
	        {
	            name:'新访客数',
	            type:'line',
	            stack: '总量',
	            data:flow_new_uv,
	            itemStyle:{
	        		normal:{
		                color: "#f26462" //图标颜色
		            }
		        }
	        },
	        {
	            name:'平均访问深度',
	            type:'line',
	            stack: '总量',
	            data:flow_visit_depth,
	            itemStyle:{
	        		normal:{
		                color: "#fc863f" //图标颜色
		            }
		        }
	        },
	        {
	            name:'人均停留时长',
	            type:'line',
	            stack: '总量',
	            data:flow_stay_time_uv,
	            itemStyle:{
	        		normal:{
		                color: "#7053b6" //图标颜色
		            }
		        }
	        },
	    ];
	    //流量统计进行显示数据
	    if(flow_series.length==0){
	    	flow_series = [flow_series_new[0],flow_series_new[1]];  
	    }else{
	    	var temp = [];
	    	for(var i=0;i<flow_series_new.length;i++){
	    		for(var j=0;j<flow_series.length;j++){
	    			if(flow_series_new[i].name == flow_series[j].name){
	    				temp.push(flow_series_new[i]);
	    				break;
	    			}
	    		}
	    	}
	    	flow_series = temp;
	    }
	}
    //流量统计数据折线显示
	$('.items-select__content .items-select__item').click(function(){
		var index_flow = $(this).index();
		if($(this).hasClass('items-select__item--selected')){
			$(this).removeClass('items-select__item--selected');
			var flow_span = $(this).find('.statis-item__title').text();
			flow_data.remove(flow_span);
			flow_series.remove(flow_series_new[index_flow]);
			flow_myChart(1);
		}else{
			$(this).addClass('items-select__item--selected');
			var flow_span = $(this).find('.statis-item__title').text();
			flow_data.push(flow_span);	
			flow_series.push(flow_series_new[index_flow]); 
			flow_myChart(1); 
		}
	})
	
	function flow_myChart(isAfresh){		
		//流量统计
		//基于准备好的dom，初始化echarts图表	
		myChart = echarts.init(document.getElementById('main')); 
		//指定图表的配置项和数据
	    var option = {
	        tooltip : {
	        	trigger: 'axis'
		    },
		    legend: {
		        data:flow_data
		    },
		    toolbox: {
		        show : true
		    },
		    calculable : true,
		    xAxis : [
		        {
		            type : 'category',
		            boundaryGap : false,
		            data : flow_xAxis
		        }
		    ],
		    yAxis : {	
		        axisLine: {
	            	show: false
		        },
		        axisTick: {
		            show: false
		        },
		        axisLabel: {
		            textStyle: {
		                color: '#999'
		            }
		        }
	        },
		    series : flow_series
	    };
	    // 使用刚指定的配置项和数据显示图表。
	    if(typeof isAfresh !="undefined")
	    	myChart.setOption(option,true);
	    else	
	    	myChart.setOption(option); 
	}

	//访问来源
	function fangwen_cahrt(visit_distribution_series){		
		// 基于准备好的dom，初始化echarts图表
		myChart_sec = echarts.init(document.getElementById('main_sec')); 
		// 指定图表的配置项和数据
	    option_sec = {
	        tooltip : {
	        	trigger: 'axis'
		    },
		    legend: {
		        data:['浏览量','访客数']
		    },
		    toolbox: {
		        show : true
		    },
		    calculable : true,
		    xAxis : [
		        {
		            type : 'category',
		            data : ['会话','二维码','小程序主页','支付完成页','其他']
		        }
		    ],
		    yAxis : {
		        axisLine: {
	            	show: false
		        },
		        axisTick: {
		            show: false
		        },
	            type : 'value',
		        axisLabel: {
		            textStyle: {
		                color: '#999'
		            },
	            	formatter: '{value}%'
		        }
		    },
		    series : [
		        {
		            name:'浏览量',
		            type:'bar',
		            stack: '总量',
		            barWidth: 30,
		            data:visit_distribution_series,
		            itemStyle:{
	            		normal:{
			                color: "#5d9cec" //图标颜色
			            }
			        },
		        }
		    ]
	    };
	    // 使用刚指定的配置项和数据显示图表。
	    myChart_sec.setOption(option_sec,true);
	}
    
    
    
    
    
    
    
    
	//交易统计
	// 基于准备好的dom，初始化echarts图表
	var myChart_thri = echarts.init(document.getElementById('main_thri')); 
	// 指定图表的配置项和数据
    var option_thri = {
        tooltip : {
        	trigger: 'axis'
	    },
	    legend: {
	        data:['付款金额','付款人数','付款笔数','客单价','下单转化率','付款转化率','全店转化率']
	    },
	    toolbox: {
	        show : true,
	        feature : {

	        }
	    },
	    calculable : true,
	    xAxis : [
	        {	
	        	boundaryGap : false,
	            type : 'category',
	            data : ['2017-11-01','2017-11-02','2017-11-03','2017-11-04','2017-11-05','2017-11-06','2017-11-07']
	        }
	    ],
	    yAxis : {
	        axisLine: {
	            show: false
	        },
	        axisTick: {
	            show: false
	        },
	        axisLabel: {
	            textStyle: {
	                color: '#999'
	            }
	        },
	        position:'right'
	    },
	    series : [
	        {
	            name:'付款金额',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#5d9cec" //图标颜色
		            }
		        },
	        },
	        {
	            name:'付款人数',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#62c87f" //图标颜色
		            }
		        },
	        },
	        {
	            name:'付款笔数',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#f15755" //图标颜色
		            }
		        },
	        },
	        {
	            name:'客单价',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#fc863f" //图标颜色
		            }
		        },
	        },
	        {
	            name:'下单转化率',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#7053b6" //图标颜色
		            }
		        },
	        },
	        {
	            name:'付款转化率',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#ffce55" //图标颜色
		            }
		        },
	        },
	        {
	            name:'全店转化率',
	            type:'line',
	            stack: '总量',
	            barWidth: 30,
	            data:[5, 5, 1, 13],
	            itemStyle:{
            		normal:{
		                color: "#6ed5e6" //图标颜色
		            }
		        },
	        },
	    ]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart_thri.setOption(option_thri);
})