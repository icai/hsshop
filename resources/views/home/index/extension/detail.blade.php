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
       {{--<img src="{{ config('app.source_url') }}home/image/xiaoxi.png" alt="">--}}
   </div>
   <div class="breadcrumb_nav">
       <div>
           <img src="{{ config('app.source_url') }}home/image/addr01.png">
           当前位置：<a href="/">首页</a>><a href="{{ config('app.url') }}home/index/extension"> 推广工具</a>><span class="active"> 消息提醒</span>
       </div>
   </div>
   <div class="main">
       <div class="main_content">
           <div class="app_intro">
               <div class="title">应用介绍</div>
               <div class="intro_cont">
                   <div class="intro_item">消息提醒功能可以通过微信公众号(请确保微信公众号已申请开通模板消息)，给买家或商家推送交易和物流相关的提醒消息，包括订单催付、发货、签收、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。</div>
                   <div class="pic_swiper">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide bottom_slide"><img src="{{ config('app.source_url') }}home/image/xxtxd.jpg" ></div>
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
                            <a href="{{ config('app.url') }}home/index/extension/detail/1">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon15.png"></span>
                                <h3>消息提醒</h3>
                                <div></div>
                                <p>向客户发布微信消息提醒</p>
                            </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/extension/detail/2">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon17.png"></span>
                                <h3>消息模板</h3>
                                <div></div>
                                <p>设置消息提醒模板</p>
                           </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/extension/detail/3">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon16.png"></span>
                                <h3>投票</h3>
                                <div></div>
                                <p>发起投票活动</p>
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
<script src="{{ config('app.source_url') }}home/js/extension.js" type="text/javascript" charset="utf-8"></script>
@endsection
