@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2 store_permissions.css" />
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
                    <a href="/staff/getAdminRole">{{$roleData['name']}}</a>
                    <span class="verLine">|</span>
                    <a href="##" class="addNewClassify" style="color: #333;">添加权限</a>
                </div>
                <div class="permission_div">
                    <!--权限列表开始-->
                    <form id="myForm">
                        <input type="hidden" name="adroleId" value="{{$roleData['id']}}" />
                    <div class="show_hide permission_list">
                        <div class="">
                            <a href="##" class="permission_menu_ele" style="color: #333;">权限列表</a>
                            <span class="verLine">|</span>
                            <a href="##" class="add_permission_ele">添加权限</a>
                        </div>
                        <br />
                        @forelse($permissionData[0]['data'] as $val)
                        <label>
                            <input type="checkbox" name="permissionId[]" id="" value="{{$val['id']}}" @if($val['is_in'] == 1)checked="checked"@endif />
                            <span>{{$val['name']}}  | {{$val['content']}} | {{$val['route']}} @if($val['type'] == 1)（PC） @else （APP） @endif</span>
                            &nbsp;&nbsp;&nbsp;<a href="##" data-id="{{$val['id']}}" class="delete_permission">删除</a>
                        </label>
                       @endforeach
                        <button id="save" type="button" class="btn btn-warning del">保存所选项</button>
                    </div>
                    </form>
                    <!--权限列表结束-->
                    <!--添加权限开始-->
                    <div class="show_hide add_permission hide">
                        <div class="">
                            <a href="##" class="permission_menu_ele">权限列表</a>
                            <span class="verLine">|</span>
                            <a href="##" class="add_permission_ele" style="color: #333;">添加权限</a>
                        </div>
                        <br />
                        <!---->
                        <form id="addPermission" class="form-horizontal">
                            <div class="form-group">
                                <label for="permission_name" class="col-sm-2 control-label">权限名称：</label>
                                <div class="col-sm-3">
                                    <input name="name" type="text" class="form-control" id="permission_name" placeholder="权限名称">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="permission_des" class="col-sm-2 control-label">权限路由：</label>
                                <div class="col-sm-3">
                                    <input name="route" type="text" class="form-control" id="permission_name" placeholder="权限路由">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="permission_des" class="col-sm-2 control-label">权限类型：</label>
                                 <input type="radio" name="type" checked="checked" value="1"> PC商家端权限&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="type" value="3"> 商家APP权限
                            </div>
                            <div class="form-group">
                                <label for="permission_des" class="col-sm-2 control-label">权限描述：</label>
                                <div class="col-sm-3">
                                    <textarea name="content" id="permission_des"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="button"  id="sub" class="btn btn-primary">确定</button>
                                    <button type="reset" class="btn btn-default">取消</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--添加权限结束-->
                </div>
            </div>
        </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2 store_permissions.js" type="text/javascript" charset="utf-8"></script>
@endsection