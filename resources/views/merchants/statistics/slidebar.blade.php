<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        数据中心
    </div>
    <ul class="second_nav">
        <!-- @if ( $slidebar == 'index' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics') }}">数据概况</a>
        </li>-->
        <li @if ( $slidebar == 'shops' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics/shops/index') }}">店铺分析</a>
        </li>
        <!-- <li @if ( $slidebar == 'customer' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics/customer/index') }}">客户分析</a>
        </li>
        <li @if ( $slidebar == 'pagedata' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics/pagedata') }}">页面流量</a>
        </li>
        <li @if ( $slidebar == 'goods' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics/goods') }}">商品分析</a>
        </li> -->
        <li @if ( $slidebar == 'transaction' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics/transaction') }}">交易分析</a>
        </li>
        <!-- <li @if ( $slidebar == 'coupons' )class="hover"@endif>
            <a href="{{ URL('/merchants/statistics/coupons') }}">卡券统计</a>
        </li> -->
    </ul>
</div>
<!-- 二级导航 结束 -->