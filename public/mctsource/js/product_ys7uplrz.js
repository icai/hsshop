    $('.delete').click(function(e){
        e.stopPropagation();//阻止事件冒泡
        var that = $(this);
        showDelProver($(this), function(){
            //执行删除
            $.post('/merchants/product/delgroup', {id: that.data('id'), '_token': $('meta[name="csrf-token"]').attr('content')}, function (data) {
                tipshow("删除成功");
                // alert(data.info);

                window.location.reload();
            })
        }, '确定要删除吗?');
    });


    //点击推广start
    $('body').on('click','.js-link',function(e){
        e.stopPropagation(); //阻止事件冒泡
        id = $(this).attr("data-id");
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        //设置产品id的值
        $(".widget-promotion1").css({"top":top-70,"left":left-376});
        $(".widget-promotion1").show();
        //获取二维码微商城
        $.ajax({
            url:"/merchants/qrCode?url="+ host + "shop/group/detail/"+ wid +"/" +id,
            type:"get",
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    $(".qrcode-right-sidebar .qr_img").html(res.data);
                    $(".widget-promotion-content .link_url_wsc").val(host + "shop/group/detail/"+ wid +"/" +id);
                }
            }
        });
        //获取小程序二维码 qrCodeXcx
        $.ajax({
            url:"/merchants/qrCodeXcx?url=pages/activity/pages/productList/productList?typeId="+ id,
            type:"get",
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    if(res.data.errCode == 0){
                        var html = '<img src="data:image/png;base64,'+res.data.data+'" />';
                        $(".qrcode-right-sidebar .qr_img_xcx").html(html);
                    }else{//无小程序
                        $("body").on("click",".xcx_code",function(){
                            $('.js-tab-content-wsc').css('display','block')
                            $('.js-tab-content-xcx').css('display','none')
                            $(this).removeClass('active');
                            $('.wsc_code').addClass('active');
                            tipshow(res.data.errMsg,'warn');
                        });
                    }
                }
            }
        });
        //设置复制链接
        $(".widget-promotion-content .link_url_xcx").val('pages/activity/pages/productList/productList?typeId='+ id);
    });

    // 复制链接
    $('body').on('click','.code-copy-a',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('input');
        copyToClipboard( obj );
        tipshow('复制成功','info');
    });


    //下载微商城二维码
    $('.down_qrcode_wsc').click(function(){
        window.location.href= "/merchants/qrCodeDownload?url="+ host + "shop/group/detail/"+ wid +"/" +id;
    });

    //下载小程序二维码
    $('.down_qrcode_xcx').click(function(){
        window.location.href= "/merchants/qrCodeDownloadXcx?url=pages/activity/pages/productList/productList?typeId="+ id;
    });

    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.widget-promotion');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){
            $(".widget-promotion").hide();
        }
    });
    // 点击小程序二维码
    $("body").on("click",".xcx_code",function(){
        $('.js-tab-content-wsc').css('display','none')
        $('.js-tab-content-xcx').css('display','block')
        $(this).addClass('active').siblings().removeClass('active');
    });
    // 点击微商城二维码
    $("body").on("click",".wsc_code",function(){
        $('.js-tab-content-wsc').css('display','block')
        $('.js-tab-content-xcx').css('display','none')
        $(this).addClass('active').siblings().removeClass('active');
    });
    // 点击推广end