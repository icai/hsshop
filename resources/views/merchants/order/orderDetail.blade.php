@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_thx64pa3.css" />
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
    <div class="third_title">订单详情</div>
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
    <!-- 拼团状态栏 -->
    @if($detail['groups_id'] != 0)
    <div class="t-step_progress">
        <div class="t-order_progress">
            <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
            <div class="t-stepIco t-stepIco1">1
                <div class="t-stepText pd">{{ $detail['orderLog'][0]['updated_at'] or '' }}</div>
                <div class="t-stepText t-step" id="createText">买家下单</div>
            </div>
            <div class="t-stepIco t-stepIco2">2
                <div class="t-stepText pd">{{ $detail['orderLog'][1]['updated_at'] or '' }}</div>
                <div class="t-stepText t-step" id="checkText" >买家付款</div>
            </div>
            <div class="t-stepIco t-stepIco3">3
                <div class="t-stepText pd">@if($detail['groups']['status']==2){{$detail['groups']['updated_at']}}@endif</div>
                <div class="t-stepText t-step" id="produceText">买家成团</div>
            </div>
            <div class="t-stepIco t-stepIco4">4
                <div class="t-stepText pd">{{ $detail['orderLog'][2]['updated_at'] or '' }}</div>
                <div class="t-stepText t-step">商家发货</div>
            </div>
            <div class="t-stepIco t-stepIco5">5
                <div class="t-stepText pd">{{ $detail['orderLog'][4]['updated_at'] or '' }}</div>
                <div class="t-stepText t-step" id="step5Text">交易完成</div>
            </div>
        </div>
    </div>
@else
    @if($detail['status']==5)
        <div class="step_progress ">
            <div class="order_progress out " >
                <ul>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <div class="stepIco stepIco1">1
                    <div class="stepText pd">{{ $detail['orderLog'][0]['updated_at'] or '' }}</div>
                    <div class="stepText step" id="createText">买家申请维权</div>
                </div>
                <div class="stepIco stepIco2">2
                    <div class="stepText pd">{{ $detail['orderLog'][1]['updated_at'] or '' }}</div>
                    <div class="stepText step">商家处理退款申请</div>
                </div>
                <div class="stepIco stepIco3">3
                    <div class="stepText pd">{{ $detail['orderLog'][2]['updated_at'] or '' }}</div>
                    <div class="stepText step" id="produceText">退款完成</div>
                </div>
            </div>
        </div>
        @endif
        @if($detail['status']!=5)
        <div class="step_progress  ">
            <div class="order_progress">
                <ul>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <div class="stepIco stepIco1" id="create">1
                    <div class="stepText pd">{{ $detail['orderLog'][0]['updated_at'] or '' }}</div>
                    <div class="stepText step" id="createText">买家下单</div>
                </div>
                <div class="stepIco stepIco2" id="check">2
                    <div class="stepText pd">{{ $detail['orderLog'][1]['updated_at'] or '' }}</div>
                    <div class="stepText step" id="checkText" >买家付款</div>
                </div>
                <div class="stepIco stepIco3" id="produce">3
                    <div class="stepText pd">{{ $detail['orderLog'][2]['updated_at'] or '' }}</div>
                    <div class="stepText step" id="produceText">商家发货</div>
                </div>
                <div class="stepIco stepIco4" id="delivery">4
                    <div class="stepText pd">{{ $detail['orderLog'][4]['updated_at'] or '' }}</div>
                    <div class="stepText step" id="deliveryText">交易完成</div>
                </div>
            </div>
        </div>
        @endif
