$(function () {
    //add role
    $("#add").click(function () {
        $.ajax({
            url:'/staff/addRole',// 跳转到 action
            data:$("#addRole").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow(response.info);
                    window.location.reload();
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