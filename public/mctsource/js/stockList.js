// add by 黄新琴 2018-08-08
$(function(){
    // 全选
    $('#all-kam').change(function(){
        if($(this).is(':checked')){
            $(this).prop("checked",true); 
            $('.J_kam').prop("checked",true);
        }else{
            $(this).prop("checked",false);   
            $('.J_kam').prop("checked",false);
        }
    });
    $('.condent_data').on('change','.J_kam',function(){
        if (!$(this).is(':checked')){
            $('#all-kam').prop("checked",false);
            return;
        }
        var allCheckNum = $('.J_kam').length,
            allCheckedNum = $('.J_kam:checked').length;
        if (allCheckNum==allCheckedNum){
            $('#all-kam').prop("checked",true);
        }
    });
    //   批量删除
    $('.J_del').click(function(){
        if(!$('.J_kam').is(':checked')){
            tipshow('请先选择要删除的订单！','warn');
            return;
        }
        $('#del-popover').show();
    });
    $('.js-del-sure').click(function(e){
        $.post('/merchants/cam/list/delbatch',$('form[name="kam_form"]').serialize(),function(data){
            if(data.status==1){
                $('input[type="checkbox"]').prop('checked',false);
                tipshow(data.info);
                window.location.href="/merchants/cam/camStockList?id="+id;
            }else{
                tipshow(data.info,'warn');
            }
            //居中弹窗
            $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json');
        $('#del-popover').hide();
        e.stopPropagation();
    });
    $('.js-cancel').click(function(e){
        $('.ui-popover').hide();
        e.stopPropagation();
    });

    //   全部导出
    $('.J_export').click(function(){
        if(!$('.J_kam').is(':checked')){
            tipshow('请先选择要导出的订单！','warn');
            return;
        }
        $('#export-popover').show();
    });
    $('.js-export-sure').click(function(e){
        window.location.href='/merchants/cam/export?'+$('form[name="kam_form"]').serialize();
        $('input[type="checkbox"]').prop('checked',false);
        $('#export-popover').hide();
        e.stopPropagation();
    });
  
})