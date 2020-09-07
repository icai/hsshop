@extends('merchants.default._layouts')
@section('head_css')
    <!-- 时间插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_h2m4vgfp.css" />
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
                    <a href="javascript:void(0)">满减送</a>
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
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix mgb10">
            <!-- 左侧 开始 -->
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li  class="hover">
                        <a href="$status=1">所有满减送</a>
                    </li>
                    <li>
                        <a href="$status=2">未开始</a>
                    </li>
                    <li>
                        <a href="$status=3">进行中</a>
                    </li>
                    <li>
                        <a href="$status=4">已结束</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程 
                </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 开始 -->
        <!-- 灰色提示条 开始 -->
        <div class="form-title">设置满就送</div>
        <!-- 灰色提示条 结束 -->
        <!-- 表单 开始 -->
        <form class="add_form form-horizontal f12" method="" action="">
            <!-- 模块头 开始 -->
            <strong class="group-title">活动信息</strong>
            <!-- 模块头 结束 -->
            <!-- 活动名称 -->
            <div class="form-group">
                <label class="col-sm-2 control-label"><span class="red">*</span>活动名称：</label>
                <div class="col-sm-4">
                    <input class="form-control" type="text" name="name" value="" placeholder="请填写活动名称" />
                </div>
            </div>
            <!-- 生效时间 -->
            <div class="box_start">
                <div class="col-sm-2 control-label">生效时间：</div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <!-- 开始时间 -->
                        <div id='startTime' class='input-group'>
                            <input class="form-control" type='text' name="start_time" placeholder="请填开始时间" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <div class="box_start">
                            <div style="margin-left:-20px">至</div>
                            <div class="col-sm-6">
                                <!-- 结束时间 -->
                                <div id='endTime' class='input-group'>
                                    <input class="form-control" type='text' name="end_time" placeholder="请填结束时间" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="7">最近7天</a>
                                &nbsp;<a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="-30">最近30天</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 模块头 开始 -->
            <strong class="group-title">优惠设置</strong>
            <!-- 模块头 结束 -->
            <!-- 优惠方式 -->
            <div class="form-group">
                <label class="col-sm-2 control-label"><span class="red">*</span>优惠方式：</label>
                <div class="col-sm-8">
                    <div class="radio">
                        <label>
                            <input class="discount_radio" type="radio" name="preferential_method" value="" checked>
                            普通优惠
                        </label>
                    </div>
                    <div class="radio">
                        <label class="box_start">
                            <input class="multistage_discount discount_radio" type="radio" name="preferential_method"  value="" />多级优惠 <div class="gray_999">（每级优惠不累积叠加）</div>
                        </label>
                    </div>
                </div>
            </div>
            <!-- 优惠条件 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">优惠条件：</label>
                <div class="col-sm-10">
                    <!-- 表格 开始 -->
                    <table class="discount_table table">
                        <thead>
                            <tr class="active">
                                <td>层级</td>
                                <td>优惠门槛</td>
                                <td class="text_left" width="400">优惠方式(可多选)</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="level_id">1</span></td>
                                <td>
                                    <div class="form-group">
                                        <div class="display_box">
                                            满&nbsp;&nbsp;<input class="form-control small" type="text" name="meet[]" value="" >&nbsp;&nbsp;元
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="center_start h32">
                                        <div class="center_start">
                                            <label>
                                                <input class="js_reduce" type="checkbox" name="" value="" />减
                                            </label>
                                            <div class="reduce_input form-group no center_start">
                                                &nbsp;&nbsp;
                                                <div class="relative">
                                                    <input class="form-control small" type="text" name="cash[]" value="" />
                                                </div>
                                                &nbsp;&nbsp;
                                            </div>
                                            <div class="tip">现金</div>
                                            <div class="tip_error"></div>
                                        </div>
                                    </div>
                                    <div class="center_start h32">
                                        <label>
                                            <input type="checkbox" name="" value="" />免邮  
                                        </label>
                                    </div>
                                    <div class="center_start h32">
                                        <label>
                                            <input type="checkbox" name="" value="" disabled />送积分  
                                        </label>
                                        <div class="gray_999">(升级认证服务号才可用)</div>
                                    </div>
                                    <div class="center_start h32">
                                        <label>
                                            <input class="give_discount" type="checkbox" name="" value="" />送 
                                        </label>
                                        <div class="tip">优惠</div>
                                        <div class="discount_select no">
                                            <select class="mglr5" name="coupon">
                                                <option value="" selected>测试</option>
                                                <option value="">测试b</option>
                                                <option value="">测试优惠码</option>
                                            </select>
                                            <a class="blue_38f" href="JavaScript:void(0);">刷新</a>|
                                            <a class="blue_38f" href="javascript:void(0);">新建</a>
                                        </div>
                                    </div>
                                    <div class="center_start h32">
                                        <label>
                                            <input class="give_giveaway" type="checkbox" name="" value="" />送 
                                        </label>
                                        <div class="tip">赠品</div>
                                        <div class="giveaway_select no">
                                            <select class="mglr5" name="coupon">
                                                <option value="" selected>测试</option>
                                                <option value="">测试b</option>
                                                <option value="">测试优惠码</option>
                                            </select>
                                            <a class="blue_38f" href="JavaScript:void(0);">刷新</a>|
                                            <a class="blue_38f" href="javascript:void(0);">新建</a>
                                        </div>
                                    </div>
                                </td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="no">
                            <tr>
                                <td colspan="4">
                                    <div class="box_start">
                                        <a class="js_add blue_38f" href="javascript:void(0);" data-count="1">+新增一级优惠</a>
                                        <span class="gray_999">最多可设置五个层级</span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <!-- 表格 结束 -->
                </div>
            </div>
            <!-- 模块头 开始 -->
            <strong class="group-title">选择活动商品</strong>
            <!-- 模块头 结束 -->
            <!-- 活动商品 -->
            <div class="form-group">
                <label class="col-sm-2 control-label"><span class="red">*</span>活动商品：</label>
                <div class="col-sm-8">
                    <div class="radio">
                        <label>
                            <input class="partake" type="radio" name="range_type" value="single" checked />全部商品参与
                        </label>
                    </div>
                    <div class="radio">
                        <label class="box_start">
                            <input class="section_partake partake" type="radio" name="range_type" value="single" />部分商品参与 <div class="gray_999"> 已选商品( <span class="num">0 </span>)个</div>
                        </label>
                    </div>
                </div>
            </div>
            <!-- 部分商品模块 开始 -->
            <div class="active_module panel panel-default no">
                <!-- 头部 -->
                <div class="panel-heading">
                    <!-- 导航 -->
                    <ul class="goods_nav">
                        <li class="hover">
                            <strong>选择商品</strong>
                        </li>
                        <li>
                            <strong>已选商品</strong>
                        </li>
                    </ul>
                </div>
                <!-- 主体 -->
                <div class="panel-body">
                    <div class="choose_goods goods_m">
                        <!-- 筛选模块 开始 -->
                        <div class="screen_module">
                            <div class="center_start">
                                <select name="tag" class="js-goods-group">
                                    <option value="0">所有分组</option>
                                    <option value="">列表中隐藏</option>
                                </select>
                                <select name="keyword_type" class="js-search-type">
                                    <option value="goods_title">商品标题</option>
                                    <option value="goods_no">商品编码</option>
                                </select>
                                <input class="js-input" type="text" name="keyword" placeholder="请输入商品编码" value="">
                                <a class="btn btn-primary js_search" href="javascript:void(0);" >搜索</a>
                            </div>
                        </div>
                        <!-- 筛选模块 结束 -->
                        <!-- 表单 -->
                        <table class="active_table table table-hover">
                            <thead>
                                <tr>
                                    <td class="text_left">商品信息</td>
                                    <td>库存</td>
                                    <td>操作</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="600">
                                        <div class="center_start">
                                            <input class="single_check" type="checkbox" name="" value="" />&nbsp;&nbsp;
                                            <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FjLXLBIeD2650Wxoi-Vxoasy1x_x.jpg?imageView2/2/w/100/h/100/q/75/format/webp" width="60" height="60" />&nbsp;&nbsp;
                                            <a class="goods_content" href="javascript:void(0);">
                                                <span class="blue_38f">测试产品</span>
                                                <p class="orange_f60">￥2.00</p>
                                            </a>
                                        </div>
                                    </td>
                                    <td>2</td>
                                    <td>此商品已经参加其他满减活动</td>
                                </tr>
                                <tr>
                                    <td width="600">
                                        <div class="center_start">
                                            <input class="single_check" type="checkbox" name="" value="" />&nbsp;&nbsp;
                                            <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FjLXLBIeD2650Wxoi-Vxoasy1x_x.jpg?imageView2/2/w/100/h/100/q/75/format/webp" width="60" height="60">
                                            <a class="goods_content" href="javascript:void(0);">&nbsp;&nbsp;
                                                <span class="blue_38f">测试产品</span>
                                                <p class="orange_f60">￥2.00</p>
                                            </a>
                                        </div>
                                    </td>
                                    <td>2</td>
                                    <td>此商品已经参加其他满减活动</td>
                                </tr>
                                <tr>
                                    <td class="600">
                                        <div class="center_start">
                                            <input class="single_check" type="checkbox" name="" value="" />&nbsp;&nbsp;
                                            <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FjLXLBIeD2650Wxoi-Vxoasy1x_x.jpg?imageView2/2/w/100/h/100/q/75/format/webp" width="60" height="60">&nbsp;&nbsp;
                                            <a class="goods_content" href="javascript:void(0);">
                                                <span class="blue_38f">测试产品</span>
                                                <p class="orange_f60">￥2.00</p>
                                            </a>
                                        </div>
                                    </td>
                                    <td>2</td>
                                    <td>
                                        <a href="javascript:void(0);">参加</a>
                                    </td>
                                </tr>
                            </tbody> 
                        </table>
                        <!-- 全选&分页模块 开始 -->
                        <div class="select_page">
                            <div class="search_module center_start">
                                <label>
                                    <input class="all_check" type="checkbox" name="" value="" />全选
                                </label>
                                <button type="button" class="btn btn-default">批量参加</button>
                                <button type="button" class="btn btn-primary">第一页全部参加</button>
                            </div>
                            <div class="pagging">
                                分页
                            </div>
                        </div>
                        <!-- 全选&单选模块 结束 -->
                    </div>
                </div>  
            </div>
            <!-- 部分商品模块 结束 -->
            <!-- 保存 -->
            <div class="save_module display_box">
                <button type="button" class="submit_btn btn btn-primary">保存</button>
                <button type="button" class="btn btn-default">预览</button>
            </div>
        </form>
        <!-- 表单 结束 -->
    </div>

@endsection

@section('page_js')
    <!-- 时间插件 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment/moment.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment/locales.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.js"></script>
    <!-- 表单验证插件js文件 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/zh_CN.js"></script>
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_h2m4vgfp.js"></script>
@endsection