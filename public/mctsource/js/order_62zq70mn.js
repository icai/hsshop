/**
 * 订单列表页
 */
var orderIds = [];//订单号集合 用于批量打印
var expressId ="";//快递编号
$(function(){
    laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
    var start = {
        elem: '#startDate',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: '2009-06-16 23:59:59', //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        event: 'focus',
        istime: true,
        istoday: false,
        choose: function(datas){
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var end = {
        elem: '#endDate',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: '2009-06-16 23:59:59',
        max: '2099-06-16 23:59:59',
        event: 'focus',
        istime: true,
        istoday: false,
        choose: function(datas){
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);
    $('.date-quick-pick').click(function(){
        var date = $(this).data('days');
        var data = getdate(date);
        $('#startDate').val(data.start_date);
        $('#endDate').val(data.end_date);
    })
//  点击批量导出按钮
    $('.js-export').click(function(){
        //筛选条件赋值
        var infoFilter = $('#infoFilter').val();
        var infoFilterValue = $('#infoFilterValue').val();
        var idName = '';
        $('#exportOrderId, #exportBuyerName, #exportBuyerPhone').text('');
        $('#exportOrderId, #exportBuyerName, #exportBuyerPhone').next('input').val('');
        if (infoFilter == 'oid') {
            idName = 'exportOrderId';
        } else if (infoFilter == 'address_name') {
            idName = 'exportBuyerName';
        } else if (infoFilter == 'address_phone') {
            idName = 'exportBuyerPhone';
        }
        $('#' + idName).text(infoFilterValue);
        $('#' + idName).next('input').val(infoFilterValue);
        //订单时间
        $('#exportStart').text($('#startDate').val());
        $('#exportEnd').text($('#endDate').val());
        $('#exportStart').next('input').val($('#startDate').val());
        $('#exportEnd').next('input').val($('#endDate').val());
        //下拉菜单过滤
        $('#exportOrderType').text($('#order_type option[value='+$('#order_type').val()+']').text());
        $('#exportOrderType').next('input').val($('#order_type').val());
        $('#exportPayWay').text($('#pay_way option[value='+$('#pay_way').val()+']').text());
        $('#exportPayWay').next('input').val($('#pay_way').val());
        $('#exportOrderStatus').text($('#status option[value='+$('#status').val()+']').text());
        $('#exportOrderStatus').next('input').val($('#status').val());
        $('#exportExpressType').text($('#express_type option[value='+$('#express_type').val()+']').text());
        $('#exportExpressType').next('input').val($('#express_type').val());
        $('#exportRefundStatus').text($("#refund_status option[value='"+$('#refund_status').val()+"']").text());
        $('#exportRefundStatus').next('input').val($("#refund_status").val());

        if($('#startDate').val()==''){
            tipshow('清先选择日期范围！','warn');
            $('#startDate').focus();
            return;
        }
        if($('#endDate').val()==''){
            tipshow('清先选择日期范围！','warn');
            $('#endDate').focus();
            return;
        }
        $('#myModalExport').show();
        $('.modal-backdrop').show();
        $('.modal-dialog').show();
        //center($('.modal-dialog'));
    })
//  打开发货弹窗
    $('.close').click(function(){
        $('.modal').hide();
        $('.modal-backdrop').hide();
//      $('.modal-dialog').hide();
//      $('.zent-dialog').hide(); 
    })
    var index;//记录点击位置
    // 备注点击
    $(document).on('click','.info',function(){
        index = $(this).parents('tbody').index();
        $('.js-remark').val('')
        $('.js-remark').val($(this).parents('tbody').children('.seller_tip').children('td').children('span').text());
        centerModel($('#base-modal-dialog'));
        $('#baseModal').show();
        $('.modal-backdrop').show();

        $('#order_id').val( $(this).data('id') );
    })
    // 备注提交
    $('.submit_info').click(function(){
        var that = $(this);
        $.post('/merchants/order/setSellerRemark', $('#seller_remark_form').serialize(), function(data) {
            // layer.alert(data.info);
            if ( data.status == 1 ) {
                var beiinfo = $('.js-remark').val();
                $('.ui-table-order tbody').eq(index-1).children('.seller_tip').show();
                $('.ui-table-order tbody').eq(index-1).children('.seller_tip').children('td').children('span').html(beiinfo);
                $('.js-remark').val('');

                tipshow(data.info);
            } else {
                tipshow(data.info, 'warn');
            }
        });

       
        hideModel($('#baseModal'));
    })
    //加星
    $('.star').raty({
        click: function(score, evt) {
            var that = $(this);
            $.post('/merchants/order/setStar', {id: $(this).data('id'), 'star_level': score, '_token': $('meta[name="csrf-token"]').attr('content')}, function(data) {
                // layer.alert(data.info);
                if ( data.status == 1 ) {
                    that.parent().hide();
                    that.parent().prev().show();
                    that.parent().prev().children('.add_pss').hide();
                    that.parent().prev().children('.star_score').children('.score').html(score);
                    that.parent().prev().children('.star_score').show();
                    tipshow(data.info);
                } else {
                    tipshow(data.info, 'warn');
                }
            });
        }
    });
    //移动到加星上显示星星
    $(document).on('mouseenter','.add_pss',function(){
        $(this).parent().hide();
        $(this).parent().next().show();
    })
    //鼠标移出星星标签
    $(document).on('mouseleave','.star_container',function(){
        $(this).hide();
        $(this).prev().show();
    })

    $(document).on('mouseenter','.star_score',function(){
        $(this).parent().hide();
        $(this).parent().next().show();
    })
    //删除评分
    $(document).on('click','.delete_star',function(){
        $(this).parent().hide();
        var that = $(this);
        $.post('/merchants/order/setStar', {id: $(this).data('id'), 'star_level': 0, '_token': $('meta[name="csrf-token"]').attr('content')}, function(data) {
            tipshow(data.info);
            if ( data.status ) {
                that.parent().hide();
                that.parent().prev().show();
                that.parent().prev().children('.add_pss').show();
                that.parent().prev().children('.star_score').hide();
            }
        });
        $(this).next().raty({
            click: function(score, evt) {
                $.post('/merchants/order/setStar', {id: $(this).data('id'), 'star_level': score, '_token': $('meta[name="csrf-token"]').attr('content')}, function(data) {
                    tipshow(data.info);
                    if ( data.status ) {
                        that.parent().hide();
                        that.parent().prev().show();
                        that.parent().prev().children('.add_pss').hide();
                        that.parent().prev().children('.star_score').children('.score').html(score);
                        that.parent().prev().children('.star_score').show();
                    }
                });
            },
            score:0
        });
        $(this).parent().prev().children('.star_score').hide();
        $(this).parent().prev().children('.add_pss').show();
    });

    $("#exportBtns a").click(function(){
        $('#exportType').val($(this).data('export-type'));
        $('#exportForm').submit();
    });
    
    //  分销详情
    $(".ui-table-order .header-row .order-dis").click(function(){
        var id = $(this).data('oid');
            $.ajax({
                "type":"GET",
                "url":"/merchants/order/getDistribute/"+id,
                "data":"",
                "dataType":'json',
                'async': false,
                "success":function(res){
                    
                var td =  $(".order-alert .order-list table");
                td.empty();
                $.each(res.data,function(i,obj){
                    var item;
                    var status='';
                    if (obj.status==0){
                        status='未到账'
                    }else if (obj.status==1){
                        status='已到账'
                    }else if(obj.status==-1){
                        status='已流失';
                    }
                    item = "<tr><td>"+obj.id+"</td><td>"+obj.member.nickname+"</td><td>"+"￥"+obj.money+"</td><td>"+status+"</td></tr>";
                    td.append(item)
                });
                
                },
                "error":function(res){
                    alert("数据访问错误");
                }
            });
        $(".order-alert").show(); 
        $(".order-alert .order-x").click(function(){
            $(".order-alert").hide();
        })
    })
    
});


function center(obj){
    var window_height = $(document).height();
    var height = obj.height();
    obj.css('margin-top',window_height/2-height/2);
}
function getdate(day){
    var today = new Date();
    var obj={
        end_date:'',
        start_date:'',
    };
    today.setHours(0);
    today.setMinutes(0);
    today.setSeconds(0);
    today.setMilliseconds(0);
    today = Date.parse(today);
    // 昨天
    var oneweek = 1000*60*60*24*day;
    yesterday = new Date(today-1);
    oneweek = new Date(yesterday-oneweek+1);
    obj.end_date = formatDate(yesterday);
    obj.start_date = formatDate(oneweek);
    return obj;
}

function formatDate(now) {
    var year=now.getFullYear();
    var month=now.getMonth()+1;
    var date=now.getDate();
    var hour=now.getHours();
    var minute=now.getMinutes();
    var second=now.getSeconds();
    if(minute == '0'){
        minute = '00';
    }
    if(second =='0'){
        second = '00';
    }
    if(hour =='0'){
        hour = '00';
    }

    // 最近7天和最近30天月份使用两位数格式 Herry 20180622
    if (month < 10) {
        month = '0' + month;
    }

    return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
}



/*取消订单*/
$("#btn_clear_order").click(function(e){
    e.stopPropagation();//阻止事件冒泡  
    var t_index = layer.open({
        type: 2,
        title:"取消订单",
        closeBtn:false, 
        move: false, //不允许拖动
        skin:"layer-tskin", //自定义layer皮肤 
        area: ['240px', '170px'], //宽高
        content: '/merchants/order/clearOrder/11204'
    });
    /*取消订单关闭按钮*/
    $("body").on("click",".layui-layer-setwin",function(){
        if(t_index)
            layer.close(t_index);
    });
});

/*修改价格*/
$(".up-order-price").click(function(e){
    e.stopPropagation();//阻止事件冒泡  
    var t_index = layer.open({
        type: 2,
        title:false,
        closeBtn:false, 
        skin:"layer-tskin1", //自定义layer皮肤 
        move: false, //不允许拖动 
        area: ['650px', '270px'], //宽高
        content: '/merchants/order/upOrderPrice/11204'
    });
});



//全选全不选 
$("#cb_all").click(function(){
    var that = this;
    $("input[name='cb_order']").each(function(index,obj){
        obj.checked = that.checked;
    });
});

//批量打印按钮点击事件
$("#btn_express").click(function(){
    //判断是否选择快递了 
    expressId = $("#sel_express").val();
    if(!expressId){
        layer.tips('请选择快递', '#sel_express', {
            tips: [1, '#08BDB7'] //还可配置颜色
        });
        return;
    } 
    orderIds = [];
    $("input[name='cb_order']").each(function(index,obj){
        if(obj.checked){
            orderIds.push(obj.value);
        }
    });
    if(orderIds.length==0){ 
        layer.tips('请选择订单', '#btn_express', {
            tips: [1, '#08BDB7'] //还可配置颜色
        });
        return;
    }

    var t_index = layer.open({
        title: false,
        closeBtn: 0,
        type: 2,
        area: ['0px', '0px'],
        content: '/merchants/order/printExpress'
      
    });  
});
//批量导出点击
$('#btn_export_express').click(function()
{
    orderIds = [];
    $("input[name='cb_order']").each(function(index,obj){
        if(obj.checked){
            orderIds.push($(obj).data('id'));
        }
    });
    if(orderIds.length==0){
        layer.tips('请选择订单', '#btn_export_express', {
            tips: [1, '#08BDB7'] //还可配置颜色
        });
        return;
    }

    $("input[name='cb_order']").removeAttr("checked");
    window.location.href = '/merchants/order/orderExportCsv?orderids='+orderIds;

});
//打印销售单按钮点击事件
$(".daying").click(function(){
    orderIds = [];
    $("input[name='cb_order']").each(function(index,obj){
        if(obj.checked){
         /* orderIds.push(obj.value);*/
         orderIds.push($(obj).data('id'));
        }
    });
    if(orderIds.length==0){ 
        layer.tips('请选择订单', '.daying', {
            tips: [1, '#08BDB7'] //还可配置颜色
        });
        return;
    }
    var t_index = layer.open({
        title: false,
        closeBtn: 0,
        type: 2,
        area: ['0px', '0px'],
       	content: '/merchants/order/salePrint'
    });
});
//享立减订单弹框
var totalPage=0
var nowPage=1
var oid = 0;
$('.share-eventBtn').on('click',function(){
    $('.eSDSpano').html('');
    nowPage=1;
	$('.enjoySubtractionBox').css('display','block');
	$('.enjoySubtraction').css('display','block');
	oid = $(this).data('oid');
	ggg(nowPage,oid)
})
//下一页数据请求
$('.nextPage').on('click',function(){
    if(nowPage<totalPage){
        nowPage++
        ggg(nowPage,oid)
    }
})
//上一页数据请求
$('.previousPage').on('click',function(){
    if(nowPage>1){
        nowPage--
        ggg(nowPage,oid)
    }
})
//数据请求  
function ggg(nowPage,oid){
    $.ajax({
        headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
        type:"get",
        url:"/merchants/order/shareEvent/member/list?oid=" + oid,
        async:true,
        data:{ page: nowPage },
        success:function(res){
            if(res.status==1){
                $(".eSContnentBox").html("")
                var content=res.list
                var length=content.length
                var _thml=''
                for(var i=0;i<length;i++){
                    var nick_name=content[i].nick_name==null? '' : content[i].nick_name
                    _thml+= '<ul class="eSContnent">'
                    _thml+= '<li><img src="'+ content[i].avatar_url +'" alt="" /></li>'
                    _thml+= '<li>'+nick_name+'</li>'
                    _thml+= '<li>'+''+'</li>'
                    _thml+= '<li>'+content[i].derate_time+'</li>'
                    _thml+= '<li>'+content[i].address+'</li>'
                    _thml+='</ul>'
                }
                $('.eSContnentBox').html(_thml)
                $('.eSDSpano').text(res.count)
                $('.maxMun').text(res.numMax)
                totalPage=parseInt(res.count)/8
                var eSDSpanye=Math.ceil(totalPage)
                $('.eSDSpanye').text(eSDSpanye)
            }
        }
    });
}
//享立减订单弹框  点击隐藏
$('.delete_shan').on('click',function(){
	$('.enjoySubtractionBox').css('display','none')
	$('.enjoySubtraction').css('display','none')
})
//批量删除 @author huoguanghui
$(".js-deleteAll").click(function() {
    layer.confirm('是否确定删除', {
      btn: ['确定','取消'] //按钮
    }, function(index){
        var orderIds = [];
        $("input[name='cb_order']").each(function(index,obj){
            if(obj.checked){
             /* orderIds.push(obj.value);*/
             orderIds.push($(obj).data('id'));
            }
        });
        if(orderIds.length==0){ 
            tipshow("请选择订单","warn")
            return;
        }
        $.ajax({
            headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
            type:"post",
            url:"/merchants/order/setAdminDel",
            async:true,
            data:{ orderIds: orderIds },
            success:function(res){
                tipshow("删除订单成功");
                layer.close(index)
                setTimeout(function(){
                    location.reload();
                },1000)
            }
        });
    });
});
/* author 韩瑜 date 2018.7.2
 * {param} orderArr array 所勾选订单
 * {param} pillurl string 打单页面地址
 */
//快速打单
$(".js-quickBill").click(function(index) {
    var orderArr = [];
    $("input[name='cb_order']").each(function(index,obj){
        if(obj.checked){
         orderArr.push($(obj).data('id'));
        }
    });
    if(orderArr.length==0){ 
        tipshow("请选择订单","warn")
        return;
    }
    $.ajax({
        headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
        type:"post",
        url:"/merchants/order/fastPrint",
        async:true,
        data:{
        	orderIds: orderArr
        },
        success:function(res){
        	var pillurl = res.data.url
        	console.log(res.data.url)
        	if(res.data.status == -3){
        		tipshow('店铺不存在','warn');
        		setTimeout(function(){
	                location.reload();
	            },1000)
        	}
        	else if(res.data.status == -4){
				$('#SetPrintType').css('display','block');
        	}
        	else if(res.data.status == -5){
				$('#SetAddress').css('display','block');
        	}
        	else if(res.data.status == -8){
        		tipshow('存在未导入快递管家的订单，无法快速打单','warn');
        	}
        	else if(res.data.status == -9){
        		layer.confirm('存在已打单的订单，是否重复打单',{
        			btn:['确定','取消']
        		},
        		function(){
        			window.open(pillurl); 
        			$('.layui-layer-shade').hide()
        			$('.layui-layer').hide()
        		});
        	}
        	else if(res.data.status == -12){
        		tipshow('存在已关闭的订单，无法快速打单','warn');
        	}
        	else if(res.data.status == -13){
        		layer.confirm('存在已发起退款的订单，是否继续打单',{
        			btn:['确定','取消']
        		},
        		function(){
        			window.open(pillurl); 
        			$('.layui-layer-shade').hide()
        			$('.layui-layer').hide()
        		});
        	}
        	else if(res.data.status == -14){
        		layer.confirm('存在已打单和退款中的订单，是否继续打单',{
        			btn:['确定','取消']
        		},
        		function(){
        			window.open(pillurl); 
        			$('.layui-layer-shade').hide()
        			$('.layui-layer').hide()
        		});
        	}
        	else if(res.data.status == -15){
        		tipshow('存在已退款到账的订单，无法快速打单','warn');
        	}
        	else if(res.data.status == -16){
        		tipshow('存在已完成的订单，无法快速打单','warn');
        	}
        	else if(res.status == 1){
      			window.open(pillurl);
                $('.layui-layer-shade').hide()
                $('.layui-layer').hide()
        	}
        }
    });
});

//导入快递管家
$(".js-importBill").click(function(index) {
    var orderArr = [];
    $("input[name='cb_order']").each(function(index,obj){
        if(obj.checked){
            orderArr.push($(obj).data('id'));
        }
    });
    if(orderArr.length==0){
        tipshow("请选择订单","warn")
        return;
    }
    $.ajax({
        headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
        type:"post",
        url:"/merchants/order/importOrderLogistics",
        async:true,
        data:{
            orderIds: orderArr
        },
        success:function(res){
            if (res.data.status == -1) {
                tipshow('订单不存在','warn');
            }
            else if(res.data.status == -2){
                tipshow('存在自提订单','warn');
            }
            else if(res.data.status == -3){
                tipshow('店铺不存在','warn');
            }
            else if(res.data.status == -4){
                $('#SetPrintType').css('display','block');
            }
            else if(res.data.status == -5){
                $('#SetAddress').css('display','block');
            }
            else if(res.data.status == -8){
                tipshow('该订单已导入快递管家，请重新选择','warn');
            }
            else if(res.data.status == -9){
                tipshow('存在不满足待发货条件的订单，无法导入快递管家','warn');
            }
            //何书哲 2018年11月16日 添加卡密订单、外卖店铺提示
            else if(res.data.status == -10){
                tipshow('存在卡密订单','warn');
            }
            else if(res.data.status == -11){
                tipshow('外卖店铺无法导入快递管家','warn');
            }
            else if(res.status == 1){
                tipshow("订单导入快递管家成功", 'info');
                setTimeout(function(){
                    location.reload();
                },1000)
            }
        }
    });

});


//单个订单删除 @ahthor huoguanghui
$(".js-delete").click(function(){
    var that = $(this);
    layer.confirm('是否确定删除', {
      btn: ['确定','取消'] //按钮
    }, function(index){
        var orderIds = [];
        orderIds.push(that.data('id'));
        $.ajax({
            headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
            type:"post",
            url:"/merchants/order/setAdminDel",
            async:true,
            data:{ orderIds: orderIds },
            success:function(res){
                tipshow("删除订单成功");
                layer.close(index)
                setTimeout(function(){
                    location.reload();
                },1000)
            }
        });
    });
    
})