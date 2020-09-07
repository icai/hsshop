<!-- 二级导航 开始 -->
<div class="second_items">
    <div class="second_title">
        客户经营
    </div>
    <ul class="second_nav">
        <!-- <li @if( $slidebar == 'index' ) class="hover" @endif >
            <a href="{{URL('/merchants/member')}}">客户概况</a>
        </li> -->
        <li @if( $slidebar == 'customer' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/customer')}}">客户管理</a>
        </li>
        <li @if( $slidebar == 'members' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/members')}}">会员管理</a>
        </li>
        <li @if( $slidebar == 'membercard' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/membercard')}}">会员卡</a>
        </li> 
        <!-- <li @if( $slidebar == 'indexPoint' ) class="hover" @endif > -->
        <li @if( $slidebar == 'indexPoint' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/point/indexPoint')}}">积分管理</a>
        </li>
            <!--<li @if( $slidebar == 'info' ) class="hover" @endif >
                <a href="{{URL('/merchants/member/info')}}">注册信息</a>
            </li>-->
        <!-- <li @if( $slidebar == 'label' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/label')}}">标签管理</a>
        </li> -->
        <!-- <li @if( $slidebar == 'fans' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/fans')}}">粉丝管理</a>
        </li> -->
        <!--只有该店铺可查看注册信息-->
        @if(in_array(session('wid'), [823]))
        <li @if( $slidebar == 'registerList' ) class="hover" @endif >
            <a href="{{URL('/merchants/member/li/registerList')}}">注册信息</a>
        </li>
        @endif
    </ul>
</div>
<!-- 二级导航 结束 -->
