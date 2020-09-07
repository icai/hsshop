$(function(){
    var $mask=$('.import-data-mask');
    var $dialog=$('.lead-dialog');
    var $syndata = $('.syndata-dialog')
    // 点击导入其他案例
    $('.lead-data').click(function(){
        $mask.show();
        $dialog.show();
    });
    // 关闭导入弹窗
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
        var k = 1024, // or 1024
            sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            i = Math.floor(Math.log(bytes) / Math.log(k));
        return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
    }
    function readFile(){
        var file = this.files[0];
        var fileExtension = file.name.substring(file.name.indexOf('.'))
        if(file == undefined){
            console.log(111)
            $('.fileName').hide();
            canLoad = false;
            $('.J_sure-btn').addClass('disabled');
            return;
        }
        if((fileExtension != '.csv') && (fileExtension != '.xls') &&(fileExtension != '.xlsx') ){
            tipshow('请选择一个csv、xls文件','warn');
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
                    url: '/staff/weixin/caseFile_up',
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
                            if (file=='') {
                                tipshow('请上传数据','warn');
                                return;
                            }
                            var data = {
                                file_path:file,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            };
                            $.ajax({
                                url: '/staff/weixin/case_import',
                                type: 'POST',
                                data: data,
                                success:function(res) {
                                    if (res.status == 1) {
                                        tipshow(res.info,'info');
                                        $mask.hide();
                                        $dialog.hide();
                                        window.location.href = '/staff/weixin/case_list';
                                    } else {
                                        tipshow(res.info,'warn');
                                        $mask.hide();
                                        $dialog.hide();
                                    }
                                },
                                error:function(res){
                                }
                            })
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
    //下载模板
    $('.download').click(function(){
        window.location.href="/staff/weixin/case_downTemp";
    });
    // 点击同步会搜云
    $('.syn-data').click(function(){
        $('.import-data-mask').show();
        $('.syndata-dialog').show();
    })
    // 确定
    $('.syndata-dialog-sure').click(function(){
            $.ajax({
                url:'/staff/sync/weixin_case',
                data:{},
                type:'get',
                cache:false,
                dataType:'json',
                success:function (res) {
                    console.log(res)
                    if(res.errCode == 0){
                        tipshow(res.msg,"info")
                        $mask.hide();
                        $syndata.hide();
                    }else{
                        tipshow(res.msg,"warn");
                        $mask.hide();
                        $syndata.hide();
                    }
                },
                error : function() {
                    tipshow("异常！");
                }
            });
    })
    // 导入数据取消
    $('.lead-dialog-cancle').click(function(){
         $mask.hide();
         $dialog.hide();
    })
    // 同步数据取消
    $('.syndata-dialog-cancle').click(function(){
        $mask.hide();
        $('.syndata-dialog').hide();
    })
    
})