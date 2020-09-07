@extends('merchants.default._layouts')
@section('head_css')
    <!--<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/base3.css" />-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpkyf68.css" />
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')
	
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav"> 
            <li>  
                <a id="income">分销用户佣金详情</a>
            </li>  
            <li class="hover">  
                <a id="contacts">分销用户人脉</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content">
	<!--显示部分-->
	<div class="screen">
		<div class="screen_top">
		    <form id="form_1" class="form-inline">
		  		<!--<div class="form-group col-sm-4">
		    		<label>微信ID:</label>
		    		<span>2585469768</span>
		  		</div>-->
		  		<div class="form-group col-sm-4">
		    		<label>微信昵称:</label>
		    		<span></span>
		  		</div>
		  		<div class="form-group col-sm-4">
		    		<label>手机号:</label>
		    		<span></span>
		  		</div>
				<div class="form-group col-sm-4">
					<label>注册时间:</label>
					<span></span>
				</div>
			</form>
		</div>
		{{--<div class="screen_bottom">--}}
			{{--<form id="form_2" class="form-inline">--}}

		  		{{--<div class="form-group col-sm-4">--}}
		    		{{--<label>二级人脉:</label>--}}
		    		{{--<span></span>--}}
		  		{{--</div>--}}
		  		{{--<div class="form-group col-sm-4">--}}
		    		{{--<label>三级人脉:</label>--}}
		    		{{--<span></span>--}}
		  		{{--</div>--}}
			{{--</form>--}}
		{{--</div>--}}
		<div class="screen_bottom">
			<form id="form_3" class="form-inline">
				<div class="form-group col-sm-4">
					<label>下级人脉:</label>
					<span></span>
				</div>
				{{--<div class="form-group col-sm-3">--}}
					{{--<label>上级序号:</label>--}}
					{{--<span></span>--}}
				{{--</div>--}}
				<div class="form-group col-sm-4">
					<label>上级昵称:</label>
					<span></span>
				</div>
				<div class="form-group col-sm-4">
					<label>上级手机号:</label>
					<span></span>
				</div>
			</form>
		</div>
	</div>
	<!--列表名称-->
	<p class="shortHint">人脉详情</p>
	<!--客户列表-->
	<div class="member_list">
		<ul class="list_item list_header">
			<li>姓名</li>
			<li>购买次数</li>
			<li>手机号</li>
			<li>来源</li>
		</ul>
		<div class="list_div">
			
		</div>
		
	</div>
	<div class="main_bottom flex-end">
		<span id="pageInfo">
			<span><!--总条数：10 &nbsp;&nbsp; 当前页码1/10--></span>
			<a class="firstPage" href="##">首页</a>
			<a class="prevPage" href="##">上一页</a>
			<a class="nextPage" href="##">下一页</a>
			<a class="lastPage" href="##">尾页</a>
		</span>
	</div>
</div>
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpkyf68.js"></script>
@endsection