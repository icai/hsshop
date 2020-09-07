<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        微社区
    </div>
    <ul class="second_nav">
        <li @if ($slidebar =='settingsList')class="hover"@endif>
            <a href="{{ URL('/merchants/microforum/settings/list') }}">社区设置</a>
        </li>
        <li @if ($slidebar =='postsList')class="hover"@endif>
            <a href="{{ URL('/merchants/microforum/posts/list') }}">帖子管理</a>
        </li>
        <li @if ($slidebar =='categoriesList')class="hover"@endif>
            <a href="{{ URL('/merchants/microforum/categories/list') }}">分类管理</a>
        </li>
        <li @if ($slidebar =='usersList')class="hover"@endif>
            <a href="{{ URL('/merchants/microforum/users/list') }}">用户管理</a>
        </li> 
        <li @if ($slidebar =='statisticsList')class="hover"@endif>
            <a href="{{ URL('/merchants/microforum/statistics/listView') }}">社区统计</a>
        </li>
    </ul>
</div>
<!-- 二级导航 结束 -->
