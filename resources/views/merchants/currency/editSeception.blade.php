@extends('merchants.default._layouts') @section('head_css')
<!--bootstrape验证插件css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css" />
<!--图片上传引入的css文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css" />
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_editSeception.css" /> 
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
					<a href="{{ URL('/merchants/currency/receptionList') }}">上门自提</a>
				</li>
				<li>
					<a href="javascript:void(0);">{{ $title }}</a>
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
	@endsection @section('content')
	<form class="form-horizontal content" id="defaultForm" method="post" action="">
		<input type="hidden" name="images" class="" value="{{ $data['images'] or '' }}" />
		<input type="hidden" name="id" class="" value="{{ $data['id'] or 0 }}" />
		<!--自提点名称-->
		<div class="form-group linkage store">
			<label class="col-lg-2 control-label"><i>*</i>自提点名称：</label>
			<div class="col-lg-8">
				<input type="text" name="title" id="title" class="form-control" placeholder="请填写自提点地址便于买家理解和管理" value="{{ $data['title'] or '' }}" />
			</div>
		</div>
		<!--联系地址部分-->
		<div class="form-group linkage adress">
			<label class="col-lg-2 control-label"><i>*</i>自提点地址：</label>
			<div class="col-lg-8">
				<!--三级联动块-->
				<div class="control-group" style="display: inline-block;">
					<div class="controls">
						<select class="js-province" name="provinceId" id="location_p">
							<option value=''>选择省份</option>
							@foreach($provinceList as $val)
							<option value="{{ $val['id'] }}" @if(isset($data['province_id']) && $data['province_id'] == $val['id']) selected="selected" @endif>{{ $val['title'] }}</option>
							@endforeach
						</select>
						<select class="js-city" name="cityId" id="location_c">
							<option value=''>选择城市</option>
							@if($cityList)
								@foreach($cityList as $city)
								<option value="{{ $city['id'] }}" @if(isset($data['city_id']) && $data['city_id'] == $city['id']) selected="selected" @endif>{{ $city['title'] }}</option>
								@endforeach
							@endif
						</select>
						<select class="js-county" name="areaId" id="location_a">
							<option value=''>选择地区</option>
							@if($areaList)
								@foreach($areaList as $area)
								<option value="{{ $area['id'] }}" @if(isset($data['area_id']) && $data['area_id'] == $area['id']) selected="selected" @endif>{{ $area['title'] }}</option>
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
				<input type="text" name="address" id="addTxt" class="form-control" placeholder="请填写自提点的具体地址，最短5个字符，最长120字"  value="{{ $data['address'] or '' }}" />
				<button type="button" id="addBtn" class="btn btn-default" onclick="searchKeyword()">搜索地图</button>
			</div>
		</div>
		<!--地图显示部分-->
		<div class="form-group linkage map">
			<label class="col-lg-2 control-label"><i>*</i>地图定位：</label>
			<div class="col-lg-8 search_map" data-lng="{{ $data['longitude'] or 0 }}"  data-lat="{{ $data['latitude'] or 0 }}">
				<div id="s_result"></div>
				<div id="mapShow">
					<!--插入地图-->
				</div>
			</div>
	    </div>
	    <!--联系电话部分-->
		<div class="form-group linkage phone">
			<label class="col-lg-2 control-label"><i>*</i>联系电话：</label>
			<div class="col-lg-8">
				<input type="tel" name="first_number" id="first_number" class="form-control" style="width: 50px; text-align: center;" placeholder="区号" value="{{ $data['area_code'] or '' }}" />&nbsp;-
				<input type="tel" name="last_number" id="last_number" class="form-control" placeholder="请填写准确联系电话，便于买家联系（区号可空）" value="{{ $data['phone'] or '' }}" />
				<input type="hidden" name="telphone" class="form-control" id="telphone" value="{{ $data['telphone'] or '' }}"/>
			</div>
	    </div>
	    <!--接待时间-->
	    <div class="form-group linktime">
	        <label for="" class="col-sm-2 control-label"><i>*</i>接待时间：</label>
	        <div class="col-sm-8 Time" >
	            <div class="timeDivShow timeDivShow1">
	            	@if(isset($data['reception_timesArr']) && $data['reception_timesArr'])
	            	@foreach($data['reception_timesArr'] as $reTime)
					<p>
						<t> {{ $reTime['startTime'] }} ~ {{ $reTime['endTime'] }} </t>
						<n style='display:inline-block'>{{ $reTime['weekDay'] }}</n>
						<a href='##' class='del1'>  | 删除</a>
					</p>
					@endforeach
					@endif
				</div>
	            <div class="selectTimeDiv selectTimeDiv1 @if($data) hide @endif">
	                <select name="beginHour">
	                    <option value="00">00</option>
	                    <option value="01">01</option>
	                    <option value="02">02</option>
	                    <option value="03">03</option>
	                    <option value="04">04</option>
	                    <option value="05">05</option>
	                    <option value="06">06</option>
	                    <option value="07">07</option>
	                    <option value="08">08</option>
	                    <option value="09">09</option>
	                    <option value="10">10</option>
	                    <option value="11">11</option>
	                    <option value="12">12</option>
	                    <option value="13">13</option>
	                    <option value="14">14</option>
	                    <option value="15">15</option>
	                    <option value="16">16</option>
	                    <option value="17">17</option>
	                    <option value="18">18</option>
	                    <option value="19">19</option>
	                    <option value="20">20</option>
	                    <option value="21">21</option>
	                    <option value="22">22</option>
	                    <option value="23">23</option>
	                </select> 时
	                <select name="beginMinut">
	                    <option value="00">00</option>
	                    <option value="30">30</option>
	                </select> 分 ~
	                <select name="endHour">
	                    <option value="00">00</option>
	                    <option value="01">01</option>
	                    <option value="02">02</option>
	                    <option value="03">03</option>
	                    <option value="04">04</option>
	                    <option value="05">05</option>
	                    <option value="06">06</option>
	                    <option value="07">07</option>
	                    <option value="08">08</option>
	                    <option value="09">09</option>
	                    <option value="10">10</option>
	                    <option value="11">11</option>
	                    <option value="12">12</option>
	                    <option value="13">13</option>
	                    <option value="14">14</option>
	                    <option value="15">15</option>
	                    <option value="16">16</option>
	                    <option value="17">17</option>
	                    <option value="18">18</option>
	                    <option value="19">19</option>
	                    <option value="20">20</option>
	                    <option value="21">21</option>
	                    <option value="22">22</option>
	                    <option value="23">23</option>
	                </select> 时
	                <select name="endMinut">
	                    <option value="00">00</option>
	                    <option value="30">30</option>
	                </select> 分
	                <div class="weekDiv weekDiv1">
	                    <div class="week week1 Mon" data-index="1">周一 <div class="weekBoard hide"></div></div>
	                    <div class="week week1 Tues" data-index="2">周二 <div class="weekBoard hide"></div></div>
	                    <div class="week week1 Wen" data-index="3">周三 <div class="weekBoard hide"></div></div>
	                    <div class="week week1 Thurs" data-index="4">周四 <div class="weekBoard hide"></div></div>
	                    <div class="week week1 Fri" data-index="5">周五 <div class="weekBoard hide"></div></div>
	                    <div class="week week1 Sat" data-index="6">周六 <div class="weekBoard hide"></div></div>
	                    <div class="week week1 Sun" data-index="7">周日<div class="weekBoard hide"></div></div>
	                </div>
	                <a href="##" id="beSure">确认 </a><a href="##" class="cancle1">| 取消</a>
	            </div>
	            <a href="javascript:void(0)" class="addTimeDiv1 @if(empty($data)) hide @endif" style="color: #27f;">新增时间段</a>
	            <input type="hidden" name="receptionTimes" id="store_time" value="" />
	        </div>
		</div>
		<!-- <div class="form-group">
			<label for="" class="col-sm-2 control-label">自提时间：</label>
			<div class="col-lg-8" style="padding-top:6px;">
				<label for="is_set_time_fl" style="font-size: 13px;">
					<input type="checkbox" id="is_set_time_fl" name="is_set_time_fl" value=""  class="checkout" @if(isset($data['is_set_time']) && $data['is_set_time']) checked @endif/>需要买家选择自提时间
					<input type="hidden" name="is_set_time" id="is_set_time" value="{{ $data['is_set_time'] or 0 }}">
				</label>
				<p class="tips-title">勾选后，买家下单选择上门自提，必须选择自提时间，卖家需要按约定时间备货</p>
				<p class="tips-title">不勾选，将会提示买家尽快到点自提</p>
			</div>
		</div> -->
		<!--自提时段-->
		<!-- <div class="form-group linktime J_set-time">
			<label for="" class="col-sm-2 control-label"><i>*</i>自提时段：</label>
			<div class="col-sm-8 Time" >
				<div class="timeDivShow timeDivShow2">
					@if(isset($data['ziti_timesArr']) && $data['ziti_timesArr'])
	            	@foreach($data['ziti_timesArr'] as $ziTime)
					<p>
						<t>{{ $ziTime['startTime'] }} ~ {{ $ziTime['endTime'] }} </t>
						<n style='display:inline-block'>{{ $ziTime['weekDay'] }}</n>
						<a href='##' class='del1'>  | 删除</a>
					</p>
					@endforeach
					@endif
				</div>
				<div class="selectTimeDiv selectTimeDiv2 @if($data) hide @endif">
					<select name="beginHour2">
						<option value="00">00</option>
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
					</select> 时
					<select name="beginMinut2">
						<option value="00">00</option>
						<option value="30">30</option>
					</select> 分 ~
					<select name="endHour2">
						<option value="00">00</option>
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
					</select> 时
					<select name="endMinut2">
						<option value="00">00</option>
						<option value="30">30</option>
					</select> 分
					<div class="weekDiv weekDiv2">
						<div class="week week2 Mon" data-index="1">周一 <div class="weekBoard hide"></div></div>
						<div class="week week2 Tues" data-index="2">周二 <div class="weekBoard hide"></div></div>
						<div class="week week2 Wen" data-index="3">周三 <div class="weekBoard hide"></div></div>
						<div class="week week2 Thurs" data-index="4">周四 <div class="weekBoard hide"></div></div>
						<div class="week week2 Fri" data-index="5">周五 <div class="weekBoard hide"></div></div>
						<div class="week week2 Sat" data-index="6">周六 <div class="weekBoard hide"></div></div>
						<div class="week week2 Sun" data-index="7">周日<div class="weekBoard hide"></div></div>
					</div>
					<a href="##" class="J_beSure">确认 </a><a href="##" class="cancle2">| 取消</a>
				</div>
				<a href="javascript:void(0)" class="addTimeDiv addTimeDiv2 @if(empty($data)) hide @endif" style="color: #27f;">新增时间段</a>
				<input type="hidden" name="zitiTimes" id="zitiTimes" value="" />
			</div>
	    </div> -->
		<!--自提点照片-->
		<div class="form-group linkage img">
			<label class="col-lg-2 control-label"><i>*</i><tit style="display: inline-block;">自提点照片：</tit></label>
			<div class="col-lg-8">
				<div id="selImg" style="display: inline-block;">
					@if(isset($data['imageArr']) && $data['imageArr'])
					@foreach($data['imageArr'] as $image)
					<span class='showLittleImg'>
						<img src="{{ config('app.source_url') }}{{ $image }}" class='addSeleImg'/>
						<i class='imgClose' style='color:white;'>×</i>
					</span>
					@endforeach
					@endif
	                <a href="javascript:void(0);" id="addImg">+加图</a>
				</div>
			</div>
		</div>
		<!--商家推荐-->
		<div class="form-group linkage recommend">
			<label class="col-lg-2 control-label"><in style="display: inline-block; vertical-align: top;">商家推荐：</in></label>
			<div class="col-lg-8">
				<textarea name="comment" id="comment" rows="4" cols="40" style="resize: both; padding: 5px" placeholder="可描述自提点的活动或相关备注信息（最多200个字）">{{ $data['comment'] or '' }}</textarea>
			</div>
	    </div>
	    <!--同时作为线下门店接待-->
	    <div class="form-group">
	        <label for="" class="col-sm-2 control-label"></label>
	        <div class="col-sm-7">
	            <label for="checkBox" style="font-size: 13px;">
					<input type="checkbox" id="checkBox" class="checkout" @if(isset($data['store_reception']) && $data['store_reception']) checked="checked" @endif />同时作为线下门店接待
					<input type="hidden" name="store_reception" id="store_reception" value="{{ $data['store_reception'] or 0 }}">
				</label>
	        </div>
	    </div>
		<!--保存和返回-->
		<div class="form-group linkage saveReturn">
			<label class="col-lg-2 control-label"></label>
			<div class="col-lg-8" style="margin-top:3px">
				<button type="button" id="saveBtn" class="btn btn-primary">保存</button>
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
							<!-- <a class="ui-btn js-confirm ui-btn-primary">确认</a> -->
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
@endsection
@section('page_js')
<script type="text/javascript">
	var lng = "{{ $data['longitude'] or 0 }}";
	var lat = "{{ $data['latitude'] or 0 }}";
</script>
<script type="text/javascript">
	var json = {!! $regions !!};
	// 自提点照片
	var imgId = [];
	@if($images)
		imgId = {!! $images !!}
	@endif
	// 接待时间段
	var storeTimes = [];
	@if(isset($reception_times) && $reception_times)
		storeTimes = {!! $reception_times !!}
	@endif
	// 自提时间段
	var zitiTimes = [];
	@if(isset($ziti_times) && $ziti_times)
		zitiTimes = {!! $ziti_times !!};
	@endif
</script>
<!--地图接口-->
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=FLIBZ-34ELI-C6WGO-5HIAO-6QBPE-KKB2D"></script>
<!--地图的方法-->
<script src="{{ config('app.source_url') }}mctsource/js/self_public/mapdetail_public.js" type="text/javascript" charset="utf-8"></script>
<!--分页器js引入-->
<script src="{{config('app.source_url')}}static/js/jqPaginator.min.js" type="text/javascript" charset="utf-8"></script>
<!--bootstrap表单验证插件js-->
<script src="{{config('app.source_url')}}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<!--图片上传引入的js文件-->
<script src="{{ config('app.source_url') }}static/js/webuploader.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_editSeception.js"></script>
@endsection