//删除事件
    $('body').on('click','.delete',function(e){
        e.stopPropagation();//阻止事件冒泡
        var that = $(this);
        var id = $(this).attr("data-id");
        showDelProver($(this), function(){
            $.ajax({
                url:'/merchants/share/event/del',
                data:{
                    id:id,
                    status:1,
                    wid:wid
                },
                type:"get",
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success:function(json){
                    layer.closeAll(); 
                    //保存成功后 移除新增栏目 插入新的ul 
                    if(json.status==1){
                        tipshow(json.info);  
                        setTimeout(function(){
                            location.reload();
                        },1000);  
                    }else{
                       tipshow(json.info,"wram"); 
                    }
                },
                error:function(){
                    layer.closeAll(); 
                    tipshow("异常","wram");
                }
            });  
        }, '确定要删除吗?');
    });
    
    //使失效事件
    $('body').on('click','.invalid',function(e){
        var id = $(this).attr("data-id");
        var t_index = layer.open({
            type: 1,
            title:"确定让享立减活动失效?",
            btn:["确定","取消"],
            yes:function(){  
                $.ajax({
                    url:'/merchants/share/event/del',
                    data:{
                        id:id,
                        type:1,
                        wid:wid
                    },
                    type:"get",
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(json){
                        layer.closeAll(); 
                        //保存成功后 移除新增栏目 插入新的ul 
                        if(json.status==1){
                            tipshow(json.info);  
                            setTimeout(function(){
                                location.reload();
                            },1000);  
                        }else{
                           tipshow(json.info,"wram"); 
                        }
                    },
                    error:function(){
                        layer.closeAll(); 
                        tipshow("异常","wram");
                    }
                });  
            },
            closeBtn:false, 
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤 
            area: ['300px', 'auto'], //宽高
            content:'<p style="margin:10px 15px;">进行中的商品活动一经失效，活动立即结束且不可再编辑。</p>'
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        }); 
    });  
    //一键翻新
    $('body').on('click','.btn_refresh',function(){
        $.ajax({
            type:"post",
            url:"/merchants/share/event/refresh",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status == 1){
                    tipshow(res.info);
                    $('#myModal').hide();
                    $('.modal-backdrop').hide();
                    setTimeout(function(){location.reload();},300)                  
                }
            }
        });
    })
    /** 
     * 处理红包数据
     * @author  huoguanghui
     */
    if(reduceData.is_open == 0){
        $(".switchEnvelope").removeClass("actived");
        $(".openEnvelopeModal").addClass("none");
    }else{
        $(".switchEnvelope").addClass("actived");
        $(".openEnvelopeModal").removeClass("none"); 
    }
    //处理
    //处理享立减设置数据
    $("input[name='share_title']").val(reduceData.share_title);//分享标题赋值
    $("input[name='is_open_card'][value="+reduceData.is_open_card+"]").attr("checked",true);
    //分享图片设置
    if(reduceData.share_img){//卡片图片存在
        $("#setXiang .share_img").show();
        $("#setXiang .share_img .img-goods").attr("src",reduceData.share_img);
        $("#setXiang .share_img_add").text("+修改")
    }else{
        $("#setXiang .share_img").hide();
        $("#setXiang .share_img_add").text("+添加");
    }
    /** 
     * 享立减设置图片添加
     * @author  huoguanghui
     */
    $(".js-upload-image").click(function(){     
        var img_add_num = $(this).data('imgadd');
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            move: false, //不允许拖动 
            area: ['860px', '660px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
         /**
         * 图片选择后的回调函数
         */
        selImgCallBack = function(resultSrc){ 
            if(resultSrc.length > 0){
                console.log(img_add_num)
                if (img_add_num == 1) {//添加卡片
                    $(".card_img").show();
                    $(".card_img .img-goods").attr("src",resultSrc[0]);
                    $(".card_img_add").text("+修改")
                } else {//添加分享图片
                    $(".share_img").show();
                    $(".share_img .img-goods").attr("src",resultSrc[0]);
                    $(".share_img_add").text("+修改")
                }
            } 
        }
    });
    
    /** 
     * 享立减设置提交
     * @author huoguanghui
     */
    $(".xiang_sure").click(function(){
        var share_img = $(".share_img .img-goods").attr("src");
        var share_title = $("input[name='share_title']").val();
        if( !share_title ){
            tipshow("请添加分享标题","warn");
            return false;
        }
        if( !share_img ){
            tipshow("请添加分享图片","warn");
            return false;
        }
        var data = {
            id: reduceData.id,
            source: "share",
            is_open_card: $("input[name='is_open_card']:checked").val(),
            share_title: share_title,
            share_img: share_img
        }
        $.ajax({
            type:"post",
            url:"/merchants/share/event/rewardSet",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            data:data,
            success:function(res){
                if(res.status == 1){
                    tipshow("设置享立减成功");
                    $("#setXiang").modal("hide"); 
                }else{
                    tipshow(res.info,"warn");
                }
            }
        });
    })
    /** 
     * 红包开关
     * @author  huoguanghui
     */
    $(".switchEnvelope").click(function(){
        $.ajax({
            type:"get",
            url:"/share/event/rewardSet",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status == 1){
                    if( !(res.data instanceof Array) ){//数据为空为空数组  不为空则存在数据
                        $("input[name='type'][value="+res.data.type+"]").prop("checked",true);
                        if(res.data.type == 0){
                            $(".config_item").removeClass("none");
                            $(".config_item").eq(1).addClass("none");
                            $("input[name='fixed']").val(res.data.fixed_money);
                        }else{
                            $(".config_item").removeClass("none");
                            $(".config_item").eq(0).addClass("none");
                            $("input[name='minimum']").val(res.data.minimum);
                            $("input[name='maximum']").val(res.data.maximum);
                        }
                    }       
                }
            }
        });
    })
    /**
     * 开启红包功能
     * @author  huoguanghui
     */
    $(".rule_sure").click(function(){
        var is_open = 0;
        if($(".switchEnvelope").hasClass("actived")){//去关闭
            is_open = 0;
        }else{//去开启
            is_open = 1;
        }
        $.ajax({
            type:"get",
            url:"/merchants/share/event/openReward",
            data:{
                id:reduceData.id,
                is_open:is_open
            },
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status == 1){
                    reduceData.is_open = is_open;
                    if(is_open == 1){//打开红包功能
                        $(".switchEnvelope").addClass("actived");
                        $(".openEnvelopeModal").removeClass("none");
                    }else{//关闭红包功能
                        $(".switchEnvelope").removeClass("actived");
                        $(".openEnvelopeModal").addClass("none");
                    }
                    $("#envelopeRule").modal("hide"); 
                }else{
                    tipshow(res.info,"warn");
                }
            }
        });
    })
    /**
     * 红包编辑  
     * @author  huoguanghui
     */
    $(".openEnvelopeModal").click(function(){
        $.ajax({
            type:"get",
            url:"/merchants/share/event/rewardSet",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status == 1){
                    if( !(res.data instanceof Array) ){//数据为空为空数组  不为空则存在数据
                        id = res.data.id;
                        $("input[name='type'][value="+res.data.type+"]").prop("checked",true);
                        if(res.data.type == 0){
                            $(".config_item").removeClass("none");
                            $(".config_item").eq(1).addClass("none");
                            $("input[name='fixed']").val(res.data.fixed_money);
                        }else{
                            $(".config_item").removeClass("none");
                            $(".config_item").eq(0).addClass("none");
                            $("input[name='minimum']").val(res.data.minimum);
                            $("input[name='maximum']").val(res.data.maximum);
                        }
                    }       
                }
            }
        });
    })
    /** 
     * 红包类型切换
     * @author huoguanghui
     */
    $("input[name='type']").change(function(){
        if($(this).val() == 0){
            $(".config_item").removeClass("none");
            $(".config_item").eq(1).addClass("none");
        }else{
            $(".config_item").removeClass("none");
            $(".config_item").eq(0).addClass("none");
        }
    })
    /**
     * 红包设置  
     * @author  huoguanghui
     */
    $(".setEnvelope").click(function(){
        var type = $("input[name='type']:checked").val();//红包状态
        var fixed = $("input[name='fixed']").val();//固定减值
        var minimum = $("input[name='minimum']").val();//最少减值
        var maximum = $("input[name='maximum']").val();//最大减值
        var data = {};
        if(type == 0){//固定
            if(!fixed){
                tipshow("请设置红包值","warn");
                return false;
            }
            data = {
                id:id,
                type:type, 
                fixed:fixed
            }
        }else{//随机
            if(!minimum){
                tipshow("请设置红包值","warn");
                return false;
            }
            if(!maximum){
                tipshow("请设置红包值","warn");
                return false;
            }
            if(maximum - minimum <= 0){
                tipshow("红包范围设置不正确,请查证后重新输入","warn");
                return false;
            }
            data = {
                id:reduceData.id,
                type:type, 
                minimum:minimum,
                maximum:maximum
            }
        }
        $.ajax({
            type:"post",
            url:"/merchants/share/event/rewardSet",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            data:data,
            success:function(res){
                if(res.status == 1){
                    tipshow("设置红包成功");
                    $("#envelopeModal").modal("hide"); 
                }else{
                    tipshow(res.info,"warn");
                }
            }
        });
    })