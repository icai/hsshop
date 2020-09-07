@extends('merchants.default._layouts')
@section('head_css')
<!--特殊按钮css样式文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_w25hu6oc.css" />
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
        	<li class="hover">
                <a href="{{ URL('/merchants/currency/express') }}">快递发货</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/receptionList') }}">上门自提</a>
            </li>
            {{--<li>
                <a href="{{ URL('/merchants/currency/localCity') }}">同城配送</a>
            </li>--}}
            
            {{--<li>
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
       
        <button type="button" id="addNewAdress" class="btn btn-success" onclick="javascript:location.href='{{ URL('/merchants/currency/expressSet') }}'">新建运费模版</button>
        <a href="https://www.huisou.cn/home/index/helpDetail/720" style="color:blue;margin-left: 10px;" target="_blank">如何设置运费模板</a>
        <!--动态添加显示部分-->
        <div class="modelShow clearfix"> 
            <div class="modelDiv" ng-if="json.length>0" ng-repeat='item1 in json'>
                <div class="modelTitle"  ng-click="arrowClick($index,item1.id)">
                    <div class="title_left">@{{item1.title}}</div>
                    <div class="title_right">
                        <i class="fs-12">最后编辑时间：<span>@{{item1.updated_at}}</span></i>
                        <a href="javascript:;" ng-click="goUp(item1.id,$event)">修改</a>-<a href="javascript:;" class="delete_btn" ng-click='removeInfo(item1.id,$event)'>删除</a>-<a href="javascript:;" class="arrow" ng-class="{0:'arrow-down',1:'arrow-up','undefined':'arrow-up'}[item1.is_reduced]"></a>
                    </div>
                </div>
                <div class="modelContent" ng-class="{0:'',1:'hide','undefined':'hide'}[item1.is_reduced]"> 
                <!-- <div class="modelContent">  -->
                   <table class="table">
                       <thead> 
                           <tr>
                               <td class="text-left" style="width:500px;">可配送区域</td>
                               <td class="text-right">首件（@{{item1.billing_type==0?'个':'Kg'}}）</td>
                               <td class="text-right">运费（元）</td>
                               <td class="text-right">续件（@{{item1.billing_type==0?'个':'Kg'}}）</td>
                               <td class="text-right">续费（元）</td>
                           </tr>  
                       </thead>
                       <tbody>
                           <tr ng-repeat='item in item1.showAddress'>
                               <td class="text-left" style="width:500px;">
                                   <span ng-if="item['-2']">默认</span>
                                   <span ng-repeat="vo in item['-1']">
                                       <span class="c-333" ng-init="pindex=$index">@{{pindex==0?vo.title:'、'+vo.title}}<span ng-if="!vo.isAllProvince">(</span><span class="c-666" ng-if="!vo.isAllProvince" ng-repeat="v in item[vo.id]"><span ng-init="cindex=$index">@{{cindex==0?v.title:'、'+v.title}}@{{item[v.id].length > 0 && item[v.id].length < list[v.id].length ?'(':''}}<span class="c-999" ng-if="item[v.id].length < list[v.id].length" ng-repeat="dv in item[v.id]"><span ng-init="dindex=$index">@{{dindex==0?dv.title:'、'+dv.title}}</span></span>@{{item[v.id].length > 0 && item[v.id].length < list[v.id].length ?')':''}}</span></span><span ng-if="!vo.isAllProvince">)</span></span>
                                   </span>
                               </td>
                               <td class="text-right">@{{item.first_amount}}</td>
                               <td class="text-right">@{{item.first_fee}}</td>
                               <td class="text-right">@{{item.additional_amount}}</td>
                               <td class="text-right">@{{item.additional_fee}}</td>
                           </tr>
                       </tbody>
                   </table>
                </div>
            </div>
            
            <div class="modelDiv" ng-if="json.length==0">还没有运费模版</div>
             
            {{$pageHtml}} 
        </div>
    </div>
    @endsection
    @section('page_js')
    <script type="text/javascript">
        var json = {!! json_encode($list) !!};
        var list = {!! $regions_data !!}; 

        // 分页靠右
        $('.pagination').addClass("pull-right");
    </script>
    <!--特殊按钮js文件-->
    <script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/specialBtn.js" type="text/javascript" charset="utf-8"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/currency_w25hu6oc.js"></script>
    @endsection