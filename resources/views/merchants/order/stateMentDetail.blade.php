@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前模块公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_llbq22x2.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_thx64pa4.css" />
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection
@section('slidebar')
@include('merchants.order.slidebar')
@endsection
@section('middle_header')
<!-- 中间 开始 -->
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">结算详情</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div class="title_info">订单信息</div>
    <div class="order_detail">
        @if($detail['status'] == 0)
        <h4>未付款</h4>
        @elseif($detail['status'] == 1)
            @if($detail['refund_status'] == 1)
            <h4>申请退款中</h4>
            @elseif($detail['refund_status'] == 2)
            <h4>申请退款被拒</h4>
            @elseif($detail['refund_status'] == 3)
            <h4>退款中</h4>
            @elseif($detail['refund_status'] == 4)
            <h4>退款完成</h4>
            @elseif($detail['refund_status'] == 5)
            <h4>买家取消退款</h4>
            @else
            <h4>已付款</h4>
            @endif
        @elseif($detail['status'] == 3)
        <h4>已完成</h4>
        @elseif($detail['status'] == 4)
        <h4>已关闭</h4>
        @endif
        <div>
        <span class="order_num">订单号：</span><span>{{ $detail['oid'] }}</span>
        </div>
        <div class="num">支付流水号：{{ $detail['serial_id'] ?? ''}}</div>
        <div class="order_info">
            <span>备注信息：</span><p class="info_detail">{{ $detail['seller_remark'] }}</p>
        </div>
        <div>
            <a class="zent-btn zent-btn-success action" href="javascript:void(0);">备注</a>
        </div>
    </div>
    <div class="title_info user_title">用户信息</div>
    <div class="user_detail">
        <div class="image">
            <span class="user_name">姓 名：{{ $detail['address_name'] }}</span>
        </div>
        <div class="phone">联系电话：{{ $detail['address_phone'] }}</div>
    </div>
    <div class="title_info order_title">消费信息</div>
    <table class="table ui-table-order">
        <thead>
            <tr class="widget-list-header">
                <th class="text-left">商品</th>
                <th class="text-left"></th>
                <th class="price-cell">单价</th>
                <th>数量</th>
                <th>加减</th>
                <th>小计</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detail['orderDetail'] as $val)
            <tr class="content-row">
                <td class="image-cell">
                    <img class="lazy" width="60" height="60" src="{{ imgUrl($val['img']) }}">
                </td>
                <td class="title-cell">
                    <p class="goods-title">
                        <a href="javascript:void(0);" class="new-window">{{ $val['title'] }}</a>
                    </p>
                    <p>
                        <span class="goods-sku">{{ $val['spec'] }}</span>
                    </p>
                </td>
                <td>{{ $val['price'] }}</td>
                <td>{{ $val['num'] }}</td>
                <td>
                    @if($detail['type'] == 7)
                        <p class="discount-money">-{{$detail['seckill_coupon']}}</p>
                        <p class="discount-people">秒杀</p>
                    @else
                        @if($detail['groups_id'] == 0 )
                            <p class="discount-money">-@if( $detail['card_discount'] == 1 ){{ ($val['price'] - $val['after_discount_price']) * $val['num']}}(会员折扣)@endif</p>
                            @if($detail['coupon_price'])
                            <p>-{{ $detail['coupon_price'] }}(优惠券)</p>
                            @endif
                            @if($detail['bonus_point_amount'])
                            <p>-{{ $detail['bonus_point_amount'] }}(积分抵扣)</p>
                            @endif
                        @else
                            <p class="discount-money">- {{ $val['price']- $detail['pay_price'] }}</p>
                        @endif
                    @endif
                </td>
                <td>
                    <p>{{ sprintf('%.2f',$detail['pay_price']) }}</p>
                </td>
            </tr>
            @empty
            <tr>
                <td>无数据信息</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="border-bottom-none">
                <td style="border-bottom:none !important" colspan="6">
                    <div class="check_order">
                        <span>合计：</span>
                        <span class="price">￥{{ sprintf('%.2f',$detail['pay_price']) }}</span>
                    </div>
                </td>
            </tr>
            <tr class="border-top-none">
                <td style="border-top:none !important" colspan="6">
                    <div class="check_order">
                        <span>应收金额：</span>
                        <span class="price">￥{{ $detail['pay_price'] }}</span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    @if($detail['status'] == 0)
    <div class="btn_group">
        <a class="zent-btn btn_clear_order" href="javascript:void(0);" data-id="{{ $detail['id'] }}">取消订单</a>
    </div>
    @elseif(($detail['status'] == 1 && $detail['refund_status'] == 0) || ($detail['status'] == 1 && $detail['refund_status'] == 5))
    <div class="btn_group">
        <a class="zent-btn zent-btn-success finish-btn-order" href="javascript:void(0);" data-id="{{ $detail['id'] }}">结单</a>
    </div>
    @endif
</div>
<!-- 备注model -->
<div class="modal export-modal" style="display: none;" id="baseModal" data-id="{{ $detail['id'] }}">
    <div class="modal-dialog" id="base-modal-dialog" style="margin-top: 250px;">
        <form id="seller_remark_form" class="form-horizontal">
            {{ csrf_field() }}
            <input id="order_id" type="hidden" name="id" value="{{ $detail['id'] }}">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">商家备注</h4>
                </div>
                <div class="modal-body"> 
                    <textarea class="js-remark form-control" name="seller_remark" rows="4" placeholder="最多可输入256个字符" maxlength="256"></textarea>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-primary submit_info" style="color:#fff;">提交</a>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-backdrop" style="opacity: 0.7;"></div>
<!-- 备注model -->  
@endsection
@section('page_js')
<script type="text/javascript">
    var STATIC_URL = "{{ config('app.source_url') }}static";
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 星级评定js插件 -->
<script src="{{ config('app.source_url') }}static/js/jquery.raty.min.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/order_thx64pa4.js"></script>
<!-- 订单公用文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/order_common.js"></script>
@endsection
