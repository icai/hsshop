@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/vote_index.css"  media="screen">
@endsection
@section('main')  
	<div class="baby_index" id="app" v-cloak>
		<!--宝贝首页图片-->
		<div class="baby_index_img" id="top">
			<img src="{{ config('app.source_url') }}shop/images/xw_banner.png" alt="" />
		</div>
		<!--评选倒计时-->
		<div class="time_down">
			<span>距离评选结束还差：</span>
			<span class="span_color" v-text="days"></span> 天
			<span class="span_color" v-text="hours"></span> 小时
			<span class="span_color" v-text="minutes"></span> 分钟
		</div>
		<!--宝贝号码-->
		<div class="baby_number">
			<span style="font-size: 16px;">萌宝号码：</span>
			<input type="text" v-model="id"/>
		</div>
		
		<div class="vote_baby">
			<div>对于同一名参赛宝宝每人每天{{ $voteData['many_ticket'] }}次投票机会，每天可为{{ $voteData['many_people'] }}名宝宝投票</div>
			<div @click="vote_ticket">投票</div>
		</div>
		<!--宝贝报名-->
		<div class="baby_apply">
			<div class="my_apply"><a href="{{ config('app.url') }}shop/vote/enroll?id={{ $voteData['id'] }}">我要报名</a></div>
			<div class="flex_around apply_list">
				<div class="have_sign_up y_apply">
					<div></div>
					<div>已报名</div>
					<div>{{ count($enrollList) }}</div>
				</div>
				<div class="have_sign_up people_number">
					<div></div>
					<div>投票人次</div>
					<div>{{ $num }}</div>
				</div>
				<div class="have_sign_up prize_number">
					<div></div>
					<div>参赛号码</div>
					@if($currentNumber == 0)
					<div>快去报名吧！</div>
					@else
					<div>{{ $currentNumber }}</div>
					@endif
				</div>
				<div class="have_sign_up award">
					<div></div>
					<div>距离大奖还差</div>
					<div>{{ $maximum }}票</div>
				</div>
			</div>
		</div>
		<!--最近参赛，投票排行-->
		<div class="baby_list">
			<div class="flex_around two_item">
				<div @click="recent_entry" :type="color_type" :class="color_type == 1 ? 'white_color' : ''"><span>最近参赛</span></div>
				<div @click="vote_rank" :type="color_type" :class="color_type == 2 ? 'white_color' : ''"><span>投票排行</span></div>
			</div>
			<div v-if="color_type == 2" class="search">
				<img src="{{ config('app.source_url') }}shop/images/xw_sousuo.png" alt="" />
				<input type="text" placeholder="输入名字或者号码搜索" v-model="search" @keyup.13="enter" @blur="blur"/>
			</div>
			<div class="baby_picture">
				<div class="baby_message" v-for="item in memberList">
					<div style="height:180px;overflow:hidden;text-align:center;display: flex;align-items: center;    justify-content: center;">
						<img :src="item.head_img" alt="" :height="Heigth"/>
					</div>
					<div class="flex_around">
						<div class="people_name">
							<div>@{{item.name}}</div>
							<div>@{{item.vote_num}}票</div>
						</div>
						<div class="click_vote" @click="get_vote_num(item.id)"><a href="#top">点击投票</a></div>
					</div>
					<div class="bh">@{{item.id}}</div>
				</div>
				
			</div>
		</div>
		<!--回到顶部-->
		<div class="top">
			<a href="#top">
				<img src="{{ config('app.source_url') }}shop/images/xw_jiantou.png" alt="" />
			</a>
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
		<!--投票成功的弹框-->
		<div v-if="isShowVoteSuccess">
			<div class="mask" @click="hide_ticket"></div>
			<div class="vote_success">
				<img class="close_img" @click="closeImg" src="{{ config('app.source_url') }}shop/images/close@3x.png">
				<div class="vote_image">
					<img :src="img">
				</div>
				<div class="you_jia">
					<div>长按图片进行保存</div>
					<div>转发朋友圈和微信群进行拉票吧~</div>
					<a class="join" href="{{ config('app.url') }}shop/vote/enroll?id={{ $voteData['id'] }}">为我家萌宝报名</a>
					
				</div>
			</div>
		</div>
		
		<!--底部-->
	<div class="sign_img flex_around">
		<a href="javascript:void(0);">
			<div class="icon">
				<img src="{{ config('app.source_url') }}shop/images/xw_shouye.png" alt="" />
				<div>首页</div>
			</div>
		</a>
		<a href="{{ config('app.url') }}shop/vote/prizes/{{ $voteData['id'] }}">
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
	</div>
	
	
  
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";//域名
    var time ="{{ $remaining_time }}";//距离结束倒计时时间搓
    var _token = "{{ csrf_token() }}";
    var id = '{{ $voteData["id"] }}';//活动id
    var wid = '{{ session("wid") }}';
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/js/vote_index.js"></script>
@endsection