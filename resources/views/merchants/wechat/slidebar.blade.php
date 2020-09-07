<div class="left_title">
    <ul class="left_nav">
        <!-- <li @if ( $slidebar == 'index' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat')}}">微信状况</a>
        </li> -->
        <!-- <li @if ( $slidebar == 'constantly' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/constantly')}}">实时信息</a>
        </li> -->
        <!--<li class="line"></li>-->
        <!-- <li @if ( $slidebar == 'mass' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/mass')}}">群发信息</a>
        </li> -->
        <li @if ( $slidebar == 'replySet' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/replySet')}}">自动回复</a>
        </li>
        <!--<li class="line"></li>-->
        <li @if ( $slidebar == 'materialWechat' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/materialWechat')}}">图文素材</a>
        </li>
        <!-- <li @if ( $slidebar == 'timerSend' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/timerSend')}}">定时发送</a>
        </li> -->
        <!-- <li @if ( $slidebar == 'phrase' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/phrase')}}">快捷短语</a>
        </li> -->
        <!-- <li @if ( $slidebar == 'historyMsg' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/historyMsg')}}">历史消息</a>
        </li> -->
        <!--<li class="line"></li>-->
        <li @if ( $slidebar == 'menu' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/menu')}}">自定义菜单</a>
        </li>
        <!--<li class="line"></li>-->
        <li @if ( $slidebar == 'setting' )class="active"@endif> 
            <a href="{{URL('/merchants/wechat/weixinSet/setting')}}">公众号设置</a>
        </li>
            @if(strtotime(session('shop_created_at'))<'1535731200')
        <li @if ( $slidebar == 'book' )class="active"@endif>
            <a href="{{URL('/merchants/wechat/book')}}">预约管理</a>
        </li>
          @endif
    </ul>
</div>