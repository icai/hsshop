@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}/staff/hsadmin/css/2.1 store_management.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection

@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>客服管理-全部</span>
            </div>

            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">客服列表</a>
                    <span class="verLine">|</span>
                    @if(empty(\CusSerManageService::getAll()) )
                    <a href="/staff/addCustomerService">新增客服</a>
                    @endif
                </div>
                @include('staff.customerservicemange.message')
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>客服名称</li>
                    <li>客服电话</li>

                    <li>操作</li>
                </ul>
                    @if(!empty($data))
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$data['name']}}</li>
                        <li>{{$data['phone']}}</li>

                        <li>
                            <a href="{{url('/staff/updateCustomerService')}}" class="modify">修改</a>
                            <a href="{{url('/staff/deleteCustomerService')}}" class="del" >删除</a>
                        </li>
                    </ul>
                        @else

                    @endif
            </div>
        </div>
    </div>
@stop