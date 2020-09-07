@extends('merchants.default._layouts')
@section('head_css') 
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/seckill_ppdcpeq2.css" />
    <style type="text/css"> 
        .laydate_box, .laydate_box * { box-sizing:content-box; } 
    </style>
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
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">秒杀</a>
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
                    @foreach($tabList as $k => $v)
                        <li @if ((empty(Route::input('status')) && $k == 'all') || (Route::input('status') == $k)) class="hover" @endif>
                            <a href="{{url('/merchants/marketing/seckills/' . $k)}}">{{$v}}</a>
                        </li>
                    @endforeach
                </ul>  
            </div>
            <div class="pull-right common-helps-entry">
            	<a class="nav_module_blank" href="{{ config('app.url') }}home/index/detail/627/help" target="_blank">秒杀使用方法</a>
            </div> 
        </div>  
        <div class="app-design clearfix"> 
            <div class="page-seckill clearfix">
                <h2 class="seckill-title">设置秒杀详情</h2>
                <!-- 内容左边 -->
                <div class="app-preview">
                    <div class="app-entry">
                        <div class="app-config js-config-region">
                            <div class="app-field clearfix editing">
                                <h1><span>{{$detail['seckill']['title']}}</span></h1>
                                <div class="seckill-goods-preview">
                                    <!-- 编辑状态下展示真实数据 -->
                                    <div class="image-box-show">
                                        <img class="goods-main-photo" src="{{ imgUrl() . $detail['product']['img']}}" alt="">
                                    </div>
                                    <div class="goods-header goods-activity">
                                        <div class="goods-price clearfix">
                                            <div class="activity-price current-price">
                                                <span class="price-title">{{$detail['seckill']['tag']}}</span>
                                                ￥<i class="js-goods-price price">X</i>
                                            </div>
                                            <div class="original-price">￥Y</div>
                                            <div class="overview-countdown">
                                                <div class="countdown-title">活动剩余时间</div>
                                                <div class="js-time-count-down countdown">
                                                    <span class="js-day">D</span>:
                                                    <span class="js-hour">H</span>:
                                                    <span class="js-min">M</span>:
                                                    <span class="js-sec">S</span>
                                                </div>
                                            </div>
                                        </div>
                                        <h2 class="title">{{$detail['product']['title']}}</h2>
                                        <p class="seckill-tip font-size-12 c-gray-dark clearfix">
                                            秒杀参与条件说明
                                        </p>
                                    </div>
                                    <div class="goods-details-block">
                                        <h4>详细信息区</h4>
                                        <p>Sku信息、运费、其他自定义组件内容</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="app-fields js-fields-region">
                            <div class="app-fields ui-sortable">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 内容右边 -->
                <div class="app-sidebar" style="margin-top: 0px;">
                    <div class="arrow">
                    </div>
                    <div class="app-sidebar-inner js-sidebar-region">
                        <div> 
                            <div class="clearfix">
                                <label class="legend">商品设置</label>
                                <div class="wrapper clearfix">
                                    <label>
                                        <em class="required">*</em>
                                        选择商品（只能添加一件商品参与秒杀活动<a href="javascript:;" target="_blank">查看详情</a>)
                                    </label>
                                    <div class="clearfix js_select_goods_div mt10" >
                                        <a href="javascript:;" class="fl"><img src="{{ imgUrl() . $detail['product']['img']}}" alt="" width="50" height="50"></a>
                                        <div class="fl"  style="margin-left:-110px;padding-left:120px;width:100%;">
                                        	<div class="pr">
                                        		<p style="height: 21px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">{{$detail['product']['title']}}</p>
                                        	</div>
                                        	<p class="mt10">
                                        		<span class="seckill_span_price">秒杀价：{{$detail['seckill']['price_range']}}</span>
                                        		<span class="seckill_span_stock_num ml10">秒杀库存：{{$detail['seckill']['stock_sum']}}</span>
                                        	</p> 
                                        </div>
                                    </div> 
                                    <p class="error-message" style="margin-left:0;">请选择一个参加秒杀活动的商品</p>
                                    <input type="hidden" id="goods_id">
                                </div>
                            </div>
                            <div>
                                <label class="legend">活动设置</label>
                                <div class="wrapper clearfix">
                                    <label class="label-title">
                                        <em class="required">*</em>活动名称：
                                    </label>
                                    <span>
                                        <input disabled class="form-control w240 iblock valid" type="text" id="title" value="{{$detail['seckill']['title']}}">
                                    </span>
                                    <p class="error-message" style="margin-left:76px;">请设置活动名称</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title"><em class="required">*</em>开始时间：</label>
                                    <span>
                                        <input disabled class="form-control w240 iblock valid" type="text" id="startTime" value="{{$detail['seckill']['start_at']}}">
                                    </span>
                                    <p class="error-message" style="margin-left:76px;">请设置秒杀开始时间</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title"><em class="required">*</em>结束时间：</label>
                                    <span>
                                        <input disabled class="form-control w240 iblock valid" type="text" id="endTime" value="{{$detail['seckill']['end_at']}}">
                                    </span>
                                    <p class="error-message" style="margin-left:76px;">请设置秒杀结束时间</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title">活动标签：</label>
                                    <span>
                                        <input disabled class="form-control w240 iblock valid" type="text" id="tag" value="{{$detail['seckill']['tag']}}">
                                        <p class="help-desc">
                                            活动期间展示于商品详情的价格旁边，2至5字
                                        </p>
                                    </span>
                                    <p class="error-message" style="margin-left:76px;">标签不能为空</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title box_float">类型选择：</label>
                                    <div class="box_flex malef4">
                                        <label class="limit-label">
                                            <input class="type_select" type="radio" name="type_select" value="0" disabled @if ($detail['seckill']['type'] == 0) checked @endif />
                                          	<span>全部</span>
                                        </label> 
                                        <label class="limit-label">
                                            <input class="type_select" type="radio" name="type_select" value="1" disabled @if ($detail['seckill']['type'] == 1) checked @endif/>
                                            <span>微商城</span>
                                        </label> 
                                        <label class="limit-label">
                                            <input class="type_select" type="radio" name="type_select" value="2" disabled @if ($detail['seckill']['type'] == 2) checked @endif/>
                                            <span>小程序</span>
                                        </label> 
                                    </div>
                                </div>
                                @if($detail['seckill']['limit_num'])
                                <div class="wrapper clearfix">
                                    <label class="label-title box_float">每人限购：</label>
                                    <div class="box_flex1 malef4">
                                        <label class="limit-label">
                                            <input type="checkbox" checked disabled id="is_limit">
                                            <span>开启限购</span>
                                        </label> 
                                    </div>
                                    <div class="js_span_limit_num">
                                        每人可购买<input disabled type="number" value="{{$detail['seckill']['limit_num']}}" min="0" class="form-control iblock input-sm w70" id="limit_num" />件
                                    </div>
                                </div>
                                @else
                                    <div class="wrapper clearfix">
                                        <label class="label-title box_float">每人限购：</label>
                                    <div>
                                        <label class="limit-label">
                                            <input type="checkbox" disabled id="is_limit">
                                            <span>开启限购</span>
                                        </label>
                                    </div>
                                    </div>
                                @endif
                                <div class="wrapper clearfix">
                                    <label class="label-title">订单取消：</label>
                                    <span>
                                        买家，
                                        <input type="number" min="5" max="10" class="form-control iblock center" style="width:60px;" disabled id="cancel_minutes" value="{{$detail['seckill']['cancel_minutes']}}">
                                        分钟未支付订单，订单取消
                                    </span>
                                </div>
                                <!-- 分享设置开始 -->
                                <div class="wrapper clearfix">
                                    <label class="label-title" style="width: 98px;">
                                        分享标题设置：
                                    </label>
                                    <span>
                                        <input class="form-control w240 iblock valid" type="text" id="share_title" value="{{$detail['seckill']['share_title'] or ''}}" placeholder="最多支持18个字">
                                    </span>
                                </div>

                                <div class="wrapper clearfix">
                                    <label class="label-title" style="width: 98px;">
                                    分享内容设置：
                                    </label>
                                    <span>
                                        <textarea cols="50" rows="10" class="form-control js_coupons_name" style="width: 240px;display: inline;" id="share_desc" placeholder="最多支持50个字">{{$detail['seckill']['share_desc'] or ''}}</textarea> 
                                    </span>
                                </div>

                                <div class="wrapper clearfix">
                                    <label class="label-title" style="width: 98px;">
                                        分享页图片：
                                    </label>
                                    <span>
                                        <input type="hidden" id="share_img" value="{{$detail['seckill']['share_img'] or ''}}">
                                        @if($detail['seckill']['share_img'])
                                        <img src="{{ imgUrl() }}{{ $detail['seckill']['share_img'] or ''}}" style="width: 80px;height: 80px;">
                                        @else
                                        <img src="{{ $detail['seckill']['share_img'] or ''}}" style="width: 80px;height: 80px;display: none;">
                                        @endif
                                    </span>
                                </div>
                                <!-- 分享设置结束 -->
                            </div>  
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
    </div>
    <div class="t-footer">
        <input type="button" class="btn btn-default" onclick="history.back();" value="返回" /> 
    </div>  
@endsection

@section('page_js') 
    <script type="text/javascript">
        var _host = "{{ imgUrl() }}";
        var wid = "{{ session('wid') }}";
    </script>
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>               
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/seckill_ppdcpeq2.js"></script>
@endsection