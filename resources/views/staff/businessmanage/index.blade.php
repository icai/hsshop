@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/1 index.css" />
@endsection
@section('content')
    <div class="main_index">
        <div class="login_info">
            <img src="/mctsource/images/merchants_logo.png"/>
            <p>总后台管理员 {{Session::get('userData')['name']}} 你好！</p>
            <p>第{{Session::get('userData')['logins']}}次登录</p>
            <p>本次登陆时间{{Session::get('userData')['login_time']}}</p>
            <p>上次登陆时间{{Session::get('userData')['last_login_time']}}</p>
            <a class="btn btn-primary operate-log" href="/staff/operateLog">查看日志</a>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/1 index.js" type="text/javascript" charset="utf-8"></script>
@endsection