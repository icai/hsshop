@extends('merchants.default._layouts')
@section('head_css')
<!-- 时间插件 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_p0dx3ur8.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">提现记录</div>
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
    <div class="screen_module container-fluid">
        <div class="row">
            <form class="form-horizontal col-md-9" role="form" method="get">
                <!-- 验证时间： -->
                <div class="form-group">
                    <label class="col-sm-2 control-label rewrite-bootstrap">起止时间：</label>
                    <div class="col-sm-3 center_start rewrite-bootstrap">
                        <!-- 开始时间 -->
                        <div id='start_time' class='input-group'>
                            <input class="form-control f12" name="start_time" type='text'  value="{{$input['start_time'] or ''}}"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div><span style="display:inline-block;float:left;margin:0 10px;">至</span>
                    <div class="col-sm-3 rewrite-bootstrap">
                        <!-- 结束时间 -->
                        <div id='end_time' class='input-group'>
                            <input class="form-control f12" name="end_time" type='text' value="{{$input['end_time'] or ''}}"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-2 rewrite-bootstrap">
                        <a class="fastSelect_time mgl10" href="javascript:void(0);" data-day="7">最近7天</a>
                        &nbsp;<a class="fastSelect_time" href="javascript:void(0);" data-day="30">最近30天</a>
                    </div>
                </div>
                @php
                    $statusTitle = array('申请中','银行处理中','提现成功','提现失败');
                @endphp
                <div class="form-group">
                    <label class="col-sm-2 control-label rewrite-bootstrap">提现状态：</label>
                    <div class="col-sm-3 rewrite-bootstrap">
                        @php
                            $status = isset($input['status'])?$input['status']:0;
                        @endphp
                        <select name='status' class="form-control">
                            <option value="0">全部</option>
                            @foreach($statusTitle as $k=>$v)
                                <option value="{{$k+1}}" @if($status==$k+1) selected @endif >{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-primary" href="javascript:void(0);">筛选</button>
                    </div>
                </div>
            </form>
            <div class="col-md-3 rewrite-bootstrap f12 tright">
                <p class="gray_999">提现咨询专线：0571-87796692</p>
                <p class="gray_999">服务时间：10:00-18:00</p>
                <a class="blue_38f" href="javascript:void(0);" target="_blank">在线客服咨询</a>
            </div>
        </div>
    </div>
    <!-- 筛选 结束 -->
    <!-- 数据 -->
    <div class="data-content">
        <!-- 数据列表 -->
        <table class="table table-hover f12">
            <thead>
                <tr class="active">
                    <td>申请时间</td>
                    <td>提现银行 | 编号</td>
                    <td>提现金额(元)</td>
                    <td>处理完成时间</td>
                    <td>状态</td>
                    <td>申请人</td>
                    <td>备注</td>
                </tr>
            </thead>
            <tbody>
               @foreach($withdrawl as $w)
               @php
                   $create_at=date('Y-m-d H:i:s',$w['create_at']);
                   $accounts = array('个人账户','对公账户');
                   $handle_at = $w['handle_at'];
                   if($handle_at){
                        $handle_at=date('Y-m-d H:i:s',$handle_at);
                   }
               @endphp
                <tr>
                    <td>{{ $create_at or '' }}</td>
                    <td>
                        <div class="reflect_wrap" data-flag="true">
                            <div class="note_tips" data-container="body" data-toggle="popover" role="button" data-placement="bottom" data-html="true"
                                 @foreach($w['bankInfo'] as $v)
                                 data-content="<p>收款银行：{{ $v['bank_name'] }}</p><p>银行帐户：{{ substr($v['account_no'],0,6) }}****{{ substr($v['account_no'],-8) }}</p><p>帐户名称：{{ $v['account_name'] }} </p>">
                                @endforeach
                                <span>{{ $accounts[$w['account_type']-1] or '未知'}} |
                                    @foreach($w['bankInfo'] as $v)
                                        {{ $v['bank_name'] }}...{{ substr($v['account_no'],-4) }}
                                    @endforeach
                                </span>
                                <i class="glyphicon glyphicon-triangle-bottom f14"></i>
                            </div>
                        </div>
                        <p class="f12 gray_999">{{$w['water_no']}}</p>
                    </td>
                    <td class="red_f00">-{{$w['money']}}</td>
                    <td>
                        @if($handle_at)
                            {{ substr($handle_at,0,10)  }} <br/>{{ substr($handle_at,-8)  }}
                        @endif
                    </td>  
                    <td>{{ $statusTitle[$w['status']-1] or '未知' }}</td>
                    <td>
                        <p> {{ $userInfo['name'] }} </p>
                        <p class="gray_999">{{ $userInfo['mphone'] }}</p>
                    </td>
                    <td>{{ $w['remark'] or ''}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(empty($withdrawl))
        <!-- 空 -->
        <div class="no_result">暂无数据</div>
        @endif
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
<script src="{{ config('app.source_url') }}mctsource/js/capital_p0dx3ur8.js"></script>
@endsection