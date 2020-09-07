<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        应用和营销
    </div>
    <ul class="second_nav">
        <li @if ( $slidebar == 'index' or $slidebar =='togetherGroupList' )class="hover"@endif>
            <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
        </li>
    </ul>
</div>
<!-- 二级导航 结束 -->