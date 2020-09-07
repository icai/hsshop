$(function(){
    // 按钮样式
    $('.switch_items').click(function(event){
        $(this).find('label').addClass('loadding');
        var _this = $(this);
        setTimeout(function(){
            _this.find('label').removeClass('loadding');
        },80);
        event.stopPropagation();    //  阻止事件冒泡
		
    });
    
    
    //点击出现隐藏内容；
    $(".title").each(function(index, ele){
    	$(this).click(function(){
			$(this).css("border-bottom", "1px solid #ccc");
    		$(this).next().toggle();
    		if ($(this).next().css("display") != "none") {
	    		$(this).find(".rowImg").css({"transform": "rotate(180deg)", "transitionDuration": "0.2s"})
    		}else{
    			$(this).css("border-bottom", "1px solid transparent");
    			$(this).find(".rowImg").css({"transform": "rotate(0deg)", "transitionDuration": "0.2s"})
    		}
    	})
    });
    
    
	$(".board").css({width: $(window).width()+"px",
					height: $(window).height()+"px",})
    //微信支付-修改点击事件；
    $("a[href='##_change']").on("click", function(){
    	$(".board").removeClass("hide");
    	$(".weixin_Layer").removeClass("hide");
    	
    	//关闭门板和弹出层；
    	$("#title_right").click(function(){
    		$(".board").addClass("hide");
    	    $(".weixin_Layer").addClass("hide");
    	})
    	
    	//微信店铺未绑定认证服务号的提示弹出层
    	$("#weixinDeploy").click(function(){
    		$(".approveLayer").removeClass("hide");
    		setTimeout(function(){
//	    		$(".approveLayer").animate({"opacity": 1});
//	    		$(".approveLayer").animate({"opacity": 0}, 5000);
	    		$(".approveLayer").addClass("hide");
    		}, 3000);
    	})
    })
    $('form').submit(function() {
        $.post('',$(this).serialize(),function(data){
            if(data.status == 1){
                tipshow(data.info,'info');
            }else{
                tipshow(data.info,'warn');
            }
        })
      return false;
    });

    // 获取小程序appid
    $.get('/merchants/xcx/config/query',function(data){
        if(data.code == 40000){
            if(data.list.app_id != undefined){
                $('input[name="appId"]').val(data.list.app_id);
                $('input[name="appSecret"]').val(data.list.app_secret);
                $('input[name="merchantNo"]').val(data.list.merchant_no);
                $('input[name="appPaySecret"]').val(data.list.app_pay_secret);
                $('input[name="merchantName"]').val(data.list.merchant_name);
            }
        }
    })

    //添加小程序配置
    $('.save_miniCode').click(function(){
        var merchantName = $('input[name="merchantName"]').val();
        var appId = $('input[name="appId"]').val();
        var appSecret = $('input[name="appSecret"]').val();
        var merchantNo = $('input[name="merchantNo"]').val();
        var appPaySecret = $('input[name="appPaySecret"]').val();
        var _token = $('meta[name="csrf-token"]').attr('content');
            if(merchantName == ''){
                tipshow('商户名称不能为空','warn');
                return;
            }else if(appId == ''){
                tipshow('应用appid不能为空','warn');
                return;
            }else if(appSecret == ''){
                tipshow('应用密钥appSecret不能为空','warn');
                return;
            }else if(merchantNo == ''){
                tipshow('商户号不能为空','warn');
                return;
            }else if(appPaySecret == ''){
                tipshow('API密钥','warn');
                return;
            }
        $.post('/merchants/xcx/config/processData',{merchantName:merchantName,appId:appId,appSecret:appSecret,merchantNo:merchantNo,appPaySecret:appPaySecret,_token:_token},function(data){
            if(data.code == 40000){
                tipshow(data.hint);
            }
        })
    })
})