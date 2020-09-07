@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_vote.css" />
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
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
                    <a href="javascript:void(0)">投票活动</a>
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
@section('content')
    <div class="content"> 
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix pr">
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li @if(!request('status'))class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/vote') }}">所有促销</a>
                    </li>
                    <li @if(request('status') == 1)class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/vote?status=1') }}">未开始</a>
                    </li>
                    <li @if(request('status') == 2)class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/vote?status=2') }}">进行中</a>
                    </li>
                    <li @if(request('status') == 3)class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/vote?status=3') }}">已结束</a>
                    </li>
                </ul>  
            </div>
        </div> 
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <div class="mb-15 clearfix pr">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a class="btn btn-success " href="{{ URL('/merchants/marketing/vote/save') }}">新建投票活动</a>
                </div>
                <div style="position: relative;">
                    <div class="js-list-search ui-search-box">
                        <form>
                            <input class="txt" name="title" value="" type="search" placeholder="搜索">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- search 结束 -->
        <!-- 拼团列表 开始 -->
        @if($voteData['data'])
        <div class="main_content">
            <ul class="main_content_title">
                <li>活动名称</li>
                <li>有效时间</li>
                <li>活动状态</li> 
                <li class="text-right">操作</li>
            </ul>
            @foreach($voteData['data'] as $val)
            <ul class="data_content">
                <li class="blue">{{ $val['act_title'] }}</li>
                <li>{{ date('Y-m-d H:i:s',$val['start_time']) }} 至 {{ date('Y-m-d H:i:s',$val['end_time']) }}</li>
                <li class="gray1">{{ $val['status'] }}</li>
                <li class="text-right pr">
                <a href="/merchants/marketing/vote/save?id={{ $val['id'] }}">编辑</a>-
                <a href="javascript:void(0);" class="delBtn" data-id={{ $val['id'] }}>删除</a>-
                <a class="link_btn customTip_items" data-url="{{ config('app.url') }}shop/vote/index/{{ session('wid') }}/{{ $val['id'] }}">
                    <span class="blue_38f" style="cursor: pointer">链接</span>
                </a>-
                <a href="/merchants/marketing/vote/userList?vote_id={{ $val['id'] }}">查看结果</a>-
                <a class="two-code" data-id={{$val['id']}}>
                    <span class="blue_38f" style="cursor: pointer">二维码</span>
                </a>
                </li> 
            </ul>
            @endforeach
        </div> 
        @else
        <div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据
        </div>
        @endif
        <!-- 拼团列表 结束 -->
        <!-- 分页 -->
        <div class="text-right">
            {!! $pageHtml !!}
        </div>
    </div>
@endsection

@section('page_js')
    <script src="{{ config('app.source_url') }}static/js/require.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_vote.js"></script>  
    <script type="text/javascript">
        var host = "{{config('app.url')}}";
        var wid = "{{ session('wid') }}";
    </script>
@endsection