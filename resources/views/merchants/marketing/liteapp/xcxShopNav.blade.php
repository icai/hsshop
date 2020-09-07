@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />

<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/publish_store.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/xcxshop_nav.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
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
                <a href="javascript:void(0)">小程序</a>
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
    <input type="hidden" name="wid" value="{{$wid}}" id="wid">
    <ul class="tab_nav">
        <li class="hover">
            <a href="/merchants/marketing/litePage">小程序微页面</a>
        </li> 
        <li>
            <a href="/merchants/marketing/footerBar">底部导航</a>
        </li>
        <li>
            <a href="/merchants/marketing/liteapp">小程序设置</a>
        </li>
        <li>
            <a href="/merchants/marketing/liteStatistics">数据统计</a>
        </li>
        <!-- <li>
            <a href="/merchants/marketing/xcxShopNav">底部导航</a>
        </li> -->
    </ul>
    <div class="card">
        <!-- card左侧 -->
        <div class="card_left">
            <div class="left_content">
                <h1>
                    <span></span>
                </h1>
            </div>
            <div class="app-entry" ng-cloak>
                <div class="preview-nav-menu" ng-cloak>
                    <div class="js-navmenu nav-show nav-menu-1 nav-menu has-menu-1"  ng-show="menus['menusType']==1">
                        <div class="nav-special-item">
                            <a href="javascript:void(0);" class="home">主页</a>
                        </div>
                        <div class="js-nav-preview-region nav-items-wrap">
                            <div class="nav-items">
                                <div class="nav-item" ng-repeat="menu in menus['menu']"  style="width:@{{menu['width']}}" ng-click="showSubmenus($index)">
                                    <a class="mainmenu" href="javascript:void(0);" target="_blank">
                                        <span class="mainmenu-txt">
                                            <i class="arrow-weixin" ng-show="menu['submenus'].length"></i>
                                            <span ng-bind="menu['title']"></span>
                                        </span>
                                    </a>
                                    <div class="submenu js-submenu" ng-show="menu['submenusShow'] && menu['submenus'].length" style="left:@{{menu['submenusLeft']}}">
                                        <span class="before-arrow"></span>
                                        <span class="after-arrow"></span>
                                        <div class="js-nav-2nd-region">
                                            <ul>
                                                <li ng-repeat="submenu in menu['submenus']">
                                                    <a href="javascript:void(0);" target="_blank" ng-bind="submenu['title']"></a>
                                                </li>
                                            </ul>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="js-navmenu nav-show nav-menu-2 nav-menu has-menu-3" style="background-color:@{{menus['bgColor']}}" ng-show="menus['menusType']==2">
                        <div class="js-nav-preview-region">
                            <ul class="nav-pop-sub" >
                                <li class="nav-item nav-pop-sub-item nav-pop-sub-item-3-1" style="width:@{{menu['width']}}"  ng-repeat="menu in menus['menu']">
                                    <a href="javascript:void(0);" style="
                                    background-image: url(@{{menu['icon']}});
                                    background-size: 64px 50px
                                    ">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        <div class="card_right"  ng-cloak>
            <div class="arrow"></div>
            <div class="editer-content">
                <div class="control-group">
                    <div>
                        <form class="form-horizontal edit-shopnav" novalidate="">
                            <div class="edit-shopnav-on-page">
                                <div>将导航应用在以下页面：</div>
                                <div>
                                    <label class="checkbox inline" ng-repeat = "page in pages" ng-cloak>
                                        <input type="checkbox" name="on_page" ng-model="page.isCheck" ng-checked = "page.isCheck">
                                        <span ng-bind = 'page.title'></span>
                                    </label>
                                </div>
                            </div>
                            <div class="edit-shopnav-header clearfix">
                                <span>当前模版：</span>
                                <span class="currentMode" ng-bind="menus['title']"></span>
                               <!--  <span class="currentMode" ng-show="menus['menusType']==2">APP导航样式</span> -->
                                <a href="javascript:void(0);" class="zent-btn zent-btn-primary pull-right js-select-nav-style" ng-click="changeModelShow()">修改模版</a>
                            </div>
                            <div class="js-main-icon-setting main-icon-setting"></div>
                            <div class="js-nav-region clearfix"  ng-show="menus['menusType']==1">
                                <ul class="choices ui-sortable edit-shopnav">
                                    <li class="choice" ng-repeat="menu in menus['menu']" ng-init="outerIndex = $index">
                                        <div class="first-nav">
                                            <h3>一级导航</h3>
                                            <div class="js-first-nav-item-meta-region">
                                                <div>
                                                    <div class="shopnav-item">
                                                        <div class="shopnav-item-title">
                                                            <span class="js-edit-title" ng-bind="menu['title']" id="menus_@{{$index}}" ng-click="changeTitle(menu,$index)"></span>
                                                        </div>
                                                        <div class="shopnav-item-link">
                                                            <span class="pull-left shopnav-item-split">|</span>
                                                            <span class="pull-left" ng-show="!menu['submenus'].length" id="menuLink_@{{$index}}">链接：</span>
                                                            <div class="pull-left" ng-show="!menu['submenus'].length">
                                                                <div class="dropdown hover" ng-show="!menu['linkUrl']">
                                                                    <a class="dropdown-toggle js-link-to" href="javascript:void(0);">选择链接页面</a>
                                                                    <ul class="dropdown-menu">
                                                                        <li class="js_product" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseShop(outerIndex,3)">商品及分类</a></li>
                                                                        <li class="js_smallPage" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="choosePageLink($index,3)">微页面及分类</a></li>
                                                                        <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,3)">营销活动</a></li>
                                                                        <li class="homepage js_shop" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,menu,1)">店铺主页</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,2)"><a role="menuitem"  href="javascript:void(0)">会员主页</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                        <li>
                                                                            <a class="js-modal-magazine" href="javascript:void(0);" ng-click="setLinkUrl($index,1)">自定义外链</a>
                                                                        </li>
                                                                        <li class="homepage js_members" role="presentation">
                                                                            <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,menu,5)">微社区</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="clearfix" ng-show="menu['linkUrl']">
                                                                    <div class="pull-left js-link-to link-to">
                                                                        <a href="javascript:void(0);" target="_blank" class="new-window link-to-title" ng-bind="menu['linkUrlName']"></a>
                                                                    </div>
                                                                    <div class="dropdown hover pull-right" ng-show="menu.dropDown" ng-cloak>
                                                                        <a class="dropdown-toggle  shopnav-item-action" href="javascript:void(0);">修改</a>
                                                                        <ul class="dropdown-menu">
                                                                            <li class="js_product" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseShop(outerIndex,3)">商品及分类</a></li>
                                                                            <li class="js_smallPage" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="choosePageLink($index,3)">微页面及分类</a></li>
                                                                            <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,3)">营销活动</a></li>
                                                                            <li class="homepage js_shop" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,menu,1)">店铺主页</a></li>
                                                                            <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,menu,2)">会员主页</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                            <li>
                                                                                <a class="js-modal-magazine" href="javascript:void(0);" ng-click="setLinkUrl($index,1)">自定义外链</a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,menu,5)">微社区</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="dropdown hover pull-right" ng-show="!menu.dropDown" ng-cloak>
                                                                        <a class="dropdown-toggle  shopnav-item-action" href="javascript:void(0);">修改</a>
                                                                        <ul class="dropdown-menu">
                                                                            <li class="js_product" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseShop(outerIndex,3)">商品及分类</a></li>
                                                                            <li class="js_smallPage" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="choosePageLink($index,3)">微页面及分类</a></li>
                                                                            <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,3)">营销活动</a></li>
                                                                            <li class="homepage js_shop" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,menu,1)">店铺主页</a></li>
                                                                            <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,menu,2)">会员主页</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                            <li>
                                                                                <a class="js-modal-magazine" href="javascript:void(0);" ng-click="setLinkUrl($index,1)">自定义外链</a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,menu,5)">微社区</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="pull-left c-gray" ng-show="menu['submenus'].length">
                                                                使用二级导航后主链接已失效。
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="second-nav" data-first-nav-index="0">
                                            <h4>二级导航</h4>
                                            <div class="actions">
                                                <span class="action delete close-modal" title="删除" ng-click="removemenus($index)">×</span>
                                            </div>
                                            <div class="js-second-nav-region">
                                                <ul class="choices ui-sortable">
                                                    <li class="choice" ng-repeat="submenu in menu['submenus']">
                                                        <div class="shopnav-item">
                                                            <div class="actions">
                                                                <span class="action delete close-modal" title="删除" ng-click="removeOneSubmenus($index,outerIndex)">×</span>
                                                            </div>
                                                            <div class="shopnav-item-title">
                                                                <span class="js-edit-title" ng-bind="submenu['title']" ng-click="changesubMenuTitle(submenu,$index,outerIndex)" id="subTitle_@{{outerIndex}}_@{{$index}}">标题</span>
                                                            </div>
                                                            <div class="shopnav-item-link">
                                                                <span class="pull-left shopnav-item-split">|</span>
                                                                <span class="pull-left" id="subLink_@{{outerIndex}}@{{$index}}">链接：</span>
                                                                <div class="pull-left">
                                                                    <div class="dropdown hover" ng-show="!submenu['linkUrl']">
                                                                        <a class="dropdown-toggle js-link-to" href="javascript:void(0);">选择链接页面</a>
                                                                        <ul class="dropdown-menu">
                                                                            <li class="js_product" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseShop($index,4,outerIndex)">商品及分类</a></li>
                                                                            <li class="js_smallPage" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="choosePageLink($index,4,outerIndex)">微页面及分类</a></li>
                                                                            <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,4,outerIndex)">营销活动</a></li>
                                                                            <li class="homepage js_shop" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,1)">店铺主页</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,submenu,2)"><a role="menuitem"  href="javascript:void(0)">会员主页</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,submenu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                            <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,submenu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                            <li>
                                                                                <a class="js-modal-magazine" href="javascript:void(0);" ng-click="setsubLinkUrl($index,submenu,outerIndex)">自定义外链</a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,submenu,5)">微社区</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class=" clearfix" ng-show="submenu['linkUrl']">
                                                                        <div class="pull-left js-link-to link-to">
                                                                            <a href="javascript:void(0);" target="_blank" class="new-window link-to-title" ng-bind="submenu['linkUrlName']" id="changesubLinkUrl_@{{outerIndex}}@{{$index}}"></a>
                                                                        </div>
                                                                        <div class="dropdown hover pull-right" ng-show="submenu.dropDown">
                                                                            <a class="dropdown-toggle shopnav-item-action" href="javascript:void(0);">修改</a>
                                                                            <ul class="dropdown-menu">
                                                                                <li class="js_product" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseShop($index,4,outerIndex)">商品及分类</a></li>
                                                                                <li class="js_smallPage" role="presentation" ng-click="choosePageLink($index,4,outerIndex)"><a role="menuitem"  href="javascript:void(0)">微页面及分类</a></li>
                                                                                <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,4,outerIndex)">营销活动</a></li>
                                                                                <li class="homepage js_shop" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,1)">店铺主页</a></li>
                                                                                <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,2)">会员主页</a></li>
                                                                                <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,3)">购物车</a></li>
                                                                                <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,4)">拼团</a></li>
                                                                                <li>
                                                                                    <a class="js-modal-magazine" href="javascript:void(0);" ng-click="changesubLinkUrl($index,submenu,outerIndex)">自定义外链</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,submenu,5)">微社区</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="dropdown hover pull-right" ng-show="!submenu.dropDown">
                                                                            <a class="dropdown-toggle shopnav-item-action" href="javascript:void(0);">修改</a>
                                                                            <ul class="dropdown-menu">
                                                                                <li class="js_product" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseShop($index,4,outerIndex)">商品及分类</a></li>
                                                                                <li class="js_smallPage" role="presentation" ng-click="choosePageLink($index,4,outerIndex)"><a role="menuitem"  href="javascript:void(0)">微页面及分类</a></li>
                                                                                <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,4,outerIndex)">营销活动</a></li>
                                                                                <li class="homepage js_shop" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,1)">店铺主页</a></li>
                                                                                <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,2)">会员主页</a></li>
                                                                                <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,3)">购物车</a></li>
                                                                                <li class="homepage js_members" role="presentation"><a role="menuitem"  href="javascript:void(0)" ng-click="chooseLinkUrl($index,submenu,4)">拼团</a></li>
                                                                                <li>
                                                                                    <a class="js-modal-magazine" href="javascript:void(0);" ng-click="changesubLinkUrl($index,submenu,outerIndex)">自定义外链</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,submenu,5)">微社区</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <p class="add-shopnav add-second-shopnav js-add-second-nav" ng-click="addSubmenus($index)">+ 添加二级导航</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- app导航模板 -->
                            <div ng-show="menus['menusType']==2">
                                <div class="shopnav-background-color">
                                    <span>底色：
                                        <span></span>
                                        <input type="color" name="background_color" class="span2" value="" ng-model="menus['bgColor']">
                                    </span>
                                </div>
                                <div class="js-nav-region clearfix">
                                    <ul class="choices ui-sortable" ng-cloak>
                                        <li class="choice" ng-repeat="menu in menus['menu']" ng-init="outerIndex = $index">
                                            <div class="app-nav">
                                                <div class="actions">
                                                    <span class="action delete close-modal" title="删除" ng-click="removemenus($index)">×</span>
                                                </div>
                                                <div class="app-nav-image-group clearfix">
                                                    <div class="app-nav-image-normal pull-left">
                                                        <p>普通：</p>
                                                        <div class="app-nav-image-box" style="background-color: #2B2D30">
                                                            <div class="app-nav-image" style="
                                                            background-image: url(@{{menu['icon']}});
                                                            background-size: 64px 50px;
                                                            ">
                                                            </div>
                                                            <a href="javascript:void(0);" class="js-trigger-image" ng-click="changeNormal($index)">修改</a>
                                                        </div>
                                                    </div>
                                                    <div class="app-nav-image-active-box pull-left">
                                                        <p>高亮（可选）：</p>
                                                        <div class="app-nav-image-box" style="background-color: #2B2D30">
                                                            <div class="app-nav-image" style="
                                                            background-image: url(@{{menu['iconActive']}});
                                                            background-size: 64px 50px;
                                                            ">
                                                            </div>
                                                            <a href="javascript:void(0);" class="js-trigger-actived-image" ng-click="changeActive($index)">修改</a>
                                                            <div class="actions">
                                                                <span class="action js-delete-actived-image close-modal" ng-click="deleteActiveIcon(menu)">×</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <div class="controls" style="margin-left: 0;">
                                                        <input type="hidden" name="image_url">
                                                    </div>
                                                </div>
                                                <p class="c-gray">图片尺寸要求：不大于128*100像素，支持PNG格式</p>
                                                <div class="split-line"></div>
                                                <div class="control-group control-group-link">
                                                    <label class="control-label" id="menuAppLink_@{{$index}}">链接：</label>
                                                    <div class="controls">
                                                        <div class="dropdown hover" ng-show="!menu['linkUrl']">
                                                            <a class="dropdown-toggle js-link-to" href="javascript:void(0);">选择链接页面</a>
                                                            <ul class="dropdown-menu">
                                                                <li class="js_product" role="presentation"  ng-click="chooseShop(outerIndex,3)"><a role="menuitem"  href="javascript:void(0)">商品及分类</a></li>
                                                                <li class="js_smallPage" role="presentation" ng-click="choosePageLink($index,3)"><a role="menuitem"  href="javascript:void(0)">微页面及分类</a></li>
                                                                <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,3)">营销活动</a></li>
                                                                <li class="homepage js_shop" role="presentation" ng-click="chooseLinkUrl($index,menu,1)"><a role="menuitem"  href="javascript:void(0)">店铺主页</a></li>
                                                                <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,2)"><a role="menuitem"  href="javascript:void(0)">会员主页</a></li>
                                                                <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                <li class="homepage js_shop" role="presentation" ng-click="chooseLinkUrl($index,menu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                <li>
                                                                    <a class="js-modal-links" data-type="link" href="javascript:void(0);" ng-click="setLinkUrl($index,2)">自定义外链</a>
                                                                </li>
                                                                <li>
                                                                    <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,menu,5)">微社区</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="pull-left width_80" ng-show="menu['linkUrl']">
                                                            <div class=" clearfix">
                                                                <div class="pull-left js-link-to link-to">
                                                                    <a href="#" target="_blank" class="new-window link-to-title" ng-bind="menu['linkUrlName']"></a>
                                                                </div>
                                                                <div class="dropdown hover pull-right" ng-show="menu.dropDown">
                                                                    <a class="dropdown-toggle shopnav-item-action" href="javascript:void(0);">修改</a>
                                                                    <ul class="dropdown-menu">
                                                                        <li class="js_product" role="presentation" ng-click="chooseShop(outerIndex,3)"><a role="menuitem"  href="javascript:void(0)">商品及分类</a></li>
                                                                        <li class="js_smallPage" role="presentation"  ng-click="choosePageLink($index,3)"><a role="menuitem"  href="javascript:void(0)">微页面及分类</a></li>
                                                                        <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,3)">营销活动</a></li>
                                                                        <li class="homepage js_shop" role="presentation" ng-click="chooseLinkUrl($index,menu,1)"><a role="menuitem"  href="javascript:void(0)">店铺主页</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,2)"><a role="menuitem"  href="javascript:void(0)">会员主页</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                        <li class="homepage js_shop" role="presentation" ng-click="chooseLinkUrl($index,menu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                        <li>
                                                                            <a class="js-modal-magazine" href="javascript:void(0);" ng-click="changeWaiLink(menu,$index)">自定义外链</a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,menu,5)">微社区</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="dropdown hover pull-right" ng-show="!menu.dropDown">
                                                                    <a class="dropdown-toggle shopnav-item-action" href="javascript:void(0);">修改</a>
                                                                    <ul class="dropdown-menu">
                                                                        <li class="js_product" role="presentation" ng-click="chooseShop(outerIndex,3)"><a role="menuitem"  href="javascript:void(0)">商品及分类</a></li>
                                                                        <li class="js_smallPage" role="presentation"  ng-click="choosePageLink($index,3)"><a role="menuitem"  href="javascript:void(0)">微页面及分类</a></li>
                                                                        <li><a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseActivity($index,3)">营销活动</a></li>
                                                                        <li class="homepage js_shop" role="presentation" ng-click="chooseLinkUrl($index,menu,1)"><a role="menuitem"  href="javascript:void(0)">店铺主页</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,2)"><a role="menuitem"  href="javascript:void(0)">会员主页</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,3)"><a role="menuitem"  href="javascript:void(0)">购物车</a></li>
                                                                        <li class="homepage js_members" role="presentation" ng-click="chooseLinkUrl($index,menu,4)"><a role="menuitem"  href="javascript:void(0)">拼团</a></li>
                                                                        <li>
                                                                            <a class="js-modal-magazine" href="javascript:void(0);" ng-click="changeWaiLink(menu,$index)">自定义外链</a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="js-modal-usercenter" data-type="community" href="javascript:void(0);" ng-click="chooseLinkUrl($index,menu,5)">微社区</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p class="add-shopnav js-add-nav" ng-click="addmenus()">+ 添加一级导航</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="btn_grounp">
            <button class="btn btn-primary" ng-click="processNav()">保存</button>
        </div>
    </div>
    <!-- 修改模板model -->
    <div class="modal export-modal" id="changeModel">
        <div class="modal-dialog" id="modal-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"  ng-click="cancelChooseModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">选择导航模板</a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body shopnav-modal">
                        <div class="clearfix choose_modal">
                            <label class="shopnav-sample">
                                <label class="radio inline">
                                    <input type="radio" name="nav_style" value="1" ng-checked="menus['menusType']==1" ng-model="nav_style">微信公众号自定义菜单样式</label>
                                <div class="shopnav-sample-wechat"></div>
                            </label>
                            <label class="shopnav-sample">
                                <label class="radio inline">
                                    <input type="radio" name="nav_style" value="2" ng-checked="menus['menusType']==2"  ng-model="nav_style">APP导航模版（图标及底色都可配置）</label>
                                <div class="shopnav-sample-app shopnav-sample-app2"></div>
                            </label>
                           <!--  <label class="shopnav-sample">
                                <label class="radio inline">
                                    <input type="radio" name="nav_style" value="3" ng-checked="menus['menusType']==3"  ng-model="nav_style">带购物车导航模版</label>
                                <div class="shopnav-sample-app shopnav-sample-app3"></div>
                            </label> -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-confirm" ng-click="sureChooseModel()">确定</a>
                        <a href="javascript:void(0);" class="zent-btn js-cancel" ng-click="cancelChooseModel()">取消</a>
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
                                <a href="javascript:void(0);" data-type="goods" class="js-modal-tab"
                                   ng-show="uploadShow" ng-click="showImage()">< 选择图片 |</a>
                            </li>
                            <li ng-show="!uploadShow">
                                <a href="javascript:void(0);" data-type="goods" class="js-modal-tab">我的图片</a>
                            </li>
                            <li class="active" ng-show="uploadShow">
                                <a href="javascript:void(0);" data-type="tag" class="js-modal-tab">上传图片</a>
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
                        </div>
                        <div class="attachment-list-region js-attachment-list-region">
                            <ul class="image-list">
                                <li class="image-item js-image-item" data-id="701007915" ng-repeat="image in uploadImages" ng-click="chooseImage(image,$index)">
                                    <div class="image-box" style="background-image: url(@{{image['FileInfo']['s_path']}})"></div>
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
                            <a href="javascript:void(0);" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;" ng-click="upload()">上传图片</a>
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
    <!-- 营销活动选择模态框 -->
    <div class="modal export-modal" id="activity_model">
        <div class="modal-dialog" id="activity-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <ul class="module-nav modal-tab">
                            <li class="js_switch" ng-class="{true: 'active', false: ''}[activityIndex == $index]" ng-repeat="item in activityNavList" ng-click="switchNav($index)">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">@{{item}}</a>
                            </li>
                            
                           <!--  <li class="link-group link-group-0" style="display: inline-block;">
                                <a href="/v2/showcase/goods/edit" target="_blank" class="new_window">新建微页面</a>
                            </li> -->
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchActivity()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat = "list in activity_list">
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseActivitySure($index,list)">选取</button> 
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
                        <div class="activity_pagenavi"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--backdrop-->
    <div class="modal-backdrop"></div>
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
    <!-- 自定义外链 -->
    <div class="ui-popover top-center" id="setWaiLink">
        <div class="ui-popover-inner">
            <span></span>
            <input class="form-control" type="text" value="" style="margin-bottom: 0;" id="wailink_input" placeholder="https://www.exemple.com">
            <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-save" style="margin-left: 20px;" ng-click="sureSetLink()">确定</a>
            <a href="javascript:void(0);" class="zent-btn js-cancel" ng-click="cancelSetLink()">取消</a>
        </div>
        <div class="arrow"></div>
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
</div>
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script> 
<!-- webuploader -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 分页 -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var store={!! $store !!};
    store.member_url = '/shop/member/index/'+store.id;
    console.log(store);
</script>
<script src="{{ config('app.source_url') }}mctsource/static/js/model.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/product_public.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_hksocsf6.js"></script>
@endsection