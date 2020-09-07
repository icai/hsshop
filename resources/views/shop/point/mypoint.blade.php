@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/index_9f4aafcc7131980861310a3d4e522e85.css">
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/myjifen.css"/>
@endsection
@section('main')
    <div class="container ">
        <div class="content">
	        <div class="pointsstore-total">
	            <h5 class="point_tip"><p>我的积分</p><p class="rule_tip">积分说明</p></h5>
	            <h5 class="points-usable">
	                                                      
	            </h5>
            </div>
	        <div class="points-title">
	        	积分变更记录
	        </div>
	        <div id="list_container" data-type="index">
	        </div>
	    </div>    
    </div>
    <!--积分规则弹窗-->
    <div id="Xms3Sq4JR6" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>
    <div id="jifenRule" class="sku-layout sku-box-shadow popup" style="overflow: hidden; position: fixed; z-index: 1000; background: white; top: 100px; left: 50%;width: 300px;display: none;overflow: hidden;border-radius: 20px; margin-left: -150px; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">
		<div class="js-cancel">x</div>
		<div class="point-desc">积分说明</div>
		<div class="point-answer">
			<div><p>Q:</p><p class="jifentxt">什么是积分</p></div>
			<div><p class="atip">A:</p><p class="jifentxt atxt">积分是店铺虚拟货币属性，能购抵现</p></div>
			<div><p>Q:</p><p class="jifentxt">怎样获得积分</p></div>
			<div><p class="atip">A:</p><p class="jifentxt atxt">用户通过签到、分享、购买消费等3种行为获取积分，具体的积分数量由商家设置</p></div>
			<div><p>Q:</p><p class="jifentxt">怎样消费积分</p></div>
			<div><p class="atip">A:</p><p class="jifentxt atxt">用户购买提交订单时，开启积分抵现按钮，系统将自动计算最终支付金额。若用户支付后进行退款操作，积分将不予与返还</p></div>
		</div>
    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
	<script type="text/javascript">
		var imgUrl = "{{ imgUrl() }}";
	</script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/myjifen.js"></script>
@endsection
