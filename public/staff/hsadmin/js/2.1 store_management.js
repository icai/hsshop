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
        $.ajax({
            url:'/staff/addCategory',// 跳转到 action
            data:$("#add").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("添加成功！", "info", 1000)
                    $('#myModal').modal('hide')
                    window.location.reload();
                }else{
                    tipshow(response.info, "warn", 1000)
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });



    });
    //select的分类显示
    $("#myModal .form-horizontal .form-group").eq(1).hide();
    $("#myModal .firstClass").on('change',function(){
        var select = $(this).find("option:selected").text();
        if (select=="二级分类") {
            $("#myModal .form-horizontal .form-group").eq(1).show()
        }else{
            $("#myModal .form-horizontal .form-group").eq(1).hide()
        }
    });

    //修改信息
    $(document).on("click",".main_content .modify", function(){
        //填写默认信息
        $.ajax({
            url:'/staff/getModifyInfo',// 跳转到 action
            data:{'id':$(this).attr('id') },
            type:'post',
            async: false,
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function (response) {
                $('#modify').html(response);
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });


        if ($("#modify_id option:selected").val() == 2){
            $("#myModal_1 .form-horizontal .form-group").eq(1).hide();
        }

        $("#myModal_1 .firstClass").bind('change',function(){
            var select = $(this).find("option:selected").text();
            if (select=="二级分类") {
                $("#myModal_1 .form-horizontal .form-group").eq(1).show()
            }else{
                $("#myModal_1 .form-horizontal .form-group").eq(1).hide()
            }
        });


        $('#myModal_1').modal('toggle');
        //模态框居中设置
        t=setTimeout(function () {
            var _modal = $('#myModal_1').find(".modal-dialog")
            _modal.css({'margin-top': parseInt(($(window).height() - _modal.height())/3)})
        },0)
    });

    $("#myModal_1 .form-horizontal .form-group").eq(1).hide();
    $("#myModal_1 .firstClass").bind('change',function(){
        tipshow(111);
        var select = $(this).find("option:selected").text();
        if (select=="二级分类") {
            $("#myModal_1 .form-horizontal .form-group").eq(1).show()
        }else{
            $("#myModal_1 .form-horizontal .form-group").eq(1).hide()
        }
    });

    $("#myModal_1 .sub").click(function(){

        $.ajax({
            url:'/staff/addCategory',// 跳转到 action
            data:$("#modify_form").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("修改成功！", "info", 1000)
                    $('#myModal').modal('hide')
                    window.location.reload();
                }else{
                    tipshow(response.info, "warn", 1000)
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });

    });

    //删除
    $(document).on("click", ".main_content .del", function(evt){
    	var obj = $(this);
        var delEle = $(this).parents(".table_body");
        var success = function(){
			//删除分类
            var url= '/staff/delCategory';
            var data = {
                'id':obj.attr('id'),
            };
            $.ajax({
                url:url,// 跳转到 action
                data:data,
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
                        tipshow(response.info, "info", 1000)
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });
        };
        showDelProver($(this), success,"你确定要删除吗？", true, 1, 7);
    });



    $('.js-province').change(function(){
        var dataId = $('.js-province option:selected').val();
        var province = json[dataId];
        var city = "<option value=''>选择城市</option>";
        for(var i = 0;i < province.length;i ++){
            city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
        }
        $('.js-city').html(city);
    });



})



