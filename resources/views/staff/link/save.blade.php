@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.1 addNews.css" /> 
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
<div class="main">
    <div class="content">
        <input id="source" type="hidden" value="{{ config('app.url') }}staff/hsadmin" />
        <div class="content_top">
            <button type="button" class="btn btn-primary">当前位置</button>
            <span><a href="/staff/link/index">友链列表</a>-{{ $title }}</span>
        </div>
        <div class="main_content">
            <div class="sorts">
                <a href="##" style="color: #333;">{{ $title }}</a>
            </div>
            <div class="addNews_list">
                <form id="saveForm" method="post">
                    <input id="edit_id" type="hidden" name="id" value="{{ $data['id'] or 0 }}">
                    <!--标题照片详情-->
                    <div class="news_detail">
                        <div class="inpGroup">
                            <label for="name" class="inpName">链接标题：</label>
                            <input type="text" style="width:365px;" name="name" id="name" value="{{ $data['name'] or '' }}" />
                        </div>
                        <div class="inpGroup">
                            <label for="url" class="inpName">链接网址：</label>
                            <input type="text" style="width:365px;" name="url" id="url" value="{{ $data['url'] or '' }}" />
                        </div> 
                        <div class="inpGroup">
                            <label class="inpName" for="sort">排序：</label>
                            <input class="clearint" type="number" name="sort" id="sort" value="{{ $data['sort'] or '' }}" />
                        </div>
                    </div>
                    <div class="btn_group" style="text-align:left;margin-top: 40px;">
                        <button id="saveup" type="button" class="btn btn-primary sure">确认提交</button>
                        <button id="cancel" type="button" class="btn btn-primary sure">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection @section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/linkSave.js" type="text/javascript" charset="utf-8"></script>
    @endsection