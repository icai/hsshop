@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/marketing_common_iyeacann.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_9vp4gg0l.css">
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
                    <a href="javascript:void(0)">降价拍</a>
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
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('content')
    <div class="content">
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix">
            <div class="pull-left">
                <ul class="tab_nav">
                    <li>
                        <a href="">所有拍卖</a>
                    </li>
                    <li>
                        <a href="">未开始</a>
                    </li>
                    <li class="">
                        <a href="">进行中</a>
                    </li>
                    <li>
                        <a href="">已结束</a>
                    </li>
                </ul>
            </div>
            <div class="pull-right">
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【拍卖管理】使用教程
                </a>
            </div>
        </div>
        <div class="widget-list-filter clearfix">
            <div class="pull-left">
                <a
                        class="btn btn-success click-none"
                        href="{{ URL('/merchants/marketing/cutsBuy/add') }}"

                        data-target="#addModal">新建拍卖</a>
            </div>
            <div class="pull-right search_module">
                <label class="search_items">
                    <input class="search_input" type="text" name="" value="" placeholder="搜索"/>
                </label>
            </div>
            <div class="no_result" style="clear: both">暂无数据！</div>
        </div>
        <!-- 表单-->
        <form name="myForm" ng-submit="save()" novalidate="novalidate" ng-controller="testController">
            <div class="auction-details">
                <h2>设置拍卖详情</h2>
                <div class="right-div">
                    <div class="activity">
                                <span class="activity-name">
                                     <em class="required">*</em>活动名称 :
                                </span>
                        <span class="add-img" >
                                    <img src="public/hsadmin/images/icon-add.png" alt="" class="img" data-toggle="modal" data-target="#myModal">
                                    <div class="add_img" style="display: none">
                                        <img src="" class='addImg' data-toggle='modal' data-target='#myModal'>
                                        <i class='delImg'>x</i>
                                    </div>
                                </span>
                        <p class="commodity-name margin-bottom">
                            商品名称 ： <a href="" class="font-blue">拍卖商品标题</a>
                        </p>
                        <p class="margin-bottom">
                            商品原价 ： ￥ 0.00
                        </p>
                        <div
                                class="start-price  "
                                ng-class="{'has-error': !myForm.startPrice.$valid  && myForm.startPrice.$touched}">
                            <label for="" class="control-label font-normal">
                                <em class="required">*</em>起拍价格 :
                                <span class="money-symbol">￥</span>
                            </label>
                            <input
                                    type="text"
                                    class="form-control val"
                                    name="startPrice"
                                    ng-model="user.startPrice"
                                    placeholder="0.00"
                                    required
                                    ng-pattern="/^[0-9]\d*(\.\d+)?$/">
                            <p
                                    class="error-prompt"
                                    ng-show="myForm.startPrice.$error.pattern && myForm.startPrice.$touched">
                                &#X3000;必须是大于0的数
                            </p>
                            <p
                                    class="error-prompt"
                                    ng-show="myForm.startPrice.$error.required && myForm.startPrice.$touched">
                                &#X3000;必须填写
                            </p>
                        </div>
                        <div
                                class="start-price"
                                ng-class="{'has-error': !myForm.endPrice.$valid  && myForm.endPrice.$touched}">
                            <label for="" class="control-label font-normal">
                                <em class="required">*</em>最低价格 :
                                <span class="money-symbol">￥</span>
                            </label>
                            <input
                                    type="text"
                                    class="form-control val"
                                    name="endPrice"
                                    ng-model="user.endPrice"
                                    placeholder="0.00"
                                    required
                                    ng-pattern="/^[0-9]\d*(\.\d+)?$/">
                            <p
                                    class="error-prompt"
                                    ng-show="myForm.endPrice.$error.pattern && myForm.endPrice.$touched">
                                &#X3000;必须是大于0的数
                            </p>
                            <p
                                    class="error-prompt"
                                    ng-show="myForm.endPrice.$error.required && myForm.endPrice.$touched">
                                &#X3000;必须填写
                            </p>
                        </div>
                        <div class="start-price   start-date">
                            <label for="" class="control-label font-normal">
                                <em class="required">*</em>开始时间 :&nbsp;
                            </label>
                            <input
                                    type="text"
                                    class="form-control start-time val"
                                    id="start_date"
                                    name="startDate"
                                    ng-model="user.startDate"
                                    readonly>
                            <p class="error-prompt start_date">时间必填</p>
                        </div>
                        <div class="start-price">
                            <label for="" class="control-label font-normal">
                                <em class="required">*</em>最低价格 :
                            </label>
                            <div ng-class="{'has-error': !myForm.minute.$valid  && myForm.minute.$touched}">
                                <label for="" class="control-label font-normal">&nbsp;每&nbsp;</label>
                                <input
                                        type="text"
                                        class="form-control small val"
                                        name="minute"
                                        ng-model="user.minute"
                                        required
                                        ng-pattern="/^[1-9]\d*$/">
                            </div>
                            <div ng-class="{'has-error': !myForm.downMoney.$valid  && myForm.downMoney.$touched}">
                                <label class="control-label font-normal">&nbsp;分钟下降
                                    <span class="money-symbol">￥</span>
                                </label>
                                <input
                                        type="text"
                                        class="form-control val"
                                        name="downMoney"
                                        ng-model="user.downMoney"
                                        required
                                        ng-pattern="/^[1-9]\d*(\.\d+)?$/"
                                        placeholder="0.00">
                            </div>
                            <p
                                    class="error-prompt"
                                    ng-show="myForm.downMoney.$error.required && myForm.downMoney.$touched">
                                必须填写
                            </p>
                            <p
                                    class="error-prompt"
                                    ng-show="myForm.downMoney.$error.pattern && myForm.downMoney.$touched">
                                必须是大于0的数
                            </p>
                        </div>
                        <div class="start-price">
                            <label class="control-label font-normal">持续时间 :&nbsp;</label>
                            <span class="calc-time"></span>
                            <span class="calc-start">计算持续时间</span><br>
                            <span class="margin-left1">下降到结束加格后还会持续一个时间周期</span>
                        </div>
                        <div class="start-price">
                            <labelclass="control-label font-normal">结束时间 :&nbsp;</label>
                            <span class="end-time">这里显示结束时间</span>
                        </div>
                        <div
                                class="start-price"
                                ng-class="{'has-error': !myForm.num.$valid  && myForm.num.$touched}">
                            <label class="control-label font-normal">
                                <em class="required">*</em>拍卖件数 :&nbsp;
                            </label>
                            <input
                                    type="text"
                                    class="form-control start-time val"
                                    name="num"
                                    ng-model="user.num"
                                    required
                                    ng-pattern="/^[1-9]\d*$/"
                                    placeholder="不受商品库存限制"><br>
                            <span class="margin-left">如需修改库存，请在
                                        <a href="" class="blue_38f">商品管理</a>中更新
                                    </span>
                            <p class="error-prompt greater"
                               ng-show="myForm.num.$error.required && myForm.num.$touched">请填写</p>
                            <p class="error-prompt greater"
                               ng-show="myForm.num.$error.pattern && myForm.num.$touched">必须是大于0的数</p>
                        </div>
                        <div class="start-price">
                            <label class="control-label font-normal">每人限购 :&nbsp;</label>
                            <label class="font-normal">1件（每场）</label>
                        </div>
                    </div>
                </div>
                <!--手机图片-->
                <div class="left-div">
                    <img src="public\hsadmin\images/titlebar.png" alt="">
                    <span class="iphone-font">拍卖</span>
                    <div class="main-img">拍卖商品主图</div>
                    <div class="go-time">
                        <span class="the-price">降价拍</span>
                        <div class="currcy">
                            <span class="white">￥<span class="white big-x">X</span><span class="del">起价拍 ： ￥￥</span></span>
                            <p class="next">
                                <span class="next-font">下次降价 ：</span><br>
                                <span class="letter">D</span>
                                <span> :</span>
                                <span class="letter">H</span>
                                <span> :</span>
                                <span class="letter">M</span>
                                <span> :</span>
                                <span class="letter">S</span>
                            </p>
                        </div>
                        <h3 class="auction-title">拍卖商品标题</h3>
                        <div class="range-rule">
                            <span class="range">降价幅度 ： 每X分钟降Y元</span>
                            <span class="rule">抢拍规则 <img src="public\hsadmin\images/下载.png" alt=""></span>
                        </div>
                        <div class="auction-footer">
                            <span>详细信息区</span>
                            <span class="font-s">Sku信息、运费、其他自定义组件内容</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fixed-bottom">
                <div class="btn-no">取消</div>
                <input type="submit" value="保存" class="btn-yes">
                <div class="successPromrt" style="display: none">保存成功</div>
            </div>
        </form>
    </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_n43oe9h9.js"></script>
@endsection