    
@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base3.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_ozhkz7jv.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')




    <!-- 左边 开始 -->

    <!-- 左边 结束 -->
    <!-- 中间 开始 -->
    <div class="middle">
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
        <!-- 主体 开始 -->
        <div class="main">
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
                        <a class="f12 blue_38f" href="javascript:void(0);" target="_blank">
                            <i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>查看【销售员】使用手册
                        </a>
                    </div>
                    <!-- 右边 结束 -->
                </div>
                <!-- 导航模块 结束 -->
                <!-- 列表模块 开始 -->
                <!-- 列表 开始 -->


                <div class="app__content js-app-main">
                    <div class="widget-list">
                        <div class="js-list-filter-region clearfix ui-box" style="position: relative;">
                            <div class="widget-list-filter">
                                <!--<div>
                                    <div class="js-list-search ui-search-box">
                                        <input class="txt" type="text" placeholder="搜索" value="">
                                    </div>
                                </div>-->
                                <h2 class="receive-list-title">

                                    <span>
                                        @if($status == 'received')
                                        已领取列表
                                        @else
                                        已使用列表
                                        @endif
                                    </span> <span class="gray"> | </span>

                                    <span class="orange">{{$couponTitle}}</span>
                                </h2>
                            </div>
                        </div>
                        <div class="ui-box">
                            <table class="ui-table ui-table-list" style="padding: 0px;">
                                <thead class="js-list-header-region tableFloatingHeaderOriginal">
                                    <tr class="widget-list-header">
                                        <th class="cell-20 text-left">
                                            客户
                                        </th>
                                        <th class="cell-10">
                                            性别
                                        </th>
                                        <th class="cell-10">
                                            领取时间
                                        </th>
                                        <th class="cell-10">
                                            面额(元)
                                        </th>
                                        <th class="cell-10">
                                            使用时间
                                        </th>
                                        <th class="cell-10">
                                            订单详情
                                        </th>
                                        <th class="cell-10">
                                            状态
                                        </th>

                                    </tr>
                                </thead>
                                <tbody class="js-list-body-region">

                                @forelse ( $list as $v )
                                    <tr class="widget-list-item">

                                        <td>
                                            <div class="fans-box clearfix">
                                                <div class="fans-avatar">
                                                    <img src="{{$v['avatar']}}">
                                                </div>
                                                <div class="fans-msg">

                                                    <p>{{$v['nickname']}}</p>
                                                    <p class="gray">{{$v['mobile']}}</p>

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="gray">
                                                @if ($v['gender'] == 1)
                                                    男
                                                @elseif ($v['gender'] == 2)
                                                    女
                                                @else
                                                    未知
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{$v['created_at']}}</td>
                                        <td>
                                            {{$v['amount']}}
                                        </td>
                                        <td>
                                            @if ($v['status'] > 0)
                                                {{$v['updated_at']}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($v['oid'])
                                                <a href="{{url('/merchants/order/orderDetail/' . $v['oid'])}}" target="_blank">详情</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <p class="gray">
                                                @if ($v['status'] == 1)
                                                    使用中
                                                @elseif ($v['status'] == 2)
                                                    已使用
                                                @else
                                                    未使用
                                                @endif
                                            </p>

                                        </td>
                                    </tr>
                                @empty
                                    暂无数据
                                @endforelse

                                </tbody>
                            </table>
                            {{ $pageHtml }}
                        </div>
                    </div>
                </div>


                <!-- 列表 结束 -->
                <!-- 列表模块 结束 -->
            </div>
            <!--底部开始-->
            <div id="app-footer" class="footer">
                <p>
                    <a class="logo" href="{{ URL('/') }}" target="_blank"></a>
                </p>
            </div>
            <!--底部结束-->
        </div>
        <!-- 主体 结束 -->
    </div>
    <!-- 中间 结束 -->

    <!-- 右侧 开始 -->
    <div class="right">
        <!-- 帮助和服务顶部 开始 -->
        <div class="right_header">
            <i class="glyphicon glyphicon-question-sign"></i>
            <span>帮助和服务</span>
            <i id="help-container-close" class="close_btn">x</i>
        </div>
        <!-- 帮助和服务顶部 结束 -->
        <!-- 帮助和服务主体内容 开始 -->
        <div class="right_body">
        </div>
        <!-- 帮助和服务主体内容 结束 -->
    </div>
    <!-- 右侧 结束 -->
    <!-- 消息和通知 开始 -->
    <div id="widget-notice-center">
        <div class="notice-center">
            <div class="notice-nav">
                <a class="" target="_blank" href="javascript:void(0);">
                    <span class="glyphicon glyphicon-comment"></span>客户消息
                </a>
                <a class="" href="javascript:void(0);">
                    <span class="glyphicon glyphicon-bell"></span>通知
                </a>
            </div>
        </div>
    </div>


@section('page_js')
    <!-- 当前页面js -->
    <!--<script src="{{ config('app.source_url') }}mctsource/js/marketing_wxpj42f2.js"></script>-->
@endsection