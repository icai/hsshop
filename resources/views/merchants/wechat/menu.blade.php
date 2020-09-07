@extends('merchants.default._layouts') @section('head_css')
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_37thiagp.css">
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
@include('merchants.wechat.slidebar')
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
    <input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
	<!--主体左侧列表开始-->
	<!--主体左侧列表结束-->
	<!--主体右侧内容开始-->
    <div class="right_container">
   		<!--操作部分开始-->
   		<div class="handle">
   			<div class="handle_title">
   				<div class="tails">
   					<p>自定义菜单</p>
   					<span>由于微信接口延迟，菜单修改后最长可能需要30分钟才会更新。如需即时查看，可先取消关注，再重新关注  </span>
   					<br /><a class="co_38f" href="{{ config('app.url') }}home/index/detail/771/news" target="_blank">自定义菜单设置教程</a>
   				</div>
   				<!-- <div class="btn1">
   					<button class="active"></button>
   				</div> -->
   			</div>
   			<div class="handle_content clearfix">
   				<div class="app_view">
   					<div class="app_inner">
   						<div class="app_list">
   							
   						</div>
   					</div>
   					<div class="app_nav">
   						<span class="menuicon"></span>
                        @if($customMenus['button'])
                        @forelse($customMenus['button'] as $val) 
   						<div class="menu_text">
                          <button>{{ $val['name'] }}</button>
                            @if(!empty($val['sub_button']))
                            <ul>
                                @foreach($val['sub_button'] as $item)
                                <li><a class="co_000" href="javascript:void(0);">{{ $item['name'] }}</a></li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        @empty
                        @endforelse
                        @endif
   					</div>
   				</div>
   				<div class="app_slidebar">
                    @if(empty($customMenus['button']))
   					<div class="set_info">
   						还没有设置任何菜单。
   						<a class="set_first co_38f" href="javascript:void(0);">+添加一级菜单</a>
   					</div>
                    @endif
   					<div class="set_right">
                        @forelse($customMenus['button'] as $items)
                            
       					<div class="menu clearfix" data-id="{{ $items['id'] }}">
							<div class="circle_close pop" data-toggle="delete_pop">x</div>
							<div class="menu_title">
								<div class="aa first">
		   							<div class="h4">一级菜单：</div>
		   						      <ul>
		   								<li class="clearfix zx">
		   									<span class="h5">{{ $items['name'] }}</span>
		   									<span class="opts co_ccc opts_editor">编辑</span>
		   								</li>
		   							</ul>
								</div>
                                <div class="aa second">
                                    <div class="h4">二级菜单：</div>
                                    <ul>
                                        @if(!empty($items['sub_button']))
                                        @foreach($items['sub_button'] as $key=>$item)
                                          <li class="clearfix" data-linkid="{{ $item['id'] }}">
                                              <span class="num">{{ $key+1 }}.</span>
                                              <span class="h5">{{ $item['name'] }}</span>
                                              <span class="opts">
                                                    <span class="co_ccc opts_editor" href="javascript:void(0);">编辑</span>
                                                       -
                                                    <span class="co_ccc opts_delete pop" data-toggle="delete_pop" href="javascript:void(0);">删除</span>
                                              </span>
                                          </li> 
                                        @endforeach
                                        @endif
                                    </ul>
                                </div>
								<a class="set_second co_38f" href="javascript:void(0);">+添加二级菜单</a>
							</div>
                            
							<div class="menu_main">
								<div class="menu_content">
                                    @if(empty($items['sub_button']))
                                        @if(is_array($items['content']))
                                        <div class="link_to" data-id="-1">
                                            @if($items['content']['type'] == 1)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [商品]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 2)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [商品分组]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 3)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [微页面]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 4)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [微页面分类]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 5)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                [店铺主页]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 6)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [会员主页]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 7)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [微信图文]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 8)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [高级图文]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 9)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($items['content']['type'] == 10)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                [外链]<span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($items['content']['type'] == 11)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($items['content']['type'] == 12)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($items['content']['type'] == 13)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($items['content']['type'] == 14)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                [营销活动]<span>{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 15)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($items['content']['type'] == 16)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[秒杀]{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 17)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[微预约]{{ $items['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($items['content']['type'] == 18)
                                            <a class="co_blue" href="{{ $items['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $items['content']['content_title'] }}]</span>
                                            </a>
                                            @endif

                                        </div>
                                        @else
                                        <div class="link_to" data-id="-1">{!! $items['content'] !!}</div>
                                        @endif
                                    
                                    @else
                                    <div class="link_to" data-id="-1">使用二级菜单后主回复已失效</div>
                                    @endif

                                    @if(!empty($items['sub_button']))
                                    @foreach($items['sub_button'] as $key=>$item)
                                        @if(is_array($item['content']))
                                        <div class="link_to no" data-id="{{ $key }}">
                                            @if($item['content']['type'] == 1)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [商品]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 2)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [商品分组]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 3)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [微页面]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 4)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [微页面分类]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 5)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                [店铺主页]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 6)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                [会员主页]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 7)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [微信图文]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 8)
                                            <a class="co_blue" href="" target="_blank" data-linkId="">
                                                [高级图文]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 9)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId=""> 
                                                <span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($item['content']['type'] == 10)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                [外链]<span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($item['content']['type'] == 11)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                           	@elseif($item['content']['type'] == 12)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($item['content']['type'] == 13)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($item['content']['type'] == 14)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                [营销活动]<span>{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 15)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                            @elseif($item['content']['type'] == 16)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[秒杀]{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 17)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[微预约]{{ $item['content']['content_title'] }}</span>
                                            </a>
                                            @elseif($item['content']['type'] == 18)
                                            <a class="co_blue" href="{{ $item['content']['url'] }}" target="_blank" data-linkId="">
                                                <span>[{{ $item['content']['content_title'] }}]</span>
                                            </a>
                                            @endif
                                        </div>
                                        @else
									    <div class="link_to no" data-id="{{ $key }}">{!! $item['content'] !!}</div>
                                       @endif
                                    @endforeach
                                    @endif
								</div>
								<div class="reply_content" @if(!empty($items['sub_button'])) style="display: none;" @endif>  
									<span class="change_text">回复内容：</span>
									<span class="main_link">
										<a class="co_38f ctl_editor" href="javascript:void(0);">一般信息</a>-
										<a class="co_38f js_showModel" href="javascript:void(0);">图文素材</a>-
										<a class="co_38f js_smallPage" href="javascript:void(0);">微页面</a>-
                                        <a class="co_38f js_product" href="javascript:void(0);">商品</a>-
										<a class="co_38f js_xcx" data-type="12" href="javascript:void(0);">小程序</a>-
										<a class="co_38f js_service" data-type="18" href="javascript:void(0);">客服</a>-
									</span>
									<div class="dropdown">
									 	<span id="dropdownMenu1" class="co_38f" data-toggle="dropdown">
									   	 	其他
									    	<span class="caret" style="color: #000;"></span>
										</span>
									  	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                            <li class="js_modal js_active" role="presentation" data-type="14" data-modal="#activeModal" data-http="post"><a role="menuitem" tabindex="-1" href="javascript:void(0);">活动</a></li>
									    	<li class="homepage js_shop" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0);">店铺主页</a></li>
									    	<li class="homepage js_members" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0);">会员主页</a></li>
                                            <li class="homepage shop_cat" data-href="{{config('app.url')}}shop/cart/index/{{ session('wid') }}" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0);">购物车</a></li>
                                            <li class="homepage sign" data-href="{{config('app.url')}}shop/point/sign/{{ session('wid') }}" role="presentation">    <a role="menuitem" tabindex="-1" href="javascript:void(0);">签到</a>
                                            </li>
                                            <li class="js_modal js_seckill" role="presentation" data-type="16" data-modal="#seckillModal"  data-http="get">
                                                <a role="menuitem" tabindex="-1" href="javascript:void(0);">秒杀</a>
                                            </li>
                                            <li class="group" data-href="{{config('app.url')}}shop/grouppurchase/index/{{ session('wid') }}" role="presentation"  data-type="13"><a role="menuitem" tabindex="-1" href="javascript:void(0);">拼团</a></li>
                                            
									    	<li class="custom" data-type="10" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0);" data-href="123.com">自定义外链</a></li>
                                            <li class="js_linkHref community" role="presentation" data-href="{{config('app.url')}}shop/microforum/forum/index/{{ session('wid') }}" data-type="15"><a role="menuitem" tabindex="-1" href="javascript:void(0);">微社区</a></li>
									    </ul>
										<div class="linkTo_cap">
											<input type="text" value="http://" />
											<button class="btn btn-primary">确定</button>
											<button class="btn btn-default">取消</button>
										</div>
									</div>
								</div>
							</div>
						</div>
                        @empty
                        @endforelse
       					<a class="set_first co_38f" href="javascript:void(0);">+添加一级菜单</a>
   					</div>
   				</div>
       		</div>
       		<!--操作部分结束-->
   		</div>
			<div class="btn_group">
				<button class="btn btn-primary">提交修改</button>
			</div>
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
<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <a class="co_38f" href="{{URL('/merchants/product/create')}}" target="_blank">新建商品</a>
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
<!--营销活动弹框开始-->
<div class="modal" id="activeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <ul class="modalNav">
                    <li class="js_switch list_active" data-activity="1" data-requestUrl="{{config('app.url')}}merchants/marketing/egg/index?size=6" data-href="{{config('app.url')}}shop/activity/egg/index/{{ session('wid') }}">砸金蛋</li>
                    <li class="js_switch" data-activity="2" data-requestUrl="{{config('app.url')}}merchants/marketing/wheelList?pagesize=6" data-href="{{config('app.url')}}shop/activity/wheel/{{ session('wid') }}">大转盘</li>
                </ul>
                <ul class="modalNav">
                    <li class="js_newActive"><a class="co_38f" href="{{config('app.url')}}merchants/marketing/egg/add" target="_blank">新建砸金蛋活动</a></li>
                    <li class="js_newActive hide"><a class="co_38f" href="{{config('app.url')}}merchants/marketing/addWheel" target="_blank">新建大转盘活动</a></li>
                </ul>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w_25 tl">
                                <span>标题</span>
                                <a class="co_38f refresh" href="javascript:void(0);">刷新</a>
                            </th>
                            <th class="w_20">开始时间</th>
                            <th class="w_20">结束时间</th>
                            <th class="search w_25">
                                <input type="text" />
                                <button class="btn btn-default">搜</button>
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        
                       <tr class="table_info hide">
                           <td colspan="4">
                               <div class="info">没有任何数据</div>
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
<!--营销活动弹框结束-->
<!--秒杀弹框开始-->
<div class="modal" id="seckillModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <ul class="modalNav">
                    <li class="js_switch list_active" data-requestUrl="{{config('app.url')}}merchants/linkTo/get?type=13" data-href="{{config('app.url')}}shop/seckill/detail/{{ session('wid') }}">秒杀</li>
                </ul>
                <ul class="modalNav">
                    <li class="js_newActive"><a class="co_38f" href="{{config('app.url')}}merchants/marketing/seckill/set" target="_blank">新建秒杀活动</a></li>
                </ul>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w_25 tl">
                                <span>标题</span>
                                <a class="co_38f refresh" href="javascript:void(0);">刷新</a>
                            </th>
                            <th class="w_20">开始时间</th>
                            <th class="w_20">结束时间</th>
                            <th class="search w_25">
                                <input type="text" />
                                <button class="btn btn-default">搜</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                       <tr class="table_info hide">
                           <td colspan="4">
                               <div class="info">没有任何数据</div>
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
<!--秒杀弹框结束-->
<!--富文本编译器开始-->
<div id="editor" name="content" type="text/plain" style="width:300px;height:190px;position:absolute;">
	<div class="ctl_btn">
		<button class="btn btn-primary">保存</button>
		<button class="btn btn-default">关闭</button>
	</div>
