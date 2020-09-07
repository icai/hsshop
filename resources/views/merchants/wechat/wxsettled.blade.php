@extends('merchants.default._layouts') @section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wxsettled.css"> 
@endsection @section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 面包屑导航 开始 -->
		<ul class="crumb_nav">
			<li>
				<a href="#&status=1">营销中心</a>
			</li>
			<li>
				<a href="#&status=2">微信</a>
			</li>
		</ul>
		<!-- 面包屑导航 结束 -->
	</div>
	<!-- 三级导航 结束 -->

	<!-- 帮助与服务 开始 -->
	<div id="help-container-open" class="help_btn">
		<i class="glyphicon glyphicon-question-sign"></i>帮助和服务
	</div>
	<!-- 帮助与服务 结束 -->
</div>
@endsection @section('content')
<!-- 中间 开始 -->
<div class="content">
	<div class="open_wethat">
		<div class="open_left">
			<p class="co_4b0">绑定微信公众号，把店铺和微信打通</p>
			<p>绑定后即可在这里管理你的公众号，会搜云提供比微信官方后台更强大的功能！</p>
			<a class="btn btn-success set" href="javascript:;" target="_blank" onclick="goset()">我有微信公众号，立即设置</a>
		</div>
		<div class="open_right">
			<p>温馨提示：</p>
			<ul>
				<li>一个微信公众号只能和一个店铺绑定</li>
				<li>认证服务绑定之后，如果要解绑可以联系会搜云客服</li>
				<li class="red">为保证所有功能正常，授权时请保持默认选择，把权限统一授权给会搜云</li>
			</ul>
		</div>
	</div>
	<div class="role_info">微信给不同类型公众号提供不同的接口，会搜云能提供的功能也不相同：</div>
	<table class="table table-bordered">
	  	<thead>
		    <tr>
		      	<th></th>
		      	<th>未认证订阅号</th>
		      	<th>认证订阅号</th>
		      	<th>未认证服务号</th>
		      	<th>认证服务号</th>
		    </tr>
	  	</thead>
	  	<tbody>
		    <tr>
		      	<td class="co_000">消息自动回复</td>
		      	<td>√</td>
		      	<td>√</td>
		      	<td>√</td>
		      	<td>√</td>
		    </tr>
		    <tr>
		      	<td class="co_000">微信自定义菜单</td>
		      	<td></td>
		      	<td>√</td>
		      	<td>√</td>
		      	<td>√</td>
		    </tr>
		    <tr>
		      	<td class="co_000">群发/定时群发</td>
		      	<td></td>
		      	<td>√</td>
		      	<td></td>
		      	<td>√</td>
		    </tr>
		    <tr>
		      	<td class="co_000">高级客户管理</td>
		      	<td></td>
		      	<td class="co_000">部分功能</td>
		      	<td></td>
		      	<td>√</td>
		    </tr>
		    <tr>
		      	<td class="co_000">可申请微信支付</td>
		      	<td></td>
		      	<td></td>
		      	<td></td>
		      	<td>√</td>
		    </tr>
	  	</tbody>
	</table>
	<a class="btn btn-default" href="http://kf.qq.com/faq/170104AJ3y26170104Yj673y.html">进一步了解他们的区别</a>
</div>
<!--商品模态框开始-->
<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">提示</h4>
            </div>
            <div class="modal-body">
                请在新窗口中完成微信公众号授权<a class="co_38f" href="javascript:void(0);">查看授权教程</a>
            </div>
            <div class="modal-footer clearfix">
                <a type="button" class="btn btn-success" href="/merchants/wechat/weixinSet">已成功设置</a >
                <a type="button" href="{{ $authUrl }}" class="btn btn-default" >授权失败，重试</a>
            </div>
        </div>
    </div>
</div>
<!--商品模态框结束-->
<!-- 中间 结束 -->
@endsection @section('page_js') @parent
<script type="text/javascript">
	function goset(){
		$.ajax({
			type: "GET",
			async: false,
			url: '/merchants/wechat/authRedirect',
			success: function(data) {
				$('.set').attr('href',data.url);
				$('.modal').modal('show');
			}
		});
		return true;
	}
	//模态框居中控制
	$('.modal').on('shown.bs.modal', function (e) { 
	  	// 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
	  	$(this).css('display', 'block'); 
	  	var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
	  	if(modalHeight < 0){
	  		modalHeight = 0;
	  	}
	  	$(this).find('.modal-dialog').css({ 
	    	'margin-top': modalHeight 
	 	}); 
	});
</script>
@endsection