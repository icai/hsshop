@extends('merchants.default._layouts') @section('head_css')
<!--bootstrape验证插件css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_8be6q1lz.css" /> @endsection @section('slidebar') @include('merchants.currency.slidebar') @endsection @section('middle_header')
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
				<a href="{{URL('/merchants/currency/contact')}}">联系我们</a>
			</li>
			<li>
				<a href="{{URL('/merchants/currency/outlets')}}">门店管理</a>
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
@endsection @section('content')
<form class="form-horizontal content" id="defaultForm" method="post" action="">
	<!--联系电话部分-->
	<div class="form-group linkage phone">
		<label class="col-lg-2 control-label"><i>*</i>客服电话：</label>
		<div class="col-lg-8">
			<input type="tel" name="first_number" id="first_number" class="form-control" style="width: 50px; text-align: center;" placeholder="区号" />&nbsp;-
			<input type="tel" name="last_number" id="last_number" class="form-control" style="width: 243px;" placeholder="请输入电话号码（区号可为空）" />
		</div>
	</div>
	<!--联系地址部分-->
	<div class="form-group linkage adress">
		<label class="col-lg-2 control-label"><i>*</i>联系地址：</label>
		<div class="col-lg-8">
			<!--三级联动块-->
			<div class="control-group" style="display: inline-block;">
				<div class="controls">
					<select name="location_p" id="location_p">
					</select>
					<select name="location_c" id="location_c">
					</select>
					<select name="location_a" id="location_a">
					</select>
					<script src="{{ config('app.source_url') }}static/js/region_select.js"></script>
					<script type="text/javascript">
						new PCAS('location_p', 'location_c', 'location_a', '北京市', '', '');
					</script>
				</div>
			</div>
			<input type="text" name="add" id="addTxt" class="form-control" value="杭州" />
			<button type="button" id="addBtn" class="btn btn-default">搜索地图</button>
		</div>
	</div>
	<!--地图显示部分-->
	<div class="form-group linkage map">
		<label class="col-lg-2 control-label"><i>*</i>地图定位：</label>
		<div class="col-lg-9">
			<div id="mapShow">
				<!--插入地图-->
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label"></label>
		<div class="col-lg-4">
			<button type="submit" id="saveBtn" class="btn btn-primary">保存</button>
		</div>
	</div>
	<div class="successPromrt hide">保存成功</div>
</form>
@endsection @section('page_js')
<!--地图接口-->
<script src="https://api.map.baidu.com/api?v=2.0&ak=Gl9ARRgPlcASCW55a33dw5AE8URjrKRu"></script>
<!--地图的方法-->
<script src="{{ config('app.source_url') }}mctsource/js/self_public/map_public.js" type="text/javascript" charset="utf-8"></script>
<!--bootstrap表单验证插件js-->
<script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_8be6q1lz.js"></script>
@endsection