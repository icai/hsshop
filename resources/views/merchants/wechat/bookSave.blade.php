@extends('merchants.default._layouts') @section('head_css')
<!-- mybase  -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_base.css">
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/bookSave.css">
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
		<!-- 导航模块 开始 -->
		<div class="nav_module clearfix">
			<!-- 左侧 开始 -->
			<div class="pull-left">
				<!-- （tab试导航可以单独领出来用） -->
				<!-- 导航 开始 -->
				<ul class="tab_nav">
					<li class="hover">
						<a href="{{ URL('/merchants/wechat/book') }}">预约管理</a>
					</li>
					
				</ul>
				<!-- 导航 结束 -->
			</div>
			<!-- 左侧 结算 -->
		</div>
		<!-- 导航模块 结束 -->
		<!--main-->
		<div>
			<h2>
				<!--<img src="{{ config('app.source_url') }}mctsource/images/book_Detail.png" alt="" />-->
				<span class="tou">预约系统设置</span></h2>
			<!--触发关键词-->
			<div>
				<label for="">触发关键词</label><input class="inpt keywords" type="text" value="{{ $bookDataInfo['keywords'] or '' }}" /><span class="red">*</span><i>如有多个关键字请用空格隔开</i>
			</div>
			<!--图文消息标题-->
			<div>
				<label for="">图文消息标题</label><input class="inpt title" type="text" value="{{ $bookDataInfo['title'] or '' }}" /><span class="red">*</span>
			</div>
			
			<!--<div class="js-pre-sale-wrap" style="display: block;">
                <div class="control-group form-group">
                    <label class="col-sm-2 control-label">
                       	选择图文封面：
                    </label>
                    <div class="controls">
		                <div class="col-sm-9 uploadImg ">
		                    <div class="setImg rtv" >+
		                        <a href="javascript:void(0);" class="clear_img">×</a>-->
		                        <!--上传成功显示图片-->
		                        <!--<img id="act_img cover_img" class="abt" style="width: 100px; height: 100px;" src="{{ imgUrl() }}{{ $voteData['act_img'] or ''}}">
		                        <input type="hidden" name="shareImg">
		                    </div>
		                </div>
                    </div>       
            	</div>         
            </div>-->
            <!--图文封面-->
             <div class="bs_fengmian">
				<p>图文封面</p>
				<a class="setImg">选择封面</a><span>建议大小(宽720高360)</span>
			</div>
			<div class="bs_fengmianlook rtv">
				<p>图文封面预览</p>
                <!--上传成功显示图片-->
                @if(isset($bookDataInfo['cover_img']) && $bookDataInfo['cover_img'])
                <img id="cover_img" class="abt" style="width: 100px; height: 100px;" src="{{ $bookDataInfo['cover_img'] }}">
                @else
                <img id="cover_img" class="abt" style="width: 100px; height: 100px;">
                @endif
                <input type="hidden" name="shareImg">
			</div>
            <!--预约地址-->
            <div>
				<label for="">预约地址</label><input class="inpt address" type="text" placeholder="请输入接待预约用户的地址" value="{{ $bookDataInfo['address'] or '' }}" /><span class="red"></span>
			</div>
            <!--预约电话-->
            <div>
				<label for="">预约电话</label><input class="inpt phone" type="number" placeholder="请输入接收预约的电话号码" value="{{ $bookDataInfo['phone'] or '' }}" /><span class="red"></span><i>如0571121212或者15487546546</i>
			</div>
            
            <!--<div class="js-pre-sale-wrap" style="display: block;">
                <div class="control-group form-group">
                    <label class="col-sm-2 control-label">
                       	预约顶部图片：
                    </label>
                    <div class="controls">
		                <div class="col-sm-9 uploadImg ">
		                    <div class="setImg rtv" >+
		                        <a href="javascript:void(0);" class="clear_img">×</a>-->
		                        <!--上传成功显示图片-->
		                       <!-- <img id="act_img banner_img" class="abt" style="width: 100px; height: 100px;" src="{{ imgUrl() }}{{ $voteData['act_img'] or ''}}">
		                        <input type="hidden" name="shareImg">
		                    </div>
		                </div>
                    </div>       
            	</div>         
            </div>
            -->
            <!--预约顶部图片-->
            <div class="bs_fengmian">
				<p>预约顶部图片</p>
				<a class="setImgf">选择封面</a><span>建议大小(宽720高360)</span>
			</div>
			<div class="bs_fengmianlookf rtv">
				<p>预约顶部图片</p>
                <!--上传成功显示图片-->
                @if(isset($bookDataInfo['banner_img']) && $bookDataInfo['banner_img'])
                <img id="banner_img" class="abt" style="width: 100px; height: 100px;" src="{{ $bookDataInfo['banner_img'] }}">
                @else
                <img id="banner_img" class="abt" style="width: 100px; height: 100px;">
                @endif
                <input type="hidden" name="shareImg">
			</div>
            
            <!--预约详情-->
            <div class="bs_details">
				<p>预约详情</p>
				<div>
					<textarea id="details" class="details" name="" rows="" cols="">{{ $bookDataInfo['details'] or '' }}</textarea>
				</div>
				
			</div>
			<!--预约接受数量-->
			<div class="bs_num">
				<p class="bs_nums">预约接受数量</p>
				<div style="display: flex;margin-left: 10px;">
					<div style="display: flex;">
						<div class="bs_numsallnormal time @if((isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 0) || empty($bookDataInfo)) bs_numsall @endif">
							<div class="bs_numsallnormaldiv time1 @if((isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 0) || empty($bookDataInfo)) bs_numsalldiv @endif">限定时间</div>
							<p class="bs_numsallnormalp time2 @if((isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 0) || empty($bookDataInfo)) bs_numsallp @endif">
								<span class="bs_numsallnormalspan time3 @if((isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 0) || empty($bookDataInfo)) bs_numsallspan @endif"></span>
							</p>
						</div>
						<div class="bs_numsallnormal liang @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 1) bs_numsall @endif">
							<div class="bs_numsallnormaldiv liang1 @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 1) bs_numsalldiv @endif">限定每日量</div>
							<p class="bs_numsallnormalp liang2 @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 1) bs_numsallp @endif"><span class="bs_numsallnormalspan liang3 @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 1) bs_numsallspan @endif"></span></p>
						</div>
						<div class="bs_numsallnormal zongLiang @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 2) bs_numsall @endif">
							<div class="bs_numsallnormaldiv zongLiang1 @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 2) bs_numsalldiv @endif">限定全部总量</div>
							<p class="bs_numsallnormalp zongLiang2 @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 2) bs_numsallp @endif"><span class="bs_numsallnormalspan zongLiang3 @if(isset($bookDataInfo['limit_type']) && $bookDataInfo['limit_type'] == 2) bs_numsallspan @endif"></span></p>
						</div>
					</div>
					
					<div>
						<div class="timer" style="width: 400px;height: 50px;line-height: 50px;">设定您接受预约的起始和结束时间</div>
						<div class="liangr" style="width: 400px;height: 50px;line-height: 50px; display: none;">设定您每日接收的预约总数</div>
						<div class="zongLiangr" style="width: 400px;height: 50px;line-height: 50px;display: none;">设定您总计可接收的预约总数</div>
					</div>
					
				</div>
				<div>
					<div class="bs_numtime" @if(isset($bookDataInfo['limit_type']) && ($bookDataInfo['limit_type'] == 1 || $bookDataInfo['limit_type'] == 2)) style="display: none;" @endif>
						<div class="bs_numtimest">
							<label for="">选择限定开始时间</label>
				                <input type="text" name="start_at" class="form-control pd_l5 fz_13" id="datetimepicker1" placeholder="开始日期" value="{{ $bookDataInfo['start_time'] or ''}}">      
						</div>
						<div class="bs_numtimest">
							<label for="">选择限定结束时间</label>
				                <input type="text" name="end_at" class="form-control pd_l5 fz_13" id="datetimepicker2" placeholder="结束日期" value="{{ $bookDataInfo['end_time'] or ''}}">
						</div>
					</div>
					<div class="bs_numDailyamount" @if((isset($bookDataInfo['limit_type']) && ($bookDataInfo['limit_type'] == 0 || $bookDataInfo['limit_type'] == 2)) || empty($bookDataInfo)) style="display: none;" @endif>
						<label for="">填写每日接收预约数</label><input class="form-control limit_num" type="text" value="{{ $bookDataInfo['limit_num'] or '' }}" /><i>0表示不限制</i>
					</div>
					
					<div class="bs_numTotalamount" @if((isset($bookDataInfo['limit_type']) && ($bookDataInfo['limit_type'] == 0 || $bookDataInfo['limit_type'] == 1)) || empty($bookDataInfo)) style="display: none;" @endif>
						<label for="">填写最大接收预约数</label><input class="form-control limit_total" type="text" value="{{ $bookDataInfo['limit_total'] or '' }}" /><i>0表示不限制</i>
					</div>
				</div>
			</div>
				
			<!--预约内容设置-->
			<div class="bs_content">
				<p>预约内容设置</p>
				<div style="margin-left: 10px;">填写你要收集的内容！预约默认选项不可以修改删除！</div>
				<ul style="display: flex;height: 50px;border-bottom:1px solid #ccc ;align-items: center;background: #f7f7f7;">
					<li>字段类型</li>
					<li>字段名称</li>
					<li>初始内容</li>
					<li>操作</li>
				</ul>
				@if(isset($bookDataInfo['content']) && $bookDataInfo['content'])
				@foreach($bookDataInfo['content'] as $fval)
				<ul @if($fval['addType'] == 'other') class="@if($fval['itype']=='select') dropbox @else box @endif" @endif>
					<li>@if($fval['itype'] == 'text')单行文字：@else 下拉框：@endif</li>
					<li><input type="text" value="{{ $fval['ikey'] }}" class="{{ $fval['iclass'] or ''}}" /></li>
					<li>
						<input type="text" placeholder="{{ $fval['ival'] }}" value="{{ $fval['ival'] }}" />
					</li>
					@if($fval['addType'] == 'content')
					<li><input type="checkbox" checked="checked" class="{{ $fval['shopClass'] or ''}}"/>是否显示</li>
					@endif
				</ul>
				@endforeach
				@else
				<div>
				<ul>
					<li>单行文字：</li>
					<li><input class="name" type="text" value="联系人"/></li>
					<li><input class="name_after" type="text" placeholder="{{ $bookDataInfo['name'] or '请输入您的名字' }}"/></li>
					<li><input class="finput" type="checkbox" checked="checked"/>是否显示</li>
				</ul>
				<ul>
					<li>单行文字：</li>
					<li><input class="phones" type="text" value="联系电话"/></li>
					<li><input class="phones_after" type="text" placeholder="请输入您的电话"/></li>
					<li><input class="binput" type="checkbox" checked="checked"/>是否显示</li>
				</ul>
				<ul>
					<li>日期选择：</li>
					<li><input class="book_date" type="text" value="预约日期" disabled="disabled"/></li>
					<li>
						<input type="text" id="start_times" value="" class="" placeholder="请输入预约日期"/>
						</li>
					<li><input class="sinput" type="checkbox" checked="checked"/>是否显示</li>
				</ul>
				<ul>
					<li>时间选择：</li>
					<li><input class="book_time" type="text" value="预约时间" disabled="disabled"/></li>
					<li>
						<input type="text" id="start_time" value="" placeholder="请输入预约时间"/>
					</li>
					<li><input class="input4" type="checkbox" checked="checked"/>是否显示</li>
				</ul>
				</div>
				@endif
				<div class="caseBox">
					<ul class="box">
						<li>输入要增加的内容：</li>
						<li><input class="box_add1" type="text"/></li>
						<li><input class="box_add2" type="text"/></li>
						<li><span class="add">添加</span></li>
					</ul>
				</div>
				<div class="caseDropBox">
					<ul class="dropbox">
						<li>下拉框：</li>
						<li><input class="dropbox_addf" type="text" /></li>
						<li><input class="dropbox_adds" type="text" placeholder="每个选项之间以“，”分割" /></li>
						<li><span class="addbox">添加</span></li>
					</ul>
				</div>	
			</div>
			
			<input type="submit" class="book_btn" value="提交">
			
			
            
		</div>
		
        	
		
	</div>
	<!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
