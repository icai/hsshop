 
$(function(){
    // 帮助弹框
    var _nodeHtml = '<div class="mgb30">';
		_nodeHtml += '<p class="mgb10">结算方式：</p>';
		_nodeHtml += '<ul class="f12">';
		_nodeHtml += '<li>［担保交易］结算时间＝发货后T+7天自然日或顾客确认收货时间</li>';
		_nodeHtml += '<li>［快速结算］结算时间＝发货时间（适用已缴纳保证金商户＝》<a class="blue_38f" href="保证金.html">查看</a>）</li>';
		_nodeHtml += '</ul>';
		_nodeHtml += '</div>';
		_nodeHtml += '<div class="mgb30">';
		_nodeHtml += '<ul class="f12">';
		_nodeHtml += '<li>ps：结算日期取整天，00:00:00-23:59:59</li>';
		_nodeHtml += '<li>日账单为当天结算时间内，所有已结算金额汇总</li>';
		_nodeHtml += '<li>月账单为该自然月结算时间内，所有已结算金额汇总</li>';
		_nodeHtml += '</ul>';
		_nodeHtml += '</div>'
    $(".note_tip").popover({
        html :true,                                 // 是否转义为html标签
        container:'body',                           // 依靠的元素
        placement:'bottom',                          // 箭头方向
        trigger:'hover',                            // 事件
        content:_nodeHtml,                          // 主体内容
    });
})
var setFormCheckSubmit = function(){  
    var fs = document.forms; //获得页面上所有的表单  
    for(var i =0;i<fs.length;i++){  
        fs[i].submited = false; //添加一个属性用来记录表单是否提交状态  
        fs[i].bashSubmit = fs[i].submit; //设置一个方法用来记录表单的submit的方法  
        fs[i].submit = new Function("formSubmit(this)");//替换表单submit方法 this传入表单本身  
        fs[i].onsubmit = function(ev){ //event对象  
            var e = ev || window.event;  
            e.returnValue?e.returnValue = false:e.preventDefault(); //ie? ie = false:其他  取消事件关联的默认动作  如submit调用该方法可以阻止表单提交  
            if(this.submited){  
                return false; //如果是提交中则返回false取消提交  
            }  
            this.submited = true;  //记录提交状态  
            this.bashSubmit(); //提交表单  
        }  
    }  
}  
function formSubmit(form){  
    if(form.submited)return false; //如果是提交中则返回false取消提交  
    form.submited = true;  //记录提交状态  
    form.bashSubmit(); //提交表单  
}  
window.onload = function(){  
    setFormCheckSubmit()  
}  

//日汇总
	$('#start_time').datetimepicker({
        format: 'YYYY-MM',
        dayViewHeaderFormat: 'YYYY 年',
        useCurrent: false,  //true 起始只可选今天
        showClear:true,
        showClose:true,
        showTodayButton:true,
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
            nextCentury: '后一世纪'
        },
        allowInputToggle:true,
    })
//月汇总
	$('#end_time').datetimepicker({
        format: 'YYYY',
        dayViewHeaderFormat: 'YYYY 年',
        useCurrent: false,  //true 起始只可选今天
        showClear:true,
        showClose:true,
        showTodayButton:true,
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
var key = "";
var html = "";
$('#start_time').on('dp.change',function(){
    var date_form = $('.form-control').val(); //当前的年月
    var dateArr = date_form.split('-');
	$.ajax({
	    url: '/merchants/capital/billSummary',
	    type: 'POST',
	    data: {"type":1,"year":dateArr[0],"month" :dateArr[1]},
	    headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
	    success:function(res) {
	    	$(".tbody-mod").empty()
            html = '';
	    	for(var key in res.data){
	    		var k_year = key.split("-")[0];
	    		var k_month = key.split("-")[1];
	    		var k_day = key.split("-")[2];
	    		var income = res.data[key].income;
	    		(income == 0)?income='0.00' : income='+'+res.data[key].income;
	    		var paid = res.data[key].paid;
	    		(paid == 0)?paid='0.00' : paid='-'+res.data[key].paid;
	    		var green_f04 = '';
	    		(income == 0)?green_f04='a' : green_f04='green_f04';
	    		var red_f00 = '';
	    		(paid == 0)?red_f00='a' : red_f00='red_f00';
	    		var url = '/merchants/capital/billSummaryContent/'+'1/'+k_year+'/'+k_month+'/'+k_day;
                html += '<tr>'
                html += '<td class="data-year">'+key+'</td>'
                html += '<td class='+green_f04+'>'+income+'</td>'
                html += '<td class='+red_f00+'>'+paid+'</td>'
                html += '<td>'
                html += "<a class='blue_38f' href="+url+">详情</a>"
                html += '</td>'
                html += '</tr>'    		
	    	}	    	
	    	$(".tbody-mod").append(html);
	    	if(res.data.length==""){
	    		$(".table,.tbody-mod").hide()
	    		$('.page_detail').show()
	    		$('.page_detail').addClass('nosp').html("暂无数据")
	    	}else{
	    		$(".table,.tbody-mod").show()
	    		$('.page_detail').hide()
	    	}
		},
		error:function(){
			alert("数据访问错误")
		}
	})
});

$('#end_time').on('dp.change',function(){	
	$.ajax({
	    url: '/merchants/capital/billSummary',
	    type: 'POST',
	    data: {'type':2,'year':$('.form-control').val()},
	    headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
	    success:function(res) {
	    	$(".tbody-mod").empty()
            html = '';
	    	for(var key in res.data){
	    		var k_year = key.split("-")[0];
	    		var k_month = key.split("-")[1];
	    		var k_day = key.split("-")[2];
	    		var income = res.data[key].income;
	    		(income == 0)?income='0.00' : income='+'+res.data[key].income;
	    		var paid = res.data[key].paid;
	    		(paid == 0)?paid='0.00' : paid='-'+res.data[key].paid;
	    		var green_f04 = '';
	    		(income == 0)?green_f04='a' : green_f04='green_f04';
	    		var red_f00 = '';
	    		(paid == 0)?red_f00='a' : red_f00='red_f00';
	    		var url = '/merchants/capital/billSummaryContent/'+'2/'+k_year+'/'+k_month;
                html += '<tr>'
                html += '<td class="data-year">'+key+'</td>'
                html += '<td class='+green_f04+'>'+income+'</td>'
                html += '<td class='+red_f00+'>'+paid+'</td>'
                html += '<td>'
                html += "<a class='blue_38f' href="+url+">详情</a>"
                html += '</td>'
                html += '</tr>'    		
	    	}	    	
	    	$(".tbody-mod").append(html);
	    	if(res.data.length==""){
	    		$(".table,.tbody-mod").hide()
	    		$('.page_detail').show()
	    		$('.page_detail').addClass('nosp').html("暂无数据")
	    	}else{
	    		$(".table,.tbody-mod").show()
	    		$('.page_detail').hide()
	    	}
		},
		error:function(){
			alert("数据访问错误")
		}
	})
})