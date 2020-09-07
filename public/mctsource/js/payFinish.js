(function(window){
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
    var state = GetRequest().state
    if(state == 1){
        // 支付成功 支付宝 微信
        $('.state2').hide()
    }else if(state == 2){
        // 汇款 审核
        $('.state1').hide()
    }
    var id = GetRequest().oId;
    if(id){
        $.ajax({
            url:'/merchants/fee/order/select/one',
            data:{id:id},
            success:function(res){
                if(res.errCode == 0){
                    $('.wid').text(res.data.widName)
                    $('.getPrice').text(res.data.products_amount)
                    $('.serviceTime').text(res.data.serviceTime)
                    $('.serviceVersion').text(res.data.serviceVersion)
                }
            }
        })
    }
    
  
})(window)