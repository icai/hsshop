<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <script src="{{ config('app.source_url') }}shop/static/js/rem.js"></script>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/shopnav_custom_c1bc734a2d27b02980b60dc03f4ca9d7.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/store_index.css">
    <style type="text/css">
        .qqkefu img{width:50px;height:50px;}


        .form1{
            padding:20px 0;
            background:#fff;
        }
        .form1 .form_title {
            text-align:center;
            font-weight: bold;
            font-size:18px;
        }
        .form1 .form_lebel {
            font-size: 16px;
            padding: 10px;
        }
        .form1 .form_lebel span {
            color:red;
            margin-left:5px;
        }
        .form1 .form-control {
            padding:0 10px;
        }
        .form1 .form-control input {
            display: block;
            width: 100%;
            height: 34px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            padding-left:10px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            outline: none;
            box-sizing: border-box;
        }
        .weui-btn {
            position: relative;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding-left: 14px;
            padding-right: 14px;
            box-sizing: border-box;
            font-size: 18px;
            text-align: center;
            text-decoration: none;
            color: #fff;
            line-height: 2.55555556;
            border-radius: 5px;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            overflow: hidden;
            width:100%;
            margin-top:20px;
        }
        .weui-btn_primary {
            background-color: #f44747;
        }
        .weui-btn_primary:not(.weui-btn_disabled):visited {
            color: #fff;
        }
        .action{
            padding:0 20px;
        }
        .form2{
            height:500px;
            background:url({{ config('app.source_url') }}/shop/images/form_web.png) no-repeat;
            background-size:100%;
        }
    </style>
</head>
<body>
<div class="container" id="container" :style="{background:bg_color}">
    <div class="content no-sidebar">
        <div class="content-body js-page-content">
            <div v-for="(list, index) in lists" v-if="lists.length" v-cloak>
                <!-- 官网模板2 -->
                <guan-text v-if="list['type']=='imageTextModel'" :list="list"></guan-text>
                <!-- 官网模板 -->
                <guan-wang v-if="list['type']=='bingbing'" :content="list"></guan-wang>

                <goods v-if="list['type']=='goods'" :list="list"></goods>
                <!-- 美妆小店头部 -->
                <guan-header v-if="list['type']=='header'" :list="list"></guan-header>

                <!-- 富文本编辑器 -->
                <rich-text v-if="list['type']=='rich_text'" :list="list"></rich-text>
                <!-- 图片广告 -->
                <image-ad v-if="list['type']=='image_ad' && list['images'].length > 0" :list="list"></image-ad>
                <!-- 标题样式 -->
                <title-style v-if="list['type']=='title'" :list="list"></title-style>
                <!-- 进入店铺 -->
                <store-in v-if="list['type']=='store'" :list="list"></store-in>
                <!-- 优惠券样式 -->
                <coupon v-if="list.type=='coupon' && list.couponList.length > 0" :list="list"></coupon>
                <!-- 优惠券样式 -->
                <!-- 会员卡样式 -->
                <card v-if="list.type=='card' && list.cardList.length > 0" :list="list" :host='_host'></card>
                <!-- 会员卡样式 -->
                <!-- 公告样式 -->
                <notice v-if="list.type == 'notice'" :content = "list.content" :bg-color="list.colorBg" :bg-txt="list.txtBg"></notice>
                <!-- 公告样式 -->
                <!-- 商品搜索 -->
                <!--update by 韩瑜 2018-9-19-->
                <search :list='list' :host="host" :wid='wid' v-if="list.type == 'search'"></search>
                <!--end-->
                <!-- 商品搜索 -->
                <!-- 商品列表 -->
                <goods-list v-if="list['type']=='goodslist'" :list="list"></goods-list>
                <!-- 商品列表 -->
                <!-- 商品分组 -->
                <good-group v-if="list.type == 'good_group' && (list.top_nav.length || list.left_nav.length)" :content="list"></good-group>
                <!-- 图片导航 -->
                <image-link v-if="list.type == 'image_link'" :content="list.images"></image-link>
                <!-- 图片导航 -->
                <!-- 文本链接 -->
                <text-link v-if="list.type == 'textlink'" :list='list'></text-link>
                <!-- 秒杀活动 -->
                <seckill v-if="list.type == 'marketing_active'&& list.content.length>0" :list = "list"></seckill>
                <!-- 秒杀活动 -->
                <!-- 拼团标题 -->
                <spell-title :content="list.pages" v-if="list.type == 'spell_title'"></spell-title>
                <!-- 拼团标题 -->
                <!-- 拼团列表 -->
                <spell-goods :content="list" v-if="list.type == 'spell_goods'"></spell-goods>
                <!-- 拼团列表 -->
                <!-- 视频组件 -->
                <cvideo :list="list" v-if="list.type == 'video'"></cvideo>
                <!-- 视频组建 -->
                <!-- 魔方组件 -->
                <cube :list="list" :wid="wid" :host="host" v-if="list.type == 'cube'"></cube>
                <!-- 魔方组件 -->
                <!-- author 华亢 update 2018/06/28 -->
                <!-- 联系方式组件 -->
                <cmobile :list="list" v-if="list.type == 'mobile'"></cmobile>
                <!-- 联系方组件 -->
                <!-- 享立减 -->
                <share-rebate :list="list" v-if="list.type == 'share_goods'"></share-rebate>
                <!-- 享立减 -->
                <!-- 留言板 -->
                <info-board :list="list" v-if="list.type == 'researchVote'"></info-board>
                <info-board :list="list" v-if="list.type == 'researchAppoint'"></info-board>
                <info-board :list="list" v-if="list.type == 'researchSign'"></info-board>
                <!-- 留言板 -->
                <seckill-list :list="list" v-if="list.type == 'seckill_list'"></seckill-list>
                <!--add by 韩瑜 2018-9-18-->
                <!--商品分组模板页-->
				<group-page :list="list" v-if="list.type == 'group_page'"></group-page>
                <!--end-->
            </div>
        </div>
    </div>
    <div class="form1">
        <div class="form_title">电话报名</div>
        <div class="form-control">
            <div class="form_lebel">
                姓名<span>*</span>
            </div>
            <div class="form-control">
                <input type="text" name="name">
            </div>
        </div>
        <div class="form-control">
            <div class="form_lebel">
                手机<span name="phone">*</span>
            </div>
            <div class="form-control">
                <input type="number" name="phone">
            </div>
        </div>
        <div class="form-control" style="display:none">
            <div class="form_lebel">
                公司
            </div>
            <div class="form-control">
                <input type="text" name="company_name">
            </div>
        </div>
        <div class="form-control" style="display:none">
            <div class="form_lebel">
                职务
            </div>
            <div class="form-control">
                <input type="text" name="company_position">
            </div>
        </div>
        <div class="action">
            <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">确定</a>    
        </div>
    </div>
    <!--add by 韩瑜 2018-8-2 拆红包弹框-->
	<div class='bouns_tip' v-if="bonusShow" v-cloak>
	  <div class='bouns_box' @click="getBouns">
	    <div class='bouns_text'>
	      <div class='bouns_text_tip'>恭喜您获得神秘红包一个！</div>
	      <div class='bouns_text_msg'>
	      	<div v-text="activity_title"></div>
	      </div>
	    </div>
	    <div class='bouns_close' @click.stop='closeBouns'></div>
	  </div>
	</div>
	<!--红包右下角图标-->
	<div class='bonusShow_tip' @click="showBonus" v-if="bonusShow_tip" v-cloak></div>
	<!--end-->
 
