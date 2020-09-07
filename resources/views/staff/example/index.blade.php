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
                <span>案例列表</span>
                <a href="/staff/example/save" type="submit" class="btn btn-primary btn-right">新建</a>
            </div>
                <div class="main_content" style="padding: 10px 1px">   
                    <div class="sorts">
                        <form id="myForm" class="form-inline">
                            <div class="input-group col-sm-2">
                                <span class="input-group-addon">
                                    <span>案例名称</span>
                                </span>
                                <input name="name" class="form-control" value="{{ request('name') }}" />
                            </div>
                            <button type="submit" class="btn btn-primary">搜索</button>
                        </form>
                    </div> 
                </div>      	          
            	<ul class="sheet table_title flex-between">
                    <li class="emalb">
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li class="emalb">案例名称</li>
                    <li class="emalb">案例类型</li>
                    <li class="emala">作者</li>
                    <li class="emalb">行业分类</li>
                    <li class="emalb">产品logo</li>
                    <li class="emalb">案例二维码</li>
                    <li class="emala">排序</li>
                    <li class="emalb">时间</li>
                    <li class="fun">操作</li>
                </ul>
                @forelse($list['data'] as $val)
                <ul class="sheet table_body  flex-between">
                    <li class="emalb"><label><input class="ulradio" type="checkbox" name='' value="" /></label></li>
                    <li class="emalb">{{ $val['name'] }}</li>
                    <li class="emalb">{{ $val['type'] }}</li>
                    <li class="emala">{{ $val['author'] }}</li>
                    @if(isset($val['industrys']) && $val['industrys'])
                    <li class="emalb">{{ substr($val['industrys'],0,-1) }}</li>
                    @else
                    <li class="emalb"></li>
                    @endif
                    <li class="emalb"><img style="width:50px; height: 50px; display: block; margin: auto;" src="{{ imgUrl() }}{{ $val['logo'] }}" /></li>
                    <li class="emalb">
                        @if(isset($val['code']) && $val['code'])
                        <img style="width:50px; height: 50px; display: block; margin: auto;" src="{{ imgUrl() }}{{ $val['code'] }}" />
                        @endif
                    </li>
                    <li class="emala">{{ $val['sort'] }}</li>
                    <li class="emalb">{{ $val['created_at'] }}</li>
                    <li class="fun">
                    	<a href="/staff/example/commentList?id={{ $val['id'] }}" class="">评论</a>
                        <a href="/staff/example/save?id={{ $val['id'] }}" class="">修改</a>
                        <a href="##" class="del" data-id="{{ $val['id'] }}">删除</a>
                        <a href="##" data-id="{{ $val['id'] }}" class="examcode">二维码</a>                      
                    </li>
                </ul>
                @empty
                暂无数据
                @endforelse
                <div class="main_bottom flex_end">
                    {!! $page !!}
                </div>
            </div>
        </div>        
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/example.js" type="text/javascript" charset="utf-8"></script>
@endsection