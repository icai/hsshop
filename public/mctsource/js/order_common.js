/*-------订单公用文件------*/
"use strict";  //严格模式
//修改订单价格
$(".btn_up_price").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id");
    var csrf_token =$("meta[name='csrf-token']").attr("content");
    var total = $(this).attr("data-total");
    var html =getOrderInfoById(id);
    var t_index = layer.open({
        type: 1,
        title:["订单原价(不含运费)"+html.total+"元","font-size:16px;font-weight:bold;margin: 0;padding-left:15px;"],
        closeBtn:false,
        skin:"layer-tskin", //自定义layer皮肤
        move: false, //不允许拖动
        area: ['650px', 'auto'], //宽高
        content: html.str
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
    /*点击确定处理事件*/
    $(".btn_layer_up_price").unbind("click").click(function(){
        var changePrice = $("#changePrice").val();
        var freightPrice = $("#freightPrice").val();
        $.ajax({
            url:'/merchants/order/changePrice',// 跳转到 action
            data:{'id':id,'changePrice':changePrice,'freightPrice':freightPrice},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'json',
            success:function (data) {
                if (data.status == 1){
                    tipshow("修改成功");
                    setTimeout(function(){
                        location.reload();
                    },1000) ;
                    layer.close(t_index);
                }else{
                    tipshow(data.info, 'warn');
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常", 'warn');
            }
        });

    });
});

//获取修改价格信息并生成html字符串
function getOrderInfoById(id){
    var json ={};
    $.ajax({
        url:'/merchants/order/getDetail/'+id,// 跳转到 action
        data:'',
        async: false,
        type:'get',
        cache:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:'json',
        success:function (data) {
            if (data.status == 1){
                json = data.data;
            }else{
                tipshow(data.info, 'warn');
            }
        },
        error : function() {
            // view("异常！");
            tipshow("异常！", 'warn');
        }
    });
//update 张永辉 2018年6月28  订单修改价格逻辑修改
    var str ='<div class="layer-wrap" id="div_up_price"><table class="order-table mb80"><thead><tr><th class="w140">商品</th><th style="width:60px;">单价(元)</th><th style="width:60px;">数量</th><th style="width:60px;">小计(元)</th><th>店铺优惠</th><th>商品改价</th><th>运费(元)</th><th>订单总价(元)</th></tr></thead>';
        str +='<tbody>';
        var orderDetail = json.orderDetail;

        var changePrice = (parseFloat(json.pay_price)-parseFloat(json.freight_price)).toFixed(2);

        for(var i=0;i<orderDetail.length;i++){
            var coupon_price = 0;
            if(typeof json.coupon_price !="undefined")
                coupon_price = parseFloat(json.coupon_price);

            //优惠还包括积分抵扣 Herry 20180518
            if(typeof json.bonus_point_amount !="undefined")
                coupon_price += parseFloat(json.bonus_point_amount);

            if(i==0){
                str +='<tr><td colspan="4" style="padding:0;"><table style="width:100%;"><tbody><tr><td class="bule">'+orderDetail[i].title+'</td><td style="width:60px;">'+orderDetail[i].price+'</td><td style="width:60px;">'+orderDetail[i].num+'</td><td style="width:60px;border-right: 1px solid #E7E8E7;">'+(parseFloat(orderDetail[i].price)*parseFloat(orderDetail[i].num)).toFixed(2)+'</td></tr></tbody></table></td>';
                str +='<td rowspan="'+orderDetail.length+'" style="border-right:1px solid #E7E8E7;padding-left:5px;">'+coupon_price+'</td><td rowspan="'+orderDetail.length+'" style="border-right:1px solid #E7E8E7;padding-left:5px;"><input type="text"class="form-control w70 t_number" id="changePrice" oninput="upChangePrice(this)" value="'+changePrice+'" name=""></td><td rowspan="'+orderDetail.length+'" style="padding-left:5px;"><input type="text"class="form-control w70 t_number" id="freightPrice" oninput="upChangePrice(this)" value="'+json.freight_price+'" name=""><a href="javascript:freightClick();" class="freight t-bule">直接免运费</a></td><td id="lastPrice" rowspan="'+orderDetail.length+'" style="border-left: 1px solid #E7E8E7;padding-left:5px;">'+json.pay_price+'</td></tr>';
            }else{
                 str +='<tr><td colspan="4" style="padding:0;border-top: 1px solid #E7E8E7;"><table style="width:100%;"><tbody><tr><td class="bule">'+orderDetail[i].title+'</td><td style="width:60px;">'+orderDetail[i].price+'</td><td style="width:60px;">'+orderDetail[i].num+'</td><td style="width:60px;border-right: 1px solid #E7E8E7;">'+(parseFloat(orderDetail[i].price)*parseFloat(orderDetail[i].num)).toFixed(2)+'</td></tr></tbody></table></td></tr>';
            }
        }
        str +='</tbody></table>';
        var result_price = (parseFloat(json.products_price)+parseFloat(json.freight_price)+parseFloat(json.change_price)-coupon_price).toFixed(2);
        str +='<div style="position: fixed;bottom:75px;left:0;width:100%; border-bottom:1px solid #E7E8E7;"></div><div class="footer"><p>'+json.address_detail+'</p><p>　　　</p>';
        str +='<button class="btn btn-yes btn_layer_up_price" style="height:30px;">确定</button></div></div>';
    var result ={"str":str,"total":json.products_price};
    return result;
}


//涨价或减价内容发生变化事件
function upChangePrice(obj){
    var products_price = $("#changePrice").val();
    var freight_price =$("#freightPrice").val();
    if (!products_price || !freight_price){
        return false;
    }
    if(isNaN(products_price) || isNaN(freight_price )){
        tipshow("请输入数值",'warn');
        return false;
    }

    var result =(parseFloat(products_price)+parseFloat(freight_price)).toFixed(2);
    $("#lastPrice").html(result);
}

//运费发生变化
function upFreightPrice(obj){
    var products_price = $("#span_products_price").html();
    $("#span_freight_price").html(obj.value);
    var change_price = $("#span_change_price").html();
    var coupon_price = $("#span_coupon_price").html();
    var result = (parseFloat(products_price)+parseFloat(obj.value)+parseFloat(change_price)-parseFloat(coupon_price)).toFixed(2);
    $("#span_result_price").html(result);
}
//直接面运费点击事件
function freightClick(){
    $("#freightPrice").val(0);
    var products_price = $("#changePrice").val();
    var freight_price =$("#freightPrice").val();

    var result =(parseFloat(products_price)+parseFloat(freight_price)).toFixed(2);
    $("#lastPrice").html(result);

}


//关闭订单
$(".btn_clear_order").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var html='<div class="layer-wrap" id="div_clear_order"><div class="t-tips-middle"><select id="sel_clear_order_remark"class="form-control w140"style="margin-left:-20px;"><option value="买家不想要了">买家不想要了</option><option value="没有货了">没有货了</option><option value="不想卖了">不想卖了</option></select></div></div>';
    var id = $(this).attr("data-id");
    var csrf_token =$("meta[name='csrf-token']").attr("content");
    var t_index = layer.open({
        type: 1,
        title:"取消订单",
        btn:["确定","取消"],
        yes:function(){
            $.ajax({
                url:'/merchants/order/clearOrder/'+id,// 跳转到 action
                data:{'remark':$('#sel_clear_order_remark').val()},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
                dataType:'json',
                success:function (data) {
                    if (data.status == 1){
                        location.reload();
                    }else{
                        tipshow(data.info,'warn');
                    }
                },
                error : function() {
                    tipshow("异常",'warn');
                },
                complete : function(){
                   layer.close(t_index);
                }
            });
        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['240px', '170px'], //宽高
        content: html
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});

//延长发货按钮点击
$(".btn_extend_send_goods").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id");
    var t_index = layer.open({
        type: 1,
        title:["延长发货时间","font-size:16px;font-weight:bold;margin: 0;padding-left:15px;"],
        btn:["确定","取消"],
        yes:function(){
            $.ajax({
                url:'/merchants/order/delay/'+id,// 跳转到 action
                data:'',
                type:'get',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tipshow(response.info, 'info');
                        window.location.reload();
                    }else{
                        tipshow(response.info, 'warn');
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常！", 'warn');
                }
            });
            layer.close(t_index);
        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['600px', '220px'], //宽高
        content: '<div class="pl15" id="div_extend_send_goods"><h3 class="mt15">确定延长收货时间？</h3><p class="mt10">延长收货时间可以让买家有更多时间收货，而不急于申请退款;</p><p class="mt10">延长本交易的"确定收货"期限为3天</p></div>'
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});

//修改物流
$(".btn_up_logistics").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var lid = $(this).attr("data-id");
    var str = getLogisticsInfo(lid);
    var t_index = layer.open({
        type: 1,
        title:"修改物流",
        btn:["确定","取消"],
        yes:function(){
            //快递编号express_id 物流编号logistic_no 物流ID id
            var data1=[];
            $(".logistic_box").each(function(index,value){
                var id = $(value).attr('data-id');
                var logistic_no = $(value).find('.logistic_no').val();
                var express_id = $(value).find('.express_id').val();
                var no_express = $(value).find('.radio_express:checked').val();
                var obj = {"id":id,"logistic_no":logistic_no,"express_id":express_id,"no_express":no_express};
                data1.push(obj);
            });
            var tt_index = layer.load(2, {time: 2000});

            //收货地址等信息修改 Herry
            var province_id = $('#div_up_logistics').find('.address-province').val();
            var city_id = $('#div_up_logistics').find('.address-city').val();
            var area_id = $('#div_up_logistics').find('.address-county').val();
            //省市区可以同时不选择 如果选择了省 则市和区必须要选 Herry
            if (province_id && (!city_id || !area_id)) {
                tipshow('请填写完整的收货地址', 'red');
                return false;
            }
            var json_data = {
                "data":data1,
                "address_id":area_id ? area_id : 0,
                "address_province":$('#div_up_logistics').find('.province_new').text(),
                "address_city":$('#div_up_logistics').find('.city_new').text(),
                "address_area":$('#div_up_logistics').find('.area_new').text(),
                "address_detail":$('#address_detail').val(),
                "address_name":$('#address_name').val(),
                "address_phone":$('#address_phone').val(),
            };
            $.ajax({
                url:'/merchants/order/modifyLogistics/'+lid,// 跳转到 action
                data:json_data,
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data) {
                    if (data.status == 1){
                        tipshow(data.info);
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }else{
                        tipshow(data.info, 'warn');
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常！", 'warn');
                },
                complete : function(){
                    layer.close(tt_index);
                }
            });
            
            //alert("点击了保持,订单号为："+id);
            // layer.close(t_index); 关闭弹窗
        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['600px', 'auto'], //宽高
        content:str
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});
$(document).on('click','.radio_express',function(data){
    if($(this).val()==0){
    	$(this).parents('.logistic_box').find('.ems_detail').removeClass('hide');
    }else{
        $(this).parents('.logistic_box').find('.ems_detail').addClass('hide');
    }
})

//获取物流信息并生成html字符串
function getLogisticsInfo(id){
    var json ={};
    $.ajax({
        url:'/merchants/order/modifyLogistics/'+id,// 跳转到 action
        data:'',
        async: false,
        type:'get',
        cache:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:'json',
        success:function (data) {
            if (data.status == 1){
                json = data.data;
            }else{
                tipshow(data.info, 'warn');
            }
        },
        error : function() {
            // view("异常！");
            tipshow("异常！", 'warn');
        }
    });
    var logistics = json.logistics;
    var express = json.express;
    //订单信息 Herry
    var order = json.order;
    var str ='<div class="layer-wrap" id="div_up_logistics"><div class="t-tips"><i class="glyphicon glyphicon-exclamation-sign" style="color: #FF8676;font-weight: 300;"></i>物流信息仅支持一次更正，请仔细填写并核对</div><br>';

    //收货地址 可修改 Herry
    str += '' +
        '<div class="zent-form__control-group ">'+
            '<label class="zent-form__control-label">修改地址：</label>'+
            '<div class="js-area-layout area-layout" data-area-code="">'+
                '<span>'+
                    '<select name="order_province" class="js-province address-province">'+
                        '<option value="">选择省份</option>';
    var provinceList = order.address.provinceList;
    for (i in provinceList) {
        var selected = '';
        
        str +=          '<option value="'+ provinceList[i].id +'" '+ selected +'>'+ provinceList[i].title +'</option>';
    }
    str +=          '</select>'+
               '</span>'+
                '<span class="marl-15">'+
                    '<select name="order_city" class="js-city address-city">'+
                        '<option value="">选择城市</option>';
    if (typeof(order.address.regionList[order.address.province_id]) != 'undefined') {
        var cityList = order.address.regionList[order.address.province_id];
        for (i in cityList) {
            selected = '';
            /*if (cityList[i].id == order.address.city_id) {
                selected = 'selected';
            }*/
            str +=      '<option value="'+ cityList[i].id +'" '+ selected +'>'+ cityList[i].title +'</option>';
        }
    }
    str +=          '</select>'+
                '</span>'+
                '<span class="marl-15">'+
                    '<select name="order_area" class="js-county address-county">'+
                        '<option value="">选择地区</option>';
    if (typeof(order.address.regionList[order.address.city_id]) != 'undefined') {
        var areaList = order.address.regionList[order.address.city_id];
        for (i in areaList) {
            selected = '';
            /*if (areaList[i].id == order.address_id) {
                selected = 'selected';
            }*/
            str +=      '<option value="'+ areaList[i].id +'" '+ selected +'>'+ areaList[i].title +'</option>';
        }
    }
    str +=          '</select>'+
                '</span>'+
            '</div>'+
        '</div>';

    //省市区
    str += '<p class="mt15"><span>省市区：</span>'+
                '<span class="province_new" style="margin-left:14px">'+order.address_province+'</span>' +
                '<span class="city_new">'+order.address_city+'</span>' +
                '<span class="area_new">'+order.address_area+'</span>' +
           '</p>';

    //具体地址
    var address_string = order.address_province + order.address_city + order.address_area;

    //省市区隐藏域
    str += '<input id="default_province" type="hidden" value="'+order.address_province+'" />';
    str += '<input id="default_city" type="hidden" value="'+order.address_city+'" />';
    str += '<input id="default_area" type="hidden" value="'+order.address_area+'" />';

    str += '<p class="mt15"><span>具体地址：</span><input id="address_detail" value="'+ order.address_detail.substring(address_string.length) +'" /></p>';
    str += '<p class="mt15"><span>收货人：</span><input style="margin-left:14px" id="address_name" value="'+ order.address_name +'" /></p>';
    str += '<p class="mt15"><span>电话：</span><input style="margin-left:28px" id="address_phone" value="'+ order.address_phone +'" /></p>';

    for(var i=0;i<logistics.length;i++){
        str +='<div class="mb30 logistic_box" data-id="'+logistics[i].id+'""><p class="mt15"><strong>包裹'+(i+1)+'</strong>共'+logistics[i].num+'类商品</p><p class="mt15"><span>发货方式：</span>';
        if(logistics[i].no_express == 0){
            str +='<label class="radio-inline no_express"><input type="radio" style="top: -6px;" name="no_express_'+i+'" class="radio_express" checked value="0"> 需要物流</label>';
            str +='<label class="radio-inline no_express"><input type="radio" style="top: -6px;" name="no_express_'+i+'" class="radio_express"  value="1"> 无需物流</label></p>';
        }else {
            str +='<label class="radio-inline no_express"><input type="radio" style="top: -6px;" name="no_express_'+i+'" class="radio_express"  value="0"> 需要物流</label>';
            str +='<label class="radio-inline no_express"><input type="radio" style="top: -6px;" name="no_express_'+i+'" class="radio_express" checked value="1"> 无需物流</label></p>';
        }
        if(logistics[i].no_express == 0){
            str += '<p class="mt15 ems_detail">';
        }else{
            str += '<p class="mt15 ems_detail hide">';
        }
        str +='<span>物流公司：</span>';
        str +='<select class="form-control w120 iblock express_id">';
        for(var j=0;j<express.length;j++){
            if(logistics[i].express_id==express[j].id){
                str +='<option value="'+express[j].id+'" selected ="selected ">'+express[j].title+'</option>';
            }else{
                str +='<option value="'+express[j].id+'">'+express[j].title+'</option>';
            }
        }
        str +='</select> <span>运单编号：</span><input type="text" class="form-control w200 iblock logistic_no" value="'+logistics[i].logistic_no+'" placeholder="请填写运单编号" /></p> </div>';
    }
    str +="</div>";
    return str;
}


//同意退款按钮点击事件
$(".btn_agree_refund").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var t_index = layer.open({
        type: 1,
        title:"维权处理",
        btn:["同意退款"],
        yes:function(){
            var tt_index = layer.load(2, {time: 2000});
            $.ajax({
                url:'/merchants/order/refundAgree/'+refundID+'/'+oid+'/'+pid,
                data:{amount: $(this).data('amount')},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data) {
                    layer.close(t_index);
                    if (data.status == 1){
                        tipshow(data.info);
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }else{
                        tipshow(data.info, 'warn');
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常！", 'warn');
                },
                complete : function(){
                    layer.close(tt_index);
                }
            });
        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['600px', 'auto'], //宽高
        content: $("#div_agree_refund")
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});

//同意退款按钮点击事件
$(".btn_agree_return").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var t_index = layer.open({
        type: 1,
        title:"维权处理",
        btn:["同意退货"],
        yes:function(){
            var tt_index = layer.load(2, {time: 2000});
            $.ajax({
                url:'/merchants/order/refundAgreeReturn/'+refundID+'/'+oid+'/'+pid,
                data:{},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data) {
                    layer.close(t_index);
                    if (data.status == 1){
                        tipshow(data.info);
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }else{
                        tipshow(data.info, 'warn');
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常！", 'warn');
                },
                complete : function(){
                    layer.close(tt_index);
                }
            });
        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['600px', 'auto'], //宽高
        content: $("#div_agree_return")
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});

//拒绝退款按钮点击事件
$(".btn_refuse_refund").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id");
    var t_index = layer.open({
        type: 1,
        title:"维权处理",
        btn:["拒绝"],
        yes:function(){
            var tt_index = layer.load(2, {time: 2000});
            var remark = $(".refuse-textarea").val();
            $.ajax({
                url:'/merchants/order/refundDisagree/'+refundID+'/'+oid+'/'+pid,
                data:{"remark":remark},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data) {
                    layer.close(t_index);
                    if (data.status == 1){
                        tipshow(data.info);
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }else{
                        tipshow(data.info, 'warn');
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常！", 'warn');
                },
                complete : function(){
                    layer.close(tt_index);
                }
            });
        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['600px', 'auto'], //宽高
        content: $("#div_refuse_refund")
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});

//同意退款后的打款操作
$(".btn_complete_refund").click(function(e){
    var id = $(this).attr("data-id");
    $.ajax({
        url:'/merchants/order/refundComplete/'+oid+'/'+pid,
        data:'',
        type:'get',
        cache:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:'json',
        success:function (data) {
            if (data.status == 1){
                tipshow(data.info);
                setTimeout(function(){
                    location.reload();
                },1000);
            }else{
                tipshow(data.info, 'warn');
            }
        },
        error : function() {
            tipshow("异常！", 'warn');
        }
    });
});

//查看物流按钮点击事件
$(".btn_view_ogistics").click(function(e){
    e.stopPropagation();//阻止事件冒泡
    var id = $(this).attr("data-id");
    var str = getlogisticsDetail(id);
    var t_index = layer.open({
        type: 1,
        title:"物流详情",
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['800px', 'auto'], //宽高
        content: str
    });
    /*移除事件绑定并绑定取消订单关闭按钮*/
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
});

//根据订单编号获取物流详情
function getlogisticsDetail(id){
    var json ={};
    $.ajax({
        url:'/merchants/order/getLogistics/'+id,// 跳转到 action
        data:'',
        async: false,
        type:'get',
        cache:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:'json',
        success:function (data) {
            json = data;
            if (data.status != 1){
                tipshow(data.info);
            }
        },
        error : function() {
            // view("异常！");
            tipshow("异常！", 'warn');
        }
    });
    var str ='<div class="layer-wrap" id="div_view_ogistics" style="margin:0px;">';
    if(json.status==1){//请求成功
        str+='<ul class="common_nav">';
        for(var i=0;i<json.data.length;i++){
            if(i==0){
                str+='<li class="hover"><a href="javascript:;">包裹'+(i+1)+'</a></li>';
            }else{
                str+='<li><a href="javascript:;">包裹'+(i+1)+'</a></li>';
            }

        }
        str+='<li class="clear"></li></ul><div class="layer-wrap-logistics">';
        for(var i=0;i<json.data.length;i++){
            var logisticsList = json.data[i].data;
            var temp_date ="";
            for(var j=0;j<logisticsList.length;j++){
                if(j==0){
                    if(i!=0){
                        str +='<div class="logistics none">';
                        str+='<p class="mb10">快递名称：'+json.data[i].com+' 单号：'+json.data[i].nu+'</p>';
                    }else{
                        str +='<div class="logistics">';
                        str+='<p class="mb10">快递名称：'+json.data[i].com+' 单号：'+json.data[i].nu+'</p>';
                    }
                    str +='<p class="logistics-p"><span class="logistics-p-date">'+logisticsList[j].date+'</span><span class="logistics-p-week">'+logisticsList[j].week+'</span><span class="logistics-p-time">'+logisticsList[j].now+'</span>'+logisticsList[j].context+'</p>';
                }else if(logisticsList[j].date==temp_date){
                    str +='<p class="logistics-p"><span class="logistics-p-time">'+logisticsList[j].now+'</span>'+logisticsList[j].context+'</p>';
                }else{
                    str +='<p class="logistics-p"><span class="logistics-p-date">'+logisticsList[j].date+'</span><span class="logistics-p-week">'+logisticsList[j].week+'</span><span class="logistics-p-time">'+logisticsList[j].now+'</span>'+logisticsList[j].context+'</p>';
                }
                temp_date = logisticsList[j].date;
                if(j==logisticsList.length-1){
                    str +="</div>";
                }
            }
        }
    }else{//无数据
        str +='<div class="t-tips-middle none">没找到物流信息</div>';
    }
    str +='</div></div>';
    return str;
}

//监听物流弹窗中的导航栏被点击事件
$("body").on('click','#div_view_ogistics .common_nav li',function(e){
    e.stopPropagation();//阻止事件冒泡
    var index =$(this).index();
    $(this).parents(".layer-wrap").find('.layer-wrap-logistics .logistics').eq(index).show().siblings().hide();
    $(this).addClass("hover").siblings().removeClass("hover");
});

//验证数字文本框
// $(".t_number").keypress(function(e){
//     var e=e||event;
//     var val = $(this).val();
//     if(e.keyCode==46){
//         if(val.indexOf('.')>=0){
//             return false;
//         }
//     }
//     if(e.keyCode==45){
//         if(val.indexOf('-')>=0 || val!=''){
//             return false;
//         }
//     }
//     // return (/[\d.]/.test(String.fromCharCode(event.keyCode))); //正数
//     return (/^[\-]?\d*?\.?\d*?$/.test(String.fromCharCode(event.keyCode))); //允许负数
// });
//打开发货弹窗

var oid111 = ""; //订单id
var safe ="";   //维权id
// 填写退货地址弹框
var noAddress = "";
var order_type = '';
if(typeof(address) == 'undefined'){
	noAddress = false;
}else{
	noAddress = address;
}
$(document).on("click",".js-express-goods",function(e){
    oid111 = $($(this).parents()[4]).find(".header-row").data("oid");  //订单id
    e.stopPropagation();//阻止事件冒泡
    // var id = $(this).attr("data-id");
    // var option1 = "";
    // option1 = "<option value='0'>圆通快递</option><option value='1'>申通快递</option>";
    // $('.js-company').append(option1);
    // var option2 = "<option value='0'>中国香港 +999</option><option value='1'>中国台湾 +888</option>"
    // $('.f_selphone').append(option2);
    order_type = $(this).data('type');
    var refund = $(this).data('refund');
        oid111 = $($(this).parents()[4]).find(".header-row").data("oid");  //订单id
        safe = $(this).data('url');
       
    if( noAddress ){  //如果商家没有填写退货地址
        tipshow("请填写退货地址", 'warn');
        
        var t_index = layer.open({
            type: 1,
            title:"发货失败",
            btn:["保存"],
            yes:function(){
                if(sendAdds()){
                    noAddress = false;
                    layer.close(t_index);
                }
            },
            closeBtn:false,
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤
            area: ['742px', 'auto'], //宽高
            content: $('#zent-dialog').html()
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        });
        $(".layui-layer-content").css('height','auto');
       
        return;
    }

    var refund = $(this).data('refund');
        oid111 = $($(this).parents()[4]).find(".header-row").data("oid");  //订单id
        safe = $(this).parents('td').siblings('.status_string').children('a').attr('href')
        if(refund == 1 || refund == 2 || refund == 3 || refund == 6 || refund == 7 || refund == 10){
            $(".model_box").show()
        }else{
            fahuo(oid111)
        }
})
$('.btn_queren').on('click',function () {
    $(".model_box").hide()
    setTimeout(function () {
        fahuo(oid111)
    },100)
})
$(".btn_close").on('click',function () {
    window.location.href = safe
})
$(".model_close").on('click',function () {
    $(".model_box").hide()
})
//输入框判断
$(document).on("blur",".f_input_t",function(e){
	e.stopPropagation();//阻止事件冒泡
	var valu = $(this).val();
	if(valu == ""){
		$(this).css('border-color','#f00');
		$(this).parent().next().show();
		$(this).parent().parent().prev().css('color','#f00');
	}else{
		$(this).css('border-color','#bbb');
		$(this).parent().next().hide();
		$(this).parent().parent().prev().css('color','#333');
	}
})

function fahuo(oid111){
    //显示自定义
	$('.custom_button-1').css('display','inline-block');
	//隐藏取消自定义
    $('.custom_button-2').css('display','none');
    //显示单号列表
    $('.js-company-2').css('display','inline-block');
    //隐藏单号输入框
    $('.js-company-3').css('display','none');
    //设置单号输入框为空值
    $('.js-company-3').val("");
    //隐藏自定义提示
    $('.custom_modal').hide(); 
    $.get("/merchants/order/delivery?oid=" + oid111,function(res){
        var num = 0;
        if(res.data.order.orderDetail.length){
            for(var i=0;i<res.data.order.orderDetail.length;i++){
                if(res.data.order.orderDetail[i]['is_delivery'] == 0){
                    num = num + 1;
                }
            }
        }
        $('.shop_num').html(num);
        $('.js-check-all').removeAttr('checked');
        $(".zent-dialog").show();
        $(".bg000").show();
        var html  = "";
        for(var i = 0; i < res.data.order.orderDetail.length; i++){
            var isSureChecked = "";
            if(res.data.order.orderDetail[i].is_delivery == 1){
                isSureChecked = "disabled";
            }
            var status = '';
            if (res.data.order.orderDetail[i].logistics.id != ''){
                status='已发货';
            }else{
                status='待发货';
            }
            if(order_type != 12){
                html += '<tr><td class="text-right"><input type="checkbox" class="js-check-item" data-id=' + res.data.order.orderDetail[i].id + " " + isSureChecked  +'></td>';
                html += '<td><div><a href="" class="new-window">' + res.data.order.orderDetail[i].title + "</a></div>";
                html += '<div></div></td><td>' + res.data.order.orderDetail[i].num + '</td><td>' + res.data.order.orderDetail[i].logistics.express_name + ' | ' +
                '' + res.data.order.orderDetail[i].logistics.logistic_no + '</td><td class="green">'+status+'</td> </tr>';
            }else{
                html += '<tr><td class="text-right"><input type="checkbox" class="js-check-item" data-id=' + res.data.order.orderDetail[i].id + " " + isSureChecked  +'></td>';
                html += '<td><div><a href="" class="new-window">' + res.data.order.orderDetail[i].title + "</a></div>";
                html += '<div></div></td><td>' + res.data.order.orderDetail[i].num + '</td><td>' + res.data.order.orderDetail[i].carmStock +'</td><td>'+ res.data.order.orderDetail[i].carmActivityName +'</td><td class="green">'+status+'</td> </tr>';
            }      
        }
        /*author 韩瑜 date 2018.7.3
		 * {param} option1 string 默认快递公司
		 * {param} option2 string 默认快递单号
		 */
        var option1 = '<option value="">请选择物流公司</option>';
        var option2 = '<option value="">请选择快递单号</option>';
        for(var j = 0; j < res.data.express.length; j++){
        	if(res.data.logistics_list && res.data.logistics_list.length>0 && res.data.logistics_list[0].express_id == res.data.express[j].id){
        		option1 += '<option selected value=' + res.data.express[j].id + '>';
	            option1 += res.data.express[j].title + "</option>";
                for(var i=0; i<res.data.logistics_list[0].kuaidi_num.length; i++){
                    option2 += '<option value="">'+res.data.logistics_list[0].kuaidi_num[i]+'</option>';
                }
        	}else{
	            option1 += '<option value=' + res.data.express[j].id + '>';
	            option1 += res.data.express[j].title + "</option>";
           }
        }
        //默认快递公司快递号
		$('.js-company-2').html(option2);
        
		/*author 韩瑜 date 2018.7.2
		 * {param} dataId num 快递公司id
		 * {param} dingnum array 快递单号
		 */
		//选择快递公司
        $('.js-company-1').change(function(){
        	$('.js-company-3').hide();
        	$('.js-company-2').css('display','inline-block');
        	$('.custom_button-1').css('display','inline-block');
        	$('.custom_button-2').hide();
        	var dingnum = [];
        	console.log(dingnum)
			var dataId = $('.js-company-1 option:selected').val();//快递公司value
			console.log(dataId)
			var city = "<option value=''>请选择快递单号</option>";
			for (var i=0; i<res.data.logistics_list.length; i++) {
				if(res.data.logistics_list[i].express_id == dataId){
					dingnum = res.data.logistics_list[i].kuaidi_num
				}
			}
			console.log(dingnum)
			if (dingnum && dingnum.length != 0) {
				for(var i = 0; i < dingnum.length; i ++){
					city += '<option>'+dingnum[i]+'</option>'; 
				} 
			}else{
				$('.js-company-2').html('<option value="">请选择快递单号</option>')
				$('.custom_modal').hide();  
			}
			$('.js-company-2').html(city);
			
		});
        
        $(".custom_button-1").click(function(){
        	var dingnum = [];
        	var dataId = $('.js-company-1 option:selected').val();//快递公司value
        	for (var i=0; i<res.data.logistics_list.length; i++) {
				if(res.data.logistics_list[i].express_id == dataId){
					dingnum = res.data.logistics_list[i].kuaidi_num
				}
			}   	
        	if(dingnum.length > 0){
        		$('.custom_modal').css('display','block'); 
        	}
        	else{
        		$('.custom_modal').hide(); 
        	}        	        		
        	console.log($('.js-company-2').children.length);
        	$('.js-company-2').hide();
        	$('.js-company-3').css('display','inline-block');
        	$('.custom_button-1').hide();
        	$('.custom_button-2').css('display','inline-block');
        });
        $(".custom_button-2").click(function(){
        	$('.js-company-3').hide();
        	$('.js-company-2').css('display','inline-block');
        	$('.custom_button-2').hide();
        	$('.custom_button-1').css('display','inline-block');
        	$('.custom_modal').hide();      	
        });


        $(".js-modal-table tbody").html(html);
        //卡密发货add by魏冬冬
        if(order_type == 12){
            $('.widget-order-express form').hide();
            $('.card_mi').show();
            $('.card_nomal').hide();
        }else{
            $('.widget-order-express form').show();
            $('.card_mi').hide();
            $('.card_nomal').show();
        }
        //end
        $(".js-company-1").html(option1);
        $(".control-action").html(res.data.order.address_detail + " ," + res.data.order.address_name + " ," +  res.data.order.address_phone);
    })
}
 // 关闭发货弹框
function close(){
    $(".zent-dialog").hide();
    $(".bg000").hide();
    $(".remover-check").removeAttr('checked');
    $(".js-express-info").removeClass('hide');
    $(".radio input[value = 0]").prop('checked','checked');
}

$(document).on("click", ".zent-dialog-close", close);
$(document).on("click", ".js-cancel", close);

$(".radio_express" ).click(function () {
    if($(this).val() == 1){
        $(".js-express-info").addClass('hide');
    }else {
        $(".js-express-info").removeClass('hide');
    }
});



 // 保存
var isTxwRequest = 1; //发货保存请求是否成功  1 成功 0.未成功
$('body').on("click",".js-save",function(e){
    e.stopPropagation();//阻止事件冒泡
    if(!oid111){
    	oid111=$(".send-goods").data("id");
    }
    if(isTxwRequest){
        isTxwRequest = 0;
        var odid = [];
        var logistic_no = '';
        for(var i = 0; i < $(".js-check-item").length; i++ ){
            if(!$($(".js-check-item")[i]).is(":checked")){
                 continue;
            }
            odid.push($($($(".js-check-item")[i])).data("id"))
        }
        if($('.js-company-3').val() !== null && $('.js-company-3').val() !== undefined && $('.js-company-3').val() !== ''){
        	logistic_no = $('.js-company-3').val()
        }else{	
        	logistic_no = $(".js-company-2").find("option:selected").text();
        }
        var no_express = $('.radio_express:checked').val();

        $.ajax({
            type:"POST",
            url:"/merchants/order/delivery",
            async:false,
            data:{
                oid : oid111,//$(".send-goods").data("id"),
                logistic_no : logistic_no,
                express_id : $('.js-company-1 option:selected').val(),
                odid : "[" + odid.toString() + "]",
                no_express : order_type == 12 ? 1 : $('.radio_express:checked').val(),
                '_token': $('meta[name="csrf-token"]').attr('content'),
                type: order_type
            },
            dataType:'json',
            success:function(res){
                if(res.status == 1){
                    tipshow(res.info);
                    close();
                    window.location.reload();
                }else{
                    tipshow(res.info, 'warn');
                }
            },
            error:function(res){
                tipshow("数据访问错误");
            },
            complete:function(){
                isTxwRequest = 1;
            }
        });
    }
})



 // 全选
$(document).on("click",".js-check-all",function(){
    if($(".js-check-all").is(":checked")){
        $(".js-check-item").each(function () {
             if (!$(this).attr("disabled")) {
                 $(this).prop("checked","checked");
             }
        })
    }else{
        $(".js-check-item").removeProp("checked");
    }
    var num = 0;
    $('.js-check-item:checkbox:checked').each(function(){
        num = num + 1;
    })
    $('.choose_num').html(num);
})
//单选收货地址
$(document).on("click",'.js-check-item',function(){
    var num = 0;
    $('.js-check-item:checkbox:checked').each(function(){
        num = num + 1;
    })
    $('.choose_num').html(num);
})
//添加地址；
var county = "<option value=''>选择地区</option>";
/*省市区三级联动*/
$(document).on('change','.js-province',function(){
	var dataId = $('.js-province option:selected:eq(1)').val();
    var city = "<option value=''>选择城市</option>";
    county = "<option value=''>选择地区</option>";
    if (dataId) {
        var province = json[dataId];
    
        for(var i = 0;i < province.length;i ++){
            city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
        }
    }
	
	$('.js-city').html(city);
    $('.js-county').html(county);
    // $('.js-country').attr("disabled","disabled");

    //订单列表页修改物流模块特殊处理 Herry
    var is_modify_logistics = $('.province_new').text();
    var new_province = $('.js-province option:selected:eq(1)').text();
    if (is_modify_logistics) {
        if (dataId) {
            $('.province_new').text(new_province);
            $('.city_new').text('');
            $('.area_new').text('');
        } else {
            $('.province_new').text($('#default_province').val());
            $('.city_new').text($('#default_city').val());
            $('.area_new').text($('#default_area').val());
        }
    }
})
$(document).on('change','.js-city',function(){
	var dataId = $('.js-city option:selected:eq(1)').val();
    county = "<option value=''>选择地区</option>";
    if (dataId) {
        var city = json[dataId];
        for(var i = 0;i < city.length;i ++){
            county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
        }
    }
    $('.js-county').html(county);

    //订单列表页修改物流模块特殊处理 Herry
    var is_modify_logistics = $('.province_new').text();
    if (is_modify_logistics) {
        if (dataId) {
            $('.city_new').text($('.js-city option:selected:eq(1)').text());
        } else {
            $('.city_new').text('');
        }
        $('.area_new').text('');
    }
});

$(document).on('change','.js-county',function(){
    //订单列表页修改物流模块特殊处理 Herry
    var dataId = $('.js-county option:selected:eq(1)').val();
    var is_modify_logistics = $('.province_new').text();
    if (is_modify_logistics) {
        if (dataId) {
            $('.area_new').text($('.js-county option:selected:eq(1)').text());
        } else {
            $('.area_new').text('');
        }
    }
});

function sendAdds(){
    var name = $(".in-lx:eq(1)").val();
    var mobile = $(".in-sj:eq(1)").val();
    var province_id = $('.js-province option:selected:eq(1)').val();
    var city_id = $('.js-city option:selected:eq(1)').val();
    var area_id = $(".js-county option:selected:eq(1)").val();
    var address = $(".in-dz:eq(1)").val();
    var type = 0;
    var is_default = 1;
    var data = {
        "name":name,
        "mobile":mobile,
        "province_id":province_id,
        "city_id":city_id,
        "area_id":area_id,
        "address":address,
        "is_default":is_default,
        "type":type,
        "_token":$('meta[name="csrf-token"]').attr('content')
    };
    if(!name || !mobile || !province_id || !city_id || !area_id || !address){
        tipshow('请完善信息','warn');
        return false;
    }else{
        $.ajax({
            "type":"POST",
            "url":"/merchants/currency/editAddress",
            "data":data,
            "dataType":'json',
            "success":function(res){
                if(res.status == 1){
                    tipshow('添加地址成功');
                }else{
                    tipshow(data.info,'warn');
                }
            },
            "error":function(res){
                tipshow("数据访问错误");
            }
        });
    }
    return true;
}
// add by 赵彬 2018-8-20
//修改订单收货地址
$(".btn_change_addr").click(function(){
    var oid = $(this).attr("data-oid");
    var mid = $(this).attr("data-mid");
    var html = getChangeAddr(oid);
    var t_index = layer.open({
        type:1,
        title:'修改地址',
        btn:['确认','取消'],
        yes:function(){
            var json_data = {
                "mid":mid,
                'oid':oid,
                "province_id":$('#div_up_logistics').find('.address-province').val(),
                "city_id":$('#div_up_logistics').find('.address-city').val(),
                "area_id":$('#div_up_logistics').find('.address-county').val(),
                "address":$('#address_detail').val(),
                "address_name":$('#address_name').val(),
                "address_phone":$('#address_phone').val(),
            };
            $.ajax({
                url:'/merchants/order/changeSendOrderAddr',// 跳转到 action
                data:json_data,
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data) {
                    if (data.status == 1){
                        tipshow(data.info);
                        layer.close(t_index);
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }else{
                        tipshow(data.info, 'warn');
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常！", 'warn');
                }
            });

        },
        closeBtn:false,
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤
        area: ['600px', 'auto'], //宽高
        content:html
    })
    //关闭并移除修改地址弹窗
    $(".layui-layer-setwin").unbind('click').click(function(){
        if(t_index)
            layer.close(t_index);
    });
})

//获取地址信息并生成html字符串
function getChangeAddr(oid){
    var json = {};
    $.ajax({
        url:'/merchants/order/changeSendOrderAddr',
        type:'get',
        async:false,
        data:{
            oid:oid
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:'json',
        success(res){
            console.log(res)
            if(res.status == 1){
                json = res.data
                console.log(json)
            }else{
                tipshow(res.info,'warn')
            }   
        },
        error(){
            tipshow("异常！", 'warn');
        }
    })
    var str = '';
    str +=
        '<div class="layer-wrap" id="div_up_logistics">'+
        '<div class="zent-form__control-group ">'+
            '<label class="zent-form__control-label">修改地址：</label>'+
            '<div class="js-area-layout area-layout" data-area-code="">'+
                '<span>'+
                    '<select name="order_province" class="js-province address-province">'+
                        '<option value="">选择省份</option>';
    var provinceList = json.provinceList;
    for (var i in provinceList) {
        var selected = '';
        
        str +=          '<option value="'+ provinceList[i].id +'" '+ selected +'>'+ provinceList[i].title +'</option>';
    }
    str +=          '</select>'+
               '</span>'+
                '<span class="marl-15">'+
                    '<select name="order_city" class="js-city address-city">'+
                        '<option value="">选择城市</option>'+
                    '</select>'+
                '</span>'+
                '<span class="marl-15">'+
                    '<select name="order_area" class="js-county address-county">'+
                        '<option value="">选择地区</option>'+
                    '</select>'+
                '</span>'+
            '</div>'+
        '</div>';
    //省市区
    str += '<p class="mt15"><span>省市区：</span>'+
                '<span class="province_new" style="margin-left:14px">'+json.addrData.address_province+'</span>' +
                '<span class="city_new">'+json.addrData.address_city+'</span>' +
                '<span class="area_new">'+json.addrData.address_area+'</span>' +
            '</p>';
    //具体地址
    var address_string = json.addrData.address_province + json.addrData.address_city + json.addrData.address_area;

    //省市区隐藏域
    str += '<input id="default_province" type="hidden" value="'+json.addrData.address_province+'" />';
    str += '<input id="default_city" type="hidden" value="'+json.addrData.address_city+'" />';
    str += '<input id="default_area" type="hidden" value="'+json.addrData.address_area+'" />';

    str += '<p class="mt15"><span>具体地址：</span><input id="address_detail" value="'+ json.addrData.address_detail.substring(address_string.length) +'" /></p>';
    str += '<p class="mt15"><span>收货人：</span><input style="margin-left:14px" id="address_name" value="'+ json.addrData.address_name +'" /></p>';
    str += '<p class="mt15"><span>电话：</span><input style="margin-left:28px" id="address_phone" value="'+ json.addrData.address_phone +'" /></p>';
    str += '</div>';
        return str
}
// end