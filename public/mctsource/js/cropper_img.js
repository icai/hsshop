/**
 * 邓钊  201-09-20
 * 基于cropper图片裁剪上传再次封装 cropper版本Cropper v3.1.3  地址https://cdn.bootcss.com/cropper/3.1.3/cropper.js  https://cdn.bootcss.com/cropper/3.1.3/cropper.css
 * 说明
 * 1. 依赖于jquery和cropper
 * 2. 使用该组件之前，要先引入jquery和cropper的js文件和cropper的css文件
 * 3. init()方法 添加图片裁剪框到页面
 * 4. initCropper()  初始化cropper  size参数 图片上传限制大小  aspectRatio 裁剪比例
 * 5. crop() 图片裁剪方法，同时移除页面中的图片裁剪框弹框  参数时一个回调函数，回调函数接收两个参数 blob（图片裁剪之后上传给后端的图片资源） 和 img_file（原始图片资源）
 * 6. show() 显示图片裁剪框弹框
 * 7. close() 关闭图片裁剪框弹框，同时移除页面中的图片裁剪框弹框
 * 注意：因为每次关闭弹框或者裁剪完成，都会移除页面中的图片裁剪框弹框，所以每次在调用这个的时候，都要走一遍初始流程，即init-->show-->initCropper-->crop-->close，
 * 必须先init，才能执行后面四步，且init之后的四步顺序不固定，
 */
