@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/categories_uynh7ai2.css" />

@endsection
@section('slidebar')
    @include('merchants.microforum.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
    	<ul class="crumb_nav">
            <li>
                <a href="javascript:;">分类管理</a>
            </li>
            <li>
                <a href="javascript:;">分类列表</a>
            </li>
        </ul> 
    </div> 
</div>

@endsection
@section('content')
<div class="content"> 
    <!--发布帖子-->
    <div><a href="/merchants/microforum/categories/add" class="btn btn-primary">添加分类</a></div>

    <div class="main_content mt20">
        <ul class="main_content_title">
        	<li><input type="checkbox" value="" id="check_all" /></li>
            <li>ID</li>
            <li>分类名称</li>
            <li>排序</li>
            <li>操作</li>
        </ul>
        @if(count($categoriesDatas))
            @foreach($categoriesDatas as $categoriesData)
                <ul class="data_content" data-id="{{$categoriesData->id}}">
                    <li><input type="checkbox" class="cb_select" value="{{$categoriesData->id}}" /></li>
                    <li>{{$categoriesData->id}}</li>
                    <li>{{$categoriesData->title}}</li>
                    <li>{{$categoriesData->sort}}</li>
                    <li>
                        <a href="/merchants/microforum/categories/edit/{{$categoriesData->id}}" class="edit">编辑</a>
                        &nbsp;|&nbsp;<a href="javascript:void(0);" class="del">删除</a>
                    </li>
                </ul>
        @endforeach
        @endif
        <!-- <ul class="data_content">暂无数据</ul> -->
    </div>
    <!-- 批量删除 -->
    <div class="mt20">
    	<button class="btn btn-default btm-sm js_batch_del">批量删除</button>
    </div>
</div>
@endsection
@section('page_js') 
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/categories_uynh7aa1.js"></script>
@endsection