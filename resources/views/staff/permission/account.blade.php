@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.4 account_management.css" />
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
                    <a href="##" style="color: #333;">帐号列表</a>
                    <span class="verLine">|</span>
                    <a href="/staff/addUser" class="addNewAdmin">新增用户信息</a>
                </div>
                <ul class="table_title flex-between">
                    <li>帐号</li>
                    <li>用户名称</li>
                    <li>是否启用</li>
                    <li>登陆次数</li>
                    <li>创建时间</li>
                    <li>操作</li>
                </ul>
                @forelse($account[0]['data'] as $val)
                <ul class="table_body  flex-between">
                    <li>{{$val['login_name']}}</li>
                    <li>{{$val['name']}}</li>
                    <li>@if($val['status'] == 1)是 @else 否@endif</li>
                    <li>{{$val['logins']}}</li>
                    <li>{{$val['created_at']}}</li>
                    <li>
                        @if($val['is_super'] != 1)
                            <a href="##" data-placement="{{$val['id']}}" class="permission">权限</a>
                        @endif
                        <a href="/staff/addUser?id={{$val['id']}}" class="modify">修改</a>
                        <a href="##" data-placement="{{$val['id']}}" class="del">删除</a>
                    </li>
                </ul>
                @endforeach
                <div class="main_bottom flex_end">
                    {{$account[1]}}
                </div>
            </div>
        </div>
@endsection
@section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
            <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
            <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
            <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
            <!--主要内容的JS-->
            <script src="{{ config('app.source_url') }}staff/hsadmin/layer/layer.js"></script>
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.4 account_management.js" type="text/javascript" charset="utf-8"></script>
@endsection