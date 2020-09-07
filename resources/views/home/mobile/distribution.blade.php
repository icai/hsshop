@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<meta name="keywords" content="分销系统">
	<meta name="description" content="会搜股份荣誉出品，会搜云专注做分销系统全套解决方案，提供分销系统哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/distribution.css">
@endsection

@section('content')
   <div class="content">
       <div class="banner">
           <a href="/home/index/serviceFir" class="link-btn">立即预约</a>
       </div>
       <div class="mian-value">
           <p class="title">分销核心价值</p>
           <div class="value-box value-box1">
               <div class="value-item">
                   <img src="{{ config('app.source_url') }}mobile/images/value1.png" class="value-img">
                   <p>开店成本低</p>
               </div>
               <div class="value-item">
                   <img src="{{ config('app.source_url') }}mobile/images/value2.png" class="value-img">
                   <p>开店速度快</p>
               </div>
               <div class="value-item">
                   <img src="{{ config('app.source_url') }}mobile/images/value3.png" class="value-img">
                   <p>管理更便捷</p>
               </div>
           </div>
           <div class="value-box value-box2">
               <div class="value-item">
                   <img src="{{ config('app.source_url') }}mobile/images/value4.png" class="value-img">
                   <p>传播力度大</p>
               </div>
               <div class="value-item">
                   <img src="{{ config('app.source_url') }}mobile/images/value5.png" class="value-img">
                   <p>销售效率高</p>
               </div>
           </div>
       </div>
       <div class="model-wraper">
            <p class="title">会搜云分销模式</p>
            <div class="model-item1">
                <p class="model-title">新微信分销</p>
                <p class="model-desc">专注于微信移动分销，让朋友圈成为您的盈利圈</p>
                <div class="model-img-wraper">
                    <img src="{{ config('app.source_url') }}mobile/images/model1.png" alt="">
                </div>
            </div>
            <div class="model-item2">
                <p class="model-title">APP三级分销</p>
                <p class="model-desc">一个APP解决您所有的销路难题<br>一套完善的三级分销商城与代理体系，一个消费者<br>与分销代理商共赢的平台</p>
                <div class="model-img-wraper">
                    <img src="{{ config('app.source_url') }}mobile/images/model2.png" alt="">
                </div>
            </div>
       </div>
       <div class="system-wraper">
            <p class="title">会搜云分销系统</p>
            <div class="systems">
                <div class="system-item">
                    <p class="system-title">
                        <span>1</span>全员分销，覆盖更多人群
                    </p>
                    <p>裂变式分销，每个分销商都可以发展下级分销商</p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts1.png" alt="">
                    </div>
                </div>
                <div class="system-item">
                    <p class="system-title">
                        <span>2</span>万店同源，掌控一切
                    </p>
                    <p>一个管理后台，轻松管理千万粉丝店铺</p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts2.png" alt="">
                    </div>
                </div>
                <div class="system-item">
                    <p class="system-title">
                        <span>3</span>自定义分销模式
                    </p>
                    <p>商家可在后台灵活设置分销模式，返佣制度，佣金比例</p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts3.png" alt="">
                    </div>
                </div>
                <div class="system-item">
                    <p class="system-title">
                        <span>4</span>可视化装修，DIY分销店铺
                    </p>
                    <p>海量模版、动能自定义搭配，吸引更多消费者</p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts4.png" alt="">
                    </div>
                </div>
                <div class="system-item">
                    <p class="system-title">
                        <span>5</span>特色营销活动，快速提升销量
                    </p>
                    <p>优惠券、刮刮卡、大转盘、拼团、秒杀、享立减、<br>会员签到等多种营销玩法助力运营</p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts5.png" alt="">
                    </div>
                </div>
                <div class="system-item">
                    <p class="system-title">
                        <span>6</span>强大的分销订单管理系统
                    </p>
                    <p>智能化订单管理，分销商产生订单一目了然，<br>实时了解经营状况 </p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts6.png" alt="">
                    </div>
                </div>
                <div class="system-item">
                    <p class="system-title">
                        <span>7</span>佣金系统自由提取激励分销商
                    </p>
                    <p>销售产品或下级分销商销售产品即可获取佣金</p>
                    <div class="system-img-wraper">
                        <img src="{{ config('app.source_url') }}mobile/images/dts7.png" alt="">
                    </div>
                </div>
            </div>
       </div>
       <a class="dis-footer" href="/home/index/serviceFir">
           立即订购分销系统<span class="arrow-icon"></span>
       </a>
   </div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection
@section('js')
    
@endsection