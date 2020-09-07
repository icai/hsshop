$(function(){
    $('.copy_btn').click(function(){
        var obj = $(this).siblings('.link_copy');
        copyToClipboard( obj );
        // layer.msg('链接复制成功',{
        //     skin: 'success_tip',
        //     offset: '40px',
        //     time:2000
        // });
        tipshow('链接复制成功!');
    });

    // 搜索
    $(".chzn-select").chosen({
        width:'150px',
        no_results_text: "没有找到",
        allow_single_de: true
    });

    // 全选、单选
    checkAll( '.check_all' , '.check_single' );

    // 列表复制 
    $('body').on('click','.copy_list',function(){
        var _this = this;
        var content = $(this).find('span').text();
        layer.tips('确定'+content+'?', _this, {
            tips: 4,
            skin: 'confirm_items',
            area: '230px',
            btn: ['确定', '取消'],
            time:2000000,
            yes:function( index ){
                var html = $(_this).parents('tr').prop("outerHTML");                                               // 要复制的东西     
                // 插入复制的内容
                $(html).insertAfter( $(_this).parents('tr') );                  
                // 设为主页样式修改(只能有一个店铺主页)
                $(_this).parents('tr').next().find('.set_homepage span').addClass('blue_38f').text('设为主页');   
                layer.close( index );

                // 复制成功消息
                // layer.msg(content+'成功',{
                //     skin: 'success_tip',
                //     offset: '40px',
                //     time:2000
                // });
                tipshow(content+'成功');
            },
        });
    });

    // 删除列表
    $('body').on('click','.del_list',function(){
        var _this = this;
        var content = $(this).find('span').text();
        layer.tips('确定'+content+'?', _this, {
            tips: 4,
            skin: 'confirm_items',
            area: '230px',
            btn: ['确定', '取消'],
            time:2000000,
            yes:function( index ){
                var setHomePage = $(_this).siblings('.set_homepage').find('span');
                $(_this).parents('tr').remove();
                if( !setHomePage.hasClass('blue_38f') ){             // 删除的是主页
                    // 默认第一个是店铺主页
                    $('table').find('tr').eq(1).find('.set_homepage span').removeClass('blue_38f').text('店铺主页');
                }

                // 删完店铺主页
                if( $('table tr').length ==1 ){
                    $('table').append('<tr class="empty_list">暂无数据</tr>')
                } 

                layer.close( index );
                // layer.msg(content+'成功',{
                //     skin: 'success_tip',
                //     offset: '40px',
                //     time:2000
                // });
                tipshow(content+'成功');
            },
        });        
    });

    // 设为主页
    $('body').on('click','.set_homepage',function(){
        $(this).find('span').removeClass('blue_38f').text('店铺主页');
        $(this).parents('tr').siblings('tr').find('.set_homepage span').addClass('blue_38f').text('设为主页');
    });
    
    // 批量管理弹框
    $('.manage_items').focusin(function(){              // 聚焦
        if( flagGrounp() ){
            $('.manage_tip').stop().fadeIn( 400 );
        }else{
            // layer.msg('请选择微页面',{
            //     skin: 'lose_tip',
            //     offset: '40px',
            //     time:2000
            // });
            tipshow('请选择微页面','warn');
        }   
    });
    // 失焦
    $('.manage_items').focusout(function() {        // 失焦 
        $('.manage_tip').stop().fadeOut( 400 );
    });
})

/**
 * [flagGrounp 判断列表是否有选中状态]
 * @return {[type]} [返回boolearn true->有选中的，false->无选中的]
 */
function flagGrounp(){
    var flag = false
    console.log('调用');
    $('table .check_single').each(function(){
        if( $(this).prop('checked') ){
            flag = true;
        }
    });
    return flag;
}

/**
 * [copyToClipboard 复制到粘切板函数]
 * @param  {[type]} obj [ 要复制的对象 ]
 * @return {[type]}     [无]
 */
function copyToClipboard( obj ) {
    var aux = document.createElement("input");                  // 创建元素用于复制
    // 获取复制内容
    var content = obj.text() || obj.val();
    // 设置元素内容
    aux.setAttribute("value", content);
    // 将元素插入页面进行调用
    document.body.appendChild(aux);
    // 复制内容
    aux.select();
    // 将内容复制到剪贴板
    document.execCommand("copy");
    // 删除创建元素
    document.body.removeChild(aux);
}