<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?v=111">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/bookGroupDetail.css?v=1.0" media="screen">
</head>
<body>
<style type="text/css">
    html,body{
        background:#F5F5F5;
    }
    .content{min-height:auto!important}
    .server-wrap::after{
        border:none;
    }
    .sku-layout-title .goods-base-info .goods-price {
        padding: 0 55px 0 0;
        height: 34px;
    }
    /* 弹窗 */
    .pop_up{
        position:fixed;
        top:0;
        left:0;
        height:100%;
        width:100%;
        z-index: 10000000;
    }
    .pop_up .shade{
        background-color:rgba(0,0,0,.8);
        height:100%;
        width:100%
    }
    
    .pop_up .pop_content{
        position:absolute;
        top:0;bottom:0;left:0;right:0;
        margin:auto;
        z-index:100;
        @if(session('wid') == '626' || session('wid') == '661')
        height:480px;
        @elseif(session('wid') == '634')
        height:440px;
        @endif
        width:85%;
        border-radius:5px;
        background:white;
        padding:20px 10px
    }
    .pop_up .attend{
        font-weight:bold;
        padding-top:20px;
    }
    
    .pop_up .title{
        text-align:center;
        font:20px/30px "微软雅黑";
        border-bottom:1px solid #e6e6e6;
        padding-bottom:10px;
        font-weight:bold
    }
    .pop_up .pop_text{
        font:16px/25px "微软雅黑";
        padding-top:10px;
        font-weight:bold;
        text-align:center
    }
    .pop_up .qrcode>div{
        width:180px;
        height:160px;
        margin:5px auto;
        text-align:center;
        display:flex;
        flex-direction:column;
    }
    .pop_up .qrcode>div img{
        width:180px;
        border:none;
        outline:none
    }
    .pop_up .qrcode_tip{
        color:#333;
        font-size:17px;
        display:block;
        font-weight:bold;
        padding:10px 0;
        border-bottom:1px solid #e6e6e6
    }
    .pop_up .code_tip{
        font:18px/40px "微软雅黑";
        font-weight:normal;
    }
    .pop_up .code_step{
        font:16px/22px "微软雅黑";
        text-align:left;
        width:230px;
        position:absolute;
        left:0;
        right:0;
        margin:0 auto
    }
    .pop_up .btn_wrap{
        position:absolute;
        bottom:20px;
        left:0;right:0;
        text-align:center
    }
    .pop_up .btn_wrap>.btn{
        width:60%;
        font:18px/40px "微软雅黑";
        color:white;
        font-weight:bold;
        background:#B0282C;
        border-radius:6px;
    }
    .pop_up .close_btn{
        position:absolute;
        right:10px;
        top:10px;
    }
    .pop_up .close_btn img{width:22px}
    .pop_up .tip_bg{
        padding: 0;
        border-radius: 14px;
        height:300px;
    }
    .pop_up .tip_bg .tip_action_title{
        text-align: center;
        padding-top: 20px;
    }
    .pop_up .tip_bg .tip_action_title a {
        display: inline-block;
        padding: 15px 50px;
        background: #B1292D;
        border-radius: 22px;
        color: #fff;
        margin: 0 auto;
    }
    .pop_up .tip_bg .xx_close{
        width: 31px;
        position: absolute;
        top: 20px;
        right: 30px;
    }
    .pop_up .tip_row{
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
        z-index: 100;
        height:50%;
        border-radius: 5px;
        padding: 20px;
    }
    .pop_up .tip_row .tip_bg1{
        width: 88%;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
    .pop_up .tip_row_a{
        position: absolute;
        width: 43%;
        height: 70px;
        left: 28%;
        bottom: 21%;
    }
    .pop_up .tip_bg .tip_title{
        text-align: center;
        font-size: 16px;
        color: #333;
        padding: 10px;
    }
    .pop_up .tip_bg .tip_title span{
        color:#F33A40;
    }
    .pop_up_click{
        z-index:1000;            
        position:fixed;
        top:0;
        left:0;
        height:100%;
        width:100%;display:none
    }
    .pop_up_click .shade{
        background-color:rgba(0,0,0,.8);
        height:100%;
        width:100%
    }
    
    .pop_up_click .pop_content{
        position:absolute;
        top:0;bottom:0;left:0;right:0;
        margin:auto;
        z-index:100;
        @if(session('wid') == '626' || session('wid') == '661')
        height:480px;
        @elseif(session('wid') == '634')
        height:440px;
        @endif
        width:80%;
        border-radius:5px;
        background:white;
        padding:20px
    }
    .pop_up_click .attend{
        font-weight:bold;
        padding-top:20px;
    }
    
    .pop_up_click .title{
        text-align:center;
        font:20px/30px "微软雅黑";
        border-bottom:1px solid #e6e6e6;
        padding-bottom:10px;
        font-weight:bold
    }
    .pop_up_click .pop_text{
        font:14px/25px "微软雅黑";
        padding-top:20px
        
    }
    .pop_up_click .qrcode>div{
        width:200px;
        height:160px;
        position:absolute;
        left:0;right:0;
        bottom:170px;
        margin:0 auto;
        text-align:center;
        display:flex;
        flex-direction:column;
    }
    .pop_up_click .qrcode>div img{
        width:200px;
        border:none;
        outline:none
    }
    .pop_up_click .qrcode_tip{
        color:#4d4d4d;
        font-size:14px;
        display:block
    }
    .pop_up_click .btn_wrap{
        position:absolute;
        bottom:10px;
        left:0;right:0;
        text-align:center
    }
    .pop_up_click .btn_wrap>.btn{
        width:170px;
        font:16px/40px "微软雅黑";
        color:white;
        background:#B0282C;
        border-radius:6px;
    }
    .pop_up_click .close_btn{
        position:absolute;
        right:10px;
        top:10px;
    }
    .pop_up_click .close_btn img{width:22px}
    .zhezhao .close_share {
        width: 40px;
        height: 40px;
        position: relative;
        top: 18.5%;
        left: 5%;
        margin: 0;
        position: absolute;
    }
    .zhezhao {
        background: rgba(0,0,0,0.7) !important;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 10000000;
    }
    .sku-layout .text-note-label {
        font-size: 16px;
    }
    .sku-layout .txt-note-input {
        padding: 11px;
    }
    .sku-layout .sku-note {
        margin-bottom: 6px;
    }
    .big-btn {
        font-size: 20px;
        font-weight: bold;
        background:#b1292d !important;
    }
    .goods-base-info .groupNum{
        color:#999 !important;
    }
    .goods-base-info .groupNum span{
        font-weight: bold;
        color: #b1292d;
    }
    .sku-layout-title .goods-base-info .current-price {
        line-height: 20px;
        margin-left: 10px;
        font-weight: bold;
    }
    .c-red {
        color: #b1292d !important;
    }
    .zhezhao .share_model img {
        width: 100% !important;
        position: relative;
    }
    .big-btn {
        font-size: 20px;
        font-weight: bold;
    }
    .goods-info-wrap {
        width: calc(100% - 127.5px);
        width: -webkit-calc(100% - 127.5px);
        width: -ms-calc(100% - 127.5px);
        padding-left: 10px !important;
        font-size: 14px;
        box-sizing: border-box;
    }
    .goods-info-price{
        margin-top:10px !important;
    }
    .goods-info-other {
        color: #666 !important;
        padding-left: 3px;
    }
    .goods-info-title {
        font: 18px/24px 'Microsoft YaHei';
        font-weight: bold;
        padding: 5px 0 10px 0;
        font-size:18px !important;
    }
    .hint{
        border-radius: 20px !important;
    }
    .hint span {
        flex: 1;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        -webkit-flex: 1;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        font-weight: bold;
        font-size: 15px;
    }
    .sku-layout {
        overflow: hidden;
        position: absolute;
        z-index: 10000001 !important;
        bottom: 0px;
        left: 0px;
        right: 0px;
        visibility: visible;
        transform: translate3d(0px, 0px, 0px);
        transition: all 300ms ease;
        opacity: 1;
        background: #fff;
    }
    .motify {
        z-index:10000002 !important;
    }
    .gp-explain-title {
        max-width: 60px;
    }
    .gp-explain-info{
        text-align:center;
    }
    .footer{
        margin-top:20px;
    }
    .gp-success-icon {
        width: 60px;
    }
    .gp-success-box {
        text-align: center;
        font-size: 25px;
        color: #47C24E;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .gp-success-tip {
        color: #666;
        text-align: center;
        padding: 10px 0 0;
        font-size: 16px;
        font-weight: bold;
        line-height: 22px;
    }
    .footer_nav{margin-top:10px}
    .footer_nav img{width:100%;display:block}
    .group-people-content{
        height: 300px;
        margin-top: -150px;
    }
    .group-people-wrap{
        z-index:10000001;
    }
    .gp-people-head-item{
        margin-left:-18px;
    }
    .gp-people-head-item:first-child{
        margin-left:0px;
    }
    .sku-layout .block-item{
		max-height:180px;
		overflow:auto;
		padding:20px
	}
    .sku-layout .sku-note {
	    margin-bottom: 16px;
	}
    [v-cloak]{display:none}
</style>
<div class='container' id="app" v-clock>
    <div class='content' v-if="typeof list.rule !='undefined'">
        <!--商品信息开始 (团详情状态1 支付成功后显示的样式) -->
        <div v-if="group_type==1">
            <div class='buy-success-wrap'>
                <img :src="imgUrl + list.rule.product.img" class='buy-success-img' />
            </div>
            <div class='gp-people-tip bg-white' style="margin-top:0;padding:15px 12px 0;font:16px/28px 'Microsoft YaHei'">
                还差
                <span class='t-red ' v-html="list.rule.groups_num-list.groups.num">
                </span> 人，@{{groupEtime}}后结束
            </div>
            <div class='gp-people-btn bg-white' style='border:none;'>
                <button class='gp-btn' @click="setShowShare">
                    邀请好友参团
                </button>
            </div>
            <div class='gp-people-tip t-c999 bg-white' style='margin-top:0;padding-bottom:15px;line-height:28px;font-size:16px'>
                分享到<span style="color:#B1292D;font-size:22px">&nbsp;3&nbsp;</span>个群后，成功率高达<span style="color:#B1292D;font-size:22px">&nbsp;98%&nbsp;</span>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap b-line-e5e5e5' style='margin-top:15px;padding-bottom:15px;'>
                <div class='gp-people-head ' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="index < 10">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num >= list.groupsDetail.length">共@{{list.groupsDetail.length}}人参团</div>
                <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num < list.groupsDetail.length">≥@{{list.rule.groups_num}}人参团</div>
                </div>
            </div>
            <!--商品信息 -->
            <div class='gp-goods-wrap youjianhao' @click="gotoDetail">
                <div class='gp-goods-other'>
                    拼团名称：
                </div>
                <div class='gp-goods-title' style='padding-right:10px;'>
                    @{{list.rule.title}}
                </div>
            </div>
            <div class='b-line-e5e5e5'></div>
            <div class='gp-goods-wrap'>
                <div class='gp-goods-other'>
                    参团时间：
                </div>
                <div class='gp-goods-title'>
                    @{{list.groups.join_time}}
                </div>
            </div>
            <div class='b-line-e5e5e5'></div>
            <!--拼团须知 -->
            <div class='gp-explain-wrap'>
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        活动期间
                    </span>
                    <span class='gp-explain-item '>
                        团长免单
                    </span>
                </div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态2 分享页面进来显示的样式） -->
        <div v-if="group_type==2">
            <div class='goods-wrap'>
                <div class='goods-imgs-wrap ' @click="gotoDetail">
                    <img :src="imgUrl + list.rule.product.img" class='buy-success-img' />
                </div>
                <div class='goods-info-wrap '>
                    <div class='goods-info-title '>
                        <span v-if="list.rule.label" class='goods-info-label' :class="{ 'v-hidden': list.rule.label=='' }">
                            @{{list.rule.label}}
                        </span> @{{list.rule.title}}
                    </div>
                    <div class='goods-info-explain ' v-if="list.rule.subtitle">
                        @{{list.rule.subtitle}}
                    </div>
                    <div class='goods-info-other ' style="font-size:14px">
                        <span>@{{list.rule.groups_num}}人团</span>
                        <span class='mlr10'>
                            已团:@{{list.groups.pnum}}件
                        </span>
                    </div>
                    <div class='goods-info-price '>
                        <span class='goods-price' style="font-size:22px">￥@{{list.rule.min}}</span>
                        <span class='t-c999 mlr10'>
                            拼团省：@{{list.rule.save}}
                        </span>
                    </div>
                </div>
            </div>
            <!--服务 -->
            <div class='server-wrap'>
                <div class='server-wrap-item' v-for="(item,index) in list.weixinLable.content">@{{item.title}}</div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap '>
                <div class='gp-people-head ' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="index < 10">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num >= list.groupsDetail.length">共@{{list.groupsDetail.length}}人参团</div>
                <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num < list.groupsDetail.length">≥@{{list.rule.groups_num}}人参团</div>
                </div>
                <div class='gp-people-tip ' style="font-size:14px" v-if="list.addGroupsNum <= 0">
                    仅剩
                    <span class='t-red '>@{{list.rule.groups_num - list.groups.num}}</span> 个名额，@{{groupEtime}}后结束
                </div>
                <div class='gp-people-tip ' style="font-size:14px" v-if="list.addGroupsNum > 0">
                    您只能帮一个人拼团哦，可以继续助力好友拼团~
                </div>
            </div>
            <div class='gp-people-btn bg-white'>
                <button class='gp-btn ' @click="groupPurchaseBuy1" v-if="list.addGroupsNum <= 0 && list.rule.state == 1">帮朋友0元凑团</button>
                <button class='gp-btn ' style="background:#999" v-if="list.addGroupsNum <= 0 && list.rule.state != 1">活&nbsp;动&nbsp;结&nbsp;束</button>
                <button class='gp-btn' @click="groupPurchaseBuy2" v-if="list.addGroupsNum > 0  && list.rule.state == 1 && list.groups.status != 1">一&nbsp;键&nbsp;开&nbsp;团</button>
                <button class="gp-btn" v-if="list.groups.status == 1 && list.addGroupsNum > 0" @click="setShowShare">助力好友凑团</button>
                <button class='gp-btn' style="background:#999" v-if="list.addGroupsNum > 0  && list.rule.state != 1">活&nbsp;动&nbsp;结&nbsp;束</button>
            </div>
            <!--拼团须知 -->
            <div class='gp-explain-wrap'>
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        活动期间
                    </span>
                    <span class='gp-explain-item '>
                        团长免单
                    </span>
                </div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态3 拼团成功显示的样式） -->
        <div v-if="group_type==3">
            <!--拼团成功 -->
            <div class='gp-success-wrap'>
                <div class='gp-success-box'>
                    <img src="{{ config('app.source_url') }}shop/images/success_big@2x.png" class='gp-success-icon' />
                    <span style='margin-left:10px;'>拼团成功</span>
                </div>
                <div class='gp-success-tip'>
                    温馨提示：我们将会通过手机短信推送给您拼团结果，请注意及时查看。
                </div>
                <div class='gp-people-btn' style='border:none;margin-top:40px'>
                    <a :href="'/shop/meeting/detail/' + ruleId + '/' + wid">
                        <button class='gp-btn'>继续发起拼团</button>
                    </a>
                </div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap ' style="padding:5px 0 15px;">
                <div class='gp-people-head' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="index < 10">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num >= list.groupsDetail.length">共@{{list.groupsDetail.length}}人参团</div>
                <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num < list.groupsDetail.length">≥@{{list.rule.groups_num}}人参团</div>
                </div>
            </div>
            <!--商品信息 -->
            <div class='gp-goods-wrap youjianhao mtr20' @click="gotoDetail">
                <div class='gp-goods-other'>
                    拼团名称：
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
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态4 拼团失败显示的样式) -->
        <div v-if="group_type==4">
            <div class='goods-wrap'>
                <div class='goods-imgs-wrap '>
                    <img :src="imgUrl + list.rule.product.img" class='goods-img' />
                </div>
                <div class='goods-info-wrap '>
                    <div class='goods-info-title '>
                        <span v-if="list.rule.label" class='goods-info-label' :class="{ 'v-hidden': list.rule.label=='' }">
                            @{{list.rule.label}}
                        </span> @{{list.rule.title}}
                    </div>
                    <div v-if="list.rule.subtitle" class='goods-info-explain '>
                        @{{list.rule.subtitle}}
                    </div>
                    <div class='goods-info-other '>
                        <span>
                            @{{list.rule.groups_num}}人团
                        </span>
                        <span class='mlr10'>
                            已团:@{{list.groups.pnum}}件
                        </span>
                    </div>
                    <div class='goods-info-price '>
                        <span class='goods-price' style="font-size:22px">￥@{{list.rule.min}}</span>
                        <span class='t-c999 mlr10'>
                            拼团省：@{{list.rule.save}}
                        </span>
                    </div>
                </div>
            </div>
            <!--服务 -->
            <div class='server-wrap'>
                <div class='server-wrap-item' v-for="(item,index) in list.weixinLable.content">@{{item.title}}</div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap'>
                <div class='gp-people-head' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="index < 10">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num >= list.groupsDetail.length">共@{{list.groupsDetail.length}}人参团</div>
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num < list.groupsDetail.length">≥@{{list.rule.groups_num}}人参团</div>
                </div>
                <div class='gp-people-tip t-red'>
                    拼团不成功，款项将原路返回！
                </div>
                <div class='gp-people-btn '>
                    <button class='gp-btn' @click="groupPurchaseBuy">@{{list.rule.is_over==1?'继续发起拼团':'我来开这个团'}}</button>
                </div>
            </div>
            <!--拼团须知 -->
            <div class='gp-explain-wrap'>
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        活动期间
                    </span>
                    <span class='gp-explain-item '>
                        团长免单
                    </span>
                </div>
            </div>
        </div>
        <!--商品信息结束 -->
        <!--商品信息开始 (团详情状态5 拼团人员已满） -->
        <div v-if="group_type==5">
            <div class='goods-wrap' @click="gotoDetail">
                <div class='goods-imgs-wrap '>
                    <img :src="imgUrl + list.rule.product.img" class='goods-img'>
                    </img>
                </div>
                <div class='goods-info-wrap '>
                    <div class='goods-info-title '>
                        <span v-if="list.rule.label" class='goods-info-label' :class="{ 'v-hidden': list.rule.label=='' }">
                            @{{list.rule.label}}
                        </span> @{{list.rule.title}}
                    </div>
                    <div v-if="list.rule.subtitle" class='goods-info-explain '>
                        @{{list.rule.subtitle}}
                    </div>
                    <div class='goods-info-other '>
                        <span>
                            @{{list.rule.groups_num}}人团
                        </span>
                        <span class='mlr10'>
                            已团:@{{list.groups.pnum}}件
                        </span>
                    </div>
                    <div class='goods-info-price '>
                        <span class='goods-price' style="font-size:22px">
                            ￥@{{list.rule.min}}
                        </span>
                        <span class='t-c999 mlr10'>
                            拼团省：@{{list.rule.save}}
                        </span>
                    </div>
                </div>
            </div>
            <!--服务 -->
            <div class='server-wrap'>
                <div class='server-wrap-item' v-for="(item,index) in list.weixinLable.content">@{{item.title}}</div>
            </div>
            <!--团购人员 -->
            <div class='gp-people-wrap'>
                <div class='gp-people-head' @click="setShowPeople">
                    <!-- 拼团人数大于10 -->
                    <div class='gp-people-head-item small-height' v-for="(item,index) in list.groupsDetail" v-if="index < 10">
                        <span class='colonel colonel_small' v-if="item.is_head==1">团长</span>
                        <img :src="item.headimgurl" class='gp-people-head-icon small-height' />
                    </div>
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num >= list.groupsDetail.length">共@{{list.groupsDetail.length}}人参团</div>
                    <div class="total-group" v-if="list.rule.groups_num >=10 && list.groupsDetail.length > 7 && list.rule.groups_num < list.groupsDetail.length">≥@{{list.rule.groups_num}}人参团</div>
                </div>
                <div class='gp-people-tip ' v-if= "list.rule.groups_num >=4">团已满(最多只显示10人)</div>
                <div class='gp-people-btn' style='border:none;'>
                    <button class='gp-btn' @click="groupPurchaseBuy2" v-if="list.rule.state == 1">一键开团</button>
                    <button class='gp-btn' style="background:#999" v-if="list.rule.state != 1">活动结束</button>
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
            <div class='gp-explain-wrap'>
                <div class='gp-explain-title '>
                    拼团须知
                </div>
                <div class='gp-explain-info '>
                    <span class='gp-explain-item '>
                        好友拼团
                    </span>
                    <span class='gp-explain-item '>
                        活动期间
                    </span>
                    <span class='gp-explain-item '>
                        成员免单
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
                <div class='group-people-people' v-for="(item,index) in list.groupsDetail" v-if="item.is_head=='0'">
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
        <div class="zhezhao" v-on:click="setShowShare" v-if="isShowShare">
            <div class="share_model">
            @if(session('wid')== '661')
                <img src="{{ config('app.source_url') }}shop/images/pintuanshare077.png" />
                @elseif(session('wid') == '626')
                <img src="{{ config('app.source_url') }}shop/images/pintuanshare626.png?t=123" />
                @else
                <img src="{{ config('app.source_url') }}shop/images/pintuanshare066.png" />
            @endif
            </div>
            <div class="close_share"></div>
        </div>
        <!-- 弹窗 -->
        <div class="pop_up" v-if="showModel">
            <div class="shade" @click="hideModel">
            </div>
            <div class="pop_content">
                <div class="title">恭喜您拼团成功！</div>
                <a class="close_btn" @click="hideModel"><img src="{{ config('app.source_url') }}/shop/static/images/close@2x.png" alt=""/></a>
                <div class="pop_text">
                    <p>了解更多详情请关注</p>
                    <div class="qrcode">
                        <div>
                           @if(session('wid')== '634')
                            <img src="{{ config('app.source_url') }}/shop/images/hsqrcode1.jpg" alt="" class="qrcodeImage">
                            @elseif(session('wid') == '626' || session('wid') == '661')
                            <img src="{{ config('app.source_url') }}/shop/images/hsqrcode626.jpg" alt="" class="qrcodeImage">
                            @endif
                        </div>
                        <p class="qrcode_tip">长按图片【识别二维码】关注公众号</p>
                        <p class="code_tip">关注公众号方式</p>
                        <ol class="code_step">
                            <li>1.打开微信，点击“添加朋友”</li>
                            <li>2.点击公众号</li>
                            @if(session('wid')== '634')
                            <li>3.搜索“杭州会搜股份”</li>
                            @elseif(session('wid') == '626' || session('wid') == '661')
                            <li>3.搜索“会搜商业智慧”</li>
                            @endif
                            <li>4.点击“关注”，完成</li>
                        </ol>
                    </div>
                    @if(session('wid') == '626' || session('wid') == '661')
                    <div class="btn_wrap"><a href="https://hsxy.huisou.cn/sxyback/html/course.html" class="btn Bred">报名现场学习</a></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="pop_up" v-if="showModel1">
            <div class="shade" @click="hideModel">
            </div>
            <div class="pop_content">
                <div class="title">恭喜您分享成功！</div>
                <a class="close_btn" @click="hideModel"><img src="{{ config('app.source_url') }}/shop/static/images/close@2x.png" alt=""/></a>
                <div class="pop_text">
                    <p>了解更多详情请关注</p>
                    <div class="qrcode">
                        <div>
                            @if(session('wid')== '634')
                            <img src="{{ config('app.source_url') }}/shop/images/hsqrcode1.jpg" alt="" class="qrcodeImage">
                            @elseif(session('wid') == '626' || session('wid') == '661')
                            <img src="{{ config('app.source_url') }}/shop/images/hsqrcode626.jpg" alt="" class="qrcodeImage">
                            @endif
                        </div>
                        <p class="qrcode_tip">长按图片【识别二维码】关注公众号</p>
                        <p class="code_tip">关注公众号方式</p>
                        <ol class="code_step">
                            <li>1.打开微信，点击“添加朋友”</li>
                            <li>2.点击公众号</li>
                            @if(session('wid')== '634')
                            <li>3.搜索“杭州会搜股份”</li>
                            @elseif(session('wid') == '626' || session('wid') == '661')
                            <li>3.搜索“会搜商业智慧”</li>
                            @endif
                            <li>4.点击“关注”，完成</li>
                        </ol>
                    </div>
                    @if(session('wid') == '626' || session('wid') == '661')
                    <div class="btn_wrap"><a href="https://hsxy.huisou.cn/sxyback/html/course.html" class="btn Bred">报名现场学习</a></div>
                    @endif
                </div>
            </div>
        </div>
        <!-- 弹窗 -->
        <div class="pop_up" v-if="show_tip">
            <div class="shade" style="background-color: rgba(0,0,0,0.6);">
            </div>
            <div class="tip_bg">
                <div class="tip_row">
                    <img class="tip_bg1" src="{{ config('app.source_url') }}shop/images/tip_bg1.png">
                    <img @click="hideModel" class="xx_close" src="{{ config('app.source_url') }}shop/images/xx2.png">
                    <a class="tip_row_a" href="/shop/meeting/groups/showMyGroups/{{session('wid')}}"></a>   
                </div>
            </div>
        </div>
        @include('shop.common.meetingBottom')
    </div>
    <div class="footer_nav" v-cloak v-if="wid == 626">
        <img src="{{ config('app.source_url') }}shop/static/images/join011_626.jpg?000">
        <img src="{{ config('app.source_url') }}shop/static/images/join022_626.jpg?0000">
        <img src="{{ config('app.source_url') }}shop/static/images/join033_626.jpg?444">
        <img src="{{ config('app.source_url') }}shop/static/images/join044_626.jpg?444">
        <img src="{{ config('app.source_url') }}shop/static/images/join055_626.jpg?9999">
        <img src="{{ config('app.source_url') }}shop/static/images/join066_626.jpg?1111">
    </div>
    <div class="footer_nav" v-cloak v-if="wid == 634">
        <img src="{{ config('app.source_url') }}shop/static/images/join01.jpg?000">
        <img src="{{ config('app.source_url') }}shop/static/images/join02.jpg?0000">
        <img src="{{ config('app.source_url') }}shop/static/images/join03.jpg?444">
        <img src="{{ config('app.source_url') }}shop/static/images/join04.jpg?444">
        <img src="{{ config('app.source_url') }}shop/static/images/join05.jpg?9999">
        <a href="https://www.huisou.cn/shop/microPage/index/626/3866">
            <img src="{{ config('app.source_url') }}shop/static/images/join06.jpg?0000">    
        </a>
        <a href="https://www.huisou.cn/shop/microPage/index/626/3520">
            <img src="{{ config('app.source_url') }}shop/static/images/join07.jpg?3333">   
        </a>
        <img src="{{ config('app.source_url') }}shop/static/images/join08.jpg?9999">
        <a href="https://www.huisou.cn/shop/microPage/index/634/4718">
            <img src="{{ config('app.source_url') }}shop/static/images/join09.jpg?5555">   
        </a>
        <img src="{{ config('app.source_url') }}shop/static/images/join10.jpg?9999">
    </div>
    <div class="footer_nav" v-cloak v-if="wid == 661">
        <img src="{{ config('app.source_url') }}shop/static/images/meeting1.jpg?000">
        <img src="{{ config('app.source_url') }}shop/static/images/meeting2.jpg?0000">
        <img src="{{ config('app.source_url') }}shop/static/images/meeting3.jpg?444">
        <img src="{{ config('app.source_url') }}shop/static/images/meeting4.jpg?444">
        <img src="{{ config('app.source_url') }}shop/static/images/meeting5.jpg?9999">
        <img src="{{ config('app.source_url') }}shop/static/images/meeting6.jpg?9999">
        <a href="https://www.huisou.cn/shop/microPage/index/661/4389">
            <img src="{{ config('app.source_url') }}shop/static/images/meeting7.jpg?0000">    
        </a>
        <img src="{{ config('app.source_url') }}shop/static/images/meeting8.jpg?9999">
    </div>
</div>
@include('shop.common.footerMeeting')
<script type="text/javascript">
    var APP_HOST = "{{ config('app.url') }}";
    var APP_IMG_URL = "{{ imgUrl() }}";
    var APP_SOURCE_URL = "{{ config('app.source_url') }}";
    var CHAT_URL = "{{config('app.chat_url')}}";
</script>
@if(config('app.env') == 'prod')
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
@endif
@if(config('app.env') == 'dev')
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
@endif
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script type="text/javascript">
var wid ="{{session('wid')}}";
var groups_id = "{{$group_id}}";
var _host = "{{ config('app.source_url') }}";
var host = "{{config('app.url')}}";
var imgUrl = "{{ imgUrl() }}";
var isBind = 0;
</script>
<script src="{{ config('app.source_url') }}shop/js/meeting_until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/meetingBookGroupDetail.js"></script>
<script type="text/javascript">
// 微信分享
$(function() {
    tool.applyPhonex();
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
            var share_url = host + 'shop/meeting/groupon/'+groups_id+'/'+wid+'?group_type=2&_pid_={{session('mid')}}';
            wx.ready(function() {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: share_title, // 分享标题
                    desc: share_desc, // 分享描述
                    link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: share_img, // 分享图标
                    success: function() {
                        // 用户确认分享后执行的回调函数
                        app.$set(app.$data,'showModel1',true)
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
                        app.$set(app.$data,'showModel1',true)
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
                        app.$set(app.$data,'showModel1',true)
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
                        app.$set(app.$data,'showModel1',true)
                    },
                    cancel: function() {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.error(function(res) {
                    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                });
            });
        } else {
            setTimeout(function() {
                wxShare();
            }, 50)
        }
    }
});
</script>
</body>
</html>