@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/messageTemplate_20180123.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/marketing') }}">营销工具</a>
            </li>
            <li>
                <a href="javascript:void(0)">消息推送</a>
            </li>
            <li>
                <a href="javascript:void(0)">群发</a>
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
@endsection
@section('content')
<div class="content">
    <!-- 导航模块 开始 -->
    <div class="nav_module clearfix pr">
        <div class="pull-left">
            <!-- 导航 开始 -->
            <ul class="tab_nav">
                <li class="">
                    <a href="{{url('/merchants/marketing/messagesPush')}}">模板消息</a>
                </li>
                <li class="">
                    <a href="{{url('/merchants/notification/settingListView/')}}">通知消息</a>
                </li>           
            </ul>            
        </div>
        <div class="pull-right common-helps-entry">
            {{--<a class="nav_module_blank" href="/home/index/detail?id=342" target="_blank"><span class="help-icon">?</span>查看【消息提醒】使用教程</a>--}}
        </div>
    </div>
    <!-- 开始 -->
    <div class="contentTxt">
        <h3 style="font-size: 16px;font-weight: bold;line-height: 30px;">消息群发</h3>
        <p style="margin-top: 0;color: #666;">消息群发：消息群发功能可以通过微信公众平台设置好固定的消息模板，在后台一键发送到公众号下的所有粉丝，高效及时的消息提醒。</p>
    </div>
    <div class="message-waring">
        您已绑定微信公众号，请确保微信公众号已申请开通模版消息。
        <a target="_blank" href="https://www.huisou.cn/home/index/detail/772/help">如何开通？</a>
    </div>    
    <!--数据展示部分-->
    <div class="showData">
    	<!--有消息提醒时显示-->
    	<div class="hasData hasNewsData">
    		<a class="btn btn-primary buildXCXNew" href="javascript:void(0);">新建小程序服务通知</a>
    		<a class="btn btn-success buildGZHNew" href="javascript:void(0);">新建公众号服务通知</a>
            <a class="btn sentdHistory" href="/merchants/message/index?type=2">发送历史</a>
    		@if($type == 1 && $list)
    		<table border="" cellspacing="" cellpadding="">
    			<tr>
    				<th>名称</th>
    				<th>类型</th>
    				<th>服务通知</th>
    				<th>创建时间</th>
    				<th>操作</th>
    			</tr>
                @foreach($list as $val)
    			<tr class="data_content">
    				<td>{{ $val['template_name'] }}</td>
                    @if($val['resource'] ==0 && $val['type'] == 0)
    				<td>预约活动开始提醒模板</td>
                    @elseif($val['resource'] ==0 && $val['type'] == 1)
                    <td>产品降价提醒模板</td>
                    @elseif($val['resource'] ==0 && $val['type'] == 2)
                    <td>签到提醒模板</td>
					@elseif($val['resource'] ==0 && $val['type'] == 3)
						<td>卡券过期提醒模板</td>
					@elseif($val['resource'] ==0 && $val['type'] == 4)
						<td>商品预售提醒模板</td>
					@elseif($val['resource'] ==0 && $val['type'] == 5)
						<td>服务到期提醒模板</td>
                    @elseif($val['resource'] ==1 && $val['type'] == 0)
                    <td>通用通知提醒模板</td>
                    @elseif($val['resource'] ==1 && $val['type'] == 1)
                    <td>课程通知提醒模板</td>
					@elseif($val['resource'] ==1 && $val['type'] == 2)
						<td>预约商品开售提醒模板</td>
					@elseif($val['resource'] ==1 && $val['type'] == 3)
						<td>预约服务到期提醒模板</td>
                    @else
                    <td>未知模板</td>
                    @endif
                    @if($val['resource'] ==0)
                    <td>小程序</td>
                    @else
                    <td>公众号</td>
                    @endif
    				<td>{{ $val['created_at'] }}</td>
    				<td>
    					<a class="tempDelBtn" data-id="{{ $val['id'] }}" href="##">删除</a>
                        @if($val['resource'] == 0)
	    				<a href="/merchants/message/save?id={{ $val['id'] }}&type={{ $val['type'] }}">编辑</a>
                        @else
                        <a href="/merchants/message/create?id={{ $val['id'] }}&type={{ $val['type'] }}">编辑</a>
                        @endif
	    				<a class="send" href="##" data-id="{{ $val['id'] }}" data-type="{{ $val['resource'] }}">发送</a>
    				</td>
    			</tr>
                @endforeach
    		</table>
    		@endif
            @if($type == 1 && $list)
            <div>{!! $indexPage !!}</div>
            @endif
    	</div>
    	<!--历史消息-->
        
    	<div class="hasData historyNews">
            @if($type == 2 && $recordList)
    		<table border="" cellspacing="" cellpadding="">
    			<tr>
    				<th>名称</th>
    				<th>类型</th>
    				<th>服务通知</th>
    				<th>发送时间</th>
    				<th>发送人数</th>
    				<th>操作</th>
    			</tr>
                @foreach($recordList as $value)
    			<tr class="record_data_content">
    				<td>{{ $value['template_name'] }}</td>
					@if($value['source'] ==0 && $value['type'] == 0)
						<td>预约活动开始提醒模板</td>
					@elseif($value['source'] ==0 && $value['type'] == 1)
						<td>产品降价提醒模板</td>
					@elseif($value['source'] ==0 && $value['type'] == 2)
						<td>签到提醒模板</td>
					@elseif($value['source'] ==0 && $value['type'] == 3)
						<td>卡券过期提醒模板</td>
					@elseif($value['source'] ==0 && $value['type'] == 4)
						<td>商品预售提醒模板</td>
					@elseif($value['source'] ==0 && $value['type'] == 5)
						<td>服务到期提醒模板</td>
					@elseif($value['source'] ==1 && $value['type'] == 0)
						<td>通用通知提醒模板</td>
					@elseif($value['source'] ==1 && $value['type'] == 1)
						<td>课程通知提醒模板</td>
					@elseif($value['source'] ==1 && $value['type'] == 2)
						<td>预约商品开售提醒模板</td>
					@elseif($value['source'] ==1 && $value['type'] == 3)
						<td>预约服务到期提醒模板</td>
					@else
						<td>未知模板</td>
					@endif
                    @if($value['source'] == 0)
                    <td>小程序</td>
                    @else
                    <td>公众号</td>
                    @endif
    				<td>{{ $value['created_at'] }}</td>
    				<td>{{ $value['send_count'] }}</td>
    				<td>
    					<a class="recordDelBtn" data-id="{{ $value['id'] }}" href="##">删除</a>
    					@if($value['source'] == 0)
	    				<a href="/merchants/message/save?id={{ $value['message_template_id'] }}&type={{ $value['type'] }}&ro=1">查看</a>
	    				@else
	    				<a href="/merchants/message/create?id={{ $value['message_template_id'] }}&type={{ $value['type'] }}&ro=1">查看</a>
	    				@endif
    				</td>
    			</tr>
                @endforeach
    		</table>
            @endif
            @if($type == 2 && $recordList)
            <div>{!! $recordPage !!}</div>
            @endif
    	</div>
        
    	<!--无消息是显示-->
        @if(empty($list) && empty($recordList))
    	<div class="noData">
    		<p>您还没有服务消息模板，快去新建一个吧！</p>
    	</div>
        @endif
    </div>
    <!--模版分类弹框-->
    <div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	  	<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="exampleModalLabel">请选择消息模版类型</h4>
	      		</div>
	      		<div class="modal-body">
	      			<!--小程序-->
	        		<ul class="xcx">
	        			<li>
	        				<img src="{{ config('app.source_url') }}mctsource/images/styleBG.png"/>
	        				<span class="styleTitle">预约活动开始提醒</span>
	        				<div class="style_hint">
	        					<p>可以给用户发送预约活动开始提醒的模版消息。</p>
								<div class="button" data-type="0">立即选择</div>
	        				</div>
	        			</li>
	        			<li>
	        				<img src="{{ config('app.source_url') }}mctsource/images/styleBG.png"/>
	        				<span class="styleTitle">产品降价提醒</span>
	        				<div class="style_hint">
	        					<p>可以给用户发送产品降价提醒的模版消息。</p>
								<div class="button" data-type="1">立即选择</div>
	        				</div>
	        			</li>
	        			<li>
	        				<img src="{{ config('app.source_url') }}mctsource/images/styleBG.png"/>
	        				<span class="styleTitle">签到提醒</span>
	        				<div class="style_hint">
	        					<p>可以给用户发送签到提醒的模版消息。</p>
								<div class="button" data-type="2">立即选择</div>
	        				</div>
	        			</li>
						<li>
							<img src="{{ config('app.source_url') }}mctsource/images/styleBG.png"/>
							<span class="styleTitle">卡券到期提醒</span>
							<div class="style_hint">
								<p>可以给用户发送优惠券/会员卡等卡券到期提醒消息。</p>
								<div class="button" data-type="3">立即选择</div>
							</div>
						</li>
						<li>
							<img src="{{ config('app.source_url') }}mctsource/images/styleBG.png"/>
							<span class="styleTitle">预售商品开售通知</span>
							<div class="style_hint">
								<p>可以给用户送预售商品开售的消息提醒。</p>
								<div class="button" data-type="4">立即选择</div>
							</div>
						</li>
						<li>
							<img src="{{ config('app.source_url') }}mctsource/images/styleBG.png"/>
							<span class="styleTitle">服务过期提醒</span>
							<div class="style_hint">
								<p>可以给用户发送服务过期的消息提醒。</p>
								<div class="button" data-type="5">立即选择</div>
							</div>
						</li>
	        		</ul>
	        		<!--公众号-->
	        		<ul class="gzh">
	        			<li data-type="0">
	        				<p>通用通知</p>
	        				<p>点击设置</p>
	        			</li>
	        			<li data-type="1">
	        				<p>课程通知</p>
	        				<p>点击设置</p>
	        			</li>
	        		</ul>
	      		</div>
	    	</div>
	  	</div>
	</div>
    <!--结束 -->
</div>
@endsection
@section('page_js')
<script type="text/javascript">
	var type = "{{ $type }}";
</script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/messageTemplate_20180123.js"></script>
@endsection
