@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
<!-- 公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/publish_store.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_public.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
<!-- 当前页面css -->
<!-- <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_u304cpia.css" /> -->
<style type="text/css">
    .editer-content .control-group {
        margin-bottom: 0;
    }
    .editer-content .setting {
        display: flex;
        width: 50%;
        float:none !important;
    }
</style>
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/product/goodsTemplate') }}">商品页模版</a>
            </li>
            <li>
                <a href="javascript:void(0);">{{$page_title}}</a>
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
    <input id="wid" type="hidden" value="{{$wid}}" />
    <div class="card">
        <form class="form-horizontal" novalidate="" name="editorForm">
            <div class="card_left">
                <div class="left_content">
                    <h1><span>商品页模板</span></h1>
                </div>
                <div class="app-entry">
                    <div class="goods-template-block" ng-click="showBaseInfo()" id="baseinfo">
                        <p>基本信息区</p>
                        <p class="c-gray">固定样式，显示商品主图、价格等信息</p>
                    </div>
                    <div class="js-fields-region">
                        <div class="app-fields ui-sortable">
                            <div class="app-field clearfix">
                            </div>
                            <div class="app-field clearfix @{{editor['editing']}}" data-type="@{{editor['type']}}" ng-repeat = 'editor in editors' style="background:@{{editor['bgcolor']}}" ng-click="tool($event,editor)" ng-mouseover="addboder($event)" ng-mouseout="removeboder($event,editor)" ng-drop="true" ng-drop-success="onDropPageComplete($index, $data,$event)">
                                <div ng-drag="true" ng-drag-data="editor">
                                    <!-- 优化券 -->
                                    <coupon ng-if="editor['type'] == 'coupon'"></coupon>
                                    <!-- 编辑器框内容 -->
                                    <editor-text ng-if="editor['type'] == 'rich_text'"></editor-text>
                                    <!-- 商品内容 -->
                                    <goods ng-if = "editor['type'] == 'goods'"></goods>
                                    <!-- 图片广告内容 -->
                                    <advs ng-if = "editor['type'] == 'image_ad'"></advs>
                                    <!-- 标题内容 -->
                                    <add-title ng-if = "editor['type'] == 'title'"></add-title>
                                    <!-- 店铺内容 -->
                                    <shop ng-if = "editor['type'] == 'store'"></shop>
                                    <!-- 公告内容 -->
                                    <notice ng-show="editor['type'] == 'notice'"></notice>
                                    <!-- 商品搜索 -->
                                    <search ng-show="editor['type'] == 'search'"></search>
                                    <!-- 商品列表 -->
                                    <goodslist ng-if = "editor['type'] == 'goodslist'"></goodslist>
                                    <!-- 自定义模块内容 -->
                                    <model ng-if = "editor['type'] == 'model'"></model>
                                    <!-- 商品分组 -->
                                    <goodgroup ng-if="editor['type'] == 'good_group'"></goodgroup>
                                    <!-- 图片导航 -->
                                    <imagelink ng-if="editor['type'] == 'image_link'"></imagelink>
                                   <!--文字导航-->
                                    <textlink ng-if="editor['type'] == 'textlink'"></textlink>
                                    <div class="actions">
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
                                    <a class="js-new-field" data-field-type="image_ad">
                                        图片<br>广告
                                    </a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addTitle(1)">标题</a>
                                </li>
                                <li ng-click="addShop(1)">
                                    <a class="js-new-field" data-field-type="store">
                                        进入<br>店铺
                                    </a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addCoupon(1)">优惠券</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="notice" ng-click="addNotice(1)">公告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="goods" ng-click="addSearch(1)">商品<br>搜索</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="goodslist"  ng-click="addGoodsList(1)">商品<br>列表</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="model" ng-click="addModel(1)">自定义<br />模块</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(1)">商品<br />分组</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="image_link" ng-click="addLinkImages(1)">图片<br />导航</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="image_link" ng-click="addtextLink(1)">文本<br />导航</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card_right baseinfo" ng-show="baseInfo['show']" ng-cloak>
                <div class="arrow"></div>
                <!-- 基本信息右侧 -->
                <baseinfo></baseinfo>
            </div>
            <div class="card_right card_right_list" ng-show="editors[index]['showRight'] && !baseInfo['show']">
                <div class="arrow"></div>
                <div ng-show="!editors[index]['is_add_content']">
                    <!-- 优化券 -->
                    <coupon ng-if="editor['type'] == 'coupon'"></coupon>
                    <!-- 富文本右侧 -->
                    <cr-richtext ng-show="editors[index]['cardRight'] == 3"></cr-richtext>
                    <!-- 商品右侧 -->
                    <cr-goods ng-if="editors[index]['cardRight'] == 4"></cr-goods>
                    <!-- 广告右侧 -->
                    <cradvs ng-if="editors[index]['cardRight'] == 5"></cradvs>
                    <!-- 标题右侧 -->
                    <crtitle ng-if="editors[index]['cardRight'] == 6"></crtitle>
                    <!-- 店铺右侧 -->
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
                    <!-- 图片链接右侧 -->
                    <crimagelink ng-if="editors[index]['cardRight'] == 15"></crimagelink>
                    <!-- 文本导航右侧 -->
                    <crtextlink ng-if="editors[index]['cardRight'] == 16"></crtextlink>
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
                        <li>
                            <a class="js-new-field" data-field-type="coupon" ng-click="addCoupon(2)">优惠券</a>
                        </li>
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
                        <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addLinkImages(2)">图片<br />导航</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addtextLink(2)">文本<br />导航</a>
                        </li>
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
            <div class="btn_grounp">
                <button class="btn btn-primary" ng-click="processProductTemplate(editorForm.$valid)">保存</button>
            </div>
        </form> 
    </div>
</div>
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
                            <a href="#" class="js-modal-tab">已上架商品</a>
                            <span>|</span>
                        </li>
                        <li style="display: none;">
                            <a href="#" class="js-modal-tab">商品分组</a>
                            <span>|</span>
                        </li>
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/merchants/product/create" target="_blank" class="new_window">新建商品</a>
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
                                                <a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchGoods()">搜</a>
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
                                        <img src="@{{list['thumbnail']}}">
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
                                <span class="ui-pagination-total">共8条， 每页15条</span>
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
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchCoupon()">搜</a>
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
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/v2/showcase/goods/edit" target="_blank" class="new_window">新建微页面</a>
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
@endsection
@section('page_js')
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<!-- angular -->
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>       
<!-- 百度编辑器 -->
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
<!-- 百度上传插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>  
<!-- laydate -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
<!-- 公共 -->
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var product_template={!! $product_template !!};
    var store={!! $store !!};
    store.member_url = '/shop/member/index/'+store.id;
</script>
<script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/model.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/product_public.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_u304cpia.js"></script>
@endsection