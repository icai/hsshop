<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=yes" />
    <title>{{ $title or '' }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css?v=123"> 
	<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/detail.css?v=1.0.1">
	<style type="text/css">
        body{
          -webkit-overflow-scrolling : touch;
        }
		.hint{
			top:7%
		}
		.hint1{
			top:15%
		}
	    .custom-image-swiper {
		    width: 100%;
		    position: relative;
		} 
		.swiper-container {
			width:100%;
		}
		.group_info {
			text-align: center;
		    font-size: 18px;
		    color: #333;
		    background: #fff;
		    padding: 15px 0px;
		    border-top: 1px solid #E6E6E6;
		}
		.my_group {
			display: -webkit-box;
			display: -moz-box;
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;
		    align-items: center;
		    background: #fff;
		    margin: 10px 0 10px 0;
		    position:relative;
		}
		.my_group .arrow{
			position: absolute;
		    right: 15px;
		    top: 50%;
		    margin-top: -9px;
		}
		.my_group .arrow img{
			width: 16px;
		}
		.my_group .img{
			height: 60px;
		    margin: 10px;
		    width: 60px;
		    border-radius: 100%;
		    overflow: hidden;
		}
		.my_group .img img{
			height: 100%;
		}
		.my_group p{
			color:#333;
			font-size:16px;
			font-weight: bold;
			line-height: 80px;
		}
		#app{
			margin-bottom: 0;
		}
		.group_info .groups_num{
			color:#B0282C;
		}
		.swiper-slide img{width:100%;height:auto;}
		.sku-layout-title .goods-base-info .goods-price {
		    padding: 0 55px 0 0;
		    height: 34px;
		}
		.name-card .detail p {
		    position: relative;
		    font-size: 15px;
		    line-height: 16px;
		    white-space: nowrap;
		    margin: 0 0 2px;
		    color: #666;
		}
		.oPrice_buy {
			padding-top: 0;
    		line-height: 55px;
    		font-size: 20px;
    		font-weight: bold;
		}
		.tf_lBut {
		    width: 65px;
		    height: 55px;
		    border-right: 1px solid #E6E6E6;
		}
		.name-card .detail p span{
			color:#b0282c;
			font-weight: bold;
		}
		/* 弹窗 */
    .pop_up{
        position:fixed;
        top:0;
        left:0;
        height:100%;
        width:100%;
        z-index:1111;
    }
    .pop_up .shade{
        background-color:rgba(0,0,0,.8);
        height:100%;
        width:100%
    }
    .code_step{
		text-align:left;
		margin:0 auto;
		font:16px/20px '微软雅黑'
	}


    .pop_up .pop_content{
        position: absolute;
	    top: 0;
	    bottom: 0;
	    left: 0;
	    right: 0;
	    margin: auto;
	    z-index: 100;
	    height: 400px;
	    width: 80%;
	    border-radius: 5px;
	    background: white;
	    padding: 20px;
    }
    .pop_up .attend{
        font-weight:bold;
        padding-top:20px;
    }
    .code_tip{
		font:18px/40px '微软雅黑';
		font-weight:normal
	}


    .pop_up .title{
        text-align:center;
        font:20px/30px "微软雅黑";
        border-bottom:1px solid #e6e6e6;
        padding-bottom:10px;
        font-weight:bold
    }
    .pop_up .pop_text{
        font:14px/25px "微软雅黑";
        font-weight:bold
    }
    .pop_up .qrcode>div{
	    text-align: center;
	    display: flex;
	    flex-direction: column;
		
    }
	.pop_up .qrcode>.qwrap{
		padding-bottom:5px;
		border-bottom:1px solid #e6e6e6
	}
    .pop_up .qrcode>div img{
        width: 200px;
	    height: 200px;
	    border: none;
	    outline: none;
	    margin: 0 auto;
    }
    .pop_up .qrcode_tip{
        color: #4d4d4d;
	    font-size: 18px;
	    display: block;
	    font-weight: bold;
    }
    .pop_up .btn_wrap{
        position:absolute;
        bottom:10px;
        left:0;right:0;
        text-align:center
    }
    .pop_up .btn_wrap>.btn{
        width:170px;
        font:16px/40px "微软雅黑";
        color:white;
        background:#B0282C;
        border-radius:10px;
    }
    .pop_up .close_btn{
        position:absolute;
        right:10px;
        top:3px;
    }
    .pop_up .info p{
    	text-align: center;
    	font-size: 20px;
    }
    .pop_up .close_btn img {
    	width: 22px;
    	position: relative;
    	top: 5px;
    }
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
    .content_l .title {
	    font-size: 17px;
	    font-weight: bold;
	    line-height: 24px;
	}
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
	    margin-bottom: 16px;
	}
	.big-btn {
	    font-size: 20px;
	    font-weight: bold;
	}
	.sku-layout .block-item{
		max-height:180px;
		overflow:auto;
		padding:20px
	}
	.sku-layout-title .goods-base-info .current-price {
	    line-height: 20px;
	    margin-left: 10px;
	    font-weight: bold;
	}
	.c-red {
	    color: #b0282c !important;
	}
	.goTuan {
	    width: 66px;
	    height: 28px;
	    border: 1px solid #b0282c;
	    color: #933334;
	    text-align: center;
	    line-height: 26px;
	    background: #b0282c;
	    color: #fff;
	    border-radius: 4px;
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
	.footer{
        margin-top:20px;
    }
    .sku-layout {
	    overflow: hidden;
	    z-index: 100000000;
	    bottom: 0px;
	    left: 0px;
	    right: 0px;
	    visibility: visible;
	    transform: translate3d(0px, 0px, 0px);
	    transition: all 300ms ease;
	    opacity: 1;
	    background: #fff;
	}
	.banner img{
		width:100%;
	}
	.motify {
        z-index:1000000002 !important;
    }
    .pin_content_top b{
    	margin-right:5px;
    	max-width:70px;
    }
	.footer_nav{margin-top:10px}
    .footer_nav img{width:100%;display:block}
	[v-cloak] { display: none }
	.content_l .people{
		line-height:20px
	}
	.reset-time{
		position:absolute;
		bottom:0px;
		left:60%
	}
	.reset-time span{
		display:inline-block;
		background:#b0282c;
		color:white;
		border-radius:2px;
		font:16px/20px '';
		padding:0px 2px
	}
	.slide-fade-enter-active {
	transition: all .3s ease;
	}
	.slide-fade-leave-active {
	transition: all .8s;
	}
	.slide-fade-enter{
	transform: translate(-100%,-20px);
	opacity: 0;
	}
	.slide-fade-leave-to{
		transform: translate(-50%,-100px);
	}


	/*  */
	.swiper1-container{
		position:fixed;
		top:10px;
		color:white;
		width:auto;
		padding:0 10px;
		z-index:10000
	}
	.swiper1-container img{
		width:20px;
		border-radius:50%
	}
	.swiper1-container .d_show>div{
		background:rgba(0,0,0,.7);
		border-radius:20px;
		padding:10px;
		font:12px/20px '';
		height:20px;
		margin-bottom:10px;
		display:flex;
		display:-webkit-flex;
		display: -moz-box;
		display: -ms-flexbox;
	}
	#clickImg{
		top:0;
		bottom:0;
		left:0;
		right:0;
		margin:auto;
		padding:0;
		width:90%;
		border-radius:0;
	}
	</style>
