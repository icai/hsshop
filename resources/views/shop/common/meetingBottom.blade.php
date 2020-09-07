<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
<div class="js-navmenu js-footer-auto-ele shop-nav nav-menu nav-menu-1 has-menu-3" style="height:55px">
    <div class="nav-items-wrap" style="margin-left:0px">
        <div class="nav-item" style="width: 50%;border-left: none;height:55px">
            <a href="/shop/index/{{session('wid')}}?{{ time() }}" class="mainmenu js-mainmenu" style="height:55px">
                <span class="mainmenu-txt" style="font-size:17px;font-weight: bold;line-height:55px">首页</span>
            </a>
            <div class="submenu js-submenu" style="display: none;">
                <span class="arrow before-arrow"></span>
                <span class="arrow after-arrow"></span>
                <ul></ul>
            </div>
        </div>
        <div class="nav-item" style="width: 50%;height:55px;">
            <a href="/shop/meeting/groups/showMyGroups/{{session('wid')}}?{{ time() }}" class="mainmenu js-mainmenu" style="height:55px">
                <span class="mainmenu-txt" style="font-size:17px;font-weight: bold;line-height:55px">我的</span>
            </a>
            <div class="submenu js-submenu" style="display: none;">
                <span class="arrow before-arrow"></span>
                <span class="arrow after-arrow"></span>
                <ul></ul>
            </div>
        </div>
    </div>
</div>
