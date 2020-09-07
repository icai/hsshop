@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/6.1 potential_customers.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/aliList.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
@endsection
@section('slidebar')
    @include('staff.alixcx.slidebar');
@endsection
@section('content')
<div class="main">
    <!-- 状态区域 -->
    <div class="nav">
        <ul>
            <li @if(request('status') === null) class="active" @endif>
                <a href="?">全部</a>
            </li>
            <li @if(request('status') !== null && request('status') == 0) class="active" @endif>
                <a href="?status=0">无操作</a>
            </li>
            <li @if(request('status') !== null && request('status') == 2) class="active" @endif>
                <a href="?status=2">审核中</a>
            </li>
            <li @if(request('status') !== null && request('status') == 3) class="active" @endif>
                <a href="?status=3">审核被拒</a>
            </li>
            <li @if(request('status') !== null && request('status') == 4) class="active" @endif>
                <a href="?status=4">审核成功</a>
            </li>
            <li @if(request('status') !== null && request('status') == 5) class="active" @endif>
                <a href="?status=5">已发布</a>
            </li>
            <li @if(request('status') !== null && request('status') == 1) class="active" @endif>
                <a href="?status=1">已提交代码</a>
            </li>
            <li @if(request('status') !== null && request('status') == 7) class="active" @endif>
                <a href="?status=7">已作废</a>
            </li>
            <li @if(request('status') !== null && request('status') == 8) class="active" @endif>
                <a href="?status=8">已下架</a>
            </li>
        </ul>
    </div>
    <!-- 状态区域 -->
    <!-- 搜索区域 -->
    <div class="search">
        <form id="code_form" class="search_form" method="get" action="/staff/xcx/list">
            <span>搜索：</span>
            <select name="search_type">
                <option @if(!empty(request('search_type')) && request('search_type') == 'title') selected='selected' @endif value="title">名称</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'request_domain') selected='selected' @endif value="request_domain">域名简称</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'app_id') selected='selected' @endif value="app_id">appid</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'shop_name') selected='selected' @endif value="shop_name">店铺名称</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'mphone') selected='selected' @endif value="mphone">手机号码</option>
            </select>
            <input type="text" name="search_value" @if(!empty(request('search_value'))) value="{{request('search_value')}}" @endif>
            <span class="action_time">操作时间</span>
            <input type="text" name="start_at" class="start_time" id="start_time" @if(!empty(request('start_at'))) value="{{request('start_at')}}" @endif>
            <span>-</span>
            <input type="text" name="end_at" class="end_time" id="end_time" @if(!empty(request('end_at'))) value="{{request('end_at')}}" @endif>
            <input type="submit" class="search_btn" value="搜索"></input>
            <a class="get_host_btn">一键获取域名</a>
        </form>
    </div>
    <!-- 搜索区域 -->
    <!-- 表格区域 -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="success">
                    <th>数据库ID</th>
                    <th>小程序</th>
                    <th>模板ID</th>
                    <th>店铺名称</th>
                    <th>手机号码</th>
                    <th>域名简称</th>
                    <th>useId</th>
                    <th>appid</th>
                    <!-- <th>是否自动发布</th> -->
                    <th>进度</th>
                    <th>授权时间</th>
                    <th colspan="3">操作</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <div class="foot_box">
            <div class="foot_left">
                <input type="checkbox" name="allCheck" id="allCheck" />
                <label for="allCheck">全选</label>
                <input type="button" class="btn-charge btn btn-primary" value="标记付费" id="isFee"/>
                <input type="button" class="btn-free btn" value="标记免费" id="isFree"/>
                <input type="button" class="btn-charge btn btn-primary" value="标记赠送" id="isGive"/>
            </div>
            <div class="foot_right">
                <span class="record">共<span>2</span>条记录</span>
                <div class="page">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!--添加备注-->
<div class="add_remark_model hide">
    <div class="remark_model">
        <div>
            <div class="info">名称:</div>
            &nbsp;<span class="remark_add_title">1111</span>
        </div>
        <div>
            <div class="info">备注:</div>
            &nbsp;<input type="text" max-length="20" class="remark_add_cont" name="remark_add_cont" value="" placeholder="请输入备注">
        </div>
    </div>
</div>
<!--添加备注-->
<!--获取二维码-->
<div class="get_qrcode_model hide">
    <div class="qr_code_model">
        <div>
            <div class="info">名称:</div>
            &nbsp;<span class="qr_code_title">1111</span>
        </div>
        <div>
            <div class="info">宽度:</div>
            &nbsp;<input type="text" class="qr_code_width" name="qr_code_width" value="430">&nbsp;px
        </div>
        <!-- <div>
            <div class="info">小程序页面:</div>
            &nbsp;<div>
                <select name="qr_code_path" class="qr_code_path">
                </select>
            </div>
        </div> -->
    </div>
    <div class="qr_code_img">
        <img id="img_qrcode" src="" width="200px;" height="200px;" class="xcx-xcximg"/>
    </div>
</div>
<!--获取二维码-->
<!-- 设置域名 -->
<div class="set_code_model hide">
    <div class="code_model" style="padding: 0 40px;">
        <div style="margin-bottom: 5px;">
            <input type="text" style="width: 100%;" class="set_zhost" value="www.huisou.cn">
        </div>
        <p style="color: red; font-size:12px ;">建议格式：网络名.域名主题.域名后缀(例如：www.baidu.com)</p>
    </div>
</div>
<!--设置域名-->
<!-- 版本上传 -->
<div class="upload_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span class="upload_title"></span>
            </div>
        </div>
        <div>
            <div class="info">
                模板ID：
            </div>
            <div>
                <input type="text" class="template_id" name="template_id" value="">
            </div>
        </div>
        <div>
            <div class="info">
                版本号：
            </div>
            <div>
                <input type="text" class="version" name="version" value="">
            </div>
        </div>
    </div>
</div>
<!-- 版本上传 -->
<!-- 提交审核 -->
<div class="submit_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span class="title_up"></span>
            </div>
        </div>
        <div class="">
            <div class="info">
                版本号：
            </div>
            <div>
                <input type="text" class="version" name="version" value="">
            </div>
        </div>
        <div  class="">
            <div class="info">
                版本描述：
            </div>
            <div>
                <input type="text" class="version_desc" name="version_desc" value="">
            </div>
        </div>
        <div  class="">
            <div class="info">
                过期时间：
            </div>
            <div>
                <input type="text" class="licenseValid_date" name="licenseValid_date" value="">
            </div>
        </div>
    </div>
</div>
<!-- 提交审核 -->
@endsection
@section('foot.js')
	<script type="text/javascript">
        var _host = "{{ config('app.source_url') }}";
		var url = "{{ config('app.url') }}";
		re=new RegExp("https://","g");
   		url=url.replace(re,"");
	</script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <!-- ajax分页js -->
	<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/aliList.js" type="text/javascript" charset="utf-8"></script>
@endsection
