@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/seckill_7glfwzmk.css" />
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
            	<!-- <a class="nav_module_blank" href="{{ config('app.url') }}home/index/detail/627/help" target="_blank">秒杀使用方法</a> -->
            </div> 
        </div> 
        <!-- search 开始 -->
        <div class="mb-15 clearfix pr">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a class="hs-btn hs-btn-success" href="{{ URL('/merchants/marketing/seckill/set') }}">新建秒杀</a>
                    <a class="hs-btn hs-btn-warning batch-delet" href="##">批量删除</a>
                </div> 
            </div>
        </div>
        <br />
        <!-- search 结束 -->
        <!-- 秒杀列表 开始 -->
        <div class="main_content">
            <ul class="main_content_title">
                <li>选择</li>
                <li>活动名称</li>
                <li>有效时间</li>
                <li>活动状态</li>
                <li>收藏数</li>
                <li class="text-right">操作</li>
            </ul>
            @forelse($list as $v)
                <ul class="data_content">
                	<li><input class="batchChoose" type="checkbox" value="{{$v['id']}}" /></li>
                    <li class="blue">
                        <a href="/shop/seckill/preview/{{$v['id']}}" target="_blank">{{$v['title']}}</a></li>
                    <li>{{$v['start_at']}} 至 {{$v['end_at']}}</li>
                    @if($v['status'] == '已结束' || $v['status'] == '已失效')
                        <li class="gray1">
                    @else
                        <li>
                    @endif
                            {{$v['status']}}
                        </li>
                    <li>{{$v['favoriteCount']}}</li><!--收藏数-->
                    <li class="text-right pr">
                        <!-- 许立 2018年6月28日 未开始和进行中的活动可以编辑 -->
                        @if ($v['status'] == '未开始' || $v['status'] == '进行中')
                            <a href="/merchants/marketing/seckill/set/{{$v['id']}}">编辑</a>
                        @else
                            <a href="/merchants/marketing/seckill/detail/{{$v['id']}}">查看</a>
                        @endif
                        @if($v['invalidate_at'] == '0000-00-00 00:00:00' && $v['end_at'] > date('Y-m-d H:i:s'))
                        -<a class="invalid" data-id="{{$v['id']}}" href="javascript:void(0);">
                            使失效<span style="color:#ff6600">[?]</span>
                        </a>
                        @endif

                        <!-- 结束或者失效 显示删除按钮 Herry 20171017 -->
                        @if($v['end_at'] <= date('Y-m-d H:i:s') || $v['invalidate_at'] != '0000-00-00 00:00:00')
                        -<a class="delete" data-id="{{$v['id']}}" href="javascript:void(0);">删除</a>
                        @endif
                        <a href="javascript:void(0)" class="seckill_url blue_38f" data-url="{{$v['url']}}" data-id="{{$v['id']}}">推广</a>
                    </li>
                </ul>
            @empty
                暂无数据
            @endforelse
        </div>
        <!-- 秒杀列表 结束 -->
        <!-- 分页 -->
        <div class="" style="text-align: right;">
        	{{ $pageHtml }}        	
        </div>
    </div>
@endsection

@section('other')
    <!-- 推广秒杀 -->
    <div class="widget-promotion widget-promotion1" style="display: none;">
        <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
            <li class="">秒杀二维码</li>
            <li class="active">秒杀链接</li>
        </ul>
        <div class="widget-promotion-content js-tabs-content">
            <div class="js-tab-content" style="display: none;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="js-qrcode-content">
                            <div class="alert">扫一扫，在手机上查看并分享
                                <!--<a class="new-window qrcode-help pull-right" href="javascript:;" target="_blank">帮助</a>-->
                            </div>
                            <div class="qrcode-content" style="background-color:white;height:100px;">
                                <div class="qrcode-left-sidebar js-qrcode-left-sidebar">
                                    <div class="qrcode-left-lists">
                                        <ul>
                                            <li class="clearfix active">直接进入秒杀</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                    <div class="text-center qrcode">
                                        <div class="qr_img"></div>
                                        <p>扫码后直接访问秒杀</p>
                                        <div class="clearfix qrcode-links">
                                            <a class="pull-left down_qrcode" href="javascript:void(0);">下载二维码</a>
                                        </div>
                                    </div>
                                    <div class="text-center qrcode-info">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="js-tab-content" style="display: block;">
                <div>
                    <div class="widget-promotion-main">
                        <div class="alert">分享才有更多人看到哦</div>
                        <div class="widget-promotion-content">
                            <label>秒杀详情页链接</label>
                            <div class="input-append">
                                <input type="text" class="form-control link_copy iblock" style="vertical-align: middle;" readonly="" value="" />
                                <a class="btn js-btn-copy code-copy-a" data-clipboard-text="">复制</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
	<script src="{{ config('app.source_url') }}static/js/require.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/seckill_7glfwzmk.js"></script>
@endsection