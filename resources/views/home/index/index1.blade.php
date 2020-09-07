@extends('home.base.head')
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/shouye.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/swiper.min.css"/> 
@endsection
@section('content')
    @include('home.base.slider')
    <div class="main_content">
        <!--分页一-->
        <!-- <div class="shou-fir">
            <div class="shou-fimg1"></div>
            <div class="shou-fimg2">
                <img width="167" height="167" class="lazy" onclick="window.location.href='/home/index/customization'" data-original="{{ config('app.source_url') }}home/image/shou4.png"/>
                <img width="167" height="167" class="lazy" onclick="window.location.href='/home/index/microshop'" data-original="{{ config('app.source_url') }}home/image/shou1.png"/>
                <img width="167" height="167" class="lazy" onclick="window.location.href='/home/index/distribution'" data-original="{{ config('app.source_url') }}home/image/shou2.png"/>
                <img width="167" height="167" class="lazy" onclick="window.location.href='/home/index/applet'" data-original="{{ config('app.source_url') }}home/image/shou3.png"/>
                <img width="167" height="167" class="lazy" onclick="window.location.href='/home/index/microMarketing'" data-original="{{ config('app.source_url') }}home/image/shou5.png"/>
            </div>
        </div>
        <!--分页四-->
        <!-- <div class="shou-foubox">
            <div class="shou-fou">
                <div class="shou-fou1">
                    <div class="shou-oul">
                        <p class="shou-op1">APP定制</p>
                        <p class="shou-op2">APP应用制定与服务专家 提供APP的专业策划、研发、推广、运营、售后服务等一站式APP定制外包服务</p>
                        <p class="shou-op3">每个行业都该拥有一款APP，会搜云，把您的idea 变成现实</p>
                        <p class="shou-op4" onclick="window.location.href='/home/index/customization'">了解更多 >></p>
                        <p class="shou-op5"  onclick="window.location.href='/home/index/reserve?type=2'" >马上定制</p>
                    </div>
                </div>
                <div class="shou-oimg">
               
                        <img width="258" height="519" class="shou-og1 lazy" data-original="{{ config('app.source_url') }}home/image/shou-img1.png"/>
                        <img width="250" height="250" class="shou-og2 lazy" data-original="{{ config('app.source_url') }}home/image/shou-img2.png"/>
                        <img width="295" height="560" class="shou-og3 lazy" data-original="{{ config('app.source_url') }}home/image/shou-img3.png"/>
             
                </div>
            </div>
        </div> -->
        <!--分页二-->
       <!--  <div class="shou-sec">
            <div class="shou-sec1">
                <div class="shou-sul">
                    <p class="shou-sl1">微信商城</p>
                    <p class="shou-sl2">一站式服务线下实体行业/线上传统电商微信开店</p>
                    <p class="shou-sl3">微信生意，用会搜云</p>
                    <p class="shou-sl4" onclick="window.location.href='/home/index/microshop'">了解更多 >></p>
                    <div class="shou-ssul">
                        <p class="shou-ssl1" onclick="window.location.href='/auth/register'" >免费试用</p>
                        <p class="shou-ssl2">APP下载</p>
                    </div>
                </div>
                <div class="shou-simg">
                    <img class="lazy" width="423" height="440" data-original="{{ config('app.source_url') }}home/image/shou-img4.png"/>
                </div>
            </div>
        </div> -->
        <!--分页三-->
       <!--  <div class="shou-tir">
            <div class="shou-tir1">
                <div class="shou-timg">
                    <img class="lazy" width="403" height="416" data-original="{{ config('app.source_url') }}home/image/shou-img5.png"/>
                </div>
                <div class="shou-tul">
                    <p class="shou-tp1">分销系统</p>
                    <p class="shou-tp2">新时代，新分销</p>
                    <p class="shou-tp3">一键开通，人人都是您的销售员，财富口碑</p>
                    <p class="shou-tp4">一起带来，拥抱变化不违规</p>
                    <p class="shou-tp5" onclick="window.location.href='/home/index/distribution'">了解更多 >></p>
                    <div class="shou-ttul">
                        <p class="shou-ttp1" onclick="window.location.href='/home/index/reserve?type=1'">立即订购</p>
                        <p class="shou-ttp2">APP分销</p>
                    </div>
                </div>
            </div>
        </div> -->
        <!--分页五-->
       <!--  <div class="shou-fiv">
            <div class="shou-fiv1">
                <div class="shou-vul">
                    <p class="shou-vp1">微信小程序，微信潮流新方向</p>
                    <p class="shou-vp2">即刻上线微信小程序，抢占巨大流量红利</p>
                    <p class="shou-vp3" onclick="window.location.href='/home/index/applet'">了解更多 >></p>
                    <p class="shou-vp4" onclick="window.location.href='/home/index/reserve?type=3'">开启预约</p>
                </div>
                <div class="shou-vimg">
                    <img class="lazy" width="892" height="595" data-original="{{ config('app.source_url') }}home/image/shou-img6.png"/>
                </div>
            </div>
        </div> -->
        <!--分页六-->
        <!-- <div class="shou-six">
            <div class="shou-xul">
                <p class="shou-xp1">微营销总裁班</p>
                <p class="shou-xp2">明星导师，真材实料，学习即实战助你快速建立一套强大的</p>
                <p class="shou-xp3">微营销系统，为您提供完美的行业解决方案</p>
                <p class="shou-xp4" onclick="window.location.href='/home/index/microMarketing'">了解更多 >> </p>
                <p class="shou-xp5" onclick="window.location.href='/home/index/reserve?type=4'">我要报名</p>
            </div>

            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">           --> 
                <!-- Wrapper for slides -->
               <!--  <div class="top_swiper">
			        <div class="swiper-container">
			            <div class="swiper-wrapper">
			                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/shouyelunbo.jpg"/></div>
			                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/shouyelunbo2.jpg"/></div>
			                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/shouyelunbo4.jpg"/></div>
			            </div> -->
			            <!--切换按钮-->
<!-- 			            <div class="swiper-button-prev"></div>
			            <div class="swiper-button-next"></div> -->
			            <!--分页器-->
	<!-- 		            <div class="swiper-pagination"></div> -->
		<!-- 	        </div>
   				</div>
	        </div> -->
	    </div> -->
	    
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}home/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/information.js" type="text/javascript" charset="utf-8"></script>
@endsection