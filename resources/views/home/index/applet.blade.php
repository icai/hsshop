@extends('home.base.head')
@section('head.css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/xiaochengxu.css"/>
@endsection
@section('content')
@include('home.base.slider')
<div class="main_content">
    <!--分页一-->
    <div class='xcx_bg'>
		<div class="xiao-fir">
			{{--<img src="{{ config('app.source_url') }}home/image/inbannera.png" alt="会搜云"/>--}}
			<a class="order-btn" href="/home/index/reserve?type=3">立即预约</a>
		</div>
	</div>
    <!--分页二-->
    <div class="xiao-sec">
        <div class="xiao-sec1">
            <div class="xiao-secul">
                <p class="xiao-sp1">会搜云小程序优势</p>
            </div>
            {{--<div class="xiao-sjiejue">--}}
            	{{--<img class="xiant02" src="{{ config('app.source_url') }}home/image/xiant02.png"/>--}}
            {{--</div>--}}
        </div>
        <div class="code-bom">
			<ul class="clearfix">
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/ksjr.png" alt="">
					<p>快速接入</p>
					<div>
						<p>授权接入会搜云系统</p>
						<p>轻松管理小程序</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_gxzx@2x.png" alt="">
					<p>个性装修</p>
					<div>
						<p>丰富模块可视化拖拽</p>
						<p>个性化设计专属小程序</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_spdr@2x.png" alt="">
					<p>商品导入</p>
					<div>
						<p>淘宝、阿里巴巴等商品</p>
						<p>一键导入</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_ddtb@2x.png" alt="">
					<p>订单同步</p>
					<div>
						<p>小程序订单直接与微商城店铺同步</p>
						<p>无需成本轻松管理</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_mjzt@2x.png" alt="">
					<p>买家自提</p>
					<div>
						<p>开启买家上门自提功能</p>
						<p>让买家就近选择预设自提点</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_dxtz@2x.png" alt="">
					<p>短信通知</p>
					<div>
						<p>开启短信通知功能，下单</p>
						<p>发货即时发送推送短信</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_wxbd@2x.png" alt="">
					<p>微信绑定</p>
					<div>
						<p>店铺绑定微信</p>
						<p>让管理员轻松管理店铺</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_pjgn@2x.png" alt="">
					<p>评价功能</p>
					<div>
						<p>管理商品评价</p>
						<p>让商品吸引更多消费者</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_khgl@2x.png" alt="">
					<p>客户管理</p>
					<div>
						<p>会员还是黑名单由你</p>
						<p>来定夺</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_yfsz@2x.png" alt="">
					<p>运费设置</p>
					<div>
						<p>按需设置运费模版，添加商品</p>
						<p>时即可一键使用</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_fhwl@2x.png" alt="">
					<p>发货物流</p>
					<div>
						<p>添加合作快递公司，批量</p>
						<p>打单，批量发货</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_jyzf@2x.png" alt="">
					<p>交易支付</p>
					<div>
						<p>支持微信、支付宝、储蓄卡</p>
						<p>信用卡支付</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_fxxt@2x.png" alt="">
					<p>分销系统</p>
					<div>
						<p>轻松设定分销模版，管理分销商品，分销</p>
						<p>订单，发放佣金，查看分销合伙人</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_zdtx@2x.png" alt="">
					<p>自动提现</p>
					<div>
						<p>所有分销用户的利润均可以直接在线</p>
						<p>自动提现，无需商家进行线下转账</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_sjzx@2x.png" alt="">
					<p>数据中心</p>
					<div>
						<p>客户访问频次、商品浏览量</p>
						<p>商品销量涨幅一目了</p>
					</div>
				</li>
				<li class='cod-sep'>
					<img src="{{ config('app.source_url') }}home/image/xcx_jyfx@2x.png" alt="">
					<p>交易分析</p>
					<div>
						<p>付款转化率、订单涨幅情况清晰明了</p>
						<p>交易数据实时发布</p>
					</div>
				</li>
			</ul>
        </div>
    </div>
    <!--分页三-->
    <div class="xiao-tir">
        <div class="qiang-ul">小程序特色功能</div>
        <div class="code-tri">
			<ul class='clearfix'>
				<li class='code-tb'>
					<img src="{{ config('app.source_url') }}home/image/hyxt.png" alt="">
					<p>会员系统</p>
					<div>
						<p>会员卡、积分签到等强大会员系统</p>
					</div>
				</li>
				<li class='code-tb'>
					<img src="{{ config('app.source_url') }}home/image/yxwf.png" alt="">
					<p>营销玩法</p>
					<div>
						<p>优惠券、刮刮卡、大转盘等</p>
						<p>多种营销玩法扩大销量</p>
					</div>
				</li>
				<li class='code-tb'>
					<img src="{{ config('app.source_url') }}home/image/yxhd.png" alt="">
					<p>营销活动</p>
					<div>
						<p>拼团、秒杀享立减等</p>
						<p>多样活动扩大销量</p>
					</div>
				</li>
				<li class='code-tb'>
					<img src="{{ config('app.source_url') }}home/image/wsq.png" alt="">
					<p>微社区</p>
					<div>
						<p>多人社区沟通分享</p>
					</div>
				</li>
			</ul>
        </div>
    </div>
	<!--小程序定制开发服务流程-->
	<div class="xcx_exploit">
		<h4>小程序定制开发服务流程</h4>
		<div class='exploit_flow'>
			<ul class='clearfix exploit_flow_ul'>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_1@2x.png" alt="">
					<p>沟通协议</p>
					<div>
						<p>前期沟通初步达成合作，</p>
						<p>签订开发合作协议</p>
					</div>
				</li>
				<li class='arrow'>
					<img src="{{ config('app.source_url') }}home/image/arrow_left.png" alt="">
				</li>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_2@2x.png" alt="">
					<p>品牌定位</p>
					<div>
						<p>了解产品品牌文化，确定</p>
						<p>品牌定位</p>
					</div>
				</li>
				<li class='arrow'>
					<img src="{{ config('app.source_url') }}home/image/arrow_left.png" alt="">
				</li>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_3@2x.png" alt="">
					<p>需求评估</p>
					<div>
						<p>分析行业用户功能需求，做好</p>
						<p>产品设计</p>
					</div>
				</li>
				<li class='arrow'>
					<img src="{{ config('app.source_url') }}home/image/arrow_left.png" alt="">
				</li>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_4@2x.png" alt="">
					<p>产品设计</p>
					<div>
						<p>初步完成产品设计，沟通</p>
						<p>修改细节</p>
					</div>
				</li>
			</ul>
			<div class='arrow_low'></div>
			<ul class='clearfix exploit_flow_ul flow_b'>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_7@2x.png" alt="">
					<p>审核上线</p>
					<div>
						<p>提交微信小程序平台，审核</p>
						<p>通过上线</p>
					</div>
				</li>
				<li class='arrow'>
					<img src="{{ config('app.source_url') }}home/image/arrow_right.png" alt="">
				</li>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_6@2x.png" alt="">
					<p>测试修改</p>
					<div>
						<p>修改调整、规范完善</p>
					</div>
				</li>
				<li class='arrow'>
					<img src="{{ config('app.source_url') }}home/image/arrow_right.png" alt="">
				</li>
				<li class='exploit_flow_li'>
					<img src="{{ config('app.source_url') }}home/image/xcx_exploit_5@2x.png" alt="">
					<p>功能开发</p>
					<div>
						<p>进行产品后台设计与功能</p>
						<p>开发</p>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<!--小程序客户案例-->
	<div class="xcx_client">
		<h4>小程序客户案例</h4>
		@if($caseList['data'])
		<ul class='clearfix xcx_client_ul'>
			@foreach($caseList['data'] as $val)
			<li>
				<a href="/home/index/caseDetails?id={{ $val['id'] }}">
					<div class='xcx_client_div_a'>
						<img src="{{ imgUrl() }}{{ $val['logo'] }}" alt="">
					</div>
					<div class='xcx_client_div_b'>
						<div>
							<img src="{{ imgUrl() }}{{ $val['code'] }}" alt="">
						</div>
						<p>{{ $val['name'] }}</p>
					</div>
				</a>
			</li>
			@endforeach
		</ul>
		<a class='getAll' href="/home/index/2/shop">
			查看更多 →
		</a>
		@endif
	</div>
	<!--小程序应用场景-->
	<div class='xcx_application'>
		<h4>小程序应用场景</h4>
		<ul class='clearfix xcx_scenarios'>
			<li>
				<img src="{{ config('app.source_url') }}home/image/mdyl.png" alt="">
				<div></div>
				<p>门店引流</p>
			</li>
			<li>
				<img src="{{ config('app.source_url') }}home/image/cywm.png" alt="">
				<div></div>
				<p>餐饮外卖</p>
			</li>
			<li>
				<img src="{{ config('app.source_url') }}home/image/jdly.png" alt="">
				<div></div>
				<p>景点旅游</p>
			</li>
			<li>
				<img src="{{ config('app.source_url') }}home/image/mrmz.png" alt="">
				<div></div>
				<p>美容美妆</p>
			</li>
		</ul>
	</div>
    <!--分页四-->
    <div class="xiao-fou">
    	<div class="qiang-ul">
            <p class="qiang-p1 colff">适用小程序的行业</p>
        </div>
        <div class="fou-box">
			<ul class='clearfix'>
				<li>
					<img src="{{ config('app.source_url') }}home/image/1@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/2@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/3@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/4@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/5@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/6@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/7@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/8@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/9@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/10@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/11@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/12@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/13@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/14@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/15@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/16@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/17@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/18@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/19@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/20@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/21@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/22@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/23@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/24@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/25@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/26@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/27@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/28@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/29@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/30@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/31@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/32@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/33@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/34@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/35@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/36@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/37@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/38@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/39@2x.png" alt="">
				</li>
				<li>
					<img src="{{ config('app.source_url') }}home/image/40@2x.png" alt="">
				</li>
			</ul>
        </div>
    </div>
    <div class="tri-sub">
		<h4>立即预约体验火爆的小程序</h4>
		<a href="/home/index/reserve?type=3">立即预约</a>
	</div>
</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection
@section('foot.js')
<script type="text/javascript">
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
            		tipshow(res.info,"warn");
            	}else{
            		tipshow("预约成功");
            	}
            },
            error : function() {
                alert("数据访问错误");
            }
        });		
	});   
</script>
@endsection