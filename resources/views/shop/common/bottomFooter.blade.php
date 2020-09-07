<div class="bottom-footer">
    <div class="bottom-copyright">
        <div class="bottom-ft-links">
            <a href="{{ config('app.url') }}/shop/index/{{session('wid')}}" >店铺主页</a>
            <a href="{{ config('app.url') }}/shop/member/index/{{session('wid')}}" >会员中心</a>
            @if($__storeNumber__>0)
            <a href="{{ config('app.url') }}/shop/store/getStore" >线下门店</a>
            @endif
            <!-- 第三方app隐藏topbar时，需要在底部显示购物记录入口 -->
        </div>
        <a href="javascript:;" >
            <img src="{{ config('app.url') }}/static/images/footer_new_logo.png" class="bottom-footer-logo" />
        </a>
    </div>
</div>