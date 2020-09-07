$(function(){
	//显示图片缩略图
	var urlVal;
    $(".filepath").on("change",function() {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        urlVal = srcs;
        $(this).parents(".imgDiv").find("img").attr("src",srcs);    //this指的是input
        $(this).val("");    //必须制空
    });
	
	//提交
	$(".saveup").click(function(){
		$.ajax({
	        url:'/staff/banner/save',
	        data:$('#saveform').serialize(),
	        type:'post',
	        cache:false,
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
	        dataType:'json',
	        success:function (res) {
	        	if(res.status==1){
	        		tipshow(res.info,'info');
	        		window.location.href='/staff/banner/index';
	        	}else{
	        		tipshow(res.info,'warn');
	        	}
	        },
	        error : function() {
	            alert("数据访问异常");
	        }
	   })	
	});
	
	//重置表单
	$(".clear-form").click(function(){
		$('.clearint').val("");	
		$(".imgurl").text("");
	});
	
})
