@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_1qdhfeb3.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                {{--<li>--}}
                    {{--<a href="{{ URL('/merchants/marketing') }}">营销中心</a>--}}
                {{--</li>--}}
                <li>
                    小程序微页面
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->

        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
    <div class="content">
        {{--<ul class="tab_nav">--}}
            {{--<li class="hover">--}}
                {{--<a href="/merchants/marketing/litePage">小程序微页面</a>--}}
            {{--</li> --}}
            {{--<li>--}}
                {{--<a href="/merchants/marketing/footerBar">底部导航</a>--}}
            {{--</li>--}}
            {{--<li class="">--}}
                    {{--<a href="/merchants/marketing/xcx/topnav">首页分类导航</a>--}}
                {{--</li>--}}
            {{--<li class=""> <!-- update 梅杰 新增列表页-->--}}
                {{--<a href="/merchants/marketing/xcx/list">小程序列表</a>--}}
            {{--</li>--}}
            {{--<li class="">--}}
                {{--<a href="/merchants/marketing/liteStatistics">数据统计</a>--}}
            {{--</li>--}}
           {{--<!--  <li>--}}
                {{--<a href="/merchants/marketing/xcxShopNav">底部导航</a>--}}
            {{--</li> -->--}}
        {{--</ul>--}}
         <!-- 新建模板 开始 -->
        <div class="clearfix mgb20"></div>
        <div class="model_itmes mgb20">
            <a href="javascript:void(0);" class="btn btn-success" id="add_page">新建微页面</a>
            @if($status==1)
            <a href="javascript:void(0);" class="btn btn-default btn-small-program">访问小程序</a>
            @endif
            <!-- 分类&搜索 开始 -->
            <div class="category_search">
                <!-- 分类 开始 -->
                <!-- 搜索 开始 -->
                <label class="search_items">
                    <input class="search_input" type="text" name="title" value="{{ request('title') }}" placeholder="搜索"/>   
                </label>
                <!-- 搜索 结束 -->
                <!-- 分类 结束 -->
            </div>
            <!-- 分类&搜索 结束 -->
        </div>
        <!-- 新建模板 结束 -->
        <!-- 列表 开始 -->
        <table class="data-table table table-hover">
            <!-- 标题 -->
            <tr class="active">
                <td><label><input type="checkbox" id="all_shop">全选</label></td>
                <td>标题</td>
                <td>
                    创建时间
                </td>
                <td>操作</td>
            </tr>
            <!-- 列表 -->
        </table>
        <div style="position:relative">
            <button class="del_list">删除</button>
        </div>
        <!-- 列表 结束 -->
        <div class="page"></div>
    </div>
    <!-- 访问小程序 -->
    <div class="xcx-mask hide">
        <div class="xcx-wrap">
            <img class="xcx-wrap-close" src="{{ config('app.source_url') }}mctsource/images/guanbi-x.png" alt="">
            <dl>
                <dd>微信“扫一扫”访问小程序</dd>
                <dd style="height:262px;">
                    <img id="img_xcxm" src="" class="xcx-xcximg" />
                </dd>
                <dd data-url="pages/index/index" >
                    <a id="path_xcxm" data-url="pages/index/index" href="javascript:;">小程序路径</a>
                </dd>
                <dd>
                    <a id="down_xcxm" href="javascript:;">下载小程序二维码</a>
                </dd> 
            </dl>
        </div>
    </div>
    <!--add by 韩瑜 2018-9-20-->
    <!-- 新建微页面弹框 开始 -->
	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <!-- 弹框标题 开始 -->
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	                <h4 class="modal-title" id="myModalLabel">选择新建模板</h4>
	            </div>
	            <!-- 弹框标题 结束 -->
	            <!-- 弹框主体 开始 -->
	            <div class="modal-body">
	                主体
	            </div>
	            <!-- 弹框主体 结束 -->
	        </div>
	    </div>
	</div>
	<!-- 新建微页面弹框 结束 -->
	<!-- 微页面选择模板弹窗 -->
	<div class="widget-feature-template modal in" aria-hidden="false">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">×</a>
	        <h3 class="title">选择页面模版</h3>
	    </div>
	    <ul class="widget-feature-template-filter js-filter-wraper">
	        <li class="active">
	            <a href="javascript:;" class="js-filter" data-type="0">所有模版</a>
	        </li>
	        <li>
	            <a href="javascript:;" class="js-filter" data-type="1">美妆配饰</a>
	        </li>
	        <li>
	            <a href="javascript:;" class="js-filter" data-type="2">服饰衣帽</a>
	        </li>
	        <li>
	            <a href="javascript:;" class="js-filter" data-type="3">节日活动</a>
	        </li>
	        <li>
	            <a href="javascript:;" class="js-filter" data-type="4">官网展示</a>
	        </li>
            <li>
            <a href="javascript:;" class="js-filter" data-type="5">博渊书院</a>
            </li>
	    </ul>
	    <div class="modal-body">
	        <ul class="widget-feature-template-list clearfix">
	           
	        </ul>
	    </div>
	    <div class="modal-footer"></div>
	</div>
	<!-- 微页面选择模板弹窗 -->
	<!--end-->
@endsection

@section('page_js') 
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
<script src="{{ config('app.source_url') }}mctsource/js/litePage.js" type="text/javascript" charset="utf-8"></script>

@endsection