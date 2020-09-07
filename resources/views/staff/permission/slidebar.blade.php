<div class="left">
    <!-- 一级导航 开始 -->
    <div class="first_items">
        <!-- 一级导航列表 开始 -->`
        <ul class="first_nav">
            <li @if($sliderba=='adminrole') class="hover" @endif>
                <a href="/staff/getAdminRole">总后台权限管理</a>
            </li>
            <li  @if($sliderba=='role') class="hover" @endif>
                <a href="/staff/getRole">店铺权限管理</a>
            </li>
            <li  @if($sliderba=='account') class="hover" @endif>
                <a href="/staff/account">账号管理</a>
            </li>
        </ul>
        <!-- 一级导航级导航列表 结束 -->
    </div>
    <!-- 一级导航 结束 -->
</div>