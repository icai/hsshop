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
		    	<label for="phoneNumber">昵称:</label>
		    	<input type="text" class="form-control" name="nickname" @if(request('nickname')) value="{{request('nickname')}}" @endif  placeholder="昵称" style="width: 130px;">
		  	</div>
		  	<div class="form-group">
		    	<label for="memberName">性别:</label>
		    	<select name="sex">
					<option @if(request('sex') == 0) selected @endif value="0">未知</option>
					<option @if(request('sex') == 1) selected @endif value="1">男</option>
					<option @if(request('sex') == 2) selected @endif value="2">女</option>
				</select>
		  	</div>
			<div class="form-group">
				<label for="memberName">来源:</label>
				<select name="pid">
					<option value="0">全部</option>
					@forelse($source as $key=>$item)
						<option @if($key == request('pid')) selected @endif value="{{$key}}">{{$item}}</option>
						@endforeach

				</select>
			</div>
			<div class="form-group">
				<label for="memberName">是否参加过拼团:</label>
				<select name="is_open_groups">
					<option value="0">全部</option>
					<option @if('1' == request('is_open_groups')) selected @endif value="1">是</option>
					<option @if('2' == request('is_open_groups')) selected @endif value="2">否</option>
				</select>
			</div>
			<div class="form-group">
				<label for="start_time">开始时间:</label>
				<input type="text" name="starttime" value="{{request('starttime')}}" id="startDate">
			</div>
			<div class="form-group">
				<label for="end_time">结束时间:</label>
				<input type="text" name="endtime" value="{{request('endtime')}}" id="endDate">
			</div>

		  	<div class="row btns">
			  	<button type="submit" class="btn btn-primary">筛选</button>
			  	<button type="reset" class="btn btn-link">清空筛选条件</button>
				<a href="/merchants/distribute/refresh">刷新当天数据</a>
		  	</div>
			<div class="row btns">
				搜索结果：总数量：{{$memberData[0]['total']}}
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
    		<li>昵称</li>
    		<li>头像</li>
    		<li>性别</li>
			<li>来源</li>
			<li>是否参加过拼团</li>
    		<li style="width: 17%">来源时间</li>
			<li>级别</li>
		</ul>
		@forelse ( $memberData[0]['data'] as $val )
			<ul class="table-body">
				<li><input type="checkbox" name="" class="chooseItem" value="{{$val['id']}}" /></li>
				<li>{{$val['nickname']}}</li>
				<li style="height: 50px;"><img style="height: 50px;width: 50px;margin: auto;" src="{{$val['headimgurl']}}"></li>
				<li>@if($val['sex'] == 1) 男 @elseif($val['sex'] == 2) 女 @else 未知 @endif</li>
				<li>{{$source[$val['topid']]}}</li>
				<li>@if($val['is_open_groups']==1)是(<button class="parentIframe" data-mid="{{$val['mid']}}">查看信息</button>)@else 否@endif</li>
				<li style="width: 17%">{{$val['intime']}}</li>
				<li>{{$val['level']}}</li>
			</ul>
		@empty
			<ul class="data_content" style="text-align: center;">暂无数据</ul>
		@endforelse
    </div>
	{{$memberData[1]}}

    <!--信息查看modal-->
    <!-- 分页 -->
    
</div>
@endsection
@section('page_js')
	<!--时间插件引入的JS文件-->
	<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
	<!-- layer选择时间插件 -->
	<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
	<script>
        var imgUrl = "{{ imgUrl() }}";//动态图片域名
	</script>
	<!-- 当前页面js -->
	<script type="text/javascript">
        $(function(){
            laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
            var start = {
                elem: '#startDate',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2009-06-16 23:59:59', //设定最小日期为当前日期
                max: '2099-06-16 23:59:59', //最大日期
                event: 'focus',
                istime: true,
                istoday: false,
                choose: function(datas){
                    end.min = datas; //开始日选好后，重置结束日的最小日期
                    end.start = datas //将结束日的初始值设定为开始日
                }
            };
            var end = {
                elem: '#endDate',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2009-06-16 23:59:59',
                max: '2099-06-16 23:59:59',
                event: 'focus',
                istime: true,
                istoday: false,
                choose: function(datas){
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            };
            laydate(start);
            laydate(end);
        })


        $('.parentIframe').on('click', function(){
			var mid = $(this).data('mid');
            layer.open({
                type: 2,
                title: '参团信息',
                maxmin: true,
                shadeClose: true, //点击遮罩关闭层
                area : ['1000px' , '520px'],
                content: '/merchants/distribute/getGroupsInfo?mid='+mid
            });
        });
	</script>
@endsection