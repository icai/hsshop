<!DOCTYPE html>
<html>

<head>
    @if($title == '会搜云学院')
    <title>@if(isset($detail['title'])){{$detail['title']}} @else {{$title}} @endif</title>
    @else
    <title>{{$title}}</title>
    @endif
    @if($title == '会搜云-会搜云新零售系统|人工智能名片制作|电子名片在线制作|微信获客神器|微信商城分销系统')
    <meta name="description" content="会搜股份荣誉出品，会搜云专注做APP定制全套解决方案，将原生App+H5网页版+微信小程序一并打通。会搜云新零售系统、人工智能名片制作、电子名片在线制作、微信获客神器、精准获客系统、智能广告获客、微信商城分销系统、微信小程序ai智能名片、小程序商城如何运营、人工智能名片哪个公司好。">
    <meta name="keywords" content="会搜云新零售系统,智能名片制作,电子名片在线制作,人工智能名片,微信获客神器,精准获客系统,微信商城分销系统,微信小程序ai智能名片,智能广告获客,小程序商城如何运营,人工智能名片哪个公司好">
    @elseif($title == '微信商城哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技')
    <meta name="description" content="会搜股份荣誉出品，会搜云专注做微信商城全套解决方案，提供微信商城哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <meta name="keywords" content="微信商城">
    @elseif($title == '分销系统哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技')
    <meta name="description" content="会搜股份荣誉出品，会搜云专注做分销系统全套解决方案，提供分销系统哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <meta name="keywords" content="分销系统">
    @elseif($title == '微信小程序哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技')
    <meta name="description" content="会搜股份荣誉出品，会搜云专注做微信小程序全套解决方案，提供微信小程序哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <meta name="keywords" content="微信小程序">
    @elseif($title == '微营销总裁班哪个公司好|如何制作|效果怎样|好用吗-会搜云-会搜科技')
    <meta name="description" content="会搜股份荣誉出品，会搜云专注做微营销总裁班培训，提供微营销总裁班哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <meta name="keywords" content="微营销总裁班">
    @elseif($title == '行业案例')
    <meta name="keywords" content="餐饮APP开发,物流APP开发,医疗APP开发,电商APP开发,教育APP开发">
    <meta name="description" content="会搜云为顾客提供最全最专业的电商app开发解决方案，商城APP开发解决方案，餐饮APP开发解决方案，社区APP开发解决方案，教育APP开发解决方案等，国内知名行业类APP开发品牌，欢迎咨询：0571-87796692">
    @elseif($title == '会搜云学院')
    <meta name="keywords" content="@if(isset($detail['title'])){{$detail['keywords']}} @else 手机APP开发,小程序开发公司,分销系统开发,行业APP定制开发,网络营销培训  @endif">
    <meta name="description" content="@if(isset($detail['title'])){{$detail['meta']}}@else 会搜云是杭州会搜股份有限公司旗下知名品牌，十年专注手机APP开发，定制APP制作，APP外包服务，已为2300多家企业定制APP服务，阿凡提系统教你运营一站式服务，资讯热线：0571-87796692 @endif">
    @endif
    <meta name="baidu-site-verification" content="HZQQLAw7mF" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="HandheldFriendly" content="true">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet">
    <link rel="icon" href="{{ config('app.source_url') }}home/image/icon_logo.png" type="image/png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/reset.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/base.css">
    @yield('head.css')
</head>

