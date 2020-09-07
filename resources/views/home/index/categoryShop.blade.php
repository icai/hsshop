@extends('home.base.head')
<title>{{$title}}</title>
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <!--<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/microshop.css"/>-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/zifenlei.css"/>
@endsection
@section('content')
    @include('home.base.slider')
    <div class="main_content">
        <!--分页一-->
        <div class="zi-fir">
            <div class="swiper-container">
			    <div class="swiper-wrapper">
			        <div class="swiper-slide aa">
			        	<div class="zi-meixiu">
			        		<div class="zi-meil1">
			        			<img src=""/>
			        			<p class="zi-p1">美秀大师</p>
			        		</div>
			        		<div class="zi-meil2">
				        		<p class="zi-p2">美秀大师APP是一款针对女性美妆养生提供服务的客户端。该客户端为用户提供纹眉、唇线级护理等服务。</p>
				        		<p class="zi-p4">美秀大师有限公司经理</p>
			        		</div>			        		
			        		<div class="zi-meil3">
			        			<p class="zi-p5">查看商城</p>
			        			<img width="108px" height="108px" class="shang-erwei" src="{{ config('app.source_url') }}home/image/weishangerweima.png"/>
			        		</div>
			        	</div>
			        </div>
			        <div class="swiper-slide aa">
			        	<div class="zi-meixiu">
			        		<div class="zi-meil1">
			        			<img src=""/>
			        			<p class="zi-p1">美秀大师</p>
			        		</div>
			        		<div class="zi-meil2">
				        		<p class="zi-p2">美秀大师APP是一款针对女性美妆养生提供服务的客户端。该客户端为用户提供纹眉、唇线级护理等服务。</p>
				        		<p class="zi-p4">美秀大师有限公司经理</p>
			        		</div>			        		
			        		<div class="zi-meil3">
			        			<p class="zi-p5">查看商城</p>
			        			<img width="108px" height="108px" class="shang-erwei" src="{{ config('app.source_url') }}home/image/weishangerweima.png"/>
			        		</div>
			        	</div>
			        </div>
			        <div class="swiper-slide aa">
			        	<div class="zi-meixiu">
			        		<div class="zi-meil1">
			        			<img src=""/>
			        			<p class="zi-p1">美秀大师</p>
			        		</div>
			        		<div class="zi-meil2">
				        		<p class="zi-p2">美秀大师APP是一款针对女性美妆养生提供服务的客户端。该客户端为用户提供纹眉、唇线级护理等服务。</p>
				        		<p class="zi-p4">美秀大师有限公司经理</p>
			        		</div>			        		
			        		<div class="zi-meil3">
			        			<p class="zi-p5">查看商城</p>
			        			<img width="108px" height="108px" class="shang-erwei" src="{{ config('app.source_url') }}home/image/weishangerweima.png"/>
			        		</div>
			        	</div>
			        </div>
			        <div class="swiper-slide aa">
			        	<div class="zi-meixiu">
			        		<div class="zi-meil1">
			        			<img src=""/>
			        			<p class="zi-p1">美秀大师</p>
			        		</div>
			        		<div class="zi-meil2">
				        		<p class="zi-p2">美秀大师APP是一款针对女性美妆养生提供服务的客户端。该客户端为用户提供纹眉、唇线级护理等服务。</p>
				        		<p class="zi-p4">美秀大师有限公司经理</p>
			        		</div>			        		
			        		<div class="zi-meil3">
			        			<p class="zi-p5">查看商城</p>
			        			<img width="108px" height="108px" class="shang-erwei" src="{{ config('app.source_url') }}home/image/weishangerweima.png"/>
			        		</div>
			        	</div>
			        </div>
			        <div class="swiper-slide aa">
			        	<div class="zi-meixiu">
			        		<div class="zi-meil1">
			        			<img src=""/>
			        			<p class="zi-p1">美秀大师</p>
			        		</div>
			        		<div class="zi-meil2">
				        		<p class="zi-p2">美秀大师APP是一款针对女性美妆养生提供服务的客户端。该客户端为用户提供纹眉、唇线级护理等服务。</p>
				        		<p class="zi-p4">美秀大师有限公司经理</p>
			        		</div>			        		
			        		<div class="zi-meil3">
			        			<p class="zi-p5">查看商城</p>
			        			<img width="108px" height="108px" class="shang-erwei" src="{{ config('app.source_url') }}home/image/weishangerweima.png"/>
			        		</div>
			        	</div>
			        </div>
			    </div>
			    <!-- 如果需要分页器 -->
			    <div class="swiper-pagination"></div>
			    
			    <!-- 如果需要导航按钮 -->
			    <div class="swiper-button-prev bu-nex"></div>
			    <div class="swiper-button-next bu-nex"></div>
			</div>
        </div>
        <!--分页二-->
        <div class="zi-sec">
            <div class="zi-sec1">
                <div class="zi-l1">
                    <img style="cursor: pointer" onclick="window.location.href='/home/index/microshop'" class="zi-lg" width="125px" height="125px" src="{{ config('app.source_url') }}home/image/weishangchengxitong.png"/>
                    <p>微商城系统</p>
                </div>
                <div class="zi-l1">
                    <img style="cursor: pointer" onclick="window.location.href='/home/index/distribution'" class="zi-lg" width="125px" height="125px" src="{{ config('app.source_url') }}home/image/weifenxiaoxitong.png"/>
                    <p>微分销系统</p>
                </div>
                <div class="zi-l1">
                    <img style="cursor: pointer" onclick="window.location.href='/home/index/customization'" class="zi-lg" width="125px" height="125px" src="{{ config('app.source_url') }}home/image/APP-dingzhi.png"/>
                    <p>APP定制</p>
                </div>
                <div class="zi-l1">
                    <img style="cursor: pointer" onclick="window.location.href='/home/index/microshop'" class="zi-lg" width="125px" height="125px" src="{{ config('app.source_url') }}home/image/APP-fenxiao.png"/>
                    <p>APP分销</p>
                </div>
                <div class="zi-l1">
                    <img style="cursor: pointer" onclick="window.location.href='/home/index/applet'" class="zi-lg" width="125px" height="125px" src="{{ config('app.source_url') }}home/image/weixinxiaochengxu.png"/>
                    <p>微信小程序</p>
                </div>
            </div>
        </div>
        <!--分页三-->
        <div class="zi-fou">
            <div class="zi-fou1">
                <div class="zi-ul1">
                    <p class="zi-op1">综合类</p>
                    <div class="zi-sle">
                    </div>
                </div>
                <div class="zi-ul2">
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                    <div class="zi-ol1">
                        <img width="226px" height="157px" src=""/>
                        <div class="zi-olp">
                            <p class="zi-op2">罗胖思维</p>
                            <p class="zi-op3">罗胖每天早上60s语音，用文字回复语音中的关键、有料做大家“身边的读书人”</p>
                            <p class="zi-op4">城市:<span>北京</span></p>
                        </div>
                        <div class="zi-olp1">
                            <img class="zi-img" width="94px" height="94px" src=""/>
                            <p class="zi-op5">扫码参观店铺</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
	<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/shop.js" type="text/javascript" charset="utf-8"></script>
@endsection