$(function(){
    
    
    var weekList = ['星期一','星期二','星期三','星期四','星期五','星期六','星期日']
    var weekList1 = ['周一','周二','周三','周四','周五','周六','周日']
    var html = '';
    for(var i=0;i<weekList.length;i++){
        html += '<div class="week-list-item">'+weekList[i]+'</div>'
    }
    $(".week-list").html(html)
    // 外卖时段选择
    $(".week-confirm").click(function(){
        var weekNum = []
        $(".week-list-item").each(function(key,val){
            $(this).hasClass('border') ? weekNum.push(key) : ''
        
        })
        if(weekNum.length>0){
            showWeek(weekNum,weekList1)
        }else{
            $(".week").val('')
        }
        $(".week-tip").hide()
    })
    
    $(".week-list").on('click','.week-list-item',function(){
        $(this).hasClass('border') ? $(this).removeClass('border') :  $(this).addClass('border');
    })

    // 添加时间段
    $(".add-more").click(function(){
        var newHtml =   '<div class="time-item">'+
                            '<div class="start-time select-item"></div>'+
                            '<span>至</span>'+
                            '<div class="end-time select-item"></div>'+
                            '<span class="delete"> 删除</span>'+
                        '</div>'
        $(".time-slot").append(newHtml)
        $(".delete").removeClass("none")
        if($(".time-item").length == 3){
            $(".add-more").hide()
        }
        selectTime()
        
    })
    
    selectTime()
    // 判断开始是否开启外卖订单
    if(is_on == 0){
        $("#uploadForm").addClass("none")
        $(".switch-wrap label").removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-on", "0");
    }else{
        $("#uploadForm").removeClass("none")
        $(".switch-wrap label").removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-on", "1");
        for(var i=0;i<work_days.length;i++){
            work_days[i] = work_days[i]-1
            $(".week-list-item").each(function(key,val){
                // $(this).hasClass('border') ? work_days.push(key+1) : '';
                if(key == work_days[i]){
                    $(this).addClass('border')
                }
            })
        }
        showWeek(work_days,weekList1)
        if(delivery_times.length>0){
            var newHtml='';
            for(var j=0;j<delivery_times.length;j++){
                newHtml += '<div class="time-item">'+
                                '<div class="start-time select-item">'+delivery_times[j].startTime+'</div>'+
                                '<span>至</span>'+
                                '<div class="end-time select-item">'+delivery_times[j].endTime+'</div>'
                if(delivery_times.length==1){
                    newHtml +='<span class="delete none"> 删除</span>'
                }else{
                    newHtml +='<span class="delete"> 删除</span>'
                }
                                
                newHtml +=  '</div>'
            }
            $(".time-slot").html(newHtml)
            selectTime()
            if(delivery_times.length == 3){
                $(".add-more").hide()
            }
        }else{
            // $(".start-time").text(delivery_times[0].startTime)
            // $(".end-time").text(delivery_times[0].endTime)
        }
        $('.unpay_min').val(unpay_min);
        $('.delivery_hour').val(delivery_hour);
    }
    // 外卖订单开启开关切换
    $(".switch-wrap label").click(function(){
        var _this = this;
        var open = $(this).attr("data-is-on");
        console.log(open)
        var is_on = open == "0" ? 1 : 0;
        if(is_set==0){
            tipshow('请先添加365小票打印机','warn');
            return false;
        }
        // 订单配置为空不能调此接口
        if(delivery_times.length>0){
            $.ajax({
                url: "/merchants/delivery/changeConfigStatus?is_on=" + is_on,
                data: {},
                type: "get",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success:function(res){
                    if(res.status == 1){
                        if(open == 1){
                            $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-on", "0");
                            $("#uploadForm").addClass("none")
                        }else{
                            $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-on", "1");
                            $("#uploadForm").removeClass("none")
                        }
                        tipshow(res.info,'info')
                    }else{
                        tipshow(res.info,'warn')
                    }
                }
            })
        }else{
            if(open == 1){
                $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-on", "0");
                $("#uploadForm").addClass("none")
            }else{
                $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-on", "1");
                $("#uploadForm").removeClass("none")
            }
            
        }
    })
    $(".confirm-btn").click(function(){
        var work_days = [];
        $(".week-list-item").each(function(key,val){
            $(this).hasClass('border') ? work_days.push(key+1) : '';
        })
        var delivery_times = []
        $(".time-item").each(function(){
            var delivery_item = {
                startTime:$(this).children(".start-time").text(),
                endTime:$(this).children(".end-time").text()
            }
            delivery_times.push(delivery_item) 
        })
        var is_on = $(".switch-wrap label").attr("data-is-on");
        var unpay_min = $(".unpay_min").val();
        var delivery_hour = $(".delivery_hour").val();
        $.ajax({
            url:'/merchants/delivery/deliveryConfig',
            type:'post',
            data:{
                work_days:work_days,
                delivery_times:delivery_times,
                is_on:is_on,
                unpay_min:unpay_min,
                delivery_hour:delivery_hour
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status == 1){
                    tipshow(res.info,'info')
                }else{
                    tipshow(res.info,'warn')
                }
            }
        })
    })

    // 输入框只能输入数字
    // $("body").on('keyup','.unpay_min',function(){
    //     $(this).val($(this).val().replace(/\D/g,''))
    // })
    

})
function checkWeek(){
    $(".week-tip").show()
}
// 时间段列表渲染
function getTimeList(start,end,type,that){
    var timeList = []
    if(type == 0){
        console.log(end.slice(0,2))
        if(!end || end.slice(0,2) == '次日'){
            for(var i=0;i<24;i++){
                var hour = i<10 ? '0' + i : i
                var time1 = hour + ':00';
                var time2 = hour + ':30';
                timeList.push(time1,time2);
            }
        }else{
            var hours = end.split(':')[0];
            var minute = end.split(":")[1];
            hours = hours < 10 ? parseInt(hours.slice(1,2)) : parseInt(hours) 
            for(var i=0;i<hours;i++){
                var hour = i<10 ? '0' + i : i;
                var time1 = hour + ':00';
                var time2 = hour + ':30';
                timeList.push(time1,time2);
            }
            if(minute == '30'){
                hours = hours<10 ? '0' + hours : hours;
                timeList.push(hours + ':00')
            }
        }
    }else{
        if(!start){
            for(var i=0;i<32;i++){
                if(i>23){
                    var hour = '次日 ' + '0' + (i-24)
                }else{
                    var hour = i<10 ? '0' + i : i
                }
                var time1 = hour + ':00';
                var time2 = hour + ':30';
                timeList.push(time1,time2);
            }
        }else{
            if(start.slice(0,1) == 0){
                console.log(start.slice(0,1),start.slice(1,2))
                for(var i=start.slice(1,2);i<32;i++){
                    if(i>23){
                        var hour = '次日 ' + '0' + (i-24)
                    }else{
                        var hour = i<10 ? '0' + i : i
                    }
                    var time1 = hour + ':00';
                    var time2 = hour + ':30';
                    timeList.push(time1,time2);
                }
                timeList.push('次日 08:00');
            }else{
                for(var i=start.slice(0,2);i<32;i++){
                    if(i>23){
                        var hour = '次日 ' + '0' + (i-24)
                    }else{
                        var hour = i<10 ? '0' + i : i
                    }
                    var time1 = hour + ':00';
                    var time2 = hour + ':30';
                    timeList.push(time1,time2);
                }
            }
            if(start.split(':')[1] == '00'){
                timeList.splice(0,1)
            }else{
                timeList.splice(0,2)
            }
        }
    }
    
    var html='';
    for(var j=0;j<timeList.length;j++){
        html += '<span class="time-option" value="'+timeList[j]+'">'+timeList[j]+'</span>'
    }
    $(".time-select").html(html)
    $(".time-option").click(function(){
        that.text($(this).text())
        $(".time-select").hide()
    })

}
// 开始结束时间段选择
function selectTime(){
    // 结束时间段选择
    $(".end-time").click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        console.log(top,left)
        $(".time-select").css({"top":top-20,"left":left-220});
        $(".time-select").show();
        console.log(3333)
        getTimeList($(this).siblings('.start-time').text(),$(this).text(),1,$(this))
    })
    // 开始时间段选择
    $(".start-time").click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        console.log(top,left)
        $(".time-select").css({"top":top-20,"left":left-220});
        $(".time-select").show();
        getTimeList($(this).text(),$(this).siblings('.end-time').text(),0,$(this))
    })
    // 删除时间段
    $(".delete").click(function(){
        $(this).parent().remove();
        $(".add-more").show()
        if($(".time-item").length == 1){
            $(".delete").addClass("none")
        }
    })
    // 关闭弹出层
    $('body').click(function(event){
        var _con = $('.time-select');
        if(!_con.is(event.target) && _con.has(event.target).length === 0){
            $(".time-select").hide()
        }
    })
}
// 外卖周天选择
function showWeek(weekNum,weekList1){
    var showHtml = '';
    var newList = wofo(weekNum);
    if(newList.length > 1){
        for(var i=0;i<newList.length;i++){
            if(newList[i].length == 2){
                if(i<newList.length-1){
                    if(newList[i][1]-newList[i][0] == 1){
                        showHtml += weekList1[newList[i][0]] + '、' + weekList1[newList[i][1]] + '、'
                    }else{
                        showHtml += weekList1[newList[i][0]] + '至' + weekList1[newList[i][1]] + '、'
                    }
                }else{
                    if(newList[i][1]-newList[i][0] == 1){
                        showHtml += weekList1[newList[i][0]] + '、' + weekList1[newList[i][1]]
                    }else{
                        showHtml += weekList1[newList[i][0]] + '至' + weekList1[newList[i][1]]
                    }
                }
            }else{
                if(i<newList.length-1){
                    showHtml += weekList1[newList[i][0]] + '、'
                }else{
                    showHtml += weekList1[newList[i][0]]
                }
                
            }
        }
    }else{
        if(newList[0].length == 2){
            if(newList[0][1]-newList[0][0] == 1){
                showHtml += weekList1[newList[0][0]] + '、' + weekList1[newList[0][1]]
            }else{
                showHtml += weekList1[newList[0][0]] + '至' + weekList1[newList[0][1]]
            }
        }else{
            showHtml += weekList1[newList[0][0]]
        }
    }
    $(".week").val(showHtml)
}

// 对选择的星期做处理
function foldRight(func, ary, acc) {
    if(0 == ary.length) return acc
    return func(ary[0], foldRight(func, ary.slice(1), acc))
}
// 对选择的星期做处理
function wofo(ary) {
    return foldRight(function(e, acc) {
        if(0 == acc.length)
            return [
                [e]
            ].concat(acc);
        if(1 == acc[0].length && acc[0][0] - 1 == e)
            return [
                [e, acc[0][0]]
            ].concat(acc.slice(1))
        if(2 == acc[0].length && acc[0][0] - 1 == e)
            return [
                [e, acc[0][1]]
            ].concat(acc.slice(1))
        else
            return [
                [e]
            ].concat(acc)
    }, ary, [])
}