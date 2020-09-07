@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/bouns_list.css" />
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
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
                    <a href="javascript:void(0)">拆红包</a>
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
        <div class="nav_module clearfix">
            <ul class="nav_top clearfix pull-left">
                @foreach($tabList as $k => $v)
                    <li  class="@if ((empty(Route::input('status')) && $k == 'all') || (Route::input('status') == $k)) li_active @endif  @if (($k == 'end')) li_last @endif" >
                        <a href="{{url('/merchants/marketing/bonus/index/' . $k)}}">{{$v}}</a>
                    </li>
                @endforeach
            </ul>
            <div class="pull-right">
                <a class="f12 blue_38f pull-right-a" href="https://www.huisou.cn/home/index/helpDetail/808" target="_blank">
                    <i class="glyphicon glyphicon-question-sign green f14"></i>
                    &nbsp;查看【拆红包】设置应用及教程
                </a>
            </div>
        </div>
        <div class='bouns_add clearfix'>
            <a href="{{ URL('/merchants/marketing/bonus/add') }}">新建拆红包活动</a>
            <div class='bouns_search'>
                <div class="js-list-search ui-search-box">
                    <form>
                        <input class="txt" id='search_txt' name="title" value="" type="search" placeholder="搜索">
                    </form>
                </div>
            </div>
        </div>
        <table class='bouns_list'>
            <tr>
                <th class='list_left_text'>活动名称</th>
                <th class='list_th_2'>已拆金额</th>
                <th class='list_th_2'>剩余金额</th>
                <th class='list_th_2'>
                    拆分成功<span class='th_tip_span'>?</span>
                    <div class='th_tip_div'>包括进行中、拆分红包成功</div>
                </th>
                <th class='list_th_2'>
                    剩余库存<span class='th_tip_span'>?</span>
                    <div class='th_tip_div'>剩余红包雨库存</div>
                </th>
                <th class='list_th_2'>
                    已使用<span class='th_tip_span'>?</span>
                    <div class='th_tip_div' style="top:-52px;left: -15px">拆分红包后使用领取优惠券的数量</div>
                </th>
                <th class='list_th_2'>活动时间</th>
                <th class='list_th_2'>状态</th>
                <th class='list_right_text'>操作</th>
            </tr>
            <tbody>
                @forelse($bonuses as $v)
                    <tr>
                        <td class='list_left_text'>{{$v['title']}}</td>
                        <td>{{$v['received_amount']}}</td>
                        <td>{{$v['left_amount_string']}}</td>
                        <td>{{$v['received_count']}}</td>
                        <td>{{$v['left']}}</td>
                        <td>{{$v['used_count']}}</td>
                        <td>{{$v['start_at']}}至{{$v['end_at']}}</td>
                        <td>{{$v['status_string']}}</td>
                        <td class='list_right_text'>
                            <a href="/merchants/marketing/bonus/edit/{{$v['id']}}">编辑</a>-
                            <!--许立 2018年08月07日 进行中的活动才有推广按钮-->
                            @if ($v['status_string'] == '进行中')
                                <!--add by 韩瑜 2018-8-3 添加推广按钮-->
                                <a class='share_activity' href="javascript:void(0)">推广</a>-
                            @endif
                            @if ($v['status'] != 2)
                            <!--end-->
                            <a data-id='{{$v['id']}}' class='stop_activity' href="javascript:void(0)">停止活动</a>-
                            @endif
                            <a data-id='{{$v['id']}}' class='del_activity' href="javascript:void(0)">删除</a>
                        </td>
                    </tr>
                @empty
                    <tr style="border-bottom: none;">
                        <td colspan="8" style="text-align: left;color: #333333;padding-left: 10px;">暂无数据</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- 分页 -->
        <div class="text-right" style="text-align: right;">
            {!! $pageHtml !!}
        </div>
    </div>
    <div class='tip_box tip_stop'>
        <div class="tip_box_div">
            <div class='clearfix tip_title'>
                <span class='fl'>提示</span>
                <span class='close_btn_stop'>x</span>
            </div>
            <div class='tip_cont'>
                确定停止活动么？活动停止后，不允许重新开始该拆红包活动，进行中的可继续参与，已成功拆的优惠券在有效期内仍可以使用！
            </div>
            <div class='tip_btn'>
                <span class='submit_btn submit_stop_btn'>确定</span>
                <span class='close_btn close_btn_stop'>取消</span>
            </div>
        </div>
    </div>
    <div class='tip_box tip_del'>
        <div class="tip_box_div" style='height: 200px;margin-top: -100px; width: 300px;margin-top: -150px;'>
            <div class='clearfix tip_title'>
                <span class='fl'>提示</span>
                <span class='close_btn_del'>x</span>
            </div>
            <div class='tip_cont'>
                确定要删除这个拆红包活动么？
            </div>
            <div class='tip_btn'>
                <span class='submit_btn submit_del_btn'>确定</span>
                <span class='close_btn close_btn_del'>取消</span>
            </div>
        </div>
    </div>
    <!-- 推广优惠券 -->
    <!--add by 韩瑜 2018-8-6-->
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
	                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="{{config('app.url')}}shop/index/{{session('wid')}}" />
	                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">商品页二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
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
	                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="pages/index/index" />
	                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">小程序二维码</label>
	                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="qrcode">
                                        <div class="qr_img_xcx"></div>
                                        <div class="clearfix qrcode-links">
                                            <a class="down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
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
    <script src="{{ config('app.source_url') }}static/js/require.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/bouns_list.js"></script>
    <script type="text/javascript">
        var host = "{{config('app.url')}}";
        var wid = "{{ session('wid') }}";
    </script>
@endsection