</head>
<body>	 
<div id="app" v-cloak>
	<div class="page" v-if="show != ''">
		<div class="detail_top">
			<div class="swiper-container" style="min-height: 200px;">
			    <div class="swiper-wrapper">
	                <div class="swiper-slide" v-for="swImg in list1.img" @click.stop="lookImg(swImg.img)">
	                    <img class="" v-bind:src="imgUrl + '' + swImg.img" />
	                </div>
			    </div>
			    <!-- 如果需要分页器 -->
			    <div class="swiper-pagination"></div>
			</div>
		</div>
		<div class="detail_content">
			<div class="content_l" style="position:relative">
				<p class="title" v-text="list1.title"></p>
				<p class="price">￥ @{{list1.min}}&nbsp;&nbsp<span style="margin-left:10px">原价：￥@{{list1.product.oprice}}</span></p>
				<p class="people">已团：<strong v-text="list1.pnum"></strong>件<strong class="groups_num" v-text="list1.groups_num"></strong>人团</p>
			</div>
			<div class="content_r">
				<img src="{{ config('app.source_url') }}shop/images/fenxiang@2x.png" @click="share"/>
				<div>分享</div>
			</div>
		</div>
		<div class="group_info">
			已有 <span class="groups_num" v-text="list1.pnum"></span>人拼团！快来参与吧！
		</div>
		<div class="banner">
			<a href="{{request()->fullUrl()}}" style="display:block"><img v-if="wid != 661 && wid != 626" src="{{ config('app.source_url') }}shop/images/baner40.png?t=111"></a>
            <a href="{{request()->fullUrl()}}" style="display:block"><img v-if="wid == 626" src="{{ config('app.source_url') }}shop/images/baner621.png?t=222"></a>
			<img v-if="wid == 661" src="{{ config('app.source_url') }}shop/images/meetingBaner.jpg">
		</div>
		<a :href="'/shop/meeting/groups/showMyGroups/' + list1.wid " class="my_group">
			<div class="img">
				<img v-if="list1.member.headimgurl" :src="list1.member.headimgurl">
				<img v-if="!list1.member.headimgurl" src=" {{ config('app.source_url') }}shop/images/avatar.png">
			</div>
			<p>我的拼团</p>
			<div class="arrow">
				<img src="{{ config('app.source_url') }}shop/images/meeting_arrow.jpg">
			</div>
		</a>
		<div class="content1 imageStrip" v-if="listLable.length != 0">
			<div class="imgStrip_bot" v-on:click="fwbzTc" v-if="groupInfo.service_status.length != 0">
				<div class="imgStrip_bot">
					<div class="fwbz" v-text="listLable.title">服务保障：</div>
					<div class="imgStrip_botR">
						<div class="imgStrip_bot_c">
							<div class="" v-for="listL in groupInfo.service_status" v-html="'<span></span>' + listL.title"><span>·</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content1 howTuan" v-if="list2.num != 0">
				<div class="h_title">
					<div class="how_poeple" v-text="list2.num + '人在开团'">89人在开团</div>
					<div class="more" v-on:click="goMorePin" v-if="list2.num > 2">查看更多</div>
					<img v-if="list2.num > 2" src="{{ config('app.source_url') }}shop/images/jinru@2x.png" />
				</div>
				<div class="tuanList" v-for="(howT,hIndex) in list2Data2">
					<img v-bind:src="howT.headimgurl" />
					<div class="tuanList_c">
						<p class="" v-text="howT.nickname" style="font-size:16px">细雨微微</p>
						<p class="tuanList_cTxt"><span v-text="'还差' + howT.num + '人，'">还差两人，</span><span v-text="'剩余' + surplusTime[hIndex]">剩余23:34:32</span></p>
					</div>
					<div class="goPin" @click="goTuanDetail(howT,hIndex)">去参团</div>
				</div> 
			</div>
			<div class="content1 evaluate" v-if="listE.num != 0 && listE.num != undefined" v-cloak>
				<div class="h_title">
					<div class="how_poeple" v-text="'全部评价（' + listE.num + '）'"></div>
					<div class="more" @click="goEval">查看评价</div>
					<img src="{{ config('app.source_url') }}shop/images/jinru@2x.png" />
				</div>
				<div class="e_list">
					<div class="e_listTit">
						<img v-bind:src="listEData.headimgurl" />
						<p class="e_name" v-text="listEData.nickname"></p>
						<p v-text="listEData.created_at"></p>
					</div>
					<div class="e_txt" v-text="listEData.content">这个鞋子质量出乎意料的好啊，额分光光度法感觉哈哈我I如何，发给方法</div>
					<div class="e_img">
						<img @click.stop="lookImg(evalImg)" v-for="evalImg in listEData.img" v-bind:src="imgUrl + '' + evalImg.m_path" />
						
					</div>
					<div class="e_ruler" v-text="listEData.spes">亮黑色；20英寸</div>
				</div>
			</div>
			<div class="content1" v-if="listProContent != ''" data-type="goods">
	            <div class="r_title"></div>
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
		</div>

	<!--头部消息轮播-->
	<transition name="slide-fade">
		<div class="hint flex_star" v-if="topTipList != null">
		  <img :src="topTipList.headimgurl" alt="">
		  <span v-text="topTipList.nickname + '，' + topTipList.sec + '秒前拼单了这个商品'"></span>
		</div>
	</transition>
	<div class="swiper1-container">
		<div  id="conts"> 
			<div class="dm">
				<div class="d_screen">
					<div class="d_mask"></div>
					<div class="d_show">
				</div>
			</div>
		</div> 
	</div>
	<!--遮罩-->
	<div class="zhezhao" v-on:click="bgClick" v-if="bgZhezhao">
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
	<!--服务保障弹窗-->
	<div v-if="fwbz">
		<div class="zhezhao" @click="closeServerModal"></div>
		<div class="fwbzTc tc">
			<div class="f_tcTit">服务保障</div>
			<div class="f_content">
				<div>
					<div class="f_tcContent" v-html="groupInfo.service_txt">
						全场商品支持配送地区内，免费配送到家
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
	<div class="preview_picture" v-if="previewShow" @click="previewHide">
		<div class="board"></div>
		<img :src="imgUrl + '' + previewImg" ref="img" id="clickImg"/>
	</div>
