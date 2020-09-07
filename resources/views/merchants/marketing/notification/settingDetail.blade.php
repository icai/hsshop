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
            @if(!$conf)
                <div data-reactroot="" class="ui-message-warning">
                    您还未绑定微信公众号，请您绑定后，进行消息提醒设置<a href="/merchants/wechat/wxsettled">立即绑定</a>
                </div>
            @else
                <div data-reactroot="" class="ui-message-warning">
                    您已绑定微信公众号，请确保微信公众号已申请开通模板消息。<a href="/home/index/detail/628/news">如何开通?</a>
                </div>
            @endif
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
                {{--<a class="nav_module_blank" href="/home/index/detail?id=342" target="_blank"><span class="help-icon">?</span>查看【消息提醒】使用教程</a>--}}
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
                                <div class="controls" style="padding-top:5px;">
                                    @if($notification['notification_type'] == 1)
                                        买家发起退款申请后
                                    @elseif($notification['notification_type'] == 2)
                                        买家付款成功后(发送给商家或者管理员)
                                    @elseif($notification['notification_type'] == 3)
                                        买家发起退货申请后
                                    @elseif($notification['notification_type'] == 4)
                                        距退款申请时间72小时发送
                                    @elseif($notification['notification_type'] == 5)
                                        卖家点击发货以后
                                    @elseif($notification['notification_type'] == 6)
                                        买家付款后立即发送
                                    @else
                                        {{--<span>--}}
                                        {{--买家下单--}}
                                        {{--<select class="trade-urge-select" name="trade_urge_t">--}}
                                        {{--<option value="5">5分钟</option>--}}
                                        {{--<option value="10">10分钟</option>--}}
                                        {{--<option value="15">15分钟</option>--}}
                                        {{--<option value="20">20分钟</option>--}}
                                        {{--<option value="30">30分钟</option>--}}
                                        {{--<option value="60">60分钟</option>--}}
                                        {{--<option value="120">2小时</option>--}}
                                        {{--<option value="180">3小时</option>--}}
                                        {{--<option value="240">4小时</option>--}}
                                        {{--</select>--}}
                                        {{--未付款--}}
                                        {{--</span>--}}
                                    @endif
                                </div>
                            </div>
                            {{--<div class="control-group clearfix">--}}
                            {{--<label class="control-label">--}}
                            {{--发送时间段：--}}
                            {{--</label>--}}
                            {{--<div class="controls">--}}
                            {{--每日--}}
                            {{--<select name="bg_t" class="t-select">--}}
                            {{--<option value="0">00:00</option>--}}
                            {{--</select>--}}
                            {{--到--}}
                            {{--<select name="ed_t">--}}
                            {{--<option value="1">01:00</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="control-group clearfix">
                                <label class="control-label">
                                    发送方式：
                                </label>
                                <div class="controls">
                                    <div class="divide-box">
                                        <div class="divide-title">
                                        <span>
                                            <label class="checkbox iblock" style="line-height: 17px;">
                                                <input type="checkbox"  name="wx" class="wechat_news" value="0" @if(in_array($notification['subscriber_id_type'],[0,2])) checked @endif>
                                                微信粉丝消息
                                                (需要微信认证服务号)
                                            </label>
                                            @if(in_array($notification['notification_type'],[1,2,3,4]))
                                                <label class="checkbox iblock" style="line-height: 17px;">
                                                <input class="admin_news" @if(in_array($notification['subscriber_id_type'],[1,2]) || $notification['notification_type'] == 2 ) checked @endif type="checkbox" name="ht" value="1">
                                                后台消息提醒 <span style="color: red;font-size: 12px;">[必填]</span>
                                            </label>
                                            @endif
                                        </span>
                                        </div>
                                    </div>
                                    <div class="divide-box">
                                        <div class="divide-title">
                                            <label class="checkbox iblock" style="line-height: 17px;">
                                                <input type="checkbox" value="0" class="wechat_news" name="wx" @if(in_array($notification['subscriber_id_type'],[0,2])) checked @endif>
                                                微信粉丝消息
                                            </label>
                                        </div>
                                        <div class="divide-content">
                                            <div class="wx-template">
                                                <div class="wx-title">
                                                    @if($notification['notification_type'] == 1)
                                                        退款消息提醒
                                                    @elseif($notification['notification_type'] == 2)
                                                        新订单提醒
                                                    @elseif($notification['notification_type'] == 3)
                                                        买家已退货提醒
                                                    @elseif($notification['notification_type'] == 4)
                                                        退款临近超时提醒
                                                    @elseif($notification['notification_type'] == 5)
                                                        订单发货提醒
                                                    @elseif($notification['notification_type'] == 6)
                                                        订单支付成功
                                                    @endif
                                                </div>
                                                <div class="wx-date">
                                                    8月8日
                                                </div>
                                                <div class="wx-content">
                                                    @if($notification['notification_type'] == 1)
                                                        您的买家发起退款申请，请在n天内处理；请尽快登录&lt;店铺名称&gt;商家后台进行操作哦~
                                                    @elseif($notification['notification_type'] == 2)
                                                        您有新的订单，请及时登录&lt;店铺名称&gt;商铺后台进行操作发货哦~
                                                    @elseif($notification['notification_type'] == 3)
                                                        您有一条退货单，请尽快登录&lt;店铺名称&gt;商家后台进行处理哦~
                                                    @elseif($notification['notification_type'] == 4)
                                                        由于您长时间未处理订单编号(D166666666)系统将于n小时后退款给买家；请尽快登录&lt;店铺名称&gt;商家后台处理哦~
                                                    @elseif($notification['notification_type'] == 5)
                                                        小主、您的御品已快马加鞭的在路上向您飞奔啦*·*
                                                    @elseif($notification['notification_type'] == 6)
                                                        小主，我们已经收到您的货款，会尽快为您打包，请耐心等候~.~
                                                    @endif
                                                    <br>
                                                    <br>
                                                    @if($notification['notification_type'] <> 5)
                                                        订单金额：￥888.88
                                                        <br>
                                                        商品详情：&lt;商品名称&gt;
                                                        <br>
                                                        @if($notification['notification_type'] <> 3)
                                                            收货信息：&lt;姓名 地址&gt;
                                                            <br>
                                                        @endif
                                                        订单编号：E8888888888888888
                                                    @else
                                                        订单编号：E8888888888888888
                                                        <br>
                                                        快递公司：&lt;物流公司&gt;
                                                        <br>
                                                        物流单号：66666666666
                                                        <br>
                                                    @endif
                                                    <br>
                                                    <br>
                                                    @if($notification['notification_type'] == 1 or $notification['notification_type'] == 6)
                                                        感谢您光临&lt;店铺名称&gt;，点击查看订单详情
                                                    @elseif($notification['notification_type'] == 2)
                                                        查看详情
                                                    @elseif($notification['notification_type'] == 3)
                                                        查看退货详情
                                                    @elseif($notification['notification_type'] == 5)
                                                        点击查看物流信息
                                                    @endif
                                                </div>
                                                @if(in_array($notification['notification_type'],[2,3,4,5,6]))
                                                    <div class="wx-link">
                                                        详情
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if(in_array($notification['notification_type'],[1,2,3,4]))
                                        <div class="divide-box">
                                            <div class="divide-title">
                                                <label class="checkbox iblock" style="line-height: 17px;">
                                                    <input class="admin_news" value="1" type="checkbox" name="ht" @if(in_array($notification['subscriber_id_type'], [1,2])) checked @endif>
                                                    后台消息提醒
                                                </label>
                                            </div>
                                            <div class="divide-content">
                                                <div class="msg-content-one">
                                                    @if($notification['notification_type'] == 1)
                                                        您的买家发起退款，订单编号&lt;订单编号&gt;，请您在&lt;天数&gt;天内处理，过时系统将自动退款。请尽快登录&lt;店铺名称&gt;商家后台操作。
                                                    @elseif($notification['notification_type'] == 2)
                                                        您有新的订单,请登录商家后台及时处理
                                                    @elseif($notification['notification_type'] == 3)
                                                        您的买家已退货，订单编号&lt;订单编号&gt;，请您在七天内核实处理，逾期未处理，系统将操作退款买家。请尽快登录&lt;店铺名称&gt;商家后台操作。
                                                    @elseif($notification['notification_type'] == 4)
                                                        订单编号&lt;订单编号&gt;，因您长时间未处理此笔订单，系统将于72小时后退款给买家。请尽快登录&lt;店铺名称&gt;商家后台操作。
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
    <script>
        var id = {{$notification['id']}};
        var notification_type = {{$notification['notification_type']}}
    </script>
    <script src="{{ config('app.source_url') }}mctsource/js/settingDetailView.js"></script>
@endsection