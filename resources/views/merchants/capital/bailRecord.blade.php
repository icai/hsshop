@extends('merchants.default._layouts')
@section('head_css')
<!-- 时间插件 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_qji1jlp4.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">保证金记录</div>
        <!-- 二级导航三级标题 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
<!-- 筛选 开始 -->
    <div class="screen_module">
        <form class="form-horizontal" role="form">
            <!-- 验证时间： -->
            <div class="form-group">
                <label class="col-sm-2 control-label rewrite-bootstrap">起止时间：</label>
                <div class="col-sm-3 center_start">
                    <!-- 开始时间 -->
                    <div id='start_time' class='input-group'>
                        <input class="form-control f12" type='text' />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div><span class="pull-left">至</span>
                <div class="col-sm-3">
                    <!-- 结束时间 -->
                    <div id='end_time' class='input-group'>
                        <input class="form-control f12" type='text' />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a class="fastSelect_time mgl10" href="javascript:void(0);" data-day="7">最近7天</a>
                    &nbsp;<a class="fastSelect_time" href="javascript:void(0);" data-day="30">最近30天</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label rewrite-bootstrap">提现状态：</label>
                <div class="col-sm-3">
                    <select class="form-control">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>
                <label class="col-sm-2 control-label rewrite-bootstrap">保证金状态：</label>
                <div class="col-sm-3">
                    <select class="form-control">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <a class="btn btn-primary" href="javascript:void(0);">筛选</a>
                </div>
            </div>
        </form>
    </div>
    <!-- 筛选 结束 -->
    <!-- 数据 -->
    <div class="data-content">
        <!-- 数据列表 -->
        <table class="table table-hover f12">
            <thead>
                <tr class="active">
                    <td>结算时间</td>
                    <td>提现银行 | 编号</td>
                    <td>提现金额(元)</td>
                    <td>处理完成时间</td>
                    <td>状态</td>
                    <td>申请人</td>
                    <td>备注</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2017-01-12 15:13:31</td>
                    <td>
                        <div class="reflect_wrap" data-flag="true">
                            <div class="note_tips" data-container="body" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<p>收款银行：中国银行</p><p>银行帐户：623572****000039888</p><p>帐户名称：陈琪誉</p>">
                                <span>提现 | 中国银行...9888</span>
                                <i class="glyphicon glyphicon-triangle-bottom f14"></i>
                            </div>
                        </div>
                        <p class="f12 gray_999">2345678</p>
                    </td>
                    <td class="red_f00">-6951.07</td>
                    <td>
                        2017-01-12 <br/>15:12:57
                    </td>  
                    <td>提现成功</td>
                    <td>
                        <p> 布姆电竞学院:小美 </p>
                        <p class="gray_999">13770913139</p>
                    </td>
                    <td>备注</td>
                </tr>
                <tr>
                    <td>2017-01-12 15:13:31</td>
                    <td>
                        <div class="reflect_wrap" data-flag="true">
                            <div class="note_tips" data-container="body" data-toggle="popover" role="button" data-placement="bottom" data-html="true" data-content="<p>收款银行：中国银行</p><p>银行帐户：623572****000039888</p><p>帐户名称：陈琪誉</p>">
                                <span>提现 | 中国银行...9888</span>
                                <i class="glyphicon glyphicon-triangle-bottom f14"></i>
                            </div>
                        </div>
                        <p class="f12 gray_999">2345678</p>
                    </td>
                    <td class="red_f00">-6951.07</td>
                    <td>
                        2017-01-12 <br/>15:12:57
                    </td>  
                    <td>提现成功</td>
                    <td>
                        <p> 布姆电竞学院:小美 </p>
                        <p class="gray_999">13770913139</p>
                    </td>
                    <td>备注</td>
                </tr>
                <tr>
                    <td>2017-01-12 15:13:31</td>
                    <td>
                        <div class="reflect_wrap" data-flag="true">
                            <div class="note_tips" data-container="body" data-toggle="popover" role="button" data-placement="bottom" data-html="true" data-content="<p>收款银行：中国银行</p><p>银行帐户：623572****000039888</p><p>帐户名称：陈琪誉</p>">
                                <span>提现 | 中国银行...9888</span>
                                <i class="glyphicon glyphicon-triangle-bottom f14"></i>
                            </div>
                        </div>
                        <p class="f12 gray_999">2345678</p>
                    </td>
                    <td class="red_f00">-6951.07</td>
                    <td>
                        2017-01-12 <br/>15:12:57
                    </td>  
                    <td>提现成功</td>
                    <td>
                        <p> 布姆电竞学院:小美 </p>
                        <p class="gray_999">13770913139</p>
                    </td>
                    <td>备注</td>
                </tr>
                <tr>
                    <td>2017-01-12 15:13:31</td>
                    <td>
                        <div class="reflect_wrap" data-flag="true">
                            <div class="note_tips" data-container="body" data-toggle="popover" role="button" data-placement="bottom" data-html="true" data-content="<p>收款银行：中国银行</p><p>银行帐户：623572****000039888</p><p>帐户名称：陈琪誉</p>">
                                <span>提现 | 中国银行...9888</span>
                                <i class="glyphicon glyphicon-triangle-bottom f14"></i>
                            </div>
                        </div>
                        <p class="gray_999">234576</p>
                    </td>
                    <td class="red_f00">-6951.07</td>
                    <td>
                        2017-01-12 <br/>15:12:57
                    </td>  
                    <td>提现成功</td>
                    <td>
                        <p> 布姆电竞学院:小美 </p>
                        <p class="f12 gray_999">13770913139</p>
                    </td>
                    <td>备注</td>
                </tr>
            </tbody>
        </table>
        <!-- 空 -->
        <div class="no_result">暂无数据</div>
    </div>
    <!-- 流量趋势图表 结束 -->
</div>
@endsection
@section('page_js')
<!-- 时间插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_qji1jlp4.js"></script>
@endsection