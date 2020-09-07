@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">我的收入</div>
        <!-- 二级导航三级标题 结束 -->
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
    <!-- 个人中心 开始 -->
    <div class="personal_center mgb15">
        <!-- 个人信息 开始 -->
        
        <div class="personal_items">
            <!-- logo 开始  -->
            <div class="img_wrap">
                @if ( session('logo') )
                <img src="{{ imgUrl(session('logo')) }}"/>
                @else
                <img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" >
                @endif
            </div>
            <!-- logo 结束 -->
            <!-- 信息 开始 -->
            
            <div class="personal_content">
                <div class="info_list">
                    <span class="info_name">店铺名称：</span>
                    <p class="items_title">{{ $info['shop_name'] }}</p>
                </div>
                
            </div>
           
            <!-- 信息 结束 -->
        </div>
        <!-- 个人信息 结束 -->
        <!-- 个人资金 开始 -->
        <ul class="money_items">
            <li>
                <p class="items_title">7天收入（截至今日0点）</p>
                <div class="money_bottom">
                    <p class="bottom_left"><span class="money_num">{{ $return['seven_income'] or '0.00' }}</span> 元</p>
                    <a class="bottom_right blue_38f" href="/merchants/capital/billDetail?start_time={{ date('Y-m-d H:s:i',strtotime('-7 day')) }}&end_time={{ date('Y-m-d H:s:i',time()) }}&type=0">收支明细</a>
                </div>
            </li>
            <li>
                <p class="items_title">30天收入（截至今日0点）</p>
                <div class="money_bottom">
                    <p class="bottom_left"><span class="money_num">{{ $return['month_income'] or '0.00' }}</span> 元</p>
                    <a class="bottom_right blue_38f" href="/merchants/capital/billDetail?start_time={{ date('Y-m-d H:s:i',strtotime('-30 day')) }}&end_time={{ date('Y-m-d H:s:i',time()) }}&type=0">收支明细</a>
                </div>
            </li>
            <li>
                <p class="items_title">总收入（截至今日0点）</p>
                <div class="money_bottom">
                    <p class="bottom_left"><span class="money_num">{{ $return['total_income'] or '0.00' }}</span>元</p>
                    <a class="bottom_right blue_38f" href="/merchants/capital/billDetail?start_time={{ $info['created_at'] }}&end_time={{ date('Y-m-d H:s:i',time()) }}&type=0">收支明细</a>
                </div>
            </li>
        </ul>
        <!-- 个人资金 结束 -->
    </div>
    <!-- 个人中心 结束 -->
    <!-- 区域标题  -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">近期交易流水</p>
        <div class="common_link"></div>
        <div class="common_right">
             <a class="blue_38f" href="/merchants/currency/payment"> 支付方式设置</a> 
            
        </div>   
    </div>
    <!-- 区域标题 结束 -->
    <!-- 流量趋势图表 开始 -->
   
    <!-- 流量趋势图表  -->
    <div class="data-content">
         <!-- 数据 -->
        @if(!empty($return['datas']))
        <table class="table table-bordered table-striped f14">
            <thead> 
                <tr class="active porel">
                    <td>结算时间
                    	<div class="help-td">？
	                    	<div class="popover bottom poabl">
	                    		<div class="arrow"></div>
	                    		<h3 class="popover-title">结算方式：</h3>
	                    		<div class="popover-content">
	                				结算时间=发货时间/商家退款或分销打款的确认打款时间
								</div>
	                    		<div class="popover-content">
	                    			<p>ps：结算日期取整天，00:00:00-23:59:59</p>
									<p>日账单为当天结算时间内，所有已结算金额汇总</p>
									<p>月账单为该自然月结算时间内，所有已结算金额汇总</p>
								</div>
	                    	</div>
                    	</div>
                	</td>
                    <td width="250">类型 | 名称 | 交易号</td>
                    <td>金额｜明细</td>
                    <!--<td>余额(元)</td>-->
                    <td width="250">支付渠道｜单号</td>
                    <td>操作</td>
                </tr>
            </thead>
            @forelse($return['datas'] as $val)
            <tbody>
                <tr>
                    <td><p>{{ $val['updated_at'] }}</p></td>                  
                    <td>
                        <p>
                            @if($val['action'] == 2)
                                订单入账
                            @elseif($val['action'] == 8)
                                订单退款
                            @elseif($val['action'] == -1)
                                分销打款
                            @endif
                        </p>
                        <p>{{ $val['order']['oid'] }}</p>
                    </td>
                    <td class="{{ $val['pay_class'] }}">@if($val['action'] == -1)-@endif{{ $val['pay_price'] }}</td>
                    <!--<td></td>-->
                    <td>
                        <p>{{ $payWayList[$val['order']['pay_way']] }}</p>
                        <p class="f12 gray_999">{{ $val['order']['serial_id'] == 3 ? '' : $val['order']['serial_id'] }}</p>
                    </td>
                    <td>
                        @if($val['action'] == -1)
                            <a class="blue_38f" href="javascript:;">详情</a>
                        @else
                            <a class="blue_38f" href="/merchants/order/orderDetail/{{ $val['order']['id'] }}">详情</a>
                        @endif
                    </td>
                </tr>  
            </tbody>
            @empty
            @endforelse
        </table>
        {{$return['pageHtml']}}
        @else
        <!-- 分页  -->
        
        <div class="no_result">暂无数据</div>
        @endif
    </div>
</div>
@endsection
