@extends('home.base.head')
<title>{{$title}}</title>
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/dinggou2.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/searchXCX.css"/>
@endsection
@section('content')  
    <div class="main_content">
    	<div class="change-title">
    		<div class="change-banner">
    			<img src="{{ config('app.source_url') }}home/image/banner03.jpg"/>    		
    		</div>
    	</div>
        <div class="ding-box">
            <div class="ding-xiangmu">
                <form id="myform">
                    <div class="ding-ul">
                        <h1 class="ding-xp2">看看您的小程序是否被抢先注册</h1>
                    </div>
                    <div class="ding-input1">
                        <p class="dinp1"><b>小程序名称</b></p>
                        <input type="text" name="title" placeholder="请输入您想注册的小程序名称" class="chenghu get-focus" value="" />
                    </div>
                    <div class="ding-input2">
                        <p class="dinp1"><b>您的称呼</b></p>
                        <input type="text" name="name" placeholder="请输入您的称呼" class="haoma get-focus" value="" />
                    </div>
                    <div class="ding-input3">
                        <p class="dinp1"><b>联系方式</b></p>
                        <input type="text" name="phone" placeholder="请输入您的手机号" class="hangy get-focus" value="" />
                    </div>
                    <input type="hidden" name="type" value="">
                    <div class="ding-input4">
                        <input style="background: #006285; border: 0; width: 100%; padding-left: 0;" type="button" name="yuyue" class="yuyue" value="立即查询" />
                    </div>
                </form>
            </div>
            <!-- 查询轮播 -->
			<div class="searchHistory swiper-container">
		        <div class="history swiper-wrapper">
		            
		        </div>
		   </div>
        </div>
    </div>
@endsection
@section('foot.js')
	<script type="text/javascript">
		var searchHistory = {!! json_encode($history) !!};
		console.log(searchHistory)
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
		        html +='<span class="searchTitle">&nbsp&nbsp'+searchHistory[i][j].title+'****</span>'
		       	html +='</p>'
			}
	        html +='</div>';
	        $(".history").append(html);
		}
	</script>
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/searchXCX.js" type="text/javascript" charset="utf-8"></script>
@endsection