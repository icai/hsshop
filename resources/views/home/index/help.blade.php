@extends('home.base.head')
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/help.css"/>
@endsection
@section('content')
    @include('home.base.slider')
    <!--帮助中心 搜索-->
    <div class="help_top">
        <div class="help_top_content clearfix">
            <div class="help_top_left fl">
                帮助中心
            </div>
            <div class="help_top_right fr">
                <div class='clearfix'>
                    <input class='right_inp fl' placeholder="请输入需要搜索的关键字" type="text">
                    <botton class="right_btn fl"><span></span></botton>
                </div>
            </div>
        </div>
    </div>
    <!--帮助中心 内容导航-->
    <div class="help_nav">
        <div class="help_nav_content">
            <a class='nav_active' href="{{ config('app.url') }}home/index/helps">帮助首页</a>
            <a href="{{ config('app.url') }}home/index/helpList">常见问题</a>
            <a href="{{ config('app.url') }}home/index/selfServe">自助服务</a>
        </div>
    </div>
    <!--帮助中心 常见问题-->
    <div class="help_details">
        <h3 class="help_details_title">常见问题</h3>
        <ul class="help_details_content clearfix">
            @forelse($nav as $val)
            <li>
                <h4>{{ $val['name'] }}</h4>
                @if(isset($val['newList']) && $val['newList'])
                @foreach($val['newList'] as $v)
                <div>
                    <a href="/home/index/helpDetail/{{ $v['id'] }}" target="_blank">
                        <span>· {{ $v['title'] }}</span>
                    </a>
                </div>
                @endforeach
                @else
                <div>暂无内容</div>
                @endif
            </li>
            @empty
            <li>
                暂无数据
            </li>
            @endforelse
        </ul>
    </div>
    <!--帮助中心 热门专题-->
    <div class="help_hot_special">
        <h3 class="help_hot_special_title">热门专题</h3>
        <ul class="help_hot_special_content clearfix"></ul>
    </div>
@endsection
@section('foot.js')
    <script>
        var APP_IMGURL = "{{ config('app.source_url') }}"
        var APP_URL = "{{ config('app.url') }}"
    </script>
    <script src="{{ config('app.source_url') }}home/js/help.js" type="text/javascript" charset="utf-8"></script>
@endsection