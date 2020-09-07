@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_pb3c980f.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">微页面草稿</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!-- logo区域 开始 -->
    <div class="content_header">
        <img src="{{ config('app.source_url') }}mctsource/images/1.jpg" width="50" height="50" />
        <!-- 店铺名称 开始 -->
        <div class="title_content">
            <p class="homepage_title">
                <strong>店铺主页</strong> (店铺主页)
            </p>
            <p class="title_des">创建时间：2016-09-22 15:57:22</p>
        </div>
        <!-- 店铺名称 结束 -->
        <!-- 链接 开始 -->
        <div class="link_itmes">
            <div class="link_tab">
                <span>编辑</span>
            </div>
            <div class="link_tab customTip_items">
                <span>链接</span>
                <!-- 店铺链接 开始 -->
                <div class="custom_tip">
                    <input class="link_copy" type="text" value="www.baidu.com1" disabled /><div class="copy_btn">复制</div>    
                </div>
                <!-- 店铺链接 结束 -->
            </div>
            <div class="QRcode_items link_tab">
                <span>二维码</span> 
                <!-- 二维码 开始 -->
                <div class="shop_QRcode">
                    <p class="items_title">手机扫码访问</p>
                    <div class="RQ_code img_wrap">
                        <img src="" />
                    </div>
                    <div class="QRcode_bottom">
                        <a href="javascript:void(0);">下载二维码</a>
                    </div>
                </div>
                <!-- 二维码 结束 -->
            </div>
        </div>
        <!-- 链接 结束 -->
    </div>
    <!-- logo区域 结束 -->
    <!-- 新建模板 开始 -->
    <div class="model_itmes mgb20">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#micro_page">新建微页面</button>
        <!-- 弹框 开始 -->
        <div class="modal fade" id="micro_page" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- 弹框标题 开始 -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">选择新建模板</h4>
                    </div>
                    <!-- 弹框标题 结束 -->
                    <!-- 弹框主体 开始 -->
                    <div class="modal-body">
                        主体
                    </div>
                    <!-- 弹框主体 结束 -->
                </div>
            </div>
        </div>
        <!-- 弹框 结束 -->
        <!-- 分类&搜索 开始 -->
        <div class="category_search">
            <!-- 分类 开始 -->
            <select class="chzn-select category_items" data-placeholder="Choose a Country" style="width:350px;" tabindex="1"> 
                <option value=""></option>  
                <option value="United States">United States</option>  
                <option value="United Kingdom">United Kingdom</option>  
                <option value="Afghanistan">Afghanistan</option>  
                <option value="Albania">Albania</option>  
            </select>
            <!-- 分类 结束 -->
            <!-- 搜索 开始 -->
            <label class="search_items">
                <input class="search_input" type="text" name="" value="" placeholder="搜索"/>   
            </label>
            <!-- 搜索 结束 -->
        </div>
        <!-- 分类&搜索 结束 -->
    </div>
    <!-- 新建模板 结束 -->
    <!-- 暂无数据 开始 -->
	 <div class="content_list">
		<table class="data-table table table-hover">
			<!-- 标题 -->
			<tr class="active">
				<td><input class="check_all" type="checkbox" name="" value="" >标题</td>
				<td class="blue_38f">创建时间↓</td>
				<td class="blue_38f">商品数</td>
				<td>浏览UV/PV</td>
				<td>到店UV/PV</td>
				<td class="blue_38f">序号</td>
				<td>操作</td>
			</tr>
			<!-- 列表 -->
			@forelse($microPageList['data'] as $item)
			<tr>
				<td class="blue_00f"><input class="check_single" type="checkbox" name="" value=""/>{{ $item['page_title'] }}</td>
				<td>{{ $item['created_at'] }}</td>
				<td>12</td>
				<td>0/0</td>
				<td>0/0</td>
				<td class="blue_38f">{{ $item['sequence_number'] }}</td>
				<td class="opt_wrap">
					<a class="copy_list" href="javascript:void(0);">
						<span class="blue_38f">复制</span>
					</a>
					<a href="javascript:void(0);">
						<span class="blue_38f">编辑</span>
					</a>
					<a class="del_list" href="javascript:void(0);">
						<span class="blue_38f">删除</span>
					</a>
					<a class="link_btn" href="javascript:void(0);" data-url="www.baidu.com0">
						<span class="blue_38f">链接</span>
					</a>
					<a class="set_homepage" href="javascript:void(0);">
						<span>店铺主页</span>
					</a>
				</td>
			</tr>
			@endforeach
		</table>
    </div>
    <div class="page">
        <!--<span>共 1 条，每页 50 条</span>-->
        {{ $pageHtml }}
    </div>
    <!-- 暂无数据 结束 -->
</div>
@endsection
@section('page_js')
<!-- 搜索插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_pb3c980f.js"></script>
@endsection
