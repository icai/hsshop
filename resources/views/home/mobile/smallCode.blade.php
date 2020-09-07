@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<meta name="keywords" content="微信小程序">
	<meta name="description" content="会搜股份荣誉出品，会搜云专注做微信小程序全套解决方案，提供微信小程序哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/SmallCode.css">
@endsection
@section('content')
    <div class="content">
        <section>
            <div class="app_header">
				<a href="/home/index/reserve?type=3">
					立即预约
				</a>
			</div>
			<div class='small_virtue'>
				<h4 class='small_title'>会搜云小程序优势</h4>
				<ul>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_ksjr.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>快速接入</p>
							<p class="virtue_p">授权接入会搜云系统</p>
							<p class="virtue_p">轻松管理小程序</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_gxzx@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>个性装修</p>
							<p class="virtue_p">丰富模块可视化拖拽</p>
							<p class="virtue_p">个性化设计专属小程序</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_jyfx@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>交易分析</p>
							<p class="virtue_p">付款转化率、订单涨幅情况清晰明了</p>
							<p class="virtue_p">交易数据实时发布</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_spdr@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>商品导入</p>
							<p class="virtue_p">淘宝、阿里巴巴等商品一键导入</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_ddtb@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>订单同步</p>
							<p class="virtue_p">小程序订单直接与微商城店铺同步</p>
							<p class="virtue_p">无需成本轻松管理</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_mjzt@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>买家自提</p>
							<p class="virtue_p">开启买家上门自提功能</p>
							<p class="virtue_p">让买家就近选择预设自提点</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_dxtz@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>短信通知</p>
							<p class="virtue_p">开启短信通知功能，下单</p>
							<p class="virtue_p">发货即时发送推送短信</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_wxbd@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>微信绑定</p>
							<p class="virtue_p">店铺绑定微信</p>
							<p class="virtue_p">让管理员轻松管理店铺</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_pjgn@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>评价功能</p>
							<p class="virtue_p">管理商品评价</p>
							<p class="virtue_p">让商品吸引更多消费者</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_khgl@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>客户管理</p>
							<p class="virtue_p">会员还是黑名单由你来定夺</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_yfsz@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>运费设置</p>
							<p class="virtue_p">按需设置运费模版</p>
							<p class="virtue_p">添加商品时即可一键使用</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_fhwl@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>发货物流</p>
							<p class="virtue_p">添加合作快递公司</p>
							<p class="virtue_p">批量打单，批量发货</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_jyzf@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>交易支付</p>
							<p class="virtue_p">支持微信、支付宝</p>
							<p class="virtue_p">储蓄卡、信用卡支付</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_fxxt@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>分销系统</p>
							<p class="virtue_p">轻松设定分销模版，管理分销商品</p>
							<p class="virtue_p">分销订单，发放佣金，查看分销合伙人</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_mjzt@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>用户自提</p>
							<p class="virtue_p">所有分销用户的利润均可以直接在线</p>
							<p class="virtue_p">自动提现，无需商家进行线下转账</p>
						</div>
					</li>
					<li>
						<div>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_sjzx@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>数据中心</p>
							<p class="virtue_p">客户访问频次、商品浏览量</p>
							<p class="virtue_p">商品销量涨幅一目了然</p>
						</div>
					</li>
				</ul>
			</div>
			<div class='small_feature'>
				<h4 class='small_title'>会搜云小程序优势</h4>
				<ul>
					<li>
						<div class='small_feature_img'>
							<img src="{{ config('app.source_url') }}mobile/images/hyxt.png"  alt="">
						</div>
						<div class='small_feature_title'>会员系统</div>
						<p class="virtue_p">会员卡、积分签到等</p>
						<p class="virtue_p">强大会员系统</p>
					</li>
					<li>
						<div class='small_feature_img'>
							<img src="{{ config('app.source_url') }}mobile/images/yxwf.png"  alt="">
						</div>
						<div class='small_feature_title'>营销玩法</div>
						<p class="virtue_p">优惠券、刮刮卡、大转盘等</p>
						<p class="virtue_p">多种营销玩法扩大销量</p>
					</li>
					<li>
						<div class='small_feature_img'>
							<img src="{{ config('app.source_url') }}mobile/images/yxhd.png"  alt="">
						</div>
						<div class='small_feature_title'>营销活动</div>
						<p class="virtue_p">拼团、秒杀享立减等</p>
						<p class="virtue_p">多样活动扩大销量</p>
					</li>
					<li>
						<div class='small_feature_img'>
							<img src="{{ config('app.source_url') }}mobile/images/wsq.png"  alt="">
						</div>
						<div class='small_feature_title'>微社区</div>
						<p class="virtue_p">多人社区沟通分享</p>
					</li>
				</ul>
			</div>
			<div class='small_virtue small_serve'>
				<h4 class='small_title'>小程序定制开发服务流程</h4>
				<ul>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_1@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>沟通协议</p>
							<p class="virtue_p">前期沟通初步达成合作，签订开发合作协议</p>
						</div>
					</li>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_2@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>品牌定位</p>
							<p class="virtue_p">了解产品品牌文化，确定品牌定位</p>
						</div>
					</li>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_3@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>需求评估</p>
							<p class="virtue_p">分析行业用户功能需求，做好产品设计</p>
						</div>
					</li>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_4@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>产品设计</p>
							<p class="virtue_p">初步完成产品设计，沟通修改细节</p>
						</div>
					</li>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_5@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>功能开发</p>
							<p class="virtue_p">进行产品后台设计与功能开发</p>
						</div>
					</li>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_6@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>测试修改</p>
							<p class="virtue_p">修改调整、规范完善</p>
						</div>
					</li>
					<li class='small_serve_li'>
						<div class='small_serve_div'>
							<img src="{{ config('app.source_url') }}mobile/images/xcx_exploit_7@2x.png" alt="">
						</div>
						<div>
							<p class='virtue_li_title'>审核上线</p>
							<p class="virtue_p">提交微信小程序平台，审核通过上线</p>
						</div>
					</li>
				</ul>
			</div>
			<div class="small_client">
				<h4 class='small_title'>会搜云小程序客户案例</h4>
				<ul>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_01@2x.png" alt="">
						<p>哈哈贝尔</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_02@2x.png" alt="">
						<p>红坡果园</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_03@2x.png" alt="">
						<p>手工毛线编织</p>
					</li>			<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_04@2x.png" alt="">
						<p>西湖旅游攻略</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_05@2x.png" alt="">
						<p>飞镖商城</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_06@2x.png" alt="">
						<p>贝乐康酒水</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_07@2x.png" alt="">
						<p>人气美食餐厅</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_08@2x.png" alt="">
						<p>嵊州土特产</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/small_09@2x.png" alt="">
						<p>聚妆汇</p>
					</li>
				</ul>
			</div>
			<div class='small_scene'>
				<h4 class='small_title'>小程序应用场景</h4>
				<ul>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/mdyl.png" alt="">
						<div></div>
						<p>门店引流</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/cywm.png" alt="">
						<div></div>
						<p>餐饮外卖</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/jdly.png" alt="">
						<div></div>
						<p>景点旅游</p>
					</li>
					<li>
						<img src="{{ config('app.source_url') }}mobile/images/mrmz.png" alt="">
						<div></div>
						<p>美容美妆</p>
					</li>
				</ul>
			</div>
			<div class='small_industry'>
				<h4>适用小程序的行业</h4>
				<div></div>
			</div>
			<div class='small_appoin'>
				<a href="/home/index/reserve?type=3">
					<span>立即预约小程序定制</span>
					<p></p>
				</a>
			</div>
			<a class="order-footer" href="/home/index/reserve?type=3">我要预约</a>
        </section>
    </div>

@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection
@section('js')
<script type="text/javascript">
	$('.fuwu').attr("href","/home/index/serviceThi");
	//预约提交
	$(".tri-sua").click(function(){
		$.ajax({
            url:'/home/index/reserve',// 跳转到 action
            data:$('#myform').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (res) {
            	if(res.status==0){
            		alert(res.info);
            	}else{
            		alert("预约成功");
            	}
            },
            error : function() {
                alert("数据访问错误");
            }
        });		
	}); 
</script>
@endsection