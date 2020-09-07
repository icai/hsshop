(function(){
    var orderId = GetRequest().orderId;
    $(window).on('load',function(){
        $.ajax({
            url:'/merchants/fee/order/aliPay',
            data:{orderId:orderId},
            type:'post',
            dataType: 'json',
            async:true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.errCode==0){
                    $('body').html(res.data);
                    havePayed()
                }else{
                    tipshow('支付宝链接失败','warn')
                }
            },
            error:function(res){
                tipshow('跳转失败','warn')
            }
        })
    })

    function havePayed(){
        var timer = setInterval(function(){
            $.ajax({
                url:'/merchants/fee/order/select/one',
                data:{id:orderId},
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(res){
                    if(res.errCode == 0){
                        if(res.data.status == 1){
                            tipshow('支付成功')
                            //支付成功
                            var timerL = setTimeout(function(){
                                location.href = host + 'merchants/capital/fee/order/pay/finish?state=1&oId='+orderId;
                                clearTimeout(timerL);
                            },100)
                            clearTimeout(timer);
                        }
                    }
                }
            })
        },3000)
    }

    //获取页面参数
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
})()