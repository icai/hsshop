@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css') 
    <!-- 当前页面css --> 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_msnkdk42.css">

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
                    <a href="#&status=1">积分规则</a>
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
    <!-- 总开关开始 -->
    <div class="switch bg-gray">
        <strong>积分管理</strong>
        <p>积分管理是帮助您增加用户用于激励和回馈用户在平台的消费行为和活动行为，提升用户对平台的黏度和重复下单率。</p>
        <!-- 总开关 -->
        <div class="switch-wrap switch-total">
            <label class="ui-switcher ui-switcher-off" id="z_is_on" data-is-open="0"></label>
        </div>
    </div>
    <!-- 总开关结束 -->
    <div id="content_box" class="none"> 
        <!-- tab选项卡 -->
        <ul class="t-tab">
            <li class="active">积分生产</li>
            <li>积分消耗</li>
        </ul>
        <div> 
            <!-- 选项卡1内容 -->
            <div class="content-warp">
                <!-- 消费送积分开始 -->
                <div>
                    <!-- 标题和开关 -->
                    <div class="switch bb-line">
                        消费送积分
                        <div class="switch-wrap sub-switch switch-small">
                            <label class="ui-switcher ui-switcher-off" id="xf_is_on" data-is-open="0"></label>
                        </div>
                    </div>
                    <!-- 规则 -->
                    <div class="rule-warp ">
                        <div class="mt10">
                            <label>基本规则：</label>
                            <span>订单支付金额X<input type="text" class="t-number" id="xf_basic_rule" value="100" />% </span> 
                            <span>(例子：订单支付金额为200元，返点比例为100%，消费送积分是200分)</span>
                        </div>
                        <div class="mt10">
                            <label>额外奖励规则：</label>
                            <span><input type="button" value="添加奖励规则" id="btnAddRule" />% <span>注：最多设置5个额外奖励规则，每个奖励不叠加</span></span>
                            <div id="divAddRule">
                              
                            </div> 
                        </div>
                    </div>
                </div>
                <!-- 消费送积分结束 -->
                <!-- 分享送积分开始 -->
                <div>
                    <!-- 标题和开关 -->
                    <div class="switch bb-line">
                        分享送积分
                        <div class="switch-wrap sub-switch switch-small">
                            <label class="ui-switcher ui-switcher-off" id="fx_is_on" data-is-open="0"></label>
                        </div>
                    </div>
                    <!-- 规则 -->
                    <div class="rule-warp">
                        <div class="mt10">
                            <label>基本规则：</label>
                            <span>每次分享送<input type="text" class="t-number" id="fx_basic_rule" value="5" />积分</span>
                        </div>
                        <div class="mt10">
                            <label>限制规则：</label>
                            <span>每人每天最多可获得<input type="text" class="t-number" id="fx_limit_rule" value="2" />次分享积分</span>
                        </div>
                    </div>
                </div>
                <!-- 分享送积分结束 -->
                <!-- 签到送积分开始 -->
                <div class="none"> 
                    <div class="switch bb-line">
                        签到送积分
                        <div class="switch-wrap sub-switch switch-small">
                            <label class="ui-switcher ui-switcher-off" data-is-open="0"></label>
                        </div>
                    </div> 
                    <div class="rule-warp">
                    这里是签到送积分内容
                    </div>
                </div>
                <!-- 签到送积分结束 -->
                <input type="hidden" id="xf_id" value="0" />
                <input type="hidden" id="fx_id" value="0" />
                <button class="btn btn-primary btn-sm " id="footer_save1">保存</button>
            </div>
            <!-- 选项卡2内容 -->
            <div class="content-warp none">
                <div>
                    <!-- 标题和开关 -->
                    <div class="switch bb-line">
                        积分抵现
                        <div class="switch-wrap sub-switch switch-small">
                            <label class="ui-switcher ui-switcher-off" id="xh_is_on" data-is-open="0"></label>
                        </div>
                    </div>
                    <!-- 规则 -->
                    <div class="rule-warp">
                        <div class="mt10">
                            <label>设置抵现比例</label>
                            <span class="add-subtract"><input type="button" value='-' class="subtract" /><input type="text" class="mlr0 t-number" id="xh_percent" value="100" /><input type="button" value='+' class="add" /></span>%
                        </div>
                        <div class="mt10">
                            <label>设置积分汇率：</label>
                            <span>1元=<input type="text" value="100" class="t-number" id="xh_rate" />积分</span>
                        </div>
                    </div>
                    <input type="hidden" value="" id="xh_id" />
                    <button class="btn btn-primary btn-sm" id="footer_save2">保存</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 底部logo 开始 -->
<div id="app-footer" class="footer">
    <a href="javascript:void(0);" class="logo" target="_blank"></a>
</div>
<!-- 底部logo 结束 -->

<!-- 浮动底部 -->
<!-- <div class="footer-warp text-center" id="footer_save1">
    <input type="hidden" id="xf_id" value="0" />
    <input type="hidden" id="fx_id" value="0" />
    <button class="btn btn-primary btn-sm" >保存</button>
</div>
<div class="footer-warp text-center none" id="footer_save2">
    <input type="hidden" value="" id="xh_id" />
    <button class="btn btn-primary btn-sm">保存</button>
</div> -->
@endsection
@section('other')

@endsection

@section('page_js')
    @parent 
    <script src="{{ config('app.source_url') }}static/js/require.js" ></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script> 
    <!-- 当前页面js --> 
    <script src="{{config('app.source_url')}}mctsource/js/member_msnkdk42.js"></script> 
    
@endsection