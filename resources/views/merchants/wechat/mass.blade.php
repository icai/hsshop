@extends('merchants.default._layouts') @section('head_css')
<!-- 时间插件样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 上传插件样式 -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_q4l9x3kr.css"> 
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
<div class="content" style="display: -webkit-box;">
	<!--主体左侧列表开始-->
	@include('merchants.wechat.slidebar')
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<div class="header  clearfix">
			<div class="container_title">
				发送给：<span class="co_4b0">所有人</span>
				<a class="co_38f" href="javascript:void(0);">去筛选</a>
				<p class="num_info">您正准备向<span>100000</span>人发送消息</p>
			</div>
			<div class="help">
				<i class="glyphicon glyphicon-question-sign" style="color: #3c3;"></i>
				<a class="co_38f" href="javascript:void(0);">群发常见问题汇总</a>
			</div>
		</div>
		<!-- 创建群发消息内容开始 -->
		<div class="set_message">
			<ul class="set_message_title">
				<li class="text opacity" data-class=".editor_text"><i class="icon icon_text"></i>文本</li>
				<li class="img" data-class=".editor_img" data-toggle="modal" data-target="#myModal-adv"><i class="icon icon_img"></i>图片</li>
				<li class="news js_showModel" data-class=".editor_news" data-toggle="modal" data-target="#myModal"><i class="icon icon_news"></i>图文</li>
			</ul>
			<div class="set_message_content">
				<div class="editor_text">
					<textarea id="emotion_text" maxlength="600"></textarea>
				</div>
				<div class="editor_img">
					<div class="select" data-toggle="modal" data-target="#myModal-adv">
						<span>+</span>
						<p>从素材库中选择</p>
					</div>
					<div class="message_img">
						<img src="" />
						<a class="co_38f" href="javascript:void(0);">删除</a>
					</div>
				</div>
				<div class="editor_news">
					<div class="add_news">
						<div class="item">
							<div class="img_text">
								<span class="green">图文</span>
								<a class="co_blue" href="javascript:void(0);"></a>
							</div>
							<div class="read_all clearfix">
								<a href="javascript:void(0);">
									<span>阅读全文</span>
									<span class="pull-right">></span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="set_message_footer clearfix">
				<span class="emotion pull-left"></span>
				<span class="word_count pull-right">大约还可输入600字</span>
			</div>
		</div>
		<p class="send_info" style="color:#b94a48;font-size:12px; display: none;">请输入要发送的内容</p>
		<!-- 创建群发内容消息结束 -->
		<div class="btn_group">
			<button class="btn btn-primary promptly">立即群发</button>
			<button class="btn btn-default preview" data-toggle="modal" data-target="#previewModal">手机预览</button>
			<button class="btn btn-default timer">定时群发</button>
			<div class="timer_cap">
				<input id='datetimepicker' type='text' />
				<button class="btn btn-primary">确定</button>
				<button class="btn btn-default">取消</button>
			</div>
		</div>
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection @section('other')
<!--图片弹框开始-->
<div class="modal export-modal myModal-adv" id="myModal-adv">
	<div class="modal-dialog" id="modal-dialog-adv">
		<form class="form-horizontal">
			<div class="modal-content content_first">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
					<ul class="module-nav modal-tab">
						<li class="active">
							<a href="#js-module-goods" class="js-modal-tab">我的图片</a>
						</li>
					</ul>
					<div class="search-region">
						<div class="ui-search-box">
							<input class="txt js-search-input" type="text" placeholder="搜索" value="">
						</div>
					</div>
				</div>
				<div class="modal-body">
					<div class="category-list-region">
						<ul class="category-list">
							<li class="js-category-item active">未分组
								<span>8</span>
							</li>
							<li class="js-category-item">111111
								<span>0</span>
							</li>
						</ul>
					</div>
					<div class="attachment-list-region">
						<div class="imgData">
							<ul class="image-list">
								<li class="image-item">
									<img class="image-box" src="https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=715133909,3016426832&fm=21&gp=0.jpg" />
									<div class="image-meta">1920*1200</div>
									<div class="image-title">01.png</div>
									<div class="attachment-selected no">
										<i class="icon-ok icon-white"></i>
									</div>
								</li>
								<li class="image-item">
									<img class="image-box" src="https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=715133909,3016426832&fm=21&gp=0.jpg" />
									<div class="image-meta">1920*1200</div>
									<div class="image-title">01.png</div>
									<div class="attachment-selected no">
										<i class="icon-ok icon-white"></i>
									</div>
								</li>
							</ul>
							<div class="attachment-pagination">
								<div class="ui-pagination">
									<span class="ui-pagination-total">共8条， 每页15条</span>
								</div>
							</div>
							<a href="##" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;">上传图片</a>
						</div>
						<!--列表中的图片个数为0的时候显示这个模态框  no隐藏数据-->
						<div id="layerContent_right" class="no">
							<a class="js_addImg" href="#uploadImg">+</a>
							<p>暂无数据，点击添加</p>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<div class="text-center">
						<a class="ui-btn js-confirm" disabled="disabled">确认</a>
						<a class="ui-btn ui-btn-primary no">确认</a>
					</div>
				</div>
			</div>
			<div class="modal-content content_second">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
					<div class="cap_head clearfix">
						<a class="co_38f js_prev" href="javascript:void(0);">
							<选择图片 </a>
								<span>上传图片</span>
					</div>
				</div>
				<div class="modal-body">
					<div id="uploadLayerContent_botm">
						<div id="wrapper">
							<div id="container">
								<!--头部，相册选择和格式选择-->
								<div id="uploader">
									<div class="queueList">
										<div id="dndArea" class="placeholder">
											<label id="filePicker"></label>
											<p>或将照片拖到这里，单次最多可选300张</p>
										</div>
									</div>
									<div class="statusBar" style="display:none;">
										<div class="progress">
											<span class="text">0%</span>
											<span class="percentage"></span>
										</div>
										<div class="info"></div>
										<div class="btns">
											<div id="filePicker2"></div>
											<div class="uploadBtn">开始上传</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<div class="text-center">
						<a class="ui-btn js-confirm" disabled="disabled">确认</a>
						<a class="ui-btn ui-btn-primary no">确认</a>
					</div>
				</div>
			</div>

		</form>
	</div>
