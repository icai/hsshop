@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/apply_publish_store.css" /> 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/addApplyPage.css" /> 
   
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')

<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav"> 
            <li>  
                <a href="{{ URL('/merchants/distribute') }}">一键配置</a>
            </li>  
            <li> 
                <a href="{{ URL('/merchants/distribute/template') }}">分销模板</a>
            </li>  
			<li class="hover"> 
                <a href="{{ URL('merchants/distribute/applyList') }}">申请页面</a>
            </li>  
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content" ng-app="myApp" ng-controller="myCtrl">
        <input id="wid" type="hidden" value="{{session('wid')}}" />
        <!-- card开始 -->
        <div class="card">
            <div class="card_left">
                <div class="left_content">
                    <h1 ng-click="showPage()">
                        <span ng-bind="pageSeting.title"></span>
                    </h1>
                </div>
                <div class="app-entry" ng-cloak>
                    <div class="js-fields-region">
                        <div class="app-fields ui-sortable">
                            <div class="app-field clearfix @{{editor['editing']}}" data-type="@{{editor['type']}}" ng-repeat = 'editor in editors' style="background:@{{editor['bgcolor']}}" ng-click="tool($event,editor)" ng-mouseover="addboder($event)" ng-mouseout="removeboder($event,editor)" ng-drop="true" ng-drop-success="onDropPageComplete($index, $data,$event)">
                               
                                <header ng-if="editor['type'] == 'header'"></header>
                                <div ng-drag="true" ng-drag-data="editor">
                                    <!-- 标题添加 -->
                                    <add-title ng-if = "editor['type'] == 'title'"></add-title>
                                    <!-- 富文本编辑器框内容 -->
                                    <editor-text ng-if="editor['type'] == 'rich_text'" class="custom-richtext"></editor-text>
                                     <!-- 分割线 -->
                                    <separator-line ng-if = "editor['type'] == 'line'"></separator-line>
                                     <!-- 魔方 -->
                                     <cube ng-if="editor['type'] == 'cube'"></cube>
                                    <!-- 图片广告 -->
                                    <advs ng-if = "editor['type'] == 'image_ad'"></advs>
                                    <!-- 公告 -->
                                    <notice ng-if = "editor['type'] == 'notice'"></notice>
                                    <!-- 联系方式 -->
                                    <mobile ng-if="editor['type'] == 'mobile'"></mobile>
                                    
                                    <div class="actions"  ng-if="editor['type'] != 'bingbing' && editor['type'] != 'imageTextModel'">
                                        <div class="actions-wrap">
                                            <span class="action edit">编辑</span>
                                            <span class="action edit" ng-click="addContent($event,$index,editor,25)">加内容</span>
                                            <span ng-click="deleteAll($index)" class="action delete">删除</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-box">
                    <button class="submit-btn" disabled>提交申请</button>
                </div>
                <!-- 底部自定义导航 -->
                <div class="js-add-region">
                    <div>
                        <div class="app-add-field">
                            <h4>添加内容</h4>
                            <ul>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addTitle(1)">标题</a>
                                </li>
                                <li ng-click="addeditor(1)">
                                    <a class="js-new-field" data-field-type="rich_text">富文本</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="line" ng-click="separatorLine(1)">分割线</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addCube(1)">魔方</a>
                                </li>
                                <li ng-click="addAdvImages(1)">
                                    <a class="js-new-field" data-field-type="image_ad">图片广告</a>
                                </li>
                                <!-- 有权限 -->
                                <li>
                                    <a class="js-new-field" data-field-type="notice" ng-click="addNotice(1)">公告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addMobile(1)">联系方式</a>
                                </li>
                            </ul>
                        </div>    
                    </div>
                </div>
            </div>
            <form class="form-horizontal" novalidate="" name="editorForm" ng-cloak>
                <div class="card_right baseinfo" ng-show="!editors.length || first_card">
                    <div class="arrow"></div>
                    <!-- 基本信息右侧 -->
                    <div class="editor-content">
                        <div class="control-group">
                            <label class="control-label">
                                <em class="required">*</em>页面名称：
                            </label>
                            <div class="controls page_title">
                                <input class="form-control" type="text" name="title" value="分销客申请" ng-model="pageSeting.title" required>
                                <p class="help-block error-message ng-hide" ng-show="editorForm.title.$dirty && editorForm.title.$error.required || iserror && editorForm.title.$invalid">此项不能为空</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">背景颜色：</label>
                            <div class="controls">
                                <input type="color" name="color" class="form-control" ng-model="pageSeting.page_bgcolor">
                                <input class="form-control color_input" maxlength="7" type="text" value="#f8f8f8" ng-model="pageSeting.page_bgcolor" >
                               
                               <p class="help-desc">背景颜色只在手机端显示。</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">
                                分享标题：
                            </label>
                            <div class="controls page_title">
                                <input class="form-control" type="text" name="title" value="分享标题设置" ng-model="pageSeting.share_title">
                                <p class="help-block error-message ng-hide" ng-show="(pageSeting.share_desc || pageSeting.share_img) && !pageSeting.share_title">此项不能为空</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">
                                分享内容：
                            </label>
                            <div class="controls page_title">
                                <!-- <input class="form-control" type="text" name="title" value="分享标题设置" ng-model="pageSeting.title"> -->
                                <textarea cols="35" rows="5" style="border-radius: 4px;" name="share_desc" class="form-control ng-pristine ng-valid ng-touched" ng-model="pageSeting.share_desc"></textarea>
                                <p class="help-block error-message ng-hide" ng-show="(pageSeting.share_title || pageSeting.share_img) && !pageSeting.share_desc">此项不能为空</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">
                                分享图片：
                            </label>
                            <div class="controls page_title">
                                <ul class="module-goods-list clearfix ui-sortable" name="goods">
                                    <li class="sort ng-scope" ng-show="pageSeting.share_img">
                                        <a href="javascript:void(0);" target="_blank">
                                            <img ng-src="@{{pageSeting.share_img}}" width="50" height="50">
                                        </a>
                                        <a class="close-modal js-delete-goods small" data-id="" title="删除" ng-click="removeShareImg()">×</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="js-add-goods add-goods" ng-click="addShareImages()">
                                            <i class="icon-add"></i>
                                        </a>
                                    </li>
                                </ul>
                                <p class="help-block error-message ng-hide" ng-show="(pageSeting.share_title || pageSeting.share_desc) && !pageSeting.share_img">此项不能为空</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card_right card_right_list" ng-show="editors[index]['showRight']">
                    <div class="arrow"></div>
                    <div ng-show="!editors[index]['is_add_content']">
                       
                        <!-- 富文本编辑器右侧 -->
                        <cr-richtext ng-show="editors[index]['cardRight'] == 3"></cr-richtext>
                       <!-- 手机号码 -->
                       <crmobile ng-show="editors[index]['cardRight'] == 26"></crmobile>
                        <!-- 图片广告右侧 -->
                        <cradvs ng-if="editors[index]['cardRight'] == 5"></cradvs>
                        <!-- 标题右侧 -->
                        <crtitle ng-if="editors[index]['cardRight'] == 6"></crtitle>
                        <!-- 分割线右侧 -->
                        <crseparator-line ng-if="editors[index]['cardRight'] == 16"></crseparator-line>
                        <!-- 公告右侧 -->
                        <crnotice ng-if="editors[index]['cardRight'] == 9"></crnotice>
                        <!-- 魔方 -->
                        <crcube ng-if="editors[index]['cardRight'] == 25"></crcube>
                      
                    </div>
                    <div class="app-add-field app-add-field1" ng-show="editors[index]['is_add_content']">
                        <h4>添加内容</h4>
                        <ul>
                            <li>
                                <a class="js-new-field" data-field-type="title" ng-click="addTitle(2)">标题</a>
                            </li>
                            <li ng-click="addeditor(2)">
                                <a class="js-new-field" data-field-type="rich_text">富文本</a>
                            </li>
                            <li>
                                <a class="js-new-field" data-field-type="line" ng-click="separatorLine(2)">分割线</a>
                            </li>
                            <li>
                                <a class="js-new-field" ng-click="addCube(2)">魔方</a>
                            </li>
                            <li ng-click="addAdvImages(2)">
                                <a class="js-new-field" data-field-type="image_ad">图片广告</a>
                            </li>
                            <!-- 有权限 -->
                            <li>
                                <a class="js-new-field" data-field-type="notice" ng-click="addNotice(2)">公告</a>
                            </li>
                            <li>
                                <a class="js-new-field" ng-click="addMobile(2)">联系方式</a>
                            </li>
                        </ul>
                        
                    </div>
                </div>
                <div class="clear"></div>
                <div class="btn_grounp">
                    <button class="zent-btn zent-btn-primary js-btn-add" ng-click="processPage(editorForm.$valid)">上 架</button>
                </div>
            </form>
        </div>
       
      
        
        <!-- 广告图片model -->
        <div class="modal export-modal myModal-adv" id="myModal-adv">
            <div class="modal-dialog" id="modal-dialog-adv">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
                                        <div class="attachment-selected" ng-show="image['isShow']">
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
       
        <!--backdrop-->
        <div class="modal-backdrop"></div>
        <!-- prover -->
        <div class="popover popover-link-wrap bottom">
            <div class="arrow"></div>
            <div class="popover-inner popover-link">
                <div class="popover-content">
                    <div class="form-inline">
                        <input type="text" class="link-placeholder js-link-placeholder form-control" placeholder="链接地址：https://example.com">
                        <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定" ng-click="sureProver()">确定</button>
                        <button type="reset" class="btn js-btn-cancel" ng-click="cancelProver()">取消</button>
                    </div>
                </div>
            </div>
        </div>
       
        <!-- 标题修改prover -->
        <div class="ui-popover top-center" id="changeTitleProver">
            <div class="ui-popover-inner">
                <span></span>
                <input class="form-control" type="text" value="" placeholder="" maxlength="20" style="margin-bottom: 0;" id="title_input">
                <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-save" style="margin-left: 20px;" ng-click="sureChangeTitle()">确定</a>
                <a href="javascript:void(0);" class="zent-btn js-cancel" ng-click="cancelChnageTitle()">取消</a>
            </div>
            <div class="arrow"></div>
        </div>

        <!-- 图片广告自定义外链弹窗 -->
        <div class="ui-popover top-center" id="setWaiLink">
            <div class="ui-popover-inner">
                <span></span>
                <input class="form-control" type="text" value="" style="margin-bottom: 0;" id="wailink_input" placeholder="https://www.exemple.com">
                <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-save" style="margin-left: 20px;" ng-click="sureSetLink()">确定</a>
                <a href="javascript:void(0);" class="zent-btn js-cancel" ng-click="cancelSetLink()">取消</a>
            </div>
            <div class="arrow"></div>
        </div>
@endsection

@section('page_js')
	<script>
        var host = "{{ config('app.url') }}";
        var page_template = {!! json_encode($data) !!}
        console.log(page_template)
        var _host = "{{ config('app.source_url') }}";
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <!-- angular -->
    <script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
    <!-- ajax分页js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
    <!-- webuploader -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
    <!-- chosen -->
    <script src="{{ config('app.source_url') }}static/js/md5.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 模块公共js -->
    <script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/apply_model.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/apply_product_public.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/addApplyPage.js"></script>
@endsection
