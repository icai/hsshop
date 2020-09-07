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
                <span>权限管理-账号管理</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="/staff/account">帐号列表</a>
                    <span class="verLine">|</span>
                    <a href="/staff/addUser" style="color: #333;">新增用户信息</a>
                </div>
                <form class="form-horizontal">
                    @if(!empty($account))
                        <input type="hidden" name="id" value="{{$account['id']}}" />
                        @endif
                    <div class="form-group">
                        <label for="accuntName" class="col-sm-2 control-label">帐号：</label>
                        <div class="col-sm-3">
                            <input type="text" name="loginName"  @if(!empty($account)) disabled @endif class="form-control"  id="loginName" placeholder="请输入帐号名称" value="@if(!empty($account)){{$account['login_name']}}@endif">
                            @if(!empty($account)) <input type="hidden" name="loginName" class="form-control"  id="loginName" placeholder="请输入帐号名称" value="@if(!empty($account)){{$account['login_name']}}@endif"> @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pwd" class="col-sm-2 control-label">密码：</label>
                        <div class="col-sm-3">
                            <input type="password" name="loginPasswd" class="form-control" id="loginPasswd" placeholder="请输入8-18位包含大小写字母数字特殊符号" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pwd" class="col-sm-2 control-label">确认密码：</label>
                        <div class="col-sm-3">
                            <input type="password" name="loginPasswd_confirmation" class="form-control" id="loginPasswd_confirmation" placeholder="请输入6-18位字母数字" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="userName" class="col-sm-2 control-label">用户名称：</label>
                        <div class="col-sm-3">
                            <input type="text" name="name" class="form-control" id="name" placeholder="请输用户名" value="@if(!empty($account)){{$account['name']}}@endif">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">是否启用：</label>
                        <div class="col-sm-3">
                            <!-- 按钮 开始 -->
                            <div class="switch_items" class="form-control">
                                <input type="radio" @if(!empty($account) && $account['status'] == 1)checked @endif name="status" value="1" />是
                                <input type="radio"  @if(!empty($account) && $account['status'] == 0)checked @endif name="status" value="0" />否
                                <label></label>
                            </div>
                            <!-- 按钮 结束 -->
                        </div>
                    </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">是否设置为超级用户：</label>
                            <div class="col-sm-3">
                                <!-- 按钮 开始 -->
                                <div class="switch_items" class="form-control">
                                    <input type="radio" @if(!empty($account) && $account['is_super'] == 1) checked @endif name="is_super" value="1" />是
                                    <input type="radio" @if(!empty($account) && $account['is_super'] == 0) checked @endif  name="is_super" value="0" />否
                                    <label></label>
                                </div>
                                <!-- 按钮 结束 -->
                            </div>
                        </div>
                    <div class="form-group">
                        <label for="phoneNumber" class="col-sm-2 control-label"></label>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary sure">确定</button>
                            <button type="button" class="btn btn-default cancel">取消</button>
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
        <!--主要内容的JS-->
        <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.4.1 addCount.js" type="text/javascript" charset="utf-8"></script>
@endsection