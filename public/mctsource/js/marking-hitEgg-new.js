$(function(){
    //添加奖项
    var couponId = [];//优惠券id
    var integralId = [];//积分id
    var giftId = []
    var method = '';
    var prize_img = '';
    var prize_num = '';
    //获取奖品数据
    checkData();
    function checkData(){
        //检查数据前先初始化数据
        couponId = [];
        integralId = [];
        $(".getPrize_data").each(function(){
            if($(this).find("input[name='prize_type[]']").val() == 1){//优惠券
                couponId.push($(this).find("input[name='prize_id[]']").val());
            }else if($(this).find("input[name='prize_type[]']").val() == 2){//积分
                integralId.push($(this).find("input[name='prize_id[]']").val());
            }else if($(this).find("input[name='prize_type[]']").val() == 3){//赠品
                giftId.push($(this).find("input[name='prize_id[]']").val());
            }
        });
    }
    var ue = UE.getEditor("active_tetail",{
        toolbars: [
               [
                'bold', //加粗
                'indent', //首行缩进
                'italic', //斜体
                'underline', //下划线
                'strikethrough', //删除线
                'subscript', //下标
                'fontborder', //字符边框
                'superscript', //上标
                'formatmatch', //格式刷
                'pasteplain', //纯文本粘贴模式
                'horizontal', //分隔线
                'removeformat', //清除格式
                'selectall', //全选
                'print', //打印
                'preview', //预览
                'fontfamily', //字体
                'fontsize', //字号
                'paragraph', //段落格式
                'edittable', //表格属性
                'edittd', //单元格属性
                'link', //超链接
                'spechars', //特殊字符
                'forecolor', //字体颜色
                'backcolor', //背景色
                'rowspacingtop', //段前距
                'rowspacingbottom', //段后距
                'imagenone', //默认
                'imagecenter', //居中
                'lineheight', //行间距
                'edittip ', //编辑提示
                'customstyle', //自定义标题
                'autotypeset' //自动排版
            ]
        ],
        maximumWords:500,
        initialFrameHeight:200,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    });
    //富文本change事件
    ue.addListener("ready", function () { 
        var content = editorContent ? editorContent : "<p>亲，祝您好运哦！</p>";
        ue.setContent(content)
        ue.addListener( "selectionchange", function () {
            var _html = ue.getContent();
            if(!ue.getContent()){
                _html = "<p>亲，祝您好运哦！</p>";
            }
            $("#active_tetail").val(_html);
            $(".egg_intro").html(_html);
        } );
    });  
	//点击活动列表 显示列表
	$(".content_top li").click(function(){
		location.href='/merchants/marketing/egg/index';
	})
	
	$(".new_add").each(function(index, ele){
		if ($(this).attr("src")!="") {$(this).show();}
	});
	//活动详情中输入，活动规则中实时显示
	
	//中奖名单的显示隐藏；
	$("input[name='showName']").each(function(index, ele){
		$(this).click(function(){
			var judge = $(this).val();
			judge == "show" ? $(".show_name").show():$(".show_name").hide()
		})
	});
	// 开始时间
    $('#datetimepicker1,#datetimepicker2').datetimepicker({
        minDate: new Date(), //时间小于当前时间时会自动清空以有的数据
        format: 'YYYY-MM-DD HH:mm:ss',
        showClear: true,
        showClose: true,
        showTodayButton: true,
        locale: 'zh-cn',
        focusOnShow: false,
        useCurrent: false,
        tooltips: {
            today: '今天',
            clear: '清除',
            close: '关闭',
            selectMonth: '选择月',
            prevMonth: '上个月',
            nextMonth: '下一月',
            selectTime: '选择时间',
            selectYear: '选择年',
            prevYear: '上一年',
            nextYear: '下一年',
            selectDecade: '十年一组',
            prevDecade: '前十年',
            nextDecade: '后十年',
            prevCentury: '前一世纪',
            nextCentury: '后一世纪',
        },
        allowInputToggle: true,
    }); 
    if(start_at){
        $("#datetimepicker1").val(start_at);
    }
    if(end_at){
        $("#datetimepicker2").val(end_at);
    }
    //datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        $(".start_time").removeClass("hide");
        $(".start_time .start_time_s").text($("#datetimepicker1").val());
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        $(".end_time").removeClass("hide");
        $(".end_time .end_time_s").text($("#datetimepicker2").val());
    });
    //设置次数
    $(".js_set").change(function(){
       if($(this).val() == 1){//每天
            $(".js_daily").removeClass("hide");
            $(".js_the").addClass("hide");
       }else{
           $(".js_the").removeClass("hide"); 
           $(".js_daily").addClass("hide");
       }
    })
    $(".js_times").change(function(){
        $(".js_daily .red").text($(this).val());
        $(".js_the .red").text($(this).val());
    })
	//表单验证
    $('#defaultForm').bootstrapValidator({
        message: '填写的值不合法',
        excluded:[":hidden"],//只对禁用域不进行验证
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            title: {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: '活动名称不能为空'
                    },
                    stringLength: {
                        min: 0,
                        max: 20,
                        message: '活动名称小于20个字符'
                    },
                }
            },
            start_at: {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: '开始时间不能为空'
                    }
                }
            },
            end_at: {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: '结束时间不能为空'
                    }
                }
            },
            detail: {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: '活动详情不能为空'
                    }
                }
            },
            noPrize_probability: {
                trigger: "change",
                validators: {
                    notEmpty: {
                        message: '奖项不能为空'
                    }
                }
            },
            'prize_name[]': {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            'prize_probability[]': {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            'prize_number[]': {
                trigger: "blur",
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
        }
    });
	
    //手动重新验证
	function Revalidate(ele){
		$('#defaultForm')
			.data('bootstrapValidator')
	        .updateStatus(ele, 'NOT_VALIDATED', null)
	        .validateField(ele);
	}
    
    //手动验证判断条件(点击下一步按钮  或  第二步奖项设置)
    $('.next, .step li:eq(1)').click(function(){   
        /*验证分享内容*/
        var share_title = $('#share_title').val();
        var share_desc = $('#share_detail').val();
        var share_img = $('input[name="shareImg"]').val();
        if(!((share_title && share_desc && share_img) || (!share_title && !share_desc && !share_img))){//都有内容或者都没内容通过
            // tipshow("请填写分享内容","warn");
            // return false;
            if(!share_img && share_title && share_desc){
                tipshow("请填写分享图片","warn");
                return false;
            }
            if(!share_title && share_img && share_desc){
                tipshow("请填写分享标题","warn");
                return false;
            }
            if(!share_desc && share_title && share_img){
                tipshow("请填写分享内容","warn");
                return false;
            }
            if(share_img){
                tipshow("请填写分享标题及内容","warn");
                return false;
            }
            if(share_title){
                tipshow("请填写分享内容及图片","warn");
                return false;
            }
            if(share_desc){
                tipshow("请填写分享标题及图片","warn");
                return false;
            }
        }    
        $('#defaultForm').bootstrapValidator('validate'); 
        var judge =$('#defaultForm').data('bootstrapValidator').isValid();
        if(!judge){   // 未通过验证
            $('#defaultForm').data('bootstrapValidator').validate();
        }else{       // 通过验证
       		step_x_show(".step_1", ".step_2");
        }
        $(".public_save").attr("disabled",false);
    });
    
    //验证过第一步后 点击流程图的 第一步  切换到第一部中；
    $(".step li:eq(0)").click(function(){
    	step_x_show(".step_2", ".step_1");
    })
    //表单提交
    $(".public_save").click(function(e){
        var _this = $(this);
        $('#defaultForm').bootstrapValidator('validate'); 
        var judge =$('#defaultForm').data('bootstrapValidator').isValid();

        if(!judge){   // 未通过验证
            $('#defaultForm').data('bootstrapValidator').validate();
        }else{       // 通过验证
            /* 将form表单序列化成对象object*/
            $.fn.serializeObject=function(){    
                var obj=new Object();
                $.each(this.serializeArray(),function(index,param){
                    if(!(param.name in obj)){
                        obj[param.name]=param.value;      
                    }    
                });    
                return obj;  
            }; 
            var data = $("#defaultForm").serializeObject();
            _this.attr("disabled",true);
            
            var url = edit?'/merchants/marketing/egg/edit/'+edit:'/merchants/marketing/egg/add';
            $.post(url,$("#defaultForm").serialize(),function (data) {
//          	console.log($("#defaultForm").serialize())
//          	return false;
               if(data.status == 1)
               {
                    tipshow("操作成功");
                    window.location.href= "/merchants/marketing/egg/index";
               }else{
                    tipshow(data.info,"warn");
               }
               _this.attr("disabled",false);
            });
        }
        e.preventDefault();
    });
    //添加积分
    $(".setIntegral").click(function(){
        $(".integralList").addClass("hide");
        $(".modal-footer").addClass("hide");
        $(".addIntegral").removeClass("hide");
        $(".myModalPage1").hide();
    });
    //取消添加积分
    $(".addIntegral .btn-default").click(function(){
        $(".integralList").removeClass("hide");
        $(".modal-footer").removeClass("hide");
        $(".addIntegral").addClass("hide");
    });
    //确定添加积分
    $(".addIntegral .sureAddIntegral").click(function(){
        var per_score = $(".per_score").val();
        var amount_score = $(".amount_score").val();
     
        var data = {
            per_score: per_score,
            amount_score: amount_score,
            _token: $("meta[name='csrf-token']").attr("content")
        }
        $.post('/merchants/marketing/score/add', data, function(res) {
            if(res.status == 1){
                $.get("/merchants/marketing/score/get",function(response){
                    if( response.status == 1 ){
                        var data = response.data;
                        modelListSuccess(data);
                        $('.myModalPage1').extendPagination({
                            totalCount: data[0].total,
                            showCount: data[0].last_page,
                            limit: data[0].per_page
                        });
                        $(".myModalPage1").show();
                        $(".integralList").removeClass("hide");
                        $(".modal-footer").removeClass("hide");
                        $(".addIntegral").addClass("hide");

                    } else {
                        tipshow(response.info,"warn")
                    }
                })
            }else{
                tipshow(res.info,"warn")
            }
        });
    });
    //创建商品
    $(".setPrize").click(function(){
        if(coupons == 1){
            window.open("/merchants/marketing/coupon/set");//打开优惠券页面
        }else if(coupons == 2){
            $(".integralList").addClass("hide");
            $(".modal-footer").addClass("hide");
            $(".addIntegral").removeClass("hide");
            $(".myModalPage1").hide();
        }
    })
    //图片上传
    function uploadImg(func,_this){
        $("#upload input[name='file']").off().click();
        $("#upload input[name='file']").off().change(function(){
            var data = new FormData($("#upload")[0]);
            $.ajax({  
                url: '/merchants/myfile/upfile',  
                type: 'POST',  
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,  
                dataType: 'JSON',  
                cache: false,  
                processData: false,  
                contentType: false,
                success: function(response){
                    if( response.status == 1 ){
                        func(response,_this);
                        //上传后清空数据
                        $("#upload input[name='file']").val("");
                    } else {
                        alert("失败")
                    }
                }  
            });  
        });
    }
    //上传活动图片
    var obj = "";
    $(".activeImg").click(function(){
        obj = $(this);
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
        /**
         * 图片选择后的回调函数
         */
        selImgCallBack = function(resultSrc){ 
            if(resultSrc.length>0){
                //2018.10.17 砸金蛋活动图片尺寸限制 by 倪凯嘉
                var scale=resultSrc[0].imgWidth/resultSrc[0].imgHeight;
                console.log(scale);
                var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
                var sum = parseInt(750 / 375 * 100) / 100
				if( num < sum - 0.2 || num > sum + 0.2){
                    tipshow("图片比例非2:1，请重新上传","warn");
                    return false
                }
                if(parseInt(resultSrc[0].imgWidth) < 320){
                    tipshow("图片尺寸小于320px，请重新上传。","warn");
                    return false
                }
				// if(parseInt(resultSrc[0].imgWidth)>=640 && scale>=1.8 && scale<=2.1){

                    //$("input[name='start_img_url']").val(resultSrc[0].imgSrc);
                    //$(".share_img").attr("src",_host+resultSrc[0].imgSrc).parent().removeClass('hide');
                    obj.parent().siblings("input").val(resultSrc[0].imgSrc);
                    obj.parent().siblings("img").attr("src",_host+resultSrc[0].imgSrc);
                // }else{
                    // tipshow('图片尺寸不符合，请重新上传图片','warm');
                // }
            } 
        }
    });
    //分享封面
    $(".js-add-picture").click(function(){
        obj = $(this);
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
        /**
         * 图片选择后的回调函数
         */
        selImgCallBack = function(resultSrc){ 
            if(resultSrc.length>0){
                //2018.10.17 砸金蛋分享图片尺寸限制 by 倪凯嘉
                if(parseInt(resultSrc[0].imgWidth)/parseInt(resultSrc[0].imgHeight) != 1){
                    tipshow("图片比例非1:1，请重新上传","warn");
                    return false
                }
                if(parseInt(resultSrc[0].imgWidth) < 400){
                    tipshow("图片尺寸小于400px，请重新上传","warn");
                    return false
                }
                $("input[name='shareImg']").val(resultSrc[0].imgSrc);
                $(".share_img").attr("src",_host+resultSrc[0].imgSrc).parent().removeClass('hide');  
                //obj.children("input").val(resultSrc[0].imgSrc);
                //obj.children("img").attr("src",_host+resultSrc[0].imgSrc);
                $(".js-add-picture").html("修改图片").removeClass("add-goods");
            } 
        }
    });
     
    //上传图片 鼠标悬浮显示 关闭图标
    $(".delete").click(function(){
        $("input[name='shareImg']").val("");
		$(".share_img").attr("src","").parent().addClass('hide');  
		$(".js-add-picture").html("+添加图片").addClass("add-goods");
    });
    //模态框居中控制
    $('.modal').on('shown.bs.modal', function (e) { 
        // 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
        $(this).css('display', 'block'); 
        var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
        if(modalHeight < 0){
            modalHeight = 0;
        }
        $(this).find('.modal-dialog').css({ 
            'margin-top': modalHeight 
        }); 
    });
	//模态框的标题 点击显示页面事件
	//update by 韩瑜 2018-8-13 新增赠品
	$(".modal-header .modal-title span").each(function(index, ele){
		$(this).click(function(){
			$(".modal-header .modal-title span").addClass("a_active");
			$(".modal-body").addClass("hide");
			$(this).removeClass("a_active");
			$(".modal-body_"+(index+1)).removeClass("hide");
            if($(this).hasClass("coupons")){//优惠券
                var url="/merchants/marketing/egg/getCouponList";
                coupons = 1;
                $(".modal-footer").removeClass("hide");
            }else if($(this).hasClass("cromo_code")){//会员卡
                var url="/merchants/marketing/score/get";
                coupons = 2;
                if($(".modal-body_2 .integralList").hasClass("hide")){
                     $(".modal-footer").addClass("hide");
                }
            }else if($(this).hasClass("gift")){
            	coupons = 3;
            	if($(".modal-body_3 .integralList").hasClass("hide")){
                     $(".modal-footer").addClass("hide");
                }
            }
            getModelList(url);
		})
	})
	//end
    
    //模态框中优惠券中的奖品选中显示边框
    var n = 0, title, prize_id;
    $(document).on("click", ".modal-body .prizeShow", function(){
    	$(".modal-body .prizeShow .attachment-selected").css("display","none");
    	var seleEle = $(this).children(".attachment-selected");
    	seleEle.css({"display":"block"});
    	title = $(this).children("title").text();
    	n = 1;
        prize_id = $(this).data("id")
    });
    //点击重选；
    var addPrizeType = 0;//判断重选和添加  添加为0  重选为1
    var addPrizeTypeIndex = 0;
    $(document).on("click", ".getPrize_data .btn-default", function(){
        addPrizeType = 1;
        addPrizeTypeIndex = $(this).parents(".getPrize_data").index();
    	// $(this).parents(".getPrize_data").remove();
    	$(".modal").modal("show");
        if(coupons == 1){//优惠券
            var url="/merchants/marketing/egg/getCouponList";
        }else if(coupons == 2){//会员卡
            var url="/merchants/marketing/score/get";
        }
        var page = 1;
        getModelList(url);
    });
    var coupons = 1;//弹框默认为优惠券数据
    
    $(".addPrize").click(function(){
        if(coupons == 1){//优惠券
            var url="/merchants/marketing/egg/getCouponList";
        }else if(coupons == 2){//会员卡
            var url="/merchants/marketing/score/get";
        }else if(coupons == 3){
        	$(".modal").modal("show");
        }
        var page = 1;
        getModelList(url);
       
    });
    /*
    *@author huoguanghui
    *@method 获取模态框列表信息列表信息
    *@param url {coupons or integral}
    */
    function getModelList(url){
        $.ajax({
            url: url,  
            type: 'get',  
            dataType: 'JSON',  
            cache: false,  
            processData: false,  
            contentType: false,
            success: function(response){
                if( response.status == 1 ){
                    $(".modal").modal("show");
                    var data = response.data;
                    modelListSuccess(data);
                    if(coupons == 1){
                        $('.myModalPage').extendPagination({
                            totalCount: data[0].total,
                            showCount: data[0].last_page,
                            limit: data[0].per_page
                        });
                    }else if(coupons == 2){
                        $('.myModalPage1').extendPagination({
                            totalCount: data[0].total,
                            showCount: data[0].last_page,
                            limit: data[0].per_page
                        });
                    }
                } else {
                    alert("失败")
                }
            } 
        });
    }
    //模态框列表请求成功方法
    function modelListSuccess(data){
        var _html = "";
        $(".prizeShow").remove();//添加数据之前先清空数据
        if(data[0].data.length==0){
            if(coupons == 1){
                $(".modal-body_1 .noPrize").removeClass("hide");
            }else if(coupons == 2){
                $(".modal-body_2 .noPrize").removeClass("hide");
            }
            return;
        }
        console.log(data,'sdsddsa');
        if(coupons == 1){
            for(var i = 0;i < data[0].data.length;i ++){
                
                _html +='<div class="prizeShow rtv" data-id="'+data[0].data[i].id+'">'
                _html +='<img src="'+_host+'hsadmin/images/prize1.png"/>'
                _html +='<title style="box-sizing:border-box;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;">'+data[0].data[i].title+'</title>'
                switch (data[0].data[i].expire_type) {
                    case '1':
                        _html +='<time>领到券当日开始'+data[0].data[i].expire_days+'天内有效</time>';
                        break;
                    case '2':
                        _html +='<time>领到券次日开始'+ data[0].data[i].expire_days + '天内有效</time>';
                        break;
                    default:
                        _html +='<time>'+data[0].data[i].start_at+'至'+data[0].data[i].end_at+'有效</time>';
                }
                _html +='<span class="word_break">'+data[0].data[i].left+'张</span>'
                _html +='<div class="attachment-selected">'
                _html +='<i class="icon-ok icon-white"></i>'
                _html +='</div>'
                _html +='</div>'
            }
            
            $(".modal-body_1 .noPrize").addClass("hide");
            $(".modal-body_1 .prizeList").prepend(_html);
        }else if(coupons == 2){
            var start_at = $("#datetimepicker1").val();
            var end_at = $("#datetimepicker2").val();
            for(var i = 0;i < data[0].data.length;i ++){
                var text = data[0].data[i].left_flag?"剩余":"共";
                _html +='<div class="prizeShow rtv" data-id="'+data[0].data[i].id+'">'
                _html +='<img src="'+_host+'mctsource/images/integral.png" height="115px"/>'
                _html +='<title style="display:inline-block;">'+data[0].data[i].per_score+'</title>'
                _html +='<span>积分</span>'
                _html +='<time>'+start_at+'至'+end_at+'</time>'
                _html +='<span class="word_break">'+text+data[0].data[i].left_score+'积分</span>'
                _html +='<div class="attachment-selected">'
                _html +='<i class="icon-ok icon-white"></i>'
                _html +='</div>'
                _html +='</div>'
            }
            $(".modal-body_2 .noPrize").addClass("hide");
            $(".integralList").prepend(_html);
        }
        
    }
    //模态框刷新  优惠券
    $(".modal-body_1 .btn-success").click(function(){
        var url="/merchants/marketing/egg/getCouponList";
        getModelList(url);
    });
    //模态框刷新  积分
    $(".modal-body_2 .btn-success").click(function(){
        var url="/merchants/marketing/score/get";
        getModelList(url);
    });
    //模态框分页请求
    function modelPage(that,url){
        var page = that.text()//下标切换页码数
        if(!parseInt(page) && that.parent().index() == 0){
            page =  that.parents(".pagination").find(".active").text();
        }else if(!parseInt(page)&& that.parent().index() != 0){
            page =  parseInt(that.parents(".pagination").find(".active").text());
        }else if(that.parents('li').hasClass('disabled')){
            return false;
        }
        $.ajax({
            url: url+page,  
            type: 'get',  
            dataType: 'JSON',  
            cache: false,  
            processData: false,  
            contentType: false,
            success: function(response){
                if( response.status == 1 ){
                    var data = response.data;
                    modelListSuccess(data);
                } else {
                    alert("失败")
                }
            } 
        });
    }
    $('.modal .modal-content').on('click','.myModalPage .pagination li a', function(event) {//优惠券
        var that = $(this);
        var url = '/merchants/marketing/egg/getCouponList?page=';
        modelPage(that,url);
    });
    $('.modal .modal-content').on('click','.myModalPage1 .pagination li a', function(event) {//积分
        var that = $(this);
        var url = '/merchants/marketing/score/get?page=';
        modelPage(that,url);
    });
    //点击选中选项
    $(document).on("click",".labelCheck",function(){
        if($(this).children(".choose").attr("checked")){
            if($(".choose:checked").length == 1){
                tipshow("至少要一个奖励", "warn");
                return false;
            }
            $(this).children(".choose").attr("checked",false);
            $(this).children("img").attr("src",_host+'mctsource/images/gou01.png');
        }else{
            $(this).children(".choose").attr("checked",true);
            $(this).children("img").attr("src",_host+'mctsource/images/gou02.png');
        }
    });
    //点击垃圾箱删除
    $(document).on("click", ".getPrize_data .dustbin", function(){
    	var dustbin_length = $(".getPrize_data .dustbin").length;
    	if (dustbin_length<=1) {
    		tipshow("至少要一个奖励", "warn")
    	}else{
            var _type = $(this).parents(".getPrize_data").find("input[name='prize_type[]']").val();
	    	$(this).parents(".getPrize_data").remove();
            //删除后检测数据
            checkData();
            $(".public_save").attr("disabled",false);
    	}

    })
    
    //获奖概率计算显示
    $(document).on("input", ".prize_probability", function(){
    	var probability = 0;
		$(".prize_probability").each(function(index, ele){
    		probability += parseFloat($(this).val());
    	})
    	$("#noPrize_probability").val(100-probability).change();
    	//未中奖概率不能大于100；
    	if (probability > 100) {
			$(this).val("");
    	}
    	//console.log(1111)
    })
    $('.modal').on('hidden.bs.modal', function (e) { 
       addPrizeType = 0;
    });
    //模态框中优惠卷点击确定的事件  及 动态添加
    $(".modal-footer .sureAdd").click(function(){
        var prize_type = 1;//1优惠券 2积分 3赠品
//      add by 韩瑜 2018-8-13 添加赠品判断
		if(coupons == 3){
			if($('.controls').find('.fir-con').val()!=""){
	        	n = 1
	        }else{
	        	n = 0
	        }
		}
        //end
        if (n==1) {
        	if(coupons == 2){
                prize_type = 2;
            }else if(coupons == 3){
            	prize_type = 3
            }
            //判断奖品是否已选择
            if(prize_type == 1){
                for(var i = 0;i < couponId.length;i ++){
                    if(prize_id == couponId[i]){
                        break;
                    }
                }
                if(i != couponId.length){
                    tipshow("已选择该奖品,请选择其他产品","warn");
                    return false;
                }
            }else if(prize_type == 2){
                
                for(var j = 0;j < integralId.length;j ++){
                    if(prize_id == integralId[j]){
                        break;
                    }
                }
                if(j != integralId.length){
                    tipshow("已选择该奖品,请选择其他产品","warn");
                    return false;
                }
			// add by 韩瑜 2018-8-13 获取赠品信息
            }else if(prize_type == 3){
            	title = $('.controls').find('.fir-con').val()
            	prize_id = ''
            	prize_num = $('.controls').find('.jpin').val() 
            	method = $('.controls').find('.shuoming1').val()
            	prize_img = $('.fir-img').val()
            }
            //end
            if(addPrizeType == 1){
                $(".getPrize_data").eq(addPrizeTypeIndex).find("input[name='prize_title[]']").val(title);
                $(".getPrize_data").eq(addPrizeTypeIndex).find("input[name='prize_id[]']").val(prize_id);
                $(".getPrize_data").eq(addPrizeTypeIndex).find("input[name='prize_type[]']").val(prize_type);
                // add by 韩瑜 2018-8-22
                //修改赠品传值新加字段
                $(".getPrize_data").eq(addPrizeTypeIndex).find("input[name='prize_img[]']").val(prize_img);
                $(".getPrize_data").eq(addPrizeTypeIndex).find("input[name='prize_method[]']").val(method);
                $(".modal-body .prizeShow .attachment-selected").css("display","none");
                $('#myModal').modal('hide');
                //end
                //修改后检测数据
                checkData();
                return false;
            }
    		var _html = '<div class="form-group getPrize_data">'+
						    '<div class="col-sm-2">'+
					      		'<input type="text" name="prize_name[]" class="form-control" placeholder="名称" />'+
						    '</div>'+
						    '<div class="col-sm-4">'+
							    '<div class="input-group">'+
                                    '<input type="text" class="form-control" name="prize_title[]" value="'+title+'" readonly style="background-color:rgba(0,0,0,0.1);">'+
                                    '<input type="hidden" class="form-control" name="prize_id[]" value="'+prize_id+'">'+
                                    '<input type="hidden" class="form-control" name="prize_type[]" value="'+prize_type+'">'+
                                    '<input type="hidden" class="form-control hide" name="prize_method[]" value="'+method+'">'+
                                    '<input type="hidden" class="form-control hide" name="prize_img[]" value="'+prize_img+'">'+
							      	'<span class="input-group-btn">'+
							        	'<button class="btn btn-default" type="button">重选</button>'+
							      	'</span>'+
							    '</div>'+
						    '</div>'+
						    '<div class="col-sm-3">'+
						    	'<div class="input-group">'+
							      	'<input type="number" name="prize_probability[]" class="form-control prize_probability js_input" min="0" max="100" value="">'+
							      	'<div class="input-group-addon">%</div>'+
						    	'</div>'+
						    '</div>'+
						    '<div class="col-sm-2">'+
					      		'<input type="number" name="prize_number[]" min="0" max="5000" class="form-control js_input" value="'+prize_num+'" />'+
						    '</div>'+
						    '<div class="col-sm-1 flex_around">'+
							    '<a href="##"  class="dustbin" style="display: inline-block"><img src="'+_host+'hsadmin/images/cancel.png"/></a>'+
						    '</div>'+
		        		'</div>';
		    $(".prize_data").append(_html);
		    $(".modal-body .prizeShow .attachment-selected").css("display","none");
		    $('#myModal').modal('hide');
		    n = 0;
            //添加后检测数据
            checkData();
            /*新增列表后，新增元素加入到Validator中*/  
            $('#defaultForm').bootstrapValidator('addField', 'prize_name[]', {  
                validators: {  
                    notEmpty: {  
                        message: ' '  
                    }  
                }  
            });
            $('#defaultForm').bootstrapValidator('addField', 'prize_content[]', {  
                validators: {  
                    notEmpty: {  
                        message: ' '  
                    }  
                }  
            });  
            $('#defaultForm').bootstrapValidator('addField', 'prize_number[]', {  
                validators: {  
                    notEmpty: {  
                        message: ' '  
                    }  
                }  
            });  
            $('#defaultForm').bootstrapValidator('addField', 'prize_probability []', {  
                validators: {  
                    notEmpty: {  
                        message: ' '  
                    }  
                }  
            });
    	}else{
    		tipshow("您还没有选择奖品", "warn")
    	}
    });
    //模态框隐藏触发事件（消除选择的边框）
    $('#myModal').on('hidden.bs.modal', function (e) {
	  	$(".modal-body .prizeShow .attachment-selected").css("display","none");	
	  	n = 0;
	})
	
	//弹出框设置
	$('.explain_1').popover({
		container:".explain_1",
		trigger: "hover",
		placement: "bottom",
		content: "可设置该活动需要用户授权登录某些平台才能参加抽奖（目前仅支持微信登录，且至少需要选择一个平台授权登录），兑奖联系方式则是在用户中奖之后需要填写的信息（目前支持填写手机号码，也可以不填）；可限制每个用户参与抽奖活动的次数和中奖次数，设置的次数单位可选择次/每天、次/活动全程。"
	});
	$('.explain_2').popover({
		container:".explain_2",
		trigger: "hover",
		placement: "bottom",
		content: "可自定义设置抽奖活动结束后的跳转页面，选填，若配置为空，点击分享时则直接分享该抽奖活动的链接"
	});
	$('.explain_3').popover({
		container:".explain_3",
		trigger: "hover",
		placement: "right",
		content: "未中奖项无需设置奖品，未中奖概率根据已设置中奖项的概率由系统自动算出"
	});
	// add by 韩瑜 2018-8-13 新增赠品
	//奖品详情
	$('.fir-con').blur(function(){
		if($('.controls').find('.fir-con').val()==""){
			$('.rem8').remove();
			$('.controls').find('.fir-con').css('border-color','red').after('<p class="nul-red rem8">奖品详情未填写</p>');
		}else{
			$('.rem8').remove();
			$('.fir-con').css('border-color','#ccc');
		};
	});
	//上传图片
	$(".js-upload-image").click(function(){		 
		imgCommona(1,index);
		layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
	     /**
	     * 图片选择后的回调函数
	     */
	    selImgCallBack = function(resultSrc){
            if(resultSrc.length>0){
                if(resultSrc[0].imgWidth / resultSrc[0].imgHeight != 1){
                    tipshow('图片尺寸不符合，请重新上传图片','warm');
                    return
                }
                if(resultSrc[0].imgWidth < 200){
                    tipshow('图片尺寸不符合，请重新上传图片','warm');
                    return
                }
	            var resultSrc = resultSrc[0].imgSrc;
			    $("input[name='image_url']").eq(index).val(resultSrc)
			    $('.image-display').eq(index).css('background-image','url("'+_host + resultSrc+'")')
			    $('.image-display').eq(index).css('display','block')
			    $('.addpic').css('display','none')
	        } 
	    }
	})
	
	//清空图片
	$('.js-clear-prize-image').click(function(){
		$("input[name='image_url']").eq(index).val("");
   		$('.image-display').eq(index).css('background-image','url("")');
   		$('.addpic').css('display','block')
   		$('.image-display').eq(index).css('display','none')
	});
	//end
});


//输入内容实时显示
function showText(ele, event, pasteEle){
	$(ele).on(event,function(){
		var _text = $(ele).val();
		$(pasteEle).text(_text);
	});
}
//步骤效果切换
//hideEle 隐藏元素；     showEle 显示元素；
function step_x_show(hideEle, showEle){
	arguments[1]!=".step_2"?
		$(".step li:eq(1)").removeClass("active"):
		$(".step li:eq(1)").addClass("active");
	$(hideEle).addClass("hide");
    $(showEle).removeClass("hide");
}

//营销-砸金蛋-帮助中心设置
(
    function hitEgg(){
        let eggFlag=true;
        $('.hide_help').on('click',function(){
            if(eggFlag){
                $(this).animate({
                    right:'-200px'
                },'normal')
                eggFlag=false
            }else{
                $(this).animate({
                    right:'0px'
                },'normal')
                eggFlag=true
            }
        })
    }
)()
var index = 0;
/**
 奖品图片调用方法
 */
function imgCommona(fileNumLimit,t_index){
	index =t_index;
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