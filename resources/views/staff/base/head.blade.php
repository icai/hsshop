<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}/home/image/icon_logo.png">
    <title>{{$title}}</title>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}/staff/hsadmin/css/base.css">
    <!--主要内容的css样式文件-->
    @yield('head.css')
</head>

<body>
<!--顶部大标题部分-->
<div class="title_div flex-between">
    <!-- logo 开始 -->
    <div class="logo">
        <a class="logo_items" href="#">
            <img src="{{ config('app.source_url') }}/mctsource/images/merchants_logo.png" width="80" height="80" />
        </a>
    </div>
    <!-- logo 结束 -->
    <div class="title">
        微商城系统后台管理系统
        <span>we are the everything</span>
    </div>
    <div class="user_msg flex-star">
        <div class="userName">{{Session::get('userData')['login_name']}}</div>
        <div class="verLine">|</div>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/staff/logout'">退出</button>
    </div>
</div>
@yield('slidebar')
<!-- 中间 开始 -->
<div class="middle">
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 普通导航 开始 -->
            <ul class="common_nav">
                <li @if($title=='首页') class="hover"@endif>
                    <a href="/staff/index">首页</a>
                </li>
                @forelse($__menu__ as $key=>$item)
                    <li @if($title == $key) class="hover"@endif>
                    <a href="{{$item['name']['url']}}">{{$item['name']['name']}}</a>
                    </li>
                 @endforeach
            </ul>
            <!-- 普通导航 结束  -->
        </div>
        <!-- 三级导航 结束 -->
    </div>
    <!-- 主体 开始 -->
 @yield('content')
    <!-- 主体 结束 -->
</div>
<!-- 中间 结束 -->
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
<!-- 核心 base.js JavaScript 文件 -->
<script src="{{ config('app.source_url') }}staff/hsadmin/js/base.js"></script>
<!--layer文件引入-->
<script src="{{ config('app.source_url') }}staff/static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    var host = "{{ config('app.url') }}";

    //导航栏的宽度动态设置值
    var navWidth = 0;
    $(".common_nav li").each(function(index,ele){
    	var itemWidth = $(this)["0"].offsetWidth;
    	navWidth += itemWidth;
    })
    $(".common_nav").css("width", navWidth+35);
</script>
<!--less文件引入-->
<!--主要内容的JS-->
@yield('foot.js')

</body>

</html>