<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        订单管理
    </div>
    <ul class="second_nav">
        <li @if ( $slidebar == 'index' )class="hover"@endif>
            <a href="{{ URL('/merchants/order') }}">订单概况</a>
        </li>
        <li @if ( $slidebar == 'orderList_0' )class="hover"@endif>
            <a href="{{ URL('/merchants/order/orderList') }}">所有订单</a>
        </li>
        <li @if ( $slidebar == 'orderList_1' )class="hover"@endif>
            <a href="{{ URL('/merchants/order/orderList/1') }}">加星订单</a>
        </li>
        <li @if ( $slidebar == 'orderList_2' )class="hover"@endif>
            <a href="{{ URL('/merchants/order/orderList/2') }}">维权订单</a>
        </li>
        <li @if ( $slidebar == 'hexiaoOrder' )class="hover"@endif>
            <a href="{{ URL('/merchants/order/hexiaoOrder') }}">核销订单</a>
        </li>
        <li @if ( $slidebar == 'evaluateOrder' )class="hover"@endif>
            <a href="{{ URL('/merchants/order/evaluateOrder') }}">评价管理</a>
        </li>
        <li @if ( $slidebar == 'printOrder' )class="hover"@endif>
            <a href="{{ URL('/merchants/order/printOrder') }}">快速打单</a>
        </li>
         <li @if ( $slidebar == 'batchDelivery' )class="hover"@endif>
            <!--<a href="{{ URL('/merchants/order/batchDelivery') }}">批量发货</a>-->           
        </li>
    </ul>
</div>
<!-- 二级导航 结束 -->