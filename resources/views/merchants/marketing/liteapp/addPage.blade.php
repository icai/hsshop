 @extends('merchants.default._layouts')
@section('head_css')
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
    <link href="{{ config('app.source_url') }}mctsource/static/css/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/publish_store.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/xcx_page.css" />
    <style type="text/css">
        .xcx_hide{
            display: none !important;
        }
        .xcx_show{
            display: block !important;
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
                <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/marketing/litePage') }}">小程序</a>
            </li>
            <li>
                <a href="javascript:void(0);">小程序微页面</a>
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
        <input type="hidden" id="wid" value="{{ $wid }}" name="">
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
                            <a href="javascript:void(0);" class="rule_info" ng-show="pageSeting.rule == 1">规则说明</a>
                            <!--add by 韩瑜 2018-10-10 小程序添加模板-->
                            <div class="app-field clearfix @{{editor['editing']}}" data-type="@{{editor['type']}}" ng-repeat = 'editor in editors' style="background:@{{editor['bgcolor']}}" ng-click="tool($event,editor)" ng-mouseover="addboder($event)" ng-mouseout="removeboder($event,editor)" ng-drop="true" ng-drop-success="onDropPageComplete($index, $data,$event)">
                                <div ng-if="editor['type'] == 'bingbing'">
                                    <div class="control-group">
                                        <div class="tpl-fbb" style="background-image:url(@{{editor['bg_image']}})">
                                            <div class="swiper-container js-tpl-fbb js-collection-region">
                                                <ul class="swiper-wrapper clearfix">
                                                    <div class="swiper-slide tpl-fbb-item done" ng-repeat="list in editor['lists']">
                                                        <a href="javascript:;" style="background-image:url(@{{list['bg_image']}})">
                                                            <div class="tpl-fbb-item-wrap">
                                                                <div class="tpl-fbb-item-name">@{{list.title}}</div>
                                                                <div class="tpl-fbb-item-line"></div>
                                                                <div class="tpl-fbb-item-icon" ng-if="list.icon">
                                                                    <img ng-src="@{{list.icon}}" width="30" height="30">
                                                                </div>
                                                                <div class="tpl-fbb-item-text">
                                                                    @{{list.desc}}
                                                                </div>
                                                                <div class="tpl-fbb-item-date">@{{list.tag}}</div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="component-border"></div>
                                    </div>
                                </div>
                                <div ng-drag="true" ng-drag-data="editor">
									<header ng-if="editor['type'] == 'header'"></header>
									<!-- 文本导航 -->
									<textlink ng-if="editor['type'] == 'textlink'"></textlink>
									<!-- 文本导航 -->
									<!-- 优惠券 -->
									<coupon ng-if="editor['type'] == 'coupon'"></coupon>
									<!-- 会员中心默认 -->
									<member ng-if="editor['type'] == 'member'"></member>
									<!-- 编辑器框内容 -->
									<editor-text ng-if="editor['type'] == 'rich_text'" class="custom-richtext"></editor-text>
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
									<search ng-if = "editor['type'] == 'search'"></search>
									<!-- 商品列表 -->
									<goodslist ng-if = "editor['type'] == 'goodslist'"></goodslist>
									<!-- 自定义模块 -->
									<model ng-if="editor['type'] == 'model'"></model>
									<!-- 商品分组 -->
									<goodgroup ng-if="editor['type'] == 'good_group'"></goodgroup>
									<!-- 图片导航 -->
									<imagelink ng-if="editor['type'] == 'image_link'"></imagelink>
									<!-- 营销活动 -->
									<active ng-if="editor['type'] == 'marketing_active'"></active>
                                    <!-- 会员卡 -->
                                    <membercard ng-if="editor['type'] == 'card'"></membercard>
									<!-- 拼团商品 -->
									<spellgoods ng-if="editor['type'] == 'spell_goods'"></spellgoods>
									<!-- 拼团分类 -->
									<spelltitle ng-if="editor['type'] == 'spell_title'"></spelltitle>
									<!-- 享立减商品 -->
									<sharegoods ng-if="editor['type'] == 'share_goods'"></sharegoods>
                                    <!-- 享立减二期 -->
                                    <ligoods ng-if="editor['type'] == 'li_goods'"></ligoods>
                                    <!-- 魔方 -->
                                    <cube ng-if="editor['type'] == 'cube'"></cube>
                                    <!-- 手机号 -->
                                    <mobile ng-if="editor['type'] == 'mobile'"></mobile>
                                    <!-- 视频 -->
                                    <cvideo ng-if="editor['type'] == 'video'"></cvideo>
                                    <!-- 留言板 -->
                                    <research ng-if="editor['type'] == 'research'"></research>
                                    <!-- 在线报名 -->
                                    <research_sign ng-if="editor['type'] == 'researchSign'"></research_sign>
                                    <!-- 在线预约 -->
                                    <research_appoint ng-if="editor['type'] == 'researchAppoint'"></research_appoint>
                                    <!-- 在线投票 -->
                                    <research_vote ng-if="editor['type'] == 'researchVote'"></research_vote>
                                    <!-- 秒杀 -->
                                    <second_kill ng-if="editor['type'] == 'seckill_list'"></second_kill>
                                    <!-- 分类模板 -->
                                    <group-page ng-if="editor['type'] == 'group_page'"></group-page>
                                    <!-- 商品分组模板 -->
                                    <group-template ng-if="editor['type'] == 'group_template'"></group-template>
                                    <!-- 直播组件 -->
                                    <live ng-if="editor['type'] == 'live'"></live>
									<div class="actions"  ng-if="editor['type'] != 'bingbing' && editor['type'] != 'group_page' && editor['type'] != 'group_template'">
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
                <!-- 底部自定义导航 -->
                <div class="js-add-region" ng-show="is_custom == 1" ng-cloak>
                    <div>
                        <div class="app-add-field">
                            <h4>添加内容</h4>
                            <div>
                            <!-- update 2018/07/02 华亢 组件分组 -->
                                <ul>
                                    <p>商品组件</p>
                                    <li ng-click="addgoods(1)">
                                        <a class="js-new-field" data-field-type="goods">添加商品</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="goods" ng-click="addSearch(1)">商品搜索</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="goddslist" ng-click="addGoodsList(1)">商品列表</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(1)">商品分组</a>
                                    </li>
                                </ul>
                                <ul>
                                    <p>图文组件</p>
                                    <li ng-click="addeditor(1)">
                                        <a class="js-new-field" data-field-type="rich_text">富文本</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="title" ng-click="addTitle(1)">标题</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="notice" ng-click="addNotice(1)">公告</a>
                                    </li>
                                </ul>
                                <ul>
                                    <p>营销组件</p>
                                    <li style="position:relative;">
                                        <a class="js-new-field" data-field-type="live" ng-click="addLive(1)">直播</a>
                                        <img style="position:absolute;top:0;right:0;width:27px;height:27px;" src="/hsshop/image/static/live-hot.png" alt="">
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="coupon" ng-click="addCoupon(1)">优惠券</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="marketing_active" ng-click="addSpellGoods(1)">拼团商品</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="marketing_active" ng-click="addShareGoods(1)">享立减商品</a>
                                    </li>
                                    <!--<li ng-if="isShowli == 1">
                                        <a class="js-new-field" data-field-type="marketing_active" ng-click="addLiGoods(1)">集赞活动</a>
                                    </li>-->
                                    <!-- <li>
                                        <a class="js-new-field" data-field-type="marketing_active" ng-click="addActive(1)">秒杀</a>
                                    </li> -->
                                    <li>
                                        <a class="js-new-field" data-field-type="marketing_active" ng-click="addSecondKill(1)">秒杀</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="marketing_active" ng-click="addCard(1)">会员卡</a>
                                    </li>
                                </ul>
                                <ul>
                                    <p>导航组件</p>
                                    <li ng-click="addAdvImages(1)">
                                        <a class="js-new-field" data-field-type="image_ad">图片广告</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" ng-click="addCube(1)">魔方</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="image_link" ng-click="addLinkImages(1)">图片<br />导航</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" data-field-type="image_link" ng-click="addtextLink(1)">文本<br />导航</a>
                                    </li>
                                    <!-- <li>
                                        <a class="js-new-field" data-field-type="image_link" ng-click="addheader()">文本<br />导航</a>
                                    </li> -->
                                    <!-- <li>
                                        <a class="js-new-field" data-field-type="image_link" ng-click="addBingBing()">文本<br />导航</a>
                                    </li> -->
                                </ul>
                                <ul>
                                    <p>其他</p>
                                    <li>
                                        <a class="js-new-field" ng-click="addVideo(1)">视频</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" ng-click="addMobile(1)">联系方式</a>
                                    </li>
                                    <!-- <li>
                                        <a class="js-new-field" ng-click="addResearch(1)">留言板</a>
                                    </li> -->
                                    <li>
                                        <a class="js-new-field" ng-click="addResearchVote(1)">在线投票</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" ng-click="addResearchSign(1)">在线报名</a>
                                    </li>
                                    <li>
                                        <a class="js-new-field" ng-click="addResearchAppoint(1)">在线预约</a>
                                    </li>
                                    <!-- <li>
                                        <a class="js-new-field" data-field-type="model" ng-click="addModel(1)">自定义<br />模块</a>
                                    </li> -->
                                    <!-- <li ng-click="addShop(1)">
                                        <a class="js-new-field" data-field-type="store">
                                            进入<br>店铺
                                        </a>
                                    </li> -->
                                </ul>
                                <!-- update 2018/07/02 华亢 组件分组 end-->
                            </div>
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
                            <label class="control-label label-title">
                                <em class="required">*</em>页面名称：
                            </label>
                            <div class="controls page_title label-div">
                                <input class="form-control" type="text" name="title" value="微页面标题" ng-model="pageSeting.title" required>
                                <p class="help-block error-message ng-hide" ng-show="editorForm.title.$dirty && editorForm.title.$error.required || iserror && editorForm.title.$invalid">此项不能为空</p>
                            </div>
                        </div>
                        

                        <div class="control-group">
                            <label class="control-label label-title">标题背景颜色：</label>
                            <div class="controls label-div">
                                <input type="color" name="color" class="form-control" ng-model="pageSeting.page_bgcolor">
                                <!--add by 韩瑜 2018-9-5 添加16进制颜色输入框-->
						        <input class="form-control color_input" maxlength="7" type="text" value="#f8f8f8" ng-model="pageSeting.page_bgcolor" >
						        <!--end-->
                                <p class="help-desc">颜色修改，需要小程序提交审核通过后可见，慎重修改</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label label-title">标题颜色：</label>
                            <div class="controls">
                               	<select class="form-control" style="width:80%" ng-model="pageSeting.title_color">
                                   	<option value="#000000" ng-selected="pageSeting.title_color == '#000000'">黑色</option>
                                   	<option value="#ffffff" ng-selected="pageSeting.title_color == '#ffffff'">白色</option>
                               	</select>
                               <p class="help-desc" style="padding-left:10px">颜色修改，需要小程序提交审核通过后可见，慎重修改</p>
                            </div>
                        </div>
                        <!--update by 邓钊 2018-6-25  规则注释 -->
                        
                        {{--<div class="control-group">--}}
                            {{--<label class="control-label label-title">--}}
                                {{--规则说明：--}}
                            {{--</label>--}}
                            {{--<div class="controls rule_controls label-div">--}}
                               {{--<input type="radio" name="rule" value="1" ng-model="pageSeting.rule" ng-checked="pageSeting.rule == 1"> 是--}}
                               {{--<input type="radio" name="rule" value="0" ng-model="pageSeting.rule" ng-checked="pageSeting.rule == 0"> 否--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="control-group" ng-show="pageSeting.rule == 1">--}}
                            {{--<label class="control-label label-title">--}}
                                {{--规则标题：--}}
                            {{--</label>--}}
                            {{--<div class="controls page_title label-div">--}}
                                {{--<input class="form-control" type="text" name="rule_title" value="规则标题" ng-model="pageSeting.rule_title">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="control-group" ng-show="pageSeting.rule == 1">--}}
                            {{--<label class="control-label">--}}
                                {{--规则内容：--}}
                            {{--</label>--}}
                            {{--<div class="controls page_title">--}}
                                {{--<textarea class="form-control" type="text" name="rule_desc" value="" ng-model="pageSeting.rule_desc" placeholder="规则内容"></textarea>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <!--end -->
                    </div>
                </div>
                <div class="card_right card_right_list" ng-show="editors[index]['showRight']">
                    <div class="arrow"></div>
                    <div ng-show="!editors[index]['is_add_content']">
                        <div ng-if="editors[index]['cardRight'] == 18">
                            <div class="control-group">
                                <label class="control-label">
                                    <em class="required">*</em>背景图片：</label>
                                <div class="controls bg_image">
                                    <img ng-src="@{{editors[index]['bg_image']}}" width="100" height="100" class="thumb-image">
                                    <a class="control-action js-trigger-image" href="javascript: void(0);" ng-click="changeBg()">修改</a>
                                    <p class="help-desc">建议尺寸：640 x 1080 像素</p>
                                </div>
                            </div>
                            <div class="control-group js-background-link">
                                <label class="control-label">背景链接：</label>
                                <div class="controls" name="link_url" ng-if="!editors[index]['linkUrl']">
                                    <div class="dropdown hover">
                                        <a class="js-dropdown-toggle dropdown-toggle control-action" href="javascript:void(0);" id="guanwang">设置链接到的页面地址
                                            <i class="caret"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="js-modal-magazine" href="javascript:void(0);" ng-click="choosePageLink($index,6)">微页面及分类</a>
                                            </li>
                                            <li>
                                                <a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseShop($index,6)">商品及分类</a>
                                            </li>
                                            <li>
                                                <a class="js-modal-homepage" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,6,store_url,3)">店铺主页</a>
                                            </li>
                                            <li>
                                                <a class="js-modal-usercenter" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,6,member_url,4)">会员主页</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="controls" name="link_url" ng-if=" editors[index]['linkUrl']">
                                    <div class="control-action clearfix">
                                        <div class="pull-left js-link-to link-to">
                                            <a href="@{{editors[index]['linkUrl']}}" target="_blank" class="new-window link-to-title" id="guanwang">
                                                <span class="label label-success">
                                                链接
                                                    <em class="link-to-title-text">@{{editors[index]['linkName']}}</em>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="dropdown hover pull-right" ng-show="editors[index]['dropDown']">
                                            <a class="dropdown-toggle" href="javascript:void(0);">修改
                                                <i class="caret"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="js-modal-magazine" href="javascript:void(0);" ng-click="choosePageLink($index,6)">微页面及分类</a>
                                                </li>
                                                <li>
                                                    <a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseShop($index,6)">商品及分类</a>
                                                </li>
                                                <li>
                                                    <a class="js-modal-homepage" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,6,store_url,3)">店铺主页</a>
                                                </li>
                                                <li>
                                                    <a class="js-modal-usercenter" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,6,member_url,4)">会员主页</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="dropdown hover pull-right" ng-show="!editors[index]['dropDown']">
                                            <a class="dropdown-toggle" href="javascript:void(0);">修改
                                                <i class="caret"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="js-modal-magazine" href="javascript:void(0);" ng-click="choosePageLink($index,6)">微页面及分类</a>
                                                </li>
                                                <li>
                                                    <a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseShop($index,6)">商品及分类</a>
                                                </li>
                                                <li>
                                                    <a class="js-modal-homepage" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,6,store_url,3)">店铺主页</a>
                                                </li>
                                                <li>
                                                    <a class="js-modal-usercenter" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,6, member_url,4)">会员主页</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="separate-line-wrap">
                                <hr>
                                <div class="separate-line">
                                    <p class="text-center">导航链接</p>
                                    <p class="text-center">v</p></div>
                            </div>
                            <div class="control-group js-collection-region">
                                <ul class="choices ui-sortable">
                                    <li class="choice" ng-repeat="list in editors[index]['lists']">
                                        <div class="control-group">
                                            <label class="control-label">
                                                <em class="required">*</em>小标题：
                                            </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" name="small_title_@{{$index}}" value="" ng-model="list.title" required>
                                                <p class="help-block error-message" ng-show="editorForm.small_title_@{{$index}}.$dirty && editorForm.small_title_@{{$index}}.$error.required || iserror && editorForm.small_title_@{{$index}}.$invalid">请填写标题</p>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">
                                                <em class="required">*</em>链接到：
                                            </label>
                                            <div class="controls" name="link_url">
                                                <div class="control-action clearfix">
                                                    <div class="pull-left js-link-to link-to" id="guanwang_level_@{{$index}}">
                                                        <a ng-href="@{{list.linkUrl}}" target="_blank" class="new-window link-to-title">
                                                            <span class="label label-success">
                                                                <em class="link-to-title-text" style="border: none; padding-left:0">@{{list.linkName.length > 0 ? list.linkName : '请选择链接'}}</em>
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="dropdown hover pull-right" ng-show="list.dropDown">
                                                        <a class="dropdown-toggle" href="javascript:void(0);">修改
                                                            <i class="caret"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="js-modal-magazine" href="javascript:void(0);" ng-click="choosePageLink($index,7)">微页面及分类</a>
                                                            </li>
                                                            <li>
                                                                <a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseShop($index,7)">商品及分类</a>
                                                            </li>
                                                            <li>
                                                                <a class="js-modal-homepage" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,7, store_url,3)">店铺主页</a>
                                                            </li>
                                                            <li>
                                                                <a class="js-modal-usercenter" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,7, member_url,4)">会员主页</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="dropdown hover pull-right" ng-show="!list.dropDown">
                                                        <a class="dropdown-toggle" href="javascript:void(0);">修改
                                                            <i class="caret"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="js-modal-magazine" href="javascript:void(0);" ng-click="choosePageLink($index,7)">微页面及分类</a>
                                                            </li>
                                                            <li>
                                                                <a class="js-modal-goods" href="javascript:void(0);" ng-click="chooseShop($index,7)">商品及分类</a>
                                                            </li>
                                                            <li>
                                                                <a class="js-modal-homepage" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,7, store_url,3)">店铺主页</a>
                                                            </li>
                                                            <li>
                                                                <a class="js-modal-usercenter" href="javascript:void(0);" ng-click="chooseLinkUrl($event,$index,7,member_url,4)">会员主页</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">小图标：</label>
                                            <div class="controls" ng-show="list.icon">
                                                <div class="icon">
                                                    <img ng-src="@{{list.icon}}" width="30" height="30" class="thumb-image">
                                                </div>
                                                <a class="control-action js-icon-image" href="javascript: void(0);" ng-click="changeIcon($index)">修改</a>
                                                <span>|</span>
                                                <a href="javascript:;" class="control-action js-trigger-delete-icon" ng-click="deleteIcon($index)">删除</a>
                                                <p class="help-desc">建议尺寸：60 x 60 像素</p>
                                            </div>
                                            <div class="controls" ng-show="!list.icon">
                                                <a class="control-action js-icon-image" href="javascript: void(0);" ng-click="changeIcon($index)">选择图片</a>
                                                <p class="help-desc">建议尺寸：60 x 60 像素</p>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">简介：</label>
                                            <div class="controls">
                                                <input type="text" name="text" value="" class="form-control" ng-model="list.desc">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">背景图：</label>
                                            <div class="controls" ng-show="!list.bg_image">
                                                <a class="control-action js-bg-image" href="javascript: void(0);" ng-click="changeSmallBg($index)">选择图片</a>
                                                <p class="help-desc">建议尺寸：85 x 188 像素</p>
                                            </div>
                                            <div class="controls" ng-show="list.bg_image">
                                                <img ng-src="@{{list.bg_image}}" width="100" height="100" class="thumb-image">
                                                <a class="control-action js-bg-image" href="javascript: void(0);" ng-click="changeSmallBg($index)">修改</a>
                                                <span>|</span>
                                                <a href="javascript:;" class="control-action js-trigger-delete-bg" ng-click="deleteSmallBg($index)">删除</a>
                                                <p class="help-desc">建议尺寸：85 x 188 像素</p>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">标签：</label>
                                            <div class="controls">
                                                <input type="text" name="tag" value="" class="form-control" ng-model="list.tag">
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <span class="action delete close-modal" title="删除" ng-click="deleteSmallList($index)">×</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="control-group options" style="display: block;">
                                <a class="add-option js-add-option" href="javascript:void(0);" ng-click="addSmallList()">
                                    <i class="icon-add"></i>添加一个导航链接
                                </a>
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
                        <!-- 公告右侧 -->
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
                        <!-- 美妆小店头部 -->
                        <crheader ng-show="editors[index]['cardRight'] == 17"></crheader>
                        <!-- 营销活动 -->
                        <cractive ng-show="editors[index]['cardRight'] == 19"></cractive>
                        <!-- 官网图文模板 -->
                        <crimage-text-model ng-show="editors[index]['cardRight'] == 20"></crimage-text-model>
                        <!-- 会员卡模板 -->
                        <crmembercard ng-show="editors[index]['cardRight'] == 21"></crmembercard>
                        <!-- 拼团商品右侧 -->
                        <crspellgoods ng-show="editors[index]['cardRight'] == 22"></crspellgoods>
                        <!-- 拼团分类右侧 -->
                        <crspelltitle ng-show="editors[index]['cardRight'] == 23"></crspelltitle>
                        <!-- 享立减商品 -->
                        <crsharegoods ng-show="editors[index]['cardRight'] == 24"></crsharegoods>
                        <!-- 魔方 -->
                        <crcube ng-show="editors[index]['cardRight'] == 25"></crcube>
                        <!-- 享立减二期 -->
                        <crligoods ng-show="editors[index]['cardRight'] == 26"></crligoods>
                        <!-- 手机号 -->
                        <crmobile ng-if="editors[index]['cardRight'] == 27"></crmobile>
                        <!-- 视频右侧 -->
                        <crvideo ng-show="editors[index]['cardRight'] == 28"></crvideo>
                        <!-- 留言板 -->
                        <cr_research ng-if="editors[index]['cardRight'] == 30"></cr_research>
                        <!-- 在线投票 -->
                        <cr_research_vote ng-if="editors[index]['cardRight'] == 31"></cr_research_vote>
                        <!-- 在线报名 -->
                        <cr_research_sign ng-if="editors[index]['cardRight'] == 32"></cr_research_sign>
                        <!-- 在线预约 -->
                        <cr_research_appoint ng-if="editors[index]['cardRight'] == 33"></cr_research_appoint>
                        <!-- 秒杀右侧 -->
                        <cr_second_kill ng-if="editors[index]['cardRight'] == 36"></cr_second_kill>
                        <!-- 分类模板右侧 -->
                        <crgroup-page ng-if="editors[index]['cardRight'] == 37"></crgroup-page>
                        <!-- 商品分组模板右侧 -->
                        <crgroup-template ng-if="editors[index]['cardRight'] == 38"></crgroup-template>
                        <!-- 直播组件右侧 -->
                        <crlive ng-if="editors[index]['cardRight'] == 39"></crlive>
                    </div>
                    <div class="app-add-field app-add-field1" ng-show="editors[index]['is_add_content']">
                        <h4>添加内容</h4>
                        <div>
                            <!-- update 2018/07/02 华亢 组件分组 -->
                            <ul>
                                <p>商品组件</p>
                                <li ng-click="addgoods(2)">
                                    <a class="js-new-field" data-field-type="goods">添加商品</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="goods" ng-click="addSearch(2)">商品搜索</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="goddslist" ng-click="addGoodsList(2)">商品列表</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(2)">商品分组</a>
                                </li>
                            </ul>
                            <ul>
                                <p>图文组件</p>
                                <li ng-click="addeditor(2)">
                                    <a class="js-new-field" data-field-type="rich_text">富文本</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addTitle(2)">标题</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="notice" ng-click="addNotice(2)">公告</a>
                                </li>
                            </ul>
                            <ul>
                                <p>营销组件</p>
                                <li style="position:relative;">
                                    <a class="js-new-field" data-field-type="live" ng-click="addLive(2)">直播</a>
                                    <img style="position:absolute;top:0;right:0;width:27px;height:27px;" src="/hsshop/image/static/live-hot.png" alt="">
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="coupon" ng-click="addCoupon(2)">优惠券</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="marketing_active" ng-click="addSpellGoods(2)">拼团商品</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="marketing_active" ng-click="addShareGoods(2)">享立减商品</a>
                                </li>
                                <!--<li ng-if="isShowli == 1">
                                    <a class="js-new-field" data-field-type="marketing_active" ng-click="addLiGoods(2)">集赞活动</a>
                                </li>-->
                                <li>
                                    <a class="js-new-field" data-field-type="marketing_active" ng-click="addSecondKill(2)">秒杀</a>
                                </li>
                                <!-- <li>
                                    <a class="js-new-field" data-field-type="marketing_active" ng-click="addActive(2)">秒杀</a>
                                </li> -->
                                <li>
                                    <a class="js-new-field" data-field-type="marketing_active" ng-click="addCard(2)">会员卡</a>
                                </li>
                            </ul>
                            <ul>
                                <p>导航组件</p>
                                <li ng-click="addAdvImages(2)">
                                    <a class="js-new-field" data-field-type="image_ad">图片广告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addCube(2)">魔方</a>
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
                            <ul>
                                <p>其他</p>
                                <li>
                                    <a class="js-new-field" ng-click="addVideo(2)">视频</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addMobile(2)">联系方式</a>
                                </li>
                                <!-- <li>
                                    <a class="js-new-field" ng-click="addResearch(1)">留言板</a>
                                </li> -->
                                <li>
                                    <a class="js-new-field" ng-click="addResearchVote(2)">在线投票</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addResearchSign(2)">在线报名</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addResearchAppoint(2)">在线预约</a>
                                </li>
                                <!-- <li>
                                    <a class="js-new-field" data-field-type="model" ng-click="addModel(1)">自定义<br />模块</a>
                                </li> -->
                                <!-- <li ng-click="addShop(1)">
                                    <a class="js-new-field" data-field-type="store">
                                        进入<br>店铺
                                    </a>
                                </li> -->
                            </ul>
                            <!-- update 2018/07/02 华亢 组件分组 end-->
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="btn_grounp">
                    <button class="zent-btn zent-btn-primary js-btn-add" ng-click="processPage(editorForm.$valid,1)">上 架</button>
                    <!-- <button class="zent-btn js-btn-preview" ng-click="previewPage(editorForm.$valid,0)">预览效果</button> -->
                </div>
            </form>
        </div>
        <!-- 商品添加框 -->
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
                                
                              <!--   <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">已上架商品</a>|</li>
                                <li style="display: none;">
                                    <a href="#js-module-tag" data-type="tag" class="js-modal-tab">商品分组</a>|</li> -->
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="{{URL('/merchants/product/create')}}" target="_blank" class="new_window">新建商品</a>
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
                                            <!-- <a href="#" ng-click="refresh()">刷新</a> -->
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
                            <div class="good_pagenavi">
                                <span class="total">共 2 条，每页 8 条</span></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 商品添加框 -->
        <div class="modal export-modal" id="shareGoodModel">
            <div class="modal-dialog" id="share-modal-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <ul class="module-nav modal-tab">
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="{{URL('/merchants/shareEvent/list')}}" target="_blank" class="new_window">新建享立减</a>
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
                                            <!-- <a href="#" ng-click="refresh()">刷新</a> -->
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchShareGoods()">搜</a>
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
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-show="goods_show" ng-click="choose($index,list)">选取</button>
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-hide="goods_show" ng-click="choose_shareGoods_sure($index,list)">选取</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseShareGoodSure()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="share_good_pagenavi">
                                <span class="total">共 2 条，每页 8 条</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 享立减二期添加框 -->
        <div class="modal export-modal" id="liGoodModel">
            <div class="modal-dialog" id="li-modal-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <ul class="module-nav modal-tab">
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="{{URL('/merchants/share/event/save')}}" target="_blank" class="new_window">新建集赞</a>
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
                                            <!-- <a href="#" ng-click="refresh()">刷新</a> -->
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchLiGoods()">搜</a>
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
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-show="goods_show" ng-click="choose($index,list)">选取</button>
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-hide="goods_show" ng-click="choose_liGoods_sure($index,list)">选取</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseLiGoodSure()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="share_good_pagenavi">
                                <span class="total">共 2 条，每页 8 条</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 拼团商品列表 -->
        <div class="modal export-modal" id="spell_Modal">
            <div class="modal-dialog" id="spell-modal-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <ul class="module-nav modal-tab">
                                
                              <!--   <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">已上架商品</a>|</li>
                                <li style="display: none;">
                                    <a href="#js-module-tag" data-type="tag" class="js-modal-tab">商品分组</a>|</li> -->
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="{{URL('/merchants/marketing/togetherGroupList')}}" target="_blank" class="new_window">新建拼团</a>
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
                                            <!-- <a href="#" ng-click="refresh()">刷新</a> -->
                                        </div>
                                    </th>
                                    <th class="information"></th>
                                    <th>
                                        <div class="td-cont">
                                            <span>创建时间</span>
                                        </div>
                                    </th>
                                    <th class="opts">
                                        <div class="td-cont" style="text-align:center">
                                            <form class="form-search">
                                                <div class="input-append">
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchSpell()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat = "list in spellGoodList" id="list_@{{list.id}}" data-price="@{{list['price']}}">
                                    <td class="image">
                                        <div class="td-cont">
                                            <img ng-src="@{{host}}@{{list['img']}}">
                                        </div>
                                    </td>
                                    <td class="title">
                                        <div class="td-cont">
                                            <a target="_blank" class="new_window" href="javascript:void(0);">@{{list['title']}}</a>

                                        </div>
                                    </td>
                                    <td>
                                        <span>
                                            @{{list['created_at']}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-show="goods_show" ng-click="chooseSpell($index,list)">选取</button>
                                            <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-hide="goods_show" ng-click="chooseSpell_sure($index,list)">选取</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div style="" class="js-confirm-choose pull-left" ng-show="tempSure && goods_show" ng-click="chooseSpellSure()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="spell_pagenavi">
                                <span class="total"></span>
                            </div>
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
                                 <!-- <div class="search-region">
                                    <div class="ui-search-box">
                                        <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                                    </div>
                                </div> -->
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
                                            <span class="ui-pagination-total"></span>
                                        </div>
                                    </div>
                                    <a href="javascript:;" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;" ng-click="upload()" ng-show="picNumber">上传图片</a>
                                    <!--列表中的图片个数为0的时候显示这个模态框  2018-10-22 增加picNumber属性-->
                                    <div id="layerContent_right" ng-show="!picNumber">
                                        <a class="js_addImg" href="#uploadImg"  ng-click="upload()">+</a>
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
                                <!-- 用于获取图片分组id 默认为0 -->
                                <input type="hidden" name="classifyId" value="0">
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
                                                <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseCoupon($index,list)">选取</button> 
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
         <!-- 魔方选择优惠券弹窗 -->
         <div class="modal export-modal" id="cube_coupon_model">
            <div class="modal-dialog" id="cube_coupon_model-dialog">
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
                                                        <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchCubeCoupon()">搜</a>
                                                    </div>
                                                </form>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat = "list in couponList" id="list_@{{list.id}}" data-price="@{{list['price']}}">
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
                                                <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseCubeCoupon($index,list)">选取</button> 
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <!-- <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" >
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div> -->
                            <div class="coupon_pagenavi"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 魔方选择优惠券弹窗 -->
        <!-- 会员卡弹窗 -->
        <div class="modal export-modal" id="my_card_model">
            <div class="modal-dialog" id="card_model-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">会员卡</a>|</li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/member/membercard/add" target="_blank" class="new_window">新建会员卡</a>
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
                                                <span>会员权益</span>
                                            </div>
                                        </th>
                                        <th class="opts" class="col-sm-4 col-xs-4">
                                            <div class="td-cont">
                                                <form class="form-search">
                                                    <div class="input-append">
                                                        <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchCard()">搜</a>
                                                    </div>
                                                </form>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat = "list in cardList" id="list_@{{$index}}" data-price="@{{list['price']}}">
                                        <td class="image">
                                            <div class="td-cont">
                                               @{{list['name']}} 
                                            </div>
                                        </td>
                                        <td>
                                            <span>
                                                @{{list['power_desc']}}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="td-cont text-right">
                                                <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseCard($index,list)">选取</button> 
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseCardSure()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="card_pagenavi"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 添加图片广告拼团Model -->
        <div class="modal export-modal" id="page_model_pintuan">
            <div class="modal-dialog" id="page-dialog_pintuan">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">拼团活动</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/marketing/togetherGroupAdd" target="_blank" class="new_window">新建拼团活动</a>
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
                                                    {{--<input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchSpellModel()">搜</a>--}}
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseSpellModelSure($index,list)">选取</button>
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
        <!-- 添加图片导航会员卡Model -->
        <div class="modal export-modal" id="page_model_card">
            <div class="modal-dialog" id="page-dialog-card">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">会员卡</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/member/membercard/add" target="_blank" class="new_window">新建会员卡</a>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="td-cont">
                                            <span>名称 </span>
                                            <!-- <a href="#">刷新</a> -->
                                        </div>
                                    </th>
                                    <th class="information" style='width: 130px'></th>
                                    <th>
                                        <div class="td-cont">
                                            <span>会员权益</span>
                                        </div>
                                    </th>
                                    <th class="opts">
                                        <div class="td-cont">
                                            <form class="form-search">
                                                <div class="input-append">
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchMenTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchMenModel()">搜</a>
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
                                                @{{list['power_desc']}}
                                            </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseMenModelSure($index,list)">选取</button>
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
                                    <a href="/merchants/marketing/liteAddPage" target="_blank" class="new_window">新建微页面</a>
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


        <!-- 添加大转盘Model -->
        <div class="modal export-modal" id="wheel_model">
            <div class="modal-dialog" id="wheel-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">大转盘</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/marketing/addWheel" target="_blank" class="new_window">新建大转盘</a>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchWheelPage()">搜</a>
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseWheelPageLinkSure($index,list)">选取</button>
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

        <!-- 添加刮刮乐活动Model -->
        <div class="modal export-modal" id="scratchCard">
            <div class="modal-dialog" id="scratchCard-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">刮刮乐</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/marketing/addScratch" target="_blank" class="new_window">新建刮刮乐</a>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="td-cont" style="padding: 7px 0 3px 8px;">
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
                                                        <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="scratchCardPage()">搜</a>
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
                                                <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="scratchCardSure($index,list)">选取</button>
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
        <!-- 拼团分类选择弹窗model -->
        <div class="modal export-modal" id="page_spell_model">
            <div class="modal-dialog" id="page-spell-dialog">
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
                                    <a href="/merchants/marketing/liteAddPage" target="_blank" class="new_window">新建微页面</a>
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
                                                <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseSpellPageLinkSure($index,list)">选取</button> 
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseSpellPageSure()">
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
        <!-- 秒杀模态框 -->
        <div class="modal export-modal" id="kill_model">
            <div class="modal-dialog" id="kill_model_dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">秒杀</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/marketing/seckill/set" target="_blank" class="new_window">新建秒杀</a>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;text-align: left;">
                                            <div class="td-cont">
                                                <span>标题 </span>
                                                <a href="javascript:void(0);" ng-click="killRefresh()">刷新</a>
                                            </div>
                                        </th>
                                        <!-- <th class="information"></th> -->
                                        <th colspan="2">
                                            <div class="td-cont">
                                                <span>创建时间</span>
                                            </div>
                                        </th>
                                        <th class="opts"  style="width: 10%">
                                            <div class="td-cont">
                                                <form class="form-search">
                                                    <div class="input-append">
                                                        <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchKillTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchKill()">搜</a>
                                                    </div>
                                                </form>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat = "list in killList">
                                        <td class="title">
                                            <div class="td-cont">
                                                <a target="_blank" class="new_window" ng-href="/merchants/marketing/seckill/detail/@{{list['id']}}" style="word-break: break-all;">@{{ list['activeName'] }}</a>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <span>
                                                @{{list['start_at']}} 至 @{{list['end_at']}}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="td-cont text-right">
                                                <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseKillItem($index,list)">选取</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="sureKill()">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="kill_page_component"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--add by 韩瑜 2018-9-27-->
        <!-- 添加商品分组模板享立减Model -->
        <div class="modal export-modal" id="page_model_shareEvent">
            <div class="modal-dialog" id="page-dialog_shareEvent">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">享立减活动</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/merchants/shareEvent/create" target="_blank" class="new_window">新建享立减活动</a>
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
                                                    {{--<input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchSpellModel()">搜</a>--}}
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="GroupShareEventSure($index,list)">选取</button>
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
        <!-- 视频模态框 -->
        <div class="modal export-modal myModal-adv" id="video_model">
            <div class="modal-dialog" id="video_model_dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideVideoModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li>
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">我的视频</a>
                                </li>
                            </ul>
                            <div class="search-region">
                                <div class="ui-search-box">
                                    <input class="txt js-search-input" type="text" placeholder="搜索" value="" ng-keypress="videoSearch($event)">
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="category-list-region js-category-list-region">
                                <ul class="category-list">
                                    <li class="js-category-item" ng-class="{true:'active',false:''}[video.groupingIndex == $index]" ng-repeat="item in video.groupList" ng-click="switchVideoGroup(item,$index)">    
                                        @{{item.name}}
                                        <span class="category-num" ng-bind="item.number"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="attachment-list-region js-attachment-list-region">
                                <ul class="image-list">
                                    <li class="video_item" ng-repeat = "item in video.modelVideoList" ng-click="checkedVideoItem(item,$index)">
                                        <div class="video_item_detail">
                                            <img class="detail_cover" ng-src="{{ imgUrl() }}@{{item.file_cover}}">
                                            <div class="detail_info">
                                                <div class="detail_info_top">
                                                    <span>@{{item.FileInfo.name}}</span>
                                                    <!-- <span>00:05</span> -->
                                                </div>
                                                <div class="detail_info_sub">
                                                    <span>@{{item.created_at}}</span>
                                                    <!-- <span>167.5kb</span> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="attachment-selected" ng-class="{true:'',false:'hide'}[video.checkedIndex == $index]">
                                            <i class="icon-ok icon-white"></i>
                                        </div>
                                    </li>

                                </ul>
                                <div class="attachment-pagination js-attachment-pagination">
                                    <div class="video_model_page">
                                        <span class="ui-pagination-total"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer clearfix">
                            <div class="text-center">
                                <button class="ui-btn js-confirm ui-btn-disabled" disabled="disabled" ng-class="{true:'',false:'hide'}[video.checkedIndex == -1]">确认</button>
                                <button class="ui-btn js-confirm ui-btn-primary" ng-class="{true:'hide',false:''}[video.checkedIndex == -1]" ng-click="sureUseVideo()">确认</button>
                            </div>
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
        <!--update by 韩瑜 2018-8-13 新营销活动弹窗-->
        <!-- 营销活动选择模态框 -->
        <div class="modal export-modal" id="activity_model">
            <div class="modal-dialog" id="activity-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                        	<!--update by 韩瑜 2018-8-9 营销活动弹框修改-->
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <p class="activity_title">营销活动</p>
                        </div>
                        <div class="activity_item">
                        	<ul class="module-nav modal-tab">
                                <li class="js_switch" ng-class="{true: 'active', false: ''}[activityIndex == $index]" ng-repeat="item in activityNavList" ng-click="switchNav($index)">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">@{{item}}</a>
                                </li>
                               <!--  <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="/v2/showcase/goods/edit" target="_blank" class="new_window">新建微页面</a>
                                </li> -->
                            </ul>
                        </div>
                        <div class="activity_btns">
                            <p class="newactivity_btn" ng-click="newActivity()">新建活动</p>
                            <p class="flush_btn" ng-click="flushActivity(switchIndex,shopLinkPosition)"><span><img src="{{ config('app.source_url') }}mctsource/images/activity_flush.png" alt="" /></span>刷新</p>
                        </div>
                        <!--end-->
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
        <!-- 底部logo 开始 -->
        <!--选择图文弹框开始-->
        <div class="modal" id="text_image_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style="display:none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="hideModel()">
                                &times;
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" data-type="goods" class="js-modal-tab">单条图文</a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body" style="min-height: 400px;">
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchTextNews()">搜</a>
                                                </div>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody class="small">
                                <tr class="table_info" ng-repeat="list in textImageList">
                                    <td class="title" colspan="2">
                                        <div class="title_content">
                                            <div class="img_text">
                                                <span class="green">图文</span>
                                                <a class="co_blue" href="javascript:void(0);">@{{list.title}}</a>
                                            </div>
                                            <div class="read_all clearfix">
                                                <a class="jump" href="javascript:void(0);">
                                                    <span>阅读全文</span>
                                                    <span class="pull-right">&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span>
                                            @{{list['created_at']}}
                                        </span>
                                    </td>
                                    <td class="choose_btn">
                                        <div class="td-cont text-right">
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="choose_text_image($index,list)" ng-class="list.isActive ? 'btn-primary': ''">选取</button> 
                                        </div>
                                    </td>
                               </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer clearfix">
                        <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="choose_text_image_sure()">
                            <input type="button" class="btn btn-primary" value="确定使用">
                        </div>
                        <div class= "myModalPage"></div><!-- 分页 -->
                    </div>
                </div>
            </div>
        </div>
        <!--选择图文弹框结束-->
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
                                            </div>
                                            <p class="zent-form__help-desc">
                                                <span>
                                                    点击“+”选择视频，视频大小不超过30 MB，建议时长9-30秒，建议宽高比16:9
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
                                                <select style="position: relative;top: 4px;" name="grounp">
                                                    <option value="@{{grounp.id}}" ng-repeat="grounp in grounps">@{{grounp.name}}</option>
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
                                                <a class="add-goods" href="javascript:void(0);"><i>+添加图片</i><input type="file" id="upload_image" style="position:absolute;top:0;left:0;right:0;bottom:0;opacity:0;width: 100%;" accept="image/jpg, image/jpeg, image/png"></a>
                                            </div>
                                            <div>
                                                <span class="up_tip">图片建议尺寸:宽度710px</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rc-video-upload__publish">
                                        <div>
                                            <label class="zent-checkbox-wrap zent-checkbox-checked">
                                                <span class="zent-checkbox">
                                                    <input name="aggree_input" type="checkbox" value="">
                                                </span>
                                                <span>
                                                    同意《
                                                    <a href="https://www.huisou.cn/home/index/detail/654/news" target="_blank" rel="noopener noreferrer">视频上传服务协议</a>
                                                    》
                                                </span>
                                            </label>
                                            <button type="submit" class="zent-btn-disabled zent-btn video_btn" disabled="">确定</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="zent-dialog-r-backdrop"></div>

        <!-- 添加留言板Model -->
        <div class="modal export-modal" id="research_model">
            <div class="modal-dialog" id="research-dialog">
                <form class="form-horizontal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#js-module-goods" data-type="goods" class="js-modal-tab">留言板</a>
                                    <span>|</span>
                                </li>
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a  target="_blank" class="new_window">新建留言板</a>
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
                                                    <input class="input-small js-modal-search-input form-control" type="text" ng-model="researchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchRes()">搜</a>
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
                                            <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseResearch($index,list)">选取</button>
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
        <!-- 图片广告自定义外链弹窗 -->
        <div class="ui-popover top-center" id="setWaiLink">
            <div class="ui-popover-inner">
                <span></span>
                <input class="form-control" type="text" value="" style="margin-bottom: 0;" id="wailink_input" placeholder="/pages/index/index">
                <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-save" style="margin-left: 20px;" ng-click="sureSetLink()">确定</a>
                <a href="javascript:void(0);" class="zent-btn js-cancel" ng-click="cancelSetLink()">取消</a>
            </div>
            <div class="arrow"></div>
        </div>
        <!-- 直播间列表弹框 add by 倪凯嘉 2020.3.12-->
        <div class="modal" id="live">
            <div class="modal-dialog live-dialog" id="live-dialog">
              <form class="form-horizontal">
                <div class="modal-content">
                    <div class="live-dialog-header">
                        <span class="dialog-title">选择直播间</span>
                        <span class="live-close" ng-click="hideModel()">×</span>
                    </div>
                    <div class="live-dialog-body">
                        <div class="live-btn-group">
                            <div class="confirm-btn-plain" ng-click="showLiveDialog(2)">刷新列表</div>
                            <a class="confirm-btn" style="margin-left:11px" href="https://mp.weixin.qq.com/" target="_blank">新增直播间</a>
                        </div>
                        <div class="table-box">
                          <table class="table">
                              <thead>
                              <tr>
                                  <th></th>
                                  <th>直播标题</th>
                                  <th>主播昵称</th>
                                  <th>开播时间</th>
                                  <th>直播状态</th>
                              </tr>
                              </thead>
                              <tbody class="table-box">
                              <tr ng-repeat="list in liveList">
                                  <td>
                                    <div class="td-box">
                                      <label class='label'>
                                        <input type="radio" name="room" value="@{{list.id}}" ng-model="currentRoomId"  ng-change="chooseLive(list)">
                                      </label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="td-box">@{{list.name}}</div>
                                  </td>
                                  <td>
                                    <div class="td-box">@{{list.anchor_name}}</div>
                                  </td>
                                  <td>
                                    <div class="td-box time">
                                      <div>@{{list.start_time}}</div>
                                      <div>至</div>
                                      <div>@{{list.end_time}}</div>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="td-box">
                                      <div ng-if="list.live_status == 101" class="status-101">直播中</div>
                                      <div ng-if="list.live_status == 102" class="status-102">未开始</div>
                                      <div ng-if="list.live_status == 103" class="status-102">已结束</div>
                                      <div ng-if="list.live_status == 104" class="status-102">禁播</div>
                                      <div ng-if="list.live_status == 105" class="status-102">暂停中</div>
                                      <div ng-if="list.live_status == 106" class="status-102">异常</div>
                                      <div ng-if="list.live_status == 107" class="status-102">已过期</div>
                                    </div>
                                  </td>
                              </tr>
                              </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="live-dialog-footer">
                        <div class="confirm-btn" ng-if= "tempSure" ng-click="selectLiveRoom()">确定</div>
                        <div class="confirm-btn-plain" ng-if= "!tempSure">确定</div>
                        <div class="good_pagenavi"></div>
                    </div>
                </div>
              </form>
            </div>
        </div>
        <!-- 直播间授权提示弹框 add by 倪凯嘉 2020.3.13-->
        <div class="modal" id="unauthorized">
            <div class="modal-dialog live-dialog unauthorized-dialog" id="unauthorized-dialog">
              <form class="form-horizontal">
                <div class="modal-content">
                    <div class="live-dialog-header">
                      <span class="dialog-title">提示</span>
                      <span class="live-close" ng-click="hideModel()">×</span>
                    </div>
                    <div class="unauthorized-body">
                      使用直播功能，您需要先进行“直播权限”授权。
                    </div>
                    <div class="live-dialog-footer unauthorized-dialog-footer">
                      <div class="confirm-btn-plain" ng-click="hideModel()">取消</div>
                      <a class="confirm-btn" href="/merchants/marketing/xcx/list" style="margin-left:14px">去授权</a>
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
   
@endsection
@section('page_js')
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
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
<script src="{{ config('app.source_url') }}static/js/md5.js"></script>
<!-- chosen -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<script src="{{ config('app.source_url') }}mctsource/static/js/cropper.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/cropper_img.js"></script>
<script>
    var imgUrl = "{{ imgUrl() }}";
    var _host = "{{ config('app.source_url') }}";
    var host = "{{ config('app.url') }}";
    var wid = "{{ $wid }}";
    var videoUrl = "{{videoUrl()}}";
    var isShowli = 0;//是否显示集赞
    var isCard = 1;
    var page_template="";
    var store={!! $store !!};
    if(store.logo == ''){
        store.logo = _host + '/home/image/huisouyun_120.png';
    }else{
        store.logo = imgUrl + store.logo;
    }
    store.member_url = '/shop/member/index/'+store.id;
    console.log(store);
</script>
<!-- 模块公共js -->
<script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
<script src="{{ config('app.source_url') }}mctsource/static/js/xcx_model.js?t=123"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/xcx_product_public.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/xcx_page.js"></script>
@endsection