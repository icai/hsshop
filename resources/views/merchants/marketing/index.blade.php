@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_uynh7ai2.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">营销中心</div>
        <!-- 二级导航三级标题 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content making_stro">
	<!-- 列表名 开始 -->
    <strong class="mgb15">经营渠道</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb20">
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/xcx/list') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/code_app.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信小程序</strong> 
                    <p class="f12 gray_999">一键生成微信小程序</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/wechat/replySet') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/wx@2x(2).png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">链接公众号,玩转微信生态</p>
                </div>
            </a>
        </li>
        <!-- <li>
            <a class="display_box"  @if(in_array(session('wid'),['2528','3708','3699']))href="/merchants/marketing/alixcx/list" @else onclick="tipshow('如有您有开发需求请咨询平台客服或支付宝客户端搜索“会搜企管培训”小程序了解','info');" @endif>
                <img src="{{ config('app.source_url') }}mctsource/images/alipay_tiny.png" width="44" height="44" />
                <div class="list_content">
                    <strong>支付宝小程序</strong> 
                    <p class="f12 gray_999">拓展新渠道</p>
                </div>
            </a>
        </li> -->
        <li>
            <a class="display_box" href="{{ URL('/merchants/distribute') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/share_fenxiao.png" width="44" height="44" />
                <div class="list_content">
                    <strong>分销佣金</strong> 
                    <p class="f12 gray_999">分销管理</p>
                </div>
            </a>
        </li>        
    </ul> 
    <!-- 列表 结束 -->
    
    <!-- 列表名 开始 -->
    <strong class="mgb15">促销折扣</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb20">
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/coupons') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/coupons.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        优惠券
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">向客户发放店铺优惠券</p>
                </div>
            </a>
        </li>
        <li class="">
            <a class="display_box" href="{{ URL('/merchants/marketing/seckills') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/seckill-icon.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        秒杀
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">快速抢购引导顾客更多消费</p>
                </div>
            </a>
        </li>
        <!--拆红包入口-->
        <li class="">
            <a class="display_box" href="{{ URL('/merchants/marketing/bonus/index') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/red_packet.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        拆红包
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong>
                    <p class="f12 gray_999">火爆引流神器</p>
                </div>
            </a>
        </li>
         <!--满减入口 add by 黄新琴-->
         <li class="">
            <a class="display_box" href="{{ URL('/merchants/marketing/discountList') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/discount-icon.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        满减
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong>
                    <p class="f12 gray_999">达到消费额度或购买件数即可满减</p>
                </div>
            </a>
        </li>
    </ul> 
    <!-- 列表 结束 -->
    
    <!-- 列表名 开始 -->
    <strong class="mgb15">促销工具</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb20">
    	@if($showLi == 1)
    	<li>
    	    <a class="display_box bg-yellow" href="{{ URL('/merchants/share/event/index') }}">
    	        <img src="{{ config('app.source_url') }}mctsource/images/xiang.png" width="44" height="44" />
    	        <div class="list_content">
    	            <strong>
                        享立减二期
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
    	            <p class="f12 gray_999">用户点击分享链接可减钱的新玩法</p>
    	        </div>
            </a>
    	</li>
    	@endif
        <li>
            <a class="display_box bg-yellow" href="{{ URL('/merchants/shareEvent/list') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/xiang.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        享立减
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">用户点击分享链接可减钱的新玩法</p>
                </div>
            </a>
        </li>
        <!--@if( in_array(session('wid'),config('app.open_share')))
        <li>
            <a class="display_box bg-yellow" href="{{ URL('/merchants/share/praise/index') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/jizan.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        集赞
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">用户点击分享链接可减钱的新玩法</p>
                </div>
            </a>
        </li>
        @endif-->
        <li>
            <a class="display_box bg-yellow" href="{{ URL('/merchants/marketing/togetherGroupList') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/group.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        多人拼团
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">引导客户邀请朋友一起拼团购买</p>
                </div>
            </a>
            <label class="hot-label hot-label-red">推荐</label>
        </li>
        <li>
        	<a class="display_box" href="{{ URL('/merchants/marketing/wheelList') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/choujiang.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        幸运大转盘
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">常见的转盘式抽奖玩法</p>
                </div>
            </a>
        </li>   
        <li>
            <a class="display_box" href="{{ URL('/merchants/microforum/settings/list') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/posts-icon.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微社区</strong> 
                    <p class="f12 gray_999">打造人气移动社区,增加客户流量</p>
                </div>
            </a>
        </li> 
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/egg/index') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/egg.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        砸金蛋
                        <span class="xcx-box hide">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">好蛋砸出来</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/sign') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/sign.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        签到
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">每日签到领取积分或奖励</p>
                </div>
            </a>
        </li>
        <li>
            {{--<a class="display_box" href="{{ URL('/merchants/marketing/scratchList') }}">--}}
            <!--updata by 邓钊 2018-7-30-->
            <a class="display_box" href="javascript:void(0)" id="getScratch">
                <img src="{{ config('app.source_url') }}mctsource/images/scratch.png" width="44" height="44" />
                <div class="list_content">
                    <strong>刮刮卡</strong>
                    <p class="f12 gray_999">通过刮开卡片进行抽奖</p>
                </div>
            </a>
        </li>
    </ul> 
    <!-- 列表 结束 -->
    
    <!-- 列表名 开始 -->
    <strong class="mgb15">会员卡券</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb20">
        <li class="">
            <a class="display_box" href="{{ URL('/merchants/member/membercard') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/huiyuanka.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        会员卡
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">设置并给客户发放会员卡</p>
                </div>
            </a>
        </li>
        <li class="">
            <a class="display_box" href="{{ URL('/merchants/member/point/indexPoint') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/jifen.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        积分
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">开通会员充值功能</p>
                </div>
            </a>
        </li>  
        <li class="">
            <a class="display_box" href="{{ URL('/merchants/member/storageValue') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/chongzhi.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        充值
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>
                    </strong> 
                    <p class="f12 gray_999">开通会员充值功能</p>
                </div>
            </a>
        </li>  
    </ul>
    <!-- 列表 结束 -->
    
    <!-- 列表名 开始 -->
    <strong class="mgb15">推广工具</strong>
    <ul class="list_items mgb20">
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/messagesPush') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/news_push.png" width="44" height="44" />
                <div class="list_content">
                    <strong>
                        消息推送
                        <span class="xcx-box">
                            <img src="{{ config('app.source_url') }}mctsource/images/xcx-icon.png" alt="" class="xcx-icon">
                            <span class="xcx-icon-tips">小程序已支持</span>
                        </span>                        
                    </strong>
                        <p class="f12 gray_999">向客户发送微信消息通知</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/researches/2') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/toupiao.png" width="44" height="44" />
                <div class="list_content">
                    <strong>在线投票</strong> 
                    <p class="f12 gray_999">向客户发起投票活动</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/researches/0') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/liuyan.png" width="44" height="44" />
                <div class="list_content">
                    <strong>在线报名</strong>
                        <p class="f12 gray_999">向客户发起调查活动</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/researches/1') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/liuyan.png" width="44" height="44" />
                <div class="list_content">
                    <strong>在线预约</strong>
                        <p class="f12 gray_999">提供多行业的预约服务</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box shai_img bg-yellow" href="javascript:void(0);">
                <img src="{{ config('app.source_url') }}mctsource/images/shai.png" width="44" height="44" />
                <div class="list_content">
                    <strong>晒图有奖</strong>
                        <p class="f12 gray_999">为你提供千万流量入口</p>
                </div>
            </a>
            <label class="hot-label hot-label-red">推荐</label>
            <div class="shai_code"><img width='120' height="120" src="{{ config('app.source_url') }}mctsource/images/shai_code.jpg" alt=""></div>
        </li>
    </ul>
    <!-- 列表 结束 -->

     <!-- 列表名 开始 -->
     <strong class="mgb15">配套工具</strong>
    <ul class="list_items mgb20">
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/orderHexiao') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/guard.png" width="44" height="44" />
                <div class="list_content">
                    <strong>验证工具</strong> 
                    <p class="f12 gray_999">核销自提单</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/cam/list') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/kam.png" width="44" height="44" />
                <div class="list_content">
                    <strong>发卡密</strong> 
                    <p class="f12 gray_999">虚拟商品发送账号卡密</p>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    
    <!-- 列表名 开始 -->
    <strong class="mgb15 hide">促销工具</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb30 hide">
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/coupons') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/coupons.png" width="44" height="44" />
                <div class="list_content">
                    <strong>优惠券</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/sign') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/sign.png" width="44" height="44" />
                <div class="list_content">
                    <strong>签到</strong> 
                    <p class="f12 gray_999">每日签到领取积分或奖励</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/couponcode') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>优惠码</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/achieveGive') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>满减/送</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box bg-yellow" href="{{ URL('/merchants/marketing/togetherGroupList') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/group.png" width="44" height="44" />
                <div class="list_content">
                    <strong>多人拼团</strong> 
                    <p class="f12 gray_999">引导客户邀请朋友一起拼团...</p>
                </div>
            </a>
            <label class="hot-label hot-label-red">推荐</label>
        </li>
        <li>
        	<a class="display_box" href="{{ URL('/merchants/marketing/wheelList') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/choujiang.png" width="44" height="44" />
                <div class="list_content">
                    <strong>幸运大转盘</strong> 
                    <p class="f12 gray_999">常见的转盘式抽奖玩法</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/groupBuy') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>团购返现</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li> 
        <li>
            <a class="display_box" href="{{ URL('/merchants/microforum/settings/list') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/posts-icon.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微社区</strong> 
                    <p class="f12 gray_999">打造人气移动社区，增加...</p>
                </div>
            </a>
        </li> 
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/discount') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>限时折扣</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/gift') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>赠品</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/cutsBuy') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>降价拍</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/orderCash') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>订单返现</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/payGift') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>支付有礼</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="">
            <a class="display_box" href="{{ URL('/merchants/marketing/seckills') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/seckill-icon.png" width="44" height="44" />
                <div class="list_content">
                    <strong>秒杀</strong> 
                    <p class="f12 gray_999">快速抢购引导顾客更多消费</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/egg/index') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/egg.png" width="44" height="44" />
                <div class="list_content">
                    <strong>砸金蛋</strong> 
                    <p class="f12 gray_999">好蛋砸出来</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/shareEvent/list') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/xiang.jpg" width="44" height="44" />
                <div class="list_content">
                    <strong>享立减</strong> 
                    <p class="f12 gray_999">用户点击分享链接可减钱的新玩法</p>
                </div>
            </a>
        </li>
        @if($showLi == 1)
        <li>
            <a class="display_box" href="{{ URL('/merchants/share/event/index') }}">
                <img src="{{ config('app.source_url') }}mctsource/images/xiang.jpg" width="44" height="44" />
                <div class="list_content">
                    <strong>享立减二期</strong> 
                    <p class="f12 gray_999">用户点击分享链接可减钱的新玩法</p>
                </div>
            </a>
        </li>
        @endif
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/packagebuy') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>优惠套餐</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/bale') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>打包一口价</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/bargain') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>砍价</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li class="hide">
            <a class="display_box" href="{{ URL('/merchants/marketing/score') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>积分</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
    </ul>

    
    <!-- 列表 结束 -->
    <!-- 列表名 开始 -->
    <strong class="mgb15 hide">店铺扩展</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb30 hide">
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/shopReceivables') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微商城收款</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/verifycard') }}" >
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>卡券验证</strong>
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/salesman') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>销售员</strong>
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/hotel') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>酒店预订</strong>
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    <!-- 列表名 开始 -->
    <strong class="mgb15 hide">互动营销</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb30 hide">
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>刮刮卡</strong>
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/apps/wheel') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>幸运大抽奖</strong>
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="{{ URL('/merchants/marketing/apps/egg') }}">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>砸金蛋</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>大转盘</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    <!-- 列表名 开始 -->
    <strong class="mgb15 hide">游戏插件</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb30 hide">
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    <!-- 列表名 开始 -->
    <strong class="mgb15 hide">第三方应用</strong>
    <!-- 列表名 结束 -->
    <!-- 列表 开始 -->
    <ul class="list_items mgb30 hide">
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
        <li>
            <a class="display_box" href="javascript:void(0);">
                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/62441b6cf4b1d2c2f00d7e603fff147b.png" width="44" height="44" />
                <div class="list_content">
                    <strong>微信公众号</strong> 
                    <p class="f12 gray_999">连接公众号，玩转微信生态</p>
                </div>
            </a>
        </li>
    </ul>
    <!-- 列表 结束 -->
    <!-- 列表 结束 -->    
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script>
    //updata by 邓钊 2018-7-30
    var isBindWechat = "{{$isBindWechat}}"
</script>
<script src="{{ config('app.source_url') }}mctsource/js/marketing_uynh7ai2.js"></script>
@endsection
