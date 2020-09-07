@extends('shop.common.template') @section('head_css')
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?v=111">
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/bookGroupDetail.css?v=1.0" media="screen">
<style type="text/css">
    .server-wrap::after{
        border:none;
    }
</style>
@endsection @section('main')
<div class='container' id="app" v-clock>
    <div class='content' v-if="typeof list.rule !='undefined'">
        <!--商品信息开始 (团详情状态1 支付成功后显示的样式) -->
        <div v-if="group_type==1">
            <div class='gp-success-wrap'>
                待成团
                <span class="gp-icon gp-wait-icon"></span>
            </div>
            <div class='gp-people-wrap ' style="padding:27px 0 20px;">
                <div class='gp-people-head ' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num >=10 && index <= 6">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <!-- 拼团人数大于10 拼团数量大于7 -->
                    <div class='gp-people-head-item nobody small-height' v-if="list.groupsDetail.length > 7 && list.rule.groups_num >=10">?</div>
                    <!-- 拼团人数大于10 拼团数量小于7 -->
                    <div v-if="list.groupsDetail.length <= 7 && list.rule.groups_num >=10">
                    	<div class='gp-people-head-item nobody small-height' v-for="item in (10-list.groupsDetail.length)">?</div>
                    </div>
                    <!-- 拼团人数大于等于6小于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num >=6 && list.rule.groups_num < 10">
                        <span class='colonel  colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <!-- 拼团人数大于等于6小于10 -->                                    
                    <!-- 拼团人数小于等于5 -->
                    <div class='gp-people-head-item' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num <= 5">
                        <span class='colonel' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon' />
                    </div>
                    <!-- 拼团人数小于5 -->  
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7">共@{{list.groupsDetail.length}}人参团</div>
                </div>
                <p class='gp-success-tip'>还差&nbsp; <span class="light-show">@{{list.rule.groups_num-list.groups.num}}人</span> &nbsp;成团</p>
                <div class='gp-success-btn-wraper'>
                    <div @click="setShowShare" class="gp-index-btn">立即分享</div>
                </div>
            </div>
            
            <!-- <div class='gp-people-tip t-c999 bg-white' style='margin-top:0;padding-bottom:15px;line-height:28px;font-size:16px'>
                分享到<span style="color:#B1292D;font-size:22px">&nbsp;3&nbsp;</span>个群后，成功率高达<span style="color:#B1292D;font-size:22px">&nbsp;98%&nbsp;</span>
            </div> -->
            <!--团购人员 -->
            
            <!--商品信息 -->
            <div class="pg-group-desc">
                <div class='gp-goods-wrap youjianhao' @click="gotoDetail">
                    <div class='gp-goods-other'>
                        商品名称：
                    </div>
                    <div class='gp-goods-title' style='padding-right:10px;'>
                        @{{list.rule.title}}
                    </div>
                </div>
                <div class='b-line-e5e5e5'></div>
                <div class='gp-goods-wrap'>
                    <div class='gp-goods-other'>
                        参加时间：
                    </div>
                    <div class='gp-goods-title'>
                        @{{list.groups.join_time}}
                    </div>
                </div>
                <div class='b-line-e5e5e5'></div>
                <!--拼团须知 -->
                <div class='gp-explain-wrap' @click="setShowNotice">
                    <div class='gp-explain-title '>
                        拼团须知
                    </div>
                    <div class='gp-explain-info '>
                        <span class='gp-explain-item '>
                            好友拼团
                        </span>
                        <span class='gp-explain-item '>
                            人满发货
                        </span>
                        <span class='gp-explain-item '>
                            人不满退款
                        </span>
                    </div>
                </div>
            </div>
            
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态2 分享页面进来显示的样式） -->
        <div v-if="group_type==2">
            <div class='goods-wrap2'>
                <div @click="gotoDetail">
                    <img :src="imgUrl + list.rule.product.img" class='buy-success-img' />
                </div>
                <div class='goods-info-wrap2 '>
                    <div class='goods-info-title2 '>
                        <span class='goods-info-label' v-if="list.rule.label">
                            @{{list.rule.label}}
                        </span> @{{list.rule.title}}
                    </div>
                    <div class='goods-info-explain2 ' v-if="list.rule.subtitle">
                        @{{list.rule.subtitle}}
                    </div>
                    <div class='goods-info-price2 '>
                        <span style="font-size:14px">拼团价：</span><span>￥@{{list.rule.min}}</span>
                    </div>
                    <div class='goods-info-other2 '>
                        <span class="normal-red">@{{list.rule.groups_num}}</span>人团  (拼团省@{{list.rule.save}}元)
                        <span class="goods-info-rt">
                            已成团:@{{list.groups.pnum}}件
                        </span>
                    </div>
                </div>
            </div>
            <div class='server-wrap'>
                <div class='server-wrap-item' v-for="(item,index) in list.weixinLable.content">@{{item.title}}</div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap '>
                <div class='gp-people-head ' @click="setShowPeople">
                    <div v-if="list.groupsDetail.length <= 7 && list.rule.groups_num >=10">
                    	<div class='gp-people-head-item nobody small-height' v-for="item in (10-list.groupsDetail.length)">?</div>
                    </div>
                    <div class='gp-people-head-item' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num <= 5">
                        <span class='colonel' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon' />
                    </div>                   
                </div>
                <div class='gp-success-tip '>
                    仅剩
                    <span class='normal-red '>@{{list.rule.groups_num - list.groups.num}}</span> 个名额，@{{groupEtime}}后结束
                </div>
                <div class='gp-success-btn-wraper'>
                    <div class='gp-index-btn' @click="groupPurchaseBuy1">一&nbsp;键&nbsp;参&nbsp;团</div>
                </div>
            </div>
           
            <!--拼团须知 -->
            <div class='gp-explain-wrap' @click="setShowNotice">
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        人满发货
                    </span>
                    <span class='gp-explain-item '>
                        人不满退款
                    </span>
                </div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态3 拼团成功显示的样式） -->
        <div v-if="group_type==3">
            <!--拼团成功 -->
            <div class='gp-success-wrap'>
                恭喜您拼团成功！
                <span class="gp-icon gp-success-icon"></span>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap '>
                <div class='gp-people-head' @click="setShowPeople">
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num >=6 && list.rule.groups_num < 10">
                        <span class='colonel  colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <!-- 拼团人数小于等于5 -->
                    <div class='gp-people-head-item' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num <= 5">
                        <span class='colonel' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon' />
                    </div>
                </div>
                <p class='gp-success-tip'>商家正努力发货，请耐心等待</p>
                <div class='gp-success-btn-wraper'>
                    <a href="/shop/index/{{session('wid')}}" class="gp-index-btn">去首页逛逛</a>
                </div>
            </div>
            <!--商品信息 -->
            <div class="pg-group-desc">
                <div class='gp-goods-wrap youjianhao mtr20' @click="gotoDetail">
                    <div class='gp-goods-other'>
                        商品名称：
                    </div>
                    <div class='gp-goods-title' style='padding-right:10px;'>
                        @{{list.rule.title}}
                    </div>
                </div>
                <div class='b-line-e5e5e5' v-if="list.order.address_id">
                </div>
                <div class='gp-goods-wrap' v-if="list.order.address_id">
                    <div class='gp-goods-other'>
                        收货人：
                    </div>
                    <div class='gp-goods-title' v-if="list.order.address_id">
                        @{{list.order.address_name}}&nbsp;@{{list.order.address_phone}}
                    </div>
                </div>
                <div class='b-line-e5e5e5' v-if="list.order.address_id"></div>
                <div class='gp-goods-wrap youjianhao' @click="gotoOrderDetail" v-if="list.order.address_id">
                    <div class='gp-goods-other'>
                        收货地址：
                    </div>
                    <div class='gp-goods-title' style='padding-right:10px;'>
                        @{{list.order.address_detail}}
                    </div>
                </div>
                <div class='b-line-e5e5e5'></div>
                <div class='gp-goods-wrap'>
                    <div class='gp-goods-other'>
                        成团时间：
                    </div>
                    <div class='gp-goods-title'>
                        @{{list.groups.complete_time}}
                    </div>
                </div>
                <div class='b-line-e5e5e5'></div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态4 拼团失败显示的样式) -->
        <div v-if="group_type==4">
            <div class='goods-wrap2'>
                <div>
                    <img :src="imgUrl + list.rule.product.img" class='goods-img' />
                </div>
                <div class='goods-info-wrap2 '>
                    <div class='goods-info-title2 '>
                        <span class='goods-info-label' v-if="list.rule.label">
                            @{{list.rule.label}}
                        </span> @{{list.rule.title}}
                    </div>
                    <div class='goods-info-explain2 ' v-if="list.rule.subtitle">
                        @{{list.rule.subtitle}}
                    </div>
                    <div class='goods-info-price2 '>
                        <span style="font-size:14px">拼团价：</span><span>￥@{{list.rule.min}}</span>
                    </div>
                    <div class='goods-info-other2 '>
                        <span class="normal-red">@{{list.rule.groups_num}}</span>人团  (拼团省@{{list.rule.save}}元)
                        <span class="goods-info-rt">
                            已成团:@{{list.groups.pnum}}件
                        </span>
                    </div>
                </div>
            </div>
            <div class='server-wrap'>
                <div class='server-wrap-item' v-for="(item,index) in list.weixinLable.content">@{{item.title}}</div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap'>
                <div class='gp-people-head' @click="setShowPeople">
                    <div v-if="list.groupsDetail.length <= 7 && list.rule.groups_num >=10">
                    	<div class='gp-people-head-item nobody small-height' v-for="item in (10-list.groupsDetail.length)">?</div>
                    </div>
                    <!-- 拼团人数大于等于6小于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num >=6 && list.rule.groups_num < 10">
                        <span class='colonel  colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <!-- 拼团人数大于等于6小于10 -->                   
                    <!-- 拼团人数小于等于5 -->
                    <div class='gp-people-head-item' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num <= 5">
                        <span class='colonel' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon' />
                    </div>
                    <!-- 拼团人数小于5 -->
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7">共@{{list.groupsDetail.length}}人参团</div>
                </div>
                <div class='gp-success-tip normal-red'>
                    拼团不成功，款项将原路返回！
                </div>
                <div class='gp-success-btn-wraper '>
                    <div class='gp-index-btn' @click="groupPurchaseBuy">@{{list.rule.is_over==1?'去首页逛逛':'我来开这个团'}}</div>
                </div>
            </div>
            <!--拼团须知 -->
            <div class='gp-explain-wrap' @click="setShowNotice">
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        人满发货
                    </span>
                    <span class='gp-explain-item '>
                        人不满退款
                    </span>
                </div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态5 拼团人员已满） -->
        <div v-if="group_type==5">
            <div class='goods-wrap2' @click="gotoDetail">
                <div>
                    <img :src="imgUrl + list.rule.product.img" class='goods-img'>
                    </img>
                </div>
                <div class='goods-info-wrap2 '>
                    <div class='goods-info-title2 '>
                        <span class='goods-info-label' v-if="list.rule.label">
                            @{{list.rule.label}}
                        </span> @{{list.rule.title}}
                    </div>
                    <div class='goods-info-explain2 ' v-if="list.rule.subtitle">
                        @{{list.rule.subtitle}}
                    </div>
                    <div class='goods-info-price2 '>
                        <span style="font-size:14px">拼团价：</span><span>￥@{{list.rule.min}}</span>
                    </div>
                    <div class='goods-info-other2 '>
                        <span class="normal-red">@{{list.rule.groups_num}}</span>人团  (拼团省@{{list.rule.save}}元)
                        <span class="goods-info-rt">
                            已成团:@{{list.groups.pnum}}件
                        </span>
                    </div>
                </div>
            </div>
            <div class='server-wrap'>
                <div class='server-wrap-item' v-for="(item,index) in list.weixinLable.content">@{{item.title}}</div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap'>
                <div class='gp-people-head' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num >=10 && index <= 7">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <!-- 拼团人数大于等于6小于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num >=6 && list.rule.groups_num < 10">
                        <span class='colonel  colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <!-- 拼团人数小于等于5 -->
                    <div class='gp-people-head-item' v-for="(item,index) in list.groupsDetail" v-if="list.rule.groups_num <= 5">
                        <span class='colonel' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon' />
                    </div>
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7">共@{{list.groupsDetail.length}}人参团</div>
                </div>
                <div class='gp-success-tip '>团已满</div>
                <div class='gp-success-btn-wraper'>
                    <div class='gp-index-btn' @click="groupPurchaseBuy2">一键开团</div>
                </div>
            </div>
            <!--参与别人的团 -->
            <div class='others-group-wrap' v-if="gpList.num>0">
                <div class='others-group-title'>
                    <span class='others-group-title-line line-left'>
                    </span> 或参加别人的团
                    <span class='others-group-title-line line-right'>
                    </span>
                </div>
                <div class='others-group-list'>
                    <div class='others-group-item' v-for="(item,index) in gpList.data" v-if="index<2">
                        <img class='others-group-head' :src="item.headimgurl" />
                        <div class='others-group-info'>
                            <div>@{{item.nickname}}</div>
                            <div class='t-c999 mtr10'>开团中</div>
                        </div>
                        <div class='others-group-other'>
                            <div class='t-red'>
                                还差@{{item.num}}人
                            </div>
                            <div class='t-c999 mtr10'>
                                剩余@{{item.end_time}}
                            </div>
                        </div>
                        <a :href="'/shop/grouppurchase/groupon/'+item.id+'?group_type=2'">
                            <button class='others-group-btn'>去参团</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--拼团须知 -->
            <div class='gp-explain-wrap' @click="setShowNotice">
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        人满发货
                    </span>
                    <span class='gp-explain-item '>
                        人不满退款
                    </span>
                </div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--邀请拼团弹窗 -->
        <div class='gp-invite-wrap' v-if="isShowInvite">
            <div class='t-mask' @click="setShowInvite">
            </div>
            <div class='gp-invite-content'>
                <img :src="imgUrl + list.rule.product.img" class='gp-invite-img' />
                <div class='gp-invite-title'>
                    还差
                    <span class='t-red'>
                        @{{list.rule.groups_num - list.groups.num}}
                    </span> 人,@{{groupEtime}}后结束
                </div>
                <div class='gp-invite-tip'>
                    分享到3个群后，成功率高达98%
                </div>
                <div class='gp-invite-btnwrap'>
                    <button class='btn-red gp-invite-btn' @click="setShowShare">邀请好友参团</button>
                </div>
            </div>
        </div>
        <!--拼团成员弹窗 -->
        <div class='group-people-wrap' v-if="isShowPeople">
            <!--遮罩 -->
            <div class='t-mask' @click="setShowPeople">
            </div>
            <!--内容 -->
            <div class='group-people-content'>
                <div class='group-people-info' v-for="(item,index) in list.groupsDetail" v-if="item.is_head=='1'">
                    <div class='group-people-head'>
                        <span class='colonel'>团长</span>
                        <img :src="item.headimgurl" class='group-people-head-icon ' />
                    </div>
                    <div class='group-people-username'>
                        @{{item.nickname}}
                    </div>
                    <div class='group-people-time'>
                        @{{list.groups.open_time}} 开团
                    </div>
                </div>
                <div class='group-people-people' v-for="(item,index) in list.groupsDetail" v-if="item.is_head=='0' && index<3">
                    <img :src='item.headimgurl' class='group-people-people-head' />
                    <div class='group-people-people-username'>
                        @{{item.nickname}}
                    </div>
                    <div class='group-people-people-time'>
                        @{{item.created_at}} 参团
                    </div>
                </div>
            </div>
        </div>
        <!--拼团须知弹窗 -->
        <div class='gp-notice-wrap' v-if="isShowNotice">
            <!--遮罩 -->
            <div class='t-mask' @click="setShowNotice">
            </div>
            <!--内容 -->
            <div class='gp-notice'>
                <div class='gp-notice-title'>
                    <span class='gp-notice-title-line line-left'>
                    </span> 如何开团
                    <span class='gp-notice-title-line line-right'>
                    </span>
                </div>
                <div class='gp-notice-item'>
                    开团或参加别人的团
                </div>
                <div class='gp-notice-item'>
                    在规定的时间内邀请好友拼团
                </div>
                <div class='gp-notice-item'>
                    达到拼团人数分别给团长和团员发货
                </div>
                <div class='gp-notice-item'>
                    未达到拼团人数,货款将原路退还
                </div>
            </div>
        </div>
        <!--you家服务弹框  -->
        <div class='youjia-wrap' :class='[isShowSever? "":"hide"]'>
            <!--you家遮罩  -->
            <div class='youjia-mask' @click="setShowSever"></div>
            <div class='youjia-info-wrap'>
                <div class='youjia-info-title'>@{{list.weixinLable.title}}</div>
                <div class='youjia-list-wrap'>
                    <div class='youjia-list-item' v-for="(item,index) in list.weixinLable.content">
                        <div class='youjia-list-label'>@{{item.title}}</div>
                        <div class='youjia-list-explain'>@{{item.content}}</div>
                    </div>
                </div>
            </div>
        </div>
        <!--推荐 -->
        <div class='recommend-wrap' @if(in_array(session('wid'),config('app.li_wid')))style="display: none" @endif>
            <div class='recommend-title'>
                <img src="{{ config('app.source_url') }}shop/images/biaoti@2x.png" class='recommend-icon'>
                </img>
                <a href="/shop/index/{{session('wid')}}" class='recommend-title-text'>进店逛逛</a>
            </div>
            <!--团购样式4 -->
            <div class='gp-list-wrap list-4'>
                <div class='gp-list-box'>
                    <a class='gp-list-item' v-for="(item,index) in tjList" :href="'/shop/grouppurchase/detail/' + item.id + '/' + item.wid">
                        <div class='gp-list-img-wrap'>
                            <img class='gp-list-img' :src='imgUrl+item.img2' />
                            <div class='gp-list-label' v-if="item.label">@{{item.label}}</div>
                        </div>
                        <div class='gp-list-goods-name'>
                            @{{item.title}}
                        </div>
                        <div class='gp-list-other'>
                            <div class='gp-list-price'>
                                ￥ @{{item.min}}
                            </div>
                            <div class='gp-list-people'>
                                <img v-for="(vo,key) in item.groups.member" v-if="key<2" :src="vo.headimgurl" class='gp-list-people-img' :style='{"right":key==1?"15px;":"0px;"}' />
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="zhezhao" v-if="isShowShare">
            <div class="share_model">
                <img src="{{ config('app.source_url') }}shop/images/share_bg3.png" />
            </div>
            <div class="close_share" v-on:click="setShowShare"></div>
        </div>
    </div>
