@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.1 addNews.css" />    
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/8.2 examplesave.css" />
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
                <span>行业分类-{{ $title }}</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">{{ $title }}</a>
                </div>
                <div class="addNews_list">
                    <form id="myForm" action="/staff/addInformation" method="post">
                        <input id="edit_id" type="hidden" name="id" value="{{ $data['id'] or 0 }}">
                    <hr />
                    <!--标题照片详情-->
                    <div class="news_detail">
                        <div class="exm-top">
                            <div class="exm-lef">
                                <div class="inpGroup">
                                    <label for="title" class="inpName">行业分类名称：</label>
                                    <input type="text" name="name" id="name" value="{{ $data['name'] or ''}}" />
                                </div>
                                <div class="inpGroup">
                                    <label for="subtitle" class="inpName">排序：</label>
                                    <input type="text" name="sort" id="sort" value="{{ $data['sort'] or 0 }}" />
                                </div>
                            </div>
                        </div>
                        <div class="btn_group" style="text-align:left; position: relative;left: 140px;top: 10px;">
                            <button id="sub" type="button" class="btn btn-primary sure">确认提交</button>
                            <button id="sub1" type="button" class="btn btn-primary sure">重置表单</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/8.4 industrysave.js" type="text/javascript" charset="utf-8"></script>   
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/upImage.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>  
@endsection