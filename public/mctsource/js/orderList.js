(function(window){
    $(window).on('load',function(){
        //数据渲染
        $.ajax({
            url:'/merchants/fee/order/select/all',
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                    var tableContent = res.data
                    if(tableContent.length>0){
                        $('.noData').addClass('hide')
                        tableRender(tableContent)
                    }else{
                        $('.noData').removeClass('hide')
                    }
                }
                return;
            }
        })
    })

    function tableRender(arr){
        var html = '';
        for(var i=0,l=arr.length;i<l;i++ ){
            var red = arr[i].statusName == '待审核'?'red':'';
            var statusName = '';
            if(arr[i].statusName == '待支付'){
                statusName = '<span class="waitPay" data-order="'+arr[i].id+'">'+arr[i].statusName+'</span>'
            }else{
                statusName = arr[i].statusName
            }
            html += '<ul class="clearfix" data-id="'+ arr[i].id +'">';
            html += '<li>'+ arr[i].create_time +'</li>'
            html += '<li>'+ arr[i].widName +'</li>'
            html += '<li>'+ arr[i].serviceVersion +'</li>'
            html += '<li>'+ arr[i].serviceTime +'</li>'
            html += '<li>'+ arr[i].products_amount +'</li>'
            html += '<li>'+ arr[i].payName +'</li>'
            html += '<li class="'+red+'">'+ statusName +'</li>'
            html += '<li><a class="delete" href="javascript:void(0);">删除</a></li>'
            html += '</ul>'
        }
        $('.table-body').html(html);
        return;
    }

    // 删除事件
    $('body').on('click','.delete',function(e){
        var _this = this;
        e.stopPropagation();
        hstool.msg('是否删除本该条续费订购订单信息？')
        $('body').on('click','.btn-yes',function(){
            deleteOrder($(_this).parents('ul'));
            hstool.close()
        })
        return;
    })
    
    //删除某个服务列表 id 参数
    function deleteOrder(obj){
        var id = $(obj).attr('data-id');
        $.ajax({
            url:'/merchants/fee/order/delete',
            data:{id:id},
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                    tipshow('成功删除');
                    //把那条数据给删了
                    $(obj).remove();
                }else{
                    tipshow("异常", "wram");
                }
                return;
            }
        })
    }

    //点击待支付
    $('body').on('click','.waitPay',function(e){
        e.stopPropagation();
        var orderId = $(this).attr('data-order');
        location.href = host + 'merchants/capital/fee/order/pay/list?orderId='+orderId;
        console.log()
    })
})(window)