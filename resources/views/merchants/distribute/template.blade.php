@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/distribute_ykeomu64.css" /> 
     <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />

	<!-- 自定义layer皮肤css -->
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
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
                <a href="{{ URL('/merchants/distribute') }}">一键配置</a>
            </li>  
            <li class="hover"> 
                <a href="{{ URL('/merchants/distribute/template') }}">分销模板</a>
            </li>  
			<li> 
                <a href="{{ URL('merchants/distribute/applyList') }}">申请页面</a>
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
		<div class="btns_clean">
			<button class="btn btn-primary add_template">新建分销模板</button>
			
			<input type="hidden" id="is_on_off" value="0" />
		</div>
	</div>
	<!--添加按钮-->
	<div class="addBtn">
		
	</div>
	<!--客户列表-->
	<div class="member_list">
		<table class="table">
			<thead>
				<tr>
					<th>模板名称</th>
					<th>分销等级</th>
					<th>模拟售价(百分比)</th>
					<th>商品成本</th>
					<th>直接分销佣金</th>
					<th>间接分销佣金</th>
					<th>创建时间</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<!-- <tr>
					<td rowspan='3' class="distribute-name">
						<p>分销模板1</p>
						<input class="w-120" type="text" value="分销模板1">
					</td>
					<td>
						<p>普通分销员</p>
						<input class="w-120" type="text" value="普通分销员">
					</td>
					<td>
						<p>100.00</p>
						<input class="w-70" type="text" value="100.00">
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70">%
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70">%
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70">%
					</td>
					<td>
						<p>2018-09-20  13:40:26</p>
						<input class="w-160" type="text">
					</td>
					<td rowspan='3' class="operate">
						<p>
							<a href="javascript:void(0);" class="operate-edit">编辑</a>&nbsp;&nbsp;
							<a href="javascript:void(0);" class="operate-delete">删除</a>
						</p>
						<input href="javascript:void(0);" class="operate-edit" value="保存">&nbsp;
						<input href="javascript:void(0);" class="operate-delete" value="取消">

					</td>
				</tr>
				<tr>
					<td>
						<p>普通分销员</p>
						<input class="w-120" type="text" value="普通分销员">
					</td>
					<td>
						<p>100.00</p>
						<input class="w-70" type="text" value="100.00">
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70%">%
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70%">%
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70%">%
					</td>
					<td>
						<p>2018-09-20  13:40:26</p>
						<input class="w-160" type="text">
					</td>
				</tr>
				<tr>
					<td>
						<p>普通分销员</p>
						<input class="w-120" type="text" value="普通分销员">
					</td>
					<td>
						<p>100.00</p>
						<input class="w-70" type="text" value="100.00">
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70">%
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70">%
					</td>
					<td>
						<p>70</p>
						<input class="w-70" type="text" value="70">%
					</td>
					<td>
						<p>2018-09-20  13:40:26</p>
						<input class="w-160" type="text">
					</td>
				</tr> -->
			</tbody>
		</table>
		
		<div class="h-30"></div>
	</div>
</div>
<script>
	var grade = {!! json_encode($grade) !!};
	console.log(grade);
	var title = "{{$shop['distribute_default_grade_title']}}";
</script>
@endsection

@section('page_js')
	<script src="{{ config('app.source_url') }}static/js/require.js"></script>
	<script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
	<script src="{{ config('app.source_url') }}mctsource/js/distribute_ykeomu64.js"></script>
	
@endsection