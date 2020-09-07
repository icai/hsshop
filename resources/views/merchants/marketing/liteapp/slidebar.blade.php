<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        应用和营销
    </div>
    <style>
        .second_items .second_nav li a {
            font-size: 14px;
        }
    </style>
    <ul class="second_nav" >
        <li @if ( $slidebar =='litePage' )class="hover"@endif>
            <a href="{{ URL('/merchants/marketing/litePage') }}">小程序微页面</a>
        </li>
        <li @if ( $slidebar =='footerBar' )class="hover"@endif>
            <a href="{{ URL('/merchants/marketing/footerBar') }}">底部导航</a>
        </li>
        <li @if ( $slidebar =='topnav' )class="hover"@endif>
            <a href="{{ URL('/merchants/marketing/xcx/topnav') }}">首页分类导航</a>
        </li>
        <!-- <li @if ( $slidebar =='infoList' )class="hover"@endif>
            <a href="{{ URL('/merchants/marketing/xcx/list') }}">小程序列表</a>
        </li> -->
        <li @if ( $slidebar =='liteStatistics' )class="hover"@endif>
            <a href="{{ URL('/merchants/marketing/liteStatistics') }}">数据统计</a>
        </li>
        <li @if ( $slidebar == 'index' or $slidebar =='list' ) class="hover" @endif>
            <a href="{{ URL('/merchants/marketing/xcx/list') }}">微信小程序</a>
        </li>
        <!-- <li @if ( $slidebar == 'index' or $slidebar =='alilist' ) class="hover" @endif>
            <a href="{{ URL('/merchants/marketing/alixcx/list') }}" style="font-size:14px">支付宝小程序</a>
        </li> -->
        <li @if ( $slidebar == 'userCenter' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/userCenter') }}">会员主页</a>
        </li>
    </ul>
</div>
<!-- 二级导航 结束 -->