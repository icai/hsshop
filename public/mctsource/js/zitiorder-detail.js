$(function() {
	//  分销详情
	var clickState = 0;
    $(".content-region .order-dp").click(function(){ 	
    	console.log(11)
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
					console.log(res);
					
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

function changeDivStyle() {
    if (o_status == 0) {
        $('.stepIco').each(function(key, val) {
            console.log(111)
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
                console.log(1)
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
                console.log(1)
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


changeStatus(o_status);
function changeStatus(status_num){
    var a=0,b=0;
    switch (status_num)
    {
        case '0':
            a=1,b=1; 
            break;
        case '1':
            a=3,b=5; 
            break;
        case '2':
            a=4,b=7; 
            break;
        case '3':
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

 

