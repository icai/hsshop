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
       {{--<img src="{{ config('app.source_url') }}home/image/huiyuanka.png" alt="">--}}
   </div>
   <div class="breadcrumb_nav">
       <div>
           <img src="{{ config('app.source_url') }}home/image/addr01.png">
           当前位置：<a href="/">首页</a>><a href="{{ config('app.url') }}home/index/memberTicket"> 会员卡劵</a>><span class="active"> 会员卡</span>
       </div>
   </div>
   <div class="main">
       <div class="main_content">
           <div class="app_intro">
               <div class="title">应用介绍</div>
               <div class="intro_cont">
                   <div class="intro_item">通过在微信内植入会员卡，基于全国6亿微信用户，帮助企业建立集品牌推广、会员管理、营销活动、统计报表于一体的微信会员管理平台。清晰记录企业用户的消费行为并进行数据分析；还可根据用户特征进行精细分类，从而实现各种模式的精准营销。
</div>
                    <div class="pic_swiper">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide bottom_slide"><img src="{{ config('app.source_url') }}home/image/huiyuankad.jpg" ></div>
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
                            <a href="{{ config('app.url') }}home/index/memberTicket/detail/1">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon12.png"></span>
                                <h3>会员卡</h3>
                                <div></div>
                                <p>设置并给客户发放会员卡</p>
                            </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/memberTicket/detail/2">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon13.png"></span>
                                <h3>积分</h3>
                                <div></div>
                                <p>完善积分奖励消耗制度</p>
                           </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/memberTicket/detail/3">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon14.png"></span>
                                <h3>充值</h3>
                                <div></div>
                                <p>开通会员充值功能</p>
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
<script src="{{ config('app.source_url') }}home/js/memberTicket.js" type="text/javascript" charset="utf-8"></script>
@endsection
