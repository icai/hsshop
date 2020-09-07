@extends('merchants.default._layouts') @section('head_css')
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_q3ut19ys.css"> @endsection @section('middle_header')
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
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix">
            <!-- 左侧 开始 -->
            <div class="pull-left">
            <!-- （tab试导航可以单独领出来用） -->
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li class="hover">
                        <a href="javascript:void(0);">所有快捷短语</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
        </div>
   		<!--操作部分开始-->
   		<div class="handle">
            <!-- 列表过滤部分 开始 -->
            <div class="widget-list-filter clearfix">
                <!-- 左边 开始 -->
                <div class="pull-left">
                    <button class="btn btn-success" data-toggle="modal" data-target="#myModal">新建快捷短语</button>
                </div>
                <!-- 左边 结束 -->
                <!-- 右边 开始 -->
                <div class="pull-right search_module">
                    <!-- 搜索 开始 -->
                    <label class="search_items">
                        <input class="search_input" type="text" name="" value="" placeholder="搜索"/>   
                    </label>
                    <!-- 搜索 结束 -->
                </div>
                <!-- 右边 结束 -->
            </div>
            <!-- 列表过滤部分 结束 -->
   			<!-- 数据提示开始 -->
   			<div class="no_result">
   				还没有相关数据
   			</div>
            <!-- 数据提示结束 -->
            <!-- 数据开始 -->
   			<div class="widget_list">
   				<table class="table">
					<thead>
						<tr>
							<th class="w30">快捷短语内容</th>
							<th class="w10">添加人</th>
							<th class="w10">编辑时间</th>
							<th class="w10 text_right">操作(序号)</th>
						</tr>
					</thead>
					<tbody>
                        <tr>
                            <td class="ctt">测试</td>
                            <td></td>
                            <td>2017-01-03 15:11:14</td>
                            <td class="text_right">
                                <div class="operate">
                                    <a class="operate_edit co_38f" href="javascript:void(0);" data-toggle="modal1" data-target="#myModal1"">编辑</a><span>-</span><a class="pop co_38f" data-toggle="del_popover" href="javascript:void(0);">删除</a>
                                </div>
                                <div class="num">
                                    序号：<span class="co_38f">0</span>
                                </div>
                            </td>
                        </tr>
					</tbody>
				</table>
				<div class="page_footer">
					<span>共 1 条，每页 20 条</span>
				</div>
   			</div>
            <!-- 数据结束 -->
   		</div>
   		<!--操作部分结束-->
        <!-- 模态框（Modal） -->
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">新建快捷短语</h4>
                    </div>
                    <div class="modal-body">
                        <div class="cap_content">
                            <textarea id="ctl_emotion" maxlength="600"></textarea>
                            <div class="cap_footer clearfix">
                                <div class="cap_tool pull-left">
                                    <span class="emotion glyphicon glyphicon-heart-empty">表情</span>
                                    <span class="link_span glyphicon glyphicon-link">超链接</span>
                                    <div class="link">
                                        <input type="text" placeholder="http://"/>
                                        <button class="btn btn-primary">确定</button>
                                    </div>
                                </div>
                                <p class="cap_info pull-right">大约还可输入600字</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">保存</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
   	</div>
   	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->

@endsection @section('page_js') @parent
<!-- 删除弹框 -->
<div class="popover del_popover left" role="tooltip" style="z-index: 999">
    <div class="arrow"></div>
    <div class="popover-content">
        <span>你确定要删除吗？</span>
        <button class="btn btn-primary sure_btn">确定</button>
        <button class="btn btn-default cancel_btn">取消</button>
    </div>
</div>
<!--jq扩充包用于qq表情-->
<script src="{{config('app.source_url')}}static/js/jquery-browser.js"></script>
<!--qq表情包插件-->
<script src="{{config('app.source_url')}}static/js/jquery.qqFace.js"></script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
	var domain_url = "{{config('app.source_url')}}";
</script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_q3ut19ys.js"></script>
@endsection