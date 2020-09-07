@extends('shop.common.template')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/distribute_bebutor.css">
@endsection
@section('main') 
<div class="container " id="container">
	<div class="distribute_agreement">
		<h1 class="title">分销协议说明</h1>
		<p>1.用户只有先确定成为店铺分销客，才能通过分享分销商品获得对应佣金，未成为分销客分享商品，即使用户购买并支付，你也不能获得对应佣金。</p>

		<p>2.成为分销客后，<span style="font-weight:bold;">你可以在店铺-会员中心-我的分销-分销商品选取合适的商品进行分销活动</span>，只要促成用户购买即可获得对应佣金。</p>

		<p>3.成为分销客后，已获得佣金分为可提现和待提现两个部分，可提现金额你可以根据提现指引提取到你的银行卡或支付宝账号；待提现金额需要等用户确定收货或收货后7天才能提现，所有的提现申请均需要商家审核（具体情况请以商家设置规则为准）。</p>

		<p>4.对于分销客存在恶意刷单或获利的情况，商家查实核对后有权直接清退其分销客身份或冻结账户提现金额。</p>

		<p>5.请遵守国家及地方法律法规规定，不对社会造成危害。</p>

		<p>6.协议内容如有补充或更新，不再重复通知，即用户已知且同意。</p>
	</div>
</div>
<!-- 当前页面js -->
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript">
	var wid = "{{session('wid')}}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/distribute_bebutor.js"></script>
@endsection