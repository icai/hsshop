require(['jquery',"layer"], function(jquery,layer) {　　　
    //获取分销摸板信息
    function getTemplate(){
        var url ="/merchants/distribute/getTemplate";
        var data={};
        $.ajax({
            url:url,
            data:data,
            type:"get",
            dataType:"json",
            success:function(json){
                // 保存成功后 移除新增栏目 插入新的ul 
                if(json.status==1){
                    var html ="",data = json.data;
                    console.log(data)
                    for (var i = 0; i < data.length; i++) {
                            html += '<tr data-id="'+data[i].id+'">'+
                                        '<td rowspan="'+data[i].data.length+'" class="distribute-name"><p>'+data[i].title;
                            if(data[i].is_default == 1){
                                html += '【默认】';
                            }           
                            html +=     '</p><input class="w-120" type="text" value="'+data[i].title+'"></td>'+
                                        '<td><p>'+title+'</p><input data-id="'+data[i].data[0].id+'" class="w-120" type="text"name="grade_title" disabled value="'+title+'"></td>'+
                                        '<td><p>'+data[i].data[0].price+'</p><input class="w-70 t-number" type="text" name="price" value="'+data[i].data[0].price+'"></td>'+
                                        '<td><p>'+data[i].data[0].cost+'</p><input class="w-70 t-number" type="text" name="cost" value="'+data[i].data[0].cost+'">%</td>'+
                                        '<td><p>'+data[i].data[0].one+'</p><input class="w-70 t-number" type="text" name="one" value="'+data[i].data[0].one+'">%</td>'+
                                        '<td><p>'+data[i].data[0].sec+'</p><input class="w-70 t-number" type="text" name="sec" value="'+data[i].data[0].sec+'">%</td>'+
                                        '<td><p>'+data[i].data[0].created_at+'</p><input readonly class="w-160" type="text" value="'+data[i].data[0].created_at+'"></td>'+
                                        '<td rowspan="'+data[i].data.length+'" data-id="'+data[i].id+'" class="operate">'+
                                            '<p><a href="javascript:void(0);" class="operate-edit">编辑</a>&nbsp;&nbsp;<a href="javascript:void(0);" data-id="'+data[i].id+'" class="operate-delete">删除</a></p>'+
                                            '<input type="button" href="javascript:void(0);" class="btn-save" value="保存">&nbsp;'+
                                            '<input type="button" href="javascript:void(0);" class="btn-cancel" value="取消">'+
                                        '</td>'+
                                    '</tr>'
                        for(var j=1;j<data[i].data.length;j++){
                            html += '<tr data-id="'+data[i].id+'">'+
                                        '<td><p>'+grade[j-1].title+'</p><input data-id="'+data[i].data[j].id+'" data-gradeid="'+data[i].data[j].grade_id+'" disabled class="w-120" type="text"name="grade_title" value="'+grade[j-1].title+'"></td>'+
                                        '<td><p>'+data[i].data[j].price+'</p><input class="w-70 t-number" type="text" name="price" value="'+data[i].data[j].price+'"></td>'+
                                        '<td><p>'+data[i].data[j].cost+'</p><input class="w-70 t-number" type="text" name="cost" value="'+data[i].data[j].cost+'">%</td>'+
                                        '<td><p>'+data[i].data[j].one+'</p><input class="w-70 t-number" type="text" name="one" value="'+data[i].data[j].one+'">%</td>'+
                                        '<td><p>'+data[i].data[j].sec+'</p><input class="w-70 t-number" type="text" name="sec" value="'+data[i].data[j].sec+'">%</td>'+
                                        '<td><p>'+data[i].data[j].created_at+'</p><input readonly class="w-160" type="text" value="'+data[i].data[j].created_at+'"></td>'+
                                    '</tr>'
                        }
                    }
                    $(".table tbody").html(html); 
                }else{
                   tipshow(json.info,"wram"); 
                }
            },
            error:function(){
                tipshow("异常","wram");
            }
        });
    }
    getTemplate();
    $(".add_template").click(function(){ 
        console.log(grade)
        //判断是否以后新增摸板未保存 
        var flag = 1;
        var is_on_off = $("#is_on_off"); 
        if(is_on_off.val()=="0"){
            var html = '<tr class="edit_status">'+
                            '<td rowspan="'+(grade.length+1)+'" class="distribute-name"><p></p><input class="w-120" type="text" value=""></td>'+
                            '<td><p></p><input class="w-120" type="text"name="grade_title" disabled value="'+title+'"></td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="price" value="100"></td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="cost" value="">%</td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="one" value="">%</td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="sec" value="">%</td>'+
                            '<td><p></p><input readonly class="w-160" type="text"></td>'+
                            '<td rowspan="'+(grade.length+1)+'" class="operate">'+
                                '<p><a href="javascript:void(0);" data-id="" class="operate-edit">编辑</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="operate-delete">删除</a></p>'+
                                '<input type="button" href="javascript:void(0);" data-flag="'+flag+'" class="btn-save" value="保存">&nbsp;'+
                                '<input type="button" href="javascript:void(0);" data-flag="'+flag+'" class="btn-cancel" value="取消">'+
                            '</td>'+
                        '</tr>'
            for(var i=0;i<grade.length;i++){
                html += '<tr class="edit_status">'+
                            '<td><p>'+grade[i].title+'</p><input disabled data-gradeid="'+grade[i].id+'" class="w-120" type="text"name="grade_title" value="'+grade[i].title+'"></td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="price" value="100"></td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="cost" value="">%</td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="one" value="">%</td>'+
                            '<td><p></p><input class="w-70 t-number" type="text" name="sec" value="">%</td>'+
                            '<td><p></p><input readonly class="w-160" type="text"></td>'+
                        '</tr>'
            }
             $(".table tbody").prepend(html)

            is_on_off.val("1");
        }else{
            tipshow("请先保存新增或编辑项");
        }
    });

    //保存分销摸板
    var length = grade.length + 1;
    $("body").on('click','.btn-save',function(e){ 
        var url ="/merchants/distribute/addTemplate";
        var tbody = $(this).parents("tbody");
        var title = $(this).parents('tr').find('.distribute-name input').val();
        // var id = $(this).attr('data-id');
        var zero = 0;
        var three = 0;
        var data = {
            title:title,
            flag:$(this).data('flag'),
            data:[]
        }
        
        
        for(var i=0;i<length;i++){
            // var grade = tbody.find(".edit_status").eq(i).find("input[name='grade_title']").val();
            var price = tbody.find(".edit_status").eq(i).find("input[name='price']").val();
            var one = tbody.find(".edit_status").eq(i).find("input[name='one']").val();
            var sec = tbody.find(".edit_status").eq(i).find("input[name='sec']").val();
            var cost = tbody.find(".edit_status").eq(i).find("input[name='cost']").val();
            var id = tbody.find(".edit_status").eq(i).find("input[name='grade_title']").attr('data-id');
            var grade_id = tbody.find(".edit_status").eq(i).find("input[name='grade_title']").attr('data-gradeid');
            //验证 必填项
            if(price==""){
                tipshow('请填写模拟售价','wram');
                return;
            }
            if(cost==""){
                tipshow('分销成本填写有误','wram');
                return;
            }
            if(one==""){
                tipshow('请填写上级佣金','wram');
                return;
            }
            if(sec==""){
                tipshow('请填写二级佣金','wram');
                return;
            }
            //验证 1.zero，one，sec，three，四个参数，必须有一个为0
            if(zero != 0 && one != 0 && sec !=0){
                tipshow('请设定合理的分销规则（最多只允许三级分销）','wram');
                return;
            }
            //验证 2、zero+one+sec+three+cost = price,必须相等
            console.log(zero);
            if((parseFloat(zero)+parseFloat(one)+parseFloat(sec)+parseFloat(three)+parseFloat(cost)).toFixed(2) !=parseFloat(price).toFixed(2)){
                tipshow('请设定合理的分销规则（售价=成本+佣金）','wram');
                return;
            }
            id = id ? id : '';
            grade_id = grade_id ? grade_id : '';
            var itemData = {
                id:id,
                grade_id:grade_id,
                price:price,
                one:one,
                sec:sec,
                cost:cost,
                three:0,
                zero:0
            }
            data.data.push(itemData);
        }
        var t_index = layer.open({
            type: 1,
            title:"是否确认执行该操作?",
            btn:["确定","取消"],
            yes:function(){
                console.log(data)
                $.ajax({
                    url:url,
                    data:data,
                    type:"POST",
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(json){
                        if(json.status==1){
                            layer.close(t_index);
                            tipshow(json.info);
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }else{
                            tipshow(json.info,"wram"); 
                        }
                    },
                    error:function(){
                        tipshow("异常","wram");
                    }
                });
            },
            closeBtn:false, 
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤 
            area: ['300px', 'auto'], //宽高
            content:'<p style="color:red;margin:10px 15px;">修改之后，所有原先使用该模板规则的商品，将使用修改后规则！</p>'
        });
        
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        }); 
    }); 

    //删除分销摸板 
    $("body").on('click','.operate-delete',function(e){
        e.stopPropagation();//组织事件冒泡
        var id = $(this).data('id')
        showDelProver($(this),function(){
            $.ajax({
                url:'/merchants/distribute/del/' + id,
                data:{},
                type:"get",
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success:function(json){ 
                    if(json.status==1){
                        tipshow(json.info);
                        setTimeout(function(){
                            location.reload();
                        },1000)
                    }else{
                        tipshow(json.info,"wram"); 
                    }
                },
                error:function(){
                    tipshow("异常","wram");
                }
            });
        });
    });



    //验证数字文本框
    $("body").on('keyup','.t-number',function(e){   
        var val = $(this).val().replace(/\D/g,'');
        $(this).val(val)
    });
    //取消编辑或新增
    $("body").on('click','.btn-cancel',function(){
        //判断是否是新增 
        var flag = $(this).attr('data-flag');
        if(flag == 0){ //编辑
            $(this).parents("tbody").find("tr").removeClass("edit_status"); 
        }else{ //新增
            $(this).parents("tbody").find('.edit_status').remove();
        }
        $("#is_on_off").val("0");
    }); 

    //编辑按钮点击事件 
    $("body").on('click','.operate-edit',function(){
        var id = $(this).parents('td').data('id');
        var is_on_off = $("#is_on_off");
        if(is_on_off.val()=="0"){
            $(this).parents('tbody').find("tr[data-id="+id+"]").addClass("edit_status"); 
            $(this).parents("td").find("input").attr('data-flag',0);
            is_on_off.val(1);
        }else{
            tipshow("请先保存新增或编辑项");
        }
    });

    //验证是否是数字
    function isNum(value) {
        var patrn = /^(-)?\d+(\.\d+)?$/;
        if (patrn.exec(value) == null || value == "") {
            return false
        } else {
            return true
        }
    }
});　