</div>
<!--图片弹框结束-->
<!--选择图文弹框开始-->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
				<ul class="list">
					<li class="js_small list_active">微信图文</li>
					<li class="co_000 js_link">
						<a class="co_38f" href="javascript:void(0)">微信图文素材管理</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th class="title">
								<span>标题</span>
								<a class="co_38f" href="javascript:void(0);">刷新</a>
							</th>
							<th class="set_time">创建时间</th>
							<th class="search">
								<input type="text" />
								<button class="btn btn-default">搜</button>
							</th>
						</tr>
					</thead>
					<tbody class="tabel_info">
						<tr>
							<td colspan="3">
								<div class="info">
									没有相关数据
								</div>
							</td>
						</tr>
					</tbody>
					<tbody class="small">
						<tr>
							<td>
								<div class="title_content">
									<div class="img_text">
										<span class="green">图文</span>
										<a class="co_blue" href="javascript:void(0);">123456</a>
									</div>
									<div class="read_all clearfix">
										<a class="jump" href="javascript:void(0)">
											<span>阅读全文</span>
											<span class="pull-right">></span>
										</a>
									</div>
								</div>
							</td>
							<td>2016-10-11<br />15:25:38</td>
							<td><button class="btn btn-default">选取</button></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer clearfix">
				<span class="pull-right">共 0 条，每页 3 条</span>
			</div>
		</div>
	</div>
</div>
<!--选择图文弹框结束-->
<!--预览弹框开始-->
<div class="modal previewModal" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
				<span class="preview_info">预览使用说明：</span>
			</div>
			<div class="modal-body">
				<p>使用管理员微信发送信息“admin:15857197034”到“您已经将微信解绑，请您将微信号重新绑定店铺后在尝试。”微信公众号完成绑定。</p>
			</div>
			<div class="modal-footer clearfix">
				<button class="btn btn-success">我知道了</button>
			</div>
		</div>
	</div>
</div>
<!--预览图文弹框结束-->
@endsection @section('page_js') @parent
<!--bootstrap-datatimepicker 时间插件-->
<script src="{{config('app.source_url')}}/static/js/moment/moment.min.js"></script>
<script src="{{config('app.source_url')}}/static/js/moment/locales.min.js"></script>
<script src="{{config('app.source_url')}}/static/js/bootstrap-datetimepicker.min.js"></script>
<!--jq扩充包用于qq表情-->
<script src="{{config('app.source_url')}}static/js/jquery-browser.js"></script>
<!--qq表情包插件-->
<script src="{{config('app.source_url')}}static/js/jquery.qqFace.js"></script>
<!-- webuploader上传插件引入 -->
<script src="{{config('app.source_url')}}/static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/wechat_upload.js"></script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_q4l9x3kr.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
	var domain_url = "{{config('app.source_url')}}";
</script>

@endsection