@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/eggIndex.css">
@endsection
@section('main') 
    <div class="content" style="max-width: 600px;">
    	<div class="stage">
    		<div class="carousel_box swiper-container hide">
	    		<ul class= "carousel swiper-wrapper">
	    			<li class="swiper-slide" v-for="item in win_list">
	    				<p>
	    					恭喜：<span style="color: #fb2e3d;">[[item.name]]</span>，获得 <span style="color: #fb2e3d;">[[item.pName]]</span>
	    				</p>
	    			</li>
	    		</ul>
    		</div>
    		<div class="hitegg"></div>
    		<img src="{{ config('app.source_url') }}shop/images/bg01.jpg">
    		<div id="shape" class="cube on">
	    		<div class="plane one"><span></span></div>
	    		<div class="plane two"><span></span></div>
	    		<div class="plane three"><span></span></div>
    		</div>
    		<div class="hit hide"></div>
    		<div class="myPrize" >我的奖品</div>
    	</div>
    	<div class="board egg_rule" v-cloak>
    		<div class="rule_box">
                <div class="participate">
                    <div class="rule_title">
                        参与次数
                    </div>
                    <div class="item" v-if="join_info.type == 1">
                        <span>今日您还可以抽奖次数：</span>
                        <span class="red">[[join_info.left_amount]]</span>
                    </div>
                    <div class="item" v-else>
                        <span>本次活动抽奖次数剩余：</span>
                        <span class="red">[[join_info.left_amount]]</span>
                    </div>
                    <div class="item start_time">
                        <span>开始时间：</span>
                        <span class="start_time_s red fz_12" style="">[[start_at]]</span>
                    </div>
                    <div class="item end_time">
                        <span>结束时间：</span>
                        <span class="end_time_s red fz_12">[[end_at]]</span>
                    </div>
                </div>
            </div>
            <div class="rule_box">
                <div class="participate">
                    <div class="rule_title">
                        活动奖项
                    </div>
                    <div class="prizeList">
                    	<div v-for="(item,index) in prize_list">
	                    	<div class="prize" v-for="ite in item">
	                    		<p>[[ite.prizeName]]</p>
	                    	</div>
                    	</div>
                    </div>
                </div>
            </div>
            <div class="rule_box">
                <div class="participate">
                    <div class="rule_title">
                        活动说明
                    </div>
                    <div class="intro" v-html= "detail">
                    	亲，祝你好运哦！
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('shop.common.footer')

@endsection
@section('page_js')
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript">
    	var wid= "{{ $wid }}";
    	var activityId= "{{ $eggId }}";
    	var _host = "{{ config('app.source_url') }}";
        var imgUrl = "{{ imgUrl() }}";
        var isBind = {{$__isBind__}};
    </script>
    <script src="{{ config('app.source_url') }}shop/js/eggIndex.js"></script>
@endsection 
