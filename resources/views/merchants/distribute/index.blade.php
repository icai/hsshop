@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/distribute_hlyx482r.css" /> 
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />

	<!-- 自定义layer皮肤css -->
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')

<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 --> 
        <ul class="common_nav"> 
            <li class="hover">  
                <a href="{{ URL('/merchants/distribute') }}">一键配置</a>
            </li>  
            <li> 
                <a href="{{ URL('/merchants/distribute/template') }}">分销摸板</a>
            </li>  
			<li> 
                <a href="{{ URL('merchants/distribute/applyList') }}">申请页面</a>
            </li>  
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content">
     <div class="switch bg-gray">
        <strong>智慧分销3.0</strong>
        <p>商家可以在这里灵活设置分销场景,当点击【启用】后，所有新增(已设定)的商品，都会执行默认(已设定)的分销摸板规则。</p>
        <p><a href="https://www.huisou.cn/home/index/detail/728/help" target="_blank" class="t-blue">查看智慧分销3.0介绍及设置教程</a></p>
        <!-- 总开关 -->
        <div class="switch-wrap switch-total">
            <label class="ui-switcher @if($shop['is_distribute']==1) ui-switcher-on @else ui-switcher-off @endif" data-is-open="{{$shop['is_distribute']}}"></label>
        </div> 
    </div>
	<div class="switch bg-gray">
		<strong>分销用户自动提现 </strong>
		<p>卖家首先在微信支付商户平台打开【产品中心】>【我的产品】>点击开启【企业付款到零钱】功能，然后点击右侧的滑动按钮【启用】。</p>
        <p><a href="https://www.huisou.cn/home/index/detail/759/help" target="_blank" class="t-blue">自动提现教程</a></p>
		{{--<p style="color:red;">注意：一旦开启微信提现功能，默认无法使用银行卡、支付宝线下手动打款功能，二者只能选其一。</p>--}}
		<!-- 总开关 -->
		<div class="switch-wrap company_pay">
			<label class="ui-switcher @if($shop['company_pay']==1 || $shop['company_pay']== 2) ui-switcher-on @else ui-switcher-off @endif" @if(in_array($shop['company_pay'],[1,2])) data-is-open="1" @else data-is-open="0" @endif></label>
		</div>
	</div>
	<!-- 分销佣金管理 by 崔源 2018.11.21 -->
	<div class="switch bg-gray commission-show">
		<strong>自动审核功能 </strong>
		<p>当开启【自动审核】，分销客发起提现，所有佣金自动打入用户微信账户。</p> 
		<!-- 总开关 -->
		<div class="switch-wrap commission_set">
			<label class="ui-switcher @if($shop['company_pay']==2) ui-switcher-on @else  ui-switcher-off @endif" data-is-open="{{$shop['company_pay']}}"></label>
		</div>
	</div>
	<div id="distribute_content" class="@if($shop['is_distribute']==0) none @endif">
        <div class="bb-line">
    	    <!-- 标题和开关 -->
    	    <div class="switch">
    	        <p><strong class="mr20">默认分销摸板：</strong><span id="template_name">@if(!empty($template)){{$template['title']}}@endif</span>&nbsp; <a href="javascript:;" id="defaultDistribute" class="blue">更换</a></p>
    	        <p  class="mt10">在选择后，每一个商品sku就将默认执行该默认分销摸板规则，也可自定义更改。</p>
    	    </div> 
        </div>
    	<div class="bb-line">
    	    <!-- 标题和开关 -->
    	    <div class="switch">
    	        <p><strong class="mr20">统一分销设定：</strong> <a href="javascript:;" id="unifiedDistribute" class="blue">去设定</a></p>  
    	    	<p class="mt10">在这里面可以对店铺中的所有商品进行统一分销场景设定，所有的(已设定/未设定分销规则)商品都执行统一分销规则。</p>
    	    </div> 
        </div>
    	
    	<div>
    	    <!-- 标题和开关 -->
    	    <div class="switch">
    	        <p><strong class="mr20">营销活动商品说明：</strong> </p> 
    	        <p  class="mt10">当商品结算后实际支付金额(不含运费)大于分销成本(商品售价-各级分销佣金)时。两者相减得金额，既为分销佣金。将按佣金比例发放给各级合伙人；</p>
                <p>
                	当商品结算后实际支付金额(不含运费)小于等于分销成本(商品售价-各级分销佣金)时，则不计算分销佣金。
                </p> 
    	    </div> 
        </div>
        
		<div>
			<!-- 标题和开关 -->
			<div class="switch">
				<div class="switch-small">
					<p class=""><strong class="mr20">是否设定用户提交申请成为分销客:</strong></p>
					<!-- 按钮 开始 -->
					<div class="js-distribute-switch">
						<label class="ui-switcher @if($shop['is_apply_distribute'] == '1')ui-switcher-on @else ui-switcher-off @endif" data-is-open="{{$shop['is_apply_distribute']}}"></label>
					</div>
					<!-- 按钮 结束 -->
					<a href="{{ URL('merchants/distribute/applyList') }}" class="blue ml20 js-distribute-show" @if($shop['is_apply_distribute'] != '1') style="display: none" @endif >去设定</a>
				</div>
				<div class="mt10 js-distribute-show" @if($shop['is_apply_distribute'] != '1') style="display: none" @endif>
					<p>1、请务必在 <strong>“去设定”</strong> 或 <strong>“申请页面”</strong> 创建成为店铺分销客的申请页面；</p>
					<p>2、设置完成后，申请页面点击 <strong>“推广”</strong> ，通过页面二维码或链接分享邀请用户成为您店铺的分销客。</p>
					<p><strong style="color:#FF4343">注： </strong>如果商家开启“用户提交申请成为店铺分销客”，用户未提交成为店铺分销客申请，即使产生购买，该用户也不能获得该商品佣金</p>
				</div>
				
			</div>
		</div>

      	<div>
    	    <!-- 标题和开关 -->
    	    <div class="switch">
    	        <div class="switch-small">
    	        	<p class=""><strong class="mr20">是否设定分销门槛:</strong></p> 
    	        	<!-- 按钮 开始 -->
		            <div class="switch_item">
			            <label class="ui-switcher @if($shop['demand'])ui-switcher-on @else ui-switcher-off @endif" data-is-open=@if($shop['demand']) 1 @else 0 @endif></label>
			        </div>
		            <!-- 按钮 结束 -->
    	        </div> 
    	        <p  class="mt10">打开分销门槛后,微商城会员中心中我的财富和财富眼会隐藏,买家购买分销产品也不会获得佣金。</p>
                <p>满足门槛限制后,会在"我的"里面提示用户是否愿意成为分销客。</p> 
                <p>用户同意成为分销客后才能获取佣金,并且在会员中心显示我的财富和财富眼。</p> 
                <div class="fenxiao_check">  
	                <p class="zx_mabom5">
						@if($shop['distribute_grade']==1 && isset($shop['demand']['pay_num']))
							<input type="checkbox" value="1" checked="checked" class="pay_num data_check">
							累计支付成功<input type="text" value="{{$shop['demand']['pay_num']}}" class="pay_num_save zx_wid60">笔
						@else
							<input type="checkbox" value="0" class="pay_num data_check">
							累计支付成功<input type="text" value="" class="pay_num_save zx_wid60 zx_backf5" disabled="disabled">笔
						@endif
	            	</p>
	            	<p class="zx_mabom5">

						@if($shop['distribute_grade']==1 && isset($shop['demand']['pay_amount']))
							<input type="checkbox" value="1" checked="checked" class="pay_amount data_check">
							或累计消费金额<input type="text" value="{{$shop['demand']['pay_amount']}}" class="pay_amount_save zx_wid60">元
						@else
							<input type="checkbox" value=" 0 " class="pay_amount data_check">
							或累计消费金额<input type="text" value="" class="pay_amount_save zx_backf5 zx_wid60" disabled=" true ">元
						@endif
	            	</p>
	            	<p class="zx_mabom5">

						@if($shop['distribute_grade']==1 && isset($shop['demand']['score']))
							<input type="checkbox" value="1" checked="checked" class="score data_check">
							或累计积分达到<input type="text" value="{{$shop['demand']['score']}}" class="score_save zx_wid60">分
						@else
							<input type="checkbox" value=" 0 " class="score data_check">
							或累计积分达到<input type="text" value="" class="score_save zx_backf5 zx_wid60" disabled=" true ">分
						@endif

	            	</p>
            		<a class="btn btn-primary screening">保存</a>
            	</div>
    	    </div> 
        </div>
		<div>
			<!-- 标题和开关 -->
			<div class="switch">
			<input type="hidden" id="is_off_on" value="0" />
				<p class=""><strong class="mr20">分销员等级设定：</strong></p>
				<table class="grade-table mt10">
					<thead>
						<tr>
							<th>等级值</th>
							<th>等级名称</th>
							<th>升级规则<i class="glyphicon glyphicon-question-sign rule-tip"></i>
								<div class="rule-detail">升级规则一致时，默认成为等级值高的级别</div>
							</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<!-- <tr>
							<td>1</td>
							<td><input class="grade-name grade-first" type="text" value="普通分销员" disabled></td>
							<td>默认成为分销员即是该等级</td>
							<td></td>
						</tr> -->
						<!-- <tr>
							<td>2</td>
							<td><input class="grade-name" type="text" placeholder="等级名称"></td>
							<td>
								<div class="rule-item">
									<input type="checkbox">
									<p>累计推广金达<input type="text" onkeyup="this.value=this.value.replace(/\D/g,'')">元</p>
								</div>
								<div class="rule-item">
									<input type="checkbox">
									<p>累计推广金与消费金总和达<input type="text" onkeyup="this.value=this.value.replace(/\D/g,'')">元</p>
								</div>
								<div class="rule-item">
									<input type="checkbox">
									<p>购买指定商品升级：</p>
									<a href="javascript:void(0);" class="add-product">+添加商品</a>
									<div class="change-content" style="display:none;">
										<div class="flex">
											<div class="specify-product">
												<p>购买指定商品升商品</p>
												<i class="close-item">×</i>
											</div>
											<a href="javascript:void(0);" class="change-product">修改</a>
										</div>
									</div>
								</div>
								<div class="delete-grade">×</div>
							</td>
						</tr> -->
					</tbody>
				</table>
				<div class="add-grade">
					<input type="hidden" id="is_on_off" value="0" />
					<button class="add-btn">+添加等级</button>
				</div>
				<!-- <a class="btn btn-primary grade_save">保存</a> -->
			</div>
		</div>

		<div>
			<!-- 标题和开关 -->
			<div class="switch_withdraw">
				<div class="switch-small">
					<p class=""><strong class="mr20">是否设定分销提现门槛:</strong></p>
					<!-- 按钮 开始 -->
					<div class="switch_item_withdraw">
						<label class="ui-switcher @if($shop['withdraw_grade'] > 0)ui-switcher-on @else ui-switcher-off @endif" data-is-open="{{$shop['withdraw_grade']}}"></label>
					</div>
					<!-- 按钮 结束 -->
				</div>
				<p  class="mt10">分销提现门槛描述：打开提现门槛后，用户进行佣金提现时，需要满足设置的最低价，方可提现</p>
				<div class="fenxiao_check_withdraw" @if($shop['withdraw_grade'] <= 0) style="display: none" @endif>
					<p class="zx_mabom5">
							最低提现金额<input type="text" value="{{$shop['withdraw_grade']}}" class="pay_num_save zx_wid60 ">元
					</p>
					<a class="btn btn-primary switch_withdraw_save">保存</a>
				</div>
			</div>
		</div>

	</div>
	 <!-- 商品添加框 -->
	 <div class="modal export-modal" id="myModal">
            <div class="modal-dialog" id="modal-dialog">
                <!-- <form class="form-horizontal"> -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <ul class="module-nav modal-tab">
								<li class="link-group link-group-0" style="display: inline-block;">
                                    已上架商品
                                </li> |
                                <li class="link-group link-group-0" style="display: inline-block;">
                                    <a href="{{URL('/merchants/product/create')}}" target="_blank" class="new_window">新建商品</a>
                                </li>
                            </ul>
						</div>
						<div class="model-search">
							<div class="input-append">
								<input class="input-small js-modal-search-input form-control" type="search">
							</div>
						</div>
                        <div class="modal-body">
                            <table class="table table-striped ui-table ui-table-list">
                                <thead>
									<tr>
										<th>标题</th>
										<th>创建时间</th>
										<th>操作</th>
									</tr>
                                </thead>
                                <tbody>
                                <!-- <tr>
                                    <td>
                                        <div class="td-cont flex">
											<img class="image" src="http://hsshop.com/hsshop/image/2018/12/03/1635036074256652.JPG">
											<p>1111111111111111</p>
                                        </div>
                                    </td>
                                    <td>
                                        <span>
										2018-10-16 15:07:00
                                        </span>
                                    </td>
                                    <td>
                                        <div class="td-cont text-right">
                                            <a href="javascript:void(0);" data-id="1" class="js-choose">选取</a>
                                        </div>
                                    </td>
                                </tr> -->
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer clearfix">
                            <div style="" class="js-confirm-choose pull-left">
                                <input type="button" class="btn btn-primary" value="确定使用">
                            </div>
                            <div class="good_pagenavi">
                                <span class="total">共 2 条，每页 8 条</span></div>
                        </div>
                    </div>
                <!-- </form> -->
            </div>
        </div>
        <!-- 商品添加框 -->
</div>
@endsection

@section('page_js') 
	<script>
		var imgUrl = "{{ imgUrl() }}";
		var wid = "{{ session('wid') }}";
		var grade = {!! json_encode($grade) !!};
		console.log(wid,111)
		var title = "{{$shop['distribute_default_grade_title']}}";
	</script>
 	<!-- layer -->
	 <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script> 
	 <!-- ajax分页js -->
	<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/distribute_hlyx482r.js"></script>
@endsection