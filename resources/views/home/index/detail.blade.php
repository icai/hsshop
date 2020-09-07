@extends('home.base.head')
@section('head.css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/weixueyuan.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/zixunxiangqing.css"/>
@endsection
@section('content')

    @include('home.base.slider')
    <div class="main_content">
    	<div class="map-banner">
    		<p class="banner-p font18">@if($type != 'help')资讯详情 @else 帮助中心 @endif</p>
    		<p class="banner-p font8">@if($type != 'help')INFORMATION DETAJLS @else HELP DETAJLS @endif</p>
    	</div>   
    	<div class="z-position">
        	<img src="{{ config('app.source_url') }}home/image/maposi.png"/>
			<span class="zimap1">当前位置：<a href="/">首页</a>-></span>
			<span class="zimap1">@if($type != 'help')<a href="/home/index/information">会搜云资讯</a>@else <a href="/home/index/helps">帮助中心</a> @endif->{{ $detail['type_path'] }}</span>
			<span class="zimap2">{{ $detail['title'] }}</span>  
        </div>
        <!--分页一-->
        <div class="zixun">
            <div class="zixun1">
            	<div class="xue-sec1">
	                <div class="xue-left">
	                    <div class="left1 xue-ldiv">
                            @if($type == 'news')
	                        @forelse($typeData[0] as $val)
                            <p class="leftp1 @if(in_array($val['id'],$groups))selected @endif ">{{$val['name']}}</p>
                            <ul class="leftul1" @if(!empty($tag) && $tag==$val['id']) style="display: block;" @endif>
                                @if(isset($typeData[$val['id']]))
                                    @forelse($typeData[$val['id']] as $v)
                                    <a class="leftul1-l1  @if(in_array($val['id'],$groups))color1 @endif " href="/home/index/information/secCategory/{{$v['id']}}">
                                        <li>{{$v['name']}}</li>
                                    </a>
                                    @endforeach
                                    @endif
                            </ul>
                            @endforeach
                            @else
                            @forelse($helpChildData as $help)
                            <p class="leftp1 @if(in_array($help['id'],$groups))selected @endif ">
                                <img class="help_img @if(in_array($help['id'],$groups))help_img_a @endif"   src="{{ config('app.source_url') }}home/image/right.png"/><span>{{$help['name']}}</span>
                                {{--<a href="/home/index/helps?secCategory={{ $help['id'] }}" class="leftp1 @if(in_array($help['id'],$groups))hover @endif ">{{$help['name']}}</a>--}}
                                <ul class="leftul1" @if(in_array($help['id'],$groups))style="display: block ;" @else style="display: none;" @endif>
                                    @if($help['erji'])
                                        @foreach($help['erji'] as $val)
                                            <a @if($val['id'] == $detail['info_type'])style="color: #999999 ;" @endif class="leftul1-l1" data-pid="{{ $help['id'] }}" data-id="{{ $val['id'] }}" href="/home/index/helps?info_type={{ $val['id'] }}&Pid={{ $help['id'] }}">
                                                <li>{{ $val['name'] }}</li>
                                            </a>
                                        @endforeach
                                    @endif
                                </ul>
                            </p>
                            @empty
                            @endforelse
                            @endif

	                    </div>
	                </div>
	                <div class="xue-right">
	                    <h2>{{$detail['title']}}</h2>
		                <div class="xun-p1">{{$detail['auth']}}<span class="xun-s1">{{$detail['created_at']}}</span></div>
		                {!! $detail['content'] !!}
	                </div>
                    @if($type != 'help')
	                <div class="z-prz">
	                	<p class="z-przp z-mat30">上一篇：
                        @if($preArr)
                            <a href="/home/index/detail/{{ $preArr['id'] }}/news">{{ $preArr['title'] }}</a>
                        @else
                            无
                        @endif
                        </p>
	                	<p class="z-przp">下一篇：
                        @if($nextArr)
                            <a href="/home/index/detail/{{ $nextArr['id'] }}/news">{{ $nextArr['title'] }}</a>
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
            			<li class="z-linew"><a href="/home/index/detail/{{ $new['id'] }}/news">{{ $new['title'] }}</a></li>
                        @empty
                        暂无相关新闻
                        @endforelse
            		</ul>
            	</div>
            	<div class="z-recom">
            		<p class="z-newp">为你推荐</p>
            		<ul class="z-ulnew">
            			@if(isset($adResult['common']))
                        @forelse($adResult['common'] as $ad)
                        <a href="{{ $ad['url'] }}">
                            <li class="z-pli">
                                <img width="80" height="55" src="{{ imgUrl() }}{{ $ad['img'] }}"/>
                                <span>{{ $ad['title'] }}</span>
                            </li>
                        </a>
                        @empty
                            暂无相关推荐信息
                        @endforelse
                        @else
                            暂无相关推荐信息
                        @endif
            		</ul>
            	</div>
            	<div class="z-adver">
            		<p class="z-newp">精选广告</p>
            		@if(isset($adResult['very']))
                    <a href="{{ $adResult['very']['url'] }}">
                        <img width="245" height="158" src="{{ imgUrl() }}{{ $adResult['very']['img'] }}"/>
                        <p>{{ $adResult['very']['title'] }}</p>
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
<script src="{{ config('app.source_url') }}home/js/detail.js" type="text/javascript" charset="utf-8"></script>
@endsection