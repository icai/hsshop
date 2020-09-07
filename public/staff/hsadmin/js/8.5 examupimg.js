$(function () {
    var button = $('#btnUpa');
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
            response =JSON.parse(response)
            var src = $('#source').val();
            if (response.status == 1){
                button.text('更改上传图片');
                $('.imgGroupa').empty();
                $('.imgGroupa').prepend('<div class="img_item img_itema">'+
                    '<img class="littleImg" src="/'+response.data.path+'" width="100" height="100"/>'+
                    '<img class="delImg delImg1" data-id="'+response.data.id+'" src="'+src+'/images/guanbi@3x.png"/>'+
                    '</div>');
                $('.absolutea').val(response.data.path);
            }else{

            }
            $("body").on('click','.delImg1',function(){
            	$('.img_itema').empty();
            	$('.absolutea').val("");
            	button.text('选择上传图片');
            })

        }
    });

	var button = $('#btnUpb');
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
            response =JSON.parse(response)
            var src = $('#source').val();
            if (response.status == 1){
                button.text('添加图片');
                $('.imgGroupb').prepend('<div class="img_item">'+
                    '<img class="littleImg" src="/'+response.data.path+'" width="100" height="100"/>'+
                    '<img class="delImg delImga" data-id="'+response.data.path+'" src="'+src+'/images/guanbi@2x.png"/>'+
                    '</div>');
                attachment.push(response.data.path);
                var test = attachment.join(';');
                $('#attachmenta').val(test);
                if(attachment.length>=3){
	            	tipshow('最多上传三张图片',"warn");
	            	$("#btnUpb").hide();
	            	return false;
	            }
            };

        }
    });

    var button = $('#btnUpc');
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
            response =JSON.parse(response)
            var src = $('#source').val();
            if (response.status == 1){
                button.text('更改上传图片');
                $('.imgGroupc').empty();
                $('.imgGroupc').prepend('<div class="img_item img_itemc">'+
                    '<img class="littleImg" src="/'+response.data.path+'" width="100" height="100"/>'+
                    '<img class="delImg delImgc" data-id="'+response.data.id+'" src="'+src+'/images/guanbi@2x.png"/>'+
                    '</div>');
                $('.absolutec').val(response.data.path);
            }else{

            }
            $("body").on('click','.delImgc',function(){
                $('.img_itemc').empty();
                $('.absolutec').val("");
                button.text('选择上传图片');
            })

        }
    });
		
});