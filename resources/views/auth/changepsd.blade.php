<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or '' }}  </title>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/changepsd.css">
    
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
                    <div class="header-title">修改密码</div>
                </div>
            </div>
        </div>
        <div class="changepsd">
            <form id="form" class="form-horizontal" role="form" method="post" action="{{ URL('/auth/changepassword/update') }}">
                <div class="form-group">
                    <label for="old_psd" class="col-sm-3 control-label">请输入旧密码：</label>
                    <div class="col-sm-6">
                        <input  class="form-control" type="password" name="old_password" id="old_psd" placeholder="输入旧密码" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="psd" class="col-sm-3 control-label">请输入新密码：</label>
                    <div class="col-sm-6">
                        <input  class="form-control" type="password" name="password" id="psd" placeholder="8~20位字符" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="psdTwo" class="col-sm-3 control-label">重复新密码：</label>
                    <div class="col-sm-6">
                        <input  class="form-control" type="password" name="password_confirmation" id="psdTwo" placeholder="再输一次" />
                    </div>
                </div>
                 <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <input class="btn btn-large btn-primary btn-signup" type="submit" value="确认修改"/>
                        <button class="btn btn-default">返回</button>
                    </div>
                </div>
                {!! csrf_field() !!}
            </form>
        </div>
    </div>

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
    <!-- 表单验证插件 -->
    <script src="{{config('app.source_url')}}/static/js/bootstrapvalidator/dist/js/bootstrapValidator.js"></script>
    <!-- 公共js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/changepsd.js"></script>
</body> 
</html> 