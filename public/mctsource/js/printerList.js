$(function(){
    // 新建打印机
    $("#add_printer").click(function(){
        var t_index = layer.open({
            type: 1,
            title:'新建打印机',
            closeBtn:false, 
            btn:['确认'],
            shadeClose:true,
            skin:"layer-tskin",
            move: false, //不允许拖动
            area: ['600px', '500px'], //宽高
            content: $(".add_printer_model").html(),
            yes:function(index, layero){
                $.ajax({
                    url:'/merchants/delivery/addPrinter',
                    type:'post',
                    data:{
                        device_brand:1,    //1为365品牌
                        device_name:layero.find(".device_name").val(),
                        device_no:layero.find(".device_no").val(),
                        key:layero.find(".key").val(),
                        times:$('input[name="times"]:checked').val(),
                    },
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        if(res.status == 0){
                            tipshow(res.info,'warn');
                        }else{
                            tipshow(res.info,'info');
                            layer.closeAll();
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    }
                })
            }
        })
        /*取消订单关闭按钮*/
        $("body").on("click",".layui-layer-setwin",function(){
            if(t_index)
                layer.close(t_index);
        });
    })
    
    //删除 
    $(".delete-btn").click(function(){
        var id = $(this).attr("data-id")
        $.ajax({
            url:'/merchants/delivery/delPrinter',
            type:'get',
            data:{
                printer_id:id
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                console.log(res)
                if(res.status == 1){
                    tipshow(res.info,'info')
                    $(".delete-model").addClass("none")
                    setTimeout(() => {
                       window.location.reload(); 
                    }, 2000);
                }
               
                
            }
        })
    })

    // 取消删除
    $(".cancel-btn").click(function(){
        $(".delete-model").addClass("none")
    })

    // 获取打印机列表
    var html = '';
    for(var i=0;i<printList.length;i++){
        if(printList[i].is_on == 1){
            printList[i].linkStatus = '已选用';
            printList[i].linkType = '弃用';
            printList[i].link = '';
        }else{
            printList[i].linkStatus = '已弃用';
            printList[i].linkType = '选用';
            printList[i].link = 'link';
        }
        if(printList[i].device_brand == 1){
            printList[i].device_brand = '365'
        }else{
            printList[i].device_brand = '其他'
        }

        html += '<tr>'+
                    '<td>'+printList[i].device_name+'</td>'+
                    '<td>'+printList[i].device_brand+'</td>'+
                    '<td>'+printList[i].device_no+'</td>'+
                    '<td>'+printList[i].linkStatus+'</td>'+
                    '<td>'+printList[i].printer_status+'</td>'+
                    '<td>'+
                        '<a href="javascript:void(0);" class="operate-item break '+printList[i].link+'" data-type="'+printList[i].is_on+'" data-id="'+printList[i].id+'">'+printList[i].linkType+'</a>'+
                        '<a href="javascript:void(0);" class="operate-item set" data-id="'+printList[i].id+'">设置</a>'+
                        '<a href="javascript:void(0);" class="operate-item delete" data-id="'+printList[i].id+'">删除</a>'+
                    '</td>'+
                '</tr>'
    }
    $(".table tbody").html(html)
    // 点击删除弹出弹窗
    $(".delete").click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(".delete-model").css({"top":top-25,"left":left-278});
        $(".delete-model").removeClass("none")
        var id = $(this).attr('data-id')
        $(".delete-btn").attr('data-id',id)
    
    })
    // 关闭弹出层
    $('body').click(function(event){
        var _con = $('.delete-model');
        if(!_con.is(event.target) && _con.has(event.target).length === 0){
            $(".delete-model").addClass('none')
        }
    })
    // 断开连接
    $(".break").click(function(){
        var id = $(this).attr("data-id");
        var is_on = $(this).attr("data-type");
        var that = $(this)
        $.ajax({
            url:'/merchants/delivery/setPrinter',
            type:'get',
            data:{
                printer_id:id,
                type:is_on == 1? 'off' : 'on'
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                console.log(res)
                if(res.status == 1){
                    tipshow(res.info,'info');
                    if(is_on == 1){
                        that.addClass('link').text('选用')
                        
                    }else{
                        that.removeClass('link').text('弃用')
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }else{
                    tipshow(res.info,'wran');
                }
            }
        })
    })
    // 设置打印机
    $(".set").click(function(){
        var id = $(this).attr("data-id");
        var t_index = layer.open({
            type: 1,
            title:'编辑打印机',
            closeBtn:false, 
            btn:['确认'],
            shadeClose:true,
            yes:function(index, layero){
                $.ajax({
                    url:'/merchants/delivery/addPrinter',
                    type:'post',
                    data:{
                        id:id,
                        device_brand:1,    //1为365品牌
                        device_name:layero.find(".device_name").val(),
                        device_no:layero.find(".device_no").val(),
                        key:layero.find(".key").val(),
                        times:$("input[name=times]:checked").val(),
                    },
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        if(res.status == 0){
                            tipshow(res.info,'warn')
                        }else{
                            tipshow(res.info,'info');
                            layer.closeAll();
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    }
                })
            },
            skin:"layer-tskin",
            move: false, //不允许拖动 
            area: ['600px', '500px'], //宽高
            content: $(".add_printer_model").html(),
            success:function(){
                $.ajax({
                    url:'/merchants/delivery/queryPrinter',
                    type:'get',
                    data:{
                        id:id
                    },
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        console.log(res)
                        $(".device_brand").val(res.data.device_brand);
                        $(".device_name").val(res.data.device_name);
                        $(".device_no").val(res.data.device_no);
                        $(".key").val(res.data.key);
                        $(".layui-layer-content input[name=times]").eq(res.data.times-1).attr("checked",true)
                    }
                })
            }
        })
        /*取消订单关闭按钮*/
        $("body").on("click",".layui-layer-setwin",function(){
            if(t_index)
                layer.close(t_index);
        });
    })
})

// 限制input输入的最大字数
function checkLength(dom,maxLength){
    var j = 0;
    for(var i=0;i<dom.value.length;i++){
        var reg = /[\u4e00-\u9fa5]/;
        if(reg.test(dom.value[i])){
            j += 2;
        }else{
            j++;
        }
        if(j>maxLength){
            dom.value = dom.value.substr(0,i);
            break;
        }
    }
}