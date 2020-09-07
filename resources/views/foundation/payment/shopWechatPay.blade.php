<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta content="telephone=no" name="format-detection" />
    <link type="text/css" rel="stylesheet" href="http://fe.huisouimg.com/weixin/tuan/css/style.css" />
    <title>微信安全支付</title>
    <style>
        .ml{margin-left:10px;}
        .mr{margin-right:10px;}
        .pay_body{background:#f0f0f0;}
        .pay_header{text-align:center;font-size:18px;line-height:40px;text-align:center;}
        .pay_price{font-size:22px;line-height:36px;}
        .pay_imme a,.pay_imme input{background:#63B908;display:block;border-radius:5px;font-size:16px;height:30px;line-height:30px;text-align:center;color:#fff;}
        .pay_imme input{border:0;}
        .pay_promise{width:100%;position:absolute;left:0;bottom:10px;height:22px;line-height:22px;text-align:center;color:#666;}
        .pay_info{padding:6px;20px;background:#fff;border-top:#ccc solid 1px;border-bottom:#ccc solid 1px;}
        .pay_info_one{width:100%;height:30px;
            display:-webkit-box;
            display:-moz-box;
            display:box;
            -webkit-box-orient:horizontal;
            -moz-box-orient:horizontal;
        }
        .pay_info_name{width:100px;color:#999;font-size: 16px;}
        .pay_info_det{color:#333;-webkit-box-flex:1;-moz-box-flex:1;box-flex:1;height:22px;line-height:22px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size: 16px;}
    </style>
    <script type="text/javascript">
        function onBridgeReady(){
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest', {!! $jsApi !!},
                function(res){   
                    // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
                    if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                        window.location.href = "{{ url('/shop/pay/paySuccess') }}/{{ $detail['id'] }}";
                    }else{
                        window.location.href = "{{ url('/shop/pay/payFail') }}/{{ $detail['id'] }}";
                    }
                }
            ); 
        }

        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        }else{
            onBridgeReady();
        }

        // onBridgeReady();

    </script>
</head>
<body class="pay_body">
    <div class="pay_header">
        <p>微信安全支付</p>
    </div>
    <ul class="pay_info">
        <li class="pay_info_one">
            <div class="pay_info_name">单号</div>
            <div class="pay_info_det">{{ $detail['tradeId'] }}</div>
        </li>
        <li class="pay_info_one">
            <div class="pay_info_name">价钱</div>
            <div class="pay_info_det">￥{{ $detail['payTotal'] }}</div>
        </li>
        <li class="pay_info_one">
            <div class="pay_info_name">商户</div>
            <div class="pay_info_det">{{ $detail['payee'] }}</div>
        </li>
    </ul>
    <div class="pay_imme mt ml mr"><a href="javascript:void(0)" onclick="onBridgeReady()">微&nbsp;信&nbsp;支&nbsp;付</a></div>
</body>
</html>