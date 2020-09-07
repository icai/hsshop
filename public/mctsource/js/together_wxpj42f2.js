$(function(){
    /**
     * 自动成团和团抽奖活动开始
     * @author txw
     * @date 2017/11/8 
     */
    //是否开奖选择框 点击事件
    $("#is_open_draw").click(function(){ 
        if(this.checked){
            $(".group-box").removeClass("hide");
            $("#auto_success")[0].checked = false;
        }else{
            $(".group-box").addClass("hide");
        } 
    });
    //自动成团选择框 点击事件
    $("#auto_success").click(function(){
        if(this.checked){
            $("#is_open_draw")[0].checked = false;
            $(".group-box").addClass("hide");
        }
    });
    //服务保障
    if(rule.id){
        console.log(rule)
        if(rule.sevice[0]==1){
            $(".service_by").attr("checked","true");
            $(".service").css('display','block')
        }
        if(rule.sevice[1]==1){
            $(".service_bz").attr("checked","true");
            $(".service").css('display','block')
        }
        if(rule.sevice[2]==1){
            $(".service_th").attr("checked","true");
            $(".service").css('display','block')
        }
    }

    $(".service_by,.service_bz,.service_th").click(function(){
        if($(".service_by").is(":checked") || $(".service_bz").is(":checked") || $(".service_th").is(":checked")){
            $(".service").css('display','block')
        }else{
            $(".service").css('display','none')
        }
    })


   /*end*/
   /**
    * 商品限购
    * @author huoguanghui
    * @created 2017年12月25日10:40:21
    */
    //商品限购点击事件 
    $("#is_limit").click(function(){
        console.log($(this).is(":checked"))
        if($(this).is(":checked")){//选中
            $(".product_limit").removeClass("hide");
        }else{//未选中
            $(".product_limit").addClass("hide");
        }
    })
    $(".limit_num").keypress(function(event){
        var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
        if(!(48<=keyCode && keyCode<=57) && keyCode != 8){
            console.log(111)
            event.preventDefault();//阻止浏览器默认时间
        }
    })
    /*end*/


    $("input[name='draw_type']").click(function(){
        if(this.value=="0"){
            $("#draw_phones").attr("disabled","true");
        }else{
            $("#draw_phones").removeAttr("disabled");
        }
    });

    $("#draw_pnum").on('input',function(){  
        if(this.value==0){
            $("input[name='draw_type']").attr("disabled","true");
            $("#draw_phones").attr("disabled","true");
        }else if(this.value<0){
            this.value = 0;
        }else{
            $("input[name='draw_type']").removeAttr("disabled");
            var draw_type = $("input[name='draw_type']:checked").val(); //0随机 1.指定 
            if(draw_type==1)
                $("#draw_phones").removeAttr("disabled");
        }
    });
    $("#draw_pnum").trigger('input');
    /**---------自动成团和团抽奖活动结束----------**/

    $(".js-add-picture").click(function() {

    });
    $("#expire_day").on("input",function(){
        if(this.value<0){
            this.value =0;
        }
    });
    $("#expire_hours").on("input",function(){
        if(this.value<0){
            this.value =0;
        }
    });

    /**---------自动成团和团抽奖活动结束----------**/
    $(".js-add-picture").click(function(){
        layer.open({
            type: 2,
            title: false,
            closeBtn: false,
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        });
    });
    /**
     * 图片选择后的回调函数
     */
    selImgCallBack = function(resultSrc) {
        console.log(resultSrc);
        // var imgWH = resultSrc[0].img_size.split("x")
        // console.log(imgWH);
        if (resultSrc.length > 0) {
            var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
            var sum = parseInt(750 / 750 * 100) / 100
            if( num < sum - 0.2 || num > sum + 0.2){
                tipshow("图片比例非1:1，请重新上传","warn");
                return false
            }
            if(parseInt(resultSrc[0].imgWidth) < 400){
                tipshow("图片尺寸小于400px，请重新上传","warn");
                return false
            }
        	$(".js-add-picture").children('span').hide();
            $("input[name='share_img']").val(resultSrc[0].imgSrc);
            $(".share_img").attr("src", _host + resultSrc[0].imgSrc).parent('.share_img_box').removeClass('hide');
            $("#shara_img").attr('src',_host + resultSrc[0].imgSrc).removeClass('hide')
        }
    }
    /*删除图片*/
    $(".del_share").click(function(e) {
        e.stopPropagation();
    	$(".js-add-picture").children('span').show()
        $("input[name='share_img']").val("");
        $(".share_img").attr("src", "").parent().addClass('hide');
        $("#shara_img").attr('src','').addClass('hide')
    });

    // 活动图
    var index = 1; //记录点击加图1，加图2
    $(".js-activity-img").click(function() {
        index = $(this).data('index');
        layer.open({
            type: 2,
            title: false,
            closeBtn: false,
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1?callback=activityCallback'
        });
    });

    activityCallback = function(resultSrc) {
        console.log(resultSrc)
        if (resultSrc.length > 0) {         
            var imgWH = resultSrc[0].img_size.split("x")
            console.log(imgWH);
            if (index == 1) {
                var num = parseInt(imgWH[0] / imgWH[1] * 100) / 100
                var sum = parseInt(750 / 400 * 100) / 100
                if( num < sum - 0.2 || num > sum + 0.2){
                    tipshow("图片比例非2:1，请重新上传","warn");
                    return false
                }
                if(parseInt(imgWH[1]) > 400){
                    tipshow("图片高度大于400px，请重新上传。","warn");
                    return false
                }
            	$(".js-activity-img[data-index='1']").children('span').hide()
                $("input[name='activity_img']").val(resultSrc[0].path);
                $(".activity_img").attr("src", _host + resultSrc[0].path).parent('.share_img_box').removeClass('hide');
                $("#pintuan_img").attr("src", _host + resultSrc[0].path).removeClass('hide');
            } else if (index == 2) {
                var num = parseInt(imgWH[0] / imgWH[1] * 100) / 100
                var sum = parseInt(750 / 750 * 100) / 100
                if( num < sum - 0.2 || num > sum + 0.2){
                    tipshow("图片比例非1:1，请重新上传","warn");
                    return false
                }
                if(parseInt(imgWH[0]) < 400){
                    tipshow("图片尺寸小于400px，请重新上传","warn");
                    return false
                }
            	$(".js-activity-img[data-index='2']").children('span').hide()
                $("input[name='img2']").val(resultSrc[0].path);
                $(".activity_img2").attr("src", _host + resultSrc[0].path).parent('.share_img_box').removeClass('hide');

            }
        }
    }
    /*删除图片*/
    $(".del_activity").click(function(e) {
        e.stopPropagation();
        $(this).parents('.js-activity-img').prev().val("");
        $(this).prev().attr("src", "").parent('.share_img_box').addClass('hide');
        $(this).prev().parent().prev('span').show()
        if(index == 1){
            $("#pintuan_img").attr('src','').addClass('hide')
        }
    });

    var spec_json = []; //sku数组 生成表格后获取数据
    if (tag == 1) { //编辑页面
        editLoad();
    }
    //编辑时，也没数据插入
    function editLoad() {
        console.log(rule);
        $(".sel-goods").attr("href", rule.product.url);
        $(".sel-goods").attr("target", "_blank");
        $("#goods_id").val(rule.product.id);
        $(".sel-goods").html('<img class="img-goods" src="/' + rule.product.img + '" /><span class="remove-img">×</span>');
        $("input[name='activity_img']").val(rule.img);
        $(".activity_img").attr("src", _host + rule.img);
        $("#pintuan_img").attr("src", _host + rule.img).removeClass('hide');
        $(".js-activity-img[data-index='1']").children('span').hide()
        $("input[name='img2']").val(rule.img2);
        $(".activity_img2").attr("src", _host + rule.img2);
        $(".js-activity-img[data-index='2']").children('span').hide()
        $("#title").val(rule.title);
        $("#start_time").val(rule.start_time);
        $("#end_time").val(rule.end_time);
        $('#subtitle').val(rule.subtitle);
        $('#label').val(rule.label);
        /*----自动成团开始----*/
        $("input[name='auto_success']")[0].checked = rule.auto_success=="1"?true:false;
        var hours = rule.expire_hours?parseFloat(rule.expire_hours) : 0;
        var day = parseInt(hours/24);
        hours = hours%24;
        $("#expire_hours").val(hours);
        $("#expire_day").val(day);
        /*----自动成团结束----*/
        $('#share_title').val(rule.share_title);
        $('#share_desc').val(rule.share_desc);
        $('input[name="share_img"]').val(rule.share_img);
        $('.share_img_box').removeClass('hide')
        console.log(rule.share_img);
        if (rule.share_img != ""){
            $('.share_img').attr('src', _host + rule.share_img);;
            $('#rule_img').removeClass('hide');
            $("#shara_img").attr('src',_host + rule.share_img).removeClass('hide');
            $(".js-add-picture").children('span').hide();
            console.log($(".js-add-picture").children('span'));
        }else{
            $('#rule_img').addClass('hide');
        }
        //参团人数 
        $("#join_num").val(rule.groups_num);

        /*团开奖活动开始*/
        var is_open_draw = rule.is_open_draw;   
        if(is_open_draw==1){ 
            $("#is_open_draw")[0].checked = true;
            $(".group-box").removeClass("hide");  
        } 
        $("#draw_pnum").val(rule.draw_pnum);

        $(":radio[name='draw_type'][value='" + rule.draw_type + "']").prop("checked", "checked"); 
        $("input[name='group_type'][value='" + rule.group_type + "']").prop("checked", "checked"); 
        $("#draw_phones").val(rule.draw_phones);
        if(rule.draw_pnum == 0){
            $("input[name='draw_type']").attr("disabled","true");
            $("#draw_phones").attr("disabled","true");
        }else{
            $("input[name='draw_type']").removeAttr("disabled");
            if(rule.draw_type==1){
                $("#draw_phones").removeAttr("disabled");
            } 
        }   
        /*团开奖活动结束*/
        // 团限购赋值
        if (rule.num > 0) {
            $("#is_limit").attr("checked", true);
            $(".product_limit").removeClass("hide");
            $("input[name='limit_type'][value='" + rule.limit_type + "']").prop("checked", "checked"); 
            $("input[name='limit_type']:checked").siblings(".limit_num").val(rule.num);
        } else {
            $("#is_limit").attr("checked", false);
        }
        // $("#limit_num").val(rule.num);
        if (rule.is_open == "1") {
            $(".js-join-group-switch").attr("checked", true);
        } else {
            $(".js-join-group-switch").attr("checked", false);
        }
        if (rule.head_discount == "1") {
            $(".js-chief-discount-switch").attr("checked", true);
        } else {
            $(".js-chief-discount-switch").attr("checked", false);
        }
        //规格表格设置
        var tobj = buildTable(rule.skus);
        $("#div_spec table").html(tobj.thead + tobj.tbody + tobj.tfoot);
        $("#div_spec").show();
        var spec = tobj.spec;
        mergeTd(spec);
        setTableInputInfo(rule.skus);
        setFormState(rule.state);
    }
    //插入拼团价和优惠价 有数据时调用 编辑查看
    function setTableInputInfo(data) {
        for (var i = 0; i < data.length; i++) {
            var head_price = data[i].g_head_price ? data[i].g_head_price : data[i].head_price;
            var price = data[i].g_price ? data[i].g_price : data[i].price;
            $('.js-tzyh-price').eq(i).val(head_price);
            $('.js-group-price').eq(i).val(price);
        }
    }

    //活动的各种姿态下的限制
    function setFormState(state) {
        switch (state) {
            case -1: //活动失效
                $(".sel-goods").addClass("disabled");
                $("#title").attr("disabled", "disabled");
                $("#start_time").attr("disabled", "disabled");
                $("#end_time").attr("disabled", "disabled");
                $("#join_num").attr("disabled", "disabled");
                // $("#is_limit").attr("disabled", "disabled");
                // $("#limit_num").attr("disabled", "disabled");
                $(".js-chief-discount-switch").attr("disabled", "disabled");
                $(".js-join-group-switch").attr("disabled", "disabled");
                $(".js-group-price").attr("disabled", "disabled");
                $(".js-tzyh-price").attr("disabled", "disabled");
                $(".js-a-group-price").hide();
                $(".js-a-tzyh-price").hide();
                $(".js-btn-save").attr("disabled", "disabled");
                break;
            case 1: //活动正在进行中
                $(".sel-goods").addClass("disabled");
                $("#start_time").attr("disabled", "disabled");
                // $("#join_num").attr("disabled", "disabled");
                // $("#is_limit").attr("disabled", "disabled");
                // $("#limit_num").attr("disabled", "disabled");
                // $(".js-chief-discount-switch").attr("disabled", "disabled");
                // $(".js-group-price").attr("disabled", "disabled");
                // $(".js-tzyh-price").attr("disabled", "disabled");
                // $(".js-a-group-price").hide();
                $(".js-a-tzyh-price").hide();
                break;
            case 2: //活动未开始
                $(".sel-goods").addClass("disabled");
                break;
            case 3: //已过期
                $(".sel-goods").addClass("disabled");
                $("#title").attr("disabled", "disabled");
                $("#start_time").attr("disabled", "disabled");
                $("#end_time").attr("disabled", "disabled");
                $("#join_num").attr("disabled", "disabled");
                $("#is_limit").attr("disabled", "disabled");
                $("#limit_num").attr("disabled", "disabled");
                $(".js-chief-discount-switch").attr("disabled", "disabled");
                $(".js-join-group-switch").attr("disabled", "disabled");
                $(".js-group-price").attr("disabled", "disabled");
                $(".js-tzyh-price").attr("disabled", "disabled");
                $(".js-a-group-price").hide();
                $(".js-a-tzyh-price").hide();
                $(".js-btn-save").attr("disabled", "disabled");
                break;
        }
    }

    var start = {
        elem: '#start_time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas) {
            // console.log(datas);
            $('#start_time').val(datas);
            $('#start_time').focus();
            $('#start_time').blur();
            // $('.edit_form').data("bootstrapValidator").validate('start_at');
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var t_min_time = rule.start_time || laydate.now();
    var end = {
        elem: '#end_time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: t_min_time,
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas) {
            // console.log($('#endTime').val())
            $('#end_time').val(datas);
            $('#end_time').focus();
            $('#end_time').blur();
            // $('.edit_form').data("bootstrapValidator").validateField('end_at');
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);


    //点击保存事件
    var distribute_template_id;//分销模板id
    $(".js-btn-save").click(function(){ 
        var is_validate = false;
        var is_open = $(".js-join-group-switch").is(":checked");
        var head_discount = $(".js-chief-discount-switch").is(":checked");
        var share_title = $('#share_title').val();
        var share_desc = $('#share_desc').val();
        var share_img = $('input[name="share_img"]').val();
        var service_txt    = $('#service_txt1').val();
        var service_by = $(".service_by")[0].checked?1:0;
        var service_bz = $(".service_bz")[0].checked?1:0;
        var service_th = $(".service_th")[0].checked?1:0;
        if (!head_discount) {
            setTzyhVal();
        }
        $(".validate").each(function(index, el) {
            var val = $(el).val();
            if (val == "") {
                $(el).parent().addClass("control-error");
                is_validate = true;
            } else {
                $(el).parent().removeClass("control-error");
            }
        });
        if (is_validate) { //是否通过验证 
            return false;
        } 

        $(this).attr("disabled", "disabled");
        var _this = this;
        var data ={};
        // 自动成团开始
        var hours = $("#expire_hours").val() || 0;
        var day = $("#expire_day").val() || 0;
        if(day>0){
            hours = parseInt(hours)+parseInt(day)*24;
        }
        var auto_success = $("input[name='auto_success']")[0].checked?1:0;
        data.expire_hours = hours;
        data.auto_success = auto_success;
        // 自动成团结束
        data.pid = $("#goods_id").val();
        data.title = $("#title").val();
        data.start_time = $("#start_time").val();
        data.end_time = $("#end_time").val();
        // 限购
        var is_limit = $("#is_limit").is(":checked");
        if (is_limit) {
            data.limit_type = $("input[name='limit_type']:checked").val();
            data.num = $("input[name='limit_type']:checked").siblings(".limit_num").val();
            if(!data.num){
                tipshow("请填写限购人数", "warn");
                $(this).removeAttr("disabled");
                return false;
            }
        } 
        data.groups_num = $("#join_num").val();
        data.img = $("input[name='activity_img']").val();
        data.img2 = $("input[name='img2']").val();
        data.subtitle = $('#subtitle').val();
        data.label = $('#label').val();
        /* 团抽奖活动验证开始 */
        var is_open_draw = $("#is_open_draw")[0].checked?1:0; //1开启,0关闭
        var draw_pnum = $("#draw_pnum").val() || 0;
        draw_pnum = parseInt(draw_pnum);
        var draw_type = $("input[name='draw_type']:checked").val(); //0随机 1.指定
        var draw_phones = $("#draw_phones").val();
        var group_type= $("input[name='group_type']:checked").val();//拼团类型
        //手机号必须小于中奖人数
        if(draw_type==1){
            if(draw_phones == ''){
                tipshow("至少输入一个指定的手机号", "warn");
                $(this).removeAttr("disabled");
                return false;
            }
            var p_nums = draw_phones.split(',').length;
            if(p_nums>draw_pnum){
                $(this).removeAttr("disabled");
                tipshow("手机号必须小于中奖人数", "warn");
                return false;
            }
        }
        data.is_open_draw = is_open_draw;
        data.draw_pnum = draw_pnum;
        data.draw_type = draw_type;
        data.draw_phones = draw_phones;
        data.group_type = group_type;
        /* 团抽奖活动验证结束 */
        /*验证分享内容*/
        if (!((share_title && share_desc && share_img) || (!share_title && !share_desc && !share_img))) { //都有内容或者都没内容通过
            if(!share_img && share_title && share_desc){
                tipshow("请填写分享图片","warn");
                $(this).removeAttr("disabled");
                return false;
            }
            if(!share_title && share_img && share_desc){
                tipshow("请填写分享标题","warn");
                $(this).removeAttr("disabled");
                return false;
            }
            if(!share_desc && share_title && share_img){
                tipshow("请填写分享内容","warn");
                $(this).removeAttr("disabled");
                return false;
            }
            if(share_img){
                tipshow("请填写分享标题及内容","warn");
                $(this).removeAttr("disabled");
                return false;
            }
            if(share_title){
                tipshow("请填写分享内容及图片","warn");
                $(this).removeAttr("disabled");
                return false;
            }
            if(share_desc){
                tipshow("请填写分享标题及图片","warn");
                $(this).removeAttr("disabled");
                return false;
            }
            // tipshow("请填写分享内容", "warn");
            // $(this).removeAttr("disabled");
            // return false;
        }

        data.is_open = is_open ? 1 : 0;
        data.head_discount = head_discount ? 1 : 0;
        var skus = [];
        for (var i = 0; i < spec_json.length; i++) { //多规格商品
            var obj = {};
            obj.id = spec_json[i].id ? spec_json[i].id : 0;
            obj.price = spec_json[i].g_price ? spec_json[i].g_price : spec_json[i].price;
            obj.head_price = spec_json[i].g_head_price ? spec_json[i].g_head_price : spec_json[i].head_price;
            skus.push(obj);
        }
        data.skus = skus;
        var id = $("#group_id").val();
        if (id) {
            data.id = id;
        }
        data.share_title = share_title;
        data.share_desc = share_desc;
        data.share_img = share_img;
        data.service_txt  = service_txt;
        data.service_by  = service_by;
        data.service_bz  = service_bz;
        data.service_th  = service_th;        
        if($('.show_dis input:checked').val()==0){
        	data.distribute_template_id = 0
        }else{
        	data.distribute_template_id = distribute_template_id;//分销模板id 
        }
        $.ajax({
            url: "/merchants/grouppurchase/editRule",
            data: data,
            type: "post",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.status == 1) {
                    tipshow(data.info);
                    setTimeout(function() {
                        location.href = "/merchants/marketing/togetherGroupList";
                    }, 1000);
                } else {
                    $(_this).removeAttr("disabled");
                    tipshow(data.info, "warn");
                }
            },
            error: function() {
                $(_this).removeAttr("disabled");
                tipshow('异常', "warn");
            }
        });

    });
    /*
     * 批量设置团长优惠文本框内容
     * 说明：如果未勾选团长优惠选项可执行该方法
     */
    function setTzyhVal() {
        $('.js-group-price').each(function(index, el) {
            $('.js-tzyh-price').eq(index).val($(this).val());
            spec_json[index].g_head_price = $(this).val();
        });
    }




    /*----选择商品新开始-----*/
    // 选择商品点击事件
    $("body").on("click", ".sel-goods", function(e) {
        if ($(this).find(".icon-add").length > 0) {
            e.preventDefault();
            var href = "/merchants/product/create";
            selGoods.open({ success: callback, href: href,postData:{filter_negotiable:1,filter_hexiao:1,filter_cam:1}});
        }
    });
    //删除商品点击事件
    $("body").on("click", ".remove-img", function(e) {
        e.stopPropagation();
        $(this).parent().attr("href", "javascript:;");
        $(this).parent().removeAttr("target");
        $("#goods_id").val("");
        $(this).parent().html('+<i class="icon-add hide"></i>');
        delGoods();
    });

    //是否开启团长优惠点击事件
    $(".js-chief-discount-switch").click(function() {
        var colspan = $(".spec-table tfoot tr td").eq(0).attr("colspan");
        if (this.checked) {
            colspan = colspan ? parseInt(colspan) + 1 : 0;
            $(".spec-table tfoot tr td").eq(0).attr("colspan", colspan);
            $(".td-tzyh-price").show();
            $(".js-a-tzyh-price").show();
        } else {
            colspan = colspan ? parseInt(colspan) - 1 : 0;
            $(".spec-table tfoot tr td").eq(0).attr("colspan", colspan);
            $(".td-tzyh-price").hide();
            $(".js-a-tzyh-price").hide();
        }
    });
    //批量设置拼团价按钮点击事件
    $("body").on("click", ".js-a-group-price", function() {
        $(this).hide().siblings('span').eq(0).show();
    });
    //批量设置团长优惠价按钮点击事件
    $("body").on("click", ".js-a-tzyh-price", function() {
        $(this).hide().siblings('span').eq(1).show();
    });

    //点击取消批量设置事件
    $("body").on("click", ".js-a-cancel", function() {
        $(this).parent().hide();
        $(this).parent().prev().show();
    });
    //点击团长优惠价保存按钮事件
    $("body").on("click", ".js-save-tzyh", function() {
        var val = $(this).siblings('input').val();
        $(".js-tzyh-price").val(val);
        for (var i = 0; i < spec_json.length; i++) {
            spec_json[i].g_head_price = val;
        }
    });
    //点击团长价保存按钮事件
    $("body").on("click", ".js-save-group", function() {
        var val = $(this).siblings('input').val();
        $(".js-group-price").val(val);
        for (var i = 0; i < spec_json.length; i++) {
            spec_json[i].g_price = val;
        }
    });



    //删除商品
    function delGoods() {
        $("#div_spec").hide();
        $("#div_spec table").html("");
        spec_json = [];
    }

    function callback(json) {
        var _json = json;
        $(".sel-goods").attr("href", json[0].url);
        $(".sel-goods").attr("target", "_blank");
        $("#title").val(json[0].title);
        $("#goods_id").val(json[0].id);
        $(".sel-goods").html('<img class="img-goods" src="/' + json[0].img + '" /><span class="remove-img">×</span>');
        $.ajax({
            type: "get",
            url: "/merchants/grouppurchase/getProps/" + json[0].id,
            dataType: "json",
            success: function(data) {
                if (data.status == 1) {
                    var json = data.data;
                    var tobj = buildTable(json, _json[0].price, _json[0].stock);
                    $("#div_spec table").html(tobj.thead + tobj.tbody + tobj.tfoot);
                    $("#div_spec").show();
                    var spec = tobj.spec;
                    mergeTd(spec);
                }
            }
        });
    }



    /*
     * 生成表格
     */
    function buildTable(json, t_price, t_stock) {
        var resultObj = {},
            colspan = 3,
            hstr = '',
            tstr = '',
            fstr = '';
        var head_discount = $(".js-chief-discount-switch").is(":checked"); //是否开启团长优惠
        var spec = ["", "", ""];
        if (head_discount)
            colspan++;
        if (json.length > 0) {
            if (json[0].k1)
                spec[0] = json[0].k1;
            if (json[0].k2)
                spec[1] = json[0].k2;
            if (json[0].k3)
                spec[2] = json[0].k3;
            hstr = '<tr>';
            if (spec[0]) {
                hstr += '<td>' + spec[0] + '</td>';
                colspan++;
            }
            if (spec[1]) {
                hstr += '<td>' + spec[1] + '</td>';
                colspan++;
            }
            if (spec[2]) {
                hstr += '<td>' + spec[2] + '</td>';
                colspan++;
            }
            hstr += '<td>微商城原价(元)</td><td>拼团价(元)</td>';
            if (head_discount)
                hstr += '<td class="td-tzyh-price">团长优惠价(元)</td>';
            else
                hstr += '<td class="td-tzyh-price none">团长优惠价(元)</td>';
            hstr += '<td>库存</td></tr>';
            for (var i = 0; i < json.length; i++) {
                tstr += '<tr>';
                if (spec[0]) {
                    tstr += '<td class="spec1">' + json[i].v1 + '</td>';
                }
                if (spec[1]) {
                    tstr += '<td class="spec2">' + json[i].v2 + '</td>';
                }
                if (spec[2]) {
                    tstr += '<td class="spec3">' + json[i].v3 + '</td>';
                }
                tstr += '<td>' + json[i].price + '</td>';
                tstr += '<td><input type="text" data-index="' + i + '" class="form-control js-group-price validate w100" /></td>';
                if (head_discount)
                    tstr += '<td class="td-tzyh-price"><input type="text" data-index="' + i + '" class="form-control js-tzyh-price validate w100" /></td>';
                else
                    tstr += '<td class="td-tzyh-price none"><input type="text" data-index="' + i + '" class="form-control js-tzyh-price validate w100" /></td>';
                tstr += '<td>' + json[i].stock_num + '</td></tr>';
            }
            resultObj.spec = spec;
        } else {
            hstr = '<tr><td>微商城原价(元)</td><td>拼团价(元)</td>';
            if (head_discount)
                hstr += '<td class="td-tzyh-price">团长优惠价(元)</td>';
            else
                hstr += '<td class="td-tzyh-price none">团长优惠价(元)</td>';
            hstr += '<td>库存</td></tr>';
            tstr = '<td>' + t_price + '</td>'; //微商城原价(元)
            tstr += '<td><input type="text" data-index="0" class="form-control js-group-price validate w100"></td>';
            if (head_discount)
                tstr += '<td class="td-tzyh-price">';
            else
                tstr += '<td class="td-tzyh-price none">';
            tstr += '<input type="text" data-index="0" class="form-control js-tzyh-price validate w100"></td>';
            tstr += '<td>' + t_stock + '</td>'; //库存
            tstr += '</tr>';
            resultObj.spec = spec;
        }
        fstr = '<tr><td colspan="' + colspan + '">批量设置：';
        fstr += '<a href="javascript:;" class="js-a-group-price">拼团价</a>';
        fstr += '<span class="none"><input type="text"  class="form-control input-sm w100" placeholder="输入价格" />';
        fstr += '<a href="javascript:;" class="ml10 js-save-group">保存</button>';
        fstr += '<a href="javascript:;" class="ml10 js-a-cancel">取消</button></span>';
        if (head_discount) {
            fstr += '<a href="javascript:;" class="ml10 js-a-tzyh-price">团长优惠价</a>';
        } else {
            fstr += '<a href="javascript:;" class="ml10 js-a-tzyh-price none">团长优惠价</a>';
        }
        fstr += '<span class="none"><input type="text" class="ml10 form-control input-sm w100" placeholder="输入价格" />';
        fstr += '<a href="javascript:;" class="ml10 js-save-tzyh">保存</button>';
        fstr += '<a href="javascript:;" class="ml10 js-a-cancel">取消</button></span>';
        fstr += '</td></tr>';
        resultObj.thead = "<thead>" + hstr + "</thead>";
        resultObj.tbody = "<tbody>" + tstr + "</tbody>";
        resultObj.tfoot = "<tfoot>" + fstr + "</tfoot>";
        if (json.length == 0)
            spec_json[0] = {};
        else
            spec_json = json;
        return resultObj;

    }

    //拼团价文本框内容发生变化，规格数据发生对应的变化
    $("body").on("change", ".js-group-price", function() {
        var index = $(this).attr("data-index");
        if (spec_json.length == 0) {
            spec_json[index] = {};
        }
        spec_json[index].g_price = $(this).val();
    });
    //团长优惠价文本框内容发生变化，规格数据发生对应的变化
    $("body").on("change", ".js-tzyh-price", function() {
        var index = $(this).attr("data-index");
        if (spec_json.length == 0) {
            spec_json[index] = {};
        }
        spec_json[index].g_head_price = $(this).val();
    });

    //合并单元格
    function mergeTd(spec) {
        var temp = "",
            temp2 = "";
        var arr = [],
            obj = {},
            rowspan1 = 1,
            rowspan2 = 1;
        var count = $("#div_spec table tbody tr").length - 1;
        var tindex1 = 0,
            tindex2 = 0;
        if (spec[0] != "") {
            $("#div_spec table tbody tr").each(function(index, el) {
                var td1 = $(el).find("td").eq(0);
                var td2 = $(el).find("td").eq(1);
                if (td1.html() == temp) {
                    if (td2.html() == temp2 && spec[1] != "") {
                        td2.remove();
                        rowspan2++;
                    } else {
                        if (rowspan2 > 1) {
                            $("#div_spec table tbody tr").eq(tindex2).find(".spec2").attr("rowspan", rowspan2);
                            rowspan2 = 1;
                        }
                        tindex2 = index;
                    }

                    td1.remove();
                    rowspan1++;
                } else {
                    if (rowspan1 > 1) {
                        $("#div_spec table tbody tr").eq(tindex1).find(".spec1").attr("rowspan", rowspan1);
                        rowspan1 = 1;
                    }
                    if (rowspan2 > 1) {
                        $("#div_spec table tbody tr").eq(tindex2).find(".spec2").attr("rowspan", rowspan2);
                        rowspan2 = 1;
                    }
                    tindex2 = index;
                    tindex1 = index;
                }
                temp = td1.html();
                temp2 = td2.html();
                if (index == count) {
                    if (rowspan1 > 1) {
                        $("#div_spec table tbody tr").eq(tindex1).find(".spec1").attr("rowspan", rowspan1);
                        rowspan1 = 1;
                    }
                    if (rowspan2 > 1) {
                        $("#div_spec table tbody tr").eq(tindex2).find(".spec2").attr("rowspan", rowspan2);
                        rowspan2 = 1;
                    }
                }
            });
        }
    }
    /*服务保障*/
    var ue = UE.getEditor("service_txt",{
        toolbars: [
            [
                'undo', //撤销
                'redo', //重做
                'bold', //加粗
                'indent', //首行缩进
                'snapscreen', //截图
                'italic', //斜体
                'underline', //下划线
                'strikethrough', //删除线
                'subscript', //下标
                'fontborder', //字符边框
                'superscript', //上标
                'formatmatch', //格式刷
                'source', //源代码
                'pasteplain', //纯文本粘贴模式
                'selectall', //全选
                'print', //打印
                'preview', //预览
                'horizontal', //分隔线
                'removeformat', //清除格式
                'time', //时间
                'date', //日期
                'mergeright', //右合并单元格
                'mergedown', //下合并单元格
                'deleterow', //删除行
                'deletecol', //删除列
                'splittorows', //拆分成行
                'splittocols', //拆分成列
                'splittocells', //完全拆分单元格
                'deletecaption', //删除表格标题
                'mergecells', //合并多个单元格
                'deletetable', //删除表格
                'insertparagraphbeforetable', //"表格前插入行"
                'fontfamily', //字体
                'fontsize', //字号
                'paragraph', //段落格式
                'edittable', //表格属性
                'edittd', //单元格属性
                'link', //超链接
                'emotion', //表情
                'spechars', //特殊字符
                'searchreplace', //查询替
                'justifyleft', //居左对齐
                'justifyright', //居右对齐
                'justifycenter', //居中对齐
                'justifyjustify', //两端对齐
                'forecolor', //字体颜色
                'backcolor', //背景色
                'rowspacingtop', //段前距
                'rowspacingbottom', //段后距
                'imagenone', //默认
                'imagecenter', //居中
                'lineheight', //行间距
                'edittip ', //编辑提示
                'customstyle', //自定义标题
                'autotypeset', //自动排版
                'background', //背景
                'inserttable', //插入表格
                'drafts', // 从草稿箱加载
                'insertimage',
                'fullscreen'
            ]
        ],
        initialFrameHeight:180,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    });
    //富文本ue change事件
    ue.addListener("ready", function () {

        var content = service_txt ? service_txt : "全场包邮<br/>支持全国绝大部分地区包邮(偏远地区除外,如新疆,西藏,内蒙古,宁夏,青海,甘肃等)<br/><br/>品质保证<br/>所售商品,保证品质<br/><br/>七天无忧退换<br/>买家收到商品后7天内,符合消费者保障法规,可以申请无理由退换货(特殊商品除外,如直接接触皮肤商品,食品类商品,定做类商品,明示不支持物流退换货等)";
        ue.setContent(content);
        ue.addListener( "selectionchange", function () {
            var _html = ue.getContent();
            if(!ue.getContent()){
                _html = "";
            }
            $("#service_txt").val(_html);
            $("#service_txt1").val(_html);
        });
    });
     //分销设置显示
    if(distribute.is_distribute == 1){
        if(rule.distribute_template_id > 0){
            $('.f_level').text(distribute.info.title).attr("data-id",distribute.info.id);//分销模板
            $('.is_distribute_price').html(distribute.info.price);//商品价格
            $('.is_distribute_cost').html(distribute.info.cost);//分销成本
            $('.is_distribute_zero').html(distribute.info.zero);//本级佣金
            $('.is_distribute_one').html(distribute.info.one);//一级佣金
            $('.is_distribute_sec').html(distribute.info.sec);//二级佣金
            $('.is_distribute_three').html(distribute.info.three);//三级佣金
            $('input[name="is_distribute"][value="'+1+'"]').attr('checked',true);//选中按钮
        }else{
            $('.show_fenxiao').hide();
            $('input[name="is_distribute"][value="'+0+'"]').attr('checked',true);//选中按钮
        }
        // $('input[name="is_distribute"][value="'+distribute.is_distribute+'"]').attr('checked',true);//选中按钮
        distribute_template_id = distribute.info.id;//分销模板id
    }else{      
        $('.group-inner').hide();
        var a =  $('.show_dis input:checked').val();
    }
    $('.show_dis label').click(function(){
        if($('.show_dis input:checked').val()==0){
            $('.show_fenxiao').hide();
            distribute_template_id = 0;
        }else{
            $('.show_fenxiao').show();
            distribute_template_id = $(".f_level").data('id');
            if(distribute_template_id == ''){
                $('.f_level').text(distribute.info.title).attr("data-id",distribute.info.id);//分销模板
                $('.is_distribute_price').html(distribute.info.price);//商品价格
                $('.is_distribute_cost').html(distribute.info.cost);//分销成本
                $('.is_distribute_zero').html(distribute.info.zero);//本级佣金
                $('.is_distribute_one').html(distribute.info.one);//一级佣金
                $('.is_distribute_sec').html(distribute.info.sec);//二级佣金
                $('.is_distribute_three').html(distribute.info.three);//三级佣金
                distribute_template_id = distribute.info.id;//分销模板id
            }
        }
    });
    
    //分销设置弹窗
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
    setDefaultDistribute = function(data){
        console.log(data);
        layer.closeAll();
        $(".f_level").text(data.title).attr("data-id",data.id);
        $('.is_distribute_price').html(data.price);//商品价格
        $('.is_distribute_cost').html(data.cost);//分销成本
        $('.is_distribute_zero').html(data.zero);//本级佣金
        $('.is_distribute_one').html(data.one);//一级佣金
        $('.is_distribute_sec').html(data.sec);//二级佣金
        $('.is_distribute_three').html(data.three);//三级佣金
        distribute_template_id =data.id;
        // $.ajax({
        //     url:url,
        //     data:data,
        //     type:"post",
        //     dataType:"json",
        //     headers: {
        //         'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
        //     },
        //     success:function(json){
        //         console.log(data)
        //         layer.closeAll(); 
        //         //保存成功后 移除新增栏目 插入新的ul 
        //         if(json.status==1){
        //             tipshow(json.info); 
        //             //修改名称 
        //             $(".f_level").text(data.title).attr("data-id",data.id); 
        //             distribute_template_id =data.id;
        //         }else{
        //            tipshow(json.info,"wram"); 
        //         }
        //     },
        //     error:function(){
        //         layer.closeAll(); 
        //         tipshow("异常","wram");
        //     }
        // }); 
         
    }
})
