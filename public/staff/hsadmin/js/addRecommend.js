$(function(){
	//推荐

	$('#sub').click(function () {
        $.ajax({
            url:'/staff/addRecommend',// 跳转到 action
            data:$("#modify_form").serialize(),
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

    //删除
    $(document).on("click", ".main_content .del", function(evt){
        clearEventBubble(evt);
        var delEle = $(this).parents(".table_body");
        var success = function(){
            delEle.remove();
            tipshow("删除成功！", "info", 1000)
        };
        showDelProver($(this), success,"你确定要删除吗？", true, 1,8);
    });

})