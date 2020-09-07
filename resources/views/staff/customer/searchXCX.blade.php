@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/6.1 potential_customers.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>小程序查重-查询记录</span>
                <span><a href="/staff/customer/exportSearchXCX" target="_blank">导出全部</a></span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <form id="myForm" class="form-inline">
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
                                <span>是否注册</span>
                            </span>
                            <select id="is_register" name="is_register" class="form-control">
                                <option @if(request('is_register') == null || request('is_register') == 'all')selected='selected'@endif value="all">--请选择--</option>
                                <option @if(request('is_register') == '0')selected='selected'@endif value="0">未注册</option>
                                <option @if(request('is_register') == '1')selected='selected'@endif value="1">已注册</option>
                            </select>
                        </div>
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
                                <span>是否加星</span>
                            </span>
                            <select id="status" name="status" class="form-control">
                                <option @if(request('status') == null || request('status') == 'all')selected='selected'@endif value="all">--请选择--</option>
                                <option @if(request('status') == '0')selected='selected'@endif value="0">未加星</option>
                                <option @if(request('status') == '1')selected='selected'@endif value="1">已加星</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">搜索</button>
                    </form>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>客户名称</li>
                    <li>手机号</li>
                    <li>查询小程序名称</li>
                    <li>查询结果</li>
                    <li>来源</li>
                    <li>访问链接来源</li>
                    <li>时间</li>
                    <li>操作</li>
                </ul>
                <form class="listForm">
                    @forelse($reserve[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='ids[]' value="{{$val['id']}}" /></li>
                        <li>{{$val['name']}}</li>
                        <li>{{$val['phone']}}</li>
                        <li>{{$val['liteapp_title']}}</li>
                        <li>{{$val['is_register'] ? '已注册' : '未注册'}}</li>
                        <li>{{$val['source']??'' }}</li>
                        <li>{{ $val['link_source'] }}</li>
                        <li>{{$val['created_at']}}</li>
                        <li data-id="{{$val['id']}}">
                            @if($val['status'] == 1)
                            <a href="##" class="star">已加星</a>
                                @else
                                <a href="##" class="star">加星</a>
                            @endif
                            <a href="##" class="del">删除</a>
                        </li>
                    </ul>
                    @endforeach
                </form>
                <div class="btn_group">
                    <a class="addStar btn btn-primary" href="javascript:void(0);">批量加星</a>
                    <a class="addDelete btn btn-danger" href="javascript:void(0);">批量删除</a>
                </div>
                <div class="main_bottom flex_end">
                   {{$reserve[1]}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/6.1 potential_customers.js" type="text/javascript" charset="utf-8"></script>
@endsection