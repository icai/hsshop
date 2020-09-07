@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_ntftu6xo.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">交易记录</div>
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
    <form class="form-horizontal ui-box list-filter-form" method="get" action="">
        <div class="clearfix">
            <div class="filter-groups">
                <div class="control-group">
                    <label class="control-label">
                        <select name="field" class="js-label-select" id="infoFilter">
                            <option value="0" selected>订单号</option>
                        </select>
                    </label>
                    <div class="controls">
                        <input type="text" name="search" id="infoFilterValue" class="js-order-text" value="">
                    </div>
                </div>
            </div>
            <div class="pull-left">
                <div class="time-filter-groups clearfix">
                    <div class="control-group">
                        <label class="control-label">下单时间：</label>
                        <div class="controls">
                            <input type="text" name="start_time" value="{{ request('start_time') }}" class="js-start-time hasDatepicker" id="startDate">
                            <span>至</span>
                            <input type="text" name="end_time" value="{{ request('end_time') }}" class="js-end-time hasDatepicker" id="endDate">
                            <span class="date-quick-pick" data-days="7">近7天</span>
                            <span class="date-quick-pick" data-days="30">近30天</span>
                            <input class="zent-btn zent-btn-primary js-filter" style="width: 100px;line-height: 28px;margin-left: 10px;" type="submit" value="筛选" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- 筛选 开始 -->
    <!-- 筛选 结束 -->
    <!-- 导航 开始 -->
    <ul class="trade_nav mgb15">
        <li @if(empty($status)) class="hover" @endif >
            <a href="{{ URL('/merchants/capital/transactionRecord') }}">全部</a>
        </li>
        <li  @if($status == 1 ) class="hover" @endif >
            <a href="{{ URL('/merchants/capital/transactionRecord/1') }}">进行中</a>
        </li>
        <li @if($status == 2 ) class="hover" @endif  >
            <a href="{{ URL('/merchants/capital/transactionRecord/2') }}">退款</a>
        </li>
        <li @if($status == 3 ) class="hover" @endif  >
            <a href="{{ URL('/merchants/capital/transactionRecord/3') }}">成功</a>
        </li>
        <li @if($status == 4 ) class="hover" @endif  >
            <a href="{{ URL('/merchants/capital/transactionRecord/4') }}">失败</a>
        </li>
    </ul>
    <!-- 导航 结束 -->
    <div class="data-content">
        @if ( count($list) )
        <!-- 数据 -->
        <table class="table table-bordered table-striped f14">
            <thead> 
                <tr class="active">
                    <td>时间</td>
                    <td width="250">名称</td>
                    <td width="250">订单号｜支付单号</td>
                    <!-- <td>对方</td> -->
                    <td>金额｜明细</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
            @foreach($list as $o)
                <tr>
                    <td><p>{{$o['created_at'] or ''}}</p></td>
                    <td>
                        @foreach($o['orderDetail'] as $od)
                            <p>{{$od['title'] or ''}}</p>
                        @endforeach
                    </td>
                    <td>
                        <p>{{$o['oid'] or ''}}</p>
                        <p class="f12 gray_999">{{$o['trade_id'] or ''}}</p>
                    </td>
                    <!-- <td></td> -->
                    <td class="green_3c3">+ {{$o['pay_price'] or ''}}</td>
                    <td>{{$orderStatus[$o['status']] or ''}}</td>
                    <td>
                        <a class="blue_38f" href="{{ url('/merchants/order/orderDetail', $o['id']) }}">查看</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <!-- 分页 -->
        {{$pageHtml}}
        @else
        <div class="no_result">暂无数据</div>
        @endif
    </div>
</div>
@endsection

@section('page_js')
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- layer选择时间插件 -->
<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/transaction.js"></script>
@endsection

