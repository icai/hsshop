$(function(){

	var Lin_js_error = "";
	var Lin_js_success = "";
	$(document).ready(function(){
		if(Lin_js_error) {
			$(".alert_error").html(Lin_js_error);
			$(".alert_error").show();
			$(".alert_error").animate({width: "95%", height: 16}, "slow");
		}
		if(Lin_js_success) {
			$(".alert_success").html(Lin_js_success);
			$(".alert_success").show();
			$(".alert_success").animate({width: "95%", height: 16}, "slow");
		}
	});
	$("#sub").click(function () {
        login()
    });
    $(document).keydown(function (e) {
        if(e.keyCode==13){
            login()
        }
    })
    function login() {
        $.ajax({
            url:'/staff/login',// 跳转到 action
            data:$("#myForm").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    window.location.href=response.url;
                }else{
                    var Lin_js_error = response.info;
                    $(".alert_error").html(Lin_js_error);
                    $(".alert_error").show();
                    $(".alert_error").animate({width: "95%", height: 16}, "slow");
                }
            },
            error : function() {
                tipshow("异常");
            }
        });
    }
})