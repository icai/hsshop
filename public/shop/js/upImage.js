$(function () {
    var button = document.getElementById('btnUp');
    console.log(button);
    new AjaxUpload(button, {
    //    action: 'upload-test.php',
        action: '/shop/order/upfile/'+$('#wid').val(),
        name: 'file',
        data: {'_token':$('meta[name="csrf-token"]').attr('content')},
        onSubmit: function (file, ext) {
            if (!(ext && /^(jpg|jpeg|JPG|JPEG|png)$/.test(ext))) {
                alert('图片格式不正确,请选择 jpg 格式的文件!', '系统提示');
                return false;
            }

            // change button text, when user selects file
            $(button).text('上传中');

            // If you want to allow uploading only 1 file at time,
            // you can disable upload button
            // this.disable();

            // Uploding -> Uploading. -> Uploading...
            interval = window.setInterval(function () {
                var text = $(button).text();
                if (text.length < 10) {
                    $(button).text(text + '');
                } else {
                    $(button).text('上传中');
                }
            }, 200);
        },
        onComplete: function (file, response) {
            //file 本地文件名称，response 服务器端传回的信息
            // button.text('上传图片(只允许上传JPG格式的图片,大小不得大于150K)');

            window.clearInterval(interval);
            response =JSON.parse(response)
            if (response.status == 1){
                var _html = '<div class="img_item relative">';
                    _html +='<div><img src="/'+response.data.s_path+'" width="100%"/></div>';
                    _html +='	<img class="delete absolute" data-id="'+response.data.id+'" src="/shop/images/img_close.png" width="18" height="18" />';
                    _html +='</div>';
                //console.log(getObjectURL(this.files[i]))
                var divhtml = '<input type="hidden" id="ip_'+response.data.id+'" name="img[]" value="'+response.data.id+'" />'
                $('#text').append(divhtml);
                $("#btnUp").parents(".uploaderDiv").before(_html);
            }else{
                alert(response.info);
            }

        }
    });

});