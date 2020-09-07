@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.1 admin_type.css" />
@endsection
@section('slidebar')
    @include('staff.share.slidebar');
@endsection
@section('content')
<div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>分享统计-分享用户报名列表</span>
            </div>
            <div class="main_content">
                <ul class="table_title flex-between">
                    <li>姓名</li>
                    <li>手机号</li>
                    <li>公司</li>
                    <li>职位</li>
                    <li>行业</li>
                </ul>

                @forelse($list as $val)
                    <ul class="table_body  flex-between">
                        <li>{{$val['real_name']}}</li>
                        <li>{{$val['mobile']}}</li>
                        <li>{{$val['company']}}</li>
                        <li>{{$val['post']}}</li>
                        <li>{{$val['industry']}}</li>
                    </ul>
                @endforeach
                
                <div class="main_bottom t-pr" style="position: relative;">
                    
                </div>
            </div>
        </div>
    </div>

@endsection