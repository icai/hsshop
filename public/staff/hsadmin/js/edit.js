$(function(){
        $('.firstcategory').change(function(){
            var secline=data[$(this).val()]

            var op=''
             for(var i=0;i<secline.length;i++){
                 op += '<option  value="'+secline[i].id+'">'+secline[i].title+'</option>'
             }
             $('.seccategory').html(op);
        })
    // 二维码上传 by 崔源 2018.1129
    var $mask=$('.import-data-mask');
    var $dialog=$('.lead-dialog');
    // 点击二维码上传
    $('.img_submit').click(function(){
        $mask.show();
        $dialog.show();
    })
        // 关闭上传弹窗
        $('.dialog-close').click(function(){
            $mask.hide();
            $dialog.hide();
            canLoad = false;
            $('.J_sure-btn').addClass('disabled');
        });
        //获取文件
        var input = document.getElementById('add_file');
        //文件域选择文件时, 执行readFile函数
        input.addEventListener('change',readFile,false);
        function bytesToSize(bytes) {
            if (bytes === 0) return '0 B';
            var k = 3074, 
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                i = Math.floor(Math.log(bytes) / Math.log(k));
            return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
        }
        function readFile(){
            var file = this.files[0];
            var fileExtension = file.name.substring(file.name.indexOf('.'))
            if(file == undefined){
                $('.fileName').hide();
                canLoad = false;
                $('.J_sure-btn').addClass('disabled');
                return;
            }
            if((fileExtension != '.png') && (fileExtension != '.jpg')){
                tipshow('请选择一个png、jgp文件','warn');
                $('.J_sure-btn').addClass('disabled');
                canLoad = false;
                return;
            }
            if(file.size>3074*3074){
                tipshow('请选择一个小于3M的文件！','warn');
                canLoad = false;
                $('.J_sure-btn').addClass('disabled');
                return;
            }
            canLoad = true;
            $('.J_sure-btn').removeClass('disabled');
            kamName = file.name;
            $('.file_name').html(file.name);
            $('.file_size').html(bytesToSize(file.size));
            $('.fileName').show();
        }
        // 上传按钮
        $('.J_sure-btn').click(function(){
            if($("#add_file").val() == ""){
                tipshow('请选择文件','warn');
                return false;
            }else{
                if (canLoad) {
                    console.log(new FormData($('#defaultForm')[0]))
                    $.ajax({
                        url: '/staff/weixin/case_qrcodeUpload',
                        type: 'POST',
                        cache: false,
                        data: new FormData($('#defaultForm')[0]),
                        processData: false, 
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(res) {
                            if (res.errCode!=0){
                                tipshow(res.errMsg,'warn');                               
                            }else {
                                $('.qrcode_img').attr('src',res.data);
                                $('.qrcode_img_hidden').val(res.data)
                            }
                            $('.qrcode_img').show();
                            $mask.hide();
                            $dialog.hide();
                        },
                        error:function(){
                            alert("数据访问错误")
                        }
                    })
                }
            } 	
        })
        // 一二级分类
        $('.sure').click(function(){
            var id = editid;
            var business_id=$('.seccategory').val();
            var business_title=$('.seccategory option:selected').text();//获取选中的项
            var imgurl =$('.qrcode_img_hidden').val();
            if (business_id=='') {
                tipshow('请选择要修改的分类','warn');
                return false;
            }
            $.ajax({
                type:"post",
                url:'/staff/weixin/case_edit',
                data:{
                    id:id,
                    business_id:business_id,
                    title:business_title,
                    qrcode:imgurl
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow(res.info,'info');                          
                        setTimeout(function() {
                            window.location.href="/staff/weixin/case_list"
                        }, 2000);
                    }else{
                        tipshow(res.info,'warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            });	        
          })
})