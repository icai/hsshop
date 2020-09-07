$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	
	//修改信息
	$(document).on("click",".main_content .star", function(){
		var obj =  $(this).parent();
		var id = obj.data('id');
		if ($(this).text()=="加星") {
			$(this).text('已加星')
			var status = 1;
		}else{
			$(this).text('加星')
			var status=0;
		}

        $.ajax({
            url:'/staff/customer/addStar/'+id+'/'+status,// 跳转到 action
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("操作成功！", "info", 1000)
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
	});
	
	//删除
	$(document).on("click", ".main_content .del", function(evt){
        var obj =  $(this).parent();
        var id = obj.data('id');
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/customer/delete/'+id,// 跳转到 action
                data:{},
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
	//批量加星
    $(".addStar").click(function(){
        var formData  = $(".listForm").serialize();
        if( !formData ){
            tipshow("请先选择要修改的数据");
            return false;
        }
        $.ajax({
            url:'/staff/customer/operate?type=1',
            data: formData,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("批量加星成功", "info", 1000);
                    setTimeout(function(){
                        history.go(0);
                    },1000);
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    });
    //批量删除
    $(".addDelete").click(function(){
        var formData  = $(".listForm").serialize();
        if( !formData ){
            tipshow("请先选择要修改的数据");
            return false;
        }
        $.ajax({
            url:'/staff/customer/operate?type=0',
            data: formData,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("批量删除成功", "info", 1000);
                    setTimeout(function(){
                        history.go(0);
                    },1000);
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    });
    
})