</div>

<!--底部按钮-->
<div class="t_footer" id="tFooter" ref="footer" v-if="show != ''">
	<div class="tf_lBut" @click="goIndex">
		<img src="{{ config('app.source_url') }}shop/images/sy@2x.png?t=123" />
	</div>
	@if ( config('app.chat_url') )
	<div class="tf_lBut" @click="showMd">
		<a>
			@if($reqFrom == 'aliapp')
			<img src="{{ config('app.source_url') }}shop/images/alikf.png" />
			@else
			<img src="{{ config('app.source_url') }}shop/images/kf@2xx.png?t=123" />
			@endif					
		</a>
	</div>
	@endif
	<div class="oPrice_buy oneOpen" v-on:click="buyTuan" v-if="list1.state == 1">
		一键开团
	</div>
	<div class="oPrice_buy oneOpen" style="background:#999" v-if="list1.state != 1">
		活动结束
	</div>
</div>
<div class="zhezhao" v-on:click="bgClick" v-if="bgZhezhao">
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
<div class="pinNow-zhezhao" v-if="mpinDan"  @click="closeMorePin">
	<div class="pinNow" v-if="mpinDan">
		<div class="pin_tit">
			<span></span>
			<p>正在拼单</p>
			<span></span>
		</div>
		<div class="lately">(最近5位)</div>
		<div class="pinList" v-for="(tcPin,tcHList) in list2Data" v-if="tcHList<5" @click="goTuanDetail(tcPin,tcHList)">
			<img v-bind:src="tcPin.headimgurl" />
			<div class="pin_content">
				<div class="pin_content_top"><b v-text="tcPin.nickname">丁香</b><span v-text="'(还差' + tcPin.num + '人)'">（还差2人）</span></div>
				<div class="surpTime" v-text="'剩余' + surplusTime[tcHList]">剩余 10:04:50</div>
			</div>
			<div class="goTuan">去参团</div>
		</div>
	</div>
