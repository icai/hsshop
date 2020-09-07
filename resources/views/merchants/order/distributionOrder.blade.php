@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前模块公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/order_llbq22x2.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_tbdo2eag.css" />
@endsection
@section('slidebar')
@include('merchants.order.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/order/distributionOrder') }}">分销单管理</a>
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
<div class="widget-app-board ui-box">
    <div class="widget-app-board-info">
        <h3>分销采购单</h3>
        <div>
            <p>买家购买了店内的分销商品后，系统会自动生成一笔采购单，用于向供货商支付货款。一般情况下采购单会自动完成付款。</p>
            <p>
                <a href="javascipt:void(0);" class="new-window" target="_blank">自动付货款规则</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascipt:void(0);" class="new-window" target="_blank">去分销市场选货</a>
            </p>
        </div>
    </div>
</div>
<div class="content">
    <div class="widget-list">
        <div class="js-list-filter-region clearfix">
            <div class="widget-list-filter">
                <form class="form-horizontal ui-box list-filter-form" onsubmit="return false;">
                    <div class="clearfix">
                        <div class="pull-left">
                            <div class="filter-groups">
                                <div class="control-group">
                                    <label class="control-label">买家订单号：</label>
                                    <div class="controls">
                                        <input type="text" name="buyer_order_no" value="">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">采购单号：</label>
                                    <div class="controls">
                                        <input type="text" name="seller_order_no" value="">
                                    </div>
                                </div>
                                <div class="control-group hide">
                                    <label class="control-label">收货人姓名：</label>
                                    <div class="controls">
                                        <input type="text" name="customer_name" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="time-filter-groups clearfix">
                                <div class="control-group">
                                    <label class="control-label">下单时间：</label>
                                    <div class="controls">
                                        <input type="text" name="start_time" value="" class="js-start-time hasDatepicker" id="startDate">
                                        <span>至</span>
                                        <input type="text" name="end_time" value="" class="js-end-time hasDatepicker" id="endDate">
                                        <span class="date-quick-pick" data-days="7">近7天</span>
                                        <span class="date-quick-pick" data-days="30">近30天</span>
                                    </div>
                                </div>
                            </div>
                            <div class="filter-groups">
                                <div class="control-group">
                                    <label class="control-label">采购单状态：</label>
                                    <div class="controls">
                                        <select name="state" class="js-state-select">
                                            <option value="all">全部</option>
                                            <option value="topay">待付款</option>
                                            <option value="tosend">待发货</option>
                                            <option value="send">已发货</option>
                                            <option value="success">已完成</option>
                                            <option value="cancel">已关闭</option>
                                            <option value="refunding">退款中</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <a href="javascipt:void(0);" class="zent-btn zent-btn-primary js-filter" data-loading-text="正在筛选...">筛选</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="ui-box">
            <table class="table ui-table-order">
                <thead>
                    <tr class="widget-list-header">
                        <th class="text-left" colspan="2">商品</th>
                        <th class="price-cell">单价/数量</th>
                        <th class="aftermarket-cell">售后</th>
                        <th class="customer-cell">买家</th>
                        <th class="time-cell">
                            <a href="javascipt:void(0);">
                                下单时间
                                <span class="orderby-arrow desc"></span>
                            </a>
                        </th>
                        <th class="state-cell">订单状态</th>
                        <th class="pay-price-cell">实付金额</th></tr>
                </thead>
                <tbody>
                    <tr class="separation-row">
                        <td colspan="8"></td>
                    </tr>
                    <tr class="header-row">
                        <td colspan="6">
                            <div>订单号: E20160613192832035378201
                                <div class="help" style="display: inline-block;">
                                    <span class="js-help-notes c-gray" data-class="bottom" style="cursor: help;">微信安全支付－代销</span>
                                    <div class="js-notes-cont hide">该订单通过会搜云代销服务完成交易，请进入“收入/提现”页面，“微信支付”栏目查看收入或提现</div>
                                </div>
                            </div>
                            <div class="clearfix">
                                <div style="margin-top: 4px;margin-right: 20px;" class="pull-left">外部订单号:
                                    <span class="c-gray">4004142001201606137215485085</span>
                                </div>
                                <div style="margin-top: 4px;" class="pull-left">支付流水号:
                                    <span class="c-gray">201606131929006194418201</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" class="text-right order_body">
                            <p class="order_action">
                                <a class="more" href="">查看详情</a>
                                <a class="info" href="javascript:void(0);" data-index="0">-备注</a>
                                <a class="add_pss" href="javascript:void(0);">-加星</a>
                               <a class="star_score">-<img src="{{ config('app.source_url') }}static/images/star-on.png">
                               x <span class="score"></span></a>
                            </p>
                            <p class="star_container">
                                <span class="delete_star">去星</span>
                                <span class="star" data-id="0" data-click="0"></span>
                            </p>
                        </td>
                    </tr>
                    <tr class="content-row">
                        <td class="image-cell">
                            <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FisJYKhuA-ma_n8mKQBeYgX9J88X.jpg?imageView2/2/w/100/h/100/q/75/format/webp"></td>
                        <td class="title-cell">
                            <p class="goods-title">
                                <a href="javascript:void(0);" target="_blank" class="new-window" title="炸蛋人零食 原味猪肉脯/肉干220g 正宗经典休闲蜜汁叉烧 满68包邮">炸蛋人零食 原味猪肉脯/肉干220g 正宗经典休闲蜜汁叉烧 满68包邮</a>
                            </p>
                            <p>
                                <span class="goods-sku">原味</span>
                            </p>
                        </td>
                        <td class="price-cell">
                            <p>17.90</p>
                            <p>(1件)</p>
                        </td>
                        <td class="aftermarket-cell" rowspan="1"></td>
                        <td class="customer-cell" rowspan="1">
                            <p>坚持不懈</p>
                            <p class="user-name">严建平</p>
                            15970355115
                        </td>
                        <td class="time-cell" rowspan="1">
                            <div class="td-cont">2016-06-13 19:28:32</div>
                        </td>
                        <td class="state-cell" style="padding-left: 5px;padding-right: 5px;" rowspan="1">
                            <div class="td-cont">
                                <p class="js-order-state">交易完成
                                    <!-- 同城送货订单 --></p>
                                <!-- 自提核销 -->
                            </div>
                        </td>
                        <td class="pay-price-cell" rowspan="1">
                            <div class="td-cont text-center">
                                <div>26.90
                                    <br>
                                    <span class="c-gray">(含运费: 9.00)</span>
                                    <br>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="remark-row">
                        <td colspan="8">商家备注：</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr class="separation-row">
                        <td colspan="8"></td>
                    </tr>
                    <tr class="header-row">
                        <td colspan="6">
                            <div>订单号: E20160613192832035378201
                                <div class="help" style="display: inline-block;">
                                    <span class="js-help-notes c-gray" data-class="bottom" style="cursor: help;">微信安全支付－代销</span>
                                    <div class="js-notes-cont hide">该订单通过会搜云代销服务完成交易，请进入“收入/提现”页面，“微信支付”栏目查看收入或提现</div>
                                </div>
                            </div>
                            <div class="clearfix">
                                <div style="margin-top: 4px;margin-right: 20px;" class="pull-left">外部订单号:
                                    <span class="c-gray">4004142001201606137215485085</span>
                                </div>
                                <div style="margin-top: 4px;" class="pull-left">支付流水号:
                                    <span class="c-gray">201606131929006194418201</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" class="text-right order_body">
                            <p class="order_action">
                                <a class="more" href="">查看详情</a>
                                <a class="info" href="javascript:void(0);" data-index="1">-备注</a>
                                <a class="add_pss" href="javascript:void(0);">-加星</a>
                               <a class="star_score">-<img src="{{ config('app.source_url') }}static/images/star-on.png">
                               x <span class="score"></span></a>
                            </p>
                            <p class="star_container">
                                <span class="delete_star">去星</span>
                                <span class="star" data-id="0" data-click="0"></span>
                            </p>
                        </td>
                    </tr>
                    <tr class="content-row">
                        <td class="image-cell">
                            <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FisJYKhuA-ma_n8mKQBeYgX9J88X.jpg?imageView2/2/w/100/h/100/q/75/format/webp"></td>
                        <td class="title-cell">
                            <p class="goods-title">
                                <a href="javascipt:void(0);" target="_blank" class="new-window" title="炸蛋人零食 原味猪肉脯/肉干220g 正宗经典休闲蜜汁叉烧 满68包邮">炸蛋人零食 原味猪肉脯/肉干220g 正宗经典休闲蜜汁叉烧 满68包邮</a>
                            </p>
                            <p>
                                <span class="goods-sku">原味</span>
                            </p>
                        </td>
                        <td class="price-cell">
                            <p>17.90</p>
                            <p>(1件)</p>
                        </td>
                        <td class="aftermarket-cell" rowspan="1"></td>
                        <td class="customer-cell" rowspan="1">
                            <p>坚持不懈</p>
                            <p class="user-name">严建平</p>
                            15970355115
                        </td>
                        <td class="time-cell" rowspan="1">
                            <div class="td-cont">2016-06-13 19:28:32</div>
                        </td>
                        <td class="state-cell" style="padding-left: 5px;padding-right: 5px;" rowspan="1">
                            <div class="td-cont">
                                <p class="js-order-state">交易完成
                                    <!-- 同城送货订单 --></p>
                                <!-- 自提核销 -->
                            </div>
                        </td>
                        <td class="pay-price-cell" rowspan="1">
                            <div class="td-cont text-center">
                                <div>26.90
                                    <br>
                                    <span class="c-gray">(含运费: 9.00)</span>
                                    <br>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="buy-remark-row">
                        <td colspan="8">买家备注：</td>
                    </tr>
                    <tr class="remark-row">
                        <td colspan="8">商家备注：</td>
                    </tr>   
                </tbody>
            </table>
        </div>
        <div class="js-list-footer-region ui-box"></div>
    </div>
