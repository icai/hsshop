@extends('merchants.default._layouts')
@section('head_css')
        <!-- 当前模块公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_llbq22x2.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_62zq70mn.css" />
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
<!--批量发货-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_2xxu7jno.css"/>

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
              
                <li>
                    <a href="">分销订单</a>
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
        <div class="widget-list">
            <div class="js-list-filter-region clearfix">
                <div class="widget-list-filter">
                    <form class="form-horizontal ui-box list-filter-form" method="get" action="">
                        <div class="clearfix">
                            <div class="filter-groups">
                                <div class="control-group">
                                    <label class="control-label">
                                        <select name="field" class="js-label-select" id="infoFilter">
                                            <option value="oid" @if(request('field') == 'oid')selected @endif>订单编号</option>
                                            <option value="address_name" @if(request('field') == 'address_name')selected @endif>客户姓名</option>
                                            <option value="address_phone" @if(request('field') == 'address_phone')selected @endif>预留手机号</option>
                                        </select>
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="search" id="infoFilterValue" class="js-order-text" value="{{ request('search') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="pull-left">
                                <div class="time-filter-groups clearfix">
                                    <div class="control-group">
                                        <label class="control-label">下单时间：</label>
                                        <div class="controls">
                                            <input type="text" name="start_time" value="{{ request('start_time') }}" class="js-start-time hasDatepicker" id="startDate">
                                            <span>至</span>
                                            <input type="text" name="end_time" value="{{ request('end_time') }}" class="js-end-time hasDatepicker" id="endDate">
                                            <span class="date-quick-pick" data-days="7">近7天</span>
                                            <span class="date-quick-pick" data-days="30">近30天</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-groups">
                                    <div class="control-group">
                                        <label class="control-label">订单状态：</label>
                                        <div class="controls">
                                            <select name="status" id="status" class="js-state-select">
                                               <option value="">全部</option>
                                               <option value="2" @if(request('status') == 2) selected @endif>已支付</option>
                                               <option value="4" @if(request('status') == 4) selected @endif>已完成</option>
                                               <option value="5" @if(request('status') == 5) selected @endif>已关闭</option>
                                               <option value="6" @if(request('status') == 6) selected @endif>退款中</option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input class="zent-btn zent-btn-primary js-filter" type="submit" value="筛选" />
                                <a href="javascript:;" class="zent-btn js-export">批量导出</a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- 导航模块 开始 -->
                    <div class="nav_module clearfix">
                        <!-- 左侧 开始 -->
                        <div class="pull-left">
                            <!-- （tab试导航可以单独领出来用） -->
                            <!-- 导航 开始 -->
                            <ul class="tab_nav">
                                 <li @if(request('status',0) == 0) class="hover" @endif>
                                    <a href="/merchants/order/hexiaoOrder">全部</a>
                                </li>
                                <li @if(request('status') == 2) class="hover" @endif>
                                    <a href="/merchants/order/hexiaoOrder?status=2">已支付</a>
                                </li>
                                <li @if(request('status') == 4) class="hover" @endif>
                                    <a href="/merchants/order/hexiaoOrder?status=4">已完成</a>
                                </li>
                                <li @if(request('status') == 5) class="hover" @endif>
                                    <a href="/merchants/order/hexiaoOrder?status=5">已关闭</a>
                                </li>
                                <li @if(request('status') == 6) class="hover" @endif>
                                    <a href="/merchants/order/hexiaoOrder?status=6">退款中</a>
                                </li>
                            </ul>
                            <!-- 导航 结束 -->
                        </div>
                        <!-- 左侧 结算 -->
                        <!-- 右边 开始-->
                        <div class="pull-right">
                           
                        </div>
                        <!-- 右边 结束 -->
                    </div>
                    <!-- 导航模块 结束 -->
                </div>
            </div>
            <table class="table ui-table-order">
                <thead>
                <tr class="widget-list-header">
                    <th class="text-left" colspan="2"> 
                        <input type="checkbox" id="cb_all" class="t-checkbox" /> 商品</th>
                    <th class="price-cell" style="text-align: center">单价/数量</th>
                    <th class="price-cell" style="text-align: center">商品编码</th>
                    <th class="aftermarket-cell">售后</th>
                    <th class="customer-cell">买家</th>
                    <th class="time-cell">
                        <a href="javascript:void(0);">
                            下单时间
                            <span class="orderby-arrow desc"></span>
                        </a>
                    </th>
                    <th class="state-cell">订单状态</th>
                    <th class="pay-price-cell">实付金额</th>
                </tr>
                </thead>
                    <tbody>
                    @forelse($data as $key=>$val)
                    <tr class="separation-row">
                        <td colspan="8"></td>
                    </tr>
                    <tr class="header-row" data-oid="{{ $val['id'] }}">
                        <td colspan="6">
                            <div><input type="checkbox" name="cb_order" value="345" class="t-checkbox" />订单号
                                {{ $val['oid'] }}
                            </div>
                            <div class="clearfix">
                                <div style="margin-top: 4px;" class="pull-left">支付流水号:
                                    <span class="c-gray">{{ $val['serial_id'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" class="text-right order_body">
                            <p class="order_action">
                                <a class="more" href="{{ URL('merchants/order/orderDetail', $val['id']) }}">查看详情</a>
                                <a class="info" href="javascript:void(0);" data-index="2" data-id="{{ $val['id'] }}">-备注</a>
                            </p>
                        </td>
                    </tr>
                        @foreach($val['orderDetail'] as $pro)
                        <tr class="content-row">
                            <td class="image-cell">
                                <img class="lazy" width="60" height="60" src="{{ imgUrl($pro['img']) }}" data-original="435345">
                            </td>
                            <td class="title-cell">
                                <p class="goods-title">
                                    <a href="javascript:void(0);" class="new-window" title="234234">{{ $pro['title'] }}</a>
                                </p>
                                <p>
                                    <span class="goods-sku">{{ $pro['spec'] }}</span>
                                </p>
                            </td>
                            <td class="price-cell" style="text-align: center">
                                <p>{{ $pro['price'] }}</p>
                                <p>{{ $pro['num'] }}件</p>
                            </td>
                            <td class="price-cell" style="border-left:1px solid #f2f2f2;text-align: center">
                                <p>{{ $pro['product_code'] }}</p>
                            </td>
                            <td class="aftermarket-cell">
                               
                            </td>
                            @if ( $loop->index == 0 )
                                <td class="customer-cell" rowspan="{{ $loop->count }}">
                                    <p class="user-name">{{ $val['address_name'] }}</p>
                                    {{ $val['address_phone'] }}
                                </td>
                                <td class="time-cell" rowspan="{{ $loop->count }}">
                                    <div class="td-cont">{{ $val['created_at'] }}</div>
                                </td>
                                <td class="state-cell" style="padding-left: 5px;padding-right: 5px;" rowspan="{{ $loop->count }}">
                                    <div class="td-cont">
                                        @if($val['status'] == 1)
                                            @if($val['refund_status'] == 1)
                                            <p class="js-order-state">申请退款中</p>
                                            @elseif($val['refund_status'] == 2)
                                            <p class="js-order-state">申请退款被拒</p>
                                            @elseif($val['refund_status'] == 3)
                                            <p class="js-order-state">退款中</p>
                                            @elseif($val['refund_status'] == 4)
                                            <p class="js-order-state">退款完成</p>
                                            @elseif($val['refund_status'] == 5)
                                            <p class="js-order-state">买家取消退款</p>
                                            <p><button class="btn btn_complete_order js-express-goods btn_single_node" data-id="{{ $val['id'] }}" style="margin-right: 0;">结单</button></p>
                                            @else
                                            <p class="js-order-state" style="color:green">买家已付款</p>
                                            <p><a href="/merchants/marketing/orderHexiao" class="btn btn-small" style="font-size: 12px;padding: 2px 10px;background: #f8f8f8;border: 1px solid #ddd;color: #666">结&nbsp;&nbsp;单</a></p>
                                            @endif
                                        @elseif($val['status'] == 2)
                                        <p class="js-order-state">买家已提货</p>
                                        @elseif($val['status'] == 3)
                                        <p class="js-order-state" style="color:blue;">已完成</p>
                                        @elseif($val['status'] == 4)
                                        <p class="js-order-state">已关闭</p>
                                        @elseif($val['status'] == 5)
                                        <p class="js-order-state">退款中</p>
                                        @elseif($val['status'] == 0)
                                        <p class="js-order-state" style="color: red;">未付款</p>
                                        <p><button class="btn btn_clear_order js-express-goods" data-id="{{ $val['id'] }}" style="margin-right: 0;">取消订单</button></p> 
                                        @else
                                        <p class="js-order-state">未知状态</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="pay-price-cell" rowspan="{{ $loop->count }}">
                                    <div class="td-cont text-center">
                                        <div>
                                            {{ $val['pay_price'] }}
                                        </div>
                                    </div>  
                                </td>
                            @endif
                        </tr>
                        
                    <tr id="tr{{ $val['id'] }}"  class="remark-row   @if(empty($val['seller_remark']))  seller_info @endif seller_ti">
                        <td colspan="8">商家备注：<span>{{ $val['seller_remark'] }}</span></td>
                    </tr>
                    @if($val['buy_remark'])
                    <tr class="remark-row">
                        <td colspan="8">买家备注：{{ $val['buy_remark'] }}</td>
                    </tr>
                    @endif
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="8">暂无数据</td>
                    </tr>
                    @endforelse
                    </tbody>              
            </table>

            <div class="pr" style="height:auto;min-height: 80px;">
                {{ $pageHtml }}       
            </div> 

        </div>
    </div>
    
    @endsection
    @section('other')
            <!-- 弹层 -->
    <div class="tip"></div>
    <!-- modal -->
    <div class="modal export-modal" id="myModal">
        
        <!--维权弹窗-->
        <div class="layer-wrap none" id="zent-dialog123" style="display: none;">
            <div class="zent-dialog1">
                <p class="rights">订单中的部分商品，买家已提交了退款申请。你需要先跟买家协商，买家撤销退款申请后，才能进行发货操作。</p>
            </div>
            
        </div>
        <!--维权弹窗结束-->

    </div>
    <!--backdrop-->
    <div class="modal-backdrop"></div>
    <!-- 备注model开始 -->
    <div class="modal export-modal" id="baseModal">
        <div class="modal-dialog" id="base-modal-dialog">
            <form id="seller_remark_form" class="form-horizontal">
                {{ csrf_field() }}
                <input id="order_id" type="hidden" name="id" value="" />
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">商家备注</h4>
                    </div>
                    <div class="modal-body">
                        <textarea class="js-remark form-control" name="seller_remark" rows="4" placeholder="最多可输入256个字符" maxlength="256"></textarea>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0)" class="btn btn-primary submit_info">提交</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        
    </script>
    @endsection
    @section('page_js')
    <!-- layer -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <!-- layer选择时间插件 -->
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <!-- 星级评定js插件 -->
    <script type="text/javascript">
        var STATIC_URL = "{{ config('app.source_url') }}static";
    </script>
    <script src="{{ config('app.source_url') }}static/js/jquery.raty.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/hexiao_order.js"></script>
    <!-- 订单公用文件 -->
    <script src="{{ config('app.source_url') }}mctsource/js/order_common.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js"></script>
    <!--批量发货-->
    <script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/order_2xxu7jno.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">     
        //懒加载
        $("img.lazy").lazyload({
            threshold : 200,
            effect : "fadeIn"
        });
    </script>
@endsection
