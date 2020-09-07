@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_qwhj4x9w.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">微信小程序</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->

        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
    <div class="content">
        <ul class="tab_nav">
            <li>
                <a href="/merchants/marketing/litePage">小程序微页面</a>
            </li>
            <li>
                <a href="/merchants/marketing/footerBar">底部导航</a>
            </li>
            <li class="">
                <a href="/merchants/marketing/xcx/topnav">首页分类导航</a>
            </li>
            <li class=""> <!-- update 梅杰 新增列表页-->
                <a href="/merchants/marketing/xcx/list">小程序列表</a>
            </li>
            <li class="">
                <a href="/merchants/marketing/liteStatistics">数据统计</a>
            </li>
        </ul>
		<div class="lite_div">
			<p>将微信小程序授权给会搜云，系统会自动帮您生成店铺小程序，并提交到微信；你不需要做复杂操作，即可获得店铺的微信小程序</p>
			<p>注意：你的小程序的主体必须是「企业」，并开通了微信支付，才能具备支付权限。</p>
			<a class="btn btn-primary set accredit" target="_blank" href="javascript:;">授权微信小程序</a>
		</div>	
		<div class="lite_div">
			<p>如果您还没有注册微信小程序，点击下方按钮注册；注册成功后，再授权给会搜云即可</p>
			<a class="btn lite_btn" href="https://mp.weixin.qq.com">注册微信小程序</a>
		</div>
    </div>
@endsection

@section('page_js')
<script src="{{ config('app.source_url') }}mctsource/js/liteapp_3fu9ck1g.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	//模态框居中控制
	$('.modal').on('shown.bs.modal', function (e) { 
	  	// 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
	  	$(this).css('display', 'block'); 
	  	var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
	  	if(modalHeight < 0){
	  		modalHeight = 0;
	  	}
	  	$(this).find('.modal-dialog').css({ 
	    	'margin-top': modalHeight 
	 	}); 
	});
	$("body").on('click','.btn-close',function(){
		$(".in").hide();
	})
</script>
@endsection