<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    @yield('head_css')
</head>
<body>
    <!-- 主体内容 开始 -->
    @yield('main')
    <!-- 主体内容 结束 -->
    <script type="text/javascript">
        var APP_HOST = "{{ config('app.url') }}";
        var APP_IMG_URL = "{{ imgUrl() }}";
        var APP_SOURCE_URL = "{{ config('app.source_url') }}";
        var CHAT_URL = "{{config('app.chat_url')}}";
        var CDN_IMG_URL = "{{config('app.cdn_img_url')}}";
    </script>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script type="text/javascript">
        //关注我们
        $('.attention').click(function(){
            $('.follow_us').show();
        });
        $(".code img").click(function(e){
            e.stopPropagation()
        })
        $('.follow_us').click(function(){
            $('.follow_us').hide();
        });

        $(function(){
            $.get("/shop/isSubscribe",function(data){
                if(data.status == 1){
                    if(data.data.subscribe == 0)
                    {
                        $('.top_attention').text('关注我们');
                        $('.top_attention').removeClass('hide');
                    }
                }
            });
            $.get("/shop/getApiName",function(data){
                if(data.status == 1){
                    $('.code img').attr('src',data.data.url);
                    $('.other_opt').text('若无法识别二维码');
                    var html = " <p>1.打开微信，点击“公众号”</p>" +
                        "<p>2.搜索公众号："+ data.data.name +"</p>" +
                        "<p>3.点击“关注”，完成</p>";
                    $('.opt').html(html);
                    $('.set').removeClass('hide');
                    $('.noset').addClass('hide');
                }else {
                    $('.set').addClass('hide');
                    $('.noset').removeClass('hide');
                }
            })

        })
    </script>
    @yield('page_js')
</body>
</html>
