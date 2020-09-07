@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/res_member_list.css" />
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
                    @if ($type == 0)
                        <a href="/merchants/marketing/researches/0">在线报名</a>
                    @elseif ($type == 1)
                        <a href="/merchants/marketing/researches/1">在线预约</a>
                    @elseif ($type == 2)
                        <a href="/merchants/marketing/researches/2">在线投票</a>
                    @endif
                </li>
                <li>
                    <a href="javascript:void(0)">参与人列表</a>
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
        <input type="hidden" value='{{$id}}' id='activity_id'>
        <input type="hidden" value='{{$type}}' id='type_id'>
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <!--add by 邓钊 2018-6-27-->
        <div class="mb-15 clearfix pr search_res">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a href="/merchants/marketing/researchExport/{{Route::input('id')}}?type=member&name={{request('name')}}">
                        <div class="btn btn-primary">导出参与记录</div>
                    </a>
                </div>
                @if ($type == 1 || $type == 2)
                    <div class="pull-left">
                        <!--导出投票类型活动的投票结果excel-->
                        &nbsp;&nbsp;<a href="/merchants/marketing/researchExport/{{Route::input('id')}}?type=result">
                            <div class="btn btn-primary">导出结果</div>
                        </a>
                    </div>
                @endif
                <div style="position: relative;">
                    <div class="js-list-search ui-search-box">
                        <form>
                            <!-- 参与人列表搜索修改 -->
                            <input class="txt" id='search_txt' name="name" value="" type="search" placeholder="搜索">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <ul class='type_ul clear'>
            <li data-id='0' class='li_active @if ($type == 0) last_li @endif'>参与人</li>
            @if ($type == 1)
            <li data-id='1' class='last_li'>预约结果</li>
            @elseif ($type == 2)
            <li data-id='2' class='last_li'>投票结果</li>
            @endif
        </ul>
        <!--end-->
        <!-- search 结束 -->
        <!-- 拼团列表 开始 -->
        {{--@if($voteData['data'])--}}
        <div class="main_content main-cont">
            <ul class="main_content_title main_content_ul">
                <li>参与人</li>
                <li>参与时间</li>
                <li class="text-right">操作</li>
            </ul>
            @forelse($list as $v)
            <ul class="data_content main_content_ul">
                <li>
                    <div class='img_div_box'><img src="{{$v->headimgurl}}" alt=""></div>
                    <span class='span_div_box'>{{$v->nickname ? $v->nickname : $v->truename}}</span>
                </li>
                <li class="blue" data-time="{{$v->created_at}}">{{$v->created_at}}</li>
                <li class="text-right pr">
                    <!--许立 2018年7月4日 增加参与次数参数-->
                    <a href="javascript:void(0);" class='look' data-rid="{{$v->research_id}}" data-mid="{{$v->id}}" data-times="{{$v->times}}">详情</a>
                </li>
            </ul>
            @empty
                暂无数据
            @endforelse
        </div>
        <!--add by 邓钊 2018-6-27-->
        <div class="main_content vote-cont hide" id='app' v-cloak>
            <h3 class='vote-title'>@{{message}}</h3>
            <div class='vote-box' v-for='(item,index) in list'>
                <div class='vote-box-title'>
                    <span class='topic'>@{{ item.title }}</span>
                    <span v-if='item.multiple == 0' class='radioCheck'>[单选]</span>
                    <!--add by 何书哲 2018-7-23 添加多选判断条件-->
                    <span v-if='item.multiple != 0' class='radioCheck'>[多选]</span>
                </div>
                <table class='vote-tab'>
                    <tr>
                        <th class='vote-th-a'>选项</th>
                        <th v-if='type_id == 2' class='vote-th-b'>票数</th>
                        <th v-if='type_id == 1' class='vote-th-b'>预约数</th>
                        <th class='vote-th-c'>比例</th>
                    </tr>
                    <tr v-for='ite in item.options'>
                        <td class='vote-option' v-if="ite.type == 'option'">@{{ ite.title }}</td>
                        <td class='vote-option' v-if="ite.type == 'other'">其他</td>
                        <td>@{{ ite.vote_count }}</td>
                        <td class='vote-option'>
                            <div class='vote-progress'>
                                <div :style="{width: ite.widths + 'px'}"></div>
                            </div>
                            <div class='vote-bar'>@{{ ite.bar}}</div>
                        </td>
                    </tr>
                    <tr class='vote-tab-footer'>
                        <td v-if='type_id == 2' class='vote-option'>本题填写总票数</td>
                        <td v-if='type_id == 1' class='vote-option'>本项预约总数</td>
                        <td>@{{ item.total }}</td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <!--end-->
        <!-- 分页 -->
        <div class="text-right">
            {!! $pageHtml !!}
        </div>
    </div>
    <!--参与人详情-->
    <div class='particulars hide'>
        <div class='par_conten'>
            <h3 class='par_title'>参与人详情</h3>
            <p class='par_time'>
                <span id="activity_title">活动标题</span>
                <span id='timesId'></span>
            </p>
            <div class='par_box'>
                <ul class='par_ul'>
                </ul>
            </div>
            <div class='par_close hide'>x</div>
        </div>
    </div>
@endsection

@section('page_js')
    <!--updata by 邓钊 2018-06-27-->
    {{--<script src="{{ config('app.source_url') }}static/js/require.js"></script>--}}
    {{--<script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>--}}
    <script src="{{ config('app.source_url') }}shop/static/js/vue.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <!--end-->
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/res_member_list.js"></script>
    <script type="text/javascript">
        var host = "{{config('app.url')}}";
        var wid = "{{ session('wid') }}";
        var id = "{{ session('id') }}";
        var imgUrl = "{{ imgUrl() }}";
        var _host = "{{ config('app.source_url') }}";
    </script>
@endsection