'use strict';
$(function() {
    var start = {
        elem: '#start_time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas) {
            // console.log(datas);
            $('#start_time').val(datas);
            $('#start_time').focus();
            $('#start_time').blur();
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var end = {
        elem: '#end_time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(),
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas) {
            // console.log($('#endTime').val())
            $('#end_time').val(datas);
            $('#end_time').focus();
            $('#end_time').blur();
            // $('.edit_form').data("bootstrapValidator").validateField('end_at');
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end); 
    //点击回复内容事件
    $(".js-reply").click(function() { 
        var id = $(this).attr("data-id");
         $.ajax({
            url: '/merchants/microforum/evaluates/content',
            type: 'POST',
            data: { id: id },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.status == 1) {
                    hstool.open({
                        btn: ["确定"],
                        area:['300px','auto'],
                        btn1: function() {
                            hstool.close();
                        },
                        title: "评论内容",
                        content: "<p>"+res.data+"</p>"
                    }); 
                } else {
                    tipshow(res.info, "wran");
                }
            },
            error: function() {
                console.log("异常");
            }
        });
        
    });

    //全选或全不选
    $("#check_all").click(function(){ 
        var checked = this.checked;
        $(".cb_select").each(function(){
            this.checked = checked;
        }); 
    });

    //批量删除
    $(".js_bacth_del").click(function(e){
        e.stopPropagation(); 
        var arr =[];
        $(".cb_select").each(function(){
            if(this.checked){
                arr.push(this.value);
            }
        });
        if(arr.length>0){
            showDelProver($(this), function(){
                $.ajax({
                    url: '/merchants/microforum/evaluates/deleted',
                    type: 'POST',
                    data: { id: arr },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status == 1) {
                            tipshow(res.info);
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            tipshow(res.info, "wran");
                        }
                    },
                    error: function() {
                        console.log("异常");
                    }
                });
            }, '确定要删除吗?',true,"right");
        }else{
            tipshow("请选择分类","wran");
        }
    });

    //删除
    $(".delete").click(function(e) {
        e.stopPropagation(); 
        var id = $(this).parent().attr("data-id");
        showDelProver($(this), function() {
            $.ajax({
                url: '/merchants/microforum/evaluates/deleted',
                type: 'POST',
                data: { id: id },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.status == 1) {
                        tipshow(res.info);
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        tipshow(res.info, "wran");
                    }
                },
                error: function() {
                    console.log("异常");
                }
            });
        }, '确定要删除吗?');
    });

});