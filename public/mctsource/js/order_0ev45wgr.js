/*维权详情*/
"use strict";  //严格模式 
//文本加载完成
$(function(){
    changeDivStyle(o_status); 
    // 倒计时 
    GetRTime(end_time);
    //发表留言
    $("#publish_message").click(function(){
        $("#assist_content").slideToggle(500);
    });
    $(".refundDetail-images").mouseenter(function(){    
        var wValue=4 * $(this).width();
        var hValue=4 * $(this).height();
        $(this).stop().animate({
                        width: wValue,    
                        height: hValue,    
                        left:("-"+(0.5 * $(this).width())/2),    
                        top:("-"+(0.5 * $(this).height())/2)}, 1000);    
    }).mouseleave(function(){    
       var id = $(this).attr('data-id')
        if(id == 1){
            $(this).stop().animate({
                width: "40",
                height: "40",
                left:"0px",
                top:"0px"}, 1000);
        }else{
            $(this).stop().animate({
                width: "60",
                height: "60",
                left:"0px",
                top:"0px"}, 1000);
        }
    }); 
    $("#assist_submit").click(function(){
        var _this = this;
        $(this).attr("disabled","disabled");
        var oid = $("#oid").val();
        var rid = $('#refundID').val();
        var content = $("#txt_message").val();
        if(content==""){
            tipshow("请填写买家留言！","warn");
            $(_this).removeAttr("disabled");
            return;
        }
        var imgs = $("#hid_img").val();
        if(imgs.length>0){
            imgs = imgs.split(",");
        }

        if (imgs.length > 3) {
            tipshow("图片最多三张","warn");
            $(_this).removeAttr("disabled");
            return;
        }

        $.ajax({
            url:'/merchants/order/refundAddMessage/' + rid + '/' + oid,// 跳转到 action
            data:{'content':content,'imgs':imgs},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (data) {
                console.log(data);
                if (data.status == 1){
                    tipshow("举证留言成功");
                    setTimeout(function(){
                        location.reload();
                    },1000) ;
                }else{
                    tipshow("留言失败","warn");
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常", 'warn');
            },
            complete : function(){
               $(_this).removeAttr("disabled");
            }
        });
    });
});

/**
 * 维权流程图 位置显示
 * @param  {[int]} o_status 维权状态 1.买家申请维权 其他.商家处理退款申请 4.退款完成
 */
function changeDivStyle(o_status) {
    if (o_status == 1) {
        $('.stepIco').each(function(key, val) { 
            if (key < 1) {
                $(this).css('background', '#428bca');
                $(this).find('.stepText:last').css('color', '#428bca');
            }
        });
        $('.order_progress li').css('background', '#bbb')
        $('.order_progress li').each(function(key, val) {
            if (key < 1) {
                $(this).css('background', '#428bca');
            }
        });
    } else if (o_status == 4 || o_status == 8) {
        $('.stepIco').each(function(key, val) {
            if (key < 3) {
                $(this).css('background', '#428bca');
                $(this).find('.stepText:last').css('color', '#428bca');
            }
        })
        $('.order_progress li').each(function(key, val) {
            if (key < 4) {
                $(this).css('background', '#428bca');
            }
        })
    }else{
        $('.stepIco').each(function(key, val) {
            if (key < 2) {
                $(this).css('background', '#428bca');
                $(this).find('.stepText:last').css('color', '#428bca');
            }
        })
        $('.order_progress li').each(function(key, val) {
            if (key < 3) {
                $(this).css('background', '#428bca');
            }
        })
    } 
}

 
/**
 * 维权流程图 位置显示
 * @param  {[int]} fileNumLimit 选择数 1-n 填写1为单选，其他多选
 */
function imgCommon(fileNumLimit){
    layer.open({
        type: 2,
        title:false,
        closeBtn:false, 
        // skin:"layer-tskin", //自定义layer皮肤 
        move: false, //不允许拖动 
        area: ['860px', '660px'], //宽高
        content: '/merchants/order/clearOrder/'+fileNumLimit
    }); 
}

/**
 * 图片选择后的回调函数
 */
function selImgCallBack(resultSrc){
    if(resultSrc.length>0){
        var imgs = $("#hid_img").val();
        if(imgs.length>0){
            imgs = imgs.split(",");
            if (imgs.length > 2) {
                tipshow("图片最多三张","warn");
                return;
            }
        }
        var str ="",hid_str="";
        for(var i=0;i<resultSrc.length;i++){
            str +='<img src="'+resultSrc[i]+'" width="60" height="60" style="display: inline-block;"  />';
            hid_str +=resultSrc[i] + ',';
        }
        $("#assist_message_img_result").append(str);
        hid_str = hid_str.substring(0,hid_str.length-1);
        var hid_val = $("#hid_img").val();
        if(hid_val!=""){
            $("#hid_img").val(hid_val+","+hid_str);
        }else{
            $("#hid_img").val(hid_str);
        }
    } 
}

//倒计时
function GetRTime(end_time){ 
    var EndTime= new Date(end_time);
    var NowTime = new Date();
    var t =EndTime.getTime() - NowTime.getTime();
    var d=0;
    var h=0;
    var m=0;
    var s=0;
    if(t>=0){
        d=Math.floor(t/1000/60/60/24);
        h=Math.floor(t/1000/60/60%24);
        m=Math.floor(t/1000/60%60);
        s=Math.floor(t/1000%60);
        var result = d + "天"+h + "小时"+m+"分钟"+s+"秒";
        $("#countdown").html(result);
        setTimeout("GetRTime(end_time)",1000);
    }
}