// add by 黄新琴 2018/8/16
//删除事件
$('.main_content').on('click','.J_delete',function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id");
    showDelProver($(this), function(){
        $.ajax({
            url:'/merchants/marketing/delDiscount/'+id,
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                layer.closeAll();
                if(res.status==1){
                    tipshow(res.info);  
                    setTimeout(function(){
                        location.reload();
                    },1000);  
                }else{
                   tipshow(res.info,"wran"); 
                }
            },
            error:function(){
                layer.closeAll(); 
                tipshow("异常","wran");
            }
        });  
    }, '确定要删除吗?');
});
// 使失效
$('.main_content').on('click','.J_invalid',function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id");
    showDelProver($(this), function(){
        $.ajax({
            url:'/merchants/marketing/invalidate/'+id,
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                layer.closeAll();
                if(res.status==1){
                    tipshow(res.info);  
                    setTimeout(function(){
                        location.reload();
                    },1000);  
                }else{
                   tipshow(res.info,"wran"); 
                }
            },
            error:function(){
                layer.closeAll(); 
                tipshow("异常","wran");
            }
        });  
    }, '确定活动结束吗?');
});
// 查看数据
$('.main_content').on('click','.J_watch_data',function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id"),
        title =  $(this).data('title');
    $('.J_title').text(title);
    $.ajax({
        url:'/merchants/marketing/getDiscountInfo/'+id,
        type:"get",
        dataType:"json",
        headers: {
            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
        },
        success:function(res){
            if(res.status==1){
                var dataList = [
                    res.data.new_member,
                    res.data.old_member,
                    res.data.num,
                    res.data.amount,
                ];
                $('.item-num').each(function(i){
                    $(this).text(dataList[i]);
                });
                $('.watch-model').show();
            }else{
                tipshow(res.info,"wran"); 
            }
        },
        error:function(){
            layer.closeAll(); 
            tipshow("异常","wran");
        }
    });  
});
$('.close-wraper,.watch-model').click(function(){
    $('.watch-model').hide();
});
$('.watch-wraper').click(function(e){
    e.stopPropagation();
})