@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!--base.css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/base.css"/>
    <!--页面的css样式-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/aboutCommon.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/growth.css">
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
            <li class="have"><a href="{{ config('app.url') }}home/index/growth"><img src="{{ config('app.source_url') }}home/image/history_1.png"/><div class="nav_name"><h5>发展历程</h5><p>会搜的一路走来</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/culture"><img src="{{ config('app.source_url') }}home/image/culture.png"/><div class="nav_name"><h5>企业文化</h5><p>爱与感恩的理念</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/recruit"><img src="{{ config('app.source_url') }}home/image/recruit.png"/><div class="nav_name"><h5>招贤纳士</h5><p>伯乐寻找千里马</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/honor"><img src="{{ config('app.source_url') }}home/image/linkus.png"/><div class="nav_name"><h5>资质荣誉</h5><p>荣誉奖项及资质</p></div></a></li>
        </ul>
    </div>
    <!--主要内容-->
    <div class="main_part">
        <!--发展历程-->
        <div class="content" id="content_2">
            <div class="grow_target">
                <img class="order_num" src="{{ config('app.source_url') }}home/image/01.png">
                <h2>会搜科技 发展目标</h2>
                <p>会搜科技股份将继续坚持以“客户第一、服务为本”的经营理念，以技术创新和用户体验为支撑的发展源泉。通过提升企业管理效能，不断吸纳技术水平高、事业心强的开发人员，用强大的技术实力打造了保证公司健康发展的坚实基础。</p>
                <p>公司的发展目标是依托现有的平台及资源，整合产业价值链，平稳布局于移动互联网，并依靠严谨深厚的技术积累，开展更多的业务领域，成为一家立足软件开发，跨领域运作、多元化发展、在行业处于领先地位的软件开发公司。通过为各行业用户提供优质高效的专业服务，不断扩大品牌影响力，最终实现服务人民、造福社会的宏伟目标！</p>
                <img class="lazy" width="670" height="420" data-original="{{ config('app.source_url') }}home/image/fazhan.jpg"/>
            </div>
            <div class="core_advantage">
                <div class="advantage_content">
                <img class="order_num" src="{{ config('app.source_url') }}home/image/002.png">
                    <h2>核心优势</h2>
                    <div class="strengths ">
                        <div class="left relative">
                            <div class="graphic D1 absolute">
                                <h3>2010年</h3>
                                <p>2010年11月，杭州会搜科技股份有限公司成立。</p>                    
                            </div>
                            <div class="graphic D2 absolute">
                                <h3>2013年</h3>
                                <p>3月，杭州会搜科技股份推出APP开发服务，风靡江浙；</p>
                                <p>7月，基于微信而研发的阿凡提微商系统成功上线，帮助大量客户推广公司的产品并提升服务质量，让客户真正地受益匪浅。</p>
                                <p>获得‘优秀成长企业’荣誉称号，成为杭州市信息服务业发展专项资金扶持企业。</p>
                                <img width="192" height="144" src="{{ config('app.source_url') }}home/image/2013.png"/>
                            </div>
                            <div class="graphic D3 absolute">
                                <h3>2015年</h3>
                                <p>会搜喜获“杭州市最具创新活力微小企业”荣誉称号，为促进全市中小企业转型升级、创新发展作出了贡献。</p>
                                <img width="200" height="200" src="{{ config('app.source_url') }}home/image/2015.png"/>
                            </div>
                            <div class="graphic D7 absolute">
                                <h3>2017年</h3>
                                <p>会搜云微商城系统上线，提供从PC端到移动端再到微信端的多端合一的线上线下解决方案。</p>
                                <p>2017年07月，获得来自苏州高新创业投资集团融联管理有限公司2000万元A轮股权融资。</p>
                            </div>
                            <div class="graphic D9 absolute">
                                <h3>2019年</h3>
                                <p>会搜云新零售系统发布，打造微信智能销售系统，全面提升企业品牌输出。</p>
                                <img width="200" height="200" src="{{ config('app.source_url') }}home/image/2019.jpg"/>
                            </div>
                        </div>
                        <div class="center">
                            <img src="{{ config('app.source_url') }}home/image/555511.png"/>
                            <div id="noEnd">未完待续</div>
                        </div>
                        <div class="right relative">
                            <div class="graphic D4 absolute">
                                <h3>2012年</h3>
                                <p>杭州会搜科技股份推出会搜网综合性平台，自主研发的来福网全面上线，迎来了用户市场的广泛好评。</p>
                                <p>会搜荣获中华人民共和国信息产业部颁发的“软件企业认定证书”；同年，成功入选杭州市科技型初创企业“雏鹰计划”培育工程，深受省市领导重视与好评!</p>
                                <img width="192" height="192" src="{{ config('app.source_url') }}home/image/2012l.png"/>
                                <img width="192" height="192" src="{{ config('app.source_url') }}home/image/2012r.png"/>
                            </div>
                            <div class="graphic D5 absolute">
                                <h3>2014年</h3>
                                <p>继2013之后，又一次获得‘优秀成长企业’荣誉称号、成为杭州市信息服务业发展专项资金扶持企业。</p>
                                <img width="200px" height="200px" class="im2014 lazy" src="{{ config('app.source_url') }}home/image/2014.png""/>
                            </div>
                            <div class="graphic D6 absolute">
                                <h3>2016年</h3>
                                <p>5月会搜登录新三板挂牌（股票代码：837521）</p>
                                <img src="{{ config('app.source_url') }}home/image/2016.png""/>
                            </div>
                            <div class="graphic D8 absolute">
                                <h3>2018年</h3>
                                <p>荣获浙江省“高新技术企业”证书。</p>
                                <img src="{{ config('app.source_url') }}home/image/2018.png""/>
                            </div>                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script>
        $(function(){
            //发展历程内容的图片数量判断显示
            $(".strengths .graphic").each(function(){
                var imgs_length = $(this).children("img").length;
                if (imgs_length<=1) {
                    $(this).children("img").css({"width": '61.8%', "display": "inline-block", "margin": "0 auto"})
                }else{
                    var width = 100/imgs_length - 2;
                    $(this).children("img").css({"width": width+'%', "display": "inline-block"})
                }
            });
        })
        
    </script>
    <script src="{{ config('app.source_url') }}home/js/aboutCommon.js" type="text/javascript" charset="utf-8"></script>
@endsection