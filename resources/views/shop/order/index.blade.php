@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_list_f15200a287ced5ba2e1e6ce6e359b15e.css">
@endsection
@section('main')
    <div class="container " style="min-height: 581px;">
        <!-- 0 未满三天  1 满三天-->
        <input type="hidden" class="three_day" value="1">
        <div class="content js-page-content">
            <div id="order-list-container">
                
                <input id="wid" type="hidden" value="{{session('wid')}}">
                <input id="status" type="hidden" value="{{$status}}">
                <input id="source" type="hidden" value="{{ imgUrl() }}">
                <div class="js-list b-list">
                    @forelse($orderData as $val)
                    <li class="js-block-order block block-order animated">
                        <div class="header">
                            <div>
                                <a href="/shop/index/{{session('wid')}}">
                                    <span class="font-size-14">店铺：{{$val['weixin']['shop_name']}}</span>
                                </a>
                                <a class="order-state-str pull-right font-size-14" href="javascript:;">
                                    @if($val['status'] == 0)
                                        	待付款
                                        @elseif($val['status'] == 1)
                                        	买家已付款
                                        @elseif($val['status'] == 2)
                                        	商家已发货
                                        @elseif($val['status'] == 3)
                                        	交易完成
                                         @elseif($val['status'] == 4)
                                        	交易关闭
                                        @elseif($val['status'] == 5)
                                      		  退款中
                                        @endif
                                </a>
                            </div>
                            <div class="order-no font-size-12">订单编号：{{$val['oid']}}</div>
                        </div>
                        <a class="name-card name-card-3col clearfix" href="/shop/order/detail/{{$val['id']}}">
                            <div class="thumb">
                                <img class="test-lazyload" data-original="{{ imgUrl($val['orderDetail'][0]['img']) }}">
                            </div>
                            <div class="detail">
                                <h3 class="font-size-14 l2-ellipsis">{{$val['orderDetail'][0]['title']}}</h3>

                                <p class="sku-detail ellipsis js-toggle-more">
                                    <span class="c-gray-darker">{{$val['orderDetail'][0]['spec']}}&nbsp;</span> 
                                </p>
                                @if($val['groups_id'])
                                    <div class="mt5">
                                        <span class="group-icon">拼团</span>
                                    </div>
                                @elseif($val['seckill_id'])
                                    <div class="mt5">
                                        <span class="group-icon">秒杀</span>
                                    </div>
                                @endif
                            </div>
                            <div class="right-col">

                                <div class="price c-black">￥<span>{{$val['orderDetail'][0]['price']}}</span></div>

                                <div class="num c-gray-darker">
                                    ×<span class="num-txt c-gray-darker">{{$val['orderDetail'][0]['num']}}</span>
                                </div>
                            </div>
                        </a>
                        <!-- 酒店商品合并成一个 -->
                        @if($val['count']>1)
                        <a class="order-more center font-size-14" href="/shop/order/detail/{{$val['id']}}">
                            查看全部{{$val['count']}}件商品
                        </a>
                        <hr class="margin-0">
                        @endif
                        <div class="bottom-price  has-bottom-btns">
                            <div class="pull-right">
                                合计：
                                <span class="c-orange">￥{{$val['pay_price']}}</span>

                            </div>
                        </div>


                        <div class="bottom">
                            <div class="opt-btn pull-right">
                                
                                @if($val['status'] == 0)
                                    @if(isset($is_overdue) && $is_overdue == 1)
                                    <a class="btn btn-default" href="/shop/index/{{ session('wid') }}">取消</a>
                                    @else
                                    <a class="btn btn-default cancle_order" data-kdtid="{{$val['id']}}">取消</a>
                                    @endif
                                    <a class="js-extend-receive btn btn-default btn-in-order-list" href="/shop/order/detail/{{$val['id']}}" data-orderno="" data-kdtid="{{$val['id']}}">去付款</a>
                                    @endif
                                @elseif($val['status'] == 1)
                                @elseif($val['status'] == 2)
                                    <a class="logistics btn btn-default" href="/shop/order/expresslist/{{session('wid')}}/{{$val['id']}}">物流</a>
                                    <a class=" btn btn-default btn-in-order-list receiveDelay" href="##" data-orderno="" data-kdtid="{{$val['id']}}">延长收货</a>
                                    <a class="js-confirm-receive btn btn-default btn-in-order-list received" href="#" data-orderno="" data-kdtid="{{$val['id']}}">确认收货</a>
                                @elseif($val['status'] == 3)
                                    @if($val['evaluate'] == 0)
                                        @if($val['is_hexiao'] == 0)
                                        <a class="logistics btn btn-default" href="/shop/order/expresslist/{{session('wid')}}/{{$val['id']}}">物流</a>
                                        @endif
                                        <a class="js-confirm-receive btn btn-default btn-in-order-list" href="@if($val['count'] == 1) /shop/order/comment/{{session('wid')}}?odid={{$val['orderDetail'][0]['id']}} @else /shop/order/commentList/{{session('wid')}}/{{$val['id']}} @endif" data-orderno="" data-kdtid="">评价</a>
                                    @else
                                        <a class="logistics btn btn-default" href="/shop/order/expresslist/{{session('wid')}}/{{$val['id']}}">物流</a>
                                        <a class="js-confirm-receive btn btn-default btn-in-order-list" href="/shop/order/commentList/{{session('wid')}}/{{$val['id']}}" data-orderno="" data-kdtid="18825274">查看评价</a>
                                    @endif
                                @elseif($val['status'] == 5 && $val['refund_status'] == 1)
                                    <a class="js-confirm-receive btn btn-default btn-in-order-list refundDel" href="##" data-orderno="" data-kdtid="{{$val['id']}}">取消退款</a>
                               @endif

                            </div>
                        </div>

                    </li>
                    @endforeach
                </div>
                @if(empty($orderData))
                <div class="empty-coupon-list center">
                    <div>
                        <h4>居然还没有订单</h4>
                        <p class="font-size-12 c-gray-dark">好东西，手慢无</p>
                    </div>
                    <div>
                        <a href="/shop/index/{{session('wid')}}" class="tag tag-big tag-orange" style="padding:8px 30px;">去逛逛</a>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script type="text/javascript">
        var wid = {{session('wid')}};
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}shop/js/order_index.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <script type="text/javascript">
    	$('.test-lazyload').picLazyLoad({
		    threshold: 200,
			effect : "fadeIn"
		});
    </script>
@endsection
