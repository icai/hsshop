<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
</head>
<link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2 store_permissions.css" />
<div class="main">
<div class="content">
    <div class="main_content">
        <div class="sorts">
            <a href="/staff/getRole">{{$account['login_name']}}</a>
        </div>
        <div class="permission_div">
            <!--权限列表开始-->
            <form id="bindPermission">
                <div class="show_hide permission_list">
                    <div class="">
                        <a href="##" class="permission_menu_ele" style="color: #333;">权限列表</a>
                        <span class="verLine">|</span>
                        <a href="##" class="add_permission_ele">添加权限</a>
                    </div>
                    <br />
                    <input type="hidden" name="id" value="{{request('id')}}" />
                    @forelse($data['permission'] as $val)
                        <label>
                            <input type="checkbox" name="permissionIds[]" id="" value="{{$val['id']}}" @if(in_array($val['id'],$data['accountPermission']))checked="checked"@endif />
                            <span>{{$val['name']}}  | {{$val['content']}}</span>
                        </label>
                        @endforeach
                        <button id="bind" type="button" class="btn btn-warning del">保存所选项</button>
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
                        <label for="permission_des" class="col-sm-2 control-label">权限描述：</label>
                        <div class="col-sm-3">
                            <textarea name="content" id="permission_des"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="2" />
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
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/bindStaffPermission.js" type="text/javascript" charset="utf-8"></script>