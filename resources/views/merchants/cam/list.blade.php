@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_39ygjl7x.css" />
    <style>
        .invalid {
            color:#999;
        }
        .invalid:hover {
            color:#999;
        }
        .kam-time {
            width: 20% !important;
        }
    </style>
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
            </li>
            <li>
                <a href="javascript:;">发卡密</a>
            </li>
        </ul>
    </div>
</div>
@endsection
@section('content')
<div class="content">
    <div class="nav_module clearfix">
        <!-- 左侧 开始 -->
        <div class="pull-left">
            <!-- 导航 开始 -->
            <ul class="tab_nav">
                <li class="hover">
                    <a href="/merchants/cam/list">所有卡密</a>
                </li>
            </ul>
            <!-- 导航 结束 -->
        </div>
         <!-- 左侧 结束 -->
         <!-- 右边 开始-->
         <div class="pull-right">
            <a class="f12 blue_38f pull-right-a" href="https://www.huisou.cn/home/index/helpDetail/844" target="_blank">
                <i class="glyphicon glyphicon-question-sign green f14"></i>
                &nbsp;查看【发卡密】设置应用及教程
            </a>
        </div>
        <!-- 右边 结束 -->
    </div>
	<div class="model_itmes mgb20">
		<a href="/merchants/cam/create" class="btn btn-success">新建发卡密</a>
        <!-- 搜索 开始 -->
        <div class="search_wrap">
            <form action="" method="get" name="searchForm">
                <label class="search_items">
                    <input class="search_input" type="text" name="title"  placeholder="搜索" value="{{ request('title') }}">   
                </label>
            </form>
        </div>
    </div>	
    <!--无数据-->
    <!--<div class="no_result">还没有相关数据</div>-->
	<!-- 列表 开始 -->
    <div class="table table-hover condent_data">
        <!-- 标题 -->
        <ul class="active ul_color data_title flex_center">
            <li>发卡密名称</li>
            <li>类型</li>
            <li>已发送</li>
            <li>剩余库存</li>
            <li>总库存</li>
            <li>操作</li>
        </ul>
        <!-- 列表 -->
        @forelse($list as $val)
            <ul class="data flex_center">
                <li>{{ $val['title'] }}</li>
                <li>{{ $val['type'] == 1 ? '一商品一码' : '通用码' }}</li>
                <li>{{ $val['count']['sendTotal'] or 0 }}</li>
                <li>{{ $val['count']['leftTotal'] or 0 }}</li>
                <li>{{ $val['count']['total'] or 0 }}</li>
                <li class="opt_wrap blue_97f">
                    <a href="{{ URL('/merchants/cam/camStockList?id='.$val['id']) }}">
                        <span class="blue_97f">卡密库</span>
                    </a>-
                    @if($val['invalid'] == 1)
                    <a class="pagecat-del" data-id={{$val['id']}}>
                        <span class="blue_97f">删除</span>
                    </a>-
                    <a class="two-code" data-id={{$val['id']}}>
                        <span class="blue_97f invalid">已失效</span>
                    </a>
                    @else
                    <a class="link_btn customTip_items" href="{{ URL('/merchants/cam/create?id='.$val['id']) }}">
                        <span class="blue_97f">编辑</span>
                    </a>-
                    <a class="two-code J_invalid" data-id={{$val['id']}}>
                        <span class="blue_97f">使失效</span>
                    </a>
                    @endif
                </li>
            </ul>
        @empty
            <div style="text-align:center;margin-top:10px;">暂无数据</div>
        @endforelse
    </div>
    <div style="text-align: right;">{{ $pageHtml }}</div>

</div>
@endsection
@section('page_js')
    <!-- 当前页面js --> 
    <script type="text/javascript">
    	var host ="{{ config('app.url') }}";
    	var _host = "{{ config('app.source_url') }}";
    	var wid = {{session('wid')}};	
    </script>   
    <script src="{{ config('app.source_url') }}mctsource/js/kamlist.js"></script>
@endsection