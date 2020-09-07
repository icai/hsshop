@extends('merchants.default._layouts')
@section('head_css')
    <!--当前页面-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpkkkf2.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/purgeLog.css" />
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')
	
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav"> 
            <li>  
                <a href="{{ URL('/merchants/distribute/partner') }}">分销合伙人</a>
            </li>  
			<li>  
                <a href="{{ URL('/merchants/distribute/applayMemberList') }}">分销审核</a>
            </li>
			<li class="hover">  
                <a href="{{ URL('/merchants/distribute/purgeLog') }}">清退记录</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content">
	<!--筛选部分-->
	<div class="screen">
	    <form class="form-inline" id="forms">
	  		
	  		<div class="form-group col-sm-4">
	    		<label for="nickName">微信昵称:</label>
	    		<input type="text" class="form-control" name="nickname" id="nickName" placeholder="微信昵称" value="{{request('nickname') }}" >
	  		</div>
	  		<div class="form-group col-sm-4">
	    		<label for="phoneNum">手机号:</label>
	    		<input type="number" class="form-control" name="mobile" id="mobile" placeholder="手机号码"  value="{{request('mobile') }}">
	  		</div>
	  		{{--<div class="form-group col-sm-4">--}}
                {{--<label>来源:</label>--}}
                {{--<select name="source" id="orderSource" class="form-control">--}}
                    {{--<option value="">全部</option>--}}
                    {{--<option value="0" @if(request('source') == '0') selected  @endif>微商城</option>--}}
                    {{--<option value="6" @if(request('source') == '6') selected  @endif>小程序</option>--}}
                {{--</select>--}}
            {{--</div>--}}
              <input type="hidden" value="created_at-desc" id="sort" name="sort">
            <br />
            <div class="btns_clean">
                <input class="btn btn-primary" type="submit" value="筛选" />
                <a id="clearJudge" href="javascript:;">清空筛选条件</a>
            </div>
		</form>
		
	</div>
	<div class="addBtn">
	</div>
	<!--客户列表-->
	<div class="member_list">
		<ul class="list_item list_header">
			<li>微信昵称</li>
			<li>手机号码</li>
			<li>来源</li>
			<li title="可提现佣金">可提现佣金</li>
			<li class="sort " data-type="total_cash" data-sort="1">累积佣金</li>
			<li class="sort " data-type="son_num" data-sort="1">下级用户数</li>
			<li class="sort " data-type="trade_amount" data-sort="1" title="下级用户交易额">下级用户交易额</li>
			<li class="sort " data-type="created_at" data-sort="2">注册时间</li>
            <li class="sort " data-type="purgeTime" data-sort="1">清退时间</li>
            <li>清退理由</li>
		</ul>
		<div class="list_div">
            @foreach($data[0]['data'] as $val)
                <ul class="list_item list_body">
                    <li title="{{$val['member']['nickname']}}">{{$val['member']['nickname']}}</li>
                    <li>{{$val['member']['mobile']}}</li>
                    <li>{{$source[$val['source']]}}</li>
                    <li>{{$val['member']['cash']}}</li>
                    <li class="color-blue">{{$val['member']['total_cash']}}</li>
                    <li class="color-blue">{{$val['member']['son_num']}}</li>
                    <li class="color-blue">{{$val['member']['trade_amount']}}</li>
                    <li class="color-blue">{{$val['member']['created_at']}}</li>
                    <li class="color-blue">{{$val['created_at']}}</li>
                    <li class="pure-reason" data-text="{{$val['reason']}}"><div class="reason-content">{{$val['reason']}}</div><span class="note-show-box">{{$val['reason']}}</span><span class="note-show-box-arrow"></span></li>
                </ul>
                @endforeach
		</div>
	</div>
	<div class="main_bottom flex-end">
		<!-- 分页 -->
        {{$data[1]}}
	</div>
    
</div>


@endsection
@section('page_js')
    <script>
        var _host = "{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/purgeLog.js"></script>
@endsection