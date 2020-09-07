@extends('merchants.default._layouts') @section('head_css')
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">

<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">

<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/userList.css">
@endsection @section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 面包屑导航 开始 -->
		<ul class="crumb_nav">
			<li>
				<a href="javascript:void(0);">公众号</a>
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
@include('merchants.wechat.slidebar')
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
	<input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
	<!--主体左侧列表开始-->
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<!-- 导航模块 开始 -->
		<div class="nav_module clearfix">
			<!-- 左侧 开始 -->
			<div class="pull-left">
				<!-- （tab试导航可以单独领出来用） -->
				<!-- 导航 开始 -->
				<ul class="tab_nav">
					<li class="hover">
						<a href="{{ URL('/merchants/wechat/book') }}">预约管理</a>
					</li>
					
				</ul>
				<!-- 导航 结束 -->
			</div>
			<!-- 左侧 结算 -->
		</div>
		<!-- 导航模块 结束 -->
		<!--预约管理-->
		<div class="bs_gl">
			<div class="bs_left">
				<img src="{{ config('app.source_url') }}mctsource/images/book_Save.png" alt="" />在线预约管理
			</div>
			<div class="bs_right">
				<form method="get" action="/merchants/wechat/userList/{{ $input['book_id'] }}">
				<div class="col-sm-5 pd_l">
	                <input type="text" name="created_at" class="form-control pd_l5 fz_13" id="datetimepicker1" placeholder="提交日期" value="{{ request('created_at') }}">
	            </div>
	            <select name="status" class="bs_slt" value="">
	            	<option value="" @if(request('status') == '') selected=selected @endif>全部</option>
					<option value="1" @if(request('status') == 1) selected=selected @endif>等待客服处理</option>
					<option value="2" @if(request('status') == 2) selected=selected @endif>已确认</option>
					<option value="3" @if(request('status') == 3) selected=selected @endif>已拒绝</option>
	            </select>
	            <button type="submit" class="bs_btn">查询</button>
				</form>
			</div>
			
		</div>
		<!--按钮-->
		<div class="right_content">
			@if($list)
			<div class="box">
				<button class="btn btn-danger" id="btn_export_express">导出预约单</button>
			</div>
			@endif
		</div>
		<!--main-->
		<div id="t_content">
			<ul class="t_content_header">
			<!-- {{--<li> <input type="checkbox" id="cb_all"/>序号</li>--}} -->
				<li>序号</li>
				<li>真实姓名</li>
				<li>电话</li>
				<li>提交时间</li>
				<li>预约状态</li>
				<li>处理时间</li>
				<li>用户是否删除</li>
				<li>操作</li>
			</ul>
			@if($list)
				@foreach($list as $k=>$v)
					<ul class="t_content_con">
						{{--<li><input type="checkbox" name="cb_order" data-id="{{$v['id']}}" />{{$k+1}}</li>--}}
						<li>{{$k+1}}</li>
						<li>{{ $v['name'] or '' }}</li>
						<li>{{ $v['phone'] or '' }}</li>

						<li>{{ $v['created_at'] }}</li>
						<li>@if($v['status'] == '1')待客服处理@elseif($v['status'] == '2')已确认@else已拒绝@endif</li>
						<li>{{ $v['shop_updated'] or ''}}</li>
						@if($v['is_delete'] == '0')
							<li>否</li>
						@else
							<li>是</li>
						@endif
						<li>
							<p><a href="/merchants/wechat/bookDetail?id={{$v['id']}}">预约详情</a></p>
							<p class="co_38f delete pop t_shan" data-toggle="delete_pop" data-id="{{ $v['id'] }}">删除</p>
							<p><a href="/merchants/wechat/usersAlter?id={{$v['id']}}">处理</a></p>

						</li>
					</ul>
				@endforeach
			@else
				<div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据
				</div>
		@endif
		<!-- 分页 -->
			<div class="text-right">
				{{$page}}
			</div>

		</div>
<!-- 中间 结束 -->
@endsection  @section('page_js') @parent
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>

<!--时间插件引入的JS文件-->
<script src="{{ config('app.source_url') }}static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">
	var start_at = "{{ $detail['start_at']?? '' }}";
</script>

<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/userList.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
</script>

<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript">	
/*删除*/
$('body').on('click','.t_shan',function(e){
	e.stopPropagation();
    var _this = this;
    var id=$(this).data('id');
    console.log(id)
    showDelProver($(_this),function(){
        $.ajax({
            type:"get",
            url:'/merchants/wechat/delApi',
            data:{
                id:id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                if(res.status===1){
                    tipshow('删除成功','info');
                    $(_this).parents('.t_content_con').remove();
                }else{
                    tipshow('删除失败','warn');
                }
            },
            error:function(){
                alert('数据访问异常')
            }
        }); 
    })
})

	//导出订单
$('#btn_export_express').click(function(){
    var book_id = '{{ $input['book_id'] or 0}}';
    var status  = '{{ $input['status'] or 0 }}';
    var book_date = '{{ $input['book_date'] or '' }}';
    window.location.href = '/merchants/wechat/orderExport?book_id='+book_id+'&status='+status+'&book_date='+book_date;
});
</script>
@endsection
