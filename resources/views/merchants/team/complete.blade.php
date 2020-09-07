<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/team_zvle2swq.css">
</head>
<body>
<div class="contenter">
    <div class="wrapper-app">
        <div id="header">
            <div class="header-title-wrap clearfix">
                <div class="account">
                    <span style="color: #000">{{ session('userInfo')['mphone'] }}</span>-
                    <span class="js-select-store" style="display: none;">
                        <a href="#">选择店铺</a>-
                    </span>
                    <a href="#">帮助</a>-
                    <a href="/auth/loginout">退出</a>
                </div>
                <a href="#">
                    <div class="header-logo"></div>
                </a>
            </div>
            <div class="addition">
                <ul class="progress-nav progress-nav-3 clearfix">
                    <li class="progress-nav-item">1.创建店铺</li>
                    {{--<li class="progress-nav-item  ">2.选择推荐模版</li>--}}
                    <li class="progress-nav-item active current-active">2.完成！</li>
                </ul>
            </div>
        </div>
        <div id="content" class="team-select">
            <div class="clearfix">
                <div class="cert-done-wrap">
                    <div class="cert-welc-wrap">
                        <h4 class="cert-welc-title">恭喜，您的店铺已创建成功！</h4>
                        <p class="cert-welc-subtitle" style="margin-top: 10px;">用微信扫下方二维码，看看你的店铺</p>
                        <div class="cert-welc-qrcode-wrap loading">
                            {!! QrCode::size(150)->generate(URL("/shop/index/".$id)); !!}
                        </div>
                    </div>
                    <div class="cert-wxauth-wrap">
                        <h4 class="cert-wxauth-title">如果您有
                            <span class="cert-important-text">微信公众号</span>
                            ，马上把店铺与微信公众号打通吧：
                        </h4>
                        <p style="margin-top: 10px;">为保证所有功能正常，授权公众号时请保持默认选择，把权限统一授权</p>
                        <form class="form-horizontal">
                            <fieldset>
                                <div class="js-cert-before-setting-area">
                                    <div class="control-group" style="margin-top:10px">
                                        <a class="btn btn-success cert-setting-btn js-cert-setting-btn" href="{{ $authUrl }}" target="_blank">我有微信公众号，立即设置</a>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <p class="cert-wxauth-help-info">
                                        <a class="js-skip btn btn-primary cert-setting-btn btn-large" style="background-image: none; width: 245px;padding: 0;" href="{{ URL('/merchants/index', Route::input('id')) }}">稍后再说，进入店铺后台</a>
                                    </p>
                                </div>
                                <div class="control-group">
                                    <ul class="cert-help-list">
                                        <li>
                                            绑定微信公众号，将可使用更多功能，
                                            <!-- <a  target="_blank">了解详情</a> -->
                                        </li>
                                        <li>
                                            请注意是微信公众号，不是微信个人账号。
                                            <!-- <a  target="_blank">了解它们的区别</a> -->
                                        </li>
                                        <li>
                                            如果您还没有微信公众号，可以
                                            <a href="https://mp.weixin.qq.com/" target="_blank">点此注册</a>
                                        </li>
                                    </ul>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="cert-done-pop cert-done-pop-help hide js-cert-done-pop-help">
                        <p>
                            若忘了公众号，请登录
                            <a href="https://mp.weixin.qq.com/" target="_blank">微信公众平台</a>，点左侧导航“设置 - 公众号设置”查看
                        </p>
                        <div class="cert-done-pop-help-image"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
 <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 三级联动 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/distpicker.data.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/distpicker.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/team_zvle2swq.js"></script>
</body>
</html>