@extends('home.base.head')
@section('head.css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/shouye.css" />
@endsection
@section('content')
@include('home.base.slider')
<!--分页一-->
<div class="shou-fir">
    <div class="new_swiper">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class='index_banner05'>
                        <div class='index_banner'>
                            {{--<img src="{{ config('app.source_url') }}home/image/index_banner05.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="https://ai.huisou.cn">了解详情</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class='index_banner07'>
                        <div class='index_banner'>
                            {{--<img src="{{ config('app.source_url') }}home/image/index-new-banner03-20200810.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="https://ai.huisou.cn">了解详情</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class='index_banner06'>
                        <div class='index_banner'>
                            {{--<img src="{{ config('app.source_url') }}home/image/index-new-banner02-20200810.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="https://ai.huisou.cn">了解详情</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class='index_banner01'>
                        <div class='index_banner'>
                            {{--<img src="{{ config('app.source_url') }}home/image/index_banner01.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="/home/index/reserve?type=3">立即预约</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class='index_banner02'>
                        <div class="index_banner">
                            {{--<img src="{{ config('app.source_url') }}home/image/index_banner02.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="/home/index/reserve?type=2">立即预约</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class='index_banner03'>
                        <div class="index_banner">
                            {{--<img src="{{ config('app.source_url') }}home/image/index_banner03.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="/home/index/reserve?type=5">立即预约</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class='index_banner04'>
                        <div class="index_banner">
                            {{--<img src="{{ config('app.source_url') }}home/image/index_banner04.png" alt="会搜云"/>--}}
                            <a class="order-btn" href="/home/index/reserve?type=4">立即预约</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 如果需要分页器 -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
<div class="wraper">
    <div class="g-box new-consult">
        <span class="consult-tips">
            最新资讯
        </span>
        @if($newestList)
        <span>
            @foreach($newestList as $val)
            <a href="/home/index/newsDetail/{{ $val['id'] }}/news" target="_blank" title="{{ $val['title'] }}" class="consult-item">
                <i class="consult-icon"></i>
                {{ $val['title'] }}
            </a>
            @endforeach
        </span>
        @endif
    </div>
</div>
<div class="wraper g-gray wraper-pad-top">
    <div class="g-box box-padding">
        <p class="box-t1">会搜云产品</p>
        <p class="box-t2 tip_box_text">全渠道全场景的SaaS产品，助力企业转型布局新零售</p>
        <ul style="font-size: 0">
            <li class="box-li J_box-li">
                <div class="show-item">
                    <img src="{{ config('app.source_url') }}home/image/jiage1.png" width="100%" height="120">
                    <p class="show-t1">会搜云新零售系统</p>
                    <p class="show-t2">直播电商，新零售，销售升级</p>
                </div>

                <a href="https://ai.huisou.cn" class="hover-link J_hover-link">
                    <p class="hover-link-t1">会搜云新零售系统</p>
                    <div class="hover-link-item-box">
                        <div class="hover-link-item">
                            <p>直播+电商</p>
                            <p>企业管理</p>
                            <p>社交名片</p>
                            <p>VR</p>
                        </div>
                        <div class="hover-link-item">
                            <p>新零售</p>
                            <p>智能拓客</p>
                            <p>分销系统</p>
                            <p>多样营销工具</p>
                        </div>
                    </div>
                    <div class="get-more">
                        <span>了解详情</span>
                    </div>
                </a>
            </li>
            <li class="box-li J_box-li">
                <div class="show-item">
                    <img src="{{ config('app.source_url') }}home/image/appIcon.jpg?t=1" width="100%" height="120">
                    <p class="show-t1">APP定制</p>
                    <p class="show-t2">功能完善，1V1进行定制</p>
                </div>
                <a href="/home/index/customization" class="hover-link J_hover-link">
                    <p class="hover-link-t1">APP定制</p>
                    <div class="hover-link-item-box">
                        <div class="hover-link-item">
                            <p>APP定制</p>
                            <p>交互设计</p>
                            <p>APP原型设计</p>
                        </div>
                        <div class="hover-link-item">
                            <p>Android开发</p>
                            <p>IOS开发</p>
                            <p>界面设计</p>
                        </div>
                    </div>
                    <div class="get-more">
                        <span>了解详情</span>
                    </div>
                </a>
            </li>
            <li class="box-li J_box-li">
                <div class="show-item">
                    <img src="{{ config('app.source_url') }}home/image/xcxIcon.jpg?t=1" width="100%" height="120">
                    <p class="show-t1">小程序定制</p>
                    <p class="show-t2">成本低、周期短、用户体验好</p>
                </div>
                <a href="/home/index/applet" class="hover-link J_hover-link">
                    <p class="hover-link-t1">小程序定制</p>
                    <div class="hover-link-item-box">
                        <div class="hover-link-item">
                            <p>小程序定制</p>
                            <p>小程序管理</p>
                        </div>
                        <div class="hover-link-item">
                            <p>小程序开发</p>
                            <p>小程序审核</p>
                        </div>
                    </div>
                    <div class="get-more">
                        <span>了解详情</span>
                    </div>
                </a>
            </li>
            <li class="box-li J_box-li">
                <div class="show-item">
                    <img src="{{ config('app.source_url') }}home/image/wxIcon.jpg?t=1" width="100%" height="120">
                    <p class="show-t1">微商城开发</p>
                    <p class="show-t2">一站式移动电商解决方案</p>
                </div>
                <a href="/home/index/microshop" class="hover-link J_hover-link">
                    <p class="hover-link-t1">微商城开发</p>
                    <div class="hover-link-item-box">
                        <div class="hover-link-item">
                            <p>微信商城开发</p>
                            <p>微信商城建设</p>
                            <p>订单管理系统</p>
                        </div>
                        <div class="hover-link-item">
                            <p>微信分销系统</p>
                            <p>客户管理系统</p>
                            <p>数据分析系统</p>
                        </div>
                    </div>
                    <div class="get-more">
                        <span>了解详情</span>
                    </div>
                </a>
            </li>
            <li class="box-li J_box-li">
                <div class="show-item">
                    <img src="{{ config('app.source_url') }}home/image/yxIcon.jpg?t=1" width="100%" height="120">
                    <p class="show-t1">移动互联网实战总裁班</p>
                    <p class="show-t2">明星导师教学，快速掌握</p>
                </div>
                <a href="/home/index/microMarketing" class="hover-link J_hover-link">
                    <p class="hover-link-t1">营销总裁班</p>
                    <div class="hover-link-item-box">
                        <div class="hover-link-item">
                            <p>小程序推广运营</p>
                            <p>APP推广运营</p>
                            <p>公众平台实操</p>
                        </div>
                        <div class="hover-link-item">
                            <p>营销团队打造</p>
                            <p>社群营销</p>
                            <p>数据分析系统</p>
                        </div>
                    </div>
                    <div class="get-more">
                        <span>了解详情</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wraper example-wraper">
    <div class="g-box box-padding">
        <p class="box-t1">
            会搜云经典案例展示
            <a href="/home/index/1/shop" class="more-btn">更多案例</a>
        </p>
        <p class="box-t2">服务千万中小企业，助力传统行业转型，他们正在使用会搜云改变自己的零售方式</p>
        <div class="clearfix">
            <div class="example-fl">
                <ul class="example-ul">
                    <li class="active">会搜云新零售系统</li>
                    <li>APP开发</li>
                    <li>小程序开发</li>
                    <li>微商城开发</li>
                    <!--  <li>微营销课程</li> -->
                </ul>
            </div>
            <div class="example-box">
                <ul class="example-img-box" style="display:block">
                    @if(isset($caseTypeList['card']) && $caseTypeList['card'])
                    @foreach($caseTypeList['card'] as $val)
                    <li>
                        <img src="{{ imgUrl() }}{{ $val['logo'] or '' }}" width="260" height="260">
                        <p class="exemple-title">会搜云新零售系统：{{ $val['name'] }}</p>
                        <a href="/home/index/caseDetails?id={{ $val['id'] }}" class="example-link J_example-link">
                            <img src="{{ imgUrl() }}{{ $val['code'] or '' }}" width="170" height="170">
                            <div class="get-more">
                                <span>查看详情</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                    @endif
                </ul>
                <ul class="example-img-box">
                    @if(isset($caseTypeList['app']) && $caseTypeList['app'])
                    @foreach($caseTypeList['app'] as $val)
                    <li>
                        <img src="{{ imgUrl() }}{{ $val['logo'] or '' }}" width="260" height="260">
                        <p class="exemple-title">APP：{{ $val['name'] }}</p>
                        <a href="/home/index/caseDetails?id={{ $val['id'] }}" class="example-link J_example-link">
                            <img src="{{ imgUrl() }}{{ $val['code'] or '' }}" width="170" height="170">
                            <div class="get-more">
                                <span>查看详情</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                    @endif
                </ul>
                <ul class="example-img-box">
                    @if(isset($caseTypeList['xcx']) && $caseTypeList['xcx'])
                    @foreach($caseTypeList['xcx'] as $val)
                    <li>
                        <img src="{{ imgUrl() }}{{ $val['logo'] or '' }}" width="260" height="260">
                        <p class="exemple-title">小程序：{{ $val['name'] }}</p>
                        <a href="/home/index/caseDetails?id={{ $val['id'] }}" class="example-link J_example-link">
                            <img src="{{ imgUrl() }}{{ $val['code'] or '' }}" width="170" height="170">
                            <div class="get-more">
                                <span>查看详情</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                    @endif
                </ul>
                <ul class="example-img-box">
                    @if(isset($caseTypeList['shop']) && $caseTypeList['shop'])
                    @foreach($caseTypeList['shop'] as $val)
                    <li>
                        <img src="{{ imgUrl() }}{{ $val['logo'] or '' }}" width="260" height="260">
                        <p class="exemple-title">公众号：{{ $val['name'] }}</p>
                        <a href="/home/index/caseDetails?id={{ $val['id'] }}" class="example-link J_example-link">
                            <img src="{{ imgUrl() }}{{ $val['code'] or '' }}" width="170" height="170">
                            <div class="get-more">
                                <span>查看详情</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                    @endif
                </ul>
                <!-- <ul class="example-img-box">
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/f-img10.png" width="260" height="260">
                            <p class="exemple-title">APP：珠宝鉴赏</p>
                            <a href="" class="example-link J_example-link">
                                <img src="{{ config('app.source_url')}}home/image/footer_code.jpg" width="170" height="170">
                                <div class="get-more">
                                    <span>查看详情</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/example.jpg" width="260" height="260">
                            <p class="exemple-title">APP：珠宝鉴赏</p>
                            <a href="" class="example-link J_example-link">
                                <img src="{{ config('app.source_url')}}home/image/footer_code.jpg" width="170" height="170">
                                <div class="get-more">
                                    <span>查看详情</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/example.jpg" width="260" height="260">
                            <p class="exemple-title">APP：珠宝鉴赏</p>
                            <a href="" class="example-link J_example-link">
                                <img src="{{ config('app.source_url')}}home/image/footer_code.jpg" width="170" height="170">
                                <div class="get-more">
                                    <span>查看详情</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <img src="{{ config('app.source_url') }}home/image/example.jpg" width="260" height="260">
                            <p class="exemple-title">APP：珠宝鉴赏</p>
                            <a href="" class="example-link J_example-link">
                                <img src="{{ config('app.source_url')}}home/image/footer_code.jpg" width="170" height="170">
                                <div class="get-more">
                                    <span>查看详情</span>
                                </div>
                            </a>
                        </li>
                    </ul> -->
            </div>
        </div>
    </div>
</div>
<div class="wraper g-gray maeketing-wraper">
    <div class="g-box box-padding">
        <p class="box-t1">
            丰富多样的营销应用
            <a href="/home/index/appRecommen" class="more-btn">查看更多</a>
        </p>
        <p class="box-t2">经营渠道、促销折扣、促销工具、会员卡券助你玩转微信营销</p>
        <div class="z-custom">
            <div class="swiper-container ">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">会搜云新零售系统<span>NEW RETAIL</span></p>
                                <p class="slider-item-t2">打造微信智能系统，助力企业转型新零售</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content slider-content-block">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-1-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">直播电商</p>
                                        <p class="slider-item-d2">裂变引流，直播带货，帮助商家构建私域流量。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-2-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">新零售</p>
                                        <p class="slider-item-d2">构建智慧零售，线上线下一体式经营。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-3-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">企业管理</p>
                                        <p class="slider-item-d2">sales千里眼，boss千里眼，助力企业管理。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-4-20200811.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">智能拓客</p>
                                        <p class="slider-item-d2">销售多渠道、多方式、多维度裂变获客。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-5-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">社交名片</p>
                                        <p class="slider-item-d2">多维度社交名片，打造专属商业形象。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-6-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">分销系统</p>
                                        <p class="slider-item-d2">分享裂变，分销扩展销售渠道。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-7-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">VR</p>
                                        <p class="slider-item-d2">720°全景展示，打造全新视觉体验。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/aiCard/home-new-retail-8-20200810.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">多样营销工具</p>
                                        <p class="slider-item-d2">拼团、秒杀、优惠券、表单、文件夹等多种营销工具灵活应用。</p>
                                    </div>
                                    <a href="https://ai.huisou.cn/" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">经营渠道<span>CHANNEL</span></p>
                                <p class="slider-item-t2">两大渠道，交互经营</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon1.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">小程序</p>
                                        <p class="slider-item-d2">一键生成微信小程序</p>
                                    </div>
                                    <a href="/home/index/manageChannel/detail/1" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon2.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">公众号</p>
                                        <p class="slider-item-d2">链接公众号，玩转微信生态圈</p>
                                    </div>
                                    <a href="/home/index/manageChannel/detail/2" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">促销折扣<span>DISCOUNT</span></p>
                                <p class="slider-item-t2">超值优惠，高效转化</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon3.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">优惠券</p>
                                        <p class="slider-item-d2">向客户发放店铺优惠劵</p>
                                    </div>
                                    <a href="/home/index/salesDiscount/detail/1" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon4.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">秒杀</p>
                                        <p class="slider-item-d2">快速抢购引导顾客更多消费</p>
                                    </div>
                                    <a href="/home/index/salesDiscount/detail/2" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">促销工具<span>TOOL</span></p>
                                <p class="slider-item-t2">多元化，引爆用户增长</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon5.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">享立减</p>
                                        <p class="slider-item-d2">用户点击分享链接可减钱的新玩法</p>
                                    </div>
                                    <a href="/home/index/salesTools/detail/1" class="view-detail-link">了解详情</a>
                                </li>
                                {{--<li>--}}
                                {{--<div class="slider-item-img">--}}
                                {{--<img src="{{ config('app.source_url') }}home/image/app-icon6.png" width="56" height="56">--}}
                                {{--</div>--}}
                                {{--<div class="slider-item-desc">--}}
                                {{--<p class="slider-item-d1">集赞</p>--}}
                                {{--<p class="slider-item-d2">邀请好友点赞可享受优惠的玩法</p>--}}
                                {{--</div>--}}
                                {{--<a href="/home/index/salesTools/detail/2" class="view-detail-link">了解详情</a>--}}
                                {{--</li>--}}
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon7.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">多人拼团</p>
                                        <p class="slider-item-d2">引导客户邀请朋友一起拼团购买</p>
                                    </div>
                                    <a href="/home/index/salesTools/detail/3" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon8.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">幸运大转盘</p>
                                        <p class="slider-item-d2">常见的转盘式抽奖玩法</p>
                                    </div>
                                    <a href="/home/index/salesTools/detail/4" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">促销工具<span>TOOL</span></p>
                                <p class="slider-item-t2">多元化，引爆用户增长</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon9.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">微社区</p>
                                        <p class="slider-item-d2">打造人气移动社区,增加客户流量</p>
                                    </div>
                                    <a href="/home/index/salesTools/detail/5" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon10.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">砸金蛋</p>
                                        <p class="slider-item-d2">好蛋砸出来，礼品不间断</p>
                                    </div>
                                    <a href="/home/index/salesTools/detail/6" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon11.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">签到</p>
                                        <p class="slider-item-d2">每日签到领取积分或奖励</p>
                                    </div>
                                    <a href="/home/index/salesTools/detail/7" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">会员卡券<span>DISCOUNT</span></p>
                                <p class="slider-item-t2">增加用户粘性，提高用户留存</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon12.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">会员卡</p>
                                        <p class="slider-item-d2">设置并给客户发放会员卡</p>
                                    </div>
                                    <a href="/home/index/memberTicket/detail/1" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon13.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">积分</p>
                                        <p class="slider-item-d2">完善的积分奖励消耗制度</p>
                                    </div>
                                    <a href="/home/index/memberTicket/detail/2" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon14.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">充值</p>
                                        <p class="slider-item-d2">开通会员充值功能</p>
                                    </div>
                                    <a href="/home/index/memberTicket/detail/3" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-item-container">
                            <div class="slider-item-title">
                                <p class="slider-item-t1">推广工具<span>EXTENSION</span></p>
                                <p class="slider-item-t2">即时消息推送、调研</p>
                            </div>
                            <ul class="slider-item-content J_slider-item-content">
                                <li class="active">
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon15.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">消息提醒</p>
                                        <p class="slider-item-d2">向客户发布微信消息提醒</p>
                                    </div>
                                    <a href="/home/index/extension/detail/1" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon17.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">消息模板</p>
                                        <p class="slider-item-d2">向客户展示微信消息提示的模版</p>
                                    </div>
                                    <a href="/home/index/extension/detail/2" class="view-detail-link">了解详情</a>
                                </li>
                                <li>
                                    <div class="slider-item-img">
                                        <img src="{{ config('app.source_url') }}home/image/app-icon16.png" width="56" height="56">
                                    </div>
                                    <div class="slider-item-desc">
                                        <p class="slider-item-d1">投票</p>
                                        <p class="slider-item-d2">向客户发起投票活动</p>
                                    </div>
                                    <a href="/home/index/extension/detail/3" class="view-detail-link">了解详情</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 切换按钮 -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</div>
<div class="wraper">
    <div class="g-box box-padding" style="padding-bottom: 34px;">
        <p class="box-t1">我们的服务流程</p>
        <p class="box-t2">完善的定制流程，层层把关，确保交给客户一份满意的答卷</p>
        <div class="service-stream">
            <div class="service-stream-item service-stream-active">
                <i class="service-stream-icon service-stream-icon1"></i>
                <p>商务洽谈</p>
            </div>
            <div class="service-stream-arrow">
                <p></p>
                <i></i>
            </div>
            <div class="service-stream-item">
                <i class="service-stream-icon service-stream-icon2"></i>
                <p>需求沟通</p>
            </div>
            <div class="service-stream-arrow">
                <p></p>
                <i></i>
            </div>
            <div class="service-stream-item">
                <i class="service-stream-icon service-stream-icon3"></i>
                <p>视觉设计</p>
            </div>
            <div class="service-stream-arrow">
                <p></p>
                <i></i>
            </div>
            <div class="service-stream-item">
                <i class="service-stream-icon service-stream-icon4"></i>
                <p>技术开发</p>
            </div>
            <div class="service-stream-arrow">
                <p></p>
                <i></i>
            </div>
            <div class="service-stream-item">
                <i class="service-stream-icon service-stream-icon5"></i>
                <p>项目测试</p>
            </div>
            <div class="service-stream-arrow">
                <p></p>
                <i></i>
            </div>
            <div class="service-stream-item">
                <i class="service-stream-icon service-stream-icon6"></i>
                <p>验收上线</p>
            </div>
        </div>
    </div>
</div>
<div class="wraper agreement-wrap">
    <div class="g-box J_agreement-container sign-box" style="position:relative;">
        <img class="sign-image" src="{{ config('app.source_url') }}/home/image/sign-image1.png">
        <div class="sign-info">
            <div class="sign-title">
                <p>商务合作，</p>
                <p>签订合同</p>
            </div>
            <div class="sign-content">
                <p>1、需求确认</p>
                <p>2、功能清单、功能报价</p>
                <p>3、项目时间进度计划</p>
            </div>
        </div>
    </div>
    <div class="g-box J_agreement-container hide-agreement sign-box">
        <img class="sign-image" src="{{ config('app.source_url') }}/home/image/sign-image2.png">
        <div class="sign-info">
            <div class="sign-title">
                <p>需求沟通，</p>
                <p>产品原型</p>
            </div>
            <div class="sign-content">
                <p>1、设计功能逻辑图</p>
                <p>2、设计原型图</p>
                <p>3、客户确认原型图</p>
            </div>
        </div>
    </div>
    <div class="g-box J_agreement-container hide-agreement sign-box">
        <img class="sign-image" src="{{ config('app.source_url') }}/home/image/sign-image3.png">
        <div class="sign-info">
            <div class="sign-title">
                <p>UI视觉设计</p>
            </div>
            <div class="sign-content">
                <p>1、UI设计视觉页面</p>
                <p>2、与客户确认UI设计图</p>
            </div>
        </div>
    </div>
    <div class="g-box J_agreement-container hide-agreement sign-box">
        <img class="sign-image" src="{{ config('app.source_url') }}/home/image/sign-image4.png">
        <div class="sign-info">
            <div class="sign-title">
                <p>技术开发</p>
            </div>
            <div class="sign-content">
                <p>1、根据需求原型图及UI图完成前端、后台开发</p>
                <p>2、前端与后台配合对接开发调试接口</p>
            </div>
        </div>
    </div>
    <div class="g-box J_agreement-container hide-agreement sign-box">
        <img class="sign-image" src="{{ config('app.source_url') }}/home/image/sign-image5.png">
        <div class="sign-info">
            <div class="sign-title">
                <p>项目测试</p>
            </div>
            <div class="sign-content">
                <p>1、测试对项目进行整体测试（功能逻辑、页面交互、数据校验）</p>
                <p>2、测试完成、提交验收</p>
            </div>
        </div>
    </div>
    <div class="g-box J_agreement-container hide-agreement sign-box">
        <img class="sign-image" src="{{ config('app.source_url') }}/home/image/sign-image6.png">
        <div class="sign-info">
            <div class="sign-title">
                <p>验收上线，</p>
                <p>项目交付</p>
            </div>
            <div class="sign-content">
                <p>1、客户端对APP进行打包上架各大应用市场</p>
                <p>2、交付给客户进行使用</p>
            </div>
        </div>
    </div>
</div>
<div class="wraper">
    <div class="g-box order-box">
        <a href="/home/index/reserve?type=3" class="order-link">我要预约</a>
    </div>
</div>
<div class="wraper about-us-wraper">
    <div class="g-box box-padding">
        <p class="box-t1">
            关于会搜云
            <a href="/home/index/about" class="more-btn">了解详情</a>
        </p>
        <p class="box-t2">会搜股份于2016年5月30日在新三板成功挂牌上市，股票代码：837521。</p>
        <div class="clearfix">
            <div class="about-fl">
                <div class="laptop-icon"></div>
                <div class="special-icon"></div>
            </div>
            <div class="about-content">
                <div class="view-count">
                    <div class="view-count-item">
                        <p class="view-num">100 +</p>
                        <p class="view-desc">技术开发团队，交期准时</p>
                    </div>
                    <div class="view-count-item">
                        <p class="view-num">10 +</p>
                        <p class="view-desc">年定制系统、商城开发经验</p>
                    </div>
                    <div class="view-count-item">
                        <p class="view-num">10000 +</p>
                        <p class="view-desc">家企业客户提供产品开发</p>
                    </div>
                </div>
                <ul class="strength-container">
                    <li>
                        <p class="strength-t1">原生开发</p>
                        <p class="strength-t2">所有APP和小程序全部原生开发，不套模板</p>
                    </li>
                    <li>
                        <p class="strength-t1">适配更新</p>
                        <p class="strength-t2">APP系统后台可PC端、移动端（Android、IOS）全适应</p>
                    </li>
                    <li>
                        <p class="strength-t1">快速响应</p>
                        <p class="strength-t2">技术问题快速响应，100多人的技术团队及时处理后期各种问题</p>
                    </li>
                    <li>
                        <p class="strength-t1">豪礼赠送</p>
                        <p class="strength-t2">确定开发业务后可赠送微营销课程</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="wraper g-gray maeketing-wraper">
    <div class="g-box box-padding">
        <p class="box-t1">
            会搜云资讯动态
            <a href="/home/index/news" class="more-btn">查看更多</a>
        </p>
        <p class="box-t2">移动互联网时代，时刻把握行业最前沿的咨询</p>
        <div class="dynamic-container">
            <div>
                <div>
                    <img src="{{ config('app.source_url') }}/home/image/dynamic1.png" width="350" height="130">
                </div>
                @if($typenewsList['newest'])
                <div class="dynamic-message">
                    @foreach($typenewsList['newest'] as $val)
                    <div class="message-link-item">
                        <span>[{{ date('m-d',strtotime($val['created_at'])) }}]</span>
                        <a href="/home/index/newsDetail/{{ $val['id'] }}/news" class="message-link" title="{{ $val['title'] }}">{{ $val['title'] }}</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div>
                <div>
                    <img src="{{ config('app.source_url') }}/home/image/dynamic2.png" width="350" height="130">
                </div>
                @if($typenewsList['industry'])
                <div class="dynamic-message">
                    @foreach($typenewsList['industry'] as $val)
                    <div class="message-link-item">
                        <span>[{{ date('m-d',strtotime($val['created_at'])) }}]</span>
                        <a href="/home/index/newsDetail/{{ $val['id'] }}/news" class="message-link" title="{{ $val['title'] }}">{{ $val['title'] }}</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div>
                <div>
                    <img src="{{ config('app.source_url') }}/home/image/dynamic3.png" width="350" height="130">
                </div>
                @if($typenewsList['online'])
                <div class="dynamic-message">
                    @foreach($typenewsList['online'] as $val)
                    <div class="message-link-item">
                        <span>[{{ date('m-d',strtotime($val['created_at'])) }}]</span>
                        <a href="/home/index/newsDetail/{{ $val['id'] }}/news" class="message-link" title="{{ $val['title'] }}">{{ $val['title'] }}</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="wraper register-box">
    <div class="g-box" style="text-align:center;">
        <p class="register-tips">立即注册即可体验火爆的小程序</p>
        <a href="/auth/register" class="register-btn">立即注册</a>
    </div>
</div>
@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/shouye.js" type="text/javascript" charset="utf-8"></script>
@endsection