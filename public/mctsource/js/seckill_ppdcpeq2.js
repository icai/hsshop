$(function(){
    $(".js-add-picture").click(function(){
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
    }); 
     /**
     * 图片选择后的回调函数
     */
    selImgCallBack = function(resultSrc){
        console.log(resultSrc);
        if(resultSrc.length>0){
            var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
            var sum = parseInt(750 / 750 * 100) / 100
            if( num < sum - 0.2 || num > sum + 0.2){
                tipshow("图片比例非1:1，请重新上传","warn");
                return false
            }
            if(parseInt(resultSrc[0].imgWidth) < 400){
                tipshow("图片尺寸小于400px，请重新上传","warn");
                return false
            }
            $(".js-add-picture").children('span').addClass('hide')
            $("input[name='share_img']").val(resultSrc[0].imgSrc);
            $(".share_img").attr("src",_host+resultSrc[0].imgSrc).parent().removeClass('hide');
            $(".example_box_img").children('img').attr('src',_host + resultSrc[0].imgSrc).removeClass('hide')
        } 
    }
    /*删除图片*/
    $(".delete").click(function(e){
        e.stopPropagation();
        $(".js-add-picture").children('span').removeClass('hide')
        $("input[name='share_img']").val("");
        $(".share_img").attr("src","").parent().addClass('hide');
        $(".example_box_img").children('img').attr('src','').addClass('hide')
    });
    // 表单提交
    var url = '/merchants/marketing/seckill/set'
    if($(".content").attr('data-id')){
        url = '/merchants/marketing/seckill/set/' + $(".content").attr('data-id');
    }
    var getAjaxFlag = true
    $(".js-submit").click(function(){
        /*验证分享内容*/
        var share_title = $('#share_title').val();
        var share_desc = $('#share_desc').val();
        var share_img = $('#share_img').val();
        if(!((share_title && share_desc && share_img) || (!share_title && !share_desc && !share_img))){//都有内容或者都没内容通过
            if(!share_img && share_title && share_desc){
                tipshow("请填写分享图片","warn");
                return false;
            }
            if(!share_title && share_img && share_desc){
                tipshow("请填写分享标题","warn");
                return false;
            }
            if(!share_desc && share_title && share_img){
                tipshow("请填写分享内容","warn");
                return false;
            }
            if(share_img){
                tipshow("请填写分享标题及内容","warn");
                return false;
            }
            if(share_title){
                tipshow("请填写分享内容及图片","warn");
                return false;
            }
            if(share_desc){
                tipshow("请填写分享标题及图片","warn");
                return false;
            }
        }
        var id = $(this).attr('data-id')
        if(!id){
            if(getAjaxFlag){
                getAjaxFlag = false
                getAjax ()
            }
        }else{
            $(".model_box").show();
        }
    });

    /*表单内容验证并提交*/
    function getAjax () {
        var arr = isValidSku();
        var bl = arr.bl;
        if(!bl){
            $(".js_select_goods_div").parent().addClass('error');
        }else{
            $(".js_select_goods_div").parent().removeClass('error');
        }
        var type = $(".type_select:checked").val();
        var bl1 = isValid();
        if(!bl || !bl1)
            return false;
        var data ={
            product_id:$("#goods_id").val(),
            title: $("#title").val(),
            start_at:$("#startTime").val(),
            end_at:$("#endTime").val(),
            tag:$("#tag").val(),
            limit_num:$("#limit_num").val(),
            cancel_minutes:$("#cancel_minutes").val(),
            share_title:$('#share_title').val(),
            share_desc:$('#share_desc').val(),
            share_img:$('#share_img').val(),
            skuData:[],
            type:type//判断秒杀活动存在小程序或者微商城
        };
        for(var i=0;i<arr.data.length;i++){
            var obj = {};
            obj.sku_id = arr.data[i].id || 0;
            obj.seckill_price = arr.data[i].seckill_price;
            obj.seckill_stock = arr.data[i].seckill_stock_num;
            data.skuData.push(obj);
        }
        console.log(data);
        $.ajax({
            type:"post",
            url:url,
            data:data,
            async: false,
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status==1){
                    tipshow(res.info);
                    setTimeout(function(){
                        location.href="/merchants/marketing/seckills";
                    },1000);
                }else{
                    getAjaxFlag = true
                    tipshow(res.info,"warn");
                }
            },
            error:function(){
                getAjaxFlag = true
                console.log("获取商品数据异常");
            }
        });
    }
    //取消订单时间验证
    $("#cancel_minutes").change(function(){
        var value = this.value || 0;
        if(parseInt(value)<5 || parseInt(value)>10){
            this.value = 5;
        }
        tipshow("只能输入5-10之间的数");
    }); 

    //验证sku
    function isValidSku(){
        var bl = true;
        var arr =[];
        var data = hstool.skuData;
        if(data.length>0){
            var k = 0;
            for(var i=0;i<data.length;i++){
                if(data[i].checked){
                    k++;
                    arr.push(data[i]);
                    if(!data[i].seckill_price || !data[i].seckill_stock_num){
                        bl =false;
                    }
                }
            }
            if(k==0){
                bl=false;
            }
        }else{
            bl=false;
        }
        var obj = {};
        obj.bl =bl;
        obj.data = arr;
        return obj;
    }
    
    //开启限制选择框点击事件
    $("#is_limit").click(function(){
        if(this.checked){
            $(".js_span_limit_num").removeClass('none');
        }else{
            $(".js_span_limit_num").addClass('none'); 
        }
    });

    // 表单验证方法
    function isValid(){
        var bl = true;
        $(".valid").each(function(key,el){
            if($(this).val()==""){
                $(this).parents(".wrapper").addClass('error');
                bl=false;
            }else{
                $(this).parents(".wrapper").removeClass('error');
            }
        });
        return bl;
    } 
    //添加秒杀商品点击事件 
    $("body").on("click",".js-add-goods",function(){ 
        hstool.selectGoods({ 
            title:"选择商品", 
            wid:wid,
            _token: $("meta[name='csrf-token']").attr("content"),
            host:_host,
            done: selGoodsCallBack,
            postData:{filter_negotiable:1}
        });
    });
    function selGoodsCallBack(data){   
        var html ='<a href="javascript:;" class="fl"><img src="'+_host+data.img+'" alt="" width="50" height="50" /></a>';
        html +='<div class="fl" style="margin-left:-110px;padding-left:120px;width:100%;"><div class="pr"><p style="height:21px;width:170px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'+data.title+'</p><div style="position: absolute;right:0;top:0;"><button class="hs-btn hs-btn-primary seckill_edit_sku btn-sm">编辑</button><button class="hs-btn hs-btn-primary btn-sm seckill_del_select_goods ml5">删除</button></div></div>';
        html +='<p class="mt10"><span class="seckill_span_price">秒杀价：未设置</span><span class="seckill_span_stock_num ml10">秒杀库存：未设置</span></p><p class="fs12" style="color:#999999">点击“编辑”查看多规格秒杀商品的秒杀价和秒杀库存</p></div>';
        $(".js_select_goods_div").html(html);
        $(".image-box-show").html('<img class="goods-main-photo" src="'+_host+data.img+'" alt="">');
        $(".goods-header .title").html(data.title);
        $(".js_select_goods_div").siblings('.error-message').html("请编辑商品，设置秒杀价格与库存");
        $("#goods_id").val(data.id);
        hstool.setSeckillSku({
            title:"设置秒杀价格和库存",
            pid:data.id, 
            wid:wid,
            price:data.price,
            isEditSku:false,
            stock_num:data.stock,
            _token: $("meta[name='csrf-token']").attr("content"),
            host:_host,
            done: setSkuCallBack
        });
    }
    //删除选择商品
    $("body").on("click",".seckill_del_select_goods",function(){
        var html = '<button type="button" class="hs-btn hs-btn-primary js-add-goods" style="margin:0 10px;">添加秒杀商品</button>';
        $(".js_select_goods_div").html(html);
        $(".js_select_goods_div").siblings('.error-message').html("请选择一个参加秒杀活动的商品");
        $(".image-box-show").html('秒杀商品主图');
        $("#goods_id").val("");
    });

    //编辑sku 
    // $("body").on("click",".seckill_edit_sku",function(){
    //     var id = $(this).attr('data-id');
    //     if(id){
    //         hstool.selectGoods({
    //             title:"选择商品",
    //             wid:wid,
    //             _token: $("meta[name='csrf-token']").attr("content"),
    //             host:_host,
    //             done: selGoodsCallBack,
    //             postData:{filter_negotiable:1}
    //         });
    //     }else{
    //         hstool.setSeckillSku({
    //             title:"设置秒杀价格和库存",
    //             host:_host,
    //             isEditSku:true,
    //             done:setSkuCallBack
    //         })
    //     }
    // });
    function setSkuCallBack(data){
        if(data.show_price)
            $(".seckill_span_price").html("秒杀价："+data.show_price);
        else
            $(".seckill_span_price").html("秒杀价：未设置"); 
        if(data.show_stock_num)
            $(".seckill_span_stock_num").html("秒杀库存："+data.show_stock_num);
        else
            $(".seckill_span_stock_num").html("秒杀库存：未设置");  
    }
    var start = {
        elem: '#startTime',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas){
            $('#startTime').val(datas);
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    }; 
    var end ={
        elem: '#endTime',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(),
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas){
        $('#endTime').val(datas);
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);
    //活动标签文本内容发生变化事件
    $("#tag").change(function(){ 
        var value = this.value;
        if(value.length>5){
            tipshow("最多输入5个字!");
            value = value.substring(0,5);
        }else if(value.length<2){
            tipshow("至少输入2个字!");
            value = "秒杀"; 
        }
        this.value = value;
        $(".price-title").html(value);
    });

    $(".btn_queren").on('click',function () {
        getAjax ()
        $(".model_box").hide();
    })
    $(".btn_close").on('click',function () {
        $(".model_box").hide();
    })
    var detail_id = $("#detil_id").attr('data-id');
    if(detail_id){
        hstool.config.pid = detail_id;
        hstool.config._token = $("meta[name='csrf-token']").attr("content");
        hstool.getSkuInfo();
        if(hstool.skuData[0].checked != 1){
            var lis = $("#sku_ul").children('li')
            var data = []
            for(var j = 0; j < lis.length; j++){
                var li_id = $(lis[j]).attr('data-id');
                for(var i=0;i<hstool.skuData.length;i++) {
                    var sku_id = hstool.skuData[i].id
                    if (li_id == sku_id) {
                        hstool.skuData[i].checked = 1
                        hstool.skuData[i].seckill_price = $(lis[i]).children(".seckill_price").html()
                        hstool.skuData[i].seckill_stock_num = $(lis[i]).children(".seckill_stock").html()
                        data.push(hstool.skuData[i])
                        continue
                    }
                }
            }
            hstool.skuData = data
        }else{
            hstool.skuData[0].seckill_price = $(".seckill_price").html()
            hstool.skuData[0].seckill_stock_num = $(".seckill_stock").html()
            hstool.skuData[0].price = parseInt($("#price_range").attr("data-id"))
            hstool.skuData[0].stock_num = parseInt($("#stock_sum").attr("data-id"))
        }
        console.log(hstool.skuData);
    }
    $("body").on("click",".seckill_edit_sku",function(){
        var detail = $("#detil_id").attr('data-id');
        if(detail){
            hstool.setSeckillSku({
                title:"设置秒杀价格和库存",
                host:_host,
                isEditSku:false,
                pid:detail_id,
                _token:$("meta[name='csrf-token']").attr("content"),
                stock_num:parseInt($("#stock_sum").attr("data-id")),
                price:parseInt($("#price_range").attr("data-id")),
                done:setSkuCallBack
            })
        }else{
            hstool.setSeckillSku({
                title:"设置秒杀价格和库存",
                host:_host,
                isEditSku:true,
                done:setSkuCallBack
            })
        }
    });
}); 