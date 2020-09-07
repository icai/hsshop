@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_w5fqrpf1.css" />
<style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide{display:none !important;}ng\:form{display:block;}.ng-animate-start{clip:rect(0,auto,auto,0);-ms-zoom:1.0001;}.ng-animate-active{clip:rect(-1px,auto,auto,0);-ms-zoom:1;}
</style>
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            {{--<li>
                <a href="{{ URL('/merchants/currency/orderSet') }}">上门自提</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/localCity') }}">同城配送</a>
            </li>--}}
            <li class="hover">
                <a href="{{ URL('/merchants/currency/express') }}">快递发货</a>
            </li>
           {{-- <li>
                <a href="{{ URL('/merchants/currency/tradingSet') }}">交易设置</a>
            </li>--}}
        </ul>
        <!-- 普通导航 结束  -->
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
<div class="content" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <form class="form-horizontal">
        <input type="hidden" name="id" value="{{$id ?? 0}}"/>
        {{ csrf_field() }}
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">模版名称：</label>
            <div class="col-sm-3">
                @{{ssList[addressIndex]}}
                <input type="text" ng-model="data.title" class="form-control"  />
            </div>
        </div>
        
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">计费方式：</label>
            <div class="col-sm-3">
                <input type="radio" class="num" ng-value="0" ng-model="data.billing_type" ng-checked="data.billing_type==0" />按件数
                <input type="radio" class="weight" ng-value="1" ng-model="data.billing_type" ng-checked="data.billing_type==1" />按重量
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">排序值：</label>
            <div class="col-sm-3">
                <input type="text" class="form-control t-number" ng-model="data.sort" />(值越大越靠前)
            </div>
        </div>
        <input type="hidden" name="data[delivery_rule]"/>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">配送区域：</label>
            <div class="col-sm-9 relative">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="td-w380 text-left">可配送区域</td>
                            <td>首件（@{{data.billing_type==0?"个":"Kg"}}）</td>
                            <td>运费（元）</td>
                            <td>续件（@{{data.billing_type==0?"个":"Kg"}}）</td>
                            <td>续费（元）</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:left;border-right: 1px solid #ccc;">
                                默认
                                
                            </td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock" ng-model="first_amount"/></td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock" ng-model="first_fee"/></td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock" ng-model="additional_amount"/></td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock" ng-model="additional_fee"/></td>
                        </tr>
                        <tr ng-repeat="item in showAddress"> 
                            <td style="text-align:left;border-right: 1px solid #ccc;">  
                                <span ng-repeat="vo in item['-1']">    
                                    <span class="c-333" ng-init="pindex=$index">@{{pindex==0?vo.title:'、'+vo.title}}<span ng-if="!vo.isAllProvince">(</span><span class="c-666" ng-if="!vo.isAllProvince" ng-repeat="v in item[vo.id]"><span ng-init="cindex=$index">@{{cindex==0?v.title:'、'+v.title}}@{{item[v.id].length > 0 && item[v.id].length < list[v.id].length ?'(':''}}<span class="c-999" ng-if="item[v.id].length < list[v.id].length" ng-repeat="dv in item[v.id]"><span ng-init="dindex=$index">@{{dindex==0?dv.title:'、'+dv.title}}</span></span>@{{item[v.id].length > 0 && item[v.id].length < list[v.id].length ?')':''}}</span></span><span ng-if="!vo.isAllProvince">)</span></span>
                                </span> 
                                <div class="pull-right">
                                    <a href="javascript:;" ng-click="editAddress($index)" class="c-07d">编辑</a>
                                    <a href="javascript:;" ng-click="removeAddress($index)" class="c-07d">删除</a>
                                </div>
                            </td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock"  ng-model="item.first_amount" value="0"/></td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock"  ng-model="item.first_fee" value="0"/></td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock"  ng-model="item.additional_amount" value="0"/></td>
                            <td><input type="text" class="form-control w70 t-number text-center input-sm iblock"  ng-model="item.additional_fee" value="0"/></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-left"><a href="javascript:;" ng-click="setShowAddress(1)">指定可配送区域和运费</a></td>
                        </tr>
                    </tfoot>
                </table>
            </div> 
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"></label>
            <div class="col-sm-3">
                <button type="button" id="saveBtn" ng-click="submitInfo()" class="btn btn-primary">保存</button>
                <button type="button" id="cancleBtn" class="btn btn-default" onclick="javascript:history.go(-1);">取消</button>
            </div>
        </div>
    </form>
    <!-- 地址选择弹窗 -->
    <div class="bg" ng-show="isShowAddress">
        <div class="address-modal">
            <div class="header">选择可配送区域</div>
            <div class="section">
                <div class="address-container original">
                    <h4>可选省、市、区</h4>
                    <ul class="province-wrap">
                        <li class="province-wrap-li" ng-repeat="item in oList['-1']">
                            <a href="javascript:;" class="province-wrap-li-a" ng-class="{true:'address-a-active',false:''}[item.active]">
                                <div class="province-wrap-title" ng-click="setActive(item,0,{pid:item.id,cid:'',did:''})">@{{item.title}}</div>
                                <span class="open-address-select" ng-click="setOpen(item,0,item.id,{pid:item.id,cid:'',did:''})">@{{item.isOpen?'-':'+'}}</span>
                            </a>
                            <ul class="city-wrap" ng-show="item.isOpen">
                                <li class="city-wrap-li" ng-repeat="vo in oList[item.id]">
                                    <a href="javascript:;" class="city-wrap-li-a" ng-class="{true:'address-a-active',false:''}[vo.active]">
                                        <div class="city-wrap-title" ng-click="setActive(vo,1,{pid:item.id,cid:vo.id,did:''})">@{{vo.title}}</div>
                                        <span class="open-address-select" ng-click="setOpen(vo,1,{pid:item.id,cid:vo.id,did:''})">@{{vo.isOpen?'-':'+'}}</span>
                                    </a>
                                    <ul class="area-wrap" ng-show="vo.isOpen">
                                        <li class="area-wrap-li"  ng-repeat="v in oList[vo.id]">
                                            <a href="javascript:;" class="area-wrap-li-a" ng-class="{true:'address-a-active',false:''}[v.active]">
                                                <div class="area-wrap-title" ng-click="setActive(v,2,{pid:item.id,cid:vo.id,did:v.id})">@{{v.title}}</div>
                                            </a>
                                        </li> 
                                    </ul>
                                </li>
                            </ul> 
                        </li> 
                    </ul>
                </div>
                <div class="address-add" ng-click="addAddress()">
                    添加
                </div>
                <div class="address-container edit">
                    <h4>已选省、市、区</h4> 
                    <ul class="province-wrap">
                        <li class="province-wrap-li" ng-repeat="item in sList[addressIndex]['-1']">
                            <a href="javascript:;" class="province-wrap-li-a">
                                <div class="province-wrap-title">@{{item.title}}</div>
                                <span class="open-address-select" ng-click="setOpen(item,0,item.id,{pid:item.id,cid:'',did:''})">@{{item.isOpen?'-':'+'}}</span>
                                <span class="remove-address-select"  ng-click="removeProvinceSelect({pobj:item,cobj:'',dobj:''})">×</span>
                            </a>
                            <ul class="city-wrap" ng-show="item.isOpen">
                                <li class="city-wrap-li" ng-repeat="vo in sList[addressIndex][item.id]">
                                    <a href="javascript:;" class="city-wrap-li-a">
                                        <div class="city-wrap-title">@{{vo.title}}</div>
                                        <span class="open-address-select" ng-click="setOpen(vo,1,{pid:item.id,cid:vo.id,did:''})">@{{vo.isOpen?'-':'+'}}</span>
                                        <span class="remove-address-select" ng-click="removeCitySelect({pobj:item,cobj:vo,dobj:''})">×</span>
                                    </a>
                                    <ul class="area-wrap" ng-show="vo.isOpen">
                                        <li class="area-wrap-li"  ng-repeat="v in sList[addressIndex][vo.id]">
                                            <a href="javascript:;" class="area-wrap-li-a">
                                                <div class="area-wrap-title">@{{v.title}}</div>
                                                <span class="remove-address-select" ng-click="removeAreaSelect({pobj:item,cobj:vo,dobj:v})">×</span>
                                            </a>
                                        </li> 
                                    </ul>
                                </li>
                            </ul> 
                        </li> 
                    </ul> 
                </div>
            </div>
            <div class="footer">
                <span class="yes" ng-click="confirmClick()">确定</span>
                <sapn class="no"  ng-click="cancelClick()">取消</sapn>
            </div>
        </div>                         
    </div>
</div>

@endsection
@section('page_js')
<!-- 图表插件 -->
<script>   
    var express ={!! json_encode($express) !!}; 
    var json = {!! $regions_data !!};
    var jsonData = {!! $jsonData !!}; 
    
</script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/currency_w5fqrpf1.js"></script>
@endsection