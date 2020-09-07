// add by 黄新琴 2018-8-7
$(function(){
    var kamName = '',file='',canLoad=false,type = $('.J_radio:checked').val();
    // 时间选择
    // var start = {
    //     elem: '#beginTime',
    //     format: 'YYYY-MM-DD hh:mm:ss',
    //     min: laydate.now(), //设定最小日期为当前日期
    //     max: '2099-06-16 23:59:59', //最大日期
    //     istime: true,
    //     istoday: false,
    //     choose: function(datas){
    //         $('#beginTime').val(datas);
    //         $('#beginTime').focus();
    //         $('#beginTime').blur();
    //         end.min = datas; //开始日选好后，重置结束日的最小日期
    //         end.start = datas //将结束日的初始值设定为开始日
    //     }
    // };
    // var end = {
    //     elem: '#endTime',
    //     format: 'YYYY-MM-DD hh:mm:ss',
    //     min: laydate.now(),
    //     max: '2099-06-16 23:59:59',
    //     istime: true,
    //     istoday: false,
    //     choose: function(datas){
    //         $('#endTime').val(datas);
    //         $('#endTime').focus();
    //         $('#endTime').blur();
    //         start.max = datas; //结束日选好后，重置开始日的最大日期
    //     }
    // };
    // laydate(start);
    // laydate(end);

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

    // $('.J_radio').change(function(){
    //     type = $(this).val();
    // });
    // 取消按钮
    $('.J_cancel-btn').click(function(){
        window.history.back();
    });
    // 确定按钮
    $('.J_submit-btn').click(function(){
        var title = $('.J_title').val();
        // var begin_time = $('#beginTime').val(),
        //     end_time = $('#endTime').val(),
        var remark = $('.remark').val();
        if (title=='') {
            tipshow('请输入发卡密名称','warn');
            return;
        }
        if (id ==0 && file=='') {
            tipshow('请上传卡密','warn');
            return;
        }
        // if (begin_time=='') {
        //     tipshow('请选择生效时间','warn');
        //     return;
        // }
        // if (end_time=='') {
        //     tipshow('请选择过期时间','warn');
        //     return;
        // }
        if (remark=='') {
            tipshow('请输入使用说明','warn');
            return;
        }
        var data = {
            title: title,
            type: type,
            file: file,
            remark: remark,
            _token: $('input[type="hidden"]').val(),
            id:id
        };
        $.ajax({
            url: '/merchants/cam/save',
            type: 'POST',
            data: data,
            success:function(res) {
                if (res.status == 1) {
                    tipshow('发卡密添加成功');
                    window.location.href = '/merchants/cam/list';
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