</div>
<!-- 弹窗 -->
<div class="pop_up" v-if="showModel">
    <div class="shade" @click="hideModel">
    </div>
    <div class="pop_content">
        <a class="close_btn" @click="hideModel"><img src="{{ config('app.source_url') }}/shop/images/hsqrcode_close.png" alt=""/></a>
        <div class="pop_text">
        	<div class="info">
        		<p>如需客服帮助请关注</p>
        		@if(session('wid')== '634')
        		<p>杭州会搜股份公众号</p>
        		@elseif(session('wid') == '626' || session('wid') == '661')
        		<p>会搜商业智慧公众号</p>
        		@endif
        	</div>
            <div class="qrcode">
                <div class="qwrap">
                    @if(session('wid')== '634')
                    <img src="{{ config('app.source_url') }}/shop/images/hsqrcode1.jpg" alt="" class="qrcodeImage">
                    @elseif(session('wid') == '626' || session('wid') == '661')
                    <img src="{{ config('app.source_url') }}/shop/images/hsqrcode626.jpg" alt="" class="qrcodeImage">
                    @endif
                    <p class="qrcode_tip">长按图片【识别二维码】关注公众号</p>
                </div>
				<div>
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
            </div>
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
<!-- 主体内容 结束 -->
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
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/fx.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/fx_methods.js"></script>
<script>
	var rule_id="{!!$rule_id!!}";
	var _host = "{{ config('app.source_url') }}";
    var host ="{{ config('app.url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var isBind = 0;
    var wid = "{{session('wid')}}";
