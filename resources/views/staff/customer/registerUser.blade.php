@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.4.1 addCount.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-注册会员</span>
                <a href="/staff/openPermission" type="button" class="btn btn-primary openPermission">一键开通权限</a>
            </div>
            <div class="main_content">
                <!-- <div class="sorts">
                    <a href="/staff/userlist">帐号列表</a>
                    <span class="verLine">|</span>
                    <a href="/staff/registerUser" style="color: #333;">新增用户信息</a>
                </div> -->
                <form class="form-horizontal" method="post" action="/staff/registerUser">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="number" class="col-sm-2 control-label">手机：</label>
                        <div class="col-sm-3">
                            <input type="text" name="mphone" class="form-control"  id="loginName" placeholder="请输入手机号码" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="userName" class="col-sm-2 control-label">用户名称：</label>
                        <div class="col-sm-3">
                            <input type="text" name="name" class="form-control" id="name" placeholder="请输用户名" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pwd" class="col-sm-2 control-label">密码：</label>
                        <div class="col-sm-3">
                            <input type="password" name="password" class="form-control" id="loginPasswd" placeholder="请输入8-18位字母数字" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pwd" class="col-sm-2 control-label">确认密码：</label>
                        <div class="col-sm-3">
                            <input type="password" name="password_confirmation" class="form-control" id="loginPasswd_confirmation" placeholder="请输入8-18位字母数字" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber" class="col-sm-2 control-label"></label>
                        <div class="col-sm-3">
                            <input type="submit" value="确定" class="btn btn-primary sure">
                        </div>
                    </div>
                </form>

            </div>
        </div>
@endsection
@section('foot.js')
        <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
        <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
@endsection