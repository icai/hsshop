@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
<!-- 时间插件 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_d9evgjke.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/capital/billDetail') }}">账单明细</a>
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
    <!-- 筛选 开始 -->
    <div class="screen_module">
        <form class="form-horizontal" role="form" action="/merchants/capital/billDetail" method="get">
            <div class="form-group">
                <label class="col-sm-2 control-label">订单号:</label>
                <div class="col-sm-3"><input type="text" class="form-control" name="order_sn" value="{{ $input['order_sn'] or '' }}" /></div>
                <div class="col-sm-2">
                    <a class="blue_38f f12" data-toggle="modal" data-target="#ruleModal" href="javascript:void(0);">结算规则一览</a>
                </div>
            </div>
            <!-- 验证时间： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">起止时间：</label>
                <div class="col-sm-3 center_start">
                    <!-- 开始时间 -->
                    <div id='start_time' class='input-group'>
                        <input class="form-control" type='text' name="start_time" value="{{ $start_time or '' }}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div><span style="display:inline-block;margin-left:-3px;float:left; line-height: 34px;">至</span>
                <div class="col-sm-3">

                    <!-- 结束时间 -->
                    <div id='end_time' class='input-group'>
                        <input class="form-control" type='text' name="end_time"  value="{{ $end_time or '' }}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a class="fastSelect_time blue_38f f12 @if(!isset($input['type'])) hover @endif" href="javascript:void(0);" data-day="7" style="display: inline-block;">最近7天</a>
                    &nbsp;<a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="30" style="display: inline-block;">最近30天</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">类型:</label>
                <div class="col-sm-3">
                    <select class="form-control" name="type">
                        <option value="0">全部</option>
                        <option value="1" @if (request('type') == 1 ) selected="selected" @endif>订单入账</option>
                        <option value="2" @if (request('type') == 2 ) selected="selected" @endif>订单退款</option>
                        {{--<option value="3" @if (request('type') == 3 ) selected="selected" @endif>分销打款</option>--}}
                    </select>
                </div>                
            </div>
            <div class="fomser">
                <input class="btn btn-primary" type="submit" value="查询">
                
            </div>
        </form>
    </div>
    <!-- 筛选 结束 -->
    <!-- 数据 -->
    <div class="data-content">
        <!-- 数据列表 -->
        <table class="table table-hover f12">
            <thead>
                <tr class="active porel">
                    <td>
                    结算时间<i class="glyphicon glyphicon-question-sign gray_999 f14 note_tip"></i>
	            	<div class="popover bottom poabl">
	            		<div class="arrow"></div>
	            		<h3 class="popover-title">结算方式：</h3>
	            		<div class="popover-content">
	        				结算时间=发货时间/商家退款或分销打款的确认打款时间
						</div>
	            	</div>
                    </td>
                    <td>类型 | 名称 | 交易号</td>
                    <td>金额 | 明细</td>
					<!--<td>余额(元)</td>-->
                    <td>支付渠道 | 单号</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                @forelse ($result as $val)
                <tr>
                    <td>{{ $val['updated_at'] }}</td>
                    <td>
                        <div class="reflect_wrap" data-flag="true">
                            <div class="note_tips" data-container="body" data-toggle="popover" role="button" data-placement="bottom" data-html="true" data-content="<p>收款银行：中国银行</p><p>银行帐户：623572****000039888</p><p>帐户名称：陈琪誉</p>">
                                <span>
                                @if( $val['action'] == 2 )
                                订单入账
                                @elseif( $val['action'] == 8 )
                                订单退款
                                @elseif( $val['action'] == -1 )
                                分销打款
                                @else
                                未知来源
                                @endif
                                </span>
                            </div>
                        </div>
                        <p class="gray_999"></p>
                    </td>
                    <td class="{{ $val['pay_class'] or ''}}">{{ $val['pay_price'] }}</td>
                    <!--<td>0.00</td>-->  
                    <td>
                        <p>{{ $payWayList[$val['order']['pay_way']] }} </p>
                        <p class="gray_999">{{ $val['order']['serial_id'] }}</p>
                    </td>
                    <td>
                        @if($val['action'] == -1)
                            <a href="javascript:;" class="blue_38f">详情</a>
                        @else
                            <a href="/merchants/order/orderDetail/{{ $val['order']['id'] }}" class="blue_38f">详情</a>
                        @endif
                    </td>
                </tr>
                @empty
                    
                @endforelse
            </tbody>

        </table>
        @if(empty($result))
        <!-- 空 -->
        <div class="no_result">暂无数据</div>
        @else
        <div class="no_result">{!! $pageHtml !!}</div>
        @endif

    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="modal-title" id="myModalLabel">结算规则</span>
            </div>
            <div class="modal-body">
                <div class="mgb30">
                    <p class="mgb10">结算方式：</p>
                    <ul class="f12">
                        <li>［担保交易］结算时间＝发货后T+7天自然日或顾客确认收货时间</li>
                       
                    </ul>
                </div>
                <div class="mgb30">
                    <p class="mgb10">结算方式：</p>
                    <ul class="f12">
                        <li>［担保交易］ 2016/8/21日 14:00下的订单，22日17:00发货，正常结算时间29日17:00，结算钱款到商户可用余额。</li>
                        <li>（如顾客23日 18:00确认收货，结算时间23日 18:00，结算钱款到商户可用余额)</li>
                       
                    </ul>
                </div>
                <div class="mgb30 gray_999 f12">
                    <p class="mgb10">补充：</p>
                    <ul>
                        <li>• 当日订单总金额≠当日结算金额(下单时间和结算时间可能不在同一天)</li>
                        <li>• 分销订单，仅消费者确认收货后或发货后T＋7天自然日结算入账；</li>
                        <li>• 虚拟商品订单（自动发货），仅核销后或发货后T＋7天自然日结算入账；</li>
                        <li>• 结算后，钱款进入到商户可用余额的同时结算手续费，商户可用余额可提现</li>
                        <li>• 商户开店，默认签约担保交易(未缴保)，顾客付款后，无维权退款情况，商家发货后T+7天自然日自动结算钱款到商户可用余额，结算之前钱款显示在待结算余额。如买家自主确认收货，将即刻结算钱款到商户可用余额。</li>
                    </ul>
                </div>
                <div class="mgb30 gray_999 f12">如需查找订单是否已结算，可以复制订单号到账单明细中搜索。搜索结果为还没有相关数据，则还未结算</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<!-- 时间插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 当前页面js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/capital_d9evgjke.js"></script>
@endsection

