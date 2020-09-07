@extends('staff.base.head')
@section('head.css')

    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.3 add_classify.css" />

@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')

    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>推荐管理</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">推荐列表</a>
                    <span class="verLine">|</span>
                    <a href="/staff/addRecommend">添加推荐</a>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>ID</li>
                    <li>推荐名称</li>
                    <li>推荐描述</li>
                    <li>推荐路由</li>
                    <li>创建时间</li>
                    <li>操作</li>
                </ul>
                @forelse($recommend[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$val['id']}}</li>
                        <li>{{$val['name']}}</li>
                        <li>{{$val['content']}}</li>
                        <li>{{$val['uri']}}</li>
                        <li>{{$val['created_at']}}</li>
                        <li>
                            <a href="/staff/addRecommend?id={{$val['id']}}" class="modify">修改</a>
                            <a href="##" id="{{$val['id']}}" class="del">删除</a>
                        </li>
                    </ul>
                    @endforeach
                    <div class="main_bottom flex_end">
                        {{$recommend[1]}}
                    </div>
            </div>
        </div>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/recommend.js" type="text/javascript" charset="utf-8"></script>
@endsection