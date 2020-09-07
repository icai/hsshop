@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/create.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <input id="source" type="hidden" value="{{ config('app.source_url') }}staff/hsadmin" />
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>行业解决方案-{{ $title }}</span>
            </div>
            <div class="main_content">
                <button class="syn-data">同步会搜云推荐店铺数据</button>
            </div>
            <div class="main_content">
                <button class="lead-data">导入其他案例店铺</button>
            </div>

            <!-- 导入其他案例弹窗 -->
                <div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none" class="import-data-mask"></div>
                    <div class="lead-dialog">
                        <div class="dialog-header ">
                            <h3 class="dialog-title">导入案例</h3>
                            <a href="javascript:;" class="dialog-close">×</a>
                        </div>
                        <div class="dialog-body">
                            <form action="" id="defaultForm" enctype="multipart/form-data">
                            <div class="fileName">
                                <div class="file-item"><span class="pp">文件名 :</span><span class="file_name"></span></div>
                                <div class="file-item"><span class="pp">文件大小 :</span><span class="file_size"></span></div>       								        								
                            </div>
                            <div class="file-box">
                                <div class="add-file">选择文件</div>
                                <input type="file" name="excelFile" id="add_file" value="" class="choose-file" accept="*.csv,*.xls,*.xlsx" name=""/>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            </div>
                            <div class="file-tips">最大支持 1MB CSV 的文件</div>
                            </form>
                        </div>
                        <div class="dialog-footer">
                            <a href="#" class="download">下载模板</a>
                            <div class="sure-btn J_sure-btn disabled">确定上传</div>
                        </div>
                    </div>
                </div>
            <!-- 导入其他案例弹窗end -->
            <!-- 同步到会搜云弹窗 -->
            <div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none" class="import-data-mask"></div>
            <div class="syndata-dialog">
                <h3>是否同步到会搜云</h3>
                <button class="syndata-dialog-sure btn-style">确认</button>
                <button class="syndata-dialog-cancle btn-style btn-cancle">取消</button>
            </div>
            <!-- 同步到会搜云弹窗end -->
        </div>
    </div>

    
@endsection
@section('foot.js')
<script type="text/javascript">
	var attachment = [];
	var data = "{{ $data['desc'] or ''}}";
</script>
    <script src="{{ config('app.source_url') }}static/js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/create.js" type="text/javascript" charset="utf-8"></script>
@endsection