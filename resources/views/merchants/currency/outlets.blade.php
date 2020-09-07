@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_9miw6ohz.css" />
 @endsection 
 @section('slidebar') 
 @include('merchants.currency.slidebar') 
 @endsection 
 @section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 普通导航 开始 -->
		<ul class="common_nav">
			<li>
				<a href="{{URL('/merchants/currency/index')}}">店铺信息</a>
			</li>
			<li>
                <a href="{{URL('/merchants/currency/location')}}">商家地址库</a>
            </li>
			
			<li class="hover">
				<a href="{{URL('/merchants/currency/outlets')}}">门店管理</a>
			</li>
			
			<li>
                <a href="{{URL('/merchants/currency/share/set')}}">通用分享设置</a>
            </li>
		</ul>
		<!-- 普通导航 结束  -->
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
	<a href="{{URL('/merchants/currency/outletsAdd')}}" type="button" id="newAdd" class="btn btn-success">新建</a>
	<div class='path'>
		<span id='downCode' style='margin-left: 78px;color: #3197FA;font-size: 14px;cursor: pointer;'>门店路径</span>
		<div class='code'>
            <span id='triangle-up'></span>
			<ul class="clearfix">
				<li data-id='0' class="shop_code li_code_active" data-tab="qrcode">微商城二维码</li>
				<li data-id='1' class="link_code" data-tab="link" class="active">门店链接</li>
				<li data-id='2' class="xcx_code" data-tab="ewm">小程序二维码</li>
			</ul>
			<div class='code_img'>
				<div>
					<img src="" alt="">
				</div>
				<p data-id="0"></p>
			</div>
            <div class='copy clearfix'>
                <input type="text">
                <span id='copy_span'>复制</span>
            </div>
		</div>
	</div>
	<div id="showMsg">
		<table class="showMsgTitle table table-striped">
		  	<thead>
			    <tr>
			      	<th class="wd_15">门店名</th>
			      	<th class="wd_30">联系地址</th>
			      	<th class="wd_20">联系电话</th>
			      	<th class="wd_20">营业时间</th>
			      	<th class="wd_15">操作</th>
			    </tr>
		  	</thead>
		  	<tbody>
			@forelse($store[0]['data'] as $val)
			    <tr>
			      	<td>{{$val['title']}}</td>
			      	<td>{{$val['province_title']}}{{ $val['city_title']}}{{ $val['area_title']}}{{ $val['address']}}</td>
			      	<td>{{$val['phone']}}</td>
			      	<td>{{$val['start_time']}}～{{$val['close_time']}}</td> <!--  许立 2018年6月26日 关门时间修改 -->
			      	<td><a href="/merchants/currency/outletsAdd?id={{$val['id']}}" class="co_38f edit">编辑</a> - <span class="co_38f delete pop" data-id="{{$val['id']}}" data-toggle="delete_pop">删除</span></td>
			    </tr>
				@endforeach
			    <tr>
		  	</tbody>
		</table>
		@if(empty($store[0]['data']))
		<div class="addMsg">还没有相关数据</div>
			@endif
		{{$store[1]}}
	</div>
</div>
<!-- 删除弹框 -->
<div class="popover left delete_pop" role="tooltip" style="display: none;height: 55px;">
	<div class="arrow"></div>
	<div class="popover-content">
		<span>你确定要删除吗？</span>
		<button class="btn btn-primary sure_btn">确定</button>
		<button class="btn btn-default cancel_btn">取消</button>
	</div>
</div> 
@endsection

@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_9miw6ohz.js"></script>
@endsection