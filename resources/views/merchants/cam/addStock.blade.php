@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/kamcreate.css" />

@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/cam/list') }}">发卡密</a>
            </li>
            <li>
                <a href="/merchants/cam/camStockList?id={{ request('id') }}">卡密库</a>
            </li>
            <li>
                <a >添加库存</a>
            </li>
        </ul>
    </div>
</div>
@endsection
@section('content')
<div class="content">
    <div class="kam-title">批量添加库存</div>
    <div class="kam-wraper">
        <div class="item">
            <span class="item-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;剩余库存：</span>
            <span class="stock-num">{{ $lelftCount }}份</span>
        </div>
        <div class="item">
            <span class="item-title"><span class="emphasize">* </span>导入卡密库：</span>
            <span class="kam-name">1.csv</span>
            <span class="upload-kam">上传卡密</span>
        </div>
    </div>
    <div class="action-btn-box">
        <div class="action-btn J_cancel-btn">返回</div>
        <div class="action-btn submit-btn J_submit-btn">提交</div>
    </div>
</div>
<!-- 上传卡密弹窗 -->
<div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none" class="J_mask"></div>
<div class="kam-dialog">
    <div class="dialog-header ">
        <h3 class="dialog-title">上传卡密</h3>
        <a href="javascript:;" class="dialog-close">×</a>
    </div>
    <div class="dialog-body">
        <form action="" id="defaultForm" enctype="multipart/form-data">
        <div class="fileName">
            <div class="file-item"><span class="pp">文件名 :</span><span class="file_name">1.csv</span></div>
            <div class="file-item"><span class="pp">文件大小 :</span><span class="file_size">0.16KB</span></div>       								        								
        </div>
        <div class="file-box">
            <div class="add-file">选择文件</div>
            <input type="file" name="info" id="add_file" value="" class="choose-file" accept="*.csv" name=""/>
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        </div>
        <div class="file-tips">最大支持 1MB CSV 的文件</div>
        <div class="desc">
            <div class="desc-title">说明：</div>
            <div class="desc-content">
                1、每次上传最多1.5万条发卡密，可多次上传 
                <br>
                2、使用EXCLE制作你的发卡账号、密码。（第一列为账号，名称可以自定义；第二列为密码，名称可以自定义，必须大于1列小于等于2列）
                <br>
                3、请自定义前两列的名称，详情请下载模板。
                <br>
                4、发卡卡号格式可由6~16位数字和字母组成；密码位数不限制
                <br>
                5、将该文件保存为CSV的格式文件（*.CSV）；
            </div>
        </div>
        </form>
    </div>
    <div class="dialog-footer">
        <a href="#" class="download">下载模板</a>
        <div class="sure-btn J_sure-btn disabled">确定上传</div>
    </div>
</div>
@endsection
@section('page_js')
    <script type="text/javascript">
    	var host ="{{ config('app.url') }}";
    	var _host = "{{ config('app.source_url') }}";
        var wid = {{session('wid')}};
        var id = {{ request('id') }};
    </script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/addStock.js"></script>
@endsection