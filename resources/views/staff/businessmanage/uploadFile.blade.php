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
                <span>店铺管理-上传微信公众号文件</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <span>上传文件</span>
                        <input type="file" name="file" id="file" accept=".txt">
                        <button type="button" class="btn btn-primary subBtn">提交</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/uploadFile.js" type="text/javascript" charset="utf-8"></script>
@endsection