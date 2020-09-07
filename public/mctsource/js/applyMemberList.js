$(function(){
    $('#startTime,#endTime').datetimepicker({
        // minDate: new Date(),
        format: 'YYYY-MM-DD HH:mm:ss',
        dayViewHeaderFormat: 'YYYY 年 MM 月',
        showClear: true,
        showClose: true,
        showTodayButton: true,
        locale: 'zh-cn',
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
        allowInputToggle: true,
    });
     /**
     * 自动审核开关
     * 
     */
    $(".js-distribute-switch label").click(function() {
        var _this = this;
        var open = $(this).attr("data-is-open");
        var status = open=="1"?0:1;
        var url = "/merchants/distribute/autoCheck/"+status;
        $.ajax({
            url:url,
            data:{},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                //保存成功后 移除新增栏目 插入新的ul
                if(json.status==1){
                    tipshow(json.info);
                    if (open == "1") {
                        //切换成关闭状态
                        $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                        $('.js-distribute-show').hide();
                    } else {
                        //切换成开启状态
                        $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                        $('.js-distribute-show').show();
                    }
                }else{
                    tipshow(json.info,"wran");
                }
            },
            error:function(){
                tipshow("异常","wran");
            }
        });
    });
    $('.help').hover(function(){
        $(this).children('.tips-content').toggle();
    });
    
	var id = 0,type;
	$(document).on("click", ".js-action", function(){
        id = $(this).data('id');
        type = $(this).data('type');
        if (type == 1) {
            $('.js-popup1').show();
        } else {
            $('.js-popup').show();
        }
    })
    //拒绝申请
	$('.js-close-wraper,.js-cancle-btn').click(function(){
		$('.js-popup').hide();
		$('.js-reason').val('');
	});
	$('.js-sure-btn').click(function(){
		var reason = $('.js-reason').val();
		if (reason!=''){
			$.get('/merchants/distribute/checkApplyMember/'+id+'/'+2,{reason},function(res){
				if (res.status == 1){
					tipshow(res.info);
					window.location.reload();
				} else {
					tipshow(res.info,'warn');
				}
			})
		} else {
			tipshow('请输入拒绝理由', 'warn');
		}
		
    })
    // 同意申请
    $('.js-close-wraper1,.js-cancle-btn1').click(function(){
		$('.js-popup1').hide();
    });
    $('.js-sure-btn1').click(function(){
        $.get('/merchants/distribute/checkApplyMember/'+id+'/'+1,function(res){
            if (res.status == 1){
                tipshow(res.info);
                window.location.reload();
            } else {
                tipshow(res.info,'warn');
            }
        })
		
    })
	//点击空白处隐藏弹出层
	$('body').click(function(event){
		var _con = $('.popup-wraper');   // 设置目标区域
		if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
			$(".popup").hide();
			$('.js-reason').val('');
		}
    });

    //清空筛选
	$("#clearJudge").click(function(){
        $("#mobile, #nickName,#startTime,#endTime,#status").val("");
        $('#buyNum').val('-1');
	})
    
});