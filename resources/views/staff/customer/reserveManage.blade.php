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
                <span>潜在客户管理-全部</span>
                <span><a href="/staff/customer/export" target="_blank">导出全部</a></span>
            </div>
            <div class="main_content">
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>客户名称</li>
                    <li>手机号</li>
                    <li>所属行业</li>
                    <li>产品服务</li>
                    <li>来源</li>
                    <li>访问链接来源</li>
                    <li>企业名称</li>
                    <li>职位</li>
                    <li>时间</li>
                    <li>操作</li>
                </ul>
                @forelse($reserve[0]['data'] as $val)
                <ul class="table_body  flex-between">
                    <li><input type="checkbox" name='' value="" /></li>
                    <li>{{$val['name']}}</li>
                    <li>{{$val['phone']}}</li>
                    <li>{{$val['industry']}}</li>
                    @if($val['type'] == 1)
                    <li>分销</li>
                    @elseif($val['type'] == 2)
                    <li>app定制</li>
                    @elseif($val['type'] == 3)
                    <li>微信小程序</li>
                    @elseif($val['type'] == 4)
                    <li>微信营销总裁班</li>
                    @elseif($val['type'] == 5)
                        <li>9块9拼团营销活动</li>
                    @else
                    <li>分销</li>
                    @endif
                    <li>{{$val['source']??'' }}</li>
                    <li>{{ $val['link_source'] }}</li>
                    <li>{{ $val['enterprise_name'] or '' }}</li>
                    <li>{{ $val['position'] or ''}}</li>
                    <li>{{$val['created_at']}}</li>
                    <li data-id="{{$val['id']}}">
                        @if($val['status'] == 1)
                        <a href="##" class="star">已加星</a>
                            @else
                            <a href="##" class="star">加星</a>
                        @endif
                        <a href="##" class="del">删除</a>
                        <a href="##" class="change_phone">修改手机号</a>
                    </li>
                </ul>
                @endforeach
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