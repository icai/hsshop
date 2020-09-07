@extends('merchants.default._layouts') @section('head_css')
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_y7guakzj.css"> @endsection @section('middle_header')
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
			<!-- 左侧 开始 -->
            <div class="pull-left">
            <!-- （tab试导航可以单独领出来用） -->
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li>
                        <a href="{{URL('/merchants/wechat/replySet')}}">关键词自动回复</a>
                    </li>
                    <li>
                        <a href="{{URL('/merchants/wechat/subscribeReply')}}">关注后自动回复</a>
                    </li>
                    <li>
                        <a href="{{URL('/merchants/wechat/messages')}}">消息托管</a>
                    </li>
                    <li>
                        <a href="{{URL('/merchants/wechat/messagesTips')}}">小尾巴</a>
                    </li>
                    <li class="hover">
                        <a href="{{URL('/merchants/wechat/weeklyReply')}}">每周回复</a>
                    </li>
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <!-- 搜素框~~或者自己要写的东西 -->
                <a class="f12 blue_38f" href="javascript:void(0);" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>自动回复使用教程 </a>
            </div>
            <!-- 右边 结束 -->
		</div>
		<!--操作部分开始-->
   		<div class="handle">
   			<div class="handle_title">
   				<button class="btn btn-success">新建自动回复</button>
   				<!--弹框开始-->
   				<div class="new_cap">
   					<input type="text" name="" id="" value="为命名规则" />
   					<button class="btn btn-primary">确定</button>
   					<button class="btn btn-default">取消</button>
   				</div>
   				<!--弹框结束-->
   				<!--搜索开始-->
           		<div class="search">
           			<input type="text" name="" id="" placeholder="搜索" />
           		</div>
           		<!--搜索结束-->
   			</div>
            @forelse ( $list as $v )
   			<div class="handle_content">
   				<div class="rule_meta">
   				<h5><span class="num">1)</span><span class="name">{{ $v['name'] }}</span>
   						<div class="rule_opts">
       						<a class="rule_edit" href="javascript:void(0)">
       							编辑
       						</a>
       						<span>-</span>
       						<a class="rule_delete" href="javascript:void(0)" data-id="{{ $v['id'] }}">删除</a>
       					</div>
           				<div class="new_cap">
           					<input type="text" name="" id="" value="{{ $v['name'] }}" />
           					<button class="btn btn-primary" data-id="{{ $v['id'] }}">确定</button>
           					<button class="btn btn-default">取消</button>
           				</div>
   					</h5>
   				</div>
   				<div class="rule_body clearfix">
   					<div class="line"></div>
   					<div class="rule_left">
   						<div class="rule_keywords">
   							关键词：
   						</div>
   						<div class="keywords_list">
   							@forelse ( $v['weixinReplyKeyword'] as $val )
                            <div class="keywords">
                                <a class="close_circle" href="javascript:void(0)" data-id="{{ $val['id'] }}">x</a>
                                <div class="words">
                                	<span class="value">{{ $val['keyword'] }}</span><span class="add">{{ $val['type'] == 0 ? '全匹配' : '模糊' }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="left_info">
                                还没有任何关键字！
                            </div>
                            @endforelse
   						</div>
   						<div class="rule_add_keywords">
   							<a class="co_38f js_ad" href="javascript:void(0)" data-rule_id="{{ $v['id'] }}">
   								+添加关键词
   							</a>
   						</div>
   					</div>
   					<div class="rule_right">
   						<div class="rule_reply">
   							自动回复：<span>按周期发送</span>
   						</div>
   						<ol class="reply_list">
   							@forelse ( $v['weixinReplyContent'] as $value )
                            <li class="reply">
                                @if ( $value['type'] == '2' )
                                <img class="images" src="{{ $value['config']['show_sub'] }}">
                                @else
                                <span class="reply_type">{{ $value['config']['show_title'] }}</span>
                                <span class="reply_text">{{ $value['config']['show_sub'] }}</span>
                                @endif
                                <div class="reply_opts">
                                    <a class="replay_edit" href="javascript:void(0)">编辑</a>
                                    <span>-</span>
                                    <a class="replay_delete" href="javascript:void(0)">删除</a>
                                </div>
                            </li>
                            @empty
                            <div class="right_info">
                                还没有任何回复！
                            </div>
                            @endforelse
   						</ol>
   					</div>
   				</div>
   			</div>
            @empty
            <!-- 内容结束 -->
            <div class="no_result">
                还没有每周回复，请点击新建。
            </div>
            @endforelse
            <div class="page">{{ $pageHtml }}</div>
   		</div>
   		<!--操作部分结束-->
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
<!--选择图文弹框开始-->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                <ul class="list">
                    <li class="js_small list_active">高级图文</li>
                    <li class="js_item">微信图文</li>
                    <li class="js_manage">
                        <a class="co_38f" href="javascript:void(0);">微信图文素材管理</a>
                    </li>
                    <li class="co_000 js_link">
                        <a class="co_38f" href="javascript:void(0)">高级图文素材管理</a>
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
                       <tr class="table_info">
                           <td colspan="3">
                               <div class="info"></div>
                           </td>
                       </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer clearfix">
                <div class= "myModalPage"></div><!-- 分页 -->
            </div>
        </div>
    </div>
</div>
<!--选择图文弹框结束-->
<!--微页面模态框开始-->
<div class="modal" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                <ul class="list">
                    <li class="js_small list_active">微页面</li>
                    <li class="js_item">微页面分类</li>
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
<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                <ul class="list">
                    <li class="js_small list_active">已上架商品</li>
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
<!--商品模态框结束-->
@endsection @section('page_js') @parent
<!-- 关键词模态框 -->
<div class="rule_add_cap" style="display: none">
    <div class="cap_keywords">
        <label class="control_label"><em>*</em>关键词:</label>
        <input type="text" name="" id="saytext" placeholder="关键词最多支持十五字" maxlength="15" />
        <span class="emotion"></span>
    </div>
    <div class="cap_rule">
        <label class="control_label"><em>*</em>规则:</label>
        <label ><input type="radio" name="optionsRadios" value="0" checked/>全匹配</label>
        <label ><input type="radio" name="optionsRadios" value="1"/>模糊</label>
    </div>
    <div class="btn_group">
        <button class="btn btn-primary">确定</button>
        <button class="btn btn-default">取消</button>
    </div>
</div>
<!--jq扩充包用于qq表情-->
<script src="{{config('app.source_url')}}static/js/jquery-browser.js"></script>
<!--qq表情包插件-->
<script src="{{config('app.source_url')}}static/js/jquery.qqFace.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
	var domain_url = "{{config('app.source_url')}}";
</script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_y7guakzj.js"></script>
@endsection