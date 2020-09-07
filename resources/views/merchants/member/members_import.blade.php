@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_ucb29gth.css" />
@endsection
@section('slidebar')
    @include('merchants.member.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 普通导航 开始 -->
            <ul class="common_nav">
                <li>
                    <a href="{{URL('/merchants/member/members')}}">会员管理</a>
                </li>
                <li class="hover">
                    <a href="##">导入会员</a>
                </li>
            </ul>
            <!-- 普通导航 结束  -->
        </div>
        <!-- 三级导航 结束 -->

        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
    <div class="content">
        <a href="{{URL('/merchants/member/add_import')}}" type="button" class="btn btn-primary">导入会员</a>
        <!--主要内容-->
        <div class="main_content">
            <ul class="main_content_title">
                <li>创建导入时间</li>
                <li>预计导入数量</li>
                <li>导入成功</li>
                <li>导入失败</li>
                <li>会员身份</li>
                <li>是否验证</li>
                <li>操作人</li>
                <!--<li>操作</li>-->
            </ul>
            @foreach($importRow as $row)
                @php
                    $isverify = empty($row['isverify'])?'否':'是';
                @endphp
                <ul class="data_content">
                    <li>{{$row['created_at']}}</li>
                    <li>{{$row['total']}}</li>
                    <li>{{$row['success_num']}}</li>
                    <li>{{$row['fail_num']}}</li>
                    <li>{{$row['card_title']}}</li>
                    <li>{{$isverify}}</li>
                    <li>{{$row['editor']}}</li>
                    <!--<li><a href="##" class="send_msg">发短信</a></li>-->
                </ul>
            @endforeach
        </div>
        <!--分页器-->
        {{$pageHtml}}
    </div>
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/member_12tjgfnd.js"></script>
    <!--<script src="{{ config('app.source_url') }}static/js/jqPaginator.min.js"></script>-->
@endsection