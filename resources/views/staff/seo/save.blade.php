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
            <span><a href="/staff/seo/index">SEO列表</a>-{{ $title }}</span>
        </div>
        <div class="main_content">
            <div class="sorts">
                <a href="##" style="color: #333;">{{ $title }}</a>
            </div>
            <div class="addNews_list">
                <form id="saveForm" action="/staff/addInformation" method="post">
                    <input id="edit_id" type="hidden" name="id" value="{{ $data['id'] or 0 }}">
                    <!--标题照片详情-->
                    <div class="news_detail">
                        <div class="inpGroup">
                            <label for="title" class="inpName">关键词：</label>
                            <input type="text" style="width:365px;" name="keywords" id="keywords" value="{{ $data['keywords'] or '' }}" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">标题：</label>
                            <input type="text" style="width:365px;" name="title" id="title" value="{{ $data['title'] or '' }}" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">描述：</label>
                            <textarea name="descript" style="vertical-align: middle;width:365px;height:150px;">{{ $data['descript'] or '' }}</textarea>
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">关联页面：</label>
                            <input type="text" style="width:365px;" name="unit_page" id="unit_page" value="" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">关联链接：</label>
                            <input type="text" style="width:365px;" name="page_url" id="page_url" value="{{ $data['page_url'] or '' }}" />
                        </div>
                    </div>
                    <div class="btn_group" style="text-align:left;margin-top: 40px;">
                        <button id="saveup" type="button" class="btn btn-primary sure">确认提交</button>
                        <button id="reset" type="button" class="btn btn-primary sure">重置表单</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection @section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/seosave_abc123ll.js" type="text/javascript" charset="utf-8"></script>
    @endsection