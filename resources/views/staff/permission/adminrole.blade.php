@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.1 background_permissions.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>权限管理-总后台权限管理</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">权限分组</a>
                </div>
                <div class="permission_list">
                    <div class="">
                        <a href="##" class="permission_menu_ele" style="color: #333;">权限列表</a>
                        <span class="verLine">|</span>
                        <a href="/staff/addAdminRole" class="add_permission_ele">添加分组</a>
                    </div>
                    <br />
                    <ul class="table_title flex-between">
                        <li>
                            <label><input type="checkbox" name="" class="allSel" />全选</label>
                        </li>
                        <li>权限分组</li>
                        <li>描述</li>
                        <li>添加时间</li>
                        <li>操作</li>
                    </ul>
                    @forelse($roleData['0']['data'] as $val)
                        <ul class="table_body  flex-between">
                            <li><label><input type="checkbox" name='' value="" />{{$val['id']}}</label></li>
                            <li>{{$val['name']}}</li>
                            <li>{{$val['content']}}</li>
                            <li>{{$val['created_at']}}</li>
                            <li>
                                <a href="/staff/addAdminRole?id={{$val['id']}}" >修改</a> |
                                <a href="/staff/bindAdminRolePermission?id={{$val['id']}}">权限</a>
                                {{--<a href="##" class="del">删除</a>--}}
                            </li>
                        </ul>
                   @endforeach
                    <div class="main_bottom flex-between">
                        {{$roleData[1]}}
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.1 background_permissions.js" type="text/javascript" charset="utf-8"></script>
@endsection