@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!--base.css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/base.css"/>
    <!--页面的css样式-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/aboutCommon.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/contactUs.css">
@endsection
@section('content')
    <input id="source" type="hidden" value="{{ config('app.source_url') }}home/">
    <div class="top_bg">
        {{--<img src="{{ config('app.source_url') }}home/image/about_banner.png" alt="">--}}
        <h2>选择会搜云&nbsp;&nbsp;&nbsp;&nbsp;值得信赖</h2>
        <p>爱、感恩、责任、坚持、创新</p>
    </div>
    <!--内容导航-->
    <!-- <div class="content_nav">
        <ul>
            <li><a href="{{ config('app.url') }}home/index/about"><img src="{{ config('app.source_url') }}home/image/intro.png"/><div class="nav_name"><h5>了解会搜云</h5><p>全面了解会搜公司</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/growth"><img src="{{ config('app.source_url') }}home/image/history.png"/><div class="nav_name"><h5>发展历程</h5><p>会搜的一路走来</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/culture"><img src="{{ config('app.source_url') }}home/image/culture.png"/><div class="nav_name"><h5>企业文化</h5><p>爱与感恩的理念</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/recruit"><img src="{{ config('app.source_url') }}home/image/recruit.png"/><div class="nav_name"><h5>招贤纳士</h5><p>伯乐寻找千里马</p></div></a></li>
            <li class="have"><a href="{{ config('app.url') }}home/index/contactUs"><img src="{{ config('app.source_url') }}home/image/linkus_1.png"/><div class="nav_name"><h5>联系我们</h5><p>帮助您解答问题</p></div></a></li>
        </ul>
    </div> -->
    <!--主要内容-->
    <div class="main_part">
        <!--联系我们-->
        <div class="content" id="content_5">
        <img class="order_num" src="{{ config('app.source_url') }}home/image/01.png">
            <h2>会搜科技 联系我们</h2>
            <div class="linkUs">
                    <div class="aboutUs">
                       <h6>杭州会搜科技股份有限公司</h6> 
                       <div class="qq"><img src="{{ config('app.source_url') }}home/image/qq1.gif">1299112710</div>
                       <div class="tel"><img src="{{ config('app.source_url') }}home/image/tel1.gif">{{$CusSerInfo['phone']}}</div>
                       <div class="website"><img src="{{ config('app.source_url') }}home/image/web1.gif">www.huishou.cn</div>
                       <div class="email"><img src="{{ config('app.source_url') }}home/image/email.gif">kf@huisou.cn</div>
                       <div class="addr"><img src="{{ config('app.source_url') }}home/image/addr1.png">杭州市江干区九盛路9号东方电子商务园7幢5层</div>
                    </div>
                    <div class="consult_tel">
                        <div>咨询热线：</div>
                        <p>0571-87796692</p>
                    </div>
                    <div class="weixin_code">
                        <div class="code_left">
                            微信添加<br />
                            扫一扫
                        </div>
                        <img src="{{ config('app.source_url') }}home/image/footer_code.jpg">
                    </div>
                </ul>
            </div>
            <div class="mapDiv flex_star">
                <div id="mapShow"><!--插入地图--></div>
                <!-- <div id="linkUs">
                    <p class="title">CONTACT</p>
                    <P>客服：{{$CusSerInfo['name']}}
                        <a href="tencent://message/?uin=1299112710&amp;Menu=yes">
                            <img src="{{ config('app.source_url') }}home/image/snowIcon/QQ.png"/>QQ交谈
                        </a>
                    </P>
                    <p>电话：<span>{{$CusSerInfo['phone']}}</span></p>
                    <p>邮箱： <span>kf@huisou.cn</span></p>
                    <p>地址： <span>杭州市江干区九盛路9号东方电子商务园7幢5层</span></p>
                    <p>邮编：<span>310019</span></p>
                </div> -->
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script>
        $(function(){
            //地图
            Map("mapShow",15);       //默认地点；
        })
    </script>
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://api.map.baidu.com/api?v=2.0&ak=Gl9ARRgPlcASCW55a33dw5AE8URjrKRu"></script>
    <!--地图的方法-->
    <script src="{{ config('app.source_url') }}home/js/map_public.js" type="text/javascript" charset="utf-8"></script>
    <!-- 页面js -->
    <script src="{{ config('app.source_url') }}home/js/aboutCommon.js" type="text/javascript" charset="utf-8"></script>
@endsection