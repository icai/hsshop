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
                <span>客服管理-添加客服</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" class="add_permission_ele" style="color: #333;">添加客服</a>
                </div>
                @include('staff.customerservicemange.validate')
                <div class="permission_div">
                    <div class="">
                        <a href="/staff/CustomerServiceManage" class="permission_menu_ele">客服列表</a>
                        <span class="verLine">|</span>
                        <a href="##" class="add_permission_ele" style="color: #333;">添加客服</a>
                    </div>
                    <br />
                    <form id="modify_form" class="form-horizontal" method="post">
                        {{csrf_field()}}
                        @if(!empty($data))
                            <input type="hidden" name="id" value="{{$data['id']}}" />
                        @endif
                        <div class="form-group">
                            <label for="permission_name" class="col-sm-2 control-label">客服名称：</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="permission_name" name="name" @if(!empty($data))value="{{$data['name']}}" @endif placeholder="请输入客服名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="permission_des" class="col-sm-2 control-label">客服电话：</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="permission_name" name="phone" @if(!empty($data))value="{{$data['phone']}}" @endif placeholder="请输入客服电话">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="sub" type="submit" class="btn btn-primary">确定</button>
                                <button type="reset" class="btn btn-default">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection