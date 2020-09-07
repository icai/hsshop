@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_up9ugzhx.css">

@endsection

@section('slidebar')
    @include('merchants.member.slidebar')
@endsection

@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="/merchants/member/score">积分规则</a>
                </li>
                <li>
                    <a href="#&status=2">新建积分规则</a>
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
    <div class="content_list">
        <form action="" role="form" id="creditForm">
            <ul>
                <li class="form-group">
                    <div class="ft border_right ">
                        <span>奖励积分：</span>
                        <span class="must">*</span>
                    </div>
                    <div class="ft col-sm-6">
                        <input type="text" class="form-control" name="credit_number">
                    </div>
                </li>
                <li class="">
                    <div class="border_right ft">
                        <span>奖励条件：</span>
                    </div>
                    <div class="ft">
                        <div><input type="radio" name="radio">关注我的微信</div>
                        <div class="notice"><input type="checkbox" name="">给粉丝发送获得了积分的通知</div>
                    </div>
                </li>
                <li>
                    <div class="ft">
                        <span></span>
                    </div>
                    <div class="ft">
                        <div class="form-group">
                            <input type="radio" name="radio">
                            每成功交易
                            <input type="text" class="form-control input_color" name="trance_number">笔
                        </div>
                        <div class="notice"><input type="checkbox" name="">给粉丝发送获得了积分的通知</div>
                    </div>
                </li>
                <li class="">
                    <div class="ft">
                        <span></span>
                    </div>
                    <div class="ft">
                        <div class="form-group">
                            <input type="radio" name="radio">
                            每购买金额
                            <input type="text" class="form-control input_color" name="buy_number">笔
                        </div>
                        <div class="notice"><input type="checkbox" name="">给粉丝发送获得了积分的通知</div>
                    </div>
                </li>
                <li class="">
                    <div class="ft">
                        <span></span>
                    </div>
                    <div class="ft">
                        ( 通知发送时段 : 8:00 ~ 22:00 )
                    </div>
                </li>
            </ul>
            <div class="btn_grounp">
                <button class="btn btn-primary">保存</button>
                <a class="btn btn-default">返回</a>
            </div>
        </form>
    </div>
</div>
<!-- 底部logo 开始 -->
<div id="app-footer" class="footer">
    <a href="javascript:void(0);" class="logo" target="_blank">HUISOU</a>
</div>
<!-- 底部logo 结束 -->
@endsection

@section('page_js')
    @parent
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 图表插件 -->
    <script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>
    <!-- 表单验证 -->
    <script src="{{config('app.source_url')}}static/js/bootstrapValidator.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member_lmtq8qvq.js"></script>
@endsection