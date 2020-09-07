@extends('home.base.head')
@section('head.css')	
    <!--swiper的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/> 
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!-- 营销应用详情css公共样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/app_detail.css"/>
@endsection
@section('content')
    <div class="wraper banner">
       {{--<img src="{{ config('app.source_url') }}home/image/youhuijuan.png" alt="">--}}
   </div>
   <div class="breadcrumb_nav">
       <div>
           <img src="{{ config('app.source_url') }}home/image/addr01.png">
           当前位置：<a href="/">首页</a>><a href="{{ config('app.url') }}home/index/salesDiscount"> 促销折扣</a>><span class="active"> 优惠券</span>
       </div>
   </div>
   <div class="main">
       <div class="main_content">
           <div class="app_intro">
               <div class="title">应用介绍</div>
               <div class="intro_cont">
                   <div class="intro_item">优惠券是商家吸引和回馈客户一种很有效的营销推广工具，设置便捷，操作简单，效果显著</div>
                   <div class="pic_swiper">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide bottom_slide"><img src="{{ config('app.source_url') }}home/image/youhuijuand.jpg" ></div>
                            </div>
                        </div>
                        <!--切换按钮-->
                        <div class="swiper-button-prev swiper-button-white" id="swiper-button-prev"></div>
                        <div class="swiper-button-next swiper-button-white" id="swiper-button-next"></div>
                    </div>
               </div>
           </div>
           <div class="relevant_app">
               <div class="title">相关应用</div>
               <div class="relevant_cont">
                   <ul>
                   <li>
                           <a href="{{ config('app.url') }}home/index/salesDiscount/detail/1">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon3.png"></span>
                                <h3>优惠券</h3>
                                <div></div>
                                <p>向客户发放店铺优惠券</p>
                            </a>
                       </li>
                       <li>
                           <a href="{{ config('app.url') }}home/index/salesDiscount/detail/2">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon4.png"></span>
                                <h3>秒杀</h3>
                                <div></div>
                           <p>快速抢购引导客户更多消费</p>
                           </a>
                       </li>
                   </ul>
               </div>
           </div>
           
       </div>
   </div>
@endsection
@section('foot.js')
<script>
    var imgUrl = "{{ config('app.source_url') }}"
</script>
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/salesDiscount.js" type="text/javascript" charset="utf-8"></script>
@endsection
