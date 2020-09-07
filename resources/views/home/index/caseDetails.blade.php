@extends('home.base.head')
@section('head.css')
	<!--swiper的css样式-->
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/> 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/caseDetails.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/share.css"/>
@endsection
@section('content')

	@include('home.base.slider')
	<!-- 顶部图片 -->
		<div class="wraper banner">
            {{--<img src="{{ config('app.source_url') }}home/image/shop-banner.png" alt="">--}}
        </div>
        <!-- 当前位置 -->
        <div class="breadcrumb_nav">
            <div>
                <img src="{{ config('app.source_url') }}home/image/addr01.png">
                当前位置：<a href="{{ config('app.url') }}">首页</a>><a href="{{ config('app.url') }}home/index/1/shop"> 案例展示</a>><span> {{ $data['name'] or '' }}</span>
            </div>
        </div>
    <div class="main_content"> 
		<!--分页一-->
		<!-- 案例信息 -->
        <div class="zixun">
            <div class="zixun1">
            	<div class="case-top">
            		<div class="flef pic">
					<img src="{{ imgUrl() }}{{ $data['logo'] or '' }}">
            		</div>
            		<div class="flef mgl20">
            			<p class="csae-fp">{{ $data['name'] or '' }}</p>
            			<p class="csae-cp">作者：{{ $data['author'] or '' }}</p>
            			<p class="csae-cp">{{ $data['type'] or '' }}</p>
            		</div>
            		<div class="frgh">
						<div class="trgh sns-share">
								<span class="csae-cp">分享至：</span>
								<a class="wb" href="javascript:void((function(s,d,e,r,l,p,t,z,c){var%20f='http://v.t.sina.com.cn/share/share.php?appkey=真实的appkey',u=z||d.location,p=['&url=',e(u),'&title=',e(t||d.title),'&source=',e(r),'&sourceUrl=',e(l),'&content=',c||'gb2312','&pic=',e(p||'')].join('');function%20a(){if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=440,height=430,left=',(s.width-440)/2,',top=',(s.height-430)/2].join('')))u.href=[f,p].join('');};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();})(screen,document,encodeURIComponent,'','','图片链接|默认为空','标题|默认当前页标题','内容链接|默认当前页location','页面编码gb2312|utf-8默认gb2312'));">
									<img src="{{ config('app.source_url') }}home/image/wb.png"/>            					
								</a> 
                                @if(isset($data['code']) && $data['code'])
								<div class="wx"><img src="{{ config('app.source_url') }}home/image/wx.png"/></div>
                                @endif
						</div>
						<div class="wx_code"><img src="{{ imgUrl() }}{{ $data['code'] or '' }}" alt=""></div>
						<div class="case-browse">
							<p class="csae-cp"><span>{{ $data['browse_num'] or 0 }}</span><br />人浏览</p>
						</div>
            			
            			<div class="trgh case-pri">
                            @if($data['type'] == 'APP定制')
            				<a class="huoqu" href="/home/index/reserve?type=2">获取报价</a>
                            @elseif($data['type'] == '微信小程序')
                            <a class="huoqu" href="/home/index/reserve?type=3">获取报价</a>
                            @elseif($data['type'] == '分销系统')
                            <a class="huoqu" href="/home/index/reserve?type=1">获取报价</a>
                            @elseif($data['type'] == '微营销总裁班')
                            <a class="huoqu" href="/home/index/reserve?type=4">获取报价</a>
                            @endif
            			</div>
            		</div>
				</div>
				<!-- 案例介绍 -->
            	<div class="case-info">
				{!! $data['intruduce'] !!}
            	</div>
				<!--轮播图 -->
            	<div class="case_swiper">
					<div class="swiper-container">
                        @if($showImgArr)
						<div class="swiper-wrapper">
                            @foreach($showImgArr as $val)
							<div class="swiper-slide bottom_slide"><img src="{{ imgUrl() }}{{ $val }}"/></div>
                            @endforeach
						</div>
						@endif
					</div>
					<!--切换按钮-->
					<div class="swiper-button-prev swiper-button-white" id="swiper-button-prev"></div>
					<div class="swiper-button-next swiper-button-white" id="swiper-button-next"></div>
				</div> 
				<!-- 评论表单 -->
            	<div class="case-sec">
            		<div class="case-lae">
	            		<div class="flef">
	            			<label>昵称:</label>
	            			<input type="" name="nickname" id="nickname" value="" />            			
	            		</div>
	            		<div class="flef fl80">
	            			<label>验证码:</label>
	            			<input type="" name="captcha" id="captcha" value="" /> 
	            			<img id="captcha_img" class="captcha_img" src="{{ captcha_src('flat') }}" onClick="this.src='{{ captcha_src('flat') }}?random='+Math.random();" style="height: 38px;" />          			
	            		</div>
            		</div>
            		<div class="case-cal">
            			<p>点评:</p>
            			<div class="case-tex">
                            <textarea name="remark" id="content" rows="3" cols="26" style="resize: vertical;"></textarea>
                        </div>
            		</div>
            		<button class="case-sub comm_sub" data-id="{{ $data['id'] }}">提交评论</button>
				</div>
				<!-- 热门评论 -->
                @if($commentData['data'])
                <p class="csae-sp pa20">热门评论(<span class="commNum">{{ $commentData['count'] }}</span>)</p>
                <div class="js_comment">
                    @foreach($commentData['data'] as $val)
                    <div class="case-sec comm_mail"> 
                        <p class="disinl">{{ $val['nickname'] }}</p>
                        <span class="csae-cp">{{ $val['created_at'] }}</span>
                        <p>{!! $val['content'] !!}</p>
                    </div>
                    @endforeach
                </div>
                @endif
			</div>
			

			<!-- 相关新闻 -->
            <div class="z-abnews">
            	<div class="z-press">
            		<p class="z-newp">相关新闻</p>
            		<ul class="z-ulnew">
                        @forelse($news as $val)
						<li class="z-linew"><a href="/home/index/newsDetail/{{ $val['id'] }}/news">{{ $val['title'] }}</a></li>
                        @empty
                        <li>暂无数据</li>
                        @endforelse
            		</ul>
				</div>
				<!-- 为你推荐 -->
            	<div class="z-recom">
            		<p class="z-newp">为你推荐</p>
            		<ul class="z-ulnew">
                		@if($adResult)
                        @forelse($adResult['common'] as $ad)
                        <li class="z-linew"><a href="{{ $ad['url'] }}">{{ $ad['title'] }}</a></li>
                        @empty
                            暂无相关推荐信息
                        @endforelse
                        @else
                        <li>暂无相关推荐信息</li>
                        @endif
            		</ul>
				</div>
				<!-- 精选广告 -->
            	<div class="z-adver">
					<p class="z-newp">精选广告</p>
					<div class="adv-box">
						@if(isset($adResult['very']))
						<a href="{{ $adResult['very']['url'] }}">
							<img src="{{ imgUrl() }}{{ $adResult['very']['img'] }}" style="width: 200px;height: 100px;"/>
						</a>
						@else
						<p>暂无广告</p>
						@endif
					</div>
                    
            </div>
        </div>
    </div>
@endsection
@section('foot.js')

<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/caseDetails.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/share.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    var commNum = "{{ $commentData['count'] }}";
	var id = "{{ $data['id'] }}";
</script>
@endsection