@endif
    <div class="content-region clearfix">
        <div class="info-region">
            <h3> 订单信息
                <!--<span class="secured-title">担保交易</span>-->
            </h3>
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>
                            订单编号：
                        </th>
                        <td>
                            {{ $detail['oid'] }}
                            {{--<a href="javascript:;">更多</a>--}}
                        </td>
                    </tr>
                    <tr style="display: table-row;">
                        <th>订单类型：</th>
                        @if($detail['type'] == 1 )
                            <td>普通订单</td>
                        @elseif($detail['type'] == 2)
                            <td>待付订单</td>
                        @elseif($detail['type'] == 3)
                            <td>多人拼团订单</td>
                        @elseif($detail['type'] == 4)
                            <td> 积分兑换订单</td>
                        @elseif($detail['distribute_type'] == 1)
                            <td>分销订单</td>
                            <p class="order-dp" data-oid="{{$detail['id']}}">明细</p>
                        @endif
                    </tr>
                    @if($detail['type'] == 3 && $detail['groups'])
                    <tr style="">
                        <th>团编号：</th>
                        <td>{{$detail['groups']['identifier']}}</td>
                    </tr>
                    @endif
                    <tr style="display: none;">
                        <th>归属网点：</th>
                        <td>-</td>
                    </tr>
                    <tr style="display: none;">
                        <th>网点信息：</th>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>付款方式：</th>
                        <td>{{ $payWayList[$detail['pay_way']] == '全部' ? '未付款' : $payWayList[$detail['pay_way']] }}</td>
                    </tr>
                    <tr>
                        <th>
                            买家：
                        </th>
                        <td>
                            <span>{{$detail['member']['nickname'] or ''}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="dashed-line"></div>
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>收货信息：</th>
                        <td>
                            <p>{{ $detail['address_detail'] }}, {{ $detail['address_name'] }}, {{ $detail['address_phone'] }}</p>
                            <div><a id="copy_user_info" href="javascript:;">[复制]</a><input type="hidden" id="hid_copy_user_info" value="{{ $detail['address_detail'] }}, {{ $detail['address_name'] }}, {{ $detail['address_phone'] }}" /></div>
                        </td>
                    </tr>
                    @if($detail['no_express'] == 0)
                        <tr>
                            <th>物流信息：</th>
                            @if($detail['status'] == 2 || $detail['status']==3)
                                <td>{{ $detail['logistics']}}　<a href="javascript:;" class="btn_view_ogistics" data-id="{{$detail['id']}}">查看物流</a></td>
                             @else
                                <td>未发货</td>
                            @endif
                        </tr>
                    @endif
                    <tr>
                        <th>买家留言：</th>
                        <td>{{ $detail['buy_remark'] or '-' }}</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <div class="kefu">
                                <a href="javascript:void(0);" class="kefu_top">
                                    <span class="ico-contact-waiter waiter"></span>
                                    <span class="kefu_contact">联系客户</span>
                                </a>
                             </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="state-region">
            <div style="padding: 0px 0px 30px 40px;">
                <h3 class="state-title">
                    <span class="icon success">√</span>
                    订单状态：
                    @if($detail['status'] == 0)
                        待付款
                        @elseif($detail['status'] == 1 && $detail['groups_status'] == 1)
                        买家已付款 等待成团
                    @elseif($detail['status'] == 1)
                        待发货
                    @elseif($detail['status'] == 2)
                        待收货
                    @elseif($detail['status'] == 3)
                        已完成
                    @elseif($detail['status'] == 4)
                        已关闭
                    @endif

                </h3>
                <div class="state-desc">
                    @if($detail['status'] == 0)
                        如果买家未在规定的时间内付款，订单将按照设置逾期自动关闭
                    @elseif($detail['status'] == 1 && $detail['groups_status'] == 1)
                        买家已付款至您的待结算账户，请尽快发货，否则买家有权申请退款（待成团订单）
                    @elseif($detail['status'] == 1)
                        买家已付款至您的待结算账户，请尽快发货，否则买家有权申请退款
                    @elseif($detail['status'] == 2)
                        买家如果在7天内没有申请退款，交易将自动完成
                    @elseif($detail['status'] == 3)
                        货款已结算至您的店铺余额账户，请注意查收
                    @elseif($detail['status'] == 4)
                        已关闭
                    @endif

                        @if($detail['refund_status'] == 1 || $detail['refund_status'] == 7)
                            等待商家处理退款申请
                        @elseif($detail['refund_status'] == 2)
                            商家不同意退款申请
                        @elseif($detail['refund_status'] == 3)
                            商家同意退款
                        @elseif($detail['refund_status'] == 4 || $detail['refund_status'] == 8)
                            退款完成
                        @elseif($detail['refund_status'] == 5)
                            买家取消退款申请
                        @elseif($detail['refund_status'] == 6)
                            商家同意退货
                        @elseif($detail['refund_status'] == 9)
                            退款关闭
                        @endif

                    <ul>
                        <li>
                            订单金额： @if(in_array('view_order_price',session('permission')??[])){{$detail['pay_price']}}@else ** @endif元
                        </li>
                        {{--<li>--}}
                            {{--订单返现金额：0.21 元--}}
                            {{--<a href="javascipt:void(0);" target="_blank">详情</a>--}}
                        {{--</li>--}}
                    </ul>
                </div>
                <div class="state-action">
                    <!-- 修改价格 -->
                    <!-- 关闭订单 -->
                    @if($detail['status']=='0')
                        @if(in_array('view_order_price',session('permission')??[]))
                        <button class="fl btn-yes btn_up_price" data-id="{{$detail['id']}}" data-total="{{$detail['pay_price']}}">修改价格</button>
                        @endif
                        <button class="fl btn-close btn_clear_order ml10" data-id="{{$detail['id']}}">关闭订单</button>
                    @endif

                    @if($detail['status']=='2')
                        @if($takeAwayConfig==0)
                            <button class="fl btn-yes btn_up_logistics ml10" data-id="{{$detail['id']}}">修改物流</button>
                            <button class="fl btn-yes btn_extend_send_goods ml10" data-id="{{$detail['id']}}">延长收货</button>
                        @endif
                    @endif

                    @if($detail['status']=='1')
                        @if($takeAwayConfig==0)
                            @if($detail['groups_id'] !=0)
                                @if($detail['groups']['status'] == 2)
                                    <button class="fl btn-yes send-goods ml10" data-id="{{$detail['id']}}">发货</button>
                                @endif
                            @else
                             <button class="fl btn-yes send-goods ml10" data-id="{{$detail['id']}}">发货</button>
                            @endif
                        @endif

                    @endif
                    <!-- 备注 -->
                    <button id="clickRemark" class="fl btn-ordinary ml10" data-id="{{$detail['id']}}">备注</button>
                    <!-- 星星 -->
                    <a href="javascript:void(0);" class="delete-star">去星</a>
                    <div class="ui-star" data-id="{{$detail['id']}}" data-click="{{$detail['star_level']}}"></div>
                </div>
                <div class="mt10">
                    <div class="dashed-line"></div>
                    <div class="state-remark">
                        <table class="info-table">
                            <tbody >
                                @if($detail['seller_remark']!="")
                                <tr>
                                    <th>商家备注：</th>
                                    <td>
                                        <div id="seller_remark">{{$detail['seller_remark']}}</div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="state-remind-region">
                <div class="dashed-line"></div>
                <div class="state-remind">
                    <h4>会搜云提醒：</h4>
                    <ul>
                        @if($detail['status'] == 0)
                            <li>请务必等待订单状态变更为"买家已付款，等待买家发货"后在进行发货</li>
                        @elseif($detail['status'] == 1)
                            <li>如果无法发货，请及时与买家联系，并说明情况后进行退款</li>
                            <li>买家申请退款后，须征得商家同意后方能发货，否则商家有权拒收</li>
                            <!--<li>买家付款后超过七天仍未发货，将有权申请会搜云客服介入发起退款维权</li>-->
                        @elseif($detail['status'] == 2)
                            <li>交易成功后，货款将结算到您的店铺余额中，您可以申请提现。</li>
                            <li>请及时关注您发出的包裹状态，确保能配送至买家手中</li>
                            <li>如果买家表示未收到货物或货物有问题，请及时联系买家积极处理，友好协商</li>
                        @elseif($detail['status'] == 3)
                            交易已完成，如果买家提出售后要求，请积极与买家协商，做好售后服务
                        @elseif($detail['status'] == 4)
                            买家无意完成订单
                        @elseif($detail['status'] == 5)
                            @if($detail['refund_status'] == 1 || $detail['refund_status'] == 7)
                                <li>如果您同意退款将直接退款给买家</li>
                                <!-- <li>如果您拒绝退款，买家可以要求会搜云客服介入</li> -->
                                <li>如果您逾期未处理，视为同意退款，系统将自动退款给买家</li>
                            @elseif($detail['refund_status'] == 2)
                                商家不同意退款申请
                            @elseif($detail['refund_status'] == 3)
                                商家同意退款
                            @elseif($detail['refund_status'] == 4 || $detail['refund_status'] == 8)
                                退款完成
                            @elseif($detail['refund_status'] == 5)
                                买家取消退款申请
                            @elseif($detail['refund_status'] == 6)
                                商家同意退货
                            @elseif($detail['refund_status'] == 9)
                                退款关闭
                            @endif

                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <table class="ui-table ui-table-simple goods-table order-detail-goods-table">
        <thead>
            <tr>
                <th></th>
                <th class="cell-30">商品</th>
                <th>价格(元)</th>
                <th>数量</th>
                <th>优惠(元)</th>
                <th class="cell-13">小计(元)</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail['orderDetail'] as $key => $product)
            <tr class="test-item" >
                <td class="td-goods-image" @if($product['noteList']) rowspan="2" @endif>
                    <div class="ui-centered-image" src="{{ imgUrl($product['img']) }}" width="48px" height="48px" style="width: 48px; height: 48px;">
                        <img src="{{ imgUrl($product['img']) }}" style="max-width: 48px; max-height: 48px;">
                    </div>
                </td>
                <td>
                    <a href="{{url('/shop/preview/' . session('wid') . '/' . $product['product_id'])}}" target="_blank">{{ $product['title'] }} </a>
                    {{--<a href="javascipt:void(0);" target="_blank" class="goods-snap-link">[商品交易快照]</a>--}}
                    {{--<p class="c-gray">口味:-</p>--}}
                    <p class="c-gray">
                       @if(!empty($product['spec'])) {{ $product['spec'] }} @endif
                    </p>
                </td>
                <td>
                    @if(in_array('view_order_price',session('permission')??[]))
                        {{ sprintf('%.2f',$product['price']) }}
                    @else
                        **
                    @endif
                </td>
                <td>{{ $product['num'] }}</td>
                <td>
                    @if($detail['type'] == 7)
                        <p class="discount-money">-{{$detail['seckill_coupon']}}</p>
                        <p class="discount-people">秒杀</p>
                    @else
                        @if($detail['groups_id'] == 0 )
                            <p class="discount-money">-@if( $detail['card_discount'] == 1 ){{ sprintf('%.2f',($product['price'] - $product['after_discount_price']) * $product['num']) }}(会员折扣)@endif</p>
                        @else
                            <p class="discount-money">- {{ sprintf('%.2f',$product['price']- $detail['pay_price']-$detail['freight_price']) }}</p>
                        @endif
                    @endif
                </td>
                <td>
                    @if($detail['groups_id'] == 0 )
                        <p>@if($detail['card_discount'] ==1){{ sprintf('%.2f',$product['after_discount_price']*$product['num']) }}@else {{ sprintf('%.2f',$product['price']*$product['num']) }} @endif</p>
                    @else
                        <p>{{$detail['pay_price']-$detail['freight_price']}}</p>
                    @endif
                    <div>
                        <a href="javascript:;" class="goods-online-refund-link">主动退款</a>
                    </div>
                </td>
                <td>
                    @if(isset($refund[$product['product_id']]))
                        <a href="/merchants/order/refundDetail/{{$detail['id']}}/{{$product['product_id']}}/{{$product['product_prop_id']}}" class="bule" >
                            {{$refund[$product['product_id']]['status_string']}}
                        </a>
                    @elseif($product['is_delivery'] == 0)
                        未发货
                    @else
                        已发货
                    @endif

                    <!-- 手动退款完成按钮 只要付款且退款未完成的商品就显示该按钮-->
                    @if ($detail['status'] > 0 && (empty($refund[$product['product_id']]) || $refund[$product['product_id']]['status'] != 8))
                            @if($detail['type'] != 12)
                        <br><a id="manually_refund_success" data-oid="{{$detail['id']}}" data-pid="{{$product['product_id']}}" data-prop-id="{{$product['product_prop_id']}}">标记退款</a>
                            @endif
                    @endif
                </td>
            </tr>
            @if($product['noteList'])
                <tr class="">
                    <td colspan="5" style="text-align: left">
                        @forelse($product['noteList'] as $item)
                            <p class="c-gray">
                                {{$item['title']}}:@if($item['type'] == 6 && $item['content'])<a href="{{ imgUrl() }}{{$item['content']}}" target="_blank">点击查看图片</a> @else {{$item['content']}} @endif
                            </p>
                            @endforeach
                    </td>
                </tr>
            @endif
            @endforeach
            <tr>
                <td colspan="7"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right">
                    <span>订单共{{count($detail['orderDetail'])}}件商品，
                        @if( isset($detail['coupon_price']) && $detail['coupon_price'] > 0 )
                            优惠券优惠{{$detail['coupon_price']}}.
                        @endif
                        积分抵现{{$detail['bonus_point_amount']??0}}，总计：</span>
                    <span class="c-red">￥</span>
                    <span class="real-pay c-red">{{ $detail['pay_price'] }}</span>
                    <span>（含运费 ￥ {{ $detail['freight_price']}}）</span>
                </td>
            </tr>
        </tfoot>
    </table>
    @if($detail['orderLog'])
    <div class="orderLog">
        <div class="head">订单日志</div>
        <table class="log-table">
            <thead>
                <tr>
                    <th>操作</th>
                    <th>时间</th>
                    <th>其他</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail['orderLog'] as $val)
                <tr>
                    <td>{{ $logStatus[$val['action']] }}</td>
                    <td>{{ $val['created_at'] }}</td>
                    <td>{{ $val['remark'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
<!--backdrop-->
<div class="modal-backdrop" style="display: none;opacity: 0.7;"></div>
<!-- 备注model开始 -->
<div class="modal export-modal" style="display: none;" id="baseModal">
    <div class="modal-dialog" id="base-modal-dialog">
        <form id="seller_remark_form" class="form-horizontal">
            {{ csrf_field() }}
            <input id="order_id" type="hidden" name="id" value="{{$detail['id']}}" />
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">商家备注</h4>
                </div>
                <div class="modal-body">
                    <textarea class="js-remark form-control" name="seller_remark" rows="4" placeholder="" maxlength="256">{{$detail['seller_remark']}}</textarea>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-primary submit_info" style="color:#fff;">提交</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 标记退款弹窗 -->
<div class="modal export-modal" style="display: none;" id="baseModalRefund">
    <div class="modal-dialog" id="base-modal-dialog-refund">
        <form id="manually_refund_form" class="form-horizontal">
            {{ csrf_field() }}
            <input id="refund_oid" type="hidden" name="oid" value="" />
            <input id="refund_pid" type="hidden" name="pid" value="" />
            <input id="refund_prop_id" type="hidden" name="prop_id" value="" />
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">标记退款</h4>
                </div>
                <div class="modal-body">
                    该订单已通过其他渠道，完成退款，标记为已退款。
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-primary submit_refund" style="color:#fff;">确定</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 延长收货时间开始 -->
<div class="pl15 none" id="div_extend_send_goods">
    <h3 class="mt15">确定延长收货时间？</h3>
    <p class="mt10">延长收货时间可以让买家有更多时间收货，而不急于申请退款;</p>
    <p class="mt10">延长本交易的"确定收货"期限为3天</p>
</div>
<!-- 延长收货时间结束 -->



<!-- 修改物流时间开始 -->
<div class="layer-wrap none" id="div_up_logistics">
    <!-- 提示 -->
    <div class="t-tips">
        <i class="glyphicon glyphicon-exclamation-sign" style="color: #FF8676;font-weight: 300;"></i>
        物流信息仅支持一次更正，请仔细填写并核对
    </div>
    <!-- 包裹1 -->
    <div class="mb30">
        <p class="mt15"><strong>包裹1</strong>共1件商品</p>
        <p class="mt15">
            <span>发货方式：</span>
            <label class="radio-inline">
                <input type="radio" style="top: -6px;" name="inlineRadio1" checked value="0"> 需要物流
            </label>
            <label class="radio-inline">
                <input type="radio" style="top: -6px;" name="inlineRadio1" value="1"> 无需物流
            </label>
        </p>
        <p class="mt15">
            <span>物流公司：</span>
            <select class="form-control w120 iblock">
                <option value="0">百世汇通</option>
                <option value="1">圆通快递</option>
            </select>
            <span>运单编号：</span>
            <input type="text" class="form-control w200 iblock" value="" placeholder="请填写运单编号" />
        </p>
    </div>
</div>
<!-- 修改物流时间结束 -->

<!-- 同意买家退款开始 -->
<div class="layer-wrap none" id="div_agree_refund">
    <!-- 提示 -->
    <div class="t-tips">
        该订单通过<span style="color:#ff8676;">微信安全支付</span>付款，需您同意退款申请，买家才能退货给您；买家退货后您需再次确认收货后，退款将自动原路退回至买家付款账号;
    </div>
    <!-- 包裹1 -->
    <div class="mb30">
        <p class="mt15">
            <span>发货方式：</span>
            <span>仅退款</span>
        </p>
        <p class="mt15">
            <span>退款金额：</span>
            <span style="color:#ff8676;">￥0.01</span>
        </p>
    </div>
</div>
<!-- 同意买家退款结束 -->

<!-- 拒绝退款申请开始 -->
<div class="layer-wrap none" id="div_refuse_refund">
    <!-- 提示 -->
    <div class="t-tips">
        建议您与买家协商后，再确定是否拒绝退款。如果您拒绝退款后，买家可以修改退款申请协议重新发起退款。
    </div>
    <!-- 包裹1 -->
    <div class="mb30">
        <p class="mt15">
            <span>仅退款</span>
        </p>
        <p class="mt15">
            <span>拒绝理由：</span>
            <span style="color:#ff8676;">
                <textarea class="form-control refuse-textarea" placeholder="请填写您的拒绝理由"></textarea>
            </span>
        </p>
    </div>
</div>
<!-- 拒绝退款申请结束 -->

<!-- 查看物流详情结束 -->
<div class="layer-wrap none" id="div_view_ogistics" style="margin:0px;">
    <!-- 包裹导航 -->
    <ul class="common_nav">
        <li class="hover"><a href="javascript:;">包裹1</a></li>
        <li><a href="javascript:;">包裹2</a></li>
        <li><a href="javascript:;">包裹3</a></li>
        <li><a href="javascript:;">包裹4</a></li>
        <li class="clear"></li>
    </ul>
    <!-- 物流详情信息 -->
    <div class="layer-wrap-logistics">
        <p class="mb10">快递名称：百世汇通 单号：2222222222222</p>
        <div>
            <p class="logistics-p">
                <span class="logistics-p-date">2017-05-02</span>
                <span class="logistics-p-week">周二</span>
                <span class="logistics-p-time">10:58:00</span> 包裹正在等待揽收
            </p>
            <p class="logistics-p"><span>20:11:54</span> [深圳市]百世汇通 福田华强收件员 已揽件</p>
            <p class="logistics-p"><span>23:01:00</span> [深圳市]快件已从福田华强发出，准备发往杭州</p>
            <p class="logistics-p"><span>23:03:19</span> [深圳市]快件已从福田华强发出，准备发往深圳</p>
        </div>
        <div>
            <p class="logistics-p">
                <span class="logistics-p-date">2017-05-03</span>
                <span class="logistics-p-week">周三</span>
                <span class="logistics-p-time">10:58:00</span> [深圳市]快件已到达 深圳中心
            </p>
            <p class="logistics-p"><span>00:45:22</span> [深圳市]快件已从深圳中心发出，准备发往深圳中心</p>
        </div>
        <div>
            <p class="logistics-p">
                <span class="logistics-p-date">2017-05-04</span>
                <span class="logistics-p-week">周四</span>
                <span class="logistics-p-time">02:46:41</span>  [嘉兴市]快件已从杭州转运中心出发，准备发往杭州九堡区
            </p>
            <p class="logistics-p"><span>07:47:44</span> [杭州市]杭州九堡区派件员：牛田承包区 13357138411</p>
            <p class="logistics-p"><span>11:16:38</span> 快件已签收，感谢您使用百世汇通！</p>
        </div>
        <!-- 左边物流图 -->
    </div>
    <!-- 如果未找到物流信息隐藏提示提示 -->
    <div class="t-tips-middle none">
        没找到物流信息
    </div>
</div>
<!-- 查看物流详情结束 -->
<!--发货弹框-->
<div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:100;display:none" class="bg000"></div>
<div class="zent-dialog widget-order-express" style="top: calc(50vh - 270px);display:none"><div class="zent-dialog-header ">
    <h3 class="zent-dialog-title">商品发货</h3>
    <a href="javascript:;" class="zent-dialog-close">×</a>
</div>
<div class="zent-dialog-body">
    <!-- 周期购订单发货step -->


    <div class="js-total-express total-express">待发货 1，已选 0</div>

    <div class="add-express-table-control">
        <div class="js-modal-table">
            <table class="ui-table delivertable">
                <thead>
                    <tr>
                        <th class="text-right cell-5">
                            <input type="checkbox" class="js-check-all">
                        </th>
                        <th class="cell-35">商品</th>
                        <th class="cell-10">数量</th>
                        <th class="cell-40">物流 | 单号</th>
                        <th class="cell-15">状态</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-right">
                            <input type="checkbox" class="js-check-item">
                        </td>
                        <td>
                            <div>
                                <a href="javascript:void(0);" class="new-window">
                            test商品002

                                </a>
                            </div>
                            <div></div>
                        </td>
                        <td>1</td>
                        <td></td>

                        <td class="green"></td>
                    </tr>
                </tbody>
            </table>
         </div>
         <p class="js-goods-tips hide error-goods-tips">请选择发货商品</p>
    </div>
    <form onsubmit="return false;" class="form-horizontal">

        <div class="control-group">
            <label class="control-label">发货方式：</label>
            <div class="controls">
                <label class="radio inline">
                    <input type="radio" name="no_express" value="0" checked="" class="radio_express" data-validate="no">物流发货
                </label>

                <label class="radio inline">
                    <input type="radio" name="no_express" value="1" class="radio_express" data-validate="no">无需物流
                </label>
            </div>
        </div>

        <div class="store-express-info clearfix control-2-col control-group js-store-express-info" style="display: none;">
            <div class="control-group">
                <label class="control-label">门店名称：</label>
                <div class="controls">
                    <span class="control-label control-label--store-name"></span>
                </div>
            </div>
            <div class="control-group control-group--memo">
                <label class="control-label control-label--long">发货单备注：</label>
                <div class="controls">
                    <input type="text" name="express_comment" placeholder="最多留言100个字（非必填）">
                </div>
            </div>
        </div>
        <div class="clearfix control-2-col js-express-info control-group">
            <div class="control-group">
                <label class="control-label">物流公司：</label>
                <div class="controls">
                    <select class="js-company select2-offscreen" name="express_id" tabindex="-1">
                        <option value="0">请选择一个物流公司</option>
                    </select>
                </div>
            </div>
            <div class="control-group js-express-name-group hide" style="display: none;">
                <label class="control-label">快递名称：</label>
                <div class="controls">
                    <input type="text" class="input" name="express_name" value="">
                </div>
            </div>
            <div class="control-group js-express-name-group hide" style="display: none;"></div>
            <div class="control-group">
                <label class="control-label">快递单号：</label>
                <div class="controls">
                    <input type="text" class="input js-number" maxlength="20" name="express_no" value="">
                </div>
            </div>
            <div class="help-desc" style="clear: both;">
                *请仔细填写物流公司及快递单号，发货后24小时内仅支持做一次更正，逾期不可修改
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">收货信息：</label>
            <div class="controls">
                <div class="control-action">
                    河北省 保定市 容城县  西牛村 西牛鲜奶订购点, l, 13111111111
                </div>
            </div>
        </div>
    </form>

</div>
    <div class="zent-dialog-footer">

        <a href="javascript:;" class="zent-btn zent-btn-primary js-save">保存</a>

    </div>
</div>

	<!--分销详情弹窗-->
	<div class="order-alert">
		<div class="order-list">
			<div class="order-bor">
				<p>分销明细</p>
				<div class="order-x">
					<img width="10" height="10" src="{{ config('app.source_url') }}mctsource/images/guanbi-x.png"/>
				</div>
			</div>
			<table border="" cellspacing="" cellpadding="">
				<tr>
					<th>用户ID</th>
					<th>用户名</th>
					<th>佣金</th>
					<th>佣金状态</th>
				</tr>
			</table>
		</div>
	</div>
@endsection
@section('page_js')
<script type="text/javascript">
    var STATIC_URL = "{{ config('app.source_url') }}static";
    var o_status = "{{$detail['status']}}"
    var out_status = "{{$detail['refund_status']}}";
    @if($detail['groups_id'] != 0)
        var group_status = "{{$detail['groups']['group_status']}}";
    @endif
    var wid = "{{session('wid')}}";
    var custId = "{{session('userInfo')['id']}}";
    var sign = "{{md5(session('wid').session('userInfo')['id'].'huisou')}}";
    var weiUserId = "{{$detail['mid']}}"
    var chatUrl = "{{config('app.chat_url')}}";
    var joinway = "{{$detail['source'] == 1 ? 'small' : 'weixin'}}"
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 星级评定js插件 -->
<script src="{{ config('app.source_url') }}static/js/jquery.raty.min.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/order_thx64pa3.js"></script>
<!-- 订单公用文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/order_common.js"></script>
@endsection
