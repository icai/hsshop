$(function(){
    //模态框
    $('.modal').on('shown.bs.modal', function (e) { 
        // 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
        $(this).css('display', 'block'); 
        var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
        if(modalHeight < 0){
            modalHeight = 0;
        }
        $(this).find('.modal-dialog').css({ 
            'margin-top': modalHeight 
        }); 
    });
    $("#addXCX_model .btn-default").click(function(){
        $(".xcx_add").val("");
        return false;
    });
    $("#addXCX_model .btn-primary").click(function(){
        $.ajax({
            url:'/staff/customer/liteappAdd',
            data:{
                title: $(".xcx_add").val() 
            },
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("添加成功", "info", 2000);
                    $('#addXCX_model').modal('hide')
                    $(".modal-backdrop").hide();
                    setTimeout(function(){
                        history.go(0);
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
    });
    $(".addHistory").click(function(){
        //编辑案例轮播
        $.get("/staff/customer/liteappHistory",function(res){
            if(res.status == 1){
                $("#addData").modal("show");
                if(res.data.length>0){
                    $(".dataList").empty();
                    for(var i = 0;i < res.data.length;i ++){
                        var _html = '';
                        _html += '<li class="item">'
                        _html += '<div class="itemLeft">'
                        _html += '<span>用户： </span>'
                        _html += '<input class="prev" type="number" oninput="if(value.length>3)value=value.slice(0,3)" value='+res.data[i].phone.slice(0,3)+'>'
                        _html += '<span> **** </span>'
                        _html += '<input class="next" type="number" oninput="if(value.length>4)value=value.slice(0,4)" value='+res.data[i].phone.slice(7,11)+'>'
                        _html += '</div>'
                        _html += '<div class="itemRight">'
                        _html += '<span>小程序： </span>'
                        _html += '<input class="title" type="text" maxlength="4" value='+res.data[i].title+'>'
                        _html += '<span> **** </span>'
                        _html += '</div>'
                        _html += '</li>';
                        $(".dataList").append(_html);
                    }
                }
            }
        })
        
    })
    //重置案例
    $("#addData .btn-default").click(function(){
        $(".prev").val("");
        $(".next").val("");
        $(".title").val("");
        return false;
    })
    //添加案例
    $("#addData .add").click(function(){
        var _html = '';
        _html += '<li class="item">'
        _html += '<div class="itemLeft">'
        _html += '<span>用户： </span>'
        _html += '<input class="prev" type="number" oninput="if(value.length>3)value=value.slice(0,3)">'
        _html += '<span> **** </span>'
        _html += '<input class="next" type="number" oninput="if(value.length>4)value=value.slice(0,4)">'
        _html += '</div>'
        _html += '<div class="itemRight">'
        _html += '<span>小程序： </span>'
        _html += '<input class="title" type="text" maxlength="4">'
        _html += '<span> **** </span>'
        _html += '</div>'
        _html += '</li>';
        $(".dataList").append(_html);
    })
    //案例提交
    $("#addData .btn-primary").click(function(){
        var phoneArr = [];
        var titleArr = [];
        var br = true;
        $(".dataList .item").each(function(){
            //排除每行个别为空
            if(!$(this).find(".prev").val() || !$(this).find(".next").val() || !$(this).find(".title").val()){
                if(!(!$(this).find(".prev").val() && !$(this).find(".next").val() && !$(this).find(".title").val())){
                    br = false;
                    return false;
                }
            }
            //排除每行为空
            if(!$(this).find(".prev").val()){
                return;
            }
            //排除位数不够
            if($(this).find(".prev").val().length<3){
                br = false;
                return false;
            }
            if($(this).find(".next").val().length<4){
                br = false;
                return false;
            }
            var phoneStr = $(this).find(".prev").val() + "****" + $(this).find(".next").val();
            var titleStr = $(this).find(".title").val();
            phoneArr.push(phoneStr);
            titleArr.push(titleStr);
        });
        if(!phoneArr || !br){
            tipshow("请填写完整数据","warn");
            return false;
        }
        $.ajax({
            url:'/staff/customer/liteappHistoryAdd',
            data:{
                phone: phoneArr, 
                title: titleArr
            },
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (res) {
                if(res.status == 0){
                    tipshow("请填写完整数据","warn");
                }else if(res.status == 1){
                    tipshow("新增案例完成","info");
                    $(".modal").modal("hide");
                    $(".modal-backdrop").hide();
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    });
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	
	//删除
	$(document).on("click", ".main_content .del", function(evt){
        var obj =  $(this).parent();
        var arr = [];
        var id = obj.data('id');
        arr.push(id);
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/customer/liteappDelete',
                data:{
                    ids:arr 
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
                    tipshow("异常！");
                }
            });

		};
		showDelProver($(this), success,"你确定要删除吗？", true, 1, 7);
	});
	
    //批量删除
    $(".addDelete").click(function(){
        var formData  = $(".listForm").serialize();
        if( !formData ){
            tipshow("请先选择要修改的数据");
            return false;
        }
        $.ajax({
            url:'/staff/customer/liteappDelete',
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
    })
})




