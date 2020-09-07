$(function(){	
	var button = $('#btnUp');
    new AjaxUpload(button, {
        action: '/staff/fileUpload',
        name: 'file',
        data: {'_token':$('meta[name="csrf-token"]').attr('content')},
        onSubmit: function (file, ext) {
            button.text('上传中');

            interval = window.setInterval(function () {
                var text = button.text();
                if (text.length < 10) {
                    button.text(text + '');
                } else {
                    button.text('上传中');
                }
            }, 200);
        },
        onComplete: function (file, response) {
            //file 本地文件名称，response 服务器端传回的信息
            window.clearInterval(interval);
            response =JSON.parse(response);
            if (response.status == 1){
                button.text('更改上传图片');
                $('.imgGroup').empty();
                $('.imgGroup').prepend('<div class="img_item">'+
                    '<img class="littleImg" src="/'+response.data.path+'" width="400" height="400"/>'+
                    '<img class="delImg" data-id="'+response.data.path+'" src="'+imgUrl+'/hsadmin/images/guanbi@3x.png"/>'+
                    '</div>');
                $('#img').val(response.data.path);
            }else{

            }
            $("body").on('click','.delImg',function(){
            	$('.imgGroup').empty();
            	$('#img').val("");
            	button.text('选择上传图片');
            })

        }
    });
	
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
	        url:'/staff/banner/adSave',
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
	        		window.location.href='/staff/banner/ad';
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
