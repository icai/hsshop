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
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/team_ce5bni1x.css">
</head>
<body>
<div class="contenter">
    <div class="wrapper-app">
        <div id="header">
            <div class="header-title-wrap clearfix">
                <div class="account">
                    <span style="color: #000">{{ session('userInfo')['mphone'] }}</span>-
                    <span class="js-select-store">
                        <a href="{{ URL('/merchants/team') }}">选择店铺</a>-
                    </span>
                    <a href="">帮助</a>-
                    <a href="/auth/loginout">退出</a>
                </div>
                <a href="/">
                    <div class="header-logo"></div>
                </a>
            </div>
            <div class="addition">
                <ul class="progress-nav progress-nav-3 clearfix">
                    <li class="progress-nav-item">1.创建店铺</li>
                    <li class="progress-nav-item active current-active">2.选择推荐模版</li>
                    <li class="progress-nav-item">3.完成！</li>
                </ul>
            </div>
        </div>
        <div id="content" class="team-select">
            <div>
                <div class="solution-desc">
                    <h4>会搜云已为您准备了多种行业模版，请根据实际情况任选一种：</h4>
                    <p>每个行业模版，都已经默认配置和启用了合适的功能，便于您能够快速的开店；</p>
                    <p>当然，您也可以根据自身情况，重新进行自定义设置的操作，满足你的日常经营；</p>
                </div>
                <ul class="solution-list clearfix js-solution-list">
                    @foreach ( $list as $v )
                    <li class="solution-item " data-type="0" data-id="{{ $v['id'] }}">
                        <div class="solution-item-screenshot" style="background-image: url({{ config('app.source_url').$v['thumb_url'] }});"></div>
                        <div class="solution-item-meta">
                            <h3>{{ $v['title'] }}</h3>
                            <p>{{ $v['description'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="solution-action">
                    <button class="btn btn-large btn-primary js-save" type="button" data-loading-text="正在提交...">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{ csrf_field() }}
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
 <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<!-- 通用base -->
<script type="text/javascript" src="{{config('app.source_url')}}mctsource/static/js/base.js"></script>
<!-- 当前页面js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/team_ce5bni1x.js"></script>
</body>
</html>