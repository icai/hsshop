@extends('staff.base.head')
@section('head.css')
<link rel="stylesheet" href="{{ config('app.source_url') }}/staff/hsadmin/css/css/invoiceList.css" />
@endsection

@section('slidebar')
    @include('staff.fee.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-发票管理</span>
            </div>
            <div class="main_content">
                <div id="shop_form" class="form-inline">
                    <div class='input-group col-sm-2'>
                        <span class="input-group-addon">
                            <span>发票编码</span>
                        </span>
                        <input type='text' name="requestNo" class="form-control"  value="" />
                        
                    </div>
                    <button class="btn btn-primary search">搜索</button>
                    <select name="choose" id="choose" class="form-control" style="float:right">
                        <option value="999" default>请选择</option>
                        <option value="0">待开具</option>
                        <option value="1">已开具</option>
                        <option value="2">已邮寄</option>
                    </select>
                </div>

                <div>
                    <ul class="table_title flex-between">
                        <li>发票编码</li>
                        <li>店铺名称</li>
                        <li>手机号码</li>
                        <li>开票服务</li>
                        <li>申请时间</li>
                        <li>发票金额(元)</li>
                        <li>发票类型</li>
                        <li>发票性质</li>
                        <li>发票信息</li>
                        <li>状态</li>
                        <li>操作</li>
                    </ul>
                    <div class="t_body"></div>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   

@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}staff/hsadmin/js/invoiceList.js" type="text/javascript" charset="utf-8"></script>
@endsection