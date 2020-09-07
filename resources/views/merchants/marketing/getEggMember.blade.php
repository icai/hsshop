@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_getPrize.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav clearfix">
            <li>
                <a href="{{URL('/merchants/marketing')}}">营销中心</a>
            </li>
            <li>
                <a href="{{URL('/merchants/marketing/egg/index')}}">砸金蛋</a>
            </li>
            <li>
                <a href="javascript:void(0);">中奖统计</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->

    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!--顶部导航内容-->
        <ul class="screen_nav nav nav-tabs mgb15">
            @foreach( $status as $k=>$v)
                {{--<li class="active"><a href="">所有参与</a></li>--}}
                {{--<li><a href="">中奖的</a></li>--}}
                <li @if( request('status',0) == $k  ) class="active" @endif ><a href="{{ URL('merchants/marketing/egg/member/list/'.$eggId.'?status='.$k) }}">{{ $v }}</a></li>
            @endforeach

        </ul>
        <!-- <a href="##" class="tutorial"><span id="icon">?</span>  查看【碎蛋行动】使用教程</a> -->
    <!--数据显示页面部分-->
    <div class="show_data">
    	<div class="searchDiv">
    		<form class="flex_around" action="" method="get">
	    		<select name="status" class="form-control">
				  	<option value="0" @if(request('status') == 0) selected=selected @endif>所有</option>
				  	<option value="1" @if(request('status') == 1) selected=selected @endif>中奖</option>
				  	<option value="2" @if(request('status') == 2) selected=selected @endif>未中奖</option>
				</select>
				<input type="text" name="name" class="form-control" value="{{request('name')}}" placeholder="请输入搜索内容">
				<button type="submit" class="btn btn-primary" style="margin-right: 10px;">搜索</button>
				<a href="{{ request()->fullUrl() .'&isExport=1' }}"><div class="btn btn-primary">批量导出</div></a>
			</form>
    	</div>
        @if(!empty($list['data']))
        <table class="table table-hover">
            <tr class="active">
                <td style="width: 24%;">粉丝</td>
                <td>参与时间</td>
                <td style="width: 14%;">奖品</td>
                @if(request('status') == "1")
                    <td style="width: 34%;">收货地址</td>
                @endif
            </tr>
            @foreach($list['data'] as $v)
                <tr>
                    <td class="td-flo">
                        <img class="flo-lef" width="60" height="60" src="{{$v['headimgurl']}}">
                        <span class="flo-lef mgl10">{{$v['name']}}</span>
                    </td>
                    <td>{{$v['created_at']}}</td>
                    <td>
                        @if($v['is_prize'] == 1)
                            {{ $v['pName'] }}
                        @else
                            未中奖
                        @endif
                    </td>
                    @if(request('status') == "1")
                    <td style="width:24%">
                    	@if($v['type'] ==3)

                            @if(!empty($v['address']))
                                <p align = "left">收货人：{{ $v['address']['title']  or ''}} </p>
                                <p align = "left">联系方式：{{ $v['address']['phone']  or ''}} </p>
                                <p align = "left"> 地址：{{ $v['address']['detail']  or ''}} </p>
                              @else
                                <p>未确认收货信息</p>
                            @endif
		            	@endif
		            </td>
		            @endif
                </tr>
            @endforeach
            {{--@empty--}}
                {{--<div class="noData">还没有相关数据</div>--}}
        <!--数据显示部分-->
            </table>
        @else
             <div class="noData">还没有相关数据</div>
        @endif
            {{$pageHtml}}
    </div>
</div>
@endsection
@section('page_js')
<!--主要内容js文件-->
<!-- <script src="{{ config('app.source_url') }}mctsource/js/marketing_getPrize.js" type="text/javascript" charset="utf-8"></script> -->
@endsection