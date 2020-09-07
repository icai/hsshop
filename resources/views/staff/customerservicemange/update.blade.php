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
                <span>客服管理-修改信息</span>
            </div>
            <div class="main_content">
                @include('staff.customerservicemange.message')
                @include('staff.customerservicemange.validate')
                <div class="permission_div col-md-5">
                    <div class="">
                        <a href="/staff/CustomerServiceManage" class="permission_menu_ele">客服列表</a>
                        <span class="verLine">|</span>
                        <a href="##" class="add_permission_ele" style="color: #333;">修改信息</a>
                    </div>
                    <br />
                    <form id="modify_form" class="form-horizontal" method="post">
                        {{csrf_field()}}

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
                    </form>
                </div>
                <div class="col-md-7">
                    <ul class="sheet table_title flex-between">
                        <li style="width:300px;">值班电话</li>
                        <li>时间</li>
                     </ul>   
                     @foreach($show as $k => $v)
                    <ul class="flex-between">
                        <li style="width:300px;">{{ $v['phone'] }}</li>
                        <li>{{ $k }}</li>
                    </ul>
                    @endforeach
                </div>
                    @foreach($list['data'] as $v)
                        <ul class="sheet table_body  flex-between">
                            <li>{{ $v['oper_id'] }}</li>
                            <li>{{ $v['name'] }}</li>
                            <li>
                                {{ $v['content']['info']['name'] or '' }} &nbsp;
                                {{ $v['content']['info']['phone'] or '' }}
                            </li>
                            <li>{{ $v['created_at'] }}</li>
                        </ul>
                    @endforeach
                    {{$html}}
                </div>
            </div>
        </div>
@endsection
