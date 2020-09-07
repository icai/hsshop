@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/evaluates_uynh7ai2.css" />
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
        <div class="third_title">评论管理</div>
    </div> 
</div>

@endsection
@section('content')
<div class="content">
	<!--头部筛选部分-->
    <form class="filter_conditions flex_between" action="" method="get">
        <ul>
            <li>
                <label>昵称：<input type="text" value="" placeholder="昵称" /></label>
            </li>
            <li>
                <button type="submit" class="btn btn-primary screening">筛选</button> 
            </li>
        </ul>
        <ul>
            <li>
                <label>
                    评价内容：
                    <input type="text" value="" placeholder="评价内容" />
                </label>
            </li> 
        </ul>
        <ul>
            <li>发布时间：
                <input type="text" class="form-control input-sm iblock" id="start_time" />
                至 <input type="text" class="form-control input-sm iblock" id="end_time" />
            </li>  
        </ul> 
    </form> 

    <div class="main_content mt20">
        <ul class="main_content_title"> 
            <li><input type="checkbox" value="" id="check_all" /></li>
            <li>ID</li>
            <li>昵称</li>
            <li>评论内容</li>
            <li>发布时间</li>
            <li>回复评论ID</li> 
            <li>操作</li>
        </ul>
        @forelse ( $list as $v )
        <ul class="data_content"> 
            <li><input type="checkbox" value="{{$v['id']}}" class="cb_select"  /></li>
            <li>{{$v['id']}}</li>
            <li>{{$v['nickname']}}</li>
            <li>{{$v['content']}}</li>
            <li>{{$v['created_at']}}</li>
            <li>@if ($v['parent_id'] > 0) <a href="javascript:;" data-id="{{$v['parent_id']}}" class="js-reply">{{$v['parent_id']}}</a> @endif</li>
            <li data-id="{{$v['id']}}">
                <a href="javascript:void(0);" class="delete">删除</a> 
            </li>
        </ul>
        @empty
            <ul class="data_content">暂无数据</ul>
        @endforelse
    </div>
    <!-- 批量删除 -->
    <div class="mt20">
    	<button class="btn btn-default btm-sm js_bacth_del">批量删除</button>
    </div>
    <!-- 分页 -->
    <div class="pageNum">{{ $pageHtml }}</div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
	
</script>
<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/evaluates_uynh7ai2.js"></script>

@endsection