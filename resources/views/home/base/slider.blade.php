<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/slider.css"/>
<div class="guding">
    <div class="connect-container">
        <div class="weixin-erwei">
            <img class="eriwei-a" width="134px" height="134px" src="{{ config('app.source_url') }}home/image/footer_code.jpg"/>
        </div>
        <div class="xianshi-dianhua phone-box">
            <a style="color: white;" href="javascript:;">Tel：{{$CusSerInfo['phone']}}</a>
        </div>
        <div class="xianshi-tel phone-box">
            <a style="color: white;" href="javascript:;">Tel：0571-87796692</a>
        </div>
        <div class="zaixian connect-method"></div>
        <div class="weixin connect-method"></div>
        <div class="lianxi connect-method"></div>
        <div class="slider_tel connect-method"></div>
        <div class="mianfei connect-method" onclick="window.location.href='/auth/register'"></div>
    </div>
    <div class="zhankai"></div>
</div>
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/slider.js" type="text/javascript" charset="utf-8"></script>
<!-- <script src="https://www.sobot.com/chat/frame/js/entrance.js?sysNum=36869a41c77f47b89e5ac494ebcb6e7a" class="zhiCustomBtn" id="zhichiScript" data-args="属性名1=属性值1&属性名2=属性值2"></script> -->
<script type="text/javascript">
    $(function(){
        $('.zaixian').click(function(){
            $('body').append('<iframe class="iframe" style="display:none;" src="tencent://message/?uin=1658349770&Site=&menu=yes"></iframe>');
        })
    })
</script>