@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/edit.css" />
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
                    <a href="{{ URL('/merchants/marketing/discountList') }}">满减</a>
                </li>
                <li>
                    <a href="javascript:void(0)">新建满减</a>
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
       <div class="discount-title">设置满减</div>
       <div class="discount-container">
           <div class="discount-info">
               <div class="info-title">满减基础信息</div>
               <div class="info-content">
                    <div class="infor-group">
                        <div class="info-label">满减名称：</div>
                        <div class="info-desc">
                            <input type="text" class="name-input J_title">
                        </div>
                    </div>
                    <div class="infor-group">
                        <div class="info-label">满减时间设置：</div>
                        <div class="info-desc">
                            <label for="time-line" class="time-label">
                                <input type="radio" name="time" id="time-line" class="time-radio" checked data-type="1">
                                设置区间时间
                            </label>
                            <label for="time-all" class="time-label">
                                <input type="radio" name="time" id="time-all" class="time-radio" data-type="2">
                                只设置开始时间
                            </label>
                            <div class="choose-control">
                                <div class="time-choose">
                                    <input type="text" class="time-choose-input" id="start_time">
                                    <i class="date-icon"></i>
                                </div>
                                <div class="time-choose J_end-time">
                                    <span class="time-sep">-</span>
                                    <input type="text" class="time-choose-input" id="end_time">
                                    <i class="date-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
           </div>
           <div class="discount-info">
               <div class="info-title">满减利益点设置</div>
               <div class="info-content">
                    <div class="infor-group">
                        <div class="info-label">满减类型：</div>
                        <div class="info-desc">
                            <label for="money-type" class="time-label">
                                <input type="radio" name="discount-type" id="money-type" class="time-radio" checked data-type="1">
                                金额
                            </label>
                            <label for="amount-type" class="time-label">
                                <input type="radio" name="discount-type" id="amount-type" class="time-radio" data-type="2">
                                件数
                            </label>
                            <span class="suggest">
                                满减设置建议
                                <div class="suggest-desc">
                                    <p>1、商家在设置满减利益点时，需要权衡店铺已发放的权益，<span style="color:#FF4343;">如会员卡权益、优惠券、积分等，</span>因为满减利益点是可以与店铺其他权益及优惠叠加使用！</p>
                                    <p>2、商家在设置满减时，<span style="color:#FF4343;">满减层级之间的利益点</span>最好根据商品或店铺特点设置一定的跨度，给商品预留出价格空间！</p>
                                    <p>3、已设置<span style="color:#FF4343;">批发价的商品</span>，仍可参与满减活动，请您注意满减利益点的设置</p>
                                </div>
                            </span>
                            <div class="profit-wraper J_wraper-1">
                                <div class="profit-item">
                                   <div class="item-box">
                                        <span class="J_profit-index">1</span>. 满<input type="number" class="profit-input J_profit_money">元
                                   </div>
                                   <div class="item-box">
                                        减<input type="number" class="profit-input J_desc_money">元
                                   </div><span class="item-del">删除</span>
                                </div>
                                <div class="profit-item">
                                    <div class="item-box">
                                        <span class="J_profit-index">2</span>. 满<input type="number" class="profit-input J_profit_money">元
                                   </div>
                                   <div class="item-box">
                                        减<input type="number" class="profit-input J_desc_money">元
                                   </div><span class="item-del">删除</span>
                                </div>
                                <div class="profit-item">
                                    <div class="item-box">
                                        <span class="J_profit-index">3</span>. 满<input type="number" class="profit-input J_profit_money">元
                                   </div>
                                   <div class="item-box">
                                        减<input type="number" class="profit-input J_desc_money">元
                                   </div><span class="item-del">删除</span>
                                </div>
                            </div>
                            <div class="profit-wraper J_wraper-2" style="display:none">
                                <div class="profit-item">
                                    <div class="item-box">
                                        <span class="J_profit-index">1</span>. 满<input type="number" class="profit-input J_profit_amount">件
                                   </div>
                                   <div class="item-box">
                                        减<input type="number" class="profit-input J_desc_amount">元
                                   </div><span class="item-del">删除</span>
                                </div>
                                <div class="profit-item">
                                    <div class="item-box">
                                        <span class="J_profit-index">2</span>. 满<input type="number" class="profit-input J_profit_amount">件
                                   </div>
                                   <div class="item-box">
                                        减<input type="number" class="profit-input J_desc_amount">元
                                   </div><span class="item-del">删除</span>
                                </div>
                                <div class="profit-item">
                                    <div class="item-box">
                                        <span class="J_profit-index">3</span>. 满<input type="number" class="profit-input J_profit_amount">件
                                   </div>
                                   <div class="item-box">
                                        减<input type="number" class="profit-input J_desc_amount">元
                                   </div><span class="item-del">删除</span>
                                </div>
                            </div>
                            <div class="profit-tips">（参加活动的商品价格不能过低及数量过少，防止满减后出现亏损）</div>
                            <div class="profit-add J_profit-add">+新增满减利益点</div>
                        </div>
                    </div>
               </div>
           </div>
           <div class="discount-info">
               <div class="info-title">满减商品选择</div>
               <div class="info-content">
                    <div class="infor-group">
                        <div class="info-label">满减类型选择：</div>
                        <div class="info-desc">
                            <label for="all-type" class="time-label">
                                <input type="radio" name="product-type" id="all-type" class="time-radio" checked data-type="1">
                                全店商品
                            </label>
                            <label for="design-type" class="time-label">
                                <input type="radio" name="product-type" id="design-type" class="time-radio" data-type="2">
                                指定商品
                            </label>
                            <span class="tips">（推荐先创建满减活动分组，核对商品价格及库存）</span>
                            <div class="product-wraper">
                                <div class="product-list" ng-show="addProduct">
                                    <div class="product-item product-thead">
                                        <div class="product-info">商品名称</div>
                                        <div class="product-action">操作</div>
                                    </div>
                                    <div class="product-item" ng-repeat="list in showPro">
                                        <div class="product-info">
                                            <img ng-src="@{{list['img']}}">
                                            <div class="product-desc">
                                                <p>@{{list['title']}}</p>
                                                <p class="product-price">￥@{{list.price}}</p>
                                            </div>
                                        </div>
                                        <div class="product-action" ng-click="delProduct($index,list,1)">取消</div>
                                    </div>
                                    <div class="look-more" ng-show="isLookMore" ng-click="lookMore()">查看全部</div>
                                </div>
                                <div class="profit-add J_product-add" ng-click="chooseShopGroup()">+添加指定商品</div>
                            </div>
                        </div>
                    </div>
               </div>
           </div>
       </div>
       <div class="save-box">
            <input class="btn btn-primary btn-sm js-btn-save" type="button" value="保 存" ng-click="savePro()">
       </div>
        <!-- 添加指定商品Model -->
        <div class="modal export-modal" id="goodslist_model">
            <div class="modal-dialog" id="goodslist_model_dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header special-header">
                            <button type="button" class="close closeSp" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="header-tab" ng-class="{'active':groupModal}">
                                    <a data-type="goods" class="js-modal-tab tab-pro" ng-click="showProGroupWraper()">商品分组</a>
                                </li>
                                <li class="header-tab" ng-class="{'active':!groupModal}">
                                    <a data-type="tag" class="js-modal-tab tab-pro" ng-click="showProWraper()">商品列表</a>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-body" ng-show="groupModal">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="td-cont" style="width:100px">
                                                <span>分组名称</span>
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
                                                <button class="btn js-choose choose_btn_@{{list.id}}" ng-click="chooseShopGroupSure($index,list)" ng-class="list.isActive ? 'btn-primary': ''">@{{ list['isActive']?'取消':'选取' }}</button>  
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-body" ng-show="!groupModal">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="td-cont">
                                            <span>商品名称 </span>
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
                                        </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{list.id}}" ng-show="!list.ischecked" ng-click="chooseShopSure($index,list)" ng-class="{'btn-primary': list.isActive}">@{{ list['isActive']?'取消':'选取' }}</button>
                                            <button class="btn js-choose choose_btn_@{{list.id}} disabled" ng-show="list.ischecked">已选取</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix" ng-show="groupModal">
                            <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseGroupSure()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="page_shopgroup"></div>
                        </div>
                        <div class="modal-footer clearfix" ng-show="!groupModal">
                            <div style="" class="js-confirm-choose pull-left" ng-show="tempSure2" ng-click="chooseSure()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="good_pagenavi">
                                <span class="total">共 2 条，每页 8 条</span></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
         <!-- 查看更多model -->
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
                                    <a data-type="goods" class="js-modal-tab">商品列表</a>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="td-cont">
                                            <span>商品名称 </span>
                                        </div>
                                    </th>
                                    <th class="information"></th>
                                    <th>
                                        <div class="td-cont">
                                            <!-- <span>创建时间</span> -->
                                        </div>
                                    </th>
                                    <th class="opts more-opts">
                                        <div class="td-cont">
                                            <span>操作</span>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="more-body">
                                <tr ng-repeat = "list in goodList" id="list_@{{list.id}}" data-price="@{{list['price']}}">
                                    <td class="image">
                                        <div class="td-cont">
                                            <img ng-src="@{{list['thumbnail']}}">
                                        </div>
                                    </td>
                                    <td class="title">
                                        <div class="td-cont">
                                            <a class="new_window" href="javascript:void(0);">@{{list['name']}}</a>
                                            <div class="price-more">@{{list.price}}</div>
                                            
                                        </div>
                                    </td>
                                    <td style="vertical-align: top !important;padding-top: 14px !important;">
                                        <div class="discount-flag" ng-show="list.isFlag">已参加</div>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{list.id}}" ng-click="delProduct($index,list,2)">取消</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div class="good_pagenavi">
                                <span class="total">共 2 条，每页 8 条</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 取消model -->
        <div class="del-modal">
            <div class="del-wraper">
                <span class="close-icon" ng-click="delCancle()">X</span>
                <p class="del-tips">确认取消满减指定商品？</p>
                <div class="btn-wraper">
                    <div class="cancel-btn action-btn" ng-click="delCancle()">取消</div>
                    <div class="del-btn action-btn" ng-click="delSure()">确认</div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('page_js')
<script>
    var wid = '{{session('wid')}}';
    var imgUrl = "{{ imgUrl() }}";
    var tempData = {!! json_encode($discount) !!};
</script>
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}mctsource/static/js/model.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/edit.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/editProduct.js"></script>
@endsection