</div>
<!--富文本编译器结束-->
<!-- 小程序弹框开始 -->
<div class="xcx_madel cap hide">
    <div class="cap_content">
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label for="lastname" class="col-sm-3 control-label">小程序路径：</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control xcx_http" placeholder="pages/lunar/index" value="pages/index/index ">
                </div>
            </div>
            <div class="form-group">
                <label for="lastname" class="col-sm-3 control-label">小程序appid：</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control xcx_appid">
                </div>
            </div>
        </form>
    </div>
    <div class="cap_footer">
        <button class="btn btn-primary">确定</button>
        <button class="btn btn-default">取消</button>
    </div>
</div>
<!-- 小程序弹框结束 -->
<!-- 提交后显示蒙版 -->
<div class="mask no">
    <div class="loader">
        <div class="loading-3">
            <i></i>
            <i></i>
            <i></i>
            <i></i>
            <i></i>
            <i></i>
            <i></i>
            <i></i>
        </div>
    </div>
    <p>微信菜单生成中</p>
</div>
<!--回复内容操作列表中的模态框结束-->
@endsection
@section('page_js') @parent
<!-- 删除弹框 -->
<div class="popover left delete_pop" role="tooltip">
    <div class="arrow"></div>
    <div class="popover-content">
        <span>你确定要删除吗？</span>
        <button class="btn btn-primary sure_btn">确定</button>
        <button class="btn btn-default cancel_btn">取消</button>
    </div>
</div>
<script type="text/javascript">
    //主体左侧列表高度控制
    //
    var host = "{{config('app.url')}}";
    $('.left_nav').height($('.content').height());
    var domain_url = "{{config('app.source_url')}}";
    var wid = "{{ session('wid') }}";
    var imgUrl = "{{ imgUrl() }}";
</script>
<!--ueditor插件-->
<script type="text/javascript" src="{{config('app.source_url')}}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" src="{{config('app.source_url')}}static/js/UE/UEditor/ueditor.all.js"></script>

<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_37thiagp.js"></script>
@endsection