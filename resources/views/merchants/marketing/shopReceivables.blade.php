@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    @if($uri=='notes')
        <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_b3mf73iw.css" />
    @elseif($uri=='discount')
        <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_3f0bss6k.css" />
    @elseif($uri=='label')
        <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_jff9sg3f.css" />
    @else
        <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_jc27w5dp.css" />
    @endif
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">微商城收款</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->

        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('content')
    <!--$shopStatus = ['index','notes','discount','label'];-->
    @if($uri=='notes')
        @include("merchants.marketing.shopReceivables.notes")
    @elseif($uri=='discount')
        @include("merchants.marketing.shopReceivables.discount")
    @elseif($uri=='label')
        @include("merchants.marketing.shopReceivables.label")
    @else
        @include("merchants.marketing.shopReceivables.index")
    @endif
@endsection

@section('page_js')
    <!--主要内容js文件-->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_discount.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/marketing_lry0qllf.js"></script>
@endsection