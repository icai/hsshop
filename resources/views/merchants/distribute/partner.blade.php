@extends('merchants.default._layouts')
@section('head_css')
    <!-- 选择商品样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" /> 
	
    <!--当前页面-->
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpkkkf2.css" />
	
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
            <li class="hover">  
                <a href="{{ URL('/merchants/distribute/partner') }}">分销合伙人</a>
            </li>  
			<li>  
                <a href="{{ URL('/merchants/distribute/applayMemberList') }}">分销审核</a>
            </li>
			<li>  
                <a href="{{ URL('/merchants/distribute/purgeLog') }}">清退记录</a>
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
	<!--筛选部分-->
	<div class="screen">
	    <form class="form-inline">
	  		
	  		<div class="form-group col-sm-4">
	    		<label for="nickName">微信昵称:</label>
	    		<input type="email" class="form-control" id="nickName" placeholder="微信昵称">
	  		</div>
	  		<div class="form-group col-sm-4">
	    		<label for="phoneNum">手机号:</label>
	    		<input type="email" class="form-control" id="phoneNum" placeholder="手机号码">
	  		</div>
	  		<div class="form-group col-sm-4">
	    		<label for="phoneNum">来源:</label>
	    		<select name="" id="orderSource" class="form-control">
	    			<option value="">全部</option>
	    			<option value="1">微商城</option>
	    			<option value="7">小程序</option>
	    		</select>
	  		</div>
	  		<br />
		</form>
		<div class="btns_clean">
			<button id="search" class="btn btn-primary">筛选</button>
			<a id="clearJudge" href="##">清空筛选条件</a>
		</div>
	</div>
	<!--添加按钮-->
	<div class="addBtn">
	</div>
	<!--客户列表-->
	<div class="member_list">
		<ul class="list_item list_header">
			<li>微信昵称</li>
			<li>手机号码</li>
			<li>来源</li>
			<li>分销等级</li>
			<li>可提现佣金</li>
			<li class="sort J_sort" data-type="total_cash" data-sort="1">累积佣金</li>
			<li class="sort J_sort" data-type="son_num" data-sort="1">下级用户数</li>
			<li class="sort J_sort" data-type="trade_amount" data-sort="1">下级用户交易额</li>
			<li class="sort J_sort active1" data-type="created_at" data-sort="2">注册时间</li>
			<li>操作</li>
		</ul>
		<div class="list_div">
			
		</div>
	</div>
	<div class="main_bottom flex-end">
		<span id="pageInfo">
			<span></span>
			<a class="firstPage" href="##">首页</a>
			<a class="prevPage" href="##">上一页</a>
			<a class="nextPage" href="##">下一页</a>
			<a class="lastPage" href="##">尾页</a>
		</span>
	</div>
    <input type="hidden" id="wid" value="{{session('wid')}}" />
    <!--弹框 选取商品-->
</div>
<div class="popup js-popup">
	<div class="popup-wraper">
		<span class="close-wraper js-close-wraper">X</span>
		<p class="popup-title">是否清退该分销客身份？</p>
		<p class="popup-desc-title"><span>* </span>清退理由：</p>
		<textarea name="" id="" class="reason js-reason"></textarea>
		<div class="popup-btns">
			<div class="clear-btn cancle-btn js-cancle-btn">取消</div>
			<div class="clear-btn sure-btn js-sure-btn">确定</div>
		</div>
	</div>
</div>
<div class="operate-popup">
	<div class="sel-goods">添加下级</div>
	<div class="give_commission">添加佣金</div>
	<div class="set_top_level">
		<span>设为顶级</span>
		<i style="color:#49c800;margin-left:5px" class="glyphicon glyphicon-question-sign f14"></i>
		<p class="top_level_tip">设置成顶级，无法再绑定上级关系</p>
	</div>
	<div class="distribution-level">分销等级设置</div>
	@if($distribute_grade == 1)
	<div class="backward">清退</div>
	@endif
</div>

@endsection
@section('page_js')
    <script>
        var _host = "{{ imgUrl() }}";
        var distribute_grade = '{{$distribute_grade}}';
        console.log(distribute_grade);
        var title = "{{$shop['distribute_default_grade_title']}}";
    </script>
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpkkkf2.js"></script>
@endsection