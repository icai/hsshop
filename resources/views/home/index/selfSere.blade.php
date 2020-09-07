@extends('home.base.head')
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/selfSere.css"/>
@endsection
@section('content')
    @include('home.base.slider')
    <!--帮助中心 搜索-->
    <div class="help_top">
        <div class="help_top_content clearfix">
            <div class="help_top_left fl">
                帮助中心
            </div>
            <!-- <div class="help_top_right fr">
                <div class='clearfix'>
                    <input class='right_inp fl' placeholder="请输入需要搜索的关键字" type="text">
                    <botton class="right_btn fl"><span></span></botton>
                </div>
                <div class="help_top_right_tip">
                    <span>小程序</span>
                    <span>小程序</span>
                    <span>小程序</span>
                    <span>小程序</span>
                </div>
            </div> -->
        </div>
    </div>
    <!--帮助中心 内容导航-->
    <div class="help_nav">
        <div class="help_nav_content">
            <a href="{{ config('app.url') }}home/index/helps">帮助首页</a>
            <a href="{{ config('app.url') }}home/index/helpList">常见问题</a>
            <a class='nav_active' href="{{ config('app.url') }}home/index/selfServe">自助服务</a>
        </div>
    </div>
    <!--帮助中心 账号相关-->
    <div class="help_details">
        <h3 class="help_details_title">账号相关</h3>
        <ul class="help_details_content clearfix">
            <li class='clearfix'>
                <a href="{{ config('app.url') }}auth/forgetpsd">
                    <div class='fl list_div_li'>
                        <img src="{{ config('app.source_url') }}home/image/help_01@2x.png" alt="">
                    </div>
                    <div class='fl list_div_txt'>
                        <p class="list_div_txt_p">找回密码</p>
                        <p class='list_div_txt_tip'>忘记登录密码时，通过注册手机号快速找回密码</p>
                    </div>
                </a>
            </li>
            <li class='clearfix'>
                <a href="{{ config('app.url') }}auth/changepsd">
                    <div class='fl list_div_li'>
                        <img src="{{ config('app.source_url') }}home/image/help_02@2x.png" alt="">
                    </div>
                    <div class='fl list_div_txt'>
                        <p class="list_div_txt_p">修改联系人密码</p>
                        <p class='list_div_txt_tip'>登录后可以更改密码</p>
                    </div>
                </a>
            </li>
            <li class="border_right clearfix">
                <a href="/merchants/currency/admin">
                    <div class='fl list_div_li'>
                        <img src="{{ config('app.source_url') }}home/image/help_03@2x.png" alt="">
                    </div>
                    <div class='fl list_div_txt'>
                        <p class="list_div_txt_p">添加店铺管理员</p>
                        <p class='list_div_txt_tip'>添加不同权限的员工，提高工作效率</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <!--帮助中心 店铺相关-->
    <div class="help_details help_shop">
        <h3 class="help_details_title">店铺相关</h3>
        <ul class="help_details_content clearfix">
            <li class='clearfix'>
                <a href="/merchants/currency/outlets">
                    <div class='fl list_div_li'>
                        <img src="{{ config('app.source_url') }}home/image/help_04@2x.png" alt="">
                    </div>
                    <div class='fl list_div_txt'>
                        <p class="list_div_txt_p">修改店铺地址</p>
                        <p class='list_div_txt_tip'>设置店铺地址,完善店铺信息</p>
                    </div>
                </a>
            </li>
            <li class='clearfix'>
                <a href="/merchants/currency/index">
                    <div class='fl list_div_li'>
                        <img src="{{ config('app.source_url') }}home/image/help_05@2x.png" alt="">
                    </div>
                    <div class='fl list_div_txt'>
                        <p class="list_div_txt_p">修改店铺名称</p>
                        <p class='list_div_txt_tip'>编辑独特的店铺名称，是成功的第一步</p>
                    </div>
                </a>
            </li>
            <li class="border_right clearfix">
                <a href="/merchants/currency/index">
                    <div class='fl list_div_li'>
                        <img src="{{ config('app.source_url') }}home/image/help_06@2x.png" alt="">
                    </div>
                    <div class='fl list_div_txt'>
                        <p class="list_div_txt_p">修改店铺logo</p>
                        <p class='list_div_txt_tip'>设置个性化店铺logo，让消费者一眼记住你</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}home/js/selfSere.js" type="text/javascript" charset="utf-8"></script>
@endsection