@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_65l45ny9.css" />
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
                    <li class="hover">
                        <a href="{{ URL('/merchants/marketing/verifycard') }}">验证卡券</a>
                    </li>
                    <li>
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
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程 </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <div class="coupons_module">
            <!-- 搜索 开始 -->
            <form id="volidForm" class="form-horizontal" action="" method="">
                <div class="form-group">
                    <div class="col-sm-11 input-group">
                        <input class="search_input form-control" type="text" name="code" value="" placeholder="请输入或搜索优惠核销码（12位数字，在“卡券二维码”中可以找到）" />
                        <div class="clear_btn input-group-addon">
                            <i class="glyphicon glyphicon-remove-circle f16"></i>
                        </div>
                        <!-- 搜索按钮 -->
                        <div class="search_btn input-group-addon">
                            <i class="glyphicon glyphicon-search f16"></i>
                        </div>
                    </div>
                </div>
                <span id="checkMessage"></span>
            </form>
            <!-- 搜索 结束 -->
            <!-- 验证主体 开始 -->
            <div class="validate_main">
                <!-- 进度 开始 -->
                <div class="schedule_module">
                    <!-- 图片区 开始 -->
                    <div class="img_wrap">
                        <img src="images/intro.png" />
                    </div>
                    <!-- 图片区 结束 -->
                    <!-- 进度 开始 -->
                    <div class="schedule_items">
                        <div class="schedule_list" data-schedule="1">
                            <strong>搜索卡券</strong>
                            <p class="gray_999 f12">请顾客出示卡券并点击“卡券二维码”，<br>输入二维码下方的优惠核销码</p>
                        </div>
                        <div class="schedule_list" data-schedule="2">
                            <strong>验证</strong>
                            <p class="gray_999 f12">本系统提供有效期验证，其他信息请自行核对</p>
                        </div>
                        <div class="schedule_list" data-schedule="3">
                            <strong>验证完成</strong>
                            <p class="gray_999 f12">验证完成后，可在“验证记录”查看相关验证信息</p>
                        </div>
                    </div>
                    <!-- 进度 结束 -->
                </div>
                <!-- 进度 结束 -->
                <!-- 验证模块 开始 -->
                <div class="validate_module">
                    <!-- 卡券标题 开始 -->
                    <div class="validate_header display_box">
                        <!-- 卡券logo -->
                        <div class="coupons_logo img_wrap">
                            <img src="{{ config('app.source_url') }}mctsource/images/shop2.png" />
                        </div>
                        <!-- 卡券主体 -->
                        <div class="coupons_content box_flex1 mgl10">
                            <div class="display_box mgb15">
                                <!-- 名称 -->
                                <p class="w250">测试优惠码</p>
                                <p class="box_flex1 green_4b0">有效卡券</p>
                            </div>
                            <p class="gray_999 mgb15">有效期 2016-12-05 10:42:03 - 2016-12-30 10:41:48</p>
                            <p class="gray_999">序列号 --</p>
                        </div>
                    </div>
                    <!-- 卡券标题 结束 -->
                    <!-- 卡券用途 开始 -->
                    <div class="validate_body">
                        <div class="coupons_list">
                            <b>卡券价值：</b>100.00元
                        </div>
                        <div class="coupons_list">
                            <b>使用限制：</b>消费满 200.00 元可用
                        </div>
                        <div class="coupons_list">
                            <b>使用说明：</b>ces
                        </div>
                    </div>
                    <!-- 卡券用途 结束 -->
                    <!-- 保存 开始 -->
                    <div class="validate_bottom">
                        <button type="button" class="mgb10 btn btn-primary">验证卡券</button>
                        <p class="f12 gray_999 center">卡券验证后不可撤回</p>
                    </div>
                    <!-- 保存 结束 -->
                </div>
                <!-- 验证模块 结束 -->
                <!-- 验证成功模块 开始 -->
                <div class="validate_success">
                    <div class="success_body display_box">
                        <i class="glyphicon glyphicon-ok-circle green"></i>
                        <div class="box_flex1 mgl10">
                            <p class="f16 mgb10">已成功验证卡券</p>
                            <p class="f12">卡券已使用。可在"<a class="blue_38f" href="验证记录.html">验证记录</a>"查看相关验证信息。</p>
                        </div>
                    </div>
                    <!-- 保存 开始 -->
                    <div class="validate_bottom">
                        <a class="mgb10 btn btn-primary" href="卡券验证.html">继续验证</a>
                        <p class="f12 gray_999 center">卡券验证后不可撤回</p>
                    </div>
                    <!-- 保存 结束 -->
                </div>
                <!-- 验证成功模块 结束 -->
            </div>
            <!-- 验证主体 结束 -->
        </div>
    </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection