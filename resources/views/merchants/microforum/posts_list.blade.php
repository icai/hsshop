@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/posts_uynh7ai2.css" />
<style type="text/css">
    .laydate_box, .laydate_box * {box-sizing:content-box;}
</style>
@endsection
@section('slidebar')
    @include('merchants.microforum.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">帖子管理</div>
        <!-- 二级导航三级标题 结束 -->
    </div> 
</div>

@endsection
@section('content')
<div class="content">
	<!--头部筛选部分-->
    <form class="filter_conditions flex_between" action="" method="get">
        <ul>
            <li>
                <label>昵称：<input type="text" name="nickname" value="{{$search['nickname'] or ''}}" placeholder="昵称" /></label>
            </li> 
            <li>发布时间：
                <input type="text" id="start_time" name="start_time" class="form-control input-sm iblock" value="{{$search['start_time'] or ''}}"/> 
            </li> 
            <li>
                <button type="submit" class="btn btn-primary screening">筛选</button> 
            </li>
        </ul>
        <ul>
            <li>
                <label>
                    标题：
                    <input type="text" value="{{$search['title'] or ''}}" name="title" placeholder="标题" />
                </label>
            </li>  
            <li>至：
                <input type="text" id="end_time" name="end_time" class="form-control input-sm iblock" value="{{$search['end_time'] or ''}}"/>
            </li>
        </ul>
        <ul>
            <li style="text-align:left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分类：
                <select name="discussions_id">
				<option value="0">全部</option> 
				@foreach ($categoriesDatas as $c)
                    <option value="{{$c['id']}}" @if (isset($search['discussions_id']) && $search['discussions_id'] == $c['id']) selected=1 @endif>{{$c['title']}}</option> 
				@endforeach
                </select>
            </li>  
        </ul>
        <ul>
            <li>置顶：
                <select name="is_top">
                    <option value="2">全部</option> 
                    <option value="0" @if (isset($search['is_top']) && $search['is_top'] == 0) selected=1 @endif>未置顶</option> 
                    <option value="1" @if (isset($search['is_top']) && $search['is_top'] == 1) selected=1 @endif>已置顶</option> 
                </select>
            </li>  
        </ul>
    </form> 
    <!--发布帖子-->
    <div><a href="/merchants/microforum/posts/release" class="btn btn-primary">发布帖子</a></div>

    <div class="main_content mt20">
        <ul class="main_content_title">
        	<li><input type="checkbox" value="" id="check_all" /></li>
            <li>ID</li>
            <li>昵称</li>
            <li>标题</li>
            <li>分类</li>
            <li>发布时间</li>
            <li>点赞</li>
            <li>评论</li>
            <li>操作</li>
        </ul>
        @forelse ( $list as $v )
        <ul class="data_content">
            <li><input type="checkbox" value="{{$v['id']}}" class="cb_select" /></li>
            <li>{{$v['id']}}</li>
            <li>{{$v['nickname']}}</li>
            <li>{{$v['title']}}</li>
            <li>{{$v['discussions_id']}}</li>
            <li>{{$v['created_at']}}</li>
            <li>{{$v['favorCount']}}</li>
            <li>{{$v['replyCount']}}</li>
            <li data-id="{{$v['id']}}">
				@if ($v['is_top'])
                <a href="javascript:void(0);" class="qxzd">取消置顶</a>
				@else
                <a href="javascript:void(0);" class="zd">置顶</a>
				@endif
                &nbsp;|&nbsp;<a href="/merchants/microforum/evaluates/list/{{$v['id']}}" class="plgl">评论管理</a>
                &nbsp;|&nbsp;<a href="/merchants/microforum/posts/edit/{{$v['id']}}">编辑</a>
                &nbsp;|&nbsp;<a href="javascript:void(0);" class="del">删除</a>
            </li>
        </ul>
        @empty
            <ul class="data_content">暂无数据</ul>
        @endforelse
    </div>
    <!-- 批量删除 -->
    <div class="mt20">
    	<button class="btn btn-default btm-sm js_batch_del">批量删除</button>
    </div>
    <!-- 分页 -->
    <div class="pageNum">{{ $pageHtml }}</div>
</div>
@endsection
@section('page_js')
 <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/posts_uynh7ai2.js"></script>
@endsection
