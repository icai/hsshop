@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_i0v42sqi.css" />
@endsection
@section('slidebar')
    @include('merchants.capital.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <!-- 二级导航三级标题 开始 -->
            <div class="third_title">申请提现</div>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title clearfix">
                    <div class="pull-left">申请提现</div>
                    <a class="pull-right blue_38f f12" href="javascript:void(0);">不加收手续费的说明</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">可提现金额：</label>
                        <div class="col-sm-10"><span class="withdraw_money">0.00 </span>元</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><em class="red mgr5">*</em>选择提现银行：</label>
                        <div class="col-sm-7">
                            <!-- 银行卡管理 -->
                            <ul class="bank_wrap">
                                <li class="active clearfix">
                                    <div class="pull-left">中国银行 <span class="gray_999">[默认]</span></div>
                                    <div class="cardId">陈琪誉（****9888）</div>
                                    <!-- 管理 -->
                                    <div class="card_manage pull-right">
                                        <div class="manage_header">
                                            <span class="blue_38f">管理</span><i class="glyphicon glyphicon-triangle-bottom"></i>
                                        </div>
                                        <div class="manage_body">
                                            <a class="blue_38f f12" href="javascript:void(0);">修改</a>
                                            <a class="blue_38f f12" href="javascript:void(0);">删除</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="clearfix">
                                    <div class="pull-left">中国银行 <span class="gray_999">[默认]</span></div>
                                    <div class="cardId">陈琪誉（****9888）</div>
                                    <!-- 管理 -->
                                    <div class="card_manage pull-right">
                                        <div class="manage_header">
                                            <span class="blue_38f">管理</span><i class="glyphicon glyphicon-triangle-bottom"></i>
                                        </div>
                                        <div class="manage_body">
                                            <a class="blue_38f f12" href="javascript:void(0);">修改</a>
                                            <a class="blue_38f f12" href="javascript:void(0);">删除</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <a class="f12 blue_38f" href="{{ URL('/merchants/capital/withdrawalSetting') }}">添加银行卡</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><em class="red mgr5">*</em>提现金额：</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" placeholder="请输入提现金额">
                        </div>
                        <div class="col-sm-1">元</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">提现审核周期：</label>
                        <div class="col-sm-10">
                            <p>1个工作日完成</p>
                            <p class="gray_999">提现咨询专线：0571-87796692，服务时间：10:00-18:00</p>
                            <a class="blue_38f f12" href="javascript:void(0);">在线客服咨询</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="btn btn-primary">确认提现</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/capital_p0dx3ur8.js"></script>
@endsection