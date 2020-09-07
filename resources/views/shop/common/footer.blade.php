<!-- 底部 开始 -->
<footer>
    <div>
        <div class="footer">
            <div class="copyright">
                @if($reqFrom != 'baiduapp')
                <div class="ft-links">
                    <a href="{{ config('app.url') }}/shop/index/{{session('wid')}}" >店铺主页</a>
                    <a href="{{ config('app.url') }}/shop/member/index/{{session('wid')}}" >会员中心</a>
                    @if($reqFrom != 'aliapp')
                    <a class="attention zx_attention" href="javascript:void(0);" >关注我们</a>
                    @endif

                    @if($__storeNumber__>0)
                    <a href="{{ config('app.url') }}/shop/store/getStore/{{session('wid')}}" >线下门店</a>
                    @endif
                    <!-- 第三方app隐藏topbar时，需要在底部显示购物记录入口 -->
                </div>
                @endif
                {{--暂时关闭掉支付宝底部logo跳转--}}
                @if($reqFrom == 'aliapp' || $__weixin['is_logo_show'] == 1 && $__weixin['is_logo_open'] == 0 || $reqFrom == 'baiduapp')
                    <a href="javascript:void(0);" >
                        <img src="@if($__weixin['logo_type']==0) {{ config('app.url') }}/static/images/footer_new_logo11.png  @else {{imgUrl()}}{{$__weixin['logo_path']}} @endif" class="footer-logo" />
                    </a>
                @elseif($__weixin['is_logo_show'] == 1 && $__weixin['is_logo_open'] == 1)
                    <a href="{{ $__weixin['link']}}" >
                        <img src="@if($__weixin['logo_type']==0) {{ config('app.url') }}/static/images/footer_new_logo11.png  @else {{imgUrl()}}{{$__weixin['logo_path']}} @endif" class="footer-logo" />
                    </a>
                @endif
            </div>
        </div>
    </div>
</footer>
<div class="follow_us zx_follow_open">
    <div class="delete zx_delete_close">x</div>
    {{--@if(!empty($apiName))--}}
    <div class="set">
        <div class="code">
            <img src="">
        </div>
        <p class="suc_info">长按图片【识别二维码】关注公众号</p>
        <p class="other_opt"></p>
        <div class="opt">
            <p>1.打开微信，点击‘公众号’</p>
            <p>2.搜索公众号：</p>
            {{--<p>2.搜索公众号：{{ $apiName }}</p>--}}
            <p>3.点击‘关注’，完成</p>
        </div>
    </div>
    {{--@else--}}
    <div class="noset hide" >
        <div class="code" style="background:url('{{ config('app.url') }}/shop/images/no_code.png') center center no-repeat; background-size: 200% 160%;margin-bottom: 0;padding-top: 20px;">
        </div>
        <p class="info">商家二维码失效</p>
        <p class="info">公众号暂时无法关注~</p>
    </div>
    {{--@endif--}}
</div>


<script type="text/javascript">
    var thump_400 = "{{ config('app.thumbnail_400')}}";
    var thump_300 = "{{ config('app.thumbnail_300')}}";
    var thump_200 = "{{ config('app.thumbnail_200')}}";
</script>