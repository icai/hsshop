@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.2 add_role.css" />
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
                        <a href="/staff/getRole" class="permission_menu_ele">角色列表</a>
                        <span class="verLine">|</span>
                        <a href="##" class="add_permission_ele" style="color: #333;">添加角色</a>
                    </div>
                    <br />
                    <form id="addRole" class="form-horizontal">
                        <div class="form-group">
                            <label for="permission_name" class="col-sm-2 control-label">角色名称：</label>
                            <div class="col-sm-3">
                                <input name="name" type="text" class="form-control" id="permission_name" placeholder="角色名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="permission_des" class="col-sm-2 control-label">角色描述：</label>
                            <div class="col-sm-3">
                                <textarea name="content" id="permission_des"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="permission_des" class="col-sm-2 control-label">是否开启：</label>
                            <input type="radio" name="status" value="0" />否　　 <input type="radio" checked="checked" name="status" value="1" />是
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" id="add" class="btn btn-primary">确定</button>
                                <button type="reset" class="btn btn-default">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.2 add_role.js" type="text/javascript" charset="utf-8"></script>
@endsection