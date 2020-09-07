$(function(){
    $(document).on('click','.search_nav .nav_list',function(){
        $(this).parent().children('.nav_list').removeClass('border1');
        $(this).addClass('border1');
        var name = $(this).parent().attr('name');
        var val =  $(this).attr('value');
        $("input[name='"+name+"']").val(val);
    })
    $('#all_fans').change(function() {
        if ($(this).is(':checked')) {
            $(this).prop("checked", true);
            $('.fans').prop("checked", true);
        } else {
            $(this).prop("checked", false);
            $('.fans').prop("checked", false);
        }
    })

    // 标签
    $('.setting_biao').click(function() {
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
        $('.biao_header').html($(this).children('a').html());
        $('.biao').show();
    })
    $('#bs_btn').click(function() {
        $('.biao').hide();
    })
    $('#bc_btn').click(function() {
        $('.biao').hide();
    })

    // 等级
    $('.setting_level').click(function() {
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
        $('.level_header').html($(this).children('a').html());
        $('.level').show();
    })
    $('#ls_btn').click(function() {
        $('.level').hide();
    })
    $('#lc_btn').click(function() {
        $('.level').hide();
    })

    // 积分
    $('.clear_credit').click(function() {
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
        $('.credit_header span').html($(this).children('a').html());
        $('.cl_credit').show();
    })
    $('#cs_btn').click(function() {
        $('.cl_credit').hide();
    })
    $('#cc_btn').click(function() {
        $('.cl_credit').hide();
    })

    $('.setting_credit').click(function() {
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
        $('.se_credit_header').html($(this).children('a').html());
        $('.se_credit').show();
    })
    $('#ss_btn').click(function() {
        $('.se_credit').hide();
    })
    $('#sc_btn').click(function() {
        $('.se_credit').hide();
    })
    $('.add_biao').click(function() {
        // alert($(this).offset().top);
        $('.wai_biao').show();
        $('.wai_biao').css('top', $(this).offset().top);
    })
    
    // 外部标签
    $('#wbs_btn').click(function() {
        $('.wai_biao').hide();
    })
    $('#wbc_btn').click(function() {
        $('.wai_biao').hide();
    })

    // 自定义积分
    $('#rcs_btn').click(function() {
        var min_credit = $('input[name="min_credit"]').val();
        var max_credit = $('input[name="max_credit"]').val();
        if(min_credit ==''){
            tipshow('最小积分范围不能为空！','warn');
            $('input[name="min_credit"]').focus();
            return;
        }
        if(max_credit ==''){
            layer.alert('最大积分范围不能为空！');
            $('input[name="max_credit"]').focus();
            return;
        }
        if(min_credit>max_credit){
            layer.alert('最小积分不能大于最大积分！');
            $('input[name="max_credit"]').focus();
            return;
        }

        var name = $(this).parent().attr('name');
        $('.focus_time[name=\''+name+'\'] .nav_list').removeClass('border1');

        //var name = 'integral';
        var val =  min_credit+'-'+max_credit;
        $("input[name='"+name+"']").val(val);
        var obj = $(".focus_time[name='"+name+"'] .credit_date");
        if(obj.length>0){
            obj.addClass('border1');
            obj.attr('value',min_credit+'-'+max_credit);
            obj.html(min_credit+'-'+max_credit);
            $('.range_credit').hide();
            return;
        }

        var html = '<span class="nav_list  credit_date border1 " value="'+min_credit+'-'+max_credit+'">'+min_credit+'-'+max_credit+'</span>'
        //$('.credit_range').append(html);
        $(".focus_time[name='"+name+"']").append(html);
        $('.range_credit').hide();
    })
    $('#rcc_btn').click(function() {
        $('.range_credit').hide();
    })
    $('.write_credit').click(function() {
        $('.range_credit').show();
        var width = $(this).offset().left - $('.range_credit').width() - 20;
        $('.range_credit').css('top', $(this).offset().top);
        $('.range_credit').css('left', width);
        var name = $(this).prev().attr('name');
        $('#rcs_btn').parent().attr('name',name);
    })
    //自定义时间
    //laydate
    laydate.skin('molv'); //切换皮肤，请查看skins下面皮肤库
    var start = {
        elem: '#start_date',
        format: 'YYYY-MM-DD',
        min: '2009-06-16 23:59:59', //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        event: 'focus',
        istime: true,
        istoday: false,
        choose: function(datas) {
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var end = {
        elem: '#end_date',
        format: 'YYYY-MM-DD',
        min: '2009-06-16 23:59:59',
        max: '2099-06-16 23:59:59',
        event: 'focus',
        istime: true,
        istoday: false,
        choose: function(datas) {
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);
    $('#rps_btn').click(function() {
        var start_date = $('input[name="start_date"]').val();
        var end_date = $('input[name="end_date"]').val();
        if(start_date ==''){
            layer.alert('最小日期范围不能为空！');
            $('input[name="start_date"]').focus();
            return;
        }
        if(end_date ==''){
            layer.alert('最大日期范围不能为空！');
            $('input[name="end_date"]').focus();
            return;
        }
        $('.focus_time .nav_list').removeClass('border1')
        var name = $(this).parent().attr('name');
       // alert(name);
        //var name = 'f_time';
        var val =  start_date +'|'+ end_date;
        $("input[name='"+name+"']").val(val);
        var objTime = $('.focus_time[name=\''+name+'\'] .last_date');

        if(objTime.length>0){
            objTime.addClass('border1');
            objTime.attr('value',start_date +'|'+ end_date);
            objTime.html(start_date+' 到 '+ end_date );
            $('.range_price').hide();
            return;
        }
        var html = '<span class="nav_list border1 last_date" value="'+ start_date +'|'+ end_date +'">'+start_date+' 到 '+ end_date +'</span>';
        $('.focus_time[name=\''+name+'\']').append(html);
        $('.range_price').hide();
    })
    $('#rpc_btn').click(function() {
        $('.range_price').hide();
    })
    $('.choose_time').click(function(){
        var name = $(this).prev().attr('name');
        $('#rps_btn').parent().attr('name',name);
        $('.range_price').show();
        var width = $(this).offset().left - $('.range_price').width() - 20;
        $('.range_price').css('top', $(this).offset().top);
        $('.range_price').css('left', width);
    })
})