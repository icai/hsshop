@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_z8th92v4.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">店铺概况</div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!-- 店铺设置 开始 -->
    <div class="shop_set">
        <!-- 店铺logo 开始 -->
        <!-- logo 设置 -->
        <div class="shop_logo img_wrap" data-toggle="modal" data-target="#myModal">
            @if ( !empty($store['logo']) )
             	<img src="{{ imgUrl($store['logo']) }}" width="40" height="40" />
            @elseif ( !empty($store_logo) )
                <img src="{{ imgUrl($store_logo) }}" width="40" height="40" />
            @else
            	<img src="{{ config('app.source_url') }}mctsource/images/huisouyun_40.png" width="40" height="40" >
            @endif
            <span class="set_logo">修改
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="file" name="file" id="files" accept="image/jpeg,image/gif,image/png">
                </form>
            </span>
        </div>
        <!-- logo 设置 -->
        <!-- 店铺logo  结束 -->
        <!-- 店铺认证 开始 -->
        <div class="set_cotent">
            <input type="hidden" id="id" value="{{$store['id']}}"/>
            <h5 class="items_title">{{ $store['shop_name'] }}</h5>
            <!-- <ul class="cotent_items">
                <li>
                    <p class="cotent_name"><i class="glyphicon glyphicon-ok"></i>个人认证</p>
                    <div class="cotent_tip">
                        <p>已经个人认证</p>
                        <a href="javascript:void(0);">查看详情</a>
                    </div>
                </li>
                <li>
                    <p class="cotent_name"><i class="glyphicon glyphicon-ok"></i>担保认证</p>
                    <div class="cotent_tip">
                        <p>已经担保认证</p>
                        <a href="javascript:void(0);">查看详情</a>
                    </div>
                </li>
                <li class="disabled">
                    <p class="cotent_name"><i class="glyphicon glyphicon-ok"></i>线下商铺</p>
                    <div class="cotent_tip">
                        <p>名下未有商铺</p>
                        <a href="javascript:void(0);">添加</a>
                    </div>
                </li>
            </ul> -->
        </div>
        <!-- 店铺认证 结束 -->
        <!-- 操作 开始 -->
        <div class="set_opt">
            <a class="opt_btn bg_06bf04" href="{{ URL('/merchants/product/create') }}">发布商品</a>
            <a class="opt_btn bg_06bf04" href="{{ URL('/merchants/store') }}">新建微页面</a>
            <div class="opt_btn btn btn-default" href="javascript:void(0);">
                <p class="items_title">访问店铺</p>
                <!-- 二维码 开始 -->
                <div class="code_two">
                    <div class="shop_QRcode">
                        <p class="items_title">手机扫码访问</p>
                        <div class="RQ_code img_wrap">
                            {!! QrCode::size(150)->generate(URL("/shop/index/$wid")); !!} 
                        </div>
                        <div class="QRcode_bottom">
                            <a href="javascript:void(0);" class='copy_url'>复制页面链接</a>
                            <input type="hidden" value='{{URL("/shop/index/$wid")}}'>
                            <a href='{{URL("/shop/index/$wid")}}' target="_blank" >在电脑上查看</a>

                        </div>
                    </div>
                </div>
                <!-- 二维码 结束 -->
            </div>
        </div>
        <!-- 操作 结束 -->
    </div>
    <!-- 店铺设置 结束 -->
    <!-- 浏览量展示 开始 -->
    <div class="pageviews_items rows">
        <div class="col-sm-2">
            <a href="javascript:void(0);">
                <span>{{ $biData['viewpv'] ?? 0 }}</span>昨日浏览
            </a>
        </div>
        <div class="col-sm-2">
            <a href="javascript:void(0);">
                <span>{{ $biData['viewuv'] ?? 0 }}</span>昨日访客数
            </a>
        </div>
        <div class="col-sm-2">
            <a href="javascript:void(0);">
                <span>{{ $biData['productpv'] ?? 0 }}</span>昨日商品浏览量
            </a>
        </div>
        <div class="col-sm-2">
            <a href="javascript:void(0);">
                <span>{{ $biData['productuv'] ?? 0 }}</span>昨日商品访客数
            </a>
        </div>
        <div class="col-sm-2">
            <a href="/merchants/store">
                <span class="blue">{{$page_num}}</span>微页面
            </a>
        </div>
        <div class="col-sm-2">
            <a href="/merchants/product/index/1">
                <span>{{$product_num}}</span>商品
            </a>
        </div>
        <div style="clear:both;"></div>
    </div>
    <!-- 浏览量展示 结束 -->

</div>
@endsection
@section('page_js')
<!-- 弹框插件 -->
<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 图表插件 -->
<!-- <script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script> -->
<!-- 当前页面js -->
<script type="text/javascript">
    var imgUrl = "{{ config('app.source_url') }}" + 'mctsource/';
</script>
<script src="{{ config('app.source_url') }}mctsource/js/shop_z8th92v4.js"></script>

<script>
    $(function(){
        $('.copy_url').click(function(){
            // 复制链接
            var obj = $(this).next('input');
            copyToClipboard( obj );
            tipshow('复制成功','info');
            $(this).parents('.del_popover').remove();
            
        });
    });
</script>
@endsection