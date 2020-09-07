@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/attachment_video.css" />
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
            <li>
                <a href="{{ URL('/merchants/store/attachmentImage') }}">图片</a>
            </li>
            <li class="hover">
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
                        <!-- <input type="text" class="txt" placeholder="搜索"> -->
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
                        <a href="javascript:void(0);" ng-click="changeGroupName($event)" class="group_name" ng-show="grounps.length >= 2 && groupDetail.show">重命名</a>
                        <a href="javascript:;" ng-click="delGroup($event)" class="delGroup" ng-show="grounps.length >= 2 && groupDetail.show">删除分组</a>
                    </span>
                    <a href="javascript:;" class="ui-btn ui-btn-success pull-right" ng-click="uploadVideo()">上传视频</a>
                </div>
                <div class="no-result ng-hide" ng-show="videos.length==0">
                    暂无数据，可点击右上角“上传视频按钮添加
                </div>
                <div class="has_data ng-hide" ng-show="videos.length">
                    <div class="table zent-table">
                        <div class="thead">
                            <div class="stickrow tr">
                                <div class="cell cell--selection" style="width: 180px; flex: 0 1 auto;">
                                    <div class="cell__child-container">
                                        <label class="select-check zent-checkbox-wrap">
                                            <span class="zent-checkbox">
                                                <span class="zent-checkbox-inner"></span>
                                                <input type="checkbox" ng-model="allChoose"></span>
                                        </label>
                                        视频
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="cell__child-container">
                                        <a>
                                            大小
                                        </a>
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="cell__child-container">
                                        <a>
                                            上传时间
                                            <span class="desc"></span>
                                        </a>
                                    </div>
                                </div>
                                <!-- <div class="cell cell--center">
                                    <div class="cell__child-container">
                                        <a>
                                            播放次数
                                        </a>
                                    </div>
                                </div> -->
                                <div class="cell">
                                    <div class="cell__child-container">
                                        状态
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="cell__child-container">
                                        操作
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tbody">
                            <div class=" tr" ng-repeat = "video in videos">
                                <div class="cell cell--selection" style="width: 180px; flex: 0 1 auto;">
                                    <label class="select-check zent-checkbox-wrap">
                                        <span class="zent-checkbox">
                                            <span class="zent-checkbox-inner"></span>
                                            <input type="checkbox" ng-model="video['ischoose']" ng-click="isChoose()"></span>
                                    </label>
                                    <div class="cell__child-container">
                                        <div class="material-image-card">
                                            <div class="image-box is-can-play" ng-click="playVideo(video)">
                                                <div class="image-cover" style="background-image: url({{video.file_cover}});">
                                                </div>
                                            </div>
                                            <div class="info-box">
                                                <p class="name">{{video['FileInfo']['name']}}</p>
                                                <p class="size"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="cell__child-container">
                                        <div>{{video['FileInfo']['size']}}</div></div>
                                </div>
                                <div class="cell">
                                    <div class="cell__child-container">
                                        <div ng-bind="video.created_at"></div></div>
                                </div>
                               <!--  <div class="cell cell--center">
                                    <div class="cell__child-container">
                                        <div>0</div></div>
                                </div> -->
                                <div class="cell">
                                    <div class="cell__child-container">
                                        <div>已上传</div>
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="cell__child-container">
                                        <div>
                                            <a ng-click="editVideo(video)">编辑</a>
                                            <span class="opts-seprate">|</span>
                                            <div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
                                                <a ng-click="removeImage($index,video,$event)" id="delete_{{$index}}">删除</a>
                                                <!-- react-empty: 144 --></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action-bar clearfix">
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
                仅删除分组，不删除视频，组内视频将自动归入未分组
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
            <div>确定删除此视频？</div>
            <div style="margin: 6px 0 12px 0; color: #999;">若删除，不会对目前已使用该视频的相关业务造成影响。</div>
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
   
   <!-- 视频上传 -->
    <div class="zent-dialog-r-wrap upload_video">
        <div class="zent-dialog-r rc-video-upload__dialog" style="min-width: 600px; max-width: 85%;">
            <button type="button" class="zent-dialog-r-close" ng-click="closeUploadVideo()">×</button>
            <div class="zent-dialog-r-body">
                <div class="zent-tabs rc-video-upload__tabs rc-video-upload__tabs--onlyone">
                    <div class="zent-tabs-nav zent-tabs-size-normal zent-tabs-type-slider zent-tabs-align-left zent-tabs-third-level">
                        <div class="zent-tabs-nav-content">
                            <div class="zent-tabs-scroll">
                                <div class="zent-tabs-tabwrap" role="tablist">
                                    <span class="zent-tabs-nav-ink-bar" style="width: 90px; left: 0px;"></span>
                                    <div>
                                        <div role="tab" aria-labelledby="zent-tabpanel-1-1" class="zent-tabs-tab zent-tabs-actived" aria-disabled="false" aria-selected="true">
                                            <div class="zent-tabs-tab-inner">
                                               上传视频
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="zent-tabs-panewrap">
                        <div role="tabpanel" id="zent-tabpanel-1-1" class="zent-tab-tabpanel ">
                            <form class="zent-form zent-form--horizontal rc-video-upload__form">
                                <div class="rc-video-upload__progress">
                                    <div class="rc-video-upload__progress-item">
                                        <span class="rc-video-upload__progress-item-close" ng-click="reUploadVideo()">×</span>
                                        <div class="rc-video-upload__progress-item-progress"></div>
                                        <div class="rc-video-upload__progress-item-detail">
                                            <span class="rc-video-upload__progress-item-detail-name">QQ20171107-091836 (1).mp4</span>
                                            <span class="rc-video-upload__progress-item-detail-speed"></span>
                                            <span class="rc-video-upload__progress-item-detail-total"></span>
                                            <span class="rc-video-upload__progress-item-detail-percent">
                                                100%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="zent-form__control-group add_video">
                                    <div class="zent-form__control-label">本地视频：</div>
                                    <div class="zent-form__controls">
                                        <div class="rc-video-upload__choose">
                                            +
                                            <input id="upload_video" type="file" placeholder="添加 +" accept=".mp4">
                                            <!-- <input id="upload_video" type="file" placeholder="添加 +" accept=".mp4, .mov, .m4v, .flv, .x-flv, .mkv, .wmv, .avi, .rmvb, .3gp, video/*"> -->
                                        </div>
                                        <p class="zent-form__help-desc">
                                            <span>
                                                点击“+”选择视频，视频大小不超过30 MB，建议宽高比16:9
                                                <br>
                                                支持的视频文件类型包括：mp4
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" name="video_url">
                                <input type="hidden" name="id">
                                <!-- has-error -->
                                <div class="zent-form__control-group  rc-video-upload__form-input video_name">
                                    <label class="zent-form__control-label">
                                        <em class="zent-form__required">*</em>
                                        名称：
                                    </label>
                                    <div class="zent-form__controls">
                                        <div class="zent-input-wrapper">
                                            <input type="text" class="zent-input" name="video_name" value="" placeholder="最长不超过10个字">
                                        </div>
                                        <p class="zent-form__error-desc">请输入不超过10个字的视频名称</p>
                                    </div>
                                </div>
                                <div class="zent-form__control-group rc-video-upload__form-input">
                                    <label class="zent-form__control-label">
                                        分组：
                                    </label>
                                    <div class="zent-form__controls">
                                        <div class="zent-popover-wrapper zent-select  " style="display: inline-block;">
                                            <!-- <div class="zent-select-text">选择视频分组</div> -->
                                            <select style="position: relative;top: 4px;" name="grounp">
                                                <option value="{{grounp.id}}" ng-repeat="grounp in grounps">{{grounp.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="zent-form__control-group rc-video-upload__form-input">
                                    <label class="zent-form__control-label">
                                        封面：
                                    </label>
                                    <div class="zent-form__controls">
                                        <div class="zent-popover-wrapper zent-select  " style="display: flex;align-items: center;position:relative;top:1px">
                                            <div class="image_views" style="height: 80px;overflow: hidden;display:none">
                                                <img src="" style="max-width:100%;max-height:100%">
                                            </div>
                                            <input type="hidden" name="image_url">
                                            <a style="position:relative;top:-30px" href="javascript:void(0);">修改图片<input type="file" id="upload_image" style="position:absolute;top:0;left:0;right:0;bottom:0;opacity:0;width: 100%;" accept="image/jpg, image/jpeg, image/png"></a>
                                        </div>
                                        <div>
                                            <span class="up_tip" style="margin-left:0">图片建议尺寸:宽度710px</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rc-video-upload__publish">
                                    <div>
                                        <label class="zent-checkbox-wrap zent-checkbox-checked">
                                            <span class="zent-checkbox">
                                               <!--  <span class="zent-checkbox-inner"></span> -->
                                                <input name="aggree_input" type="checkbox" value="">
                                            </span>
                                            <span>
                                                同意《
                                                <a href="https://www.huisou.cn/home/index/detail/654/news" target="_blank" rel="noopener noreferrer">视频上传服务协议</a>
                                                》
                                            </span>
                                        </label>
                                        <button type="submit" class="zent-btn-disabled zent-btn" disabled="">确定</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 视频弹窗 -->
    <div class="zent-dialog-r-backdrop"></div>
    <div class="zent-dialog-r-wrap video_model">
        <div class="zent-dialog-r rc-video-player__dialog" style="min-width: 450px; max-width: 75%;">
            <button type="button" class="zent-dialog-r-close" ng-click="closeVideo()">×</button>
            <div class="zent-dialog-r-body" id="video" style="width:100%;height:400px">
                
            </div>
        </div>
    </div>
    <!-- 删除弹窗 -->
    <div class="zent-portal zent-pop zent-popover zent-popover-internal-id-6 zent-popover-position-left-center" style="position: absolute; left: -5003px; top: 239px;display:none">
        <div data-reactroot="" class="zent-popover-content">
            <div class="zent-pop-inner">
                <div class="video-pop-wrap">
                    <p class="title">确定删除该视频？</p>
                    <p class="note">删除该视频后将无法恢复，所有使用该视频的页面对应的视频都无法播放。</p>
                </div>
                <div class="zent-pop-buttons">
                    <button type="button" class="zent-btn-primary zent-btn-small zent-btn">我再想想</button>
                    <button type="button" class="zent-btn-small zent-btn">确定删除</button>
                </div>
            </div>
            <i class="zent-pop-arrow"></i>
        </div>
    </div>
</div>
@endverbatim
@endsection
@section('page_js')
<script type="text/javascript">
    var _thumbs = {!! $fileData or '[]' !!};
    var _data = {!! $classify or '[]' !!};
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var videoUrl = "{{videoUrl()}}";
    var wid = "{{session('wid')}}";
    var isCreate = "{{ $isCreate }}";
</script>
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script> 
<!-- 百度上传插件 -->
<!-- <script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script> -->
<!-- <script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script> -->
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
<!-- 懒加载js -->
<script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js"></script>
<!-- 又拍云 -->
<script src="{{ config('app.source_url') }}static/js/spark-md5.js"></script>
<script src="{{ config('app.source_url') }}static/js/async.js"></script>
<script src="{{ config('app.source_url') }}static/js/upyun-mu.js"></script>
<script src="{{ config('app.source_url') }}static/js/md5.js"></script>
<!-- 视频 -->
<script src="{{ config('app.source_url') }}static/js/ckplayer/ckplayer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/attachment_video.js"></script>
@endsection     