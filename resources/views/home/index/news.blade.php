@extends('home.base.head')
@section('head.css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/weixueyuan.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css" />
<!--当前页面css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/zixunlist.css" />
@endsection
@section('content')

@include('home.base.slider')
<div class="main_content">
    <!--分页一-->
    <div class="wraper banner">
        {{--<img src="{{ config('app.source_url') }}home/image/home-news-bannber-20200810.png" >--}}
    </div>
    <!--<div class="xue-fir">
            @forelse($bannerList['data'] as $banner)
            <div class="xue-ful">
			    <img src="{{ imgUrl() }}{{ $banner['img'] }}"/>			             
            </div>
            @empty
            <div class="xue-ful">
                <img src="{{ config('app.source_url') }}home/image/hsyzx.png"/>                      
            </div>
            @endforelse
        </div>-->
    <div class="z-position-wrap">
        <div class="z-position">
            <img src="{{ config('app.source_url') }}home/image/addr01.png" />
            <span class="zimap1">当前位置：<a href="/">首页</a>> </span>
            <span class="zimap2">会搜云资讯</span>
        </div>
    </div>
    <!--分页二-->
    <div class="xue-sec">
        <div class="xue-sec1">
            <!--左侧菜单-->
            <div class="xue-left">
                <div class="left1 xue-ldiv">
                    <div class="left_search">
                        <div class="left_search_warp">
                            <input class="left_search_content" type="text" name="" id="searchVal" value="{{ request('keywords') }}" placeholder="关键词搜索" />
                            <img class="searchBtn" src="{{ config('app.source_url') }}home/image/zixun_left_search.png" />
                        </div>
                    </div>
                    @forelse($type as $val)
                    <p data-id="{{ $val['id']}}" class="leftp1">
                        {{$val['name']}}<img class="right_img_1" src="{{ config('app.source_url') }}home/image/zixun_left_1.png" />
                        <ul class="leftul1" style="display: none;">
                            @if(isset($val['child']) && $val['child'])
                            @forelse($val['child'] as $v)
                            <a data-id="{{ $v['id']}}" data-pid="{{ $val['id'] }}" class="leftul1-l1">
                                <li>{{$v['name']}}</li>
                            </a>
                            @endforeach
                            @endif
                        </ul>
                    </p>
                    <div class="list-bor"></div>
                    @endforeach
                </div>
            </div>
            <!--左侧菜单end-->
            <!--资讯列表-->
            <div class="xue-right">
                @forelse($information['data'] as $val)
                <div class="xue-rdiv1">
                    <a href="/home/index/newsDetail/{{$val['id']}}/news">
                        @if(isset($val['source']) && $val['source'])
                        <div class="right-img1">
                            <img width="240px" height="220px" src="{{ imgUrl($val['source']['l_path']) }}" />
                        </div>
                        @else
                        <div class="right-img1">
                            <img width="240px" height="220px" src="" />
                        </div>
                        @endif
                        <div class="right-ul1">
                            <p class="right-l3">{{$val['created_at']}}</p>
                            <p class="right-l1">{{$val['title']}}</p>
                            <p class="right-l2">{{$val['content']}}</p>
                            <p class="right-l4"><img class="right-l4-1" src="{{ config('app.source_url') }}home/image/zixun_more_1.png" /><img class="right-l4-2" src="{{ config('app.source_url') }}home/image/zixun_more_2.png" />More</p>
                        </div>
                    </a>
                </div>
                @empty
                <div class="xue-rdiv1-none" style="border-bottom:0">
                    该分类下暂无数据
                </div>
                @endforelse
                <nav aria-label="Page navigation" class="page-bottom">
                    {{$page}}
                </nav>
            </div>
            <!--资讯列表end-->
        </div>
    </div>

</div>
@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}home/js/information.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
@endsection