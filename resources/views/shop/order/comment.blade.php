@extends('shop.common.marketing')
@section('head_css')
    <script src='{{ config('app.source_url') }}shop/static/js/rem.js'></script>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_comment.css">
@endsection
@section('main')
    <div class="item">
        <!--商品信息-->
        <div class="ware box_bottom_1px">
            <div class="detail">
                <div class='detail_img'>
                    <img src="{{ imgUrl() }}{{$orderDetail['img']}}" alt="">
                </div>
                <div class='com_detail'>
                    <div class="content con-min con_detail">
                        <div class="name">{{$orderDetail['title']}}</div>
                        <div class="comment">{{$orderDetail['spec']}}</div>
                    </div>
                    <div class="number">
                        <div class="price">¥{{$orderDetail['price']}}</div>
                        <div class="num">x<span>{{$orderDetail['num']}}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <!---->
        <input id="wid" type="hidden" value="{{session('wid')}}" >
        <form id="myForm" action="" class='form_submit'>
            <input type="hidden" name="odid" value="{{$orderDetail['id']}}">
            <textarea id="content" name="content" rows="5" placeholder="说点什么吧，你的感受对其他朋友很重要"></textarea>
            <div id="text" style="display:none;">
            </div>
            <div class="upload_images">
                <div class="uploaderDiv relative">
                    <input id="btnUp" type="button" multiple="multiple" name="" class="absolute" value="" />
                    <img src="{{ config('app.source_url') }}shop/images/xj.png"/>
                </div>
            </div>
            <div class='com_evaluate'>
                <div class="population">
                    <div class="feel">总体感受</div>
                    <div class="evaluate">
                        <span data-placement="1" class="btn box_1px good">好评</span>
                        <span data-placement="2" class="btn box_1px middle">中评</span>
                        <span data-placement="3" class="btn box_1px wrong">差评</span>
                    </div>
                </div>
            </div>
        </form>
        <div class="mark">
            <div class="title">服务打分</div>
            <div class="class">
                <div class="describe clearfix">
                    <p>商品描述</p>
                    <p class='clearfix'>
                        <span data-index="1"></span>
                        <span data-index="2"></span>
                        <span data-index="3"></span>
                        <span data-index="4"></span>
                        <span data-index="5"></span>
                    </p>
                </div>
                <div class="serice clearfix">
                    <p>商家服务</p>
                    <p class='clearfix'>
                        <span data-index="1"></span>
                        <span data-index="2"></span>
                        <span data-index="3"></span>
                        <span data-index="4"></span>
                        <span data-index="5"></span>
                    </p>
                </div>
                <div class="speed clearfix">
                    <p>发货速度</p>
                    <p class='clearfix'>
                        <span data-index="1"></span>
                        <span data-index="2"></span>
                        <span data-index="3"></span>
                        <span data-index="4"></span>
                        <span data-index="5"></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="share">
            <div data-id="0" class='input_order'></div>
            <div>评价分享给好友</div>
        </div>
        <div class="submit">提交</div>
    </div>
    @include('shop.common.footer') 
@endsection
@section('page_js')
    <script type="text/javascript"> 
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/jquery-2.1.4.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/order_comment.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/ajaxupload.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/upImage.js"></script>
@endsection
