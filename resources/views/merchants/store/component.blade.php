@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_6cqrtqa2.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">自定义模块</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div class="content_header">
        <div class="btn_grounp">
            <a href="{{ URL('/merchants/store/componentAdd') }}" class="btn btn-success">新建自定义模块</a>
           <!-- <a href="#" class="teach_setting">自定义模块设置教程</a>-->
        </div>
        <!--  <div class="search">
            <div class="js-list-search ui-search-box">
                <input class="txt" id="search" type="text" placeholder="搜索" value="">
            </div>
        </div>   -->
        <div class="pull-right search_module">
            <!-- 搜索 开始 -->
            <form method="get" action="">
                <label class="search_items">
                    <input class="search_input" type="text" name="title" value="{{request('title')}}" placeholder="搜索"/> 
                </label>
            </form>
            <!-- 搜索 结束 -->
        </div>
    </div>
    <!-- 没有数据 
    <div class="no_result">暂无数据!</div>-->
    <!-- 有数据列表 -->
    <div class="content_list">
        <table class="table">
            <thead>
                <tr>
                    <th class="col-sm-1">名称</th>
                <!--<th class="col-sm-2">最近应用在</th>
                    <th class="col-sm-2">共应用次数</th>-->
                    <th class="col-sm-3 text-right">操作</th>
                </tr>
            </thead>
            <tbody>
			@forelse($templateList as $item)
                <tr>
                    <td class="text-left">{{ $item['template_name'] }}</td>
                   <!-- <td>Mark324234</td>
                    <td>@mdo</td>-->
                    <td class="action text-right">
                        <a href="/merchants/store/componentAdd/{{ $item['id'] }}">编辑</a>
                        <span>-</span>
                        <a href="javascript:void(0);" class="change_name" data-id="{{ $item['id'] }}">改名</a>
                        <span>-</span>
                        <a href="javascript:void(0);" class="delete" data-id="{{ $item['id'] }}">删除</a>
                    </td>
                </tr>
			@endforeach
            </tbody>
        </table>
        <div class="page_items">
			{{ $pageHtml }}
        </div>
    </div>
    <!-- 改名弹窗 -->
    <div id="hsgf149058723771" class="popover change_input left">
        <div class="arrow"></div>
        <div class="popover-content">
            <div class="inline_block">
                <input type="text" name="title" class="form-control" id="change_name_input">
            </div>
            <button class="btn btn-primary sure_change_name">确定</button>
            <button class="btn btn-default cancel_change_name">取消</button>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_6cqrtqa2.js"></script>
@endsection