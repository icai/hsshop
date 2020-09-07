@extends('merchants.default._layouts')
@section('head_css')
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <!-- <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"> -->
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_tkaol5f3.css" />
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection
@section('slidebar')
    @include('merchants.order.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 普通导航 开始 -->
            批量发货记录
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
        <!--主要内容-->
        <div class="main_content">
            <ul class="main_content_title">
                <li style="width:6%">序号</li>
                <li>业务单号</li>
                <li>物流公司</li>
                <li>运单号</li>
                <li>上传时间</li>
                <li style="width:20%">发货状态</li>
            </ul>
            @forelse ( $data as $k=>$v )
                <ul class="data_content" >
                    <li style="width:6%">{{ $k + 1}}</li>
                    <li>{{ $v['oid'] }}</li>
                    <li>{{ $v['express_name'] }}</li>
                    <li>{{ $v['express_no'] }}</li>
                    <li>{{ $v['created_at']}}</li>
                    <li style="width:20%">{{ $v['status'] ? '发货成功' : '发货失败:'.$v['err_msg'] }}</li>
                </ul>
            @empty
                <ul class="data_content">暂无数据</ul>
            @endforelse
        </div>
        <!-- 分页 -->
        <div class="pageNum">
            &nbsp;共 {{ $data->total() }} 条记录 &nbsp;&nbsp;&nbsp;
            {{  $data->links() }}
        </div>
    </div>
@endsection
@section('page_js')
    <script src="{{ config('app.source_url') }}static/js/require.js" ></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>

@endsection
