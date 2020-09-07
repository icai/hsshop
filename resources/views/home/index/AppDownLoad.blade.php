@extends('home.base.head')
@section('head.css')
    <!--swiper的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <!--页面的css样式-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/appDownload.css">

@endsection
@section('content')
   <div>
       <div class="banner">
           <div class="download-btn-box">
               <div class="download-item">
                    <div class="download-btn">
                        <i class="down-icon icon1"></i>
                        <span>App Store</span>
                    </div>
               </div>
               <div class="download-item">
                    <div class="download-btn">
                        <i class="down-icon icon2"></i>
                        <span>Android</span>
                    </div>
               </div>
               <div class="download-item J_download">
                    <div class="download-btn down-link">
                        <i class="down-icon icon3"></i>
                        <span>二维码下载</span>
                    </div>
                    <div class="er-code-box">
                        <img src="{{ imgUrl() }}{{ $qrcodeUrl }}" width="200" height="200">
                    </div>
               </div>
           </div>
       </div>
       <div class="tool">
           <h2 class="year">2018</h2>
           <p class="tool-desc">好用的小程序微商城开店工具</p>
           <div class="banner-slider">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="{{ config('app.source_url') }}home/image/down-banner1.png" alt="会搜云"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ config('app.source_url') }}home/image/down-banner2.png" alt="会搜云"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ config('app.source_url') }}home/image/down-banner3.png" alt="会搜云"/>
                        </div>
                    </div>
                </div>
           </div>
       </div>
       <div class="wraper">
           <div class="g-box service-box">
                <p class="service-tips">功能多样 一站式服务</p>
                <div class="icon-box">
                    <div class="icon-item">
                        <img src="{{ config('app.source_url') }}home/image/service-icon1.png" width="120" height="120">
                        <p class="service-t1">商品管理</p>
                        <p class="service-t2">商品组合、定价方法</p>
                    </div>
                    <div class="icon-item">
                        <img src="{{ config('app.source_url') }}home/image/service-icon2.png" width="120" height="120">
                        <p class="service-t1">订单管理</p>
                        <p class="service-t2">推动经济效益和客户满意度</p>
                    </div>
                    <div class="icon-item">
                        <img src="{{ config('app.source_url') }}home/image/service-icon3.png" width="120" height="120">
                        <p class="service-t1">数据统计</p>
                        <p class="service-t2">精准快速的查找与分类</p>
                    </div>
                    <div class="icon-item">
                        <img src="{{ config('app.source_url') }}home/image/service-icon4.png" width="120" height="120">
                        <p class="service-t1">营销工具</p>
                        <p class="service-t2">多样化的促销活动</p>
                    </div>
                </div>
           </div>
       </div>
       <div class="wraper footer-wraper">
           <div class="footer-item">
               <img src="{{ imgUrl() }}{{ $qrcodeUrl }}" width="120" height="120">
           </div>
           <div class="footer-item">
               <img src="{{ config('app.source_url') }}home/image/android-lg.png" width="120" height="120">
           </div>
           <div class="footer-item">
               <img src="{{ config('app.source_url') }}home/image/ios-lg.png" width="120" height="120">
           </div>
       </div>
   </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/appDownload.js"></script>
@endsection