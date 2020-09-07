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
                <span>推荐管理-添加推荐</span>
            </div>
            <div class="main_content">
                <div class="permission_div">
                    <div class="sorts">
                        <a href="/staff/getRecomment" class="permission_menu_ele">推荐列表</a>
                        <span class="verLine">|</span>
                        <a href="##" class="add_permission_ele" style="color: #333;">添加推荐</a>
                    </div>
                    <br />
                    <form id="modify_form" class="form-horizontal">
                        @if(!empty($recommend))
                            <input type="hidden" name="id" value="{{$recommend['id']}}">
                        @endif
                        <div class="form-group">
                            <label for="permission_name" class="col-sm-2 control-label">推荐名称：</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="permission_name" name="name" @if(!empty($recommend))value="{{$recommend['name']}}" @endif placeholder="推荐名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="permission_name" class="col-sm-2 control-label">推荐路由：</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="permission_name" name="uri" @if(!empty($recommend))value="{{$recommend['uri']}}" @endif placeholder="推荐路由">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="permission_des" class="col-sm-2 control-label">推荐描述：</label>
                            <div class="col-sm-3">
                                <textarea name="content" id="permission_des">@if(!empty($recommend)){{$recommend['content']}} @endif</textarea>
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
    </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/addRecommend.js" type="text/javascript" charset="utf-8"></script>
@endsection