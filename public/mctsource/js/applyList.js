// add by 黄新琴  2018/10/9

//点击推广start
var wsc_url = '',lit_url = '',id=0;
$('body').on('click','.js-ads',function(e){
    e.stopPropagation(); //阻止事件冒泡
    id = $(this).attr("data-id");
    wsc_url = $(this).attr('data-url');
    lit_url = 'pages/main/pages/distribution/distribution?id=' + id;
    var top = $(this).offset().top;
    var left = $(this).offset().left;
    $(".widget-promotion1").css({"top":top-130,"left":left-590});
    $(".widget-promotion-content .link_url_wsc").val(wsc_url);

    //获取二维码微商城
    $.ajax({
        url:"/merchants/distribute/qrCode?url=" + wsc_url,
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
        url:"/merchants/shareEvent/getMinAppQRCode",
        type:"get",
        data:{url:lit_url},
        dataType:"json",
        success:function(res){
            if(res.status==1){
                var html = '<img src="data:image/png;base64,'+res.data+'" />';
                $(".qrcode-right-sidebar .qr_img_xcx").html(html);
                
            } else {//无小程序
                $("body").on("click",".xcx_code",function(){
                    $('.js-tab-content-wsc').css('display','block')
                    $('.js-tab-content-xcx').css('display','none')
                    $(this).removeClass('active');
                    $('.wsc_code').addClass('active');
                    tipshow('该店铺未开通小程序','warn');
                });
            }
        }
    });
    //设置复制链接
    $(".widget-promotion-content .link_url_xcx").val(lit_url);
    $(".widget-promotion1").show();
     
});

// 复制链接
$('body').on('click','.code-copy-a',function(e){
    e.stopPropagation(); //阻止事件冒泡
    var obj = $(this).siblings('input');
    copyToClipboard( obj );
    tipshow('复制成功','info'); 
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

//点击空白处隐藏弹出层
$('body').click(function(event){
    var _con = $('.widget-promotion');   // 设置目标区域
    if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
        $(".widget-promotion").hide();
        $('.wsc_code').addClass('active').siblings().removeClass('active');
    }
});
// 点击推广end

// 删除
$('body').on('click', '.js-del', function(e){
    e.stopPropagation();
    var id = $(this).data("id");
    var that = $(this);
    showDelProver($(this), function(){
        $.ajax({
            url:'/merchants/distribute/delApplyList',
            data:{ids:id},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status==1){
                    tipshow(res.info);  
                    that.parent().parent().remove();
                }else{
                   tipshow(res.info,"wran"); 
                }
            },
            error:function(){
                tipshow("异常","wran");
            }
        });  
    }, '确定要删除吗?');
   
})

// 全选
$('.js-checkall').change(function(){
    if($(this).is(':checked')){
        $(this).prop("checked",true); 
        $('.js-checkbtn').prop("checked",true);
    }else{
        $(this).prop("checked",false);   
        $('.js-checkbtn').prop("checked",false);
    }
})
$('.js-checkbtn').change(function(){
    if ($(this).is(':checked')){
        if ($('.js-checkbtn').length == $('.js-checkbtn:checked').length){
            $('.js-checkall').prop('checked',true);
        } else {
            $('.js-checkall').prop('checked',false);
        }
    } else {
        $('.js-checkall').prop('checked',false);
    }
});

// 批量删除
$('.js-delAll').click(function(e){
    e.stopPropagation();
    if(!$('.js-checkbtn').is(':checked')){
        tipshow('请先选择要删除的页面！','warn')
        return;
    }
    var checks = $('.js-checkbtn'),ids = [];
    for (var i=0;i<checks.length; i++){
        if (checks[i].checked) {
            ids.push(+$(checks[i]).val());
        }
    }
    ids = ids.toString();
    showDelProver($(this), function(){
        $.ajax({
            url:'/merchants/distribute/delApplyList',
            data:{ids:ids},
            type:"get",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status==1){
                    tipshow(res.info);
                    window.location.reload();
                }else{
                   tipshow(res.info,"wran"); 
                }
            },
            error:function(){
                tipshow("异常","wran");
            }
        });  
    }, '确定要删除吗?',true,2,20);
});