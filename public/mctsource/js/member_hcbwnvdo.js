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
            end.max = datas; //开始日选好后，重置结束日的最小日期
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
require(["jquery","bootstrap","layer","extendPagination"],function(jquery,bootstrap,layer,extendPagination){
    //筛选部分的输入框样式
	var ele = $(".filter_conditions select, .filter_conditions input")
	focusStyle(ele);
	
	//筛选按钮事件；
	$(".screening").click(function(){
		//alert("后台数据进行筛选");
		//后台数据库进行筛选；
	});
	//清空筛选条件；表单重置
	$(".clear_screen").click(function(){
		window.location.href='/merchants/member/members';
    })
    // 更多点击事件 2018/05/30 hk
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
         +'<p><a href="javascript:void(0);" class="delete_card">删除会员卡</a></p>'
         +'<p><a href="javascript:void(0);" class="annotate">备注</a></p></div>';
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
	
	//如果没有数据；
	if($(".main_content .data_content").length == 0){
		var _html = '<div class="no_date">没有更多数据了</div>'
		$(".main_content").append(_html);
		//页码隐藏
		$("#show, .pagination").hide();
	}
	
	

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
    $("body").on("click",".t-pop-footer-yes",function(){ 
        var mid = $(this).attr("data-mid");
        var score = $("#input_integral").val();
        var msg = $("#msg").val();
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
    $("body").on("click",".t-pop-footer-clear1",function(e){
        $(".t-pop").remove();
    });

    //点击确定事件
    $("body").on("click",".t-pop-footer-yes1",function(){ 
        var mid = $(this).attr("data-mid");
        var money = $("#input_integral1").val();
        var msg = $("#msg").val();
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
                               tipshow(json.errMsg,"wram"); 
                            }
                        },
                        error:function(){
                            tipshow("异常","wram");
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
        '<button class="btn btn-primary t-pop-footer-yes1 btn-xs" data-mid="'+mid+'">确定<tton>'+
        '<button class="btn btn-default btn-xs t-pop-footer-clear1">取消<tton></div>';
        div.innerHTML=html;
        $("body").append(div);
    });
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
    /**
     * 2018-05-31 author:hk
     * 会员管理的删除会员卡
    */

    //删除会员卡
    $("body").on("click",".delete_card",function(e){
        // e.stopPropagation();//阻止事件冒泡
        $("#del_model").show();
        var mid = $(this).parents(".data_content").attr("data-mid");
        // add by zhaobin 2018-9-11
        $.ajax({
            url:'/merchants/member/getOneMemberMemberCardList',
            type:'GET',
            data:{
                mid:mid,
                page:1
            },
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                console.log(res)
                var data = res.data.data
                var memberType = ['无门槛会员卡','按规则发放的会员卡','购买的会员卡']
                var html = '';
                for(var i = 0; i<data.length; i++){
                    html +=            '<tr>'+
                                            '<td>'+data[i].memberCard.title+'</td>'+
                                            '<td>'+data[i].memberCard.created_at+'</td>'+
                                            '<td>'+memberType[data[i].memberCard.card_status]+'</td>'+
                                            '<td>'+data[i].memberCard.member_explain+'</td>'+
                                            '<td><input type="checkbox" data-mid="'+data[i].mid+'" data-card="'+data[i].card_id+'"></td>'+
                                        '</tr>'
                }
                $("#del_model-dialog tbody").html(html)
                var totalCount = res.data.total, showCount = 10,
                limit = res.data.per_page;
                // alert(totalCount)
                $('.del_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/member/getOneMemberMemberCardList?mid='+ mid +'&page='+page,function(response){
                            // if(response.status ==1){
                                console.log(response);
                                var data = response.data.data;
                                var html = '';
                                for(var i = 0; i<data.length; i++){
                                    html +=              '<tr>'+
                                                            '<td>'+data[i].memberCard.title+'</td>'+
                                                            '<td>'+data[i].memberCard.created_at+'</td>'+
                                                            '<td>'+memberType[data[i].memberCard.card_status]+'</td>'+
                                                            '<td>'+data[i].memberCard.member_explain+'</td>'+
                                                            '<td><input type="checkbox" data-mid="'+data[i].mid+'" data-card="'+data[i].card_id+'"></td>'+
                                                        '</tr>'
                                }
                                $("#del_model-dialog tbody").html(html)
                            // }
                        })
                    }
                });
            }
        }) 
        
    });
    // 删除会员卡
    $("body").on("click",".btn-del",function(){
        var deteleMember = [];
        $('#del_model input[type="checkbox"]:checked').each(function(){
            deteleMember.push({
                mid:$(this).attr("data-mid"),
                card_id:$(this).attr("data-card")
            })
        })
        if(deteleMember.length<=0){
            tipshow('请选择需删除的会员卡');
            return false;
        }
        //2. ajax数据交互
        if(deteleMember != false){
            new DeleteTrans({memberData:deteleMember})
        }
        //3. 清空数据还原事件
        deteleMember = [];
        $('#del_model input[type="checkbox"]:checked').attr({'checked':false});
        $("#del_model").hide()
    });
    $("body").on("click","#del_model .close",function(){
        $('#del_model input[type="checkbox"]:checked').attr({'checked':false});
        $("#del_model").hide()
    })
    // end
    //点击确定事件--删除会员卡
    $("body").on("click",".delete-comfirm",function(){
        var mArr = {memberData:[{mid:$(this).attr('data-mid'),card_id:$(this).attr('data-cardId')}]};
        console.log(mArr)
        //new DeleteTrans(mArr)
    });

    //批量删除按钮
    $("body").on("click",".batch-delete",function(e){
        var judgeDo = [];
        $('.main_content input[type="checkbox"]:checked').each(function(){
            judgeDo.push($(this))
        })
        if(judgeDo.length<=0){
            return false
        }
        hstool.open({
            title:"提示",
            area:["300px","100px"],
            content:'<div style="height:300px;overflow:auto;">确定要删除吗？<div class="del-content"><button class="btn btn-primary btn-batch-delete">确定</button><button class="btn btn-cancel-delete">取消</button></div></div>',
        });
    });

    // 批量删除取消事件
    $("body").on("click",".btn-cancel-delete",function(){
        hstool.close();
    })

    //批量删除确定事件
    $("body").on("click",".btn-batch-delete",function(){
        //1. 获取所有被选中的checkbox的 mid&card_id
        var dels = [];
        $('.main_content input[type="checkbox"]:checked').each(function(){
            dels.push({
                mid:$(this).parents('.data_content').attr('data-mid'),
                card_id:$(this).parents('.data_content').attr('data-cardId')
            })
        })

        //2. ajax数据交互
        if(dels != false){
            new DeleteTrans({memberData:dels})
        }

        //3. 清空数据还原事件
        dels = [];
        $('.main_content input[type="checkbox"]:checked').attr({'checked':false})
        hstool.close();
    })

    //删除数据传输事件
    function DeleteTrans(DelArr,delURL){
        this.delURL = delURL?delURL:'/merchants/member/deleteMemberCard';
        this.DelArr = DelArr;
        if(this.DelArr != false){
            this.trans()
        }
        return;
    }
    DeleteTrans.prototype.trans = function(){
        var that = this;
        $.ajax({
            url:that.delURL,
            data:that.DelArr,
            type:'post',
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.info == "操作成功"){
                    if(that.DelArr.memberData.length>1){
                        
                    }else{
                        $(".t-pop").remove();
                        
                    }     
                    tipshow("成功删除!");
                    location.reload();
                }else{
                    tipshow("异常","warn");
                }
            },
            error:function(){
                tipshow("异常","warn");
            },
            complete:function(){
                $(".t-pop").remove();
            }
        })
    }
    $("#allcheck").click(function(){
        if ($(this).prop("checked")) {
            $(".main_content input[type='checkbox']").prop("checked", true)
        }else{
            $(".main_content input[type='checkbox']").prop("checked", false)
        }
    });
    // add by 赵彬 2018-8-27
    if(window.location.search){
        if(window.location.search.split("&")[0].split("=")[1] == 1){
            $(".orderby-arrow").addClass('desc')
        }else{
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