$(function(){

    //新增分类
    $("#myModal .sub").click(function(){
        $.ajax({
            url:'/staff/BusinessManage/addRegion',// 跳转到 action
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


    // //修改信息
    // $(document).on("click",".main_content .modify", function() {
    //     //填写默认信息
    //     $.ajax({
    //         url: '/staff/getModifyInfo',// 跳转到 action
    //         data: {'id': $(this).attr('id')},
    //         type: 'post',
    //         async: false,
    //         cache: false,
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function (response) {
    //             $('#modify').html(response);
    //         },
    //         error: function () {
    //             // view("异常！");
    //             tipshow("异常！");
    //         }
    //     }
    //     });


    //
    // $("#myModal_1 .sub").click(function(){
    //
    //     $.ajax({
    //         url:'/staff/BusinessManage/addRegion',// 跳转到 action
    //         data:$("#modify_form").serialize(),
    //         type:'post',
    //         cache:false,
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         dataType:'json',
    //         success:function (response) {
    //             if (response.status == 1){
    //                 tipshow("添加成功！", "info", 1000)
    //                 $('#myModal').modal('hide')
    //                 window.location.reload();
    //             }else{
    //                 tipshow(response.info, "warn", 1000)
    //             }
    //         },
    //         error : function() {
    //             tipshow("异常！");
    //         }
    //     });
    //
    // });

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


    $('.hide_region').click(function () {
        var obj = $(this);
        var id = obj.data('id');
        var status = obj.data('status');
        var html = '';
        $.get('/staff/BusinessManage/hideRegion/'+id,{},function (res) {
            if(res.status == 1){
                if (res.data.status == -2){
                    obj.html('显示');
                }else{
                    obj.html('隐藏');
                }
                tipshow("操作成功！", "info", 1000)
            }else {
                alert(res.info);
            }
        })
    })



})



