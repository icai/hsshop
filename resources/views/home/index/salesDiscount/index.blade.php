@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!-- 营销应用css公共样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/appCommon.css"/>
    <!-- 页面css样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/salesDiscount.css"/>
@endsection
@section('content')
    <div class="wraper app-banner">
        {{--<img src="{{ config('app.source_url') }}home/image/app-banner.png" >--}}
    </div>
   <div class="breadcrumb_nav">
       <div>
           <img src="{{ config('app.source_url') }}home/image/addr01.png">
           当前位置：<a href="/">首页</a>><span> 促销折扣</span>
       </div>
   </div>
   <div class="main">
       <div class="main_content">
           <div class="sideBar">
               <div class="sideBar_title">应用推荐</div>
               <div class="sideBar_nav">
                   <ul>
                       <li><a href="{{ config('app.url') }}home/index/appRecommen"><span class="icon sideBar_icon"></span>应用推荐</a></li>
                       <li><a href="{{ config('app.url') }}home/index/manageChannel"><span class="icon sideBar_icon1"></span>经营渠道</a></li>
                       <li class="active"><a href="{{ config('app.url') }}home/index/salesDiscount"><span class="icon sideBar_icon2"></span>促销折扣</a></li>
                       <li><a href="{{ config('app.url') }}home/index/salesTools"><span class="icon sideBar_icon3"></span>促销工具</a></li>
                       <li><a href="{{ config('app.url') }}home/index/memberTicket"><span class="icon sideBar_icon4"></span>会员卡劵</a></li>
                       <li><a href="{{ config('app.url') }}home/index/extension"><span class="icon sideBar_icon5"></span>推广工具</a></li>
                   </ul>
               </div>
           </div>
           <div class="right_content">
              <!-- <div class="right_title">应用推荐</div> -->
              <div class="cont">
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
    <script src="{{ config('app.source_url') }}home/js/appCommon.js" type="text/javascript" charset="utf-8"></script>
@endsection