(function (win,$) {
    function cutImage() {
        var URL = window.URL || window.webkitURL;
        var html = "    <div class='cropper-box' id='cropper'>" +
            "        <div class='cropper'>" +
            "            <div class='cropper-header flex'>" +
            "                <span class='cropper-header-title'>添加图片</span>" +
            "                <span class='close-cropper close-dalong'></span>" +
            "            </div>" +
            "            <div class='cropper-body flex'>" +
            "                <div class='cropper-tailor'>" +
            "                    <p class='cropper-title'>裁剪操作框</p>" +
            "                    <div class='tailor cropper-hide' id='tailor_a'>" +
            "                        <img src='' id='photo'>" +
            "                    </div>" +
            "                    <div class='tailor' id='tailor_b'></div>" +
            "                </div>" +
            "                <div class='cropper-preview'>" +
            "                    <p class='cropper-title'>裁剪预览框</p>" +
            "                    <div class='preview flex cropper-hide' id='preview_a'>" +
            "                        <div class='img-preview'></div>" +
            "                    </div>" +
            "                    <div class='preview flex' id='preview_b'></div>" +
            "                </div>" +
            "            </div>" +
            "            <div class='cropper-footer flex'>" +
            "                <div class='cropper-select'>" +
            "                    <span>选择图片</span>" +
            "                    <input type='file' accept='image/png, image/jpeg, image/gif, image/jpg' id='input' class='select-only'>" +
            "                </div>" +
            "                <div class='cropper-button'>" +
            "                    <div class='close-button footer-button close-dalong'>取消</div>" +
            "                    <div class='submit-button footer-button' id='tailor'>确认</div>" +
            "                </div>" +
            "            </div>" +
            "        </div>" +
            "    </div>"
        var img_file;
        var img_type;
        var option;
        var cropper;
        this.init = function () {
            $('body').append(html);
        };
        /**
         * @author 邓钊
         * @param size   图片限制大小
         * @param aspectRatio  图片裁剪比例
         * @param uploadFlag  是否需要裁剪选择的图片
         * @param img_src  选择的图片的链接
         * @param FileInfo 选择的图片的属性
         */
        this.initCropper = function (size, aspectRatio, uploadFlag, img_src, FileInfo) {
            var type_size = size * 1024 *1024
            // var $image = $('#photo');
            var options = {
                aspectRatio: aspectRatio || 1, // 纵横比
                viewMode: 2,
                preview: '.img-preview', // 预览图的class名
                autoCropArea: 1,
                cropBoxResizable: false,
                dragMode: 'none',
            };
            var image = document.getElementById('photo');
            cropper = new Cropper(image, options);
            // $image.cropper('destroy')
            // $image.cropper(options);
            var $inputImage = $('#input');
            var uploadedImageURL;
            if (URL) {
                // 给input添加监听
                if(!uploadFlag){
                    $inputImage.change(function () {
                        var files = this.files;
                        var file;
                        if(files && files.size > type_size){
                            tipshow("图片不能超过"+size+"M","warn");
                            return;
                        }
                        if (cropper && files && files.length) {
                            img_file = files[0]
                            img_type = img_file.type
                            file = files[0];
                            if (/^image\/\w+/.test(file.type)) {
                                if (uploadedImageURL) {
                                    URL.revokeObjectURL(uploadedImageURL);
                                }
                                image.src = uploadedImageURL = URL.createObjectURL(file);
                                cropper.destroy();
                                cropper = new Cropper(image, options);
                                $('#tailor_a').removeClass('cropper-hide')
                                $('#preview_a').removeClass('cropper-hide')
                                $('#tailor_b').addClass('cropper-hide')
                                $('#preview_b').addClass('cropper-hide')
                                $inputImage.val('');
                            } else {
                                tipshow("请选择一个图像文件！","warn");
                            }
                        }
                        // if (!$image.data('cropper')) {
                        //     return;
                        // }
                        // if(files && files.size > type_size){
                        //     tipshow("图片不能超过"+size+"M","warn");
                        //     return;
                        // }
                        // if (files && files.length) {
                        //     img_file = this.files[0]
                        //     img_type = img_file.type
                        //     file = files[0];
                        //     // 判断是否是图像文件
                        //     if (/^image\/\w+$/.test(file.type)) {
                        //         // 如果URL已存在就先释放
                        //         if (uploadedImageURL) {
                        //             URL.revokeObjectURL(uploadedImageURL);
                        //         }
                        //         uploadedImageURL = URL.createObjectURL(file);
                        //         // 销毁cropper后更改src属性再重新创建cropper
                        //         $image.cropper('destroy').attr('src', uploadedImageURL).cropper(options);
                        //         $('#tailor_a').removeClass('cropper-hide')
                        //         $('#preview_a').removeClass('cropper-hide')
                        //         $('#tailor_b').addClass('cropper-hide')
                        //         $('#preview_b').addClass('cropper-hide')
                        //         $inputImage.val('');
                        //     } else {
                        //         tipshow("请选择一个图像文件！","warn");
                        //     }
                        // }
                    });
                }else{
                    img_type = FileInfo.type
                    img_file = {
                        name: FileInfo.name
                    }
                    // $image.cropper('destroy').attr('src', img_src).cropper(options);
                    cropper.destroy();
                    image.src = img_src;
                    cropper = new Cropper(image, options);
                    console.log(cropper);
                    $('#tailor_a').removeClass('cropper-hide')
                    $('#preview_a').removeClass('cropper-hide')
                    $('#tailor_b').addClass('cropper-hide')
                    $('#preview_b').addClass('cropper-hide')
                }
            } else {
                $inputImage.prop('disabled', true).addClass('disabled');
            }
        };
        this.crop = function (callback) {
            $('#tailor').on('click', function () {
                // var $image = $('#photo');
                // if(!$image.cropper('getCroppedCanvas')){
                //     tipshow("请选择一个图像文件！","warn");
                //     return false
                // }
                console.log(cropper);
                if(!cropper.getCroppedCanvas()){
                    tipshow("请选择一个图像文件！","warn");
                    return false
                }
                cropper.getCroppedCanvas().toBlob(function(blob){
                    $('#cropper').hide()
                    $('#tailor_a').addClass('cropper-hide')
                    $('#preview_a').addClass('cropper-hide')
                    $('#tailor_b').removeClass('cropper-hide')
                    $('#preview_b').removeClass('cropper-hide')
                    cropper.reset()
                    $('#cropper').remove()
                    if(callback && typeof callback === 'function') {
                        callback(blob, img_file)
                    }
                },img_type);
                // $image.cropper('getCroppedCanvas').toBlob(function(blob){
                //     $('#cropper').hide()
                //     $('#tailor_a').addClass('cropper-hide')
                //     $('#preview_a').addClass('cropper-hide')
                //     $('#tailor_b').removeClass('cropper-hide')
                //     $('#preview_b').removeClass('cropper-hide')
                //     $image.cropper('reset')
                //     $('#cropper').remove()
                //     if(callback && typeof callback === 'function') {
                //         callback(blob, img_file)
                //     }
                // },img_type);
            })
        }
        this.show = function (size, aspectRatio, uploadFlag, img_src, FileInfo) {
            $('#cropper').show()
            this.initCropper(size, aspectRatio, uploadFlag, img_src, FileInfo)
        }
        this.close = function () {
            $('.close-dalong').on('click', function () {
                $('#cropper').hide()
                $('#tailor_a').addClass('cropper-hide')
                $('#preview_a').addClass('cropper-hide')
                $('#tailor_b').removeClass('cropper-hide')
                $('#preview_b').removeClass('cropper-hide')
                cropper.reset()
                $('#cropper').remove()
            })
        }
    }
    win.setCropper = new cutImage()
})(window, jQuery)

/**
 * @auther 邓钊
 * @param size 裁剪图片限制大小
 * @param aspectRatio 裁剪比例
 * @param callback 裁剪成功后的回调函数
 * @param uploadFlag  是否需要裁剪选择的图片
 * @param img_src  选择的图片的链接
 * @param FileInfo 选择的图片的属性
 * @description 调用setCropper组件
 * @update 2019-9-23
 * @return
 */
function getCropper(size, aspectRatio, callback, uploadFlag, img_src,FileInfo) {
    setCropper.init()
    setCropper.show(size, aspectRatio, uploadFlag, img_src, FileInfo)
    setCropper.close()
    setCropper.crop(callback)
}