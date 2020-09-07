@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/commendation.css" />
@endsection
@section('slidebar')
    @include('merchants.recommend.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <ul class="common_nav">
                <li data-type="1">
                    <a href="javascript:void(0);">享立减推荐</a>
                </li>
                <li data-type="2">
                    <a href="javascript:void(0);">拼团推荐</a>
                </li>
            </ul>
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
    <div class="content" ng-controller="myCtrl">
        <div class="widget-list-filter">
            <div class="pull-left">
                <a class="btn btn-success " href="javascript:void(0);" ng-click="addActivity()">添加活动</a>
            </div>
            <div class="pull-right">
                <span style="font-weight: bold;font-size: 15px;">是否开启推荐?</span>
                <span class="switch fr switchEnvelope" data-toggle="modal" data-target="#envelopeRule"></span>
            </div>
        </div>
        <test-directive close-model="closeModel" type="type" choose-succ="chooseSure" recomments="recomments"></test-directive>
        <div class="main_content">
            <ul class="main_content_title">
                <!-- <li>
                    <input type="checkbox" name="" ng-value="list.id">
                </li> -->
                <li>活动ID</li>
                <li>活动标题</li>
                <!-- <li>活动图片</li> -->
                <li class="text-right">操作</li>
            </ul>
            <ul class="data_content" ng-repeat = "list in lists" ng-cloak>
                <!-- <li>
                    <input type="checkbox" name="">
                </li> -->
                <li ng-if="list.type==1"><a href="/merchants/shareEvent/create?id=@{{list.recommendation_id}}" ng-bind="list.recommendation_id"></a></li>
                <li ng-if="list.type==2"><a href="/merchants/marketing/togetherGroupAdd?id=@{{list.recommendation_id}}" ng-bind="list.recommendation_id"></a></li>
                <li ng-bind="list.title"></li>
                <!-- <li class="activity_img">
                    <img src="http://192.168.63.28/home/image/huisouyun_120.png">
                </li> -->
                <li class="text-right pr">
                    <a class="delete" href="javascript:void(0);" ng-click="removeRecomment($index,list,$event)">移除该推荐</a>
                </li>
            </ul>
            <!-- <ul class="data_content">暂无数据</ul> -->
        </div>
        <div class="text-right">
            <div class="pager_list"></div>
        </div>
    </div>
    </div>
@endsection

@section('page_js')
    <script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
    <script type="text/javascript">
        var _host = "{{ imgUrl() }}";
        var wid = {{session('wid')}};

    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/commendation.js"></script>
@endsection