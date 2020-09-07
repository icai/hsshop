@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/getStore.css">
@endsection
@section('main')
    <div class="container">
        <form method="get" action="">
        <div class="search">
            <input class="search_input" type="text" name="word" placeholder="请输入门店名称或所在省、市、区县" >
            <i class="search_icon"></i>
        </div>
        </form>
        @if($list['data'])
        <ul class="store_list">
            @foreach($list['data'] as $val)
            <li  class="store_item">
                <div class="store_item_name">
                    店铺：{{ $val['title'] }}
                </div>
                <div class="store_item_content">
                    <a class="store_item_address" href="/shop/store/storeMap/{{ $val['id'] }}/1">
                        <img class="store_item_img" src="{{ imgUrl() }}{{ $val['images'] }}">
                        <span class="store_item_title">
                            {{ $val['province'] }}{{ $val['city'] }}{{ $val['area'] }}{{ $val['address'] }}
                        </span>
                    </a>
                    <a class="phone" href="tel:{{ $val['telphone'] }}"></a>
                </div>
                <div class="store_item_footer">
                    特色服务：{{ $val['comment'] }}
                </div>
            </li>
            @endforeach
        </ul>
        @endif
        <div class="noData"></div>
    </div>
    @include('shop.common.footer')
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/javascript">
    var source = '{{ config('app.source_url') }}';
    var imgUrl = "{{ imgUrl() }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/getStore.js"></script>
@endsection
