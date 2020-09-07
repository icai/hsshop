@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_jfsul41e.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <ul class="common_nav">
        <li>
            <a href="/merchants/store?is_show=1">微页面</a>
        </li>
        <li>
            <a href="/merchants/store?is_show=0">微页面草稿</a>
        </li>
        <li class="hover">
            <a href="/merchants/store/pagecat">微页面分类</a>
        </li>
    </ul>
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
    <!-- 新建微页面分类 开始 -->
    <div class="model_itmes mgb20">
        <!-- <button type="button" class="btn btn-success">新建微页面分类</button>-->
		<a href="{{ URL('/merchants/store/pagecatAdd') }}" class="btn btn-success">新建微页面分类</a>
        <!-- 搜索 开始 -->
        <div class="search_wrap">
            <form action="" method="get" name="searchForm">
                <label class="search_items">
                    <input class="search_input" type="text" name="title" value="{{ request('title') }}" placeholder="搜索"/>   
                </label>
            </form>
        </div>
        <!-- 分类&搜索 结束 -->
    </div>
    <!-- 新建微页面分类 结束 -->
    <!-- 列表 开始 -->
    <table class="table table-hover">
        <!-- 标题 -->
        <tr class="active">
            <td>标题</td>
            <td>微页面数</td>
            <td>创建时间↓</td>
            <td>操作</td>
        </tr>
        <!-- 列表 -->
		@forelse($list as $item)
        <tr>
            <td>{{ $item['title'] }}</td>
            <td>{{ $item['page_num'] }}</td>
            <td>{{ $item['created_at'] }}</td>
            <td class="opt_wrap">
                <a href="/merchants/store/pagecatAdd/{{ $item['id'] }}">
                    <span class="blue_38f">编辑</span>
                </a>
                @if($item['is_auto']==0)
                <a class="pagecat-del" data-id="{{ $item['id'] }}" href="javascript:;">
                    <span class="blue_38f">删除</span>
                </a>
                @endif
               <!-- <a class="link_btn customTip_items" href="javascript:void(0);">
                    <span class="blue_38f">链接</span>
                    <div class="custom_tip">
                        <input class="link_copy" type="text" value="www.baidu.com4" disabled /><div class="copy_btn">复制</div>    
                    </div>
                </a>-->
            </td>
        </tr>
		@endforeach
    </table>
    <!-- 列表 结束 -->
    <!-- 管理和分页 开始 -->
    <div class="manage_page">
        <!-- 管理 开始 -->
        <div class="manage_items">
            <!-- 提示框 开始 -->
            <div class="manage_tip">
                <!-- 未分组 开始 -->
                <div class="ungrouped_items">
                    <p class="items_title">您未创建分类</p>
                    <a class="blue_38f" href="javascript:void(0);">管理分类</a>
                </div>
                <!-- 未分组 结束 -->
                <!-- 分组管理 开始 -->
                <div class="grouped_items">
                    <!-- 分组头 开始 -->
                    <div class="grouped_header">
                        <a class="items_title" href="javascript:void(0);">修改分类</a>
                        <a class="blue_38f" href="javascript:void(0);">管理</a>
                    </div>
                    <!-- 分组头 结束 -->
                    <!-- 分组body 开始 -->
                    <div class="grouped_body">
                        <label>
                            <input type="checkbox" name="" value="" />
                            分组1
                        </label>
                        <label>
                            <input type="checkbox" name="" value="" />
                            分组2
                        </label>
                    </div>
                    <!-- 分组body 结束 -->
                    <!-- 分组底部 开始 -->
                    <div class="grouped_footer">
                        <button type="button" class="btn btn-info btn-sm">确定</button>
                        <button type="button" class="btn btn-default btn-sm">取消</button>
                    </div>
                    <!-- 分组底部 结束 -->
                </div>
                <!-- 分组管理 结束 -->
            </div>
            <!-- 提示框 结束 -->
        </div>
        <!-- 管理 结束 -->
        <!-- 分页 开始 -->
        <div class="page_items">
		{{ $pageHtml }}
		</div>
        <!-- 分页 结束 -->
    </div>
    <!-- 管理和分页 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_jfsul41e.js"></script>
@endsection
