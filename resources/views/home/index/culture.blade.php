@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!--base.css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/base.css"/>
    <!--页面的css样式-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/aboutCommon.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/culture.css">
@endsection
@section('content')
    <input id="source" type="hidden" value="{{ config('app.source_url') }}home/">
    <div class="top_bg">
        {{--<img src="{{ config('app.source_url') }}home/image/about_banner.png" alt="">--}}
        <h2>选择会搜云&nbsp;&nbsp;&nbsp;&nbsp;值得信赖</h2>
        <p>爱、感恩、责任、坚持、创新</p>
    </div>
    <!-- <div class="top_swiper">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @forelse($bannerList['data'] as $banner)
                <div class="swiper-slide"><img src="{{ imgUrl() }}{{ $banner['img'] }}"/></div>
                @empty
                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/ab-banner0.jpg"/></div>
                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/ab-banner1.jpg"/></div>
                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/ab-banner2.jpg"/></div>
                <div class="swiper-slide"><img src="{{ config('app.source_url') }}home/image/ab-banner3.jpg"/></div>
                @endforelse
            </div> -->
            <!--切换按钮-->
            <!-- <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div> -->
            <!--分页器-->
            <!-- <div class="swiper-pagination"></div> -->
        <!-- </div>
    </div> --> 
    <!--内容导航-->
    <div class="content_nav">
        <ul>
            <li><a href="{{ config('app.url') }}home/index/about"><img src="{{ config('app.source_url') }}home/image/intro.png"/><div class="nav_name"><h5>了解会搜云</h5><p>全面了解会搜公司</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/growth"><img src="{{ config('app.source_url') }}home/image/history.png"/><div class="nav_name"><h5>发展历程</h5><p>会搜的一路走来</p></div></a></li>
            <li class="have"><a href="{{ config('app.url') }}home/index/culture"><img src="{{ config('app.source_url') }}home/image/culture_1.png"/><div class="nav_name"><h5>企业文化</h5><p>爱与感恩的理念</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/recruit"><img src="{{ config('app.source_url') }}home/image/recruit.png"/><div class="nav_name"><h5>招贤纳士</h5><p>伯乐寻找千里马</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/honor"><img src="{{ config('app.source_url') }}home/image/linkus.png"/><div class="nav_name"><h5>资质荣誉</h5><p>荣誉奖项及资质</p></div></a></li>
        </ul>
    </div>
    <!--主要内容-->
    <div class="main_part">
        <!--企业文化-->
        <div class="content" id="content_3">
            <div class="culture"> 
            <img class="order_num" src="{{ config('app.source_url') }}home/image/01.png">
                <h2>企业文化</h2>
                <div class="culture_content">
                    <!-- <p class="indent1" >爱、感恩、责任、坚持、创新 </p> -->
                    <p class="indent1">杭州会搜科技股份有限公司以“爱”、“感恩”、“责任”、“创新”、“坚持”作为企业核心文化和价值观，始终贯彻“心怀感恩、尽心负责、开拓创新、坚持不懈”的核心企业理念，营造出一个温馨、积极向上的会搜大家庭。</p>
                </div>
            </div>
            <div class="honor">
            <img class="order_num" src="{{ config('app.source_url') }}home/image/02-01.png">
                <h2>公司荣誉</h2>
                <div class="honor_content">
                    <p class="indent1">自公司成立以来，借力技术突破和创新发展，会搜科技及盈搜科技先后荣获二十多项著作权登记证书；</p>
                    <p class="indent1">2012年，荣获中华人民共和国信息产业部颁发的“软件企业认定证书”；同年，成功入选杭州市科技型初创企业“雏鹰计划”培育工程，深受省市领导重视与好评!</p>
                    <p class="indent1">2013年，公司取得浙江省通信管理局颁发的增值电信业务经营许可证书；同年，成功入选杭州市高新技术企业，并荣获杭州江干区经济园‘创新示范企业’称号！连续两年获得‘优秀成长企业’荣誉称号、杭州市信息服务业发展专项资金扶持企业！</p>
                    <p class="indent1">2016年，荣获江干区2016年度现代产业成长企业。</p>
                </div>
            </div>
            <div class="team_mien">
            <img class="order_num" src="{{ config('app.source_url') }}home/image/0 3.png">
                <h2>团队风采</h2>
                <div class="mien_content">
                    <p class="indent1">杭州会搜科技股份有限公司注重企业文化的建设，为了丰富公司职工业余生活,营造健康向上的企业文化氛围,公司定期组织具有团队建设意义的各项活动，包括健身运动、户外体育、野外拓展、K歌、聚餐、摄影、公益、电影等，全力打造一支青春活力的卓越团队！</p>
                </div>
                <div class="thear">
                    <ul>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture1.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture2.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture3.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture4.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture5.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture6.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture7.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture8.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture9.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture10.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture11.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/culture12.jpg">
                        </li>
                    </ul>
                    <!-- <img class="lazy"  data-original="{{ config('app.source_url') }}home/image/fc1.png"/>
                    <img class="lazy"  data-original="{{ config('app.source_url') }}home/image/fc2.png"/>
                    <img class="lazy"  data-original="{{ config('app.source_url') }}home/image/fc3.png"/>
                    <img class="lazy" data-original="{{ config('app.source_url') }}home/image/fc4.png"/> -->
                </div>
            </div>
            <div class="team_mien">
            <img class="order_num" src="{{ config('app.source_url') }}home/image/04.png">
                <h2>技术团队</h2>
                <!-- <div class="mien_content">
                    <p class="indent1">杭州会搜科技股份有限公司注重企业文化的建设，为了丰富公司职工业余生活,营造健康向上的企业文化氛围,公司定期组织具有团队建设意义的各项活动，包括健身运动、户外体育、野外拓展、K歌、聚餐、摄影、公益、电影等，全力打造一支青春活力的卓越团队！</p>
                </div> -->
                <div class="thear">
                    <ul>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/technology_01.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/technology_02.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/technology_03.jpg">
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/technology_04.jpg">
                        </li>
                    </ul>
                    <!-- <img class="lazy"  data-original="{{ config('app.source_url') }}home/image/fc1.png"/>
                    <img class="lazy"  data-original="{{ config('app.source_url') }}home/image/fc2.png"/>
                    <img class="lazy"  data-original="{{ config('app.source_url') }}home/image/fc3.png"/>
                    <img class="lazy" data-original="{{ config('app.source_url') }}home/image/fc4.png"/> -->
                </div>
            </div>
        </div>
       
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}home/js/aboutCommon.js" type="text/javascript" charset="utf-8"></script>
@endsection