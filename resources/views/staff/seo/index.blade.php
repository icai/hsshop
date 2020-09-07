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
                <span>seo列表</span>
                <a href="/staff/seo/save" type="submit" class="btn btn-primary btn-right">新建</a>
            </div>
            <div class="main_content">                        
                <ul class="sheet table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" /></label>
                    </li>
                    <li>标题</li>
                    <li>关键词</li>
                    <li>描述</li>
                    <li>关联页面</li>
                    <li>关联连接</li> 
                    <li>时间</li> 
                    <li class="fun">操作</li>
                </ul>
                @forelse($list['data'] as $val)
                <ul class="sheet table_body  flex-between">
                    <li class="fun"><label><input type="checkbox" name='' value="" /></label></li>
                    <li>{{ $val['title'] }}</li>
                    <li>{{ $val['keywords'] }}</li>
                    <li>{{ $val['descript'] }}</li>
                    <li>{{ $val['unit_page'] }}</li>
                    <li>{{ $val['page_url'] }}</li> 
                    <li>{{ $val['created_at'] }}</li> 
                    <li class="fun">
                        <a href="/staff/seo/save?id={{ $val['id'] }}" class="edit">修改</a>
                        <a href="##" class="del" data-id="{{ $val['id'] }}">删除</a>
                        <a href="#" class="copy" data-url="{{ $val['page_url'] }}">复制链接</a>
                    </li>
                </ul> 
                @empty
                暂无数据
                @endforelse
                <div class="main_bottom flex_end">
                    <!-- 分页位置 -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/9 seolist.js" type="text/javascript" charset="utf-8"></script>
@endsection