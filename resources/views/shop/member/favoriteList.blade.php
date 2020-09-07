<!--add by 韩瑜 2018-9-5 收藏列表页-->
@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/favoriteList.css">
@endsection
@section('main')
   <!-- 顶部分类 -->
   <div id="main">
        <div class="vux-tab tabNav">
            <div v-for="nav in nav" :class="nav.isActive ? 'vux-tab-selected' : ''" class="vux-tab-item" @click="chooseTab(nav)" >
            	<span v-html="nav.title"></span>
            </div>
            <div class="vux-tab-ink-bar vux-tab-ink-bar-transition-backward">
            </div>
        </div>
        <!-- 中间内容 -->
        <ul class="content" v-if="favoriteList.length">
            <li v-for="(list,index) in favoriteList" :key="index">
                <div class="list_detail">
                    <div class="goods_info flex_between_v">
                    	<a :href = "list.validity == 'INVALID' ? noGo() : judgeGo(list.type,list.relative_id,list.share_product_id)">
                        	<img :src="imgUrl + list.image" width="100">
                        </a>
                        <div class="describe" >
                        	<span class="title" v-html="list.title"></span>
                        	<span class="price" v-html="list.price"></span>
                        	<p>
                        		<span class="is-sx" v-if="list.validity == 'INVALID'">已失效</span>
                        		<span class="no-collect" @click="CancelCollect(list.relative_id,list.type,index)">取消收藏</span>
                        	</p>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <!--无收藏-->
        <div class="no-collect" v-if="!favoriteList.length">
        	<img src="{{ config('app.source_url') }}shop/images/nocollect@2x.png" alt="" />
        	<p>暂无收藏商品哦！</p>
        </div>
        <!--删除提示-->
		<div class='deltip'>
	        <div>取消成功</div>
	    </div>
   </div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var wid = "{{$wid}}";
        var host = "{{config('app.url')}}";
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/rem.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/favoriteList.js"></script>
@endsection
