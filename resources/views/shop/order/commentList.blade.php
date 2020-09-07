@extends('shop.common.marketing')
@section('head_css')
    <script src='{{ config('app.source_url') }}shop/static/js/rem.js'></script>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_comment_list.css">
@endsection
@section('main')
    <div class="container " style="min-height: 394px;">
        <div class="content confirm-container">
            <div class="app app-order">
                <div class="app-inner inner-order" id="js-page-content">
                    <div class="js-goods-list-container block block-list block-order ">
                        <div class="js-goods-list">
                            @forelse($order['orderDetail'] as $val)
                            <div class="js-goods-item order-goods-item clearfix block-list">
                                <div class="name-card name-card-goods box_bottom_1px">
                                    <a href="/shop/product/detail/{{session('wid')}}/{{$val['product_id']}}" class="thumb">
                                        <img class="js-view-image" src="{{ imgUrl($val['img']) }}" alt="">
                                    </a>
                                    <div class="detail">
                                        <div class="clearfix detail-row">
                                            <div class="right-col text-right">
                                                <div class="price">￥<span>{{$val['price']}}</span></div>
                                            </div>
                                            <div class="left-col">
                                                <a href="javascript:;">
                                                    <h3 class="l2-ellipsis">{{$val['title']}}</h3>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="clearfix detail-row">
                                            <div class="right-col">
                                                <div class="num c-gray-darker">x<span class="num-txt">{{$val['num']}}</span></div>
                                            </div>
                                            <div class="left-col">
                                                <p class="sku">
                                                    {{$val['spec']}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($reqFrom != 'aliapp')
                                <div class="clearfix detail-row">
                                    <div class="right-col">
                                        <div class="goods-action">
                                            @if($val['is_evaluate'] == 0)
                                                <a class="tag-white tag-opt" href="/shop/order/comment/{{$order['wid']}}?odid={{$val['id']}}">评价 </a>
                                            @else
                                            <a class="tag tag-white tag-opt" href="/shop/product/evaluateDetail/{{$order['wid']}}?eid={{$val['evaluate']['id']}}">查看评价 </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>

                    </div>

                </div>


                <div class="app-inner inner-order" style="display:none;padding-top:40px;" id="js-datetime-picker-poppage">
                </div>
                <div class="app-inner inner-order selffetch-address" style="display:none;padding-top:40px;" id="js-address-poppage">
                </div>



            </div>
        </div>
    </div>
    @include('shop.common.footer') 
@endsection
@section('page_js')
<script>
    $(function(){
        var screenHeight = window.screen.height;
        var domHeight  = $("body").height();
        if(domHeight <　screenHeight){
            $(".container").css("height",domHeight + screenHeight - domHeight - 86);
        }
    })
</script>
@endsection
