@extends('merchants.default._layouts')

@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}">
@endsection

@section('slidebar')
    @include('merchants.member.slidebar')
@endsection

@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="#&status=1">客户概况</a>
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
    <!--头部-->
    <div class="order_info_header margin_20">
        <div class="border_right">
            <?php
            $fans_total = isset($fans_dashboard->fans_total)?$fans_dashboard->fans_total: 0;
            $mobile_total = isset($fans_dashboard->mobile_total)?$fans_dashboard->mobile_total: 0;

            $man_total = isset($fans_dashboard->man_total)?$fans_dashboard->man_total: 0;
            $woman_total = isset($fans_dashboard->woman_total)?$fans_dashboard->woman_total: 0;
            $unknown_total = isset($fans_dashboard->unknown_total)?$fans_dashboard->unknown_total: 0;
            $buy_fans_total = isset($fans_dashboard->buy_fans_total)?$fans_dashboard->buy_fans_total: 0;
            $nobuy_fans_total = isset($fans_dashboard->nobuy_fans_total)?$fans_dashboard->nobuy_fans_total: 0;
            ?>
            <a class="block">{{$fans_total}}</a>
            <p>昨日微信粉丝</p>
        </div>
        <div class="">
            <a class="block">{{$mobile_total}}</a>
            <p>昨日手机用户</p>
        </div>
    </div>
    <div class="subcontent margin_20">
        <h3>客户属性</h3>
        <nav>
            <span>
                <a href="javascript:void(0);" class="new-window" target="_blank">详细 》</a>
            </span>
        </nav>
        <div class="ui-block-head-help">
            <a href="javascript:void(0);" class="js-help-notes"></a>
           
            <div class="js-intro-popover popover popover-help-notes bottom" style="display: none;top: 16px;left: -20px;">
                <div class="arrow"></div>
                <div class="popover-inner">
                    <div class="popover-content">
                        <p><strong>下单笔数：</strong>所有用户的下单总数。</p>
                        <p><strong>付款订单：</strong>已付款的订单总数；</p>
                        <p><strong>发货订单：</strong>已发货的订单总数。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 会员图表 -->
    <div class="echarts">
        <input type="hidden" name="man_total" value="{{$man_total}}">
        <input type="hidden" name="woman_total" value="{{$woman_total}}">
        <input type="hidden" name="unknown_total" value="{{$unknown_total}}">
        <div class="echarts_left" id="echarts_left"></div>

        <input type="hidden" name="buy_fans_total" value="{{$buy_fans_total}}">
        <input type="hidden" name="nobuy_fans_total" value="{{$nobuy_fans_total}}">
        <div class="echarts_right" id="echarts_right"></div>
    </div>
    <!-- 会员分布头 -->
    <div class="subcontent margin_20">
        <h3>客户分布</h3>
        <nav>
            <span>
                <a href="javascript:void(0);" class="new-window" target="_blank">详细 》</a>
            </span>
        </nav>
        <div class="ui-block-head-help">
            <a href="javascript:void(0);" class="js-help-notes"></a>
            
            <div class="js-intro-popover popover popover-help-notes bottom" style="display: none;top: 16px;left: -20px;">
                <div class="arrow"></div>
                <div class="popover-inner">
                    <div class="popover-content">
                        <p><strong>下单笔数：</strong>所有用户的下单总数。</p>
                        <p><strong>付款订单：</strong>已付款的订单总数；</p>
                        <p><strong>发货订单：</strong>已发货的订单总数。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 区域分布图 -->
    <div class="area_echarts">
        <div class="left_area" id="left_area"></div>
        <div class="range">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>排名</th>
                        <th>地区</th>
                        <th>粉丝数</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Mark</td>
                        <td>Otto</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>  
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
    
@endsection
@section('page_js')
    @parent
    <!-- 图表插件 -->
    <script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member.js"></script>
@endsection