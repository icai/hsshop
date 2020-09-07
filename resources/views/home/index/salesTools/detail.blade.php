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
       {{--<img src="{{ config('app.source_url') }}home/image/xianglijian.png" alt="">--}}
   </div>
   <div class="breadcrumb_nav">
       <div>
           <img src="{{ config('app.source_url') }}home/image/addr01.png">
           当前位置：<a href="/">首页</a>><a href="{{ config('app.url') }}home/index/salesTools"> 促销工具</a>> <span class="active">享立减</span>
       </div>
   </div>
   <div class="main">
       <div class="main_content">
           <div class="app_intro">
               <div class="title">应用介绍</div>
               <div class="intro_cont">
                   <div class="intro_item">享立减是一种一键快捷分享，让好友帮你减价的常用营销推广活动</div>
                   <div class="pic_swiper">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide bottom_slide"><img src="{{ config('app.source_url') }}home/image/xianglijiand.jpg" ></div>
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
                            <a href="{{ config('app.url') }}home/index/salesTools/detail/1">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon5.png"></span>
                                <h3>享立减</h3>
                                <div></div>
                                <p>用户点击分享链接可减钱的新玩法</p>
                            </a>
                       </li>
                       {{--<li>--}}
                            {{--<a href="{{ config('app.url') }}home/index/salesTools/detail/2">--}}
                                {{--<span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon6.png"></span>--}}
                                {{--<h3>集赞</h3>--}}
                                {{--<div></div>--}}
                                {{--<p>邀请好友点赞可享受优惠的玩法</p>--}}
                           {{--</a>--}}
                       {{--</li>--}}
                       <li>
                            <a href="{{ config('app.url') }}home/index/salesTools/detail/3">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon7.png"></span>
                                <h3>多人拼团</h3>
                                <div></div>
                                <p>引导客户邀请朋友一起拼团购买</p>
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
    <script src="{{ config('app.source_url') }}home/js/salesTools.js" type="text/javascript" charset="utf-8"></script>  
@endsection
