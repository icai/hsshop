<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}/home/image/icon_logo.png">
    <title>微商城后台登陆</title>
    <link type="text/css" href="{{ config('app.source_url') }}staff/hsadmin/css/0 login.css" rel="stylesheet" />
</head>
<body>
<div id="container">
    <div class="logo">
        <a href="#" id="logoImg">
            <img src="{{ config('app.source_url') }}/mctsource/images/merchants_logo.png"  height="100" alt="" />
            <span>微商城总后台管理系统</span>
        </a>
    </div>
    <div id="box">
        <h4 class="alert_error">登录失败</h4>
        <h4 class="alert_success">登录成功</h4>
        <form method="POST" id="myForm">
            <div class="admin_info">
                <label for="userName">用户名: <input name="loginName" id="userName" value="" /></label>
                <br/><br/>
                <label for="pwd">&nbsp;&nbsp;&nbsp;密码: <input type="password" name="loginPasswd" id="pwd" value=""></label>
            </div>
            <br />
            <div class="login">
                <input id="sub" type="button" value="登陆" />
            </div>
        </form>
    </div>
</div>
</body>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js" type="text/javascript" language="javascript"></script>
<script src="{{ config('app.source_url') }}staff/hsadmin/js/0 login.js" type="text/javascript" charset="utf-8"></script>
</html>
