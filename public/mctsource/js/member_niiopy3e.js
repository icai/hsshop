$(function(){
//  未选择文件验证
    $("form .btn-primary").click(function(){
    	if($("#add_file").val() == ""){
    		tipshow('请选择文件');
    		return false;  //清除默认
    	}else{
    		readFile();
    	} 	
    });
    //获取文件大仙
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
            $('.add_file').show();
            $('.fileName').hide();
            return;
        }
      
        if(file.size>1024*1024*10){
            tipshow('请选择一个小于10M的文件！');
            return;
        }

        $('.file_name').html(file.name);
        $('.file_size').html(bytesToSize(file.size));
        $('.fileName').show();
        $('.add_file').hide();
    }
    $('#defaultForm').bootstrapValidator({
        message: '填写的值不合法',
        feedbackIcons: {
//          valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            add_file: {
                validators: {
                    notEmpty: {
                        message: '请选择文件'
                    }
                }
            }
        }
    });
})