<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_location.css">
</head>
<body>
    <div class="destination-contaiuner">
        <div class="map-img"></div>
        <div class="J_go-destination go-destination">查看线路</div>
    </div>

    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>

    <script type="text/javascript">
        var longitude=0,latitude=0; 
        function getLocation() {
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {  
                        longitude = position.coords.longitude;  
                        latitude = position.coords.latitude;
                    },
                    function (e) {
                            var msg = e.code;
                            var dd = e.message;
                            console.log(msg);
                            console.log(dd);
                    }
                ) 
            }
        }
        getLocation();
        $('.J_go-destination').click(function(){
            var olat = '{{ $olat }}';
            var olng = '{{ $olng }}';
            window.location = '/shop/order/detailMap?lat='+latitude+'&lng='+longitude+'&olat='+olat+'&olng='+olng;
        });

    </script>
</body>
</html>