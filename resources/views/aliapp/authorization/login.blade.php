<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <style type="text/css">
        .container{
            min-height:auto;
        }
        .login_header,.aliapp_loading,.login_info{
            text-align:center;
        }
        .login_header{
            padding:60px 0 20px 0;
        }
        .login_header img{
            width:60px;
        }
        .login_info{
            color:#999;
        }
        .aliapp_loading img{
            width:45px;
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <div class="content no-sidebar">
            <div class="login_header">
                <img src="{{ config('app.source_url') }}shop/images/aliapp_login_header.png">
            </div>
            <div class="login_info">正在加载中</div>
            <div class="aliapp_loading">
                <img src="{{ config('app.source_url') }}shop/images/aliapp_login.gif">
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    <script type="text/javascript">
        var fromUrl = "{{request('fromUrl')}}";
        var type = "{{request('type')}}";
        my.postMessage({isLogin:1,fromUrl:fromUrl,type:type});
    </script>
</body>
</html>
