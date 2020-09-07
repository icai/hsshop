@extends('home.base.head')
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/distribution.css"/>
@endsection
@section('content')
@include('home.base.slider')
<div class="container">
    <div class="banner-wraper">
        <div class="banner">
            <a class="link-btn" href="/home/index/reserve?type=1">立即订购</a>
        </div>
    </div>
    <div class="wraper">
        <div class="g-box main-value">
            <p class="wraper-title">会搜云分销核心价值</p>
            <div class="value-wraper">
                <div>
                    <img src="{{ config('app.source_url') }}home/image/value-icon1.png" class="value-img">
                    <p class="value-desc">开店成本低</p>
                </div>
                <div>
                    <img src="{{ config('app.source_url') }}home/image/value-icon2.png" class="value-img">
                    <p class="value-desc">开店速度快</p>
                </div>
                <div>
                    <img src="{{ config('app.source_url') }}home/image/value-icon3.png" class="value-img">
                    <p class="value-desc">管理更便捷</p>
                </div>
                <div>
                    <img src="{{ config('app.source_url') }}home/image/value-icon4.png" class="value-img">
                    <p class="value-desc">传播力度大</p>
                </div>
                <div>
                    <img src="{{ config('app.source_url') }}home/image/value-icon5.png" class="value-img">
                    <p class="value-desc">销售效率高</p>
                </div>
            </div>
        </div>
    </div>
    <div class="wraper g-gray">
        <div class="g-box model-wraper">
            <p class="wraper-title">会搜云分销模式</p>
            <div class="img-wraper">
                <div class="img-item">
                    <div class="item-box padding-box1">
                        <h3 class="title-big">新微信分销</h3>
                        <p class="content">专注于微信移动分销，让朋友圈成为您的盈利圈</p>
                    </div>
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/model1.png" class="icon-img">
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/model2.png" class="icon-img">
                    </div>
                    <div class="item-box padding-box2">
                        <h3 class="title-big">APP三级分销</h3>
                        <p class="content">一个APP解决您所有的销路难题<br>一套完善的三级分销商城与代理体系<br>一个消费者与分销代理商共赢的平台</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wraper">
        <div class="g-box system-wraper">
            <p class="wraper-title">会搜云分销系统</p>
            <div class="img-wraper">
                <div class="img-item">
                    <div class="item-box padding-box1">
                        <p class="title-system">
                            <span class="index-icon">1</span>全员分销，覆盖更多人群
                        </p>
                        <p class="content">裂变式分销，每个分销商都可以发展下级分销商</p>
                    </div>
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts1.png" class="icon-img">
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts2.png" class="icon-img">
                    </div>
                    <div class="item-box padding-box2">
                        <p class="title-system">
                            <span class="index-icon">2</span>万店同源，掌控一切
                        </p>
                        <p class="content">一个管理后台，轻松管理千万粉丝店铺</p>
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box padding-box1">
                        <p class="title-system">
                            <span class="index-icon">3</span>自定义分销模式
                        </p>
                        <p class="content">商家可在后台灵活设置分销模式，返佣制度，佣金比例</p>
                    </div>
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts3.png" class="icon-img">
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts4.png" class="icon-img">
                    </div>
                    <div class="item-box padding-box2">
                        <p class="title-system">
                            <span class="index-icon">4</span>可视化装修，DIY分销店铺
                        </p>
                        <p class="content">海量模版、功能自定义搭配，吸引更多消费者</p>
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box padding-box1">
                        <p class="title-system">
                            <span class="index-icon">5</span>特色营销活动，快速提升销量
                        </p>
                        <p class="content">优惠券、刮刮卡、大转盘、拼团、秒杀、享立减、会员签到等<br>多种营销玩法助力运营</p>
                    </div>
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts5.png" class="icon-img">
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts6.png" class="icon-img">
                    </div>
                    <div class="item-box padding-box2">
                        <p class="title-system">
                            <span class="index-icon">6</span>强大的分销订单管理系统
                        </p>
                        <p class="content">智能化订单管理，分销商产生订单一目了然，实时了解经营状况</p>
                    </div>
                </div>
                <div class="img-item">
                    <div class="item-box padding-box1">
                        <p class="title-system">
                            <span class="index-icon">7</span>佣金系统自由提取激励分销商
                        </p>
                        <p class="content">销售产品或下级分销商销售产品即可获取佣金</p>
                    </div>
                    <div class="item-box center-box">
                        <img src="{{ config('app.source_url') }}home/image/dts7.png" class="icon-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wraper distribution-footer">
        <p>立即订购分销系统，给你想要的分销</p>
        <a href="/home/index/reserve?type=1" class="footer-order">立即订购</a>
    </div>
</div>
   
@endsection
@section('foot.js')
@endsection