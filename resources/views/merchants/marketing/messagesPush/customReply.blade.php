@extends('merchants.default._layouts') @section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/custom_reply.css" />
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
                    <a href="javascript:;">客服消息通知</a>
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
                                <!-- <div class="divide-box">
                                    <div class="divide-title no-describe">
                                        <label class="zent-form__checkbox zent-checkbox-wrap zent-checkbox-checked">
                                            <span class="zent-checkbox">
                                                <input type="checkbox" name="mb" value="on">
                                            </span>
                                            <span>手机短信（计费发送）</span>
                                        </label>
                                    </div>
                                    <div class="divide-content">
                                        <div class="scale-size-wrap">
                                            <div class="position-wrap">
                                                <div class="message-template-preview">
                                                    <div class="message-template-scroll">
                                                        <div class="fake-time">上午9:41</div>
                                                        <div>
                                                            <div class="message-template-content">客服新消息通知：<br />“XXX店铺” 管理员，“用户昵称” 用户正访问您的店铺并向你咨询对应服务信息，请您看到消息后登录商家后台及时回复处理，感谢您对会搜云的支持，祝生意兴隆！</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="divide-box">
                                    <div class="divide-title no-describe">
                                        <label class="zent-form__checkbox zent-checkbox-wrap zent-checkbox-checked">
                                        <span class="zent-checkbox">
                                            <input  type="checkbox" @if(in_array(2,$data['config'])) {{ 'checked' }}@endif name="mb" value="2">
                                        </span>
                                            <span>消息通知（免费发送）</span>
                                        </label>
                                    </div>
                                    <div class="divide-content">
                                        <p class="setting-title">
                                            仅支持认证订阅号和认证服务号，并且需要客户与公众号在48小时内有过互动（关注公众号、发送信息、点击自定义菜单等）。发送成功后，客户可在公众号中收到下图所示的卡片消息
                                        </p>
                                        <div class="scale-size-wrap">
                                            <div class="position-wrap">
                                                <div class="wechat-fans-msg-preview">
                                                    <div class="wechat-fans-msg-company">{{ request()->session()->get('shop_name') }}</div>
                                                    <div class="wechat-fans-msg-scroll">
                                                        <div class="fake-time">
                                                            <span>昨天 上午12:37</span>
                                                        </div>
                                                        <div class="wechat-fans-msg-content">
                                                            <img class="wechat-fans-msg-avatar" src="{{ config('app.source_url') }}mctsource/images/logo_icon.png" alt="头像">
                                                            <div class="wechat-fans-msg-text">
                                                                客服通知:您有新的客服消息，请及时查看。<span class="wechat-fans-msg-link">查看客服消息</span>。
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