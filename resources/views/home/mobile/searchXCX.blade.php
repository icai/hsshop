@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/searchXCX.css">  
@endsection

@section('content')
	<div class="content">
		<div class="topp-div sub-order">
			<img src="{{ config('app.source_url') }}mobile/images/searchXCX.jpg" width="100%" height="100%"/>
		</div>
		<div class="page-box">
			<div class="page">			
				<form class="service">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="s_cus">
						<div class="cus-app">
							<span class="appd">看看您的小程序是否被抢先注册</span>
						</div>						
					</div>
					<div class="s_input">
						<span>小程序名称</span>
						<div class="bor">|</div>
						<input type="text" name="title" class="get-focus" value="" placeholder="请输入小程序名称" />
					</div>
					<div class="s_input">
						<span>您的称呼</span>
						<div class="bor">|</div>
						<input type="text" name="name" class="get-focus" value="" placeholder="请输入您的称呼" />
					</div>
					<div class="s_input">
						<span>联系方式</span>
						<div class="bor">|</div>
						<input type="number" name="phone" class="get-focus" value="" placeholder="请输入正确的手机号码" />
					</div>
				</form>
				<div>
					<input type="button" class="start_reservation" value="立即查询" />
				</div>
				<div class="phone_div">
					<a style="color: #fff;" href="tel:{{$CusSerInfo['phone']}}" class="phone_reservation">
						<img src="{{ config('app.source_url') }}mobile/images/ser_41.png" />
						<span>电话预约</span>
					</a>
				</div>
				<!-- 查询记录轮播开始 -->
				<div class="searchHistory swiper-container hide">
			        <div class="history swiper-wrapper">
			            
			        </div>
			    </div>
			    <!-- 查询记录轮播结束 -->		
			</div>
		</div>		
			
		<!--弹出层-->
		<div class="sel_suc">
			<div class="sel_close sel_close2">
				
			</div>
			<div class="suc_tit">提交成功!</div>
			<p>专业的客户代表将在第一时间为您服务，请您耐心等候，也可直接联系客服~~~</p>
			<!--<span><b>3</b>s</span>-->
		</div>
	</div> 
	
@endsection

@section('js')
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}mobile/js/searchXCX.js"></script>
<script type="text/javascript">
	var sel_index = 3;
	var searchHistory = {!! json_encode($history) !!};
	if(searchHistory.length > 0){
		$(".searchHistory").removeClass("hide");
	}
	for(var i = 0;i < searchHistory.length;i ++){
		var html = "";
		html += '<div class="swiper-slide">'
		for(var j = 0;j < searchHistory[i].length;j ++){
	        html +='<p class="historyItem">'
	        html +='<span class="time">刚刚</span>'
	        html +='<span class="telphone">'+searchHistory[i][j].phone+'</span>'
	        html +='查询了'
	        html +='<span class="searchTitle">'+searchHistory[i][j].title+'****</span>'
	       	html +='</p>'
		}
        html +='</div>';
        $(".history").append(html);
	}
</script>
@endsection