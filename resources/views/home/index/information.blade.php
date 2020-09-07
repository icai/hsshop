@extends('home.base.head')
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/weixueyuan.css"/>
@endsection
@section('content')

    @include('home.base.slider')
    <div class="main_content">
        <!--分页一-->
        <div class="xue-fir">
            @forelse($bannerList['data'] as $banner)
            <div class="xue-ful">
			    <img src="{{ imgUrl() }}{{ $banner['img'] }}"/>			             
            </div>
            @empty
            <div class="xue-ful">
                <img src="{{ config('app.source_url') }}home/image/hsyzx.png"/>                      
            </div>
            @endforelse
        </div>
        <div class="z-position">
        	<img src="{{ config('app.source_url') }}home/image/maposi.png"/>
			<span class="zimap1">当前位置：<a href="/">首页</a>-></span>
			<span class="zimap2">会搜云资讯</span>  
        </div>
        <!--分页二-->
        <div class="xue-sec">
            <div class="xue-sec1">
                <div class="xue-left">                	
                    <div class="left1 xue-ldiv">
                        @forelse($type[0] as $val)                        	
                            <p data-id="{{ $val['id']}}" class="leftp1 @if(Request('secCategory')==$val['id'] || in_array($val['id'],$groups))selected @endif">
                            	<img class="right_img" src="{{ config('app.source_url') }}home/image/right.png"/>{{$val['name']}}
	                            <ul class="leftul1" @if(!empty($tag) && $tag==$val['id']) style="display: block;" @endif>
	                                @if(isset($type[$val['id']]))
	                                    @forelse($type[$val['id']] as $v)
	                                    <a data-id="{{ $v['id']}}" class="leftul1-l1  @if($cateId == $v['id'] || in_array($val['id'],$groups))active @endif" href="/home/index/information/secCategory/{{$v['id']}}">
	                                        <li>{{$v['name']}}</li>
	                                	</a>   
	                                    @endforeach
	                                @endif
	                            </ul>
                        	</p>
                        	<p class="list-bor"></p>
                        @endforeach
                    </div>
                </div>
                <div class="xue-right">
                	<p class="right-til">会搜云资讯</p>
                    @forelse($information[0]['data'] as $val)
                    <div class="xue-rdiv1">
                    	<a href="/home/index/detail/{{$val['id']}}/news">
	                        @if($val['source'])
	                            <div class="right-img1">
	                                <img width="246px" height="192px" src="{{ imgUrl($val['source'][0]['l_path']) }}"/>
	                            </div>
	                        @endif
	                        <div class="right-ul1">
	                            <p class="right-l1">{{$val['title']}}</p>
	                            <p class="right-l3">{{$val['auth']}}：{{$val['created_at']}}</p>
	                            <p class="right-l2">{{$val['content']}}</p>
	                        </div>
                        </a>
                    </div>
                    @empty
                    <div class="xue-rdiv1" style="border-bottom:0">
                        该分类下暂无数据
                    </div>
                    @endforelse

                    <nav aria-label="Page navigation">
                      {{$information[1]}}
                    </nav>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}home/js/information.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>

@endsection
