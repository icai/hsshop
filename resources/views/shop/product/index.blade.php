@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/product_list.css">
    <style type="text/css">
        .js-footer{margin-bottom:51px;}
    </style>
@endsection
@section('main')
<div class="body-fixed-bottom">
    <div class="container " id = "container">
        <div class="content no-sidebar">
            <div class="content-body js-page-content">
                <div class="custom-title">
                    <h2 class="title">
                        最热商品 
                    </h2>
                </div>
                <ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;">
                    <li class="js-goods-card goods-card card" v-for="list in goodList">
                        <a :href="list.url" class="js-goods link clearfix" title="">
                            <div class="photo-block" style="background-color: rgb(255, 255, 255);">
                                <img class="goods-photo js-goods-lazy" :src="imgUrl + list.img" />
                            </div>
                            <div class="info">
                                <p class="goods-title" v-html="list.title"></p>
                                <p class="goods-price"><em v-html="list.price"></em></p>
                                <div class="goods-buy btn1"></div>
                                <div class="js-goods-buy buy-response"></div>
                            </div>
                        </a>
                    </li>
                </ul>
                <!-- 详细列表模式 -->
            </div>
        </div>
        <input type="hidden" value="{{session('mid')}}" id="mid"/>
        <div id="shop-nav" v-if="footer != {} && footer.menu" v-cloak>
            <div class="js-navmenu js-footer-auto-ele shop-nav nav-menu nav-menu-1 has-menu-3" v-if="footer.menusType == 1">
                <div class="nav-special-item">
                    <a href="/shop/index/{{$wid}}" class="home">主页</a>
                </div>
                <div class="nav-items-wrap">
                    <div class="nav-item" v-for="(menu,index) in footer.menu" :style="{width:menu.width}">
                        <a class="mainmenu js-mainmenu" :href="menu.submenus.length > 0 ? 'javascript:void(0);': menu.linkUrl" v-on:click="showSub(menu,index)">
                            <span class="mainmenu-txt">
                                <i class="arrow-weixin" v-if="menu.submenus.length"></i>@{{menu.title}}</span>
                        </a>
                        <!-- 子菜单 -->
                        <div class="submenu js-submenu" style="display:none" v-show = "menu.submenusShow && menu.submenus.length">
                            <span class="arrow before-arrow"></span>
                            <span class="arrow after-arrow"></span>
                            <ul>
                                <li v-for = "submenu in menu.submenus">
                                    <a :href="submenu['linkUrl']">@{{submenu['title']}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="js-navmenu js-footer-auto-ele shop-nav nav-menu nav-menu-2 has-menu-3" v-bind:style="{backgroundColor: footer.bgColor}" v-if="footer.menusType == 2">
                <ul class="clearfix" style="display:flex;">
                    <li v-for="menu in footer.menu" style="width:100%;">
                        <a :href="menu.linkUrl" style="
                        background-size: 64px 50px;height:50px;
                        " v-bind:style="menu.styleObject">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{session('wid')}}" id="wid">
    <input type="hidden" value="{{ imgUrl() }}" id="sourceUrl">

    <!--加入购物车-->
    @if (!empty($cartNum))
        <div id="right-icon" class="js-right-icon no-text">
            <div class="js-right-icon-container right-icon-container clearfix" style="width: 50px;">
                <a id="global-cart" href="{{ config('app.url') }}shop/cart/index/{{session('wid')}}" class="icon new s1" style="">
                    <p class="icon-img"></p>
                    <p class="icon-txt">购物车</p>
                    <span class="goods-num">{{$cartNum}}</span>
                </a>
                <a class="js-show-more-btn icon show-more-btn hide new"></a>
            </div>
        </div>
    @endif
    <!--加入购物车，提示-->
    <div class="motify">
        <div class="motify-inner">已成功添加到购物车</div>
    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
<!-- 加入购物车弹窗 -->
<script src="{{ config('app.source_url') }}shop/static/js/vue.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var wid = {!!$wid!!};
</script>
<script type="text/javascript">
    var num = {{$cartNum}};
    if(num == 0){
        $(".goods-num").hide();
    }
    var id = {!!$wid!!};
    new Vue({
      el: '#container',
      data:{
        footer:{},
        goodList:[],
        imgUrl:imgUrl
      },
      methods: {
          showSub:function(menu,index){
            for(var i=0;i<this.footer.menu.length;i++){
                if(i != index){
                    this.footer.menu[i].submenusShow = false;
                }
            }
            if(menu.submenus.length>0){
                if(menu.submenusShow){
                    menu.submenusShow = false;
                }else{
                    menu.submenusShow = true;
                }
            }
          },
      },
      beforeCreate: function () {
        var that = this;
        this.$http.get("/shop/member/indexHome/"+ id).then(
            function (res) {
                if(res.body.data.footer != ''){
                    var footer = JSON.parse(res.body.data.footer);
                    this.footer = footer;
                    for(var i =0;i< this.footer.menu.length;i++){
                        if(footer.menu[i]['icon'].substr(0,7) == '/static'){
                            footer.menu[i].styleObject = { backgroundImage:'url('+ _host + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};    
                        }else{
                            footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};
                        }
                    }
                }
            },function (res) {
            // 处理失败的结果
            }
        );
        
      },
      created: function () {
        var url = location.host;
        var mid = $("#mid").val();
        this.$http.get("/shop/product/list/" + wid).then(function(res){
            console.log(res);
            console.log(res.body.data.data);
            if(res.body.data.data.length){
                console.log(res);
                for(var i = 0;i<res.body.data.data.length;i++){
                    res.body.data.data[i]['url'] = 'http://' + url + '/shop/product/detail/' + res.body.data.data[i]['wid'] + '/' + res.body.data.data[i]['id'] + '?_pid_=' + mid;
                    res.body.data.data[i]['price'] = res.body.data.data[i]['is_price_negotiable'] === '1' ? "面议" : '￥' + res.body.data.data[i]['price'];
                    this.goodList.push(res.body.data.data[i]);
                }
            }
        })
        //下拉加载
        // 下拉加载更多
        var page = 2;
        var loading = false;  //状态标记
        var hasData = true;
        var that = this;
        window.onscroll = function () {
            if (scrollTop() + windowHeight() >= (documentHeight() - 50)) {
                if (loading) return;
                loading = true;
                if(!hasData){
                        return;
                }
                that.$http.get("/shop/product/list/" + wid, {params:{page:page}}).then(function(res){
                    if(res.body.data.data.length){
                        for(var i = 0;i<res.body.data.data.length;i++){
                            res.body.data.data[i]['url'] = 'http://' + url + '/shop/product/detail/' + res.body.data.data[i]['wid'] + '/' + res.body.data.data[i]['id'] + '?_pid_=' + mid;
                            res.body.data.data[i]['price'] = res.body.data.data[i]['is_price_negotiable'] === '1' ? "面议" : '￥' + res.body.data.data[i]['price'];
                            that.goodList.push(res.body.data.data[i]);
                        }
                    }
                    page++;
                    loading = false;
                })
            }
        }
        console.log(this.goodList);
      }
    })
    //获取页面顶部被卷起来的高度
    function scrollTop() {
        return Math.max(
            document.body.scrollTop,
            document.documentElement.scrollTop);
    }
    //获取页面文档的总高度
    function documentHeight() {
        //现代浏览器（IE9+和其他浏览器）和IE8的document.body.scrollHeight和document.documentElement.scrollHeight都可以
        return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    }
    function windowHeight() {
        return (document.compatMode == "CSS1Compat") ?
            document.documentElement.clientHeight :
            document.body.clientHeight;
    }
</script>
@endsection