@endsection @section('other')
<!-- 删除弹框 -->
<div class="popover left delete_pop" role="tooltip">
	<div class="arrow"></div>
	<div class="popover-content">
		<span>你确定要删除吗？</span>
		<button class="btn btn-primary sure_btn">确定</button>
		<button class="btn btn-default cancel_btn">取消</button>
	</div>
</div>
@endsection @section('page_js') @parent
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>

<script type="text/javascript">
	var start_at = "{{ isset($bookDataInfo['start_time']) ? $bookDataInfo['start_time']== 0 ? '' : $bookDataInfo['start_time'] : '' }}";
	var end_at = "{{ isset($bookDataInfo['end_time']) ? $bookDataInfo['end_time']== 0 ? '' : $bookDataInfo['end_time'] : '' }}";
	var bs_contentstart_at = "{{ $detail['bs_contentstart_at']?? '' }}";
	var bs_contentend_at = "{{ $detail['bs_contentend_at']?? '' }}";
	var details = '{!! isset($bookDataInfo["details"])? $bookDataInfo["details"]: ""!!}'
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript">
	var _host = "{{ imgUrl() }}";
</script>
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!--时间插件引入的JS文件-->
<script src="{{ config('app.source_url') }}static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>

<!-- 富文本编译器 -->
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>

<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/bookSave.js"></script>
<script type="text/javascript">
	//主体左侧列表高度控制
	$('.left_nav').height($('.content').height());
</script>
@endsection
