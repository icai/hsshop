@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/topNav.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                {{--<li>--}}
                    {{--<a href="{{ URL('/merchants/marketing') }}">营销中心</a>--}}
                {{--</li>--}}
                <li>
                    首页分类导航
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
        <form ng-app="myApp" ng-controller="myCtrl" name="editorForm" ng-click="navSelectHide()">
            {{--<ul class="tab_nav">--}}
                {{--<li>--}}
                    {{--<a href="/merchants/marketing/litePage">小程序微页面</a>--}}
                {{--</li> --}}
                {{--<li class="">--}}
                    {{--<a href="/merchants/marketing/footerBar">底部导航</a>--}}
                {{--</li>--}}
                {{--<li class="hover">--}}
                    {{--<a href="/merchants/marketing/xcx/topnav">首页分类导航</a>--}}
                {{--</li>--}}
                {{--<li class=""> <!-- update 梅杰 新增列表页-->--}}
                    {{--<a href="/merchants/marketing/xcx/list">小程序列表</a>--}}
                {{--</li>--}}
                {{--<li class="">--}}
                    {{--<a href="/merchants/marketing/liteStatistics">数据统计</a>--}}
                {{--</li>--}}
            {{--</ul>--}}
        	<div class="switch bg-gray">
		        <strong>首页分类导航</strong>
		        <p>开启分类导航后,首页可以展示分类导航,否则不显示</p>
		        <!-- 总开关 -->
		        <div class="switch-wrap switch-total">
		            <label class="ui-switcher ui-switcher-off" data-is-open="0"></label>
		        </div> 
		    </div> 
		    <!--主体控制-->
            <div class="app">
		    	<!--左侧显示-->
            	<div class="app-content clearfix" ng-cloak>
            		<div class="show">
            			<div class="wrapper">
            				<img src="{{ config('app.source_url') }}mctsource/images/header_bg.jpg">
	            			<div class="bottom">
	            				<div class="item" ng-repeat="item in tabBarList">
	            					<div class="navauto">
	                                    
	    	        					<p class="title">@{{item.title}}</p>
	            					</div>
	            				</div>
	            			</div>
            			</div>
            		</div>
            		<!--右侧设置-->
            		<div class="edit">
            			<div class="header">导航设置</div>
    					<div class="navigation">
                            <!-- 导航模块开始 -->
    						<div class="item-wrapper" ng-repeat="item in tabBarList">
    							<div class="label vertical-top fz-12">导航@{{$index + 1}}：</div>
                                <!-- 导航设置开始 -->
    							<div class="item">
                                    <div class="delete" ng-click="deleteNavBar(item,$index)" ng-if="$index != 0 && $index!= tabBarList.length">x</div>
                                    <!-- 名称 -->
                                    <div class="control-group mb-20">
                                        <label class="control-label"><em class="required">*</em>名称：</label>
                                        <div class="wrapper">
                                            <input type="text" class="form-control title" ng-model="item.title" name="title_$index" maxlength = "10" required>
                                        </div>
                                        <p class="help-block error-message" ng-show="!item.title">名称不能为空</p>
                                    </div>    
                                    <!-- 链接 -->
                                    <div class="control-group mb-20">
                                        <label class="control-label"><em class="required">*</em>链接：</label>
                                        <div class="wrapper">
                                            <div class="set-url co-38f fz-12" ng-if="!item.urlTitle" ng-click="openPageModal($index)">  设置链接到的微页面
                                            </div>
                                            <div ng-if="item.urlTitle">
                                                <span class="fz-12 select-url">@{{item.urlTitle}}</span>
                                                <span class="co-38f fz-12" ng-if="item.isCanReviseUrl" ng-click="openPageModal($index)">修改</span>
                                            </div>
                                        </div>
                                        <input type="hidden" ng-model="item.urlTitle" required>
                                        <p class="help-block error-message" ng-show="!item.urlTitle">链接不能为空</p>
                                    </div>                 
                                </div>
                                <!-- 导航设置结束 -->
    						</div>
                            <!-- 导航模块结束 -->
                            <div class="add-nav">
                                <ul class="select-type" ng-if="navSelectData.isNavSelectShow" style="top:-@{{navSelectData.top}}px;">
                                    <li ng-click="addNav(item.type,$event)" ng-repeat="item in navSelectData.navList">@{{item.title}}</li>
                                </ul>
                                <div ng-click="addNavs($event)">+添加导航</div>
                            </div>
    					</div>
            		</div>
            	</div>
           	</div>
            
            
            <!-- 微页面弹框开始 -->
            <div class="modal export-modal" id="page_model">
                <div class="modal-dialog" id="page-dialog">
                    <form class="form-horizontal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <ul class="module-nav modal-tab">
                                    <li class="active">
                                        <a href="#js-module-goods" data-type="goods" class="js-modal-tab">微页面</a>
                                        <span>|</span>
                                    </li>
                                    <li class="link-group link-group-0" style="display: inline-block;">
                                        <a href="/v2/showcase/goods/edit" target="_blank" class="new_window co-38f">新建微页面</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped ui-table ui-table-list">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="td-cont">
                                                    <span>标题 </span>
                                                    <!-- <a href="#">刷新</a> -->
                                                </div>
                                            </th>
                                            <th class="information"></th>
                                            <th>
                                                <div class="td-cont">
                                                    <span>创建时间</span>
                                                </div>
                                            </th>
                                            <th class="opts">
                                                <div class="td-cont">
                                                    <form class="form-search">
                                                        <div class="input-append">
                                                            <input class="input-small js-modal-search-input form-control" type="text" ng-model="pageData.searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchPage()">搜</a>
                                                        </div>
                                                    </form>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat = "list in pageData.list">
                                            <td class="title" colspan="2">
                                                <div class="td-cont">
                                                    <a target="_blank" class="new_window" href="javascript:void(0);">@{{ list['name'] }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <span>
                                                    @{{list['created_at']}}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="td-cont text-right">
                                                    <span class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="choosePageLinkSure(list)">选取</span> 
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer clearfix">
                                <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="choosePageSure()">
                                    <input type="button" class="btn btn-primary" value="确定使用">
                                </div>
                                <div class="page_pagenavi"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- 微页面弹框结束 -->

            <!-- 保存功能开始 -->
            <div class="btn-wrapper">
                <!--<button class="btn btn-primary" ng-click="save(editorForm.$valid)">保存</button>-->
                <button class="btn btn-primary" ng-click="saveData()">保存</button>
            </div>
            <!-- 保存功能结束 -->
        </form>
    </div>
    
@endsection

@section('page_js') 
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";//静态图片域名
    var host ="{{ config('app.url') }}";//网站域名
    var imgUrl = "{{ imgUrl() }}";//动态图片域名
</script>
<script src="{{ config('app.source_url') }}mctsource/js/topNav.js" type="text/javascript" charset="utf-8"></script>

@endsection