@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <!--base.css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/base.css"/>
    <!--页面的css样式-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/aboutCommon.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/honor.css">
@endsection
@section('content')
    <input id="source" type="hidden" value="{{ config('app.source_url') }}home/">
    <div class="top_bg">
        {{--<img src="{{ config('app.source_url') }}home/image/about_banner.png" alt="">--}}
        <h2>选择会搜云&nbsp;&nbsp;&nbsp;&nbsp;值得信赖</h2>
        <p>爱、感恩、责任、坚持、创新</p>
    </div>
    <!--内容导航-->
    <div class="content_nav">
        <ul>
            <li><a href="{{ config('app.url') }}home/index/about"><img src="{{ config('app.source_url') }}home/image/intro.png"/><div class="nav_name"><h5>了解会搜云</h5><p>全面了解会搜公司</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/growth"><img src="{{ config('app.source_url') }}home/image/history.png"/><div class="nav_name"><h5>发展历程</h5><p>会搜的一路走来</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/culture"><img src="{{ config('app.source_url') }}home/image/culture.png"/><div class="nav_name"><h5>企业文化</h5><p>爱与感恩的理念</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/recruit"><img src="{{ config('app.source_url') }}home/image/recruit.png"/><div class="nav_name"><h5>招贤纳士</h5><p>伯乐寻找千里马</p></div></a></li>
            <li class="have"><a href="{{ config('app.url') }}home/index/honor"><img src="{{ config('app.source_url') }}home/image/linkus_1.png"/><div class="nav_name"><h5>资质荣誉</h5><p>荣誉奖项及资质</p></div></a></li>
        </ul>
    </div>
    <!--主要内容-->
    <div class="main_part">
        <!--发展历程-->
        <div class="content" id="content_2">
            <div class="grow_target">
                <img class="order_num" src="{{ config('app.source_url') }}home/image/01.png">
                <h2>会搜股份  资质荣誉</h2>
            </div>
            <div class="honor">
                <ul>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/cert-1.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/cert-2.png" alt="">
                    </li>
                </ul>
                <ul>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-1.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-2.png" alt="">
                    </li>
                </ul>
            </div>
            <div class="honor-1">
                <ul>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-3.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-4.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-5.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-6.png" alt="">
                    </li>
                </ul>
                <ul>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-7.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-8.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-9.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-10.png" alt="">
                    </li>
                </ul>
            </div>
            <div class="honor-1">
                <ul>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-11.png" alt="">
                        <img style="margin-top: 14px" src="{{ config('app.source_url') }}home/image/honor/honor-12.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-13.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-14.png" alt="">
                    </li>
                    <li>
                        <img src="{{ config('app.source_url') }}home/image/honor/honor-15.jpg" alt="">
                    </li>
                </ul>
            </div>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-1.jpg" alt="">
                        </div>
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-2.jpg" alt="">
                        </div>
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-3.jpg" alt="">
                        </div>
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-4.jpg" alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-5.jpg" alt="">
                        </div>
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-6.jpg" alt="">
                        </div>
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-7.jpg" alt="">
                        </div>
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-8.jpg" alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="img">
                            <img src="{{ config('app.source_url') }}home/image/honor/bottom-honor-9.jpg" alt="">
                        </div>
                    </div>
                </div>
                <!-- 如果需要分页器 -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <!-- <div class="swiper-pagination"></div>  -->
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/aboutCommon.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(function () {
            var mySwiper = new Swiper('.swiper-container', {
                paginationClickable: true,
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev',
                parallax: true,
                speed: 600,
            })
        })
    </script>
@endsection