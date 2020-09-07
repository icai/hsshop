@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_pkexn5x0.css" />
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
                    <a href="javascript:void(0)">验证工具</a>
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
                    <li>
                        <a href="{{ URL('/merchants/marketing/verifycard') }}">验证卡券</a>
                    </li>
                    <li class="hover">
                        <a href="{{ URL('/merchants/marketing/verifycard/promocard') }}">优惠券验证记录</a>
                    </li>
                    <li>
                        <a href="{{ URL('/merchants/marketing/verifycard/promocode') }}">优惠码验证记录</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <a class="f12 box_flex1 blue_38f" href="javascript:void(0);" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程
                </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- 列表过滤部分 开始 -->
        <div class="widget-list-filter clearfix">
            <!-- 左边 开始 -->
            <div class="pull-left">
                <a class="btn btn-success" href="{{ URL('/merchants/marketing/verifycard') }}">验证卡券</a>
            </div>
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
        <!-- 列表过滤部分 结束 -->
        <!-- 筛选模块 开始 -->
        <div class="screen_module">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label class="col-sm-2 control-label">卡券名称：</label>
                    <div class="col-sm-10">
                        <div class="col-sm-2">
                            <select class="category_select" data-placeholder="全部卡券" tabindex="1">
                                <option value=""></option>
                                <option value="United States">United States</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Afghanistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">验证时间：</label>
                        <div class="col-sm-4">
                            <div class='input-group date' id='start_time'>
                                <input type='text' class="form-control" />
                                <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                            </div>
                        </div>
                        <div class="col-sm-4 center_start">
                            <div style="margin-left:-20px">至</div>&nbsp;&nbsp;&nbsp;
                            <div class='input-group date' id='end_time'>
                                <input type='text' class="form-control" />
                                <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-8">
                        <a class="btn btn-primary" href="javascript:void(0);">筛选</a>
                        <a class="btn btn-default">导出验证记录</a>
                    </div>
                </div>
            </form>

        </div>
        <!-- 筛选模块 结束 -->
        <!-- 列表模块 开始 -->
        <table class="table table-hover f12">
            <tr class="active">
                <td>时间</td>
                <td>优惠券</td>
                <td>核销券</td>
                <td>卡券名称</td>
                <td>使用限制</td>
                <td>验证人员</td>
            </tr>
            <tr>
                <td>2016-12-05 11:40:59</td>
                <td>774231967734</td>
                <td></td>
                <td>
                    测试优惠券
                    <p class="gray_999">价值：100.00元</p>
                </td>
                <td>200.00</td>
                <td>ronglinfang</td>
            </tr>
            <tr>
                <td>2016-12-05 11:40:59</td>
                <td>774231967734</td>
                <td></td>
                <td>
                    测试优惠券
                    <p class="gray_999">价值：100.00元</p>
                </td>
                <td>200.00</td>
                <td>ronglinfang</td>
            </tr>
        </table>
        <!-- 列表模块 结束 -->
        <!-- 空列表 开始 -->
        <div class="no_result">还没有相关数据</div>
        <!-- 空列表 结束 -->
    </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection