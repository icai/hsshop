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
    </style>
</head>
<body>
    <div class="container" id="container">
    </div>
    <script type="text/javascript" src="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan.js"></script>
    <script type="text/javascript">
        swan.webView.navigateTo({url: '/pages/login/login'});
    </script>
</body>
</html>
