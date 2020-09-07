$(function() {
    // 客服跳转
    $('.kefu_top').click(function(){
        var tempwindow= window.open('about:blank', '_blank'); // 先打开页面
        $.get(chatUrl + '/list/message/orderOfflineMsg',{shopId:wid,custId:custId,weiUserId:weiUserId,joinway:joinway},function(data){
            data = typeof data === 'string' ? JSON.parse(data) : data;
            if(data.code == 100){
                tempwindow.location= chatUrl + "/#/transfer?shopId="+ wid +"&custId="+ custId +"&sign="+ sign +"&custJoinWay=PC"; // 后更改页面地址
            }
        })
    })
	//  分销详情
	var clickState = 0;
    $(".content-region .order-dp").click(function(){ 	
        if( clickState == 1){
            //如果状态为1就什么都不做
        }else{
            clickState = 1;  //如果状态不是1  则添加状态 1
            var id=$(this).data('oid');
            Aajax(id);
        }
        function Aajax(id){ 
			$.ajax({
				"type":"GET",
				"url":"/merchants/order/getDistribute/"+id,
				"data":"",
				"dataType":'json',
				"success":function(res){
					
				var td =  $(".order-alert .order-list table");
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
					tipshow("数据访问错误");
				}
			});
        }
    	$(".order-alert").show(); 
    	$(".order-alert .order-x").click(function(){
    		$(".order-alert").hide();
    	})
    });
	
    changeDivStyle();
    var div_score = $(".ui-star").attr("data-click");
    //星星初始化
    $('.ui-star').raty({
        score: div_score,
        click: function(score, evt) {
            var data = {
                id: $(this).data('id'),
                'star_level': score,
                '_token': $('meta[name="csrf-token"]').attr('content')
            }
            $.post('/merchants/order/setStar', data, function(data) {
                if (data.status == 1) {
                    tipshow(data.info);
                } else {
                    tipshow(data.info, 'warn');
                }
            });
        }
    });
    //去星点击
    $('.delete-star').click(function() {
        var data = {
            id: $(".ui-star").data('id'),
            'star_level': 0,
            '_token': $('meta[name="csrf-token"]').attr('content')
        }
        $.post('/merchants/order/setStar', data, function(data) {
            if (data.status == 1) {
                tipshow(data.info);
            } else {
                tipshow(data.info, 'warn');
            }
        });
        $('.ui-star').raty({ score: 0 });
    });
    //点击备注  
    $(document).on('click', '#clickRemark', function() {
        centerModel($('#base-modal-dialog'));
        $('#baseModal').show();
        $('.modal-backdrop').show();
    })

    /**
     * 手动标记退款完成
     * @author 许立 2018年7月2日
     */
    $(document).on('click', '#manually_refund_success', function() {
        // 获取订单数据
        $('#refund_oid').val($(this).data('oid'));
        $('#refund_pid').val($(this).data('pid'));
        $('#refund_prop_id').val($(this).data('prop-id'));
        
        // 弹框显示
        centerModel($('#base-modal-dialog-refund'));
        $('#baseModalRefund').show();
        $('.modal-backdrop').show();
    });

    // 打开发货弹框
    $(document).on("click", ".send-goods", function() {
        $.get("/merchants/order/delivery?oid=" + $(".send-goods").data("id"),function(res){

            $(".zent-dialog").show();
            $(".bg000").show();

            var html  = "";
            for(var i = 0; i < res.data.order.orderDetail.length; i++){

                var isSureChecked = "";
                if(res.data.order.orderDetail[i].is_delivery == 1){
                    isSureChecked = "disabled";
                }
                var isdelivertable = '未发货';
                var deliverNo = "";
                if(res.data.order.orderDetail[i].logistics.id != ""){
                    isdelivertable = '已发货'
                    deliverNo = res.data.order.orderDetail[i].logistics.express_name+' | '+res.data.order.orderDetail[i].logistics.logistic_no;
                }

                html += '<tr><td class="text-right"><input type="checkbox" class="js-check-item" data-id=' + res.data.order.orderDetail[i].id + " " + isSureChecked  +'></td>';
                html += '<td><div><a href="" class="new-window">' + res.data.order.orderDetail[i].title + "</a></div>";
                html += '<div></div></td><td>' + res.data.order.orderDetail[i].num + '</td><td>'+deliverNo+'</td><td class="green">'+isdelivertable+'</td> </tr>';
            }
            var option = "";
            for(var j = 0; j < res.data.express.length; j++){
                option += '<option value=' + res.data.express[j].id + '>';
                option += res.data.express[j].title + "</option>";
            }

            $(".js-modal-table tbody").html(html);
            $(".js-company").append(option);
            $(".control-action").html(res.data.order.address_detail + " ," + res.data.order.address_name + " ," +  res.data.order.address_phone);
        })


    })

    // 关闭发货弹框
    function close(){
        $(".zent-dialog").hide();
        $(".bg000").hide();
    }
    $(document).on("click", ".zent-dialog-close", close);

    // 保存
    $(document).on("click",".js-save",function(){
        var odid = [];

        for(var i = 0; i < $(".js-check-item").length; i++ ){

            if(!$($(".js-check-item")[i]).is(":checked")){
                continue;
            }
            odid.push($($($(".js-check-item")[i])).data("id"))
        }

        $.post("/merchants/order/delivery",{
            oid : $(".send-goods").data("id"),
            logistic_no : $(".js-number").val(),
            no_express :$(".radio_express").val(),
            express_id : $('.js-company option:selected') .val(),
            odid : "[" + odid.toString() + "]",
            '_token': $('meta[name="csrf-token"]').attr('content')
        },function(res){

            if(res.status == 1){
                tipshow(res.info);
                close();
                window.location.reload();
            }else{
                tipshow(res.info, 'warn');
            }

        })
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
    })
    
});

