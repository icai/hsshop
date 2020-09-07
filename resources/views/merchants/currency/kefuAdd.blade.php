@extends('merchants.default._layouts')
@section('head_css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/editAddress.css" />
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
	                <a href="{{URL('/merchants/currency/kefu')}}">新增客服</a>
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
		<div class="app">
			<div class="app-inner clearfix">
				<div class="app-init-container">
					<div class="app__content"><div data-reactroot="" class="location-add">
							<h3>新增客服</h3>
							<form class="zent-form zent-form--horizontal location-add-form">
								<div class="zent-form__control-group ">
									<label class="zent-form__control-label">客服QQ</label>
									<div class="zent-form__controls">
										<div class="zent-input-wrapper">
											<input type="text" class="zent-input" name="" placeholder="请填写客服QQ号" value="">
										</div>
									</div>
								</div>
								<div class="zent-form__form-actions">
									<button class="ui-btn ui-btn-primary" type="button">保存</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="notify-bar js-notify animated hinge hide"></div>
			</div>
		</div>
	</div>
@endsection
@section('page_js')
<script type="text/javascript">
	$(function(){
		$(".ui-btn-primary").click(function(){
			$.ajax({
				type:"GET",
				url:"",
				data:{
					QQnumber:$(".zent-input").val()
				}
				async:true,
				success:function(res){
					console.log(res)
					if(rea.status == 1){
						tipshow(res.info);
						window.location.href="/merchants/currency/kefu";
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

