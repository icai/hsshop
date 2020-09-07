<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        店铺管理
    </div>
    <ul class="second_nav">
        <li @if ( $slidebar == 'home' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/home') }}">店铺概况</a>
        </li>
        <li @if ( $slidebar == 'index' )class="hover"@endif>
            <a href="{{ URL('/merchants/store?is_show=1') }}">微页面</a>
        </li>
        <!-- <li @if ( $slidebar == 'pagecat' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/pagecat') }}">页面分类</a>
        </li> -->
        <!--<li @if ( $slidebar == 'drapt' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/drapt') }}">微页面草稿</a>
        </li>-->
        <li @if ( $slidebar == 'userCenter' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/userCenter') }}">会员主页</a>
        </li>
        <li @if ( $slidebar == 'shopNav' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/shopNav') }}">店铺导航</a>
        </li>
        <!--<li @if ( $slidebar == 'globalTemplate' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/globalTemplate') }}">全店风格</a>
        </li>-->
        <li @if ( $slidebar == 'ad' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/ad') }}">公共广告</a>
        </li>
        <li @if ( $slidebar == 'component' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/component') }}">自定义模块</a>
        </li>
        <li @if ( $slidebar == 'attachmentImage' )class="hover"@endif>
            <a href="{{ URL('/merchants/store/attachmentImage') }}">我的文件</a>
        </li>
    </ul>
</div>
<!-- 二级导航 结束 -->
