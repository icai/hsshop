$(function(){
	if($("#add_file").val() == ""){
		$('.btn-csv').attr('disabled',true)
	}
//  未选择文件验证
    $(".btn-csv").click(function(){
    	if($("#add_file").val() == ""){
    		tipshow('请选择文件');
    		return false;  //清除默认
    	}else{
//  		readFile();
//			var data = $("#defaultForm").serialize()
		
    		$.ajax({
			    url: '/merchants/order/BatchDelivery',
			    type: 'POST',
				cache: false,
				data: new FormData($('#defaultForm')[0]),
				processData: false,
				contentType: false,
			    headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
			    success:function(res) {
                    console.log(res)
                    // add by zhaobin 2018-8-31
                    var html = '';
                    if(res.status == 1){
                        $(".error_list").html('');
                        $(".error_record").show();
                        if(res.data.error.length > 0){
                            for(var i=0;i<res.data.error.length;i++){
                                html += '<p class="item_record">业务单号为&nbsp;<i class="num">'+res.data.error[i]+'</i>&nbsp;未发货订单不存在</p>'
                            }
                        }
                        $(".error_list").append(html)
                        $(".success-num span").html(res.data.success.length);
                        $(".error-num span").html(res.data.error.length)
                        $('.btn-csv').attr('disabled',true)
                        $("#add_file").val('') 	//清空文件，防止第二次不能触发change事件
                    }else{
                        tipshow(res.info,'warn')
                    }
			    	//$('#myModal').hide()
                    //tipshow('系统正在处理批量发货申请，请稍后查看订单的发货状态....');
                    //end
				},
				error:function(){
					alert("数据访问错误")
				}
			})  		
    		
        }
        
    });
    $(".js-batch").click(function(){
        $('.fileName').hide();
        $(".error_record").hide()
    })
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
            $('.file-siz').hide();
            $('.fileName').hide();
            return;
        }
        if(file.name.substring(file.name.indexOf('.')) != '.csv'){
            tipshow('请选择一个csv文件')
            return;
        }
        if(file.size>1024*1024*10){
        	$('.file-siz').show();
            tipshow('请选择一个小于10M的文件！');
            return;
        }


        $('.file_name').html(file.name);
        $('.file_size').html(bytesToSize(file.size));
        $('.fileName').show();     
        $('.btn-csv').attr('disabled',false)
   
    }
    //使成团
    $('.be_group').click(function(){
        var id = $(this).data('id');
        $.get('/merchants/order/makeCompleteGroups/' + id ,function(data){
            if(data.status){
                tipshow(data.info);
                setTimeout(function(){
                    window.location.reload();
                },2000)
            }else{
                tipshow(data.info,'warn');
            }
        })
    })
    
})