<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/team_okcs62a1.css">
</head>
<body>
    <div class="contenter">
        <div class="wrapper-app">
            <div id="header">
                <div class="header-title-wrap clearfix">
                    <div class="account">
                        <span style="color: #000">{{ session('userInfo')['mphone'] }}</span>-
                        <a target="_blank" href="{{ URL('/home/index/information') }}">帮助</a>-
                        <a href="{{ URL('/auth/loginout') }}">退出</a>
                    </div>
                    <a href="/">
                        <div class="header-logo"></div>
                    </a>
                    <div class="header-title">选择公司/店铺</div>
                </div>
                @if(0)
                <div class="adNav">
                    <a href="javascript:void(0);"><img src="{{ config('app.source_url') }}static/images/ad011.jpg" alt=""/></a>
                    <a href="/merchants/team/create" class="host_1"></a>
                    <a href="tencent://message/?uin=1658349770&Menu=yes" class="host_2"></a>
                    <a href="javascript:void(0);" class="host_3"></a>
                </div>
                @endif
                <div class="addition">
                    <div class="user-info">
                        @if ( session('userInfo')['head_pic'] )
                        <span class="avatar" style="background: url({{ imgUrl() }}{{session('userInfo')['head_pic']}}) no-repeat center center;background-size: 100% 100%;"></span>
                        @else
                        <span class="avatar" style="background-image: url({{ config('app.source_url') }}home/image/huisouyun_120.png);"></span>
                        @endif
                        <div class="user-info-content">
                            <div class="info-row">{{ session('userInfo')['name'] }}</div>
                            <div class="info-row info-row-info">帐号: {{ session('userInfo')['mphone'] }}</div>
                            <a href="{{ URL('/auth/set') }}" class="personal-setting">设置</a>
                        </div>
                        <div class="search-team hide">
                            <div class="form-search">
                                <input type="text" class="span3 search-query" placeholder="搜索店铺/微信/微博">
                                <button type="button" class="btn search-btn">搜索</button>
                            </div>
                        </div>
                        <div class="team-opt-wrapper">
                            @if ( $myShopNum >= 30 )
                            <a onclick="tipshow('不能建立超过30个店铺','warn');" class="js-create-shop">创建新店铺</a>
                            @else
                            <a href="{{ URL('/merchants/team/create') }}" class="js-create-shop">创建新店铺</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div id="content" class="team-select">
                <div>
                    <div class="team-select">
                        @if ( !empty($list) )
                        <div class="company-pane">
                            <div class="company-line">
                                <div class="company">
                                    <div class="row">
                                        <div class="company-line-name">我管理的店铺</div>
                                    </div>
                                    <!-- 店铺列表 -->
                                    <div class="team-select-pane">
                                        @foreach ($list as $v)
                                        <div class="team-icon mis @if ($v['show_color'] == 'green') suc @endif" data-href="{{ URL('/merchants/index',[$v['id']]) }}">
                                            @if($v['is_overdue'] == 1)<div class="customer-wrap"><img src="{{ config('app.source_url') }}mctsource/images/dayang.png"/></div>@endif
                                            <div class="team-opt-wrap">
                                                <a href="{{ URL('/merchants/currency/index',[$v['id']]) }}" class="edit-team">
                                                    <span>修改</span>
                                                </a>
                                                <a href="javascript:void(0);" class="delete-team" data-id="{{ $v['id'] }}" data-url="{{ URL('/merchants/team/delete',[$v['id']]) }}">
                                                    <span>删除</span>
                                                </a>
                                            </div>
                                            <div class="team-name-wrap ">
                                                <div class="team-name">{{ $v['shop_name'] }}</div>
                                            </div>
                                            <div class="weixin team-desc">公众号：@if ( $v['wx_status'] == '0' )无权限@elseif($v['wx_status'] == '1')未绑定 @elseif($v['wx_status'] == '2')已绑定 @endif</div>
                                            <div class="weixin team-desc">小程序：@if ( $v['minapp_status'] == '0' )无权限@elseif($v['minapp_status'] == '1')未绑定@elseif($v['minapp_status'] == '2')已绑定 @endif</div>
                                            <!--<div class="weixin team-desc">微信：已开通</div> -->
                                            <div class="weixin team-desc">@if($v['is_overdue'] == 1)  店铺状态：已打烊 @else 有效期至：{{ $v['limited'] }} @endif</div>
                                        </div>
                                        @endforeach
                                        <div style="clear: both"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="js-has-company">
                            <div class="pagenavi js-pagenavi">
                                {{ $pageHtml }}
                            </div>
                        </div>
                        @else
                        <!-- 没有数据 -->
                        <div class="js-no-company select-info">
                            <div class="desc-info">您还没有创建或加入任何店铺</div>
                            <div>
                            @if ( $myShopNum >= 30 )
                            <a onclick="alert('不能建立超过30个店铺');">
                                <button type="button" class="btn btn-success btn-xlarge">创建店铺</button>
                            </a>
                            @else
                            <a href="{{ URL('/merchants/team/create') }}">
                                <button type="button" class="btn btn-success btn-xlarge">创建店铺</button>
                            </a>
                            @endif


                            </div>
                            <!--<div class="how-to-join-info">您也可以加入别人创建的店铺参与管理
                                <a href="{{ URL('/home/index/detail/2') }}" target="_blank">如何加入</a>
                            </div>-->
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{ csrf_field() }}
    </div>
    <!-- 弱密码修改弹窗 -->
    @if (session('userInfo')['isWeakPasswd'] ?? false)
    <div class="passwrod-dialog-wrap">
        <div class="passwrod-dialog">
            <div class="password-dialog-title">
                提示
            </div>
            <div class="password-dialog-content">
                <div class="content-title">
                    您的登录密码安全性较弱，需要改为强密码后方可登录
                </div>
                <div class="content-info">
                    会搜云系统网络安全机制升级，为了您的账户安全，请将密码修改为英文字符+数字的组合形式。
                </div>
            </div>
            <div class="password-dialog-footer">
                <button class="password-btn">去修改</button>
            </div>
        </div>
    </div>
    @endif
    <style type="text/css">
        .model_bg{
            position: fixed;
            top: 50%;
            margin-top: -317px;
            z-index: 11111;
            margin-left: -562px;
            left: 50%;
        }
        .model_bg .close_mode_bg{
            position: absolute;
            left: 1076px;
            top: 10px;
            width: 40px;
            height: 40px;
            z-index:11111;
        }
    </style>
    <div class="modal-backdrop false in" style="display: none;"></div>
    <div class="model_bg" style="display:none">
        <div class="close_mode_bg"></div>
        <img src="{{ config('app.source_url') }}mctsource/images/model_bg2.png">
    </div>
    <!--删除店铺弹框-->
    <div class="del-shop">
        <div class="section">
            <div class="title">提示<span class="close-btn">x</span></div>
            <div class="warn">删除店铺，所有店铺相关信息丢失，店铺购买的应用和业务失效且不予退款，请谨慎操作！</div>
            <div class="margin"> 短信验证码：</div>   
            <div>
                <input type="num">
                <span class="getCode">获取验证码</span>
            </div>
            <div class="check">验证短信将发送到您绑定的手机：<span class="phone">{{ session('userInfo')['mphone'] }}</span>，请注意查收</div>
            <div class="sure">
                <input type="checkbox">已知晓删除店铺的风险，确认删除
            </div>
            <div class="all-btn">
                <button disabled="disabled" class="delshop del">删除店铺</button>
                <button class="cancel">取消</button>
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
    <!-- 公共js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/team_okcs62a1.js"></script>
    <script>
        // 弱密码弹窗
        $(function() {
            $('.password-dialog-footer .password-btn').click(function () {
                window.location.href="/auth/changepsd"
            })
        })
    </script>
</body> 
</html> 