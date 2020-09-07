@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.4 account_management.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-配置权限</span>


            </div>
            <div class="main_content">
                <ul class="table_title flex-between">
                    <li>手机</li>
                    <li>店铺状态</li>
                    <li>有效期限</li>
                    <li>店铺权限</li>
                    <li>短信通知</li>
                    <li>操作时间</li>
                </ul>
                @forelse($data as $val)
                    <ul class="table_body  flex-between">
                        <li>{{$val['phone']}}</li>
                        <li>
                            {{$val['createShopLog']['isCreateShop'] == 1 ? '选择创建' : '未选择创建'}}
                            @if($val['createShopLog']['isCreateShop'] == 1)
                                ({{$val['createShopLog']['detail']['err_code'] == 0 ? '创建成功': '失败:'.$val['createShopLog']['detail']['err_msg']}})
                            @endif
                        </li>
                        <li>1年</li>
                        <li>
                            {{$val['createShopLog']['isCreateShop'] == 1 ? '选择创建' : '未选择创建'}}
                            @if($val['createShopLog']['isCreateShop'] == 1)
                                ({{$val['createShopLog']['detail']['err_code'] == 0 ? '配置成功': '失败:'.$val['createShopLog']['detail']['err_msg']}})
                            @endif
                        </li>
                        <li>
                            {{$val['smsLog']['isSendMsg'] == 1 ? '选择发送' : ' 未选择发送'}}
                            @if($val['smsLog']['isSendMsg'] == 1)
                                （{{$val['smsLog']['detail']['statusCode'] == 0 ? '发送成功': '失败:'.$val['smsLog']['detail']['statusMsg']}})
                            @endif
                        </li>
                        <li>{{date('Y-m-d H:i:s',$val['created_at'] ) }}</li>
                    </ul>
                    @endforeach
            </div>
        </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
            <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
            <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
            <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
            <!--主要内容的JS-->
            <script src="{{ config('app.source_url') }}staff/hsadmin/layer/layer.js"></script>
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.4 account_management.js" type="text/javascript" charset="utf-8"></script>
@endsection