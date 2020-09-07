
$(function(){

    //导出店铺信息(目前主要是商品及相关信息)
    $("body").on('click','.export',function(){
        var wid_from = $(this).data('id');
        $('.wid_from').text(wid_from);
        layer.open({
            type: 1,
            area: ['400px', 'auto'],
            title: '请输入需要导出到的目标店铺',
            shadeClose: true, //点击遮罩关闭
            content: $('.change_tip').html(),
            btn: ['确认', '取消'],
            success:function(index,layero){
            },
            yes:function(idnex,layero){
                var data = {
                    wid_from:wid_from,
                    wid_to:$(layero).find(".wid_to").val()
                };
                $.ajax({
                    url:"/staff/BusinessManage/export",
                    type:"POST",
                    data:data,
                    dataType:'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(res){
                        if(res.status == 1){
                            tipshow(res.info,"info");
                        }else{
                            tipshow(res.info,"warn")
                        }
                    },
                    error:function(res){
                        alert("数据访问错误");
                    }
                });

                layer.closeAll();
            }
        });
    });

    //全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	});
	
	//修改信息
	$(document).on("click",".modify", function(){
            $.ajax({
                url:'/staff/showEditShop',// 跳转到 action
                data:{'id':$(this).attr('id')},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                async: false,
                success:function (response) {
                  $("#myModal").html(response);
                },
                error : function() {
					return false;
                    tipshow("异常！");
                }
            });



        $(document).on('click','.top_radio li input',function(){
            $('.classfiy_1').html($(this).next().html());
            var id = $(this).parent().parent().data('id');
            $('input[name="business_id"]').val($(this).val());
            $('#classfiy_input').val(id);
            var html = '';
            for(var i = 1;i<business_datas.length;i++){
                for(var j = 0;j<business_datas[i].length;j++){
                    if(business_datas[i][j]['pid'] == id){
                        if(j == 0){
                            $('.classfiy_2 .c-gray').html(business_datas[i][j]['title']);
                            $('input[name="business_id"]').val(business_datas[i][j]['id']);
                            html += '<li><label class="radio"><input type="radio" name="shop_category_2" value="'+ business_datas[i][j]['id']+'" checked><span>'+ business_datas[i][j]['title']+'</span></label></li>'
                        }else{
                            html += '<li><label class="radio"><input type="radio" name="shop_category_2" value="'+ business_datas[i][j]['id']+'"><span>'+ business_datas[i][j]['title']+'</span></label></li>'
                        }
                    }
                }
            }
            if(html != ''){
                $('.choose_classfiy_2').css('display','inline-block');
                $('.choose_classfiy_2 .widget-selectbox-content ul').html(html)
            }else{
                $('.choose_classfiy_2').css('display','none');
            }
        })




		$('#myModal').modal('toggle');
		//模态框居中设置
		t=setTimeout(function () {
			var _modal = $('#myModal').find(".modal-dialog")
			_modal.css({'margin-top': parseInt(($(window).height() - _modal.height())/5.5)})
		},0)
	})



	$(document).on("click", "#myModal .sub", function(){
		var inp_1 = $("#storeName").val();
		var inp_2 = $("#address").val();
		var inp_3 = $("#companyName").val();
		var inp_4 = $("#Btime").val();
		var inp_5 = $("#Ctime").val();
		if(inp_1!=""&&inp_2!=""&&inp_3!=""&&inp_4!=""&&inp_5!=""){
	  		layer.msg('提交成功', {	
	  			icon: 6,
	  			time: 2000
	  		});
	  		$('#myModal').modal('hide')
		}else{
			layer.msg("须填不可空")
		}
	});
    
    //按钮-是否付费
    $(document).on('click','#isFee,#isFree,#isGive,#isOpen,#isClose,#all_open',function(){
        var judgeFree = [];
        var changeLogo = [];
        $(".table_body input[type='checkbox']:checked").each(function(){
            judgeFree.push($(this).val())
            changeLogo.push($(this).parents('.sheet').find('.Fimg'))
        })
        free_ids = JSON.stringify(judgeFree)
        if($(this).attr('id') == 'isFee'){
            $(changeLogo).each(function(){
                $(this).find('.logo').attr({src:host+'staff/hsadmin/images/fu@2x.png'});
            })
            transFreeAjax(free_ids,1);
        }else if( $(this).attr('id') == 'isGive' ){
            $(changeLogo).each(function(){
                $(this).find('.logo').attr({src:host+'staff/hsadmin/images/zeng@2x.png'});
            })
            transFreeAjax(free_ids,2);
        }else if( $(this).attr('id') == 'isFree' ){
            $(changeLogo).each(function(){
                $(this).find('.logo').attr({src:host+'staff/hsadmin/images/mian@2x.png'});
            })
            transFreeAjax(free_ids,0);
        }else if( $(this).attr('id') == 'isOpen' ){
            transFreeAjax(free_ids,3);
        }else if( $(this).attr('id') == 'isClose' ){
            transFreeAjax(free_ids,4);
        }else if( $(this).hasClass('js-open') ){
            transFreeAjax('["all"]',5);
            $(this).removeClass('js-open').addClass('js-close').text('全部关闭底部logo链接')
        }else if($(this).hasClass('js-close')){
            transFreeAjax('["all"]',6);
            $(this).removeClass('js-close').addClass('js-open').text('全部开启底部logo链接')
        }
        judgeFree = [];
        changeLogo = [];
        return
    })
    function transFreeAjax(ids,isFee){
        if(ids != '[]' && ids != ''){
            $.ajax({
                url:'/staff/store/updateForFee',
                data:{ids,isFee},
                type:'POST',
                headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:'json',
                success:function(res){
                    $(".table_body input[type='checkbox']").prop("checked", false);
                    tipshow("标记成功！", "info", 1000)
                }
            })
        }
    }


	//推荐
    // @update 许立 2018年09月27日 取消推荐功能
    // @update 许立 2018年09月27日 推荐和取消推荐后刷新网页
	$(".main_content .recommend").each(function(index,ele){
		$(this).click(function(evt){
            var obj = $(this);
            var recommendText = $(this).text();
            var antiRecommendText = recommendText == '推荐' ? '已推荐' : '推荐';
            var cancelRecommendText = recommendText == '推荐' ? '推荐' : '取消推荐';
            clearEventBubble(evt);
            var _this = $(this);
            var success = function(){
                $.ajax({
                    url:'/staff/recommend',// 跳转到 action
                    data:{"id":obj.attr('id')},
                    type:'post',
                    cache:false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    success:function (response) {
                        if (response.status == 1){
                            window.location.reload();
                        }else{
                            tipshow(response.info);
                        }
                    },
                    error : function() {
                        tipshow("异常！");
                    }
                });
                _this.text(antiRecommendText)
                tipshow(cancelRecommendText + '成功', "info", 1000)
            };
            showDelProver($(this), success,"你确定" + cancelRecommendText + "吗？", true, 1,8);
		})
	});
	
	//删除
	$(document).on("click", ".main_content .del", function(evt){
        var obj = $(this);
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            var request  = obj.attr('id').split('_')
            $.ajax({
                url:'/staff/delShop',// 跳转到 action
                data:{
                    'id':request[0],
                    'uid':request[1]
                },
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                    }else{
                        tipshow(response.info);
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });



			delEle.remove();
			tipshow("删除成功！", "info", 1000)
		};
		showDelProver($(this), success,"你确定要删除吗？", true, 1,8);
	});
	
	//时间插件
	$('#datetimepicker1, #datetimepicker2, #datetimepicker3, #datetimepicker4').datetimepicker({
    	format: 'YYYY-MM-DD HH:mm:ss',               
	    dayViewHeaderFormat: 'YYYY 年 MM 月DD日',
	    useCurrent: true, 
    	showClear:true,                               
	    showClose:true,                               
	    showTodayButton:true,
	    locale:'zh-cn',
	    allowInputToggle:true, 
	    focusOnShow: true,
        useCurrent: false 				//必须要设置的
    });
	//datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });

    $("#datetimepicker3").on("dp.change", function (e) {
        $('#datetimepicker4').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker4").on("dp.change", function (e) {
        $('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
    });


    $("body").on("click","#clean",function () {
        console.log(111)
         var wid = $(this).data('wid');
        $.ajax({
            url:'/staff/BusinessManage/cleanDistribute',// 跳转到 action
            data:{
                'wid':wid,
            },
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow(response.info,'info');
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });
    })


    //添加地址；
    var area = "<option value=''>选择区县</option>";
    /*省市区三级联动*/
    $('.js-province').change(function(){
        var dataId = $('.js-province option:selected').val();
        var province = json[dataId];
        var city = "<option value=''>选择城市</option>";
        for(var i = 0;i < province.length;i ++){
            city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
        }
        $('.js-city').html(city);
        $('.js-area').html(area);
    });
    $('.js-city').change(function(){
        var dataId = $('.js-city option:selected').val();
        var city = json[dataId];

        for(var i = 0;i < city.length;i ++){
            area += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
        }
        $('.js-area').html(area);
    });

    //店铺导出
    $("#shop_export").click(function(){
        window.location.href = '/staff/shopExport?' + $('#shop_form').serialize();
    });

    //重置按钮
    $("#reset").click(function () {
        $(".js-province option:first").attr("selected",true).siblings("option").attr("selected",false);
        $(".js-city option:first").attr("selected",true).siblings("option").attr("selected",false);
        $(".js-area option:first").attr("selected",true).siblings("option").attr("selected",false);
        $("#shop_form :input").attr('value','');
    })

    //解除登录
    $(document).on("click", ".main_content .relieve", function(evt){
        var obj = $(this);
        clearEventBubble(evt);
        var success = function(){
            var request  = obj.data('account');
            $.ajax({
                url:'/staff/relieveLogin',// 跳转到 action
                data:{
                    'account':request
                },
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                    }else{
                        tipshow(response.info);
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });
            tipshow("解除成功！", "info", 1000)
        };
        showDelProver($(this), success,"确定解除登录限制？", true, 1,8);
    });
    // add by zhaobin 2018-9-20
    // 备注弹窗
    $(".remarks").click(function(){
        var uid = $(this).data("uid");
        var wid = $(this).data('wid');
        layer.open({
            type: 1,
            area: ['400px', '280px'],
            title: '备注信息',
            shadeClose: true, //点击遮罩关闭
            content: $(this).nextAll(".remarks_tip").html(),
            // btn: ['确认', '取消'],
            yes: function(){
            },
            success:function(){
                $("body").on("click",".success_remarks",function(){
                    console.log($(this).parent().parent().find(".sales-man").val())
                    $.ajax({
                        url:'/staff/userRemark',
                        type:'post',
                        data:{
                            uid:uid,
                            wid:wid,
                            salesman:$(this).parent().parent().find(".sales-man").val(),
                            achievement:$(this).parent().parent().find(".achievement").val(),
                            trade:$(this).parent().parent().find(".trade").val()
                        },
                        dataType:'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(res){
                            console.log(res)
                            if(res.status == 1){
                                tipshow(res.info,'info');
                                layer.closeAll();
                                window.location.reload();
                            }else{
                                tipshow(res.info,'warn')
                            }
                            
                        }
                    })
                })
                $("body").on("click",".btn-cancle",function(){
                    layer.closeAll()
                })
            }
        })
    })
    
    // end
    // add by zhaobin 2018-9-25
    // 查看二维码
    $(".qrcode").click(function(e){
        e.stopPropagation()
        var id = $(this).attr("data-id");
        layer.open({
            type:1,
            area:['500px','auto'],
            title:'二维码',
            shadeClose:true,
            content:$(".qrcode-tip").html(),
            success:function(){
                // 获取二维码
                $.ajax({
                    url:'/staff/getShopCode',
                    type:'GET',
                    data:{
                        id:id
                    },
                    dataType:'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(res){
                        console.log(res)
                        if(res.status == 1){
                            $(".qrcode-img-wsc .wxc-qrcode").html(res.data.code);
                            if(res.data.xcxCode.errCode == 0 && res.data.xcxCode.data){
                                var xcximg = '<img src="data:image/png;base64,'+res.data.xcxCode.data+'" />'
                                $(".qrcode-img-xcx .xcx-qrcode").html(xcximg);
                            }else{
                                $(".qrcode-img-xcx").hide()
                            }
                        }
                    }
                })
            }
        })
    })
    
    // 点击更多
    $(".more").click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(this).parent().parent().find(".more-operate").css({"top":top-180,"left":left-210});
        $(this).parent().parent().find(".more-operate").show();
    })
    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.more-operate');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
            $(".more-operate").hide();
        }
    });
    // 单个店铺忽略
    $("body").on("click",".ignore",function(){
        var ignore;
        if($(this).data("ignore")){
            ignore = 0;
            $(this).text('忽略');
        }else{
            ignore = 1;
            $(this).text('已忽略');
        }
        var id = $(this).data('id');
        $.ajax({
            url:'/staff/ignoreShop',
            data:{
                id:id,
                ignore:ignore
            },
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                console.log(response)
                if (response.status == 1){
                    tipshow(response.info,'info');
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });
    })
    // 一键忽略全部店铺
    $("body").on("click","#shop_ignore",function(){
        $.ajax({
            url:'/staff/batchIgnore',
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (res) {
                console.log(res)
                if(res.status == 1){
                    tipshow(res.info,"info")
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });
    })
    $("#shop_case").one('click',function(){
        $.ajax({
            url:'/staff/sync/weixin_case',
            data:{},
            type:'get',
            cache:false,
            dataType:'json',
            success:function (res) {
                if(res.errCode == 0){
                    tipshow(res.msg,"info")
                }else{
                    tipshow(res.msg,"warn")
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });
    })

    // end
})
