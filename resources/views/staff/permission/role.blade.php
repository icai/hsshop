@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.1 admin_type.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>权限管理-店铺权限管理</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">管理员种类</a>
                </div>
                <div class="permission_list">
                    <div class="">
                        <a href="##" class="permission_menu_ele" style="color: #333;">角色列表</a>
                        <span class="verLine">|</span>
                        <a href="/staff/addRole" class="add_permission_ele">添加角色</a>
                    </div>
                    <br />
                    <ul class="table_title flex-between">
                        <li>ID</li>
                        <li>角色名称</li>
                        <li>角色描述</li>
                        <li>权限状态</li>
                        <li>操作</li>
                    </ul>
                    @forelse($roleData[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li>{{$val['id']}}</li>
                        <li>{{$val['name']}}</li>
                        <li>{{$val['content']}}</li>
                        <li>@if($val['status'] == 1)已开启@else已关闭@endif</li>
                        <li>
                            <a href="/staff/bindRolePermission?id={{$val['id']}}" class="dir">权限</a>
                            @if($val['id'] != 1)
                                |<a href="#"  id={{$val['id']}} class="state"> @if($val['status'] == 1)关闭@else开启@endif</a>
                            @endif
                        </li>
                    </ul>
                    @endforeach

                </div>
            </div>
        </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
@endsection