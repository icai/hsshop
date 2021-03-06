@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpj42f2.css" />
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
                    <a href="javascript:void(0)">满减送</a>
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
                    <li class="hover">
                        <a href="$status=1">所有满减送</a>
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
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【销售员】使用手册
                </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 开始 -->
        <!-- 列表模块 开始 -->
        <div class="clearfix">
            <div class="pull-left">
               <a class="btn btn-success mgb15" href="{{ URL('/merchants/marketing/achieveGive/add') }}">新建满减送</a>
            </div>
            <div class="pull-right w350 relative">
                <!-- 搜索 开始 -->
                <label class="search_items">
                    <input class="search_input" type="text" name="" value="" placeholder="搜索"/>   
                </label>
                <!-- 搜索 结束 -->
            </div>
        </div>
        <!-- 列表 开始 -->
        <table class="table table-hover f12">
            <tr class="active">
                <td>优惠券名称</td>
                <td>价值(元)</td>
                <td>领取限制</td>
                <td>有效期</td>
                <td>领取人/次</td>
                <td>已使用</td>
                <td>操作</td>
            </tr>
            <tr>
                <td>测试1</td>
                <td>
                    <p>1.00</p>
                    <p class="gray_999">最低消费: ￥12344.00</p>
                </td>
                <td>
                    <p>不限张数</p>
                    <p class="gray_999">库存：111</p>
                </td>
                <td>
                    <p>2016-11-30 09:47:42 至</p>
                    <p>2016-12-13 09:47:44</p>
                </td>
                <td>
                    <a class="blue_38f" href="javascript:void(0);">0</a>
                    / 0
                </td>
                <td>0</td>
                <td>
                    <a class="blue_38f" href="javascript:void(0);">编辑</a>
                    <a class="js_invalid blue_38f" href="javascript:void(0);">使失效</a>
                    <a class="blue_38f" href="javascript:void(0);">推广</a>
                    <a class="gray_999" href="javascript:void(0);">已失效</a>
                </td>
            </tr>
            <tr>
                <td>测试1</td>
                <td>
                    <p>1.00</p>
                    <p class="gray_999">最低消费: ￥12344.00</p>
                </td>
                <td>
                    <p>不限张数</p>
                    <p class="gray_999">库存：111</p>
                </td>
                <td>
                    <p>2016-11-30 09:47:42 至</p>
                    <p>2016-12-13 09:47:44</p>
                </td>
                <td>
                    <a class="blue_38f" href="javascript:void(0);">0</a>
                    / 0
                </td>
                <td>0</td>
                <td>
                    <a class="blue_38f" href="javascript:void(0);">编辑</a>
                    <a class="js_invalid blue_38f" href="javascript:void(0);">使失效</a>
                    <a class="blue_38f" href="javascript:void(0);">推广</a>
                    <a class="gray_999" href="javascript:void(0);">已失效</a>
                </td>
            </tr>
        </table>
        <!-- 列表 结束 -->
        <!-- 列表模块 结束 -->
    </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection