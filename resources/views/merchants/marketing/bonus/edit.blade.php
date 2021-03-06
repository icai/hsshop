@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/bouns_add.css" />
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/publish_store.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
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
                    <a href="javascript:void(0)">拆红包</a>
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
    <div class="content" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <div class="clearfix">
            <h4 class='bouns_title'>设置拆红包活动</h4>
            <div class='bouns_cont'>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left pd_8 fl'>
                        <span class='required'>*</span> 拆红包名称：
                    </div>
                    <div class='control-label-right'>
                        <input type="text" class='control_inp' ng-model='bounsTitle'>
                        <span class='tip'>拆红包名称必须在1-10个字内</span>
                    </div>
                </div>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left pd_8 fl'>
                        <span class='required'>*</span> 拆红包活动时间：
                    </div>
                    <div class='control-label-right'>
                        <input type="text" class='control_inp time_input' id="startTime" value='' ng-model='start_at'>
                        至
                        <input type="text" class='control_inp time_input' id="endTime" value='' ng-model='end_at'>
                    </div>
                </div>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        <span class='required'>*</span> 添加优惠劵：
                    </div>
                    <div class='control-label-right'>
                        <p class='add_coupon'>
                            <span class='add_coupon_span' ng-click="showCouponModel()">+ 添加优惠劵</span>
                            <span>（最多可添加五张）</span>
                        </p>
                        <ul class='clearfix coupon_list' ng-if="coupon_li.length > 0">
                            <li class='clearfix' ng-repeat="item in coupon_li">
                                <div class="li_div_left fl">
                                    <p>￥@{{item.amount}}</p>
                                    <p>@{{ item.range_type_title }}</p>
                                </div>
                                <div class='li_div_right fl'>
                                    <p>@{{item.name}}</p>
                                    <div>
                                        <p>@{{item.limit_desc}}</p>
                                        <p>@{{item.start_at}} 至 @{{item.end_at}}</p>
                                    </div>
                                </div>
                                <i class='close_div_coupon' ng-click="closeCoupon($index)"></i>
                                <div class='tip_coupon' ng-if="item.tip_coupon != 0" ng-class="tip_style"></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        设置封面：
                    </div>
                    <div class='control-label-right'>
                        <div class='add_img_banner'>
                            <span ng-if="!imageUrl" class='add_span'></span>
                            <img ng-if="imageUrl" ng-src="@{{imageUrl}}" alt="">
                            <i  ng-if="imageUrl" class='close_div_coupon' ng-click="closeImg()"></i>
                        </div>
                        <div class="add_img_banner_tip">
                            <p><span ng-click="addAdvs()">更改封面</span>（不更改为默认封面）</p>
                            <p>建议图片尺寸为：750*375px，大小不超过3M</p>
                        </div>
                    </div>
                </div>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        小程序封面跳转微页面：
                    </div>
                    <div class='control-label-right'>
                        <p class='add_coupon' ng-if="!microPage.title">
                            <span class='add_coupon_span' ng-click="micropageModel(1)">+ 添加微页面</span>
                        </p>
                        <p class='add_coupon' ng-if="microPage.title">
                            <span class='add_coupon_span'>@{{microPage.title}}</span>
                            <span class='add_coupon_span' ng-click="closePage(1)">删除</span>
                            <span>(添加微页面，点击删除，可以重新设置微页面)</span>
                        </p>
                    </div>
                </div>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        微商城封面跳转微页面：
                    </div>
                    <div class='control-label-right'>
                        <p class='add_coupon' ng-if="!shop_microPage.title">
                            <span class='add_coupon_span' ng-click="micropageModel(2)">+ 添加微页面</span>
                        </p>
                        <p class='add_coupon' ng-if="shop_microPage.title">
                            <span class='add_coupon_span'>@{{shop_microPage.title}}</span>
                            <span class='add_coupon_span' ng-click="closePage(2)">删除</span>
                            <span>(添加微页面，点击删除，可以重新设置微页面)</span>
                        </p>
                    </div>
                </div>
                <div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        <span class='required'>*</span> 说明：
                    </div>
                    <div class='control-label-right'>
                        <p class='add_coupon'>
                            <span>每期开启的活动，用户首次打开首页时，会弹出拆红包。关闭时则缩在右下角浮标，打开可以继续领，一直等到用户领完会消失右下角浮标。</span>
                        </p>
                    </div>
                </div>
                <!--<div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        <span class='required'>*</span> 红包发放方式：
                    </div>
                    <div class='control-label-right'>
                        <div class='grant_div_top'>
                            <input ng-click='grantClick(0)' ng-checked="grantType == 0" type="radio" name="grant">
                            <span>未领到继续弹</span>
                        </div>
                        <div class='grant_div_bottom'>
                            <input ng-click='grantClick(1)' ng-checked="grantType == 1" type="radio" name="grant">
                            <input type="text" class='grant_input' ng-model="grantVal">
                            <span>小时弹一次</span>
                            <span class='span_tip'>(红包弹出时间只能输入数字，不能为负数)</span>
                        </div>
                    </div>
                </div>-->
                <!--<div class='mb_15 clearfix'>
                    <div class='control-label-left fl'>
                        <span class='required'>*</span> 每人限领次数：
                    </div>
                    <div class='control-label-right'>
                        <div class='grant_div_top'>
                            <input ng-click='timeClick(0)' checked type="radio" name="time">
                            <span>不限次数</span>
                        </div>
                        <div class='grant_div_bottom'>
                            <input ng-click='timeClick(1)' type="radio" name="time">
                            <input type="text" class='grant_input' ng-model="timeVal">
                            <span>次</span>
                            <span class='span_tip'>(红包领取次数只能输入正整数)</span>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
        <!-- 底部保存 -->
        <div class="t-footer">
            <input class="btn js-btn-quit btn-sm" type="button" onclick="history.back();" value="取消">
            <input class="btn btn-primary btn-sm ml10 js-btn-save" ng-click="submit()" type="button" value="保存">
            <input type="hidden" id="wid" value="{{$wid}}"/>
            <input type="hidden" id="group_id" value="{{$id}}"/>
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
                                    <a ng-if="shop_micro_page == 1" href="/merchants/marketing/liteAddPage" target="_blank" class="new_window">新建微页面</a>
                                    <a ng-if="shop_micro_page == 2" href="/merchants/store/showMicroPage/create/7" target="_blank" class="new_window">新建微页面</a>
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
                                                    <input ng-if="shop_micro_page == 1" id="searchTitle" class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" ng-if="shop_micro_page == 1" class="btn js-fetch-page js-modal-search" ng-click="searchPage()">搜</a>
                                                    <input ng-if="shop_micro_page == 2" id="shop_searchTitle" class="input-small js-modal-search-input form-control" type="text" ng-model="shop_searchTitle"><a href="javascript:void(0);" ng-if="shop_micro_page == 2" class="btn js-fetch-page js-modal-search" ng-click="searchPage()">搜</a>
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="choosePageLinkSure(list)">选取</button>
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
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">优惠券</a>|</li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/marketing/coupon/set" target="_blank" class="new_window">新建优惠券</a>
                                </li>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="couponTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchCoupon()">搜</a>
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
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseCoupon(list)">选取</button>
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

@endsection

@section('page_js')
    <!-- angular -->
    <script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <!-- 核心 base.js JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
    <!-- webuploader -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/md5.js"></script>
    <!-- chosen -->
    <script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var _host = "{{ config('app.source_url') }}";
        var host = "{{ config('app.url') }}";
        var editData = {!! json_encode($bonus) !!};
        var id = $("#group_id").val()
    </script>
    <script src="{{ config('app.source_url') }}mctsource/js/bouns_edit.js"></script>
@endsection