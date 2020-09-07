$(function(){
	$('.add_form').bootstrapValidator({
        message: '不能为空',                    // 设置默认提示语
        trigger:'blur',                         // 设置验证默认触发事件(失焦时验证)                 
        feedbackIcons: {
            //valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {                                // 验证
            name: {                             // 活动名称
                validators: {
                	notEmpty: {
                        message: '名称不能为空',
                    },
                }
            },
            start_time:{        // 开始时间
                validators: {
                    notEmpty: {
                        'message':'时间不能为空',
                    },
                }
            },
            end_time:{      // 过期时间
                validators: {
                    notEmpty: {
                        'message':'时间不能为空',
                    },
                }
            },
            total:{             // 发行量
                validators: {
                    notEmpty: {
                        message: '发行量不能为空',
                    },
                    integer: {
                        'default': '请输入有效的整数值'
                    },
                }
            },
            values:{            // 面值下限
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
            value_random_to:{   // 面值上限
                validators: {
                    notEmpty: {
                        message: '面值不能为空',
                    },
                    numeric: {
                        'default': '优惠券面值范围的上限必须大于下限'
                    },
                    greaterThan: {
                        value: 'values',
                        message:'优惠券面值范围的上限必须大于下限',
                    },
                    lessThan: {
                        value: 10000
                    },
                }
            },
            at_least: {                     //使用门槛
                enabled:false ,
                validators: {
                    notEmpty: {},

                    numeric: {
                        'default': '请输入有效的面值，可以是小数'
                    },
                    greaterThan: {
                        value:'values',             // 关联两个控件
                        message:'订单限制金额必须大于等于优惠券的面值',
                    },
                }
            },
            
            color:{                     // 颜色
                validators: {
                    notEmpty: {}
                }
            },
            weixin_title:{            // 卡券标题
                validators: {
                    notEmpty: {
                        'message':'卡券标题不能为空',
                    }
                }
            },
            weixin_sub_title:{      // 卡券副标题
                validators: {
                    notEmpty: {
                        'message':'卡券副标题不能为空',
                    }
                }
            },
            quota:{
                validators: {       // 每人限领
                    notEmpty: {
                        'message':'每人限领',
                    }
                }
            },
            'meet[]':{
                validators: {       // 满
                    notEmpty:{},
                    numeric: {
                        'default': '请输入有效的数值，允许小数'
                    },
                    greaterThan: {
                        value:1,
                        'message':'满减金额必须大于0',
                    },
                }
            },
           'cash[]':{
                container:'.tip_error',
                validators: {       // 满
                    notEmpty:{},
                    numeric: {
                        'default': '请输入有效的数值，允许小数'
                    },
                    greaterThan: {
                        value:1,
                        'message':'满减金额必须大于0',
                    },
                }
            },
        }
    });
    
    $('.submit_btn').click(function() {
        $('.edit_form').bootstrapValidator('validate');
        return false;
    });

    // 多选
    $('.synchro_select').chosen({ 
        width:'200px',
        height:'150px',
        no_results_text: "没有找到",
    });
    // 开始时间
    $('#startTime,#endTime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        dayViewHeaderFormat: 'YYYY 年 MM 月',
        useCurrent: true,
        showClear:true,
        showClose:true,
        showTodayButton:true,
        locale:'zh-cn',
        focusOnShow: true,
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
         useCurrent: false,
        allowInputToggle:true,
    });
    // 最小最大时间设置
    $("#startTime").on("dp.change", function (e) {
        $('#endTime').data("DateTimePicker").minDate(e.date);
    });

    $("#endTime").on("dp.change", function (e) {
        $('#startTime').data("DateTimePicker").maxDate(e.date);
    });

    // 多级优惠还是普通优惠
    $('body').on('click','.discount_radio',function(){
        if( $(this).hasClass('multistage_discount') ){                   // 多级优惠
            $('.discount_table').find('tfoot').removeClass('no');        // 添加多级的按钮显示
        }else{                                                           // 普通优惠
            $('.discount_table').find('tfoot').addClass('no'); 
        }
    });

    // 减钱
    $('body').on('click','.js_reduce',function(){
        if( $(this).is(':checked') ){
            $(this).parent().next('.reduce_input').removeClass('no');
            $(this).parent('label').siblings('.tip').text('元');
        }else{
            $(this).parent().next('.reduce_input').addClass('no');
            $(this).parent('label').siblings('.tip').text('  现金');
        }
    });

    // 送优惠
    $('body').on('click','.give_discount',function(){
        if( $(this).prop('checked') ){
            $(this).parent().nextAll('.discount_select').removeClass('no');
            $(this).parent().siblings('.tip').hide();
        }else{
            $(this).parent().nextAll('.discount_select').addClass('no');
            $(this).parent().siblings('.tip').show();
        }
    });
    // 送赠品
    $('body').on('click','.give_giveaway',function(){
        if( $(this).prop('checked') ){
            $(this).parent().nextAll('.giveaway_select').removeClass('no');
            $(this).parent().siblings('.tip').hide();
        }else{
            $(this).parent().nextAll('.giveaway_select').addClass('no');
            $(this).parent().siblings('.tip').show();
        }
    });

    // 新增优惠层级
    $('.js_add').click(function(){
        var count = parseInt($(this).data('count'));            // 得到当前层级的ID值
        count ++;                                               // ID增加1
        $(this).data('count',count);                            // 保存当年层级的ID值  
        if( count >= 5 ){                                       // 层级>=5
            $('.discount_table tfoot').addClass('no');          // 添加优惠层的按钮隐藏
        }
        var _html = '<tr>';
            _html += '<td><span class="level_id">'+count+'</span></td>';
            _html += '<td>';
            _html += '<div class="form-group">';
            _html += '<div class="display_box">';
            _html += '满&nbsp;&nbsp;<input class="form-control small" type="text" name="meet[]" value="" >&nbsp;&nbsp;元';
            _html += '</div>';
            _html += '</div>';
            _html += '</td>';
            _html += '<td>';
            _html += '<div class="center_start h32">';
            _html += '<div class="display_box">';
            _html += '<label>';
            _html += '<input class="js_reduce" type="checkbox" name="" value="" />减';
            _html += '</label>';
            _html += '<div class="reduce_input form-group no center_start">';
            _html += '<div class="relative">';
            _html += '<input class="form-control small" type="text" name="cash[]" value="" />  ';
            _html += '</div>';
            _html += '</div>';
            _html += '<div class="tip">现金</div>';
            _html += '<div class="tip_error"></div>';
            _html += '</div>';
            _html += '</div>';
            _html += '<div class="center_start h32">';
            _html += '<label>';
            _html += '<input type="checkbox" name="" value="" />免邮';  
            _html += '</label>';
            _html += '</div>';
            _html += '<div class="center_start h32">';
            _html += '<label>';
            _html += '<input type="checkbox" name="" value="" disabled />送积分';  
            _html += '</label>';
            _html += '<div class="gray_999">(升级认证服务号才可用)</div>';
            _html += '</div>';
            _html += '<div class="center_start h32">';
            _html += '<label>';
            _html += '<input class="give_discount" type="checkbox" name="" value="" />送 ';
            _html += '</label>';
            _html += '<div class="tip">优惠</div>';
            _html += '<div class="discount_select no">';
            _html += '<select class="mglr5" name="coupon">';
            _html += '<option value="" selected>测试</option>';
            _html += '<option value="">测试b</option>';
            _html += '<option value="">测试优惠码</option>';
            _html += '</select>';
            _html += '<a class="blue_38f" href="JavaScript:void(0);">刷新</a>|';
            _html += '<a class="blue_38f" href="javascript:void(0);">新建</a>';
            _html += '</div>';
            _html += '</div>';
            _html += '<div class="center_start h32">';
            _html += '<label>';
            _html += '<input class="give_giveaway" type="checkbox" name="" value="" />送 ';
            _html += '</label>';
            _html += '<div class="tip">赠品</div>';
            _html += '<div class="giveaway_select no">';
            _html += '<select class="mglr5" name="coupon">';
            _html += '<option value="" selected>测试</option>';
            _html += '<option value="">测试b</option>';
            _html += '<option value="">测试优惠码</option>';
            _html += '</select>';
            _html += '<a class="blue_38f" href="JavaScript:void(0);">刷新</a>|';
            _html += '<a class="blue_38f" href="javascript:void(0);">新建</a>';
            _html += '</div>';
            _html += '</div>';
            _html += '</td>';
            _html += '<td>';
            _html += '<a class="js_del_discount gray_999" href="javascript:void(0);">删除</a>';
            _html += '</td>';
            _html += '</tr>'
        $('.discount_table tbody').append( _html );
        $('.add_form').bootstrapValidator('addField', 'meet[]');
        $('.add_form').bootstrapValidator('addField', 'cash[]');
    });

    // 删除层级
    $('body').on('click','.js_del_discount',function(){
        $(this).parents('tr').remove();                         // 当前层级删除
        var count = 0;
        $('.discount_table tbody tr').each(function(){
            count++;
            $(this).find('.level_id').text( count );            // 层级ID从新分配
            $('.js_add').data('count',count);                   // 保存层级ID 的最大值
            if( count < 5 ){

                $('.discount_table tfoot').removeClass('no');   // 添加按钮显示 
            }
        });
    });

    // 商品参与
    $('.partake').click(function(){
        if( $(this).hasClass('section_partake') ){      // 部分商品参与
            $('.active_module').removeClass('no');      // 参与模块显示
        }else{
            $('.active_module').addClass('no');         // 参与模块隐藏
        }
    });
    // 全选
    $('.all_check').click(function(){
        if( $(this).prop('checked') ){      //全选
            $('.single_check').prop('checked',true);
        }else{
            $('.single_check').prop('checked',false);
        }
    });

    // 单选
    var _length = $('.single_check').length;
    $('.single_check').click(function(){
        var count = $('.single_check:checked').length;
        if( count == _length ){             // 全选
            $('.all_check').prop('checked',true);
        }else{
            $('.all_check').prop('checked',false);
        }
        $('.num').text( count );
    });

    $('.fastSelect_time').click(function(){
        $(this).addClass('active').siblings('.data_btn').removeClass('active');
        var day   = $(this).data('day');              // 前/后 n天
        var date = fun_time( day );
        $('#startTime input').val( date[0] );        // 开始时间
        $('#endTime input').val( date[1] );          // 结束时间
        // 开始时间验证
        $('.add_form').data('bootstrapValidator').updateStatus('start_time', 'VALID', null);
        // 结束时间验证
        $('.add_form').data('bootstrapValidator').updateStatus('end_time', 'VALID', null);
    });
})
 


