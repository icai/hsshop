@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/help.css">  
@endsection

@section('content')
    <div class="content" id="top">
        <!-- 顶部图片 -->
        <div class="app_header banner"></div>
        <!-- 搜索框 -->
        <div class="help-search">
            <div class="search-box">
                <input type="text" value="" placeholder="请输入要搜索的内容">
                <button class="search-pic"><img src="{{ config('app.source_url') }}mobile/images/search.png" alt=""></button>
            </div>
        </div>
        <!-- 二级分类 -->
        <div class="help-content">
            @if($mobileHelpTypes['nav'])
            @foreach($mobileHelpTypes['nav'] as $key =>$val)
            <div class="help-item">
                <div class="help-title">
                    <h1 class="num">{{ $key+1 }}</h1>
                    <h1 class="item-title">{{ $val['name'] }}</h1>
                </div>
                @if(isset($val['child']) && $val['child'])
                <ul>
                    @foreach($val['child'] as $k=>$v)
                    <li>
                        <a href="/home/index/helpList?info_type={{ $v['id'] }}&Pid={{ $val['id'] }}&key={{ $key+1 }}.{{ $k+1 }}">{{ $key+1 }}.{{ $k+1 }} {{ $v['name'] }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endforeach
            @endif
        </div>    
    </div>
@endsection


@section('js')
<script>
	var appUrl = "{{ config('app.url') }}";
</script>
<script src="{{ config('app.source_url') }}mobile/js/help.js"></script>
@endsection