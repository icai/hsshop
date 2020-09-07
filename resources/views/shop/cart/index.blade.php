@extends('shop.common.marketing')
@section('head_css')
    <script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cart_d4c65a42c9967641395785e90c4463a7.css">
    <style type="text/css">
        .block-list-cart .block-item-cart .name-card .num .txt {
            background: #fff;
        }
        .hide{display:none;}
        .check{cursor: pointer;}
    </style>
@endsection
@section('main')

<div class="container">
<div class="content clearfix js-page-content">
    <input id="wid" type="hidden" value="{{session('wid')}}" />
   
    <div id="cart-container">
        <div>
            <!-- 有数据的时候 -->
            @if(!empty($cartData['data']))
            <div class="js-shop-list shop-list">
                <div class="block block-order block-cart">
                        <div class="header cart_header">
                            <div class="select-group js-select-group cart_check">
                                <span class="check" ></span>
                            </div>
                            <a class="shop-title cart_shop_title">
                                <i class="custom-store-img">店铺：</i>{{$cartData['shop']['shop_name']}}</a>
                            <a href="javascript:;" data-type="cart" class="j-edit-list pull-right edit-list">
                                编辑</a>
                        </div>
                        <div>
                            <ul class="js-list block block-list block-list-cart border-0 on_sale" >
                                @forelse($cartData['data'] as $v)
                                    @if($v['flag'] == 1)
                                        <li class="block-item cart_li block-item-cart" data-placeholder="{{$v['num']}}" data-id="{{$v['id']}}" id="li_{{$v['id']}}">
                                            <div>
                                                <div class="check-container cart_check_span" style="cursor: pointer;">
                                                    <span class="check" data-id="{{$v['id']}}"></span>
                                                </div>
                                                <div class="name-card cart_shop_name clearfix">
                                                    <a href="/shop/product/detail/{{$cartData['shop']['id']}}/{{$v['product_id']}}?_pid_={{session('mid')}}" class="thumb js-goods-link cart_img">
                                                        <img data-src="{{$v['img']}}" class="js-lazy test-lazyload" data-original="{{ imgUrl() }}{{$v['img']}}" src="{{ imgUrl() }}{{$v['img']}}">
                                                    </a>
                                                    <div class="detail cart_shop_detail">
                                                        <a href="/shop/product/detail/{{$cartData['shop']['id']}}/{{$v['product_id']}}?_pid_={{session('mid')}}" class="js-goods-link">
                                                            <h3 class="title js-ellipsis cart_title_h3">
                                                                {{$v['title']}}
                                                            </h3>
                                                            <p class="sku ellipsis cart_sku">
                                                                @if($v['prop1'])<span>{{$v['prop1']}}:{{$v['prop_value1']}}</span>@endif
                                                                @if($v['prop2'])<span>{{$v['prop2']}}:{{$v['prop_value2']}}</span>@endif
                                                                @if($v['prop3'])<span>{{$v['prop3']}}:{{$v['prop_value3']}}</span>@endif
                                                            </p>
                                                        </a>
                                                        <div class='cart_num_price'>
                                                            <div class="num">
                                                                <div class="num_show hide">
                                                                    ×<span class="num-txt">{{$v['num']}}</span>
                                                                </div>
                                                                <div class="quantity">
                                                                    <button type="button" class="minus"></button>
                                                                    <input disabled data-id="{{$v['id']}}" type="text" pattern="[0-9]*" class="txt" value="{{$v['num']}}">
                                                                    <button type="button" class="plus"></button>
                                                                    <div @if(!empty($v['buy_min']))data-buyMin="{{$v['buy_min']}}" @else data-buyMin="1" @endif class="response-area response-area-minus" style="cursor: pointer;"></div>
                                                                    <div @if(!empty($v['quota']))data-quota="{{$v['quota']}}" @else data-quota="0" @endif class="response-area response-area-plus" style="cursor: pointer;"></div>
                                                                </div>
                                                            </div>
                                                            <div class="price c-red-text">￥
                                                                <span>{{$v['price']}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="error-box"></div>
                                                </div>
                                                <div class="delete-btn" data-id="{{$v['id']}}"  style="cursor: pointer;">
                                                    <span>删除</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    @endforeach
                            </ul>
                        </div>
                </div>
            </div>
            @endif
            <div @if(empty($invalid)) style="display: none;" @endif class="js-invalid-goods shop-list no_sale_div">
                <p class="block invalid-list-title">以下商品无法一起购买</p>
                <div class="block block-list-cart block-order border-top-0">
                    <form id="invalid">
                    <ul class="block border-0 invalid-list js-invalid-list no_sale">
                        @forelse($invalid as $v)
                        @if($v['flag'] != 1)
                            <li class="block-item cart_li block-item-cart error" style='padding: 0'>
                                <div index="0">
                                    <div class="check-container hide" style="cursor: pointer;">
                                        <span class="check"></span>
                                    </div>
                                    <div class="name-card cart_shop_name clearfix" style='padding: 0.2rem'>
                                        <a href="/shop/product/detail/{{$cartData['shop']['id']}}/{{$v['product_id']}}?_pid_={{session('pid')}}" class="thumb js-goods-link cart_img">
                                            <img data-src="{{$v['img']}}" class="js-lazy test-lazyload" data-original="{{ imgUrl() }}{{$v['img']}}" src="{{ imgUrl() }}{{$v['img']}}">
                                        </a>
                                        <div class="detail cart_shop_detail">
                                            <a href="/shop/product/detail/{{$cartData['shop']['id']}}/{{$v['product_id']}}?{{session('pid')}}" class="js-goods-link">
                                                <h3 class="title js-ellipsis cart_title_h3">{{$v['title']}}</h3>
                                                <p class="sku ellipsis cart_sku">
                                                    @if($v['prop1'])<span>{{$v['prop1']}}:{{$v['prop_value1']}}</span>@endif
                                                    @if($v['prop2'])<span>{{$v['prop2']}}:{{$v['prop_value2']}}</span>@endif
                                                    @if($v['prop3'])<span>{{$v['prop3']}}:{{$v['prop_value3']}}</span>@endif
                                                </p>
                                            </a>
                                            <div class='cart_num_price'>
                                                <div class="price c-red-text">
                                                    @if($v['flag'] == -1)
                                                        商品已删除
                                                    @elseif($v['flag'] == 0)
                                                        商品已下架
                                                    @elseif($v['flag'] == 3)
                                                        规格发生变化
                                                    @elseif($v['flag'] == 4)
                                                        商品已售罄
                                                    @endif
                                                </div>
                                            </div>
                                         </div>
                                        <div class="error-box"></div>
                                    </div>
                                    <div class="delete-btn" style="cursor: pointer;"><span>删除</span></div>
                                </div>
                                <input type="hidden" name="ids[]" value="{{$v['id']}}" />
                            </li>
                        @endif
                        @endforeach
                    </ul>
                    </form>
                    <div class="center clear-block">
                        <button id="sub" class="btn clear-btn js-clear card_clear_btn">清空失效商品</button></div>
                </div>
            </div>
            <!-- 商品失效的时候 -->
            @if(!empty($cartData['data']))
            <div style="padding:0;" class="js-bottom-opts bottom-fix">
                <div class="bottom-cart clear-fix">
                    <div class="select-all">
                        <span class="check"></span>全选<div class='cart_total_num hide'>(<i></i>)</div>
                    </div>
                    
                    <div class="total-price c-gray-dark cart_total_price">
                        <div class='cart_total'>
                            <em class="font-control">合计:</em>
                            <p class="price-control">￥<span class="js-total-price" style="color: rgb(153, 153, 153);">0</span></p>
                        </div>
                        <div>
                            <p class="c-gray-dark">不含运费</p>
                        </div>
                    </div>
                    <button href="javascript:;" disabled="disabled" class="js-go-pay btn btn-orange-dark font-size-14">结算<div class='cart_total_num hide'>(<i></i>)</div>
                    </button>
                    <button href="javascript:;" class="j-delete-goods btn font-size-14 btn-red" disabled="disabled" style="display: none;">删除<div class='cart_total_num hide'>(<i></i>)</div>
                    </button> 
                </div>
            </div>
            @endif
            <!-- 有数据的时候 -->
        @if(empty($cartData['data']) && empty($invalid))
            <!-- 暂无数据的时候 -->
            <div style="padding-top:60px;min-height: 400px;" class="empty-list ">
                <div class="empty-list-header">
                    <h4>购物车快饿瘪了 T.T</h4>
                    <span>快给我挑点宝贝</span>
                </div>
                <div class="empty-list-content">
                    <a href="/shop/index/{{session('wid')}}" class="js-go-home home-page tag tag-big tag-orange">去逛逛</a>
                </div>
            </div>
        @endif
        <div style="padding-top:60px;min-height: 400px;" class="empty-list hide">
            <div class="empty-list-header">
                <h4>购物车快饿瘪了 T.T</h4>
                <span>快给我挑点宝贝</span>
            </div>
            <div class="empty-list-content">
                <a href="/shop/index/{{session('wid')}}" class="js-go-home home-page tag tag-big tag-orange">去逛逛</a>
            </div>
        </div>
        <!-- 暂无数据的时候 -->
        </div>
    </div>
</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var _host = "{{ config('app.source_url') }}";
        var imgUrl ="{{ imgUrl() }}";
        var data = {!!json_encode($cartData)!!};
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/cart.js"></script>
@endsection