</script>
<script src="{{ config('app.source_url') }}shop/js/meeting_until.js?v=1.00"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/product_vue_component.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
<!--懒加载插件-->
<script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
	<script>
		// 微信分享
	    $(function(){
	    	setTimeout(function(){
	    		$('video').attr('width',$('window').width());
	    		$('video').attr('height','auto');
	    		if(tool.isPhonex()){
	    			$('.t_footer').css('height','60px');
	                $('.tf_lBut').css({
	                	'width':'80px',
	                	'height':'60px'
	                });
	    		}
	    	},2000)
            $('.attention').click(function(){
                $('.follow_us').show();
            });
            $(".code img").click(function(e){
                e.stopPropagation()
            })
            $('.follow_us').click(function(){
                $('.follow_us').hide();
            });
			
	        var url = location.href.split('#').toString();
	        $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){ 
	            if(data.errCode == 0){
	                wx.config({
	                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	                    appId: data.data.appId, // 必填，公众号的唯一标识
	                    timestamp: data.data.timestamp, // 必填，生成签名的时间戳
	                    nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
	                    signature: data.data.signature,// 必填，签名，见附录1
	                    jsApiList: [
	                        'checkJsApi',
	                        'onMenuShareTimeline',
	                        'onMenuShareAppMessage',
	                        'onMenuShareQQ',
	                        'chooseWXPay'
	                    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	                });
	            }
	            wxShare();
	        })

            function wxShare(){
	            if(typeof app.list1.share_title !="undefined"){
                    if(wid == 661){
						var share_title ='39800微商城我已免费领到，你也赶紧领一个';
						var share_desc ='一起来拼团，拼团满50人，全部免单！';
                    }else if(wid == 626){
                        var share_title ='移动互联网实战总裁班课程我已免费领到，你也赶紧领！';
                        var share_desc =' 超值拼团限时回馈，5人成团，人人0元领取《移动互联网实战总裁班》1天！';
                    }else{
						var share_title =' 19800小程序我已免费领到，你也赶紧领一个';
						var share_desc ='一起来拼团，拼团满50人全部免单！';
                    }


                    var share_img =app.list1.share_img ?imgUrl + app.list1.share_img : imgUrl + app.list1.img2;
	                var share_url=host+'shop/meeting/detail/'+rule_id+'/{{session('wid')}}?_pid_={{session('mid')}}'
	                wx.ready(function () {
	
	                    //分享到朋友圈
	                    wx.onMenuShareTimeline({
	                        title: share_title, // 分享标题
	                        desc: share_desc, // 分享描述
	                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
	                        imgUrl: share_img, // 分享图标
	                        success: function () {
	                            // 用户确认分享后执行的回调函数

	                        },
	                        cancel: function () {
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
	                        success: function () {
	                            // 用户确认分享后执行的回调函数

	                        },
	                        cancel: function () {
	                            // 用户取消分享后执行的回调函数
	                        }
	                    });
	
	                    //分享到QQ
	                    wx.onMenuShareQQ({
	                        title: share_title, // 分享标题
	                        desc: share_desc, // 分享描述
	                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
	                        imgUrl: share_img, // 分享图标
	                        success: function () {
	                           // 用户确认分享后执行的回调函数
	                        },
	                        cancel: function () {
	                           // 用户取消分享后执行的回调函数
	                        }
	                    });
	
	                    //分享到腾讯微博
	                    wx.onMenuShareWeibo({
	                        title: share_title, // 分享标题
	                        desc: share_desc, // 分享描述
	                        link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
	                        imgUrl: share_img, // 分享图标
	                        success: function () {
	                           // 用户确认分享后执行的回调函数
	                        },
	                        cancel: function () {
	                            // 用户取消分享后执行的回调函数
	                        }
	                    });
	                    wx.error(function(res){
	                        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
	                    });
	                });
	            }else{
	                setTimeout(function(){
	                    wxShare();
	                },50)
	            }
	        }
		});
	</script>
	<!-- 当前页面js -->
	<script src="{{ config('app.source_url') }}shop/js/detail3.js"></script>
</body>
</html>
