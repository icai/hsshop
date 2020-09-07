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
<div class="model_box">
    <div class="model_box_div">
        <p>买家已发起维权是否确认发货</p >
        <div class="model_box_btn">
            <button class="btn btn_queren">确认发货</button>
            <button class="btn btn_close">处理维权</button>
        </div>
        <div class="model_close">x</div>
    </div>
</div>
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="common_nav">
                @foreach ($navTypeList as $plv)
                <li @if ( $loop->index == Route::input('nav', '0') )class="hover" @endif>
                    <a href="{{ URL('merchants/order/orderList', [Route::input('menu', '0'), $loop->index]) }}">{{ $plv }}</a>
                </li>
                {{--<li>
                    <a href="">分销订单</a>
                </li>--}}
                @endforeach
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
                                            @foreach ( $fieldList as $key => $value )
                                                @if ( request('field') == $key )
                                                    <option value="{{ $key }}" selected>{{ $value }}</option>
                                                @else
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="search" id="infoFilterValue" class="js-order-text" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">订单类型：</label>
                                    <div class="controls">
                                        <select name="order_type" id="order_type" class="js-type-select">
                                            @foreach ( $typeList as $key => $value )
                                                @if ( request('order_type') == $key )
                                                    <option value="{{ $key }}" selected>{{ $value }}</option>
                                                @else
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">订单来源：</label>
                                    <div class="controls">
                                        <select name="order_source" id="order_source" class="js-type-select"> 
                                            <option value="">全部</option>
                                            <option value="1" @if(Request('order_source') == 1) selected @endif>微商城</option>
                                            <option value="2" @if(Request('order_source') == 2) selected @endif>微信小程序</option>
                                            <option value="3" @if(Request('order_source') == 3) selected @endif>支付宝小程序</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">维权状态：</label>
                                    <div class="controls">
                                        <select name="refund_status" id="refund_status" class="js-feedback-select">
                                            @foreach ( $refundStatusList as $key => $value )
                                                @if ( request('refund_status') == $key )
                                                    <option value="{{ $key }}" selected>{{ $value }}</option>
                                                @else
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endif
                                            @endforeach
                                        </select>
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
                                                @foreach ( $statusList as $key => $value )
                                                    @if ( request('status') == $key )
                                                        <option value="{{ $key }}" selected>{{ $value }}</option>
                                                    @else
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endif
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">付款方式：</label>
                                        <div class="controls">
                                            @if ( Route::input('nav') == 3 )
                                                <select name="pay_way" id="pay_way" class="js-buyway-select" disabled>
                                                    @else
                                                        <select name="pay_way" id="pay_way" class="js-buyway-select">
                                                            @endif
                                                            @foreach ( $payWayList as $key => $value )
                                                                @if ( request('pay_way') == $key || ( Route::input('nav') == 3 and $key == 4 ) )
                                                                    <option value="{{ $key }}" selected>{{ $value }}</option>
                                                                @else
                                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="control-group" @if($takeAwayConfig == 1)style="display: none"@endif>
                                        <label class="control-label">打单状态：</label>
                                        <div class="controls">
                                            <select name="logistics_status" id="logistics_status" class="js-type-select">
                                                <option value="">全部</option>
                                                <option value="1" @if(Request('logistics_status') == 1) selected @endif>已导入</option>
                                                <option value="2" @if(Request('logistics_status') == 2) selected @endif>已打单</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-groups">
                                    <div class="control-group">
                                        <label class="control-label">物流方式：</label>
                                        <div class="controls">
                                            @if ( in_array( Route::input('nav'), [1,2] ) )
                                                <select name="express_type" id="express_type" class="js-express-select" disabled>
                                                    @else
                                                        <select name="express_type" id="express_type" class="js-express-select">
                                                            @endif
                                                            @foreach ( $expressList as $key => $value )
                                                                @if ( request('express_type') == $key || ( Route::input('nav') == 1 and $key == 3 ) || ( Route::input('nav') == 2 and $key == 2 ) )
                                                                    <option value="{{ $key }}" selected>{{ $value }}</option>
                                                                @else
                                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input class="zent-btn zent-btn-primary js-filter" type="submit" value="筛选" style="border-radius: 3px; border-color: #337ab7;"/>
                                <a href="javascript:void(0);" class="zent-btn js-export " style="border-radius: 3px;">批量导出</a>
                            </div>
                        </div>
                    </form>
                            </div>
        			<a class="btn btn-primary fahuo js-batch" data-toggle="modal" data-target="#myModal" @if($takeAwayConfig == 1)style="display: none"@endif>批量发货</a>
                    <!-- Modal -->

                    <a class="btn btn-primary fahuo daying" @if($takeAwayConfig == 1)style="display: none"@endif>打印销售单</a>
                    @if($admin_del_show == 1)
                    <a class="btn btn-primary js-deleteAll" >批量删除</a>
                    @endif
                    <!--add by 韩瑜 date 2018.6.29-->
                    <a class="btn btn-primary js-importBill" @if($takeAwayConfig == 1)style="display: none"@endif>导入快递管家</a>
                    <a class="btn btn-primary js-quickBill" @if($takeAwayConfig == 1)style="display: none"@endif>快速打单</a>
                    <!--end-->
                    <a class="btn btn-primary js-fahuoRecord" href="/merchants/order/batchDeliveryLog" @if($takeAwayConfig == 1)style="display: none"@endif>批量发货记录</a>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    	<div class="modal-dialog" role="document">
                    		<div class="modal-content">
                    			<div class="modal-header">
                    				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    				<h4 class="modal-title" id="myModalLabel">上传文件</h4>
                    			</div>
                    			<form id="defaultForm" method="post" class="form-horizontal" action="BatchDelivery" enctype="multipart/form-data" >
	                    			<div class="modal-body">
	                    				<div class="fileName">
	                    					<div class="file-nam"><span class="pp">文件名 :</span><span class="file_name">1.csv</span></div>
	                    					<div class="file-nam"><span class="pp">文件大小 :</span><span class="file_size">0.16KB</span></div>       								        								
	        							</div>
	                    				<div class="file-siz">文件大小不能大于1MB</div>
	                    				<div class="form-group">
	                    					<label class="col-lg-2 control-label" for=""></label>
	                    					<div class="">
	                    						<div class="file_board position_rel">
	                    							<a href="##" class="add_file">选择文件...</a>                    							
		                    						<input type="file" name="info" id="add_file" class="form-control position_abs" value="" accept="*.csv" />		                    					
	                    						</div>
	                    						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	                    						<p class="hint">最大支持1MB CSV的文件。</p>    							
	                    						<p><a class="aj" href="/merchants/order/BatchDeliveryTemplate">下载批量发货模板</a></p>
                                                <p class="hint">上传时请将文件另存为.csv格式</p>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="error_record" style="display:none">
                                        <div class="error_list"></div>
                                        <div class="record-num">
                                            <a class="btn btn-more" href="/merchants/order/batchDeliveryLog">查看更多</a>
                                            <div class="num-update">
                                                <div class="success-num">上传成功&nbsp;<span>0</span></div>
                                                <div class="error-num">上传失败&nbsp;<span>0</span></div>
                                            </div>
                                        </div>
                                            
                                    </div>
	                    			<div class="modal-footer">
	                    				<button type="button" class="btn btn-primary btn-csv">确认上传</button>
	                    			</div>
	                    		</form>	
                    		</div>
                    	</div>
                    </div>
                </div>
            </div>
            
            <!-- 导航模块 开始 -->
            <div class="nav_module clearfix">
                <!-- 左侧 开始 -->
                <div class="pull-left">
                    <!-- （tab试导航可以单独领出来用） -->
                    <!-- 导航 开始 -->
                    <ul class="tab_nav">
                        @if(Route::input('menu') == 2)
                            <!-- 维权订单导航 -->
                            @foreach ($refundStatusList as $k => $v)
                                @if ( $k == request('refund_status', 0) )
                                    <li class="hover">
                                @else
                                    <li>
                                @endif
                                <a href="{{ URL('merchants/order/orderList', [Route::input('menu', '0'), Route::input('nav', '0') ]) }}?refund_status={{ $k }}&order_type={{request('order_type')}}">{{ $v }}</a>
                            </li>
                            @endforeach
                            @elseif(Route::input('menu') == 3)
                            <li class="hover">
                                <a href="">全部</a>
                            </li>
                            <li>
                                <a href="">已支付</a>
                            </li>
                            <li>
                                <a href="/merchants/order/orderList/0/0?status=4&order_type=">已完成</a>
                            </li>
                            <li>
                                <a href="/merchants/order/orderList/0/0?status=5&order_type=">已关闭</a>
                            </li>
                            <li>
                                <a href="/merchants/order/orderList/0/0?status=6&order_type=">退款中</a>
                            </li>
                        @else
                            <!-- 其他订单导航 -->
                            @foreach ($statusList as $key=>$slv)
                                @if($key <> 100)
                                    @if ( $key == request('status', 0) )
                                        <li class="hover">
                                    @else
                                        <li>
                                    @endif
                                    <a href="{{ URL('merchants/order/orderList', [Route::input('menu', '0'), Route::input('nav', '0') ]) }}?status={{$key}}&order_type={{request('order_type')}}">{{ $slv }}</a>
                                </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                    <!-- 导航 结束 -->
                </div>
                <!-- 左侧 结算 -->
                <!-- 右边 开始-->
                <div class="pull-right">
                    <!-- 搜素框~~或者自己要写的东西 -->
                </div>
                <!-- 右边 结束 -->
            </div>
            <!-- 导航模块 结束 -->
            <table class="table ui-table-order">
                <thead>
                <tr class="widget-list-header">
                    <th class="text-left" colspan="2"> 
                        <input type="checkbox" id="cb_all" class="t-checkbox" /> 商品
                    </th>

                    <th class="price-cell" style="text-align: center">单价/数量</th>
                    <th class="price-cell" style="text-align: center">商品编码</th>
                    <th class="aftermarket-cell">售后</th>
                    <th class="customer-cell">买家</th>
                    <th class="time-cell">
                        <a href="javascipt:void(0);">
                            下单时间
                            <span class="orderby-arrow desc"></span>
                        </a>
                    </th>
                    <th class="state-cell">订单状态</th>
                    <th class="pay-price-cell">实付金额</th>
                </tr>
                </thead>
                @forelse ( $list['data'] as $value )
                    @if($value['admin_del'] == 0)
                    <tbody>
                    <tr class="separation-row">
                        <td colspan="8"></td>
                    </tr>
                    <tr class="header-row" data-oid="{{$value['id']}}" data-mid="{{ $value['mid'] }}" data-addrId="{{ $value['address_id'] }}">
                        <td colspan="6">
                            <div><input type="checkbox" name="cb_order" data-id="{{$value['id']}}" value="{{$value['oid']}}" class="t-checkbox" />订单号: {{ $value['oid'] }}

                                @if($value['type'] == 7)
                                    <span class="c-gray">秒杀</span>
                                @elseif($value['type'] == 10)
                                    <button class="c-gray share-eventBtn" data-oid="{{ $value['id'] }}">享立减订单</button>
                                @elseif($value['type'] == 11)
                                    <button class="c-gray share-eventBtn" data-oid="{{ $value['id'] }}">集赞订单</button>
                                @elseif($value['type'] == 12)
                                    <p class="order-import kami-tips">虚拟卡密</p>
                                @endif

                                @if($value['distribute_type'] == 1)
                            	 <div class="order-dis" data-oid="{{$value['id']}}">分销</div>
                                @endif
                                @if($value['groups_id'] != 0)
                                    <span class="c-gray">拼团订单</span>
                                @endif
                                @if($value['discount'] != 0)
                                    <span class="c-gray">满减订单</span>
                                @endif
                                @if($value['is_hexiao'] == 1)
                                    <span class="c-gray">到店自提</span>
                                @endif
                                <!-- 何书哲 2018年7月2日 订单已导入和已打单显示 -->
                                @if($value['is_import'] == 1)
                                    <p class="c-gray order-import">已导入</p>
                                @endif
                                @if($value['is_print'] == 1)
                                    <p class="c-gray order-import">已打单</p>
                                @endif


                            </div>
                            <div class="clearfix">
                                <div style="margin-top: 4px;" class="pull-left">支付流水号:
                                    <span class="c-gray">{{ $value['serial_id'] }}</span>
                                </div>
                                
                            </div>
                            <!-- 何书哲 2018年8月1日 修改订单来源 -->
                            <div>
                                    订单来源：
                                    @if($value['source'] == 0)
                                        <span>微商城</span>
                                    @elseif($value['source'] == 1)
                                    <span>微信小程序 @if($value['xcxTitle']) ({{ $value['xcxTitle'] }})@endif </span>
                                    @elseif($value['source'] == 2)
                                        <span>支付宝小程序</span>
                                    @endif

                                </div>
                        </td>
                        <td colspan="2" class="text-right order_body">
                            <p class="order_action">
                                @if($admin_del_show == 1)
                                <a class="more js-delete" href="javascript:void(0);" data-index="{{ $loop->index }}" data-id="{{ $value['id'] }}">删除-</a>
                                @endif
                                <a class="more" href="{{ URL('/merchants/order/orderDetail', $value['id']) }}">查看详情</a>
                                <a class="info" href="javascript:void(0);" data-index="{{ $loop->index }}" data-id="{{ $value['id'] }}">-备注</a>
                                @if ( empty( $value['star_level'] ) )
                                    @if($value['is_hexiao'] == 0)
                                    <a class="add_pss" href="javascript:void(0);" data-id="{{ $value['id'] }}">-加星</a>
                                    @endif
                                    <a class="star_score">-<img src="{{ config('app.source_url') }}static/images/star-on.png">x <span class="score">{{ $value['star_level'] }}</span></a>
                                @else
                                    @if($value['is_hexiao'] == 0)
                                    <a class="add_pss hide" href="javascript:void(0);" data-id="{{ $value['id'] }}">-加星</a>
                                    @endif
                                    <a class="star_score isshow">-<img src="{{ config('app.source_url') }}static/images/star-on.png">x <span class="score">{{ $value['star_level'] }}</span></a>
                                @endif
                            </p>
                            <p class="star_container">
                                <span class="delete_star" data-id="{{ $value['id'] }}">去星</span>
                                <span class="star" data-id="{{ $value['id'] }}" data-click="0"></span>
                            </p>
                        </td>
                    </tr>
                    @foreach ( $value['orderDetail'] as $val )
                        <tr class="content-row">
                            <td class="image-cell">
                                <img class="lazy" width="60" height="60" src="" data-original="{{ imgUrl($val['img']) }}">
                            </td>
                            
                            <td class="title-cell">
                                <p class="goods-title">
                                    <a href="javascript:void(0);" class="new-window" title="{{ $val['title'] }}">{{ $val['title'] }}</a>
                                </p>
                                <p>
                                    <span class="goods-sku">{{ $val['spec'] }}</span>
                                </p>
                            </td>
                            <td class="price-cell" style="text-align: center">
                                @if(in_array('view_order_price',session('permission')??[]))
                                    <p >{{ $val['price'] }}</p>
                                @else
                                    **
                                @endif
                                <p >({{ $val['num'] }}件)</p>
                            </td>
                            <td class="price-cell" style="border:1px solid #f2f2f2;text-align: center">
                                <p >{{ $val['product_code'] }}</p>
                            </td>
                            <td class="aftermarket-cell status_string" @if($val['status_string'] == '买家发起维权') data-id='1' @endif>
                                @if($val['status_string'])
                                    <a href="/merchants/order/refundDetail/{{$value['id']}}/{{$val['product_id']}}/{{$val['product_prop_id']}}" class="bule">
                                        {{$val['status_string']}}
                                    </a>
                                @endif
                            </td>
                            @if ( $loop->index == 0 )

                                <td class="customer-cell" rowspan="{{ $loop->count }}">
                                    <p>{{ $value['address_detail'] }}</p>
                                    <p class="user-name">{{ $value['address_name'] }}</p>
                                    <p>{{ $value['address_phone'] }}<p>
                                    @if($value['status'] == 1 && $value['is_hexiao'] == 0 && $value['type'] <> 12)
                                        <p><button class="btn btn_change_addr" data-oid="{{$value['id']}}" data-mid="{{ $value['mid'] }}">修改地址</button></p>
                                    @endif
                                </td>
                                <td class="time-cell" rowspan="{{ $loop->count }}">
                                    <div class="td-cont">{{ $value['created_at'] }}</div>
                                </td>
                                <td class="state-cell" style="padding-left: 5px;padding-right: 5px;" rowspan="{{ $loop->count }}">
                                    <div class="td-cont">
                                            @if($value['groups_status'] == 1 && $value['status'] ==1)
                                                <p class="js-order-state">待成团</p>
                                            @elseif($value['groups_status'] == 3 && $value['status'] == 7 && $value['refund_status'] == 1)
                                                <p class="js-order-state">未中奖,代申请退款</p>
                                            @else
                                                <p class="js-order-state">
                                                    @if(isset($statusList[$value['status']+1]))
                                                        {{$statusList[$value['status']+1]}}
                                                    @elseif($value['status'] == 2)
                                                        待发货
                                                    @elseif($value['status'] == 9)
                                                        已导入
                                                    @elseif($value['status'] == 10)
                                                        已打单
                                                    @endif
                                                </p>
                                            @endif
                                        
                                        @if($value['status'] == 1)
                                            @if($value['is_hexiao'] == 1)
                                                <p>
                                                    <a href="/merchants/marketing/orderHexiao" class="btn btn-small" style="font-size: 12px;padding: 2px 10px;background: #f8f8f8;border: 1px solid #ddd;color: #666">结&nbsp;&nbsp;单</a>
                                                </p>
                                            @else
                                                @if($value['groups_id'] != 0)
                                                    @if($value['groups_status']==2)
                                                        <p>
                                                            <a href="javascript:;" class="btn btn-small js-express-goods" data-refund="{{ $value['refund_status'] }}" data-type="{{$value['type']}}" @if($value['is_hexiao'] == 1) data-url="{{ URL('/merchants/order/stateMentDetail', $value['id']) }}" @else data-url="{{ URL('/merchants/order/orderDetail', $value['id']) }}" @endif>发&nbsp;&nbsp;货</a>
                                                        </p>
                                                    @elseif($value['groups_status'] ==1)
                                                        <p>
                                                            <a href="javascript:;" class="btn btn-small be_group" data-id="{{$value['id']}}"  @if($value['is_hexiao'] == 1) data-url="{{ URL('/merchants/order/stateMentDetail', $value['id']) }}" @else data-url="{{ URL('/merchants/order/orderDetail', $value['id']) }}" @endif>使成团</a>
                                                        </p>
                                                    @endif
                                                 @else
                                                    <p>
                                                        <a href="javascript:;"  class="btn btn-small js-express-goods" data-refund="{{ $value['refund_status'] }}" data-type="{{$value['type']}}" @if($value['is_hexiao'] == 1) data-url="{{ URL('/merchants/order/stateMentDetail', $value['id']) }}" @else data-url="{{ URL('/merchants/order/orderDetail', $value['id']) }}" @endif>发&nbsp;&nbsp;货</a>
                                                    </p>
                                                @endif
                                            @endif
                                            
                                        @endif
                                        
                                        <!-- status = 1 状态 开始 -->
                                        @if($value['status'] == 0)
                                        <p><button class="btn btn_clear_order js-express-goods" data-id="{{ $value['id'] }}" style="margin-right: 0;">取消订单</button></p>
                                        @endif
                                        <!-- status = 1 状态 结束 -->
                                        <!-- 修改物流按钮 -->
                                        @if($value['status'] == 2 && $takeAwayConfig == 0)
                                            @if($value['is_hexiao'] == 1)
                                            <p><button class="btn js-express-goods" data-id="{{$value['id']}}" style="margin-right: 0;" disabled="disabled">买家已提货</button></p>
                                            @else
                                            <p><button class="btn btn_up_logistics js-express-goods" data-id="{{$value['id']}}" style="margin-right: 0;">修改物流</button></p>
                                            @endif
                                        
                                        @endif
                                        @if(!empty($value['groups_id']))
                                            <a class="new-window" href="/merchants/order/orderList?status={{request('status')}}&order_type={{request('order_type')}}&groups_id={{$value['groups_id']}}">查看同团订单</a>
                                        @endif

                                    </div>
                                </td>
                                <td class="pay-price-cell" rowspan="{{ $loop->count }}">
                                    <div class="td-cont text-center">
                                        <div>
                                            @if(in_array('view_order_price',session('permission')??[]))
                                                {{ $value['pay_price'] }}
                                                <br>
                                                @if($value['is_hexiao'] == 0)
                                                <span class="c-gray">(含运费: {{ $value['freight_price'] }})</span>
                                                @endif
                                                <br>
                                            @else
                                                **
                                                <br>
                                                <span class="c-gray">(含运费: **})</span>
                                                <br>
                                             @endif
                                            <br>
                                            
                                        </div>
                                    </div>  
                                    @if($value['status'] < 1) 
                                    <div>
                                        @if(in_array('view_order_price',session('permission')??[]))
                                            <a href="javascript:;" class="t-bule btn_up_price" data-id="{{$value['id']}}">修改价格</a>
                                        @endif
                                    </div>
                                    @endif
                                    @if(isset($groups[$value['id']]) && $groups[$value['id']]['is_head'] ==1)
                                        团长订单
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    <tr class="remark-row @if(empty($value['seller_remark']))   seller_info @endif seller_tip">
                        <td colspan="8">商家备注：<span>{{ $value['seller_remark'] }}</span></td>
                    </tr>
                    @if(!empty($value['buy_remark']))
                        <tr class="remark-row">
                            <td colspan="8">买家备注：{{ $value['buy_remark'] }}</td>
                        </tr>
                    @endif
                    </tbody>
                    @endif
                @empty
                <div class="no_result">暂无数据</div>            		
                @endforelse
            </table>
            <div class="pr" style="height:auto;min-height: 80px;">
                {{ $pageHtml }}
                <!-- 批量打印 -->
                <div class="express-wrap">
                    <select id="sel_express" class="form-control input-sm t-select">
                        <option value=''>选择快递</option>
                        <option value='ems'>ems</option>
                        <option value='zhongtong'>中通快递</option>
                        <option value='tiantian'>天天快递</option>
                        <option value='shentong'>申通快递</option>
                        <option value='shunfeng'>顺丰快递</option>
                        <option value='yuantong'>圆通快递</option>
                        <option value='yunda'>韵达快递</option>
                        <option value='huitong'>汇通快递</option>
                    </select>
                    <input type="button" class="zent-btn zent-btn-primary" id="btn_express" value="批量打印" >

                        <a href="javascript:void(0);"  class="zent-btn zent-btn-primary" id="btn_export_express">批量导出</a>

                </div>
            </div>
        </div>
    
    @endsection
    @section('other')
            <!-- 弹层 -->
    <div class="tip"></div>
    <!-- modal -->
    <div class="modal export-modal" id="myModalExport">
		<!--发货失败弹窗-->
		<div class="layer-wrap none" id="zent-dialog" style="display: none;"> 
			<div class="zent-dialog1">
				<!-- 提示 -->
			    <div class="t-tips f_warning">
			    	<div class="f_content">
			    		<div class="f_content-wrap">你还未填写默认退货地址，为保证消费体验，请先填写默认退货地址再进行发货哦～</div>
			    	</div>
			    </div>
			    <!-- 包裹1 -->
			    <form class="f_form">
			    	<div class="f_form_control_group">
			    		<label class="f_form_control_label">联系人：</label>
			    		<div class="f_form_controls">
			    			<div class="f_input_wrapper">
			    				<input type="text" class="f_input f_input_t in-lx" placeholder="请填写联系人姓名" />
			    			</div>
			    			<p class="f_error_desc">联系人不能为空</p>
			    		</div>
			    	</div>
			    	<div class="f_form_control_group">
			    		<label class="f_form_control_label">联系方式：</label>
			    		<div class="f_form_controls">
			    			<div class="areacode">
			    				<select class="f_select_text f_selphone">
				    				<option value="6">中国&nbsp;&nbsp;+86</option>
				    			</select>
			    			</div>
			    			<div class="f_input_wrapper phone_num">
			    				<input type="text" class="f_input in-sj" placeholder="请填写手机号" />
			    			</div>
			    			<p class="f_error_desc">请输入正确的手机号</p>
			    		</div>
			    	</div>
			    	<div class="f_form_control_group">
			    		<label class="f_form_control_label">联系地址：</label>
			    		<div class="f_form_controls">
			    			<div class="js-area-layout area-layout" data-area-code="">
								<span>
		                            <select name="member_province" class="js-province address-province">
		                            	<option value=''>选择省份</option>
		                            	@foreach($provinceList as $pro)
		                            	<option value="{{ $pro['id'] }}"> {{ $pro['title'] }}</option>
		                            	@endforeach
		                            </select>
		                       </span>
								<span class="marl-15">
		                            <select name="member_city" class="js-city address-city">
		                            	<option value=''>选择城市</option>								
		                            </select>
		                        </span>
								<span class="marl-15">
		                            <select name="member_county" class="js-county address-county">
		                            	<option value=''>选择地区</option>									
		                            </select>
		                        </span>
							</div>
			    			<p class="f_error_desc">请选择地址</p>
			    		</div>
			    	</div>
			    	<div class="f_form_control_group">
			    		<label class="f_form_control_label">详细地址：</label>
			    		<div class="f_form_controls">
			    			<div class="f_input_wrapper">
			    				<input type="text" class="f_input f_input_t in-dz" placeholder="请填写详细地址，如街道名称，门牌号等信息" />
			    			</div>
			    			<p class="f_error_desc">请输入地址</p>
			    		</div>
			    	</div>
			    </form>
			</div>
			      
		</div>
		<!--发货失败弹窗结束-->
		<!--维权弹窗-->
		<div class="layer-wrap none" id="zent-dialog123" style="display: none;">
			<div class="zent-dialog1">
				<p class="rights">订单中的部分商品，买家已提交了退款申请。你需要先跟买家协商，买家撤销退款申请后，才能进行发货操作。</p>
			</div>
			
		</div>
		<!--维权弹窗结束-->
        <div class="modal-dialog" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">导出订单</h4>
                </div>
                <form class="form-horizontal" id="exportForm" role="form" method="post" action="/merchants/order/export">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="clearfix">
                            <div class="filter-meta">
                                <span>订单号：</span> <span id="exportOrderId">-</span>
                                <input type="hidden" name="exportOrderId"/>
                            </div>
                            <div class="filter-meta">
                                <span>下单时间：</span>
                                <span id="exportStart"></span>
                                <input type="hidden" name="exportStart"/>
                                至
                                <span id="exportEnd"></span>
                                <input type="hidden" name="exportEnd"/>
                            </div>
                        </div>
                        <div class="clearfix">
                            <!--<div class="filter-meta">
                                <span>外部单号：</span><span>-</span>
                            </div>-->
                            <div class="filter-meta">
                                <span>订单类型：</span> <span id="exportOrderType">全部</span>
                                <input type="hidden" name="exportOrderType"/>
                            </div>
                            <div class="filter-meta">
                                <span>付款方式：</span> <span id="exportPayWay">全部</span>
                                <input type="hidden" name="exportPayWay"/>
                            </div>
                            <div class="filter-meta">
                                <span>收货人姓名：</span> <span id="exportBuyerName">-</span>
                                <input type="hidden" name="exportBuyerName"/>
                            </div>
                            <div class="filter-meta">
                                <span>订单状态：</span> <span id="exportOrderStatus">全部</span>
                                <input type="hidden" name="exportOrderStatus"/>
                            </div>
                            <div class="filter-meta">
                                <span>物流方式：</span> <span id="exportExpressType">全部</span>
                                <input type="hidden" name="exportExpressType"/>
                            </div>
                            <div class="filter-meta">
                                <span>收货人手机：</span> <span id="exportBuyerPhone">-</span>
                                <input type="hidden" name="exportBuyerPhone"/>
                            </div>
                            <!--<div class="filter-meta">
                                <span>微信昵称：</span> <span>-</span>
                            </div>-->
                            <div class="filter-meta">
                                <span>维权状态：</span> <span id="exportRefundStatus">全部</span>
                                <input type="hidden" name="exportRefundStatus"/>
                            </div>
                        </div>
                        <div class="explain">
                            <h4>为了给你提供更好的查询性能以及体验，我们对导出功能进行了改进：</h4>
                            <ul>
                                <li>· 为了保证您的查询性能，两次导出的时间间隔请保持在 5 分钟以上。</li>
                                <li>· 我们将为您保留30天内导出的数据，便于您随时导出。</li>
                            </ul>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-left" id="exportBtns">
                            <input type="hidden" id="exportType" name="exportType"/>
                            <input type="hidden" id="page" name="page" value="{{$page}}"/>
                            <input type="hidden" id="orderListType" name="orderListType" value="{{$orderListType}}"/>
                            <a href="javascript:void(0);" class="zent-btn zent-btn-large js-export" data-export-type="order">生成普通报表</a>
                            <a href="javascript:void(0);" class="zent-btn zent-btn-large js-export" data-export-type="bill">生成对账单</a>
                            <a href="javascript:void(0);" class="zent-btn zent-btn-large js-export" data-export-type="otherBill">生成代付对账单</a>
                            
                            <a href="javascript:void(0);" class="zent-btn zent-btn-large js-export" data-export-type="delivery">生成发货格式报表</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>    
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
	<!--分销详情弹窗-->
	<div class="order-alert">
		<div class="order-list">
			<div class="order-bor">
				<p>分销明细</p>
				<div class="order-x">
					<img width="10" height="10" src="{{ config('app.source_url') }}mctsource/images/guanbi-x.png"/>
				</div>				
			</div>
			<table border="" cellspacing="" cellpadding="">
				<tr>
					<th>用户ID</th>
					<th>用户名</th>
					<th>佣金</th>
					<th>佣金状态</th>
				</tr>
			</table>
		</div>
	</div>
    <!--发货弹框-->
    <div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none" class="bg000"></div>
    <div class="zent-dialog widget-order-express" style="top: calc(50vh - 270px);display:none"><div class="zent-dialog-header ">
        <h3 class="zent-dialog-title">商品发货</h3>
        <a href="javascript:;" class="zent-dialog-close">×</a>
    </div>
    <div class="zent-dialog-body">
        <!-- 周期购订单发货step -->
        

        <div class="js-total-express total-express">待发货 <span class="shop_num"></span>，已选 <span class="choose_num">0</span></div>

        <div class="add-express-table-control">
            <div class="js-modal-table">
                <table class="ui-table">
                    <thead>
                        <tr class="card_nomal">
                            <th class="text-right cell-5">
                                <input type="checkbox" class="js-check-all">
                            </th>
                            <th class="cell-35">商品</th>
                            <th class="cell-15">数量</th>
                            <th class="cell-30">物流 | 单号</th>
                            <th class="cell-20">状态</th>
                        </tr>
                        <tr class="card_mi">
                            <th class="text-right cell-5">
                                <input type="checkbox" class="js-check-all">
                            </th>
                            <th class="cell-25">商品</th>
                            <th class="cell-15">数量</th>
                            <th class="cell-15">卡密库存</th>
                            <th class="cell-30">卡密活动</th>
                            <th class="cell-10">状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right">
                                <input type="checkbox" class="js-check-item">
                            </td>
                            <td>
                                <div>
                                    <a href="javascript:void(0);" class="new-window" target="_blank">
                                test商品002
                
                                    </a>
                                </div>
                                <div></div>
                            </td>
                            <td>1</td>
                            <td></td>
                            
                            <td class="green"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="js-goods-tips hide error-goods-tips">请选择发货商品</p>
        </div>
        <form onsubmit="return false;" class="form-horizontal">
            
            <div class="control-group">
                <label class="control-label">发货方式：</label>
                <div class="controls">
                    <label class="radio inline">
                        <input type="radio" name="no_express" value="0" checked="" class="radio_express" data-validate="no">物流发货
                    </label>
                    
                    <label class="radio inline">
                        <input type="radio" class="remover-check radio_express" name="no_express" value="1" data-validate="no">无需物流
                    </label>
                </div>
            </div>
            
            <div class="store-express-info clearfix control-2-col control-group js-store-express-info" style="display: none;">
                <div class="control-group">
                    <label class="control-label">门店名称：</label>
                    <div class="controls">
                        <span class="control-label control-label--store-name"></span>
                    </div>
                </div>
                <div class="control-group control-group--memo">
                    <label class="control-label control-label--long">发货单备注：</label>
                    <div class="controls">
                        <input type="text" name="express_comment" placeholder="最多留言100个字（非必填）">
                    </div>
                </div>
            </div>
            <div class="clearfix control-2-col js-express-info control-group">
                <div class="control-group">
                    <label class="control-label">物流公司：</label>
                    <div class="controls">
                        <select class="js-company-1 select2-offscreen" name="express_id" tabindex="-1">
                            <option value="0">请选择一个物流公司</option>
                        </select>
                    </div>
                </div>
                <div class="control-group js-express-name-group hide" style="display: none;">
                    <label class="control-label">快递名称：</label>
                    <div class="controls">
                        <input type="text" class="input" name="express_name" value="">
                    </div>
                </div>
                <div class="control-group js-express-name-group hide" style="display: none;"></div>
                <div class="control-group">
                    <label class="control-label">快递单号：</label>
					<select class="js-company-2 select2-offscreen" name="express_id" tabindex="-1">
                        <option value="0">选择快递单号</option>
                    </select>
                    <input style="display: none;" type="text" id="" class="js-company-3" value="" />
                    <a href="javascript:void(0)" class="custom_button-1" style="display: none;">手动输入</a>
                    <a href="javascript:void(0)" class="custom_button-2" style="display: none;">取消手动输入</a>
                </div>
                <div class="help-desc" style="clear: both;">
                    *请仔细填写物流公司及快递单号，发货后24小时内仅支持做一次更正，逾期不可修改
                </div>
                <div class="custom_modal" style="display:none;color: #ff0000;padding-top: 5px;">
					*该订单已经用快递管家打过单了，请您慎重修改物流信息	。
    			</div>
            </div>
            <div class="control-group">
                <label class="control-label">收货信息：</label>
                <div class="controls">
                    <div class="control-action">
                        河北省 保定市 容城县  西牛村 西牛鲜奶订购点, l, 13111111111
                    </div>
                </div>
            </div>
        </form>
        
    </div>
        <div class="zent-dialog-footer">
        
            <a href="javascript:;" class="zent-btn zent-btn-primary js-save">确定</a>
            <a href="javascript:;" class="zent-btn js-cancel">取消</a>
        
        </div>
    </div>
