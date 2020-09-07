@extends('home.mobile.default._layouts')

@section('title',$title)


@section('css')
	<meta name="keywords" content="App定制">
	<meta name="description" content="会搜股份荣誉出品，会搜云专注做APP定制全套解决方案，提供App定制哪个公司好、如何制作、找谁做、效果怎样、好用吗？">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/AppCustomize.css">
@endsection
@section('content')
    <div class="content">
    	<!--分页1-->
        <div class="app_header">
        	<a href="/home/index/reserve?type=4">
				<p>立即预约</p>
			</a>
        </div>
        <!--分页2-->
        <div class="app_ys">
        	<h2>会搜云APP定制优势</h2>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/ydfg@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>全面覆盖移动终端</h3>
        			<p>ios App + 安卓App + <br />小程序 + 微商城 +  pc网站</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/jslx.png" alt="" />
        		<div class="ys-item-r">
        			<h3>行业技术领先</h3>
        			<p>独有App更新技术、模拟预览技术<br />主流手机完美适配</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/ztpz@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>主题丰富模块自由搭配</h3>
        			<p>可视化操作，功能自由搭配<br />个性化编辑排版</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/zxtj@2x.png" alt="" />
        		<div class="ys-item-r" style="padding-top: .65rem;">
        			<h3>资讯头条推荐一目了然</h3>
        			<p>让用户快速找到所需产品</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/ybzc@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>用户注册一步到位</h3>
        			<p>通过移动端、<br />微信等第三方登录</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/tsdz@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>App电商特色</h3>
        			<p>分销系统、会员系统<br />优惠活动、营销插件</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/1v1dz@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>1v1定制时时对接</h3>
        			<p>独立项目小组对接<br />制作工期加速</p>
        		</div>
        	</div>
        	<div class="ys-item">
        		<img src="{{ config('app.source_url') }}mobile/images/sydz@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>多种多样商业盈利模式</h3>
        			<p>自营电商、联动营销<br />招商入住、付费发布</p>
        		</div>
        	</div>
        	<div class="ys-item" style="border: none;">
        		<img src="{{ config('app.source_url') }}mobile/images/ptqq@2x.png" alt="" />
        		<div class="ys-item-r">
        			<h3>配套服务齐全</h3>
        			<p>免费培训后台操作<br />提供运营推广学习课程</p>
        		</div>
        	</div>
        </div>
        <!--分页3-->
        <div class="app-lc">
        	<h2>小程序定制开发服务流程</h2>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/1@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>沟通协议</h3>
        			<p>前期沟通初步达成合作，签订开发合作协议</p>
        		</div>
        	</div>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/2@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>品牌定位</h3>
        			<p>了解产品品牌文化，确定品牌定位</p>
        		</div>
        	</div>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/3@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>需求评估</h3>
        			<p>分析行业用户功能需求，做好产品设计</p>
        		</div>
        	</div>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/4@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>产品设计</h3>
        			<p>初步完成产品设计，沟通修改细节</p>
        		</div>
        	</div>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/5@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>功能开发</h3>
        			<p>进行产品后台设计与功能开发</p>
        		</div>
        	</div>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/6@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>测试修改</h3>
        			<p>修改调整、规范完善</p>
        		</div>
        	</div>
        	<div class="lc-item">
        		<img src="{{ config('app.source_url') }}mobile/images/7@2x.png" alt="" />
        		<div class="lc-item-r">
        			<h3>审核上线</h3>
        			<p>提交微信小程序平台，审核通过上线</p>
        		</div>
        	</div>
        </div>
        <!--分页4-->
        <div class="app-al">
        	<h2>会搜云APP客户案例</h2>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz01@2x.png" alt="" />
        		<p>全球厨房电器</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz02@2x.png" alt="" />
        		<p>中国和田玉</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz03@2x.png" alt="" />
        		<p>中国制服</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz04@2x.png" alt="" />
        		<p>唐人社区</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz05@2x.png" alt="" />
        		<p>全球珠宝首饰</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz06@2x.png" alt="" />
        		<p>新抗原产品</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz07@2x.png" alt="" />
        		<p>中国皮具</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz08@2x.png" alt="" />
        		<p>家纺优购</p>
        	</div>
        	<div class="al-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz09@2x.png" alt="" />
        		<p>果拼多</p>
        	</div>
        </div>
        <!--分页5-->
        <div class="app-hy">
        	<h2>APP适用行业</h2>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/sheying@2x.png" alt="" />
        		<p>摄影</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/hunqing@2x.png" alt="" />
        		<p>婚庆</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/edc@2x.png" alt="" />
        		<p>教育培训</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/yundong@2x.png" alt="" />
        		<p>运动健身</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/yangsheng@2x.png" alt="" />
        		<p>生活养生</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/money@2x.png" alt="" />
        		<p>金融</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/jiancai@2x.png" alt="" />
        		<p>家装建材</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/zixun@2x.png" alt="" />
        		<p>资讯</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/waimai@2x.png" alt="" />
        		<p>外卖</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/lvyou@2x.png" alt="" />
        		<p>旅游</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/hair@2x.png" alt="" />
        		<p>美容美发</p>
        	</div>
        	<div class="hy-item">
        		<img src="{{ config('app.source_url') }}mobile/images/dz-more@2x.png" alt="" />
        		<p>更多</p>
        	</div>
        </div>
        <!--分页6-->
        <div class="app-yuyue">
			<a href="/home/index/reserve?type=4">
				<p>立即预约APP定制</p><span><img src="{{ config('app.source_url') }}mobile/images/zc-arr.png" alt="" /></span>
			</a>
		</div>
		<!--底部-->
        <a class="order-footer" href="/home/index/reserve?type=4">我要预约</a>
    </div>

@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection
@section('js')

@endsection