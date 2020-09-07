@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_xiixe4hu.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/store/attachmentImage') }}">图片</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/store/attachmentVideo') }}">视频</a>
            </li>
            <li>
                {{--<a href="{{ URL('/merchants/store/attachmentVoice') }}">语音</a>--}}
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
@verbatim
<div class="content" ng-app="myApp" ng-controller="myCtrl">
    <div class="app-inner">
        <div class="page-showcase-attachment">
            <div class="category-container">
                <div>
                    <ul class="category-list">
                        <li class="ui-tooltip" ng-class="{true :'active', false :''}[grounp.isactive]" data-tooltip-title="未分组" data-tooltip-placement="left" ng-repeat="grounp in grounps" ng-click="chooseGroup(grounp)">
                            <span class="category-name" ng-bind="grounp['name']"></span>
                            <span class="category-num" ng-bind="grounp['number']"></span>
                        </li>
                    </ul>
                    <input type="hidden" name="classifyId" value="">
                    <div class="text-center">
                        <a href="javascript:;" class="ui-btn text-center" ng-click="addGrounp($event)" id="addGrounp">+ 添加分组</a>
                    </div>
                </div>
            </div>
            <div class="media-container">
                <div class="search-region">
                    <div class="ui-search-box">
                        <input type="text" class="txt" placeholder="搜索">
                    </div>
                </div>
                <!-- <div class="media-title ui-box">
                    <span class="media-title-wrap">
                        <h1>未分组</h1>
                    </span>
                    <a href="javascript:;" class="ui-btn ui-btn-success pull-right upload_btn" ng-click="uploadImages()">上传图片</a>
                </div> -->
                <div class="media-title ui-box">
                    <span class="media-title-wrap">
                        <h1 ng-bind="groupDetail.title"></h1>
                        <a href="javascript:void(0);" ng-click="changeGroupName($event)" class="group_name" ng-show="groupDetail.show">重命名</a>
                        <a href="javascript:;" ng-click="delGroup($event)" ng-show="groupDetail.show" class="delGroup">删除分组</a>
                    </span>
                    <a href="javascript:;" class="ui-btn ui-btn-success pull-right" ng-click="uploadImages()">上传图片</a>
                </div>
                <div class="no-result ng-hide" ng-show="images.length==0">
                    暂无数据，可点击右上角“上传图片按钮添加
                </div>
                <div class="has_data ng-hide" ng-show="images.length">
                    <div class="action-bar clearfix">
                        <label class="inline">
                            <input type="checkbox" ng-model="allChoose">
                        </label>
                        <a href="javascript:;" class="batch-opt b-gray" ng-click="changeAllGrounp(1,$event)" id="changeGrounptop">修改分组</a>
                        <a href="javascript:;" class="batch-opt b-gray" ng-click="removeAllImages(1,$event)" id="delete_top">删除</a>
                    </div>
                    <div class="image-list">
                        <div class="image-item" ng-repeat="image in images">
                            <div class="image-box" style="background-image: url({{image['FileInfo']['s_path']}});" ng-click="previewImg(image)">
                                <!-- <img class="lazy" src="" /> -->
                            </div>
                            <div class="image-title">
                                <label>
                                    <input type="checkbox" ng-model="image['ischoose']" ng-click="isChoose()">
                                    <!-- react-text: 57 -->
                                    <span ng-bind="image['FileInfo']['name']"></span>
                                    <!-- /react-text -->
                                </label>
                            </div>
                            <div class="image-opt">
                                <a href="javascript:;" ng-click="changeName($index,image,$event)" id="changeName_{{$index}}">改名</a>
                               <!--  <a href="javascript:;">链接</a> -->
                                <a href="javascript:;" ng-click="changeGrounp($index,image,$event)" id="changeGrounp_{{$index}}">分组</a>
                                <a href="javascript:;" ng-click="removeImage($index,image,$event)" id="delete_{{$index}}">删除</a>
                            </div>
                        </div>
                    </div>
                    <div class="action-bar clearfix">
                        <label class="inline">
                            <input type="checkbox" ng-model="allChoose">
                            全选
                        </label>
                        <a href="javascript:;" class="batch-opt b-gray"  ng-click="changeAllGrounp(2,$event)" id="changeGrounpbottom"
                        >修改分组</a>
                        <a href="javascript:;" class="batch-opt b-gray" ng-click="removeAllImages(2,$event)"
                         id="delete_bottom">删除</a>
                        <div class="pull-right">
                            <div class="ui-pagination pagenavi"></div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
    <!--backdrop-->
    <div class="modal-backdrop"></div>
    <!-- 改变名字 -->
    <div class="ui-popover top-center" id="changeNameProver">
        <div class="ui-popover-inner">
            <div style="margin-bottom: 6px;">修改名称</div>
            <div style="margin-bottom: 6px;">
                <input class="form-control" id="changeNameTitle" type="text" value="" placeholder="" style=" width: 166px;">
            </div>
            <div class="clearfix">
                <a href="javascript:;" class="ui-btn ui-btn-primary js-save" ng-click="changeSureName()">确定</a>
                <a href="javascript:;" class="ui-btn js-cancel pull-right" ng-click="cancelChangeName()">取消</a>
            </div>
        </div>
        <div class="arrow"></div>
    </div>
    <!-- 删除分组 -->
    <div class="ui-popover top-center" id="delGroup">
        <div class="ui-popover-inner">
            <div style="margin-bottom: 6px;">确定删除分组？</div>
            <div style="margin-bottom: 6px;color:#888">
                仅删除分组，不删除图片，组内图片将自动归入未分组
            </div>
            <div class="clearfix">
                <a href="javascript:;" class="ui-btn ui-btn-primary js-save" ng-click="sureDelGroup()">确定</a>
                <a href="javascript:;" class="ui-btn js-cancel pull-right" ng-click="cancelDelGroup()">取消</a>
            </div>
        </div>
        <div class="arrow"></div>
    </div>
    <!-- 删除prover -->
    <div class="ui-popover top-center" id="image_prover">
        <div class="ui-popover-inner clearfix">
            <div>确定删除该图片？</div>
            <div style="margin: 6px 0 12px 0; color: #999;">若删除，不会对目前已使用该图片的相关业务造成影响。</div>
            <div class="clearfix">
                <a href="javascript:;" class="ui-btn ui-btn-primary js-save" ng-click="sureDeBtn()">确定</a>
                <a href="javascript:;" class="ui-btn js-cancel pull-right" ng-click="cancelDeBtn()">取消</a>
            </div>
        </div>
        <div class="arrow"></div>
    </div>
    <!-- 修改分组prover -->
    <div class="ui-popover top-center" id="changeGrounp">
        <div class="ui-popover-inner clearfix">
            <div>选择分组</div>
            <ul class="js-category-list" style="max-height: 192px; overflow-y: auto; margin: 8px 0">
                <li style="padding: 4px 2px;" ng-repeat = "group in grounps"> 
                    <label>
                        <input type="radio" name="category" value="{{group['id']}}" style="margin: 0;" ng-checked="group.checked">
                        <span ng-bind="group['name']"></span>
                    </label>
                </li>
            </ul>
            <div class="clearfix">
                <a href="javascript:;" class="ui-btn ui-btn-primary js-save" ng-click="changeSureBtn()">确定</a>
                <a href="javascript:;" class="ui-btn js-cancel pull-right" ng-click="cancelCgBtn()">取消</a>
            </div>
        </div>
        <div class="arrow"></div>
    </div>
    <!-- 添加分组 -->
    <div class="ui-popover top-center" id="addGpProver">
        <div class="ui-popover-inner">
            <div style="margin-bottom: 6px;">添加分组</div>
            <div style="margin-bottom: 6px;">
                <input class="form-control" type="text" value="" placeholder="不超过6个字" maxlength="6" id="grounp_title">
            </div>
            <div class="clearfix">
                <a href="javascript:;" class="ui-btn ui-btn-primary js-save" ng-click="addGpSureBtn()">确定</a>
                <a href="javascript:;" class="ui-btn js-cancel pull-right js-save" ng-click="cancelGpBtn()">取消</a>
            </div>
        </div>
        <div class="arrow"></div>
    </div>
    <!-- 修改分组名称 -->
    <div class="ui-popover top-center" id="chanageGruopNameProver" ng-show="groupDetail.isshow">
        <div class="ui-popover-inner">
            <div style="margin-bottom: 6px;">编辑名称</div>
            <div style="margin-bottom: 6px;">
                <input class="form-control" type="text" value=""  placeholder="不超过6个字" maxlength="6" ng-model="groupDetail.title">
            </div>
            <div class="clearfix">
                <a href="javascript:;" class="ui-btn ui-btn-primary js-save" ng-click="changeGroupNameSure()">确定</a>
                <a href="javascript:;" class="ui-btn js-cancel pull-right js-save" ng-click="changeGroupNameCancel()">取消</a>
            </div>
        </div>
        <div class="arrow"></div>
    </div>
     <!-- 广告图片model -->
    <div class="modal export-modal myModal-adv" id="myModal-adv">
        <div class="modal-dialog" id="modal-dialog-adv">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">图片上传</a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body">
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
                            <button class="ui-btn js-confirm ui-btn-primary" ng-show="uploadShow" ng-click="uploadSureBtn()">确认</button>
                        </div>
                    </div>
                </div>分组
            </form>
        </div>
    </div>
    <!--预览-->
    <div class='preview hide' id='preview' ng-click='closePreview()'>
        <div class='preview_close' ng-click='closePreview()'></div>
        <div class='preview_box' ng-click='$event.stopPropagation();'>
            <div class='preview_img' id='preview_img'></div>
            <div id='preview_down' style="line-height:50px;font-size: 18px;">
                <span style='color: #3197fa; cursor: pointer' ng-click='downloadImg()'>下载图片</span>
            </div>
        </div>
    </div>
</div>
@endverbatim
@endsection
<script type="text/javascript">
    var _thumbs = {!! $fileData or '[]' !!};
    var _data = {!! $classify or '[]' !!};
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var isCreate = "{{ $isCreate }}";
</script>
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script> 
<!-- 百度上传插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
<!-- 懒加载js -->
<script src="{{ config('app.source_url') }}/static/js/jquery.lazyload.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_xiixe4hu.js"></script>
@endsection     