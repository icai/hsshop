<div class="left">
    <!-- 一级导航 开始 -->
    <div class="first_items">
        <!-- 一级导航列表 开始 -->`
        <ul class="first_nav">
            <li>
                <a href="/staff/BusinessCategory">分类管理</a>
            </li>
            <li >
                <a href="/staff/registerUser">注册会员</a>
            </li>
            <li>
                <a href="/staff/getShop">店铺会员</a>
            </li>
            <li>
                <a href="/staff/getTemplate">默认模板设置</a>
            </li>
            <li>
                <a href="/staff/uploadFile">上传微信公众号文件</a>
            </li>
            <li>
                <a href="/staff/BusinessManage/customer">店铺访客</a>
            </li>
            <li>
                <a href="/staff/BusinessManage/affiche">店铺公告</a>
            </li>
            <li @if($sliderba=='orderlist') class="hover" @endif>
                <a href="/staff/fee/order/list">续费订购</a>
            </li>
            <li @if($sliderba=='invoicelist') class="hover" @endif>
                <a href="/staff/fee/invoice/list">发票管理</a>
            </li>
        </ul>
        <!-- 一级导航级导航列表 结束 -->
    </div>
    <!-- 一级导航 结束 -->
</div>