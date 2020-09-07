@extends('home.base.head')
@section('head.css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/weixueyuan.css"/>
	<!--当前页面css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/zixunxiangqing.css"/>
@endsection
@section('content')

    @include('home.base.slider')
    <div class="main_content">
		
		<div class="wraper banner">
            {{--<img src="{{ config('app.source_url') }}home/image/zixun_banner.png" >--}}
    	</div>
    	<div class="z-position-wrap">
	    	<div class="z-position">
	        	<img src="{{ config('app.source_url') }}home/image/addr01.png"/>
				<span class="zimap1">当前位置：<a href="/">首页</a>> </span>
				<span class="zimap1">@if($type != 'help')<a href="/home/index/news">会搜云资讯</a>@else <a href="/home/index/helps">帮助中心</a> @endif> </span>
				<span class="zimap2">{{ $detail['title'] }}</span>  
	        </div>
       </div>
        <!--分页一-->
        <div class="zixun">
            <div class="zixun1">
            	<div class="xue-sec1">
	                <div class="xue-right">
	                	<div class="xue-right-title">
		                    <h2>{{$detail['title']}}</h2>
			                <div class="xun-p1">{{$detail['auth']}}<span class="xun-s1">{{$detail['created_at']}}</span></div>
		                </div>
		            	{!! $detail['content'] !!}
	                </div>
                    @if($type != 'help')
	                <div class="z-prz">
	                	<p class="z-przp z-mat30">上一篇：
                        @if($preArr)
                            <a href="/home/index/newsDetail/{{ $preArr['id'] }}/news">{{ $preArr['title'] }}</a>
                        @else
                            无
                        @endif
                        </p>
	                	<p class="z-przp">下一篇：
                        @if($nextArr)
                            <a href="/home/index/newsDetail/{{ $nextArr['id'] }}/news">{{ $nextArr['title'] }}</a>
                        @else
                            无
                        @endif
                        </p>
	                </div>
                    @endif
	            </div>            
            </div>
            <div class="z-abnews">
            	<div class="z-press">
            		<p class="z-newp">相关新闻</p>
            		<ul class="z-ulnew">
                        @forelse($releveNews as $new)
            			<li class="z-linew"><a href="/home/index/newsDetail/{{ $new['id'] }}/news">{{ $new['title'] }}</a></li>
                        @empty
                        暂无相关新闻
                        @endforelse
            		</ul>
            	</div>
            	<div class="z-press">
            		<p class="z-newp">为你推荐</p>
            		<ul class="z-ulnew">
                        @if(isset($adResult['common']) && $adResult['common'])
                        @forelse($adResult['common'] as $ad)
                        <li class="z-linew"><a href="{{ $ad['url'] }}">{{ $ad['title'] }}</a></li>
                        @empty
                            暂无相关推荐信息
                        @endforelse
                        @else
                            暂无相关推荐信息
                        @endif
            		</ul>
            	</div>
            	<div class="z-press">
            		<p class="z-newp">精选广告</p>
            		@if(isset($adResult['very']))
                    <a href="{{ $adResult['very']['url'] }}">
                        <img src="{{ imgUrl() }}{{ $adResult['very']['img'] }}" />
                    </a>
                    @else
                    <p class="z-press-none">暂无广告</p>
                    @endif
            	</div>
            </div>
        </div>
    </div>

@endsection
@section('foot.js')
	<script src="{{ config('app.source_url') }}home/js/newsdetail.js" type="text/javascript" charset="utf-8"></script>
@endsection