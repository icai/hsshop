'use strict';//严格模式
$(function(){
	var start = {
      elem: '#start_time',
      format: 'YYYY-MM-DD hh:mm:ss',
      max: '2099-06-16 23:59:59', //最大日期
      istime: true,
      istoday: false,
      choose: function(datas){
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
      max: '2099-06-16 23:59:59',
      istime: true,
      istoday: false,
      choose: function(datas){
        $('#end_time').val(datas);
        $('#end_time').focus();
        $('#end_time').blur();
        start.max = datas; //结束日选好后，重置开始日的最大日期
      }
    };
    laydate(start);
    laydate(end);
    //帖子置顶
    $(".zd").click(function(){
    	var id = $(this).parent().attr("data-id");
    	$.ajax({
            url: '/merchants/microforum/posts/topped',
            data:{id:id},
            type:"post",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                if(data.status==1){
                    tipshow(data.info);
                    setTimeout(function(){
                        location.href="/merchants/microforum/posts/list";
                    },1000);
                }else{
                    tipshow(data.info,"warn");
                }
            },
            error:function(){
                tipshow('异常',"warn"); 
            }
        });
    });
    //取消置顶
    $(".qxzd").click(function(){
    	var id = $(this).parent().attr("data-id");
    	$.ajax({
            url: '/merchants/microforum/posts/untopped',
            data:{id:id},
            type:"post",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                if(data.status==1){
                    tipshow(data.info);
                    setTimeout(function(){
                        location.href="/merchants/microforum/posts/list";
                    },1000);
                }else{
                    tipshow(data.info,"warn");
                }
            },
            error:function(){
                tipshow('异常',"warn"); 
            }
        });
    });

    //删除帖子
    $(".del").click(function(e){
    	var id = $(this).parent().attr("data-id");
        e.stopPropagation(); 
        showDelProver($(this), function(){
            $.ajax({
                url: '/merchants/microforum/posts/deleted',
                data:{id:id},
                type:"post",
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(data){
                    if(data.status==1){
                        tipshow(data.info);
                        setTimeout(function(){
                            location.href="/merchants/microforum/posts/list";
                        },1000);
                    }else{
                        tipshow(data.info,"warn");
                    }
                },
                error:function(){
                    tipshow('异常',"warn"); 
                }
            });
        }, '确定要删除吗?'); 
    }); 
    //全选或全不选
    $("#check_all").click(function(){ 
        var checked = this.checked;
        $(".cb_select").each(function(){
            this.checked = checked;
        }); 
    }); 
         
    //批量删除
    $(".js_batch_del").click(function(e){
        e.stopPropagation(); 
        var arr = [];
        $(".cb_select").each(function(){
            if(this.checked){
                arr.push(this.value);
            }
        }); 
        if(arr.length>0){
            showDelProver($(this), function(){
                $.ajax({
                    url: '/merchants/microforum/posts/deleted',
                    data:{id:arr},
                    type:"post",
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        if(data.status==1){
                            tipshow(data.info);
                            setTimeout(function(){
                                location.href="/merchants/microforum/posts/list";
                            },1000);
                        }else{
                            tipshow(data.info,"warn");
                        }
                    },
                    error:function(){
                        tipshow('异常',"warn"); 
                    }
                });
            }, '确定要删除吗?',true,"right");
        }else{
            tipshow("请选择帖子","wran");
        }
    });
});
