@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/templateList.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <div class="content">
            <div class="main_content">
                <div class="sorts">
                    <form id="myForm" class="form-inline">
                        <div class="nav">
                            <div class="item nav-draft" ng-class="{true:'co_000',false:''}[isDraft]" ng-click="switchNav(1)">草稿箱</div>
                            <div class="line"></div>
                            <div class="item nav-template" ng-class="{true:'co_000',false:''}[!isDraft]" ng-click="switchNav(2)">模板列表</div>
                        </div>
                    </form>
                </div>
                <!-- 草稿箱 -->
                <ul class="table_title flex-between draft" ng-show="isDraft">
                    <li>版本号</li>
                    <li>描述</li>
                    <li>最新提交时间</li>
                    <li>操作</li>
                </ul>
                <form class="listForm draft" ng-show="isDraft">
                    <ul class="table_body flex-between" ng-repeat="item in xcxList">
                        <li>@{{item.user_version}}</li>
                        <li>@{{item.user_desc}}</li>
                        <li>@{{item.create_time}}</li>
                        <li>
                        	<div class="add draft-add" ng-click="addRepository(item.id)">添加到模板库</div>
                        	<div class="del draft-del" ng-click="deleteDraft(item.id,$index)">删除</div>
                        </li>
                    </ul>
                </form>
                
                <!-- 模板列表 -->
                <ul class="table_title flex-between template" ng-show="!isDraft">
                    <li>版本号</li>
                    <li>描述</li>
                    <li>templateId</li>
                    <li>添加到模板库时间</li>
                    <li>操作</li>
                </ul>
                <form class="listForm template" ng-show="!isDraft">
                    <ul class="table_body flex-between" ng-repeat="item in xcxList">
                        <li>@{{item.user_version}}</li>
                        <li>@{{item.user_desc}}</li>
                        <li>@{{item.template_id}}</li>
                        <li>@{{item.create_time}}</li>
                        <li>
                        	<div class="add template-add" ng-click="setVersion(item.id, 1)" ng-show="item.is_online == 0">作为普通版本</div>
                            <div class="add template-add" ng-click="setVersion(item.id, 2)" ng-show="item.is_online == 0">作为直播版本</div>

                            <div class="add template-add" ng-click="setVersion(item.id)" ng-show="item.is_online == 1">当前普通版本</div>
                            <div class="add template-add" ng-click="setVersion(item.id)" ng-show="item.is_online == 2">当前直播版本</div>

                        	<div class="del template-del" ng-click="deleteTemplate(item.id,$index)">删除</div>
                        </li>
                    </ul>
                </form>

                <div class="page">
                    <!-- fenye -->
                </div>
                <!-- 其他操作 -->
                <div>
                    <button type="button" class="btn btn-primary" ng-click="syncDraft()">同步草稿箱</button>
                    <button type="button" class="btn btn-primary" ng-click="syncTemplate()">同步小程序模板库</button>
                    <div style="color:red;">@{{draftContent}}</div>
                    <div style="color:red;">@{{templateContent}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
    <!-- angular -->
	<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
    <!-- ajax分页js -->
	<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/templateList.js" type="text/javascript" charset="utf-8"></script>
@endsection