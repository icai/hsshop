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
            <li class="hover">
                <a href="javascript:;">微信客服</a>
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
		<div class="clear">
			<a class="btn btn-primary z_left" data-toggle="modal" data-target="#exampleModal">新增客服</a>
			<div class="z_left z_lef20">请确保该店铺已绑定了微信服务号且已开通过了客服功能
				<a href="https://mp.weixin.qq.com/" target="_blank" style="color: #0099FC;">(去开通)</a><br />
			</div>
			<a class="btn btn-primary z_right" href="https://mpkf.weixin.qq.com/" target="_blank">多客服系统登录</a>			
		</div>		
		<table class="location-table">
			<thead>
				<tr>
					<th class="text-left cell-8">客服姓名/昵称</th>
					<th class="text-left cell-8">客服头像</th>
					<th class="text-left cell-8">客服微信号</th>
					<th class="text-left cell-8">状态</th>
					<th class="text-left cell-8">操作</th>
				</tr>
			</thead>
			<tbody class="add_kefu">
				
			</tbody>
		</table>
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
        <form id='wx_form'>
	        <div class="form-group">
	            <label for="message-text" class="control-label">客服账户:</label>
	            <input type="text" class="form-control" name="kf_account" placeholder="帐号最多10个字符，必须是英文、数字字符或者下划线">
	        </div>
	        <div class="form-group">
	            <label for="message-text" class="control-label">客服昵称:</label>
	            <input type="text" class="form-control" name="kf_nick" placeholder="最长16个字">
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
<!--绑定弹出框-->
<div class="modal fade" id="exampleModal_w" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">新增客服</h4>
      </div>
      <div class="modal-body">
        <dev id="invite">
	        <div class="form-group">
	            <label for="message-text" class="control-label">客服微信号(非手机号，未设置微信号的用户请先在微信设置微信号):</label>
	            <input type="text" class="form-control" name="invite_wx">
	        </div>       
        </dev>
      </div>
      <div class="modal-footer">
      	<a class="btn btn-primary kfwx_up">确认</a>
        <a class="btn btn-default" data-dismiss="modal">取消</a>
      </div>
    </div>
  </div>
</div> 
<!--绑定弹出框-->
<!--修改弹出框-->
<div class="modal fade" id="exampleModal_x" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">新增客服</h4>
      </div>
      <div class="modal-body">
        <form id="invite_x">
	        <div class="form-group">
	            <label for="message-text" class="control-label">客服昵称:</label>
	            <input type="text" class="form-control" name="kf_nick_up">
	        </div>       
        </form>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-primary xiugai_up">确认</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div> 
<!--修改弹出框-->
<!--图片弹出框-->
<div class="modal fade" id="exampleModal_img" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<h4 class="modal-title" id="exampleModalLabel">新增客服</h4>
        </div>
        <div class="modal-body">
       		<div class="form-group set">
	            <div class="set_right">
	            	<label for="recipient-name" class="control-label">客服头像:</label>
	            	<span>支持jpg, png格式，图片大小不超过5M，建议上传正方形图片</span>	            	
	                <input type="hidden" id="logo" value="">
	                <form id="uploadForm" enctype="multipart/form-data">
	                    <a class="alter" href="javascript:void(0);" id="logoChange">
	                    	<span class="img_span">上传图片 </span>
	                    	<input type="file" name="file" id="files" accept="image/jpeg,image/gif,image/png">                    		
	                	</a>
	                </form>
	                <div class="img_src hidden"><img class="logo" src=""></div>
	            </div>
	        </div> 
        </div>
        <div class="modal-footer">
      		<button type="button" class="btn btn-primary img_up">确认</button>
        	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        </div>
    </div>
  </div>
</div> 
<!--图片弹出框-->
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/kefu_weChatKf.js"></script>
@endsection

