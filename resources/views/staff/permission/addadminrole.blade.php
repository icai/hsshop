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
                <span>权限分组-添加权限分组</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" class="add_permission_ele" style="color: #333;">添加权限分组</a>
                </div>
                <div class="permission_div">
                    <div class="">
                        <a href="/staff/getAdminRole" class="permission_menu_ele">分组列表</a>
                        <span class="verLine">|</span>
                        <a href="##" class="add_permission_ele" style="color: #333;">添加权限分组</a>
                    </div>
                    <br />
                    <form id="modify_form" class="form-horizontal">
                        @if(!empty($roleData))
                            <input type="hidden" name="id" value="{{$roleData['id']}}" />
                        @endif
                        <div class="form-group">
                            <label for="permission_name" class="col-sm-2 control-label">分组名称：</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="permission_name" name="name" @if(!empty($roleData))value="{{$roleData['name']}}" @endif placeholder="分组名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="permission_des" class="col-sm-2 control-label">分组描述：</label>
                            <div class="col-sm-3">
                                <textarea name="content" id="permission_des" cols="54" rows="8">@if(!empty($roleData)){{$roleData['content']}} @endif</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="sub" type="button" class="btn btn-primary">确定</button>
                                <button type="reset" class="btn btn-default">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.1 background_permissions.js" type="text/javascript" charset="utf-8"></script>
@endsection