<!--享立减订单弹框-->
    <div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none" class="enjoySubtractionBox"></div>
	<div class="enjoySubtraction">
		<p>享立减用户 <a class="delete_shan" href="javascript:;">X</a></p>
		<p>同IP最多人数值：<span class="maxMun">18</span></p>
		<ul class="eSTop">
			<li>头像</li>
			<li>昵称</li>
			<li>设备型号</li>
			<li>助减时间</li>
			<li>地址</li>
		</ul>
		<div class="eSContnentBox">
		</div>
		
		<div class="enjoySubtractionDiv">
			共<span class="eSDSpano">0</span>条，
			每页<span>8</span>条，
			共<span class="eSDSpanye">0</span>页，
			<span class="previousPage">上一页</span>
			<span class="nextPage">下一页</span>
		</div>
	</div>
    <!--前往配置快递管家参数弹窗-->
    <div class="modal export-modal" id="SetPrintType" style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none">
        <div class="modal-dialog" id="SetPrintType_content">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">提示</h4>
                </div>
                <div class="modal-body">
                	<div class="SetPrintType_title">快递管家参数未配置！</div>
                	<div>请前往<a href="/merchants/order/printOrder">订单--快速打单</a>配置快递管家参数</div>
                </div>
                <div class="modal-footer">
                    <a href="/merchants/order/printOrder" class="btn btn-primary">确定</a>
                </div>
            </div>
        </div>
    </div>
    <!--前往配置发货地址弹窗-->
    <div class="modal export-modal" id="SetAddress" style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none">
        <div class="modal-dialog" id="SetAddress_content">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">提示</h4>
                    </div>
                    <div class="modal-body">
                    	<div class="SetAddress_title">发货地址不存在！</div>
                    	<div>请前往<a href="/merchants/currency/location">设置--店铺信息--商家地址库</a>请前往配置发货地址</div>
                    </div>
                    <div class="modal-footer">
                        <a href="/merchants/currency/location" class="btn btn-primary">确定</a>
                    </div>
                </div>
        </div>
    </div>
    <!--手动填写快递单号提示弹框-->
    
    
    
    
    <script type="text/javascript">
    	var json = {!! $regions_data !!};
		// json = JSON.parse('[1, 5, "false"]');
        @if(empty($shopAddress))
            var address = true;
            @else
                var address = false;
            @endif
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
    <script src="{{ config('app.source_url') }}mctsource/js/order_62zq70mn.js"></script>
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
