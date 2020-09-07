$(function(){
	//删除
	$(document).on("click", ".main_content .del", function(evt){
        var obj =  $(this).parent();
        var id = obj.data('id');
        var phone = obj.data("phone")
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/seller/del',// 跳转到 action
                data:{
                	id    : id,
                	mobile: phone
                },
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        delEle.remove();
                        tipshow("删除成功！", "info", 1000)
                        setTimeout(function () {
                            location.reload()
                        },1000)
                    }else{
                        tipshow(response.info);
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });

		};
		showDelProver($(this), success,"你确定要删除吗？", true, 1, 7);
	});
})
