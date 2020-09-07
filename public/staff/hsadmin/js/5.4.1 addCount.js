$(function(){
	//确定按钮
	$(".sure").click(function(){
        var loginName = $("#loginName").val();
		var loginPasswd = $("#loginPasswd").val();
		var loginPasswd_confirmation = $("#loginPasswd_confirmation").val();
		var name = $("#name").val();
		if (loginName && loginPasswd && loginPasswd_confirmation && name != "") {
            $.ajax({
                url:'/staff/addUser',// 跳转到 action
                data:$('.form-horizontal').serialize(),
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tipshow("添加成功！", "info", 1000)
                        window.location.href='/staff/account';
                    }else{
                        tipshow(response.info);
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });
		}else{
			tipshow("信息未填写完整！", "warn", 1000)
		}
	})
	//取消按钮
	$(".cancel").click(function(){
	})



})