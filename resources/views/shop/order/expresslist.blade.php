@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/order_7zglwyum.css">
    
    <style type="text/css">
        .block-list-cart .block-item-cart .name-card .num .txt {
            background: #fff;
        }
        .hide{display:none;}
        .check{cursor: pointer;}
    </style>
@endsection
@section('main')
    
    <div id="container" class="container " style="min-height: 581px;">
        <div class="top-tip"></div>
        <div class="package-list box_bottom_1px mg_btm_10">
            <ul>
                <div class="listwrapper">
                </div>
            </ul>
        </div>
        <div id="express_content">
        </div> 
    </div>
        <input type="hidden" value="{{$id}}" id="order_id" />
            
        </div>
@include('shop.common.footer') 
@endsection
@section('page_js')
    <script type="text/javascript">
        var wid = {{session('wid')}};
        var _host = "{{ config('app.source_url') }}";
        var imgUrl ="{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script> 
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script> 
    <script src="{{ config('app.source_url') }}shop/js/order_expresslist.js"></script>


@endsection


