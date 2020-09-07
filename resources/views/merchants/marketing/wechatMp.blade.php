@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpj42f2.css" />
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0);">微信公众号</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>   
        <!-- 三级导航 结束 -->
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
    <!-- 微信公众号头部 开始 -->
    <div class="weixin_header display_box mgb30">
        <!-- 公共号设置 开始 -->
        <div class="weixin_set box_flex1">
            <p class="green f16 mgb15">绑定微信公众号，把店铺和微信打通</p> 
            <p class="f12 mgb30">绑定后即可在这里管理您的公众号，会搜云提供比微信官方后台更强大的功能！</p>
            <a class="opt_btn btn btn-success" href="https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx7cd6227fafe53336&amp;pre_auth_code=preauthcode%40%40%40PxbfXoQtIn_GLyhYgljW77LzZIRBl7uq4wktrp8pjspKcrt7VMZpavBiL41m65yC&amp;redirect_uri=" target="_blank">我有微信公众号，立即设置</a> 
        </div>
        <!-- 公共号设置 结束 -->
        <!-- 提示 开始 -->
        <ul class="f12 weixin_tip">
            <p>温馨提示：</p>
            <li>一个微信公众号只能和一个店铺绑定</li>
            <li>认证服务号绑定之后，如果要解绑可以联系会搜云客服</li>
            <li class="red">为保证所有功能正常，授权时请保持默认选择，把权限统一授权给会搜云</li>
        </ul>
        <!-- 提示 结束 -->
    </div>
    <!-- 微信公众号头部 结束 -->
    <!-- 表格头部 开始 -->
    <p class="mgb15">微信给不同类型公众号提供不同的接口，会搜云能提供的功能也不相同：</p>
    <!-- 表格头部 结束 -->
    <!-- 表格 开店 -->
    <table class="table table-bordered table-hover">
        <tr class="active">
            <td></td>
            <td>未认证订阅号</td>
            <td>认证订阅号</td>
            <td>未认证服务号</td>
            <td>认证服务号</td>
        </tr>
        <tr>
            <td class="active">消息自动回复</td>
            <td class="green">√</td>
            <td class="green">√</td>
            <td class="green">√</td>
            <td class="green">√</td> 
        </tr>
        <tr>
            <td class="active">微信自定义菜单</td>
            <td></td>
            <td class="green">√</td>
            <td class="green">√</td>
            <td class="green">√</td> 
        </tr>
        <tr>
            <td class="active">群发/定时群发</td>
            <td></td>
            <td class="green">√</td>
            <td></td>
            <td class="green">√</td> 
        </tr>
        <tr>
            <td class="active">高级客户管理</td>
            <td></td>
            <td>部分功能</td>
            <td></td>
            <td></td> 
        </tr>
        <tr>
            <td class="active">可申请微信支付</td>
            <td></td>
            <td></td>
            <td></td>
            <td class="green">√</td> 
        </tr>       
    </table>
    <!-- 表格 结束 -->
    <!-- 更改了解 开始 -->
    <div class="more_items">
        <a class="more_btn btn btn-default" href="javascript:void(0);" target="_blank">进一步了解它们的区别</a>
    </div>
    <!-- 更改了解 结束 -->
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection