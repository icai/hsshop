@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.1 admin_type.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
	<div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>分享统计-统计列表</span>
            </div>
            <div class="main_content">
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>分享推荐手机号</li>
                    <li>分享报名人数</li>
                    <li>分享链接</li>
                    <li>操作</li>
                </ul>

                @forelse($list as $key=>$val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$key}}</li>
                        <li><a href="/staff/showSignerList?phone={{ $key }}">{{$val['count']}}</a></li>
                        @if($key == 'default')
                        <li><a href="{{ config('app.url') }}applet/index" target="_blank">{{ config('app.url') }}applet/index</a></li>
                        @else
                        <li><a href="{{ config('app.url') }}applet/index?phone={{ $key }}" title="{{ config('app.url') }}applet/index?phone={{ $key }}" target="_blank">{{ config('app.url') }}applet/index?phone=15857191559</a></li>
                        @endif
                        <li>
                            <a href="/staff/showSignerList?phone={{ $key }}" class="modify">查看详情</a>
                        </li>
                    </ul>
                @endforeach
                
                <div class="main_bottom t-pr" style="position: relative;">
                    
                </div>
            </div>
        </div>
    </div>

@endsection