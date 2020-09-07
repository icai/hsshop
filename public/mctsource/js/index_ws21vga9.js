$(function () {
    $('.authentication1').hover( hoverDom('.authentication'), leaveDom('.authentication'))
    $('.market').hover( hoverDom('.market1'), leaveDom('.market1'))
    $('.vip').hover( hoverDom('.vip1'), leaveDom('.vip1'))
    $('.iph').hover( hoverDom('.iphone-none'), leaveDom('.iphone-none'))
    $('.pc').hover( hoverDom('.pc-none'), leaveDom('.pc-none'))
    $('.J_download-ercode').hover(function(){
        $('.er-popup').addClass('er-show');
    },function(){
        $('.er-popup').removeClass('er-show');
    });

    function hoverDom(dom) {
        $(dom).css("display", "inline-block")
    }
    function leaveDom(dom) {
        $(dom).hide();
    } 
    calc();
    window.onresize=function(){
        calc();
    }
    function calc(){
        var height = $(".swiper").width() / 1.28;
    }
    $("img.lazy").lazyload({effect: "fadeIn"});
    
    //ty 2017-10-20   店铺逾期不可点击
    $(".overdue_item").on("click", function(){
		
        tipshow("店铺已打烊，无法操作！",'info')

    })
    $('.widget-feature-template .close_model').click(function(){
        $('.widget-feature-template').hide();
        $('.modal-backdrop').hide();
    })
    // 免费升级
    $('.model_btn .model_btn1').click(function(){
        $.get('/merchants/store/shopUpgrade',{},function(data){
            if(data.status == 1){
                tipshow(data.info);
                setTimeout(function(){
                    window.location.reload();
                },2000)
            }else{
                tipshow(data.info,'warn');
            }
        })
    })
    $('.model_bg .close_mode_bg').click(function(){
        $.get('/merchants/store/closeFrame',{},function(){});
        $('.model_bg').hide();
        $('.modal-backdrop').hide();
    })
    $('.index_image').click(function(){
        $('.model_bg').show();
        $('#modal_backdrop').show();
    })
    $('.especially img').click(function(){
        $('.model_bg').show();
        $('#modal_backdrop').show();
    })
    // 续费数据的获取
    function getRenewContent(){
        $.ajax({
            url:'/merchants/fee/selfProduct/select/all',
            type:'get',
            dataType:'json',
            success:function(res){
                // console.log(res,'this is flag')
            }
        })
    }
    getRenewContent()

    /** 
     * author 华亢
     * created 2018/7/6
     * toDo 点击续费出现续费弹框
    */
    $('.renew-wrap .head').click(function(e){
        // showRenewPop(e)
    })
    $('.header-tip').click(function(e){
        $('.header').addClass('hide')
    })
    /** 
     * author 华亢
     * created 2018/7/6
     * toDo 续费的事件函数
    */
    function showRenewPop(e){
        e.stopPropagation();
        var t_index = layer.open({
            type: 1,
            title:"续费店铺：精选店铺  续费服务：微商城<span style='color:red'>（基础版）</span> 1年",
            closeBtn:false, 
            move: false,
            shadeClose:true,
            skin:"layer-tskin",
            area: ['626px', '640px'],
            content: $('#renew-pop')
        });
        /*取消订单关闭按钮*/
        $("body").on("click",".layui-layer-setwin",function(e){
            closePop(e,t_index)
        });
        $("body").on("click",".renew-close",function(e){
            closePop(e,t_index)
        });
    }
    /** 
     * author 华亢
     * created 2018/7/6
     * toDo 关闭弹框
    */
    function closePop(e,t_index){
        e.stopPropagation();
        if(t_index)
            layer.close(t_index);
    }

    function getServiceContent(id){
        $.ajax({
            url:'/merchants/fee/selfProduct/select/one',
            data:{id:id},
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                    
                }
            }
        })
    }
})