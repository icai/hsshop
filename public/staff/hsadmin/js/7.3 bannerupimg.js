$(function () {
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
                    '<img class="littleImg" src="/'+response.data.path+'" width="800" height="200"/>'+
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

});