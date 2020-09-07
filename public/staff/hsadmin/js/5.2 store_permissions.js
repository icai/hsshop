$(function(){
	var judge=window.location.search.slice(window.location.search.lastIndexOf("id"));
	if (judge == 1) {
		$(".first_nav li:eq(1)").removeClass("hover");
		$(".first_nav li:eq(0)").addClass("hover");
		$(".main_content .sorts a, .sorts span").text("");
		$(".main_content .sorts .addNewClassify").text("编辑权限");
	}
	
	//权限列表  添加权限的转换
	show_hide(".add_permission_ele", ".add_permission", ".permission_list")
	show_hide(".permission_menu_ele", ".permission_list", ".add_permission")
	
	function show_hide(clickEle, showEle, hideEle){
		$(clickEle).click(function(){
			$(showEle).removeClass("hide");
			$(hideEle).addClass("hide");
		});
	}

	//modify role permission
	$("#save").click(function () {
        $.ajax({
            url:'/staff/bindAdminRolePermission',// 跳转到 action
            data:$("#myForm").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow(response.info, 'info');
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    })
	//add permission
    $("#sub").click(function () {
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
                    tipshow(response.info, 'info');
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

    //modify front role permission
    $("#bind").click(function () {
        $.ajax({
            url:'/staff/bindRolePermission',// 跳转到 action
            data:$("#bindRole").serialize(),
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
                // view("异常！");
                tipshow("异常！");
            }
        });
    })

    //删除权限
    $(".delete_permission").click(function () {
        var id = $(this).data('id');
        $.ajax({
            url:'/staff/deletePermission',
            data:{ids: [id]},
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
                        window.location.reload();
                    },1000);
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