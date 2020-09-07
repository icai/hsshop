@extends('home.base.head')
@section('head.css')
    <!-- layer  -->
    {{--<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />--}}
    {{--<!-- 自定义layer皮肤css -->--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />--}}
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/helpList.css"/>
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
                    <input class='right_inp fl' placeholder="请输入需要搜索的关键字" type="text" value="{{ request('keywords') }}">
                    <botton class="right_btn fl"><span></span></botton>
                </div>
            </div>
        </div>
    </div>
    <!--帮助中心 内容导航-->
    <div class="help_nav">
        <div class="help_nav_content">
            <a href="{{ config('app.url') }}home/index/helps">帮助首页</a>
            <a class='nav_active'  href="{{ config('app.url') }}home/index/helpList">常见问题</a>
            <a href="{{ config('app.url') }}home/index/selfServe">自助服务</a>
        </div>
    </div>
    <!--帮助中心 常见问题列表-->
    <div class='help_list clearfix'>
        <div class='fl help_list_left'>
            @if($type)
            <ul class='list_left_ul'>
                @foreach($type as $val)
                <li>
                    <div class='list_left_div'>{{ $val['name'] }} <span></span></div>
                    @if(isset($val['child']) && $val['child'])
                    <ul class="list_left_ul_li" @if($pid == $val['id']) style="display: block;" @endif>
                        @foreach($val['child'] as $v)
                        <li @if($type_info == $v['id']) class='li_active' @endif><a href="/home/index/helpList?info_type={{ $v['id'] }}&Pid={{ $val['id'] }}">{{ $v['name'] }}</a></li>
                        @endforeach

                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
        <div class='fr help_list_right'>
            <h4>{{ $typeTitle ? $typeTitle : '常见问题'}}</h4>
            <!-- <div class='list_right_nav'>
                <a class='a_active' href="/home/index/helpList">全部</a>
                <a href="/home/index/helpList?sort">最新</a>
            </div> -->

            <ul class='list_right_content'>
                @forelse($information['data'] as $val)
                    <li>
                        <p class='list_p_a'><a href="{{ config('app.url') }}home/index/helpDetail/{{ $val['id'] }}">{{ $val['title'] }}</a></p>
                        <p>{{ $val['created_at'] }}</p>
                    </li>
                @empty
                    <li>
                        暂无数据
                    </li>
                @endforelse
            </ul>
            <nav aria-label="Page navigation" class="page-bottom">
       			{{$page}}
            </nav>
        </div>
    </div>
@endsection
@section('foot.js')
    <script>
        var APP_URL = "{{ config('app.url') }}"
    </script>
    <script src="{{ config('app.source_url') }}home/js/helpList.js" type="text/javascript" charset="utf-8"></script>
@endsection