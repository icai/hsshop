$(function () {
    var button = $('#btnUp');
    new AjaxUpload(button, {
    //    action: 'upload-test.php',
        action: '/staff/fileUpload',
        name: 'file',
        data: {'_token':$('meta[name="csrf-token"]').attr('content')},
        onSubmit: function (file, ext) {
            if (!(ext && /^(jpg|jpeg|JPG|JPEG|png)$/.test(ext))) {
                tipshow("图片格式不正确")
                return false;
            }
            // return false;
            if (attachment.length>5){
                tipshow('最多只能传递6张图片')
                return false;
            }

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
            // button.text('上传图片(只允许上传JPG格式的图片,大小不得大于150K)');

            window.clearInterval(interval);

            response =JSON.parse(response)
            var src = $('#source').val();
            if (response.status == 1){
                button.text('添加图片');
                $('.imgGroup').prepend('<div class="img_item">'+
                    '<img class="littleImg" src="/'+response.data.path+'" width="100" height="100"/>'+
                    '<img class="delImg" data-id="'+response.data.id+'" src="'+src+'/images/guanbi@2x.png"/>'+
                    '</div>');
                attachment.push(response.data.id);

                var test = attachment.join(',');
                $('#attachment').attr('value',test);
            }else{

            }

        }
    });

});