@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/vote_canvass.css"  media="screen">
    <style type="text/css">
    	[v-cloak] {
			display: none;
		} 
    </style>
@endsection
@section('main')
	<div id="app" v-cloak style="padding-bottom: 65px;background: #fff;">
		<div class="baby_index_img" id="top">
			<img src="{{ config('app.source_url') }}shop/images/xw_banner.png" alt="" />
		</div>
		<div class="baby_index" style="padding: 10px;">
			<!--放置富文本-->
		@if(isset($voteData['prize_set']) && $voteData['prize_set'])
		{!! $voteData['prize_set'] !!}
		@else
		<span>请先在后台添加投票活动</span>
		@endif
		
		</div>
		<!--活动规则-->
		<div class="activity_rules" @click="show_rules"></div>
		<!--活动规则弹框-->
		<div v-if="isShowRules">
			<div class="mask" @click="show_rules"></div>
			<div class="activity_rules_msg">
				{!! $voteData['act_rule'] or '' !!}
			</div>
		</div>
	</div>
	<!--底部-->
	<div class="sign_img flex_around">
		<a href="{{ config('app.url') }}shop/vote/index/{{ session('wid') }}/{{ $voteData['id'] }}">
			<div class="icon">

				<img src="{{ config('app.source_url') }}shop/images/xw_shouye.png" alt="" />

				<div>首页</div>
			</div>
		</a>
		<a href="javascript:void(0);">
			<div class="icon">
				<img src="{{ config('app.source_url') }}shop/images/xw_dajiang.png" alt="" />
				<div>大奖设置</div>
			</div>
		</a>
		<a href="{{ config('app.url') }}shop/vote/enroll?id={{ $voteData['id'] }}">
			<div class="icon"> 

				<img src="{{ config('app.source_url') }}shop/images/xw_baoming.png" alt="" />

				<div>报名</div>
			</div>
		</a>
		<a href="{{ config('app.url') }}shop/vote/canvass/{{ $voteData['id'] }}">
			<div class="icon">
				<img src="{{ config('app.source_url') }}shop/images/xw_paihang.png" alt="" />
				<div>拉票秘籍</div>
			</div>
		</a>
	</div>

@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script type="text/javascript">
	var app = new Vue({
	el:"#app",
	data:{
		isShowRules  : false				    //是否展示提示信息
	},
	mounted: function () {
	},
	created:function(){ 
		
	},
	methods:{ 
		show_rules(){
			this.isShowRules = !this.isShowRules
		}
	},
	
}); 
</script>

@endsection