</div>
@endsection
@section('other')
<!-- 弹层 -->
<div class="tip"></div>
<!-- modal -->
<div class="modal export-modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">导出订单</h4>
            </div>
            <div class="modal-body">
                <div class="clearfix">
                    <div class="filter-meta">
                        <span>订单号：</span> <span>-</span>
                    </div>
                    <div class="filter-meta">
                        <span>下单时间：</span> 
                        <span>2016-12-13 00:00:00 至 2017-01-15 00:00:00</span>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="filter-meta">
                        <span>外部单号：</span><span>-</span>
                    </div>
                    <div class="filter-meta">
                        <span>订单类型：</span> <span>全部</span>
                    </div>
                    <div class="filter-meta">
                        <span>付款方式：</span> <span>全部</span>
                    </div>
                    <div class="filter-meta">
                        <span>收货人姓名：</span> <span>-</span>
                    </div>
                    <div class="filter-meta">
                        <span>订单状态：</span> <span>全部</span>
                    </div>
                    <div class="filter-meta">
                        <span>物流方式：</span> <span>全部</span>
                    </div>
                    <div class="filter-meta">
                        <span>收货人手机：</span> <span>-</span>
                    </div>
                    <div class="filter-meta">
                        <span>微信昵称：</span> <span>-</span>
                    </div>
                    <div class="filter-meta">
                        <span>维权状态：</span> <span>全部</span>
                    </div>
                </div>
                <div class="explain">
                    <h4>为了给你提供更好的查询性能以及体验，我们对导出功能进行了改进：</h4>
                    <ul>
                        <li>· 为了保证您的查询性能，两次导出的时间间隔请保持在 5 分钟以上。</li>
                        <li>· 我们将为您保留30天内导出的数据，便于您随时导出。</li>
                        <li>· 订单导出E店宝格式的订单报表下线公告 <a href="javascipt:void(0);" target="_blank">了解详情</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-left">
                    <a href="javascript:void(0);" target="_blank" class="zent-btn zent-btn-large js-export" data-export-type="default">生成普通报表</a>
                    <a href="javascript:void(0);" target="_blank" class="zent-btn zent-btn-large js-export" data-export-type="account_check">生成对账单</a>
                    <a href="javascript:void(0);" target="_blank" class="zent-btn zent-btn-large js-export" data-export-type="peerpay">生成代付对账单</a>
                    <a href="javascript:void(0);" target="_blank" class="zent-btn zent-btn-large pull-right js-goto-export-list">查看已生成报表</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 备注model开始 -->
<div class="modal export-modal" id="baseModal">
    <div class="modal-dialog" id="base-modal-dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">商家备注</h4>
                </div>
                <div class="modal-body">
                    <textarea class="js-remark form-control" rows="4" placeholder="最多可输入256个字符" maxlength="256"></textarea>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-primary submit_info">提交</a>
                </div>
            </div>
        </form>
    </div>
</div>
<!--backdrop-->
<div class="modal-backdrop"></div>
@endsection
@section('page_js')
<script type="text/javascript">
    var STATIC_URL = "{{ config('app.source_url') }}static";
</script>
<!-- layer选择时间插件 -->
<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!-- 星星插件 -->
<script src="{{ config('app.source_url') }}static/js/jquery.raty.min.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/order_tbdo2eag.js"></script>
@endsection