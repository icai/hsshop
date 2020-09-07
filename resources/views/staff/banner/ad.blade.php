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
                <span>Banner列表</span>
                <a href="/staff/banner/adSave" type="submit" class="btn btn-primary btn-right">新建</a>
            </div>
            <div class="main_content">          	          
            	<ul class="sheet table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>广告名称</li>
                    <li>广告位置</li>
                    <li>广告图</li>
                    <li>广告链接</li>
                    <li>广告类型</li>
                    <li>排序</li>
                    <li>时间</li>
                    <li class="fun">操作</li>
                </ul>
                @forelse($list['data'] as $val)
                <ul data-id="{{ $val['id'] }}" class="sheet table_body flex-between">
                    <li class="fun"><label><input data-id="{{ $val['id'] }}" type="checkbox" name='' class="ulradio" value="" /></label></li>
                    <li>{{ $val['title'] }}</li>
                    <li>{{ $val['position'] }}</li>
                    <li>
                    	<div class="sheet-img">                    		
	                    	<img src="{{ imgUrl() }}{{ $val['img'] }}"/>
                    	</div>
                    </li>
                    <li>{{ $val['type'] ==0 ?'普通广告':'精选广告' }}</li>
                    <li>{{ $val['url'] }}</li>
                    <li>{{ $val['sort'] }}</li>
                    <li>{{ $val['created_at'] }}</li>
                    <li class="fun">
                        <a href="/staff/banner/adSave?id={{ $val['id'] }}" class="modify1">编辑</a>
                        <a href="##" data-id="{{ $val['id'] }}" data-con="del" class="del butta">删除</a>
                    </li>
                </ul>
                @empty
                暂无数据
                @endforelse
                <div class="flex-left">
                	<a class="btn btn-primary del_bom">删除</a>
                </div>
                <div class="main_bottom flex_end">
                    {!! $page !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/adlist.js" type="text/javascript" charset="utf-8"></script>
@endsection