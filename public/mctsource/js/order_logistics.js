$(function(){
    //打开参数配置弹窗
    $('.params-set').click(function(){
        $.get('/merchants/order/printOrderParams',{},function(data){
            if(data.status == 0){
                tipshow('获取快递100配置参数失败！','warn');
                return;
            }
            console.log(data)
            $('#kuaidi_app_id').val(data.data.data.kuaidi_app_id);
            $('#kuaidi_app_secret').val(data.data.data.kuaidi_app_secret);
            $('#kuaidi_app_uid').val(data.data.data.kuaidi_app_uid);
            $('#params-set').show();
        });
    });
    //关闭参数配置弹窗
    $('.close').click(function(){
        $('#params-set').hide();
    });
    //取消按钮
    $('.js-cancel').click(function(){
        $('#params-set').hide();
    });
    //保存按钮
    //author 韩瑜  date 2018.6.29
    $('.js-confirm').click(function(){
	    var print_type = $('#print_type').val();
	    var kuaidi_app_id = $('#kuaidi_app_id').val();
	    var kuaidi_app_secret = $('#kuaidi_app_secret').val();
	    var kuaidi_app_uid = $('#kuaidi_app_uid').val();
		$.ajax({
			type : "post",
			url : "/merchants/order/printOrderParams",
			data : {
				"print_type":print_type,
				"kuaidi_app_id":kuaidi_app_id,
				"kuaidi_app_secret":kuaidi_app_secret,
				"kuaidi_app_uid":kuaidi_app_uid,
			},
			async: false,
			dataType : "json",
			headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
			success : function(data) {
				if(data.status == 1){
					tipshow('快递管家参数设置成功','info');
					$('#params-set').hide();
				}
				if(data.status == 0){
					tipshow('请填写完整信息！','info');
				}
			},
			error:function(){
				tipshow('快递管家参数设置异常','wran');
			}
		});
    });

    $('.code-copy-a').click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.int-cody-a');
        copyToClipboard( obj );
        tipshow('复制成功','info');
    });
    
    
});
