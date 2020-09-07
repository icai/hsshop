@extends('merchants.default._layouts')
@section('head_css')
<!-- 时间插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_hbesnrcx.css" />
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
                <!-- 横幅 开始 -->
                <div class="message_notice_warning red">
                    您店铺当前剩余短信条数为0啦，赶快去充值吧！ <a class="blue_38f" href="{{URL('/merchants/marketing/msgrecharge')}}">立即充值</a>
                </div>
                <!-- 横幅 结束 -->
                <!-- 导航模块 开始 -->
                <div class="nav_module clearfix">
                    <!-- 左侧 开始 -->
                    <div class="pull-left">
                        <!-- 导航 开始 -->
                        <!-- 导航 开始 -->
                        <ul class="tab_nav">
                            <li>
                                <a href="{{URL('/merchants/marketing/messagepush')}}">消息推送</a>
                            </li>
                            <li>
                                <a href="{{URL('/merchants/marketing/pushstatistics')}}">推送统计</a>
                            </li>
                            <li class="hover">
                               <a>短信充值</a>
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
                <!-- 充值操作 开始 -->
                <div class="rechange_items display_box mgb15">
                    <!-- 手动充值 开始 -->
                    <div class="box_flex1">
                        <div class="panel panel-default">
                            <!-- 面板头部 开始 -->
                            <div class="panel-heading">
                                <!-- 手动充值 开始 -->
                                <div class="common_top">
                                    <span class="common_line"></span>
                                    <p class="common_title">手动输入</p>
                                    <div class="common_link"></div>
                                    <!-- 按钮 开始 -->
                                    <div class="switch_items">
                                        <input type="checkbox" checked name="" value="" />
                                        <label></label>
                                    </div>
                                    <!-- 按钮 结束 -->
                                </div>
                                <!-- 手动充值 结束 -->
                            </div>
                            <!-- 面板头部 结束 -->
                            <!-- 面板主体 开始 -->
                            <div class="panel-body">
                                <form class="form-horizontal" action="" method="post">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">当剩余条数少于：</label>
                                        <div class="col-sm-5">
                                            <select name="">
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="300">300</option>
                                                <option value="500">500</option>
                                                <option value="1000">1000</option>
                                            </select>
                                            <span class="unit">条</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">自动充值</label>
                                        <div class="col-sm-5">
                                            <select name="">
                                                <option value="1">1000条/50元</option>
                                                <option value="6">6000条/300元</option>
                                                <option value="3">10000条/500元</option>
                                                <option value="7">20000条/950元</option>
                                                <option value="8">40000条/1800元</option>
                                                <option value="9">100000条/4000元</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label"></label>
                                        <div class="col-sm-5">
                                            <input class="submit_btn btn btn-primary" type="submit" name="" value="保存" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- 面板主体 结束 -->
                        </div>
                    </div>
                    <!-- 手动充值 结束 -->
                    <!-- 自动充值 开始 -->
                    <div class="box_flex1">
                        <div class="panel panel-default">
                            <!-- 面板头部 开始 -->
                            <div class="panel-heading">
                                <div class="common_top">
                                    <span class="common_line"></span>
                                    <p class="common_title">自动充值</p>
                                    <div class="common_link"></div>
                                </div>
                            </div>
                            <!-- 面板头部 结束 -->
                            <!-- 面板主体 开始 -->
                            <div class="panel-body">
                                <!-- 手动充值列表 开始 -->
                                <ul class="rechange_list">
                                    <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#rechange_model">
                                            <p class="num">
                                                <span class="orange_f70">1000</span>条
                                            </p>
                                            <p class="price">
                                                <span class="gray_999 f12"><strong>50元</strong>(0.050元/条)</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#rechange_model">
                                            <p class="num">
                                                <span class="orange_f70">6000</span>条
                                            </p>
                                            <p class="price">
                                                <span class="gray_999 f12"><strong>300元</strong>(0.050元/条)</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#rechange_model">
                                            <p class="num">
                                                <span class="orange_f70">10000</span>条
                                            </p>
                                            <p class="price">
                                                <span class="gray_999 f12"><strong>500元</strong>(0.050元/条)</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#rechange_model">
                                            <p class="num">
                                                <span class="orange_f70">20000</span>条
                                            </p>
                                            <p class="price">
                                                <span class="gray_999 f12"><strong>950元</strong>(0.048元/条)</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#rechange_model">
                                            <p class="num">
                                                <span class="orange_f70">40000</span>条
                                            </p>
                                            <p class="price">
                                                <span class="gray_999 f12"><strong>1800元</strong>(0.045元/条)</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#rechange_model">
                                            <p class="num">
                                                <span class="orange_f70">100000</span>条
                                            </p>
                                            <p class="price">
                                                <span class="gray_999 f12"><strong>4000元</strong>(0.040元/条)</span>
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                                <!-- 手动充值列表 结束 -->
                            </div>
                            <!-- 面板头部 结束 -->
                        </div>
                    </div>
                    <!-- 自动充值 结束 -->
                </div>
                <!-- 充值操作 结束 -->
                <!-- 区域标题 开始 -->
                <div class="common_top mgb15">
                    <span class="common_line"></span>
                    <p class="common_title">充值记录</p>
                    <div class="common_link"></div>
                </div>
                <!-- 区域标题 结束 -->
                <!-- 充值记录表格 开始 -->
                <table class="table table-bordered table-hover">
                    <tr class="active">
                        <td>充值时间</td>
                        <td>充值金额</td>
                        <td>充值条数</td>
                        <td>充值来源</td>
                    </tr>
                </table>
                <!-- 充值记录表格 结束 -->
                <div class="no_result">暂无数据!</div>
@endsection
@section('other')
 <!-- Modal -->
    <div class="modal fade" id="rechange_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">短信充值</h4>
                </div>
                <div class="modal-body">
                    <!-- 列表 开始 -->
                    <div class="modal_list">
                        <dl>
                            <dt>短信充值店铺：</dt>
                            <dd>拿去用把161118</dd>
                        </dl>
                        <dl>
                            <dt>当前剩余：</dt>
                            <dd>0条</dd>
                        </dl>
                        <dl>
                            <dt>充值条数：</dt>
                            <dd>1000条</dd>
                        </dl>
                        <dl>
                            <dt>应付金额：</dt>
                            <dd>50.00元</dd>
                        </dl>
                        <dl>
                            <dt>支付方式：</dt>
                            <dd>店铺余额支付</dd>
                        </dl>
                        <dl>
                            <dt></dt>
                            <dd>
                                <span class="read-agreement">
                                    阅读并同意<a class="blue_38f" href="javascript:void(0);" target="_blank">《短信充值协议》</a>
                                </span>
                            </dd>
                        </dl>
                        <dl>
                            <dt></dt>
                            <dd>
                                <a class="btn btn-danger" href="#/资产/充值">充值</a>
                                <div class="modal_tip">
                                    <span class="red">提醒：</span>当前店铺余额不足，请先充值，成功后再进行订购
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <!-- 列表 结束 -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<!-- 时间插件 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment/moment.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment/locales.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.js"></script>
    <!-- 时间插件 文件 -->
    <script src="{{ config('app.source_url') }}static/js/laydate/laydate.js"></script>
    <!-- 核心 base.js JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
@endsection