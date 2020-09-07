@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_udwqf70g.css" />
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
                    <a href="javascript:void(0)">新增订单返现</a>
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
        <div class="display_box mgb15">
            <!-- 导航 开始 -->
            <ul class="module_nav">
                <li  class="hover">
                    <a href="$status=1">所有订单返现</a>
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
            <!-- 消息说明 开始 -->
            <a class="message_tip f12 box_flex1 blue_38f" href="javascript:void(0);" target="_blank">
                <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【团队管理】使用教程
            </a>
            <!-- 消息说明 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- 标题 开始 -->
        <div class="addTitle_items mgb30">设置订单返现</div>
        <!-- 标题 结束 -->
        <!-- 表单 开始 -->
        <form id="backForm" action="" post="">
            <!-- 表单块标题 -->
            <strong class="f14">活动信息</strong>
            <!-- 活动名称 -->
            <label class="group-list">
                        <span class="group-name">
                            <em class="red">*</em>活动名称：
                        </span>
                <div class="group-content display_box">
                    <div class="controls form-group">
                        <input class="form-control" type="text" name="names" value="" placeholder="请填写活动名称" />
                    </div>
                </div>
            </label>
            <!-- 生效时间 -->
            <label class="group-list">
                        <span class="group-name">
                            <em class="red">*</em>生效时间：
                        </span>
                <div class="group-content">
                    <div class="controls display_box">
                        <div class="form-group w250">
                            <div id="startTime" class="input-group">
                                <input class="form-control" type="text" name="start_time" value="" placeholder="请填写开始时间"/>
                                <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                            </div>
                        </div>
                        <span class="link">至</span>
                        <div class="form-group w250">
                            <div id="endTime" class="input-group">
                                <input class="form-control" type="text" name="end_time" value="" placeholder="请填写结束时间" />
                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="gray_999 f12">返现活动有效周期不能超过15天</div>
                </div>
            </label>
            <!-- 表单块标题 -->
            <strong class="f14">返现方式：</strong>
            <!-- 返现方式 -->
            <div class="group-list">
                        <span class="group-name">
                            <em class="red">*</em>返现方式：
                        </span>
                <div class="group-content">
                    <div class="controls form-group display_box w250">
                        <label>
                            <input class="cashback" data-back="interval_random" type="radio" name="cashback_method" value="random" checked />随机返现
                        </label>
                        <label>
                            <input class="cashback" data-back="interval_fixed" type="radio" name="cashback_method" value="fixed" />固定返现
                        </label>
                    </div>
                </div>
            </div>
            <!-- 返现区间 -->
            <div class="group-list">
                        <span class="group-name">
                            <em class="red">*</em>返现区间：
                        </span>
                <div class="group-content display_box">
                    <div class="controls form-group">
                        <label class="interval_random interval">
                            <input class="form-control w30" type="text" name="cashback_start" value="" />
                        </label>
                    </div>
                    <div class="controls form-group w150">
                        <label class="interval_fixed interval">
                            %&nbsp;至&nbsp;&nbsp;&nbsp;<input class="form-control w30" type="text" name="cashback_end" value="" /> %
                        </label>
                    </div>
                </div>
            </div>
            <!-- 返现限制 -->
            <div class="group-list">
                        <span class="group-name">
                            <em class="red">*</em>返现限制：
                        </span>
                <div class="group-content display_box">
                    <label>
                        前
                        <div class="limit_items form-group">
                            <input class="form-control w30" type="text" name="cashback_limit" value="" />
                        </div> 笔订单
                    </label>
                    <div class="tip_module gray_999 f12">
                        (活动时间内每个买家在该店的前N笔订单)
                        <i class="glyphicon glyphicon-question-sign gray_999 f14"></i>
                        <div class="tip_items">例如：前5笔订单 ，表示活动时间内每位买家在该店的前5笔订单（含第5笔）都返现</div>
                    </div>
                </div>
            </div>
            <!-- 表单块标题 -->
            <strong class="f14">返现方式：</strong>
            <!-- 活动商品 -->
            <div class="group-list">
                        <span class="group-name">
                            <em class="red">*</em>活动商品：
                        </span>
                <div class="group-content display_box">
                    <label>
                        <input type="radio" name="" value="" checked />全部商品参与
                    </label>
                </div>
            </div>
            <!-- 保存 -->
            <div class="group-list">
                <span class="group-name"></span>
                <div class="group-content display_box">
                    <a class="btn btn-default" href="{{ URL('/merchants/marketing/orderCash') }}">取消</a>
                    <input class="submit_btn btn btn-primary" type="submit" name="" value="保存" />
                </div>
            </div>
        </form>
        <!-- 表单 结束 -->
    </div>

@endsection

@section('page_js')
    <!--主要内容js文件-->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/marketing_oc5hqm63.js"></script>
@endsection