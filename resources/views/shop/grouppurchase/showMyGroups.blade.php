@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showMyGroups.css"  media="screen">

@endsection

@section('main')
<div id="app" style="width: 100%;min-height: 100%;">
    <div class="orderList" v-clock v-if="pageShow">
        <!--导航栏-->
        <div class="tabNav flex_between_v">
            <span :class="nav_index==index?'select':''" v-for="(item,index) in nav_bar"  @click="navChange(item.status,index)" v-text="item.title"></span>
        </div>

        <ul class="goods_list" v-if="groupList.length > 0">
            <li v-for="item in groupList">
                <!--商品详情-->
                <a class="list_detail" :href="'/shop/grouppurchase/groupon/'+item.id+'/'+wid+'?group_type='+item.group_type">
                    <div class="goods_info">
                        <img :src="imgUrl+item.rule.pimg" width="100" />
                        <div class="describe">
                            <p class="goods_title">[[item.rule.ptitle]]</p>
                            <div class="pin_info">
                                <span class="co_999">[[item.rule.groups_num]]人团</span>
                                <span class="co_b1">￥[[item.rule.min]]</span>
                                <span class="co_b1 fr">[[item.statusText]]</span>
                            </div>
                        </div>
                    </div>
                </a>
                <!--商品列表功能按钮-->
                <div class="list_fun">
                    <div class="btn Bred" v-if="item.statusText === '待成团'" @click="getShare(item)">邀请好友拼团</div>
                    <a class="btn" v-if="item.statusText !== '待成团'" :href="'/shop/grouppurchase/groupon/'+item.id+'/'+wid+'?group_type='+item.group_type">团详情</a>
                    <a class="btn Bred" :href="'/shop/order/groupsOrderDetail/'+item.oid">订单详情</a>
                </div>
            </li>
        </ul>
        <div class="noMore" v-if="noMore && groupList.length > 3">没有更多数据</div>
        <div class="noList noGoods" v-if="groupList.length == 0">
            <div class="imgDiv">
                <img src="{{ config('app.source_url') }}shop/images/no-order.png"/>
            </div>
            <p class="no-order-tips">您还没有相关的订单</p>
            <div class="no-order-link">
                <a href="{{ config('app.url') }}shop/index/{{session('wid')}}">去首页看看</a>
            </div>
        </div>
        <!-- 分享mask -->
        <div class="share_mask" v-if="shareShow" @click="shareHide">
            <img src="{{ config('app.source_url') }}/shop/static/images/guide_arrow@2x.png" class="share_img"/>
        </div>
        <!-- 数据加载 -->
        <div class="loading" v-if="!noMore && groupList.length > 3">
            <img src="{{ config('app.source_url') }}/shop/static/images/loading.gif">
        </div>
    </div>
    <!-- 页面加载 -->
    <div class="pageMask" v-if="!pageShow">
        <img class="pageLoading" src="{{ config('app.source_url') }}/shop/static/images/loading.gif">
    </div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";;//静态资源
	var host ="{{ config('app.url') }}";;//网址域名
	var imgUrl = "{{ imgUrl() }}";//动态图片地址
    var wid = "{{session('wid')}}";
</script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/clipboard.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/js/showMyGroups.js" ></script>
<script type="text/javascript">
var url = location.href.split('#').toString();
$.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){ 
    if(data.errCode == 0){
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: data.data.appId, // 必填，公众号的唯一标识
            timestamp: data.data.timestamp, // 必填，生成签名的时间戳
            nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
            signature: data.data.signature,// 必填，签名，见附录1
            jsApiList: [
                'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'chooseWXPay'
            ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
    }
})
wxShare();
function wxShare(){
    if(vm.share){  
        var share_title =vm.share.share_title || "团详情分享标题";
        var share_desc =vm.share.share_desc || "团详情分享说明";
        var share_img =vm.share.share_img?host+vm.share.share_img:"https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=4186641830,3509273267&fm=173&s=689200D71221B14942BF9AA70300C00B&w=600&h=400&img.JPEG";
        var share_url=vm.share.share_url;  
        @if($reqFrom == 'aliapp')
        my.postMessage({share_title:share_title,share_desc:share_desc,share_url:share_url,imgUrl:share_img});
        @endif
        @if($reqFrom == 'wechat')
        wx.ready(function () {
            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: share_title, // 分享标题
                desc: share_desc, // 分享描述
                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: share_img, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享给朋友
            wx.onMenuShareAppMessage({
                title: share_title, // 分享标题
                desc: share_desc, // 分享描述
                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl:share_img, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享到QQ
            wx.onMenuShareQQ({
                title: share_title, // 分享标题
                desc: share_desc, // 分享描述
                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: share_img, // 分享图标
                success: function () {
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                   // 用户取消分享后执行的回调函数
                }
            });

            //分享到腾讯微博
            wx.onMenuShareWeibo({
                title: share_title, // 分享标题
                desc: share_desc, // 分享描述
                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: share_img, // 分享图标
                success: function () {
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.error(function(res){
                // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
            });
        });
        @endif
    }
}
</script>

@endsection