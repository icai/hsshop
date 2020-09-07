@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_9ps47mzo.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/location.css" />
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
            <li class="hover">
                <a href="javascript:;">商家地址库</a>
            </li>
            
            <li>
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
	<div class="box">
		<a class="location-btn" href="/merchants/currency/editAddress">新增地址</a>
		<table class="location-table">
			<thead>
				<tr>
					<th class="text-left cell-8">联系人</th>
					<th class="text-left cell-15">联系方式</th>
					<th class="text-left cell-50">地址</th>
					<th class="text-left cell-16">地址类型</th>
					<th class="text-left cell-12">操作</th>
				</tr>
			</thead>
		<tbody>
		@forelse($address as $val)
			<tr>
				<td class="">{{$val['name']}}</td>
				<td>
					<span class="">{{$val['mobile']}}</span>
				</td>
				<td>
					<div class="" style="display: inline-block;">
						<div>{{$val['province_id']}}{{$val['city_id']}}{{$val['area_id']}}{{$val['address']}}</div>
					</div>
				</td>
				<td class="location-type" style="min-width: 120px;">
					@if($val['type'] == 0)
						<div class="show">退货地址@if($val['is_default'] == 1)<span class="show-sp">默认</span>@endif</div>
					@elseif($val['type'] == 1)
						<div class="show">收票地址@if($val['is_default'] == 1)<span class="show-sp">默认</span>@endif</div>
					@elseif($val['type'] == 2)
					<div class="show">发货地址@if($val['is_send_default'] == 1)<span class="show-sp">默认</span>@endif</div>
					@elseif($val['type'] == 3)
					<div class="show">
						退货地址@if($val['is_default'] == 1)<span class="show-sp">默认</span>@endif  /
						发货地址@if($val['is_send_default'] == 1)<span class="show-sp">默认</span>@endif
					</div>
					@endif


				</td>
				<td class="location-action" style="min-width: 120px;">
					<a class="ui-btn ui-btn-link" href="/merchants/currency/editAddress?id={{$val['id']}}">编辑</a>
					|<div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;">
					<a class="ui-btn disabled a-shanchu" href="javascript:;" data-id ={{$val['id']}}>删除</a>									
					</div>
				</td>
			</tr>
			@endforeach
		</tbody>
		</table>
		
	</div>
    
@endsection
@section('page_js')
<!--layer文件引入-->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<!--上传图片js-->
<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 当前页面js -->
<script type="text/javascript">
    var imgUrl = "{{ config('app.source_url') }}" + 'mctsource/';
    var _type = "0";
	var _default = "0";
	var _send_default = "0";
</script>
<script src="{{ config('app.source_url') }}mctsource/js/currency_9ps47mzo.js"></script>
@endsection

