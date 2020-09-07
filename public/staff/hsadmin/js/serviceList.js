(function(window){
    // 数据渲染
    $(window).on('load',function(){
        getList({name:'',status:''});
    })

    $('.search').click(function(e){
        e.stopPropagation()
        var i = $('.search-value').val();
        var vs = $('#choose option:selected').val();
        if(vs == 'choose'){
            vs = ''
        }
        getList({name:i,status:vs})
    })
    $('#choose').change(function(e){
        e.stopPropagation()
        var vs = $('#choose option:selected').val();
        var serachVal = $('.search-value').val();
        if(vs != 'choose'){
            getList({name:serachVal,status:vs})
        }
    })
    function getList(obj){
        $.ajax({
            url:'/staff/fee/order/select/all',
            data:{
                name:obj.name,
                status:obj.status
            },
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                    getListContent(res.data)
                }
            }
        })
    }
    function getListContent(arr){
        var html = '';
        if(arr.length>0){
            for(var i=0,l=arr.length;i<l;i++){
                var create_time = arr[i].create_time.split(' ')[0]
                html += '<ul class="table_body flex-between"><li><input type="checkbox" value="" /></li>'
                html += '<li>'+ arr[i].widName +'</li>'
                html += '<li>'+ arr[i].mphone +'</li>'
                html += '<li>'+ create_time +'</li>'
                html += '<li>'+ arr[i].serviceVersion +'</li>'
                html += '<li>'+ arr[i].serviceTime +'</li>'
                html += '<li>'+ arr[i].pay_amount +'</li>'
                html += '<li>'+ arr[i].payName +'</li>'
                html += '<li>'+ arr[i].statusName +'</li>'
                html += '<li><a href="javascript:;" class="modify" data-id="'+ arr[i].id +'">查看</a></li>'
                html += '</ul>'
            }
        }else{
            html = '<p class="noData">暂无数据</p>'
        }
        $('.t_body').html(html)
    }
    $('body').on('click','.modify',function(e){
        getOrderDetail(e,$(this).attr('data-id'))
    })
    function getOrderDetail(e,id){
        $.ajax({
            url:'/staff/fee/order/select/one',
            method:'get',
            data:{id:id},
            success:function(res){
                if(res.errCode == 0){
                    var data = res.data;
                    var html = '<div id="pop" class="clearfix"><p class="pull-left title">订单信息</p><div class="pull-left detail">'
                    html+='<p>'+ data.widName +'&nbsp;&nbsp;&nbsp;&nbsp;<span>'+data.serviceVersion+'</span>&nbsp;&nbsp;&nbsp;&nbsp;'+data.serviceTime+'</p>'
                    html+='<p>续费时间：'+ data.create_time +'</p>'
                    html+='<p>金额：'+ data.pay_amount +'元</p>'
                    html+='<p>服务期限：'+ data.serviceTime +'</p>'
                    html+='<p>支付方式：'+ data.payName +'</p>'
                    if(data.statusName != '待审核'){
                        html+='<p>订单状态：<span class="red">'+ data.statusName +'</span></p>'
                        html+='<p>手机号码：'+ data.mphone +'</p></div>'
                    }else{
                        html+='<p>订单状态：<label><input type="radio" name="status" value="2" checked><span>待审核</span></label>&nbsp;&nbsp;&nbsp;'
                        html+='<label><input type="radio" name="status" value="1"><span>支付成功</span></label>&nbsp;&nbsp;&nbsp;'
                        html+='<label><input type="radio" name="status" value="3"><span>支付失败</span> </label></p>'
                        html+='<p>手机号码：'+ data.mphone +'</p>'
                        html+='</div><p class="pull-left remark"><span class="remarktip">汇款备注：</span><textarea id="remark" cols="80" rows="4" value=""></textarea></p>'
                        html+='<p class="submit pull-left"><button class="btn submit-update" data-id="'+ data.id +'">确定</button></p>'
                    }
                    html+='</div>'
                    showCheckPop(e,html)
                }
            }
        })
    }
    function showCheckPop(e,str){
        e.stopPropagation();
        var t_index = layer.open({
            type: 1,
            title: '续费订购订单信息',
            closeBtn:true,
            move: true,
            shadeClose:false,
            area: ['600px', '500px'],
            content: str
        });
        /*取消订单关闭按钮*/
        $("body").on("click",".layui-layer-setwin",function(e){
            closePop(e,t_index)
        });
        $("body").on("click",".renew-close,.layui-layer-shade",function(e){
            closePop(e,t_index)
        });
        //待审核 点击确定 修改状态
        $('body').on('click','.submit-update',function(e){
            e.stopPropagation();
            var params = {}
            params.id = $(this).attr('data-id');
            params.status = $('input:radio[name="status"]:checked').val();
            updateStatus(params);
            location.reload()
        })
    }
    function closePop(e,t_index){
        e.stopPropagation();
        if(t_index)
            layer.close(t_index);
    }
    
    function updateStatus(obj){
        $.ajax({
            url:'/staff/fee/order/update',
            data:obj,
            type:'get',
            success:function(res){
                console.log(res)
            }
        })
    }

})(window)