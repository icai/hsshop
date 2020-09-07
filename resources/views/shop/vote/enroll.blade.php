@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/enroll.css"  media="screen">
@endsection
@section('main')  
	<div class="enroll" id="app" v-cloak>
		<div class="baby_img">
			<div class="title">上传一张宝宝最"萌"的照片：</div>
			<div>
				<img :src="images" alt="" v-if="images"/>
				<img src="{{ config('app.source_url') }}shop/images/baby_add.png" alt="" v-else/>
				<input id="btnUp" type="file" multiple="multiple" name="" class="absolute" value="" @change="file(event)"/>
				
			</div>
		</div>
		<div class="baby_message">
			<div class="baby_more title">让大家更多的了解宝宝：</div>
			<div class="baby_name_sex">
				<div class="baby_name">
					<label>
						<span>宝宝姓名：</span>
						<input type="text" placeholder="输入宝宝的姓名,切勿填写小名哦！" v-model="name" class="baby_names"/>
					</label>
				</div>
				<div class="baby_sex">
					<span>宝宝性别：</span>
					<label>
						<input type="radio" v-model="sex" value="1"/>
						<span>女宝宝</span>
					</label>
					<label>
						<input type="radio" v-model="sex" value="2">
						<span>男宝宝</span>
					</label>
				</div>
			</div>
		</div>
		<div class="contact_way">
			<div class="contact_font title">留下联系方式,兑奖是需要哦：</div>
			<div class="family">
				<div class="family_mother_father">
					<span>我是：</span>
					<label>
						<input type="radio"  v-model="parent_name" value="妈妈"/>
						<span>妈妈</span>
					</label>
					<label>
						<input type="radio"  v-model="parent_name" value="爸爸"/>
						<span>爸爸</span>
					</label>
				</div>
				<div class="call_phone">
					<label>
						<span>我的联系方式：</span>
						<input type="text" placeholder="填写准确哦，兑奖时需要哦！" v-model="phone"/>
					</label>
				</div>
			</div>
		</div>
		<div class="apply">
			<div class="apply_success">报名成功后：</div>
			<div style="display: flex;">
				<div style="padding-top: 25px;">
					<p>长按识别二维码：</p>
					<p>添加客服微信</p>
				</div>
					<img src="{{ config('app.source_url') }}shop/images/xw_wxerweima3.png" alt="" style="height: 90px;width: 90px;"/>
			</div>
		</div>
		<div class="agree_comfrim">
			<div>提交成功后不可修改，请确认你的信息无误</div>
			<div @click="agreeComfrim" class="agreett">我要提交</div>
			<div class="agreet" @click="agreeComfrimt">已经报名，去拉票吧！</div>

		</div>
		<!--提示信息-->
		<div class="hint" v-text="hint" v-if="isShowHint"></div>
		
		<!--活动规则-->
		<!--活动规则弹框-->
		<div v-if="isShowRules">
			<div class="mask" @click="show_rules"></div>
			<div class="activity_rules_msg">
				{!! $voteData['act_rule'] or '' !!}
			</div>
		</div>
		<!--投票成功的弹框-->
		<div v-if="isShowVoteSuccess">
			<div class="mask" @click="vote_ticket"></div>
			<div class="vote_success">
				<img class="close_img" @click="closeImg" src="{{ config('app.source_url') }}shop/images/close@3x.png">
				<div class="vote_image">
					<img :src="img">
				</div>
				<div class="you_jia">
					<div>长按图片进行保存</div>
					<div>转发朋友圈和微信群进行拉票吧~</div>
					<a class="join" href="{{ config('app.url') }}shop/vote/enroll?id={{ request('id') }}">为我家萌宝报名</a>
					

				</div>
			</div>
		</div>
		
		<!--投票弹框-->
		<div v-if="isShowVoteSuccess_s">
			<div class="mask" @click="closeImg_s"></div>
			<div class="vote_success">
				<img class="close_img" @click="closeImg_s" src="{{ config('app.source_url') }}shop/images/close@3x.png">
				<div class="vote_image">
					<img src="{{ $headImg }}">
				</div>
				<div class="you_jia" style="text-align: center;">
					<div style="font-size: 18px;font-weight: 800;color: red;" >请长按图片进行保存</div>
					<div style="font-weight: 800;">公平起见,通过图片拉票才有效</div>
					<a class="join" href="{{ config('app.url') }}shop/vote/enroll?id={{ request('id') }}">为我家萌宝报名</a>
					
				</div>
			</div>
		</div>
		
		<!--底部-->
		<div class="sign_img flex_around">
			<a href="{{ config('app.url') }}shop/vote/index/{{ session('wid') }}/{{ request('id') }}">
				<div class="icon">
					<img src="{{ config('app.source_url') }}shop/images/xw_shouye2.png" alt="" />
					<div>首页</div>
				</div>
			</a>
			<a href="{{ config('app.url') }}shop/vote/prizes/{{ request('id') }}">
				<div class="icon">
					<img src="{{ config('app.source_url') }}shop/images/xw_dajiang.png" alt="" />
					<div>大奖设置</div>
				</div>
			</a>
			<a href="javascript:void(0);">
				<div class="icon"> 
					<img src="{{ config('app.source_url') }}shop/images/bm-x.png" alt="" />
					<div>报名</div>
				</div>
			</a>
			<a href="{{ config('app.url') }}shop/vote/canvass/{{ request('id') }}">
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
	var wid = "{{ session('wid') }}";
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var _token = "{{ csrf_token() }}";
    var id = '{{ request("id") }}';
    var wid = '{{ session("wid") }}';
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/jquery-2.1.4.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/ajaxupload.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/enroll.js"></script>
@endsection