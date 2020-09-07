<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        商品管理
    </div>
    <ul class="second_nav">  
        <li @if ( $slidebar == 'index' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/index/1?tag=0' )}}">商品库</a>
        </li>
        <li @if ( $slidebar == 'productGroup' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/productGroup') }}">商品分组</a>
        </li>
        <!-- <li @if ( $slidebar == 'goodsTemplate' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/goodsTemplate') }}">商品页模板</a>
        </li>  -->
        <li @if ( $slidebar == 'importGoods' )class="hover"@endif>
            {{--<a href="{{ URL('/merchants/product/importGoods') }}">商品导入</a>--}}
            <a href="{{ URL('/merchants/product/importMaterial') }}">商品导入</a>
        </li>
        <li @if ( $slidebar == 'distributionGoods' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/index/1?tag=1') }}">分销商品</a>
        </li>
        <li @if ( $slidebar == 'discountGoods' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/index/1?tag=2') }}">折扣商品</a>
        </li>
        <li @if ( $slidebar == 'pointGoods' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/index/1?tag=3') }}">积分商品</a>
        </li>
        <li @if ( $slidebar == 'camGoods' )class="hover"@endif>
            <a href="{{ URL('/merchants/product/index/1?tag=4') }}">卡密商品</a>
        </li>
    </ul>
</div>
<!-- 二级导航 结束 -->