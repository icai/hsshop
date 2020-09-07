$(function(){
	$(".recommend").hide()
	//推荐 按钮
	$(".artical .btns .recom").click(function(){
		$(".recommend").slideToggle("slow");
	});
	
	//确定推荐 
	$(".recommend button").click(function(){
            $.ajax({
                url:'/staff/addRecomment',// 跳转到 action
                data:$("#myForm").serialize(),
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        $(".recommend").slideUp("slow");
                        $(".artical .btns .recom").text("已推荐")
                    }else{
                        tipshow(response.info);
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });

	})

	$("#del").click(function () {
		var id=$('#infoId').val();
        $.ajax({
            url:'/staff/delInfomation',// 跳转到 action
            data:{
                'id':id,
            },
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    window.history.go(-1);
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
})