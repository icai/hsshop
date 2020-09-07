@extends('home.base.head')
	<title>{{$title}}</title>
	<meta name="keywords" content="App定制">
    <meta name="description" content="会搜股份荣誉出品，会搜云专注做APP定制全套解决方案，提供App定制哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
@section('head.css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/dingzhiAPP.css"/>
@endsection
@section('content')
    @include('home.base.slider')
    <div class="main_content">
        <!--分页1-->
        <div class="app-fir">
            <div class="app-beijing">
            	<!--add by 韩瑜 2018-7-30 banner图修改-->
            	{{--<img src="{{ config('app.source_url') }}home/image/APPdz-banner.png" />--}}
				<a class="order-btn" href="/home/index/reserve?type=2">立即预约</a>
            	<!--end-->
            </div>
        </div>
        <!--分页2-->
        <div class="app-sec">
            <div class="app-qiang">
                <div class="qiang-ul">
                    <p class="">会搜云App定制优势</p>
                </div>
                <div class="ys_wrap">
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/ydfg@2x.png" alt="" />
	                	<h3>全面覆盖移动终端</h3>
	                	<p>ios App+安卓App+小程序+微商城+pc网站</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/jslx.png" alt="" />
	                	<h3>行业技术领先</h3>
	                	<p>独有App更新技术、模拟预览技术，主流手机完美适配</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/ztpz@2x.png" alt="" />
	                	<h3>主题丰富模块自由搭配</h3>
	                	<p>可视化操作，功能自由搭配，个性化编辑排版</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/zxtj@2x.png" alt="" />
	                	<h3>资讯头条推荐一目了然</h3>
	                	<p>让用户快速找到所需产品</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/ybzc@2x.png" alt="" />
	                	<h3>用户注册一步到位</h3>
	                	<p>通过移动端、微信等第三方登录</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/tsdz@2x.png" alt="" />
	                	<h3>App电商特色功能</h3>
	                	<p>分销系统、会员系统、优惠活动、营销插件</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/1v1dz@2x.png" alt="" />
	                	<h3>1v1定制时时对接</h3>
	                	<p>独立项目小组对接，制作工期加速</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/sydz@2x.png" alt="" />
	                	<h3>多种多样商业盈利模式</h3>
	                	<p>自营电商、联动营销、招商入住、付费发布</p>
	                </div>
	                <div class="ys_item">
	                	<img src="{{ config('app.source_url') }}home/image/ptqq@2x.png" alt="" />
	                	<h3>配套服务齐全</h3>
	                	<p>免费培训后台操作，提供运营推广学习课程</p>
	                </div>
                </div>
            </div>
        </div>
        <!--分页3-->
        <div class="app-lc-wrap">
	        <div class="app-lc">
	            <h3>App定制开发服务流程</h3>
	            <div class="lc-item-top">
		            <div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app1@2x.png" alt="" />
			        	<h4>沟通协议</h4>
			        	<p>前期沟通初步达成合作<br />在产品定义上达成一致</p>
			        </div>
			        <div class="left lc-item-arr">
			        	<img src="{{ config('app.source_url') }}home/image/arr01.png" alt="" />
			        </div>
			        <div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app2@2x.png" alt="" />
			        	<h4>品牌定位</h4>
			        	<p>了解产品品牌文化，寻找产品<br />创新机会点，确定品牌定位</p>
			        </div>
			        <div class="left lc-item-arr">
			        	<img src="{{ config('app.source_url') }}home/image/arr01.png" alt="" />
			        </div>
			        <div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app3@2x.png" alt="" />
			        	<h4>需求评估</h4>
			        	<p>综合行业、市场、用户多元因素，<br />分析行业用户功能需求</p>
			        </div>
			        <div class="left lc-item-arr">
			        	<img src="{{ config('app.source_url') }}home/image/arr01.png" alt="" />
			        </div>
			        <div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app4@2x.png" alt="" />
			        	<h4>产品设计</h4>
			        	<p>以用户为中心，初步完成<br />产品设计，沟通修改细节</p>
			        </div>
		        </div>
		        <div class="lc-item-mid">
		        	<img src="{{ config('app.source_url') }}home/image/arr03.png" alt="" />
		        </div>
		        <div class="lc-item-bottom">
		        	<div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app7@2x.png" alt="" />
			        	<h4>审核上线</h4>
			        	<p>各大应用平台审核上线，并定期<br />回访客户，即使处理反馈的问题</p>
			        </div>
			        <div class="left lc-item-arr">
			        	<img src="{{ config('app.source_url') }}home/image/arr02.png" alt="" />
			        </div>
			        <div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app6@2x.png" alt="" />
			        	<h4>测试修改</h4>
			        	<p>开发人员自测、性能测试、用户体验<br />测试等整体修改调整、规范完善</p>
			        </div>
			        <div class="left lc-item-arr">
			        	<img src="{{ config('app.source_url') }}home/image/arr02.png" alt="" />
			        </div>
			        <div class="left lc-item">
			        	<img src="{{ config('app.source_url') }}home/image/app5@2x.png" alt="" />
			        	<h4>沟通协议</h4>
			        	<p>功能开发、数据库设计、<br />管理后台搭建</p>
			        </div>
		        </div>
	        </div>
        </div>
        <!--分页4-->
        <div class="app-al-wrap">
	        <div class="app-al">
	        	<h3>App客户案例</h3>
	        	@if($caseList['data'])
	        	@foreach($caseList['data'] as $val)
	        	<div class="al_item">
	        		<img class="al_item_img" src="{{ imgUrl() }}{{ $val['logo'] }}" alt="" />
	        		<div class="al_code">
	        			<img src="{{ imgUrl() }}{{ $val['code'] }}" alt="" />
	        			<a href="/home/index/caseDetails?id={{ $val['id'] }}">
	        				<p>{{ $val['name'] }}</p>
	        			</a>
	        		</div>
	        	</div>
	        	@endforeach
	        </div>
	        <div class="al-btn">
	        	<a href="/home/index/1/shop">
	        		<button>查看更多&nbsp;→</button>
	        	</a>
	        </div>
	        @endif
        </div>
        <!--分页5-->
        <div class="app-sy">
        	<h3>App适用行业</h3>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/sheying@2x.png"/><p>摄影</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/hunqing@2x.png"/><p>婚庆</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/edc@2x.png"/><p>教育培训</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/yundong@2x.png"/><p>运动健身</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/out@2x.png"/><p>户外拓展</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/yangsheng@2x.png"/><p>生活养生</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/money@2x.png"/><p>金融</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/car@2x.png"/><p>汽车服务</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/zhubao@2x.png"/><p>珠宝</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/jiajv@2x.png"/><p>家居装潢</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/jiancai@2x.png"/><p>家装建材</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/yiliao@2x.png"/><p>医疗</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/zixun@2x.png"/><p>资讯</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/life@2x.png"/><p>生活服务</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/hotel@2x.png"/><p>商务酒店</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/canyin@2x.png"/><p>饭店餐饮</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/waimai@2x.png"/><p>外卖</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/cake@2x.png"/><p>蛋糕烘焙</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/KTV@2x.png"/><p>KTV</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/movie@2x.png"/><p>电影院</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/lvyou@2x.png"/><p>旅游</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/baoxian@2x.png"/><p>社会保险</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/hair@2x.png"/><p>美容美发</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/wenshen@2x.png"/><p>纹身</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/meijia@2x.png"/><p>美甲</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/wuye@2x.png"/><p>物业</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/food@2x.png"/><p>食品饮料</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/furit@2x.png"/><p>水果生鲜</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/yifu@2x.png"/><p>服装鞋帽</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/bao@2x.png"/><p>箱包配饰</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/muying@2x.png"/><p>母婴用品</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/daily@2x.png"/><p>日用百货</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/jiafang@2x.png"/><p>家纺装饰</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/jxiie@2x.png"/><p>机械五金</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/jiazheng@2x.png"/><p>家政</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/pet@2x.png"/><p>宠物</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/zhaopin@2x.png"/><p>招聘</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/kuaidi@2x.png"/><p>快递物流</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/licai@2x.png"/><p>投资理财</p></div>
        	<div class="hy_item"><img src="{{ config('app.source_url') }}home/image/game@2x.png"/><p>桌游娱乐</p></div>
        </div>
        <!--分页6-->
        <div class="app-yy">
        	<p>立即预约APP定制开发</p>
        	<a href="/home/index/reserve?type=2">
        		<button>立即预约</button>
        	</a>
        </div>
    </div>
@endsection
@section('foot.js') 
<script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}home/js/dingzhiAPP.js" type="text/javascript" charset="utf-8"></script>
@endsection