$(function(){
	$('.dropdown-toggle').dropdown();
	$(".main_content .state").click(function () {
		var obj = $(this);
            $.ajax({
                url:'/staff/openRole',// 跳转到 action
                data:{'roleId':obj.attr('id')},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tipshow(response.info);
                    }else{
                        tipshow(response.info);
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
        })
    })
	
	
	//开启状态
	$(document).on("click", ".main_content .state", function(evt){
		var _text = $(this).text();
		if(_text == "开启"){
			$(this).text("关闭");
			$(this).parents(".table_body").children("li:eq(3)").text("已开启")
		}else{
			$(this).text("开启");
			$(this).parents(".table_body").children("li:eq(3)").text("已关闭")
		}
	});
})