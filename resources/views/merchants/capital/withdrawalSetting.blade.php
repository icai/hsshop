@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_0l322835.css" />
@endsection
@section('slidebar')
    @include('merchants.capital.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <!-- 二级导航三级标题 开始 -->
            <div class="third_title">设置提现账号</div>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">设置提现账号</div>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺名称：</label>
                        <div class="col-sm-10">布姆电竞学院161018</div>
                    </div>
                </div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><em class="red mgr5">*</em>可提现方式：</label>
                        <div class="col-sm-7">
                            <!-- 银行卡管理 -->
                            <ul class="bank_wrap clearfix">
                                <li class="js_selectBank">
                                    <input type="radio" name="type" value="" checked data-class="private_bank">
                                    <div class="bank_list active">
                                        <p>对私银行账户</p>
                                        <p>支持提现至个人银行借记卡</p>
                                    </div>
                                </li>
                                <li class="js_selectBank">
                                    <input type="radio" name="type" value=""/ data-class="public_bank">
                                    <div class="bank_list active">
                                        <p>对公银行账户</p>
                                        <p>提现至对公司账户</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="faceplate_module">
                        <!-- 提示 -->
                        <div class="private_bank message_notice_warning f12">
                            <p>1. 请仔细填写账户信息，如果由于您填写错误导致资金流失，会搜云概不负责；</p>
                            <p>2. 只支持提现到银行借记卡，<span class="red_f60">不支持信用卡和存折</span>。提现审核周期为1个工作日；</p>
                        </div>
                        <div class="public_bank message_notice_warning f12 no">
                            <p>1. 请仔细填写账户信息，如果由于您填写错误导致资金流失，会搜云概不负责；</p>
                            <p>2. 只支持提现至公司账户，不支持信用卡和存折，提现审核周期为1个工作日；</p>
                            <p>3. 准确填写银行开户许可证上的公司名称，否则无法提现；</p>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><em>*</em>开户银行：</label>
                            <div class="col-sm-3">
                                <select class="form-control">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><em>*</em> 公司账户：</label>
                            <div class="col-sm-6 f12">
                                <input type="text" class="form-control" placeholder="公司账户">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><em>*</em> 公司名称：</label>
                            <div class="col-sm-6 f12">
                                <input type="text" class="form-control" placeholder="公司账户">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><em>*</em> 短信验证码：</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input class="form-control" type="text"  placeholder="请输入6位验证码">
                                    <div class="input-group-addon">获取</div>
                                </div>
                                <div class="f12 gray_999">验证短信将发送到您绑定的手机：+86-18867501944，请注意查收</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">保存</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <!-- 时间插件 -->
    <script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/capital_ttz2phof.js"></script>
@endsection