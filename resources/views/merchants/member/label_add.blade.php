@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_hjxw7me7.css">
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
                    <a href="/merchants/member/label">标签管理</a>
                </li>
                <li>
                    <a href="#">新建标签</a>
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
                <form action="{{URL('/merchants/member/label')}}" method="post" role="form" id="signForm">
                    <ul>
                        <li class="form-group">
                            <div class="border_right">
                                <span>标签名称</span>
                                <span class="must">*</span>
                            </div>
                            <div>
                                <input type="text" class="form-control" name="rule_name">
                            </div>
                        </li>
                        <li class="border_top form-group" >
                            <div class="border_right">
                                <span>自动打标签条件</span>
                            </div>
                            <div>
                                累计成交金额
                                <input type="text" class="form-control" name="trade_limit" value="0" >元
                            </div>
                        </li>
                        <li class="border_top form-group">
                            <div class="border_right">
                                <span>或</span>
                            </div>
                            <div>
                                累计购买金额
                                <input type="text" class="form-control" name="amount_limit" value="0" >元
                            </div>
                        </li>
                        <li class="border_top form-group">
                            <div class="border_right">
                                <span>或</span>
                            </div>
                            <div>
                                累计积分达到
                                <input type="text" class="form-control" name="points_limit" value="0">
                            </div>
                        </li>
                    </ul>
                    <div class="btn_grounp">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <button class="btn btn-primary">保存</button>
                        <a class="btn btn-default">返回</a>
                    </div>
                </form>
            </div>
        </div>

        
@endsection

@section('other')
    <!-- 删除弹窗 -->
    <div class="popover del_popover left" role="tooltip">
        <div class="arrow"></div>
        <div class="popover-content">
            <span>你确定要删除吗？</span>
            <button class="btn btn-primary sure_btn">确定</button>
            <button class="btn btn-default cancel_btn">取消</button>
        </div>
    </div>
    <!--弹层-->
    <div class="tip"></div>
@endsection

@section('page_js')
    @parent
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 图表插件 -->
    <script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>
    <!-- layer -->
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
    <!-- 表单验证 -->
    <script src="{{config('app.source_url')}}static/js/bootstrapValidator.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member_jw2kz538.js"></script>
@endsection