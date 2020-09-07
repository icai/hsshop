@extends('merchants.default._layouts')
@section('head_css')
<!-- layer  -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/registerList_ty20180124.css" />
@endsection
@section('slidebar')
    @include('merchants.member.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="##">注册信息管理</a>
            </li>
            <li>
                {{--<a href="{{URL('/merchants/member/import')}}">导入会员</a>--}}
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
    <div class="search">
    	<form class="form-inline" method="get" action="">
			<div class="form-group">
				<label for="phoneType">活动类型:</label>
				<select name="type" id="phoneType">
					<option @if(!request('type')) selected='selected' @endif value="0">默认活动</option>
					<option @if(request('type') == 1) selected='selected' @endif value="1">免费送小程序活动</option>
					<option @if(request('type') == 2) selected='selected' @endif value="2">3月3日活动</option>
					<option @if(request('type') == 8) selected='selected' @endif value="8">5月5日活动</option>
					<option @if(request('type') == 9) selected='selected' @endif value="9">7月9日活动</option>
				</select>
			</div>
			<!--上级手机号筛选-->
			<div class="form-group">
				<label for="parent_phone">上级手机号:</label>
				<input type="text" class="form-control" name="parent_phone" id="parent_phone" value="{{request('parent_phone')}}" placeholder="手机号码" style="width: 130px;">
			</div>
		  	<div class="form-group">
		    	<label for="phoneNumber">手机号:</label>
		    	<input type="text" class="form-control" name="phone" id="phoneNumber" value="{{request('phone')}}" placeholder="手机号码" style="width: 130px;">
		  	</div>
		  	<div class="form-group">
		    	<label for="memberName">姓名:</label>
		    	<input type="text" class="form-control" name="name" id="memberName" value="{{request('name')}}" placeholder="姓名" style="width: 120px;">
		  	</div>
		  	<div class="form-group">
		    	<label for="">提交时间:</label>
		    	<input type="text" name="start_at" class="form-control" id="datetimepicker1" value="{{request('start_at')}}" placeholder="开始时间">--
		    	<input type="text" name="end_at" class="form-control" id="datetimepicker2" value="{{request('end_at')}}" placeholder="结束时间">
		  	</div>
		  	<div class="form-group">
		  		<a class="aWeek" href="##">近7天</a>
		  		<a class="aMonth" href="##">近30天</a>
	  		</div>
		  	<div class="row btns">
			  	<button type="submit" class="btn btn-primary">筛选</button>
		  	</div>
		</form>
    </div>
    <div class="table">
    	<ul class="table_header">
    		<li>
    			<label>
    				<input type="checkbox" id="allChoose">全选
				</label>
			</li>
    		<li>提交时间</li>
			<li>活动类型</li>
    		<li>姓名</li>
    		<li>手机号</li>
    		<li>公司名称</li>
    		<li>职务</li>
			<li>微信</li>
			<li>是否已注册</li>
			<li>是否已发送</li>
    		<li>操作</li>
    	</ul>
		@forelse ( $list as $v )
			<ul class="table-body">
				<li><input type="checkbox" name="" class="chooseItem" value="{{$v['id']}}" /></li>
				<li>{{$v['created_at']}}</li>
				<li>
					@if($v['type']==0)
						默认活动
					@elseif($v['type']==1)
						免费送小程序活动
					@elseif($v['type']==2)
						3月3日活动
					@elseif($v['type']==8)
						5月5日活动
					@elseif($v['type']==9)
						7月9日活动
					@else
						其他
					@endif
				</li>
				<li>{{$v['name']}}</li>
				<li>{{$v['phone']}}</li>
				<li>{{$v['company_name']}}</li>
				<li>{{$v['company_position']}}</li>
				<li>{{$v['wechat_name']}}</li>
				<li>
					@if($v['is_register'] == '0')未注册
					@else已注册
					@endif
				</li>
				<li>
					@if($v['is_sms'] == '0')未发送
					@elseif($v['is_sms'] == '1')发送中
					@else已发送
					@endif
				</li>
				<li>
					<a href="##" class="look" data-value="{{json_encode($v)}}">查看</a>
					 @if($v['is_register'] == '0')
					-<a href="##" class="register" data-value="{{json_encode($v)}}">注册</a>
					@endif
					@if($v['is_sms'] == '0')
					-<a href="##" class="sms" data-value="{{json_encode($v)}}">发短信</a>
					@endif
					@if($v['type'] == 9 && $v['parent_phone'])
						-<a href="/merchants/member/li/registerList?type=9&parent_phone={{$v['parent_phone']}}">邀请列表</a>
					@endif
				</li>
			</ul>
		@empty
			<ul class="data_content" style="text-align: center;">暂无数据</ul>
		@endforelse
    </div>
	{{$pageHtml}}
    <div class="bottom_fun">
	    <button type="button" class="btn btn-primary branch_output">批量导出</button>
		<button type="button" class="btn btn-primary branch_outputAll">全部导出</button>
    </div>
    <!--信息查看modal-->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  	<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<!--<h4 class="modal-title" id="myModalLabel">Modal title</h4>-->
	      		</div>
		      	<div class="modal-body">
			        <form class="form-horizontal">
					  	<div class="form-group">
					    	<label for="memberName" class="col-sm-3 control-label">姓名：</label>
					    	<div class="col-sm-6">
					      		<input type="text" class="form-control" id="showMemberName" placeholder="姓名" disabled>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="phoneNumber" class="col-sm-3 control-label">手机号：</label>
					    	<div class="col-sm-6">
					      		<input type="number" class="form-control" id="showPhoneNumber" placeholder="手机号" disabled>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="companyName" class="col-sm-3 control-label">公司名称：</label>
					    	<div class="col-sm-6">
					      		<input type="text" class="form-control" id="showCompanyName" placeholder="公司名称" disabled>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="position" class="col-sm-3 control-label">职务：</label>
					    	<div class="col-sm-6">
					      		<input type="text" class="form-control" id="showPosition" placeholder="职务" disabled>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="position" class="col-sm-3 control-label">地址：</label>
					    	<div class="col-sm-6">
					      		<input type="text" class="form-control" id="showAddress" placeholder="地址" disabled>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="" class="col-sm-3 control-label">营业执照：</label>
					    	<div class="col-sm-6">
					      		<img id="businessLicense" src="" width="300"/>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="" class="col-sm-3 control-label">身份证正面：</label>
					    	<div class="col-sm-6">
					      		<img id="IDcardPositive" src="" width="300"/>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="" class="col-sm-3 control-label">身份证反面：</label>
					    	<div class="col-sm-6">
					      		<img id="IDcardOpposite" src="" width="300"/>
					    	</div>
					  	</div>
					</form>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
		      	</div>
	    	</div>
	  	</div>
	</div>
    <!-- 分页 -->
    <!--<div class="pageNum"></div>-->
</div>
@endsection
@section('page_js')
<!--时间插件引入的JS文件-->
<script src="{{ config('app.source_url') }}static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
<script>
	var imgUrl = "{{ imgUrl() }}";//动态图片域名
</script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/registerList_ty20180124.js"></script>   
@endsection
