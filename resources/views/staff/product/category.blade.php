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
                <span>商品管理-商品品类</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="javascript:;" style="color: #333;">品类列表</a>
                    <span class="verLine">|</span>
                    <a href="javascript:;" class="addCategory">新增品类</a>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>品类</li>
                    <li>排序</li>
                    <li>操作</li>
                </ul>
                @forelse($category[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$val['category_name']}}</li>
                        <li>{{$val['listorder']}}</li>
                        <li>
                            <a href="javascript:;" data-id="{{$val['id']}}" data-parent="{{$val['parent_id']}}" class="modify">修改</a>
                            <a href="javascript:;" data-id="{{$val['id']}}" class="del">删除</a>
                        </li>
                    </ul>
                @endforeach
                <div class="main_bottom t-pr" style="position: relative;">
                    {{$category[1]}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/categoryList.js" type="text/javascript" charset="utf-8"></script>
@endsection