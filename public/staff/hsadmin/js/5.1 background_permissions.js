$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	//添加
	$(document).on("click", ".main_content .add", function(evt){
		clearEventBubble(evt);
		window.location.href = "5.1.1 添加权限.html"
	})
	//编辑
	$(document).on("click", ".main_content .modify", function(evt){
		clearEventBubble(evt);
		window.location.href = "5.2 店铺权限管理.html?judge=1"
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

	//添加分组
	$('#sub').click(function () {
        $.ajax({
            url:'/staff/addAdminRole',// 跳转到 action
            data:$("#modify_form").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
          		if (response.status == 1){
					tipshow(response.info, 'info');
					setTimeout(function(){
						location.href="/staff/getAdminRole";
					},1000);
				}else{
                    tipshow(response.info);
				}
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });
    })

})