</div>

<!-- 底部 结束
<!-- 当前页面js -->
<script type="text/javascript">
    var APP_HOST = "{{ config('app.url') }}"
    var APP_IMG_URL = "{{ imgUrl() }}"
    var APP_SOURCE_URL = "{{ config('app.source_url') }}"
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var imgUrl ="{{ imgUrl() }}";
    var host = "{{ config('app.url') }}";
    var videoUrl = "{{ videoUrl() }}";
    var wid = {!!$wid!!};
    var id = {!! $id !!};
    var mid = "{{session('mid')}}";
</script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-lazyload.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}shop/js/vue_component.js"></script>
<script src="{{ config('app.source_url') }}shop/js/meeting_index.js"></script>
<script type="text/javascript">
    $(function(){
        var flag = true;
        $('.weui-btn').click(function(){
            if(!flag) return;
            flag = false;
            var data = {};
            data.name = $('input[name="name"]').val();
            data.phone = $('input[name="phone"]').val();
            data.company_name = $('input[name="company_name"]').val();
            data.company_position = $('input[name="company_position"]').val();
            data._token = $('meta[name="csrf-token"]').attr('content');
            if(data.name == ''){
                tool.tip('请输入姓名');
                flag = true;
                return;
            }
            if(data.phone == ''){
                tool.tip('请输入手机号码');
                flag = true;
                return;
            }
            if(!/^[1][3,4,5,6,7,8,9][0-9]{9}$/.test(data.phone)){
                tool.tip('手机号码格式不正确');
                flag = true;
                return;
            }
            $.post('',data,function(res){
                if(res.status == 1){
                    tool.tip('領取成功')
                    setTimeout(function(){
                        window.location.reload();
                    },2000)
                }
                flag = true;
            })
        })
    })
</script>