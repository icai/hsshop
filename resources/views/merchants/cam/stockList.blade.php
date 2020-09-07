@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_39ygjl7x.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/stockList.css">
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/cam/list') }}">发卡密</a>
            </li>
            <li>
                <a >卡密库</a>
            </li>
        </ul>
    </div>
</div>
@endsection
@section('content')
<div class="content">
	<ul class="tab_nav" style="margin-bottom: 20px;">
        <li class="hover">
            <a>所有卡密</a>
        </li>   
    </ul>  
	<div class="mgb20">
		<a href="/merchants/cam/addStock?id={{ request('id') }}" class="btn btn-success">添加库存</a>
    </div>	
	<!-- 列表 开始 -->
    <div class="table table-hover condent_data">
        <form name="kam_form">
            <!-- 标题 -->
            <ul class="active ul_color data_title flex_center">
                <li>序号</li>
                @if($list && isset($list[0]['name_key']) && $list[0]['name_key'])
                <li>{{ $list[0]['name_key'] }}</li>
                @endif
                @if($list && isset($list[0]['attr_key']) && $list[0]['attr_key'])
                <li>{{ $list[0]['attr_key'] }}</li>
                @endif
                <li>创建时间</li>
                <li>发送时间</li>
                <li>购买者</li>
               <!-- <li>使用时间</li> -->
                <li>订单详情</li>
            </ul>
            <!-- 列表 -->
            @forelse($list as $key => $val)
            <ul class="data flex_center">
                <li class="index-li">
                    <input type="checkbox"  name="ids[]" value="{{ $val['id'] }}" class="J_kam">
                    @if(request('page'))
                    {{ (request('page')-1)*20 + ($key+1) }}
                    @else
                    {{ $key + 1 }}
                    @endif
                </li>
                @if(isset($val['name_val']) && $val['name_val'])
                <li>{{ $val['name_val'] }}</li>
                @endif
                @if(isset($val['attr_val']) && $val['attr_val'])
                <li>{{ $val['attr_val'] }}</li>
                @endif
                <li>{{ $val['created_at'] }}</li>
                <li>{{ $val['is_send'] == 0 ? '未发送' : $val['send_time'] }}</li>
                <li>
                    @if(!empty($val['getMember']))
                      {{ $val['getMember']['truename'] ?  $val['getMember']['truename'] : $val['getMember']['nickname'] }}
                    @else
                        --
                    @endif
                </li>
                <!-- <li>{{ empty(intval($val['use_time'])) ? '未使用' : $val['use_time'] }}</li> -->
                @if(!empty($val['oid']))
                <li class="opt_wrap blue_97f">
                    <a href="/merchants/order/orderDetail/{{ $val['oid'] }}">查看详情</a>
                </li>
                @else
                <li class="opt_wrap">
                    --
                </li>
                @endif
            </ul>
           @empty
           <div style="text-align:center;margin-top:10px;">暂无数据</div>
           @endforelse
           <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        </form>
        <!-- <div style="text-align:center;margin-top:10px;">暂无数据</div> -->
        
    </div>
    @if($list)
    <div style="text-align: right;" class="clearfix">
        <div class="action-footer">
            <label for="all-kam" class="allkam-label">
                <input type="checkbox" id="all-kam">
                全选
            </label>
        </div>
        <div class="del-box J_del">
            <a href="javascript:void(0);">批量删除</a>
            <div class="ui-popover ui-popover--confirm right-center" id="del-popover">
                <div class="ui-popover-inner clearfix ">
                    <div class="inner__header clearfix">
                        <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确认删除？</div>
                        <div class="pull-right">
                            <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-del-sure">确定</a>
                            <a href="javascript:void(0);" class="zent-btn zent-btn-cancel js-cancel">取消</a>
                        </div>
                    </div>
                </div>
                <div class="arrow"></div>
            </div>
        </div>
        <div class="del-box J_export">
            <a href="javascript:void(0);">导出全部</a>
            <div class="ui-popover ui-popover--confirm right-center" id="export-popover">
                <div class="ui-popover-inner clearfix ">
                    <div class="inner__header clearfix">
                        <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确认导出？</div>
                        <div class="pull-right">
                            <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-export-sure">确定</a>
                            <a href="javascript:void(0);" class="zent-btn zent-btn-cancel js-cancel">取消</a>
                        </div>
                    </div>
                </div>
                <div class="arrow"></div>
            </div>
        </div>
        {!! $pageHtml !!}
    </div>
    @endif
</div>
@endsection
@section('page_js')
    <!-- 当前页面js --> 
    <script type="text/javascript">
    	var host ="{{ config('app.url') }}";
    	var _host = "{{ config('app.source_url') }}";
    	var wid = {{session('wid')}};	
        var id = {{ $id }};
    </script>   
    <script src="{{ config('app.source_url') }}mctsource/js/stockList.js"></script>
@endsection