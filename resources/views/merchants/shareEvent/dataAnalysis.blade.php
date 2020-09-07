@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/share_list.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/data-analysis.css" />
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
                    <a href="{{ URL('/merchants/shareEvent/list') }}">享立减</a>
                </li>
                <li>
                    <a href="javascript:void(0)">查看数据</a>
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
        <div class="activity-header">
            <div class="activity-desc">
                <div>活动名称：<span class="J_activity-desc">暂无数据</span></div>
                <div>活动商品：<span class="J_activity-desc">暂无数据</span></div>
                <div>活动时间：<span class="J_activity-desc">暂无数据</span></div>
            </div>
            <div class="activity-desc desc-amount">
                <div>
                    <p>销售价（元）</p>
                    <p class="activity-amount">0</p>
                </div>
                <div>
                    <p>每次减（元）</p>
                    <p class="activity-amount">0</p>
                </div>
                <div>
                    <p>保底价（元）</p>
                    <p class="activity-amount">0</p>
                </div>
                <div>
                    <p>成交单数</p>
                    <p class="activity-amount">0</p>
                </div>
                <div>
                    <p>成交金额（元）</p>
                    <p class="activity-amount emphasize">0</p>
                </div>
            </div>
        </div>
        <div class="common_top">
            <span class="common_line"></span>
            <div class="common_title">
                活动概览
            </div>
            <div class="common_link"></div>
            <!-- <div class="horn-tips">
                <i class="tips-icon"></i>
                活动期间数据每10分钟刷新
            </div> -->
        </div>
        <div class="activity-overview clearfix">
            <div class="overview-item">
                <div>
                    浏览量
                    <span class="data-tips">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <span class="tips-content">当天数据第二天展示</span>
                    </span>
                </div>
                <p class="item-amount">0</p>
            </div>
            <div class="overview-item">
                <p>
                    访客数
                    <span class="data-tips">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <span class="tips-content">当天数据第二天展示</span>
                    </span>
                </p>
                <p class="item-amount">0</p>
            </div>
            <div class="overview-item">
                <p>成单量</p>
                <p class="item-amount">0</p>
                <!-- <p class="emphasize">（新用户：<span class="J_new-member">0</span>）</p> -->
            </div>
            <div class="overview-item">
                <p>
                    转化率
                    <span class="data-tips">
                        <i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
                        <span class="tips-content" style="width:120px;">成单量/访客数</span>
                    </span>
                </p>
                <p class="item-amount">0</p>
            </div>
            <div class="overview-item border-right-none">
                <p>成交金额（元）</p>
                <p class="item-amount">0</p>
            </div>
            <!-- <div class="overview-item">
                <p>新用户人数</p>
                <p class="item-amount">0</p>
            </div> -->
            <!-- <div class="overview-item">
                <p>老用户人数</p>
                <p class="item-amount">0</p>
            </div> -->
            <div class="overview-item">
                <p>分享者人数</p>
                <p class="item-amount">0</p>
                <!-- <span class="emphasize">（新用户：<span class="J_new-mwmber">0</span>）</span> -->
            </div>
            <div class="overview-item">
                <p>参与者人数</p>
                <p class="item-amount">0</p>
            </div>
            <div class="overview-item border-right-none"></div>
        </div>
        <div class="common_top">
            <span class="common_line"></span>
            <div class="common_title">
                用户分析
                <span class="export">数据导出</span>
            </div>
            <div class="common_link"></div>
        </div>
        <!-- 列表 开始 -->
        <div class="main_content">
            <ul class="main_content_title">
                <li>序号</li>
                <li>分享时间</li>
                <li style="padding-left:20px;">分享者</li>
                <li>是否购买</li>
                <li>点击参与人数</li>
                <li>完成时间</li>
            </ul>
            <div class="J_data-content">

            </div>
            <!-- <ul class="data_content">
                <li class="">1</li>
                <li class="">2018-07-10 09:50:50</li>
                <li class=""><img src="https://img.yzcdn.cn/public_files/2018/07/18/be5f7ae74599ab9ae776090f31c0cf4e.png" class="avatar">是是是</li>
                <li class="">是</li>
                <li class="">222</li>
                <li class="">30分钟</li>
            </ul> -->
        </div> 
        <!-- 列表 结束 -->
        <!-- 分页 -->
        <div class="text-right J_page">
            
        </div>
       
    </div>
@endsection

@section('page_js')
<script type="text/javascript">
    var id = "{{ $id }}";
</script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/data-analysis.js"></script> 
@endsection