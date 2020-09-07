@extends('merchants.default._layouts')
@section('head_css')
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <!-- <link rel="stylesheet" href="{{ config('app.source_url') }}static/js/kindeditor/themes/default/default.css" /> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/res_publish_store.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/res_z12g34jg.css" />
    <style type="text/css">
        .maketing_active{
            display: block !important;
        }
        .wsc_hide{
            display: none;
        }
        .page_title input.w240 {
            width: 148px !important;
        }
        .laydate_box, .laydate_box * { box-sizing:content-box; }
        .laydate_body .laydate_top {
            border-top: 1px solid #009F95;
            background-color: #FFFFFF !important;
            -moz-box-sizing: content-box;
            -webkit-box-sizing: content-box;
            -o-box-sizing: content-box;
            -ms-box-sizing: content-box;
            box-sizing: content-box;
        }
        .laydate_body .laydate_ym {
            border: 1px solid #009F95;
            background-color: #FFFFFF !important;
            -moz-box-sizing: content-box;
            -webkit-box-sizing: content-box;
            -o-box-sizing: content-box;
            -ms-box-sizing: content-box;
            box-sizing: content-box;
        }
        .laydate_body .laydate_table .laydate_click {
            /*background-color: #009F95 !important;*/
            color: #333333 !important;
        }
        .laydate_body .laydate_table td {
            border: none;
            height: 21px!important;
            line-height: 21px!important;
            background-color: #fff;
            color: #333333 !important;
        }
        .laydate_body .laydate_table .laydate_nothis {
            color: #999 !important;
        }
        .res_time{
            margin-top: 10px;
        }
    </style>
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
                    <a href="{{ config('app.url') }}merchants/marketing">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">在线报名</a>
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
    <!-- 主体 开始 -->
    <div class="content" ng-app="myApp" ng-controller="myCtrl">
        {{--<input id="wid" type="hidden" value="{{$wid}}" />--}}
        <input id="wid" type="hidden"/>
        <!-- add by 赵彬 2018-8-8 -->
        <h3 class='newres' ng-if="type==0">新建在线报名</h3>
        <h3 class='newres' ng-if="type==1">新建在线预约</h3>
        <h3 class='newres' ng-if="type==2">新建在线投票</h3>
        <!-- end -->
        <!--add by 邓钊 2018-6-27-->
        <!-- <ul class='type_ul clear'>
            <li ng-class="{'li_active' : type == 0}">留言编辑</li>
            <li ng-class="{'li_active' : type == 1}">预约编辑</li>
            <li ng-class="{'li_active' : type == 2}" class='last_li'>投票编辑</li>
        </ul> -->
        <!--end-->
        <!-- card开始 -->
        <div class="card">
            <div class="card_left">
                <div class="left_content">
                    <h1 ng-click="showPage()">
                        <span ng-bind="pageSeting.title"></span>
                    </h1>
                </div>
                <div class="app-entry" ng-cloak style="background:@{{pageSeting.background_color}}">
                    <div class="js-fields-region">
                        <div class="app-fields ui-sortable">
                            <div class="app-field clearfix @{{editor['editing']}}" data-type="@{{editor['type']}}" ng-repeat = 'editor in editors' style="background:@{{editor['bgcolor']}};margin-bottom: 5px;" ng-click="tool($event,editor)" ng-mouseover="addboder($event)" ng-mouseout="removeboder($event,editor)" ng-drop="true" ng-drop-success="onDropPageComplete($index, $data,$event)">
                                <header ng-if="editor['type'] == 'header'"></header>
                                <div ng-drag="true" ng-drag-data="editor">
                                    <!-- 编辑器框内容 -->
                                    <editor-text ng-if="editor['type'] == 'rich_text'" class="custom-richtext"></editor-text>
                                    <!-- 日期添加 -->
                                    <deta-time ng-if = "editor['type'] == 'time'"></deta-time>
                                    <!-- 文字添加 -->
                                    <text ng-if = "editor['type'] == 'text'"></text>
                                    <!-- 电话添加 -->
                                    <tel ng-if = "editor['type'] == 'phone'"></tel>
                                    <!-- 店铺导航 -->
                                    <email ng-if = "editor['type'] == 'email'"></email>
                                    <!-- 文本投票 -->
                                    <text-vote ng-if = "editor['type'] == 'vote_text'"></text-vote>
                                    <!-- 图片投票 -->
                                    <img-vote ng-if = "editor['type'] == 'vote_image'"></img-vote>
                                    <!-- 文本预约 -->
                                    <txtbooking ng-if = "editor['type'] == 'appoint_text'"></txtbooking>
                                    <!-- 图片预约 -->
                                    <imgbooking ng-if = "editor['type'] == 'appoint_image'"></imgbooking>
                                    <!-- 地域调查 -->
                                    <separator ng-if = "editor['type'] == 'address'"></separator>
                                    <!-- 图片 -->
                                    <upload ng-if = "editor['type'] == 'image'"></upload>
                                    <!-- 分割线 -->
                                    <separator-line ng-if = "editor['type'] == 'line'"></separator-line>
                                    <!-- 数字 -->
                                    <num ng-if = "editor['type'] == 'num'"></num>
                                    <!-- 预约时段 -->
                                    <timebooking ng-if = "editor['type'] == 'appoint_time'"></timebooking>
                                    <!-- 外观样式 -->
                                    <face-type ng-if = "editor['type'] == 'face_type'"></face-type>
                                    <!-- 图片设置 -->
                                    <img-set ng-if = "editor['type'] == 'set_image'">></img-set>
                                    <div class="actions"  ng-if="editor['type'] != 'bingbing' && editor['type'] != 'imageTextModel'">
                                        <div class="actions-wrap">
                                            <span class="action edit">编辑</span>
                                            <span ng-click="deleteAll($index)" class="action delete">删除</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-box" style="background:@{{pageSeting.background_color}}">
                    <button class="submit-btn" ng-bind="pageSeting.submit_button_title" style="background:@{{pageSeting.submit_button_color}};border:0;color:#fff" disabled></button>
                </div>
                <!-- 底部自定义导航 -->
                <div class="js-add-region" ng-show="is_custom == 1" ng-cloak>
                    <div>
                        <div class="app-add-field">
                            <h4>添加内容</h4>
                            <ul>
                                {{--<li ng-click="addeditor(1)">--}}
                                {{--<a class="js-new-field" data-field-type="rich_text">富文本</a>--}}
                                {{--</li>--}}
                                <li ng-click="adddataTime(1)">
                                    <a class="js-new-field" data-field-type="time">日期</a>
                                </li>
                                <li ng-click="addtext(1)">
                                    <a class="js-new-field" data-field-type="text">文本框</a>
                                </li>
                                <li  ng-click="addTel(1)">
                                    <a class="js-new-field" data-field-type="phone">电话</a>
                                </li>
                                {{--<li ng-click="addEmail(1)">--}}
                                {{--<a class="js-new-field" data-field-type="email">--}}
                                {{--邮箱--}}
                                {{--</a>--}}
                                {{--</li>--}}
                                <!--update by 邓钊 2018-6-27-->
                                <li ng-if='type == 2'>
                                    <a class="js-new-field" data-field-type="vote_text" ng-click="addTextOption(1)">文本投票</a>
                                </li>
                                <li ng-if='type == 2'>
                                    <a class="js-new-field" data-field-type="vote_image" ng-click="addImages(1)">图片投票</a>
                                </li>
                                <!--end-->
                                <li>
                                    <a class="js-new-field" data-field-type="image" ng-click="addUpload(1)">图片</a>
                                </li>
                                <!--update by 邓钊 2018-6-27-->
                                <li ng-if='type == 1'>
                                    <a class="js-new-field" data-field-type="appoint_text" ng-click="txtBooking(1)">文本预约</a>
                                </li>
                                <li ng-if='type == 1'>
                                    <a class="js-new-field" data-field-type="appoint_image" ng-click="imgBooking(1)">图片预约</a>
                                </li>
                                <!--end-->
                                <li>
                                    <a class="js-new-field" data-field-type="address" ng-click="addSeparator(1)">地域调查</a>
                                </li>
                                <!--update by 赵彬 2018-7-13-->
                                <li ng>
                                    <a class="js-new-field" data-field-type="line" ng-click="separatorLine(1)">分割线</a>
                                </li>
                                <li ng-if='type == 1'>
                                    <a class="js-new-field" data-field-type="num" ng-click="addNum(1)">数字</a>
                                </li>
                                <li ng-if='type == 1'>
                                    <a class="js-new-field" data-field-type="appoint_time" ng-click="timeBooking(1)">预约时段</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="face_type" ng-click="faceType(1)">外观样式</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="set_image" ng-click="imgSet(1)">图片设置</a>
                                </li>
                                <!-- end -->
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
                                <em class="required">*</em>标题：
                            </label>
                            <div class="controls page_title">
                                <input class="form-control" type="text" name="title" value="调查留言" ng-model="pageSeting.title" required>
                                <p class="help-block error-message ng-hide" ng-show="editorForm.title.$dirty && editorForm.title.$error.required || iserror && editorForm.title.$invalid">此项不能为空</p>
                            </div>
                            <label class="control-label res_time">
                                <em class="required">*</em>有效期：
                            </label>
                            <div class="controls page_title res_time">
                                <input class="form-control w240 iblock valid" type="text" id="startTime" ng-model='pageSeting.start_at' >
                                至
                                <input class="form-control w240 iblock valid" type="text" id="endTime" value='1235465151' ng-model='pageSeting.end_at' >
                            </div>
                            <label class="control-label res_time">
                                <em class="required">*</em>参与次数：
                            </label>
                            <div class="controls page_title res_time radio_box">
                                <div><input ng-model="pageSeting.times_type" ng-checked="pageSeting.times_type == 0" value="0" type="radio">只能参与一次</div>
                                <div><input ng-model="pageSeting.times_type" ng-checked="pageSeting.times_type == 1" type="radio"  value='1'>可参与多次（取最后一次为结果）</div>
                                <div><input ng-model="pageSeting.times_type" ng-checked="pageSeting.times_type == 2" type="radio"  value='2'>可参与多次（每人最多可投10次，结果可以累加）</div>
                            </div>
                            <label class="control-label res_time page_bg_color">页面背景颜色：
                            </label>
                            <div class="controls page_title res_time">
                                <input type="color" name="color" class="form-control" ng-model="pageSeting.background_color">
                            </div>
                            <div class="button-tip">说明：以下设置只应用于"提交"相关操作，按钮名称可自定义</div>
                            <label class="control-label">按钮名称：
                            </label>
                            <div class="controls page_title">
                                <input type="text" class="form-control" ng-model="pageSeting.submit_button_title">
                            </div>
                            <label class="control-label res_time ">背景颜色：
                            </label>
                            <div class="controls page_title res_time">
                                <input type="color" name="color" class="form-control" ng-model="pageSeting.submit_button_color">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card_right card_right_list" ng-show="editors[index]['showRight']">
                    <div class="arrow"></div>
                    <div ng-show="!editors[index]['is_add_content']">
                        <!-- 富文本编辑器右侧 -->
                        <cr-richtext ng-show="editors[index]['cardRight'] == 3"></cr-richtext>
                        <!-- 日期添加 -->
                        <crdeta-time ng-if="editors[index]['cardRight'] == 4"></crdeta-time>
                        <!-- 文字添加 -->
                        <crtext ng-if="editors[index]['cardRight'] == 5"></crtext>
                        <!-- 电话添加 -->
                        <crtel ng-if="editors[index]['cardRight'] == 6"></crtel>
                        <!-- 店铺导航右侧 -->
                        <cremail ng-if="editors[index]['cardRight'] == 7"></cremail>
                        <!-- 文本投票 -->
                        <crtext-vote ng-if="editors[index]['cardRight'] == 9"></crtext-vote>
                        <!-- 图片投票 -->
                        <crimg-vote ng-if="editors[index]['cardRight'] == 11"></crimg-vote>
                        <!-- 图片 -->
                        <crupload ng-if="editors[index]['cardRight'] == 12"></crupload>
                        <!-- 文本预约 -->
                        <crtxtbooking ng-if="editors[index]['cardRight'] == 13"></crtxtbooking>
                        <!-- 图片预约 -->
                        <crimgbooking ng-if="editors[index]['cardRight'] == 14"></crimgbooking>
                        <!-- 地域调查 -->
                        <crseparator ng-if="editors[index]['cardRight'] == 15"></crseparator>
                        <!-- 分割线右侧 -->
                        <crseparator-line ng-if="editors[index]['cardRight'] == 16"></crseparator-line>
                        <!-- 数字右侧 -->
                        <crnum ng-if="editors[index]['cardRight'] == 17"></crnum>
                        <!-- 预约时段右侧 -->
                        <crtimebooking ng-if="editors[index]['cardRight'] == 18"></crtimebooking>
                        <!-- 外观样式右侧 -->
                        <crface-type ng-if="editors[index]['cardRight'] == 19"></crface-type>
                        <!-- 图片设置右侧 -->
                        <crimg-set ng-if = "editors[index]['cardRight'] == 20">></crimg-set>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="btn_grounp">
                    <button class="zent-btn zent-btn-primary js-btn-add" ng-click="processPage(editorForm.$valid,1)">上 架</button>
                    {{--<button class="zent-btn js-btn-save" ng-click="processPage(editorForm.$valid,0)">保存成草稿</button>--}}
                    <!-- <button class="zent-btn js-btn-preview" ng-click="previewPage(editorForm.$valid,0)">预览效果</button> -->
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

    </div>
    <!-- 主体 结束 -->
    <!-- 二维码prover -->
    <div class="js-intro-popover popover popover-help-notes bottom center homepage-qrcode" id="qrcode">
        <div class="arrow"></div>
        <div class="popover-inner">
            <div class="popover-content">
                <p>微信扫一扫访问：</p>
                <p class="team-code">
                    <img src="" alt=""></p>
                <p>
                    <a href="javascript:void(0);" download="">下载二维码</a></p>
            </div>
        </div>
    </div>
    <!-- 中间 结束 -->
    <!-- 右侧 开始 -->
    <div class="right">
        <!-- 帮助和服务顶部 开始 -->
        <div class="right_header">
            <i class="glyphicon glyphicon-question-sign"></i>
            <span>帮助和服务</span>
            <i id="help-container-close" class="close_btn">x</i>
        </div>
        <!-- 帮助和服务顶部 结束 -->
        <!-- 帮助和服务主体内容 开始 -->
        <div class="right_body">
            <h5 class="body_title">买家怎么访问我的店铺？</h5>
            <div class="body_content">点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问点击访问</div>
        </div>
        <!-- 帮助和服务主体内容 结束 -->
    </div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var host = "{{ config('app.url') }}";
    </script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <!-- <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script> -->
    <!-- 核心 base.js JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
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
    <!-- <script type="text/javascript" src="{{ config('app.source_url') }}static/js/upload.js"></script> -->
    <!-- chosen -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <script>
        var _host = "{{ config('app.source_url') }}";
        var imgUrl = "{{ imgUrl() }}";

        var editData = {!! json_encode($data) !!};
        var start = {
            elem: '#startTime',
            format: 'YYYY-MM-DD hh:mm:ss',
            min: laydate.now(), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function(datas){
                $('#startTime').val(datas);
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end ={
            elem: '#endTime',
            format: 'YYYY-MM-DD hh:mm:ss',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function(datas){
                $('#endTime').val(datas);
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
    </script>
    <!-- 模块公共js -->
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/res_model.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/res_product_public.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/res_z12g34jg_edit.js"></script>
@endsection