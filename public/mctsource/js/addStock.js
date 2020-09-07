$(function(){
    var canLoad = false,kamName='',file = '';
    var $mask=$('.J_mask');
    var $dialog=$('.kam-dialog');
    // 上传卡密
    $('.upload-kam').click(function(){
        $mask.show();
        $dialog.show();
    });
    // 关闭卡密弹窗
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
        var k = 1000, // or 1024
            sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            i = Math.floor(Math.log(bytes) / Math.log(k));
        return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
    }
    function readFile(){
        var file = this.files[0];
        if(file == undefined){
            $('.fileName').hide();
            canLoad = false;
            $('.J_sure-btn').addClass('disabled');
            return;
        }
        if(file.name.substring(file.name.indexOf('.')) != '.csv'){
            tipshow('请选择一个csv文件','warn');
            $('.J_sure-btn').addClass('disabled');
            canLoad = false;
            return;
        }
        if(file.size>1024*1024){
            tipshow('请选择一个小于1M的文件！','warn');
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
                $.ajax({
                    url: '/merchants/cam/upExcel',
                    type: 'POST',
                    cache: false,
                    data: new FormData($('#defaultForm')[0]),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(res) {
                        if (res.errCode==0){
                            file = res.data;
                            $('.kam-name').html(kamName).show();
                            $mask.hide();
                            $dialog.hide();
                        } else{
                            tipshow(res.errMsg,'warn');
                        }
                    },
                    error:function(){
                        alert("数据访问错误")
                    }
                })
            }
            
    	} 	
    })

     // 取消按钮
     $('.J_cancel-btn').click(function(){
        window.history.back();
    });
    // 确定按钮
    $('.J_submit-btn').click(function(){
        if (file=='') {
            tipshow('请上传卡密','warn');
            return;
        }
        var data = {
            file: file,
            id: id,
            _token: $('input[type="hidden"]').val()
        };
        $.ajax({
            url: '/merchants/cam/doAddStock',
            type: 'POST',
            data: data,
            success:function(res) {
                if (res.status == 1) {
                    tipshow('库存添加成功');
                    window.location.href = '/merchants/cam/camStockList?id=' + id;
                } else {
                    tipshow(res.info,'warn');
                }
            },
            error:function(res){
                console.log(res)
            }
        })
        
    });

    //下载模板
    $('.download').click(function(){
        window.location.href="/merchants/cam/downExcel";
    });
})