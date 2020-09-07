@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/footerBar.css" />

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
                <!-- <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li> -->
                <li>
                    <a href="javascript:void(0)">底部导航</a>
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
            
            <!-- <ul class="tab_nav">
                <li>
                    <a href="/merchants/marketing/litePage">小程序微页面</a>
                </li> 
                <li class="hover">
                    <a href="/merchants/marketing/footerBar">底部导航</a>
                </li>
                <li class="">
                    <a href="/merchants/marketing/xcx/topnav">首页分类导航</a>
                </li>
                <li class=""> 
                    <a href="/merchants/marketing/xcx/list">小程序列表</a>
                </li>
                <li class="">
                    <a href="/merchants/marketing/liteStatistics">数据统计</a>
                </li>
            </ul> -->
            <div class="app">
                <div class="appInfo">
                    <i class="icon-info glyphicon glyphicon-info-sign"></i>
                    修改标题，或图片小程序端及时生效！
                </div>
                <div class="app-content clearfix" ng-cloak>
                    <div class="show">
                        <div class="wrapper">
                            <img src="{{ config('app.source_url') }}mctsource/images/header_bg.jpg">
                        </div>
                        <div class="bottom">
                            <div class="item" ng-repeat="item in tabBarList">
                                <div>
                                    <div style="width: 30px;height: 30px;display: inline-block">
                                        <img ng-if="$index === 0" ng-src="@{{_host}}@{{item.selectedIconPath}}">
                                        <img ng-if="$index !== 0 && item.iconPath" ng-src="@{{_host}}@{{item.iconPath}}">
                                    </div>
                                    <p class="title">@{{item.text}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="edit">
                        <div class="header">导航设置</div>
                        <div class="navigation">
                            <!-- 导航模块开始 -->
                            <div class="item-wrapper" ng-repeat="item in tabBarList">
                                <div class="label vertical-top fz-12">导航@{{$index + 1}}：</div>
                                <!-- 导航设置开始 -->
                                <div class="item">
                                    <!-- 名称 -->
                                    <div class="control-group mb-20">
                                        <label class="control-label"><em class="required">*</em>名称：</label>
                                        <div class="wrapper">
                                            <input type="text" class="form-control title" ng-model="item.text" name="title_$index" maxlength = "5" required>
                                        </div>
                                        <p class="help-block error-message" ng-show="!item.text">名称不能为空</p>
                                    </div>    
                                    <!-- 图片 -->
                                    <div class="control-group mb-20">
                                        <label class="control-label vertical-top">图片：</label>
                                        <div class="wrapper">
                                            <span class="revise vertical-top" ng-click=iconModalShow($index)> @{{item.iconPath?"修改":"添加"}}</span>
                                            <div class="picture-group" ng-if="item.iconPath">
                                                <div class="picture-one">
                                                    <img ng-src="@{{_host}}@{{item.iconPath}}" width="30px">
                                                </div>
                                                <div class="picture-two">
                                                    <img ng-src="@{{_host}}@{{item.selectedIconPath}}" width="30px">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" ng-model="item.iconPath" required>
                                        <p class="help-block error-message" ng-show="!item.iconPath">图片不能为空</p>
                                    </div> 
                                    <!-- 链接 -->
                                    <div class="control-group mb-20">
                                        <label class="control-label"><em class="required">*</em>链接：</label>
                                        <div class="wrapper" ng-show="item.pagePath || item.pageId > 0">
                                            <div class="set-url co-38f fz-12" ng-if="!item.urlTitle" ng-click="openPageModal($index)">  设置链接到的微页面
                                            </div>
                                            <div ng-if="item.urlTitle">
                                                <span class="fz-12 select-url">@{{item.urlTitle}}</span>
                                            </div>
                                        </div>
                                        <div class="wrapper" ng-show="!item.pagePath && item.pageId == 0">
                                            <select class="form-control" ng-change="selectChange($index);" ng-model="item.grade">
                                                <option ng-repeat="list in selectList" value="@{{list.type}}" ng-bind="list.title"></option>  
                                            </select>  
                                        </div>
                                        <input type="hidden" ng-model="item.urlTitle" required>
                                        <p class="help-block error-message" ng-show="!item.urlTitle">链接不能为空</p>
                                    </div>                 
                                </div>
                                <!-- 导航设置结束 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--选择icon弹框开始-->
            <div class="modal pic-modal" id="iconModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content" id='model_icon'>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <div class="h3">图标选择</div>
                        </div>
                        <div class="modal-body icon_show_box">
                            <ul class="picGroup" ng-repeat="ite in iconGroupLists">
                                <li class="picItem inline-block" ng-repeat="item in ite" ng-click="changeIcon(item)">
                                    <div class="inline-block">
                                        <img ng-src="@{{_host}}@{{item.iconPath}}" >
                                    </div>
                                    <div class="inline-block">
                                        <img ng-src="@{{_host}}@{{item.selectedIconPath}}" >
                                    </div>
                                    <div class="text">@{{item.text}}</div>
                                </li>
                            </ul>
                            <ul class="picGroup">
                                <li class="picItem inline-block"
                                    ng-repeat="item in addIconImg track by $index"
                                    ng-click="changeIcon(item)"
                                >
                                    <div class="inline-block">
                                        <img ng-src="@{{imgUrl}}@{{item.iconPath}}" >
                                    </div>
                                    <div class="inline-block">
                                        <img ng-src="@{{imgUrl}}@{{item.selectedIconPath}}" >
                                    </div>
                                    <div class="text">@{{item.text}}</div>
                                    <span class='del_span' ng-click='delImg(item.id);$event.stopPropagation()'>x</span>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-footer clearfix" style='display: none'>
                            <div class= "myModalPage">
                                <span class='addIcn' ng-click="add_icon()">新建图标</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-content modal_icon hide" id='modal_icon_show'>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <div class="h3">新建图标</div>
                        </div>
                        <div class="modal-body content_icon">
                            <label>名称：</label>
                            <input ng-model='icon_title' type="text" placeholder='请输入图标名称'>
                            <div class='icon_box'>
                                <div class='icon_box_a'>
                                    <p>普通：</p>
                                    <div class='div_img'>
                                        <img ng-if='!amend_a' src="../../../../../mctsource/images/placeholder.jpg" alt="">
                                        <img ng-if='amend_a' ng-src="@{{amend_a[0].FileInfo.path}}" alt="">
                                        <span ng-click='close_img(1)'>x</span>
                                    </div>
                                    <p class='amend_p' ng-click='addAdvs(1)'>修改</p>
                                </div>
                                <div class='icon_box_b'>
                                    <p>高亮：</p>
                                    <div class='div_img'>
                                        <img ng-if='!amend_b' src="../../../../../mctsource/images/placeholder.jpg" alt="">
                                        <img ng-if='amend_b' ng-src="@{{amend_b[0].FileInfo.path}}" alt="">
                                        <span ng-click='close_img(2)'>x</span>
                                    </div>
                                    <p class='amend_p' ng-click='addAdvs(2)'>修改</p>
                                </div>
                            </div>
                            <div style='color: #CCCCCC;margin-top: 20px;'>
                                图片尺寸要求：不大于128*100像素，支持PNG格式
                            </div>
                        </div>
                        <div class="modal-footer clearfix">
                            <div class= "myModalPage">
                                <button type="button" class="close addIcn icn_close icn_btn" data-dismiss="modal" aria-hidden="true">取消</button>
                                <span class='addIcn icn_btn' ng-click='preserve_img()'>保存</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--选择icon弹框结束-->
            <!--提醒弹框开始-->
            <div class="modal " id="remindModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog" style="width: 500px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="h3">提醒</div>
                        </div>
                        <div class="modal-body">
                            你调整了导航栏样式,需重新提交微信审核,审核通过后新的导航栏才会生效
                        </div>
                        <div class="modal-footer clearfix">
                            <span class="btn btn-primary" ng-click="saveWeixin()">保存并提交审核</span>
                            <span class="btn btn-default" ng-click="saveData()">保存</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--提醒弹框结束-->
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
            <!-- 图片上传 -->
            <div class="modal export-modal myModal-adv" id="myModal-adv">
                <div class="modal-dialog" id="modal-dialog-adv">
                    <form class="form-horizontal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <ul class="module-nav modal-tab">
                                    <li ng-show="uploadShow">
                                        <a href="#js-module-goods" data-type="goods" class="js-modal-tab"
                                           ng-show="uploadShow" ng-click="showImage()">< 选择图片 |</a>
                                    </li>
                                    <li ng-show="!uploadShow">
                                        <a href="#js-module-goods" data-type="goods" class="js-modal-tab">我的图片</a>
                                    </li>
                                    <li class="active" ng-show="uploadShow">
                                        <a href="#js-module-tag" data-type="tag" class="js-modal-tab">上传图片</a>
                                    </li>
                                </ul>
                                <!-- <div class="search-region">
                                    <div class="ui-search-box">
                                        <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                                    </div>
                                </div> -->
                            </div>
                            <div class="modal-body" ng-show="!uploadShow">
                                <div class="category-list-region js-category-list-region">
                                    <ul class="category-list">
                                        <li class="js-category-item" ng-class="{true :'active', false :''}[grounp.isactive]"  ng-repeat="grounp in grounps" ng-click="chooseGroup(grounp)"> @{{grounp['name']}}
                                            <span class="category-num" ng-bind="grounp['number']"></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="attachment-list-region js-attachment-list-region">
                                    <ul class="image-list">
                                        <li class="image-item js-image-item" data-id="701007915" ng-repeat="image in uploadImages" ng-click="chooseImage(image,$index)">
                                            <div class="image-box" style="background-image: url(@{{image['FileInfo']['path']}})"></div>
                                            <div class="image-title">@{{image['FileInfo']['name']}}
                                            </div>
                                            <div class="attachment-selected" ng-if="imgIndex == $index">
                                                <i class="icon-ok icon-white"></i>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="attachment-pagination js-attachment-pagination">
                                        <div class="ui-pagination">
                                            <span class="ui-pagination-total"></span>
                                        </div>
                                    </div>
                                    <a href="javascript:;" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;" ng-click="upload()">上传图片</a>
                                </div>
                            </div>
                            <div class="modal-body" ng-show="uploadShow">
                                <div id="container">
                                    <!--头部，相册选择和格式选择-->
                                    <div id="uploader">
                                        <div class="queueList">
                                            <div id="dndArea" class="placeholder">
                                                <div id="filePicker"></div>
                                                <p>或将照片拖到这里，单次最多可选300张</p>
                                            </div>
                                        </div>
                                        <div class="statusBar" style="display:none;">
                                            <div class="progress">
                                                <span class="text">0%</span>
                                                <span class="percentage"></span>
                                            </div>
                                            <div class="info"></div>
                                            <div class="btns">
                                                <div id="filePicker2"></div>
                                                <div class="uploadBtn">开始上传</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer clearfix">
                                <div class="selected-count-region js-selected-count-region hide">
                                    已选择<span class="js-selected-count">2</span>张图片
                                </div>
                                <div class="text-center">
                                    <button class="ui-btn js-confirm ui-btn-disabled" disabled="disabled" ng-show="!chooseSureBtn && !uploadShow">确认</button>
                                    <button class="ui-btn js-confirm ui-btn-primary" ng-show="chooseSureBtn  && !uploadShow" ng-click="chooseAdvSureBtn()">确认</button>
                                    <button class="ui-btn js-confirm ui-btn-primary" ng-show="uploadShow" ng-click="uploadSureBtn()">确认</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- 保存功能开始 -->
            <div class="btn-wrapper">
                <button class="btn btn-primary" style="margin-right:10px" ng-click="save(editorForm.$valid)">保存</button>
                <button class="btn btn-default" ng-click="refresh()">刷新</button>
            </div>
            <!-- 保存功能结束 -->
        </form>
    </div>
    
@endsection

@section('page_js') 
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- webuploader -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/md5.js"></script>
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";//静态图片域名
    var host ="{{ config('app.url') }}";//网站域名
    var imgUrl = "{{ imgUrl() }}";//动态图片域名
</script>
<script src="{{ config('app.source_url') }}mctsource/js/footerSyncBar.js" type="text/javascript" charset="utf-8"></script>

@endsection