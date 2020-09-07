@extends('merchants.default._layouts')
@section('head_css')
<!-- 公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_kwvhib03.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_53swtdra.css" />
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">页面模版</div>
        <!-- 二级导航三级标题 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div class="js-list-filter-region clearfix ui-box">
        <div class="widget-list-filter">
            <div>
                <a href="{{ URL('/merchants/product/createTemplate') }}" class="zent-btn zent-btn-success js-add-template">新建模板</a>
                <div class="common-helps-entry pull-right">
                </div>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>标题</th>
                <th>创建时间</th>
                <th class="text-right">操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $item)
                <tr>
                    <td>{{ $item['template_name'] }}</td>
                    <td>{{ $item['created_at'] }}</td>
                    <td class="text-right">
                        <a href="/merchants/product/createTemplate/{{$item['id']}}">编辑</a>
                        <a href="javascript:void(0);" data-id="{{$item['id']}}" class="delete">删除</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!--<div class="no_result">暂无相关页面模板</div>-->
    
    <div class="page">
	{{ $pageHtml }}
    </div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_53swtdra.js"></script>
@endsection