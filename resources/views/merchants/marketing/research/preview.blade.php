<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css"> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/resesrch_preview.css">
    <style type="text/css">
        html{position:relative;}
        body {
            background-position: center top;
            background-repeat: no-repeat;
            background-size: 100% auto;
            background-size: cover;
            height:100%;
        }
        .card-coupon {
            flex-wrap: wrap;
            justify-content: center;
        }
        .member-card {
            width: 80% !important;
            margin-left: 0 !important;
            margin-top: 10px !important;
        }
        .member-card:nth-child(1){
            margin-top: 0 !important;
        }
        .tpl-fbb .swiper-container{
            overflow:auto;
        }
        .tpl-fbb .swiper-container {
            position:fixed;
        }
        .showLink{display:none;}
        #container{padding-bottom:50px;}
        .custom-nav-4 li img {
            vertical-align: middle;
            max-width: 50px;
            max-height: 50px;
        }
        .responsive-320 .content {
            width: 540px;
        }
        .content-sidebar{
            display: block;
            margin-left: 550px;
        }
    </style>
</head>
<body>
<div class="container" id="container" >
    <div class="content">
        <div class="content-body js-page-content">
            <div v-for="(list,index) in lists" v-if="lists.length" v-cloak>
                <!-- 日期 -->
                <deta-time v-if = "list.type == 'time'" :content="list"></deta-time>
                <!-- 文字框 -->
                <text-box v-if = "list.type == 'text'" :content="list"></text-box>
                <!-- 电话 -->
                <tel v-if = "list.type == 'phone'" :title="list.title" :phone="list.rule_phone_value"></tel>
                <!-- 文本投票 -->
                <text-vote v-if = "list.type == 'vote_text'" :content="list"></text-vote>
                <!-- 图片投票 -->
                <img-vote v-if = "list.type == 'vote_image'" :content="list" :imgurl="imgUrl"></img-vote>
                <!-- 文本预约 -->
                <txtbooking v-if = "list.type == 'appoint_text'" :content="list"></txtbooking>
                <!-- 图片预约 -->
                <imgbooking v-if = "list.type == 'appoint_image'" :content="list" :imgurl="imgUrl" ></imgbooking>
                <!-- 地域调查 -->
                <div v-if = "list.type == 'address'">
                    <p class='detaTime_title'><em v-if='list.required'>*</em>@{{list.title}}</p>
                    <p class='detaTime_title_a' v-if='list.subtitle'>@{{list.subtitle}}</p>
                    <label>
                        <select style="width: 80px">
                            <option>请选择</option>
                            <option>北京市</option>
                            <option>天津市</option>   
                            <option>河北省</option>
                            <option>山西省</option>
                        </select>
                        <select style="width: 80px;margin-right: 10px;margin-left: 10px">
                            <option>请选择</option>
                            <option>北京市</option>
                        </select>
                        <select style="width: 80px">
                            <option>请选择</option>
                            <option>东城区</option>
                            <option>西城区</option>
                            <option>朝阳区</option>
                        </select>
                    </label>
                </div>
                <!-- <separator v-if = "list.type == 'address'" :title="list.title" :subtitle="list.subtitle" :require="list.required"></separator> -->
                <!-- 图片 -->
                <upload v-if = "list.type == 'image'" :content="list"></upload>
                <!-- 分割线 -->
                <separator-line v-if = "list.type == 'line'" :content="list"></separator-line>
                <!-- 数字 -->
                <num v-if = "list.type == 'num'" :content="list"></num>
                <!-- 预约时段 -->
                <timebooking v-if = "list.type == 'appoint_time'" :content="list"></timebooking>
                <!-- 外观样式 -->
                <face-type v-if = "list.type == 'face_type'" :content="list"></face-type>
                <!-- 图片设置 -->
                <img-set v-if = "list.type == 'set_image'" :content="list" :imgurl="imgUrl"></img-set>
            </div>
            <!-- 左侧二维码 -->
      <div class="content-sidebar" v-if="codeShow">
            <div class="sidebar-section qrcode-info">
                <div class="section-detail">
                    <p class="text-center shop-detail">
                        <strong>手机扫码访问</strong></p>
                    <p class="text-center weixin-title">微信“扫一扫”分享到朋友圈</p>
                    <p class="text-center qr-code">
                        <img :src="codeUrl" alt="">
                    </p>
                </div>
            </div>
        </div>
        <!-- 左侧二维码 -->
        </div>
      
       
    </div>
</div>

<script type="text/javascript">
    var APP_HOST = "{{ config('app.url') }}"
    var APP_IMG_URL = "{{ imgUrl() }}"
    var APP_SOURCE_URL = "{{ config('app.source_url') }}"
    var CDN_IMG_URL = "{{config('app.cdn_img_url')}}";
    var wid = '{{ session("wid") }}'
</script>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/vue_component.js"></script>
<script>
    var data= {!! json_encode($data)  !!};
    console.log(data)
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/resesrch_preview.js"></script>
</body>
</html>
