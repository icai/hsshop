@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/edit.css" />    
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
                        <input id="edit_id" type="hidden" name="id" value="{{ request('id') }}">
                    <hr />
                    <div class="news_detail">
                        <div class="exm-top">
                            <div class="exm-lef">
                                <div class="inpGroup">
                                    <label for="title" class="inpName">
                                      <select name=""  class="firstcategory">
                                            @if($categoryList[0])
                                                <option value="">一级分类</option>
                                                @foreach($categoryList[0] as $val)
                                                    <option value="{{ $val['id'] }}" @if($val['id'] == $pid) selected="selected" @endif>{{ $val['title'] }}</option>
                                                @endforeach
                                            @endif
                                      </select>
                                    </label>
                                </div>
                                <div class="inpGroup" style="margin-bottom:30px;">
                                    <label for="subtitle" class="inpName">
                                        <select name="" class="seccategory">
                                            <option value="">二级类目</option>
                                            @foreach($secondCates as $cate)
                                                <option value="{{ $cate['id'] }}" @if($cate['id'] == $caseData['business_id']) selected="selected" @endif>{{ $cate['title'] }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                                <!-- 二维码提交 -->
                                <input type="hidden" value="{{ $caseData['qrcode'] }}" class="qrcode_img_hidden">
                                 <div class="btn_img" style="text-align:left; position: relative;top: 10px;left:90px;">
                                    @if($caseData['type'] == 1)
                                    <img class="qrcode_img" src="{{ imgUrl($caseData['qrcode']) }}" alt="">
                                    @elseif($caseData['type'] == 2)
                                        @if(!starts_with($caseData['qrcode'],'http'))
                                        <img class="qrcode_img" src="data:image/png;base64,{{ $caseData['qrcode'] }}" alt="">
                                        @else
                                        <img class="qrcode_img" src="{{ $caseData['qrcode'] }}" alt="">
                                        @endif
                                    @else 
                                    <img class="qrcode_img" src="{{ $caseData['qrcode'] }}" alt="">
                                    @endif
                                    <div class="img_submit btn">二维码上传</div>
                                 </div>
                                 <!-- 二维码提交end -->
                            </div>
                        </div>
                        <!-- 确认按钮 -->
                        <div class="btn_group" style="text-align:left; position: relative;top: 10px;left:90px;">
                                <button id="sub" type="button" class="btn btn-primary sure">确认提交</button>
                        </div>
                    </div>
                </div>
            </div>
                <!-- 上传二维码 -->
                <div style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none" class="import-data-mask"></div>
                    <div class="lead-dialog">
                        <div class="dialog-header ">
                            <h3 class="dialog-title">上传二维码</h3>
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
                                <input type="file" name="image" id="add_file" value="" class="choose-file" accept="*.png,*.jpg" name=""/>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            </div>
                            <input type="hidden" name="id" value="{{ request('id') }}" >
                            </form>
                        </div>
                        <div class="dialog-footer">
                            <div class="sure-btn J_sure-btn disabled">确定上传</div>
                        </div>
                    </div>
                </div>
                <!-- 上传二维码end -->
        </div>
    </div>
    
@endsection
@section('foot.js')
<!--当前页面js-->
<script src="{{ config('app.source_url') }}staff/hsadmin/js/edit.js" type="text/javascript" charset="utf-8"></script>
<script>
    var data = {!! json_encode($categoryList) !!};
    var editid = {{ request('id') }}
</script>
@endsection
