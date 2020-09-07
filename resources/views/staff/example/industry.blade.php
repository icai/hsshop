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
                <span>行业案例</span>
                <a href="/staff/example/industrySave" type="submit" class="btn btn-primary btn-right">新建</a>
            </div>
            <div class="main_content">          	          
            	<ul class="sheet table_title flex-between">
                    <li class="emalb">
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li class="emalb">行业分类名称</li>
                    <li class="emala">排序</li>
                    <li class="emalb">时间</li>
                    <li class="fun">操作</li>
                </ul>
               @forelse($list['data'] as $val)
               <ul class="sheet table_body  flex-between">
                    <li class="emalb"><label><input type="checkbox" name='' value="" /></label></li>
                    <li class="emalb">{{ $val['name'] }}</li>
                    <li class="emalb">{{ $val['sort'] }}</li>
                    <li class="emalb">{{ $val['created_at'] }}</li>
                    <li class="fun">
                        <a href="/staff/example/industrySave?id={{ $val['id'] }}" class="">修改</a>
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
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/8.3 industry.js" type="text/javascript" charset="utf-8"></script>
@endsection