@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/printerList.css" />
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
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
            <li>
                <a href="{{ URL('/merchants/delivery/deliveryConfig') }}">外卖订单设置</a>
            </li>
            <li class="hover">
                <a href="{{ URL('merchants/delivery/printerList') }}">打印机</a>
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
    <div class="header-btn">
        <a href="javascript:void(0);" class="btn btn-success" id="add_printer">新建打印机</a>
        <!-- <a href="javascript:void(0);" class="btn btn-buy">购买打印机</a> -->
        <p>目前支持的WiFi打印机品牌：365</p>
        <div class="explain_icon">
            <i class="glyphicon green f14"></i>
            <a href="https://www.huisou.cn/home/index/newsDetail/860/news">365小票机外卖操作说明</a>
        </div>
    </div>
    <table class="data-table table table-hover">
        <thead>
            <tr class="active">
                <td>打印机</td>
                <td>类型</td>
                <td>设备号码</td>
                <td>选用状态</td>
                <td>连接状态</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
            <!-- <tr>
                <td>365小打印机</td>
                <td>小票机</td>
                <td>365小打印机</td>
                <td>365小打印机</td>
                <td>
                    <a href="javascript:void(0);" class="operate-item break">断开</a><a href="javascript:void(0);" class="operate-item set">设置</a><a href="javascript:void(0);" class="operate-item delete" data-id="0">删除</a>
                </td>
            </tr> -->
        </tbody>
    </table>
</div>
<!-- 新建打印机弹窗 -->
<div class="add_printer_model model none">
    <div class="modal-content">
        <p>PC仅支持链接WiFi打印机，蓝牙打印机请使用App链接。请确保WiFi打印机已链接网络。</p>
        <form>
            <!-- <div class="input-item">
                <label><span>*</span> 设备品牌：</label>
                <input class="device_brand" vaule="" type="text" placeholder="输入设备品牌">
                
            </div> -->
            <div class="input-item">
                <label><span>*</span> 设备名称：</label>
                <input class="device_name" type="text" placeholder="输入名称，如厨房打印机" onkeyup="checkLength(this,20)">
            </div>
            <div class="input-item">
                <label><span>*</span> 设备号码：</label>
                <input class="device_no" type="text" placeholder="输入设备底部的机器号" onkeyup="this.value=this.value.replace(/[\u4E00-\u9FA5]/g,'')">
            </div>
            <div class="input-item">
                <label><span>*</span> 设备密钥：</label>
                <input class="key" type="text" placeholder="输入密钥" onkeyup="this.value=this.value.replace(/[\u4E00-\u9FA5]/g,'')">
            </div>
        </form>
        <div class="input-item">
            <label><span>*</span> 打印数量：</label>
            <input id="option1" name="times" type="radio" value="1" checked><label class="page_num" for="option1">1张</label>
            <input type="radio" id="option2" name="times" value="2" ><label class="page_num" for="option2">2张</label>
            <input type="radio" id="option3" name="times" value="3" ><label class="page_num" for="option3">3张</label>
            <input type="radio" id="option4" name="times" value="4" ><label class="page_num" for="option4">4张</label>
        </div>
    </div>
</div>
<!-- 删除弹窗 -->
<div class="delete-model none">
    <p>确认删除？</p>
    <div class="foot-btn">
        <a href="javascript:void(0);" class="delete-btn" data-id="0">删除</a>
        <a href="javascript:void(0);" class="cancel-btn">取消</a>
    </div>
</div>
@endsection
@section('page_js')
<!--layer文件引入-->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script type="text/javascript">
    var printList = {!!$printList!!};
    console.log(printList)
</script>
<script src="{{ config('app.source_url') }}mctsource/js/printerList.js"></script>
@endsection

