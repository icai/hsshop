<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        店铺管理
    </div>
    <ul class="second_nav">
        <li @if ( $slidebar == 'index' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/index') }}">店铺信息</a>
        </li>
        
        <li @if ( $slidebar == 'admin' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/admin') }}">店铺管理员</a>
        </li>
        <li @if ( $slidebar == 'payment' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/payment') }}">支付/交易</a>
        </li>
        <!--<li @if ( $slidebar == 'reseptionList' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/receptionList') }}">订单设置</a>
        </li>-->
        {{--<li @if ( $slidebar == 'guarantee' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/guarantee') }}">消费保障</a>
        </li>--}}
        <li @if ( $slidebar == 'orderSet' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/express') }}">运费设置</a>
        </li>
        <li @if ( $slidebar == 'bindAdmin' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/bindAdmin') }}">微信绑定</a>
        
        <li @if ( $slidebar == 'smsconf' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/smsConf') }}">短信通知</a>
        </li>
        </li>
        <li @if ( $slidebar == 'cert' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/cert') }}">商户证书</a>
        </li>
        <li @if ( $slidebar == 'commonSetting' )class="hover"@endif>
            <a href="{{ URL('/merchants/currency/commonSetting') }}">通用设置</a>
        </li>
        <li @if ( $slidebar == 'commonSetting' )class="hover"@endif>
            <a href="{{ URL('/merchants/delivery/deliveryConfig') }}">外卖设置</a>
        </li>

    </ul>
</div>
<!-- 二级导航 结束 -->
