@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_eq5irpip.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{ URL('/merchants/capital/billSummary') }}">账单总汇</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
	<div class="content">
		<!-- 要打印的东西 -->
		<div id="print">
			<!-- 标题 -->
			@if($type == 1)
			<h4 class="detail-title">{{ $year }}年{{ $month }}月{{ $day }}日汇总账单</h4>
			@else
			<h4 class="detail-title">{{ $year }}年{{ $month }}月汇总账单</h4>
			@endif
	  		<!-- 账单详情 -->
	  		<ul class="account-detail clearfix">
	  			<li>
	  				<span>店铺名称：</span>{{ $weixinInfo['shop_name'] }}
	  			</li>
	  			<li>
	  				<span>币种：</span>人民币
	  			</li>
	  			<li>
	  				<span>起始日期：</span> {{ $startTime }}
	  			</li>
	  			<li>
	  				<span>终止日期：</span> {{ $endTime }}
	  			</li>
	  		</ul>
	  		<hr>
	  		<!-- 表格 -->
	  		<table class="table table-bordered">
	  			<tr class="active">
	  				<td>上期汇总营收</td>
	  				<td>本期收入</td>
	  				<td>本期支出</td>
	  				<td>本期汇总营收</td>
	  			</tr>
	  			<tr>
	  				<td>{{ $return['periodSum'] }}</td>
	  				<td>{{ $return['thisIncome'] }}</td>
	  				<td>{{ $return['thisPaid'] }}</td>
	  				<td>{{ $return['thisSum'] }}</td>
	  			</tr>
			</table>
			<!-- 面板 -->
			<div class="data-content">
				<!-- 左面板 -->
				<div class="data-items">
					<div class="panel panel-default">
					<div class="panel-heading">本期收入</div>
						<div class="panel-body">
							@if($return['thisIncome'])
							<!-- 数据列表 -->
							<div class="data-wrap">
								<div class="data-list clearfix">
									<span class="pull-left">业务类型</span>
									<span class="pull-right">收入金额</span>
								</div>
								<div class="data-list clearfix">
									<span class="pull-left">交易收入</span>
									<span class="pull-right">{{ $return['thisIncome'] }}</span>
								</div>
								<div class="data-list clearfix">
									<span class="pull-left">合计收入</span>
									<span class="pull-right green_3c3">+{{ $return['thisIncome'] }}</span>
								</div>
							</div>
							@else
							<!-- 空数据 -->
							<div class="no-result">本期无收入</div>
							@endif
						</div>
					</div>
				</div>
				<!-- 右面板 -->
				<div class="data-items">
					<div class="panel panel-default">
						<div class="panel-heading">本期支出</div>
						<div class="panel-body">
							@if($return['thisPaid'])
							<!-- 数据列表 -->
							<div class="data-wrap">
								<div class="data-list clearfix">
									<span class="pull-left">业务类型</span>
									<span class="pull-right">支出金额</span>
								</div>
								<div class="data-list clearfix">
									@if(isset($return['refund']) && $return['refund'])
									<div class="pull-left">
										<p>交易退款</p>
									</div>
									<div class="pull-right">
										<p>{{ $return['refund'] }}</p>
									</div>
									@elseif(isset($return['distribute']) && $return['distribute'])
									<div class="pull-left">
										<p>分销打款</p>
									</div>
									<div class="pull-right">
										<p>{{ $return['distribute'] }}</p>
									</div>
									@endif
								</div>
								<div class="data-list clearfix">
									<span class="pull-left">合计支出</span>
									<span class="pull-right red_f00">-{{ $return['thisPaid'] }}</span>
								</div>
							</div>
							@else
							<!-- 空数据 -->
							<div class="no-result">本期无支出</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- 打印 -->
		<div class="print-btn">
			<a class="btn btn-primary" href="javascript:void(0);" target="_self">打印汇总账单</a>
		</div>
    </div>
    @endsection
    @section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/capital_eq5irpip.js"></script>
    <script type="text/javascript">
    	$(function(){
    		//账单打印
    		$('.btn-primary').click(function(){
    			window.print();
    		});
    	});
    </script>
    @endsection