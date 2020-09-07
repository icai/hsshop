@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_9ps47mzo.css" />
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
                <a href="{{URL('/merchants/currency/index')}}">店铺信息</a>
            </li>
            <li>
                <a href="{{URL('/merchants/currency/location')}}">商家地址库</a>
            </li>
            <li>
                <a href="{{URL('/merchants/currency/outlets')}}">门店管理</a>
            </li>
            <li>
                <a href="{{URL('/merchants/currency/share/set')}}">通用分享设置</a>
            </li> 
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
<div class="content">
    @if ($weixin)
    <ul>
        <input type="hidden" id="id" value="{{$weixin['id']}}"/>
    	<li class="css_input">
            店铺名称：
    		<input type="text" id="shop_name" class="disabled" value="{{$weixin['shop_name']}}" disabled />
    		<a href="javascript:;" id="nameChange">修改</a>
    	</li>
    	<li>经营类目：
    		<p>{{$business}}</p>
    	</li>
    	<li>创建日期：
    		<p>{{$weixin['created_at']}}</p>
    	</li>
    	<li><span style="float:left;padding-right:1px">店铺logo：</span>
    		<div id="logoImgDiv" style="display: inline-block;">
                @if ( !empty($weixin['logo']) )
                <img src="{{ imgUrl($weixin['logo']) }}" width="50" height="50"  style="display: inline-block;"/>
                @elseif ( !empty(session('logo')) )
                    <img src="{{ imgUrl(session('logo')) }}" width="50" height="50"  style="display: inline-block;"/>
                @else
                <img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" width="50" height="50" style="display: inline-block;">
                <input type="hidden" class="logo" value="home/image/huisouyun_120.png">
                @endif
    		</div>
            <form id="uploadForm" enctype="multipart/form-data">
    		    <a href="javascript:;" id="logoChange">修改图片 
                    <input type="file" name="file" id="files" accept="image/jpeg,image/gif,image/png"  >
                </a>
            </form>
            <span id="hintText" class="help-block" style="margin-left:70px">图片建议尺寸：116*116px，图片大小不超过3M</span>
    	</li>
    	
        <li>
            <button class="btn btn-primary save">保存</button>
        </li>
    </ul>
    @else
        店铺不存在
    @endif

</div>
@endsection
@section('page_js')
<!--layer文件引入-->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
<!--上传图片js-->
<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
<!-- 当前页面js -->
<script type="text/javascript">
    var imgUrl = "{{ config('app.source_url') }}" + 'mctsource/';
    var _type = 0;
    var _default = 0;
    var _send_default = 0; 
</script>
<script src="{{ config('app.source_url') }}mctsource/js/currency_9ps47mzo.js"></script>
@endsection

