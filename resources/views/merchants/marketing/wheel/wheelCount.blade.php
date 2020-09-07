@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_ajcshyc2.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="crumb_nav clearfix">
            <li>
                <a href="{{URL('/merchants/marketing')}}">营销中心</a>
            </li>
            <li>
                <a href="{{URL('/merchants/marketing/wheelList')}}">幸运大转盘</a>
            </li>
            <li>
                <a href="javascript:void(0);">中奖统计</a>
            </li>
        </ul>
        <!-- 二级导航三级标题 结束 -->
    </div>
</div>
@endsection
@section('content')
<div class="content">	
	<ul class="screen_nav nav nav-tabs mgb15" role="tablist">
        <li role="presentation" @if(!request('status')) class="active" @endif>
            <a href="/merchants/marketing/wheelCount/{{Route::input('wheelId')}}">所有参与</a>
        </li> 
        <li role="presentation"  @if(request('status') == 2) class="active"@endif>
            <a href="/merchants/marketing/wheelCount/{{Route::input('wheelId')}}?status=2">中奖</a>
        </li>  
        <li role="presentation" @if(request('status') == 1) class="active"@endif>
            <a href="/merchants/marketing/wheelCount/{{Route::input('wheelId')}}?status=1">未中奖</a>
        </li>         
   	</ul>
   	<div class="searchDiv">
		<form class="flex_around" action="" method="get">
    		<select name="status" class="form-control">
			  	<option value="" @if(request('status') == "") selected=selected @endif>所有</option>
			  	<option value="2" @if(request('status') == "2") selected=selected @endif>中奖</option>
			  	<option value="1" @if(request('status') == "1") selected=selected @endif>未中奖</option>
			</select>
			<input type="text" name="name" class="form-control" value="{{request('name')}}" placeholder="请输入搜索内容">
			<button type="submit" class="btn btn-primary" style="margin-right: 10px;">搜索</button>
            <a href="/merchants/marketing/wheelCount/{{Route::input('wheelId')}}?is_export=1&status={{request('status')}}&name={{request('name')}}"><div class="btn btn-primary">批量导出</div></a>
		</form>
	</div>
    <!--无数据-->
    <!--<div class="no_result">还没有相关数据</div>-->
	<!-- 列表 开始 -->
    <table class="table table-hover">
        <!-- 标题 -->
        <tr class="active">
            <td style="width:22%">粉丝</td>
            <td style="width:20%">参与时间</td>
            <td>奖品</td>
            <!--update 梅杰 2018年7月27日 增加收货地址 -->
            @if(request('status') == "2") <td style="width:26%">收货信息</td>  @endif
            <td>消耗积分</td>
            <td>获得积分</td>
        </tr>
        <!-- 列表 -->
        @forelse($data[0]['data'] as $val)
        <tr>
            <td class="td-flo">
            	<img class="flo-lef" width="60" height="60" src="{{$val['member']['headimgurl']}}"/>
            	<span class="flo-lef mgl10">{{$val['member']['nickname']}}</span>
        	</td>
            <td>{{$val['created_at']}}</td>
            <td>@if($val['is_win'] == 0)未中奖@elseif($val['is_win'] == 1){{$val['prize']}}@endif</td>
            <!--update 梅杰 2018年7月27日 增加收货地址 -->
            <!--update 梅杰 2018年7月30日 地址返回信息数据格式 -->
            @if(request('status') == "2") 
            <td style="width:24%">
                <!-- 许立 2018年08月17日 赠品且确认过收货信息才显示 -->
                @if ($val['address'])
                    <p align = "left">收货人：{{ $val['address']['title'] or '' }}</p>
                    <p align = "left">联系方式：{{ $val['address']['phone'] or '' }}</p>
                    <p align = "left">地址：{{ $val['address']['detail'] or '' }}</p>
                @else
                	<p>未确认收货信息</p>
                @endif
            </td>  
            @endif
            <td>{{$val['reduce_integra']}}</td>
            <td>{{$val['send_integra']}}</td>
        </tr>
        @endforeach
	</table>
    {{$data[1]}}
</div>
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_ajcshyc2.js"></script>
@endsection