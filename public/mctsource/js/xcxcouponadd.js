$(function () {
    $('#addCouponForm').bootstrapValidator({
        message: '不能为空', // 设置默认提示语
        trigger: 'blur', // 设置验证默认触发事件(失焦时验证)
        // excluded:[],//只对禁用域不进行验证         
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: { // 验证
            title: { // 活动名称
                validators: {
                    notEmpty: {
                        message: '名称不能为空',
                    },
                    stringLength: {
                        max: 10,
                    },
                    stringLength: {
                        min: 1,
                    },
                }
            },
            total: { // 发行量
                validators: {
                    notEmpty: {
                        message: '发行量不能为空',
                    },
                    integer: {
                        'default': '请输入有效的整数值'
                    },
                    greaterThan: {
                        value: 1
                    },
                }
            },
            amount: { // 面值下限
                validators: {
                    notEmpty: {
                        message: '面值不能为空',
                    },
                    numeric: {
                        'default': '请输入有效的面值，可以是小数'
                    },
                    greaterThan: {
                        value: 0.01
                    },
                    lessThan: {
                        value: 10000
                    },
                }
            },
            value_random_to: { // 面值上限
                validators: {
                    notEmpty: {
                        message: '面值不能为空',
                    },
                    numeric: {
                        'default': '优惠券面值范围的上限必须大于下限'
                    },
                    greaterThan: {
                        value: 'values',
                        message: '优惠券面值范围的上限必须大于下限',
                    },
                    lessThan: {
                        value: 10000
                    },
                }
            },
            limit_amount: { //使用门槛
                enabled: false,
                validators: {
                    notEmpty: {},

                    numeric: {
                        'default': '请输入有效的面值，可以是小数'
                    },
                    greaterThan: {
                        value: 'values', // 关联两个控件
                        message: '订单限制金额必须大于等于优惠券的面值',
                    },
                }
            },
            color: { // 颜色
                validators: {
                    notEmpty: {
                        'message': '颜色不能为空'
                    }
                }
            },
            weixin_title: { // 卡券标题
                validators: {
                    notEmpty: {
                        'message': '卡券标题不能为空',
                    }
                }
            },
            weixin_sub_title: { // 卡券副标题
                validators: {
                    notEmpty: {
                        'message': '卡券副标题不能为空',
                    }
                }
            },
            quota: {
                validators: { // 每人限领
                    notEmpty: {
                        'message': '每人限领',
                    }
                }
            },
            start_at: { // 开始时间
                validators: {
                    notEmpty: {
                        'message': '时间不能为空',
                    },
                },
                trigger: 'blur'// 设置验证默认触发事件(失焦时验证) 
            },
            end_at: { // 过期时间
                validators: {
                    notEmpty: {
                        'message': '时间不能为空',
                    },
                },
                trigger: 'blur' // 设置验证默认触发事件(失焦时验证)
            },
            // share_title:{
            //     validators: { 
            //         callback: {
            //             message: '分享标题设置不能为空',
            //             callback: function (value, validator, $field) {
            //                 console.log(2);
            //                 var share_title = $('input[name="share_title"]').val();
            //                 var share_desc = $('textarea[name="share_desc"]').val();
            //                 var share_img = $('input[name="share_img"]').val();
            //                 if((share_desc != '' || share_img  != '') && value == ''){
            //                     return false;
            //                 }
            //                 return true;
            //             }
            //         }
            //     }
            // },
            // share_desc:{
            //     validators: { 
            //         callback: {
            //             message: '分享内容设置不能为空',
            //             callback: function (value, validator, $field) {
            //                 console.log(1);
            //                 var share_title = $('input[name="share_title"]').val();
            //                 var share_desc = $('textarea[name="share_desc"]').val();
            //                 var share_img = $('input[name="share_img"]').val();
            //                 if((share_title != '' || share_img  != '') && value == ''){
            //                     return false;
            //                 }
            //                 return true;
            //             }
            //         }
            //     }
            // },
            // share_img:{
            //     validators: { 
            //         callback: {
            //             message: '请选择分享页图片',
            //             callback: function (value, validator, $field) {
            //                 alert(2)
            //                 var share_title = $('input[name="share_title"]').val();
            //                 var share_desc = $('textarea[name="share_desc"]').val();
            //                 var share_img = $('input[name="share_img"]').val();
            //                 if((share_title != '' || share_desc  != '') && value == ''){
            //                     return false;
            //                 }
            //                 return true;
            //             }
            //         },
            //     }
            // },
        }
    });

     /**
     * 图片选择后的回调函数
     */
    selImgCallBack = function(resultSrc){ 
        if(resultSrc.length>0){
            $("input[name='share_img']").val(resultSrc[0]);
            $("#img_share_img").attr("src",_host+resultSrc[0]).parent().removeClass('hide');  
        } 
    }
    
    // 删除分享图片
    $("body").on("click",".share_img_close",function(){
        $("input[name='share_img']").val('');
        $(this).parent().addClass('hide');
    });
    
    $(".js-add-picture").click(function(){
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['860px', '660px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
    }); 

    // 验证

    var range_value;   //指定商品
    var host = window.location.host;
    $('.submit_btn').click(function (e) {
        var _this = this;
        e.preventDefault();
        $(_this).attr("disabled","disabled");
        $('#addCouponForm').bootstrapValidator('validate');
        if (!$("#addCouponForm").data('bootstrapValidator').isValid()) {
            $(_this).removeAttr("disabled"); 
            return false;
        } 
        var share_title = $('input[name="share_title"]').val();
        var share_desc = $('textarea[name="share_desc"]').val();
        var share_img = $('input[name="share_img"]').val();
        if((share_title == '' && share_desc  == '' && share_img == '') || (share_title != '' && share_desc  != '' && share_img != '')){
            if ($(".goods_appoint").is(":checked")) { //是否选中指定商品
                range_value = [];
                var couponId = $("#couponId").val();
                if ($(".appoint_module table a").length == 0) {
                    $(".error").show();
                    $(_this).removeAttr("disabled");
                    return false;
                } else {
                    for (var i = 0; i < $(".appoint_module table").find(".checked").length; i++) {
                        range_value.push($($(".appoint_module table").find(".checked")[i]).attr("data-id"));
                    }
                    range_value = range_value.toString();
                    if (isEditConpon) {
                        postAdit(range_value, couponId); 
                        return;
                    } else {
                        postAdit(range_value, "");
                        return;
                    }

                }
            }
            if($(".help-block1").length>0){
                tipshow("请完善信息","warn");
                $(_this).removeAttr("disabled");
                return;
            }
            if (isEditConpon) {
                var couponId = $("#couponId").val();
                post(couponId);
                return;
            } else {
                post("");
            }
            function post(couponId) {
                $.post("/merchants/marketing/coupon/xcxCouponAdd/" + couponId, $("form").serialize(), function (res) {
                    mesg(res);
                })
            }
            function mesg(res) {
                if (res.status == 1) {
                    tipshow("保存成功", "info", 1000);
                    window.location.href = "http://" + host + "/merchants/marketing/coupons/all/";
                }else{
                    tipshow(res.info, "warn");
                    $(_this).removeAttr("disabled");
                }
            }
            function postAdit(range_value, couponId) {
                $.post("?merchants/marketing/coupon/set/" + couponId, ($("form").serialize() + "&range_value=" + range_value), function (res) {
                    mesg(res);
                    window.location.href = "http://" + host + "/merchants/marketing/coupons/all/";
                    return;
                })
            }
        }else{
            tipshow("请完善分享设置","warn");
            $(_this).removeAttr("disabled");
            return false;
        } 
    });
 
    

    // 多选
    // $('.synchro_select').chosen({
    //     width: '200px',
    //     height: '150px',
    //     no_results_text: "没有找到",
    // });
    
    // 开始时间
//     $('#startTime,#endTime').datetimepicker({
//         format: 'YYYY-MM-DD HH:mm:ss',
//         dayViewHeaderFormat: 'YYYY 年 MM 月',
//         showClear: true,
//         showClose: true,
//         showTodayButton: true,
//         locale: 'zh-cn',
//         focusOnShow: false,
//         useCurrent: false,
//         tooltips: {
//             today: '今天',
//             clear: '清除',
//             close: '关闭',
//             selectMonth: '选择月',
//             prevMonth: '上个月',
//             nextMonth: '下一月',
//             selectTime: '选择时间',
//             selectYear: '选择年',
//             prevYear: '上一年',
//             nextYear: '下一年',
//             selectDecade: '十年一组',
//             prevDecade: '前十年',
//             nextDecade: '后十年',
//             prevCentury: '前一世纪',
//             nextCentury: '后一世纪',
//         },
//         allowInputToggle: true,
//     });
//     // 最小最大时间设置
//     $("#startTime").on("dp.change", function (e) { 
//         var date = e.date;
//         date._d = new Date(); 
//         // date._isValid = false;
//         $('#startTime').data("DateTimePicker").minDate(date);
//         $('#endTime').data("DateTimePicker").minDate(e.date);
//         //判断开始时间大于结束时间，清空结束时间
//         var startTime = $('#startTime input').val();
//         var endTime = $('#endTime input').val();
//         if(startTime>endTime){
//             $('#endTime input').val("");
//         }
//     });

//     $("#endTime").on("dp.change", function (e) {
//      var ev = $('#startTime').data("DateTimePicker");
//         // console.log(ev.date);
//      $('#endTime').data("DateTimePicker").minDate(ev.date());
// //   if(ev.date() != null){
// //       console.log(e.date);
// //       var ev1 = $('#endTime').data("DateTimePicker");
//          // $('#startTime').data("DateTimePicker").maxDate(e.date);
// //   } 
//     });
    var start = {
      elem: '#startTime',
      format: 'YYYY-MM-DD hh:mm:ss',
      min: laydate.now(), //设定最小日期为当前日期
      max: '2099-06-16 23:59:59', //最大日期
      istime: true,
      istoday: false,
      choose: function(datas){
        // console.log(datas);
        $('#startTime').val(datas);
        $('#startTime').focus();
        $('#startTime').blur();
         // $('.edit_form').data("bootstrapValidator").validate('start_at');
         end.min = datas; //开始日选好后，重置结束日的最小日期
         end.start = datas //将结束日的初始值设定为开始日
      }
    };
    var end = {
      elem: '#endTime',
      format: 'YYYY-MM-DD hh:mm:ss',
      min: laydate.now(),
      max: '2099-06-16 23:59:59',
      istime: true,
      istoday: false,
      choose: function(datas){
        // console.log($('#endTime').val())
        $('#endTime').val(datas);
        $('#endTime').focus();
        $('#endTime').blur();
        // $('.edit_form').data("bootstrapValidator").validateField('end_at');
        start.max = datas; //结束日选好后，重置开始日的最大日期
      }
    };
    laydate(start);
    laydate(end);
   
    /* 交互效果 */
    // 优惠券名称
    $('.js_coupons_name').blur(function () {
        $('.coupons_title').text($(this).val());
    });

    //  随机
    $('.js_random_btn').click(function () {
        if(isEditConpon){
            return;
        }
        if ($(this).prop('checked')) { // 选中
            $('.js_random').removeClass('no'); // 随机上线显示
        } else { // 取消随机
            $('.js_random').addClass('no'); // 随机上线隐藏
        }
    });

    // 面值下线设置
    $('.js_lowerLimit').blur(function () {
        if(isEditConpon){
            return false;
        }
        var _lowerVal = parseFloat($(this).val()).toFixed(2); // 下限值
        var _upperval = $('.js_upperLimit').val(); // 上限值
        var _html = '￥' + _lowerVal;
        if (_upperval) { // 如果存在下限值
            if (_lowerVal > _upperval) {
                // layer.msg('优惠券面值范围的上限必须大于下限', {
                //     skin: 'lose_tip',
                //     offset: '40px',
                //     time: 2000
                // });
                tipshow('优惠券面值范围的上限必须大于下限!', 'warn');
                $('.coupons_denomination').text('￥ 0.00');
                $('.js_lowerLimit').val(''); // 清空下限
                // 从新验证下限
                $('.edit_form').data('bootstrapValidator').updateStatus('values', 'NOT_VALIDATED', null)
                    .validateField('values');
                return; // 结束程序
            } else {
                _html += '~' + parseFloat(_upperval).toFixed(2);
            }
        }
        $('.coupons_denomination').text(_html);
        if($(".use_limit").is(":checked")){
            setMesg()
        }
    });

    // 面值上线设置
    $('.js_upperLimit').blur(function () {
        var _lowerVal = parseFloat($('.js_lowerLimit').val()); // 下限值
        var _upperval = parseFloat($(this).val()).toFixed(2); // 上限值
        if (!_lowerVal) {
            _lowerVal = '0.00';
        }
        var _html = '￥' + parseFloat(_lowerVal).toFixed(2);
        if (!_upperval) {
            layer.msg('优惠券面值范围必须大于等于 0.01 元', {
                skin: 'lose_tip',
                offset: '40px',
                time: 2000
            });
            $(this).val(''); // 清空上限
            $('.edit_form').data('bootstrapValidator').updateStatus('value_random_to', 'NOT_VALIDATED', null)
                .validateField('value_random_to');
            return;
        } else {
            _html += '~' + _upperval;
        }
        $('.coupons_denomination').text(_html);
    });

    // 使用微信
    $('.is_sync_weixin').click(function () {
        if ($(this).prop('checked')) { // 同步到微信
            $('.weixin_group,.weixin_set').removeClass('no');
        } else { // 取消同步到微信
            $('.weixin_group,.weixin_set').addClass('no');
        }
    });

    // 微信卡券背景色
//  $('.js_color').change(function () {
//      $('.card_module').css('background', $(this).val());
//  });

    // 微信标题
    $('.js_weixin_title').blur(function () {
        var _val = $(this).val();
        var _html = '';
        if (_val) {
            _html += _val;
        } else {
            _html += '微商城';
        }
        $('.card_name').text(_html);
    });

    // 微信副标题
    $('.js_sub_title').blur(function () {
        var _val = $(this).val(),
            _html = '';
        if (_val) {
            _html += _val;
        } else {
            _html += '微信卡券标题';
        }
        $('.card_limit').text(_html);
    });


    // 可使用商品
    $('.goods_range').click(function () {
        var _objTip = $('.' + $(this).data('tip')); // 对应的提示对象
        _objTip.removeClass('no').siblings('.tip_des').addClass('no'); // 对应的提示显示，其他的提示隐藏
        $('.appoint_module').addClass('no'); // 指定商品隐藏
    });

    // 指定商品
    $('.goods_appoint').click(function () {
        $('.appoint_module').removeClass('no'); // 指定商品显示
    });

    // 添加商品的弹框显示

    var _product = 1;//默认已上架商品
    $('.js_add_goods').click(function () {  
        //新代码
        var href = _host+"merchants/product/create"; 
        selGoods.open({success:callback,href:href,is_multiple:1});
    });

    //选择商品回调函数
    function callback(data){
        console.log(data);
        var _html ="";
        for(var i=0;i<data.length;i++){
            var is_select = false; //false 商品未选， true商品已选
            $(".appoint_module table tbody .checked").each(function(){
                var id = $(this).attr("data-id");
                if(id==data[i].id){//判断商品是否已选
                    is_select = true;
                    return false;
                }
            });
            if(!is_select){ 
                _html += ' <tr>';
                _html += ' <td><a class="checked" href="javascript:;" data-id="'+data[i].id+'">'+data[i].title+'</a></td>';
                _html += ' <td><a class="del_goods blue_38f f12" href="javascript:void(0);">删除</a></td>';
                _html += ' </tr>';
            }
            
        }
        $('.appoint_module table tbody').append(_html);
    }

    // 删除商品
    $('body').on('click', '.del_goods', function () {
        // if(isEditConpon){
        //     return;
        // }
        $(this).parents('tr').remove();
    });


    // input设为不可用
    var url = window.location.href;
    var arr = url.split("/");
    var isEditConpon = false;
//     if (url[url.length - 1] != "/") {

//         if (arr[arr.length - 1] != "set") {
//             isEditConpon = true;
// //          setInputDisable()     微信卡券无法设置
//         }
//     } else {
//         if (arr[arr.length - 2] != "set") {
//             isEditConpon = true;
//             setInputDisable()
//         }
//     }
    var id = $('#couponId').val();
    if(id != 0){
        isEditConpon = true;
        // setInputDisable()     微信卡券无法设置
    }
    // 设置input不可选
    function setInputDisable() {
        $("form input").attr("readonly", "true");
        $("form select").attr({
            "onfocus": "this.defaultIndex=this.selectedIndex;",
            "onchange": "this.selectedIndex=this.defaultIndex;"
        });
        $("input[name='title'], input[name='total'], input[name='expire_remind'], input[name='is_share']").removeAttr("readonly");
        $("input[name='is_sync_weixin']").attr("onclick", "return false");
        $("input[name='is_sync_weixin']").unbind();
        if ($(".unlimited").is(":checked")) {   //使用门槛是否选中
            $(".use_limit").attr("disabled", "disabled");
        } else {
            $(".unlimited").attr("disabled", "disabled");
        }
        if ($(".goods_range").is(":checked")) {   //指定商品是否选中
            $(".goods_appoint").attr("disabled", "disabled");

        } else {
            $(".js_add_goods").hide();
            $(".goods_range").attr("disabled", "disabled");
        }
        $(".js_random_btn").attr("onclick", "return false");
    }



    // 不设置门槛
    $(".no_use").click(function () {
        $(".help-block1").remove();
        $(".coupons_limit").html("不限制");
    })

    // 设置门槛
    $(".use_limit").click(function () {
        setMesg();
    })
    //设置使用门槛后显示在页面上的信息
    function setMesg() {
        var pre = $("input[name='limit_amount']").val() || 0;
        var price = $("input[name='amount']").val() || 0;
        var html = '<span class="help-block1" style="color:#a94442;text-align:center;display:block">';
        html += '订单限制金额必须大于等于优惠券的面值</span>';
        if ($(".help-block1").length == 1) { return; }
        if (parseFloat(pre) < parseFloat(price)) {
            $(".coupons_limit").html("订单满xx元(含运费)");
            $("input[name='limit_amount']").parent().parent().parent().after(html);
        } else {
            // 当值为0时
            if (pre && pre != "0") {
                price = pre

            } else {
                price = "xx";
            }
            $(".coupons_limit").html("订单满" + price + "元(含运费)");
            $(".help-block1").remove();
        }
    }
    $(".js_limit").change(function(){
        $(".help-block1").remove();
        if($(".use_limit").is(":checked")){
            setMesg()
        }
    });

    // 观察面值是否小于门槛
    function lt() {
        if ($(".use_limit").is(":checked")) {
            var price = $("input[name='limit_amount']").val() || 0;
            if ($("input[name='amount']").val() > price) {
                if ($(".help-block1")) {
                    return;
                }
                $("input[name='limit_amount']").parent().parent().parent().after(html);;
            } else {
                $(".help-block1").remove();
            }
        }
    }
    $("input[name='amount']").blur(function () {
        lt();

        if ($(".use_limit").is(":checked")) {
            setMesg();
        }
    })
    $("input[name='limit_amount']").blur(function () {
        lt();
        if ($(".use_limit").is(":checked")) {
            setMesg();
        }
    })


    
    //  禁止重读提交
    // $('.btn-primary').click(function(){
    //  $('.btn-primary').attr("disabled","disabled");
    //  $(":text").on("input focus", function () {
    //       $('.btn-primary').attr("disabled",false);
    //  });         
    // })
    
//  微信卡券颜色
    var classArr = ["Color010","Color020","Color030","Color040","Color050","Color060","Color070","Color080","Color081",
    "Color082","Color090","Color100","Color101","Color102"]
    var html = '';
    for(var i = 0;i < classArr.length;i ++){
        html += '<li class="'+classArr[i]+'"></li>'        
    }
    $('.bgColor_cap').append(html);
    $('.controls').hover(function() {
        $('.bgColor_cap').show();
    },function() {
        $('.bgColor_cap').hide();
    });
    //选择背景颜色
    $(document).on('click','.bgColor_cap li',function(){        
        $('.bgColor').attr('class','bgColor');
        $('.bgColor').addClass($(this).attr('class'));
        var color_num = $(this).attr('class');
        $(".color-num").val(color_num);
        $('.card_module').attr('class','card_module');
        $('.card_module').addClass(color_num);
        $('.card_module').css('background-color',$(this).css('background-color'))
        $('.bgColor_cap').hide();
        $('input[name="bg_color"]').val($(this).attr('class'));
    });

    // 分享加图
    $('#file').on('change', function(){
        var reader = new FileReader();
        reader.readAsDataURL(this.files[0]);
        if(this.files[0].size > 102400){
            tipshow("图片不能超过100K","warn");
            return;
        }
        reader.onload = function(e){
            $('.share_img').attr('src',this.result);
            $('.share_img').show();
        }
        var formData = new FormData();
        formData.append("file", document.getElementById('file').files[0]);
        $.ajax({
            url: '/merchants/myfile/upfile',
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res) {
                res = JSON.parse(res);
                logo = res.data.FileInfo['path'];
                $('input[name="share_img"]').val(logo);
            },
            error:function(){

            }
        })
    }); 
})
