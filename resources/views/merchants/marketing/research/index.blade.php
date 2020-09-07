@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/message_list.css" />
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
                    <a href="javascript:void(0)">
                        @if ($type == 0)
                            在线报名
                        @elseif ($type == 1)
                            在线预约
                        @elseif ($type == 2)
                            在线投票
                        @endif
                    </a>
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
                            <a href="{{url('/merchants/marketing/researches/' . $type . '/' . $k)}}">{{$v}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- <a class="nav_module_blank" href="javascript:void(0);" target="_blank">查看【投票活动】使用教程</a> -->
        </div>
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <div class="mb-15 clearfix pr">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a class="btn btn-success " id="add_page" data-type="{{$type}}" href="javascript:void(0);">
                        @if ($type == 0)
                            新建在线报名
                        @elseif ($type == 1)
                            新建在线预约
                        @elseif ($type == 2)
                            新建在线投票
                        @endif
                    </a>
                    <!-- {{ URL('/merchants/marketing/researchAdd') }} -->
                    <a id='close_all' class="btn" style='background: #f8f8f8;border: 1px solid #ddd;color: #333333' href="javascript:;">批量删除</a>
                </div>
                <div style="position: relative;">
                    <div class="js-list-search ui-search-box">
                        <form>
                            <input class="txt" id='search_txt' name="title" value="" type="search" placeholder="搜索">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- search 结束 -->
        <!-- 拼团列表 开始 -->
        {{--@if($voteData['data'])--}}
            <div class="main_content">
                <ul class="main_content_title">
                    <li>选择</li>
                    <li>活动名称</li>
                    <li>创建时间</li>
                    <li>状态</li>
                    <li>参与人数</li>
                    <li class="text-right">操作</li>
                </ul>
                @forelse($list as $v)
                    <ul class="data_content">
                        <li><input class='close_inp_id' type="checkbox" value='{{$v['id']}}'></li>
                        <li class="blue">{{$v['title']}}</li>
                        <li>{{$v['start_at']}} 至 {{$v['end_at']}}</li>
                        <li class="gray1">{{$v['status']}}</li>
                        <li>{{$v['partakeCount']}}</li>
                        <li class="text-right pr">
                            <a href="/merchants/marketing/researchEdit/{{$v['id']}}">编辑</a>-
                            @if($v['status'] != '进行中')
                                <a href="javascript:void(0);" class="delBtn" data-id="{{$v['id']}}">删除</a>-
                            @endif
                            @if($v['status'] == '进行中')
                                <a href="javascript:void(0);" class="extension" data-id="{{$v['id']}}">推广</a>-
                            @endif
                            @if ($v['invalidate_at'] == '0000-00-00 00:00:00')
                                <a href="javascript:void(0);" class="closeBtn" data-id="{{$v['id']}}">使失效</a>-
                            @endif
                            <a href="javascript:void(0);" class='look' data-id="{{$v['id']}}">查看参与人</a>
                        </li>
                    </ul>
                @empty
                    <div class="no_data">还没有相关数据</div>
                @endforelse
            </div>
        {{--@else--}}
            {{--<div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据--}}
            {{--</div>--}}
    {{--@endif--}}
    <!-- 拼团列表 结束 -->
        <!-- 分页 -->
        <div class="text-right">
            {!! $pageHtml !!}
        </div>
    </div>
    {{--参与人详情--}}
    <div class='particulars hide'>
        <div class='par_conten'>
            <h3 class='par_title'>参与人详情</h3>
            <p class='par_time'>
                <span>#3</span>
                <span>2018-05-09 18:31:13</span>
            </p>
            <div class='par_box'>
                <ul class='par_ul'>
                    <li>
                        <span>商品好货</span>
                        <span>/</span>
                    </li>
                    <li>
                        <span>数字</span>
                        <span>11</span>
                    </li>
                    <li>
                        <span>文本框</span>
                        <span class='per_text'>
                            把心给了你，把世界都给了你!
                        </span>
                    </li>
                    <li>
                        <span>性别</span>
                        <span>男</span>
                    </li>
                    <li>
                        <span>生日</span>
                        <span>2018-05-10</span>
                    </li>
                    <li>
                        <span>姓名</span>
                        <span>啦啦</span>
                    </li>
                    <li>
                        <span>手机</span>
                        <span>12345678900</span>
                    </li>
                    <li>
                        <span>邮箱</span>
                        <span>12345678900@139.com</span>
                    </li>
                    <li>
                        <span>公司</span>
                        <span>没有公司</span>
                    </li>
                    <li>
                        <span>部门</span>
                        <span>没有</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class='par_close'>x</div>
    </div>
    <!-- 微页面选择模板弹窗 -->
    <div class="widget-feature-template modal in" aria-hidden="false">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3 class="title">选择页面模版</h3>
        </div>
        <ul class="widget-feature-template-filter">
            <li class="active">
                <a href="javascript:;" class="js-filter" data-type="all">所有模版</a>
            </li>
        </ul>
        <div class="modal-body">
            <ul class="widget-feature-template-list clearfix"></ul>
        </div>
        <div class="modal-footer"></div>
    </div>
    <!-- 微页面选择模板弹窗 -->
    <!-- add by 赵彬 2018-8-9-->
    <!-- 推广 -->
    <div class="widget-promotion widget-promotion1" style="display:none;">
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
	                            <label>微页面链接</label>
	                            <div class="input-append">
	                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="{{config('app.url')}}shop/index/{{session('wid')}}" />
	                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
	                            </div>
	                        </div>
	                        <div class="widget-promotion-content">
	                            <label class="label-b">微页面二维码</label>
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
	                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" />
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
@endsection

@section('page_js')
    <script src="{{ config('app.source_url') }}static/js/require.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/message_list.js"></script>
    <script type="text/javascript">
        var host = "{{config('app.url')}}";
        var wid = "{{ session('wid') }}";
    </script>
@endsection