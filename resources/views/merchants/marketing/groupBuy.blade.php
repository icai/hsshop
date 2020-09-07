@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_f0qcs9ri.css" />
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
                    <a href="javascript:void(0)">团购</a>
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
            <!-- 左侧 开始 -->
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li  class="hover">
                        <a href="团购.html">所有团购</a>
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
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【消息推送】使用教程 </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- 列表模块 开始 -->
        <a class="btn btn-success mgb15" href="{{ URL('/merchants/marketing/groupBuy/content') }}">新建团购</a>
        <!-- 列表 开始 -->
        <table class="table table-hover f12">
            <tr class="active">
                <td>团购商品</td>
                <td>团购价</td>
                <td>库存</td>
                <td>团购售出</td>
                <td>开团时间</td>
                <td>结束时间</td>
                <td>返现设置</td>
                <td>操作</td>
            </tr>
            <tr>
                <td>
                    <div class="display_box">
                        <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fq9Xi4vSuS8D804oC_1CD04sb8uA.png?imageView2/2/w/100/h/100/q/75/format/webp" alt="" width="60" height="60">
                        <div class="box_flex1 mgl10 w150 tleft">
                            <a class="blue_38f" href="{{URL('/merchants/marketing/groupBuy/content') }}" target="_blank" class="new-window">
                                实物商品（购买时需填写收货地址，测试商品，不发货，不退款）
                            </a>
                            <p>原价：￥1.00</p></div>
                    </div>
                </td>
                <td>￥2.00</td>
                <td>99999</td>
                <td>
                    <p class="gray">-</p>
                </td>
                <td>2016-11-30 15:57:32</td>
                <td>2016-12-13 15:57:32</td>
                <td> 售出达1件，每件返现￥1.00</td>
                <td>
                    <a class="blue_38f" href="javascript:void(0);">编辑</a>
                    <a class="blue_38f" href="javascript:void(0);">使失效</a>
                    <div class="QRcode_items blue_38f" href="javascript:void(0);">
                        <p>二维码</p>
                        <!-- 二维码 开始 -->
                        <div class="QRcode_module">
                            <!-- 弹框头部 -->
                            <div class="QRcode_header display_box">
                                <p class="box_flex1">活动二维码</p>
                                <i class="close_QRcode glyphicon glyphicon-remove-circle"></i>
                            </div>
                            <!-- 弹框主体 -->
                            <div class="img_wrap">
                                <img src="" />
                            </div>
                            <!-- 弹框底部 -->
                            <p class="mgb10">扫一扫立即参与活动</p>
                            <div class="QRcode_bottom">
                                <a href="javascript:void(0);">下载二维码</a>
                                <a href="/v2/weixin/autoreply/scan">设置带参数二维码</a>
                            </div>
                        </div>
                        <!-- 二维码 结束 -->
                    </div>
                    <a class="blue_38f" href="javascript:void(0);">推广</a>
                    <a class="gray_999" href="javascript:void(0);">已失效</a>
                </td>
            </tr>
        </table>
        <!-- 列表 结束 -->
        <div class="no_result">暂无数据！</div>
        <!-- 列表模块 结束 -->
    </div>

@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection