@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_zitiList.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/currency/express') }}" style="border: none;">快递发货</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/currency/receptionList') }}">上门自提</a>
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
    <div class="content_top">
        <div class="top_left">
            <h4>买家上门自提功能</h4>
            <p>启用上门自提功能后，买家可以就近选择你预设的自提点，下单后你需要尽快将商品配送至指定自提点。<br />
                <a href="https://www.huisou.cn/home/index/detail/785/help" target="_blank" style="color: #2277FF;">查看【上门自提】功能使用教程</a>
            </p>
        </div>
        <div class="top_right">
            <!-- 按钮 开始 -->
            <div class="switch_items">
                <label class="ui-switcher @if($isOpenZiti) ui-switcher-on @else ui-switcher-off @endif" data-is-open="{{ $isOpenZiti }}"></label>
            </div>
            <!-- 按钮 结束 -->
        </div>
    </div>
    <a href="{{URL('/merchants/currency/editSeception')}}" type="button" id="newAdd" class="btn btn-success">新增自提点</a>
    <!--动态添加显示块-->
    <div id="showMsg">
        @if($list)
		<table class="showMsgTitle table table-striped">
		  	<thead>
			    <tr>
			      	<th class="wd_15">自提点名称</th>
			      	<th class="wd_10">省份</th>
			      	<th class="wd_10">城市</th>
			      	<th class="wd_10">地区</th>
                    <th class="wd_20">地址</th>
                    <th class="wd_15">联系电话</th>
                    <th class="wd_10">操作</th>
			    </tr>
		  	</thead>
            @foreach($list as $val)
		  	<tbody>
			    <tr>
			      	<td>{{ $val['title'] }}</td>
			      	<td>{{ $val['province_id'] }}</td>
			      	<td>{{ $val['city_id'] }}</td>
                    <td>{{ $val['area_id'] }}</td>
                    <td>{{ $val['address'] }}</td>
                    <td>{{ $val['telphone'] }}</td>
			      	<td><a href="/merchants/currency/editSeception?id={{ $val['id'] }}" class="co_38f edit">编辑</a> - <span class="co_38f delete pop" data-id="{{ $val['id'] }}" data-toggle="delete_pop">删除</span></td>
			    </tr>
		  	</tbody>
            @endforeach
		</table>
		@else
		<div class="addMsg">还没有相关数据</div>
        @endif
	</div>
</div>
<!-- 删除弹框 -->
<div class="popover left delete_pop" role="tooltip" style="display: none;height: 55px;">
	<div class="arrow"></div>
	<div class="popover-content">
		<span>你确定要删除吗？</span>
		<button class="btn btn-primary sure_btn">确定</button>
		<button class="btn btn-default cancel_btn">取消</button>
	</div>
</div> 
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_zitiList.js"></script>
<script type="text/javascript">
    var count = {{ $count }};
</script>
@endsection