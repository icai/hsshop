@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_i8y27jkv.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">赠品</a>
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
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix">
            <!-- 左侧 开始 -->
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li  class="hover">
                        <a href="赠品.html">所有赠品</a>
                    </li>
                    <li>
                        <a href="$status=2">未开始</a>
                    </li>
                    <li>
                        <a href="$status=3">进行中</a>
                    </li>
                    <li>
                        <a href="$status=4">已结束</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程 </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- 新建模块 开始 -->
        <div class="widget-list-filter clearfix">
            <!-- 左边 开始 -->
            <a class="btn btn-success" href="{{ URL('/merchants/marketing/gift/add') }}">新建赠品</a>
            <!-- 左边 结束 -->
            <!-- 右边 开始 -->
            <div class="pull-right relative w350">
                <!-- 搜索 开始 -->
                <label class="search_items">
                    <input class="search_input" type="text" name="" value="" placeholder="搜索"/>
                </label>
                <!-- 搜索 结束 -->
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 新建模块 结束 -->
        <!-- 列表模块 开始 -->
        <!-- 列表 开始 -->
        <table class="table table-hover f12">
            <tr class="active">
                <td>活动名称</td>
                <td>有效时间</td>
                <td>活动状态</td>
                <td>已赠送</td>
                <td>已领取</td>
                <td>操作</td>
            </tr>
            <tr>
                <td>活动1</td>
                <td>2016-10-31 14:45:00 至 2016-11-22 14:44:00</td>
                <td>已结束</td>
                <td>0</td>
                <td>0</td>
                <td>
                    <a class="blue_38f" href="javascript:void(0);">编辑</a>-
                    <a class="blue_38f" href="javascript:void(0);">使失效</a>-
                    <a class="blue_38f" href="javascript:void(0);">删除</a>
                    <a class="gray_999" href="javascript:void(0);">已失效</a>
                </td>
            </tr>
        </table>
        <!-- 列表 结束 -->
        <!-- 列表模块 结束 -->
    </div>
@endsection

@section('page_js')
    <!--主要内容js文件-->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_discount.js" type="text/javascript" charset="utf-8"></script>
@endsection