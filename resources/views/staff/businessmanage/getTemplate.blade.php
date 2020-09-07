@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.1 admin_type.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')

    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-模式模板设置</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">模板列表</a>
                    <span class="verLine">|</span>
                    <a href="/staff/addTemplate">新增默认模板</a>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>模板名称</li>
                    <li>排序</li>
                    <li>操作</li>
                </ul>
                @forelse($template[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$val['title']}}</li>
                        <li>{{$val['sort']}}</li>
                        <li>
                            <a href="/staff/addTemplate?id={{$val['id']}}" class="modify">修改</a>
                            <a href="##" class="del" data-id="{{$val['id']}}">删除</a>
                        </li>
                    </ul>
                @endforeach
                <div class="main_bottom t-pr" style="position: relative;">
                    {{$template[1]}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/get_template.js" type="text/javascript" charset="utf-8"></script>
@endsection