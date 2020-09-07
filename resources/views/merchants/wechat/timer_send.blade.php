@extends('merchants.default._layouts')
@section('head_css')
    <!-- mybase  -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_ch1zqjrw.css">

@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="#&status=1">营销中心</a>
            </li>
            <li>
                <a href="#&status=2">微信</a>
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
    <!-- 中间 开始 -->
     <div class="content" style="display: -webkit-box;">
                <!--主体左侧列表开始-->
                   @include('merchants.wechat.slidebar')
                <!--主体左侧列表结束-->
                <!--主体右侧内容开始-->
                <div class="right_container">
                    <!-- 导航模块 开始 -->
                    <div class="nav_module clearfix">
                        <!-- 左侧 开始 -->
                        <div class="pull-left">
                        <!-- （tab试导航可以单独领出来用） -->
                            <!-- 导航 开始 -->
                            <ul class="tab_nav">
                                <li class="hover">
                                    <a href="javascript:void(0);">所有定时发送</a>
                                </li>
                            </ul>
                            <!-- 导航 结束 -->
                        </div>
                        <!-- 左侧 结算 -->
                    </div>
                    <div class="no_result">
                        还没有相关数据
                    </div>
                    <div class="data_list">
                        <table class="table table1">
                            <thead>
                                <tr>
                                    <th class="w20">快捷短语内容</th>
                                    <th class="w10">创建时间</th>
                                    <th class="w10">发送时间</th>
                                    <th class="w10 text_right">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 图文开始 -->
                                <tr>
                                    <td class="ctt">
                                        <div class="item">
                                            <div class="img_text">
                                                <span class="green">图文</span>
                                                <a class="co_blue" href="javascript:void(0);">123456</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2017-01-03 15:11:14</td>
                                    <td>2017-01-03 15:11:14</td>
                                    <td class="text_right">
                                        <div class="operate">
                                            <a class="operate_edit co_38f" href="javascript:void(0) data-toggle="modal1" data-target="#myModal1"">编辑</a><span>-</span><a class="pop co_38f" data-toggle="del_popover" href="javascript:void(0)">取消发送</a>
                                        </div>
                                    </td>
                                </tr>
                                <!-- 图片开始 -->
                                <tr>
                                    <td class="ctt">
                                        <div class="item">
                                            <div class="img_text">
                                                <span class="green">图片</span>
                                                <img class="no" src="">
                                            </div>
                                        </div>
                                    </td>
                                    <td>2017-01-03 15:11:14</td>
                                    <td>2017-01-03 15:11:14</td>
                                    <td class="text_right">
                                        <div class="operate">
                                            <a class="operate_edit co_38f" href="javascript:void(0) data-toggle="modal1" data-target="#myModal1"">编辑</a><span>-</span><a class="pop co_38f" data-toggle="del_popover" href="javascript:void(0)">取消发送</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ctt">
                                        <div class="item">
                                            <div class="img_text">
                                                <p class="text">123456789</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2017-01-03 15:11:14</td>
                                    <td>2017-01-03 15:11:14</td>
                                    <td class="text_right">
                                        <div class="operate">
                                            <a class="operate_edit co_38f" href="javascript:void(0) data-toggle="modal1" data-target="#myModal1"">编辑</a><span>-</span><a class="pop co_38f" data-toggle="del_popover" href="javascript:void(0)">取消发送</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_footer">
                            <span>共 1 条，每页 20 条</span>
                        </div>
                    </div>
                </div>
                <!--主体右侧内容结束-->
            </div>
    <!-- 中间 结束 -->
@endsection
<!-- 删除弹框 -->
<div class="popover del_popover left" role="tooltip" style="z-index: 999">
    <div class="arrow"></div>
    <div class="popover-content">
        <span>你确定要删除吗？</span>
        <button class="btn btn-primary sure_btn">确定</button>
        <button class="btn btn-default cancel_btn">取消</button>
    </div>
</div>
@section('page_js')
    @parent
    <!-- 微信模块公共样式 -->
    <script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/wechat_ch1zqjrw.js"></script>
    <script type="text/javascript">
        //主体左侧列表高度控制
        $('.left_nav').height($('.content').height());
    </script>
    
@endsection