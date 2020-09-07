<div class="left">
    <!-- 一级导航 开始 -->
    <div class="first_items">
        <!-- 一级导航列表 开始 -->`
        <ul class="first_nav">
            <li @if($sliderbar=='reserveManage')class="hover"@endif>
                <a href="/staff/customer/reserveManage">全部</a>
            </li>
            <li @if($sliderbar=='reserveManage1')class="hover"@endif>
                <a href="/staff/customer/reserveManage?status=1">加星客户</a>
            </li>
        <!-- 一级导航级导航列表 结束 -->
    </div>
    <!-- 一级导航 结束 -->
</div>