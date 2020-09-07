$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	//新增分类
	$("#myModal .sub").click(function(){
  		var inp_1 = $(this).parents(".modal-content").find(".classify").val();
  		if(inp_1!=""){
	  		tipshow("提交成功！", "info", 1000)
	  		$('#myModal').modal('hide')
  		}else{
  			tipshow("内容不可空！", "warn", 1000)
  		}
	});
	//select的分类显示
	$("#myModal .form-horizontal .form-group").eq(1).hide();
	$("#myModal .firstClass").change(function(){
		var select = $(this).find("option:selected").text();
		if (select=="二级分类") {
			$("#myModal .form-horizontal .form-group").eq(1).show()
		}else{
			$("#myModal .form-horizontal .form-group").eq(1).hide()
		}
	});
	//修改信息
	$(document).on("click",".main_content .modify", function(){
		window.location.href = "3.3.1 添加分类.html?judge=11"
	});
	
	//删除
	$(document).on("click", ".main_content .del", function(evt){
		var obj = $(this);
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/delInfoType',// 跳转到 action
                data:{'id':obj.attr('id')},
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
                    // view("异常！");
                    tipshow("异常！");
                }
            });

		};
		showDelProver($(this), success,"你确定要删除吗？", true, 1, 7);
	});
    var sheetFlag = true
    var token = $("meta[name='csrf-token']").attr('content')
    $(".sheet-li").on('click',function () {
        if(sheetFlag){
            var id = $(this).siblings('.sheet-li-id').html()
            sheetFlag = false
            var txt = $(this).html();
            var html = "<input class='sheet-li-input' type='number' value='"+txt+"'/>"
            $(this).html(html);
            $('.sheet-li-input').focus()
            var _this = this
            $('.sheet-li-input').on('blur',function () {
                var val = $(this).val()
                $.ajax({
                    url:"/staff/saveInformationSort",
                    data:{
                        'id':id,
                        "type":'infortype',
                        "sort":val,
                        "_token":token
                    },
                    type:'post',
                    success:function (data) {
                        if(data.status == 1){
                            location.reload()
                        }else{
                            tipshow("异常！");
                            sheetFlag = true
                        }
                    },
                    error:function () {
                        tipshow("异常！");
                        sheetFlag = true
                    }
                })
                $(_this).html(val)
            })
        }
    })
})



