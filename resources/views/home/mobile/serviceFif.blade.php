@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/service.css">  
@endsection



@section('content')
	<div class="content">
		<div class="topp-div sub-order">
			<img src="{{ config('app.source_url') }}mobile/images/code-ban.jpg" width="100%" height="100%"/>
		</div>
		<div class="page-box">
			<div class="page">			
				<form class="service">
					<div class="s_cus">
						<div class="cus-app">
							<span class="appd">获取报价</span>
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
					<!-- <div class="s_input">
						<span>产品需求</span>
						<div class="bor">|</div>
						<select>
							<option>APP定制</option>
							<option>小程序定制</option>
							<option>微商城定制</option>
							<option>分销系统定制</option>
							<option>微营销总裁班</option>
						</select>
					</div> -->
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
			<div class="bott-div sub-order">
				<p class="bott-tit">开发小程序，为什么要选择会搜云？</p>
				<p class="sp-blod">零门槛制作</p>
				<p>无需编程知识，纯图形化操作，免去漫长的开发时间和高昂的成本。</p>
				<p class="sp-blod">一体化管理</p>
				<p>原生APP + 微站 + 微信小程序一并打通，一体化、实时在线管理，提高运营效率。</p>
				<p class="sp-blod">O2O场景化应用</p>
				<p>用户扫一扫或者搜一下即可打开应用，无须安装、触手可及、用完即走、无须卸载，作为微信的一部分用户使用不需再注册，使用门槛大大降低，提升了用户的体验感。</p>
				<p class="sp-blod">公众号关联</p>
				<p>同一主体的小程序和公众号可以相互关联，相互跳转，公众号提供内容运营与线上推广，小程序则提供用户所需服务，两者结合，实现内容和服务的功能互补。</p>
				<p class="sp-blod">支持页面分享</p>
				<p>支持分享页面到微信群和微信好友，依托微信9亿+用户以及足够强大的用户黏性，通过社交推荐，获得更高的流量，提升品牌关注度。</p>				
				<p class="sp-blod">可视化编辑</p>
				<p>在线拖拉组件，排版布局自主选定，成熟的模板套用，一键生成小程序源码包。</p>
				<p class="sp-blod">多行业适用</p>
				<p>适用于数码、电器、家居、服装、运动、母婴、食品、汽车等行业。</p>
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
	var sel_index = 5;
</script>
</script>
@endsection