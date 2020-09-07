@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/messageCreate_20180129.css" />
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
				<!--预约活动开始提醒-->
				<div class="edit_content">
					<h4></h4>
					<p class="gray_999" style="margin-top: 5px;">01-09</p>
					<div class="ordered_content">标题</div>
					<div class="time_remark type_1">
						<p class="newsType">
							<span class="ptitle gray_999">消息类型</span>
							@if(isset($data['content']['news_type']) && $data['content']['news_type'])
							<span class="pcontent">{{ $data['content']['news_type'] }}</span>
							@else
							<span class="pcontent"></span>
							@endif
						</p>
						<p class="timeShow">
							<span class="ptitle gray_999">跟进时间</span>
							@if(isset($data['content']['follow_time']) && $data['content']['follow_time'])
							<span class="pcontent">{{ $data['content']['follow_time'] }}</span>
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
					<div class="time_remark type_2">
						<p class="newsType">
							<span class="ptitle gray_999">课程名称</span>
							@if(isset($data['content']['course_name']) && $data['content']['course_name'])
							<span class="pcontent">{{ $data['content']['course_name'] }}</span>
							@else
							<span class="pcontent"></span>
							@endif
						</p>
						<p class="timeShow">
							<span class="ptitle gray_999">开始时间</span>
							@if(isset($data['content']['start_time']) && $data['content']['start_time'])
							<span class="pcontent">{{ $data['content']['start_time'] }}</span>
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
				<!--底部-->
				<div class="edit_footer">
					<span>详情</span>
					<img src="{{ config('app.source_url') }}mctsource/images/row.png" width="10"/>
				</div>
				
			</div>
		</div>
	</div>
	<!--信息编辑框-->
	<div class="editDiv">
		<form class="form-horizontal dataForm">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
		  	<div class="form-group">
		    	<label for="templateName" class="col-sm-4 control-label">模版名称</label>
		    	<div class="col-sm-8">
		      		<input type="text" class="form-control" id="templateName" name="tempName" placeholder="名称" value="{{ $data['template_name'] or '' }}">
		    	</div>
		  	</div>
		  	<!--通用通知提醒-->
		  	<div class="type_1">
			  	<div class="form-group">
			    	<label for="templateContent" class="col-sm-4 control-label">消息类型</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['news_type']) && $data['content']['news_type'])
			    		<input type="text" id="templateContent_1" class="form-control templateContent" name="news_type" maxlength="8" placeholder="消息类型" value="{{ $data['content']['news_type'] }}">
			    		@else
			     	 	<input type="text" id="templateContent_1" class="form-control templateContent" name="news_type" maxlength="8" placeholder="消息类型">
			     	 	@endif
			     	 	<span class="form_hint">不要超过8个字</span>
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="datetimepicker" class="col-sm-4 control-label">跟进时间</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['follow_time']) && $data['content']['follow_time'])
			     	 	<input id="datetimepicker_1" class="form-control datetimepicker" name="follow_time" placeholder="跟进时间" value=" {{ $data['content']['follow_time'] }}">
			     	 	@else
			     	 	<input id="datetimepicker_1" class="form-control datetimepicker" name="follow_time" placeholder="跟进时间">
			     	 	@endif
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="templateTitle" class="col-sm-4 control-label">标题</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['title']) && $data['content']['title'])
			    		<textarea id="templateTitle_1" class="form-control templateTitle" rows="3" name="title" placeholder="标题">{{ $data['content']['title'] }}</textarea>
			    		@else
			     	 	<textarea id="templateTitle_1" class="form-control templateTitle" rows="3" name="title" placeholder="标题"></textarea>
			     	 	@endif
			    	</div>
			  	</div>
		  	</div>	
		  	<!--课程通知提醒-->
		  	<div class="type_2">
			  	<div class="form-group">
			    	<label for="templateContent" class="col-sm-4 control-label">课程名称</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['course_name']) && $data['content']['course_name'])
			    		<input type="text" id="templateContent_2" class="form-control templateContent" name="course_name" maxlength="8" placeholder="课程名称" value="{{ $data['content']['course_name'] }}">
			    		@else
			     	 	<input type="text" id="templateContent_2" class="form-control templateContent" name="course_name" maxlength="8" placeholder="课程名称">
			     	 	@endif
			     	 	<span class="form_hint">不要超过8个字</span>
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="datetimepicker" class="col-sm-4 control-label">开始时间</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['start_time']) && $data['content']['start_time'])
			    		<input id="datetimepicker_2" class="form-control datetimepicker" name="start_time" placeholder="开始时间" value="{{ $data['content']['start_time'] }}">
			    		@else
			     	 	<input id="datetimepicker_2" class="form-control datetimepicker" name="start_time" placeholder="开始时间">
			     	 	@endif
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="templateTitle" class="col-sm-4 control-label">标题</label>
			    	<div class="col-sm-8">
			    		@if(isset($data['content']['title']) && $data['content']['title'])
			    		<textarea id="templateTitle_2" class="form-control templateTitle" rows="3" name="course_title" placeholder="标题">{{ $data['content']['title'] }}</textarea>
			    		@else
			     	 	<textarea id="templateTitle_2" class="form-control templateTitle" rows="3" name="course_title" placeholder="标题"></textarea>
			     	 	@endif
			    	</div>
			  	</div>
		  	</div>	 

		  	<div class="form-group">
		    	<label for="inputPassword3" class="col-sm-4 control-label">备注</label>
		    	<div class="col-sm-8" style="position: relative;">
		     	 	<div contenteditable="true" class="form-control" rows="3" name="remark" id="remark">@if(isset($data) && $data){!! $data['remark'] !!}@endif</div>
	     	 		<span class="form_hint">不要超过40个字<a href="##" class="emoji">+添加表情</a></span>
	     	 		<div class="emojiBox"></div>
		    	</div>
		  	</div>
		  	<div class="form-group">
		    	<label for="inputPassword3" class="col-sm-4 control-label">链接页面</label>
		    	<input type="hidden" name="url" id="linkType" value="{{ $data['url'] or '' }}" />
		    	<input type="hidden" name="url_title" id="linktitle" value="{{ $data['url_title'] or '' }}" />
		    	<div class="col-sm-8 linkFun">
		    		<a class="choosLink choose_link" href="javascript:void(0);">{{ $data['url_title'] or '设置链接到的页面地址' }}</a>
		    		<ul class="linkBox">
		    			<li class="homepage" data-id="1">店铺主页</li>
		    			<li data-id="2">微页面及分类</li>
						<li data-id="3">拼团商品</li>
						<li data-id="4">商品</li>
						<li data-id="5">秒杀</li>
						<li data-id="6">会员主页</li>
						<li data-id="7">签到</li>
						<li data-id="8">享立减商品</li>
						<li data-id="9">自定义链接</li>
					</ul>
					<!-- 自定义外链 -->
					<div class="ui-popover top-center" id="setWaiLink">
						<div class="ui-popover-inner">
							<span></span>
							<input class="form-control" type="text" value="" style="margin-bottom: 0;display:inline-block;width:auto;font-size:12px" id="wailink_input" placeholder="https://www.exemple.com">
							<a href="javascript:void(0);" class="zent-btn zent-btn-primary js-save" style="margin-left: 20px;">确定</a>
							<a href="javascript:void(0);" class="zent-btn js-cancel" style="margin-left: 10px;">取消</a>
						</div>
						<div class="arrow"></div>
					</div>
		    	</div>
		  	</div>
		  	<div class="form-group submitFunDiv">
		    	<div class="col-sm-offset-2 col-sm-8">
		      		<a href="/merchants/message/index" type="button" class="btn btn-default">取消</a>
		      		<button type="submit" class="btn btn-primary test">保存</button>
		      		<!--<button type="button" class="btn btn-success">发送</button>-->
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
	                                <!--<span>标题</span>-->
	                                <!--<a class="co_38f" href="javascript:void(0);">刷新</a>-->
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
	                <div class= "myModal1Page"></div><!-- 分页 -->
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
<script src="{{ config('app.source_url') }}mctsource/js/messageCreate_20180129.js"></script>
<script type="text/javascript">
	var imgUrl = "{{ config('app.source_url') }}"
</script>
@endsection
