@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_h6en3vyn.css" />
<!--特殊按钮样式的css文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a style="color: #666;" href="{{ URL('/merchants/currency/payment') }}">支付/交易</a>
            </li>
            <a style="color: #0099fc;" href="/home/index/detail/606/news" target="_Blank">如何配置微信支付入口</a>
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
    <form class="form-horizontal form" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="payment" value="1" />
        <input type="hidden" name="id" value="{{ $weChatPayInfo['id'] or '' }}" />
        <div class="div weiXin">
            <div class="title weiXin_title">
                <div class="title_left">
                    <b>微信支付</b>
                    <div class="rowImg"></div>
                </div>
                <div class="title_right">
                    <!-- 按钮 开始 -->
                    <div class="switch_items">
                        <input type="checkbox" name="status" value="1" @if (isset($weChatPayInfo['status']) && $weChatPayInfo['status'] == 1) checked @endif />
                        <label></label>
                    </div>
                    <!-- 按钮 结束 -->
                </div>
            </div>
            <div class="contentDiv weiXin_content">
                <div class="form-group">
                    <label class="col-sm-4 control-label">收款方名称：</label>
                    <div class="col-sm-5">
                        <input name="config[payee]" class="form-control" value="{{ $weChatPayInfo['payee'] or $weixinInfo['shop_name'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">开发者ID(AppID)：</label>
                    <div class="col-sm-5">
                        <input  class="form-control" value="{{ $weChatPayInfo['app_id'] or '' }}"  disabled />
                        <input name="config[app_id]" class="form-control" value="{{ $weChatPayInfo['app_id'] or '' }}"  type="hidden" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">开发者密码(AppSecret)：</label>
                    <div class="col-sm-5">
                        <input name="config[app_secret]" class="form-control" value="{{ $weChatPayInfo['app_secret'] or '' }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 control-label" accept="image/*">商户号：</label>
                    <div class="col-sm-5">
                       <input name="config[mch_id]" class="form-control" value="{{ $weChatPayInfo['mch_id'] or '' }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">商户密钥：</label>
                    <div class="col-sm-5">
                       <input name="config[mch_key]" class="form-control" value="{{ $weChatPayInfo['mch_key'] or '' }}" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-10">
                        <button type="submit" class="btn btn-default">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="form-horizontal form" style="display: none;">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="" />
        <div class="div weiXin">
            <div class="title weiXin_title">
                <div class="title_left">
                    <b>小程序支付</b>
                    <div class="rowImg"></div>
                </div>
                <div class="title_right">
                    
                </div>
            </div>
            <div class="contentDiv weiXin_content">
                <div class="form-group">
                    <label class="col-sm-4 control-label">商户名称：</label>
                    <div class="col-sm-5">
                        <input name="merchantName" class="form-control" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">开发者ID(appId)：</label>
                    <div class="col-sm-5">
                        <input  class="form-control" name="appId" value=""/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">开发者密码(appSecret)：</label>
                    <div class="col-sm-5">
                        <input name="appSecret" class="form-control" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 control-label" accept="image/*">商户号：</label>
                    <div class="col-sm-5">
                       <input name="merchantNo" class="form-control" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">API密钥：</label>
                    <div class="col-sm-5">
                       <input name="appPaySecret" class="form-control" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-10">
                        <button type="submit" class="btn btn-default save_miniCode">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
@section('other')
<!--蒙板开始-->
<div class="board hide"></div>
<!--蒙板结束-->
<!--弹出层开始-->
<div class="weixin_Layer hide">
    <div class="layer_title">
        <div id="title_left"><b>微信支付</b></div>
        <div id="title_right"></div>
    </div>
    <div class="layer_content">
        <span id="">请根据您的实际情况，选择一种方式：</span>
        <hr />
        <span id=""><b>情况1：店铺已绑定“认证服务号”，且已向微信申请开通“微信支付权限”</b></span>
        <p>您可以在此配置，使用自己的微信支付。货款直接进入您的微信支付对应的财付通账号。微信将收取每笔0.6%的交易手续费。</p>
        <button type="button" id="weixinDeploy" class="btn btn-primary">立即配置</button>
        <hr />
        <span id=""><b>情况2：无论店铺是否绑定了微信公众号</b></span>
        <p>您的店铺可以通过会搜云代销商品，后由会搜云与您结算货款（需您发起提现申请）</p>
        <p>微信收取0.6%交易手续费，通过会搜云完成代缴。</p>
        <p>提现人工审核周期：当天18点前申请提现，当天审核完成，实际到账时间以银行入账时间为准。</p>
        <button type="button" class="btn btn-success">正在使用</button>
    </div>
</div>
<div class="approveLayer hide">
    店铺尚未绑定“认证服务号”，请查看<a href="##" style="color: #38f;">微信账号设置</a>
</div>
<!--弹出层结束-->
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_h6en3vyn.js"></script>
@endsection