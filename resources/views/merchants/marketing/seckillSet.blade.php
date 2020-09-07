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
    <div class="content" data-id="{{$detail['seckill']['id'] or ''}}">
        @if(!empty($detail['sku']))
            <ul id="sku_ul" style="display: none">
                @foreach($detail['sku'] as $v)
                    <li data-id="{{$v['sku_id']}}">
                        <span class="seckill_price">{{$v['seckill_price']}}</span>
                        <span class="seckill_stock">{{$v['seckill_stock']}}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix pr">
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li class="hover">
                        <a href="{{ URL('/merchants/marketing/seckills') }}">所有活动</a>
                    </li>
                    <li>
                        <a href="{{ URL('/merchants/marketing/seckills?status=1') }}">未开始</a>
                    </li>
                    <li>
                        <a href="{{ URL('/merchants/marketing/seckills?status=2') }}">进行中</a>
                    </li>
                    <li>
                        <a href="{{ URL('/merchants/marketing/seckills?status=3') }}">已结束</a>
                    </li>
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
                                <h1><span>秒杀</span></h1>
                                <div class="seckill-goods-preview">
                                    <!-- 编辑状态下展示真实数据 -->
                                    <div class="image-box-show">
                                        @if(!empty($detail['product']['img']))
                                            <img class="goods-main-photo" src="{{ imgUrl() . $detail['product']['img']}}" alt="">
                                        @else
                                        秒杀商品主图
                                        @endif
                                    </div>
                                    <div class="goods-header goods-activity">
                                        <div class="goods-price clearfix">
                                            <div class="activity-price current-price">
                                                <span class="price-title">{{$detail['seckill']['tag'] or '秒杀'}}</span>
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
                                        <h2 class="title">{{$detail['seckill']['title'] or '秒杀商品标题'}}</h2>
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
                                        选择商品（只能添加一件商品参与秒杀活动<a href="https://www.huisou.cn/home/index/detail/721/help" target="_blank">查看详情</a>)
                                    </label>
                                    @if(!empty($detail['seckill']['id']))
                                    <div class="clearfix js_select_goods_div mt10">
                                        <a href="javascript:;" class="fl"><img src="{{ imgUrl() . $detail['product']['img']}}" alt="" width="50" height="50"></a>
                                        <div class="fl"  style="margin-left:-110px;padding-left:120px;width:100%;">
                                            <div class="pr">
                                                <p style="height: 21px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">{{$detail['product']['title']}}</p>
                                                <div style="position: absolute;right:0;top:0;">
                                                    <button id="detil_id" data-id="{{$detail['product']['id']}}" class="hs-btn hs-btn-primary seckill_edit_sku btn-sm" data-id="1">编辑</button>
                                                    <button class="hs-btn hs-btn-primary btn-sm seckill_del_select_goods ml5">删除</button>
                                                </div>
                                            </div>
                                            <p class="mt10">
                                                <span class="seckill_span_price">秒杀价：<span id="price_range" data-id="{{$detail['product']['price']}}">{{$detail['seckill']['price_range']}}</span></span>
                                                <span class="seckill_span_stock_num ml10">秒杀库存：<span id="stock_sum" data-id="{{$detail['product']['stock']}}">{{$detail['seckill']['stock_sum']}}</span></span>
                                            </p>
                                        </div>
                                    </div>
                                    @else
                                    <div class="clearfix js_select_goods_div mt10" >
                                        <button type="button" class="hs-btn hs-btn-primary js-add-goods" style="margin:0 10px;">添加秒杀商品</button>
                                    </div>
                                    <p class="error-message" style="margin-left:0;">请选择一个参加秒杀活动的商品</p>
                                    @endif
                                    <input type="hidden" class="valid" id="goods_id" value="{{$detail['product']['id'] or ''}}">
                                </div>

                            </div>
                            <div>
                                <label class="legend">活动设置</label>
                                <div class="wrapper clearfix">
                                    <label class="label-title">
                                        <em class="required">*</em>
                                        活动名称：
                                    </label>
                                    <div class="disinblock">
                                        <input class="form-control w240 iblock valid" type="text" id="title" value="{{ $detail['seckill']['title'] or ''}}">
                                    </div>
                                    <p class="error-message" style="margin-left:101px;">请设置活动名称</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title"><em class="required">*</em>
                                    	开始时间：</label>
                                    <div class="disinblock">
                                        <input class="form-control w240 iblock valid" type="text" id="startTime" value="{{ $detail['seckill']['start_at'] or ''}}">
                                    </div>
                                    <p class="error-message" style="margin-left:101px;">请设置秒杀开始时间</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title"><em class="required">*</em>
                                    	结束时间：</label>
                                    <div class="disinblock">
                                        <input class="form-control w240 iblock valid" type="text" id="endTime" value="{{ $detail['seckill']['end_at'] or ''}}">
                                    </div>
                                    <p class="error-message" style="margin-left:101px;">请设置秒杀结束时间</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title">活动标签：</label>
                                    <div class="disinblock">
                                        <input class="form-control w240 iblock valid" type="text" id="tag" value="{{$detail['seckill']['tag'] or "秒杀"}}" maxlength="5"  />
                                    </div>
                                    <p class="help-desc">
                                        活动期间展示于商品详情的价格旁边，2至5字
                                    </p>
                                    <p class="error-message" style="margin-left:101px;">标签不能为空</p>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title box_float">类型选择：</label>
                                    @if (!empty($detail['seckill']['type']))
                                        <div class="box_flex malef4">
                                        <label class="limit-label">
                                            <input class="type_select" @if ($detail['seckill']['type'] == 0) checked @endif type="radio" name="type_select" value="0" />
                                            <span>全部</span>
                                        </label>
                                        <label class="limit-label">
                                            <input class="type_select" @if ($detail['seckill']['type'] == 1) checked @endif type="radio" name="type_select" value="1"/>
                                            <span>微商城</span>
                                        </label>
                                        <label class="limit-label">
                                            <input class="type_select" @if ($detail['seckill']['type'] == 2) checked @endif type="radio" name="type_select" value="2"/>
                                            <span>小程序</span>
                                        </label>
                                    </div>
                                    @else
                                        <div class="box_flex malef4">
                                            <label class="limit-label">
                                                <input class="type_select" checked type="radio" name="type_select" value="0" />
                                                <span>全部</span>
                                            </label>
                                            <label class="limit-label">
                                                <input class="type_select" type="radio" name="type_select" value="1"/>
                                                <span>微商城</span>
                                            </label>
                                            <label class="limit-label">
                                                <input class="type_select" type="radio" name="type_select" value="2"/>
                                                <span>小程序</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title box_float">每人限购：</label>
                                    <div class="box_flex1 malef4">
                                        <label class="limit-label">
                                            <input type="checkbox" id="is_limit" @if(!empty($detail['seckill']['limit_num']))checked @endif/>
                                            <span>开启限购</span>
                                        </label> 
                                    </div>
                                    <div class="js_span_limit_num @if(!!empty($detail['seckill']['limit_num']))none @endif">
                                        每人可购买<input type="number" min="0" class="form-control iblock input-sm w70" id="limit_num" value="{{$detail['seckill']['limit_num'] or "0"}}" />件
                                    </div>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title">订单取消：</label>
                                    <div class="disinblock">
                                        买家
                                        <input type="number" min="5" max="10" class="form-control iblock center" style="width:60px;" id="cancel_minutes" value="{{$detail['seckill']['cancel_minutes'] or "5"}}" />
                                        分钟未支付订单，订单取消
                                    </div>
                                </div>

                                <!-- 分享设置开始 -->
                                <div class="wrapper clearfix">
                                    <label class="label-title">
                                        分享标题设置：
                                    </label>
                                    <div class="disinblock">
                                        <input class="form-control w240 iblock" type="text" id="share_title" value="{{ $detail['seckill']['share_title'] or ''}}" placeholder="最多支持18个字">
                                    </div>
                                </div>

                                <div class="wrapper clearfix">
                                    <label class="label-title">
                                        分享内容设置：
                                    </label>
                                    <div class="disinblock">
                                        <textarea cols="50" rows="10" class="form-control js_coupons_name" style="width: 240px;display: inline;" id="share_desc" placeholder="最多支持50个字">{{ $detail['seckill']['share_desc'] or ''}}</textarea>
                                    </div>
                                </div>
                                <div class="wrapper clearfix">
                                    <label class="label-title flo_lef">
                                        分享页图片：
                                    </label>
                                    <div class="disinblock malef4">
                                        <input type="hidden" name="share_img" id="share_img" value="{{$detail['seckill']['share_img'] or '' }}">
                                        <div class='clearfix' style='position: relative;'>
                                            <a href="javascript:;" class="add-goods js-add-picture">
                                                <span class="@if(!empty($detail['seckill']['share_img'])) hide @endif">+添加图片</span>
                                                <div class="share_img_box @if(!!empty($detail['seckill']['share_img'])) hide @endif ">
                                                    <img src="{{ $detail['seckill']['share_img'] or '' }}" class="share_img">
                                                    <span class="delete">x</span>
                                                </div>
                                            </a>
                                            <span class="active_span" style='margin-top: 5px; color: #999999;font-size: 12px'>文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</span>
                                            <div class='example'>
                                                <div class='example_title'>示例</div>
                                                <div class='example_box'>
                                                    <div class='example_box_img'>
                                                        <img src="{{ $detail['seckill']['share_img'] or '' }}" alt="" class='@if(!!empty($detail['seckill']['share_img'])) hide @endif '>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        <!-- 许立 2018年6月28日 取消返回到前一页  -->
        <input type="button" class="btn btn-default" onclick="history.back();" value="取消" />
        <input type="button" data-id="{{$detail['seckill']['id'] or ''}}" class="btn btn-primary js-submit" value="保存" />
    </div>
    <div class="model_box">
        <div class="model_box_div">
            <p>活动进行中，修改价格跟库存，会对活动造成未知的影响，请慎重修改，点击“确认”此次修改即生效</p>
            <div class="model_box_btn">
                <button class="btn btn_queren">确认</button>
                <button class="btn btn_close">取消</button>
            </div>
        </div>
    </div>
@endsection

@section('page_js') 
    <script type="text/javascript">
        var _host = "{{ imgUrl() }}";
        var wid = "{{ session('wid') }}";
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>               
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/seckill_ppdcpeq2.js"></script>
@endsection