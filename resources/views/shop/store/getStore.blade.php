@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/getStore.css">
@endsection
@section('main')
    <div class="container">
        <div class="search">
            <input v-model="search" @input="searchInput" class="search_input" type="text" name="search" placeholder="请输入门店名称或所在省、市、区县" >
            <!-- <a @click="searchBut" class="search_button" href="javascript:void(0);">搜索</a> -->
            <i class="search_icon"></i>
        </div>
        <ul class="store_list" v-cloak>
            <li v-for="(item,index) in storeList" v-if="storeList" class="store_item">
                <div class="store_item_name">
                    店铺：[[item.title]]
                    <div class="subtitle">营业时间：[[item.week]] [[item.open_time]]~[[item.close_time]]</div>
                </div>
                <div class="store_item_content">
                    <a class="store_item_address" :href="'/shop/store/storeMap/'+item.id">
                        <img class="store_item_img" :src="'/'+item.file[0].s_path">
                        <span class="store_item_title">
                            [[item.address]]
                        </span>
                    </a>
                    <a class="phone" @click="openModal(item.phone)"></a>
                </div>
                <div class="store_item_footer">
                    商家推荐：[[item.comment]]
                </div>
            </li>
        </ul>
        <div class="noData" v-if="storeList.length == 0" style='text-align: center;margin-top: 10px'>
            <img style='width: 114px;height: 125px;' src="{{ config('app.source_url') }}shop/images/noStore.png" alt="">
            <p style='color: #999999;font-size: 14px;text-align: center;margin-top: 10px'>抱歉，暂无该门店！</p>
        </div>
    </div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <script>
        var reqFrom = '{{ $reqFrom }}';
    </script>
    @if($reqFrom == 'aliapp')
	<script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript">
    var source = '{{ config('app.source_url') }}';
    var imgUrl = "{{ imgUrl() }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/getStore.js"></script>
@endsection
