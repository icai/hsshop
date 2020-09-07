@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_39ygjl7x.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <!-- 二级导航三级标题 开始 -->
            <div class="third_title"><span>营销中心 /</span> 幸运大转盘</div>
            <!-- 二级导航三级标题 结束 -->
        </div>
    </div>
@endsection
@section('content')

    记录
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_39ygjl7x.js"></script>
@endsection