二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        资产中心
    </div>
    <ul class="second_nav">
        <li @if ( $slidebar == 'index' )class="hover"@endif>
            <a href="{{ URL('/merchants/capital') }}">我的收入</a>
        </li>
        <li @if ( $slidebar == 'billSummary' )class="hover"@endif>
            <a href="{{ URL('/merchants/capital/billSummary') }}">对账单</a>
        </li>
        <li @if ( $slidebar == 'billDetail' )class="hover"@endif>
            <a href="{{ URL('/merchants/capital/billDetail') }}">账单明细</a>
        </li> 
        <!-- <li @if ( $slidebar == 'serviceList' )class="hover"@endif>
            <a href="{{ URL('/merchants/capital/fee/serviceList') }}">订购服务</a>
        </li>  -->
        <!-- <li @if ( $slidebar == 'invoiceList' )class="hover"@endif>
            <a href="{{ URL('/merchants/capital/fee/invoiceList') }}">发票管理</a>
        </li>  -->
    </ul>
</div>
<!-- 二级导航 结束