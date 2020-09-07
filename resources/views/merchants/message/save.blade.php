@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/messageTemSave_20180123.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
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
                <a href="javascript:void(0)">新建模版</a>
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
	<input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
	<div class="phone_model">
		<img src="{{ config('app.source_url') }}mctsource/images/sever-notice.png" width="320"/>
		<div class="edit_msg">
			<div class="edit_card">
				<div class="edit_header">
					<img src="{{ imgUrl($storeInfo['logo']) }}" width="35" height="35"/>
					<span>{{ $storeInfo['shop_name'] }}</span>
					<img src="{{ config('app.source_url') }}mctsource/images/threePoint.png" width="30"/>
				</div>
				<!--预约活动开始提醒-->
				<div class="edit_content type_1">
					<h4>预约活动开始提醒</h4>
					<p class="gray_999">01-09</p>
					<div class="ordered_content" style="color: #ff0015; font-size: 32px;">
						@if(isset($data['content']['bookContent']) && $data['content']['bookContent'])
						{{ $data['content']['bookContent'] }}
						@endif
					</div>
					<div class="time_remark">
						<p class="timeShow">
							<span class="ptitle gray_999">预约时间</span>
							@if(isset($data['content']['bookTime']) && $data['content']['bookTime'])
							<span class="pcontent">{{ $data['content']['bookTime'] or ''}}</span>
							@else
							<span class="pcontent"></span>
							@endif
						</p>
						<p class="remarkShow">
							<span class="ptitle gray_999">备注</span>
							@if(isset($data['remark']) && $data['remark'])
							<span class="pcontent">{!! $data['remark'] !!}</span>
							@else
							<span class="pcontent"></span>
							@endif
						</p>
					</div>
					
				</div>
				<!--商品降价提醒-->
				<div class="edit_content type_2">
					<h4>商品降价提醒</h4>
					<p class="gray_999">01-09</p>
					<div class="time_remark">
						<p class="goodsNameShow">
							<span class="gray_999">商品名称</span>
							@if(isset($data['content']['productTitle']) && $data['content']['productTitle'])
							<span class="goodsName" style="color: #ff0015;">{{ $data['content']['productTitle'] }}</span>
							@else
							<span class="goodsName" style="color: #ff0015;"></span>
							@endif
						</p>
						<p class="goodsPriceShow">
							<span class="gray_999">商品现价</span>
							@if(isset($data['content']['price']) && $data['content']['price'])
							<span class="goodsPrice">{{ sprintf('%.2f',$data['content']['price']) }}</span>
							@else
							<span class="goodsPrice"></span>
							@endif
						</p>
						<p class="goodsOpriceShow">
							<span class="gray_999">商品原价</span>
							@if(isset($data['content']['cost_price']) && $data['content']['cost_price'])
							<span class="goodsOprce">{{ sprintf('%.2f',$data['content']['cost_price']) }}</span>
							@else
							<span class="goodsOprce"></span>
							@endif

						</p>
						<p class="remarkShow">
							<span class="ptitle gray_999">备注</span>
							@if(isset($data['remark']) && $data['remark'])
							<span class="pcontent">{!! $data['remark'] !!}</span>
							@else
							<span class="pcontent"></span>
							@endif
						</p>
					</div>
				</div>
				<!--签到提醒-->
				<div class="edit_content type_3">
					<h4>签到提醒</h4>
					<p class="gray_999">01-09</p>
					<div class="time_remark">
						<p class="tixingShow">
							<span class="gray_999">提醒内容</span>
							@if(isset($data['content']['remindContent']) && $data['content']['remindContent'])
							<span class="tixing" style="color: #ff0015">{{ $data['content']['remindContent'] }}</span>
							@else
							<span class="tixing" style="color: #ff0015"></span>
							@endif

						</p>
						<p class="remarkShow">
							<span class="ptitle gray_999">备注</span>
							@if(isset($data['remark']) && $data['remark'])
							<span class="pcontent">{!! $data['remark'] !!}</span>
							@else
							<span class="pcontent"></span>
							@endif
						</p>
					</div>
				</div>
				<!--卡券到期提醒-->
				<div class="edit_content type_4">
					<h4>卡券到期提醒</h4>
					<p class="gray_999">01-09</p>
					<div class="time_remark">
						<p class="tixingShow">
							<span class="gray_999 ptitle">卡券名称</span>
							@if(isset($data['content']['name']) && $data['content']['name'])
								<span class="tixing card_volume" style='word-wrap : break-word'>{{ $data['content']['name'] }}</span>
							@else
								<span class="tixing card_volume" style='word-wrap : break-word'></span>
							@endif
						</p>
						<p class="tixingShow">
							<span class="gray_999 ptitle">使用限制</span>
							@if(isset($data['content']['use_limit']) && $data['content']['use_limit'])
								<span class="tixing time_limit" style='word-wrap : break-word'>{{ $data['content']['use_limit'] }}</span>
							@else
								<span class="tixing time_limit" style='word-wrap : break-word'></span>
							@endif
						</p>
						<p class="tixingShow">
							<span class="gray_999 ptitle">到期时间</span>
							@if(isset($data['content']['expiration_time']) && $data['content']['expiration_time'])
								<span class="tixing date_time">{{ $data['content']['expiration_time'] }}</span>
							@else
								<span class="tixing date_time"></span>
							@endif
						</p>
						<p class="remarkShow">
							<span class="ptitle gray_999">备注</span>
							@if(isset($data['remark']) && $data['remark'])
								<span class="pcontent remark" style='color: #333333'>{!! $data['remark'] !!}</span>
							@else
								<span class="pcontent remark" style='color: #333333'></span>
							@endif
						</p>
					</div>
				</div>
				<!--预售商品开售通知-->
				<div class="edit_content type_5">
					<h4>预售商品开售通知</h4>
					<p class="gray_999">01-09</p>
					<div class="time_remark">
						<p class="tixingShow">
							<span class="gray_999">商品名称</span>
							@if(isset($data['content']['product_name']) && $data['content']['product_name'])
								<span class="tixing shopName" style='word-wrap : break-word'>{{ $data['content']['product_name'] }}</span>
							@else
								<span class="tixing shopName" style='word-wrap : break-word'></span>
							@endif

						</p>
						<p class="tixingShow">
							<span class="gray_999">开售时间</span>
							@if(isset($data['content']['sale_time']) && $data['content']['sale_time'])
								<span class="tixing date_time" style='word-wrap : break-word'>{{ $data['content']['sale_time'] }}</span>
							@else
								<span class="tixing date_time" style='word-wrap : break-word'></span>
							@endif

						</p>
						<p class="tixingShow">
							<span class="gray_999">商品价格</span>
							@if(isset($data['content']['sale_price']) && $data['content']['sale_price'])
								<span class="tixing presell_price" style='word-wrap : break-word'>{{ $data['content']['sale_price'] }}</span>
							@else
								<span class="tixing presell_price" style='word-wrap : break-word'></span>
							@endif

						</p>
						<p class="remarkShow">
							<span class="ptitle gray_999">备注</span>
							@if(isset($data['remark']) && $data['remark'])
								<span class="pcontent" style='color: #333333'>{!! $data['remark'] !!}</span>
							@else
								<span class="pcontent" style='color: #333333'></span>
							@endif
						</p>
					</div>
				</div>
				<!--服务过期提醒-->
				<div class="edit_content type_6">
					<h4>服务过期提醒</h4>
					<p class="gray_999">01-09</p>
					<div class="time_remark">
						<p class="tixingShow">
							<span class="ptitle gray_999">服务名</span>
							@if(isset($data['content']['server_name']) && $data['content']['server_name'])
								<span class="tixing serviceName"  style='word-wrap : break-word'>{{ $data['content']['server_name'] }}</span>
							@else
								<span class="tixing serviceName"  style='word-wrap : break-word'></span>
							@endif

						</p>
						<p class="tixingShow">
							<span class="gray_999">过期原因</span>
							@if(isset($data['content']['expiration_reason']) && $data['content']['expiration_reason'])
								<span class="tixing service" style='word-wrap : break-word'>{{ $data['content']['expiration_reason'] }}</span>
							@else
								<span class="tixing service" style='word-wrap : break-word'></span>
							@endif

						</p>
						<p class="tixingShow">
							<span class="gray_999">过期时间</span>
							@if(isset($data['content']['server_expiration_time']) && $data['content']['server_expiration_time'])
								<span class="tixing date_time" >{{ $data['content']['server_expiration_time'] }}</span>
							@else
								<span class="tixing date_time" ></span>
							@endif

						</p>
						<p class="remarkShow">
							<span class="ptitle gray_999">备注</span>
							@if(isset($data['remark']) && $data['remark'])
								<span class="pcontent" style='color: #333333'>{!! $data['remark'] !!}</span>
							@else
								<span class="pcontent" style='color: #333333'></span>
							@endif
						</p>
					</div>
				</div>

				<!--底部-->
				<div class="edit_footer">
					<span>进入小程序查看</span>
					<img src="{{ config('app.source_url') }}mctsource/images/row.png" width="10"/>
				</div>
				
			</div>
		</div>
	</div>
	<!--信息编辑框-->
	<div class="editDiv">
		<form class="form-horizontal dataForm">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
		  	<div class="form-group" id='from_title'>
		    	<label for="templateName" class="col-sm-4 control-label">模版名称</label>
		    	<div class="col-sm-8">
		      		<input type="text" class="form-control" id="templateName" name="tempName" placeholder="名称" value="{{ $data['template_name'] or '' }}">
		    	</div>
		  	</div>
		  	<!--预约活动开始提醒-->
		  	<div class="type_1">
			  	<div class="form-group">
			    	<label for="templateContent" class="col-sm-4 control-label">预约内容</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['bookContent']) && $data['content']['bookContent'])
			     	 	<input type="text" class="form-control" id="templateContent" name="bookContent" maxlength="8" placeholder="预约内容" value="{{ $data['content']['bookContent']}}">
			     	 	@else
			     	 	<input type="text" class="form-control" id="templateContent" name="bookContent" maxlength="8" placeholder="预约内容">
			     	 	@endif
			     	 	<span class="form_hint">不要超过8个字</span>
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="datetimepicker" class="col-sm-4 control-label">预约时间</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['bookTime']) && $data['content']['bookTime'])
			     	 	<input type="" class="form-control datetimepicker" id="datetimepicker" name="bookTime" placeholder="预约时间" value="{{ $data['content']['bookTime'] }}">
			     	 	@else
			     	 	<input type="" class="form-control datetimepicker" id="datetimepicker" name="bookTime" placeholder="预约时间">
			     	 	@endif
			    	</div>
			  	</div>
		  	</div>
		  	<!--商品降价提醒-->
		  	<div class="type_2">
			  	<div class="form-group">
			    	<label for="goodsName" class="col-sm-4 control-label">商品名称</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['productTitle']) && $data['content']['productTitle'])
			      		<textarea type="text" class="form-control" id="goodsName" name="productTitle" rows="3" maxlength="40" placeholder="商品名称">{{ $data['content']['productTitle'] or ''}}</textarea>
			      		@else
			      		<textarea type="text" class="form-control" id="goodsName" name="productTitle" rows="3" maxlength="40" placeholder="商品名称"></textarea>
			      		@endif
			      		<span class="form_hint">不要超过40个字</span>
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="goodsPrice" class="col-sm-4 control-label">商品现价</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['price']) && $data['content']['price'])
			      		<input type="" class="form-control" id="goodsPrice" name="price" placeholder="商品现价" onkeyup="num(this)" value="{{ sprintf('%.2f',$data['content']['price']) }}">
			      		@else
			      		<input type="" class="form-control" id="goodsPrice" name="price" placeholder="商品现价" onkeyup="num(this)">
			      		@endif
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="goodsOprice" class="col-sm-4 control-label">商品原价</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['cost_price']) && $data['content']['cost_price'])
			      		<input type="" class="form-control" id="goodsOprice" name="cost_price" placeholder="商品原价" onkeyup="num(this)" value="{{ sprintf('%.2f',$data['content']['cost_price']) }}">
			      		@else
			      		<input type="" class="form-control" id="goodsOprice" name="cost_price" placeholder="商品原价" onkeyup="num(this)">
			      		@endif
			    	</div>
			  	</div>
		  	</div>

		  	<!--签到提醒-->
		  	<div class="type_3">
			  	<div class="form-group">
			    	<label for="tixingContent" class="col-sm-4 control-label">提醒内容</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['remindContent']) && $data['content']['remindContent'])
			     	 	<input type="text" class="form-control" id="tixingContent" name="remindContent" maxlength="8" placeholder="提醒内容" value="{{ $data['content']['remindContent']}}">
			     	 	@else
			     	 	<input type="text" class="form-control" id="tixingContent" name="remindContent" maxlength="8" placeholder="提醒内容">
			     	 	@endif
			     	 	<span class="form_hint">不要超过8个字</span>
			    	</div>
			  	</div>
		  	</div>
			<!--卡券到期提醒-->
			<div class="type_4">
				<div class="form-group">
					<label for="templateName" class="col-sm-4 control-label">卡券名称</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="card_volume" name="name" placeholder="输入卡券名称" value="{{ $data['content']['name'] or '' }}">
					</div>
				</div>
				<div class="form-group">
					<label for="tixingContent" class="col-sm-4 control-label">使用期限</label>
					<div class="col-sm-8">
						@if(isset($data['content']['use_limit']) && $data['content']['use_limit'])
							<input type="text" class="form-control" id="time_limit" name="use_limit" placeholder="输入使用限制" value="{{ $data['content']['use_limit']}}">
						@else
							<input type="text" class="form-control" id="time_limit" name="use_limit" placeholder="输入使用限制">
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="datetimepicker" class="col-sm-4 control-label">到期时间</label>
					<div class="col-sm-8 dateTime_icon">
						@if(isset($data['content']['expiration_time']) && $data['content']['expiration_time'])
							<input type="" class="form-control datetimepicker" id="datetimepicker" name="expiration_time" value="{{ $data['content']['expiration_time'] }}">
						@else
							<input type="" class="form-control datetimepicker" id="datetimepicker" name="expiration_time" >
						@endif
						<span></span>
					</div>
				</div>
			</div>
			<!--预售商品开售通知-->
			<div class="type_5">
				<div class="form-group">
					<label for="templateName" class="col-sm-4 control-label">商品名称</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="shopName" name="product_name" placeholder="输入商品名称" value="{{ $data['content']['product_name'] or '' }}">
					</div>
				</div>
				<div class="form-group">
					<label for="datetimepicker" class="col-sm-4 control-label">开售时间</label>
					<div class="col-sm-8 dateTime_icon">
						@if(isset($data['content']['sale_time']) && $data['content']['sale_time'])
							<input type="" class="form-control datetimepicker" id="datetimepicker" name="sale_time" value="{{ $data['content']['sale_time'] }}">
						@else
							<input type="" class="form-control datetimepicker" id="datetimepicker" name="sale_time" >
						@endif
						<span></span>
					</div>
				</div>
				<div class="form-group">
					<label for="tixingContent" class="col-sm-4 control-label">商品价格</label>
					<div class="col-sm-8">
						@if(isset($data['content']['sale_price']) && $data['content']['sale_price'])
							<input type="text" class="form-control" id="presell_price" name="sale_price" placeholder="输入商品价格" value="{{ $data['content']['sale_price']}}">
						@else
							<input type="text" class="form-control" id="presell_price" name="sale_price" placeholder="输入商品价格">
						@endif
					</div>
				</div>
			</div>
			<!--服务过期提醒-->
			<div class="type_6">
				<div class="form-group">
					<label for="templateName" class="col-sm-4 control-label">服务名</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="serviceName" name="server_name" placeholder="输入服务名" value="{{ $data['content']['server_name'] or '' }}">
					</div>
				</div>
				<div class="form-group">
					<label for="tixingContent" class="col-sm-4 control-label">过期原因</label>
					<div class="col-sm-8">
						@if(isset($data['content']['expiration_reason']) && $data['content']['expiration_reason'])
							<input type="text" class="form-control" id="service" name="expiration_reason" placeholder="输入过期原因" value="{{ $data['content']['expiration_reason']}}">
						@else
							<input type="text" class="form-control" id="service" name="expiration_reason" placeholder="输入过期原因">
						@endif
					</div>
				</div>
				<div class="form-group dateTime_icon">
					<label for="datetimepicker" class="col-sm-4 control-label">过期时间</label>
					<div class="col-sm-8">
						@if(isset($data['content']['server_expiration_time']) && $data['content']['server_expiration_time'])
							<input type="" class="form-control datetimepicker" id="expiration_time" name="server_expiration_time" value="{{ $data['content']['server_expiration_time'] }}">
						@else
							<input type="" class="form-control datetimepicker" id="expiration_time" name="server_expiration_time" >
						@endif
						<span></span>
					</div>
				</div>
			</div>


		  	<div class="form-group">
		    	<label for="inputPassword3" class="col-sm-4 control-label">备注</label>
		    	<div class="col-sm-8" style="position: relative;">
		     	 	<div contenteditable="true" class="form-control" rows="3" name="remark" id="remark">@if(isset($data) && $data){!! $data['remark'] !!}@endif</div>
	     	 		<span class="form_hint">不要超过40个字<a href="##" class="emoji"  style='display: none'>+添加表情</a></span>
	     	 		<div class="emojiBox"></div>
		    	</div>
		  	</div>
		  	<div class="form-group"  style='display: none'>
		    	<label for="inputPassword3" class="col-sm-4 control-label">小程序连接</label>
		    	<input type="hidden" name="url" id="linkType" value="{{ $data['url'] or '' }}" />
		    	<input type="hidden" name="url_title" id="linktitle" value="{{ $data['url_title'] or '' }}" />
		    	<div class="col-sm-8 linkFun">
		    		@if(isset($data['url']) && $data['url'])
		    		<a class="choosLink choose_link" href="javascript:void(0);">{{ $data['url_title'] }}</a>
		    		@else
		    		<a class="choosLink choose_link" href="javascript:void(0);">设置链接到的页面地址</a>
		    		@endif
		    		<ul class="linkBox">
		    			<li class="homepage" data-id="1">店铺主页</li>
		    			<li data-id="2">微页面及分类</li>
		    			<li data-id="3">拼团商品</li>
		    		</ul>
		    	</div>
		  	</div>
		  	<div class="form-group submitFunDiv">
		    	<div class="col-sm-offset-2 col-sm-8">
		      		<a href="/merchants/message/index" type="button" class="btn btn-default">取消</a>
		      		<button type="submit" class="btn btn-primary test">保存</button>
		    	</div>
		  	</div>
		</form>
	</div>
	<!--微页面模态框开始-->
	<div class="modal" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                <ul class="list">
	                    <li class="js_small list_active">微页面</li>
	                </ul>
	            </div>
	            <div class="modal-body">
	                <table class="table">
	                    <thead>
	                        <tr>
	                            <th class="title">
	                            	标题
	                               
	                            </th>
	                            <th class="set_time">创建时间</th>
	                            <th class="search">
	                                <input type="text" />
	                                <button class="btn btn-default">搜</button>
	                            </th>
	                        </tr>
	                    </thead>
	                    <tbody class="small">
	                       
	                    </tbody>
	                </table>
	            </div>
	            <div class="modal-footer clearfix">
	                <div class= "myModal1Page"></div>
				
	            </div>
	        </div>
	    </div>
	</div>
</div>
@endsection
@section('page_js')
<!-- 时间插件 -->
<script src="{{ config('app.source_url') }}static/js/moment.min.js"></script> 
<script src="{{ config('app.source_url') }}static/js/locales.min.js"></script> 
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js"></script> 
<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/messageTemSave_20180123.js"></script>
<script type="text/javascript">
	var imgUrl = "{{ config('app.source_url') }}"
	console.log(imgUrl)
</script>
@endsection
