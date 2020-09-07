<div class="left">
    <!-- 一级导航 开始 -->
    <div class="first_items">
        <!-- 一级导航列表 开始 -->`
        <ul class="first_nav">
            <li @if($sliderba=='businesscategory') class="hover" @endif>
                <a href="/staff/BusinessCategory">分类管理</a>
            </li>
            <li @if($sliderba=='registerUser') class="hover" @endif>
                <a href="/staff/registerUser">注册会员</a>
            </li>
            <li @if($sliderba=='getShop') class="hover" @endif>
                <a href="/staff/getShop">店铺会员</a>
            </li>
            <li @if($sliderba=='getTemplate') class="hover" @endif>
                <a href="/staff/getTemplate">默认模板设置</a>
            </li>
            <li @if($sliderba=='uploadFile') class="hover" @endif>
                <a href="/staff/uploadFile">上传微信公众号文件</a>
            </li>
            <li @if($sliderba=='customer') class="hover" @endif>
                <a href="/staff/BusinessManage/customer">店铺的访客</a>
            </li>
        </ul>
        <!-- 一级导航级导航列表 结束 -->
    </div>
    <!-- 一级导航 结束 -->
</div>