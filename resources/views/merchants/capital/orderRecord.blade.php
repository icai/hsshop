@extends('merchants.default._layouts')
@section('head_css')
<!-- 时间插件 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_o3qeceb3.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{ URL('/merchants/capital/myService') }}">我的服务</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/capital/orderRecord') }}">订购记录</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
    <!-- 导航 开始 -->
    <ul class="nav nav-tabs mgb15" role="tablist">
        <li role="presentation" class="active">
            <a href="javascript:void(0);">所有服务</a>
        </li>
        <li role="presentation">
            <a href="javascript:void(0);">即将到期</a>
        </li>
        <li role="presentation">
            <a href="javascript:void(0);">已过期</a>
        </li>
    </ul>
    <!-- 导航 结束 -->
    <!-- 数据检索 开始 -->
    <div class="dataSearch_wrap mgb15 f12">
        <!-- 检索 开始 -->
        <div class="screen_module">
            <form class="form-horizontal" role="form">
                <!-- 验证时间： -->
                <div class="form-group cleatfix">
                    <label class="col-sm-1 control-label rewrite-bootstrap">服务名称：</label>
                    <div class="col-sm-2 rewrite-bootstrap">
                        <select class="form-control">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <label class="col-sm-1 control-label rewrite-bootstrap">起止时间：</label>
                    <div class="col-sm-3 center_start rewrite-bootstrap">
                        <!-- 开始时间 -->
                        <div id='start_time' class='input-group'>
                            <input class="form-control f12" type='text' />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div><span class="pull-left">至</span>
                    <div class="col-sm-3 rewrite-bootstrap">
                        <!-- 结束时间 -->
                        <div id='end_time' class='input-group'>
                            <input class="form-control f12" type='text' />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label rewrite-bootstrap">服务类型：</label>
                    <div class="col-sm-2 rewrite-bootstrap">
                        <select class="form-control">
                            <option value="0">全部</option>
                            <option value="1">已提现</option>
                            <option calue="2">进行中</option>
                        </select>
                    </div>
                    <label class="col-sm-1 control-label rewrite-bootstrap">订购状态：</label>
                    <div class="col-sm-2 rewrite-bootstrap">
                        <select class="form-control">
                            <option value="0">全部</option>
                            <option value="1">已提现</option>
                            <option calue="2">进行中</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label rewrite-bootstrap"></label>
                    <div class="col-sm-3">
                        <a class="btn btn-primary" href="javascript:void(0);">筛选</a>
                    </div>
                </div>
            </form>
            <!-- 时间 -->    
        </div>
        <!-- 检索 结束 -->
    </div>
    <!-- 数据检索 结束 -->
    <!-- 列表 开始 -->
    <table class="table table-bordered table-hover">
        <tr class="active">
            <td>服务名称</td>
            <td>服务类型</td>
            <td>到期时间</td>
            <td>服务状态</td>
            <td>操作</td>
        </tr>
        <tr>
            <td colspan="5">
                没有相关服务记录哟～去
                <a class="blue_38f"　href="服务订购.html">服务市场</a>
                逛逛吧
            </td>
        </tr>
    </table>
    <!-- 列表 结束 -->
</div>
@endsection
@section('page_js')
<!-- 时间插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_o3qeceb3.js"></script>
@endsection