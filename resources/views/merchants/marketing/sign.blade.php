@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" href="{{ config('app.source_url') }}static/js/kindeditor/themes/default/default.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
<!-- 公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/publish_store.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/sign_b55f7i3j.css" />
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
                <a href="javascript:void(0);">签到</a>
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
<div class="content" ng-app="myApp" ng-controller="myCtrl">
    <input id="wid" type="hidden" value="{{ $wid }}" />
    <div class="widget-app-board ui-box">
        <div class="widget-app-board-info">
            <h3>设置签到</h3>
            <div>
                <p>注意：请勿频繁修改“签到”的设置；正确设置后再开启“签到”功能</p>
            </div>
        </div>
        <div class="widget-app-board-control">
            <label class="js-switch ui-switcher pull-right" ng-class="baseInfo.ison ? 'ui-switcher-on' : 'ui-switcher-off'" ng-click="switcher($event)"></label>
        </div>
    </div>
    <!-- card开始 -->
    <div class="card">
        <form class="form-horizontal" novalidate="" name="editorForm">
            <div class="card_left">
                <div class="left_content">
                    <h1>
                        <span ng-bind="editors[0]['title']"></span>
                    </h1>
                </div>
                <div class="app-entry">
                    <div class="js-fields-region">
                        <div class="app-fields ui-sortable">
                            <div class="app-field clearfix @{{editor['editing']}}" data-type="@{{editor['type']}}" ng-repeat = 'editor in editors' style="background:@{{editor['bgcolor']}}" ng-click="tool($event,editor)" ng-mouseover="addboder($event)" ng-mouseout="removeboder($event,editor)" ng-drop="true" ng-drop-success="onDropPageComplete($index, $data,$event)">
                                <!-- 签到 -->
                                <div class="checkin-snapshot" ng-if="editor['type'] == 'sign'"></div>
                                <div ng-drag="true" ng-drag-data="editor">
                                    <!-- 优化券 -->
                                    <coupon ng-if="editor['type'] == 'coupon'"></coupon>
                                    <!-- 会员中心默认 -->
                                    <member ng-if="editor['type'] == 'member'"></member>
                                    <!-- 编辑器框内容 -->
                                    <editor-text ng-if="editor['type'] == 'rich_text'"></editor-text>
                                    <!-- 商品添加 -->
                                    <goods ng-if = "editor['type'] == 'goods'"></goods>
                                    <!-- 广告添加 -->
                                    <advs ng-if = "editor['type'] == 'image_ad'"></advs>
                                    <!-- 标题添加 -->
                                    <add-title ng-if = "editor['type'] == 'title'"></add-title>
                                    <!-- 店铺导航 -->
                                    <shop ng-if = "editor['type'] == 'store'"></shop>
                                    <!-- 公告 -->
                                    <notice ng-if = "editor['type'] == 'notice'"></notice>
                                    <!-- 商品搜索 -->
                                    <search ng-if="editor['type'] == 'search'"></search>
                                    <!-- 商品列表 -->
                                    <goodslist ng-if = "editor['type'] == 'goodslist'"></goodslist>
                                    <!-- 自定义model -->
                                    <model ng-if = "editor['type'] == 'model'"></model>
                                    <!-- 商品分组 -->
                                    <goodgroup ng-if="editor['type'] == 'good_group'"></goodgroup>
                                    <div class="actions" ng-show="$index !=0">
                                        <div class="actions-wrap">
                                            <span class="action edit">编辑</span>
                                            <span class="action edit" ng-click="addContent($event,$index,editor,130)">加内容</span>
                                            <span ng-click="deleteAll($index)" class="action delete">删除</span>
                                        </div>
                                    </div>
                                </div>
                            </div>                               
                        </div>
                    </div>
                </div>
                <!-- 底部自定义导航 -->
                <div class="js-add-region">
                    <div>
                        <div class="app-add-field">
                            <h4>添加内容</h4>
                            <ul>
                                <li ng-click="addeditor(1)">
                                    <a class="js-new-field" data-field-type="rich_text">富文本</a>
                                </li>
                                <li ng-click="addgoods(1)">
                                    <a class="js-new-field" data-field-type="goods">商品</a>
                                </li>
                                <li ng-click="addAdvImages(1)">
                                    <a class="js-new-field" data-field-type="image_ad">图片
                                        <br>广告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addTitle(1)">标题</a>
                                </li>
                                <li ng-click="addShop(1)">
                                    <a class="js-new-field" data-field-type="store">
                                        进入<br>店铺
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addCoupon()">优惠券</a>
                                </li> -->
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addNotice(1)">公告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addSearch(1)">商品<br />搜索</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="goddslist" ng-click="addGoodsList(1)">商品<br />列表</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="model" ng-click="addModel(1)">自定义<br />模块</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(1)">商品<br />分组</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card_right card_right_list" ng-show="editors[index]['showRight']"  ng-cloak>
                <div class="checkin-preview-btn-wrap js-checkin-opt-wrap">
                    <a class="btn btn-default js-checkin-copy-url" href="javascript:void(0);" ng-click="copyLink($event)">复制地址</a>
                    <input type="hidden" name="link" value='{{URL("shop/point/sign/$wid")}}'>
                    <a class="btn btn-default js-checkin-preview" href="javascript:void(0);" ng-click="showQrcode($event)">二维码</a>
                </div>
                <div class="arrow"></div>
                <div ng-show="!editors[index]['is_add_content']">
                    <div class="editer-content sign_content" ng-if="editors[index]['cardRight'] == 16">
                        <h4 class="field-title">活动信息</h4>
                        <div class="control-group">
                            <label class="control-label">
                                <em class="required">*</em>活动名称：</label>
                            <div class="controls">
                                <input type="text" name="sign_title" value="" ng-model="editors[index]['activityName']" ng-maxlength="30" class="form-control" required>
                                <p class="help-block error-message" ng-show="editorForm.sign_title.$dirty && editorForm.sign_title.$error.required">此项不能为空</p>
                                <p class="help-block error-message" ng-show="editorForm.sign_title.$dirty && editorForm.sign_title.$error.maxlength">长度不能大于30</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">活动说明：</label>
                            <div class="controls">
                                <textarea name="content" cols="28" maxlength="100"  style="resize:auto;border-radius: 5px;padding-left: 10px" ng-model="editors[index]['activityInfo']"></textarea>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">分享文案：</label>
                            <div class="controls">
                                <input type="text" name="shareText" ng-model="editors[index]['shareText']" value="" maxlength="30" placeholder="来签个到吧！" class="form-control"  ng-model="editor['shareText']">
                                <div class="ui-block-head-help">
                                    <a href="javascript:void(0);" class="js-help-notes" data-class="bottom center"></a>
                                    <div class="js-notes-cont hide">
                                        <p>此文案用于分享到朋友圈、微信群、微信好友</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4 class="field-title">设置连续签到</h4>
                        <div class="checkin-rule-list-wrap">
                            <div>
                                <div class="rule-list-empty-info" ng-show="!editors[index]['signList'].length">未设置规则</div>
                                <div class="rule-list-wrap">
                                    <div class="checkin-rule-wrap" ng-repeat="list in editors[index]['signList']">
                                        <div class="control-group">
                                            <label class="control-label">每连续签到：</label>
                                            <div class="controls">
                                                <input type="text" name="checkin_times_@{{$index}}" value="1" ng-model="list['signDay']" class="form-control width_30" ensure-integer> 天
                                                <p class="help-block error-message" ng-show="editorForm.checkin_times_@{{$index}}.$dirty && editorForm.checkin_times_@{{$index}}.$error.integer || editorForm.checkin_times_@{{$index}}.$pristine && iserror && editorForm.checkin_times_@{{$index}}.$error.integer">此项必须是数字</p>
                                            </div>
                                        </div>
                                        <div class="control-group js-gift-error">
                                            <label class="control-label">送积分：</label>
                                            <div class="controls">
                                                <input type="text" name="points_@{{$index}}" class="form-control width_30" min="0" value="0" ng-model="list['signCredite']" maxlength="6" ensure-integer> 个
                                                <p class="help-block error-message" ng-show="editorForm.points_@{{$index}}.$dirty && editorForm.points_@{{$index}}.$error.integer || editorForm.points_@{{$index}}.$pristine && iserror && editorForm.points_@{{$index}}.$error.integer">此项必须是数字</p>
                                            </div>
                                        </div>
                                        
                                        <div class="control-group">
                                            <label class="control-label">领取限制：</label>
                                            <div class="controls">
                                                <label class="checkbox inline">
                                                    <span class="ui-tooltip">
                                                        <input type="checkbox" name="people_once" ng-model="list['limit']">每人仅领一次</span>
                                                    <div class="ui-block-head-help">
                                                        <a href="javascript:void(0);" class="js-help-notes" data-class="bottom center"></a>
                                                        <div class="js-notes-cont hide">
                                                            <p>领取限制，表明该粉丝在第一次满足设定的连续签到次数时，可领取奖励；以后再满足则不能领取</p>
                                                        </div>
                                                    </div>
                                                </label>
                                                <a href="javascript:;" class="js-delete control-opts pull-right" ng-click="removeSignList($index)">删除</a>
                                            </div>
                                        </div>
                                        <p class="help-message"></p>
                                    </div>
                                </div>
                                <p class="checkin-rule-list-opt">
                                    <a href="javascript:;" class="js-add" ng-click="addSignList()">增加一条</a>
                                </p>
                            </div>
                        </div>
                        <hr>
                        <h4 class="field-title">设置分享页</h4>
                        <div class="checkin-rule-list-wrap">
                            <div class="control-group">
                                <label class="control-label" style="width: 98px">分享标题设置：</label>
                                <div class="controls">
                                    <input type="text" name="share_title" value="" ng-model="editors[index]['shareTitle']" ng-maxlength="30" class="form-control" style="width:250px;">
                                    <p class="help-block error-message ng-hide" ng-show="(editors[index]['shareDesc'] || editors[index]['share_img']) && !editors[index]['shareTitle']">此项不能为空</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" style="width: 98px">分享内容设置：</label>
                                <div class="controls">
                                    <textarea name="share_desc" cols="28" maxlength="100"  style="resize:auto;border-radius: 4px;padding:10px;" ng-model="editors[index]['shareDesc']"></textarea>
                                    <p class="help-block error-message ng-hide" ng-show="(editors[index]['shareTitle'] || editors[index]['share_img']) && !editors[index]['shareDesc']">此项不能为空</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" style="width: 98px">分享页图片：</label>
                                <div class="controls">
                                    <input type="hidden" name="share_img" ng-model="editors[index]['shareImg']">
                                    <div class="share_img_box" ng-show="editors[index]['share_img']">
                                        <img ng-src="@{{editors[index]['share_img']}}" style="width: 80px;height: 80px;" class="share_img" >
                                        <a class="delete" href="javascript:void(0);" ng-click="deleteShareImg(index)">x</a>
                                    </div>
                                    <a href="javascript:;" class="js-add-picture" ng-class="{true:'add-goods',false:'add-goods2'}[classShow]" ng-click="addShareImages()">@{{addPic}}</a>
                                    <p class="help-block error-message ng-hide" ng-show="(editors[index]['shareTitle'] || editors[index]['shareDesc']) && !editors[index]['share_img']">此项不能为空</p>
                                </div>
                                <p class="up_tip">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</p>
                            </div>
                        </div>
                    </div>
                    <cr-member ng-if="editors[index]['cardRight'] == 1"></cr-member>
                    <!-- 富文本编辑器右侧 -->
                    <cr-richtext ng-show="editors[index]['cardRight'] == 3"></cr-richtext>
                    <!-- 商品右侧 -->
                    <cr-goods ng-if="editors[index]['cardRight'] == 4"></cr-goods>
                    <!-- 广告右侧 -->
                    <cradvs ng-if="editors[index]['cardRight'] == 5"></cradvs>
                    <!-- 标题右侧 -->
                    <crtitle ng-if="editors[index]['cardRight'] == 6"></crtitle>
                    <!-- 店铺导航右侧 -->
                    <crshop ng-if="editors[index]['cardRight'] == 7"></crshop>
                    <!-- 优惠券右侧 -->
                    <crcoupon ng-if="editors[index]['cardRight'] == 8"></crcoupon>
                    <!-- 公共广告右侧 -->
                    <crnotice ng-if="editors[index]['cardRight'] == 9"></crnotice>
                    <!-- 商品搜索右侧 -->
                    <crsearch ng-if="editors[index]['cardRight'] == 11"></crsearch>
                    <!-- 商品列表右侧 -->
                    <crgoodslist ng-if="editors[index]['cardRight'] == 12"></crgoodslist>
                    <!-- 自定义模块右侧 -->
                    <crmodel ng-if="editors[index]['cardRight'] == 13"></crmodel>
                    <!-- 商品分组 -->
                    <crgoodgroup ng-if="editors[index]['cardRight'] == 14"></crgoodgroup>
                </div>
                <div class="app-add-field app-add-field1" ng-show="editors[index]['is_add_content']">
                    <h4>添加内容</h4>
                    <ul>
                        <li ng-click="addeditor(2)">
                            <a class="js-new-field" data-field-type="rich_text">富文本</a>
                        </li>
                        <li ng-click="addgoods(2)">
                            <a class="js-new-field" data-field-type="goods">商品</a>
                        </li>
                        <li ng-click="addAdvImages(2)">
                            <a class="js-new-field" data-field-type="image_ad">图片
                                <br>广告</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="title" ng-click="addTitle(2)">标题</a>
                        </li>
                        <li ng-click="addShop(2)">
                            <a class="js-new-field" data-field-type="store">
                                进入<br>店铺
                            </a>
                        </li>
                        <!-- <li>
                            <a class="js-new-field" data-field-type="coupon" ng-click="addCoupon(2)">优惠券</a>
                        </li> -->
                        <li>
                            <a class="js-new-field" data-field-type="notice" ng-click="addNotice(2)">公告</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="goods" ng-click="addSearch(2)">商品<br />搜索</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="goddslist" ng-click="addGoodsList(2)">商品<br />列表</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="model" ng-click="addModel(2)">自定义<br />模块</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(2)">商品<br />分组</a>
                        </li>
                        <!-- <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addLinkImages(2)">图片<br />导航</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addtextLink(2)">文本<br />导航</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="marketing_active" ng-click="addActive(2)">营销<br />活动</a>
                        </li> -->
                        <!-- <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addheader()">文本<br />导航</a>
                        </li> -->
                        <!-- <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addBingBing()">文本<br />导航</a>
                        </li> -->
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </form>
    </div>
    <div class="btn_grounp design_bottom">
        <button class="btn btn-primary" ng-click="processNotice(editorForm.$valid)">保存</button>
    </div>
    <!-- 选择商品弹窗 -->
    <div class="modal export-modal" id="myModal">
        <div class="modal-dialog" id="modal-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">已上架商品</a>|</li>
                            <li style="display: none;">
                                <a href="#js-module-tag" data-type="tag" class="js-modal-tab">商品分组</a>|</li>
                            <li class="link-group link-group-0" style="display: inline-block;">
                                <a class="co_38f" target="_blank" href="{{URL('/merchants/product/create')}}">新建商品</a></li>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchGoods()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in goodList" id="list_@{{list.id}}" data-price="@{{list['price']}}">
                                    <td class="image">
                                        <div class="td-cont">
                                            <img ng-src="@{{list['thumbnail']}}">
                                        </div>
                                    </td>
                                    <td class="title">
                                        <div class="td-cont">
                                            <a target="_blank" class="new_window" href="javascript:void(0);">@{{list['name']}}</a>
                                            
                                        </div>
                                    </td>
                                    <td>
                                        <span>
                                            @{{list['timeDay']}}
                                            <br>@{{list['timestamp']}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="choose($index,list)">选取</button> 
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer clearfix">
                        <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseSure()">
                            <input type="button" class="btn btn-primary" value="确定使用">
                        </div>
                        <div class="good_pagenavi"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 选择商品弹窗 -->
    <!-- 选择优惠券弹窗 -->
    <div class="modal export-modal" id="my_coupon_model">
        <div class="modal-dialog" id="coupon_model-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">优化券</a>|</li>
                            <li class="link-group link-group-0" style="display: inline-block;">
                                <a href="/merchants/marketing/coupon/set" target="_blank" class="new_window">新建优惠券</a></li>
                        </ul>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped ui-table ui-table-list">
                            <thead>
                                <tr>
                                    <th class="col-sm-4 col-xs-4">
                                        <div class="td-cont">
                                            <span>名称 </span>
                                            <!-- <a href="#">刷新</a> -->
                                        </div>
                                    </th>
                                    <th class="col-sm-2 col-xs-2">
                                        <div class="td-cont">
                                            <span>面值</span>
                                        </div>
                                    </th>
                                    <!-- <th class="information"></th> -->
                                    <th class="col-sm-2 col-xs-2">
                                        <div class="td-cont">
                                            <span>使用条件</span>
                                        </div>
                                    </th>
                                    <th class="opts" class="col-sm-4 col-xs-4">
                                        <div class="td-cont">
                                            <form class="form-search">
                                                <div class="input-append">
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search"  ng-click="searchCoupon()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in couponList" id="list_@{{$index}}" data-price="@{{list['price']}}">
                                    <td class="image">
                                        <div class="td-cont">
                                           @{{list['name']}} 
                                        </div>
                                    </td>
                                    <td>
                                        <span>
                                            @{{list['amount']}}
                                        </span>
                                    </td>
                                    <td>
                                        <span>
                                            @{{list['limit_desc']}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseCoupon($index,list)">选取</button> 
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer clearfix">
                        <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseCouponSure()">
                            <input type="button" class="btn btn-primary" value="确定使用">
                        </div>
                        <div class="coupon_pagenavi"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 选择优惠券弹窗 -->
    <!-- 广告图片model -->
    <div class="modal export-modal myModal-adv" id="myModal-adv">
        <div class="modal-dialog" id="modal-dialog-adv">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header" style="padding:0">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li ng-show="uploadShow">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab"
                                   ng-show="uploadShow" ng-click="showImage()">< 选择图片 |</a>
                            </li>
                            <li ng-show="!uploadShow">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab" style="color:#3197fa">我的图片</a>
                            </li>
                            <li class="active" ng-show="uploadShow">
                                <a href="#js-module-tag" data-type="tag" class="js-modal-tab">上传图片</a>
                            </li>
                        </ul>
                       <!--  <div class="search-region">
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
                            <div class='add_group'>
                                <div class="add_group_list"  ng-click="showAddGroup()">+添加分组</div>
                                <div class="add_group_box" ng-show="groupShow">
                                    <div class='add_group_title'>添加分组</div>
                                    <input class='add_group_input' placeholder='不超过6个字' type="text" maxlength='6' ng-model="groupName">
                                    <div class='clearfix add_group_btn'>
                                        <div class="btn_left" ng-click="addGroup()">确定</div>
                                        <div class="btn_right" ng-click="hideAddGrounp()">取消</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="attachment-list-region js-attachment-list-region">
                            <div class="imgDate">
                                <ul class="image-list" ng-show="picNumber">
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
                                        <span class="ui-pagination-total">共8条， 每页15条</span>
                                    </div>
                                    <a href="javascript:;" class="ui-btn ui-btn-success js-show-upload-view" style="position:absolute; bottom: 11px; left:20px" ng-click="upload()" ng-show="picNumber">上传图片</a>
                                </div>
                                <!--列表中的图片个数为0的时候显示这个模态框  2018-10-22 增加picNumber属性-->
                                <div id="layerContent_right" ng-show="!picNumber">
                                    <a class="js_addImg" href="#uploadImg" ng-click="upload()">+</a>
                                    <p>暂无数据，点击添加</p>
                                </div>
                            </div>
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
                    <input type="text" class="link-placeholder js-link-placeholder form-control" placeholder="链接地址：http://example.com">
                    <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定" ng-click="sureProver()">确定</button>
                    <button type="reset" class="btn js-btn-cancel" ng-click="cancelProver()">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 底部logo 结束 -->
    <!-- 上传model -->
    <div class="modal export-modal myModal-adv" id="upload_model">
        <div class="modal-dialog" id="upload_model_content">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">上传图片</a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body">
                        <div class="attachment-list-region js-attachment-list-region">
                            <div id="uploader" class="wu-example">
                                <div class="queueList">
                                    <div id="dndArea" class="placeholder">
                                        <label id="filePicker"></label>
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
                           <!--  <button class="ui-btn js-confirm ui-btn-disabled" disabled="disabled" ng-show="!chooseSureBtn">确认</button> -->
                            <button class="ui-btn js-confirm ui-btn-primary" ng-click="chooseAdvSureBtn()">确认1</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 添加微页面Model -->
    <div class="modal export-modal" id="page_model">
        <div class="modal-dialog" id="page-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">微页面</a>
                                <span>|</span>
                            </li>
                            <li style="display: none;">
                                <a href="#js-module-tag" data-type="tag" class="js-modal-tab">商品分组</a>
                                <span>|</span>
                            </li>
                            <li class="link-group link-group-0" style="display: inline-block;">
                                <a href="/v2/showcase/goods/edit" target="_blank" class="new_window">新建微页面</a>
                            </li>
                           <!--  <li class="link-group link-group-0" style="display: inline-block;">
                                <a href="/v2/showcase/goods#list&amp;is_display=0" target="_blank" class="new_window">草稿管理</a>
                            </li> -->
                            <li class="link-group link-group-1" style="display: none;">
                                <a href="/v2/showcase/tag" target="_blank" class="new_window">分组管理</a>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchPage()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in pageList">
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="choosePageLinkSure($index,list)">选取</button> 
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
    <!-- 添加自定义Model -->
    <div class="modal export-modal" id="component_model">
        <div class="modal-dialog" id="component_model_dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">自定义页面模块</a>
                                <span>|</span>
                            </li>
                            <li class="link-group link-group-0" style="display: inline-block;">
                                <a href="/merchants/store/componentAdd" target="_blank" class="new_window">新建自定义页面模块</a>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchComponent()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in components">
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseComponent($index,list)">选取</button> 
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
                        <div class="page_component"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 商品列表商品分组选择Model -->
    <div class="modal export-modal" id="goodslist_model">
        <div class="modal-dialog" id="goodslist_model_dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">商品分组</a>
                                <span>|</span>
                            </li>
                            <li>
                                <a href="/merchants/product/productGroup" data-type="tag" class="js-modal-tab" target="_blank">分组管理</a>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchShopGroup()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in goodsGroupList">
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseShopGroupSure($index,list)" ng-class="list.isActive ? 'btn-primary': ''">选取</button> 
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseGroupSure()">
                            <input type="button" class="btn btn-primary" value="确定使用">
                        </div>
                        <div class="page_shopgroup"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 链接选择商品框 -->
    <div class="modal export-modal" id="chooseShopModel">
        <div class="modal-dialog" id="chooseShopModel-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class = "js_switch" ng-class="{true: 'active', false: ''}[productModal.navIndex == $index]" ng-repeat="item in productModal.navList" ng-click="switchProductNav($index)">
                                <a href="javascript:void(0);" data-type="goods" class="js-modal-tab">@{{item}}</a>
                            </li>
                            <li ng-repeat="item in productModal.new" ng-if="productModal.navIndex == $index">
                                <a ng-href="@{{item.href}}" target="_blank" class="new_window">@{{item.title}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped ui-table ui-table-list">
                            <thead>
                            <tr>
                                <th style="width: 40%;text-align: left;">
                                    <div class="td-cont">
                                        <span>标题</span>
                                       <!--  <a href="#" ng-click="refresh()">刷新</a> -->
                                    </div>
                                </th>
                                <th  style="width: 25%;">
                                    <div class="td-cont">
                                        <span>创建时间</span>
                                    </div>
                                </th>
                                <th class="opts">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchProductList()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat = "list in productModal.list" id="list_@{{$index}}" data-price="@{{list['price']}}">
                                <td class="image" style="text-align: left;">
                                    <div class="td-cont" style="display: inline-block;" ng-if="list['thumbnail']">
                                        <img ng-src=" @{{host}}@{{list['thumbnail']}}">
                                    </div>
                                    <div class="td-cont" style="display: inline-block;vertical-align: top;">
                                        <a target="_blank" class="new_window" href="javascript:void(0);">@{{list['name']}}</a>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                        @{{list['timeDay']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseShopLink($index,list)">选取</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer clearfix">
                        <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseSure()">
                            <!-- <input type="button" class="btn btn-primary" value="确定使用"> -->
                        </div>
                        <div class="good_pagenavi">
                            <span class="total">共 2 条，每页 8 条</span></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 微预约选择弹窗 -->
    <div class="modal export-modal" id="activity_appointment">
        <div class="modal-dialog" id="activity-dialog-appointment">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">微预约</a>
                                <span>|</span>
                            </li>
                            <li class="link-group link-group-0" style="display: inline-block;">
                                <a href="/merchants/wechat/bookSave" target="_blank" class="new_window">新建预约</a>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle">
                                                    <a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchAppoint()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in appointMent">
                                    <td class="title" colspan="2">
                                        <div class="td-cont">
                                            <a target="_blank" class="new_window" href="javascript:void(0);">@{{ list['title'] }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        <span>
                                            @{{list['created_at']}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseAppointSure($index,list)">选取</button> 
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
                        <div class="appoint_pagenavi"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>  
  
    <!-- 二维码 -->
    <div class="popover left qrcode" style="display: block; top: 104px; left: -11642px;">
        <div class="arrow"></div>
        <div class="popover-inner popover-qrcode">
            <div class="popover-content">
                <div class="popover-qrcode-header">
                    <button ng-click="closeQrcode()" type="button" class="close js-close">×</button>
                    <div class="popover-qrcode-title">活动二维码</div>
                </div>
                <div class="qrcode-wrap">
                    {!! QrCode::size(150)->generate(URL("shop/point/sign/$wid")); !!}
                    <p class="scan-info">扫一扫立即参与活动</p></div>
                <div class="popover-qrcode-footer">
                    <!--<a href="">下载二维码</a>
                    <a href="/v2/weixin/autoreply/scan" class="pull-right">设置带参数二维码</a>-->
                    </div>
            </div>
        </div>
    </div>
    <!-- 二维码 -->
</div>
@endsection
@section('page_js')
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<!-- angular -->
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>      
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
<!-- webuploader -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 分页 -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
<script>
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var store={!! $store !!};
    var sign_template={!! $sign_template !!};
    store.member_url = '/shop/member/index/'+store.id;
</script>
<!-- 模块公共js -->
<script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
<script src="{{ config('app.source_url') }}mctsource/static/js/model.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/product_public.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/sign_b55f7i3j.js"></script>
@endsection