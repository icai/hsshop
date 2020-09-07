@extends('merchants.default._layouts')
@section('head_css')
    <!-- 时间插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_yihlxlpi.css" />
@endsection
@section('slidebar')
    @include('merchants.capital.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <!-- 二级导航三级标题 开始 -->
            <div class="third_title">充值</div>
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
        <!-- 充值表单 开始 -->
        <div class="recharge_items mgb15">
            <!-- 表单头 开始 -->
            <div class="recharge_header">充值</div>
            <!-- 表单头 结束 -->
            <!-- 表单 主体 开始 -->
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">账户可用余额：</label>
                    <div class="col-sm-10"><span class="money_red">0.00</span>元</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">充值金额：</label>
                    <div class="col-sm-4">
                        <input class="js_money form-control" type="text" name="" value="" placeholder="请输入充值金额" />
                    </div>
                    <div class="col-sm-1">元</div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" class="js-submit btn btn-primary" data-toggle="modal" >提交</button>
                    </div>
                </div>
            </div>
            <!-- 表单主体 结束 -->
        </div>
        <!-- 充值表单 结束 -->
        <!-- 问题 开始 -->
        <div class="question_items">
            <p class="question_title">使用遇到问题？</p>
            <h5>·输入充值金额后，为什么无法成功充值？</h5>
            <p class="question_des">答：大额充值会受到充值额度的限制，建议您分批进行充值，每次充值金额不超过10000元。
            </p>
            <h5>·充值成功了，为什么收支明细里没有记录？</h5>
            <p class="question_des">答：充值金额是实时到账的，若收支明细中没有记录，可能是系统延时引起的，您可稍后查看。若超过一天仍未显示，请与会搜云客服联系。
            </p>
        </div>
        <!-- 问题 结束 -->
    </div> 
@endsection
<!--支付弹框 -->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p class="modal-title" id="myModalLabel">充值收银平台</p>
            </div>
            <div class="modal-body">
                <!-- 信息 -->
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">店铺名称：</label>
                        <div class="col-sm-8">布姆电竞学院161018</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">当前余额：</label>
                        <div class="col-sm-8">0.00元</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">充值金额：</label>
                        <div class="col-sm-8">
                            <span class="recharge_money money_red">11.11 元</span><span class="blue_38f pointer" data-dismiss="modal">&nbsp;修改</span>
                        </div>
                    </div>
                </div>
                <!-- 支付 -->
                <!-- 支付导航 -->
                <ul class="js-pay nav nav-tabs">
                    <li class="active" data-class="online-recharge"><a href="javascript:void(0);">线上扫码充值</a></li>
                    <li data-class="unline-recharge"><a href="javascript:void(0);">线下转账充值</a></li>
                </ul>
                <!-- 线上支付盒子 -->
                <div class="online-recharge recharge_wrap">
                    <!-- 警告框 -->
                    <div class="wran_item f12">
                        <span class="red_f30">提醒：</span>大额充值如需快速入账，建议您分多笔在线扫码充值，银行卡单笔支付限额以具体银行为准
                    </div>
                    <!-- 二维码支付 -->
                    <div class="QRcode_wrap">
                        <img src="//www.youzan.com/v2/pay/recharge/rechargeqr.png?recharge_number=R1702201439310436436289" alt="充值二维码" class="loading js-img-src" width="200" height="200">
                    </div>
                    <div class="qrcode_confirm">
                        <!-- 二维码标识 -->
                        <div class="QRcode_marke">
                            <hr class="QRcode_line" />
                        </div>
                        <p class="recharge_info">微信或支付宝扫码支付，成功后立即充值到账</p>
                        <div class="clearfix">
                            <button type="button" class="btn btn-primary pull-left">我已经成功支付</button>
                            <a class="pay_question pull-right btn" href="javascript:void(0);" data-dismiss="modal">支付遇到问题</a>
                        </div>
                    </div>
                </div>
                <div class="unline-recharge recharge_wrap hidden">
                    <!-- 警告框 -->
                    <div class="wran_item f12">
                        <span class="red_f30">提醒：</span>线下转账在上传凭证后需<span class="red_f30">3-5</span>个工作日审核到账，您可以选择小金额分多笔线上充值，快速到账
                    </div>
                    <div class="gray_bg">
                        <p>您需转账 <span class="recharge_money money_red">11.11元</span> 至以下账户，转账成功后填写相应信息并提交审核</p>
                        <p>收款方户名：杭州起码科技有限公司</p>
                        <p>收款方开户行：招商银行杭州高新支行</p>
                        <p>收款方账号：571907177010106</p>   
                    </div>
                    <div class="clearfix">
                        <button type="button" class="js_upload btn btn-primary pull-right" data-toggle="modal">已转账汇款，我要上传凭证</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 证明弹框 -->
<div class="modal fade" id="proveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">线下转账凭证</h4>
            </div>
            <div class="modal-body">
                <!-- 标题 -->
                <p class="gray_ccc mgb10">收款方信息</p>
                <div class="form-horizontal mgl85">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">户名：</label>
                        <div class="col-sm-8">杭州起码科技有限公司</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">开户行：</label>
                        <div class="col-sm-8">招商银行杭州高新支行</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">账号：</label>
                        <div class="col-sm-8">571907177010106</div>
                    </div>
                </div>
                <hr class="dotted_line" />
                <p class="gray_ccc mgb10">付款方信息</p>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">户名：</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="" value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">开户行：</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="例：招商银行 杭州高新支行">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">账号：</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">金额：</label>
                        <div class="recharge_money col-sm-8">1111.00 元</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">付款时间：</label>
                        <div class="col-sm-8">
                            <!-- 开始时间 -->
                            <div id='start_time' class='input-group'>
                                <input class="form-control" name="start_time" type='text'  value=""/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button type="button" class="btn btn-primary">确认提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('page_js')
<!-- 时间插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/moment/locales.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/capital_yihlxlpi.js"></script>
@endsection