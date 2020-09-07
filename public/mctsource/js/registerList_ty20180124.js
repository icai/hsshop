$(function(){
	// 开始时间
    $('#datetimepicker1, #datetimepicker2').datetimepicker({
        //minDate: new Date(), //时间小于当前时间时会自动清空以有的数据
        format: 'YYYY-MM-DD',
        showClear: true,
        showClose: true,
        showTodayButton: true,
        locale: 'zh-cn',
        focusOnShow: false,
        useCurrent: false,
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
        allowInputToggle: true,
    });
    //datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        $(".start_time").removeClass("hide");
        $(".start_time .start_time_s").text($("#datetimepicker1").val());
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        $(".end_time").removeClass("hide");
        $(".end_time .end_time_s").text($("#datetimepicker2").val());
    });
    
    //近七天
    $(".aWeek").click(function(){
    	$("#datetimepicker1").val(getBeforeTime(7).beforeTime)
    	$("#datetimepicker2").val(getBeforeTime().nowTime)
    })
    //近七天
    $(".aMonth").click(function(){
    	$("#datetimepicker1").val(getBeforeTime(30).beforeTime)
    	$("#datetimepicker2").val(getBeforeTime().nowTime)
    })
    //查看
    $("body").on("click", ".look", function(){
    	var value = JSON.parse($(this)["0"].dataset.value)
    	console.log(value)
    	$('#myModal').modal()
    	$("#showMemberName").val(value.name);
    	$("#showPhoneNumber").val(value.phone);
    	$("#showCompanyName").val(value.company_name);
        $("#showPosition").val(value.company_position);
    	$("#showAddress").val(value.company_address);
        if(value.business_licence_url){
    	   $("#businessLicense").attr("src", imgUrl+value.business_licence_url);
        }
        // if(value.id_card_on){
        //     $("#IDcardPositive").attr("src", imgUrl+value.id_card_on);
        // }
        // if(value.id_card_off){
        //     $("#IDcardOpposite").attr("src", imgUrl+value.id_card_off);
        // }
        if(value.id_card_on){
            $("#IDcardPositive").attr("src", imgUrl+value.id_card_on);
        }else{
            $("#IDcardPositive").attr("src", '');
        }
        if(value.id_card_off){
            $("#IDcardOpposite").attr("src", imgUrl+value.id_card_off);
        }else {
            $("#IDcardOpposite").attr("src", '');
        }
    })
    // 删除列表
    $('body').on('click','.delet',function(e){            
        e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'',
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow('删除成功','info');
                        $(_this).parents('.data_content').remove();
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    tipshow('数据访问异常','warn');
                }
            }); 
        })
    }); 
    
    //全选
    var ids = [];
    $("#allChoose").click(function(){
    	var choosJud = $(this).prop("checked");
    	if(choosJud){
    		$(".chooseItem").each(function(index, ele){
    			//单选过的则跳过 否则push进ids
    			if(ids.indexOf($(this).val()) == -1){
    				ids.push($(this).val())
    			}
    			$(this).prop("checked", true)
    		});
    	}else{
    		ids = [];
    		$(".chooseItem").each(function(index, ele){
    			$(this).prop("checked", false)
    		});
    	}
    	console.log(ids)
    })
    //单选
    $("body").on("click", ".chooseItem", function(){
    	var itemVal = $(this).val()
    	if($(this).prop("checked")){
    		ids.push(itemVal);
    		if(ids.length == $(".chooseItem").length){
    			$("#allChoose").prop("checked", true)
    		}
    	}else{
    		var delIndex = ids.indexOf(itemVal);
    		ids.splice(delIndex, 1);
    		$("#allChoose").prop("checked", false)
    	}
    	console.log(ids)
    })
    //批量导出
    $(".branch_output").click(function(){
    	if(ids.length ==0){
    		tipshow('请选择要导出的数据','warn');
    	}else{
            window.location.href='/merchants/member/li/exportXls?idarr='+ids;
    	}
    })
    $(".branch_outputAll").click(function(){
            window.location.href='/merchants/member/li/exportXls?all=1';
    })
})


//点击注册
$('body').on('click','.register',function(e){
    var _this = this;
    var value=$(this).data('value');
    $.ajax({
        type:"post",
        url:'/merchants/member/li/user',
        data:{
            'id':value.id,
            'all':'1'
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res){
            if(res.status===1){
                tipshow('注册成功','info');
                location.reload()
            }else{
                tipshow(res.info,'warn');
            }
        },
        error:function(){
            tipshow('数据访问异常','warn');
        }
    });

});
// //点击发送短信
// $('body').on('click','.sms',function(e){
//     var _this = this;
//     var value=$(this).data('value');
//     $.ajax({
//         type:"post",
//         url:'/merchants/member/li/user',
//         data:{
//             'id':value.id,
//             'all':'2'
//         },
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function(res){
//             if(res.status===1){
//                 tipshow('发送成功','info');
//                 location.reload()
//             }else{
//                 tipshow(res.info,'warn');
//             }
//         },
//         error:function(){
//             tipshow('数据访问异常','warn');
//         }
//     });
//
// });

$('body').on('click','.sms',function(e){
    tipshow('暂无文案','info');
});

function getBeforeTime(beforeDay){
	beforeDay = beforeDay ? beforeDay : 0;
 	var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth()+1;
    var day = date.getDate();
    
    var timestamp = date.getTime()
    var dateB = new Date(timestamp - beforeDay * 24 * 3600 * 1000);
    var yearB = dateB.getFullYear();
    var monthB = dateB.getMonth()+1;
    var dayB = dateB.getDate();
    
    return {nowTime: year+"-"+month+"-"+day, beforeTime: yearB+"-"+monthB+"-"+dayB}
}
