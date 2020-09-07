@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/distribute_apply.css">
@endsection
@section('main')
<div class="container" id="container" >
    <div class="content" :style="{background:bg_color}">
        <div class="content-body">
            <div v-for="(list,index) in lists" v-if="lists.length" v-cloak>
                <!-- 富文本编辑器 -->
                <div class="custom-richtext js-custom-richtext js-lazy-container" v-if="list['type']=='rich_text'" :style="{background:list.bgcolor}">
                    <div class='rich_text_html' v-html = "list['content']"></div>
                </div>
                <!-- 富文本编辑器 -->
                <!-- 图片广告 -->
                <div class="image_ad" v-if="list['type']=='image_ad' && list['images'].length > 0">
                    <!-- 分开大图模式 -->
                    <ul class="custom-image clearfix js-image-ad-seperated js-view-image-list js-lazy-container" v-if="list['advsListStyle'] ==3">
                        <li class="" v-for = "image in list['images']" v-if="list['advSize']==1">
                            <a href="javascript:void(0);">
                                <h3 class="title" v-html="image.title" v-if="image.title"></h3> 
                                <img class="js-lazy js-view-image-item" :src="imgUrl + image.FileInfo.path" :data-src="imgUrl + image.FileInfo.path" :class="{'J_parseImg':list['resize_image']==1}"> 
                            </a>
                        </li>
                        <!-- 分开小图模式 -->
                        <li class="custom-image-small" v-for = "image in list['images']" v-if="list['advSize']==2">
                            <a href="javascript:void(0);">
                                <div>
                                    <h3 class="title" v-html="image.title" v-if="image.title"></h3> 
                                    <img class="js-lazy " :src="imgUrl + image.FileInfo.path" :data-src="imgUrl + image.FileInfo.path" :class="{'J_parseImg':list['resize_image']==1}"> 
                                </div>
                            </a>
                        </li>
                        <!-- 分开小图模式 -->
                    </ul>
                    <!-- 分开大图模式 -->
                    <!-- 图片广告折叠模式 -->
                    <div class="swiper-container" v-if="list['advsListStyle'] ==2" :id="list['attr_id']">
                        <div class="swiper-wrapper" style="height:auto;width:100%;">
                            <a class="swiper-slide" style="text-align:center" href="javascript:void(0);" v-for="image in list['images']"> 
                                <img class="js-res-load" style="height:auto;width:100%" :src="imgUrl + image.FileInfo.path" :data-src="imgUrl + image.FileInfo.path" :class="{'J_parseImg':list['resize_image']==1}">
                                <h3 class="title" style="position:absolute;bottom:0;width:100%;background-color:rgba(0,0,0,0.4);color:#fff;line-height:30px;text-align:left;" v-html="image.title" v-if="image.title"></h3>
                            </a>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <!-- 图片广告折叠模式 -->
                </div>
                <!-- 图片广告 -->

                <!-- 标题样式 -->
                <div class="custom-title-noline" v-if="list['type']=='title'" v-bind:style="{background:list.bgColor}">
                    <div class="custom-title wx_template" :class="{'text-left':list['showPosition']==1,'text-center':list['showPosition']==2,'text-right':list['showPosition']==3}">
                        <h2 class="title">
                            <span v-html="list.titleName"></span>
                        </h2>
                        <p class="sub_title" v-if="list.titleStyle == 1" v-html="list.subTitle"></p>
                        <p class="sub_title" v-if="list.titleStyle == 2">
                            <span class="sub_title_date" v-html="list.date"></span>
                            <span class="sub_title_author" v-html="list.author"></span>
                        </p>
                    </div>
                </div>
                <!-- 标题样式 -->

                <!-- 公告样式 -->
                <notice v-if="list.type == 'notice'" :content = "list.content" :bg-color="list.colorBg" :bg-txt="list.txtBg"></notice>
                <!-- 公告样式 -->

                <!-- 魔方组件 -->
                <cube :list="list" :wid="wid" v-if="list.type == 'cube'"></cube>
                <!-- 魔方组件 -->

                <!-- 联系方式组件 -->
                <cmobile :list="list" v-if="list.type == 'mobile'"></cmobile>
                <!-- 联系方式组件 -->

                <!-- 分割线组件 -->
                <serline :list="list" v-if="list.type == 'line'"></serline>
            </div>
           
        </div>
        <button class="btn btn_apply" v-if="isDistribute == 0" @click="applyDistribue">提交申请</button>
        <button class="btn btn_apply" v-else @click="goMymoney">我的财富</button>
        
    </div>
    <div class="result_model" v-if="showModel" v-cloak>
        <div class="result_content" >
            <img v-if="applySuccess" src="{{ config('app.source_url') }}shop/images/apply_success.png" alt="">
            <img v-else src="{{ config('app.source_url') }}shop/images/apply_exam.png" alt="">
            <p v-text="modelInfo"></p>
        </div>
    </div>
   
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var host ="{{ config('app.url') }}";
    var wid = "{{session('wid')}}";
    var imgUrl = "{{ imgUrl() }}";
    var mid = '{{ session("mid") }}';
    var data = {!! json_encode($data)  !!};
    var logdata = {!! json_encode($logdata) !!};
    var isBind = {{$__isBind__}};
    var isDistribute = {{ $is_distribute }};
</script>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/distribute_apply.js"></script>
<script>
   $('body').on('click','.J_parseImg',function(){
        var nowImgurl = $(this).data('src');
        wx.previewImage({
            "urls":[nowImgurl],
            "current":nowImgurl
        });
    });
    /*
    * @auther 黄新琴
    * @desc 富文本图片点击放大
    * @date 2018-10-18
    * */
    $('body').on('click','.js-custom-richtext',function(){
        var imgs = [];
        var imgObj = $(this).find('img');
        for(var i=0; i<imgObj.length; i++){
            imgs.push(imgObj.eq(i).attr('src'));
            imgObj.eq(i).click(function(){
                var nowImgurl = $(this).attr('src');
                wx.previewImage({
                    "urls":imgs,
                    "current":nowImgurl
                });
            });
        }
    });
		// 微信分享
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
            
            wxShare();
        })
        function wxShare(){
            if(typeof app.list1.share_title !="undefined"){  
                var share_title =app.list1.share_title ? app.list1.share_title : app.list1.title;
                var share_desc =app.list1.share_desc ? app.list1.share_desc : app.list1.subtitle;
                var share_img =app.list1.share_img ?imgUrl + app.list1.share_img : imgUrl + app.list1.img2;
                var share_url=host+'shop/distribute/apply/'+wid +'/'+app.list1.id
                wx.ready(function () {

                    //分享到朋友圈
                    wx.onMenuShareTimeline({
                        title: share_title, // 分享标题
                        desc: share_desc, // 分享描述
                        link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
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
                        link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                        imgUrl: share_img, // 分享图标
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
                        link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
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
                        link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
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
            }else{
                setTimeout(function(){
                    wxShare();
                },50)
            }
        }
</script>
@endsection
