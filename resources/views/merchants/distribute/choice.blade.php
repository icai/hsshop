<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
	<meta name="renderer" content="webkit">
	<meta name="csrf-token" content="RBwNrxCTfVKdfG04yNSk6OTpEtUQpQodIqqIxvJN">
	<meta name="csrf-token" content="{{ csrf_token() }}"> 
	<title></title>
	<!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link href="{{ config('app.source_url') }}static/css/bootstrap.min.css" rel="stylesheet">
    <!-- 搜索美化插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css">
    <!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-rewrite.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/distribute_8gn5zwn3.css" />
</head>
<body>
<!-- 顶部导航 -->
<div class="header distribute_tip">
	<p><span class="mr10">分销模块</span> | <a href="{{ URL('/merchants/distribute/template') }}" class="blue ml10" target="_blank">新建分销模块</a></p>
	<i class="header-close">×</i>
</div>
<!-- 内容 -->
<div class="content">
	<table class="table-wrap">
		<thead>
			<tr>
				<th style="width:45%;">标题</th>
				<th>创建时间</th>
				
				<th style="text-align:right;padding-right:15px;">操作</th>
			</tr>
		</thead> 
		<tbody>
			
		</tbody> 
	</table>
</div>
<!-- 底部 -->
<div class="footer"> 
	<input type="button" class="btn btn-primary btn-sm" value="确定使用" id="btn_ok" /> 
</div>
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
<!-- 核心 base.js JavaScript 文件 -->
<script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/distribute_8gn5zwn3.js"></script>

</body>
</html>

