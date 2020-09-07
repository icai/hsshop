@extends('merchants.default._layouts') @section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/notificationList.css" /> @endsection @section('slidebar') @include('merchants.marketing.slidebar') @endsection @section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="javascript:;">营销中心</a>
            </li>
            <li>
                <a href="javascript:;">消息提醒</a>
            </li>
        </ul>
    </div>
</div>
@endsection @section('content')
<div class="content">
    <!-- 消息提醒 -->
    <div class="widget-app-board">
        <div class="widget-app-board-info">
            <h3>消息提醒</h3>
            <p> 消息提醒功能可以通过微信公众号，给买家或商家推送交易和物流相关的提醒消息，包括订单催付、发货、签收、退款等，以提升买家的购物体验，获得更高的订单转化率和复购率。</p>
        </div>
    </div>
    <div>
        <div data-reactroot="" class="ui-message-warning">
            您还未绑定微信公众号，请您绑定后，进行消息提醒设置<a href="#recharge">立即绑定</a>
        </div>
    </div>
    <!-- 导航模块 开始 -->
    <div class="nav_module clearfix pr">
        <div class="pull-left">
            <!-- 导航 开始 -->
            <ul class="tab_nav">
                <li class="hover">
                    <a href="{{url('/merchants/marketing/seckills/')}}">消息提醒</a>
                </li>
            </ul>
        </div>
        <div class="pull-right common-helps-entry">
            <a class="nav_module_blank" href="/home/index/detail?id=342" target="_blank"><span class="help-icon">?</span>查看【消息提醒】使用教程</a>
        </div>
    </div>
    <div class="app__content">
        <div>
            <div>
                <div style="margin-top: 20px;">
                    <div class="ui-block-head">
                        <h3 class="block-title">交易物流信息提醒</h3>
                    </div>
                    <div class="zent-form form-horizontal push-setting-form">
                        <hr class="setting-hr">
                        <div class="control-group clearfix">
                            <label class="control-label">发送时间点：</label>
                            <div class="controls">
                                <span>
                                    买家下单
                                    <select class="trade-urge-select" name="trade_urge_t">
                                        <option value="5">5分钟</option>
                                        <option value="10">10分钟</option>
                                        <option value="15">15分钟</option>
                                        <option value="20">20分钟</option>
                                        <option value="30">30分钟</option>
                                        <option value="60">60分钟</option>
                                        <option value="120">2小时</option>
                                        <option value="180">3小时</option>
                                        <option value="240">4小时</option>
                                    </select>
                                    未付款
                                </span>
                            </div>
                        </div>
                        <div class="control-group clearfix">
                            <label class="control-label">
                                发送时间段：
                            </label>
                            <div class="controls">
                                每日
                                <select name="bg_t" class="t-select">
                                    <option value="0">00:00</option>
                                </select>
                                到
                                <select name="ed_t">
                                    <option value="1">01:00</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group clearfix">
                            <label class="control-label">
                                发送方式：
                            </label>
                            <div class="controls">
                                <div class="divide-box">
                                    <div class="divide-title">
                                        <span>
                                            <label class="checkbox iblock" style="line-height: 17px;">
                                                <input type="checkbox" name="mb">
                                                手机短信
                                            </label>
                                            <label class="checkbox iblock" style="line-height: 17px;">
                                                <input type="checkbox"  name="wx" disabled="">
                                                微信粉丝消息
                                                （需要认证公众号）
                                            </label>
                                        </span>
                                    </div>
                                    <div class="divide-content">
                                        <div class="msg-content-one">
                                            嗨！你要购买的“&lt;商品名称&gt;”还没付款哦。查看详情 &lt;订单链接&gt;
                                        </div>
                                    </div>
                                </div>
                                <div class="divide-box">
                                    <div class="divide-title">
                                        <label class="checkbox iblock" style="line-height: 17px;">
                                            <input type="checkbox" name="wxtp">
                                            微信模版消息
                                        </label> 
                                    </div>
                                    <div class="divide-content">
                                        <div class="wx-template">
                                            <div class="wx-title">
                                                订单未付款通知
                                            </div>
                                            <div class="wx-date">
                                                8月8日
                                            </div>
                                            <div class="wx-content">
                                                亲，请尽快处理未付款的订单，超时会自动关闭哟～
                                                <br>
                                                <br>
                                                订单金额：￥888.88
                                                <br>
                                                商品详情：&lt;商品名称&gt;
                                                <br>
                                                收货信息：&lt;姓名 地址&gt;
                                                <br>
                                                订单编号：E8888888888888888
                                                <br>
                                                <br>
                                                感谢你对&lt;店铺名称&gt;的支持！点击查看详情
                                            </div>
                                            <div class="wx-link">
                                                详情
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions error-tips">
                            <div class="alert messagepush-alert red hide">
                                微信模版丢失，请再次点击保存以重新启用微信模版消息。
                            </div>
                            <div class="alert hide">
                                请先在
                                <a href="https://mp.weixin.qq.com">
                                    微信公众号后台
                                </a>
                                完成以下设置后，再点击保存：
                                <br>
                                1. 将模版消息功能授权給有赞
                                <br>
                                2. 在微信后台开启模版消息功能
                                <br>
                                3. 公众号属于电商／金融行业
                                <br>
                                <a href="//help.youzan.com/qa#/menu/2256/detail/1007?_k=j69snp" target="_blank">
                                    设置教程&gt;&gt;
                                </a>
                            </div>
                        </div>
                        <div class="control-group clearfix" style="margin-top:20px;">
                            <label class="control-label">
                            </label>
                            <div class="controls">
                                <button class="btn btn-primary btn-sm">
                                    保存
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @section('page_js')
<!-- 图表插件 -->
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/notificationList.js"></script>

@endsection