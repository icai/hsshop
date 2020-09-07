@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
    <!--bootstrap datatimepicker时间插件-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/> 
    <!-- 选择商品样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_z7zce6ix.css" />
    <style type="text/css">
        input.validate_hidden { position:absolute; height:0; width:0; border:0; }
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
                    <a href="{{ URL('/merchants/marketing/coupons') }}">优惠券</a>
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
@endsection
@section('content')
    <div class="content" style="padding:15px">
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix">
            <!-- 左侧 开始 -->
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    @foreach($tabList as $k => $v)
                        <li @if ((empty(Route::input('status')) && $k == 'all') || (Route::input('status') == $k)) class="hover" @endif>
                            <a href="{{url('/merchants/marketing/coupons/' . $k)}}">{{$v}}</a>
                        </li>
                    @endforeach
                </ul>
                <!-- 导航 结束 -->
            </div>
            <!-- 左侧 结算 -->
            <!-- 右边 开始-->
            <div class="pull-right">
                <a class="f12 blue_38f" href="{{URL('/home/index/detail/40')}}" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>
                    查看【优惠券】设置及应用教程
                </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- app设计模块 开始 -->
        <div class="app_design">
            <!-- 设计主体 开始 -->
            <form id="addCouponForm" role="form" method="post"
                  @if(!empty($coupon['id']))
                  action="{{ URL('/merchants/marketing/coupon/xcxCouponAdd/' . $coupon['id']) }}"
                  @else
                  action="{{ URL('/merchants/marketing/coupon/xcxCouponAdd') }}"
                  @endif
            >
                {{ csrf_field() }}
                <input type="hidden" name="wid" value="{{ session('wid') }}" id="wid"/>
                <input type="hidden" name="id" value="{{ $coupon['id'] ?? 0 }}" id="couponId"/>
            <div class="design_body">
                <!-- 预览模块 开始 -->
                <div class="app_preview">
                    <!-- 预览头部 开始 -->
                    <div class="preview_header"></div>
                    <!-- 预览头部 结束 -->
                    <!-- 预览主体 开始 -->
                    <div class="preview_body">
                        <!-- app标题区域 开始 -->
                        <div class="app_header">
                            <p class="app_title">优惠券</p>
                        </div>
                        <!-- app标题区域 结束 -->
                        <!-- app主体区域 开始 -->
                        <div class="app_body">
                            <!-- 预览组 开始 -->
                            <div class="preview_group editting">
                                <!-- 组描述 开始 -->
                                <div class="group_des gray_999 center">(微商城优惠券)</div>
                                <!-- 组描述 结束 -->
                                <!-- 预览内容 开始 -->
                                <div class="group_content">
                                    <!-- 卡券样式 开始 -->
                                    <div class="coupons_module">
                                        <div class="box_start">
                                            <div class="coupons_title box_flex1">{{ $coupon['title'] ?? '优惠券标题' }}</div>
                                            <!-- <p class="js_share f14">分享</p> -->
                                        </div>
                                        <!-- 面值 -->
                                        <p class="coupons_denomination center f28">￥
                                            @if(isset($coupon['is_random']) && $coupon['is_random'] == 1)
                                                {{$coupon['amount']}} ~ {{$coupon['amount_random_max']}}
                                            @elseif(!empty($coupon['amount']))
                                                {{$coupon['amount']}}
                                            @else
                                                0.00
                                            @endif
                                        </p>
                                        <!-- 限制 -->
                                        <p class="coupons_limit f13 center ptb10">
                                            @if(!empty($coupon['is_limited']))
                                                订单满 {{$coupon['limit_amount'] ?? '0'}} 元(不含运费)
                                            @else
                                                不限制
                                            @endif
                                        </p>
                                        <!-- 有效日期 -->
                                        <div class="coupons_data f12 display_box">
                                            有效日期:<span class="start_times">{{$coupon['start_at'] ?? '2017-01-01 00:00:00'}}</span> - <span class="end_times">{{$coupon['end_at'] ?? '2017-01-02 00:00:00'}}</span>
                                        </div>
                                    </div>
                                    <!-- 卡券样式 结束 -->
                                    <!-- 卡券描述 开始 -->
                                    <div class="group_des">使用说明</div>
                                    <!-- 卡券描述 结束 -->
                                    <!-- 卡券说明 -->
                                    <p class="coupons_explain">{{$coupon['description'] ?? '暂无说明'}}</p>
                                </div>
                                <!-- 预览内容 结束 -->
                                <!-- 操作模块 开始 -->
                                <div class="actions_module" style="display:none">
                                    <span class="edit_btn action_btn">编辑</span>
                                </div>
                                <!-- 操作模块 结束 -->
                            </div>
                            <!-- 预览组 结束 -->
                            <!-- 预览组 开始 -->
                            <div class="weixin_group preview_group editting @if(empty($coupon['is_sync_weixin'])) no @endif">
                                <!-- 组描述 开始 -->
                                <div class="group_des gray_999 center">(微信卡券包)</div>
                                <!-- 组描述 结束 -->
                                <!-- 预览内容 开始 -->
                                <div class="group_content">
                                    <!-- 卡券样式 开始 -->
                                    <div class="card_module {{$weixinCoupon['color'] or 'Color010'}}">
                                        <div class="box_start">
                                            <!--<img class="shop_logo" src="">-->
                                            <div class="card_title box_flex1 f14">微商城</div>
                                        </div>
                                        <!-- 卡券名称 -->
                                        <p class="card_name center f24">{{$weixinCoupon['title'] ?? '微信卡券标题'}}</p>
                                        <!-- 限制 -->
                                        <p class="card_limit center f12">{{$weixinCoupon['subtitle'] ?? '微信卡券副标题'}}</p>
                                        <!-- 有效日期 -->
                                        <div class="card_times f12 gray_e8 display_box">
                                            有效日期:<span class="start_times">{{$coupon['start_at'] ?? '2017-01-01 00:00:00'}}</span> - <span class="end_times">{{$coupon['end_at'] ?? '2017-01-02 00:00:00'}}</span>
                                        </div>
                                    </div>
                                    <!-- 卡券样式 结束 -->
                                    <!-- 卡券码 -->
                                    <div class="card_code center">
                                        <p class="f24">H7MR XXXX ZKSM</p>
                                        <p class="gray_999 f12">请在会搜云微商城购物使用</p>
                                    </div>
                                </div>
                                <!-- 预览内容 结束 -->
                                <!-- 操作模块 开始 -->
                                <div class="actions_module" style="display:none">
                                    <span class="edit_btn action_btn">编辑</span>
                                </div>
                                <!-- 操作模块 结束 -->
                            </div>
                            <!-- 预览组 结束 -->
                        </div>
                        <!-- app主体区域 结束 -->
                    </div>
                    <!-- 预览主体 结束 -->
                </div>
                <!-- 预览模块 结束 -->
                <!-- 编辑模块 开始 -->
                <div class="app_edit">
                    <div class="edit_form">
                        <!-- 编辑模块 开始  -->
                        <div class="edit_module">
                            <!-- 箭头 -->
                            <div class="module_arrow"></div>
                            <!-- 模块头 开始 -->
                            <h5 class="module_title">优惠券基础信息</h5>
                            <!-- 模块头 结束 -->
                            <!-- 优惠券名称 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>优惠券名称：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <input class="js_coupons_name form-control" maxlength="10" type="text" value="{{ $coupon['title'] ?? '' }}" name="title" placeholder="最多支持10个字" />
                                    </div>
                                </div>
                            </div>
                            <!-- 发放总量 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>发放总量：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <div class="box_start has_unit">
                                            <input class="form-control small" type="text" name="total" value="{{ $coupon['total'] ?? '' }}" /><div class="unit">张</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 面值 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>面值：</div> 
                                <div class="group_content">
                                    <div class="box_flex1">
                                        <div class="box_start">
                                            <div class="form-group">
                                                <input class="js_lowerLimit form-control small"  type="text" name="amount" value="{{ $coupon['amount'] ?? '' }}" />
                                            </div>
                                            <div class="js_random @if(empty($coupon['is_random'])) no @endif">
                                                <div class="form-group">
                                                    <div class="box_start">
                                                        &nbsp;&nbsp;至&nbsp;&nbsp;
                                                        <input class="js_upperLimit form-control small" type="text" name="amount_random_max" value="{{ $coupon['amount_random_max'] ?? '' }}" />
                                                    </div>
                                                </div>
                                            </div>&nbsp;&nbsp;
                                            <label class="display_box">
                                                <input class="js_random_btn" type="checkbox" name="is_random" value="1" @if(!empty($coupon['is_random'])) checked @endif />&nbsp;&nbsp;随机
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 使用门槛 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>使用门槛：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <label class=" no_use">
                                            <input class="use_restrictio unlimited" type="radio" name="is_limited" value="0" @if(empty($coupon['is_limited'])) checked @endif /> 不限制
                                        </label>
                                        <div class="box_start box_start line-ht">
                                            <label>
                                                <input class="use_limit use_restrictio" type="radio" name="is_limited" value="1" @if(!empty($coupon['is_limited'])) checked @endif /> 满
                                            </label>&nbsp;
                                            <input class="js_limit form-control small" type="text" name="limit_amount" value="{{ !empty($coupon['limit_amount']) ? $coupon['limit_amount'] : '' }}" />&nbsp;元可使用
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <!-- 模块头 开始 -->
                            <h5 class="module_title">基本规则</h5>
                            <!-- 模块头 结束 -->
                            <!-- 每人限领 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>每人限领：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <select name="quota">
                                            <option value="0" selected>不限张</option>
                                            @for($i = 1; $i < 6; $i++)
                                            <option value="{{$i}}" @if(!empty($coupon['quota']) && $i == $coupon['quota']) selected @endif>{{$i}}张</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- 生效时间 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>生效时间：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <div class='input-group w240'>
                                            <input id='startTime' type='text' class="form-control" name="start_at" value="{{$coupon['start_at'] ?? ''}}" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 过期时间 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>过期时间：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <div class='input-group w240'>
                                            <input id='endTime' type='text' class="form-control" name="end_at" value="{{$coupon['end_at'] ?? ''}}" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- 推广设置 -->
                            <div class="edit_group">
                                <div class="group_name">推广设置：</div> 
                                <div class="group_content">
                                    <div class="box_flex1 box_start">
                                        <label>
                                            <input type="checkbox" name="is_share" value="1" @if(!isset($coupon['is_share']) || $coupon['is_share'] == 1) checked @endif >允许分享领取链接
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- 分享设置 -->
                            <div class="edit_group">
                                <div class="group_name">分享标题设置：</div>
                                <div class="form-group box_flex1">
                                    <input class="js_coupons_name form-control" type="text" value="{{ $coupon['share_title'] ?? '' }}" name="share_title" placeholder="最多支持18个字" style="width:265px;height: 31px;"/> 
                                </div>
                            </div>

                            <div class="edit_group">
                                <div class="group_name">分享内容设置：</div>
                                <div class="form-group box_flex1"> 
                                    <textarea cols="50" rows="10" class="js_coupons_name form-control" style="width: 265px;" name="share_desc" placeholder="最多支持50个字">{{ $coupon['share_desc'] ?? '' }}</textarea> 
                                </div>
                            </div>

                            <div class="edit_group">
                                <div class="group_name">分享页图片：</div>
                                <div class="form-group box_flex1"> 
                                    <input type="text" name="share_img" class="validate_hidden" value="{{ $coupon['share_img'] ?? '' }}">
                                    @if(!empty($coupon['share_img']))
                                    <span class="pr iblock"> 
                                        <img id="img_share_img" src="{{ config('app.source_url') }}{{ $coupon['share_img'] ?? '' }}" style="width: 80px;height: 80px;" class="share_img">
                                        <span class="share_img_close">×</span> 
                                    </span> 
                                    @else
                                    <span class="pr iblock hide"> 
                                        <img id="img_share_img" src="" style="width: 80px;height: 80px;" class="share_img">
                                        <span class="share_img_close">×</span>   
                                    </span> 
                                    @endif
                                    <a href="javascript:;" class="add-goods js-add-picture">+加图</a>
                                </div>
                            </div>

                            <!-- 可使用商品 -->
                            <div class="edit_group">
                                <div class="group_name"><span class="red">*&nbsp;</span>可使用商品：</div> 
                                <div class="group_content">
                                    <div class="form-group box_flex1">
                                        <div class="box_start">
                                            <label class="box_start">
                                                <input class="goods_range form-control" type="radio" name="range_type" value="0" data-tip="all_tip" @if(empty($coupon['range_type'])) checked @endif />&nbsp;&nbsp;全店通用&nbsp;&nbsp;
                                            </label>
                                            <label class="box_start">
                                                <input class=" goods_appoint form-control" type="radio" name="range_type" value="1" data-tip="appoint_tip" @if(!empty($coupon['range_type']) && $coupon['range_type'] == 1) checked @endif />&nbsp;&nbsp;指定商品&nbsp;&nbsp;
                                                <!--<input class="goods_range goods_appoint form-control" type="radio" name="range_type" value="1" data-tip="appoint_tip" @if(!empty($coupon['range_type']) && $coupon['range_type'] == 1) checked @endif />&nbsp;&nbsp;指定商品&nbsp;&nbsp;-->
                                            </label>
                                        </div>
                                        <!-- 指定商品添加 -->
                                        <div class="appoint_module @if(empty($coupon['range_type'])) no @endif">
                                            <table class="table table-bordered table-hover">
                                                <tr class="active">
                                                    <td>商品名称</td>
                                                    <td>操作</td>
                                                </tr>
                                                @foreach($products as $v)
                                                    <tr>

                                                        <td>
                                                        <a class="checked" href="javascript:;" data-id="{{$v['id']}}">{{$v['title']}}</a>
                                                        </td>
                                                        <td><a class="del_goods blue_38f f12" data-id="{{$v['id']}}" href="javascript:void(0);">删除</a></td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            <div class="js_add_goods blue_38f f12" data-toggle="modal"> + 添加商品</div>
                                            <span class="error">指定商品不能为空</span>
                                        </div>
                                        <div class="all_tip tip_des">保存后，不能更改成指定商品</div>
                                        <div class="appoint_tip tip_des no">指定商品可用时，订单金额不包含运费</div>
                                        <label class="box_start">
                                            <input type="checkbox" name="only_original_price" value="1" />&nbsp;&nbsp;仅原价购买商品时可用&nbsp;&nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- 使用说明 -->
                            <div class="edit_group">
                                <div class="group_name">使用说明：</div>
                                <div class="group_content">
                                    <div class="box_start box_flex1">
                                        <textarea class="" name="description" cols="30" rows="2" placeholder="填写活动的详细说明，支持换行;">{{$coupon['description'] ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 编辑模块 结束 -->
                    </div>
                </div>
                <!-- 编辑模块 结束 -->
            </div>
            <!-- 设计主体 结束 -->
            <!-- 设计底部 开始 -->
            <div class="design_bottom display_box">
                <button type="submit" class="submit_btn btn btn-primary">保存</button>
                <!-- <button type="button" class="btn btn-default">预览</button> -->
            </div>
            </form>
            <!-- 设计底部 结束 -->
        </div>
        <!-- app设计模块 结束 -->
    </div>
@endsection

@section('page_js')
    <!-- 时间插件 -->
    <script>
        var _host = "{{ config('app.source_url') }}";
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>

    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <!-- 表单验证插件js文件 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/zh_CN.js"></script>
    <script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/xcxcouponadd.js"></script>
@endsection