</div>
@include('shop.common.footer')
@endsection @section('page_js')
<script type="text/javascript">
var wid ="{{session('wid')}}";
var groups_id = "{{$group_id}}";
var _host = "{{ config('app.source_url') }}";
var host = "{{config('app.url')}}";
var imgUrl = "{{ imgUrl() }}";
var isBind = {{$__isBind__}};
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/bookGroupDetail.js"></script>
<script type="text/javascript">
// 微信分享
$(function() {
    var url = location.href.split('#').toString();
    $.get("/home/weixin/getWeixinSecretKey", { "url": url }, function(data) {
        if (data.errCode == 0) {
            wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: data.data.appId, // 必填，公众号的唯一标识
                timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                signature: data.data.signature, // 必填，签名，见附录1
                jsApiList: [
                    'checkJsApi',
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'onMenuShareQQ',
                    'chooseWXPay'
                ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });
        }
    })
    wxShare();

    function wxShare() {
        if (typeof app.list.rule != "undefined") {
            var share_title = app.list.rule.share_title || app.list.rule.title;
            var share_desc = app.list.rule.share_desc || app.list.rule.subtitle;
            var share_img = app.list.rule.share_img ? app.imgUrl + app.list.rule.share_img : app.imgUrl + app.list.rule.img2; 
            var share_url = host + 'shop/grouppurchase/groupon/'+groups_id+'/'+wid+'?group_type=2&_pid_={{session('mid')}}';
            @if($reqFrom == 'aliapp')
            my.postMessage({share_title:share_title,share_desc:share_desc,share_url:share_url,imgUrl:share_img});
            @endif
            @if($reqFrom == 'wechat')
            wx.ready(function() {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: share_title, // 分享标题
                    desc: share_desc, // 分享描述
                    link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: share_img, // 分享图标
                    success: function() {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function() {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: share_title, // 分享标题
                    desc: share_desc, // 分享描述
                    link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: share_img, // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function() {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function() {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享到QQ
                wx.onMenuShareQQ({
                    title: share_title, // 分享标题
                    desc: share_desc, // 分享描述
                    link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: share_img, // 分享图标
                    success: function() {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function() {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: share_title, // 分享标题
                    desc: share_desc, // 分享描述
                    link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: share_img, // 分享图标
                    success: function() {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function() {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.error(function(res) {
                    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                });
            });
            @endif
        } else {
            setTimeout(function() {
                wxShare();
            }, 50)
        }
    }
});
</script>
@endsection
