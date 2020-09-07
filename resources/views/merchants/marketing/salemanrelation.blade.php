@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_base.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0);">消息推送</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->
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
            <!-- 巨幕 开始 -->
            <div class="faceplate_module mgb15">
                <!-- 巨幕 结束 -->
                <div class="faceplate_module mgb15">
                    <div class="container-fluid">
                        <!-- 巨幕内容 开始 -->
                        <div class="faceplate_content col-sm-9">
                            <strong class="f16 mgb15">消息推送</strong>
                            <p class="f12">消息推送功能可以让您通过短信和微信公众号，给买家推送交易和物流相关的提醒消息，包括订单催付、发货、签收、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。支付成功、供应商订单、采购单、维权的短信目前仍由会搜云免费发送</p>
                        </div>
                        <!-- 巨幕内容 结束 -->
                    </div>
                </div>
            </div>
            <!-- 巨幕 结束 -->
            <!-- 横幅 开始 -->
            <div class="message_notice_warning red">
                您店铺当前剩余短信条数为0啦，赶快去充值吧！ <a class="blue_38f" href="短信充值.html">立即充值</a>
            </div>
            <!-- 横幅 结束 -->
            <div class="nav_module clearfix">
                <!-- 左侧 开始 -->
                <div class="pull-left">
                    <!-- 导航 开始 -->
                    <ul class="tab_nav">
                        <li  class="hover">
                            <a href="{{URL('/merchants/marketing/messagepush')}}">消息推送</a>
                        </li>
                        <li>
                            <a href="{{URL('/merchants/marketing/pushstatistics')}}">推送统计</a>
                        </li>
                        <li>
                            <a href="{{URL('/merchants/marketing/msgrecharge')}}">短信充值</a>
                        </li>
                    </ul>
                    <!-- 导航 结束 -->
                </div>
                <!-- 左侧 结算 -->
                <!-- 右边 开始-->
                <div class="pull-right">
                    <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                        <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程 
                    </a>
                </div>
                <!-- 右边 结束 -->
            </div>
            <!-- 导航模块 结束 -->
            <!-- 区域标题 开始 -->
            <div class="common_top mgb15">
                <span class="common_line"></span>
                <p class="common_title">销售员关系提醒设置</p>
                <div class="common_link"></div>
            </div>
            <!-- 区域标题 结束 -->
            <!-- 表单 开始 -->
            <form class="form-horizontal" method="post" action="">
                    <!-- 设置 -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">销售员关系提醒：</label>
                        <div class="col-sm-10">
                            <!-- 按钮 开始 -->
                            <div class="switch_items">
                                <input type="checkbox" checked name="" value="" />
                                <label></label>
                            </div>
                            <!-- 按钮 结束 -->
                        </div>
                    </div>
                    <hr class="split_line" />
                    <!-- 发送时间点 -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送时间点：</label>
                        <div class="col-sm-10">买家付款后立即发送</div>
                    </div>
                    <!-- 发送时间段 -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送时间段：</label>
                        <div class="col-sm-10">
                            <div class="center_start">
                                每日&nbsp;&nbsp;
                                <select class="form-control w100" name="">
                                    <option value="0">00:00</option>
                                    <option value="1">01:00</option>
                                    <option value="2">02:00</option>
                                    <option value="3">03:00</option>
                                    <option value="4">04:00</option>
                                    <option value="5">05:00</option>
                                    <option value="6">06:00</option>
                                    <option value="7">07:00</option>
                                    <option value="8">08:00</option>
                                    <option value="9">09:00</option>
                                    <option value="10">10:00</option>
                                    <option value="11">11:00</option>
                                    <option value="12">12:00</option>
                                    <option value="13">13:00</option>
                                    <option value="14">14:00</option>
                                    <option value="15">15:00</option>
                                    <option value="16">16:00</option>
                                    <option value="17">17:00</option>
                                    <option value="18">18:00</option>
                                    <option value="19">19:00</option>
                                    <option value="20">20:00</option>
                                    <option value="21">21:00</option>
                                    <option value="22">22:00</option>
                                    <option value="23">23:00</option>
                                </select>
                                &nbsp;&nbsp;到&nbsp;&nbsp;
                                <select class="form-control w100" name="">
                                    <option value="1">01:00</option>
                                    <option value="2">02:00</option>
                                    <option value="3">03:00</option>
                                    <option value="4">04:00</option>
                                    <option value="5">05:00</option>
                                    <option value="6">06:00</option>
                                    <option value="7">07:00</option>
                                    <option value="8">08:00</option>
                                    <option value="9">09:00</option>
                                    <option value="10">10:00</option>
                                    <option value="11">11:00</option>
                                    <option value="12">12:00</option>
                                    <option value="13">13:00</option>
                                    <option value="14">14:00</option>
                                    <option value="15">15:00</option>
                                    <option value="16">16:00</option>
                                    <option value="17">17:00</option>
                                    <option value="18">18:00</option>
                                    <option value="19">19:00</option>
                                    <option value="20">20:00</option>
                                    <option value="21">21:00</option>
                                    <option value="22">22:00</option>
                                    <option value="23">23:00</option>
                                    <option value="24">24:00</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 发送方式 -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送方式：</label>
                        <div class="col-sm-10">
                            <!-- 面板1 开始 -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="" value="" />手机短信
                                    </label>&nbsp;
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="" value="" />微信粉丝消息（需要认证公众号）
                                    </label>
                                </div>
                                <div class="panel-body">
                                    <div class="bulletin_nav">
                                        <ul class="tab_nav">
                                            <li class="hover" data-tab="tab0">
                                                <a href="javascript:void(0);">物流快递</a>
                                            </li>
                                            <li data-tab="tab1">
                                                <a href="javascript:void(0);">货到付款</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab_body mg15">
                                        <div class="tab0 tab_items">您购买的“<商品名称>”已于<发货时间>，使用<快递公司名>发货啦！快递单号：<快递单号>。跟踪物流详细<跟踪物流链接>
                                        </div>
                                        <div  class="tab1 tab_items no">您的【货到付款】订单已发货，将由<快递公司名称>为您配送！快递单号：<快递单号>。请当面验收后再支付货款。跟踪物流详细<跟踪物流链接></div>        
                                    </div>
                                </div>
                            </div>
                            <!-- 面板1 结束 -->
                            <!-- 面板2 开始 -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="clearfix">
                                        <div class="pull-left">
                                            <label>
                                                <input type="checkbox" name="" value="" />
                                                微信模版消息
                                            </label>
                                        </div>
                                        <div class="pull-right">
                                            <!-- 提示 开始 -->
                                            <div class="note_tip" data-toggle="popover">
                                                <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>代发
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="bulletin_nav">
                                        <ul class="tab_nav">
                                            <li class="hover" data-tab="tab0">
                                                <a href="javascript:void(0);">物流快递</a>
                                            </li>
                                            <li data-tab="tab1">
                                                <a href="javascript:void(0);">货到付款</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab_body mg15">
                                        <div class="tab0 tab_items">
                                            <!-- 公共栏 开始 -->
                                            <div class="bulletin_board">
                                                <strong class="f20">订单支付成功</strong>
                                                <p class="f12 mg30">8月8日</p>
                                                <div class="f14">亲，您的宝贝已在路上啦，正全速向您飞奔～<br/><br/>
                                                    订单号：E88888888888888<br>
                                                    物流公司：<快递公司><br/>
                                                    物流单号：888888888888<br/><br/>
                                                    点击查看完整的物流信息
                                                </div>       
                                                <a class="more_bulletin" href="javascript:void(0);">详情</a>
                                            </div>
                                            <!-- 公告栏 结束 -->
                                        </div>
                                        <div class="tab1 tab_items no">
                                            <!-- 公共栏 开始 -->
                                            <div class="bulletin_board">
                                                <strong class="f20">订单支付成功</strong>
                                                <p class="f12 mg30">8月8日</p>
                                                <div class="f14">亲，您的宝贝已在路上啦，正全速向您飞奔～～<br/><br/>
                                                    订单号：E88888888888888<br>
                                                    物流公司：<快递公司><br/>
                                                    物流单号：888888888888<br/><br/>
                                                    您选择的是货到付款，收货后请当面验收再支付货款哟～<br/>
                                                    点击查看完整的物流信息
                                                </div>     
                                                <a class="more_bulletin" href="javascript:void(0);">详情</a>
                                            </div>
                                            <!-- 公告栏 结束 -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 面板2 结束 -->
                        </div>
                    </div>
                    <!-- 保存 -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <input class="btn btn-primary" type="submit" name="" value="提交" />
                        </div>
                    </div>
                </form>
            <!-- 表单 结束 -->
    </div>
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_5ehay1bt.js"></script>
@endsection