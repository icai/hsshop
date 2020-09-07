<div class="left">
    <!-- 一级导航 开始 -->
    <div class="first_items">
        <!-- 一级导航列表 开始 -->`
        <ul class="first_nav">
            <li @if($sliderbar=='getInformation')class="hover"@endif>
                <a href="/staff/getInformation">资讯列表</a>
            </li>
            <li @if($sliderbar=='editInformation')class="hover"@endif>
                <a href="/staff/editInformation">添加资讯</a>
            </li>
            <li @if($sliderbar=='getInfoType')class="hover"@endif>
                <a href="/staff/getInfoType">资讯分类</a>
            </li>
            <li @if($sliderbar=='getRecomment')class="hover"@endif>
                <a href="/staff/getRecomment">推荐管理</a>
            </li>
            <li @if($sliderbar=='')class="hover"@endif>
                <a href="/staff/getRecomment">帮助管理</a>
            </li>
        </ul>
        <!-- 一级导航级导航列表 结束 -->
    </div>
    <!-- 一级导航 结束 -->
</div>