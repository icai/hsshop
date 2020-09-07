/**
 * Created by admin on 2018/1/11.
 */
$(function () {
    var  frameindex= parent.layer.getFrameIndex(window.name);
    $("#bind").click(function () {
        $.ajax({
            type:"post",
            url:'/staff/permission/bindStaffPermission',
            data:$('#bindPermission').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                if(res.status===1){
                    parent.layer.close(frameindex);
                }else{

                }
            },
            error:function(){
                alert('数据访问异常')
            }
        });

    })
    
    
    
    $(".permission_menu_ele").click(function () {
        $('.permission_list').removeClass('hide');
        $('.add_permission').addClass('hide');
    })
    $(".add_permission_ele").click(function () {
        $('.permission_list').addClass('hide');
        $('.add_permission').removeClass('hide');
    })

    $("#sub").click(function () {
        var ii = layer.load();
        //此处用setTimeout演示ajax的回调
        $.ajax({
            url:'/staff/addPermission',// 跳转到 action
            data:$("#addPermission").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    layer.msg(response.info);
                    parent.layer.close(frameindex);
                }else{
                    layer.msg(response.info);
                }
            },
            error : function() {
                layer.msg('服务器出错了……');
            },
            complete : function () {
                layer.close(ii);
            }
        });

    })
    
})