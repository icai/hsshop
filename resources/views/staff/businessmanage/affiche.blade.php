@extends('staff.base.head')
@section('head.css')
    <style type="text/css">
        .title{padding:15px;}
        .btn-group{
            padding:20px;
        }
    </style>
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <style type="text/css">
        .t-pr{position: relative;}
        .t-pagination{position: absolute;right: 0;top: 0;margin-top: 0;border-bottom: none !important;}
        .main{margin-top:160px;}
        .t-tips{padding: 5px;display:inline-block;}
        /*2017-4-27  田小文*/
        /**
         * 粒度小的样式定义
         * 作用通用 统一前缀 t-
         */
        /*---定位---*/
        .t-pr{position:relative;}
        .t-t0{top:0;}
        .t-b0{bottom:0;}
        .t-r0{right:0;}
        .t-l0{left:0;}
        /*---尺寸---*/
        .t-w-50{width:50px !important;}
        .t-w-60{width:60px !important;}
        .t-w-70{width:70px !important;}
        .t-w-80{width:80px !important;}
        .t-w-90{width:90px !important;}
        .t-w-100{width:100px !important;}
        .t-w-150{width:150px !important;}
        .t-w-200{width:200px !important;}
        /*---隐藏---*/
        .t-none{display: none;}
        /*---边框---*/
        .t-br0{border-radius: 0;}
    </style>
    <div class="main">
        <div class="content">
            <div class="title">公告添加：</div>
            <form class="myForm">
                <textarea id="editor" name="content" type="text/plain" style="width:calc(100% - 120px);height:300px;margin-left: 60px;">{!! $obj['content'] or '' !!}</textarea>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id" value="{{ $obj['id'] or '' }}">
                <div class="btn-group">
                    <div class="btn btn-primary">提交</div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        var _host = '{{ config('app.source_url') }}';
        var imgUrl ="{{ imgUrl() }}";
    </script>
@endsection
@section('foot.js')
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/affiche.js" type="text/javascript" charset="utf-8"></script>
@endsection