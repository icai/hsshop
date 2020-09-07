@extends('merchants.default._layouts') @section('head_css')
<!--bootstrape验证插件css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css" />
<!--图片上传引入的css文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<!--bootstrap datatimepicker时间插件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_8mv93ncz.css" /> 
@endsection 
@section('slidebar') 
@include('merchants.currency.slidebar') 
@endsection 
@section('middle_header')
<body onload="DefaultLocation()">
	<div class="middle_header">
		<!-- 三级导航 开始 -->
		<div class="third_nav">
			<!-- 面包屑导航 开始 -->
			<ul class="crumb_nav">
				<li>
					<a href="{{ URL('/merchants/currency/outlets') }}">门店管理</a>
				</li>
				<li>
					<a href="javascript:void(0);">新建门店</a>
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
	<form class="form-horizontal content" id="defaultForm" method="post" action="">
		<input type="hidden" name="imgs" value="@if($storeData){{$storeData['imgs']}}@endif">
		<input type="hidden" name="latitude" value="@if($storeData){{$storeData['latitude']}}@endif">
		<input type="hidden" name="longitude" value="@if($storeData){{$storeData['longitude']}}@endif">
		<input type="hidden" name="id" value="@if($storeData) {{$storeData['id']}} @endif" >
		<!--门店名称-->
		<div class="form-group linkage store">
			<label class="col-lg-2 control-label"><i>*</i>店铺名称：</label>
			<div class="col-lg-8">
				<input type="tel" name="title" id="title" class="form-control" placeholder="门店名称 最长支持20个字符" value="@if(!empty($storeData)){{$storeData['title']}}@endif" />
			</div>
		</div>
		<!--联系电话部分-->
		<div class="form-group linkage phone">
			<label class="col-lg-2 control-label"><i>*</i>客服电话：</label>
			<div class="col-lg-8">
				<input type="tel" name="phone[]" id="first_number" class="form-control" style="width: 50px; text-align: center;" placeholder="区号" value="@if(!empty($storeData)){{$storeData['phone'][0]}}@endif" />&nbsp;-
				<input type="tel" name="phone[]" id="last_number" class="form-control" placeholder="请输入电话号码（区号可为空）" value="@if(!empty($storeData)){{$storeData['phone'][1]}}@endif" />
			</div>
		</div>
		<!--联系地址部分-->
		<div class="form-group linkage adress">
			<label class="col-lg-2 control-label"><i>*</i>联系地址：</label>
			<div class="col-lg-8">
				<!--三级联动块-->
				<div class="control-group" style="display: inline-block;">
					<div class="controls">
						<select class="js-province" name="province_id" id="location_p">
							<option value=''>选择省</option>
							@forelse($provinceList as $value)
								<option  @if(isset($storeData['province_id']) && $value['id'] == $storeData['province_id']) selected="selected" @endif value="{{$value['id']}}">{{$value['title']}}</option>
								@endforeach
						</select>
						<select class="js-city" name="city_id" id="location_c">
							<option value=''>选择城市</option>
							@if($storeData && isset($regionList[$storeData['province_id']]))
								@forelse($regionList[$storeData['province_id']] as $val)
									<option @if($val['id'] == $storeData['city_id']) selected="selected" @endif value="{{$val['id']}}" >{{$val['title']}}</option>
							@endforeach
							@endif
						</select>
						<select class="js-county" name="area_id" id="location_a">
							<option value=''>选择地区</option>
							@if($storeData && isset($regionList[$storeData['city_id']]))
								@forelse($regionList[$storeData['city_id']] as $val)
									<option @if($val['id'] == $storeData['area_id']) selected="selected" @endif value="{{$val['id']}}" >{{$val['title']}}</option>
									@endforeach
									@endif
						</select>
					</div>
				</div>
			</div>
		</div>
		<div id="indetailAdd" class="form-group linkage">
			<label class="col-lg-2 control-label"><i>*</i>详细地址：</label>
			<div class="col-lg-8">
				<input type="text" name="address" id="addTxt" class="form-control" placeholder="请填写详细地址以便买家联系；（勿重复填写省市区地址）" value="@if(!empty($storeData)){{$storeData['address']}}@endif"  />
				<button type="button" id="addBtn" class="btn btn-default" onclick="searchKeyword()">搜索地图</button>
			</div>
		</div>
		<!--地图显示部分-->
		<div class="form-group linkage map">
			<label class="col-lg-2 control-label"><i>*</i>地图定位：</label>
			<div class="col-lg-8 search_map">
				<div id="s_result"></div>
				<div id="mapShow">
					<!--插入地图-->
				</div>
			</div>
		</div>
		<!--门店照片-->
		<div class="form-group linkage img">
			<label class="col-lg-2 control-label"><i>*</i><tit style="display: inline-block;">门店照片：</tit></label>
			<div class="col-lg-8">
				<div id="selImg" style="display: inline-block;">
					@if(isset($storeData['file']))
					@foreach($storeData['file'] as $val)
					<span class="showLittleImg"><img src="{{ config('app.source_img_url') }}{{$val['s_path']}}" class="addSeleImg"><i class="imgClose" style="color:white;">×</i></span>
					@endforeach
					@endif
					<a href="javascript:void(0);" @if(isset($storeData['file']) && count($storeData['file'])>=4)style="display:none;" @endif id="addImg">+加图</a>
					<span style='color:#8a8a8a;margin-left: 20px;font-size: 14px;'>建议上传图片尺寸750X380</span>
				</div>
			</div>
		</div>
		<!--运营时间 新需求显示 许立 2018年6月26日-->
		<div class="form-group linkage beginTime">
			<label class="col-lg-2 control-label">运营时间：</label>
			<div class="start">
				<input type='text' class="timepicker inp_time" class="form-control" id='startTime' name="start_time[]" value="@if(!empty($storeData['open_time'])){{$storeData['open_time']}}@endif" />
				<div class='times_xian'>—</div>
				<input type='text' class="timepicker inp_time" name="end_time[]" class="form-control" id='endTime' value="@if(!empty($storeData['close_time'])){{$storeData['close_time']}}@endif" />
				<ul class='times_ul'>
					<li @if(!empty($storeData['monday'])) class="active" @endif>
						<span>周一</span>
						<input type="text" name='monday' @if(!empty($storeData['monday'])) value='1' @else value='0' @endif>
					</li>
					<li @if(!empty($storeData['tuesday'])) class="active" @endif>
						<span>周二</span>
						<input type="text" name='tuesday' @if(!empty($storeData['tuesday'])) value='1' @else value='0' @endif>
					</li>
					<li @if(!empty($storeData['wednesday'])) class="active" @endif>
						<span>周三</span>
						<input type="text" name='wednesday' @if(!empty($storeData['wednesday'])) value='1' @else value='0' @endif>
					</li>
					<li @if(!empty($storeData['thursday'])) class="active" @endif>
						<span>周四</span>
						<input type="text" name='thursday' @if(!empty($storeData['thursday'])) value='1' @else value='0' @endif>
					</li>
					<li @if(!empty($storeData['friday'])) class="active" @endif>
						<span>周五</span>
						<input type="text" name='friday' @if(!empty($storeData['friday'])) value='1' @else value='0' @endif>
					</li>
					<li @if(!empty($storeData['saturday'])) class="active" @endif>
						<span>周六</span>
						<input type="text" name='saturday' @if(!empty($storeData['saturday'])) value='1' @else value='0' @endif>
					</li>
					<li @if(!empty($storeData['sunday'])) class="active" @endif>
						<span>周日</span>
						<input type="text" name='sunday' @if(!empty($storeData['sunday'])) value='1' @else value='0' @endif>
					</li>
				</ul>
			</div>
		</div>
		<!--商家推荐-->
		<div class="form-group linkage recommend">
			<label class="col-lg-2 control-label"><in style="display: inline-block; vertical-align: top;">商家推荐：</in></label>
			<div class="col-lg-8">
				<textarea name="comment" rows="4" cols="40" style="resize: both; padding: 5px" placeholder="你可以简述门店的推荐或者活动，也可以向买家陈述特色服务，例如免费停车和WIFI。（最多200个字）">@if(!empty($storeData)){{$storeData['comment']}}@endif</textarea>
			</div>
		</div>
		<!--保存和返回-->
		<div class="form-group linkage saveReturn">
			<label class="col-lg-2 control-label"></label>
			<div class="col-lg-8" style="margin-top:3px">
				<button type="submit" id="saveBtn" class="btn btn-primary">保存</button>
				<a href="javascript:history.go(-1);" type="button" id="returnBtn" class="btn btn-default">返回</a>
			</div>
		</div>
	</form>
	<!-- 广告图片model -->
	<div class="modal export-modal myModal-adv" id="myModal-adv" onselectstart="return false" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog" id="modal-dialog-adv">
			<form class="form-horizontal">
				<div class="modal-content modal_content_1" style="width: 863px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
	                        <span aria-hidden="true">&times;</span>
	                        <span class="sr-only">Close</span>
	                    </button>
						<ul class="module-nav modal-tab">
							<li class="active">
								<a href="#js-module-goods" class="js-modal-tab">我的图片</a>
							</li>
						</ul>
						<div class="search-region">
							<div class="ui-search-box">
								<input class="txt js-search-input" type="text" placeholder="搜索" value="">
							</div>
						</div>
					</div>
					<div class="modal-body">
						<div class="category-list-region">
							<ul class="category-list">
	
							</ul>
						</div>
						<div class="attachment-list-region attachment_1">
							<ul class="image-list">
								
							</ul>
							<div class="attachment-pagination">
	                            <div class= "picturePage"></div><!-- 分页 -->
	                        </div>
							<a href="#uploadImg" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;">上传图片</a>
						</div>
						<!--列表中的图片个数为0的时候显示这个模态框-->
						<div class="attachment-list-region Img_add no">
							<div id="layerContent_right">
								<a href="#uploadImg">+</a>
								<p>暂无数据，点击添加</p>
							</div>
						</div>
					</div>
					<div class="modal-footer clearfix">
						<div class="selected-count-region">
							已选择<span class="js-selected-count">0</span>张图片
						</div>
						<div class="text-center">
							<button type="button" class="ui-btn js-confirm" disabled="disabled">确认</button>
						</div>
					</div>
				</div>
				<!--上传图片模态框-->
				<div class="modal-content modal_content_2 no">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
	                        <span aria-hidden="true">&times;</span>
	                        <span class="sr-only">Close</span>
	                    </button>
						<ul class="module-nav modal-tab">
							<li class="active">
								<a href="#layer" class="js-modal-tab" style="color: #27f;">
									<选择图片</a>
								<a href="##" class="js-modal-tab">| 上传图片</a>
							</li>
						</ul>
					</div>
					<div class="modal-body" id="uploadLayerContent">
						<div id="uploadLayerContent_botm">
							<div id="wrapper">
								<div id="container">
									<!--头部，相册选择和格式选择-->
									<div id="uploader">
										<div class="queueList">
											<div id="dndArea" class="placeholder">
												<label id="filePicker"></label>
												<p>或将照片拖到这里，单次最多可选300张</p>
											</div>
										</div>
										<div class="statusBar" style="display:none;">
											<div class="progress">
												<span class="text">0%</span>
												<span class="percentage"></span>
											</div>
											<div class="info"></div>
											<div class="btns">
												<div id="filePicker2"></div>
												<div class="uploadBtn">开始上传</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer clearfix">
						<div class="text-center">
							<button type="button" class="ui-btn js-confirm" disabled="disabled">确认</button>
						</div>
					</div>
				</div>
	
			</form>
			<input type="hidden" name="classifyId">
		</div>
	</div>
</body>
<script type="text/javascript">
    var json = {!! $regions !!};
    // json = JSON.parse('[1, 5, "false"]');
</script>
@endsection 
@section('page_js')
<!--地图接口-->
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=FLIBZ-34ELI-C6WGO-5HIAO-6QBPE-KKB2D"></script>
<!--地图的方法-->
<script src="{{ config('app.source_url') }}mctsource/js/self_public/map_public.js" type="text/javascript" charset="utf-8"></script>
<!--分页器js引入-->
<script src="{{config('app.source_url')}}static/js/jqPaginator.min.js" type="text/javascript" charset="utf-8"></script>
<!--bootstrap表单验证插件js-->
<script src="{{config('app.source_url')}}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
<!--bootstrap-datatimepicker 时间插件-->
<script src="{{ config('app.source_url') }}static/js/moment.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/locales.min.js"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js"></script>
<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<!--图片上传引入的js文件-->
<script src="{{ config('app.source_url') }}static/js/webuploader.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_8mv93ncz.js"></script>
@endsection