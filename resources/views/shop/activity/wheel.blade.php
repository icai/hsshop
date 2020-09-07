@extends('shop.common.marketing')
@section('head_css')
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/wheel_h56nr4f9.css">
@endsection
@section('main')
<div class="content">
	<div class="top">{{$data['title']}}</div>
	<div class="main">
		<div class="out">
			<img id="lotteryBtn" src="{{ config('app.source_url') }}shop/images/rotatePlan.png">
			<div class="in">
				<img id="btnstart" src="{{ config('app.source_url') }}shop/images/activity-lottery-2.png">
			</div>
			<div class="posiab">					
				<img calss="" src="{{ config('app.source_url') }}shop/images/wheel-en.png">
			</div>
			<a href="/shop/activity/myGift/{{ session('wid') }}" style="position: absolute;right: 35px;bottom: -4px;color:#fff">我的奖品</a>
		</div>
		<div class="clear">		
		</div>
		<div class="info prize_set">
			<div class="border">
				<div class="title">奖项设置</div>
				<div class="">
					@forelse($data['prizeData'] as $val)
						<p>
							@if($val['grade'] == 1)
								一
							@elseif($val['grade'] == 2)
								二
							@elseif($val['grade'] == 3)
								三
							@endif
								等奖： <span>
								@if($val['type'] == 1)
									{{$val['content']}} 积分
									@elseif($val['type'] == 2)
									{{$val['coupon']['title'] or ''}}
									@elseif($val['type'] == 3)
									{{$val['content']}}
									@elseif($val['type'] == 4)
									{{$val['title']}}
								@endif
								</span></p>
					@endforeach
				</div>
			</div>
		</div>
		<div class="info activity_info">
			<div class="border">
				<div class="title">活动说明</div>

				<div class="">
					{{--<p>本次活动@if($data['rule'] == 1) 每人每天可以转动{{$data['times']}}次@else 每人转动{{$data['times']}}次 @endif,你已经转了 <num>{{$data['count']}}</num> 次,如果次数没用完，请重新进入本页面可以再转，下一个中奖的可能就是你！</p>--}}
					<p>{{$data['descr']}}</p>
				</div>
			</div>
		</div>
		<div class="info activity_time">
			<div class="border">
				<div class="title">活动时间</div>
				<div class="">
					<p>开始时间：{{$data['start_time']}}</p>
					<p>结束时间：{{$data['end_time']}}</p>
					{{--<p>兑奖请联系热线电话： 010-110110110</p>--}}
					<p>本活动最终解释权归商家所有</p>
				</div>
			</div>
		</div>
	</div>
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script type="text/javascript">
		var wid = {{session('wid')}};
		var data = {!! json_encode($data) !!};
		var num_times={{$data['count']}};
		var _host = "{{ config('app.source_url') }}";
		var imgUrl = "{{ imgUrl() }}";
		var isBind = {{$__isBind__}}; //是否需要绑定手机号1为需要绑定0不需要
		var id = data.id;
		$(".activity_info p num").text(data.count); //已抽奖次数
	</script>
	<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
	<script src="{{ config('app.source_url') }}static/js/jQueryRotate.2.2.js"></script>
	<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/wheel_h56nr4f9.js"></script>
@endsection