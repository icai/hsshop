@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/capital_ktdlh5hw.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{ URL('/merchants/capital/serviceOrdering') }}">服务订购</a>
            </li>
            <li class="hover">
                <a href="{{ URL('/merchants/capital/bulkPurchase') }}">批量采购</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/capital/cdkeyExchange') }}">激活码兑换</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
    <!-- 头部 开始 -->
    <div class="purchase_header">
        <p class="f18 mgb15 items_ellipsis">微商城批量采购</p>
        <p class="gray_999 f16 mgb15 items_ellipisis2">一次性采购所需数量 同样享受会搜云币奖励</p>
        <a class="f14 blue_38f" href="javascript:void(0);" target="_blank">了解详情</a>
    </div>
    <!-- 头部 结束 -->
    <p class="f16 mgb15 gray_999">批量采购定价（限时特价至2016年9月30日）</p>
    <!-- table 开始 -->
    <table class="table table-bordered">
        <tr class="red">
            <td>订购产品数量</td>
            <td>微商城1年期单价（元）</td>
            <td>合计（元）</td>
        </tr>
        <tr>
            <td>1个激活码</td>
            <td>4800.00</td>
            <td>4800.00</td>
        </tr>
        <tr>
            <td>5个激活码</td>
            <td>4300.00</td>
            <td>21500.00</td>
        </tr>
        <tr>
            <td>10个激活码</td>
            <td>4000.00</td>
            <td>40000.00</td>
        </tr>
        <tr>
            <td>20个激活码</td>
            <td>3800.00</td>
            <td>76000.00</td>
        </tr>
    </table>
    <!-- table 结束 -->
    <p class="f12 gray_999 mgb30">会搜云批量采购坚持透明的统一定价原则，采用简洁的激活码包灵活组合模式，如有疑问请拨打咨询热线 {{$CusSerInfo['phone']}}与我们联系</p>
    <!-- 表单 开始 -->
    <form class="form" action="" method="post" >
        <!-- 数量 -->
        <div class="mgb15 display_box">
            <div class="list_name">您购买的数量：</div>
            <div class="box_flex1" >
                <div class="w250 display_box">
                    <div class="box_flex1 form-group">
                        <input class="form-control " type="text" name="num" value="" placeholder="请输入您购买的数量" require />
                    </div><span class="list_tip">个</span>
                </div>
            </div>
        </div>
        <!-- 应付款 -->
        <div class="mgb15 display_box">
            <div class="list_name">应付款：</div>
            <div class="box_flex1 form-group">
                <p class="orange_f60"><strong class="f20">0</strong>元</p>
            </div>
        </div>
        <!-- 支付方式： -->
        <div class="mgb15 display_box">
            <div class="list_name">支付方式：</div>
            <div class="box_flex1 form-group">
                <p>请将应付款转账汇款至以下账号</p>
                <div class="pay_info">
                    <dl class="display_box">
                        <dt>收款方户名：</dt>
                        <dd class="box_flex1">杭州起码科技有限公司</dd>
                    </dl>
                    <dl class="display_box">
                        <dt>收款方开户行：</dt>
                        <dd class="box_flex1">招商银行杭州高新支行</dd>
                    </dl>
                    <dl class="display_box">
                        <dt>收款方账号：</dt>
                        <dd class="box_flex1">571907177010106</dd>
                    </dl>
                </div>
            </div>
        </div>
        <!-- 付款方户名： -->
        <div class="mgb15 display_box">
            <div class="list_name">付款方户名：</div>
            <div class="box_flex1 form-group">
                <div class="w350 display_box">
                    <input class="box_flex1 form-control" type="text" name="payName" value="" placeholder="开户银行卡单位/个人名称" required minlength="2"  max="20" data-bv-stringlength-message="请输入正确的付款方户名(2-20个字符)。" />
                </div>
            </div>
        </div>
        <!-- 开户银行： -->
        <div class="mgb15 display_box">
            <div class="list_name">开户银行：</div>
            <div class="box_flex1 form-group">
                <div class="w350 display_box">
                    <input class="box_flex1 form-control" name="bankName" value="" type="text" placeholder="如，招商银行杭州高新支行" required minlength="4"  max="20" data-bv-stringlength-message="请输入正确的开户银行名(4-20个字符)。" />
                </div>
            </div>
        </div>
        <div class="mgb15 display_box">
            <div class="list_name">银行账号：</div>
            <div class="box_flex1 form-group">
                <div class="w350 display_box">
                    <input class="box_flex1 form-control" type="text" name="bankAccount" value="" placeholder="银行账号"  minlength="10" max="30" data-bv-stringlength-message="请输入正确的银行账号(10-30个字符)。" />
                </div>
            </div>
        </div>
        <div class="mgb15 display_box">
            <div class="list_name">付款时间：</div>
            <div class="box_flex1 form-group">
                <div class="w350 display_box">
                    <input id="pay_date" class="box_flex1 form-control laydate-icon" type="text" name="payDate" value="" placeholder="请选择转账付款的时间" />
                </div>
            </div>
        </div>
        <div class="mgb15 display_box">
            <div class="list_name">订购人姓名：</div>
            <div class="box_flex1 form-group">
                <div class="w350 display_box">
                    <input class="box_flex1 form-control" type="text" name="" value="" placeholder="填写订购人姓名，便于我们沟通使用" />
                </div>
            </div>
        </div>
        <div class="mgb15 display_box">
            <div class="list_name">手机号：</div>
            <div class="box_flex1">
                <div class="w350 display_box">
                    <div class="box_flex1 form-group">
                        <input class="mobile_num form-control" type="text" name="mobile" value="" placeholder="用于接收您采购的激活码" />
                    </div><a class="validate_mobile" href="javascript:void(0);">验证手机号</a>
                </div>
                
            </div>
        </div>
        <div class="mgb15 display_box">
            <div class="list_name">验证码：</div>
            <div class="box_flex1 form-group">
                <div class="w350 display_box">
                    <input class="box_flex1 form-control" type="text" name="validateCode" value="" placeholder="请输入六位短信验证码" />
                </div>
            </div>
        </div>
        <div class="mgb15 display_box">
            <div class="list_name"></div>
            <div class="box_flex1 form-group">
                <input class="submit_btn" type="submit" value="提交订单"/>
            </div>
        </div>
    </form>
    <!-- 表单 结束 -->
    <!-- 问题块 -->
    <!-- 区域标题 开始 -->
    <div class="common_top mgb15">
        <span class="common_line"></span>
        <p class="common_title">常见问题</p>
        <div class="common_link"></div>   
    </div>
    <!-- 区域标题 结束 -->  
    <!-- 常见问题 开始 -->
    <div class="question_wrap">
        <h5>1.如何批量采购？</h5>
        <div class="f12 mgb30">
            <p>第一步：确定购买数量后，通过网银将采购款转账至会搜云的收款账户；</p>
            <p>第二步：同时提交采购单，并附上您的转账记录；</p>
            <p>第三步：会搜云财务审核确认到账后，系统将激活码发送到您采购时填写的手机号码，并请妥善保存。</p>
        </div>
        <h5>2.批量采购每次能买多少？</h5>
        <div class="f12 mgb30">激活码每次至少够买5个以上（包含5个），才能成功提交订单；每笔订单最多可购买100个激活码，如需订购更多，请分批提交订单。</div>
        <h5>3.激活码什么时候发放？</h5>
        <div class="f12 mgb30">财务审核通过后，系统将会购买的激活码发送到您订单时填写的手机号上，请您务必妥善保管激活码。</div>
        <h5>4.如何使用激活码？</h5>
        <div class="f12 mgb30">您获得激活码以后，可在激活码兑换页面进行兑换。且每个激活码仅可兑换一次，兑换成功后，可获得会搜云微商城1年期（即365日）的使用期限。</div>
        <h5>5.批量采购的发票如何申请？</h5>
        <div class="f12 mgb30">我们只向批量采购激活码的采购用户，一次性开具相应金额的“软件服务费”发票。您可致电客服电话申请发票：0571-87796692</div>
    </div>
    <!-- 常见问题 结束 -->
</div>
@endsection
@section('page_js')
<!-- 表单验证插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/zh_CN.js"></script>
<!-- 轮播插件 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script>
<!-- 弹框插件 -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 时间插件 文件 -->
<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/capital_ktdlh5hw.js"></script>
@endsection