(function(window){
    $(".pay-way-content input").eq(0).prop('checked',true)
    // step1: 获取页面参数
    function GetRequest() {
        var url = location.search; //获取url中"?"符后的字串
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            var strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
            }
        }
        return theRequest;
    }
    (function(){
        var id = GetRequest().orderId;
        if( typeof id == 'string' && id != ''){
            $.ajax({
                url:'/merchants/fee/order/select/one',
                data:{id:id},
                success:function(res){
                    if(res.errCode == 0){
                        $('.wid').text(res.data.widName)
                        $('.getPrice').text(res.data.products_amount)
                        $('.serviceVersion').text(res.data.serviceVersion)
                    }
                },error:function(err){
                    tipshow('支付失败！刷新页面','warn')
                }
            })
        }else{
            tipshow('请按照常规流程正常付款','warn')
        }
    })()

    /** 
     * author 华亢
     * created 2018/7/13
     * params 支付方式 -> 1 支付宝,2 微信,3 汇款
     * toDo: 点击不用支付方式 内容改变
    */
    changePayComtent(2)//默认微信
    function changePayComtent(state){
        $('.payment').css({'display':'none'})
        $('.payment').eq(state-1).css({'display':'block'})
        var urlParams = {};
        urlParams.orderId = GetRequest().orderId;
        var str = ''
        if(state == 1){
            //选择支付宝支付
            $('.remit-CS').hide()
            str = 'zhifubao'
            location.href=host+"merchants/capital/fee/aliPay/page?orderId="+urlParams.orderId
            console.log('zhifubao')
        }else if(state == 2){
            // 选择微信支付
            $('.remit-CS').hide()
            str = 'weixin'
            urlParams.url = '/merchants/fee/order/wechatPay'
            console.log('weixin')
            getPay(str,urlParams)
        }else{
            //汇款支付
            $('.remit-CS').show()
        }
    }

    $('.pay-way-content input').click(function(){
        changePayComtent($(this).attr('value'))
    })
    
    // 支付宝、微信
    function getPay(str,obj){
        $.ajax({
            url:obj.url,
            data:{orderId:obj.orderId},
            type:'post',
            dataType: 'json',
            async:true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.errCode == 0){
                    if(str == 'weixin'){
                        $('.wechatQRcode').html(res.data)
                        havePayed()
                    }
                }else{
                    tipshow('二维码获取失败','warn')
                }
            }
        })
    }
    var orderId = GetRequest().orderId;
    function havePayed(){
        var timer = setInterval(function(){
            $.ajax({
                url:'/merchants/fee/order/select/one',
                data:{id:orderId},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                success:function(res){
                    if(res.errCode == 0){
                        if(res.data.status == 1){
                            tipshow('支付成功')
                            //支付成功
                            var timerL = setTimeout(function(){
                                location.href = host + 'merchants/capital/fee/order/pay/finish?state=1&oId='+orderId;
                                clearTimeout(timerL);
                            },500)
                            clearTimeout(timer);
                        }else if(res.data.status == 3){
                            tipshow('支付失败','warn')
                        }
                    }else{
                        tipshow(res.errMsg,'warn')
                    }
                }
            })
        },1000)
    }

    // 获取汇款账号信息
    function getBillConfig(){
        $.ajax({
            url:'/merchants/fee/order/remit/config',
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                   $('.receiveCompany').text(res.data.receiveCompany) 
                    $('.receiveBank').text(res.data.receiveBank)
                    $('.receiveAccount').text(res.data.receiveAccount)
                }
            }
        })
    }
    getBillConfig()
    // 汇款提交按钮
    $('.remit-submit button').click(function(){
        $.ajax({
            url:'/merchants/fee/order/remitPay',
            data:{orderId:GetRequest().orderId},
            type:'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.errCode == 0){
                    location.href = host + 'merchants/capital/fee/order/pay/finish?state=2&oId='+GetRequest().orderId
                }else{
                    tipshow(res.errMsg,'warn')
                }
            }
        })
    })
})(window)