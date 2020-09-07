<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or '' }}</title>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 表单验证插件 -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/js/bootstrapvalidator/dist/css/bootstrapValidator.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/set.css">
</head>
<body>
    <div class="contenter">
        <div class="wrapper-app">
            <div id="header">
                <div class="header-title-wrap clearfix">
                    <div class="account">
                        <span style="color: #000">{{ session('userInfo')['mphone'] }}</span>-
                        <a href="{{ URL('/home/index/detail/1') }}">帮助</a>-
                        <a href="{{ URL('/auth/loginout') }}">退出</a>
                    </div>
                    <a href="/">
                        <div class="header-logo"></div>
                    </a>
                    <div class="header-title">个人账号设置</div>
                </div>
            </div>
        </div>
        <div class="set">
            <div class="set_group">
                <div class="set_left">会搜云账号：</div>
                <div class="set_right">
                    <span>{{ session('userInfo')['mphone'] }}</span>
                    <a class="changePsd" href="{{ URL('/auth/changepsd') }}">修改密码</a>
                </div>
            </div>
            <div class="set_group">
                <div class="set_left">昵称：</div>
                <div class="set_right">
                   <input type="text" name="name" value="{{ session('userInfo')['name'] }}">
                </div>
            </div>
            <div class="set_group">
                <div class="set_left">头像：</div>
                <div class="set_right">
                    @if ( session('userInfo')['head_pic'] )
                    <img class="logo" src="{{ imgUrl(session('userInfo')['head_pic']) }}">
                    <input type="hidden" id="logo" value="{{ session('userInfo')['head_pic'] }}">
                    @else
                    <img class="logo" src="{{ config('app.source_url') }}home/image/huisouyun_120.png">
                    <input type="hidden" id="logo" value="home/image/huisouyun_120.png">
                    @endif
                    <form id="uploadForm" enctype="multipart/form-data">
                        <a class="alter" href="javascript:void(0);" id="logoChange">修改 <input type="file" name="file" id="files" accept="image/jpeg,image/gif,image/png"></a>
                    </form>
                </div>
            </div>
            <div class="btn_group">
                <button class="btn btn-primary">确认修改</button>
                <a href="{{ URL('/merchants/team') }}">返回店铺后台</a>
            </div>
        </div>
    </div>

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 公共js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/set.js"></script>
</body> 
</html> 