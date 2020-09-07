@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!-- 营销应用css公共样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/appCommon.css"/>
    <!-- 页面css样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/salesTools.css"/>
@endsection
@section('content')
    <div class="wraper app-banner sales_bg">
        {{--<img src="{{ config('app.source_url') }}home/image/app-banner.png" >--}}
    </div>
   <div class="breadcrumb_nav">
       <div>
           <img src="{{ config('app.source_url') }}home/image/addr01.png">
           当前位置：<a href="/">首页</a>><span> 促销工具</span>
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
                       <li><a href="{{ config('app.url') }}home/index/salesDiscount"><span class="icon sideBar_icon2"></span>促销折扣</a></li>
                       <li class="active"><a href="{{ config('app.url') }}home/index/salesTools"><span class="icon sideBar_icon3"></span>促销工具</a></li>
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
                       <li>
                            <a href="{{ config('app.url') }}home/index/salesTools/detail/4">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon8.png"></span>
                                <h3>幸运大转盘</h3>
                                <div></div>
                                <p>常见的转盘式抽奖玩法</p>
                           </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/salesTools/detail/5">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon9.png"></span>
                                <h3>微社区</h3>
                                <div></div>
                                <p>打造人气移动社区，增加客户流量</p>
                           </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/salesTools/detail/6">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon10.png"></span>
                                <h3>砸金蛋</h3>
                                <div></div>
                                <p>好蛋砸出来，礼品不间断</p>
                           </a>
                       </li>
                       <li>
                            <a href="{{ config('app.url') }}home/index/salesTools/detail/7">
                                <span class="type"><img src="{{ config('app.source_url') }}home/image/app-icon11.png"></span>
                                <h3>签到</h3>
                                <div></div>
                                <p>每日签到领取积分或奖励</p>
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
