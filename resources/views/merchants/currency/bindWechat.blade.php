@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_rm37no4u.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a>微信绑定</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
<div class="content content_1">
    <div class="content_top">
        <div class="content_top_left">
            @if(!empty($manager['open_id']))
                <a  type="button" class="btn btn-success unbind">解绑</a>
                <p>绑定者微信昵称：{{ $manager['nick_name'] }}</p>
                @else
                <a  type="button" class="btn btn-success bind">打开绑定二维码</a>
            @endif

        </div>
        <div class="content_top_right">
            <a href="/home/index/detail/32" target="_blank">查看管理员相关教程</a>
        </div>
    </div>
    <div class="content_center">

    </div>
    <div class="content_bottom">
        <p>店铺绑定微信，说明如下：</p>
        <span id="">
            1. 为当前登陆管理员绑定微信号；<br />
            2. 绑定微信号只与当前店铺相关联<br />
            3. 若当前店铺公众号已经开启了微信消息模板功能，以及设置了店铺相应的消息提醒功能可绑定成功后；该绑定公众号将会获取到相应的消息提醒<br />
            <a href="/home/index/detail/32" style="color: blue;line-height:50px"target="_blank">查看管理员相关教程</a>
        </span>
    </div>
</div>


@endsection
@section('page_js')

    <script type="text/javascript">
        $(function () {

            $(".bind").click(function () {
                var uid = "{{ $uid }}";
                var url = '/merchants/currency/bindAdmin';
                var data = {
                    'uid':uid,
                    '_token':$("meta[name='csrf-token']").attr('content')
                };
                $.post(url,data,function (data) {
                    if(data.status == 1){
                        hstool.open({
                            title:"店铺管理员绑定邀请二维码（有效期5分钟，过期请重新获取）",
                            area:["430px","430px"],
                            content: '<div><img src="'+data.data+'" ></div>'
                        })

                    }else {
                        layer.msg(data.info, {icon: 5});
                    }
                })
            });


            $(".unbind").click(function () {
                var uid = {{ $uid }};
                var url = '/merchants/currency/unbindAdmin';
                $.get(url,{ uid: uid },function (data) {
                    if(data.status == 1){
                        window.location.reload();
                        layer.msg('解绑成功', {icon: 4});
                    }else {
                        layer.msg(data.info, {icon: 5});
                    }
                })
            });
        });
    </script>

    <!-- 搜索插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_rm37no4u.js"></script>
@endsection


