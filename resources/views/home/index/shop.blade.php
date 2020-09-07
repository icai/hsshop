@extends('home.base.head')
@section('head.css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css" />

<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/hangyeanli.css" />
<style type="text/css">
    body {
        font-family: "Arial", "Microsoft YaHei", sans-serif;
    }

    .hover {
        color: #337ab7;
    }
</style>
@endsection
@section('content')
@include('home.base.slider')
<!-- 主要内容 -->
<div class="main_content">
    <!-- 顶部图片 -->
    <div class="wraper banner">
        {{--<img src="{{ config('app.source_url') }}home/image/home-case-bannber-20200810.png" alt="">--}}
    </div>
    <!-- 当前位置 -->
    <div class="breadcrumb_nav">
        <div>
            <img src="{{ config('app.source_url') }}home/image/addr01.png">
            当前位置：<a href="{{ config('app.url') }}">首页</a>><span> 展示案例</span>
        </div>
    </div>
    <div class="cases_content">
        <!-- 案例类型导航 -->
        <div class="cases_nav">
            <ul>
                @foreach($typeData as $key =>$val)
                <li @if($type==$key) class="active" @endif>
                    <a href="/home/index/{{ $key }}/shop">{{ $val }}</a>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="cases_menu">
            <!-- 案例内容分类导航 -->
            <div class="cases_type">行业分类</div>
            <div class="type_list">
                <ul>
                    <li @if($industry==0) class="active" data-id="0" @endif><a href="/home/index/{{ $type }}/shop">全部</a></li>
                    @if($industryList && $industryList[0])
                    @foreach($industryList[0] as $val)
                    <li @if($industry==$val['id']) class="active" data-id="{{ $val['id'] }}" @endif><a href="/home/index/{{ $type }}/shop?industry={{ $val['id'] }}">{{ $val['name'] }}</a></li>
                    @endforeach
                    @endif
                </ul>
            </div>
            <!-- add by cuiyuan 2019.5.30 -->
            @if($type==1 && $industryList && $industryList[1])
            <div class="cases_menu_select show-more">
                <img src="{{ config('app.source_url') }}home/image/selectBox.png" alt="">
            </div>
            <div class="cases_menu_select close-more" style="display:none">
                <img src="{{ config('app.source_url') }}home/image/closeBox.png" alt="">
            </div>
            <!-- end -->

            <div class="more_case_box" style="display:none;">
                <ul>
                    @foreach($industryList[1] as $val)
                    <li @if($industry==$val['id']) class="active" data-id="{{ $val['id'] }}" @endif><a href="/home/index/{{ $type }}/shop?industry={{ $val['id'] }}">{{ $val['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <!-- 案例内容列表 -->
        <div class="cases_list">
            <ul>
                @forelse($caseList['data'] as $val)
                <li>
                    @if(isset($val['code']) && $val['code'])
                    <div class="code">
                        <div class="code-pic">
                            <img src="{{ imgUrl() }}{{ $val['code'] or '' }}">
                        </div>
                        <div class="seeDetail">
                            <div class="left_line"></div>
                            <p><a href="{{ config('app.url') }}home/index/caseDetails?id={{ $val['id'] }}">查看详情</a></p>
                            <div class="right_line"></div>
                        </div>
                        <p><a href="{{ config('app.url') }}home/index/caseDetails?id={{ $val['id'] }}">{{ $val['name'] }}</a></p>
                    </div>
                    @endif
                    <a href="{{ config('app.url') }}home/index/caseDetails?id={{ $val['id'] }}">
                        <div class="pic">
                            <img src="{{ imgUrl() }}{{ $val['logo'] or '' }}">
                        </div>
                        <p>{{ $val['name'] }}</p>
                    </a>
                </li>
                @empty
                <li style="line-height:330px">暂无数据</li>
                @endforelse
            </ul>
        </div>
        <!-- 页数 -->
        <nav aria-label="Page navigation" class="page-bottom">
            {{$page}}
        </nav>
    </div>


</div>
@endsection
@section('foot.js')
<script type="text/javascript">
    var url = '{{ imgUrl() }}';
</script>
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/shop.js" type="text/javascript" charset="utf-8"></script>
@endsection