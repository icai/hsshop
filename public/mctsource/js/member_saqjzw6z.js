$(function(){
    laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
    var start = {
        elem: '#startDate',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: '2009-06-16 23:59:59', //设定最小日期为当前日期
        max: laydate.now(0, "YYYY-MM-DD hh:00:00"), //最大日期
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
        max: laydate.now(0, "YYYY-MM-DD hh:00:00"),
        event: 'focus',
        istime: true,
        istoday: false,
        choose: function(datas){
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);
})
require(["jquery","layer","extendPagination"],function(jquery,layer,extendPagination){
	//筛选部分的输入框样式
	var ele = $(".filter_conditions select, .filter_conditions input")
	focusStyle(ele);


	$(".go_order_detail").on('click', function () {
        var id = $(this).attr('data-id')
        window.location.href = '/merchants/order/orderList?field=mid&search='+id+'&order_type=0&order_source=&refund_status=0&start_time=&end_time=&status=0&pay_way=0&logistics_status=&express_type=0'
    })

	//筛选按钮事件；
	$(".screening").click(function(){
		//alert("后台数据库进行筛选");
		//后台数据库进行筛选；
	});
	//清空筛选条件；表单重置
	$(".clear_screen").click(function(){
        window.location.href='/merchants/member/customer';
		// $(".filter_conditions")[0].reset();
    })

    // 点击更多
    $("body").on("click",".get_more",function(e){
        $(".t-pop").remove();
        e.stopPropagation();//阻止事件冒泡 
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =-10+"px";
        div.style.left=-120+"px";
        var html ='<div class="get-more">'+
        '<p><a href="javascript:void(0);" class="send_msg">加余额</a></p>'
         +'<p><a href="javascript:void(0);" class="give_integral">给积分</a></p>'
        +'<p><a href="javascript:void(0);" class="add_card">发放会员卡</a></p>'
        +'<p><a href="javascript:void(0);" class="annotate">备注</a></p>'
        +'<p><a href="javascript:void(0);" class="pullBlack">拉黑</a></p></div>';
        div.innerHTML=html;
        $(this).append(div)
    });

    /**
     * @auther 邓钊
     * @desc 余额分页  start
     * @date 2018-7-30
     * */
    $("#close_balance_box").on('click',function () {
        $("#balance_detail").addClass('none');
    })
    //余额点击事件 2017/10/31 新增 txw
    $(".balance-detail").click(function(){
        var mid = $(this).parent().parent().attr("data-mid");
        $.ajax({
            url:"/merchants/member/getMemberBalaceLog",
            type:"get",
            data:{mid:mid,page:1},
            dataType:"json",
            async:false,
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if (res.status == 1){
                    var totalCount = res.data[1].total;
                    var limit = res.data[1].pageSize
                    var list = res.data[0];
                    getBalanceInfo(list)
                    $('.balance_pageNum').extendPagination({
                        totalCount: totalCount,
                        showCount: 10,
                        limit: limit,
                        callback: function (page, limit, totalCount) {
                            $.ajax({
                                url: "/merchants/member/getMemberBalaceLog?mid=" + mid + "&&page=" + page,
                                data: {},
                                type: "get",
                                async: false,
                                cache: false,
                                dataType: "json",
                                headers: {
                                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                                },
                                success: function (data) {
                                    //保存成功后 移除新增栏目 插入新的ul
                                    if (data.status == 1) {
                                        var list = data.data[0];
                                        getBalanceInfo(list)
                                    } else {
                                        tipshow(data.errMsg);
                                    }
                                },
                                error: function () {
                                    tipshow("异常", "wram");
                                }
                            })
                        }
                    });
                }else{
                    tipshow(res.errMsg);
                }
            }
        });
    });
    //根据mid获取余额信息 2017/10/31 新增 txw
    function getBalanceInfo(list){
        var result = ''
        for(var i=0;i<list.length;i++){
            result+='<tr>' +
                '<td>'+list[i].created_at+'</td>' +
                '<td>'+list[i].pay_way_name+'</td>' +
                '<td>'+list[i].type_name+list[i].money+'</td>' +
                // '<td>'+list[i].money_total/100+'</td>' +
                '<td>'+list[i].pay_desc+'</td>' +
                '</tr>';
        }
        $(".balance_table").find(".balance_tbody").html(result)
        $("#balance_detail").removeClass('none');
    }
    /*end*/
	
	//    ele: 需要改变样式或清除样式的元素；
	//color_1: 改变后的颜色；
	//color_2: 改变前的颜色；
	function focusStyle(ele, color_1, color_2){
		var color_1 = arguments[1]?arguments[1]:"dodgerblue";
		var color_2 = arguments[2]?arguments[2]:"#CCCCCC";
		$(ele).focus(function(){
			$(ele).css({
				"border-color":color_2, 
				"box-shadow": "none"
			});
			$(this).css({
				"border-color":color_1,
				"box-shadow": "0 0 5px "+color_1
			});
		});
		//点击页面其他部分清除样式；
		$(ele).blur(function(){
			$(this).css({
				"border-color":color_2, 
				"box-shadow": "none"
			});	
		})
	};
	
	//模态框居中设置
	$("#add_customer").click(function(e){
		var _target = $(this).attr('data-target');
		t=setTimeout(function () {
			var _modal = $(_target).find(".modal-dialog");
			_modal.css({'margin-top': parseInt(($(window).height() - _modal.height())/3.5)});
		},0);
	});
	

	$('.addtxt').focus(function(){
		$('.sure').removeAttr('disabled');
	})
    //确定按钮的事件；
    $('.sure').click(function(){
        $(this).attr('disabled','disabled');

    });
    //取消按钮的事件；
    $('.cancle').click(function() {
        $('#defaultForm').data('bootstrapValidator').resetForm(true);
    });
    
    //点击发短信
    $("body").on("click",".send_msg",function(e){
        // $(".t-pop").remove();
        e.stopPropagation();//阻止事件冒泡 
        var mid = $(this).parents(".data_content").attr("data-mid");
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =$(this).offset().top-10+"px";
        div.style.left=$(this).offset().left-284+"px";
        var html ='<div class="t-pop-header">添加余额</div>'+
        '<div class="t-pop-content"><input type="text"class="form-control input-sm" id="input_integral1" value="" placeholder="负数为扣除余额"/></div>'+
        '<div class="t-pop-content"><input type="text"class="form-control input-sm" id="msg" value="" placeholder="操作原因"/></div>'+
        '<div class="t-pop-footer">'+
        '<button class="btn btn-primary t-pop-footer-yes1 btn-xs" data-mid="'+mid+'" data-type="1">确定<tton>'+
        '<button class="btn btn-default btn-xs t-pop-footer-clear1">取消<tton></div>';
        div.innerHTML=html;
        $("body").append(div);
    });
  
    //给积分
    $("body").on("click",".give_integral",function(e){
        // $(".t-pop").remove();
        e.stopPropagation();//阻止事件冒泡 
        var mid = $(this).parents(".data_content").attr("data-mid");
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =$(this).offset().top-10+"px";
        div.style.left=$(this).offset().left-284+"px";
        var html ='<div class="t-pop-header">给积分</div><div class="t-pop-content"><input type="text"class="form-control input-sm" id="input_integral" value="" placeholder="负数为扣除积分"/></div> <div class="t-pop-content"><input type="text"class="form-control input-sm" id="msg" value="" placeholder="操作原因"/></div><div class="t-pop-footer"><button class="btn btn-primary t-pop-footer-yes btn-xs" data-mid="'+mid+'" data-type="2">确定<tton><button class="btn btn-default btn-xs t-pop-footer-clear">取消<tton></div>';
        div.innerHTML=html;
        $("body").append(div);
    });

    //点击确定事件
    $("body").on("click",".t-pop-footer-yes1",function(){ 
        var mid = $(this).attr("data-mid");
        var money = $("#input_integral1").val();
        var msg = $("#msg").val();
        // var type = $(this).data('type');//1加余额，2给积分
        if(money>100000){
            tipshow('一次最多添加10万余额','warn');
            return;
        }
        function AddEventInput(i){
                if(isNum(money)){
                    $.ajax({
                        url:"/merchants/member/addBalanceBySystem?mid="+mid+"&money="+money+"&msg="+msg,
                        data:{},
                        type:"get",
                        dataType:"json",
                        headers: {
                            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                        },
                        success:function(json){
                            //保存成功后 移除新增栏目 插入新的ul 
                            if(json.errCode==0){
                                tipshow("操作成功，刷新查看!"); 
                            }else{
                               tipshow(json.errMsg,"warn"); 
                            }
                        },
                        error:function(){
                            tipshow("异常","warn");
                        }
                    }); 
                    $(".t-pop").remove();
                }else{
                    tipshow("请输入正确的金额","wran");
                }
//          };
        }
        AddEventInput(money);
    });

    //点击确定事件
    $("body").on("click",".t-pop-footer-yes",function(){ 
        var mid = $(this).attr("data-mid");
        var score = $("#input_integral").val();
        var msg = $("#msg").val();
        if(score > 100000){
            tipshow('一次最多添加10万积分','warn');
            return;
        }
        function AddEventInput(i){
				if(isNum(score)){
		            $.ajax({
		                url:"/merchants/member/point/addPointBySystem?mid="+mid+"&score="+score+"&type=5"+"&msg="+msg,
		                data:{},
		                type:"get",
		                dataType:"json",
		                headers: {
		                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
		                },
		                success:function(json){
		                    //保存成功后 移除新增栏目 插入新的ul 
		                    if(json.errCode==0){
		                        tipshow("操作成功，刷新查看!"); 
		                    }else{
		                       tipshow(json.errMsg,"wram"); 
		                    }
		                },
		                error:function(){
		                    tipshow("异常","wram");
		                }
		            }); 
		            $(".t-pop").remove();
		        }else{
		            tipshow("请输入正确的积分数","wran");
		        }
//	        };
		}
		AddEventInput(score);
    });

    $("#btn_add_client").click(function(){
        var _this = this;
        $(_this).attr("disabled","disabled");
        url = $('#defaultForm').attr('action');
        $.ajax({
            url:url,// 跳转到 action
            data:$('#defaultForm').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            dataType:'json',
            success:function (data) { 
                if ( data.status == 1 ) {
                    layer.msg( data.info,{icon:6});
                    /* 后台验证通过 */
                    if ( data.url ) {
                        /* 后台返回跳转地址则跳转页面 */
                        window.location.href = data.url;
                    } else {
                        /* 后台没有返回跳转地址 */
                        window.location.reload();
                    }
                } else {
                    flag = 0;
                    layer.msg( data.info);
                }
            },
            error : function() {
                tipshow("异常",'warn');
            },
            complete : function(){
               $(_this).removeAttr('disabled');
            }
        });
    });   

    //是否为数字
    function isNum(value) { 
        var patrn = /^(-)?\d+(\.\d+)?$/;
        if (patrn.exec(value) == null || value == "") {
            return false
        } else {
            return true
        }
    }
    
    //给积分弹窗点击弹窗本身阻止事件冒泡 
    $("body").on("click",".t-pop",function(e){
        e.stopPropagation();
    });
    //给积分弹窗点击取消按钮移除弹窗
    $("body").on("click",".t-pop-footer-clear",function(e){
        $(".t-pop").remove();
    });
    $("body").on("click",".t-pop-footer-clear1",function(e){
        $(".t-pop").remove();
    });
    //给积分弹窗点击body 移除弹窗
    $("body").click(function(e){
        $(".t-pop").remove();
    });


    /**
     * @auther 邓钊
     * @desc 积分分页  start
     * @date 2018-7-30
     * */
    $("#close_jifen_box").on('click',function () {
        $("#integral_detail").addClass('none');
    })
    //点击积分明细
    $(".integral-detail").click(function(e){
        e.stopPropagation();
        var totalCount = null;
        var limit = null
        var mid = null
        mid = $(this).parents(".data_content").attr("data-mid");
        $.ajax({
            url:"/merchants/member/point/selectPointRecord?mid="+ mid +"&&page=1",
            data:{},
            type:"get",
            async: false,
            cache:false,
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(data){
                //保存成功后 移除新增栏目 插入新的ul
                if (data.errCode == 0){
                    totalCount = data.total;
                    limit = data.pageSize
                    var json = data.data;
                    selectPointRecord(json)
                    $('.jifen_pageNum').extendPagination({
                        totalCount: totalCount,
                        showCount: 10,
                        limit: limit,
                        callback: function (page, limit, totalCount) {
                            $.ajax({
                                url: "/merchants/member/point/selectPointRecord?mid=" + mid + "&&page=" + page,
                                data: {},
                                type: "get",
                                async: false,
                                cache: false,
                                dataType: "json",
                                headers: {
                                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                                },
                                success: function (data) {
                                    //保存成功后 移除新增栏目 插入新的ul
                                    if (data.errCode == 0) {
                                        var json = data.data;
                                        selectPointRecord(json)
                                    } else {
                                        tipshow(data.errMsg);
                                    }
                                },
                                error: function () {
                                    tipshow("异常", "wram");
                                }
                            })
                        }
                    });
                }else{
                    tipshow(data.errMsg);
                }
            },
            error:function(){
                tipshow("异常","wram");
            }
        });
    });

    function selectPointRecord(json){
        var result = ''
        for (var i = 0; i < json.length; i++) {
            result +='<tr>';
            result +='<td>'+json[i].created_at+'</td>';
            result +='<td>'+json[i].type_name+'</td>';
            result +='<td style="color:dodgerblue;">'+json[i].score+'</td>';
            result +='<td>'+json[i].remark+'</td>';
            // result +='<td>'+json[i].totalScore+'</td>';
            result +='</tr>';
        }
        $(".jifen_table").find(".jifen_tbody").html(result)
        $("#integral_detail").removeClass('none');
    }
    /*end*/

    //添加备注
    $("body").on("click",".annotate",function(e){
        // $(".t-pop").remove();
        e.stopPropagation();//阻止事件冒泡
        var mid = $(this).parents(".data_content").attr("data-mid");
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =$(this).offset().top-10+"px";
        div.style.left=$(this).offset().left-284+"px";
        var html="";
        $.ajax({
            url:"/merchants/member/updateRemark",
            data:{
                id:mid
            },
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                if(json.status == 1){
                    html ='<div class="t-pop-header">添加备注</div><div class="t-pop-content"><textarea type="text" rows="6" class="form-control input-sm beizhu_textarea" id="input_remark" placeholder="">'+json.data.remark+'</textarea></div> <div class="t-pop-footer"><button class="btn btn-primary  btn-xs btn_beizhu" data-mid="'+mid+'" data-type="2">确定<tton><button class="btn btn-default btn-xs t-pop-footer-clear">取消<tton></div>';
                    div.innerHTML=html;
                    $("body").append(div);
                }
            },
            error:function(){
                tipshow("异常","wran");
            }
        });

    });


    //点击确定事件//备注
    $("body").on("click",".btn_beizhu",function(){
        var mid = $(this).attr("data-mid");
        var remark = $("#input_remark").val();
        function AddEventInput(i){
            $.ajax({
                url:"/merchants/member/updateRemark",
                data:{
                    id:mid,
                    remark:remark
                },
                type:"POST",
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success:function(json){
                    if(json.data.errCode==0){
                        tipshow("操作成功，刷新查看!");
                    }else{
                        tipshow(json.data.errMsg,"warn");
                    }
                },
                error:function(){
                    tipshow("异常","warn");
                }
            });
            $(".t-pop").remove();
        }
        AddEventInput(remark);
    });


    // 拉黑
    $('body').on('click','.pullBlack',function(e){            
        e.stopPropagation();
        var _this = this;
        var type = 1;
        var mid = $(this).parents('.data_content').attr("data-mid");
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/member/setMemberType',
                data:{
                    type:type,
                    id:mid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.errCode===0){
                        tipshow('加入黑名单成功,2秒后跳转。','info');
                        setTimeout(function(){//两秒后跳转  
                            window.location.href='/merchants/member/blackList';
                        },2000);  
                    }else{
                        tipshow('加入黑名单失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            }); 
        },'确认加入黑名单吗？')
    }); 

    /** 
     * 发放会员卡 author->hk updateTime->2018/05/31
    */
    //发卡
    $('body').on('click','.add_card',function(e){
        new AddCard({mid:$(this).parents('.data_content').attr('data-mid')});
    })  

    $('body').on('click','.batch-add',function(e){
        var adds = [];
        $('.main_content input[type="checkbox"]:checked').each(function(){
            adds.push($(this).parents('.data_content').attr('data-mid'))
        })
        if(adds.length == 0){
            return false
        }else if(adds.length==1){
            new AddCard({mid:adds[0]});
        }else{
            new AddCard({mid:adds});
        }
        adds=[]
    })

    function AddCard(adds){
        if(typeof adds.mid == 'string'){
            this.adds = adds;
        }else if(typeof adds.mid == 'object' && adds.mid.length > 1){
            this.oAdds = adds;
            this.adds = '';
        }else{
            this.adds = adds;
        }
        this.select = null;
        this.createParams = null;
        this.openCard()
    }
    AddCard.prototype.openCard = function(){
        var that = this;
        that.getCardInfo();
    }
    //获取会员卡信息
    AddCard.prototype.getCardInfo = function(){
        var that = this;
        var options = null;
        hstool.load();
        $.ajax({
            url:'/merchants/member/getUnclaimedMemberCardList',
            data:that.adds,
            type:'get',
            dataType:'json',
            success:function(res){
                // 卡列表信息
                options = res.data;
                that.select = '<select class="add-frame">';
                if(res.info == "操作成功"){
                    for(var i=0,l=options.length;i<l;i++){
                        that.select+='<option value="'+options[i].id+'">'+options[i].title+'</option>'
                    }
                    that.select+='</select>';
                    hstool.open({
                        title:"发放会员卡",
                        area:["400px","200px"],
                        content:'<div class="addCardFrame"><div class="cardContent">'+that.select+'<a href="'+_url+'merchants/member/membercard">管理会员卡</a></div>'
                        +'<p class="FrameBottom"><button class="addComfirm frameBtn btn btn-primary">确定<tton><button class="btn frameBtn addcancel">取消<tton></p></div>',
                    });
                }else{
                    tipshow("异常","warn");
                }
                
                $('body').on('click','.addComfirm',function(){
                    if(that.adds){
                        that.adds.card_id=$('.add-frame>option:selected').val();
                        that.dataTrans(that.adds)
                    }else{
                        that.oAdds.card_id=$('.add-frame>option:selected').val();
                        that.dataTrans(that.oAdds)
                    }
                })
            },
            error:function(){
                tipshow("异常","warn");
            }
        })
        hstool.closeLoad();

    }
    // 生成会员卡
    AddCard.prototype.dataTrans = function(params){
        var that = this
        $.ajax({
            url:'/merchants/member/grantCardToMember',
            data:params,
            type:'post',
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.info == "操作成功"){
                    hstool.close();
                    tipshow("操作成功!");
                    location.reload();
                }else{
                    tipshow(res.info,"warn");
                }
            },
            error:function(err){
                tipshow("异常","warn")
            }
        })
    }

    //弹框取消按钮事件
    $('body').on('click','.addcancel',function(){
        hstool.close()
    })

    // 全选事件
    $("#allcheck").click(function(){
		if ($(this).prop("checked")) {
			$(".main_content input[type='checkbox']").prop("checked", true)
		}else{
			$(".main_content input[type='checkbox']").prop("checked", false)
		}
    });
    // add by 赵彬 2018-8-27
    if(window.location.search.split("&")[0].split("=")[0] == '?visit_time'){
        if(window.location.search.split("&")[0].split("=")[1] == 1){
            $(".orderby-arrow").addClass('desc')
        }else if(window.location.search.split("&")[0].split("=")[1] == 0){
            $(".orderby-arrow").addClass('asc')
        }
    }
    
});

//点击排序
function sort_desc(){
    var sort;
    if(window.location.search){
        sort = window.location.search.split("&")[0].split("=")[1] == 1 ? 0 : 1;
    }else{
        sort = 1
    }
    window.location.href = 'http://'+ location.host + location.pathname + '?visit_time='+ sort;
}
//end