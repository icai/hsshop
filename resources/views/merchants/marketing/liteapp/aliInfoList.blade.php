@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/aliInfoList.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">支付宝<span style="color:red;font-size:12px">（目前仅支持企业账户）</span></a>
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
        <ul class="tab_nav">
            <li class="hover">
                <a href="/merchants/marketing/alixcx/list">小程序列表</a>
            </li>
            <li>
                <a href="/merchants/store" style="display:inline-block;">小程序微页面</a>
                <span style="color:orange">注：支付宝小程序微页面是与店铺微页面打通的</span>
            </li>
        </ul>
        <p class="mid-btn">
            <a href="{{config('app.url')}}aliapp/getUrl" class="btn">授权支付宝小程序</a>
        </p>
        <table class="data-table table table-hover" >
                <!-- 标题 -->
                <tr class="active">
                    <th>小程序名称</th>
                    <th>小程序应用id(AppID)</th>
                    <th>支付宝公钥</th>
                    <th>授权时间</th>
                    <th>操作</th>
                </tr>
            @forelse($data[0]['data'] as $val)
                <tr data-id="{{$val['id']}}">
                    <td>{{$val['app_name']}}</td>
                    <td>
                        {{$val['auth_app_id']}}
                    </td>
                    <td>
                        <div class="pub_key">
                            {{$val['ali_rsa_pub_key']}}
                        </div>
                        
                    </td>
                    <td>
                        {{$val['created_at']}}
                    </td>
                    <td>
                        <a href="javascript:void(0);">访问小程序</a>
                        -
                        <a href="javascript:void(0);" class="update_key">添加公钥</a>
                    </td>
                </tr>
                @endforeach
            </table>
        {{$data[1]}}
    </div>
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}/static/js/layer/layer.js"></script>
<!-- 当前JS -->
<script src="{{ config('app.source_url') }}mctsource/js/aliInfoList.js"></script>
@endsection