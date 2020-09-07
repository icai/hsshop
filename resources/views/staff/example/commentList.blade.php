@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/8.1 example.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
        	<div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span><a href="/staff/example/index">行业案例</a>--评论</span>
            </div>
            <div class="main_content">          	          
            	<ul class="sheet table_title flex-between">
                    <li class="emalb">案例名称</li>
                    <li class="emala">昵称</li>
                    <li class="emalb">评论内容</li>
                    <li class="emalb">时间</li>
                    <li class="emalb">操作</li>
                </ul>
               @forelse($list['data'] as $val)
               <ul class="sheet table_body  flex-between">
                    <li class="emalb">{{ $val['name'] }}</li>
                    <li class="emalb">{{ $val['nickname'] }}</li>
                    <li class="emalb">{{ $val['content'] }}</li>
                    <li class="emalb">{{ $val['created_at'] }}</li>
                    <li class="emalb">
                        <a href="##" class="del" data-id="{{ $val['id'] }}">删除</a>
                    </li>
                </ul>
                @empty
                暂无数据
                @endforelse
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/commentlist.js" type="text/javascript" charset="utf-8"></script>
@endsection