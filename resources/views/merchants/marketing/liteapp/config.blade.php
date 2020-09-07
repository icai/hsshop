@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_qwhj4x9w.css" />
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
                    <a href="javascript:void(0)">微信小程序</a>
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
        <!-- 导航 开始 -->
        <ul class="tab_nav">
            <li role="presentation">
                <a href="/merchants/marketing/litePage">小程序微页面</a>
            </li>
            <li>
                <a href="/merchants/marketing/footerBar">底部导航</a>
            </li>
            <li>
                <a href="/merchants/marketing/xcx/topnav">首页分类导航</a>
            </li>
            <li class="hover">
                <a href="/merchants/marketing/liteapp">小程序设置</a>
            </li>       
            <li>
                <a href="/merchants/marketing/liteStatistics">数据统计</a>
            </li>     
        </ul>
        <div class="lite_con">
            <div class="lite_flx">
                <p class="title_p">小程序:</p>
                <p class="title_res"></p>
                <a class="title_a"  data-toggle="modal" data-target="#myModal">解除授权</a>
                <!--模态框-->
                <div class="modal fade" tabindex="-1" role="dialog" id="myModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">提示</h4>
                            </div>
                            <div class="modal-body">
                                <p class="red">解除绑定小程序，会造成线上小程序异常，请谨慎操作！</p>
                                <label class="mto20 mt_lab">
                                    <input type="checkbox" name="" class="mt_che" value="" />
                                    已知晓解除绑定的风险，确认解绑
                                </label>
                            </div>
                            <div class="modal-footer">
                                <input type="button" name="" class="btn btn-jiec form_remov" value="解除绑定" />
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--模态框-->
            </div>
            <div class="lite_flx">
                <p class="title_p">微信支付:</p>
                <p>目前小程序只支持自有支付，你可以在「小程序后台-微信支付」页面下申请开通并完成相关配置。</p>
            </div>
            <div class="ml110">
                <p>小程序的主体必须为企业，才可以申请微信支付；如果你的小程序不是企业主体，请另注册一个，重新授权给会搜云。</p>
                <p>完成设置后，请在本页填写你的商户号和商户密钥。</p>              
            </div>
            </div>
            <form id="form">
                <!--<div class="lite_flx">
                    <p class="title_p">收款方名称:</p>
                    <input type="" name="merchant_name" class="form-control" value="" />
                </div>-->
                <div class="lite_flx">
                    <p class="title_p">AppID(小程序ID):</p>
                    <input type="" name="app_id" class="form-control app_id" value="" readonly="readonly"/>
                </div>
                <div class="lite_flx">
                    <p class="title_p">AppSecret(小程序密钥):</p>
                    <input type="" name="app_secret" class="form-control" value="" />
                </div>
                <div class="lite_flx">
                    <p class="title_p">商户号:</p>
                    <input type="" name="merchant_no" class="form-control" value="" />
                </div>
                <p class="fex_p">微信支付审核通过后，微信会将商户号发送到接收邮箱</p>
                <div class="lite_flx">
                    <p class="title_p">API密钥:</p>
                    <input type="" name="app_pay_secret" class="form-control" value="" />
                </div>
                <p class="fex_p">请登录微信商户平台，进入「账户中心-API安全」页面，设置密钥</p>
                <div class="lite_flx">
                    <label class="ml110 lab_che">
                        <input type="checkbox" name="" class="z_check" value="" />
                        已确认商户号和商户密钥配置正确(否则将导致微信支付异常，小程序无法通过审核)
                    </label>
                </div>
                <input class="ml110 z_mon form_but form_close" type="button" value="提交审核"/>
            </form>         
        </div>
        <!--模态框开始-->
        <div class="modal" id="myModal2" style="background: rgba(0,0,0,.3); display: block;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="myModalLabel">提示</h4>
                    </div>
                    <div class="modal-body">
                         请在新窗口中完成微信小程序授权
                    </div>
                    <div class="modal-footer clearfix">
                        <a type="button" href="#" class="btn btn-success btn-close">已成功设置</a >
                        <a type="button" href="{{ URL('/merchants/marketing/liteapp') }}" class="btn btn-default" >授权失败，重试</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
<script src="{{ config('app.source_url') }}mctsource/js/liteapp_qwhj4x9w.js" type="text/javascript" charset="utf-8"></script>
@endsection