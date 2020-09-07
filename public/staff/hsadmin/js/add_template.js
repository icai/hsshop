$(function () {
    var button = $('#btnUp');
    new AjaxUpload(button, {
        action: '/staff/fileUpload',
        name: 'file',
        data: {'_token':$('meta[name="csrf-token"]').attr('content')},
        onSubmit: function (file, ext) {
            if (!(ext && /^(jpg|jpeg|JPG|JPEG|png)$/.test(ext))) {
                tipshow("图片格式不正确")
                return false;
            }
            // return false;
            // change button text, when user selects file
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
            if (response.status == 1){
                button.attr('src',imgUrl+response.data.s_path);
                $("#img").val(imgUrl+response.data.s_path);
            }else{

            }

        }
    });


    $("#sub").click(function () {
        $.ajax({
            url:'/staff/addTemplate',// 跳转到 action
            data:$("#myForm").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow(response.info);
                    window.location.href='/staff/getTemplate';
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    })

});