<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        分销管理
    </div>
    <ul class="second_nav">
        <li @if ( $slidebar == 'index' or $slidebar == 'template' )class="hover"@endif>
            <a href="{{ URL('/merchants/distribute') }}">佣金设置</a>
        </li>
         <li @if ( $slidebar == 'commission' )class="hover"@endif>
            <a href="{{ URL('/merchants/distribute/commission') }}">佣金提现</a>
        </li>
        <li @if ( $slidebar == 'partner' || $slidebar == 'partnerIncome' || $slidebar == 'partnerContacts' )class="hover"@endif>
            <a href="{{ URL('/merchants/distribute/partner') }}">分销用户</a>
        </li>
        <li>
            <a href="{{ URL('/merchants/product/index/1?tag=1') }}">分销商品</a>
        </li>
        <li>
            <a href="{{ URL('/merchants/order/orderList?field=oid&distribute_type=1') }}">分销订单</a>
        </li>
        @if(in_array(session('wid'),config('app.li_wid')))
            <li>
                <a href="{{ URL('/merchants/distribute/getSourceInfo') }}">广告客户</a>
            </li>
    @endif
        
    </ul>
</div>
<!-- 二级导航 结束 -->