$(function () {
    var id = null
    /*
    * @auther 邓钊
    * @desc 显示停止活动弹框
    * @date 2018-7-18
    * */
    $(".stop_activity").on('click',function () {
        id = $(this).attr('data-id');
        $(".tip_stop").show()
    })
    /*
    * @auther 邓钊
    * @desc 关闭停止活动弹框
    * @date 2018-7-18
    * */
    $(".close_btn_stop").on('click',function () {
        $(".tip_stop").hide()
    })
    /*
     * @auther 邓钊
     * @desc 确定停止活动
     * @date 2018-7-18
     * */
    $(".submit_stop_btn").on('click',function () {
        $.ajax({
            url:'/merchants/marketing/bonus/stop/' + id,
            type:'post',
            data:{
                _token:$('meta[name="csrf-token"]').attr('content'),
            },
            success: function(res){
                if(res.status===1){
                    $(".tip_stop").hide()
                    tipshow('活动停止成功','info');
                    setTimeout(function () {
                        location.reload();
                    },1500)
                }else{
                    tipshow('活动停止失败','warn');
                }
            },
            error:function(){
                alert('数据访问异常')
            }
        })
    })
    /*
    * @auther 邓钊
    * @desc 显示删除活动弹框
    * @date 2018-7-18
    * */
    $(".del_activity").on('click',function () {
        id = $(this).attr('data-id');
        $(".tip_del").show()
    })
    /*
    * @auther 邓钊
    * @desc 关闭删除活动弹框
    * @date 2018-7-18
    * */
    $(".close_btn_del").on('click',function () {
        $(".tip_del").hide()
    })
    /*
    * @auther 邓钊
    * @desc 确定删除活动
    * @date 2018-7-18
    * */
    $(".submit_del_btn").on('click',function () {
        $.ajax({
            url:'/merchants/marketing/bonus/delete/' + id,
            type:'post',
            data:{
                _token:$('meta[name="csrf-token"]').attr('content'),
            },
            success: function(res){
                if(res.status===1){
                    $(".tip_del").hide()
                    tipshow('活动删除成功','info');
                    setTimeout(function () {
                        location.reload();
                    },1500)
                }else{
                    tipshow('活动删除失败','warn');
                }
            },
            error:function(){
                alert('数据访问异常')
            }
        })
    })

    /*
    * @auther 邓钊
    * @desc 输入框获得焦点时宽度变长
    * @date 2018-7-18
    * */
    $("#search_txt").on("focus",function () {
        $(this).animate({
            'width':'207px'
        },250,"linear")
    })
    /*
    * @auther 邓钊
    * @desc 输入框失去焦点时 进行搜索
    * @date 2018-7-18
    * */
    $("#search_txt").on('blur',function () {
        var val = $(this).val();
        $(this).animate({
            'width':'97px'
        },250,"linear")
        search(val)
    })
    /*
    * @auther 邓钊
    * @desc 敲击回车键 进行搜索
    * @date 2018-7-18
    * */
    $(document).on('keydown',function (e) {
        if(e.keyCode == 13){
            var val = $('#search_txt').val();
            search(val)
        }
    })
    /*
    * @auther 邓钊
    * @desc 活动搜索
    * @date 2018-7-18
    * */
    function search(txt) {
        if(txt){
            window.location.href = '/merchants/marketing/bonus/index?title=' + txt
        }else{
            window.location.href = '/merchants/marketing/bonus/index'
        }
    }

    /*
    * @auther 邓钊
    * @desc 标题提示文字
    * @date 2018-7-23
    * */
    $(".th_tip_span").on('mouseenter',function () {
        $(this).siblings('.th_tip_div').show()
    })
    $(".th_tip_span").on('mouseleave',function () {
        $(this).siblings('.th_tip_div').hide()
    })
	/*
    * @add by 韩瑜
    * @desc 点击推广
    * @date 2018-8-6
    * */
    $('body').on('click','.share_activity',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var url = '';
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(".widget-promotion1").css({"top":top-130,"left":left-590});
        $(".widget-promotion1").show();
        //获取二维码微商城
        $.ajax({
            url:"/merchants/marketing/bonus/qrCode",
            type:"get",
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    $(".qrcode-right-sidebar .qr_img").html(res.data);
                }
            }
        });
        //获取小程序二维码
        $.ajax({
            url:"/merchants/marketing/bonus/qrCodeXcx",
            type:"get",
            dataType:"json",
            success:function(res){
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
//              		$('.xcx_code').hide();
//              		$('#onlywcs').removeClass('wsc_code').addClass('onlywsc')
                	}
                }
            }
        });    	    	
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
        window.location.href= '/merchants/marketing/bonus/qrCodeDownload';
    });
    //下载小程序二维码
    $('.down_qrcode_xcx').click(function(){
        window.location.href= '/merchants/marketing/bonus/qrCodeDownloadXcx';
    });
    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.widget-promotion');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
            $(".widget-promotion").hide();
        }
    });
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