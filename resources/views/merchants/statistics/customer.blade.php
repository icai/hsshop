@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_i2xobajm.css" />
@endsection
@section('slidebar')
    @include('merchants.statistics.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <!-- 二级导航三级标题 开始 -->
            <div class="third_title">客户分析</div>
            <!-- 二级导航三级标题 结束 -->
        </div>
        <!-- 帮助与服务 开始 -->
        <div class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
    <div class="content">
        <!-- 区域标题 开始 -->
        <div class="common_top mgb15">
            <span class="common_line"></span>
            <p class="common_title">商品概况</p>
            <div class="common_link"></div>
            <div class="common_right">
                <!-- 分组&搜索 开始 -->
                <div class="category_search">
                    <!-- 分组 开始 -->
                    <div class="grouping_items">
                        <select class="grouping_select" data-placeholder="请选择分组" style="width:350px;" tabindex="1">
                            <option value=""></option>
                            <option value="全部分组">全部分组</option>
                            <option value="列表中隐藏">列表中隐藏</option>
                            <option value="最近30天">最近30天</option>
                            <option value="自然天">自然天</option>
                            <option value="自然月">自然月</option>
                            <option value="自定义">自定义</option>
                        </select>
                    </div>
                    <!-- 分组 结束 -->
                    <!-- 搜索 开始 -->
                    <label class="search_items">
                        <input class="search_input" type="text" name="" value="" placeholder="搜索"/>
                    </label>
                    <!-- 搜索 结束 -->
                </div>
                <!-- 分组&搜索 结束 -->
                <!-- 时间分类 开始 -->
                <div class="times_items">时间筛选：
                    <select class="times_select" data-placeholder="请选择" style="width:350px;" tabindex="1">
                        <option value=""></option>
                        <option value="0">今日实时</option>
                        <option value="1">最近7天</option>
                        <option value="2">最近30天</option>
                        <option value="3">自然天</option>
                        <option value="4">自然月</option>
                        <option value="5">自定义</option>
                    </select>
                </div>
                <!-- 时间分类 结束 -->
                <!-- 不选择时间控件 开始 -->
                <div class="notime_control">
                    2016-11-14 <a class="blue_38f" href="javascript:location.reload();">刷新</a>
                </div>
                <!-- 不选择时间控件 结束 -->
                <!-- 选择时间控件 开始 -->
                <div class="time_control">
                    <!-- 自然天开始 -->
                    <div id="layer_date1" class="layer_date time_wrap laydate-icon"></div>
                    <!-- 开始时间 结束 -->
                    <!-- 自然月 开始 -->
                    <div id="layer_mouth1" class="layer_mouth time_wrap laydate-icon"></div>
                    <!-- 自然月 结束 -->
                    <!-- 自定义 开始 -->
                    <div class="layer_idefine time_wrap">
                        <div id="start_time1" class="time_wrap laydate-icon"></div>&nbsp;至&nbsp;
                        <div id="end_time1" class="time_wrap laydate-icon"></div>
                    </div>
                    <!-- 自定义 结束 -->
                </div>
                <!-- 选择时间控件  结束 -->
            </div>
        </div>
        <!-- 区域标题 结束 -->
        <!-- 商品概况 开始 -->
        <table class="shop_items table">
            <tr>
                <td>
                    <div class="step_name">商品<br/>分布</div>
                </td>
                <td>
                    <p class="items_title">在架商品数</p>
                    <span class="items_num">0</span>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div class="step_name">商品<br/>访问</div>
                </td>
                <td>
                    <p class="items_title">被访问商品数</p>
                    <span class="items_num">0</span>
                </td>
                <td>
                    <p class="items_title">商品浏览人数（商品UV）</p>
                    <span class="items_num">0</span>
                </td>
                <td>
                    <p class="items_title">商品浏览次数（商品PV）</p>
                    <span class="items_num">0</span>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="step_name">商品<br/>转化</div>
                </td>
                <td>
                    <p class="items_title">付款商品数</p>
                    <span class="items_num">0</span>
                </td>
                <td>
                    <p class="items_title">商品详情页转化率</p>
                    <span class="items_num">0.00%</span>
                </td>
                <td></td>
            </tr>
        </table>
        <!-- 商品概况 结束 -->
        <!-- 区域标题 开始 -->
        <div class="common_top mgb15">
            <span class="common_line"></span>
            <p class="common_title">商品效果</p>
            <div class="common_link"></div>
            <div class="common_right">
                <!-- 分组&搜索 开始 -->
                <div class="category_search">
                    <!-- 分组 开始 -->
                    <div class="grouping_items">
                        <select class="grouping_select" data-placeholder="请选择分组" style="width:350px;" tabindex="1">
                            <option value=""></option>
                            <option value="全部分组">全部分组</option>
                            <option value="列表中隐藏">列表中隐藏</option>
                            <option value="最近30天">最近30天</option>
                            <option value="自然天">自然天</option>
                            <option value="自然月">自然月</option>
                            <option value="自定义">自定义</option>
                        </select>
                    </div>
                    <!-- 分组 结束 -->
                    <!-- 搜索 开始 -->
                    <label class="search_items">
                        <input class="search_input" type="text" name="" value="" placeholder="搜索"/>
                    </label>
                    <!-- 搜索 结束 -->
                </div>
                <!-- 分组&搜索 结束 -->
                <!-- 时间分类 开始 -->
                <div class="times_items">时间筛选：
                    <select class="times_select" data-placeholder="请选择" style="width:350px;" tabindex="1">
                        <option value=""></option>
                        <option value="0">今日实时</option>
                        <option value="1">最近7天</option>
                        <option value="2">最近30天</option>
                        <option value="3">自然天</option>
                        <option value="4">自然月</option>
                        <option value="5">自定义</option>
                    </select>
                </div>
                <!-- 时间分类 结束 -->
                <!-- 不选择时间控件 开始 -->
                <div class="notime_control">
                    2016-11-14 <a class="blue_38f" href="javascript:location.reload();">刷新</a>
                </div>
                <!-- 不选择时间控件 结束 -->
                <!-- 选择时间控件 开始 -->
                <div class="time_control">
                    <!-- 自然天开始 -->
                    <div id="layer_date2" class="layer_date time_wrap laydate-icon"></div>
                    <!-- 开始时间 结束 -->
                    <!-- 自然月 开始 -->
                    <div id="layer_mouth2" class="layer_mouth time_wrap laydate-icon"></div>
                    <!-- 自然月 结束 -->
                    <!-- 自定义 开始 -->
                    <div class="layer_idefine time_wrap">
                        <div id="start_time2" class="time_wrap laydate-icon"></div>&nbsp;至&nbsp;
                        <div id="end_time2" class="time_wrap laydate-icon"></div>
                    </div>
                    <!-- 自定义 结束 -->
                </div>
                <!-- 选择时间控件  结束 -->
            </div>
        </div>
        <!-- 区域标题 结束 -->
        <!-- 商品效果 开始 -->
        <table class="effect_items table">
            <tr class="active">
                <td>商品信息</td>
                <td class="blue_38f">曝光次数</td>
                <td class="blue_38f">曝光人数</td>
                <td class="blue_38f">浏览人数↓</td>
                <td class="blue_38f">浏览次数</td>
                <td class="blue_38f">付款人数</td>
                <td>单品转化率</td>
                <td class="blue_38f">付款商品件数</td>
            </tr>
            <tr><td colspan="8">暂无数据</td></tr>
        </table>
        <!-- 商品效果 结束 -->
    </div>
@endsection
@section('page_js')
    <!-- 下拉框美化插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/shop_i2xobajm.js"></script>
@endsection