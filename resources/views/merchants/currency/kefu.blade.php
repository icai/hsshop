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
            <li class="hover">
                <a href="javascript:;">QQ客服</a>
            </li>
            <li class="">
                <a href="/merchants/currency/weChatKf">微信客服</a>
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
		请先开通客服组件
		<a href="http://shang.qq.com/v3/widget.html" target="_blank" style="color: #0099FC;">(去开通)</a><br />
		<table class="location-table">
			<thead>
				<tr>
					<th class="text-left cell-8">客服姓名/昵称</th>
					<th class="text-left cell-8">客服QQ</th>
					<!--<th class="text-left cell-8">客服微信号</th>-->
					<th class="text-left cell-8">客服电话</th>
					<th class="text-left cell-8">添加时间</th>
					<th class="text-left cell-8">操作</th>
				</tr>
			</thead>
			<tbody class="add_kefu">
				@forelse($list['data'] as $val)
				<tr class="remover_del">
					<td class="">
						@if($val['name'])
						{{ $val['name']}}
						@else
						---
						@endif
					</td>
					<td>{{ $val['qq'] }}</td>
					<!--<td>{{ $val['weixin'] }}</td>-->
					<td>{{ $val['telphone'] }}</td>
					<td>{{ $val['created_at'] }}</td>
					<td class="location-action" style="min-width: 120px;">
						<a class="a-shanchu" href="javascript:;" data-id ="{{ $val['id'] }}">删除</a>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="6" class="z_none">暂无数据</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<div>{{ $pageHtml }}</div>
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
	            <label for="message-text" class="control-label"><span style="color: red;">*</span>客服QQ:</label>
	            <input type="text" class="form-control" name="qq">
	        </div>
	        
	        <div class="form-group">
	            <label for="message-text" class="control-label">客服电话:</label>
	            <input type="text" class="form-control" name="telphone">
	        </div>
            <div class="form-group">
	            <label for="recipient-name" class="control-label">客服姓名:</label>
	            <input type="text" class="form-control" name="name">
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
<script type="text/javascript">
	$(function(){
		// 删除列表
	    $('body').on('click','.a-shanchu',function(e){
	        e.stopPropagation();
	        var _this = this;
			var id=$(this).data('id');
	        showDelProver($(_this),function(){
				$.ajax({
					type:"GET",
					url:"/merchants/currency/kefuDel/"+id,
					dataType:'json',
					success: function(res){
						if(res.status == 1){						
							tipshow(res.info);
							setTimeout(function(){
								location.reload() 
							},1000);
						}else{
							tipshow(res.info,'warn');
						}
					},
					error:function(){
						alert("数据访问错误");
					}
				});	
	        })
	   });		
		
		//添加
		$("body").on('click','.qq_up',function(){
			$.ajax({
				type:"POST",
				url:"/merchants/currency/kefu",
				data:{
					name:$("input[name='name']").val(),
					qq:$("input[name='qq']").val(),
					weixin:$("input[name='weixin']").val(),
					telphone:$("input[name='telphone']").val()
				},
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				async:true,
				success:function(res){
					if(res.status == 1){
						tipshow(res.info);
						setTimeout(function(){
							location.reload() 
						},1000);
					}else{
						tipshow(res.info,'warn');
					}
				},
				error:function(){
					alert("数据访问错误")
				}
			});
		})				
	})
</script>
@endsection

