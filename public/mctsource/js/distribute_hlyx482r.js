$(function(){
    getDistributeGrade()
    function getDistributeGrade(){
        var tr = '<tr><td>1</td><td><input disabled class="grade-name" type="text" placeholder="等级名称" value="'+title+'"></td><td>默认成为分销员即是该等级</td>'+
        		'<td>'+
        		'<input type="button" href="javascript:void(0);" class="btn btn-primary grade_edit_first" value="编辑">&nbsp;'+
                '<input style="display:none;" type="button" href="javascript:void(0);" class="btn btn-primary grade_cancel_first" value="取消">'+
        		'</td>'+
        		'</tr>'
        for(var i=0;i<grade.length;i++){
            tr += '<tr>'+
                        '<td>'+(i+2)+'</td>'+
                        '<td><input disabled class="grade-name" type="text" placeholder="等级名称" value="'+grade[i].title+'"></td>'+
                        '<td>'+
        //                     '<div class="rule-item extension_amount">';
        // if(grade[i].extension_amount>0){
        //     tr +=               '<input disabled type="checkbox" checked>';
        //         }else{
        //     tr +=               '<input disabled type="checkbox">';
        //         }
        //     tr +=                    `<p>累计推广金达<input disabled type="text" class="t-number" value="`+grade[i].extension_amount+`">元</p>`+
        //                     '</div>'+
        //                     '<div class="rule-item total_amount">';
        // if(grade[i].total_amount>0){
        //     tr +=               '<input disabled type="checkbox" checked>';
        // }else{
        //     tr +=               '<input disabled type="checkbox">';
        // }
        //     tr +=               `<p>累计推广金与消费金总和达<input disabled class="t-number" type="text" value="`+grade[i].total_amount+`">元</p>`+
        //                     '</div>'+
                            '<div class="rule-item product_choice">'+
                                '<input name="pid" disabled type="checkbox" checked>'+
                                '<p>购买指定商品升级：</p>'+
                                '<a href="javascript:void(0);" class="add-product" style="display:none;">+添加商品</a>'+
                                '<div class="change-content">'+
                                    '<div class="flex">'+
                                        '<div class="specify-product">'+
                                            '<p class="product-item" data-pid="'+grade[i].pids+'">'+grade[i].product_title+'</p>'+
                                            '<i class="close-item" style="display:none;">×</i>'+
                                        '</div>'+
                                        '<a href="javascript:void(0);" style="display:none;" data-pid="'+grade[i].pids+'" class="change-product">修改</a>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</td>'+
                        '<td data-id="'+grade[i].id+'">'+
                            '<input type="button" href="javascript:void(0);" class="btn btn-primary grade_edit" value="编辑">&nbsp;'+
                            '<input type="button" href="javascript:void(0);" class="btn btn-primary grade_del" value="删除">'+
                        '</td>'+
                    '</tr>'
        }
        $(".grade-table tbody").html(tr)
        if(grade.length == 2){
            $(".add-grade").hide()
        }
    }
    if ($(".switch_item").find('label').data('is-open') == '1'){
        $('.fenxiao_check').show();
    }

    /**
     * 总开关按钮点击事件
     * 1.切换开关状态
     * 2.所有子开关切换成总开关状态
     * 3.若是关闭状态 则 子模块全部隐藏
     */ 
    $(".switch-total label").click(function() {
        var _this = this;
        var open = $(this).attr("data-is-open");  
        var status = open=="1"?0:1;
        var url = "/merchants/distribute/open/"+status; 
        $.ajax({
            url:url,
            data:{},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                //保存成功后 移除新增栏目 插入新的ul 
                if(json.status==1){
                    tipshow(json.info);
                    if (open == "1") {
                        //切换成关闭状态
                        $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                        $("#distribute_content").addClass('none');
                    } else {
                        //切换成开启状态
                        $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                        $("#distribute_content").removeClass('none');
                    }
                }else{
                   tipshow(json.info,"wram"); 
                }
            },
            error:function(){
                tipshow("异常","wram");
            }
        }); 
    });


    /**
     * 开启关闭企业打款
     */
    // 打开分销弹窗
    function openPop(open,_this){
        layer.open({
            type: 1,
            title: '提示<span id="closeBtn">x</span>', 
            closeBtn: false,
            skin:"layer-tskin", 
            shade: 0.8,
            anim: 2,
            btnAlign: 'c',
            move:false,
            btn:['已开通，现在启用','还未开通，去看看'],
            closeBtn:1,
            yes:function(index, layero){
                $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                postData(open,_this);
                layer.closeAll()
            },
            cancel:function(index, layero){
                window.open('https://www.huisou.cn/home/index/detail/759/help');
                return false;
            },
            content: '<div style="padding-bottom: 30px;">请确保微信支付商户后台已开通企业付款到零钱功能，并且运营账户余额充足，否则无法正常使用该功能，具体操作请查看自动提现教程。</div>'
        });
    }
    $('body').on('click','#closeBtn',function(){
        layer.closeAll()
    })

    $(".company_pay label").click(function() {
        var open = $(this).attr("data-is-open");
        var _this = this;
        if(open == "1"){
            //切换成关闭状态
            postData(open,_this)
        }else{
            openPop(open,_this)
        }
        
    });
    
    // 分销佣金管理
    if ($(".company_pay ").find('label').data('is-open') == '0'){
        $('.commission-show').hide();
    }
    console.log( $(".commission_set label").data('is-open'))
    $(".commission_set label").click(function() {
        var open = $(this).attr("data-is-open");
        postcommission(open);
       
    });
    function postcommission(open){
        if(open == "2" ){
            var status = 1;
        }else{
            var status = 2;
        }
        var url = "/merchants/distribute/openCompanyPay?company_pay=" + status;
        $.ajax({
            url:url,
            data:{},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                if(json.status==1){
                    tipshow(json.info);
                    // 佣金管理 1为关闭，2为开启
                    if (open == "2") {
                         $('.commission_set label').removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "1");
                    }else {
                        $('.commission_set label').removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "2");
                    }
                }else{
                    tipshow(json.info,"wram");
                }
            },
            error:function(){
                tipshow("异常","wram");
            }
        })
     };
    function postData(open,_this){
        var status = open=="1"?0:1;
        var url = "/merchants/distribute/openCompanyPay?company_pay="+status;
        $.ajax({
            url:url,
            data:{},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                //保存成功后 移除新增栏目 插入新的ul
                if(json.status==1){
                    tipshow(json.info);
                    // 佣金管理 1为关闭，2为开启
                    if (open == "1") {
                        //切换成关闭状态
                        $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                        $('.commission-show').hide();
                    }else {
                        //切换成开启状态
                        $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                        $('.commission-show').show();
                    }
                }else{
                    tipshow(json.info,"wram");
                }
            },
            error:function(){
                tipshow("异常","wram");
            }
        });
    }

    /**
     * 分销客申请开关
     * add by 黄新琴 2018/10/9
     */
    $(".js-distribute-switch label").click(function() {
        var _this = this;
        var open = $(this).attr("data-is-open");
        var status = open=="1"?0:1;
        var url = "/merchants/distribute/applyDistribut/"+status;
        $.ajax({
            url:url,
            data:{},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                //保存成功后 移除新增栏目 插入新的ul
                if(json.status==1){
                    tipshow(json.info);
                    if (open == "1") {
                        //切换成关闭状态
                        $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                        $('.js-distribute-show').hide();
                    } else {
                        //切换成开启状态
                        $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                        $('.js-distribute-show').show();
                    }
                }else{
                    tipshow(json.info,"wran");
                }
            },
            error:function(){
                tipshow("异常","wran");
            }
        });
    });


    $("#defaultDistribute").click(function(){
        var fn = "setDefaultDistribute"; //回调方法名称
        layer.open({
            type: 2,
            title: false, 
            closeBtn:false, 
            skin:"layer-tskin", //自定义layer皮肤 
            shade: 0.8,
            area: ['655px', '525px'],
            content: '/merchants/distribute/choice?fn='+fn
        });
    }); 

    $("#unifiedDistribute").click(function(){
        var fn ="isSubmitDistribute";//回调方法名称
        layer.open({
            type: 2,
            title: false, 
            closeBtn:false, 
            skin:"layer-tskin", //自定义layer皮肤 
            shade: 0.8,
            area: ['655px', '525px'],
            content: '/merchants/distribute/choice?fn='+fn
        });
    }); 
    
    //是否设定分销模板开关
    $('.switch_item').click(function(event){
        $(this).find('label').addClass('loadding');
        var _this = $(this);
        var open = $(this).find('label').attr("data-is-open");  
        var status = open=="1"?0:1;
        setTimeout(function(){
            _this.find('label').removeClass('loadding');
        },80);        
        event.stopPropagation();    //  阻止事件冒泡 
        if (open == "1") {
            //切换成关闭状态
            $.ajax({
                type:"POST",
                url:"/merchants/distribute/addDistributeGrade",
                data:{
                    distribute_grade:0,
                },
                async:true,
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success:function(res){
                    if(res.status==1){
                        tipshow(res.info);
                        $(_this).find('label').removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                        $('.fenxiao_check').hide();
                    }else{
                        tipshow(res.info,"wram");
                    }
                },
                error:function(){
                    alert("数据访问错误")
                }
            });

        } else {
            //切换成开启状态
            $(_this).find('label').removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
        	$('.fenxiao_check').show();
        }
    });
    
    //分销门槛数据填写
    $('.fenxiao_check .data_check').click(function(){
    	if($(this).val()==0){
    		$(this).next('input').attr('disabled',false).removeClass('zx_backf5');
    		$(this).val('1');
    	}else{
    		$(this).next('input').attr('disabled',true).val('').addClass('zx_backf5');
    		$(this).val('0');
    	}
    })
    
	//分销门槛数据提交
	$('.screening').click(function(){
		if(!$('.pay_num').is(':checked') && !$('.pay_amount').is(':checked') && !$('.score').is(':checked')){
        	tipshow('至少选择一项','warm');
        	return false;
        };
        var demand = {};
        demand.pay_num=$('.pay_num_save').val();
        demand.pay_amount=$('.pay_amount_save').val();
        demand.score=$('.score_save').val();
        console.log(demand)
      	$.ajax({
        	type:"POST",
        	url:"/merchants/distribute/addDistributeGrade",
        	data:{
        		distribute_grade:1,
        		demand:demand
        	},
        	async:true,
        	headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
        	success:function(res){
                if(res.status==1){
                    tipshow(res.info);
                }else{
                   tipshow(res.info,"wram"); 
                }
        	},
        	error:function(){
        		alert("数据访问错误")
        	}
        });
	})
    
    setDefaultDistribute = function(data){  
        var url = "/merchants/distribute/choice";
        $.ajax({
            url:url,
            data:data,
            type:"post",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(json){
                 layer.closeAll(); 
                //保存成功后 移除新增栏目 插入新的ul 
                if(json.status==1){
                    tipshow(json.info); 
                    //修改名称 
                    $("#template_name").html(data.title); 
                }else{
                   tipshow(json.info,"wram"); 
                }
            },
            error:function(){
                layer.closeAll(); 
                tipshow("异常","wram");
            }
        }); 
         
    }

    //是否提交分销
    isSubmitDistribute = function(data){
        var t_index = layer.open({
            type: 1,
            title:"是否确认执行该操作?",
            btn:["确定","取消"],
            yes:function(){
                var url = "/merchants/distribute/setTemplate/"+data.id;
                $.ajax({
                    url:url,
                    data:{},
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
            content:'<p style="color:red;margin:10px 15px;">执行后所有的商品将会使用该分销规则，<br />所有商品原先设定的分销规则将被覆盖，请<br />谨慎选择！</p>'
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        }); 
    }


    $(".switch_item_withdraw label").click(function () {
        var withdraw = $(this).data('is-open');
        if (withdraw>0){
            $(this).removeClass('ui-switcher-on');
            $(this).addClass('ui-switcher-off');
            $(this).data('is-open','0');
            dealWithdraw();
        }else{
            $(this).removeClass('ui-switcher-off')
            $(this).addClass('ui-switcher-on');
            $(this).data('is-open','1');
            $(".fenxiao_check_withdraw").show();
        }

    })
    
    function dealWithdraw() {
        var withdraw_grade = 0
        console.log(withdraw_grade);
        $.ajax({
            type:"POST",
            url:"/merchants/distribute/withdrawGrade",
            data:{
                withdraw_grade:withdraw_grade
            },
            async:true,
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status==1){
                    $(".fenxiao_check_withdraw").hide();
                }else{
                    tipshow(res.info,"wram");
                }
            },
            error:function(){
                alert("数据访问错误")
            }
        });
    }
    
    $(".switch_withdraw_save").click(function () {
        var withdraw_grade = $(".fenxiao_check_withdraw .pay_num_save").val();
        console.log(withdraw_grade);
        $.ajax({
            type:"POST",
            url:"/merchants/distribute/withdrawGrade",
            data:{
                withdraw_grade:withdraw_grade
            },
            async:true,
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status==1){
                    tipshow(res.info);
                }else{
                    tipshow(res.info,"wram");
                }
            },
            error:function(){
                alert("数据访问错误")
            }
        });
    })
    // 分销等级规则说明
    $(".rule-tip").hover(function(){
        $(".rule-detail").show()
    },function(){
        $(".rule-detail").hide()
    })
    // 分销第1等级编辑
    $("body").on('click','.grade_edit_first',function(){
        if($("#is_off_on").val() == 1){
            tipshow('您有未保存的分销等级','warn')
            return;
        }
        $(this).removeClass('grade_edit_first').addClass('grade_save_first').val('保存');
        $(this).parents('tr').find(".grade-name").removeAttr('disabled');
        $(this).parents('tr').find("input[type='checkbox']").removeAttr('disabled');
        $(this).parents('tr').find(".grade_cancel_first").css('display','inline-block');
        $(this).parents('tr').find(".change-content").attr('data-key',1);
        $(this).parents('tr').find('.change-product').show();
        $(this).parents('tr').find('.close-item').show();
        $("#is_off_on").val(1);
        $("#is_on_off").val(1)
    })
    // 分销等级编辑
    $("body").on('click','.grade_edit',function(){
        if($("#is_off_on").val() == 1){
            tipshow('您有未保存的分销等级','warn')
            return;
        }
        $(this).removeClass('grade_edit').addClass('grade_save').val('保存');
        $(this).siblings(".grade_del").removeClass('grade_del').addClass('grade_cancel').val('取消').attr('data-key',1);
        $(this).parents('tr').find("input[type='checkbox']").removeAttr('disabled');
        $(this).parents('tr').find(".grade-name").removeAttr('disabled');
        $(this).parents('tr').find(".change-content").attr('data-key',1);
        $(this).parents('tr').find('.change-product').show();
        $(this).parents('tr').find('.close-item').show();
        $("#is_off_on").val(1);
        $("#is_on_off").val(1)
    })
    // 添加分销等级
    $(".add-btn").click(function(){
        if($("#is_on_off").val() == 1){
            tipshow('请先保存未完成的分销等级','warn')
            return
        }
        $("#is_on_off").val(1)
        $("#is_off_on").val(1);
        var num = $(".grade-table tbody tr").length;
        var html = '';
        html = '<tr>'+
                    '<td>'+(num+1)+'</td>'+
                    '<td><input class="grade-name" type="text" placeholder="等级名称"></td>'+
                    '<td>'+
                        // '<div class="rule-item extension_amount">'+
                        //     '<input type="checkbox">'+
                        //     `<p>累计推广金达<input disabled type="text" class="t-number">元</p>`+
                        // '</div>'+
                        // '<div class="rule-item total_amount">'+
                        //     '<input type="checkbox">'+
                        //     `<p>累计推广金与消费金总和达<input disabled class="t-number" type="text">元</p>`+
                        // '</div>'+
                        '<div class="rule-item product_choice">'+
                            '<input name="pid" type="checkbox" disabled checked>'+
                            '<p>购买指定商品升级：</p>'+
                            '<a href="javascript:void(0);" class="add-product">+添加商品</a>'+
                            '<div class="change-content" style="display:none;">'+
                                '<div class="flex">'+
                                    '<div class="specify-product">'+
                                        '<p class="product-item" data-pid="">购买指定商品升商品</p>'+
                                        '<i class="close-item">×</i>'+
                                    '</div>'+
                                    '<a href="javascript:void(0);" class="change-product">修改</a>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</td>'+
                    '<td data-id="">'+
                        '<input type="button" href="javascript:void(0);" class="btn btn-primary grade_save" value="保存">&nbsp;'+
                        '<input type="button" href="javascript:void(0);" class="btn btn-primary grade_cancel" value="取消">'+
                    '</td>'+
                '</tr>'
        $(".grade-table tbody").append(html)
        if(num+1 == 3){
            $(".add-grade").hide()
        }
    })
    // 分销等级规则添加指定商品
    $('body').on('click','.add-product',function(){
        if(!$(this).siblings("input[type='checkbox']").is(":checked")){
            tipshow('请先勾选对应的选择框','warn')
            return;
        }
        $(this).parents('td').find(".product-item").addClass("product_info");
        $('.js-modal-search-input').attr('data-pids','');
        $(".js-modal-search-input").val('');
        var pids = [];
        var title = '';
        getProduct(pids,title)
    })
    // 分销等级规则修改指定商品
    $('body').on('click','.change-product',function(){
        $(this).parents('.change-content').find(".product-item").addClass("product_info");
        var pids = $(this).attr('data-pid').split(',');
        $('.js-modal-search-input').attr('data-pids',pids);
        $(".js-modal-search-input").val('');
        var title = '';
        getProduct(pids,title)
    })
    // 获取商品列表
    function getProduct(pids,val){
        $("#myModal").show()
        $.ajax({
            type:"get",
            url:'/merchants/linkTo/get?type=1&wid='+ wid +'&page=1&title=' + val,
            data:{},
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(data){
                console.log(data.data[0].data)
                var length = data.data[0].data.length;
                var html = '';
                for(var i=0;i<length;i++){
                    html += '<tr>'+
                                '<td><div class="td-cont flex"><img class="image" src="'+imgUrl+data.data[0].data[i].img+'"><p>'+data.data[0].data[i].title+'</p></div></td>'+
                                '<td><span>'+data.data[0].data[i].created_at+'</span></td>'+
                                '<td><div class="td-cont text-right"><a href="javascript:void(0);" data-id="'+data.data[0].data[i].id+'" data-name="'+data.data[0].data[i].title+'" class="js-choose">选取</a></div></td>'+
                            '</tr>'
                }
                $("#myModal tbody").html(html)
                $('.js-choose').each(function(key,val){
                    for(var j=0;j<pids.length;j++){
                        if($(this).data('id') == pids[j]){
                            $(this).addClass('choose_btn_1').html('取消');
                        }
                    }
                })
                var totalCount = data.data[0].total, showCount = 8,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page='+page +'&title=' + val,function(response){
                            if(response.status ==1){
                                var length = response.data[0].data.length;
                                var html = '';
                                for(var i=0;i<length;i++){
                                    html += '<tr>'+
                                                '<td><div class="td-cont flex"><img class="image" src="'+imgUrl+response.data[0].data[i].img+'"><p>'+response.data[0].data[i].title+'</p></div></td>'+
                                                '<td><span>'+response.data[0].data[i].created_at+'</span></td>'+
                                                '<td><div class="td-cont text-right"><a href="javascript:void(0);" data-id="'+response.data[0].data[i].id+'" data-name="'+response.data[0].data[i].title+'" data-type="1" class="js-choose">选取</a></div></td>'+
                                            '</tr>'
                                }
                                $("#myModal tbody").html(html)
                                $('.js-choose').each(function(key,val){
                                    for(var j=0;j<pids.length;j++){
                                        if($(this).data('id') == pids[j]){
                                            $(this).addClass('choose_btn_1').html('取消');
                                        }
                                    }
                                })
                            }
                        })
                    }
                });
            }
        })
    }
    // 分销等级选取指定商品
    $('body').on('click','.js-choose',function(){
        if($(this).hasClass('choose_btn_1')){
            $(this).removeClass('choose_btn_1').html('选取');
        }else{
            $(this).addClass('choose_btn_1').html('取消');
        }
    })
    $('body').on('click','.close-item',function(){
        $(this).siblings(".product-item").attr("data-pid",'');
        $(this).parents('td').find(".add-product").show();
        $(this).parents('td').find(".change-content").hide();
        console.log(111)
    })
    $('body').on('click','.close',function(){
        $("#myModal").hide()
    })
    // 分销等级确定商品
    $('body').on('click','.js-confirm-choose',function(){
        var pid = $('.product_info').attr('data-pid');
        if(pid != ''){
            pid = pid.split(',');
        }else{
            pid = [];
        }
        $('.js-choose').each(function(key,val){
            if($(this).hasClass('choose_btn_1')){
                $('.product_info').text($(this).attr('data-name'));
                var num = 0;
                for(var i=0;i<pid.length;i++){
                    if(pid[i] != $(this).attr('data-id')){
                        num++;
                    }
                }
                if(num == pid.length){
                    pid.push($(this).attr('data-id'));
                }
            }else{
                for(var i=0;i<pid.length;i++){
                    if(pid[i] == $(this).attr('data-id')){
                        pid.splice(i,1)
                    }
                }
            }
        })
        console.log(pid)
        $('.product_info').attr({'data-pid':pid,'data-num':pid.length});
        $('.change-product').attr('data-pid',pid)
        $("#myModal").hide();
        $(".add-product").hide();
        $(".change-content").show();
    })
    //验证数字文本框
    $("body").on('keyup','.t-number',function(e){   
        var val = $(this).val().replace(/\D/g,'');
        $(this).val(val)
    });
    // 保存第1分销等级
    $("body").on('click','.grade_save_first',function(){
    	var _this = this;
    	var tr =  $(this).parents('tr');
        var grade_title = tr.find('.grade-name').val();
		console.log(grade_title)
		// 验证
        if(grade_title == ''){
            tipshow('请填写等级名称','warn');
            return;
        }
        layer.open({
            type: 1,
            title: '提示<span id="closeBtn">x</span>', 
            closeBtn: false,
            skin:"layer-tskin", 
            shade: 0.8,
            anim: 2,
            btnAlign: 'c',
            move:false,
            btn:['确认','取消'],
            closeBtn:1,
            yes:function(index, layero){
                $.ajax({
                    type:"post",
                    url:"/merchants/store/set",
                    data:{
                        grade_title:grade_title,
                    },
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        $(_this).removeClass('grade_save').addClass('grade_edit').val('编辑');
                        $(_this).siblings('.grade_cancel').removeClass('grade_cancel').addClass('grade_del').val('删除');
                        $("#is_on_off").val(0);
                        $('.product-item').removeClass('product_info');
                        $(_this).parents('tr').find('.change-product').hide();
                        $(_this).parents('tr').find('.close-item').hide();
                        layer.closeAll();
                        tipshow('添加成功','info');
                        $("#is_off_on").val(0)
                        location.reload()
                    },
                    error:function(){
                        tipshow("异常","warn");
                    }
                })
                
            },
            cancel:function(index, layero){
                layer.closeAll()
            },
            content: '<div style="padding-bottom: 30px;width:250px;">保存成功。</div>'
        });
    });
    
    // 保存分销等级
    $("body").on('click','.grade_save',function(){
        var _this = this;
        var tr =  $(this).parents('tr');
        var id = $(this).parents('td').attr('data-id');
        var title = tr.find('.grade-name').val();
        var pid = [];
        var extension_amount_stauts = tr.find(".extension_amount input[type='checkbox']").is(':checked');
        var total_amount_status = tr.find(".total_amount input[type='checkbox']").is(':checked');
        var extension_amount = tr.find(".extension_amount input[type='text']").val();
        var total_amount = tr.find(".total_amount input[type='text']").val();
        // 验证
        if(title == ''){
            tipshow('请填写等级名称','warn');
            return;
        }
        if(extension_amount_stauts){
            if(parseFloat(extension_amount) == 0 || extension_amount == ''){
                tipshow('请填写大于0的推广金','warn');
                return
            }
        }
        if(total_amount_status){
            if(total_amount == '' || parseFloat(total_amount) == 0 ){
                tipshow('请填写大于0的推广金与消费金总和','warn');
                return;
            }
            if(extension_amount_stauts && parseFloat(total_amount) < parseFloat(extension_amount)){
                tipshow('累计推广金与消费金总和应大于累计推广金',"wran"); 
                return;
            }
        }
        if(tr.find(".product_choice input[type='checkbox']").is(':checked')){
            pid = tr.find(".product-item").attr('data-pid');
            if(tr.find(".product-item").attr('data-num') == 0){
                pid = [pid];
            }else{
                pid = pid.split(',');
            }
            
            if(pid.length == 0 || pid == ''){
                tipshow('请选择指定商品','warn');
                return;
            }
        }
        layer.open({
            type: 1,
            title: '提示<span id="closeBtn">x</span>', 
            closeBtn: false,
            skin:"layer-tskin", 
            shade: 0.8,
            anim: 2,
            btnAlign: 'c',
            move:false,
            btn:['确认','取消'],
            closeBtn:1,
            yes:function(index, layero){
                $.ajax({
                    type:"post",
                    url:"/merchants/distribute/addStoreDistributeGrade",
                    data:{
                        title:title,
                        extension_amount:extension_amount,
                        total_amount:total_amount,
                        pids:pid,
                        id:id
                    },
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        $(_this).removeClass('grade_save').addClass('grade_edit').val('编辑');
                        $(_this).siblings('.grade_cancel').removeClass('grade_cancel').addClass('grade_del').val('删除');
                        $("#is_on_off").val(0);
                        $('.product-item').removeClass('product_info');
                        $(_this).parents('tr').find('.change-product').hide();
                        $(_this).parents('tr').find('.close-item').hide();
                        layer.closeAll();
                        tipshow('添加成功','info');
                        $("#is_off_on").val(0)
                        location.reload()
                    },
                    error:function(){
                        tipshow("异常","warn");
                    }
                })
                
            },
            cancel:function(index, layero){
                layer.closeAll()
            },
            content: '<div style="padding-bottom: 30px;">添加成功，该等级佣金默认与上个等级保持一致，在分销模版中修改。</div>'
        });
    })
    // 取消编辑第一分销等级
    $("body").on('click','.grade_cancel_first',function(){
		$("#is_on_off").val() == 1 ? $("#is_on_off").val(0) :'';
        $("#is_off_on").val(0)
        getDistributeGrade()
    })
    // 取消编辑分销等级
    $("body").on('click','.grade_cancel',function(){
        $("#is_on_off").val() == 1 ? $("#is_on_off").val(0) :'';
        $("#is_off_on").val(0)
        if($(this).data('key') == 1){
            // $(this).siblings('.grade_save').removeClass('grade_save').addClass('grade_edit').val('编辑');
            // $(this).removeClass('grade_cancel').addClass('grade_del').val('删除');
            // $(this).parents('tr').find("input[type='checkbox']").attr('disabled','disabled');
            // $(this).parents('tr').find(".grade-name").attr('disabled','disabled');
            // $(this).parents('tr').find(".change-content").attr('data-key',0);
            getDistributeGrade()
        }else{
            $(this).parents('tr').remove();
            $(".add-grade").show()
        }
        
    })
    // 删除分销等级
    $('body').on('click','.grade_del',function(){
        $("#is_on_off").val() == 1 ? $("#is_on_off").val(0) :'';
        if($("#is_off_on").val() == 1){
            tipshow('您有未保存的分销等级','warn');
            return;
        }
        $("#is_off_on").val(0);
        var id = $(this).parents('td').attr('data-id');
        var _this = this;
        layer.open({
            type: 1,
            title: '提示<span id="closeBtn">x</span>', 
            closeBtn: false,
            skin:"layer-tskin", 
            shade: 0.8,
            anim: 2,
            btnAlign: 'c',
            move:false,
            btn:['确认','取消'],
            closeBtn:1,
            yes:function(index, layero){
                $.ajax({
                    url:'/merchants/distribute/delStoreDistributeGrade',
                    type:"post",
                    data:{
                        id:id
                    },
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        if(res.status == 1){
                            $(_this).parents('tr').remove();
                            $(".add-grade").show()
                            layer.closeAll();
                            tipshow(res.info,'info')
                            location.reload()
                        }
                    },
                    error:function(){
                        tipshow("异常","warn");
                    }
                })
            },
            cancel:function(index, layero){
                layer.closeAll()
            },
            content: '<div style="padding-bottom: 30px;">删除对应分销等级，该等级用户身份将被重置，请谨慎删除。已经完成的该等级分销佣金不会发生变化。</div>'
        });
    })
    $("body").on('click',".rule-item input[type='checkbox']",function(){
        $(this).is(":checked") ? $(this).siblings('p').find('input').removeAttr('disabled') :$(this).siblings('p').find('input').attr('disabled','disabled');
    })
    $("body").on('keydown','.js-modal-search-input',function(e){
        if(e.key == 'Enter' || e.keyCode == 13){
            var val = $(this).val();
            var pids = $(this).attr('data-pids');
            if(pids == ''){
                pids = [];
            }else{
                pids = pids.split(',');
            }
            getProduct(pids,val)
        }
        
    })
});
