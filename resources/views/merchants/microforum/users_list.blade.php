@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/users_uynh7ai2.css" />

@endsection
@section('slidebar')
    @include('merchants.microforum.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">帖子管理</div>
        <!-- 二级导航三级标题 结束 -->
    </div> 
</div>

@endsection
@section('content')
<div class="content">
	<!--头部筛选部分-->
    <form class="filter_conditions flex_between" action="" method="get">
        <ul>
            <li>
                <label>用户ID：<input type="text" value="{{$search['id'] or ''}}" name="id" placeholder="" /></label>
            </li>
            <li>
                <button type="submit" class="btn btn-primary screening">筛选</button> 
            </li>
        </ul>
        <ul>
            <li>
                <label>
                    昵称：
                    <input type="text" value="{{$search['nickname'] or ''}}" name="nickname"  placeholder="标题" />
                </label>
            </li> 
        </ul>
        <ul>
            <li>状态：
                <select name="is_block">
                    <option value="0" @if (isset($search['is_block']) && $search['is_block'] == 0) selected=1  @endif>正常</option>
                    <option value="1" @if (isset($search['is_block']) && $search['is_block'] == 1) selected=1  @endif>拉黑</option>
                    <option value="2" @if (!isset($search['is_block'])) selected=1 @endif>全部</option>
                </select>
            </li>  
        </ul> 
    </form> 

    <div class="main_content mt20">
        <ul class="main_content_title"> 
            <li>用户ID</li>
            <li>头像</li>
            <li>昵称</li>
            <li>帖子数量</li>
            <li>状态</li> 
            <li>操作</li>
        </ul>
        @forelse ( $list as $v )
        <ul class="data_content"> 
            <li>{{$v['id']}}</li>
            <li>
            	<img src="{{$v['headimgurl']}}" class="mt5" width="40" height="40" />
            </li>
            <li>{{$v['nickname']}}</li>
            <li>{{$v['postCount']}}</li>
            <li>{{$v['is_block']}}</li>
            <li data-id="{{$v['id']}}">
			@if ($v['is_block'] == '拉黑')
                <a href="javascript:void(0);" class="recovery">恢复</a> 
			@else
                <a href="javascript:void(0);" class="defriend">拉黑</a>
			@endif
            </li>
        </ul>
        @empty
            <ul class="data_content">暂无数据</ul>
        @endforelse
    </div>
    <!-- 分页 -->
    <div class="pageNum">{{ $pageHtml }}</div>
</div>
@endsection
@section('page_js') 
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/users_uynh7ai2.js"></script>
@endsection
