/**
 * Created by admin on 2017/4/28.
 */
$(function () {
    $(".del").click(function () {
        var id = $(this).data('id');
        var obj = $(this);
        $.ajax({
            url:'/staff/delTemplate/'+id,// 跳转到 action
            data:$("#myForm").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow(response.info);
                    obj.parent().parent().remove();
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    })
})