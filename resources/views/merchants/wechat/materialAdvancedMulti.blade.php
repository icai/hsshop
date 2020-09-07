@extends('merchants.default._layouts') @section('head_css')
<!-- 上传插件样式 -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_jblhryde.css"> 
@endsection @section('middle_header')
<div class="middle_header">
	<!-- 三级导航 开始 -->
	<div class="third_nav">
		<!-- 面包屑导航 开始 -->
		<ul class="crumb_nav">
			<li>
				<a href="javascript:void(0);">公众号</a>
			</li>
			<li>
				<a href="javascript:void(0);">{{ $title }}</a>
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
	<input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
	<!--主体左侧列表开始-->
	@include('merchants.wechat.slidebar')
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
	<div class="right_container">
		<!-- 导航模块 开始 -->
		<div class="nav_module clearfix">
			<!-- 左侧 开始 -->
			<div class="pull-left">
				<!-- （tab试导航可以单独领出来用） -->
				<!-- 导航 开始 -->
				<ul class="tab_nav">
					<li>
						<a href="{{ URL('/merchants/wechat/materialWechat') }}">微信图文</a>
					</li>
					<li class="hover">
						<a href="{{ URL('/merchants/wechat/materialAdvanced') }}">高级图文</a>
					</li>
				</ul>
				<!-- 导航 结束 -->
			</div>
			<!-- 左侧 结算 -->
			<!-- 右边 开始-->
			<div class="pull-right">
				<!-- 搜素框~~或者自己要写的东西 -->
				<!-- <a class="f12 blue_38f" href="##;" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>图文素材简介及使用教程 </a> -->
			</div>
			<!-- 右边 结束 -->
		</div>
		<!-- 导航模块 结束 -->
		<!--新建图文开始-->
		<div class="right_content">
			<div class="app">
				<div class="app_info">由于微信公众平台的接口规范，仅提供向微信认证服务号商户，提供群发接口。如您的公众号同时具有微信支付权限，您还可以在正文内添加超级链接。</div>
				<div class="app_content clearfix">
					<div class="app_left">
						<div class="app_header">
							<h1>高级图文</h1>
						</div>
						<div class="left_content">
							<div class="cover list" data-id="{{ $detail['id'] or 0 }}">
								<div class="cover_img">
									<span>封面图片</span>
									<img src="{{ $detail['cover'] or '' }}" />
								</div>
								<p class="title">{{ $detail['title'] or '标题' }}</p>
								<!-- 用于保存外链 -->
								<a class="cover_href" href="{{ $detail['href'] or '#'}}" style="display: none;">{{ $detail['href_title'] or ''}}</a>
								<div class="opts">
									<span class="editor">编辑</span>
								</div>
							</div>
							@forelse ( $detail['_child'] as $k=>$v )
							<div class="cover_list list" data-id="{{ $v['id'] }}">
								<div class="coverListBody">
									<span class="title">{{ $v['title'] or '标题' }}</span>
									<div class="cover_img">
										<span>缩略图</span>
										<img src="{{ $v['cover'] or '' }}" />
									</div>
								</div>
								<!-- 用于保存外链 -->
								<a class="cover_href" href="{{ $v['href'] }}" style="display: none;">{{ $v['href_title'] }}</a>
								<div class="opts">
									<span class="editor">编辑</span>
									@if($k > 0)
									<span class="delete">删除</span>
									<div class="delete_cap">
										<span>确定删除</span>
										<button class="btn btn-primary">确定</button>
										<button class="btn btn-default">取消</button>
									</div>
									@endif
								</div>
							</div>
							@empty
							<div class="cover_list list" data-id="0">
								<div class="coverListBody">
									<span class="title">标题</span>
									<div class="cover_img">
										<span>缩略图</span>
										<img src="" />
									</div>
								</div>
								<!-- 用于保存外链 -->
								<a class="cover_href" href="javascripr:void(0);" style="display: none;"></a>
								<div class="opts">
									<span class="editor">编辑</span>
								</div>
							</div>
							@endforelse
							<div class="left_bottom">
								<a class="co_38f" href="##;">+新增</a>
							</div>
						</div>
					</div>
					<div class="app_right">
						<form role="form">
							<div class="form-group">
								<span class="red">*</span>
								<label>链接到</label>
								<div class="dropdown" style="position: relative;">
									<a class="outer_link" href="{{ $detail['href'] or '#'}}" target="_blank" @if(isset($detail['href_title']) && !empty($detail['href_title']))style="display: inline-block;"@endif>{{ $detail['href_title'] or ''}}</a>
									@if(isset($detail['href_title']) && !empty($detail['href_title']))
									<a class="co_38f" id="menu1" data-toggle="dropdown">修改</a>
									@else
									<a class="co_38f" id="menu1" data-toggle="dropdown">设置链接到的页面  <span class="caret"></span></a>
									@endif
									<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
										<li class="js_smallPage" role="presentation">
											<a role="menuitem" tabindex="-1" href="##">微页面及分类</a>
										</li>
										<li class="js_product" role="presentation">
											<a role="menuitem" tabindex="-1" href="##">商品及分类</a>
										</li>
										<!-- <li role="presentation">
											<a role="menuitem" tabindex="-1" href="##" data-toggle="modal" data-target="#myModal">营销活动</a>
										</li> -->
										<!-- <li role="presentation">
											<a role="menuitem" tabindex="-1" href="##" data-toggle="modal" data-target="#myModal3">投票调查</a>
										</li> -->
										<li class="js_shop homepage" role="presentation" class="link_to">
											<a role="menuitem" tabindex="-1" href="##">店铺主页</a>
										</li>
										<li class="js_members homepage" role="presentation" class="link_to">
											<a role="menuitem" tabindex="-1" href="##">会员主页</a>
										</li>
										<!-- <li class="js_members homepage" role="presentation" class="custom">
											<a role="menuitem" tabindex="-1" href="##">自定义外链</a>
										</li> -->
									</ul>
								</div>
							</div>
							<div class="form-group">
								<label for="title"><span class="red">*</span>标题</label>
								<input type="text" class="form-control" id="title" value="{{ $detail['title'] or '' }}">
							</div>
							<div class="form-group">
								<span class="red">*</span>
								<label>
									封面
									<span class="co_999">（图片建议尺寸：900*500px, 大小不超过3M）</span>
								</label>
								<div class="js_inline">
									<!-- <img class="img_small" @if ( !isset($detail['cover']) || empty($detail['cover']) )style="display:none; @endif src="{{ $detail['cover'] or '' }}" />
									@if(!empty($detail['cover']))
									<a class="co_38f js_img" href="javascripr:void(0);">重新选择</a>
									@else
									<a class="co_38f js_img" href="javascripr:void(0);">添加图片....</a>
									@endif -->
									<div class="controls">
										<input type="hidden" name="share_img" value="{{ $detail['content_source_url'] or '' }}">
										<div class="share_img_box @if ( !isset($detail['cover']) || empty($detail['cover']) ) hide @endif ">
											<img src="{{ $detail['cover'] or '' }}" style="width: 80px;height: 80px;" class="share_img" >
											<span class="delete">x</span>
										</div>
										<a href="javascript:;" class="@if ( !isset($detail['cover']) || empty($detail['cover']) ) add-goods @else add-goods2 @endif js-add-picture js_img">+添加图片</a>
                                	</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!--新建图文结束-->
		<!--图片弹框开始-->
	    <div class="modal export-modal myModal-adv" id="myModal-adv" onselectstart="return false;" aria-hidden="true" data-backdrop="static">
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
	                                <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
	                            </li>
	                        </ul>
	                    </div>
	                    <div class="modal-body">
	                        <div class="category-list-region">
	                            <ul class="category-list">
	                               
								</ul>
								<div class='add_group'>
									<div class="add_group_list" data-id='1'>+添加分组</div>
									<div class="add_group_box hide">
										<div class='add_group_title'>添加分组</div>
										<input class='add_group_input' placeholder='不超过6个字' type="text" maxlength='6'  style="font-size:14px">
										<div class='clearfix add_group_btn'>
											<div class="btn_left">确定</div>
											<div class="btn_right">取消</div>
										</div>
									</div>
								</div>
	                        </div>
	                        <div class="attachment-list-region">
								<div class="search-region" style="display:none">
									<div class="ui-search-box">
										<input class="txt js-search-input" type="text" placeholder="搜索" value="">
									</div>
								</div>
	                        	<div class="imgData">
	                                <ul class="image-list">
	                                    
	                                </ul>
	                                <div class="attachment-pagination">
	                                   	<div class= "picturePage"></div><!-- 分页 -->
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
				                <a class="co_38f js_prev" href="##;"> <选择图片 </a>
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
                                                    <!-- <p>或将照片拖到这里，单次最多可选300张</p> -->
                                                </div>
                                            </div>
                                            <div class="statusBar" style="display:none;">
                                                <div class="progress">
                                                    <span class="text">0%</span>
                                                    <span class="percentage"></span>
                                                </div><div class="info"></div>
                                                <div class="btns">
                                                    <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
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
	                <div class="modal-content content_third">
	                    <div class="modal-header">
	                        <button type="button" class="close" data-dismiss="modal">
	                            <span aria-hidden="true">&times;</span>
	                            <span class="sr-only">Close</span>
	                        </button>
	                        <ul class="module-nav modal-tab">
	                            <li class="active">
	                                <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
	                                <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
	                            </li>
	                        </ul>
	                        <div class="search-region">
	                            <div class="ui-search-box">
	                                <input class="txt js-search-input" type="text" placeholder="搜索" value="">
	                            </div>
	                        </div>
	                    </div>
	                    <div class="modal-body">
	                        <ul id="iconStyleSelect">
								<li id="style">风格: <a href="##" class="selected">全部</a><a href="##">普通</a><a href="##">简约</a></li>
								<li id="color">颜色: <a href="##" class="selected">全部</a><a href="##">白色</a><a href="##">灰色</a></li>
								<li id="type">类型: <a href="##" class="selected">全部</a><a href="##">常规</a><a href="##">购物</a><a href="##">交通</a><a href="##">食物</a><a href="##">商务</a><a href="##">娱乐</a><a href="##">美妆</a></li>
							</ul>
							<div id="iconImgShow">
								<ul id="iconImgSelect">
									<li>
										<img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bb3503203766425965b7517336df979d.png?imageView2/2/w/160/h/160/q/75/format/png"/>
										<div class="attachment-selected no">
		                                    <i class="icon-ok icon-white"></i>
		                                </div>
									</li>
									
								</ul>
							</div>
							<div id="pageNum">
								共<span>270</span>条，每页27条&nbsp;&nbsp;
							</div>
	                    </div>
	                    <div class="modal-footer clearfix">
	                        <div class="selected-count-region hide">
	                            已选择<span class="js-selected-count">2</span>张图片
	                        </div>
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
   		<!--自定义外链的模态框开始-->
   		<!-- <div class="linkTo_cap">
			<input type="text" placeholder="链接地址：http://example.com" />
			<button class="btn btn-primary">确定</button>
			<button class="btn btn-default">取消</button>
		</div> -->
   		<!--自定义外链的模态框结束-->
   		
		<!--微页面模态框开始-->
		<div class="modal" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true" data-backdrop="static">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
		                        &times;
		                    </button>
		                <ul class="list">
		                    <li class="js_small list_active">微页面</li>
		                    <!-- <li class="js_item">微页面分类</li> -->
		                    <li class="js_manage">
		                        <a class="co_38f" href="javascript:void(0);">分类管理</a>
		                    </li>
		                    <li class="co_000 js_link">
		                        <a class="co_38f" href="javascript:void(0)">新建微页面</a>-
		                        <a class="co_38f" href="javascript:void(0)">草稿管理</a>
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
		                    <tbody class="small">
		                       
		                    </tbody>
		                    
		                </table>
		            </div>
		            <div class="modal-footer clearfix">
		                <div class= "myModal1Page"></div><!-- 分页 -->
		            </div>
		        </div>
		    </div>
		</div>
		<!--微信模态框结束-->
		<!--商品模态框开始-->
		<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true" data-backdrop="static">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
		                        &times;
		                    </button>
		                <ul class="list">
		                    <li class="js_small list_active">已上架商品</li>
		                    <li class="js_item">商品分组</li>
		                    <li class="co_000 js_manage">
		                        <a class="co_38f" target="_blank" href="{{URL('/merchants/product/create')}}">新建分组</a>
		                    </li>
		                    <li class="co_000 js_link">
								<a class="co_38f" target="_blank" href="{{URL('/merchants/product/create')}}">新建商品</a>
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
		                    <tbody class="small">
		                       
		                    </tbody>
		                </table>
		            </div>
		            <div class="modal-footer clearfix">
		                <div class= "myModal2Page"></div><!-- 分页 -->
		            </div>
		        </div>
		    </div>
		</div>

	</div>
	<!--主体右侧内容结束-->
	<!-- 中间 结束 -->
	@endsection @section('other')
	<div class="submit_btn">
		<button class="btn btn-primary">提交</button>
	</div>
	@endsection @section('page_js') @parent
	<!-- webuploader上传插件引入 -->
	<script src="{{config('app.source_url')}}static/js/webuploader.js"></script>
	<script src="{{config('app.source_url')}}mctsource/js/wechat_upload.js"></script>
	<!-- 分页插件 -->
	<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
	<!-- 当前页面js -->
	<script src="{{config('app.source_url')}}mctsource/js/wechat_base.js"></script>
	<script src="{{config('app.source_url')}}mctsource/js/wechat_jblhryde.js"></script>
	<script type="text/javascript">
		//主体左侧列表高度控制
		$('.left_nav').height($('.content').height());
		var domain_url = "{{config('app.source_url')}}";
		var imgUrl = "{{ imgUrl() }}";
	</script>

	@endsection