//点击备注提交按钮
$('.submit_info').click(function() {
    var that = $(this);
    $.post('/merchants/order/setSellerRemark', $('#seller_remark_form').serialize(), function(data) {
        if (data.status == 1) {
            tipshow(data.info);
            $("#seller_remark").html($('.js-remark').val());
        } else {
            tipshow(data.info, 'warn');
        }
    });
    hideModel($('#baseModal'));
});

/**
 * 手动标记退款完成提交
 * @author 许立 2018年7月2日
 */
$('.submit_refund').click(function() {
    var that = $(this);
    $.post('/merchants/order/manuallyRefundSuccess', $('#manually_refund_form').serialize(), function(data) {
        if (data.status == 1) {
            tipshow(data.info);
            window.location.reload();
        } else {
            tipshow(data.info, 'warn');
        }
    });
    hideModel($('#baseModalRefund'));
});

//关闭备注框
$('.close').click(function() {
    $('.modal').hide();
    $('.modal-backdrop').hide();
});

//复制
$('body').on('click', '#copy_user_info', function(e) {
    e.stopPropagation(); //阻止事件冒泡
    var obj = $(this).siblings('#hid_copy_user_info');
    copyToClipboard(obj);
    tipshow('复制成功', 'info');
});

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
                tipshow(data.info);
            }
        },
        error : function() {
            // view("异常！");
            tipshow("异常！");
        }
    });
    var logistics = json.logistics;
    var express = json.express;
    var str ='<div class="layer-wrap" id="div_up_logistics"><div class="t-tips"><i class="glyphicon glyphicon-exclamation-sign" style="color: #FF8676;font-weight: 300;"></i>物流信息仅支持一次更正，请仔细填写并核对</div>';
    for(var i=0;i<logistics.length;i++){
        str +='<div class="mb30 logistic_box" data-id="'+logistics[i].id+'""><p class="mt15"><strong>包裹'+(i+1)+'</strong>共'+logistics[i].num+'件商品</p><p class="mt15"><span>发货方式：</span>';
        str +='<label class="radio-inline"><input type="radio" style="top: -6px;" name="" checked="checked" value="0"> 需要物流</label></p>';
        str +='<p class="mt15"><span>物流公司：</span>';
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

function changeDivStyle() {
    if (o_status == 0) {
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
    } else if (o_status == 1) {
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
    } else if (o_status == 3) {
        $('.stepIco').each(function(key, val) {
            if (key < 4) {
                $(this).css('background', '#428bca');
                $(this).find('.stepText:last').css('color', '#428bca');
            }
        })
        $('.order_progress li').each(function(key, val) {
            if (key < 6) {
                $(this).css('background', '#428bca');
            }
        })
    }else if (o_status == 2) {
        $('.stepIco').each(function(key, val) {
            if (key < 3) {
                $(this).css('background', '#428bca');
                $(this).find('.stepText:last').css('color', '#428bca');
            }
        })
        $('.order_progress li').each(function(key, val) {
            if (key < 5) {
                $(this).css('background', '#428bca');
            }
        })
    }else if (o_status == 4) {
        $('#deliveryText').text('交易关闭');
        $('.stepIco').each(function(key, val) {
            if (key < 4) {
                $(this).css('background', '#428bca');
                $(this).find('.stepText:last').css('color', '#428bca');
            }
        })
        $('.order_progress li').each(function(key, val) {
            if (key < 6) {
                $(this).css('background', '#428bca');
            }
        })
    }
}
out_statu();
function out_statu(){ 
    switch (out_status)
    {
        case '1':
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
            break;
        case '2':
            $('.stepIco').each(function(key, val) {
                if (key < 3) {
                    $(this).css('background', '#428bca');
                    $(this).find('.stepText:last').css('color', '#428bca');
                }
                if(key == 2){
                    $(this).find('.stepText:last').text('退款关闭');
                }
            });
            $('.order_progress li').css('background', '#bbb')
            $('.order_progress li').each(function(key, val) {
                if (key < 4) {
                    $(this).css('background', '#428bca');
                }
            });
            break;
        case '3':
            $('.stepIco').each(function(key, val) {
                if (key < 2) {
                    $(this).css('background', '#428bca');
                    $(this).find('.stepText:last').css('color', '#428bca');
                }

            });
            $('.order_progress li').css('background', '#bbb')
            $('.order_progress li').each(function(key, val) {
                if (key < 3) {
                    $(this).css('background', '#428bca');
                }
            });
            break;
        case '4':
            $('.stepIco').each(function(key, val) {
                if (key < 3) {
                    $(this).css('background', '#428bca');
                    $(this).find('.stepText:last').css('color', '#428bca');
                }
            });
            $('.order_progress li').css('background', '#bbb')
            $('.order_progress li').each(function(key, val) {
                if (key < 4) {
                    $(this).css('background', '#428bca');
                }
            });
            break; 
    }
};

if(typeof group_status !="undefined")
    groupStatus(group_status);
/*
* 拼团状态栏函数
*/
function groupStatus(status_num){
    var a=0,b=0;
    switch (status_num)
    {
        case '1':
            a=1,b=1; 
            break;
        case '2':
            a=2,b=3; 
            break;
        case '3':
            a=3,b=5; 
            break;
        case '4':
            a=4,b=7; 
            break; 
        case '5':
            a=5,b=8; 
            break; 
    } 
    $('.t-stepIco').each(function(key, val) {
        if (key < a) {
            $(this).css('background', '#428bca');
            $(this).find('.t-stepText:last').css('color', '#428bca');
        }
    });
    $('.t-order_progress li').css('background', '#bbb')
    $('.t-order_progress li').each(function(key, val) {
        if (key < b) {
            $(this).css('background', '#428bca');
        }
    });
}


//监听物流弹窗中的导航栏被点击事件
$("body").on('click','#div_view_ogistics .common_nav li',function(e){
    e.stopPropagation();//阻止事件冒泡
    var index =$(this).index();
    $(this).parents(".layer-wrap").find('.layer-wrap-logistics .logistics').eq(index).show().siblings().hide();
    $(this).addClass("hover").siblings().removeClass("hover");
});
 

