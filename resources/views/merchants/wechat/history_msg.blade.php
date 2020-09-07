@extends('merchants.default._layouts') @section('head_css')
<!-- 时间插件样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- webuploader上传插件引入 -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css"> 
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_3df1cp4t.css">
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
   		<div class="his_news">
   			<div class="news_info">
   				<span class="title">历史消息</span>
   				<p>由于微信历史消息仅支持通过微信公众平台，一次性向全部粉丝进行群发时，才可以收录至微信的历史消息页面。<br />
				因此，会搜云向您提供了，您通过会搜云向粉丝群发消息后，将您的群发内容收录至历史消息页面，提供粉丝查看的作用</p>
				<a class="co_38f" href="javascript:void(0);" target="_blank">如何查看会搜云群发的历史消息</a>
   			</div>
   			<div class="his_operate">
   				<a class="co_38f pop" data-toggle="link_cap" href="javascript:void(0);">链接</a>
   				<div class="wx_code">
       				<a class="co_38f code" href="javascript:void(0);"style="margin-bottom: 20px;display: block;">二维码</a>
       				<div class="code_cap">
       					<p>微信扫一扫访问：</p>
       					<img src="" />
       					<a class="co_38f" href="" download>下载二维码</a>
       				</div>
   				</div>
   			</div>
   		</div>
   		<div class="dropdown">
		  	<p id="dropdownMenu1" data-toggle="dropdown">
		   	 	添加内容
		    	<span class="caret"></span>
		 	</p>
		  	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		    	<li class="his_img" role="presentation" data-toggle="modal" data-target="#myModal-adv"><a role="menuitem" tabindex="-1" href="javascript:void(0)">图片</a></li>
		    	<li class="hie_news" role="presentation" data-toggle="modal" data-target="#myModal"><a role="menuitem" tabindex="-1" href="javascript:void(0)">图文</a></li>
		  	</ul>
		</div>
   		<div class="no_result">
   			还没有相关数据
   		</div>
   		<!--数据列表开始-->
			<div class="data_list">
   			<table class="table">
				<thead>
					<tr>
						<th class="title">标题</th>
						<th class="set_time">显示时间</th>
						<th class="send">发送</th>
						<th class="delivery">送达</th>
						<th class="options">操作</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="title">
							<div class="item">
								<div class="img_text">
									<span class="green">图文</span>
									<a class="co_blue" href="javascript:void(0);">123456</a>
								</div>
								<div class="read_all clearfix">
									<a class="jump" href="javascript:void(0);">
                                        <span>阅读全文</span>
                                        <span class="pull-right">></span>
                                    </a>
								</div>
                                <div class="read_all clearfix">
                                    <span class="green">图文</span>
                                    <a class="co_blue" href="javascript:void(0);">123456</a>
                                </div>
							</div>
						</td>
						<td class="set_time">2016-12-07 11:51:46</td>
						<td class="send">0</td>
						<td class="delivery">0</td>
						<td class="options">
							<div class="opts">
								<a class="co_38f time" href="javascript:void(0);">修改时间</a>-
								<a class="co_38f pop" data-toggle="del_popover" href="javascript:void(0);">删除</a>
								<div class="time_cap">
                                    <input class='datetimepicker' type="text" />
                                    <button class="btn btn-primary">确定</button>
                                    <button class="btn btn-default">取消</button>
                                </div> 
							</div>
							<div class="btn1">
               					<button></button>
               				</div>
						</td>
					</tr>
					<tr>
						<td class="title">
							<img src=""/>
						</td>
						<td class="set_time">2016-12-07 11:51:46</td>
						<td class="send">0</td>
						<td class="delivery">0</td>
						<td class="options">
							<div class="opts">
								<a class="co_38f time" href="javascript:void(0);">修改时间</a>-
								<a class="co_38f pop" data-toggle="del_popover" href="javascript:void(0);">删除</a>
								<div class="time_cap">
                                    <input class='datetimepicker' type="text" />
                                    <button class="btn btn-primary">确定</button>
                                    <button class="btn btn-default">取消</button>
                                </div>
							</div>
							<div class="btn1">
               					<button></button>
               				</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="page clearfix">
				<span class="pull-right">共2条，每页 20 条</span>
			</div>
			</div>
		<!--数据列表结束-->
    </div>
    <!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->

<!-- 右侧 开始 -->

<!-- 右侧 结束 -->
@endsection @section('other')
<!-- 删除弹框 -->
<div class="popover left del_popover" role="tooltip" style="z-index: 999;">
	<div class="arrow"></div>
	<div class="popover-content">
    	<span>你确定要删除吗？</span>
        <button class="btn btn-primary sure_btn">确定</button>
        <button class="btn btn-default cancel_btn">取消</button>
    </div>
</div>
<!-- 删除弹框结束 -->
<!-- 链接弹框开始 -->
<div class="popover left link_cap" role="tooltip" style="z-index: 999;">
	<div class="arrow"></div>
	<div class="popover-content">
    	<input type="text" value="" disabled/>
		<button class="btn btn-default">复制</button>
    </div>
</div>
<!-- 链接弹框结束 -->
<!--图片弹框开始-->
<div class="modal export-modal myModal-adv" id="myModal-adv" onselectstart="return false;">
    <div class="modal-dialog" id="modal-dialog-adv">
        <form class="form-horizontal">
            <div class="modal-content content_first">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li>
                            <a href="#js-module-goods" class="js_prev co_38f">用过的图片</a>
                            <a href="#js-module-goods" class="js_newImg">新图片</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <div class="body">
                        <div class="opts_info">
                            <div class="info">
                                点击图片即可选中 <a class="co_38f" href="javascript:void(0);">刷新</a>
                            </div>
                            <div class="search">
                                <input type="text" name="" id="" placeholder="搜索" />
                                <button class="btn btn-default">搜</button>
                            </div>
                        </div>
                        <div class="cap_body clearfix">
                            <ul class="img_list clearfix">
                                <li>
                                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FuNjiSwx-AI6khTvfREzav9o_uRk.jpg!100x100.jpg"/>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="cap_footer clearfix">
                        <div class="pull-right">
                            <span>共 64 条，每页 27 条</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-content content_second">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li>
                            <a href="#js-module-goods" class="js_prev">用过的图片</a>
                             <a href="#js-module-goods" class="js_newImg co_38f">新图片</a>
                        </li>
                    </ul>
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
                                        <a class="jump" href="javascript:void(0);">
                                            <span>阅读全文</span>
                                            <span class="pull-right">></span>
                                        </a>
                                    </div>
                                    <div class="read_all clearfix">
                                        <span class="green">图文</span>
                                        <a class="co_blue" href="javascript:void(0);">123456</a>
                                    </div>
								</div>
							</td>
							<td class="news_time">2016-10-11 15:25:38</td>
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
@endsection @section('page_js') @parent
 <!--bootstrap-datatimepicker 时间插件-->
<script src="{{config('app.source_url')}}static/js/moment/moment.min.js"></script>
<script src="{{config('app.source_url')}}static/js/moment/locales.min.js"></script>
<script src="{{config('app.source_url')}}static/js/bootstrap-datetimepicker.min.js"></script>
<!-- webuploader上传插件引入 -->
<script src="{{config('app.source_url')}}static/js/webuploader.js"></script>
<script src="{{config('app.source_url')}}mctsource/js/wechat_upload.js"></script>
<!-- 微信模块基础样式 -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_3df1cp4t.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
</script>

@endsection