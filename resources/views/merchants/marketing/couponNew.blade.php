@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpj42f2.css" />
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
                    <a href="javascript:void(0);">优惠券</a>
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
                <a class="f12 blue_38f pull-right-a" href="https://www.huisou.cn/home/index/helpDetail/715" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14"></i>
                    &nbsp;查看【优惠券】设置及应用教程
                </a>
            </div>
            <!-- 右边 结束 -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- 列表模块 开始 -->
        <a class="btn btn-success mgb15" href="{{ URL('/merchants/marketing/coupon/set') }}" id="addCoupon">新建优惠券</a>
        <!-- 列表 开始 -->
        <table class="table table-hover f13">
          
            <tr class="table-active">
                <td>优惠券名称</td>
                <td>价值(元)</td>
                <td>领取限制</td>
                <td>有效期</td>
                <td>领取人/次</td>
                <td>已使用</td>
                <td>操作</td>
            </tr>
            @forelse ( $list as $v )
                <tr>
                    <td>{{$v['title']}}</td>
                    <td>
                        <p>
                            @if ($v['is_random'])
                                {{number_format($v['amount'], 2)}} ~ {{number_format($v['amount_random_max'], 2)}}
                            @else
                                {{number_format($v['amount'], 2)}}
                            @endif
                        </p>
                        <p class="gray_999">
                            @if ($v['limit_amount'] > 0)
                                最低消费: ￥{{number_format($v['limit_amount'], 2)}}
                            @endif
                        </p>
                    </td>
                    <td>
                        <p>@if ($v['quota']) 一人{{$v['quota']}}张 @else 不限张数 @endif</p>
                        <p class="gray_999">库存：{{$v['left']}}</p>
                    </td>
                    <td>
                        @if ($v['expire_type'] == 1)
                            <p>领到券当日开始{{$v['expire_days']}}天内有效</p>
                        @elseif ($v['expire_type'] == 2)
                            <p>领到券次日开始{{$v['expire_days']}}天内有效</p>
                        @else
                            <p>{{$v['start_at']}} 至</p>
                            <p>{{$v['end_at']}}</p>
                        @endif
                    </td>
                    <td>
                        <a class="blue_38f" href="{{URL('/merchants/marketing/couponReceiveList/' . $v['id'])}}">{{$v['memberNum']}}</a> / {{$v['receiveNum']}}
                    </td>
                    <td><a class="blue_38f" href="{{URL('/merchants/marketing/couponReceiveList/' . $v['id'] . '/used')}}">{{$v['useNum']}}</a></td>
                    <td>
                        @if ($v['invalid_at'])
                            <a class="gray_999" href="javascript:void(0);">已失效</a>
                        @elseif (strtotime($v['end_at']) > time())
                            <a class="blue_38f" href="{{URL('/merchants/marketing/coupon/set/'.$v['id'])}}">编辑</a>
                        @else
                            <a class="blue_38f" href="{{URL('/merchants/marketing/coupon/set/'.$v['id'])}}">编辑</a>
                        @endif

                        @if (empty($v['invalid_at']) && (($v['expire_type'] == 0 && strtotime($v['end_at']) > time()) || $v['expire_type'] > 0))
                            <a class="invalid_btn blue_38f" href="javascript:void(0);">使失效</a>
                        @endif

                        @if ($v['is_share'])
                            <!--<a class="blue_38f" href="javascript:void(0);">推广</a>-->
                        @endif
                            <a href="javascript:void(0)" class="coupon_receive_url blue_38f" data-url="{{$v['receiveUrl']}}" data-id="{{$v['id']}}" data-title="{{$v['title']}}" data-amount="@if($v['is_random'] == 1) {{$v['amount']}}~{{$v['amount_random_max']}}@else {{$v['amount']}} @endif">推广</a>

                        @if ($v['invalid_at'] || ($v['expire_type'] == 0 && strtotime($v['end_at']) <= time()))
                            <a class="delete_btn blue_38f" href="javascript:void(0);">删除</a>
                        @endif
                        <input type="hidden" value="{{$v['id']}}"/>
                    </td>
                </tr>
            @empty
            <tr>
                 <div class="null-data">
                    暂无数据
                </div>
            </tr>
            @endforelse
        </table>
        <div class="pagehidden">        	
        	{{ $pageHtml }}
        </div>
        <!-- 列表 结束 -->
        <!-- 列表模块 结束 -->
    </div> 
@endsection
@section('other')
    <!-- 推广优惠券 -->
    <!--updata by 韩瑜 2018-8-6-->
    <div class="widget-promotion widget-promotion1" style="display: none;">
        <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
            <li class="wsc_code active">微商城</li>
            <li class="xcx_code">小程序</li>
        </ul>
        <div class="widget-promotion-content js-tabs-content">
        	<!--微商城-->
            <div class="js-tab-content-wsc" style="display: block;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="js-qrcode-content">
                            <div class="widget-promotion-content">
	                            <label>商品页链接</label>
	                            <div class="input-append">
	                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="" />
	                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">商品页二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
                                        </div>
                                    </div>
                               	</div>
	                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--小程序-->
            <div class="js-tab-content-xcx" style="display: none;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="js-qrcode-content">
                            <div class="widget-promotion-content">
	                            <label>小程序链接</label>
	                            <div class="input-append">
	                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="" />
	                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">小程序二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img_xcx"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
                                        </div>
                                    </div>
                               </div>
	                        </div>           	
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end-->
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>
@endsection