<body>
    <div class="nav wraper">
        <div class="nav_content-box">
            <a class="nav_left" href="/">
                <i class="logo-icon"></i>
            </a>
            @if(empty(session('userInfo')))
            <div class="login">
                <a class="btn-login" href="/auth/login">登录</a>
                <!--            <a class="btn-register_" href="/auth/register">注册</a> -->
            </div>
            @else
            <div class="login">
                <a class="user" href="/merchants/team">{{session('userInfo')['name']}}</a>
                <a class="exit" href="{{ URL('/auth/loginout') }}">退出</a>
            </div>
            @endif
            <div class="nav_center">
                <ul class="nav-menu-list">
                    <li class="chil-li @if(!empty($slidebar) && $slidebar == 'home') chil-li-active @endif">
                        <a href="/">
                            首页
                            <span class="active-bar"></span>
                        </a>
                    </li>
                    <li class="chil-li J_service @if(!empty($slidebar) && $slidebar == 'service') chil-li-active @endif" style="position: relative"><a href="/home/index/productServiec">产品服务<span class="active-bar"></span></a>
                        <ul class="second_list">
                            <li><a href="https://ai.huisou.cn">会搜云新零售系统</a></li>
                            <li><a href="/home/index/customization">APP定制</a></li>
                            <li><a href="/home/index/applet">微信小程序</a></li>
                            <li><a href="/home/index/microshop">微信商城</a></li>
                            <li><a href="/home/index/microMarketing">微营销总裁班</a></li>
                            <!-- <li><a href="/home/index/distribution" >分销系统</a></li> -->
                            <!-- <li><a href="/home/index/AppDownLoad" >会搜云商家版APP</a></li> -->
                        </ul>
                    </li>
                    <li class="chil-li @if(!empty($slidebar) && $slidebar == 'marketing') chil-li-active @endif"><a href="/home/index/appRecommen">营销应用<span class="active-bar"></span></a></li>
                    <li class="chil-li @if(!empty($slidebar) && $slidebar == 'shop') chil-li-active @endif"><a href="/home/index/1/shop">案例展示<span class="active-bar"></span></a></li>
                    <li class="chil-li @if(!empty($slidebar) && $slidebar == 'news') chil-li-active @endif"><a href="/home/index/news">会搜云资讯<span class="active-bar"></span></a></li>
                    <li class="chil-li @if(!empty($slidebar) && $slidebar == 'helps') chil-li-active @endif"><a href="/home/index/helps">帮助中心<span class="active-bar"></span></a></li>
                    <li class="chil-li @if(!empty($slidebar) && $slidebar == 'about') chil-li-active @endif"><a href="/home/index/about">关于我们<span class="active-bar"></span></a></li>
                </ul>
                <div class="sliderBar J_sliderBar"></div>
            </div>
        </div>
    </div>
    <!--主要内容区域-->
    @yield('content')
    <!--底部区域-->
    <div class="footer wraper">
        <div class="footer-content clearfix">
            <div class="footer-left">
                <img class="kf-img" width="46" height="50" src="{{ config('app.source_url') }}home/image/kf-icon.png" />
                <div class="z-reveal">
                    <p class="reveal-tips">咨询热线</p>
                    <p class="footer-phone">0571-87796692</p>
                    <p class="footer-phone">{{$CusSerInfo['phone']}}</p>
                    <div class="qq-link">在线QQ咨询</div>
                </div>
            </div>
            <div class="footer-menu">
                <ul class="mune-box">
                    <li class="menu-item">产品服务</li>
                    <li class="menu-item"><a href="https://ai.huisou.cn/" title="会搜云新零售系统">会搜云新零售系统</a></li>
                    <li class="menu-item"><a href="/home/index/customization" title="APP定制">APP定制</a></li>
                    <li class="menu-item"><a href="/home/index/applet" title="微信小程序">微信小程序</a></li>
                    <li class="menu-item"><a href="/home/index/microshop" title="微信商城">微商城系统</a></li>
                    <li class="menu-item"><a href="/home/index/distribution" title="分销系统">分销系统</a></li>
                    <li class="menu-item"><a href="/home/index/microMarketing" title="微营销总裁班">微营销总裁班</a></li>
                </ul>
                <ul class="mune-box">
                    <li class="menu-item">案例展示</li>
                    <li class="menu-item"><a href="/home/index/1/shop" title="会搜云新零售系统">会搜云新零售系统</a></li>
                    <li class="menu-item"><a href="/home/index/2/shop" title="APP定制">APP定制</a></li>
                    <li class="menu-item"><a href="/home/index/3/shop" title="微信小程序">微信小程序</a></li>
                    <li class="menu-item"><a href="/home/index/4/shop" title="微信商城">微信商城</a></li>
                </ul>
                <ul class="mune-box">
                    <li class="menu-item">帮助中心</li>
                    @foreach($publicData['helpsType'] as $val)
                    <li class="menu-item"><a href="/home/index/helpList?Pid={{ $val['id'] }}" title="{{ $val['name'] }}">{{ $val['name'] }}</a></li>
                    @endforeach
                    <!-- <li class="menu-item"><a href="#" target="_blank" title="商品管理">商品管理</a></li>
                <li class="menu-item"><a href="#" target="_blank" title="营销管理">营销管理</a></li>
                <li class="menu-item"><a href="#" target="_blank" title="店铺管理">店铺管理</a></li>
                <li class="menu-item"><a href="#" target="_blank" title="订单管理">订单管理</a></li>
                <li class="menu-item"><a href="#" target="_blank" title="分销管理">分销管理</a></li>
                <li class="menu-item"><a href="#" target="_blank" title="客服管理">客服管理</a></li> -->
                </ul>
                <ul class="mune-box">
                    <li class="menu-item">新闻资讯</li>
                    @foreach($publicData['newsType'] as $val)
                    <li class="menu-item"><a href="/home/index/news?Pid={{ $val['id'] }}" title="{{ $val['name'] }}">{{ $val['name'] }}</a></li>
                    @endforeach
                    <!-- <li class="menu-item"><a href="/home/index/information/oneCategory/19" target="_blank" title="微商动态">微商动态</a></li>
                <li class="menu-item"><a href="/home/index/information/oneCategory/34" target="_blank" title="微营销技能">微营销技能</a></li>
                <li class="menu-item"><a href="/home/index/information/oneCategory/35" target="_blank" title="行业新闻">行业新闻</a></li>
                <li class="menu-item"><a href="/home/index/information/oneCategory/36" target="_blank" title="电商研究">电商研究</a></li>
                <li class="menu-item"><a href="/home/index/information/oneCategory/39" target="_blank" title="会搜云资讯">会搜云资讯</a></li>
                <li class="menu-item"><a href="/home/index/information/oneCategory/40" target="_blank" title="产品动态">产品动态</a></li>
                <li class="menu-item"><a href="/home/index/information/oneCategory/41" target="_blank" title="学习答疑">学习答疑</a></li> -->
                </ul>
                <ul class="mune-box">
                    <li class="menu-item">关于我们</li>
                    <li class="menu-item"><a href="/home/index/about" title="会搜简介">会搜简介</a></li>
                    <li class="menu-item"><a href="/home/index/growth" title="发展历程">发展历程</a></li>
                    <li class="menu-item"><a href="/home/index/culture" title="企业文化">企业文化</a></li>
                    <li class="menu-item"><a href="/home/index/recruit" title="招贤纳士">招贤纳士</a></li>
                    <li class="menu-item"><a href="/home/index/contactUs" title="联系我们">联系我们</a></li>
                </ul>
            </div>
            <div class="footer-code">
                <p class="menu-item">关于会搜云新零售系统</p>
                <img class="lazy" width="134" height="134" data-original="{{ config('app.source_url') }}home/image/aiCard/huisou.jpg" alt="会搜智慧平台二维码">
            </div>
        </div>
        <div class="ft-nav-box">
            <ul class="ft-nav-ul">
                <li><a href="/" target="_blank" title="会搜云">会搜云</a></li>
                <li><a href="//ai.huisou.cn/" target="_blank" title="会搜云新零售系统">会搜云新零售系统</a></li>
                <li><a href="http://afantisoft.cn/" target="_blank" title="阿凡提微商系统">阿凡提微商系统</a></li>
                <li><a href="http://china.huisou.com/" target="_blank" title="会搜网">会搜网</a></li>
            </ul>
        </div>
        <div class="beian">
            <p>杭州会搜科技股份有限公司 浙ICP备11003017号-1</p>
            <div class="icon-beian">
                <a class="upa" target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=33010402002295" style="display:inline-block;text-decoration:none;height:20px;line-height:20px;">
                    <img src="{{ config('app.source_url') }}home/image/beian.png" style="float:left;" />
                    <p style="float:left;height:20px;line-height:20px;margin: 0px 0px 0px 5px; color:#b0b4b7;">浙公网安备 33010402002295号</p>
                </a>
            </div>
        </div>
    </div>
</body>
<!--<script src="{{ config('app.source_url') }}home/js/rem.js" type="text/javascript" charset="utf-8"></script>-->
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/base.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js" type="text/javascript" charset="utf-8"></script>
@if(config('app.env') == 'prod')
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
@endif
@if(config('app.env') == 'dev')
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
@endif
@yield('foot.js')

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?1428d683bd5c972642b671971b847d6a";
        var s = document.
        getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();

    // 加入收藏 兼容360和IE6   
    function AddFavorite(title, url) {
        try {
            window.external.addFavorite(url, title);
        } catch (e) {
            try {
                window.sidebar.addPanel(title, url, "");
            } catch (e) {
                tipshow("您可以尝试通过快捷键 Ctrl+D 加入到网站收藏夹~");
            }
        }
    }
</script>
<!--站长推送代码-->
<script>
    (function() {
        var bp = document.createElement('script');
        var curProtocol = window.location.protocol.split(':')[0];
        if (curProtocol === 'https') {
            bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
        } else {
            bp.src = 'http://push.zhanzhang.baidu.com/push.js';
        }
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(bp, s);
    })();
</script>

</html>