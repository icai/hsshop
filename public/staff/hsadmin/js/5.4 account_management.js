$(function(){
	//启用
	$(document).on("click", ".toggtl", function(e){
		var text = e.target.innerText
		text == "启用" ? $(this).text("已启用"):$(this).text("启用")
	})
	
	//修改信息
	$(document).on("click",".main_content .modify", function(){
		window.location.href = "5.4.1 新增帐号.html"
	});
	
	//删除
	$(document).on("click", ".main_content .del", function(evt){
		var obj = $(this);
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/delAccount',// 跳转到 action
                data:{'id':obj.data('placement')},
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


	$(".permission").click(function () {
	    var id = $(this).data('placement');
        layer.open({
            type: 2,
            title: '权限管理',
            maxmin: true,
            shadeClose: true, //点击遮罩关闭层
            area : ['800px' , '520px'],
            content: '/staff/permission/staffPermission?id='+id
        });
    })

    
	
})



