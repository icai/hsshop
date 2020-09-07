$(function(){
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
		maxDate: new Date(new Date().getTime()),
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
	//时间筛选的自动填充
	var initDate = new Date(new Date().getTime());
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
	var selectNum = 0;
	$('select.time_select').change(function(){
		selectNum = $(this).val();
		$('.zent-input').eq(selectNum).removeClass('hidden').siblings('.zent-input').addClass('hidden');
		nowPage1 = 1;
		selectGetAll();
		
	})
	function selectGetAll(){
		var startDate = null;
		var endDate = null;
		switch (selectNum){
			case '0':
				endDate = $('#flow_timeone').val();
				getAll(nowPage1,id,endDate,endDate,1);
				break;
			case '1':
				endDate = $('#flow_timetwo').val();	
				getAll(nowPage1,id,endDate,endDate,2);
				break;
			case '2':
				endDate = $('#flow_timethr_2').val();
				startDate = $('#flow_timethr_1').val();
				getAll(nowPage1,id,startDate,endDate,3);
				break;
		}
	}

	//dp.change
	$('#flow_timeone').on('dp.change',function(){
		nowPage1 = 1;
		let endDate = $('#flow_timeone').val();
		getAll(nowPage1,id,endDate,endDate,1);
	});
	$('#flow_timetwo').on('dp.change',function(){
		nowPage1 = 1;
		let endDate = $('#flow_timetwo').val();
		getAll(nowPage1,id,endDate,endDate,2);
	});
	$('#flow_timethr_2,#flow_timethr_1').on('dp.change',function(){
		let endDate = $('#flow_timethr_2').val();
		let startDate = $('#flow_timethr_1').val();
		nowPage1 = 1;
		getAll(nowPage1,id,startDate,endDate,3);
	});

	//全局定义总页数和当前页数
	var totalPage = 0, nowPage = 1, countPage = 0;
	var totalPage1 = 0, nowPage1 = 1, countPage1 = 0;
	var id = GetQueryString('mid');
	
	//点击佣金详情
	$("#income").click(function(){
		location.href = "/merchants/distribute/partnerIncome?mid="+id;
	})
	//点击合伙人脉
	$("#contacts").click(function(){
		location.href = "/merchants/distribute/partnerContacts?mid="+id;
	})
	
	//页面第一次加载
	var endDate = $('#flow_timeone').val();
	getIncome('', 1, id);
	getAll(1,id,endDate,endDate,1);
	//点击首页
	$(".js-firstPage").click(function(){
		nowPage = 1;
		getIncome(1, nowPage, id)
	});
	//点击尾页
	$(".js-lastPage").click(function(){
		nowPage = totalPage;
		getIncome(1, nowPage, id)
	})
	//点击上一页
	$(".js-prevPage").click(function(){
		if(nowPage > 1) {
			nowPage--;
			getIncome(1, nowPage, id)
		}
	})
	//点击下一页
	$(".js-nextPage").click(function(){
		if(nowPage < totalPage) {
			nowPage++;
			getIncome(1, nowPage, id)
		}
	});
	// 佣金流水
	function getIncome(tag, page, id){
		$.get("/merchants/distribute/getIncome/"+id,{
			tag  : tag,
			page : page,
		}, function(res){
			//每次加载之前先清空
			$(".member_list .js-list_div").html("")
			$("#pageInfo span").html("");
			//只有当tag的值为空的时候加载会员信息；后面切换分页则tag=1；
			if(tag != 1){
				//会员信息
				var member = res.data.member;
				$("#form_1 div:eq(0) span").text(member.nickname);
				$("#form_1 div:eq(1) span").text(member.mobile);
				$("#form_1 div:eq(2) span").text(member.created_at);
				$("#form_2 div:eq(0) span").text(member.all);
				$("#form_2 div:eq(1) span").text(member.cash);
				$("#form_2 div:eq(2) span").text(member.wait);
				$("#form_3 div:eq(0) span").text(member.trade_amount);
				
				//页数信息(tag=1的时候获取数据，为空的时候计算页码)
				var pageInfo = res.data.pageInfo;
				$("#pageInfo span").prepend('总条数：'+pageInfo.count+' &nbsp;&nbsp; 当前页码'+pageInfo.pageNow+'/'+pageInfo.pageNum);
				//赋值总页数和当前页
				totalPage = pageInfo.pageNum;
				countPage = pageInfo.count;
			}else{
				$("#pageInfo span").prepend('总条数：'+countPage+' &nbsp;&nbsp; 当前页码'+nowPage+'/'+totalPage);
			}
			
			//佣金列表
			var income = tag==1 ? res.data : res.data.income;
			for(var i=0; i<income.length; i++){
				var status;    	//判断分销状态信息
				if (income[i].status==0) {
					status = "未到账"
				}else if (income[i].status==1) {
					status = "已到账"
				}else if(income[i].status==-1){
					status = "已流失"
				}				
				var income_item = '<ul class="list_item list_body">';
					if(income[i].orderMember){
						income_item +='<li>'+income[i].orderMember.nickname+'</li>';						
					}else{
						income_item +='<li></li>';
					}
					income_item +='<li>'+income[i].money+'</li>';
					if(income[i].order){							
						income_item +='<li>'+income[i].order.oid+'</li>';							
					}else{							
						income_item +='<li></li>';
					}
					income_item +='<li>'+status+'</li>';
					income_item +='<li>'+income[i].created_at+'</li>';
					income_item +='</ul>';
				$(".member_list .js-list_div").append(income_item)
			}
		});
	};

	// 所有订单
	function getAll(page, id, star, end,type){
		$.get("/merchants/distribute/getOrderList",{
			mid  : id,
			page : page,
			start_time: star,
			end_time: end,
			type: type
		}, function(res){
			if (res.status == 1) {
				var pageInfo = res.data[0];
				$("#pageInfo1 span").html('总条数：'+pageInfo.total+' &nbsp;&nbsp; 当前页码'+pageInfo.current_page+'/'+pageInfo.last_page);
				//赋值总页数和当前页
				totalPage1 = pageInfo.last_page;
				countPage1 = pageInfo.total;
			
				var income = res.data[0].data,html="",status="";
				for(var i=0; i<income.length; i++){
					if (income[i].status==0) {
						status = "待付款"
					}else if (income[i].status==1) {
						status = "待发货"
					}else if(income[i].status==2){
						status = "已发货（待收货）"
					}else if(income[i].status==3){
						status = "已完成"
					}else if(income[i].status==4){
						status = "已关闭"
					}else if(income[i].status==7){
						status = "待抽奖"
					}
					html += '<ul class="list_item list_body">';
					html +='<li>'+income[i].nickname+'</li>';				
					html +='<li>'+income[i].oid+'</li>';
					html +='<li>'+income[i].pay_price+'</li>';
					html +='<li>'+status+'</li>';
					html +='<li>'+income[i].created_at+'</li>';
					html +='</ul>';
				}
				$(".member_list .js-list_div1").html(html);
			}
		});
	};
	//点击首页
	$(".js-firstPage1").click(function(){
		nowPage1 = 1;
		selectGetAll();
	});
	//点击尾页
	$(".js-lastPage1").click(function(){
		nowPage1 = totalPage1;
		selectGetAll();
	})
	//点击上一页
	$(".js-prevPage1").click(function(){
		if(nowPage1 > 1) {
			nowPage1--;
			selectGetAll();
		}
	})
	//点击下一页
	$(".js-nextPage1").click(function(){
		if(nowPage1 < totalPage1) {
			nowPage1++;
			selectGetAll();
		}
	});

	$('.js-order-btn').click(function(){
		$(this).addClass('active').siblings('.active').removeClass('active');
		if ($(this).data('type') == 1){
			$('.fx-wraper').show();
			$('.all-wraper').hide();
			$('.timer-wraper').hide();
		} else if ($(this).data('type') == 2){
			$('.fx-wraper').hide();
			$('.all-wraper').show();
			$('.timer-wraper').show();
		}
	})
})

function GetQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}
