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
        var search = window.location.search;
        var url = location.href.split('#').toString();
        var param = '';
        //筛选条件赋值
        var infoFilter = $('#infoFilter').val();
        var infoFilterValue = $('#infoFilterValue').val();
        
        param += 'field='+infoFilter+'&searchVal='+infoFilterValue;
        

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
        param += '&start_time='+ $('#startDate').val() + '&end_time='+$('#endDate').val();
        var status = $('#status').val();
        if(status){
            param += '&start_time='+ $('#startDate').val() + '&end_time='+$('#endDate').val()+'&status='+status;
        }
        var fullUrl = '';
        if(search){
            fullUrl = url +'&'+param;
        }else{
            fullUrl = url +'?'+param;
        }
        window.location.href = fullUrl +'&type=express';
        /*$.get(fullUrl,{start_time:$('#startDate').val(),end_time:$('#endDate').val(),type:'express'},function(data){

        });*/
    })
//  打开发货弹窗
    $('.close').click(function(){
        $('.modal').hide();
        $('.modal-backdrop').hide();
    })
    var index;//记录点击位置
    // 备注点击
    $(document).on('click','.info',function(){ 
        index = $(this).parents('tbody').index();
        $('.js-remark').val('')
        $('.js-remark').val($(this).parents('tbody').children('.header-row').children('td').children('span').text());
        // showModel($('#baseModal'),$('#base-modal-dialog'),1000)
        centerModel($('#base-modal-dialog'));
        $('#baseModal').show();
        $('.modal-backdrop').show();

        $('#order_id').val( $(this).data('id'));
    })
    // 备注提交
    $('.submit_info').click(function(){
        var that = $(this);
        var oid = $("#order_id").val();
        var text = $(this).parents('.modal-content').find('textarea').val();
        if(text==""){
            tipshow('请填写商家备注！','wran');
            return;
        }
        $.post('/merchants/order/setSellerRemark', $('#seller_remark_form').serialize(), function(data) {
            if ( data.status == 1 ) {
                var beiinfo = $('.js-remark').val();
                
                $('.ui-table-order tbody').eq(index-1).children('.seller_tip').show();
                $('.ui-table-order tbody').eq(index-1).children('.seller_tip').children('td').children('span').html(beiinfo);
                $('.js-remark').val('');
                $('#tr'+oid).find("span").html(text);
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
    });

    $(".btn_single_node").click(function(){
        var oid = $(this).attr("data-id");
        hstool.open({
            title:"结单操作",
            btn:["提交"],
            footAlign:"right",
            content:'<p style="padding:0 20px;"><input id="txt_single_node" class="form-control" placeholder="请输入核销号" maxlength="50" /></p>',
            btn1:function(){
                var content = $("#txt_single_node").val();
                $.ajax({
                    url:"/merchants/order/finishOrder",
                    type:"post",
                    data:{"orderId":oid,"content":content},
                    dataType:"json", 
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        if(res.errCode==0){
                            tipshow("结单成功");
                            hstool.close();
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }else{
                            tipshow(res.errMsg,"wran");
                        }
                    }
                })
                
            }
        });
    });
	
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
    return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
}

/*加载自定义layer皮肤*/
// layer.config({
//   extend: 'tskin/style.css', //加载您的扩展样式
//   skin: 'layer-ext-tskin'
// });

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


//结单
$('.btn_complete_order').click(function(){
    var _this = $(this);
    $.ajax({
        url:"/merchants/order/finishOrder",
        type:'post',
        data:{'orderId':_this.data('id')},
        datatype:'json',
        headers: {
            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
        },
        success: function(data){
            var json = JSON.parse(data);
            if(json.errCode == 0){
                tipshow('已结单')
                setTimeout(function(){
                    window.location.reload();
                },2000);
            } 
        },
        error: function(){
            alert('error');
        }
    });
});