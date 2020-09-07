$(function(){
  
     $('.one_choose .nav_list').click(function() {
        $(this).parent().children('.nav_list').removeClass('border1');
        $(this).addClass('border1');
        var name = $(this).parent().attr('name');
        var val =  $(this).attr('value');
        $("input[name='"+name+"']").val(val);
    })
    // 多选点击
    $(document).on('click','.area_show span',function(){
        $('.no_limit').removeClass('border1');
        var id = $(this).data('value');
        var ids = $('input[name="regions_id"]').val();
        if($(this).hasClass('border1')){
            $(this).removeClass('border1');
            ids = ids.split(",");
            if($.inArray(id, ids)){
                for(var i =0;i<ids.length;i++){
                    if(ids[i] == id){
                        ids.splice(i,1);
                        break;
                    }
                }
                ids = ids.join(',')
                $('input[name="regions_id"]').val(ids);
            }
        }else{
            if(ids ==''){
                ids = id;
            }else{
                ids = ids + ',' + id;
            }
            $('input[name="regions_id"]').val(ids);
            $(this).addClass('border1');
        }
    })
    $('#all_fans').change(function(){
        if($(this).is(':checked')){
            $(this).prop("checked",true); 
            $('.fans').prop("checked",true);
        }else{
            $(this).prop("checked",false);   
            $('.fans').prop("checked",false);
        }
    })
    // 更多地区选择
    $(document).on('click','.more',function(){
        $('.area_show span').each(function(key,val){
            if($(this).hasClass('border1')){
                var id = $(this).data('value');
                $('.items-ul').each(function(key,val){
                   $(this).children('.search_nav').each(function(key1,val1){
                        if($(this).children('.nav_list').data('value')==id){
                            $(this).children().addClass('border1');
                        }
                   }) 
                })
            }
        })
        $('#area_popover').show();
        var position = $(this).offset();
        $('#area_popover').css({
            'top':position.top+$(this).height(),
            'left':position.left - $('#area_popover').width()/2
        })
    })
    //地区确定按钮点击
    $('.js-save').click(function(){
        var html = '';
        var ids = '';
        $('.items-ul').each(function(key,val){
            $(this).children('.search_nav').each(function(key1,val1){
                if($(this).children('.nav_list').hasClass('border1')){
                    html += '<span class="nav_list border1" data-value="'+ $(this).children('.nav_list').data('value') +'">'+ $(this).children('.nav_list').text() +'</span>';
                    var id = $(this).children('.nav_list').data('value');
                        ids += id + ',';
                }
            }) 
        })
        // html += '<a class="more" href="javascript:void(0);">更多..</a>';
        ids = ids.substr(0,ids.length - 1);
        $('input[name="regions_id"]').val(ids);
        $('#regions_id').eq(0).children('.area_show').html(html);
        $('#area_popover').hide();
    })
    //地区取消按钮点击
    $('.js-cancel').click(function(){
        $('#area_popover').hide();
    })
    //地区不限点击
    $('.no_limit').click(function(){
        $('input[name="regions_id"]').val('');
        $('.regions_id span').removeClass('border1');
        $(this).addClass('border1');
    })
    //地区全选
    $('.js-select-all').click(function(){
        var ids = '';
        if($(this).is(':checked')){
            $('.items-ul').each(function(key,val){
               $(this).children('.search_nav').each(function(key1,val1){
                    $(this).children().addClass('border1');
                    var id = $(this).children('.nav_list').data('value');
                    ids += id + ',';
               }) 
            })
            ids = ids.substr(0,ids.length - 1);
            $('input[name="regions_id"]').val(ids);
        }else{
            $('.items-ul').each(function(key,val){
               $(this).children('.search_nav').each(function(key1,val1){
                    $(this).children().removeClass('border1');
               }) 
            })
            $('input[name="regions_id"]').val('');
        }
    })
    // 标签
    $('.setting_biao').click(function(){
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
        $('.biao_header').html($(this).children('a').html());
        $('.biao').show();
    })
    $('#bs_btn').click(function(){
        $('.biao').hide();
        //
    })
    $('#bc_btn').click(function(){
         $('.biao').hide();
    })

    // 等级
    $('.setting_level').click(function(){
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
       $('.level_header').html($(this).children('a').html());
       $('.level').show();
    })
    $('#ls_btn').click(function(){
         $('.level').hide();
    })
    $('#lc_btn').click(function(){
         $('.level').hide();
    })

    // 积分
    $('.clear_credit').click(function(){
       if(!$(".avatar input[type='checkbox']").is(':checked')){
          tipshow('请选中粉丝','warn');
          return false;
       }
       $('.credit_header span').html($(this).children('a').html());
       $('.cl_credit').show();
    })
    $('#cs_btn').click(function(){
        $('.cl_credit').hide();
    })
    $('#cc_btn').click(function(){
         $('.cl_credit').hide();
    })

    $('.setting_credit').click(function(){
        if(!$(".avatar input[type='checkbox']").is(':checked')){
            tipshow('请选中粉丝','warn');
            return false;
        }
       $('.se_credit_header').html($(this).children('a').html());
       $('.se_credit').show();
    })
    $('#ss_btn').click(function(){
        
        //
        $('.se_credit').hide();
    })
    $('#sc_btn').click(function(){
         $('.se_credit').hide();
    })
    $('.add_biao').click(function(){
        // alert($(this).offset().top);
        $('.wai_biao').show();
        $('.wai_biao').css('top',$(this).offset().top);
    })
        
    // 外部标签
    $('#wbs_btn').click(function(){
         $('.wai_biao').hide();
    })
    $('#wbc_btn').click(function(){
         $('.wai_biao').hide();
    })  
})

function ajax_func(url,__type,__data){
    $.ajax({
        type: __type,
        url: url,
        data: __data,
        dataType: "json",
        success: function (data) {
            alert(data);
            if(data.status=="0"){
                layer.msg(data.info);
            }else{
                layer.msg(data.info,
                    {
                        icon: 1,
                        time: 5000 //5秒关闭（如果不配置，默认是3秒）
                    }
                );
               // window.location.href = url;
            }
        }
    });
}

