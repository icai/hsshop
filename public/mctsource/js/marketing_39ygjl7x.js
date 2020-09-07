$(function(){
    //删除列表 
    $(".pagecat-del").click(function(e){
        e.stopPropagation();//阻止事件冒泡
        var id = $(this).data("id");
        showDelProver($(this), function(){
            //执行删除
            $.get('/merchants/marketing/delWheel/'+id,function (res) {
                if(res.status==1){
                    tipshow("删除成功");
                    window.location.reload();
                }else{
                    tipshow(res.info,'warn');
                } 
            })
        }, '确定要删除吗?');
    });
    
	//点击推广
	//update by 韩瑜 2018-8-16
    $('body').on('click','.link_btn',function(e){
        e.stopPropagation(); //阻止事件冒泡
        id = $(this).attr("data-id");
        var url = $(this).attr('data-url');
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(".widget-promotion1").css({"top":top-130,"left":left-590});
        $(".widget-promotion1").show();
        //获取二维码微商城
        $.ajax({
            url:"/merchants/marketing/wheelQrCode/" + id,
            type:"get",
            data:{},
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    $(".qrcode-right-sidebar .qr_img").html(res.data);
                }
            }
        });
        //获取小程序二维码
        $.ajax({
            url:"/merchants/marketing/wheelQrCodeXcx/" + id,
            type:"get",
            data:{},
            dataType:"json",
            success:function(res){
            	console.log(res)
                if(res.status==1){
                	if(res.data.errCode == 0 && res.data.data){
                		var xcximg = '<img src="data:image/png;base64,'+res.data.data+'" />'
                		$(".qrcode-right-sidebar .qr_img_xcx").html(xcximg);
                	}else{//无小程序
                		$("body").on("click",".xcx_code",function(){
					    	$('.js-tab-content-wsc').css('display','block')
					    	$('.js-tab-content-xcx').css('display','none')
					        $(this).removeClass('active');
					        $('.wsc_code').addClass('active');
					        tipshow('该店铺未开通小程序','warn');
					    });
                	}
                }
            }
        });
        //设置复制链接
        $(".widget-promotion-content .link_url_wsc").val(url);
    });
	
    // 复制微商城链接
    $('body').on('click','.js-btn-copy-wsc',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.link_url_wsc');
        copyToClipboard( obj );
        tipshow('复制成功','info'); 
    });
    
    // 复制小程序链接
    $('body').on('click','.js-btn-copy-xcx',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.link_url_xcx');
        copyToClipboard( obj );
        tipshow('复制成功','info'); 
    });

    //下载微商城二维码
    $('.down_qrcode_wsc').click(function(){
        window.location.href= '/merchants/marketing/wheelQrCodeDownload/' + id;
    });
    
    //下载小程序二维码
    $('.down_qrcode_xcx').click(function(){
        window.location.href= '/merchants/marketing/wheelQrCodeXcxDownload/' + id;
    });
    
    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.widget-promotion');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
            $(".widget-promotion").hide();
        }
    });
    /* ------------- 推官二维码功能代码结束 -------------*/
    // 点击小程序优惠券二维码
    $("body").on("click",".xcx_code",function(){
    	$('.js-tab-content-wsc').css('display','none')
    	$('.js-tab-content-xcx').css('display','block')
        $(this).addClass('active').siblings().removeClass('active');
    });
    // 点击微商城优惠券二维码
    $("body").on("click",".wsc_code",function(){
    	$('.js-tab-content-wsc').css('display','block')
    	$('.js-tab-content-xcx').css('display','none')
    	$(this).addClass('active').siblings().removeClass('active');
    });
    //end
})