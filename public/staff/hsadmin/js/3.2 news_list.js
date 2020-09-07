$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	});
	//查看详情
    $(".sheet-li-title").on('click',function () {
        var id = $(this).siblings('.sheet-li-id').html()
        window.location.href = "/staff/informationDetal?id="+id;
    })
	//修改
	$(document).on("click", ".main_content .modify", function(evt){
		clearEventBubble(evt);
		window.location.href = "3.1 添加资讯.html?judge=1"
	})
	//推荐
	$(document).on("click", ".main_content .recommend", function(evt){
		clearEventBubble(evt);
		var a_id = $(this).data("id"); 
	})
	//删除
	$(document).on("click", ".main_content .del", function(evt){
		clearEventBubble(evt);
		var obj = $(this);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/delInfomation',// 跳转到 action
                data:{
                    'id':obj.attr('id'),
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
		showDelProver($(this), success,"你确定要删除吗？", true, 1,8);
	});

$("#one").on('change',function () {
	var id = $(this).val();
    if ( id ) {
        var secdata = categoryData[id];
        var sec = '<option value="">二级分类</option>';
        if(typeof (secdata) != 'undefined'){
            for ( var i = 0; i < secdata.length; i++ ) {
                sec +='<option value="' + secdata[i]['id'] + '">' + secdata[i]['name'] + '</option>';
            }
        }
        $('#sec').html(sec);
    }else{
        $('#sec').html('<option value="">二级分类</option>');
    }
    $('#three').html('<option value="">三级分类</option>');
})

	$('#sec').on('change',function () {
        var id = $(this).val();
        if ( id ) {
            var threedata = categoryData[id];
            var three = '<option value="">三级分类</option>';
            if (typeof (threedata) != 'undefined'){
                for (var i = 0; i < threedata.length; i++) {
                    three +='<option value="' + threedata[i]['id'] + '">' + threedata[i]['name'] + '</option>';
                }
            }

            $('#three').html(three);
        } else {
            $('#three').html('<option value="">三级分类</option>');
        }
    })

$("#reset").click(function () {
    $("#one option:first").attr("selected",true).siblings("option").attr("selected",false);
    $("#sec").html('<option value="">二级分类</option>')
    $("#three").html('<option value="">三级分类</option>')
	$("#myForm :input").attr('value','');
})
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
                        "type":'information',
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