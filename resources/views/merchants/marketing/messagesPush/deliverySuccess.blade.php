@extends('merchants.default._layouts') @section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/custom.css" />
    <style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide{display:none !important;}ng\:form{display:block;}.ng-animate-start{clip:rect(0,auto,auto,0);-ms-zoom:1.0001;}.ng-animate-active{clip:rect(-1px,auto,auto,0);-ms-zoom:1;}
    </style>
@endsection @section('slidebar') @include('merchants.marketing.slidebar') @endsection @section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <ul class="crumb_nav">
                <li>
                    <a href="/merchants/marketing">营销概况</a>
                </li>
                <li>
                    <a href="/merchants/marketing/messagesPush">消息推送</a>
                </li>
                <li>
                    <a href="javascript:;">发货成功消息通知</a>
                </li>
            </ul>
        </div>
    </div>
@endsection @section('content')
    <div class="content">
        <!-- 导航模块 开始 -->
        <div class="app__content">
            <div style="margin-top: 20px;">
                <form class="form-horizontal push-setting-form">
                    <div class="form-group control-group">
                        <label for="inputEmail3" class="control-label">发送方式：</label>
                        <div class="controls">
                            <div class="preview-field-group">
                                <div class="divide-box">
                                    <div class="divide-title no-describe">
                                        <label class="zent-form__checkbox zent-checkbox-wrap zent-checkbox-checked">
                                        <span class="zent-checkbox">
                                            <input  type="checkbox" @if(in_array(3,$data['config'])) {{ 'checked' }}@endif name="mb" value="3">
                                        </span>
                                            <span>微信公众号模板消息（免费发送）</span>
                                        </label>
                                    </div>
                                    <div class="divide-content">
                                        <p class="setting-title">仅支持认证服务号，发送成功后客户可在公众号中收到下图所示的卡片消息
                                        </p>
                                        <div class="scale-size-wrap">
                                            <div class="position-wrap">
                                                <div class="wechat-template-preview">
                                                    <div class="wechat-template-content">
                                                        <div class="wechat-template-company">{{ request()->session()->get('shop_name') }}</div>
                                                        <div class="wechat-template-title">发货通知</div>
                                                        <div class="wechat-template-date">10月11日</div>
                                                        <div class="wechat-template-html">亲，你的宝贝已在路上啦，正全速向你飞奔～
                                                            <br>订单编号：88888888888888
                                                            <br>快递公司：xx快递
                                                            <br>运单号码：66666666666666
                                                            <br>订单价格：66.66
                                                            <br>收货地址：xxxxxx
                                                            <br>点击查看物流信息
                                                        </div>
                                                        <div class="wechat-template-link">详情</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="divide-box">
                                    <div class="divide-title no-describe">
                                        <label class="zent-form__checkbox zent-checkbox-wrap zent-checkbox-checked">
                                        <span class="zent-checkbox">
                                            <input  type="checkbox" @if(in_array(4,$data['config'])) {{ 'checked' }}@endif name="mb" value="4">
                                        </span>
                                            <span>小程序模板消息（免费发送）</span>
                                        </label>
                                    </div>
                                    <div class="divide-content">
                                        <p class="setting-title">
                                            注：使用小程序订阅消息功能，1，商家需在小程序服务类目中添加<span style="color:#38f">【商家自营 > 服装/鞋/箱包 】</span>；2，每次更新服务类目，都需要重新配置订阅消息模板；
否则会导致订阅消息服务通知无法发出。
                                        </p>
                                        <div class="scale-size-wrap">
                                            <div class="position-wrap">
                                                <div class="weapp-template-wrap just-view">
                                                    <div class="wechat-template-company">服务通知</div>
                                                    <div class="wechat-template-scroll">
                                                        <div class="fake-time">
                                                            <span>昨天 上午12:37</span>
                                                        </div>
                                                        <div class="weapp-template-preview just-view">
                                                            <div class="weapp-template-preview-header">
                                                                <img class="weapp-logo" src="{{ config('app.source_url') }}mctsource/images/logo_icon.png" alt="logo">
                                                                <div class="weapp-nickname">小程序名称</div>
                                                            </div>
                                                            <div class="weapp-template-preview-content">
                                                                <div class="weapp-library-title">订单发货提醒</div>
                                                                {{--<div class="weapp-send-time">5月8日</div>--}}
                                                                <div class="weapp-content">
                                                                    <div>
                                                                        <label>订单号</label>
                                                                        <p>888888888888888</p>
                                                                    </div>
                                                                    <div>
                                                                        <label>发货平台</label>
                                                                        <p>{{ request()->session()->get('shop_name') }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <label>快递公司</label>
                                                                        <p>xxx快递</p>
                                                                    </div>
                                                                    <div>
                                                                        <label>快递单号</label>
                                                                        <p>E8888888888888888</p>
                                                                    </div>
                                                                    <div>
                                                                        <label>地址</label>
                                                                        <p>xxxxxxxxxxxxxxx</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="weapp-template-preview-footer">
                                                                <span>进入小程序查看</span>
                                                                <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="btn_grounp">
                <button class="zent-btn zent-btn-primary js-btn-add">保存</button>
                <button class="zent-btn js-btn-cancel">取消</button>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <!-- 图表插件 -->
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
    <!-- <script src="{{ config('app.source_url') }}mctsource/js/setting_list.js"></script> -->
    <script type="text/javascript">
        $(function(){
            // $('input[name="mb"]').change(function(){
            //     var value = $(this).val();
            //     if($(this).is(':checked')){
            //         sendWay.push(value)
            //     }else{
            //         sendWay.splice(sendWay.indexOf(value),1);
            //     }
            //     console.log(sendWay)
            // })
            //保存提交
            $('.js-btn-add').click(function(){
                var sendWay = [];//发送方式 1:短信 2：微信粉丝 3：公众号模板消息 4：小程序模板消息
                $('input[name="mb"]').each(function(key,val){
                    if($(this).is(':checked')){
                        var value = $(this).val();
                        sendWay.push(value)
                    }
                })
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.post('',{sendWay:sendWay,_token:_token},function(data){
                    tipshow(data.info);
                })
            })
            //取消
            $('.js-btn-cancel').click(function(){
                window.location.href="/merchants/marketing/messagesPush"
            })
        })
    </script>
@endsection