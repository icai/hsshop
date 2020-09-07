@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cashier_37c11f150f3d2a2b01fbc8c4d9e92bf4.css">
@endsection
@section('main')
    <div class="container " style="min-height: 482px;">
        <div class="content">
            <div class="paid-status success">
                <div class="header center">
                    <h2>
                        <p class="warn-icon"></p>
                        <p class="paid-success-tips">订单支付失败</p></h2>
                    <!-- 价格 -->
                    <p class="price-warp">
                        <span class="">￥{{ $order['pay_price'] ?? $order['money']/100 }}</span></p>
                </div>
                <div class="nbody nBody_li">
                    <div>
                        <ul>
                            <li>
                                <label>支付方式</label>
                                <div>
                                    @if($order['pay_way'] == 1)
                                        微信支付
                                    @elseif($order['pay_way'] == 2)
                                        支付宝支付
                                    @elseif($order['pay_way'] == 3)
                                        储值余额支付
                                    @elseif($order['pay_way'] == 4)
                                        货到付款
                                    @elseif($order['pay_way'] == 5)
                                        代付
                                    @elseif($order['pay_way'] == 6)
                                        领取赠品
                                    @elseif($order['pay_way'] == 7)
                                        优惠兑换
                                    @elseif($order['pay_way'] == 8)
                                        银行卡支付
                                    @elseif($order['pay_way'] == 9)
                                        会员卡支付
                                    @else
                                        未支付
                                    @endif

                                </div></li>
                            {{--<li class="">--}}
                            {{--<label>关注店铺公众号</label>--}}
                            {{--<div>--}}
                            {{--<a href="javascript:;" class="btn js-favour-shop">关注</a></div>--}}
                            {{--</li>--}}
                        </ul>
                    </div>
                </div>
                <div class="bottom">
                    <div class="action-container">
                        <div class="item" style="width:100%;">
                        
                        <!-- todo 记得调整样式 -->
                            {{--<div class="item" style="width: 100%; ">--}}
                            {{--<a href="" class="--}}
                            {{--btn btn-white btn-block         ">我要晒订单</a>--}}
                            {{--</div>--}}
                            <div class="item" style="width: 100%; ">
                                @if($type == 1)
                                <a class="btn btn-block btn-white" href="/shop/member/mycards/{{$order['wid']}}">查看订单详情</a>
                                @else
                                    @if($order['groups_id']<>0)
                                        <a class="btn btn-block btn-white" href="/shop/order/groupsOrderDetail/{{$order['id']}}/{{session('wid')}}">查看订单详情</a>
                                    @else
                                        <a class="btn btn-block btn-white" href="/shop/order/detail/{{$order['id']}}">查看订单详情</a>
                                    @endif

                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('shop.common.footer')
        @endsection
        @section('page_js')
            <script type="text/javascript">
                var imgUrl = "{{ imgUrl() }}";
            </script>
            <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
@endsection


