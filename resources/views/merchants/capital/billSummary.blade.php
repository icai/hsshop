@extends('merchants.default._layouts')
@section('head_css')
<!-- 时间插件 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_rtn6dbmh.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
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
    <!-- 结算规则 开始 -->
    <div class="settlement_items mgb15">
        <p class="items_title">会搜云自2017年5月1日起向所有微商城商户提供账单汇总服务，5月之前账单请查看账单明细，月账单会在次月首日生成</p>
         <a class="blue_38f" href="javascript:void(0);" data-toggle="modal" data-target="#myModal">结算规则一览</a> 
        <!-- 规则弹框 开始 -->
        <!-- 规则 -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- 弹框主体 头部 -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">结算规则</h4>
                    </div>
                    <!-- 弹框头部 结束 -->
                    <!-- 弹框主体 开始 -->
                    <div class="modal-body">
                        <!-- 规则 -->
                        <div class="rule_list">
                            <span class="rule_name">结算方式：</span>
                            <p class="rule_des">［结算时间=发货时间/商家退款或分销打款的确认打款时间</p>
                        </div>
                        <!-- 补充 -->
                        <div class="rule_list c_gray mgt30">
                            <span class="rule_name">ps:结算日期取整天,00:00:00-23:59:59</span>
                            <p class="rule_des"> 日账单为当前结算时间内,所有已结算金额汇总
                                <p class="rule_des">月账单为该自然月结算时间内,所有已结算金额汇总</p>                                
                            </div>
                        </div>
                        <!-- 弹框主体 结束 -->
                        <!-- 弹框底部 开始 -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                        <!-- 弹框底部 结束 -->
                    </div>
                </div>
            </div>
            <!-- 规则弹框 结算 -->
        </div>
        <!-- 结算规则 结束 -->
        <!-- 导航 开始 -->
        <ul class="screen_nav nav nav-tabs mgb15" role="tablist">
            <li role="presentation" @if ( $type == 1 ) class="active" @endif  >
                <a href="{{ URL('/merchants/capital/billSummary') }}?type=1">日总汇</a>
            </li>
            <li role="presentation" @if ( $type == 2 ) class="active" @endif >
                <a href="{{ URL('/merchants/capital/billSummary') }}?type=2">月总汇</a>
            </li>            
        </ul>
        
        @if ( $type == 1 )
        <div class="col-sm-3 center_start">
            <!-- 日汇总 -->
            <div id='start_time' class='input-group' data-date="12-02-2012" data-date-format="dd-mm-yyyy">
                <input class="form-control" type='text' value="{{ $year }}-{{ $month }}"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        @elseif( $type == 2 )
        <div class="col-sm-3">
            <!-- 月汇总 -->
            <div id='end_time' class='input-group' data-date="2017" data-date-format="dd-mm-yyyy">
                <input class="form-control" type='text' value="{{ $year }}"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        @endif
        <div class="data-content">
            <!-- 列表 开始 -->
            <table class="table table-hover f12">
                <thead>
                    <tr class="active">
                        <td>结算日期
                        	<div class="help-td">？
                        		<div class="popover bottom poabl">
                        			<div class="arrow"></div>
                        			<h3 class="popover-title">结算方式：</h3>
                        			<div class="popover-content">
                        				<p>ps：结算日期取整天，00:00:00-23:59:59</p>
                        				<p>日账单为当天结算时间内，所有已结算金额汇总</p>
                        				<p>月账单为该自然月结算时间内，所有已结算金额汇总</p>
                        			</div>
                        		</div>
                        	</div>
                        </td>
                        <td>收入（元）</td>
                        <td>支出（元）</td> 
                        <td>操作</td> 
                    </tr>
                </thead>
                <tbody class="tbody-mod">
                    @forelse ( $list as $k => $v )
                    <tr>
                        <td>{{ $k }}</td>
                        
                            @if(isset($v['income']) && $v['income'])
                                <td class="green_f04">+{{ sprintf('%.2f',$v['income']) }}</td>
                            @else
                                <td>0.00</td>
                            @endif

                            @if(isset($v['paid']))
                                <td class="red_f00">-{{ sprintf('%.2f',$v['paid']) }}</td>
                            @else
                                <td>0.00</td>
                            @endif
                         
                        <td>
                            <a class="blue_38f" href='{{ URL("/merchants/capital/billSummaryContent/$type/{$v["param"]}") }}'>详情</a>
                        </td> 
                    </tr>
                    @empty
                    @endforelse
               
                </tbody>
            </table>
            <!-- 列表 结束 -->
            <span class="page_detail">{!! $pageHtml !!}</span>
           
        </div>
    </div>
    @endsection
    @section('page_js')
    <!-- 时间插件 -->
	<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/capital_rtn6dbmh.js"></script>
    <script type="text/javascript">
    	//首先是工具提示：
		$(function () { $("[data-toggle='tooltip']").tooltip(); });
		//然后是弹出框：
		$(function () { $("[data-toggle='popover']").popover(); });
    </script>
    @endsection