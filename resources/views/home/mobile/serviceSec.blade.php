@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/service.css">  
@endsection



@section('content')
	<div class="content">
		<div class="topp-div sub-app">
			<img src="{{ config('app.source_url') }}mobile/images/app-ban.jpg" width="100%" height="100%"/>
		</div>
		<div class="page-box">
			<div class="page">			
				<form class="service">
					<div class="s_cus">
						<div class="cus-app">
							@if($type == 1)
                            <span class="appd">智慧分销</span>
							@elseif($type == 2)
							<span class="appd">APP定制</span>
							@elseif($type == 3)
							<span class="appd">微信小程序</span>
							@elseif($type == 4)
							<span class="appd">微信营销总裁班</span>
							@elseif($type == 5)
							<span class="appd">微信商城</span>
							@endif							
						</div>						
					</div>
					<div class="s_input">
						<span>您的称呼</span>
						<div class="bor">|</div>
						<input type="" name="" class="get-focus" value="" placeholder="请写下您的姓名" />
					</div>
					<div class="s_input">
						<span>手机号码</span>
						<div class="bor">|</div>
						<input type="" name="" class="get-focus" value="" placeholder="请输入正确的手机号" />
					</div>
					<div class="s_input">
						<span>所属行业</span>
						<div class="bor">|</div>
						<input type="" name="" class="get-focus" value="" placeholder="例:电商、物流、零售..." />
					</div>
					<input type="hidden" name="type" value="{{ $type }}">
				</form>
				<div>
					<input type="button" class="start_reservation" value="开始预约" />
				</div>
				<div class="phone_div">
					<a style="color: #fff;" href="tel:{{$CusSerInfo['phone']}}" class="phone_reservation">
						<img src="{{ config('app.source_url') }}mobile/images/ser_41.png" />
						<span>电话预约</span>
					</a>
				</div>			
			</div>
		</div>		
		<div class="bott-border">
			<div class="bott-div sub-app">
				<p class="bott-tit">定制APP, 为什么要选择会搜云？</p>
				<p class="sp-blod">“一体化”优质方案定制</p>
				<p>原生APP + H5网页版 + 微信小程序一并打通，PC端，iOS端，安卓，移动端后台四位一体，根据您的需求和行业特性，获取更多的流量，增强用户的粘性。</p>
				<p class="sp-blod">个性化应用模块选择</p>
				<p>包括直播，拼团，秒杀，领券，潮流营销工具等200多项实用电商功能，一应俱全，打造个性化平台。</p>
				<p class="sp-blod">精细化大数据运营</p>
				<p>一个后台，一并管理APP、H5、小程序上的店铺。客户管理、订单管理、营销推送和用户分析等强大功能，有效收集、监控消费者行为，用大数据加强对消费者触达。</p>
				<p class="sp-blod">低投入高品质云管理模式</p>
				<p>基于云计算的创新模式，统一运维管理，提高资源利用率，满足流量快速增长，为企业省钱、省力、省心。</p>
				<p class="sp-blod">一对一“一站式”高效服务</p>
				<p>1V1客户团队，设计团队，研发团队，名师营销讲堂，提供APP的策划、研发、推广、运营、售后服务等一站式等APP定制外包服务，致力于为每一位客户打造满足营销创新和市场需求的解决方案。</p>				
			</div>	
		</div>		
		<!--弹出层-->		
		<div class="sel_suc">
			<div class="sel_close sel_close2">
				
			</div>
			<div class="suc_tit">提交成功!</div>
			<p>专业的客户代表将在第一时间为您服务，请您耐心等候，也可直接电话预约~~~</p>
			<!--<span><b>3</b>s</span>-->
		</div>
	</div> 

@endsection


@section('js')
<script src="{{ config('app.source_url') }}mobile/js/service.js"></script>
<script type="text/javascript">
	var sel_index = {{ $type }};
</script>
@endsection