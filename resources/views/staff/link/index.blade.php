@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/7 bannerlist.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>友链管理</span>
                <a href="/staff/link/save" type="submit" class="btn btn-primary btn-right">新建</a>
            </div>
            <div class="main_content">                        
                <ul class="sheet table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" /></label>
                    </li>
                    <li>链接标题</li>
                    <li>链接网址</li>
                    <li>排序</li> 
                    <li>时间</li> 
                    <li class="fun">操作</li>
                </ul>
                @forelse($list['data'] as $val)
                <ul class="sheet table_body  flex-between">
                    <li class="fun"><label><input type="checkbox" name='' value="" /></label></li>
                    <li>{{ $val['name'] }}</li>
                    <li>{{ $val['url'] }}</li>
                    <li>{{ $val['sort'] }}</li>  
                    <li>{{ $val['created_at'] }}</li> 
                    <li class="fun">
                        <a href="/staff/link/save?id={{ $val['id'] }}" class="edit">修改</a>
                        <a href="##" class="del" data-id="{{ $val['id'] }}">删除</a>
                        <a href="#" data-url="{{ $val['url'] }}" class="copy">复制链接</a>
                    </li>
                </ul> 
                @empty
                暂无数据
                @endforelse
                <div class="main_bottom flex_end">
                    <!-- 分页位置 -->
                    {!! $page !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/10 linklist.js" type="text/javascript" charset="utf-8"></script>
@endsection