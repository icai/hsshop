@extends('staff.base.head')
@section('head.css')
<link rel="stylesheet" href="{{ config('app.source_url') }}/staff/hsadmin/css/css/serviceList.css" />
@endsection

@section('slidebar')
    @include('staff.fee.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-续费订购</span>
            </div>
            <div class="main_content">
                <div id="shop_form" class="form-inline">
                    <div class='input-group col-sm-2'>
                        <span class="input-group-addon">
                            <span>店铺名称</span>
                        </span>
                        <input type='text' name="category" class="form-control search-value"  value="" />
                    </div>
                    <button class="btn btn-primary search">搜索</button>
                    <select name="choose" id="choose" class="form-control" style="float:right">
                        <option value="choose" default>请选择</option>
                        <option value="2">待审核</option>
                        <option value="0">待支付</option>
                        <option value="1">支付成功</option>
                        <option value="3">支付失败</option>  
                        <option value="-1">订单关闭</option>  
                    </select>
            </div>
                <div>
                    <ul class="table_title flex-between">
                        <li><label><input type="checkbox" name="" class="allSel" />全选</label></li>
                        <li>店铺名称</li>
                        <li>手机号码</li>
                        <li>续费时间</li>
                        <li>续费服务</li>
                        <li>服务期限</li>
                        <li>金额（元）</li>
                        <li>支付方式</li>
                        <li>订单状态</li>
                        <li>操作</li>
                    </ul>
                    <div class="t_body">
                   
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}staff/hsadmin/js/serviceList.js" type="text/javascript" charset="utf-8"></script>
@endsection