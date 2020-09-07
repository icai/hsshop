@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/location.css" />
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
            <li class="">
                <a href="/merchants/currency/kefu">QQ客服</a>
            </li>
            <li class="">
                <a href="/merchants/currency/weChatKf">微信客服</a>
            </li>
            <li class="hover">
                <a href="javascript:;">客服电话</a>
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
	<div class="box">
		<a class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">新增客服</a>
		<table class="location-table">
			<thead>
				<tr>
					<th class="text-left cell-8">客服电话</th>
					<th class="text-left cell-8">添加时间</th>
					<th class="text-left cell-8">操作</th>
				</tr>
			</thead>
			<tbody class="add_kefu">
				
			</tbody>
		</table>
	</div>
	<div class="main_bottom flex-end">
		<span id="pageInfo">
			<span></span>
			<a class="firstPage" href="##">首页</a>
			<a class="prevPage" href="##">上一页</a>
			<a class="nextPage" href="##">下一页</a>
			<a class="lastPage" href="##">尾页</a>
		</span>
	</div>
</div>  
<!--弹出框-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">新增客服</h4>
      </div>
      <div class="modal-body">
        <form>
            <div class="form-group">
	            <label for="recipient-name" class="control-label">客服电话:</label>
	            <input type="text" class="form-control" name="telphone">
            </div>          
        </form>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-primary qq_up">确认</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div> 
<!--弹出框-->
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/kefu_phoneKf.js"></script>
@endsection

